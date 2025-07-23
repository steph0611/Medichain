<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

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

            // Try logging in as a Customer
            $customerResponse = $client->get('/rest/v1/customer', [
                'query' => [
                    'username' => 'eq.' . $username,
                    'select' => '*'
                ]
            ]);

            $customers = json_decode($customerResponse->getBody()->getContents(), true);

            if (!empty($customers)) {
                $user = $customers[0];

                if ($user['password'] !== $password) {
                    return back()->withErrors(['Incorrect password'])->withInput();
                }

                session(['user' => $user, 'role' => 'customer']);
                return redirect('/dashboard')->with('success', 'Customer login successful!');
            }

            // Try logging in as a Shop
            $shopResponse = $client->get('/rest/v1/Shop', [
                'query' => [
                    'user_name' => 'eq.' . $username,
                    'select' => '*'
                ]
            ]);

            $shops = json_decode($shopResponse->getBody()->getContents(), true);

            if (!empty($shops)) {
                $shop = $shops[0];

                if ($shop['password'] !== $password) {
                    return back()->withErrors(['Incorrect password'])->withInput();
                }

                session(['user' => $shop, 'role' => 'shop']);
                return redirect('/shop-dashboard')->with('success', 'Shop login successful!');
            }

            return back()->withErrors(['No user found with this username'])->withInput();

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            \Log::error('Supabase login error', [
                'status' => $e->getResponse()?->getStatusCode(),
                'message' => $e->getResponse()?->getBody()->getContents() ?? $e->getMessage()
            ]);

            return back()->withErrors(['An error occurred while trying to login. Please try again later.']);
        } catch (\Exception $e) {
            \Log::error('Unexpected error during login', ['message' => $e->getMessage()]);
            return back()->withErrors(['Unexpected error. Please contact support.']);
        }
    }

    public function logout()
    {
        session()->flush();
        return redirect('/login')->with('success', 'Logged out successfully!');
    }
}
