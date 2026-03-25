<?php
namespace App\libs;
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */
use GuzzleHttp\Client;
/**
 * Description of ApiCheckInfinity
 *
 * @author bjaimes
 */
class ApiCheckautoservicios {
   
    private $url;
    private $cedula;
            
    public function __construct($url, $cedula) {
        
        $this->cedula = $cedula;
        $this->baseUrl = env($url).$cedula;
        $this->cliente = new Client();     
    }
    
    public function CheckInfinityPersonal(){  
        //dd($this->request);
        $response = $this->cliente->request('POST', $this->baseUrl,[ 
        'headers' => [
                'Origin' => 'http://'.env('IPSERVER').'/infinity/',
                'Access-Control-Allow-Origin'=>'http://'.env('IPSERVER').'/infinity/',
                'Accept' => 'application/json; charset=utf-8',            
            ]
        ]);
        return json_decode($response->getBody());          
    }
    
    public function CheckValidateUserAutoservicio(){  
        //dd($this->request);
        $response = $this->cliente->request('POST', $this->baseUrl,[ 
        'headers' => [
                'Origin' => 'http://'.env('IPSERVER').'/infinity/',
                'Access-Control-Allow-Origin'=>'http://'.env('IPSERVER').'/infinity/',
                'Accept' => 'application/json; charset=utf-8',            
            ]
        ]);
        return json_decode($response->getBody());          
    }
}
