<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
        'https://test4.stagingwebsites.info/user/quickbook_post_data',  //statging
        'http://localhost:8888/user/quickbook_post_data',   // local
    ];
}
