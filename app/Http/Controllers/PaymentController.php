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
            ]
        ]);
    }

    // Show payment page
    public function showPaymentForm(Request $request)
    {
        $prescriptionId = $request->prescription_id;
        $amount = $request->amount;

        return view('customer.payment', compact('prescriptionId', 'amount'));
    }

    // Process payment
    public function processPayment(Request $request)
    {
        $request->validate([
            'prescription_id' => 'required',
            'amount' => 'required|numeric',
            'payment_method_id' => 'required'
        ]);

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $request->amount * 100, // amount in cents
                'currency' => 'usd',
                'payment_method' => $request->payment_method_id,
                'confirmation_method' => 'manual',
                'confirm' => true,
                'return_url' => route('payment.success', ['prescription_id' => $request->prescription_id])
            ]);

            if ($paymentIntent->status === 'requires_action') {
                return response()->json([
                    'requires_action' => true,
                    'payment_intent_client_secret' => $paymentIntent->client_secret
                ]);
            }

            // Payment succeeded → update prescription
            $client = $this->supabaseClient();
            $client->patch("/rest/v1/prescriptions?id=eq.{$request->prescription_id}", [
                'json' => [
                    'status' => 'Paid',
                    'payment_id' => $paymentIntent->id
                ]
            ]);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    // Success page after 3D secure / redirect payments
    public function paymentSuccess(Request $request)
    {
        $prescriptionId = $request->prescription_id;
        return view('customer.payment-success', compact('prescriptionId'));
    }
}
