<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class AdminUserController extends Controller
{
    private $supabaseUrl;
    private $supabaseKey;

    private array $tableMap = [
        'admins'     => ['table' => 'admin',     'id' => 'id'],
        'pharmacies' => ['table' => 'Shop',       'id' => 'shop_id'],
        'customers'  => ['table' => 'customer',  'id' => 'customer_id'],
    ];

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

    /**
     * Show tiles for Admins, Pharmacies, Customers
     */
    public function index()
    {
        return view('admin.users'); // Overview tiles page
    }

    /**
     * Show table for a specific user type
     */
    public function show($type)
    {
        if (!isset($this->tableMap[$type])) abort(404);

        $client = $this->supabaseClient();
        $table = $this->tableMap[$type]['table'];
        $idField = $this->tableMap[$type]['id'];

        try {
            $res = $client->get("/rest/v1/{$table}?select=*");
            $users = json_decode($res->getBody(), true) ?? [];

            return view('admin.users-list', [
                'users' => $users,
                'userType' => $type,
                'idField' => $idField,
            ]);
        } catch (\Exception $e) {
            \Log::error("Error fetching {$type}: " . $e->getMessage());
            return redirect()->back()->withErrors(['Failed to fetch ' . $type . ': ' . $e->getMessage()]);
        }
    }

    /**
     * Store a new user
     */
    public function store(Request $request, $type)
    {
        if (!isset($this->tableMap[$type])) abort(404);

        $client = $this->supabaseClient();
        $table = $this->tableMap[$type]['table'];

        $data = $request->only(['name', 'username', 'email', 'password']);
        if ($type === 'pharmacies') $data['shop_name'] = $data['name'];

        try {
            $client->post("/rest/v1/{$table}", ['json' => $data]);
            return redirect()->back()->with('success', ucfirst($type) . ' added successfully!');
        } catch (\Exception $e) {
            \Log::error("Error adding {$type}: " . $e->getMessage());
            return redirect()->back()->withErrors(['Failed to add ' . $type . ': ' . $e->getMessage()]);
        }
    }

    /**
     * Delete a user
     */
    public function destroy($type, $id)
    {
        if (!isset($this->tableMap[$type])) abort(404);

        $client = $this->supabaseClient();
        $table = $this->tableMap[$type]['table'];
        $idField = $this->tableMap[$type]['id'];

        try {
            $client->delete("/rest/v1/{$table}?{$idField}=eq.{$id}");
            return redirect()->back()->with('success', ucfirst($type) . ' deleted successfully!');
        } catch (\Exception $e) {
            \Log::error("Error deleting {$type}: " . $e->getMessage());
            return redirect()->back()->withErrors(['Failed to delete ' . $type . ': ' . $e->getMessage()]);
        }
    }
}
