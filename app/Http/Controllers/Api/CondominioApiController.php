<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Apis\CondominioModel;
use App\Models\personal\SedeModel;

class CondominioApiController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        try {
            $arrayDate = explode("-", $request->fecha);
            $arrayBegin = explode("/", $arrayDate[0]);
            $begin = trim($arrayBegin[2]) . "-" . trim($arrayBegin[0]) . "-" . trim($arrayBegin[1]);
            $arrayEnd = explode("/", $arrayDate[1]);
            $end = trim($arrayEnd[2]) . "-" . trim($arrayEnd[0]) . "-" . trim($arrayEnd[1]);
            $data = array();

            $sedes = SedeModel::pluck('nombre_sede','id')->toArray();
            
            $sql = CondominioModel::whereBetween('created_at', array("$begin 00:00:01", "$end 23:59:59"))
                    ->where('dir_condominio.sede', $request->sede)
                    ->orderBy('dir_condominio.created_at', 'asc');
            $data = $sql->get();

            return response()->json([$data, $begin, $end,$sedes], 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
}
