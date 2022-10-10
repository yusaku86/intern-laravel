<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Hospital;

class Vacation extends Model
{
    use HasFactory;

    protected $fillable = ['hospital_id', 'start_date', 'end_date', 'reason'];

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }
}
