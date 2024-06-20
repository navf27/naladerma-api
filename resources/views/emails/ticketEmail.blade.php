{{-- <!DOCTYPE html>
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

</html> --}}

{{-- <h2>INI TIKET BERHASIL DIBUAT</h2> --}}

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Custom CSS demo</title>
    <style>
        /* Reset CSS */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Container style */
        .container {
            width: 90%;
            margin: 0 auto;
        }

        /* Header style */
        .header {
            /* width: 100%; */
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            /* padding: 10px; */
        }

        /* Main content style */
        main {
            padding-top: 20px;
        }

        /* Table style */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        .inside-table {
            border: none
        }

        th,
        td {
            border: 1px solid #dee2e6;
            padding: 8px;
        }

        th {
            background-color: #f8f9fa;
            text-align: left;
        }

        /* Additional style */
        .mt-2 {
            margin-top: 20px;
        }

        .fw-bold {
            font-weight: bold;
        }
    </style>
</head>

<body style="font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif">
    <div class="container">
        <div class="header">
            <div style="margin-bottom: 20px; font-size: 20px"><b>Naladerma</b></div>
            <div>
                <b>ID Tiket:</b> <br>{{ $ticket->ticket_id }} <div style="text-align: right"> <b>Waktu Pemesanan:</b>
                    <br>
                    {{ $orderData->updated_at }}
                </div>
            </div>
        </div>
        <main>
            <table>
                <thead>
                    <tr>
                        <th>Detail Pemesan</th>
                        <th>Detail Event</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <table class="inside-table">
                                <tbody>
                                    @if ($orderData->user && !$orderData->customer)
                                        <tr class="inside-table">
                                            <td class="inside-table">Nama</td>
                                            <td class="inside-table">:</td>
                                            <td class="inside-table">{{ $orderData->user->name }}</td>
                                        </tr>
                                        <tr class="inside-table">
                                            <td class="inside-table">Email</td>
                                            <td class="inside-table">:</td>
                                            <td class="inside-table">{{ $orderData->user->email }}</td>
                                        </tr>
                                        <tr class="inside-table">
                                            <td class="inside-table">Telepon</td>
                                            <td class="inside-table">:</td>
                                            <td class="inside-table">{{ $orderData->user->phone }}</td>
                                        </tr>
                                    @elseif ($orderData->user && $orderData->customer)
                                        <tr class="inside-table">
                                            <td class="inside-table">Nama</td>
                                            <td class="inside-table">:</td>
                                            <td class="inside-table">{{ $orderData->customer->name }}</td>
                                        </tr>
                                        <tr class="inside-table">
                                            <td class="inside-table">Email</td>
                                            <td class="inside-table">:</td>
                                            <td class="inside-table">{{ $orderData->customer->email }}</td>
                                        </tr>
                                        <tr class="inside-table">
                                            <td class="inside-table">Telepon</td>
                                            <td class="inside-table">:</td>
                                            <td class="inside-table">{{ $orderData->customer->phone }}</td>
                                        </tr>
                                    @elseif ($orderData->customer && !$orderData->user)
                                        <tr class="inside-table">
                                            <td class="inside-table">Nama</td>
                                            <td class="inside-table">:</td>
                                            <td class="inside-table">{{ $orderData->customer->name }}</td>
                                        </tr>
                                        <tr class="inside-table">
                                            <td class="inside-table">Email</td>
                                            <td class="inside-table">:</td>
                                            <td class="inside-table">{{ $orderData->customer->email }}</td>
                                        </tr>
                                        <tr class="inside-table">
                                            <td class="inside-table">Telepon</td>
                                            <td class="inside-table">:</td>
                                            <td class="inside-table">{{ $orderData->customer->phone }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </td>
                        <td>
                            <table class="inside-table">
                                <tbody>
                                    <tr class="inside-table">
                                        <td class="inside-table">Nama</td>
                                        <td class="inside-table">:</td>
                                        <td class="inside-table">{{ $orderData->event->name }}</td>
                                    </tr>
                                    <tr class="inside-table">
                                        <td class="inside-table">Lokasi</td>
                                        <td class="inside-table">:</td>
                                        <td class="inside-table">{{ $orderData->event->location }}</td>
                                    </tr>
                                    <tr class="inside-table">
                                        <td class="inside-table">Tanggal</td>
                                        <td class="inside-table">:</td>
                                        <td class="inside-table">
                                            {{ \Carbon\Carbon::parse($orderData->event->start_time)->translatedFormat('l, d F Y') }}
                                        </td>
                                        {{-- <td class="inside-table">
                                            {{ date('Y-m-d', strtotime($orderData->event->start_time)) }}</td> --}}
                                    </tr>
                                    <tr class="inside-table">
                                        <td class="inside-table">Waktu</td>
                                        <td class="inside-table">:</td>
                                        <td class="inside-table">
                                            {{ date('H:i', strtotime($orderData->event->start_time)) }} WIB</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="mt-2">
                <span class="fw-bold">Scan disini untuk masuk</span>
                <div class="mt-2">
                    {!! $clear !!}
                </div>
            </div>
        </main>
    </div>
</body>

</html>
