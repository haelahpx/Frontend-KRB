<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('slas', function (Blueprint $table) {
            $table->id(); // sla_id
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
            $table->boolean('active')->default(true);
            $table->timestamps(); // includes created_at
        });
    }
    public function down(): void {
        Schema::dropIfExists('slas');
    }
};
