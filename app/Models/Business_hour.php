<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Hospital;

class Business_hour extends Model
{
    use HasFactory;

    protected $table = 'business_hours';
    protected $fillable = ['hospital_id', 'days_of_week', 'start_time', 'end_time'];

    // 病院テーブルとのリレーション
    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }
}
