<?php
if (! function_exists('asset')) {
    /**
     * Generate an asset path for the application.
     *
     * @param  string  $path
     * @param  bool|null  $secure
     * @return string
     */
    function asset($path, $secure = null)
    {
        // Ajusta esta variable según tu estructura de activos
        $basePath = '/public/assets'; // Por ejemplo, si tus activos están en la carpeta "public"

        return $basePath . '/' . ltrim($path, '/');
    } 
}

if (! function_exists('public_asset')) {
    /**
     * Generate an public path for the application.
     *
     * @param  string  $path
     * @param  bool|null  $secure
     * @return string
     */
    function public_asset($path, $secure = null)
    {
        // Ajusta esta variable según tu estructura de activos
        $basePath = '/public'; // Por ejemplo, si tus activos están en la carpeta "public"

        return $basePath . '/' . ltrim($path, '/');
    } 
}

if (! function_exists('route')) {
    /**
     * Generate an asset path for the application.
     *
     * @param  string  $path
     * @param  bool|null  $secure
     * @return string
     */
    if (! function_exists('route')) {
        function route($name, $parameters = [])
        {
            global $router;
    
            $url = $router->getNamedRoute($name);
    
            if ($url === null) {
                throw new Exception("Route {$name} not defined.");
            }
    
            if (!is_array($parameters)) {
                throw new InvalidArgumentException("Parameters should be an array. Given: " . gettype($parameters));
            }
    
            foreach ($parameters as $key => $value) {
                $url = str_replace('{' . $key . '}', $value, $url);
            }
    
            return $url;
        }
    }
    

    if (!function_exists('paginate')) {
        /**
         * Paginate an array of items.
         *
         * @param  array  $items
         * @param  int  $perPage
         * @param  int  $currentPage
         * @param  string  $path
         * @return array
         */
        function paginate($items, $perPage = 15, $currentPage = 1, $path = '/')
        {
            $total = count($items);
            $start = ($currentPage - 1) * $perPage;
            $paginatedItems = array_slice($items, $start, $perPage);
    
            return [
                'data' => $paginatedItems,
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $currentPage,
                'last_page' => ceil($total / $perPage),
                'path' => $path
            ];
        }
    }
    
    if (!function_exists('pagination_links')) {
        /**
         * Generate pagination links.
         *
         * @param  array  $pagination
         * @return string
         */
        function pagination_links($pagination, $framework = 'bootstrap')
        {
            $links = '';
            $path = $pagination['path'];
            $currentPage = $pagination['current_page'];
            $lastPage = $pagination['last_page'];

            if ($lastPage > 1) {
                if ($framework === 'bootstrap') {
                    $links .= '<ul class="pagination justify-content-center">';
                } else if ($framework === 'tailwind') {
                    $links .= '<div class="flex justify-center"><ul class="inline-flex items-center">';
                }

                // Botón "Atrás"
                if ($currentPage > 1) {
                    $prevPage = $currentPage - 1;
                    if ($framework === 'bootstrap') {
                        $links .= "<li class='page-item'><a class='page-link' href='{$path}?page={$prevPage}'>Atrás</a></li>";
                    } else if ($framework === 'tailwind') {
                        $links .= "<li><a class='px-3 py-1 rounded-l-md border border-gray-300 bg-white text-gray-500 hover:bg-gray-100 hover:text-gray-700' href='{$path}?page={$prevPage}'>Atrás</a></li>";
                    }
                } else {
                    if ($framework === 'bootstrap') {
                        $links .= "<li class='page-item disabled'><span class='page-link'>Atrás</span></li>";
                    } else if ($framework === 'tailwind') {
                        $links .= "<li><span class='px-3 py-1 rounded-l-md border border-gray-300 bg-white text-gray-500 cursor-not-allowed'>Atrás</span></li>";
                    }
                }

                // Generar los números de página con puntos suspensivos
                $range = 2; // Número de páginas a mostrar antes y después de la actual
                $initial = max(1, $currentPage - $range);
                $final = min($lastPage, $currentPage + $range);

                if ($initial > 1) {
                    if ($framework === 'bootstrap') {
                        $links .= "<li class='page-item'><a class='page-link' href='{$path}?page=1'>1</a></li>";
                        if ($initial > 2) {
                            $links .= "<li class='page-item disabled'><span class='page-link'>...</span></li>";
                        }
                    } else if ($framework === 'tailwind') {
                        $links .= "<li><a class='px-3 py-1 border border-gray-300 bg-white text-gray-500 hover:bg-gray-100 hover:text-gray-700' href='{$path}?page=1'>1</a></li>";
                        if ($initial > 2) {
                            $links .= "<li><span class='px-3 py-1 border border-gray-300 bg-white text-gray-500'>...</span></li>";
                        }
                    }
                }

                for ($i = $initial; $i <= $final; $i++) {
                    $active = $i == $currentPage ? 'active' : '';
                    if ($framework === 'bootstrap') {
                        $links .= "<li class='page-item {$active}'><a class='page-link' href='{$path}?page={$i}'>{$i}</a></li>";
                    } else if ($framework === 'tailwind') {
                        $activeClass = $i == $currentPage ? 'bg-blue-500 text-white' : 'bg-white text-gray-500 hover:bg-gray-100 hover:text-gray-700';
                        $links .= "<li><a class='px-3 py-1 border border-gray-300 {$activeClass}' href='{$path}?page={$i}'>{$i}</a></li>";
                    }
                }

                if ($final < $lastPage) {
                    if ($final < $lastPage - 1) {
                        if ($framework === 'bootstrap') {
                            $links .= "<li class='page-item disabled'><span class='page-link'>...</span></li>";
                        } else if ($framework === 'tailwind') {
                            $links .= "<li><span class='px-3 py-1 border border-gray-300 bg-white text-gray-500'>...</span></li>";
                        }
                    }
                    if ($framework === 'bootstrap') {
                        $links .= "<li class='page-item'><a class='page-link' href='{$path}?page={$lastPage}'>{$lastPage}</a></li>";
                    } else if ($framework === 'tailwind') {
                        $links .= "<li><a class='px-3 py-1 border border-gray-300 bg-white text-gray-500 hover:bg-gray-100 hover:text-gray-700' href='{$path}?page={$lastPage}'>{$lastPage}</a></li>";
                    }
                }

                // Botón "Siguiente"
                if ($currentPage < $lastPage) {
                    $nextPage = $currentPage + 1;
                    if ($framework === 'bootstrap') {
                        $links .= "<li class='page-item'><a class='page-link' href='{$path}?page={$nextPage}'>Siguiente</a></li>";
                    } else if ($framework === 'tailwind') {
                        $links .= "<li><a class='px-3 py-1 rounded-r-md border border-gray-300 bg-white text-gray-500 hover:bg-gray-100 hover:text-gray-700' href='{$path}?page={$nextPage}'>Siguiente</a></li>";
                    }
                } else {
                    if ($framework === 'bootstrap') {
                        $links .= "<li class='page-item disabled'><span class='page-link'>Siguiente</span></li>";
                    } else if ($framework === 'tailwind') {
                        $links .= "<li><span class='px-3 py-1 rounded-r-md border border-gray-300 bg-white text-gray-500 cursor-not-allowed'>Siguiente</span></li>";
                    }
                }

                if ($framework === 'bootstrap') {
                    $links .= '</ul>';
                } else if ($framework === 'tailwind') {
                    $links .= '</ul></div>';
                }
            }

            return $links;
        }
    }    
}