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
     * Buat signature untuk signed upload (TEMP).
     * Body: { tmp_key, filename, bytes }
     */
    public function signatureTemp(Request $req)
    {
        $data = $req->validate([
            'tmp_key'  => ['required','string','max:120'],
            'filename' => ['required','string','max:255'],
            'bytes'    => ['required','integer','min:1','max:10485760'], // 10MB per file
        ]);

        // Validasi ekstensi dasar
        $allowed = ['jpg','jpeg','png','webp','pdf','doc','docx','xlsx','zip'];
        $ext = strtolower(pathinfo($data['filename'], PATHINFO_EXTENSION));
        abort_if(!in_array($ext, $allowed, true), 422, 'Format file tidak diizinkan');

        $cloud = env('CLOUDINARY_CLOUD_NAME');
        $apiKey = env('CLOUDINARY_API_KEY');
        $apiSecret = env('CLOUDINARY_API_SECRET');
        $uploadPreset = env('CLOUDINARY_UPLOAD_PRESET', 'KRBS');

        // folder TEMP per user + tmp_key
        $folder = "krbs/tmp/".Auth::id()."/".$data['tmp_key'];

        // signature = sha1 string to sign -> gunakan param yang akan dikirim ke Cloudinary
        $timestamp = time();
        $toSign = "folder={$folder}&timestamp={$timestamp}&upload_preset={$uploadPreset}{$apiSecret}";
        $signature = sha1($toSign);

        return response()->json([
            'cloud_name'     => $cloud,
            'api_key'        => $apiKey,
            'upload_preset'  => $uploadPreset,
            'timestamp'      => $timestamp,
            'signature'      => $signature,
            'folder'         => $folder,
        ]);
    }

    /**
     * Hapus file TEMP dari Cloudinary (opsional).
     * Body: { public_id, file_type? }
     */
    public function deleteTemp(Request $req)
    {
        $data = $req->validate([
            'public_id'  => ['required','string','max:255'],
            'file_type'  => ['nullable','string','max:50'], // image/raw/video
        ]);

        abort_unless(str_starts_with($data['public_id'], 'krbs/tmp/'.Auth::id().'/'), 403, "Can't delete others' temp");

        $cloud = env('CLOUDINARY_CLOUD_NAME');
        $apiKey = env('CLOUDINARY_API_KEY');
        $apiSecret = env('CLOUDINARY_API_SECRET');

        $type = $this->guessResourceType($data['file_type'] ?? null);

        // destroy API
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
     * Finalize: pindahkan dari TMP -> final, lalu insert DB.
     * Body: { ticket_id, items: [ {public_id, secure_url, bytes, resource_type, format, original_filename} ] }
     */
    public function finalizeTemp(Request $req)
    {
        $data = $req->validate([
            'ticket_id' => ['required','integer','exists:tickets,ticket_id'],
            'items'     => ['array'],
            'items.*.public_id' => ['required','string'],
            'items.*.secure_url' => ['required','url'],
            'items.*.bytes' => ['required','integer','min:1'],
            'items.*.resource_type' => ['nullable','string'],
            'items.*.format' => ['nullable','string'],
            'items.*.original_filename' => ['nullable','string'],
        ]);

        Log::info('FINALIZE start', [
            'user'   => Auth::id(),
            'ticket' => $data['ticket_id'] ?? null,
            'count'  => is_countable($data['items'] ?? null) ? count($data['items']) : null,
        ]);

        $ticket = DB::table('tickets')->where('ticket_id', $data['ticket_id'])->first();
        abort_if(is_null($ticket), 404);
        abort_if((int)$ticket->user_id !== Auth::id(), 403, 'Not allowed');

        // Quota total 15MB/ticket (configurable)
        $incoming   = array_sum(array_map(fn($it)=>(int)$it['bytes'], $data['items'] ?? []));
        $quotaBytes = (int)env('ATTACHMENTS_TOTAL_QUOTA_MB', 15) * 1024 * 1024;
        $usedBytes  = (int)DB::table('ticket_attachments')->where('ticket_id', $data['ticket_id'])->sum('bytes');
        abort_if($usedBytes + $incoming > $quotaBytes, 422, 'Kuota lampiran tiket terlampaui');

        $cloudName   = env('CLOUDINARY_CLOUD_NAME');
        $apiKey      = env('CLOUDINARY_API_KEY');
        $apiSecret   = env('CLOUDINARY_API_SECRET');
        $finalFolder = "krbs/tickets/".$data['ticket_id'];

        $saved = [];
        foreach ($data['items'] as $it) {
            // keamanan: hanya terima file TMP milik user
            if (!str_starts_with($it['public_id'], 'krbs/tmp/'.Auth::id().'/')) {
                Log::warning('FINALIZE invalid temp file', ['public_id' => $it['public_id'], 'user' => Auth::id()]);
                return response()->json(['message' => 'Invalid temp file'], 403);
            }

            $resType  = $this->guessResourceType($it['resource_type'] ?? null); // penting
            $basename = basename($it['public_id']);
            $to       = $finalFolder.'/'.$basename;

            // 1) Coba rename TMP -> FINAL
            $renameEndpoint = "https://api.cloudinary.com/v1_1/{$cloudName}/resources/{$resType}/upload/rename";
            $rename = Http::withBasicAuth($apiKey, $apiSecret)
                ->asForm()
                ->post($renameEndpoint, [
                    'from_public_id' => $it['public_id'],
                    'to_public_id'   => $to,
                    'overwrite'      => true,
                    'invalidate'     => true,
                ]);

            $rj = $rename->json();
            Log::info('FINALIZE rename resp', [
                'from' => $it['public_id'],
                'to'   => $to,
                'type' => $resType,
                'ok'   => $rename->successful(),
                'body' => $rj,
            ]);

            // 2) Kalau rename gagal (misal resource_type mismatch), fallback:
            $finalUrl = $rj['secure_url'] ?? null;
            $finalPid = $rj['public_id'] ?? null;

            if (!$rename->successful() || isset($rj['error'])) {
                Log::warning('Rename failed, fallback keep TMP', [
                    'public_id' => $it['public_id'],
                    'err'       => $rj['error']['message'] ?? 'unknown',
                    'type'      => $resType,
                ]);
                $finalUrl = $finalUrl ?: $it['secure_url'];  // pakai URL TMP
                $finalPid = $finalPid ?: $it['public_id'];   // simpan public_id TMP
            }

            // 3) Insert ke DB
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

        Log::info('FINALIZE done', ['saved_count' => count($saved)]);
        return response()->json(['ok' => true, 'count' => count($saved), 'public_ids' => $saved]);
    }

    /**
     * Normalisasi resource type Cloudinary.
     * - image/*  -> image
     * - video/*  -> video
     * - selain itu -> raw  (pdf/doc/zip dll)
     */
    private function guessResourceType(?string $type): string
    {
        $t = strtolower($type ?? '');
        if (str_starts_with($t, 'image/')) return 'image';
        if ($t === 'video' || str_starts_with($t, 'video/')) return 'video';
        return 'raw';
    }
}
