<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class AttachmentController extends Controller
{
    private array $allowedExt = ['jpg', 'jpeg', 'png', 'webp', 'pdf', 'docx', 'xlsx', 'zip'];

    /**
     * 1. SIGNATURE ENDPOINT
     */
    public function signature(Request $req)
    {
        $data = $req->validate([
            'ticket_id' => ['required', 'integer', 'exists:tickets,ticket_id'],
            'filename' => ['required', 'string', 'max:255'],
            'bytes' => ['required', 'integer', 'min:1'],
        ]);

        // cek tiket
        $ticket = DB::table('tickets')->where('ticket_id', $data['ticket_id'])->first();
        abort_if(is_null($ticket), 404, 'Ticket not found');
        abort_if((int) $ticket->user_id !== Auth::id(), 403, 'Not allowed to attach');

        // validasi ekstensi
        $ext = strtolower(pathinfo($data['filename'], PATHINFO_EXTENSION));
        abort_unless(in_array($ext, $this->allowedExt, true), 422, 'Format file tidak diizinkan');

        // validasi ukuran per file
        $maxPerFile = (int) env('ATTACHMENTS_MAX_PER_FILE_MB', 10) * 1024 * 1024;
        abort_if((int) $data['bytes'] > $maxPerFile, 422, 'Ukuran file melebihi batas per file');

        // validasi kuota total
        $totalBytes = (int) DB::table('ticket_attachments')
            ->where('ticket_id', $data['ticket_id'])
            ->sum('bytes');
        $quotaBytes = (int) env('ATTACHMENTS_TOTAL_QUOTA_MB', 15) * 1024 * 1024;
        abort_if($totalBytes + (int) $data['bytes'] > $quotaBytes, 422, 'Kuota lampiran tiket terlampaui');

        // generate signature
        $cloudName = env('CLOUDINARY_CLOUD_NAME');
        $apiKey = env('CLOUDINARY_API_KEY');
        $apiSecret = env('CLOUDINARY_API_SECRET');
        $preset = env('CLOUDINARY_UPLOAD_PRESET', 'KRBS');
        $baseFolder = trim(env('CLOUDINARY_BASE_FOLDER', 'krbs/tickets'), '/');
        $timestamp = time();
        $folder = $baseFolder . '/' . $data['ticket_id'];

        $toSign = "folder={$folder}&timestamp={$timestamp}&upload_preset={$preset}";
        $signature = sha1($toSign . $apiSecret);

        return response()->json([
            'cloud_name' => $cloudName,
            'api_key' => $apiKey,
            'upload_preset' => $preset,
            'timestamp' => $timestamp,
            'folder' => $folder,
            'signature' => $signature,
            'max_per_file_mb' => (int) env('ATTACHMENTS_MAX_PER_FILE_MB', 10),
            'remaining_quota_mb' => round(($quotaBytes - $totalBytes) / 1024 / 1024, 2),
            'resource_type' => 'auto',
        ]);
    }

    /**
     * 2. STORE ATTACHMENT METADATA
     */
    public function store(Request $req)
    {
        $data = $req->validate([
            'ticket_id' => ['required', 'integer', 'exists:tickets,ticket_id'],
            'file_url' => ['required', 'url', 'max:1000'],
            'file_type' => ['nullable', 'string', 'max:100'],
            'cloudinary_public_id' => ['required', 'string', 'max:255'],
            'bytes' => ['required', 'integer', 'min:1'],
            'original_filename' => ['nullable', 'string', 'max:255'],
        ]);

        $ticket = DB::table('tickets')->where('ticket_id', $data['ticket_id'])->first();
        abort_if(is_null($ticket), 404);
        abort_if((int) $ticket->user_id !== Auth::id(), 403, 'Not allowed');

        // enforce kuota total
        $quotaBytes = (int) env('ATTACHMENTS_TOTAL_QUOTA_MB', 15) * 1024 * 1024;
        $totalBytes = (int) DB::table('ticket_attachments')
            ->where('ticket_id', $data['ticket_id'])
            ->sum('bytes');
        abort_if($totalBytes + (int) $data['bytes'] > $quotaBytes, 422, 'Kuota lampiran tiket terlampaui');

        $id = DB::table('ticket_attachments')->insertGetId([
            'ticket_id' => $data['ticket_id'],
            'file_url' => $data['file_url'],
            'file_type' => $data['file_type'] ?? null,
            'uploaded_by' => Auth::id(),
            'cloudinary_public_id' => $data['cloudinary_public_id'],
            'bytes' => $data['bytes'],
            'original_filename' => $data['original_filename'] ?? null,
            'created_at' => now(),
        ]);

        return response()->json(['id' => $id], 201);
    }

    /**
     * 3. LIST ATTACHMENTS PER TICKET
     */
    public function index($ticketId)
    {
        $ticket = DB::table('tickets')->where('ticket_id', $ticketId)->first();
        abort_if(is_null($ticket), 404);
        abort_if((int) $ticket->user_id !== Auth::id(), 403);

        $attachments = DB::table('ticket_attachments')
            ->select('attachment_id', 'file_url', 'file_type', 'original_filename', 'bytes', 'uploaded_by', 'created_at')
            ->where('ticket_id', $ticketId)
            ->orderByDesc('attachment_id')
            ->get();

        $quotaBytes = (int) env('ATTACHMENTS_TOTAL_QUOTA_MB', 15) * 1024 * 1024;
        $usedBytes = (int) DB::table('ticket_attachments')->where('ticket_id', $ticketId)->sum('bytes');

        return response()->json([
            'items' => $attachments,
            'quota' => [
                'used_bytes' => $usedBytes,
                'limit_bytes' => $quotaBytes,
                'remaining_mb' => round(($quotaBytes - $usedBytes) / 1024 / 1024, 2),
            ],
        ]);
    }

    /**
     * 4. DESTROY ATTACHMENT
     */
    public function destroy($attachmentId)
    {
        $att = DB::table('ticket_attachments')->where('attachment_id', $attachmentId)->first();
        abort_if(is_null($att), 404, 'Attachment not found');
        abort_if((int) $att->uploaded_by !== Auth::id(), 403, 'Not allowed');

        $cloudName = env('CLOUDINARY_CLOUD_NAME');
        $apiKey = env('CLOUDINARY_API_KEY');
        $apiSecret = env('CLOUDINARY_API_SECRET');

        $resourceType = $this->guessResourceType($att->file_type); // 'image' atau 'raw'

        // HAPUS di Cloudinary: gunakan public_ids[] (array)
        $endpoint = "https://api.cloudinary.com/v1_1/{$cloudName}/resources/{$resourceType}/upload";
        $resp = Http::withBasicAuth($apiKey, $apiSecret)
            ->asForm()
            ->delete($endpoint, [
                // Cloudinary menerima public_ids[] sebagai array
                'public_ids' => [$att->cloudinary_public_id],
            ]);

        // Kalau mau hati-hati, cek sukses dulu:
        // if (!$resp->successful()) {
        //     return response()->json(['deleted' => false, 'cloudinary_resp' => $resp->json()], 502);
        // }

        // HAPUS di DB
        DB::table('ticket_attachments')->where('attachment_id', $attachmentId)->delete();

        return response()->json([
            'deleted' => true,
            'cloudinary_resp' => $resp->json(),
        ]);
    }


    private function guessResourceType(?string $fileType): string
    {
        if (!$fileType)
            return 'auto';
        $ft = strtolower($fileType);
        if (str_starts_with($ft, 'image/'))
            return 'image';
        return 'raw'; // pdf/docx/xlsx/zip → raw
    }
}
