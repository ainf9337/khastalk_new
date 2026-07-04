<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('rpi_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rpi_id')
                  ->constrained('rpi_documents')
                  ->onDelete('cascade');
            $table->text('goal_description');
            $table->text('strategy')->nullable();
            $table->tinyInteger('progress_percentage')->default(0);
            $table->date('target_date')->nullable();
            $table->enum('status', ['not_started', 'in_progress', 'achieved'])
                  ->default('not_started');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('rpi_goals');
    }
};
