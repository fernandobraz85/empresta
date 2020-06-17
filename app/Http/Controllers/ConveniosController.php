<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ConveniosController extends Controller
{
    protected $convenios;

    public function __construct()
    {
        $this->convenios = File::get(database_path().'/json/convenios.json');
    }

    public function getConvenios(){

        try {
            $convenios = json_decode(trim($this->convenios));

            return $convenios;
        }
        catch (Exception $ex) { // Anything that went wrong
            abort(500, 'ERROR');
        }

    }
}
