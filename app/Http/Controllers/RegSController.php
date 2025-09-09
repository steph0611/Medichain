<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Hash;

class RegSController extends Controller
{
    protected $supabaseUrl = 'https://zazdljyechhzsiodnvts.supabase.co';
    protected $supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InphemRsanllY2hoenNpb2RudnRzIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTMwMjE2MzMsImV4cCI6MjA2ODU5NzYzM30.OZLL_quXsqD2PJEtyQjSBOR9SaZBVXvaTfoAcBYCZTM';

    // Show pharmacy registration form
    public function showRegisterSForm()
    {
        return view('registerS');
    }

    // Handle registration
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'shop_name' => 'required|string|max:100',
            'location' => 'required|string|max:150',
            'city' => 'required|string|max:50',
            'phone' => 'required|string|max:15',
            'user_name' => 'required|string|max:50',
            'password' => 'required|string|min:6',
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
            // Check if username already exists
            $checkResponse = $client->get('/rest/v1/Shop', [
                'query' => [
                    'user_name' => 'eq.' . $request->user_name,
                    'select' => 'shop_id'
                ]
            ]);

            $existing = json_decode($checkResponse->getBody(), true);
            if (!empty($existing)) {
                return back()->withErrors(['Username already exists']);
            }

            // Hash the password
            $hashedPassword = Hash::make($request->password);

            // Fetch latitude & longitude using Nominatim from city
            $cityQuery = $request->city . ', Sri Lanka';
            $httpClient = new Client(['headers' => ['User-Agent' => 'Medichain/1.0']]);
            $nominatimUrl = "https://nominatim.openstreetmap.org/search?q=" . urlencode($cityQuery) . "&format=json&limit=1";

            try {
                $response = $httpClient->get($nominatimUrl);
                $data = json_decode($response->getBody(), true);

                $latitude = $data[0]['lat'] ?? null;
                $longitude = $data[0]['lon'] ?? null;
            } catch (\Exception $e) {
                $latitude = null;
                $longitude = null;
            }

            // Insert new shop into Supabase
            $client->post('/rest/v1/Shop', [
                'json' => [
                    'name' => $request->name,
                    'shop_name' => $request->shop_name,
                    'location' => $request->location,
                    'city' => $request->city,
                    'phone' => $request->phone,
                    'user_name' => $request->user_name,
                    'password' => $hashedPassword,
                    'latitude' => $latitude,
                    'longitude' => $longitude
                ]
            ]);

            return redirect('/login')->with('success', 'Shop account created successfully. Please log in.');
        } catch (\Exception $e) {
            return back()->withErrors(['Registration failed: ' . $e->getMessage()]);
        }
    }
}
