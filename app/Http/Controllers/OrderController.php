<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Event;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function checkoutUser(Request $request, $event_id)
    {
        // Set your Merchant Server Key
        \Midtrans\Config::$serverKey = config('midtrans.serverKey');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = false;
        // Set sanitization on (default)
        \Midtrans\Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        \Midtrans\Config::$is3ds = true;

        $eventData = Event::findOrFail($event_id);
        $me = auth()->user();

        $filteredMe = [
            'name' => $me->name,
            'email' => $me->email,
            'phone' => $me->phone,
            'address' => $me->address,
        ];
        $filteredReq = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ];

        $isSame = $filteredMe === $filteredReq;

        if ($isSame) {
            $orderData = [
                'user_id' => $me->id,
                'event_id' => $event_id,
                'quantity' => $request->quantity,
                'total' => $eventData->price * $request->quantity,
                'status' => 'pending',
                'date' => Carbon::now(),
            ];

            $orderData = Order::create($orderData);
        } else {
            $customerData = Customer::create($filteredReq);

            $orderData = [
                'user_id' => $me->id,
                'customer_id' => $customerData->id,
                'event_id' => $event_id,
                'quantity' => $request->quantity,
                'total' => $eventData->price * $request->quantity,
                'status' => 'pending',
                'date' => Carbon::now(),
            ];

            $orderData = Order::create($orderData);
        }

        $params = array(
            'transaction_details' => array(
                'order_id' => 'ORDERID-' . $me->id . $event_id . $orderData->id . mt_rand(100, 999),
                'gross_amount' => $eventData->price * $request->quantity,
            ),
            'customer_details' => array(
                'first_name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
            ),
        );

        $snapToken = \Midtrans\Snap::getSnapToken($params);

        return response()->json([
            'status' => true,
            'message' => 'Create snap token success',
            'data' => [
                'orderId' => $orderData->id,
                'snapToken' => $snapToken,
            ],
        ]);
    }

    public function checkoutCustomer(Request $request, $event_id)
    {
        // Set your Merchant Server Key
        \Midtrans\Config::$serverKey = config('midtrans.serverKey');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = false;
        // Set sanitization on (default)
        \Midtrans\Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        \Midtrans\Config::$is3ds = true;

        $eventData = Event::findOrFail($event_id);

        $filteredReq = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ];

        $customerData = Customer::create($filteredReq);

        $orderData = [
            'customer_id' => $customerData->id,
            'event_id' => $event_id,
            'quantity' => $request->quantity,
            'total' => $eventData->price * $request->quantity,
            'status' => 'pending',
            'date' => Carbon::now(),
        ];

        $orderData = Order::create($orderData);

        $params = array(
            'transaction_details' => array(
                'order_id' => 'ORDERID-' . $customerData->id . $event_id . $orderData->id . mt_rand(100, 999),
                'gross_amount' => $eventData->price * $request->quantity,
            ),
            'customer_details' => array(
                'first_name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
            ),
        );

        $snapToken = \Midtrans\Snap::getSnapToken($params);

        return response()->json([
            'status' => true,
            'message' => 'Create snap token success',
            'data' => [
                'orderId' => $orderData->id,
                'snapToken' => $snapToken,
            ],
        ]);
    }

    public function setPaymentStatus($order_id)
    {
        $order = Order::findOrFail($order_id);
        $order['status'] = 'paid';
        $order->save();

        return response()->json([
            'status' => true,
            'message' => 'Set payment status success.',
            'data' => $order,
        ]);
    }
}
