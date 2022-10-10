<?php

namespace App\Http\Controllers\AdminUser;

use App\Http\Controllers\Controller;

class IndexAddController extends Controller
{
    public function __invoke()
    {
        return view('add_account');
    }
}
