<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('guestbooks', function (Blueprint $table) {
            $table->id('guestbook_id');
            $table->foreignId('company_id')->nullable()->constrained('companies', 'company_id')->nullOnDelete();
            $table->date('date');
            $table->string('name');
            $table->string('phone_number', 30)->nullable();
            $table->time('jam_in');
            $table->time('jam_out')->nullable();
            $table->string('instansi')->nullable();
            $table->string('keperluan')->nullable();
            $table->string('petugas_penjaga')->nullable();
            $table->timestamps();
            $table->index(['date', 'company_id']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guestbooks');
    }
};
