<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\TestCase;
use App\libs\ApiCheckautoservicios;
use GuzzleHttp\Client;

class ApiAutoservicioTest extends TestCase
{
    /**
     * A basic feature test example.
     * ./vendor/bin/phpunit --verbose tests/Feature/ApiAutoservicioTest.php
     * @return void
     */
    
    public function test_Validar_Conexion() {
       
        $cedula = 197546170;        
        ///se usa la cedula administrativa con el caso 2 y la ip 10.15 para pruebas en produccion
        switch (2){
            case 1;
        $bashurl = 'http://172.16.21.246/autoservicio/api/disabled/generals/'.$cedula;
        $ipserver = "localhost";
            break;
            case 2:
         $bashurl = 'http://192.168.10.15:8084/api/disabled/generals/'.$cedula;
        $ipserver = "192.168.10.17";
                break;
        }        
        
        $cliente = new Client();       
    
        $response = $cliente->request('POST',$bashurl,[ 
        'headers' => [
                'Origin' => 'http://'.$ipserver.'/infinity/',
                'Access-Control-Allow-Origin'=>'http://'.$ipserver.'/infinity/',
                'Accept' => 'application/json; charset=utf-8',            
            ]
        ]);          
        $r = \GuzzleHttp\json_decode($response->getStatusCode());        
        $this->assertNotNull($r);
    }
}
