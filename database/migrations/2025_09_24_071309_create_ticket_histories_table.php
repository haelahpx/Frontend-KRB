<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('ticket_histories', function (Blueprint $table) {
            $table->id(); // history_id
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->string('action'); // e.g., "status_changed", "assigned", etc.
            $table->enum('status', ['OPEN','IN_PROGRESS','RESOLVED','CLOSED'])->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('changed_at')->useCurrent();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('ticket_histories');
    }
};
