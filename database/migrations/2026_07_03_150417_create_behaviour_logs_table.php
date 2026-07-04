<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('behaviour_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')
                  ->constrained('students')
                  ->onDelete('cascade');
            $table->foreignId('teacher_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->string('behaviour_type');
            $table->tinyInteger('severity')->nullable();
            $table->string('duration')->nullable();
            $table->text('triggers')->nullable();
            $table->text('teacher_response')->nullable();
            $table->boolean('resolved')->default(false);
            $table->text('notes')->nullable();
            $table->timestamp('logged_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('behaviour_logs');
    }
};
