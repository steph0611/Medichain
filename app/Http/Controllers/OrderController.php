<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    protected $supabaseUrl = 'https://zazdljyechhzsiodnvts.supabase.co';
    protected $supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InphemRsanllY2hoenNpb2RudnRzIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTMwMjE2MzMsImV4cCI6MjA2ODU5NzYzM30.OZLL_quXsqD2PJEtyQjSBOR9SaZBVXvaTfoAcBYCZTM';

    /**
     * Supabase client helper
     */
    private function supabaseClient()
    {
        return new Client([
            'base_uri' => $this->supabaseUrl,
            'headers' => [
                'apikey' => $this->supabaseKey,
                'Authorization' => 'Bearer ' . $this->supabaseKey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'verify' => false  // Disable SSL verification for development
        ]);
    }

    /**
     * Store a new order in the main Supabase orders table
     */
    public function store(array $data)
    {
        $client = $this->supabaseClient();

        // 1️⃣ Validate required fields
        $requiredFields = [
            'customer_id', 'shop_id', 'prescription_id',
            'patient_name', 'doctor_name', 'prescription_date',
            'address', 'phone', 'email'
        ];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new \Exception("Order creation failed: Missing required field '{$field}'.");
            }
        }

        $payload = [
            'customer_id'      => (string)$data['customer_id'],   // varchar
            'shop_id'          => (int)$data['shop_id'],          // bigint
            'prescription_id'  => $data['prescription_id'],       // uuid
            'patient_name'     => $data['patient_name'],
            'doctor_name'      => $data['doctor_name'],
            'prescription_date'=> $data['prescription_date'],
            'address'          => $data['address'],
            'phone'            => $data['phone'],
            'email'            => $data['email'],
            'instructions'     => $data['instructions'] ?? null,
            'status'           => 'Pending',
            'created_at'       => Carbon::now()->toISOString(),
        ];
        
        try {
            $response = $client->post('/rest/v1/orders', [
                'headers' => ['Prefer' => 'return=representation'],
                'json' => $payload,
            ]);

            $body = (string)$response->getBody();
            $statusCode = $response->getStatusCode();

            // 2️⃣ Check response status
            if ($statusCode !== 201) {
                \Log::error("Supabase order insert failed", [
                    'status' => $statusCode,
                    'payload' => $payload,
                    'response' => $body,
                ]);
                throw new \Exception("Failed to create order. Response: {$body}");
            }

            // 3️⃣ Decode inserted order
            $orderData = json_decode($body, true);
            if (empty($orderData[0]['id'])) {
                \Log::error("Order inserted but no ID returned. Response: {$body}");
                throw new \Exception("Order created but no ID returned.");
            }

            return $orderData[0];

        } catch (\Exception $e) {
            \Log::error("Order creation exception: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Show all orders for the logged-in customer
     */
    public function myOrders()
    {
        $customerId = Auth::id() ?? session('user')['id'] ?? null;
        if (!$customerId) {
            return redirect('/login')->with('error', 'User not authenticated.');
        }

        try {
            $client = $this->supabaseClient();

            $response = $client->get('/rest/v1/orders', [
                'query' => [
                    'customer_id' => 'eq.' . $customerId,
                    'order' => 'created_at.desc',
                    'select' => '*',
                ]
            ]);

            $orders = json_decode($response->getBody(), true) ?? [];
            return view('my-orders', compact('orders'));

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to fetch orders: ' . $e->getMessage());
        }
    }

    /**
     * Admin/Pharmacy: View all orders in system
     */
    public function allOrders()
    {
        try {
            $client = $this->supabaseClient();

            $response = $client->get('/rest/v1/orders', [
                'query' => [
                    'select' => '*',
                    'order' => 'created_at.desc'
                ]
            ]);

            $orders = json_decode($response->getBody(), true) ?? [];
            return view('all-orders', compact('orders'));

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to fetch all orders: ' . $e->getMessage());
        }
    }

    /**
     * Update the status of an order
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:Pending,Accepted,Ready,Delivered,Cancelled',
        ]);

        try {
            $client = $this->supabaseClient();

            $response = $client->patch('/rest/v1/orders?id=eq.' . $id, [
                'json' => ['status' => $request->status],
            ]);

            if ($response->getStatusCode() !== 204) {
                return back()->with('error', 'Failed to update order status.');
            }

            return back()->with('success', "Order status updated to {$request->status}!");

        } catch (\Exception $e) {
            return back()->with('error', 'Error updating order status: ' . $e->getMessage());
        }
    }

    /**
     * Delete an order
     */
    public function delete($id)
    {
        try {
            $client = $this->supabaseClient();

            $response = $client->delete('/rest/v1/orders?id=eq.' . $id);

            if ($response->getStatusCode() !== 204) {
                return back()->with('error', 'Failed to delete order.');
            }

            return back()->with('success', 'Order deleted successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting order: ' . $e->getMessage());
        }
    }
}
