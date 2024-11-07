<?php

namespace App\Middleware;

use App\Helpers\CsrfHelper;

class CsrfMiddleware
{
    public function handle($request, $next)
    {
        if ($request->getMethod() === 'POST') {
            $token = $request->input('_token');

            if (!CsrfHelper::validateToken($token)) {
                die('CSRF token validation failed.');
            }
        }

        return $next($request);
    }
}
