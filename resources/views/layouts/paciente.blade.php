<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Inmater Clínica de Fertilidad | Nuevo Datos Generales de Paciente')</title>
    <link rel="icon" href="{{ public_asset('_images/favicon.png') }}" type="image/x-icon"> 
    <link rel="stylesheet" href="{{ public_asset('css/tema_inmater.min.css') }}">
    <link rel="stylesheet" href="{{ public_asset('css/jquery.mobile.icons.min.css') }}">
    <link rel="stylesheet" href="{{ public_asset('css/jquery.mobile.structure-1.4.5.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ public_asset('css/global.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="{{ public_asset('js/utils_fx.js') }}"></script>
</head>
<body class=""> 
    @yield('content') 
    <footer class="bg-gray-900 text-white text-center p-4 mt-6">
        <p>&copy; {{ date('Y') }} Inmater. Todos los derechos reservados.</p>
    </footer>
    <script src="https://cdn.tailwindcss.com"></script>
</body>
</html>
