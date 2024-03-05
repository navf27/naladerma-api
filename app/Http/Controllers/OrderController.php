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
                'order_id' => $orderData->id,
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
            'date' => Carbon::now(),
        ];

        $orderData = Order::create($orderData);

        $params = array(
            'transaction_details' => array(
                'order_id' => $orderData->id,
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
                $orderData = Order::where('id', $request->order_id)->with(['user', 'customer', 'event'])->first();
                $orderData->update(['status' => 'paid']);

                for ($i = 1; $i <= $orderData->quantity; $i++) {
                    $id = $orderData->id . Carbon::now()->micro . mt_rand(100, 999);

                    $ticket = new Ticket();
                    $ticket->fill([
                        'ticket_id' => $id,
                        'order_id' => $orderData['id'],
                        'ticket_link' => "http://localhost:8000/" . $id,
                    ]);
                    $ticket->save();

                    $qrcode = QrCode::size(150)->generate($ticket->ticket_link);
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

                if ($orderData->user) {
                    Mail::to($orderData->user->email)->send(new TicketMailer($orderData));
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

    public function testing()
    {
        $qrcode = QrCode::size(150)->generate('A basic of Qcode!');
        $clear = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $qrcode);

        // require_once __DIR__ . '/vendor/autoload.php';
        $mpdf = new \Mpdf\Mpdf();
        $mpdf->WriteHTML(view('emails.ticketEmail', ['qr' => $clear]));
        $mpdf->Output(storage_path('app/tickets/') . 'ticket-test.pdf', 'F');

        // return $qr;

        // $pdf = Pdf::loadView('emails.ticketEmail');
        // return $pdf->stream();

        // return Pdf::loadFile(public_path() . '/ticket.html')->stream('download.pdf');
        // return Pdf::loadFile(storage_path('app/'))->stream('ticket.pdf');

        // Storage::put('/tickets/ticket.pdf', $pdfContent);

        // return response()->json(['message' => 'Send email success.']);

        // Mail::to('muhnaufalaji@gmail.com')->send(new TicketMailer);
        // return view('emails.ticketEmail', ['qr' => $qr]);

        // return QrCode::generate(
        //     'Hello, World!',
        // );
    }
}
