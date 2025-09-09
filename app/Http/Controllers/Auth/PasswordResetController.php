<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class PasswordResetController extends Controller
{
    /**
     * Show forgot password form
     */
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send password reset email via Gmail
     */
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Generate secure token
        $token = Str::random(60);

        // Store token in cache for 30 minutes
        Cache::put('password_reset_' . $token, $request->email, 1800);

        $resetUrl = url("/reset-password/{$token}");

        // Send email via Gmail
        Mail::send('emails.password-reset', ['resetUrl' => $resetUrl], function ($message) use ($request) {
            $message->to($request->email)
                    ->subject('Reset Your Password');
        });

        return back()->with('status', 'Password reset email sent! Check your Gmail.');
    }

    /**
     * Show reset password form
     */
    public function showResetForm($token)
    {
        $email = Cache::get('password_reset_' . $token);
        if (!$email) {
            return redirect('/forgot-password')->withErrors(['email' => 'Invalid or expired reset link']);
        }

        return view('auth.reset-password', ['token' => $token]);
    }

    /**
     * Handle password update
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'password' => 'required|confirmed|min:6',
        ]);

        // Verify token
        $email = Cache::get('password_reset_' . $request->token);
        if (!$email) {
            return redirect('/forgot-password')->withErrors(['email' => 'Invalid or expired reset link']);
        }

        // Hash the new password
        $newPassword = Hash::make($request->password);

        // Update password in Supabase 'customer' table via Table API
        $response = Http::withHeaders([
            'apikey' => env('SUPABASE_SERVICE_ROLE_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_ROLE_KEY'),
            'Content-Type' => 'application/json',
            'Prefer' => 'return=representation', // optional
        ])->patch(env('SUPABASE_URL') . '/rest/v1/customer?email=eq.' . urlencode($email), [
            'password' => $newPassword,
        ]);

        if ($response->failed()) {
            return back()->withErrors(['password' => 'Failed to reset password']);
        }

        // Delete token from cache
        Cache::forget('password_reset_' . $request->token);

        return redirect('/login')->with('status', 'Password reset successful! Please login.');
    }

}
