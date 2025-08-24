<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'MediChain')</title>
    <link rel="stylesheet" href="{{ asset('styles4.css') }}">
</head>
<body>
    <div class="container">
        @yield('content')
    </div>
</body>
</html>
