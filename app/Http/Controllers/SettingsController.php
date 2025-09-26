<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class SettingsController extends Controller
{
    private $client;
    private $supabaseUrl;
    private $supabaseKey;

    public function __construct()
    {
        $this->supabaseUrl = rtrim(env('SUPABASE_URL'), '/');
        $this->supabaseKey = env('SUPABASE_KEY');

        $this->client = new Client([
            'base_uri' => $this->supabaseUrl . '/rest/v1/',
            'headers'  => [
                'apikey'        => $this->supabaseKey,
                'Authorization' => 'Bearer ' . $this->supabaseKey,
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
            ],
            'verify' => false  // Disable SSL verification for development
        ]);
    }

    public function index(Request $request)
    {
        $user = session('user');
        $role = session('role');

        if (!$user || $role !== 'customer') {
            return redirect('/login')->withErrors(['Please log in as a customer first.']);
        }

        try {
            $response = $this->client->get("customer?customer_id=eq.{$user['customer_id']}&select=settings");
            $data = json_decode($response->getBody(), true);

            // Default settings
            $defaults = [
                'email_notifications' => true,
                'sms_notifications'   => false,
                'theme'               => 'light',
                'show_location'       => true,
                'show_phone'          => false,
            ];

            if (empty($data)) {
                $settings = $defaults;
            } else {
                $dbSettings = $data[0]['settings'] ?? [];
                $settings = array_merge($defaults, $dbSettings);
            }

        } catch (\Exception $e) {
            $settings = [
                'email_notifications' => true,
                'sms_notifications'   => false,
                'theme'               => 'light',
                'show_location'       => true,
                'show_phone'          => false,
            ];
        }

        return view('customer.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $user = session('user');
        $role = session('role');

        if (!$user || $role !== 'customer') {
            return redirect('/login')->withErrors(['Please log in as a customer first.']);
        }

        $newSettings = [
            'email_notifications' => $request->has('email_notifications'),
            'sms_notifications'   => $request->has('sms_notifications'),
            'theme'               => $request->input('theme', 'light'),
            'show_location'       => $request->has('show_location'),
            'show_phone'          => $request->has('show_phone'),
        ];

        $this->updateSettings($user['customer_id'], $newSettings);

        return redirect()->back()->with('success', 'Settings updated successfully!');
    }

    private function updateSettings($customerId, $newSettings)
    {
        $response = $this->client->get("customer?customer_id=eq.$customerId&select=settings");
        $data = json_decode($response->getBody(), true);

        $defaults = [
            'email_notifications' => true,
            'sms_notifications'   => false,
            'theme'               => 'light',
            'show_location'       => true,
            'show_phone'          => false,
        ];

        $current = (!empty($data) && isset($data[0]['settings']))
            ? $data[0]['settings']
            : [];

        $merged = array_merge($defaults, $current, $newSettings);

        $this->client->patch("customer?customer_id=eq.$customerId", [
            'json' => ['settings' => $merged]
        ]);
    }
}
