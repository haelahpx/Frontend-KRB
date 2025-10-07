<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AttachmentController extends Controller
{
    /**
     * Signed upload params (TEMP folder)
     */
    public function signatureTemp(Request $req)
    {
        $data = $req->validate([
            'tmp_key'  => ['required','string','max:120'],
            'filename' => ['required','string','max:255'],
            'bytes'    => ['required','integer','min:1','max:10485760'], // 10MB max
        ]);

        $allowed = ['jpg','jpeg','png','webp','pdf','doc','docx','xlsx','zip'];
        $ext = strtolower(pathinfo($data['filename'], PATHINFO_EXTENSION));
        abort_if(!in_array($ext, $allowed, true), 422, 'Format file tidak diizinkan');

        $cloud        = env('CLOUDINARY_CLOUD_NAME');
        $apiKey       = env('CLOUDINARY_API_KEY');
        $apiSecret    = env('CLOUDINARY_API_SECRET');
        $uploadPreset = env('CLOUDINARY_UPLOAD_PRESET', 'KRBS');

        $folder = "krbs/tmp/" . Auth::id() . "/" . $data['tmp_key'];
        $timestamp = time();
        $toSign = "folder={$folder}&timestamp={$timestamp}&upload_preset={$uploadPreset}{$apiSecret}";
        $signature = sha1($toSign);

        return response()->json([
            'cloud_name'    => $cloud,
            'api_key'       => $apiKey,
            'upload_preset' => $uploadPreset,
            'timestamp'     => $timestamp,
            'signature'     => $signature,
            'folder'        => $folder,
        ]);
    }

    /**
     * Delete one TEMP file
     */
    public function deleteTemp(Request $req)
    {
        $data = $req->validate([
            'public_id' => ['required','string','max:255'],
            'file_type' => ['nullable','string','max:50'],
        ]);

        abort_unless(str_starts_with($data['public_id'], 'krbs/tmp/'.Auth::id().'/'), 403, "Can't delete others' temp");

        $cloud     = env('CLOUDINARY_CLOUD_NAME');
        $apiKey    = env('CLOUDINARY_API_KEY');
        $apiSecret = env('CLOUDINARY_API_SECRET');

        $type = $this->guessResourceType($data['file_type'] ?? null);
        $endpoint = "https://api.cloudinary.com/v1_1/{$cloud}/resources/{$type}/upload";

        $resp = Http::withBasicAuth($apiKey, $apiSecret)
            ->asForm()
            ->delete($endpoint, [
                'public_ids' => [$data['public_id']],
                'invalidate' => true,
            ]);

        return response()->json([
            'ok'   => $resp->successful(),
            'body' => $resp->json(),
        ]);
    }

    /**
     * Finalize TEMP -> permanent ticket folder + DB insert
     */
    public function finalizeTemp(Request $req)
    {
        $data = $req->validate([
            'ticket_id' => ['required','integer','exists:tickets,ticket_id'],
            'tmp_key'   => ['nullable','string','max:120'],
            'items'     => ['array'],
            'items.*.public_id' => ['required','string'],
            'items.*.secure_url' => ['required','url'],
            'items.*.bytes' => ['required','integer','min:1'],
            'items.*.resource_type' => ['nullable','string'],
            'items.*.format' => ['nullable','string'],
            'items.*.original_filename' => ['nullable','string'],
        ]);

        Log::info('FINALIZE start', [
            'user' => Auth::id(),
            'ticket' => $data['ticket_id'],
            'count' => is_countable($data['items'] ?? null) ? count($data['items']) : 0,
        ]);

        // Authorization check
        $ticket = DB::table('tickets')->where('ticket_id', $data['ticket_id'])->first();
        abort_if(is_null($ticket), 404);
        abort_if((int)$ticket->user_id !== Auth::id(), 403, 'Not allowed');

        // Quota check
        $incoming = array_sum(array_map(fn($it) => (int)$it['bytes'], $data['items'] ?? []));
        $quotaBytes = (int) env('ATTACHMENTS_TOTAL_QUOTA_MB', 15) * 1024 * 1024;
        $usedBytes = (int) DB::table('ticket_attachments')->where('ticket_id', $data['ticket_id'])->sum('bytes');
        abort_if($usedBytes + $incoming > $quotaBytes, 422, 'Kuota lampiran tiket terlampaui');

        $cloud = env('CLOUDINARY_CLOUD_NAME');
        $apiKey = env('CLOUDINARY_API_KEY');
        $apiSecret = env('CLOUDINARY_API_SECRET');
        $finalFolder = "krbs/tickets/" . $data['ticket_id'];

        $saved = [];
        $allRenamed = true;

        foreach ($data['items'] as $it) {
            if (!str_starts_with($it['public_id'], 'krbs/tmp/'.Auth::id().'/')) {
                Log::warning('FINALIZE invalid temp file', ['public_id' => $it['public_id']]);
                return response()->json(['message' => 'Invalid temp file'], 403);
            }

            $resType  = $this->guessResourceType($it['resource_type'] ?? null);
            $basename = basename($it['public_id']);
            $to       = $finalFolder . '/' . $basename;

            // Rename API call
            $renameEndpoint = "https://api.cloudinary.com/v1_1/{$cloud}/resources/{$resType}/upload/rename";
            $rename = Http::withBasicAuth($apiKey, $apiSecret)
                ->asForm()
                ->post($renameEndpoint, [
                    'from_public_id' => $it['public_id'],
                    'to_public_id'   => $to,
                    'overwrite'      => true,
                    'invalidate'     => true,
                ]);

            $rj = $rename->json();
            $ok = $rename->successful() && !isset($rj['error']);

            Log::info('FINALIZE rename resp', [
                'from' => $it['public_id'],
                'to'   => $to,
                'ok'   => $ok,
                'body' => $rj,
            ]);

            // Use Cloudinary's returned secure_url (do NOT rewrite path)
            $finalUrl = $rj['secure_url'] ?? $it['secure_url'];
            $finalPid = $rj['public_id'] ?? $it['public_id'];

            if (!$ok) {
                // rename failed -> keep TMP url/pid so link remains valid
                $allRenamed = false;
            }

            // DB insert
            DB::table('ticket_attachments')->insert([
                'ticket_id'            => $data['ticket_id'],
                'file_url'             => $finalUrl,
                'file_type'            => ($resType === 'image' ? ('image/'.($it['format'] ?? '')) : ($it['format'] ?? null)),
                'uploaded_by'          => Auth::id(),
                'cloudinary_public_id' => $finalPid,
                'bytes'                => (int)$it['bytes'],
                'original_filename'    => $it['original_filename'] ?? basename($finalPid),
                'created_at'           => now(),
                'updated_at'           => now(),
            ]);

            $saved[] = $finalPid;
        }

        // Cleanup TMP folder only if all items were renamed successfully
        if ($allRenamed) {
            try {
                $tmpKey = $data['tmp_key'] ?? null;
                if (!$tmpKey && !empty($data['items'][0]['public_id'])) {
                    if (preg_match('#^krbs/tmp/\d+/([^/]+)/#', $data['items'][0]['public_id'], $m)) {
                        $tmpKey = $m[1] ?? null;
                    }
                }

                if ($tmpKey) {
                    $prefix = "krbs/tmp/" . Auth::id() . "/" . $tmpKey;
                    foreach (['image','raw','video'] as $t) {
                        $del = Http::withBasicAuth($apiKey, $apiSecret)
                            ->asForm()
                            ->delete("https://api.cloudinary.com/v1_1/{$cloud}/resources/{$t}/upload", [
                                'prefix' => $prefix,
                                'invalidate' => true,
                            ]);
                        Log::info('CLEAN TMP prefix', [
                            'type' => $t, 'ok' => $del->successful(), 'body' => $del->json(),
                        ]);
                    }

                    $folderDel = Http::withBasicAuth($apiKey, $apiSecret)
                        ->delete("https://api.cloudinary.com/v1_1/{$cloud}/folders/{$prefix}");
                    Log::info('CLEAN TMP folder', [
                        'ok' => $folderDel->successful(), 'body' => $folderDel->json(),
                    ]);
                }
            } catch (\Throwable $e) {
                Log::warning('CLEAN TMP failed', ['err' => $e->getMessage()]);
            }
        } else {
            Log::warning('SKIP CLEAN TMP: ada file gagal rename, biarkan URL TMP tetap valid');
        }

        Log::info('FINALIZE done', ['saved_count' => count($saved)]);
        return response()->json([
            'ok' => true,
            'count' => count($saved),
            'public_ids' => $saved,
        ]);
    }

    /**
     * Normalize Cloudinary resource type
     */
    private function guessResourceType(?string $type): string
    {
        $t = strtolower($type ?? '');
        if (str_starts_with($t, 'image/')) return 'image';
        if ($t === 'video' || str_starts_with($t, 'video/')) return 'video';
        return 'raw';
    }
}
