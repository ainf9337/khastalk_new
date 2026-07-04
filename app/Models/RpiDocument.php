<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RpiDocument extends Model {
    protected $fillable = [
        'student_id', 'created_by', 'approved_by', 'status', 'period',
    ];

    public function student() {
        return $this->belongsTo(Student::class);
    }

    public function createdBy() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy() {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function goals() {
        return $this->hasMany(RpiGoal::class, 'rpi_id');
    }

    public function overallProgress(): int {
        $goals = $this->goals;
        if ($goals->isEmpty()) return 0;
        return (int) $goals->avg('progress_percentage');
    }
}
