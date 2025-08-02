<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    protected $supabaseUrl = 'https://zazdljyechhzsiodnvts.supabase.co';
    protected $supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InphemRsanllY2hoenNpb2RudnRzIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTMwMjE2MzMsImV4cCI6MjA2ODU5NzYzM30.OZLL_quXsqD2PJEtyQjSBOR9SaZBVXvaTfoAcBYCZTM';

    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        $username = $request->input('username');
        $password = $request->input('password');

        try {
            $client = new Client([
                'base_uri' => $this->supabaseUrl,
                'headers' => [
                    'apikey' => $this->supabaseKey,
                    'Authorization' => 'Bearer ' . $this->supabaseKey,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ]
            ]);

            // Customer login
            $customerResponse = $client->get('/rest/v1/customer', [
                'query' => ['username' => 'eq.' . $username, 'select' => '*']
            ]);

            $customers = json_decode($customerResponse->getBody()->getContents(), true);

            if (!empty($customers)) {
                $user = $customers[0];

                if (!$user['email_verified']) {
                    return back()->withErrors(['Please verify your email before logging in.']);
                }

                if (!Hash::check($password, $user['password'])) {
                    return back()->withErrors(['Incorrect password'])->withInput();
                }

                session(['user' => $user, 'role' => 'customer']);
                return redirect('/dashboard')->with('success', 'Customer login successful!');
            }

            // Shop login
            $shopResponse = $client->get('/rest/v1/Shop', [
                'query' => ['user_name' => 'eq.' . $username, 'select' => '*']
            ]);

            $shops = json_decode($shopResponse->getBody()->getContents(), true);

            if (!empty($shops)) {
                $shop = $shops[0];

                if (!Hash::check($password, $shop['password'])) {
                    return back()->withErrors(['Incorrect password'])->withInput();
                }

                session(['user' => $shop, 'role' => 'shop']);
                return redirect('/shop-dashboard')->with('success', 'Shop login successful!');
            }

            return back()->withErrors(['No user found with this username'])->withInput();

        } catch (\Exception $e) {
            return back()->withErrors(['Unexpected error: ' . $e->getMessage()]);
        }
    }

    public function verifyEmail(Request $request)
    {
        $email = $request->query('email');
        $token = $request->query('token');

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
            // Check token and email match
            $response = $client->get('/rest/v1/customer', [
                'query' => [
                    'email' => 'eq.' . $email,
                    'verification_token' => 'eq.' . $token,
                    'select' => '*'
                ]
            ]);

            $user = json_decode($response->getBody(), true);

            if (empty($user)) {
                return redirect('/login')->withErrors(['Invalid verification link']);
            }

            // Update verification status
            $client->patch('/rest/v1/customer', [
                'query' => ['email' => 'eq.' . $email],
                'json' => ['email_verified' => true, 'verification_token' => null]
            ]);

            return redirect('/login')->with('success', 'Email verified! You may now log in.');

        } catch (\Exception $e) {
            return redirect('/login')->withErrors(['Verification failed: ' . $e->getMessage()]);
        }
    }

    public function logout()
    {
        session()->flush();
        return redirect('/login')->with('success', 'Logged out successfully!');
    }
}
