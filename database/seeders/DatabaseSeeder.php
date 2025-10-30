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
    VehicleBooking,
    VehicleBookingPhoto,
    Delivery,
    Announcement,
    Information,
    Guestbook,
    BookingRoom,
    Ticket,
    TicketAssignment,
    TicketAttachment,
    TicketComment,
    TicketHistory
};

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $now = Carbon::now();

            $companies = [
                ['Kebun Raya Bogor', 'krbogor.id', 'https://tiketkebunraya.id/assets/images/kebun-raya-bogor.png'],
                ['Kebun Raya Bali', 'krbali.id', 'https://tiketkebunraya.id/assets/images/kebun-raya-bali.png'],
                ['Kebun Raya Cibodas', 'krcibodas.id', 'https://tiketkebunraya.id/assets/images/kebun-raya-cibodas.png'],
                ['Kebun Raya Purwodadi', 'krpurwodadi.id', 'https://tiketkebunraya.id/assets/images/kebun-raya-purwodadi.png'],
            ];

            foreach ($companies as [$companyName, $domain, $imageUrl]) {
                echo "\n🌿 Seeding {$companyName}...\n";

                // === COMPANY CREATION ===
                $company = Company::create([
                    'company_name' => $companyName,
                    'company_address' => 'Jl. Raya ' . $companyName,
                    'company_email' => "info@{$domain}",
                    'image' => $imageUrl,
                ]);

                $companyId = $company->company_id;

                // === ROLES ===
                $roles = [];
                foreach (['Superadmin', 'Admin', 'User', 'Receptionist'] as $r) {
                    $roles[$r] = Role::firstOrCreate(['name' => $r]);
                }

                // === DEPARTMENTS ===
                $deptNames = [
                    'IT','Finance','HRD','Marketing','Operations',
                    'General Affairs','Executive','Research & Development',
                    'Customer Support','Legal','Maintenance','Administration'
                ];
                $depts = [];
                foreach ($deptNames as $d) {
                    $depts[$d] = Department::firstOrCreate([
                        'company_id' => $companyId,
                        'department_name' => $d,
                    ]);
                }

                // === USERS ===
                $superadmin = User::firstOrCreate(
                    ['email' => "superadmin@{$domain}"],
                    [
                        'company_id' => $companyId,
                        'department_id' => $depts['Executive']->department_id,
                        'role_id' => $roles['Superadmin']->role_id,
                        'full_name' => "Superadmin {$companyName}",
                        'phone_number' => '08000000000',
                        'password' => Hash::make('superpassword'),
                    ]
                );

                $receptionist = User::firstOrCreate(
                    ['email' => "receptionist@{$domain}"],
                    [
                        'company_id' => $companyId,
                        'department_id' => $depts['Administration']->department_id,
                        'role_id' => $roles['Receptionist']->role_id,
                        'full_name' => "Receptionist {$companyName}",
                        'phone_number' => '087812345678',
                        'password' => Hash::make('receppassword'),
                    ]
                );

                $admins = collect();
                foreach ($depts as $name => $dept) {
                    $slug = Str::slug($name);
                    $admin = User::firstOrCreate(
                        ['email' => "admin-{$slug}@{$domain}"],
                        [
                            'company_id' => $companyId,
                            'department_id' => $dept->department_id,
                            'role_id' => $roles['Admin']->role_id,
                            'full_name' => "Admin {$name} ({$companyName})",
                            'phone_number' => '081' . random_int(100000000, 999999999),
                            'password' => Hash::make('password'),
                        ]
                    );
                    $admins->push($admin);
                }

                $users = collect([$superadmin, $receptionist]);
                $firstNames = ['Agus','Bambang','Cici','Dedi','Endang','Fajar','Gita','Hadi','Indah','Joko','Kartika','Lina','Mega','Nina','Oscar','Putra','Qori','Rian','Sari','Tono','Umar','Vina','Wati','Yoga','Zul'];
                $lastNames = ['Susanto','Wijaya','Permata','Nugroho','Pratama','Wibowo','Hidayat','Kusuma','Lestari','Setiawan'];

                for ($i = 1; $i <= 100; $i++) {
                    $name = Arr::random($firstNames) . ' ' . Arr::random($lastNames);
                    $slug = Str::slug($name) . "-{$i}";
                    $dept = Arr::random($depts);

                    $users->push(User::firstOrCreate(
                        ['email' => "{$slug}@{$domain}"],
                        [
                            'company_id' => $companyId,
                            'department_id' => $dept->department_id,
                            'role_id' => $roles['User']->role_id,
                            'full_name' => $name,
                            'phone_number' => '089' . random_int(100000000, 999999999),
                            'password' => Hash::make('password'),
                        ]
                    ));
                }

                $this->seedAssetsAndActivities($companyId, $companyName, $depts, $roles, $admins, $users, $receptionist, $now);
            }
        });
    }

    protected function seedAssetsAndActivities($companyId, $companyName, $depts, $roles, $admins, $users, $receptionist, $now)
    {
        mt_srand($companyId * 999);

        $demoImages = [
            'https://res.cloudinary.com/demo/image/upload/sample.jpg',
            'https://res.cloudinary.com/demo/image/upload/dog.jpg',
            'https://res.cloudinary.com/demo/image/upload/cat.jpg',
            'https://res.cloudinary.com/demo/image/upload/girl.jpg',
            'https://res.cloudinary.com/demo/image/upload/car.jpg',
            'https://res.cloudinary.com/demo/image/upload/beach.jpg',
            'https://res.cloudinary.com/demo/image/upload/mountain.jpg',
        ];

        // ===== ROOMS =====
        $rooms = collect(['Garuda','Merak','Cendrawasih','Aula','Elang'])
            ->map(fn($r) => Room::firstOrCreate(['company_id'=>$companyId,'room_number'=>"Ruang {$r}"]));

        // ===== REQUIREMENTS =====
        foreach (['Projector & Screen','Whiteboard','Coffee Break','Lunch Set','Sound System'] as $req) {
            Requirement::firstOrCreate(['company_id'=>$companyId,'name'=>$req]);
        }

        // ===== STORAGES =====
        foreach ([['S-01','Rak Dokumen'],['S-02','Loker Paket'],['S-03','Gudang ATK']] as [$code,$name]) {
            Storage::firstOrCreate(['company_id'=>$companyId,'code'=>$code],['name'=>$name]);
        }

        // ===== VEHICLES =====
        foreach ([
            ['Avanza','car',2022],
            ['Innova','car',2021],
            ['Honda Vario','motorcycle',2023],
            ['Carry PickUp','pickup',2019]
        ] as [$name,$type,$year]) {
            $plate = 'B ' . rand(1000,9999) . ' ' . Str::upper(Str::random(3));
            Vehicle::firstOrCreate(
                ['plate_number'=>$plate],
                [
                    'company_id'=>$companyId,
                    'name'=>$name,
                    'category'=>$type,
                    'year'=>$year,
                ]
            );
        }

        // ===== DELIVERIES =====
        for ($i=1; $i<=100; $i++) {
            Delivery::create([
                'company_id'=>$companyId,
                'receptionist_id'=>$receptionist->user_id,
                'item_name'=>"Paket {$companyName} #{$i}",
                'type'=>Arr::random(['package','document']),
                'nama_pengirim'=>Arr::random(['JNE','TIKI','SiCepat','Pos Indonesia']),
                'nama_penerima'=>$users->random()->full_name,
                'status'=>Arr::random(['pending','stored','delivered']),
                'pengiriman'=>$now->copy()->subDays(rand(0,300)),
            ]);
        }

        // ===== ANNOUNCEMENTS, INFORMATION, GUESTBOOK =====
        for ($i=1; $i<=100; $i++) {
            $randomDate=$now->copy()->subDays(rand(0,365));

            Announcement::create([
                'company_id'=>$companyId,
                'description'=>"📢 Pengumuman {$companyName} #{$i} - Event internal {$companyName}",
                'event_at'=>$randomDate->addDays(rand(2,10)),
                'created_at'=>$randomDate,
            ]);

            Information::create([
                'company_id'=>$companyId,
                'department_id'=>Arr::random($depts)->department_id,
                'description'=>"📰 Info khusus {$companyName} #{$i}",
                'event_at'=>$randomDate->addDays(rand(1,5)),
                'created_at'=>$randomDate,
            ]);

            Guestbook::create([
                'company_id'=>$companyId,
                'department_id'=>Arr::random($depts)->department_id,
                'date'=>$randomDate->toDateString(),
                'jam_in'=>sprintf("%02d:%02d:00", rand(8,10), rand(0,59)),
                'jam_out'=>sprintf("%02d:%02d:00", rand(14,17), rand(0,59)),
                'name'=>"Tamu #{$i} ({$companyName})",
                'instansi'=>"Instansi {$i} {$companyName}",
                'keperluan'=>"Meeting dengan Dept ".Arr::random(array_keys($depts)),
                'petugas_penjaga'=>$receptionist->full_name,
                'created_at'=>$randomDate,
            ]);
        }

        // ===== ROOM BOOKINGS =====
        foreach (range(1,50) as $i) {
            $booker = $users->random();
            $room = $rooms->random();
            $startDate = $now->copy()->subDays(rand(0,180));
            $endDate = $startDate->copy()->addHours(rand(1,3));
            BookingRoom::create([
                'room_id'=>$room->room_id,
                'company_id'=>$companyId,
                'user_id'=>$booker->user_id,
                'department_id'=>$booker->department_id,
                'meeting_title'=>"Rapat {$companyName} #{$i}",
                'date'=>$startDate->toDateString(),
                'number_of_attendees'=>rand(3,30),
                'start_time'=>$startDate,
                'end_time'=>$endDate,
                'is_approve'=>1,
                'booking_type'=>'meeting',
                'status'=>'approved',
                'approved_by'=>$admins->random()->user_id,
            ]);
        }

        // ===== VEHICLE BOOKINGS =====
        $vehicles = Vehicle::where('company_id', $companyId)->get();
        foreach (range(1,50) as $i) {
            $user=$users->random();
            $vehicle=$vehicles->random();
            $start=$now->copy()->subDays(rand(0,180))->hour(rand(8,14));
            $end=$start->copy()->addHours(rand(2,6));

            $purposeType = Arr::random(['dinas', 'operasional', 'antar_jemput', 'lainnya']);

            $booking=VehicleBooking::create([
                'vehicle_id'=>$vehicle->vehicle_id,
                'company_id'=>$companyId,
                'department_id'=>$user->department_id,
                'user_id'=>$user->user_id,
                'borrower_name'=>$user->full_name,
                'start_at'=>$start,
                'end_at'=>$end,
                'purpose'=>ucfirst(str_replace('_', ' ', $purposeType)),
                'purpose_type'=>$purposeType,
                'destination'=>Arr::random(['Bogor','Jakarta','Bali','Purwodadi']),
                'status'=>Arr::random(['approved','in_use','returned']),
                'terms_agreed'=>1,
            ]);

            foreach (['before','after'] as $type) {
                VehicleBookingPhoto::create([
                    'vehiclebooking_id'=>$booking->vehiclebooking_id,
                    'user_id'=>$user->user_id,
                    'photo_type'=>$type,
                    'photo_url'=>Arr::random($demoImages),
                    'cloudinary_public_id'=>'demo_sample_'.$type,
                ]);
            }
        }

        // ===== TICKETING =====
        $this->seedTicketSystem($companyId,$companyName,$depts,$users,$now,$demoImages);
    }

    protected function seedTicketSystem($companyId,$companyName,$depts,$users,$now,$demoImages)
    {
        $priorities=['low','medium','high'];
        $statuses=['OPEN','IN_PROGRESS','RESOLVED','CLOSED'];

        $slaTargets = [
            'high' => 24,
            'medium' => 48,
            'low' => 72,
        ];

        foreach (range(1,100) as $i) {
            $user=$users->random();
            $dept=$depts[array_rand($depts)];
            $reqDept=$depts[array_rand($depts)];
            $priority=Arr::random($priorities);
            $status=Arr::random($statuses);
            $created=$now->copy()->subDays(rand(0,300));

            $targetHours = $slaTargets[$priority];
            $isResolved = in_array($status, ['RESOLVED','CLOSED']);
            $isWithinSla = true;
            $updated = $created->copy();

            if ($isResolved) {
                $isWithinSla = rand(0,100) < 70;
                $hoursTaken = $isWithinSla ? rand(1,$targetHours-1) : $targetHours + rand(10,48);
                $updated = $created->copy()->addHours($hoursTaken);
            } else {
                $updated = $created->copy()->addHours(rand(1,12));
            }

            $ticket=Ticket::create([
                'company_id'=>$companyId,
                'department_id'=>$dept->department_id,
                'requestdept_id'=>$reqDept->department_id,
                'user_id'=>$user->user_id,
                'subject'=>"Ticket {$companyName} #{$i}",
                'description'=>"Masalah prioritas {$priority} pada {$companyName}",
                'priority'=>$priority,
                'status'=>$status,
                'created_at'=>$created,
                'updated_at'=>$updated,
            ]);

            TicketAssignment::create([
                'ticket_id'=>$ticket->ticket_id,
                'user_id'=>$users->random()->user_id,
                'created_at'=>$created,
            ]);

            TicketAttachment::create([
                'ticket_id'=>$ticket->ticket_id,
                'file_url'=>Arr::random($demoImages),
                'file_type'=>'image/jpeg',
                'uploaded_by'=>$user->user_id,
                'cloudinary_public_id'=>'demo_sample_ticket',
                'bytes'=>random_int(10000,50000),
                'original_filename'=>'demo_sample.jpg',
                'created_at'=>$created,
            ]);

            TicketComment::create([
                'ticket_id'=>$ticket->ticket_id,
                'user_id'=>$user->user_id,
                'comment_text'=>"Komentar #{$i} untuk {$companyName}",
                'created_at'=>$created->copy()->addHours(1),
            ]);

            TicketHistory::create([
                'ticket_id'=>$ticket->ticket_id,
                'status'=>$status,
                'changed_by'=>$user->user_id,
                'created_at'=>$updated,
            ]);

            if ($isResolved) {
                echo "🎫 {$companyName} Ticket #{$i} ({$priority}) [{$status}] → " .
                     ($isWithinSla ? "✅ SLA met" : "⏰ SLA missed") .
                     " (" . $updated->diffInHours($created) . "h)\n";
            } else {
                echo "🕓 {$companyName} Ticket #{$i} ({$priority}) [{$status}] still open\n";
            }
        }

        echo "✅ {$companyName} – ticketing data with SLA simulation seeded successfully.\n";
    }
}
