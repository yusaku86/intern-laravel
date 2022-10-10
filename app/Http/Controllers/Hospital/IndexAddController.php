<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;

class IndexAddController extends Controller
{
    public function __invoke()
    {
        return view('hospital');
    }
}
