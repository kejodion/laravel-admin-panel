<?php

namespace Kjjdion\LaravelAdminPanel\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth_admin', 'can:Access Admin Panel']);
    }

    public function changeForm()
    {
        return view('lap::auth.passwords.change');
    }

    protected function change()
    {
        $this->validate(request(), [
            'current_password' => 'required|current_password',
            'new_password' => 'required|confirmed',
        ]);

        auth()->user()->update(['password' => Hash::make(request()->input('new_password'))]);

        activity('Changed Password');
        flash(['success', 'Password changed!']);

        return response()->json(['reload_page' => true]);
    }
}