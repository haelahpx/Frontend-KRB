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
// use App\Models\BookingRequirement; // Pivot model might not be needed if using attach()

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // Use a consistent reference date as "today"
            $now = Carbon::parse('2025-10-14 10:00:00');
            // Define the 5-year historical window
            $totalDaysIn5Years = 5 * 365; // Approx. 1825 days
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

            // Added more departments
            $deptNames = ['IT', 'Finance', 'HRD', 'Marketing', 'Operations', 'General Affairs', 'Executive', 'Research & Development', 'Customer Support', 'Legal', 'Security', 'Maintenance'];
            $depts = [];
            foreach ($deptNames as $d) {
                $depts[$d] = Department::firstOrCreate(['company_id' => $companyId, 'department_name' => $d]);
            }

            // ===== 2. USERS (Create a variety of users) =====
            $allUsers = collect(); // To store all created users for random picking

            $superadmin = User::firstOrCreate(
                ['email' => "superadmin@{$emailDomain}"],
                ['company_id' => $companyId, 'department_id' => $depts['Executive']->getKey(), 'role_id' => $roles['Superadmin']->getKey(), 'full_name' => 'Superadmin User', 'phone_number' => '08000000000', 'password' => Hash::make('superpassword')]
            );
            $allUsers->push($superadmin);

            $receptionist = User::firstOrCreate(
                ['email' => "receptionist@{$emailDomain}"],
                ['company_id' => $companyId, 'department_id' => $depts['General Affairs']->getKey(), 'role_id' => $roles['Receptionist']->getKey(), 'full_name' => 'Receptionist User', 'phone_number' => '087812345678', 'password' => Hash::make('receppassword')]
            );
            $allUsers->push($receptionist);

            // Create a dedicated Security User for Guestbook
            $securityUser = User::firstOrCreate(
                ['email' => "security@{$emailDomain}"],
                ['company_id' => $companyId, 'department_id' => $depts['Security']->getKey(), 'role_id' => $roles['User']->getKey(), 'full_name' => 'Security Staff', 'phone_number' => '081299991111', 'password' => Hash::make('password')]
            );
            $allUsers->push($securityUser);


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

            // Expanded user list
            $userList = [
                ['Budi Santoso', 'IT', $roles['User']],
                ['Citra Lestari', 'Finance', $roles['User']],
                ['Dewi Anggraini', 'HRD', $roles['User']],
                ['Eko Prasetyo', 'Operations', $roles['User']],
                ['Fajar Nugroho', 'Marketing', $roles['User']],
                ['Gita Permata', 'IT', $roles['User']],
                ['Hadi Wibowo', 'Research & Development', $roles['User']],
                ['Indah Kusuma', 'Customer Support', $roles['User']],
                ['Joko Susilo', 'Legal', $roles['User']],
                ['Kania Dewi', 'Finance', $roles['User']],
                ['Lutfi Hakim', 'Operations', $roles['User']],
                ['Mega Utami', 'HRD', $roles['User']],
            ];

            foreach ($userList as [$name, $deptName, $role]) {
                $slug = Str::slug($name);
                $user = User::firstOrCreate(
                    ['email' => "{$slug}@{$emailDomain}"],
                    ['company_id' => $companyId, 'department_id' => $depts[$deptName]->getKey(), 'role_id' => $role->getKey(), 'full_name' => $name, 'phone_number' => '085' . random_int(100000000, 999999999), 'password' => Hash::make('password')]
                );
                $allUsers->push($user);
            }

            // --- ADD MORE RANDOM USERS (NOW 200) ---
            $firstNames = ['Agus', 'Bambang', 'Cici', 'Dedi', 'Endang', 'Fajar', 'Gita', 'Hadi', 'Indah', 'Joko', 'Kartika', 'Lina', 'Mega', 'Nina', 'Oscar', 'Putra', 'Qori', 'Rian', 'Sari', 'Tono', 'Umar', 'Vina', 'Wati', 'Yoga', 'Zul'];
            $lastNames = ['Susanto', 'Wijaya', 'Permata', 'Nugroho', 'Pratama', 'Wibowo', 'Hidayat', 'Kusuma', 'Lestari', 'Setiawan'];

            for ($i = 1; $i <= 200; $i++) {
                $name = Arr::random($firstNames) . ' ' . Arr::random($lastNames);
                $slug = Str::slug($name) . "-{$i}";

                // ====== THIS IS THE FIX ======
                $dept = Arr::random($depts); // Get a random department model from the array
                // =============================

                $user = User::firstOrCreate(
                    ['email' => "{$slug}@{$emailDomain}"],
                    [
                        'company_id' => $companyId,
                        'department_id' => $dept->getKey(),
                        'role_id' => $roles['User']->getKey(),
                        'full_name' => $name,
                        'phone_number' => '089' . random_int(100000000, 999999999),
                        'password' => Hash::make('password')
                    ]
                );
                $allUsers->push($user);
            }

            // ===== 3. ASSETS (Rooms, Requirements, Vehicles, Storages) =====
            $rooms = collect();
            // Added more rooms
            foreach (['Garuda', 'Merak', 'Cendrawasih', 'Nuri', 'Elang', 'Kakatua', 'Merpati', 'Rajawali', 'Podium Utama', 'Aula', 'Meeting Room A1', 'Meeting Room A2', 'Focus Booth 1', 'Focus Booth 2'] as $r) {
                $rooms->push(Room::firstOrCreate(['company_id' => $companyId, 'room_number' => "Ruang {$r}"]));
            }

            $requirements = collect();
            // Added more requirements
            foreach (['Projector & Screen', 'Whiteboard & Markers', 'Video Conference Set', 'Coffee Break', 'Lunch Catering', 'Sound System (Mic & Speaker)', 'Notepad & Pens', 'Mineral Water'] as $req) {
                $requirements->push(Requirement::firstOrCreate(['company_id' => $companyId, 'name' => $req]));
            }

            $storages = collect();
            // Added more storages
            foreach ([['S-01', 'Rak Dokumen A'], ['S-02', 'Loker Paket B'], ['S-03', 'Lemari C Receptionist'], ['S-04', 'Gudang ATK'], ['S-05', 'Loker Tamu']] as [$code, $name]) {
                $storages->push(Storage::firstOrCreate(['company_id' => $companyId, 'code' => $code], ['name' => $name]));
            }

            $vehicles = collect();
            // Added more vehicles
            foreach ([
                ['Avanza', 'car', 'B 1234 ABC', '2022'],
                ['Innova Reborn', 'car', 'B 5678 DEF', '2021'],
                ['Carry PickUp', 'pickup', 'F 9876 XZ', '2019'],
                ['Honda Vario', 'motorcycle', 'F 5555 KL', '2023'],
                ['HiAce Commuter', 'car', 'B 7777 GHI', '2020'],
                ['Pajero Sport', 'car', 'F 1111 JKL', '2023'],
                ['Honda Beat', 'motorcycle', 'F 2222 MNO', '2022'],
                ['Grand Max', 'pickup', 'F 3333 PQR', '2020'],
                ['Yamaha NMax', 'motorcycle', 'F 4444 STU', '2023']
            ] as [$name, $type, $plate, $year]) {
                $vehicles->push(Vehicle::firstOrCreate(['plate_number' => $plate], ['company_id' => $companyId, 'name' => $name, 'category' => $type, 'year' => $year]));
            }

            // ===== 4. GENERAL ENTRIES (Announcements, Info, Guestbook, Deliveries) =====

            // --- Create 150 Announcements, Info, and Guestbook entries over 5 years ---
            for ($i = 1; $i <= 150; $i++) {
                // Create a random date within the last 5 years
                $randomCreationDate = $now->copy()->subDays(rand(0, $totalDaysIn5Years))->subHours(rand(1, 24));
                $randomEventDate = $randomCreationDate->copy()->addDays(rand(5, 30)); // Event happens after creation

                Announcement::create([
                    'company_id' => $companyId,
                    'description' => "Pengumuman Penting #{$i}: Acara akan diadakan.",
                    'event_at' => $randomEventDate,
                    'created_at' => $randomCreationDate,
                    'updated_at' => $randomCreationDate
                ]);

                Information::create([
                    'company_id' => $companyId,
                    'department_id' => $depts[Arr::random($deptNames)]->getKey(),
                    'description' => "Informasi Dept #{$i}: Mohon perbarui data Anda.",
                    'event_at' => $randomCreationDate->copy()->addDays(rand(1, 10)),
                    'created_at' => $randomCreationDate,
                    'updated_at' => $randomCreationDate
                ]);

                Guestbook::create([
                    'company_id' => $companyId,
                    'date' => $randomCreationDate->toDateString(),
                    'jam_in' => $randomCreationDate->copy()->hour(rand(9, 14))->format('H:i:s'),
                    'jam_out' => $randomCreationDate->copy()->hour(rand(15, 17))->format('H:i:s'),
                    'name' => "Tamu Ke-{$i}",
                    'instansi' => "Perusahaan Tamu {$i}",
                    'keperluan' => "Meeting dengan Dept. " . Arr::random($deptNames),
                    'petugas_penjaga' => $securityUser->full_name, // Use security user
                    'created_at' => $randomCreationDate,
                    'updated_at' => $randomCreationDate
                ]);
            }

            // --- Create 500 Deliveries over 5 years ---
            for ($i = 1; $i <= 500; $i++) {
                $status = Arr::random(['pending', 'stored', 'taken', 'delivered']);
                $deliveryTime = $now->copy()->subDays(rand(0, $totalDaysIn5Years))->subHours(rand(1, 48));
                $pickupTime = null;
                if ($status == 'taken' || $status == 'delivered') {
                    // Ensure pickup is after delivery
                    $pickupTime = $deliveryTime->copy()->addHours(rand(1, 72));
                }

                Delivery::create([
                    'company_id' => $companyId,
                    'receptionist_id' => $receptionist->getKey(),
                    'item_name' => ($i % 2 == 0 ? 'Paket' : 'Dokumen') . " #{$i}",
                    'type' => $i % 2 == 0 ? 'package' : 'document',
                    'nama_pengirim' => Arr::random(['Kurir Express', 'SiCepat', 'JNE', 'AntarAja']),
                    'nama_penerima' => $allUsers->random()->full_name,
                    'storage_id' => in_array($status, ['stored', 'taken']) ? $storages->random()->getKey() : null,
                    'status' => $status,
                    'pengiriman' => $deliveryTime,
                    'pengambilan' => $pickupTime,
                    'created_at' => $deliveryTime,
                    'updated_at' => $pickupTime ?? $deliveryTime
                ]);
            }

            // ===== 5. BOOKING ROOMS & REQUIREMENTS (800 bookings over 5 years) - UPDATED =====
            for ($i = 0; $i < 800; $i++) {
                $booker = $allUsers->where('role_id', '!=', $roles['Superadmin']->getKey())->random();
                $adminApprover = $admins->random();

                // Meeting date spread over the last 5 years, plus/minus 15 days from "now"
                $startDate = $now->copy()->subDays(rand(0, $totalDaysIn5Years))->addDays(rand(-15, 15))->hour(rand(9, 15))->minute(Arr::random([0, 15, 30, 45]))->second(0);
                $endDate = $startDate->copy()->addHours(rand(1, 3));
                // Booking created 1-30 days before the meeting
                $creationDate = $startDate->copy()->subDays(rand(1, 30));

                // --- NEW LOGIC FOR STATUS & TYPE ---
                $bookingType = Arr::random(['meeting', 'online_meeting', 'hybrid', 'etc', 'meeting', 'meeting']);
                $status = '';

                if ($startDate->isPast()) {
                    // Meetings in the past are most likely completed or rejected
                    $status = Arr::random(['completed', 'rejected', 'completed', 'approved']); // 'approved' means it happened but status wasn't updated
                } else {
                    // Meetings in the future are pending or approved
                    $status = Arr::random(['pending', 'approved', 'rejected', 'approved']);
                }

                $onlineData = [
                    'online_provider' => null,
                    'online_meeting_url' => null,
                    'online_meeting_code' => null,
                    'online_meeting_password' => null,
                ];

                if (in_array($bookingType, ['online_meeting', 'hybrid'])) {
                    $provider = Arr::random(['zoom', 'google_meet']);
                    $meetingCode = sprintf('%s-%s-%s', Str::lower(Str::random(3)), Str::lower(Str::random(4)), Str::lower(Str::random(3)));

                    $onlineData['online_provider'] = $provider;
                    $onlineData['online_meeting_code'] = $meetingCode;
                    $onlineData['online_meeting_password'] = Str::random(8);

                    if ($provider == 'zoom') {
                        $onlineData['online_meeting_url'] = 'https://zoom.us/j/' . random_int(1000000000, 9999999999);
                    } else {
                        $onlineData['online_meeting_url'] = 'https://meet.google.com/' . $meetingCode;
                    }
                }

                $approvalDate = !in_array($status, ['pending', 'rejected']) ? $creationDate->copy()->addDays(rand(0, 2)) : $creationDate;

                $booking = BookingRoom::create(array_merge(
                    [
                        'room_id' => $rooms->random()->getKey(),
                        'company_id' => $companyId,
                        'user_id' => $booker->getKey(),
                        'department_id' => $booker->department_id,
                        'meeting_title' => "Rapat " . Arr::random(['Proyek', 'Bulanan', 'Evaluasi', 'Presentasi Klien', 'Internal']) . " " . Str::ucfirst(Str::random(5)),
                        'date' => $startDate->toDateString(),
                        'number_of_attendees' => rand(3, 25),
                        'start_time' => $startDate,
                        'end_time' => $endDate,
                        'special_notes' => ($i % 10 == 0) ? 'Tolong siapkan air mineral.' : null,
                        'status' => $status,
                        'is_approve' => in_array($status, ['approved', 'completed']) ? 1 : 0,
                        'approved_by' => in_array($status, ['approved', 'completed', 'rejected']) ? $adminApprover->getKey() : null,
                        'booking_type' => $bookingType,
                        'created_at' => $creationDate,
                        'updated_at' => $approvalDate
                    ],
                    $onlineData // Merge the online data array
                ));
                // --- END OF NEW LOGIC ---

                // Attach 1 to 3 random requirements
                $booking->requirements()->attach($requirements->random(rand(1, 3))->pluck('requirement_id'));
            }

            // ===== 6. VEHICLE BOOKINGS (700 bookings over 5 years) =====
            for ($i = 0; $i < 700; $i++) {
                $booker = $allUsers->where('department_id', '!=', null)->random();

                // Booking date spread over the last 5 years, plus/minus 10 days
                $startDate = $now->copy()->subDays(rand(0, $totalDaysIn5Years))->addDays(rand(-10, 10))->hour(rand(8, 16))->minute(0);
                $endDate = $startDate->copy()->addHours(rand(2, 8));
                // Booking created 1-14 days before usage
                $creationDate = $startDate->copy()->subDays(rand(1, 14));

                $status = Arr::random(['pending', 'approved', 'in_use', 'returned', 'rejected', 'cancelled', 'approved', 'returned', 'returned']);
                $approvalDate = $status != 'pending' ? $creationDate->copy()->addDays(rand(0, 1)) : $creationDate;

                VehicleBooking::create([
                    'vehicle_id' => $vehicles->random()->getKey(),
                    'company_id' => $companyId,
                    'department_id' => $booker->department_id,
                    'user_id' => $booker->getKey(),
                    'borrower_name' => $booker->full_name,
                    'start_at' => $startDate,
                    'end_at' => $endDate,
                    'purpose' => Arr::random(["Perjalanan dinas ke klien", "Kunjungan vendor", "Pengambilan barang", "Survei lokasi"]),
                    'destination' => Arr::random(["Jakarta Pusat", "Bandung", "Bekasi", "Tangerang", "Depok", "Cikarang"]),
                    'status' => $status,
                    'is_approve' => $status == 'approved' ? 1 : 0,
                    'terms_agreed' => 1,
                    'purpose_type' => 'dinas',
                    'created_at' => $creationDate,
                    'updated_at' => $approvalDate
                ]);
            }

            // ===== 7. TICKETING SYSTEM (1200 tickets over 5 years) =====
            $ticketPriorities = ['low', 'medium', 'high', 'medium', 'low']; // Skew towards low/medium
            $ticketStatuses = ['OPEN', 'IN_PROGRESS', 'RESOLVED', 'CLOSED', 'CLOSED', 'RESOLVED', 'CLOSED']; // Skew towards resolved/closed
            $ticketSubjects = [
                "Masalah Printer",
                "Koneksi WiFi Lambat",
                "Email Tidak Bisa Kirim",
                "Request Software (Adobe)",
                "Laptop Mati Total",
                "Error Aplikasi Internal",
                "Reset Password",
                "Monitor Rusak",
                "Request Akses Folder",
                "Mouse/Keyboard Error"
            ];

            // Get all IT admins/users
            $itSupportUsers = $allUsers->where('department_id', $depts['IT']->getKey())->all();

            for ($i = 1; $i <= 1200; $i++) {
                // Ensure requester is not from IT or Maintenance (who have their own system maybe)
                $requester = $allUsers->whereNotIn('department_id', [$depts['IT']->getKey(), $depts['Maintenance']->getKey()])->whereNotNull('department_id')->random();
                $it_admin = Arr::random($itSupportUsers); // Assign to a random IT user
                $status = Arr::random($ticketStatuses);

                // Ticket created randomly in the last 5 years
                $ticketCreateTime = $now->copy()->subDays(rand(0, $totalDaysIn5Years))->subMinutes(rand(60, 60 * 24));
                $lastUpdateTime = $ticketCreateTime; // Initialize last update time

                // Create Ticket
                $ticket = Ticket::create([
                    'company_id' => $companyId,
                    'department_id' => $depts['IT']->getKey(), // Ticket is FOR the IT department
                    'requestdept_id' => $requester->department_id, // Ticket is FROM this department
                    'user_id' => $requester->getKey(),
                    'subject' => Arr::random($ticketSubjects) . " di Dept. " . $requester->department->department_name,
                    'description' => "Deskripsi detail untuk masalah: " . $ticketSubjects[array_rand($ticketSubjects)] . ". Lokasi: Meja " . rand(1, 50) . ". Mohon segera ditindaklanjuti.",
                    'priority' => Arr::random($ticketPriorities),
                    'status' => $status,
                    'created_at' => $ticketCreateTime,
                    'updated_at' => $lastUpdateTime // Will be updated later
                ]);

                // Create Ticket History
                TicketHistory::create(['ticket_id' => $ticket->getKey(), 'status' => 'OPEN', 'changed_by' => $requester->getKey(), 'created_at' => $ticketCreateTime]);

                if (in_array($status, ['IN_PROGRESS', 'RESOLVED', 'CLOSED'])) {
                    $inProgressTime = $ticketCreateTime->copy()->addMinutes(rand(5, 120));
                    TicketHistory::create(['ticket_id' => $ticket->getKey(), 'status' => 'IN_PROGRESS', 'changed_by' => $it_admin->getKey(), 'created_at' => $inProgressTime]);
                    $lastUpdateTime = $inProgressTime;
                }
                if (in_array($status, ['RESOLVED', 'CLOSED'])) {
                    $resolvedTime = $lastUpdateTime->copy()->addHours(rand(1, 48));
                    TicketHistory::create(['ticket_id' => $ticket->getKey(), 'status' => 'RESOLVED', 'changed_by' => $it_admin->getKey(), 'created_at' => $resolvedTime]);
                    $lastUpdateTime = $resolvedTime;
                }
                if ($status == 'CLOSED') {
                    $closedTime = $lastUpdateTime->copy()->addDays(rand(1, 7)); // User closes it later
                    TicketHistory::create(['ticket_id' => $ticket->getKey(), 'status' => 'CLOSED', 'changed_by' => $requester->getKey(), 'created_at' => $closedTime]);
                    $lastUpdateTime = $closedTime;
                }

                // --- Update ticket's main updated_at timestamp ---
                $ticket->updated_at = $lastUpdateTime;
                $ticket->save();

                // Create Ticket Assignment
                TicketAssignment::create(['ticket_id' => $ticket->getKey(), 'user_id' => $it_admin->getKey(), 'created_at' => $ticketCreateTime->copy()->addMinutes(2)]);

                // Create Ticket Comments
                TicketComment::create(['ticket_id' => $ticket->getKey(), 'user_id' => $requester->getKey(), 'comment_text' => "Mohon bantuannya untuk segera diperbaiki, terima kasih.", 'created_at' => $ticketCreateTime->copy()->addMinutes(1)]);
                if ($status != 'OPEN') {
                    TicketComment::create(['ticket_id' => $ticket->getKey(), 'user_id' => $it_admin->getKey(), 'comment_text' => "Baik, tim kami sedang menangani ini. Mohon ditunggu.", 'created_at' => $lastUpdateTime->copy()->subMinutes(rand(5, 30))]);
                }
                if ($status == 'RESOLVED' || $status == 'CLOSED') {
                    TicketComment::create(['ticket_id' => $ticket->getKey(), 'user_id' => $it_admin->getKey(), 'comment_text' => "Masalah sudah diselesaikan. Unit printer telah di-restart dan cartridge diganti.", 'created_at' => $lastUpdateTime->copy()->subMinutes(rand(2, 5))]);
                }

                // Create Ticket Attachment (for some tickets)
                if ($i % 5 == 0) { // Add attachment for 1 in 5 tickets
                    TicketAttachment::create([
                        'ticket_id' => $ticket->getKey(),
                        'file_url' => 'https://picsum.photos/200/300',
                        'file_type' => 'image/png',
                        'uploaded_by' => $requester->getKey(),
                        'original_filename' => 'error_icon.png',
                        'created_at' => $ticketCreateTime->copy()->addSeconds(30)
                    ]);
                }
            }
        });
    }
}