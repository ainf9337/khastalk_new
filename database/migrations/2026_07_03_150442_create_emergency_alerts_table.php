<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('emergency_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')
                  ->constrained('students')
                  ->onDelete('cascade');
            $table->foreignId('teacher_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->string('alert_type');
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'resolved'])
                  ->default('pending');
            $table->timestamp('parent_confirmed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('emergency_alerts');
    }
};
