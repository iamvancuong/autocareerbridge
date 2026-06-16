<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Workshop extends Model {
    protected $fillable = ['company_id', 'university_id', 'title', 'description', 'date', 'scheduled_at'];
    protected $casts = ['date' => 'date', 'scheduled_at' => 'datetime'];
    public function company() { return $this->belongsTo(Company::class); }
    public function university() { return $this->belongsTo(University::class); }
}