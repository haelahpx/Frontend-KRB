<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vehicle_bookings', function (Blueprint $table) {
            $table->id('vehiclebooking_id');

            $table->foreignId('vehicle_id')
                ->constrained('vehicles', 'vehicle_id')
                ->cascadeOnDelete();

            $table->foreignId('company_id')
                ->constrained('companies', 'company_id')
                ->cascadeOnDelete();

            $table->foreignId('department_id')
                ->constrained('departments', 'department_id')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users', 'user_id')
                ->nullOnDelete();

            $table->string('borrower_name')->nullable();

            $table->dateTime('start_at'); 
            $table->dateTime('end_at');    

            $table->string('purpose');                  
            $table->string('destination')->nullable();  

            $table->enum('odd_even_area', ['ya', 'tidak'])
                ->default('tidak');

            $table->enum('purpose_type', ['dinas', 'operasional', 'antar_jemput', 'lainnya'])
                ->default('dinas');

            // Checkbox S&K (harus dicentang)
            $table->boolean('terms_agreed')->default(false);

            $table->boolean('is_approve')->default(false);
            $table->enum('status', ['pending', 'approved', 'in_use', 'returned', 'rejected', 'cancelled'])
                ->default('pending');

            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['vehicle_id', 'start_at']);
            $table->index(['department_id', 'status']);
            $table->index(['company_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_bookings');
    }
};
