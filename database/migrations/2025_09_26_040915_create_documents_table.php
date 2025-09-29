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
        Schema::create('documents', function (Blueprint $table) {
            $table->bigIncrements('document_id'); 
            $table->unsignedBigInteger('company_id')->index()->constrained('companies', 'company_id')->cascadeOnDelete();
            $table->unsignedBigInteger('user_id')->index()->constrained('users', 'user_id')->cascadeOnDelete();
            $table->unsignedBigInteger('department_id')->index()->constrained('departments', 'department_id')->cascadeOnDelete();
            $table->string('document_name');
            $table->string('nama_pengirim');
            $table->string('nama_penerima');
            $table->enum('type', ['document','invoice','etc'])->default('document');
            $table->string('penyimpanan', 50);
            $table->dateTime('pengambilan');
            $table->dateTime('pengiriman');
            $table->enum('status', ['pending','taken','delivered'])->default('pending'); 
            $table->timestamps();
        });
    }
    /**
     * Run the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
