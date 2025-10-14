<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Carbon\Carbon;

// ====== MODELS (ensure all are imported) ======
use App\Models\Announcement;
use App\Models\BookingRoom;
use App\Models\Company;
use App\Models\Department;
use App\Models\Guestbook;
use App\Models\Information;
use App\Models\Requirement;
use App\Models\Role;
use App\Models\Room;
use App\Models\Ticket;
use App\Models\TicketAssignment;
use App\Models\TicketAttachment;
use App\Models\TicketComment;
use App\Models\TicketHistory;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleBooking;
use App\Models\Storage;
use App\Models\Delivery;
use App\Models\BookingRequirement; // Pivot model might not be needed if using attach()

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // Use a consistent reference date
            $now = Carbon::parse('2025-10-14 10:00:00');
            $emailDomain = 'krbogor.id';

            // ===== 1. CORE DATA (Companies, Roles, Departments) =====
            $companyMain = Company::updateOrCreate(
                ['company_name' => 'Kebun Raya Bogor'],
                ['company_address' => 'Jl. Ir. H. Juanda No.13, Bogor', 'company_email' => 'info@' . $emailDomain]
            );
            $companyId = $companyMain->getKey();

            $roleNames = ['Superadmin', 'Admin', 'User', 'Receptionist'];
            $roles = [];
            foreach ($roleNames as $r) {
                $roles[$r] = Role::firstOrCreate(['name' => $r]);
            }

            $deptNames = ['IT', 'Finance', 'HRD', 'Marketing', 'Operations', 'General Affairs'];
            $depts = [];
            foreach ($deptNames as $d) {
                $depts[$d] = Department::firstOrCreate(['company_id' => $companyId, 'department_name' => $d]);
            }

            // ===== 2. USERS (Create a variety of users) =====
            $allUsers = collect(); // To store all created users for random picking

            $superadmin = User::firstOrCreate(
                ['email' => "superadmin@{$emailDomain}"],
                ['company_id' => $companyId, 'department_id' => null, 'role_id' => $roles['Superadmin']->getKey(), 'full_name' => 'Superadmin User', 'phone_number' => '08000000000', 'password' => Hash::make('superpassword')]
            );
            $allUsers->push($superadmin);

            $receptionist = User::firstOrCreate(
                ['email' => "receptionist@{$emailDomain}"],
                ['company_id' => $companyId, 'department_id' => $depts['General Affairs']->getKey(), 'role_id' => $roles['Receptionist']->getKey(), 'full_name' => 'Receptionist User', 'phone_number' => '087812345678', 'password' => Hash::make('receppassword')]
            );
            $allUsers->push($receptionist);

            $admins = collect();
            foreach ($depts as $name => $dept) {
                $slug = Str::slug($name);
                $adminUser = User::firstOrCreate(
                    ['email' => "admin-{$slug}@{$emailDomain}"],
                    ['company_id' => $companyId, 'department_id' => $dept->getKey(), 'role_id' => $roles['Admin']->getKey(), 'full_name' => "Admin {$name}", 'phone_number' => '081' . random_int(100000000, 999999999), 'password' => Hash::make('password')]
                );
                $admins->push($adminUser);
                $allUsers->push($adminUser);
            }

            $userList = [
                ['Budi Santoso', 'IT', $roles['User']], ['Citra Lestari', 'Finance', $roles['User']],
                ['Dewi Anggraini', 'HRD', $roles['User']], ['Eko Prasetyo', 'Operations', $roles['User']],
                ['Fajar Nugroho', 'Marketing', $roles['User']], ['Gita Permata', 'IT', $roles['User']],
            ];

            foreach ($userList as [$name, $deptName, $role]) {
                $slug = Str::slug($name);
                $user = User::firstOrCreate(
                    ['email' => "{$slug}@{$emailDomain}"],
                    ['company_id' => $companyId, 'department_id' => $depts[$deptName]->getKey(), 'role_id' => $role->getKey(), 'full_name' => $name, 'phone_number' => '085' . random_int(100000000, 999999999), 'password' => Hash::make('password')]
                );
                $allUsers->push($user);
            }

            // ===== 3. ASSETS (Rooms, Requirements, Vehicles, Storages) =====
            $rooms = collect();
            foreach (['Garuda', 'Merak', 'Cendrawasih', 'Nuri', 'Elang'] as $r) {
                $rooms->push(Room::firstOrCreate(['company_id' => $companyId, 'room_number' => "Ruang {$r}"]));
            }

            $requirements = collect();
            foreach (['Projector & Screen', 'Whiteboard & Markers', 'Video Conference Set', 'Coffee Break', 'Lunch Catering'] as $req) {
                $requirements->push(Requirement::firstOrCreate(['company_id' => $companyId, 'name' => $req]));
            }

            $storages = collect();
            foreach ([['S-01', 'Rak Dokumen A'], ['S-02', 'Loker Paket B'], ['S-03', 'Lemari C Receptionist']] as [$code, $name]) {
                $storages->push(Storage::firstOrCreate(['company_id' => $companyId, 'code' => $code], ['name' => $name]));
            }

            $vehicles = collect();
            foreach ([['Avanza', 'car', 'B 1234 ABC', '2022'], ['Innova Reborn', 'car', 'B 5678 DEF', '2021'], ['Carry PickUp', 'pickup', 'F 9876 XZ', '2019'], ['Honda Vario', 'motorcycle', 'F 5555 KL', '2023']] as [$name, $type, $plate, $year]) {
                $vehicles->push(Vehicle::firstOrCreate(['plate_number' => $plate], ['company_id' => $companyId, 'name' => $name, 'category' => $type, 'year' => $year]));
            }

            // ===== 4. GENERAL ENTRIES (Announcements, Info, Guestbook, Deliveries) =====
            for ($i = 1; $i <= 5; $i++) {
                Announcement::create(['company_id' => $companyId, 'description' => "Pengumuman Penting #{$i}: Acara akan diadakan minggu depan.", 'event_at' => $now->copy()->addDays($i + 5)]);
                Information::create(['company_id' => $companyId, 'department_id' => $depts[Arr::random($deptNames)]->getKey(), 'description' => "Informasi Dept #{$i}: Mohon perbarui data Anda.", 'event_at' => $now->copy()->addDays($i)]);
                Guestbook::create(['company_id' => $companyId, 'date' => $now->copy()->subDays(rand(1, 10)), 'jam_in' => '09:30:00', 'jam_out' => '11:00:00', 'name' => "Tamu Ke-{$i}", 'instansi' => "Perusahaan {$i}", 'keperluan' => "Meeting dengan Dept. Marketing", 'petugas_penjaga' => 'Security A']);
            }
            
            for ($i = 1; $i <= 10; $i++) {
                $status = Arr::random(['pending', 'stored', 'taken', 'delivered']);
                Delivery::create(['company_id' => $companyId, 'receptionist_id' => $receptionist->getKey(), 'item_name' => ($i % 2 == 0 ? 'Paket' : 'Dokumen') . " #{$i}", 'type' => $i % 2 == 0 ? 'package' : 'document', 'nama_pengirim' => "Kurir Express", 'nama_penerima' => $allUsers->random()->full_name, 'storage_id' => in_array($status, ['stored', 'taken']) ? $storages->random()->getKey() : null, 'status' => $status, 'pengiriman' => $now->copy()->subHours(rand(1, 48)), 'pengambilan' => $status == 'taken' ? $now->copy()->subHours(rand(1, 5)) : null]);
            }

            // ===== 5. BOOKING ROOMS & REQUIREMENTS =====
            for ($i = 0; $i < 15; $i++) {
                $booker = $allUsers->where('role_id', '!=', $roles['Superadmin']->getKey())->random();
                $adminApprover = $admins->random();
                $startDate = $now->copy()->addDays(rand(-5, 15))->hour(rand(9, 15))->minute(0)->second(0);
                $status = Arr::random(['pending', 'approved', 'rejected']);

                $booking = BookingRoom::create([
                    'room_id' => $rooms->random()->getKey(),
                    'company_id' => $companyId,
                    'user_id' => $booker->getKey(),
                    'department_id' => $booker->department_id,
                    'meeting_title' => "Rapat Proyek " . Str::ucfirst(Str::random(8)),
                    'date' => $startDate->toDateString(),
                    'number_of_attendees' => rand(5, 20),
                    'start_time' => $startDate,
                    'end_time' => $startDate->copy()->addHours(rand(1, 3)),
                    'status' => $status,
                    'is_approve' => $status == 'approved' ? 1 : 0,
                    'approved_by' => $status != 'pending' ? $adminApprover->getKey() : null,
                    'booking_type' => 'meeting',
                ]);

                // Attach 1 to 3 random requirements
                $booking->requirements()->attach($requirements->random(rand(1, 3))->pluck('requirement_id'));
            }

            // ===== 6. VEHICLE BOOKINGS =====
            for ($i = 0; $i < 15; $i++) {
                $booker = $allUsers->where('department_id', '!=', null)->random();
                $startDate = $now->copy()->addDays(rand(-2, 10))->hour(rand(8, 16));
                $status = Arr::random(['pending', 'approved', 'in_use', 'returned', 'rejected', 'cancelled']);

                VehicleBooking::create([
                    'vehicle_id' => $vehicles->random()->getKey(),
                    'company_id' => $companyId,
                    'department_id' => $booker->department_id,
                    'user_id' => $booker->getKey(),
                    'borrower_name' => $booker->full_name,
                    'start_at' => $startDate,
                    'end_at' => $startDate->copy()->addHours(rand(2, 8)),
                    'purpose' => "Perjalanan dinas ke klien",
                    'destination' => "Jakarta Pusat",
                    'status' => $status,
                    'is_approve' => $status == 'approved' ? 1 : 0,
                    'terms_agreed' => 1,
                    'purpose_type' => 'dinas'
                ]);
            }

            // ===== 7. TICKETING SYSTEM (Tickets, History, Comments, Assignments, Attachments) =====
            $ticketPriorities = ['low', 'medium', 'high'];
            $ticketStatuses = ['OPEN', 'IN_PROGRESS', 'RESOLVED', 'CLOSED'];

            for ($i = 1; $i <= 20; $i++) {
                // THIS IS THE CORRECTED LINE
                $requester = $allUsers->whereNotNull('department_id')->where('department_id', '!=', $depts['IT']->getKey())->random();
                
                $it_admin = $allUsers->where('department_id', $depts['IT']->getKey())->random();
                $status = Arr::random($ticketStatuses);

                // Create Ticket
                $ticket = Ticket::create([
                    'company_id' => $companyId,
                    'department_id' => $depts['IT']->getKey(),
                    'requestdept_id' => $requester->department_id,
                    'user_id' => $requester->getKey(),
                    'subject' => "Masalah Printer di Lantai " . rand(2, 5),
                    'description' => "Printer tidak bisa mencetak, lampu indikator berkedip merah. Sudah coba restart tapi tidak berhasil.",
                    'priority' => Arr::random($ticketPriorities),
                    'status' => $status,
                ]);

                // Create Ticket History
                TicketHistory::create(['ticket_id' => $ticket->getKey(), 'status' => 'OPEN', 'changed_by' => $requester->getKey(), 'created_at' => $now->copy()->subMinutes(60)]);
                if (in_array($status, ['IN_PROGRESS', 'RESOLVED', 'CLOSED'])) {
                    TicketHistory::create(['ticket_id' => $ticket->getKey(), 'status' => 'IN_PROGRESS', 'changed_by' => $it_admin->getKey(), 'created_at' => $now->copy()->subMinutes(30)]);
                }
                if (in_array($status, ['RESOLVED', 'CLOSED'])) {
                    TicketHistory::create(['ticket_id' => $ticket->getKey(), 'status' => 'RESOLVED', 'changed_by' => $it_admin->getKey(), 'created_at' => $now->copy()->subMinutes(10)]);
                }

                // Create Ticket Assignment
                TicketAssignment::create(['ticket_id' => $ticket->getKey(), 'user_id' => $it_admin->getKey()]);

                // Create Ticket Comments
                TicketComment::create(['ticket_id' => $ticket->getKey(), 'user_id' => $requester->getKey(), 'comment_text' => "Mohon bantuannya untuk segera diperbaiki, terima kasih.", 'created_at' => $now->copy()->subMinutes(59)]);
                if ($status != 'OPEN') {
                    TicketComment::create(['ticket_id' => $ticket->getKey(), 'user_id' => $it_admin->getKey(), 'comment_text' => "Baik, tim kami sedang menuju ke lokasi untuk pengecekan.", 'created_at' => $now->copy()->subMinutes(25)]);
                }
                
                // Create Ticket Attachment
                TicketAttachment::create([
                    'ticket_id' => $ticket->getKey(),
                    'file_url' => 'https://via.placeholder.com/150/FF0000/FFFFFF?text=ErrorScreen.jpg',
                    'file_type' => 'image/jpeg',
                    'uploaded_by' => $requester->getKey(),
                    'original_filename' => 'error_printer.jpg'
                ]);
            }
        });
    }
}