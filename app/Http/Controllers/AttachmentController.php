<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AttachmentController extends Controller
{
    /**
     * Upload file sementara ke local (public storage).
     * Body: multipart/form-data (file, tmp_key)
     */
    public function tempUpload(Request $req)
    {
        $req->validate([
            'file'    => ['required', 'file', 'max:10240', 'mimes:jpg,jpeg,png,webp,pdf,doc,docx,xlsx,zip'],
            'tmp_key' => ['required', 'string', 'max:120'],
        ]);

        $user = Auth::user();
        $tmpFolder = "tmp/{$user->id}/" . $req->tmp_key;

        $file = $req->file('file');
        $storedPath = $file->storeAs(
            "public/attachments/{$tmpFolder}",
            Str::random(10) . '_' . $file->getClientOriginalName()
        );

        $url = Storage::url($storedPath);

        return response()->json([
            'public_id' => $storedPath,
            'secure_url' => $url,
            'bytes' => $file->getSize(),
            'resource_type' => 'raw',
            'format' => $file->getClientOriginalExtension(),
            'original_filename' => $file->getClientOriginalName(),
        ]);
    }

    /**
     * Hapus file sementara (opsional).
     */
    public function deleteTemp(Request $req)
    {
        $req->validate([
            'public_id' => ['required', 'string'],
        ]);

        if (Storage::exists($req->public_id)) {
            Storage::delete($req->public_id);
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Finalize: pindahkan dari tmp ke folder final per ticket, insert DB.
     */
    public function finalizeTemp(Request $req)
    {
        $req->validate([
            'ticket_id' => ['required', 'integer', 'exists:tickets,ticket_id'],
            'items' => ['required', 'array'],
            'items.*.public_id' => ['required', 'string'],
            'items.*.secure_url' => ['required', 'string'],
            'items.*.bytes' => ['required', 'integer'],
            'items.*.format' => ['nullable', 'string'],
            'items.*.original_filename' => ['nullable', 'string'],
        ]);

        $user = Auth::user();
        $ticketId = $req->ticket_id;
        $finalFolder = "public/attachments/tickets/{$ticketId}";
        Storage::makeDirectory($finalFolder);

        foreach ($req->items as $item) {
            $src = $item['public_id'];
            if (!Storage::exists($src)) {
                Log::warning('Temp file not found', ['src' => $src]);
                continue;
            }

            $filename = basename($src);
            $dest = "{$finalFolder}/{$filename}";

            // move file dari tmp ke folder ticket
            Storage::move($src, $dest);
            $url = Storage::url($dest);

            DB::table('ticket_attachments')->insert([
                'ticket_id' => $ticketId,
                'file_url' => $url,
                'file_type' => $item['format'] ?? null,
                'uploaded_by' => $user->id,
                'cloudinary_public_id' => null,
                'bytes' => $item['bytes'],
                'original_filename' => $item['original_filename'] ?? $filename,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json(['ok' => true]);
    }
}
