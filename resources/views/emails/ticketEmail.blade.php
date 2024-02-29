<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    <h2>Ini email ticket.</h2>
    <h3>Data</h3>
    Id tiket : {{ $ticket->ticket_id }} <br>
    @if ($orderData->user)
        Nama pembeli (user) : {{ $orderData->user->name }} <br>
        Email pembeli : {{ $orderData->user->email }} <br>
        Waktu transaksi : {{ $orderData->updated_at }} <br>
        Total pembayaran : {{ $orderData->event->price * $orderData->quantity }} <br>
    @elseif ($orderData->customer && !$orderData->user)
        Nama pembeli (customer) : {{ $orderData->customer->name }} <br>
        Email pembeli : {{ $orderData->customer->email }} <br>
        Waktu transaksi : {{ $orderData->updated_at }} <br>
        Total pembayaran : {{ $orderData->event->price * $orderData->quantity }} <br>
    @endif
    Ticket link : {{ $ticket->ticket_link }}
</body>

</html>
