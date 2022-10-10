<?php

namespace App\Http\Controllers\AdminUser;

use App\Http\Controllers\Controller;
use App\Models\Admin_user;

class DeleteController extends Controller
{
    public function __invoke($id)
    {
        Admin_user::destroy($id);

        return redirect()->route('account.index');
    }
}
