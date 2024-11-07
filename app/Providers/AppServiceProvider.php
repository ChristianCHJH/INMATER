<?php
use Illuminate\Support\Facades\Blade;
use App\Helpers\CsrfHelper;

class AppServiceProvider{
    public function boot()
    {
        Blade::directive('csrf', function () {
            $token = CsrfHelper::generateToken();
            return "<input type='hidden' name='_token' value='$token'>";
        });
    } 
} 