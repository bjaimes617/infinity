<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Indicates whether the XSRF-TOKEN cookie should be set on the response.
     *
     * @var bool
     */
    protected $addHttpCookie = false;

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [        
        '/api/arc/generate/data',
        '/api/Personal/active/DG/*',
        '/api/Personal/review/lp/external',
        '/api/Personal/validate/ficha/personal',
        '/api/Personal/constancias/generate',
        '/api/Personal/validate/constancias/show',
        '/api/Personal/generate/resource/payments/',
        '/api/Personal/constancias/generate',
        '/api/Personal/validate/constancias/show',
        '/api/arc/generate/data',
        '/api/ari/search/tribute',
        '/api/ari/create/historial',
        '/api/ari/consult/historial',
        '/api/habilidades/search/assessment',
        '/api/habilidades/assessment/show',
        '/api/habilidades/assessment/store',
        '/api/reclutamientoSimple/preseleccion/storage',
        '/api/reclutamiento/validate/candidato',
        '/api/reclutamientoSimple/Storage',
        '/api/listado/personal/general/dg',       
    ];
}
