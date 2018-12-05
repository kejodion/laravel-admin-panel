<?php

namespace Kjjdion\LaravelAdminPanel\Controllers;

use App\Http\Controllers\Controller;

class BackendController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth_admin', 'can:Access Admin Panel'])->except('index');
        $this->middleware('can:Update Settings')->only(['settingsForm', 'settings']);
    }

    public function index()
    {
        return redirect()->route('admin.' . (auth()->check() ? 'dashboard' : 'login'));
    }

    public function dashboard()
    {
        return view('lap::backend.dashboard');
    }

    public function settingsForm()
    {
        return view('lap::backend.settings');
    }

    public function settings()
    {
        $this->validate(request(), $this->settingsRules());

        foreach (request()->all() as $key => $value) {
            if ($setting = app(config('lap.models.setting'))->where('key', $key)->first()) {
                $setting->update(['value' => $value]);
            }
        }

        activity('Updated Settings', request()->all());
        flash(['success', 'Settings updated!']);

        return response()->json(['reload_page' => true]);
    }

    public function settingsRules()
    {
        return [
            'example' => 'required',
        ];
    }
}