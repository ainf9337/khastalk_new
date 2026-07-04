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
    Schema::create('students', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('mykid_number')->unique()->nullable(); // Standard Malaysian Child ID format
        $table->date('date_of_birth')->nullable();

        // Relationships linked to your 'users' table
        $table->foreignId('parent_id')->nullable()->constrained('users')->onDelete('set null');
        $table->foreignId('teacher_id')->nullable()->constrained('users')->onDelete('set null');

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
