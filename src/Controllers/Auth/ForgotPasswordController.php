<?php

namespace Kjjdion\LaravelAdminPanel\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    public function __construct()
    {
        $this->middleware('guest_admin');
    }

    public function emailForm()
    {
        return view('lap::auth.passwords.email');
    }

    protected function sendResetLinkResponse(Request $request, $response)
    {
        flash(['success', trans($response)]);

        return response()->json(['reload_page' => true]);
    }

    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        return response()->json([
            'message' => 'The given data was invalid.',
            'errors' => [
                'email' => [trans($response)],
            ],
        ], 422);
    }
}