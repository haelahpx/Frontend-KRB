<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('booking_rooms', function (Blueprint $table) {
            // What kind of booking is this?
            // meeting = on-site meeting (default)
            // online_meeting = fully online
            // hybrid = mix (optional but handy)
            $table->enum('booking_type', ['meeting', 'online_meeting', 'hybrid', 'etc'])
                ->default('meeting')
                ->after('is_approve');

            // Online-meeting specific fields (nullable for non-online bookings)
            $table->enum('online_provider', ['zoom', 'google_meet'])
                ->nullable()
                ->after('booking_type');

            $table->string('online_meeting_url', 2048)
                ->nullable()
                ->after('online_provider');

            // Optional extra metadata some providers use
            $table->string('online_meeting_code', 120)
                ->nullable()
                ->after('online_meeting_url');

            $table->string('online_meeting_password', 120)
                ->nullable()
                ->after('online_meeting_code');

            // Useful for filtering/searching online entries
            $table->index(['booking_type', 'online_provider']);
        });
    }

    public function down(): void
    {
        Schema::table('booking_rooms', function (Blueprint $table) {
            $table->dropIndex(['booking_rooms_booking_type_online_provider_index']);
            $table->dropColumn([
                'booking_type',
                'online_provider',
                'online_meeting_url',
                'online_meeting_code',
                'online_meeting_password',
            ]);
        });
    }
};
