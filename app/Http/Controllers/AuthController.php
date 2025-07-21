<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class AuthController extends Controller
{
    protected $supabaseUrl = 'https://zazdljyechhzsiodnvts.supabase.co';
    protected $supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InphemRsanllY2hoenNpb2RudnRzIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTMwMjE2MzMsImV4cCI6MjA2ODU5NzYzM30.OZLL_quXsqD2PJEtyQjSBOR9SaZBVXvaTfoAcBYCZTM'; 

     public function showLoginForm()
    {
        return view('login'); 
    }

        public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        $username = $request->input('username');
        $password = $request->input('password');

        try {
            $client = new \GuzzleHttp\Client([
                'base_uri' => $this->supabaseUrl,
                'headers' => [
                    'apikey' => $this->supabaseKey,
                    'Authorization' => 'Bearer ' . $this->supabaseKey,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ]
            ]);

            $response = $client->get('/rest/v1/customer', [
                'query' => [
                    'username' => 'eq.' . $username,
                    'select' => '*'
                ]
            ]);

            $body = $response->getBody()->getContents();
            $users = json_decode($body, true);

            if (empty($users)) {
                return back()->withErrors(['No user found with this username'])->withInput();
            }

            $user = $users[0];

            if ($user['password'] !== $password) {
                return back()->withErrors(['Incorrect password'])->withInput();
            }

            session(['user' => $user]);
            return redirect('/dashboard')->with('success', 'Login successful!');

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $statusCode = $e->hasResponse() ? $e->getResponse()->getStatusCode() : 'N/A';
            $errorMessage = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage();

            \Log::error('Supabase login error', [
                'status' => $statusCode,
                'message' => $errorMessage
            ]);

            return back()->withErrors(['An error occurred while trying to login. Please try again later.']);
        } catch (\Exception $e) {
            \Log::error('Unexpected error during login', ['message' => $e->getMessage()]);
            return back()->withErrors(['Unexpected error. Please contact support.']);
        }
    }
}