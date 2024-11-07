<?php
namespace App\Http\Controllers;
use Jenssegers\Blade\Blade;
class Controller {
    protected $blade;
    
    public function __construct(Blade $blade)
    {
        $this->blade = $blade; 
    }
    public function render($view, $data = []) {
        extract($data);
        require "resources/views/{$view}.php";
        exit;die;
    }
}