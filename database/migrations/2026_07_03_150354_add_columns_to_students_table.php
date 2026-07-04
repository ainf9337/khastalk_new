<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('class_id')
                  ->nullable()
                  ->after('teacher_id')
                  ->constrained('classes')
                  ->onDelete('set null');
            $table->string('diagnosis')->default('Autism')->after('class_id');
            $table->string('photo')->nullable()->after('diagnosis');
        });
    }

    public function down(): void {
        Schema::table('students', function (Blueprint $table) {
            $table->dropConstrainedForeignId('class_id');
            $table->dropColumn(['diagnosis', 'photo']);
        });
    }
};
