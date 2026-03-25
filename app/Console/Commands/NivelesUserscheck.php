<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\PoliceEstructure;
use App\Models\User;

class NivelesUserscheck extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'infinity:checknivel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        while (true) {
            $usuario = trim($this->ask("Ingrese nombre de usuario: "));
            if (empty($usuario)) {
                $this->error("El nombre de usuario no puede estar vacío.");
                continue;
            }

            $iduser = User::where('username', $usuario)->first();
            if ($iduser) {
                $nivel = PoliceEstructure::VerifiedNivelPermissions($iduser);
                $this->info("Nivel de Privilegios " . $nivel);
            } else {
                $this->error("Usuario no Encontrado");
            }

            $respuesta = strtoupper($this->ask("Quiere Realizar una nueva Consulta? S/N:  "));
            if ($respuesta == "N") {
                break;
            }
        }
    }
}
