<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Tasks — CBUAE Compliance Maintenance
|--------------------------------------------------------------------------
*/

// Purge expired tokens every hour (keep db clean)
Schedule::command('tokens:purge-expired')->hourly();

// Archive audit logs older than 90 days to cold storage (keep 7 years total)
Schedule::command('audit:archive')->dailyAt('02:00');
