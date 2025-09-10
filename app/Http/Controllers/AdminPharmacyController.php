<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class AdminPharmacyController extends Controller
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
     * Show all pharmacies
     */
    public function index()
    {
        try {
            $client = $this->supabaseClient();
            $res = $client->get('/rest/v1/Shop', [
                'query' => ['select' => '*']
            ]);
            $pharmacies = json_decode((string) $res->getBody(), true) ?? [];

            return view('admin.pharmacies', compact('pharmacies'));
        } catch (\Exception $e) {
            \Log::error('Error fetching pharmacies: ' . $e->getMessage());
            return view('admin.pharmacies', ['pharmacies' => [], 'error' => $e->getMessage()]);
        }
    }

    /**
     * Add a new pharmacy
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string',
            'user_name' => 'required|string',
            'email'     => 'required|email',
            'password'  => 'required|string|min:6',
        ]);

        try {
            $client = $this->supabaseClient();

            $client->post('/rest/v1/Shop', [
                'json' => [
                    'name'       => $request->name,
                    'user_name'  => $request->user_name,
                    'email'      => $request->email,
                    'password'   => bcrypt($request->password),
                    'created_at' => now()->toISOString(),
                ]
            ]);

            return redirect()->route('admin.pharmacies.index')->with('success', 'Pharmacy added successfully!');
        } catch (\Exception $e) {
            \Log::error('Error adding pharmacy: ' . $e->getMessage());
            return redirect()->back()->withErrors(['Failed to add pharmacy: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete a pharmacy
     */
    public function destroy($id)
    {
        try {
            $client = $this->supabaseClient();

            $client->delete('/rest/v1/Shop', [
                'query' => ['shop_id' => 'eq.' . $id]
            ]);

            return redirect()->route('admin.pharmacies.index')->with('success', 'Pharmacy removed successfully!');
        } catch (\Exception $e) {
            \Log::error('Error deleting pharmacy: ' . $e->getMessage());
            return redirect()->back()->withErrors(['Failed to remove pharmacy: ' . $e->getMessage()]);
        }
    }
}
