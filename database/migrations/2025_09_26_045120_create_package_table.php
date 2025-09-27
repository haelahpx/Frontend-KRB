<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id('package_id');
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable(); 
            $table->string('package_name');
            $table->string('nama_pengirim')->nullable();
            $table->string('nama_penerima')->nullable();
            $table->enum('penyimpanan', ['rak1','rak2','rak3'])->nullable();
            $table->dateTime('pengambilan')->nullable();
            $table->dateTime('pengiriman')->nullable();
            $table->enum('status', ['pending','taken','delivered'])->default('pending');
            $table->timestamps();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->index(['company_id','department_id']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package');
    }
};
