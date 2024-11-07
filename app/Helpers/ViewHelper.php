<?php

if (! function_exists('csrf_field')) {
    function csrf_field() {
        return '<input type="hidden" name="_token" value="' . $_SESSION['_token'] . '">';
    }
}

if (! function_exists('method_field')) {
    function method_field($method) {
        return '<input type="hidden" name="_method" value="' . strtoupper($method) . '">';
    }
}
