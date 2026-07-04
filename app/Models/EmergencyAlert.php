<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmergencyAlert extends Model {
    protected $fillable = [
        'student_id', 'teacher_id', 'alert_type',
        'description', 'status', 'parent_confirmed_at',
    ];

    protected function casts(): array {
        return [
            'parent_confirmed_at' => 'datetime',
        ];
    }

    public function student() {
        return $this->belongsTo(Student::class);
    }

    public function teacher() {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
