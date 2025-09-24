<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('ticket_assignments', function (Blueprint $table) {
            $table->id(); // assignment_id
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->foreignId('agent_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamps();

            $table->unique(['ticket_id','agent_id']); // optional: avoid duplicate assignment rows
        });
    }
    public function down(): void {
        Schema::dropIfExists('ticket_assignments');
    }
};
