<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model {
    protected $fillable = [
        'student_id', 'sensory_triggers', 'calming_strategies',
        'medical_info', 'communication_level',
    ];

    public function student() {
        return $this->belongsTo(Student::class);
    }

    // ── Helper: return triggers as array ─────────────────────
    public function triggersArray(): array {
        return $this->sensory_triggers
            ? array_map('trim', explode(',', $this->sensory_triggers))
            : [];
    }

    public function strategiesArray(): array {
        return $this->calming_strategies
            ? array_map('trim', explode(',', $this->calming_strategies))
            : [];
    }
}
