<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class RegController extends Controller
{
    protected $supabaseUrl = 'https://zazdljyechhzsiodnvts.supabase.co';
    protected $supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InphemRsanllY2hoenNpb2RudnRzIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTMwMjE2MzMsImV4cCI6MjA2ODU5NzYzM30.OZLL_quXsqD2PJEtyQjSBOR9SaZBVXvaTfoAcBYCZTM'; 


    public function showRegisterForm()
    {
        return view('register');
    }


    public function register(Request $request)
    {

        $request->validate([
            'username' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:6',
            'phone' => 'required|string',
            'location' => 'required|string',
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
           
            $checkResponse = $client->get('/rest/v1/customer', [
                'query' => [
                    'username' => 'eq.' . $request->username,
                    'select' => 'id'
                ]
            ]);

            $existing = json_decode($checkResponse->getBody(), true);
            if (!empty($existing)) {
                return back()->withErrors(['Username already exists']);
            }

       
            $client->post('/rest/v1/customer', [
                'json' => [
                    'username' => $request->username,
                    'email' => $request->email,
                    'password' => $request->password,
                    'phone' => $request->phone,
                    'location' => $request->location    
                ]
            ]);

            
            return redirect('/login')->with('success', 'Account created. Please log in.');

        } catch (\Exception $e) {
            return back()->withErrors(['Registration failed: ' . $e->getMessage()]);
        }
    }
}
