<?php
namespace Database\Seeders;

use App\Models\User;
use App\Models\Student;
use App\Models\ClassRoom;
use App\Models\StudentProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder {
    public function run(): void {

        // ── Users ─────────────────────────────────────────────
        $admin = User::create([
            'name'     => 'Admin KHAS',
            'email'    => 'admin@khastalk.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
            'phone_number'    => '0123456789',
        ]);

        $teacher = User::create([
            'name'     => 'Cikgu Novianti',
            'email'    => 'teacher@khastalk.com',
            'password' => Hash::make('password'),
            'role'     => 'teacher',
            'phone_number'    => '0123456780',
        ]);

        $parent = User::create([
            'name'     => 'Puan Rohani',
            'email'    => 'parent@khastalk.com',
            'password' => Hash::make('password'),
            'role'     => 'parent',
            'phone_number'    => '0123456781',
        ]);

        $senior = User::create([
            'name'     => 'Pn Siti Hajar',
            'email'    => 'senior@khastalk.com',
            'password' => Hash::make('password'),
            'role'     => 'senior-assistant',
            'phone_number'    => '0123456782',
        ]);

        // ── Class ─────────────────────────────────────────────
        $class = ClassRoom::create([
            'class_name'    => 'PPKI 6A',
            'teacher_id'    => $teacher->id,
            'academic_year' => 2026,
        ]);

        // ── Students ──────────────────────────────────────────
        $students = [
            ['Ahmad Zikri Bin Hafiz',        'Verbal'],
            ['Qaleesya Humaira Binti Razif',  'Limited verbal'],
            ['Hazim Azfar Bin Rosli',         'Non-verbal'],
            ['Izzat Hakimi Bin Zulkifli',     'Verbal'],
            ['Nur Aisyah Binti Kamal',        'Verbal'],
            ['Haikal Azri Bin Fauzi',         'Limited verbal'],
        ];

        foreach ($students as [$name, $commLevel]) {
            $student = Student::create([
                'name'       => $name,
                'parent_id'  => $parent->id,
                'teacher_id' => $teacher->id,
                'class_id'   => $class->id,
                'diagnosis'  => 'Autism',
            ]);

            StudentProfile::create([
                'student_id'           => $student->id,
                'sensory_triggers'     => 'Loud noises, Transitions, Bright lights',
                'calming_strategies'   => 'Sensory break, Calming corner, Fidget tool',
                'medical_info'         => 'No known allergies',
                'communication_level'  => $commLevel,
            ]);
        }
    }
}
