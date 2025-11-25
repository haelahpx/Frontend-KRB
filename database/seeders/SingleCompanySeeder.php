<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\{
    Company,
    Department,
    Role,
    User,
    Room,
    Requirement,
    Storage,
    Vehicle,
    Wifi
};

class SingleCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define the details for the single company to be seeded
        $companyName = 'Mandiri Sejahtera';
        $domain = 'mandiri-sejahtera.com';
        $imageUrl = 'https://picsum.photos/400/300?random=1';
        $now = Carbon::now();

        DB::transaction(function () use ($companyName, $domain, $imageUrl, $now) {
            echo "\n🏢 Seeding single company: {$companyName}...\n";

            // --- COMPANY CREATION ---
            $company = Company::firstOrCreate(
                ['company_name' => $companyName],
                [
                    'company_address' => "Jl. Utama {$companyName} No. 1",
                    'company_email' => "info@{$domain}",
                    'image' => $imageUrl,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            $companyId = $company->company_id;

            // --- ROLES ---
            $roles = [];
            $roleNames = ['Superadmin', 'Admin', 'User', 'Receptionist'];
            foreach ($roleNames as $r) {
                $roles[$r] = Role::firstOrCreate(['name' => $r]);
            }

            // --- DEPARTMENTS ---
            $deptNames = [
                'IT','Finance','HRD','Marketing','Operations',
                'General Affairs','Executive','Administration'
            ];
            $depts = [];
            foreach ($deptNames as $d) {
                $depts[$d] = Department::firstOrCreate([
                    'company_id' => $companyId,
                    'department_name' => $d,
                ], [
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            // --- SUPERADMIN USER ---
            $superadminEmail = "superadmin@{$domain}";
            $superadmin = User::firstOrCreate(
                ['email' => $superadminEmail],
                [
                    'company_id' => $companyId,
                    'department_id' => $depts['Executive']->department_id,
                    'role_id' => $roles['Superadmin']->role_id,
                    'full_name' => "Superadmin {$companyName}",
                    'phone_number' => '0801' . random_int(10000000, 99999999),
                    'password' => Hash::make('superpassword'),
                    'is_agent' => 'no',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
            echo "  ✅ Superadmin User: {$superadmin->email} (superpassword)\n";

            // --- RECEPTIONIST USER ---
            $receptionistEmail = "receptionist@{$domain}";
            $receptionist = User::firstOrCreate(
                ['email' => $receptionistEmail],
                [
                    'company_id' => $companyId,
                    'department_id' => $depts['Administration']->department_id,
                    'role_id' => $roles['Receptionist']->role_id,
                    'full_name' => "Receptionist {$companyName}",
                    'phone_number' => '0878' . random_int(10000000, 99999999),
                    'password' => Hash::make('receppassword'),
                    'is_agent' => 'no',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            // --- GENERAL USER (Example) ---
            $generalUserEmail = "user@{$domain}";
            $generalUser = User::firstOrCreate(
                ['email' => $generalUserEmail],
                [
                    'company_id' => $companyId,
                    'department_id' => $depts['IT']->department_id,
                    'role_id' => $roles['User']->role_id,
                    'full_name' => "General User {$companyName}",
                    'phone_number' => '085' . random_int(100000000, 999999999),
                    'password' => Hash::make('password'),
                    'is_agent' => 'no',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
            echo "  ✅ General User: {$generalUser->email} (password)\n";

            // --- ROOMS ---
            $roomNames = ['Rapat Kecil', 'Rapat Besar', 'Auditorium'];
            foreach ($roomNames as $r) {
                Room::firstOrCreate([
                    'company_id'=>$companyId,
                    'room_name'=>"Ruang {$r}"
                ], [
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
            echo "  ✅ Seeded Rooms\n";

            // --- REQUIREMENTS (for Rooms) ---
            $requirementNames = ['Projector & Screen','Whiteboard','Coffee Break','Lunch Set'];
            foreach ($requirementNames as $req) {
                Requirement::firstOrCreate(['company_id'=>$companyId,'name'=>$req], [
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
            echo "  ✅ Seeded Requirements\n";

            // --- STORAGES ---
            $storages = [['A-01','Dokumen Aktif'],['A-02','Peralatan Kantor']];
            foreach ($storages as [$code,$name]) {
                Storage::firstOrCreate(['company_id'=>$companyId,'code'=>$code],['name'=>$name], [
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
            echo "  ✅ Seeded Storages\n";

            // --- VEHICLES ---
            $vehiclesList = [
                ['Toyota Avanza','car',2022],
                ['Honda Mobilio','car',2020],
                ['Yamaha NMAX','motorcycle',2023]
            ];
            foreach ($vehiclesList as [$name,$type,$year]) {
                $plate = 'D ' . rand(1000,9999) . ' ' . Str::upper(Str::random(3));
                Vehicle::firstOrCreate(
                    ['plate_number'=>$plate],
                    [
                        'company_id'=>$companyId,
                        'name'=>$name,
                        'category'=>$type,
                        'year'=>$year,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
            }
            echo "  ✅ Seeded Vehicles\n";

            // --- WIFI ---
            Wifi::firstOrCreate(
                ['company_id' => $companyId, 'ssid' => "{$companyName}_GUEST"],
                [
                    'password' => 'GuestWifi@2025',
                    'location' => 'Lobby Utama',
                    'is_active' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
            Wifi::firstOrCreate(
                ['company_id' => $companyId, 'ssid' => "{$companyName}_STAFF"],
                [
                    'password' => 'StaffPass@2025',
                    'location' => 'Area Kerja',
                    'is_active' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
            echo "  ✅ Seeded Wi-Fi Access Points\n";
        });
    }
}