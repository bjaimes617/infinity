<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Apis\CondominioModel;
use App\Exports\CondominioExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Notifications\EmailCondomios;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class SendEmailsCondominios extends Command
{
       
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'infinity:sendmailscondominios';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envio de correo adjunto con los visitantes registrados por vigilancia';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        ///array con todas las sedes correspondientes
        $this->condominio   = ["1"=>"Autana"];       
        ///ubicamos el correo del cliente a quien se le enviara el email egun el condominio anterior
        $this->emails       = ["Autana"=>"willpalacios@gmail.com","Plaza"=>"rodriguezbj@outlook.com"];
        /// ubicamos el nombre del responsable en recibir el correo.
        $this->clientes     = ["Autana"=>"Willmer Palacios","Plaza"=>"Braian Jaimes"];
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $fecha = date('Y-m-d', strtotime(now()."-1 days"));         
        
        foreach($this->condominio as $condominio => $value){   
            
             $this->line($value ." ". $this->clientes[$value]." ".$condominio);
            
            $sql = CondominioModel::whereBetween('created_at', array("$fecha 00:00:01", "$fecha 23:59:59"))
                ->where('dir_condominio.sede',$condominio)
                ->orderBy('dir_condominio.created_at', 'asc');
            $data = $sql->get();    
            
            $namefile = 'Reporte De Condominio '.$value.' '. $fecha .'.xls';
            $namecliente = $this->clientes[$value];
            
            $attach = ["namefile"=>$namefile, "namecliente"=>$namecliente,"fecha"=>$fecha];
            Excel::store(new CondominioExport($data,$fecha), 'Reporte De Condominio '.$value.' '. $fecha .'.xls', 'condominios');  
            
            Notification::route('mail',$this->emails[$value])->notify(new EmailCondomios($attach));
            
            Storage::disk('condominios')->delete($namefile);           
        }
    }
}
