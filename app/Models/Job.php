<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Job extends Model {
    use SoftDeletes;
    protected $fillable = ['company_id', 'major_id', 'title', 'description', 'requirements', 'is_approved'];
    protected function casts(): array { return ['is_approved' => 'boolean']; }
    public function company() { return $this->belongsTo(Company::class); }
    public function major() { return $this->belongsTo(Major::class); }
    public function applications() { return $this->hasMany(Application::class); }
}