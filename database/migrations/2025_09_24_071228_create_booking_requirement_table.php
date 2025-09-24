<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('booking_rooms', function (Blueprint $table) {
            $table->id(); // bookingroom_id
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('meeting_title');
            $table->date('date');
            $table->unsignedInteger('number_of_attendees')->default(0);
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->text('special_notes')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('booking_rooms');
    }
};
