<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Send abandoned cart recovery emails once a day at 10:00
Schedule::command('cart:send-recovery-emails')->dailyAt('10:00');
