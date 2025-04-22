<?php

function errorAction (int $errorCode, string $default): string
{
    return match (headers('Content-Type')) {
        'application/json' => 'json' . ucfirst(config("app.error_page_{$errorCode}", $default)),
        'application/xml', 'text/xml' => 'xml' . ucfirst(config("app.error_page_{$errorCode}", $default)),
        default => 'web' . ucfirst(config("app.{$errorCode}", $default)),
    };
}