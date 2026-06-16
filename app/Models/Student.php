<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Student extends Model {
    protected $fillable = ['user_id', 'university_id', 'major_id', 'student_code', 'gpa'];
    public function user() { return $this->belongsTo(User::class); }
    public function university() { return $this->belongsTo(University::class); }
    public function major() { return $this->belongsTo(Major::class); }
    public function skills() { return $this->belongsToMany(Skill::class); }
    public function resumes() { return $this->hasMany(Resume::class); }
    public function applications() { return $this->hasMany(Application::class); }
}