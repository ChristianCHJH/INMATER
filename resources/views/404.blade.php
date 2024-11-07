@extends('layouts.notfound')

@section('title', 'Error 404 - Acceso no autorizado')

@section('content')
    <div class="container">
        <img src="{{ public_asset('images/logo_fondo.png') }}" alt="Logo Inmater" class="logo">
        <!-- Error Page -->
        <div class="error">
            <div class="container-floud">
                <div class="col-xs-12 text-center">
                    <div class="container-error-404">
                        <div class="clip"><div class="shadow"><span class="digit thirdDigit"></span></div></div>
                        <div class="clip"><div class="shadow"><span class="digit secondDigit"></span></div></div>
                        <div class="clip"><div class="shadow"><span class="digit firstDigit"></span></div></div>
                        <div class="msg">Upss<span class="triangle"></span></div>
                    </div> 
                    <h2 class="h1">Lo siento, la pagina que buscas no existe</h2>
                   <h3 class="h1"><a href="/index.php">Haz click aquí para volver</a></h3>
                </div>
            </div>
        </div>
        <!-- Error Page --> 
    </div>
@endsection
