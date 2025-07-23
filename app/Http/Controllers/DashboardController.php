<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'activeOrders' => 3,
            'preferredPharmacies' => 5,
            'totalSaved' => 127,
            'ordersThisYear' => 12,
            'orders' => [
                [
                    'id' => '#MC2025010',
                    'pharmacy' => 'HealthPlus Pharmacy',
                    'medication' => 'Lisinopril 10mg',
                    'status' => 'READY',
                    'total' => 24.99,
                    'action' => 'Get Directions',
                ],
                [
                    'id' => '#MC2025011',
                    'pharmacy' => 'MedExpress',
                    'medication' => 'Metformin 1000mg',
                    'status' => 'READY',
                    'total' => 18.75,
                    'action' => 'Track',
                ],
                [
                    'id' => '#MC2025009',
                    'pharmacy' => 'Community Pharmacy',
                    'medication' => 'Vitamin D3',
                    'status' => 'DELIVERED',
                    'total' => 15.50,
                    'action' => 'Reorder',
                ],
            ]
        ];

        return view('dashboard', $data);
    }
}
