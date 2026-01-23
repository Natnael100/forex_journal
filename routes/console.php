<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule subscription renewals
use Illuminate\Support\Facades\Schedule;

Schedule::command('subscriptions:process-renewals')->daily();

