<?php

namespace App\Http\Controllers\Vacation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vacation;

class CreateController extends Controller
{
    public function __invoke(Request $request)
    {
        Vacation::create([
            'hospital_id' => $request->input('hospital'),
            'start_date'  => $request->input('add_start-date'),
            'end_date'    => $request->input('add_end-date'),
            'reason'      => $request->input('add_reason'),
        ]);

        return redirect()->back();
    }
}
