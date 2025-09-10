<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class AdminPrescriptionController extends Controller
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
        ]);
    }

    /**
     * Show all pharmacies as tiles
     */
    public function index()
    {
        try {
            $client = $this->supabaseClient();
            $res = $client->get('/rest/v1/Shop?select=shop_id,name');
            $pharmacies = json_decode((string) $res->getBody(), true) ?? [];

            return view('admin.prescriptions', compact('pharmacies'));
        } catch (\Exception $e) {
            \Log::error('Error fetching pharmacies: ' . $e->getMessage());
            return view('admin.prescriptions', ['pharmacies' => [], 'error' => $e->getMessage()]);
        }
    }

    /**
     * Show all prescriptions for a pharmacy
     */
    public function show($shop_id)
    {
        try {
            $client = $this->supabaseClient();

            // Fetch all prescriptions for the selected pharmacy
            $res = $client->get('/rest/v1/prescriptions', [
                'query' => [
                    'select'  => '*',
                    'shop_id' => 'eq.' . $shop_id
                ]
            ]);
            $prescriptions = json_decode((string) $res->getBody(), true) ?? [];

            // Fetch pharmacy info
            $shopRes = $client->get('/rest/v1/Shop', [
                'query' => [
                    'select'  => '*',
                    'shop_id' => 'eq.' . $shop_id
                ]
            ]);
            $pharmacyData = json_decode((string) $shopRes->getBody(), true);
            $pharmacy = $pharmacyData[0] ?? null;

            return view('admin.prescriptions-list', compact('prescriptions', 'pharmacy'));
        } catch (\Exception $e) {
            \Log::error('Error fetching prescriptions: ' . $e->getMessage());
            return redirect()->back()->withErrors(['Failed to fetch prescriptions: ' . $e->getMessage()]);
        }
    }
}
