<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// BR-07 / FR-10: Setiap tanggal 1 pukul 00:05, tandai tagihan lewat jatuh tempo menjadi OVERDUE
Schedule::command('tagihan:update-overdue')->monthlyOn(1, '00:05');
