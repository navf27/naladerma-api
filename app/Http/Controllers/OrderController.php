<?php

namespace App\Http\Controllers;

use App\Mail\TicketMailer;
use App\Models\Customer;
use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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

        $me = auth()->user();

        $today = Carbon::today();

        $orderCount = Order::where('user_id', $me->id)
            ->whereDate('created_at', $today)
            ->count();

        if ($orderCount >= 2) {
            return response()->json([
                'status' => false,
                'message' => 'daily order limit',
            ], 400);
        }

        $eventData = Event::findOrFail($event_id);

        $filteredMe = [
            'name' => $me->name,
            'email' => $me->email,
            'phone' => $me->phone,
        ];
        $filteredReq = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ];

        $isSame = $filteredMe === $filteredReq;

        if ($isSame) {
            $orderData = [
                'user_id' => $me->id,
                'event_id' => $event_id,
                'quantity' => $request->quantity,
                'total' => $eventData->price * $request->quantity,
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
                'date' => Carbon::now(),
            ];

            $orderData = Order::create($orderData);
        }

        $params = array(
            'transaction_details' => array(
                'order_id' => $orderData->id + 30,
                'gross_amount' => $eventData->price * $request->quantity,
            ),
            'customer_details' => array(
                'first_name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
            ),
            'item_details' => array(
                array(
                    'id' => $eventData->id,
                    'price' => $eventData->price,
                    'quantity' => $request->quantity,
                    'name' => $eventData->name,
                ),
            ),
        );

        $snapToken = \Midtrans\Snap::getSnapToken($params);

        $thisOrder = Order::findOrFail($orderData->id);
        $thisOrder->snap_token = $snapToken;
        $thisOrder->save();

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

        $today = Carbon::today();

        $orderCount = Customer::where('email', $request->email)
            ->whereDate('created_at', $today)
            ->count();

        if ($orderCount >= 2) {
            return response()->json([
                'status' => false,
                'message' => 'daily order limit',
            ], 400);
        }

        $eventData = Event::findOrFail($event_id);

        $filteredReq = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ];

        $customerData = Customer::create($filteredReq);

        $orderData = [
            'customer_id' => $customerData->id,
            'event_id' => $event_id,
            'quantity' => $request->quantity,
            'total' => $eventData->price * $request->quantity,
            'date' => Carbon::now(),
        ];

        $orderData = Order::create($orderData);

        $params = array(
            'transaction_details' => array(
                'order_id' => $orderData->id + 30,
                'gross_amount' => $eventData->price * $request->quantity,
            ),
            'customer_details' => array(
                'first_name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
            ),
            'item_details' => array(
                array(
                    'id' => $eventData->id,
                    'price' => $eventData->price,
                    'quantity' => $request->quantity,
                    'name' => $eventData->name,
                ),
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

    public function finalTransaction($order_id)
    {
        // $order = Order::findOrFail($order_id);
        // $order['status'] = 'paid';
        // $order->save();

        $order = Order::with(['user', 'customer'])->get();

        for ($i = 0; $i < $order->quantity; $i++) {
            $id = $order->id . Carbon::now()->micro . mt_rand(100, 999);

            $ticket = new Ticket();
            $ticket->fill([
                'ticket_id' => $id,
                'order_id' => $order['id'],
                'ticket_link' => "http://localhost:8000/" . $id,
            ]);
            $ticket->save();
        }

        // for ($i = 0; $i < $order->quantity; $i++) {
        //     $pdf = Pdf::loadView('emails.ticketEmail');
        //     $pdfContent = $pdf->output();
        //     Storage::put('/tickets/ticket.pdf', $pdfContent);
        // }

        return response()->json([
            'status' => true,
            'message' => 'Payment success.',
            'data' => [
                'order_data' => $order,
                'ticket_created' => $order->quantity,
            ],
        ]);
    }

    public function midtransCallback(Request $request)
    {
        $serverKey = config('midtrans.serverKey');
        $hashed = hash('sha512', $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed == $request->signature_key) {
            if ($request->transaction_status == 'settlement' || $request->transaction_status == 'captured') {
                $orderData = Order::where('id', $request->order_id - 30)->with(['user', 'customer', 'event'])->first();
                $orderData->update(['status' => 'paid']);

                $eventData = Event::where('id', $orderData->event->id)->first();
                $eventData->update(['quota' => $eventData->quota - $orderData->quantity]);

                for ($i = 1; $i <= $orderData->quantity; $i++) {
                    $id = $orderData->id . Carbon::now()->micro . mt_rand(100, 999);

                    $ticket = new Ticket();
                    $ticket->fill([
                        'ticket_id' => $id,
                        'order_id' => $orderData['id'],
                        'ticket_link' => "https://sanggarnaladerma.my.id/api/use/" . $id,
                    ]);
                    $ticket->save();

                    $qrcode = QrCode::size(250)->generate($ticket->ticket_link);
                    $clear = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $qrcode);
                    $mpdf = new \Mpdf\Mpdf();
                    $mpdf->WriteHTML(view('emails.ticketEmail', compact('ticket', 'orderData', 'clear')));

                    if ($orderData->user && !$orderData->customer) {
                        $mpdf->Output(storage_path('app/tickets/') . $i . '-' . $ticket->ticket_id . '-' . $orderData->user->name . '.pdf', 'F');
                    } elseif ($orderData->user && $orderData->customer) {
                        $mpdf->Output(storage_path('app/tickets/') . $i . '-' . $ticket->ticket_id . '-' . $orderData->customer->name . '.pdf', 'F');
                    } elseif ($orderData->customer && !$orderData->user) {
                        $mpdf->Output(storage_path('app/tickets/') . $i . '-' . $ticket->ticket_id . '-' . $orderData->customer->name . '.pdf', 'F');
                    }
                }

                if ($orderData->user && !$orderData->customer) {
                    Mail::to($orderData->user->email)->send(new TicketMailer($orderData));
                } elseif ($orderData->user && $orderData->customer) {
                    Mail::to($orderData->user->email)->send(new TicketMailer($orderData));
                    Mail::to($orderData->customer->email)->send(new TicketMailer($orderData));
                } elseif ($orderData->customer && !$orderData->user) {
                    Mail::to($orderData->customer->email)->send(new TicketMailer($orderData));
                }

                $files = Storage::files('/tickets');

                foreach ($files as $file) {
                    Storage::delete($file);
                }
            }
        }
    }

    public function getPendingUsersOrder()
    {
        $me = auth()->user();

        $orders = Order::where(['user_id' => $me->id, 'status' => 'pending'])->with('event:id,name,img_link')->get();

        return response()->json([
            'status' => true,
            'data' => $orders,
            'message' => "Get users order data success.",
        ]);
    }

    public function getUserOrders()
    {
        $me = auth()->user();

        $orders = Order::where(['user_id' => $me->id, 'status' => 'paid'])->with('event:id,name,img_link,start_time,location,file_link')->get();

        return response()->json([
            'status' => true,
            'data' => $orders,
            'message' => "Get user orders data success.",
        ]);
    }

    public function destroy($id)
    {
        $event = Order::findOrFail($id);
        $event->delete();

        $response['status'] = true;
        $response['message'] = 'Delete transaction success.';
        $response['data'] = $event;

        return response()->json($response);
    }

    public function useTicket($ticket_id)
    {
        $ticket = Ticket::where('ticket_id', $ticket_id)->first();

        if ($ticket->ticket_used == true) {
            return response()->json([
                'status' => false,
                'data' => $ticket,
                'message' => "Tiket already used.",
            ], 406);
        }

        $ticket->ticket_used = true;
        $ticket->save();

        return response()->json([
            'status' => true,
            'data' => $ticket,
            'message' => "Tiket successfully used.",
        ]);
    }

    public function testing()
    {
        // TESTING CRON JOB //
        // $now = Carbon::now();
        // $pendingOrders = Order::where('status', 'pending')->get();
        // $pendingOrders = Order::where('status', 'pending')->get()->count();
        // $dataDeleted = 0;

        // foreach ($pendingOrders as $order) {
        //     $createdTime = Carbon::parse($order->created_at);
        //     // $diffInHours = $createdTime->diffInHours($now);
        //     $diffInDays = $createdTime->diffInDays($now);

        //     if ($diffInDays > 62) {
        //         $order->delete();
        //         $dataDeleted = $dataDeleted + 1;
        //     }
        // }

        // return response()->json(['message' => 'testing successfull', 'data' => $pendingOrders]);

        // TESTING ORDER LIMIT //
        $me = auth()->user();
        $today = Carbon::today();

        $orderCount = Order::where('user_id', $me->id)
            ->whereDate('created_at', $today)
            ->count();

        if ($orderCount >= 2) {
            return response()->json([
                'status' => false,
                'message' => 'daily order limit',
            ], 400);
        }

        return response()->json(['order_count' => $orderCount]);
    }
}
