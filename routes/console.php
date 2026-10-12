<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Cierra cada madrugada lo vencido hace más de 7 días que nadie resolvió.
// Se fija la hora de Bolivia: Laravel corre en UTC y "a diario" sería las 20:00.
Schedule::command('app:cerrar-salidas-pendientes')
    ->dailyAt('03:00')
    ->timezone('America/La_Paz')
    ->withoutOverlapping();
