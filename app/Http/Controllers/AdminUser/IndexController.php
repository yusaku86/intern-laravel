<?php

namespace App\Http\Controllers\AdminUser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin_user;

class IndexController extends Controller
{
    public function __invoke()
    {
        $accounts = Admin_user::all();

        return view('admin_user')->with('accounts', $accounts);
    }
}
