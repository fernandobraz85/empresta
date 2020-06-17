<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class CreditoController extends Controller
{
    protected $instituicoes;
    protected $convenios;
    protected $taxas;
    
    public function __construct()
    {
        $this->instituicoes = json_decode(File::get(database_path().'/json/instituicoes.json'));
        $this->convenios = json_decode(File::get(database_path().'/json/convenios.json'));
        $this->taxas = json_decode(File::get(database_path().'/json/taxas_instituicoes.json'));
    }

    public function simulacao(Request $request){
        
        $data = $request->all();
        $ret = [];

        try{
            //se nao houver parametro "instituicoes" pega todas do arquivo json
            if(!isset($data['instituicoes'])){
                foreach($this->instituicoes as $inst){            
                    $data['instituicoes'][] = $inst->chave;
                }
            }

            //se nao houver parametro "convenios" pega todas do arquivo json
            if(!isset($data['convenios'])){
                foreach($this->convenios as $convenio){            
                    $data['convenios'][] = $convenio->chave;
                }
            }
            
            foreach($data['instituicoes'] as $instituicao){
                $taxas_inst = array_filter($this->taxas, function($obj) use ($instituicao) {
                    if($obj->instituicao == $instituicao)
                        return true;
                });

                foreach($taxas_inst as $taxa){
                    if(in_array($taxa->convenio,$data['convenios'])){ // filtrando convenios
                        if(isset($data['parcela'])){
                            if($taxa->parcelas == $data['parcela']){ // filtrando parcela
                                $ret[$instituicao][] = [
                                    'taxa'=> $taxa->taxaJuros,
                                    'parcelas'=> $taxa->parcelas,
                                    'valor_parcela'=> round($data['valor_emprestimo'] * $taxa->coeficiente,2),
                                    'convenio'=> $taxa->convenio,
                                ];
                            }
                        }
                        else{
                            $ret[$instituicao][] = [
                                'taxa'=> $taxa->taxaJuros,
                                'parcelas'=> $taxa->parcelas,
                                'valor_parcela'=> round($data['valor_emprestimo'] * $taxa->coeficiente,2),
                                'convenio'=> $taxa->convenio,
                            ];
                        }
                        
                    }
                }
            }

            return $ret;
        }
        catch (Exception $ex) { // Anything that went wrong
            abort(500, 'ERROR');
        }

    }
}
