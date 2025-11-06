<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // <-- TAMBAHKAN INI

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Salin data yang ada dari users.department_id ke user_departments
        // Ini akan mengambil setiap baris di 'users' yang 'department_id'-nya tidak null
        // dan menyalinnya ke tabel pivot baru.
        DB::statement('
            INSERT INTO user_departments (user_id, department_id)
            SELECT user_id, department_id
            FROM users
            WHERE department_id IS NOT NULL
        ');

        // 2. Hapus kolom 'department_id' yang lama dari tabel 'users'
        Schema::table('users', function (Blueprint $table) {
            // Hapus foreign key dulu (berdasarkan file SQL Anda)
            $table->dropForeign('users_department_id_foreign');
            // Hapus kolomnya
            $table->dropColumn('department_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Jika migrasi di-rollback, buat kolomnya lagi
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('department_id')->nullable()->after('company_id');
            $table->foreign('department_id')->references('department_id')->on('departments')->onDelete('set null');
        });

        // (Catatan: Mengembalikan datanya ke kolom lama akan lebih rumit, 
        // jadi kita hanya memulihkan strukturnya saja di 'down')
    }
};