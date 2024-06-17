<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    <p>Klik tombol di bawah untuk reset password!</p>
    <a href={{ 'http://sanggarnaladerma.my.id/reset-password/' . $token }}>
        <button
            style=" padding-right: 20px; padding-left: 20px; padding-top: 10px; padding-bottom: 10px; background-color: #FFCC00; border: 0px; border-radius: 5px">
            <span style="color: black">Reset Password</span>
        </button>
    </a>
    {{-- <p>Token : {{ $token }}</p> --}}
</body>

</html>
