<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define los comandos Artisan.
     */
    protected function commands()
    {
        // Aquí puedes registrar tus comandos personalizados
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    /**
     * Define el programa de tareas a ser ejecutado.
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('offers:remove-expired')->daily();
    }
}
