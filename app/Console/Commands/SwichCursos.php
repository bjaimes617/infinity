<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\cursos\CursosModel;
use Carbon\Carbon;

class SwichCursos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'infinity:cursosactive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activa y desactiva los cursos disponibles';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        
        ///// cerrar los cursos automaticos
        $today = Carbon::now()->subDays(1)->format('Y-m-d');        
      
        ///apertura automatica de cursos
        $todays = Carbon::now()->format('Y-m-d');
        
        CursosModel::WhereDate('inicio', '=', $todays)->where('active', 2)
        ->update(['active' => 1]);         
        $this->line('[==========ACTIVACION de  CURSOS PROCESADA===========]');
                
        CursosModel::WhereDate('finaliza', '<', $todays)->where('active', 1)
        ->update(['active' => 0, 'automaticClose' => 1]);    
        $this->line('[==========CIERRE de  CURSOS VIEJOS PROCESADA===========]');
    }
}