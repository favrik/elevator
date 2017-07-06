<html>
    <head>
        <title>@yield('title')</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="{{ env('APP_URL') }}:6001/socket.io/socket.io.js"></script>
        <link href="{{ mix('/css/app.css') }}" rel="stylesheet" />
    </head>
    <body>
        <h1>@yield('heading')</h1>
        <div class="container">
            @yield('content')
        </div>
        <script src="{{ mix('/js/app.js') }}"></script>
    </body>
</html>
