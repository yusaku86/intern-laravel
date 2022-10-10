<?php

namespace App\Http\Controllers\BusinessHour;

use App\Http\Controllers\Controller;
use App\Models\Hospital;

class IndexController extends Controller
{
    public function __invoke($selectedId = null)
    {
        $hospitals = Hospital::orderBy('id', 'asc')->get();

        $selectedId = $selectedId ?? $hospitals[0]->id;
        $businessHours = Hospital::find($selectedId)->businessHours->sortBy('days_of_week');

        return view('business_hour')->with([
            'hospitals' => $hospitals,
            'businessHours' => $businessHours,
            'selectedId' => intval($selectedId),
        ]);
    }
}
