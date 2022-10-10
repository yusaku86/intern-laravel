<?php

namespace App\Http\Controllers\Vacation;

use App\Http\Controllers\Controller;
use App\Models\Vacation;

class DeleteController extends Controller
{
    public function __invoke($id)
    {
        Vacation::destroy($id);
        return redirect()->back();
    }
}
