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
        // 1) Company
        $company = Company::updateOrCreate(
            ['company_name' => 'Tech Corp'], // dicari berdasarkan nama
            [
                'company_address' => 'Jl. Mawar No. 123',
                'company_email'   => 'info@techcorp.com',
            ]
        );

        $companyId = $company->getKey();

        // 2) Department
        $dept = Department::firstOrCreate(
            [
                'company_id'      => $companyId,
                'department_name' => 'IT Department',
            ]
        );

        // 3) Roles
        $superAdminRole   = Role::firstOrCreate(['name' => 'Superadmin']);
        $adminRole        = Role::firstOrCreate(['name' => 'Admin']);
        $userRole         = Role::firstOrCreate(['name' => 'User']);
        $receptionistRole = Role::firstOrCreate(['name' => 'Receptionist']);

        // 4) Users
        User::firstOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'company_id'     => $companyId,
                'department_id'  => $dept->getKey(),
                'role_id'        => $superAdminRole->getKey(),
                'full_name'      => 'Superadmin User',
                'phone_number'   => '08000000000',
                'password'       => Hash::make('superpassword'),
                'remember_token' => Str::random(10),
            ]
        );

        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'company_id'     => $companyId,
                'department_id'  => $dept->getKey(),
                'role_id'        => $adminRole->getKey(),
                'full_name'      => 'Admin User',
                'phone_number'   => '08123456789',
                'password'       => Hash::make('password'),
                'remember_token' => Str::random(10),
            ]
        );

        User::firstOrCreate(
            ['email' => 'user@gmail.com'],
            [
                'company_id'     => $companyId,
                'department_id'  => $dept->getKey(),
                'role_id'        => $userRole->getKey(),
                'full_name'      => 'Regular User',
                'phone_number'   => '08987654321',
                'password'       => Hash::make('password'),
                'remember_token' => Str::random(10),
            ]
        );

        User::firstOrCreate(
            ['email' => 'receptionist@gmail.com'],
            [
                'company_id'     => $companyId,
                'department_id'  => $dept->getKey(),
                'role_id'        => $receptionistRole->getKey(),
                'full_name'      => 'Receptionist User',
                'phone_number'   => '087812345678',
                'password'       => Hash::make('receppassword'),
                'remember_token' => Str::random(10),
            ]
        );

        // 5) Rooms (contoh 3 ruangan)
        Room::firstOrCreate(
            ['room_number' => 'Room 101'],
            ['company_id'  => $companyId]
        );
        Room::firstOrCreate(
            ['room_number' => 'Room 202'],
            ['company_id'  => $companyId]
        );
        Room::firstOrCreate(
            ['room_number' => 'Room 303'],
            ['company_id'  => $companyId]
        );

        // 6) Requirements (buat checklist form)
        foreach (['projector','whiteboard','video_conference','catering','other'] as $req) {
            Requirement::firstOrCreate(['name' => $req]);
        }
    }
}
