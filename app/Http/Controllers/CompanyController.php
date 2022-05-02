<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use JWTAuth;

use App\Models\Company;
use App\Utils\ApiUtils;

class CompanyController extends Controller
{
    protected $user;

    public function __construct() {
        $this->middleware('jwt.auth');
    }

    public function listar(){
        try {
            $empresas = Company::orderBy('razon_social','asc')->get();
            foreach($empresas as $empresa) {
                $empresa->domicilio_fiscal = $empresa->direccion_fiscal . " " . $empresa->distrito_ciudad . " " . $empresa->departamento;
                unset($empresa->created_at,$empresa->updated_at,$empresa->deleted_at,$empresa->email);
                unset($empresa->es_propia,$empresa->numero_telefonico,$empresa->departamento,$empresa->direccion_fiscal);
            }
        }
        catch (Exception $ex) {
            return ApiUtils::respuesta(false);
        }

        return ApiUtils::respuesta(true, ['empresas' => $empresas]);
    }

    public function simpleListar() {
        try { 
            $empresas = Company::select('id','razon_social')->get();
            foreach($empresas as $empresa) {
                $empresa->label = $empresa->razon_social;
                unset($empresa->razon_social);
            }
            $empresas[] = [
                "label" => "Selecciona una empresa",
                "id" => 0
            ];
        }
        catch (Exception $ex) {
            return ApiUtils::respuesta(false);
        }
        return ApiUtils::respuesta(true, ['empresas' => $empresas]);
    }

    public function crear(Request $request) {
        $empresa = new Company;

        $empresa->ruc = $request->ruc;
        $empresa->razon_social = $request->razon_social;
        $empresa->tipo_contribuyente = $request->tipo_contribuyente;
        $empresa->direccion_fiscal = $request->direccion_fiscal;
        $empresa->distrito_ciudad = $request->distrito_ciudad;
        $empresa->departamento = $request->departamento;
        $empresa->email = $request->email;
        $empresa->numero_telefonico = $request->numero_telefonico;
        $empresa->es_propia = 0;
        $empresa->estado = 1;

        $empresa->save();

        return ApiUtils::respuesta(true, ['empresa' => $empresa]);
    }

    public function detalle($id){
        try {
            $empresa = Company::find($id);
        }
        catch (Exception $ex) {
            return ApiUtils::respuesta(false);
        }
        
        return ApiUtils::respuesta(true, ['empresa' => $empresa]);
    }

    public function editar(Request $request) {
        $empresa = Company::find($request->id);

        $empresa->ruc = $request->ruc;
        $empresa->razon_social = $request->razon_social;
        $empresa->tipo_contribuyente = $request->tipo_contribuyente;
        $empresa->direccion_fiscal = $request->direccion_fiscal;
        $empresa->distrito_ciudad = $request->distrito_ciudad;
        $empresa->departamento = $request->departamento;
        $empresa->email = $request->email;
        $empresa->numero_telefonico = $request->numero_telefonico;

        $empresa->save();

        return ApiUtils::respuesta(true, ['empresa' => $empresa]);
    }

    public function activar($id) {
        try {
            $empresa = Company::find($id);
        }
        catch (Exception $ex) {
            return ApiUtils::respuesta(false);
        }

        $empresa->estado = 1;

        $empresa->save();

        return ApiUtils::respuesta(true, ['empresa' => $empresa]);
    }

    public function desactivar($id) {
        try {
            $empresa = Company::find($id);
        }
        catch (Exception $ex) {
            return ApiUtils::respuesta(false);
        }

        $empresa->estado = 0;

        $empresa->save();

        return ApiUtils::respuesta(true, ['empresa' => $empresa]);
    }
}
