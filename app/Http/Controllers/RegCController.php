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

    // Show registration form
    public function showRegisterCForm()
    {
        return view('registerC');
    }

    // Handle registration
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
            'address'   => 'required|string|max:150',
            'latitude'  => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $client = new Client([
            'base_uri' => $this->supabaseUrl,
            'headers' => [
                'apikey' => $this->supabaseKey,
                'Authorization' => 'Bearer ' . $this->supabaseKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'verify' => false  // Disable SSL verification for development
        ]);

        try {
            // Check if username or email already exists
            $orQuery = "(username.eq.{$request->username},email.eq.{$request->email})";
            $checkResponse = $client->get('/rest/v1/customer', [
                'query' => [
                    'or' => $orQuery,
                    'select' => 'customer_id,username,email'
                ]
            ]);

            $existing = json_decode($checkResponse->getBody(), true);
            if (!empty($existing)) {
                return back()->withErrors(['error' => 'Username or email already exists']);
            }

            // Generate email verification token
            $token = Str::random(40);

            // Save user in Supabase
            $client->post('/rest/v1/customer', [
                'json' => [
                    'full_name' => $request->full_name,
                    'username'  => $request->username,
                    'email'     => $request->email,
                    'password'  => Hash::make($request->password),
                    'phone'     => $request->phone,
                    'province'  => $request->province,
                    'district'  => $request->district,
                    'city'      => $request->city,
                    'address'   => $request->address,
                    // ✅ Cast latitude/longitude to float to match DB type
                    'latitude'  => $request->latitude !== null ? floatval($request->latitude) : null,
                    'longitude' => $request->longitude !== null ? floatval($request->longitude) : null,
                    'email_verified' => false,
                    'verification_token' => $token,
                ]
            ]);

            // Prepare verification URL
            $verifyUrl = url('/verify-email?token=' . $token . '&email=' . $request->email);

            // Send verification email
            Mail::to($request->email)->send(new VerifyEmailMail($verifyUrl));

            return redirect()->route('verify.notice')
                ->with('success', 'Account created. Please verify your email before logging in.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Registration failed: ' . $e->getMessage()]);
        }
    }
}
