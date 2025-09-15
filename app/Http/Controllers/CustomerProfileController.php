<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use GuzzleHttp\Client;

class CustomerProfileController extends Controller
{
    private $supabaseUrl;
    private $supabaseKey;

    public function __construct()
    {
        $this->supabaseUrl = rtrim(env('SUPABASE_URL'), '/');
        $this->supabaseKey = env('SUPABASE_KEY');
    }

    private function supabaseClient()
    {
        return new Client([
            'base_uri' => $this->supabaseUrl . '/rest/v1/',
            'headers' => [
                'apikey' => $this->supabaseKey,
                'Authorization' => 'Bearer ' . $this->supabaseKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);
    }

    /** Show profile page */
    public function edit()
    {
        if (!session()->has('user') || session('role') !== 'customer') {
            return redirect('/login')->withErrors(['Please login as a customer.']);
        }

        $user = session('user');
        return view('customer.profile', compact('user'));
    }

    /** Update profile details */
    public function update(Request $request)
    {
        if (!session()->has('user') || session('role') !== 'customer') {
            return redirect('/login')->withErrors(['Please login as a customer.']);
        }

        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone'     => 'nullable|string|max:20',
            'city'      => 'nullable|string|max:100',
            'latitude'  => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $user = session('user');
        $client = $this->supabaseClient();

        $client->patch("customer?customer_id=eq.{$user['customer_id']}", [
            'json' => [
                'full_name' => $request->full_name,
                'phone'     => $request->phone,
                'city'      => $request->city,
                'latitude'  => $request->latitude,
                'longitude' => $request->longitude,
            ]
        ]);

        // update session data
        $user['full_name'] = $request->full_name;
        $user['phone']     = $request->phone;
        $user['city']      = $request->city;
        $user['latitude']  = $request->latitude;
        $user['longitude'] = $request->longitude;
        session(['user' => $user]);

        return back()->with('success', 'Profile updated successfully.');
    }

    /** Update password */
    public function updatePassword(Request $request)
    {
        if (!session()->has('user') || session('role') !== 'customer') {
            return redirect('/login')->withErrors(['Please login as a customer.']);
        }

        $request->validate([
            'current_password'      => 'required',
            'new_password'          => 'required|min:6|confirmed',
        ]);

        $user = session('user');
        $client = $this->supabaseClient();

        // fetch stored password
        $res = $client->get("customer?customer_id=eq.{$user['customer_id']}");
        $customer = json_decode($res->getBody(), true)[0];

        if (!Hash::check($request->current_password, $customer['password'])) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        // update Supabase
        $client->patch("customer?customer_id=eq.{$user['customer_id']}", [
            'json' => ['password' => Hash::make($request->new_password)]
        ]);

        return back()->with('success', 'Password updated successfully.');
    }
}
