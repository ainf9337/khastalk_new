<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BehaviourLog extends Model {
    protected $fillable = [
        'student_id', 'teacher_id', 'behaviour_type',
        'severity', 'duration', 'triggers',
        'teacher_response', 'resolved', 'notes', 'logged_at',
    ];

    protected function casts(): array {
        return [
            'logged_at' => 'datetime',
            'resolved'  => 'boolean',
        ];
    }

    public function student() {
        return $this->belongsTo(Student::class);
    }

    public function teacher() {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
