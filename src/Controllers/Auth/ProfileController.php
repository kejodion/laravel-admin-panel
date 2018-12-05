<?php

namespace Kjjdion\LaravelAdminPanel\Controllers\Auth;

use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth_admin', 'can:Access Admin Panel']);
    }

    public function updateForm()
    {
        return view('lap::auth.profile');
    }

    protected function update()
    {
        $this->validate(request(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . auth()->user()->id,
        ]);

        auth()->user()->update(request()->all());

        activity('Updated Profile', request()->all());
        flash(['success', 'Profile updated!']);

        return response()->json(['reload_page' => true]);
    }
}