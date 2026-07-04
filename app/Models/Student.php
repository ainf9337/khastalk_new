<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model {
    use HasFactory;

    protected $fillable = [
        'name', 'mykid_number', 'date_of_birth',
        'parent_id', 'teacher_id', 'class_id',
        'diagnosis', 'photo',
    ];

    protected function casts(): array {
        return [
            'date_of_birth' => 'date',
        ];
    }

    // ── Relationships ─────────────────────────────────────────
    public function parent() {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function teacher() {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function classRoom() {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function profile() {
        return $this->hasOne(StudentProfile::class, 'student_id');
    }

    public function behaviourLogs() {
        return $this->hasMany(BehaviourLog::class, 'student_id');
    }

    public function emergencyAlerts() {
        return $this->hasMany(EmergencyAlert::class, 'student_id');
    }

    public function messages() {
        return $this->hasMany(Message::class, 'student_id');
    }

    public function rpiDocuments() {
        return $this->hasMany(RpiDocument::class, 'student_id');
    }

    // ── Helpers ───────────────────────────────────────────────
    public function loggedToday(): bool {
        return $this->behaviourLogs()
                    ->whereDate('logged_at', today())
                    ->exists();
    }

    public function todayLog() {
        return $this->behaviourLogs()
                    ->whereDate('logged_at', today())
                    ->latest('logged_at')
                    ->first();
    }
}
