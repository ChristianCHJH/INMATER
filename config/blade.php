<?php

use Jenssegers\Blade\Blade;

// Ruta a tus vistas Blade
$views = __DIR__ . '/../resources/views';

// Ruta a la carpeta de caché (fuera de resources)
$cache = __DIR__ . '/../storage/cache/views';

$blade = new Blade($views, $cache);