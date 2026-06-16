<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable {
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'is_active', 'avatar',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function company() { return $this->hasOne(Company::class); }
    public function university() { return $this->hasOne(University::class); }
    public function student() { return $this->hasOne(Student::class); }
    public function hiring() { return $this->hasOne(Hiring::class); }
    public function academicAffair() { return $this->hasOne(AcademicAffair::class); }
    public function notifications() { return $this->hasMany(Notification::class); }

    // Sub-role helpers
    public function getActiveCompanyAttribute() {
        return $this->role === 'company' ? $this->company : ($this->role === 'hiring' ? $this->hiring->company : null);
    }

    public function getActiveUniversityAttribute() {
        return $this->role === 'university' ? $this->university : ($this->role === 'academic_affairs' ? $this->academicAffair->university : null);
    }

    public function hasCompanyRole() {
        return in_array($this->role, ['company', 'hiring']);
    }

    public function hasUniversityRole() {
        return in_array($this->role, ['university', 'academic_affairs']);
    }
}