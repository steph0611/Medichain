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

    /**
     * Helper to check password dynamically:
     * - If bcrypt hash → use Hash::check
     * - Else → plain string comparison
     */
    private function checkPassword($inputPassword, $dbPassword)
    {
        if (strpos($dbPassword, '$2y$') === 0) {
            return Hash::check($inputPassword, $dbPassword);
        }
        return $inputPassword === $dbPassword;
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
                ],
                'verify' => false  // Disable SSL verification for development
            ]);

            // 🧍 Try customer login
            $customerResponse = $client->get('/rest/v1/customer', [
                'query' => ['username' => 'eq.' . $username, 'select' => '*']
            ]);

            $customers = json_decode($customerResponse->getBody()->getContents(), true);

            if (!empty($customers)) {
                $user = $customers[0];

                if (!$user['email_verified']) {
                    return back()->withErrors(['Please verify your email before logging in.']);
                }

                if (!$this->checkPassword($password, $user['password'])) {
                    return back()->withErrors(['Incorrect password'])->withInput();
                }

                // ✅ Update last_active
                $client->patch('/rest/v1/customer', [
                    'query' => ['customer_id' => 'eq.' . $user['customer_id']],
                    'json' => ['last_active' => now()->toISOString()]
                ]);

                session(['user' => $user, 'role' => 'customer']);
                return redirect('/dashboard')->with('success', 'Customer login successful!');
            }

            // 🏪 Try shop login
            $shopResponse = $client->get('/rest/v1/Shop', [
                'query' => ['user_name' => 'eq.' . $username, 'select' => '*']
            ]);

            $shops = json_decode($shopResponse->getBody()->getContents(), true);

            if (!empty($shops)) {
                $shop = $shops[0];

                if (!$this->checkPassword($password, $shop['password'])) {
                    return back()->withErrors(['Incorrect password'])->withInput();
                }

                // ✅ Update last_active
                $client->patch('/rest/v1/Shop', [
                    'query' => ['shop_id' => 'eq.' . $shop['shop_id']],
                    'json' => ['last_active' => now()->toISOString()]
                ]);

                session(['user' => $shop, 'role' => 'shop']);
                return redirect()->route('pharmacy.dashboard', ['shop_id' => $shop['shop_id']])
                    ->with('success', 'Shop login successful!');
            }

            // 👑 Try admin login (no password hashing)
            $adminResponse = $client->get('/rest/v1/admin', [
                'query' => ['username' => 'eq.' . $username, 'select' => '*']
            ]);

            $admins = json_decode($adminResponse->getBody()->getContents(), true);

            if (!empty($admins)) {
                $admin = $admins[0];

                if ($password !== $admin['password']) {
                    return back()->withErrors(['Incorrect password'])->withInput();
                }

                // ✅ Update last_active
                $client->patch('/rest/v1/admin', [
                    'query' => ['id' => 'eq.' . $admin['id']], // replace with correct PK if needed
                    'json' => ['last_active' => now()->toISOString()]
                ]);

                session(['user' => $admin, 'role' => 'admin']);
                return redirect('/admin/dashboard')->with('success', 'Admin login successful!');
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
            ],
            'verify' => false  // Disable SSL verification for development
        ]);

        try {
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
        $user = session('user');
        $role = session('role');

        if ($user && $role) {
            try {
                $client = new Client([
                    'base_uri' => $this->supabaseUrl,
                    'headers' => [
                        'apikey'        => $this->supabaseKey,
                        'Authorization' => 'Bearer ' . $this->supabaseKey,
                        'Accept'        => 'application/json',
                        'Content-Type'  => 'application/json',
                    ],
                    'verify' => false  // Disable SSL verification for development
                ]);

                // 🛑 Clear last_active on logout
                if ($role === 'customer') {
                    $client->patch('/rest/v1/customer', [
                        'query' => ['customer_id' => 'eq.' . $user['customer_id']],
                        'json' => ['last_active' => null],
                    ]);
                } elseif ($role === 'shop') {
                    $client->patch('/rest/v1/Shop', [
                        'query' => ['shop_id' => 'eq.' . $user['shop_id']],
                        'json' => ['last_active' => null],
                    ]);
                } elseif ($role === 'admin') {
                    $client->patch('/rest/v1/admin', [
                        'query' => ['id' => 'eq.' . $user['id']], // adjust if PK is different
                        'json' => ['last_active' => null],
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error("Logout update failed: " . $e->getMessage());
            }
        }

        session()->flush();
        return redirect('/login')->with('success', 'Logged out successfully!');
    }
}
