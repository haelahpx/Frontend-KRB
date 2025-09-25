<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('guestbooks', function (Blueprint $table) {
            $table->bigIncrements('guestbook_id');
            $table->date('date');
            $table->time('jam_in');
            $table->time('jam_out')->nullable();
            $table->string('name');
            $table->string('phone_number')->nullable();
            $table->string('instansi')->nullable();
            $table->string('keperluan')->nullable();
            $table->string('petugas_penjaga');
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('guestbook');
    }
};
