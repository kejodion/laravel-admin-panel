<?php

namespace Kjjdion\LaravelAdminPanel\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function __construct()
    {
        $this->middleware('guest_admin')->except('logout');
    }

    public function loginForm()
    {
        return view('lap::auth.login');
    }

    protected function authenticated(Request $request, $user)
    {
        if ($request->input('auth_user_timezone')) {
            $user->update(['timezone' => $request->input('auth_user_timezone')]);
        }

        activity('Logged In');

        return response()->json(['redirect' => session()->pull('url.intended', route('admin.dashboard'))]);
    }

    protected function loggedOut(Request $request)
    {
        return redirect()->route('admin.login');
    }
}