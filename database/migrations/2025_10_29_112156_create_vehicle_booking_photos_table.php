<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_booking_photos', function (Blueprint $table) {
            $table->bigIncrements('id');

            // sesuai struktur vehicle_bookings di db kamu
            $table->unsignedBigInteger('vehiclebooking_id');

            // sesuai struktur users di db kamu (user_id bigint unsigned)
            $table->unsignedBigInteger('user_id');

            $table->enum('photo_type', ['before', 'after'])->index();
            $table->string('photo_url', 1024)->nullable();
            $table->string('cloudinary_public_id')->nullable();
            $table->timestamps();

            // foreign key ke vehicle_bookings.vehiclebooking_id
            $table->foreign('vehiclebooking_id')
                ->references('vehiclebooking_id')
                ->on('vehicle_bookings')
                ->onDelete('cascade');

            // foreign key ke users.user_id
            $table->foreign('user_id')
                ->references('user_id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_booking_photos');
    }
};
