<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('booking_requirement', function (Blueprint $table) {
            $table->foreignId('bookingroom_id')->constrained('booking_rooms')->cascadeOnDelete();
            $table->foreignId('requirement_id')->constrained('requirements')->cascadeOnDelete();
            $table->primary(['bookingroom_id', 'requirement_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('booking_requirement');
    }
};
