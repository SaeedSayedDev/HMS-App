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
        Schema::create('admin_permissions', function (Blueprint $table) {
            $table->id();
            $table->boolean('admin_management')->default(0);
            $table->boolean('doctor_management')->default(0);
            $table->boolean('salary_management')->default(0);
            $table->boolean('absence_management')->default(0);
            $table->boolean('medicine_management')->default(0);

            $table->unsignedBigInteger('admin_id');
            $table->foreign('admin_id')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_permissions');
    }
};
