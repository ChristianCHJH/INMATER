<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <!-- Enlace al archivo CSS de TailwindCSS -->
    <link href="https://cdn.tailwindcss.com" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="
https://cdn.jsdelivr.net/npm/sweetalert2@11.12.2/dist/sweetalert2.min.css
" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/just-validate@3.3.3/dist/js/just-validate.production.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal"> 
 
    <header class="bg-gray-900 p-6">
        <nav class="container mx-auto">
            <ul class="flex items-center justify-between">
                <li><a href="{{ route('home') }}" class="text-white hover:text-gray-300">Inicio</a></li> 
            </ul>
        </nav>
    </header>
 
    <main class="container mx-auto mt-6">
        @yield('content')
    </main>
 
    <footer class="bg-gray-900 text-white text-center p-4 mt-6">
        <p>&copy; {{ date('Y') }} Inmater. Todos los derechos reservados.</p>
    </footer>
 
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function togglePasswordVisibility() {
            var passwordInput = document.getElementById('password');
            var passwordToggle = document.getElementById('password-toggle');

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                passwordToggle.classList.remove('fa-eye');
                passwordToggle.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = "password";
                passwordToggle.classList.remove('fa-eye-slash');
                passwordToggle.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
