<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Détection automatique des impayés le 11 de chaque mois à 8h
Schedule::command('school:detect-unpaid --notify')
    ->monthlyOn(11, '08:00')
    ->appendOutputTo(storage_path('logs/unpaid-detection.log'));
