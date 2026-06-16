<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Resume extends Model {
    protected $fillable = ['student_id', 'file_path', 'original_name', 'content', 'is_default'];
    protected function casts(): array { return ['is_default' => 'boolean']; }
    public function student() { return $this->belongsTo(Student::class); }
}