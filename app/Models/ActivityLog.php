<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model {
    protected $fillable = [
        'user_id', 'action', 'description', 'ip_address',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    // ── Static helper — call from anywhere ───────────────────
    public static function record(
        int    $userId,
        string $action,
        string $description = ''
    ): void {
        static::create([
            'user_id'     => $userId,
            'action'      => $action,
            'description' => $description,
            'ip_address'  => request()->ip(),
        ]);
    }
}
