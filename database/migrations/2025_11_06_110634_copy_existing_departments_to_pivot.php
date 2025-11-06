<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Salin semua departemen yang ada di tabel user
        // ke dalam tabel pivot yang baru.
        // Ini tidak menghapus data lama.
        DB::statement('
        INSERT INTO user_departments (user_id, department_id)
        SELECT user_id, department_id
        FROM users
        WHERE department_id IS NOT NULL
        ON DUPLICATE KEY UPDATE user_id = users.user_id
    ');
        // 'ON DUPLICATE KEY' agar migrasi ini aman dijalankan berkali-kali
    }

    public function down(): void
    {
        // Jika di-rollback, kita bisa kosongkan pivotnya
        DB::table('user_departments')->truncate();
    }
};
