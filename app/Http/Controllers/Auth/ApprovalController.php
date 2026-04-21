<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

class ApprovalController extends Controller
{
    public function pending()
    {
        return view('auth.approval-pending');
    }

    public function rejected()
    {
        return view('auth.approval-rejected');
    }
}
