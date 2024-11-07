<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generar Token JWT</title>
    <!-- Tailwind CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Alpine.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js" defer></script>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.2/dist/sweetalert2.min.css" rel="stylesheet"> 
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Generar Token JWT</h1>
        <form id="tokenForm" action="{{ route('generate-token') }}" method="POST" class="bg-white p-6 rounded shadow-md">
            <div class="mb-4">
                <label for="payload" class="block text-gray-700">Datos del Payload:</label>
                <textarea id="payload" name="payload" rows="4" class="form-control w-full border border-gray-300 p-2 rounded" required></textarea>
            </div>
            <div class="mb-4">
                <label for="algorithm" class="block text-gray-700">Algoritmo de Firma:</label>
                <select id="algorithm" name="algorithm" class="form-control w-full border border-gray-300 p-2 rounded">
                    <option value="HS256" selected>HS256</option>
                    <option value="HS384">HS384</option>
                    <option value="HS512">HS512</option>
                </select>
                <div class="feedback text-red-500 text-sm mt-1"></div>
            </div>
            <div class="mb-4">
                <label for="expires_in" class="block text-gray-700">Tiempo de Expiración (en segundos, dejar en blanco para sin expiración):</label>
                <input type="number" id="expires_in" name="expires_in" class="form-control w-full border border-gray-300 p-2 rounded">
            </div>
            <button type="submit" class="btn btn-primary bg-blue-500 text-white p-2 rounded">Generar Token</button>
        </form>

        <!-- Modal -->
        <div x-data="{ showModal: false }" x-show="showModal" x-transition class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center">
            <div class="bg-white p-6 rounded shadow-md w-1/2">
                <h2 class="text-xl font-bold mb-4">Token Generado</h2>
                <textarea id="generatedToken" rows="6" class="w-full border border-gray-300 p-2 rounded" readonly></textarea>
                <button @click="showModal = false" class="mt-4 bg-blue-500 text-white p-2 rounded">Cerrar</button>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('tokenForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevenir el envío tradicional del formulario

            var form = document.getElementById('tokenForm');
            var formData = new FormData(form);

            // Obtener el token CSRF de manera segura
            var csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
            var csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '';

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest' 
                }
            })
            .then(response => { 
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data); // Para verificar la respuesta del servidor en la consola 
                if (data.status) {
                    mostrarToast('success', data.message);
                    form.reset();
                    // Mostrar modal con el token
                    var generatedTokenTextarea = document.getElementById('generatedToken');
                    if (generatedTokenTextarea) {
                        generatedTokenTextarea.value = data.token; // Actualizar el textarea con el token
                    }
                    document.querySelector('[x-data]').__x.$data.showModal = true; // Mostrar modal
                } else {
                    showErrors(form, data.html);
                    mostrarToast('error', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarToast('error', 'Ha ocurrido un error');
            });

            return false; // Asegura que el formulario no se envíe de manera tradicional   
        });


        function showErrors(form, html) {
            // Clear previous error messages and classes
            form.querySelectorAll('.border-red-500, .border-green-500').forEach(element => {
                element.classList.remove('border-red-500', 'border-green-500');
            });
            form.querySelectorAll('.text-red-500, .text-green-500').forEach(element => {
                element.classList.remove('text-red-500', 'text-green-500');
            });
            form.querySelectorAll('.feedback').forEach(element => {
                element.textContent = '';
            });

            for (const [key, error] of Object.entries(html)) {
                const input = form.querySelector(`[name="${key}"]`);
                if (input) {
                    input.classList.add(error.class);
                    const feedbackElement = input.nextElementSibling;
                    if (feedbackElement && feedbackElement.classList.contains('feedback')) {
                        feedbackElement.classList.add(error.classmsj);
                        feedbackElement.textContent = error.msj;
                    }
                }
            }
        }

        function mostrarToast(icon, title) {
            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });

            Toast.fire({
                icon: icon,
                title: title
            });
        }
    </script>
</body>
</html>
