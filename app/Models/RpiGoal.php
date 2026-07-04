<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RpiGoal extends Model {
    protected $fillable = [
        'rpi_id', 'goal_description', 'strategy',
        'progress_percentage', 'target_date', 'status',
    ];

    protected function casts(): array {
        return ['target_date' => 'date'];
    }

    public function rpiDocument() {
        return $this->belongsTo(RpiDocument::class, 'rpi_id');
    }
}
