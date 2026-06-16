<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class University extends Model {
    protected $fillable = ['user_id', 'university_name', 'website', 'description', 'address'];
    public function user() { return $this->belongsTo(User::class); }
    public function students() { return $this->hasMany(Student::class); }
    public function academicAffairs() { return $this->hasMany(AcademicAffair::class); }
    public function collaborations() { return $this->hasMany(Collaboration::class); }
    public function workshops() { return $this->hasMany(Workshop::class); }
    public function majors() { return $this->belongsToMany(Major::class); }
}