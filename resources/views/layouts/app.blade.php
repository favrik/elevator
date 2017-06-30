<html>
    <head>
        <title>@yield('title')</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <script src="//el.loc:6001/socket.io/socket.io.js"></script>

        <script src="css/app.css"></script>
    </head>
    <body>

        <div class="container">
            @yield('content')
        </div>

        <script src="js/app.js"></script>
    </body>
</html>
