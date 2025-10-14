<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use Carbon\Carbon;
use DB;

// ====== Models ======
use App\Models\User;
use App\Models\Company;
use App\Models\Department;
use App\Models\Role;
use App\Models\Room;
use App\Models\Requirement;
use App\Models\BookingRoom;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\TicketAssignment;
use App\Models\TicketHistory;
use App\Models\TicketAttachment;
use App\Models\Guestbook;
use App\Models\Announcement;
use App\Models\Document;
use App\Models\Package;
use App\Models\Vehicle;
use App\Models\VehicleBooking;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $emailDomain = 'krbogor.id';

        // ===== Company =====
        $company = Company::updateOrCreate(
            ['company_name' => 'Kebun Raya Bogor'],
            [
                'company_address' => 'Jl. Ir. H. Juanda No.13, Bogor',
                'company_email'   => 'info@' . $emailDomain,
            ]
        );
        $companyId = $company->getKey();

        // ===== Roles =====
        $superAdminRole   = Role::firstOrCreate(['name' => 'Superadmin']);
        $adminRole        = Role::firstOrCreate(['name' => 'Admin']);
        $userRole         = Role::firstOrCreate(['name' => 'User']);
        $receptionistRole = Role::firstOrCreate(['name' => 'Receptionist']);

        // ===== Departments =====
        $departments = collect(['IT','Finance','HRD','Marketing','Sales','Operations','Legal','Visitor Services'])
            ->mapWithKeys(fn($d) => [$d => Department::firstOrCreate([
                'company_id' => $companyId,
                'department_name' => $d,
            ])]);

        // ===== Superadmin =====
        User::firstOrCreate(
            ['email' => "superadmin@{$emailDomain}"],
            [
                'company_id'     => $companyId,
                'department_id'  => null,
                'role_id'        => $superAdminRole->getKey(),
                'full_name'      => 'Superadmin User',
                'phone_number'   => '08000000000',
                'password'       => Hash::make('superpassword'),
                'remember_token' => Str::random(10),
            ]
        );

        // ===== Receptionist =====
        User::firstOrCreate(
            ['email' => "receptionist@{$emailDomain}"],
            [
                'company_id'     => $companyId,
                'department_id'  => null,
                'role_id'        => $receptionistRole->getKey(),
                'full_name'      => 'Receptionist User',
                'phone_number'   => '087812345678',
                'password'       => Hash::make('receppassword'),
                'remember_token' => Str::random(10),
            ]
        );

        // ===== Admin per Department =====
        foreach ($departments as $deptName => $deptModel) {
            $slug = Str::slug($deptName, '-');
            $email = "admin-{$slug}@{$emailDomain}";
            User::firstOrCreate(
                ['email' => $email],
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

        // ===== Banyak User (Faker) =====
        $this->command->info('Generating bulk fake users...');
        for ($i = 0; $i < 60; $i++) {
            User::firstOrCreate(
                ['email' => strtolower($faker->unique()->userName()) . "@{$emailDomain}"],
                [
                    'company_id'    => $companyId,
                    'department_id' => $departments->random()->getKey(),
                    'role_id'       => $userRole->getKey(),
                    'full_name'     => $faker->name(),
                    'phone_number'  => $faker->e164PhoneNumber(),
                    'password'      => Hash::make('password123'),
                    'remember_token'=> Str::random(10),
                ]
            );
        }

        $users = User::where('company_id', $companyId)->get();

        // ===== Rooms =====
        foreach (['Room 101','Room 202','Room 303','Aula Besar','Meeting A','Meeting B','Ruang Rapat 1','Ruang Rapat 2'] as $room) {
            Room::firstOrCreate(['room_number' => $room, 'company_id' => $companyId]);
        }

        // ===== Requirements =====
        foreach (['projector','whiteboard','video_conference','catering','microphone','stage','speaker'] as $req) {
            Requirement::firstOrCreate(['name' => $req, 'company_id' => $companyId]);
        }

        $rooms = Room::where('company_id', $companyId)->get();
        $reqs  = Requirement::where('company_id', $companyId)->get();

        // ===== Tickets =====
        $this->command->info('Generating tickets...');
        $priorities = ['low','medium','high'];
        $statuses   = ['OPEN','IN_PROGRESS','RESOLVED','CLOSED'];

        for ($t = 0; $t < 100; $t++) {
            $ticket = Ticket::create([
                'company_id'    => $companyId,
                'department_id' => $departments->random()->getKey(),
                'requestdept_id'=> $departments->random()->getKey(),
                'user_id'       => $users->random()->id,
                'subject'       => ucfirst($faker->words(3, true)),
                'description'   => $faker->sentence(10),
                'priority'      => $faker->randomElement($priorities),
                'status'        => $faker->randomElement($statuses),
                'created_at'    => Carbon::now()->subDays($faker->numberBetween(0, 90)),
            ]);

            // comments
            for ($i = 0; $i < $faker->numberBetween(0, 5); $i++) {
                TicketComment::create([
                    'ticket_id'   => $ticket->id,
                    'user_id'     => $users->random()->id,
                    'comment_text'=> $faker->sentence(),
                ]);
            }

            // history
            for ($i = 0; $i < $faker->numberBetween(1, 4); $i++) {
                TicketHistory::create([
                    'ticket_id' => $ticket->id,
                    'status'    => $faker->randomElement($statuses),
                    'changed_by'=> $users->random()->id,
                ]);
            }
        }

        // ===== Booking Rooms =====
        $this->command->info('Generating booking rooms...');
        for ($b = 0; $b < 80; $b++) {
            $room = $rooms->random();
            $user = $users->random();
            $dept = $departments->random();
            $date = Carbon::today()->addDays($faker->numberBetween(-20, 20));
            $start = $date->setTime($faker->numberBetween(8,16), 0);
            $end   = (clone $start)->addHours($faker->numberBetween(1,4));

            $booking = BookingRoom::create([
                'room_id'       => $room->id,
                'company_id'    => $companyId,
                'user_id'       => $user->id,
                'department_id' => $dept->getKey(),
                'meeting_title' => ucfirst($faker->words(3, true)),
                'date'          => $date->toDateString(),
                'number_of_attendees' => $faker->numberBetween(2, 150),
                'start_time'    => $start,
                'end_time'      => $end,
                'status'        => $faker->randomElement(['pending','approved','rejected']),
                'booking_type'  => $faker->randomElement(['meeting','online_meeting','hybrid']),
                'is_approve'    => $faker->boolean(60),
            ]);

            foreach ($reqs->random($faker->numberBetween(1,3)) as $r) {
                DB::table('booking_requirements')->insert([
                    'bookingroom_id' => $booking->id,
                    'requirement_id' => $r->id,
                ]);
            }
        }

        // ===== Guestbook =====
        $this->command->info('Generating guestbook...');
        for ($i = 0; $i < 50; $i++) {
            Guestbook::create([
                'company_id' => $companyId,
                'date'       => Carbon::now()->subDays($faker->numberBetween(0, 30)),
                'jam_in'     => $faker->time('H:i'),
                'jam_out'    => $faker->optional(0.6)->time('H:i'),
                'name'       => $faker->name(),
                'instansi'   => $faker->company(),
                'keperluan'  => $faker->randomElement(['Meeting','Survey','Tour']),
                'petugas_penjaga' => $users->random()->full_name,
            ]);
        }

        // ===== Announcements =====
        for ($i = 0; $i < 10; $i++) {
            Announcement::create([
                'company_id'  => $companyId,
                'description' => $faker->sentence(10),
                'event_at'    => Carbon::now()->addDays($faker->numberBetween(-10, 30)),
            ]);
        }

        // ===== Documents =====
        for ($i = 0; $i < 20; $i++) {
            Document::create([
                'company_id' => $companyId,
                'receptionist_id' => $users->first()->id,
                'document_name' => $faker->word(),
                'nama_pengirim' => $faker->name(),
                'nama_penerima' => $faker->name(),
                'type' => $faker->randomElement(['document','invoice']),
                'status' => $faker->randomElement(['pending','taken']),
            ]);
        }

        // ===== Vehicles =====
        foreach (['Kijang Innova','Avanza','Pickup X','Motor Dinas'] as $name) {
            Vehicle::firstOrCreate(['name' => $name, 'company_id' => $companyId], [
                'plate_number' => strtoupper($faker->bothify('B###??')),
                'category' => $faker->randomElement(['car','pickup','motorcycle']),
                'year' => (string)$faker->numberBetween(2010,2024),
                'is_active' => 1,
            ]);
        }

        $vehicles = Vehicle::where('company_id', $companyId)->get();

        // ===== Vehicle Bookings =====
        for ($i = 0; $i < 20; $i++) {
            $vehicle = $vehicles->random();
            $start = Carbon::now()->addDays($faker->numberBetween(-5,5))->setTime(8,0);
            $end   = (clone $start)->addHours($faker->numberBetween(1,8));

            VehicleBooking::create([
                'vehicle_id' => $vehicle->id,
                'company_id' => $companyId,
                'department_id' => $departments->random()->getKey(),
                'user_id' => $users->random()->id,
                'borrower_name' => $faker->name(),
                'start_at' => $start,
                'end_at' => $end,
                'status' => $faker->randomElement(['pending','approved','in_use','returned']),
                'purpose' => $faker->sentence(3),
                'terms_agreed' => 1,
                'is_approve' => $faker->boolean(80),
            ]);
        }
    }
}
