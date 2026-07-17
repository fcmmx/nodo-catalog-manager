<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Envía automáticamente las publicaciones de redes sociales programadas
// cuya fecha ya llegó. Requiere que el cron del servidor ejecute
// `php artisan schedule:run` cada minuto (ver INSTALL-HOSTINGER.md).
Schedule::command('social:publish-due')->everyMinute()->withoutOverlapping();
