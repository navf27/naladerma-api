<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|max:255|email',
            'phone' => 'required|max:20',
            'address' => 'required|max:255',
        ]);

        $customer = Customer::create($validated);

        $response['status'] = true;
        $response['data'] = $customer;

        return response()->json($response);
    }
}
