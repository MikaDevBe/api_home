<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'name' => 'Mikaprod API',
        'environment' => app()->environment(),
        'php_version' => PHP_VERSION,
        'lumen_version' => app()->version(),
        'timestamp' => now()->toISOString(),
    ]);
});
