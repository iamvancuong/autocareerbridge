<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Major extends Model {
    protected $fillable = ['name', 'field_id'];
    public function field() { return $this->belongsTo(Field::class); }
    public function students() { return $this->hasMany(Student::class); }
    public function jobs() { return $this->hasMany(Job::class); }
    public function universities() { return $this->belongsToMany(University::class); }
}