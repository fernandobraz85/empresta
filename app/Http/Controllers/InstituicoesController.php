<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class InstituicoesController extends Controller
{
    protected $instituicoes;
    
    public function __construct()
    {
        $this->instituicoes = File::get(database_path().'/json/instituicoes.json');
    }
    
    public function getInstituicoes(){

        try {
            $instituicoes = json_decode(trim($this->instituicoes));

            return $instituicoes;
        }
        catch (Exception $ex) { // Anything that went wrong
            abort(500, 'ERROR');
        }

    }
}
