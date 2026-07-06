<?php

function loadConfig(): array
{
    static $config = null;
    if ($config !== null) {
        return $config;
    }

    $path = dirname(__DIR__) . '/config.php';
    if (!file_exists($path)) {
        $path = dirname(__DIR__) . '/config.example.php';
    }
    $config = require $path;
    return $config;
}
