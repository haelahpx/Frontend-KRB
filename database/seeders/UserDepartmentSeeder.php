<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
// Kita tidak perlu Department, cukup ID-nya
// use App\Models\Department;

class UserDepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Kosongkan tabel pivot agar kita bisa menjalankannya berulang kali
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('user_departments')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Ambil User dan Department yang kita butuhkan
        // Berdasarkan file .sql Anda:
        $budi = User::find(3);     // admin-it@krbogor.id. Departemen Utama = 1 (IT)
        $deni = User::find(15);    // bambang-wijaya-1@krbogor.id. Departemen Utama = 10 (Legal)
        
        $itDeptId = 1;     // ID 1 = IT
        $financeDeptId = 2; // ID 2 = Finance

        // 3. Gunakan sync() untuk memasang relasi
        
        // --- KASUS 1: "Budi" (ID 3) ---
        if ($budi) {
            // Departemen Utama Budi adalah 1 (IT). Dia tidak punya akses tambahan.
            $primaryDept = $budi->department_id; // Hasil: 1
            $additionalDepts = [];

            // Gabungkan dept utama + tambahan. array_unique() untuk jaga-jaga.
            $allDepts = array_unique(array_merge([$primaryDept], $additionalDepts)); // Hasil: [1]
            
            $budi->departments()->sync($allDepts);
            $this->command->info('User "Budi" (ID 3) [Primary: ' . $primaryDept . '] disinkronkan ke pivot. Total akses: ' . count($allDepts));
        }

        // --- KASUS 2: "Deni" (Bambang, ID 15) ---
        if ($deni) {
            // Departemen Utama Deni adalah 10 (Legal). 
            // Kita beri dia akses tambahan ke 1 (IT) dan 2 (Finance).
            $primaryDept = $deni->department_id; // Hasil: 10
            $additionalDepts = [$itDeptId, $financeDeptId]; // Hasil: [1, 2]

            // Gabungkan dept utama + tambahan.
            $allDepts = array_unique(array_merge([$primaryDept], $additionalDepts)); // Hasil: [10, 1, 2]
            
            // Sync semua ID ini ke tabel pivot
            $deni->departments()->sync($allDepts);
            $this->command->info('User "Deni" (ID 15) [Primary: ' . $primaryDept . '] disinkronkan ke pivot. Total akses: ' . count($allDepts));
        }

        // Cek jika user tidak ditemukan
        if (!$budi || !$deni) {
            $this->command->error('User ID 3 atau 15 tidak ditemukan. Pastikan Anda sudah seed data user utama.');
        }
    }
}