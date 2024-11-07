<?php

require 'vendor/autoload.php';

use Jenssegers\Blade\Blade;

$commands = [
    'view:clear' => 'clearCache',
    'make:controller' => 'makeController',
];

if ($argc < 2) {
    echo "Please provide a command.\n";
    exit(1);
}

$command = $argv[1];
$options = array_slice($argv, 2);

if (!array_key_exists($command, $commands)) {
    echo "Unknown command: $command\n";
    exit(1);
}

call_user_func($commands[$command], $options);

function clearCache()
{
    $cachePath = __DIR__ . '/resources/cache/views';
    $files = glob($cachePath . '/*'); 

    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }

    echo "View cache cleared.\n";
}

function makeController($options)
{
    if (empty($options)) {
        echo "Please provide a name for the controller.\n";
        exit(1);
    }

    $name = $options[0];
    $createMVC = in_array('-mvc', $options);

    createController($name);
    createModel($name);

    if ($createMVC) {
        createViews($name);
    }

    echo "$name Controller, Model and Views have been created.\n";
}

function createController($name)
{
    $controllerTemplate = "<?php

namespace App\Http\Controllers;

use Jenssegers\Blade\Blade;

class {$name}Controller extends Controller
{
    protected \$blade;

    public function __construct(Blade \$blade)
    {
        \$this->blade = \$blade;
    }

    public function index()
    {
        echo \$this->blade->make('$name.index')->render();
    }

    public function create()
    {
        echo \$this->blade->make('$name.create')->render();
    }

    public function update()
    {
        echo \$this->blade->make('$name.update')->render();
    }

    public function search()
    {
        echo \$this->blade->make('$name.search')->render();
    }
}";

    $controllerPath = __DIR__ . "/app/Http/Controllers/{$name}Controller.php";
    file_put_contents($controllerPath, $controllerTemplate);
}

function createModel($name)
{
    $modelTemplate = "<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class $name extends Model
{
    // Add your model properties and methods here
}";

    $modelPath = __DIR__ . "/app/Models/{$name}.php";
    file_put_contents($modelPath, $modelTemplate);
}

function createViews($name)
{
    $views = ['index', 'create', 'update', 'search'];
    $viewPath = __DIR__ . "/resources/views/$name";

    if (!is_dir($viewPath)) {
        mkdir($viewPath, 0755, true);
    }

    foreach ($views as $view) {
        $viewTemplate = "<!-- Add your $view view content here -->";
        file_put_contents("$viewPath/$view.blade.php", $viewTemplate);
    }
}
