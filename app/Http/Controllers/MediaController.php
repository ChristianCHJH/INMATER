<?php

namespace App\Http\Controllers;

use App\Models\Media\Media;
use App\Http\Requests\MediaRequest;
use App\Helpers\ResponseHelper;

class MediaController extends Controller
{
    public function index()
    {
        // Lógica para listar los medios de comunicación si es necesario
    }

    public function store(MediaRequest $request)
    {
        // Lógica para almacenar los medios de comunicación
    }

    public function list($elegido)
    {
         
        //print_r($elegido);
        $mediaOptions = '';

        if (in_array($elegido, ['P', 'D', 'EXT', 'EMP'])) {
            $query = Media::where('estado', 1);

            if ($elegido == 'P' || $elegido == 'D') {
                $mediaOptions = $query->get()->map(function ($media) {
                    return [
                        'id' => $media->id,
                        'name' => $media->nombre,
                        'disabled' => $media->grupo != '1'
                    ];
                });
            } elseif ($elegido == 'EXT') {
                $mediaOptions = $query->get()->map(function ($media) {
                    return [
                        'id' => $media->id,
                        'name' => $media->nombre,
                        'disabled' => $media->grupo != '3'
                    ];
                });
            } elseif ($elegido == 'EMP') {
                $mediaOptions = $query->get()->map(function ($media) {
                    return [
                        'id' => $media->id,
                        'name' => $media->nombre,
                        'disabled' => $media->grupo != '2'
                    ];
                });
            }
        } else {
            $mediaOptions = [['id' => '', 'name' => 'Seleccionar*', 'disabled' => false]];
        }

        return ResponseHelper::json($mediaOptions);
    }
}
