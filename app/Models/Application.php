<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Application extends Model {
    protected $fillable = ['job_id', 'student_id', 'status', 'ai_score', 'ai_review', 'hr_feedback'];
    public function job() { return $this->belongsTo(Job::class); }
    public function student() { return $this->belongsTo(Student::class); }
}