<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use GuzzleHttp\Client;

class PaymentController extends Controller
{
    protected $supabaseUrl = 'https://zazdljyechhzsiodnvts.supabase.co';
    protected $supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InphemRsanllY2hoenNpb2RudnRzIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTMwMjE2MzMsImV4cCI6MjA2ODU5NzYzM30.OZLL_quXsqD2PJEtyQjSBOR9SaZBVXvaTfoAcBYCZTM';

    private function supabaseClient()
    {
        return new Client([
            'base_uri' => $this->supabaseUrl,
            'headers'  => [
                'apikey'        => $this->supabaseKey,
                'Authorization' => 'Bearer ' . $this->supabaseKey,
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
            ],
            'verify' => false  // Disable SSL verification for development
        ]);
    }

    // Show payment page
    public function showPaymentForm(Request $request)
    {
        $amount = $request->amount;
        
        // Check if we have pending prescription data in session
        $pendingPrescription = session('pending_prescription');
        
        if (!$pendingPrescription) {
            return redirect('/dashboard')->with('error', 'No prescription data found. Please upload a prescription first.');
        }

        return view('customer.payment', compact('amount'));
    }

    // Process payment
    public function processPayment(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'payment_method_id' => 'required'
        ]);

        // Check if we have pending prescription data
        $pendingPrescription = session('pending_prescription');
        
        if (!$pendingPrescription) {
            return response()->json(['error' => 'No prescription data found. Please upload a prescription first.']);
        }

        // Check if this is demo mode
        if ($request->has('demo_mode') && $request->demo_mode) {
            return $this->processDemoPayment($request);
        }

        // Production mode with Stripe
        $stripeSecret = env('STRIPE_SECRET');
        
        if (!$stripeSecret) {
            return response()->json(['error' => 'Stripe is not configured. Please set up your Stripe keys in the .env file.']);
        }

        Stripe::setApiKey($stripeSecret);

        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $request->amount * 100, // amount in cents
                'currency' => 'usd',
                'payment_method' => $request->payment_method_id,
                'confirmation_method' => 'manual',
                'confirm' => true,
                'return_url' => route('payment.success')
            ]);

            if ($paymentIntent->status === 'requires_action') {
                return response()->json([
                    'requires_action' => true,
                    'payment_intent_client_secret' => $paymentIntent->client_secret
                ]);
            }

            // Payment succeeded → store prescription in database
            $prescriptionId = $this->storePrescriptionAfterPayment($pendingPrescription, $paymentIntent->id);

            // Store prescription ID in session for success page
            session(['last_prescription_id' => $prescriptionId]);

            // Clear session data
            session()->forget('pending_prescription');

            return response()->json(['success' => true, 'prescription_id' => $prescriptionId]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    // Demo payment processing
    private function processDemoPayment(Request $request)
    {
        try {
            // Simulate payment processing delay
            sleep(1);
            
            // Get pending prescription data
            $pendingPrescription = session('pending_prescription');
            
            if (!$pendingPrescription) {
                return response()->json(['error' => 'No prescription data found.']);
            }
            
            // Store prescription in database after successful demo payment
            $prescriptionId = $this->storePrescriptionAfterPayment($pendingPrescription, 'demo_payment_' . time());

            // Store prescription ID in session for success page
            session(['last_prescription_id' => $prescriptionId]);

            // Clear session data
            session()->forget('pending_prescription');

            return response()->json(['success' => true, 'demo_mode' => true, 'prescription_id' => $prescriptionId]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Demo payment failed: ' . $e->getMessage()]);
        }
    }

    // Store prescription in database after successful payment
    private function storePrescriptionAfterPayment($prescriptionData, $paymentId)
    {
        try {
            $client = $this->supabaseClient();
            
            // Add payment information to prescription data
            $prescriptionData['status'] = 'Paid';
            $prescriptionData['payment_id'] = $paymentId;
            
            // Store prescription in database
            $prescriptionResponse = $client->post('/rest/v1/prescriptions', [
                'json' => [$prescriptionData],
                'headers' => ['Prefer' => 'return=representation']
            ]);

            if ($prescriptionResponse->getStatusCode() !== 201) {
                throw new \Exception('Failed to store prescription in database');
            }

            $prescriptionResult = json_decode($prescriptionResponse->getBody(), true)[0] ?? null;
            $prescriptionId = $prescriptionResult['id'] ?? null;

            if (!$prescriptionId) {
                throw new \Exception('Prescription stored but ID missing');
            }

            // Create order record
            $this->createOrderRecord($prescriptionData, $prescriptionId);

            return $prescriptionId;

        } catch (\Exception $e) {
            \Log::error('Failed to store prescription after payment: ' . $e->getMessage());
            throw $e;
        }
    }

    // Create order record
    private function createOrderRecord($prescriptionData, $prescriptionId)
    {
        try {
            $client = $this->supabaseClient();
            
            $orderPayload = [
                'order_id'         => \Illuminate\Support\Str::uuid()->toString(),
                'prescription_id'  => $prescriptionId,
                'customer_id'      => $prescriptionData['customer_id'],
                'shop_id'          => $prescriptionData['shop_id'],
                'patient_name'     => $prescriptionData['patient_name'],
                'doctor_name'      => $prescriptionData['doctor_name'],
                'prescription_date'=> $prescriptionData['prescription_date'],
                'address'          => $prescriptionData['address'],
                'phone'            => $prescriptionData['phone'],
                'email'            => $prescriptionData['email'],
                'instructions'     => $prescriptionData['instructions'],
                'image_data'       => $prescriptionData['image_data'],
                'image_type'       => $prescriptionData['image_type'],
                'status'           => 'Paid',
                'amount'           => 50.00, // Default amount
                'payment_id'       => $prescriptionData['payment_id'],
                'created_at'       => \Carbon\Carbon::now()->toISOString(),
            ];

            $orderResponse = $client->post('/rest/v1/orders', [
                'json' => [$orderPayload],
                'headers' => ['Prefer' => 'return=representation']
            ]);

            if ($orderResponse->getStatusCode() !== 201) {
                \Log::error('Failed to create order record');
            }

        } catch (\Exception $e) {
            \Log::error('Failed to create order record: ' . $e->getMessage());
        }
    }

    // Success page after 3D secure / redirect payments
    public function paymentSuccess(Request $request)
    {
        // Get prescription ID from session or request
        $prescriptionId = $request->prescription_id ?? session('last_prescription_id');
        
        if (!$prescriptionId) {
            return redirect('/dashboard')->with('error', 'Payment successful but prescription ID not found.');
        }
        
        return view('customer.payment-success', compact('prescriptionId'));
    }
}
