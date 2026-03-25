<?php

namespace app\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CloseAllFileSessions extends Command
{
    protected $signature = 'sessions:close-all-file';
    protected $description = 'Elimina todos los archivos de sesión (cierra todas las sesiones activas)';

    public function handle()
    {
        $sessionPath = storage_path('framework/sessions');
        $files = File::files($sessionPath);
        $count = 0;

        foreach ($files as $file) {
            File::delete($file);
            $count++;
        }
        \Auth::logout();
        \session()->invalidate();
        $this->info("Se han eliminado {$count} archivos de sesión.");
        return 0;
    }
}