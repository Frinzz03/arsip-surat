<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Otomatis hapus file PDF yang lebih dari 9 bulan setiap hari jam 00:00
Schedule::command('storage:cleanup-old-pdf')->daily();
