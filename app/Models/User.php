<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable {
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'phone',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ── Role helpers ─────────────────────────────────────────
    public function isAdmin(): bool            { return $this->role === 'admin'; }
    public function isTeacher(): bool          { return $this->role === 'teacher'; }
    public function isParent(): bool           { return $this->role === 'parent'; }
    public function isSeniorAssistant(): bool  { return $this->role === 'senior_assistant'; }

    // ── Relationships ─────────────────────────────────────────
    public function teacherClass() {
        return $this->hasOne(ClassRoom::class, 'teacher_id');
    }

    public function children() {
        // Parents are linked directly on the students table
        return $this->hasMany(Student::class, 'parent_id');
    }

    public function behaviourLogs() {
        return $this->hasMany(BehaviourLog::class, 'teacher_id');
    }

    public function sentMessages() {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages() {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function activityLogs() {
        return $this->hasMany(ActivityLog::class, 'user_id');
    }

    public function rpiDocumentsCreated() {
        return $this->hasMany(RpiDocument::class, 'created_by');
    }

    public function rpiDocumentsApproved() {
        return $this->hasMany(RpiDocument::class, 'approved_by');
    }
}
