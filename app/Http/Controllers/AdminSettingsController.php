<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Hash;

class AdminSettingsController extends Controller
{
    private $supabaseUrl;
    private $supabaseKey;

    public function __construct()
    {
        $this->supabaseUrl = rtrim(env('SUPABASE_URL'), '/');
        $this->supabaseKey = env('SUPABASE_KEY');
    }

    private function supabaseClient(): Client
    {
        return new Client([
            'base_uri' => $this->supabaseUrl,
            'headers' => [
                'apikey'        => $this->supabaseKey,
                'Authorization' => 'Bearer ' . $this->supabaseKey,
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
            ],
            'timeout' => 20,
            'verify' => false  // Disable SSL verification for development
        ]);
    }

    /** Show admin settings */
    public function index()
    {
        $admin = session('user'); // get logged-in admin from session
        return view('admin.settings', compact('admin'));
    }

    /** Update admin info */
    public function update(Request $request)
    {
        $admin = session('user');
        if (!$admin) {
            return redirect('/login')->withErrors(['Not logged in']);
        }

        $client = $this->supabaseClient();
        $adminId = $admin['id']; // adjust if PK differs

        $data = $request->only(['full_name', 'username', 'email', 'password']);

        // Only send password if provided
        if (empty($data['password'])) {
            unset($data['password']);
        }

        try {
            $client->patch("/rest/v1/admin?id=eq.{$adminId}", ['json' => $data]);

            // Update session data
            $admin = array_merge($admin, $data);
            session(['user' => $admin]);

            return redirect()->back()->with('success', 'Admin info updated successfully!');
        } catch (\Exception $e) {
            \Log::error("Error updating admin info: " . $e->getMessage());
            return redirect()->back()->withErrors(['Failed to update admin info: ' . $e->getMessage()]);
        }
    }

    /** Logout admin */
    public function logout(Request $request)
    {
        $admin = session('user');
        if ($admin) {
            try {
                $client = $this->supabaseClient();
                $client->patch("/rest/v1/admin?id=eq.{$admin['id']}", [
                    'json' => ['last_active' => null]
                ]);
            } catch (\Exception $e) {
                \Log::error("Logout failed: " . $e->getMessage());
            }
        }

        session()->flush();
        return redirect('/login')->with('success', 'Logged out successfully!');
    }
}
