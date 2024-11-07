<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title> 
    <link rel="stylesheet" href="{{ asset('css/app.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Anton|Passion+One|PT+Sans+Caption' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="{{ asset('css/404.css') }}">
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal notfound"> 
    <div data-role="page" class="ui-responsive-panel header">
        <div data-role="header" data-position="fixed">
            <h1>Especialistas en Medicina Reproductiva S.A.C.</h1>
        </div>
        <div class="ui-content" role="main">
            <div class="ui-grid-b">
                <div class="ui-block-a"></div>
                <div class="ui-block-b"></div>
                <div class="ui-block-c"></div>
            </div>
        </div>
    </div>
    <main class="container mx-auto mt-6">
        @yield('content')
    </main>
    <footer class="bg-gray-900 text-white text-center p-4 mt-6">
        <p>&copy; {{ date('Y') }} Inmater. Todos los derechos reservados.</p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/404.js') }}"></script> 
</body>
</html>