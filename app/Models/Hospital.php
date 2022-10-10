<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Business_hour;
use App\Models\Vacation;

class Hospital extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'is_open_sun', 'is_open_mon', 'is_open_tue', 'is_open_wed', 'is_open_thu', 'is_open_fri', 'is_open_sat'];


    // 診療時間テーブルとのリレーション
    public function businessHours()
    {
        return $this->hasMany(Business_hour::class);
    }

    // 長期休暇テーブルとのリレーション
    public function vacations()
    {
        return $this->hasMany(Vacation::class);
    }
}
