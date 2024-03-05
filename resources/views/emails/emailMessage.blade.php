<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    @if ($orderData->user && !$orderData->customer)
        <div style="font-size: 20px; margin-bottom: 20px">Halo, <b>{{ $orderData->user->name }}!</b></div>
        <p>Pesanan tiket {{ $orderData->event->name }} Anda telah dikonfirmasi.</p>
        <p>Terimakasih telah menjadi bagian dari Sanggar Seni Naladerma dan berkontribusi dalam pelestarian kesenian
            Indonesia!</p>
    @elseif ($orderData->user && $orderData->customer)
        <div style="font-size: 20px; margin-bottom: 20px">Halo, <b>{{ $orderData->customer->name }}!</b></div>
        <p>Pesanan tiket {{ $orderData->event->name }} Anda telah dikonfirmasi.</p>
        <p>Terimakasih telah menjadi bagian dari Sanggar Seni Naladerma dan berkontribusi dalam pelestarian kesenian
            Indonesia!</p>
    @elseif ($orderData->customer && !$orderData->user)
        <div style="font-size: 20px; margin-bottom: 20px">Halo, <b>{{ $orderData->customer->name }}!</b></div>
        <p>Pesanan tiket {{ $orderData->event->name }} Anda telah dikonfirmasi.</p>
        <p>Terimakasih telah menjadi bagian dari Sanggar Seni Naladerma dan berkontribusi dalam pelestarian kesenian
            Indonesia!</p>
    @endif
</body>

</html>
