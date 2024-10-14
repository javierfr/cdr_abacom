<?php

namespace Config;


/**
 * --------------------------------------------------------------------------
 * Cross-Origin Resource Sharing (CORS) Configuration
 * --------------------------------------------------------------------------
 *
 * Here you may configure your settings for cross-origin resource sharing
 * or "CORS". This determines what cross-origin operations may execute
 * in web browsers. You are free to adjust these settings as needed.
 *
 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
 */
class Cors extends \Fluent\Cors\Config\Cors
{
    /**
     * --------------------------------------------------------------------------
     * Allowed HTTP headers
     * --------------------------------------------------------------------------
     *
     * Indicates which HTTP headers are allowed.
     *
     * @var array
     */
    public $default = [
        'allowedHeaders' => ['*'],
        'allowedMethods' => ['*'],
        'allowedOrigins' => ['*'],
        'allowedOriginsPatterns' => [],
        'exposedHeaders' => [],
        'maxAge' => 0,
        'supportsCredentials' => false,
    ];

    public $allowedHeaders = ['*'];
    public $allowedMethods = ['*'];
    public $allowedOrigins = ['*'];
    public $allowedOriginsPatterns = [];
    public $exposedHeaders = [];
    public $maxAge = 0;
    public $supportsCredentials = false;
}
