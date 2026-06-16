<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Company extends Model {
    protected $fillable = ['user_id', 'company_name', 'website', 'description', 'address'];
    public function user() { return $this->belongsTo(User::class); }
    public function hirings() { return $this->hasMany(Hiring::class); }
    public function jobs() { return $this->hasMany(Job::class); }
    public function collaborations() { return $this->hasMany(Collaboration::class); }
    public function workshops() { return $this->hasMany(Workshop::class); }
}