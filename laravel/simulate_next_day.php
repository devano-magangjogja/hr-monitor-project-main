<?php

/**
 * Script Simulasi Pergantian Hari Terpadu
 * 
 * Penggunaan dari terminal:
 *   php simulate_next_day.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\Illuminate\Support\Facades\Artisan::call('day:simulate');
echo \Illuminate\Support\Facades\Artisan::output();
