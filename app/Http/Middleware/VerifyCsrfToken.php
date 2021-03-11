<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'admin/*',
        'double/*',
        'api/*',
        'bandit/*',
        'chat/*',
        'deposit_parse',
        'deposit_send',
        'shop_parse',
        'shop_send',
        'poker/*',
        'saveUrl',
        'getMyBalance',
        'promo/*',
        'support',
        'p4r4p3t',
        'test'
    ];
}
