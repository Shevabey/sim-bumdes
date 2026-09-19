<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('iuran:generate-bulanan')->monthlyOn(1, '00:05');
Schedule::command('referral:expire-check')->hourly();
Schedule::command('referral:verify-check')->daily();
