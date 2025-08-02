<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class RegSController extends Controller
{
    protected $supabaseUrl = 'https://zazdljyechhzsiodnvts.supabase.co';
    protected $supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InphemRsanllY2hoenNpb2RudnRzIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTMwMjE2MzMsImV4cCI6MjA2ODU5NzYzM30.OZLL_quXsqD2PJEtyQjSBOR9SaZBVXvaTfoAcBYCZTM';

    public function showRegisterSForm()
    {
        return view('registerS');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'shop_name' => 'required|string',
            'location' => 'required|string',
            'city'       => 'required|string',
            'phone' => 'required|string',
            'user_name' => 'required|string',
            'password' => 'required|string|min:6',
            'api_key' => 'required|string',
            'url' => 'required|string'
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
                    'select' => 'id'
                ]
            ]);

            $existing = json_decode($checkResponse->getBody(), true);
            if (!empty($existing)) {
                return back()->withErrors(['Username already exists']);
            }

            // Insert new shop data
            $client->post('/rest/v1/Shop', [
                'json' => [
                    'name' => $request->name,
                    'shop_name' => $request->shop_name,
                    'location' => $request->location,
                    'city'       => $request->city,
                    'phone' => $request->phone,
                    'user_name' => $request->user_name,
                    'password' => $request->password,
                    'api_key' => $request->api_key,
                    'url' => $request->url
                ]
            ]);

            return redirect('/login')->with('success', 'Shop account created successfully. Please log in.');
        } catch (\Exception $e) {
            return back()->withErrors(['Registration failed: ' . $e->getMessage()]);
        }
    }
}
