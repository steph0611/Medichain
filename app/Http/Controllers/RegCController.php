<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Mail\VerifyEmailMail;

class RegCController extends Controller
{
    protected $supabaseUrl = 'https://zazdljyechhzsiodnvts.supabase.co';
    protected $supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InphemRsanllY2hoenNpb2RudnRzIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTMwMjE2MzMsImV4cCI6MjA2ODU5NzYzM30.OZLL_quXsqD2PJEtyQjSBOR9SaZBVXvaTfoAcBYCZTM';

    public function showRegisterCForm()
    {
        return view('registerC');
    }

    public function register(Request $request)
    {
        // Validate incoming data
        $request->validate([
            'full_name' => 'required|string|max:100',
            'username'  => 'required|string|max:50',
            'email'     => 'required|email|max:100',
            'password'  => 'required|string|min:6',
            'phone'     => 'required|string|max:15',
            'province'  => 'required|string|max:50',
            'district'  => 'required|string|max:50',
            'city'      => 'required|string|max:50',
        ]);

        $client = new Client([
            'base_uri' => $this->supabaseUrl,
            'headers' => [
                'apikey' => $this->supabaseKey,
                'Authorization' => 'Bearer ' . $this->supabaseKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]
        ]);

        try {
            // Check if username already exists in Supabase
            $checkResponse = $client->get('/rest/v1/customer', [
                'query' => ['username' => 'eq.' . $request->username, 'select' => 'customer_id']
            ]);

            $existing = json_decode($checkResponse->getBody(), true);
            if (!empty($existing)) {
                return back()->withErrors(['username' => 'Username already exists']);
            }

            // Generate email verification token
            $token = Str::random(40);

            // Save user with hashed password and email_verified = false
            $client->post('/rest/v1/customer', [
                'json' => [
                    'full_name' => $request->full_name,
                    'username'  => $request->username,
                    'email'     => $request->email,
                    'password'  => Hash::make($request->password), // Hash password
                    'phone'     => $request->phone,
                    'province'  => $request->province,
                    'district'  => $request->district,
                    'city'      => $request->city,
                    'email_verified' => false,
                    'verification_token' => $token,
                ]
            ]);

            // Prepare verification URL
            $verifyUrl = url('/verify-email?token=' . $token . '&email=' . $request->email);

            // Send verification email
            Mail::to($request->email)->send(new VerifyEmailMail($verifyUrl));

            // Redirect to verify notice page using named route
            return redirect()->route('verify.notice')->with('success', 'Account created. Please verify your email before logging in.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Registration failed: ' . $e->getMessage()]);
        }
    }
}
