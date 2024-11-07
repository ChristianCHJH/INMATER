@extends('layouts.app')

@section('title', 'Editar Usuario')

@section('content')
    <div class="max-w-2xl mx-auto mt-10 px-4 py-6 bg-white shadow-md sm:rounded-lg">
        <h1 class="text-2xl font-bold mb-6">Editar Usuario</h1>
        
        <form action="{{ route('users.update', ['id' => $user->id]) }}" method="POST" id="editUserForm">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label for="username" class="block text-sm font-medium text-gray-700">Username:</label>
                <input type="text" name="userx" id="username" value="{{ $user->userx }}" class="w-full shadow-inner bg-gray-100 rounded-lg placeholder-black text-2xl p-2 block mt-1 border">
                <div class="feedback text-red-500 text-sm mt-1"></div>
            </div>
            
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Nombre:</label>
                <input type="text" name="nom" id="name" value="{{ $user->nom }}" class="w-full shadow-inner bg-gray-100 rounded-lg placeholder-black text-2xl p-2 block mt-1 border">
                <div class="feedback text-red-500 text-sm mt-1"></div>
            </div>
            
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email:</label>
                <input type="email" name="mail" id="email" value="{{ $user->mail }}" class="w-full shadow-inner bg-gray-100 rounded-lg placeholder-black text-2xl p-2 block mt-1 border">
                <div class="feedback text-red-500 text-sm mt-1"></div>
            </div>
            
            <div class="mb-4 relative">
                <label for="password" class="block text-sm font-medium text-gray-700">Contraseña:</label>
                <div class="flex">
                    <input type="password" name="pass" id="password" value="" class="w-full shadow-inner bg-gray-100 rounded-lg placeholder-black text-2xl p-2 block mt-1 border">
                    <button type="button" class="ml-2 px-3 py-2 text-gray-500" onclick="togglePasswordVisibility()">
                        <i id="password-toggle" class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="feedback text-red-500 text-sm mt-1"></div>
            </div>
            
            <div class="mb-4">
                <label for="role" class="block text-sm font-medium text-gray-700">Rol:</label>
                <select name="role" id="role" class="w-full shadow-inner bg-gray-100 rounded-lg placeholder-black text-2xl p-2 block mt-1 border">
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" {{ $user->role == $role->id ? 'selected' : '' }}>{{ $role->nombre }}</option>
                    @endforeach
                </select>
                <div class="feedback text-red-500 text-sm mt-1"></div>
            </div>
            
            <div class="mb-4">
                <label for="cmp" class="block text-sm font-medium text-gray-700">CMP:</label>
                <input type="number" name="cmp" id="cmp" value="{{ $user->cmp }}" class="w-full shadow-inner bg-gray-100 rounded-lg placeholder-black text-2xl p-2 block mt-1 border">
                <div class="feedback text-red-500 text-sm mt-1"></div>
            </div>
        
            <div class="mb-4 flex items-center">
                <span class="block text-sm font-medium text-gray-700 mr-4">Estado:</span>
                <label for="estado-off" class="flex items-center cursor-pointer">
                    <input type="radio" id="estado-off" name="estado" value="0" {{ $user->estado == 0 ? 'checked' : '' }} class="form-radio h-5 w-5 text-indigo-600">
                    <span class="ml-2 text-sm text-gray-700">Inactivo</span>
                </label>
                <label for="estado-on" class="flex items-center cursor-pointer ml-4">
                    <input type="radio" id="estado-on" name="estado" value="1" {{ $user->estado == 1 ? 'checked' : '' }} class="form-radio h-5 w-5 text-indigo-600">
                    <span class="ml-2 text-sm text-gray-700">Activo</span>
                </label>
                <span class="text-red-500" id="estadoError"></span>
            </div>
        
            <div class="flex items-center justify-end">
                <button type="submit" id="submitForm" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Guardar
                </button>
            </div>
        </form>
    </div>
     
    <script src="https://cdn.jsdelivr.net/npm/just-validate@3.3.3/dist/js/just-validate.production.min.js"></script>
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

        document.getElementById('submitForm').addEventListener('click', function(event) {
            event.preventDefault(); // Evitar el envío tradicional del formulario

            var form = document.getElementById('editUserForm');
            var formData = new FormData(form);
            
            fetch(form.action, {
                method: 'POST',
                body: formData
            })
            .then(response => { 
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data); // Para verificar la respuesta del servidor en la consola 
                if (data.success) {
                    mostrarToast('success', 'Usuario actualizado'); 
                    // form.reset(); 
                    // window.location.href = '/users';
                } else {
                    showErrors(form, data.html);
                    mostrarToast('error', data.message);  
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarToast('error', error.message);  
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
@endsection
