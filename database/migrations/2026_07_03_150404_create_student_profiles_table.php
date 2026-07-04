<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')
                  ->unique()
                  ->constrained('students')
                  ->onDelete('cascade');
            $table->text('sensory_triggers')->nullable();
            $table->text('calming_strategies')->nullable();
            $table->text('medical_info')->nullable();
            $table->string('communication_level')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('student_profiles');
    }
};
