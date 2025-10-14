<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use App\Models\Department;
use App\Models\Role;
use App\Models\Room;
use App\Models\Requirement;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ====== Konfigurasi dasar ======
        $emailDomain = 'krbogor.id'; 

        // 1) Company: Kebun Raya Bogor
        $company = Company::updateOrCreate(
            ['company_name' => 'Kebun Raya Bogor'],
            [
                'company_address' => 'Jl. Ir. H. Juanda No.13, Bogor',
                'company_email'   => 'info@' . $emailDomain,
            ]
        );
        $companyId = $company->getKey();

        // 2) Roles
        $superAdminRole   = Role::firstOrCreate(['name' => 'Superadmin']);
        $adminRole        = Role::firstOrCreate(['name' => 'Admin']);
        $userRole         = Role::firstOrCreate(['name' => 'User']);
        $receptionistRole = Role::firstOrCreate(['name' => 'Receptionist']);

        // 3) Departments
        $departmentNames = [
            'IT',
            'Finance',
            'HRD',
            'Marketing',
            'Sales',
            'Operations',
        ];

        $departments = [];
        foreach ($departmentNames as $name) {
            $departments[$name] = Department::firstOrCreate([
                'company_id'      => $companyId,
                'department_name' => $name,
            ]);
        }

        // 4) Superadmin (tanpa department)
        User::firstOrCreate(
            ['email' => "superadmin@{$emailDomain}"],
            [
                'company_id'     => $companyId,
                'department_id'  => null, // tidak terikat department
                'role_id'        => $superAdminRole->getKey(),
                'full_name'      => 'Superadmin User',
                'phone_number'   => '08000000000',
                'password'       => Hash::make('superpassword'),
                'remember_token' => Str::random(10),
            ]
        );

        // 5) Receptionist (tanpa department)
        User::firstOrCreate(
            ['email' => "receptionist@{$emailDomain}"],
            [
                'company_id'     => $companyId,
                'department_id'  => null, // tidak terikat department
                'role_id'        => $receptionistRole->getKey(),
                'full_name'      => 'Receptionist User',
                'phone_number'   => '087812345678',
                'password'       => Hash::make('receppassword'),
                'remember_token' => Str::random(10),
            ]
        );

        // 6) Admin per department (email: admin-<slug-dept>@krbogor.id)
        foreach ($departments as $deptName => $deptModel) {
            $deptSlug   = Str::slug($deptName, '-'); // contoh: "IT" -> "it", "Human Resources" -> "human-resources"
            $adminEmail = "admin-{$deptSlug}@{$emailDomain}";

            User::firstOrCreate(
                ['email' => $adminEmail],
                [
                    'company_id'     => $companyId,
                    'department_id'  => $deptModel->getKey(),
                    'role_id'        => $adminRole->getKey(),
                    'full_name'      => "Admin - {$deptName}",
                    'phone_number'   => '081' . random_int(100000000, 999999999),
                    'password'       => Hash::make('password'),
                    'remember_token' => Str::random(10),
                ]
            );
        }

        // 7) Rooms
        Room::firstOrCreate(['room_number' => 'Room 101'], ['company_id' => $companyId]);
        Room::firstOrCreate(['room_number' => 'Room 202'], ['company_id' => $companyId]);
        Room::firstOrCreate(['room_number' => 'Room 303'], ['company_id' => $companyId]);

        // 8) Requirements
        foreach (['projector','whiteboard','video_conference','catering','other'] as $req) {
            Requirement::firstOrCreate(['name' => $req]);
        }
    }
}
