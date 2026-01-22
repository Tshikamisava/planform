<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule DCR due date reminders - run daily at 8 AM
Schedule::command('dcr:send-due-date-reminders')
    ->dailyAt('08:00')
    ->timezone('Africa/Johannesburg');

// Schedule DCR escalation checks - run every 6 hours
Schedule::command('dcr:check-escalations')
    ->everySixHours()
    ->timezone('Africa/Johannesburg');
