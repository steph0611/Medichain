<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class PharmacyController extends Controller
{
    protected $supabaseUrl = 'https://zazdljyechhzsiodnvts.supabase.co';
    protected $supabaseKey = 'YOUR_GLOBAL_KEY'; 

    public function index(Request $request)
    {
        $city = session('user')['city'];

        $client = new Client([
            'base_uri' => $this->supabaseUrl,
            'headers' => [
                'apikey' => $this->supabaseKey,
                'Authorization' => 'Bearer ' . $this->supabaseKey,
                'Accept' => 'application/json',
            ]
        ]);

        $response = $client->get('/rest/v1/Shop', [
            'query' => ['city' => 'eq.' . $city]
        ]);

        $pharmacies = json_decode($response->getBody(), true);

        return view('pharmacies.index', compact('pharmacies'));
    }
}
