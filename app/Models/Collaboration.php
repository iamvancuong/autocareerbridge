<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Collaboration extends Model {
    protected $fillable = ['company_id', 'university_id', 'status', 'initiated_by'];
    public function company() { return $this->belongsTo(Company::class); }
    public function university() { return $this->belongsTo(University::class); }
}