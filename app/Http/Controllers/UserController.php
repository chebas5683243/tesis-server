<?php

namespace App\Http\Controllers;

use Excel;
use Exception;
use App\Models\User;
use App\Utils\ApiUtils;

use Illuminate\Support\Str;
use App\Exports\UsersExport;
use Illuminate\Http\Request;
use App\Mail\UserRegistration;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function listar(){
        try {
            $usuarios = User::with(['company'])->orderBy('primer_apellido','asc')
                            ->orderBy('segundo_apellido','asc')
                            ->orderBy('primer_nombre','asc')
                            ->orderBy('segundo_nombre','asc')
                            ->get();
            foreach($usuarios as $usuario) {
                $usuario->nombre = $usuario->primer_apellido . " " .
                                    $usuario->segundo_apellido . " " .
                                    $usuario->primer_nombre . " " .
                                    $usuario->segundo_nombre;
                $usuario->empresa = $usuario->company->razon_social;
                unset($usuario->primer_apellido, $usuario->segundo_apellido, $usuario->primer_nombre, $usuario->segundo_nombre);
                unset($usuario->company,$usuario->company_id,$usuario->created_at,$usuario->deleted_at,$usuario->updated_at);
                unset($usuario->numero_celular,$usuario->es_admin);
            }
        }
        catch (Exception $ex) {
            print($ex);
            return ApiUtils::respuesta(false);
        }

        return ApiUtils::respuesta(true, ['usuarios' => $usuarios]);
    }

    public function crear(Request $request) {
        $usuario = new User;

        $usuario->cargo = $request->cargo;
        $usuario->company()->associate($request->company["id"]);
        $usuario->dni = $request->dni;
        $usuario->email = $request->email;
        $usuario->estado = 1;
        $usuario->numero_celular = $request->numero_celular;
        $usuario->primer_apellido = $request->primer_apellido;
        $usuario->primer_nombre = $request->primer_nombre;
        $usuario->segundo_apellido = $request->segundo_apellido;
        $usuario->segundo_nombre = $request->segundo_nombre;
        $usuario->password = Str::random(10);
        $usuario->es_admin = 0;

        $usuario->save();

        Mail::to($usuario)->send(new UserRegistration($usuario));

        $usuario->codigo = "EV-USU-" . $usuario->id;

        $usuario->save();

        return ApiUtils::respuesta(true, ['usuario' => $usuario]);
    }

    public function editar(Request $request) {
        $usuario = User::with(['company'])->where('id',$request->id)->first();

        $usuario->cargo = $request->cargo;
        $usuario->company_id = $request->company["id"];
        $usuario->dni = $request->dni;
        $usuario->email = $request->email;
        $usuario->numero_celular = $request->numero_celular;
        $usuario->primer_apellido = $request->primer_apellido;
        $usuario->primer_nombre = $request->primer_nombre;
        $usuario->segundo_apellido = $request->segundo_apellido;
        $usuario->segundo_nombre = $request->segundo_nombre;

        $usuario->save();

        return ApiUtils::respuesta(true, ['usuario' => $usuario]);
    }

    public function detalle($id){
        $usuario = User::with(['company:id,razon_social as label'])->where('id',$id)->first();
        
        return ApiUtils::respuesta(true, ['usuario' => $usuario]);
    }

    public function cambiarPassword(Request $request) {
        $usuario = User::find($request->id);
        $usuario->password = $request->password;
        $usuario->save();

        return ApiUtils::respuesta(true, ['usuario' => $usuario]);
    }

    public function desactivar(Request $request) {
        $usuario = User::find($request->id);
        $usuario->estado = 0;
        $usuario->save();

        return ApiUtils::respuesta(true, ['usuario' => $usuario]);
    }

    public function activar(Request $request) {
        $usuario = User::find($request->id);
        $usuario->estado = 1;
        $usuario->save();

        return ApiUtils::respuesta(true, ['usuario' => $usuario]);
    }

    public function simpleListar($id) {
        try { 
            $usuarios = User::select('id','primer_nombre','segundo_nombre','primer_apellido','segundo_apellido')->where('company_id',$id)->get();
            foreach($usuarios as $usuario) {
                $usuario->label = $usuario->primer_nombre . " " . $usuario->segundo_nombre . " " . $usuario->primer_apellido . " " . $usuario->segundo_apellido;
                unset($usuario->razon_social);
            }
            $usuarios[] = [
                "label" => "Selecciona una persona",
                "id" => 0
            ];
        }
        catch (Exception $ex) {
            return ApiUtils::respuesta(false);
        }
        return ApiUtils::respuesta(true, ['usuarios' => $usuarios]);
    }

    public function simpleListarPropio() {
        try { 
            $usuarios = User::select('id','primer_nombre','segundo_nombre','primer_apellido','segundo_apellido')->where('company_id',1)->get();
            foreach($usuarios as $usuario) {
                $usuario->label = $usuario->primer_nombre . " " . $usuario->segundo_nombre . " " . $usuario->primer_apellido . " " . $usuario->segundo_apellido;
                unset($usuario->razon_social);
            }
            $usuarios[] = [
                "label" => "Selecciona una persona",
                "id" => 0
            ];
        }
        catch (Exception $ex) {
            return ApiUtils::respuesta(false);
        }
        return ApiUtils::respuesta(true, ['usuarios' => $usuarios]);
    }

    public function export() 
    {
        return Excel::download(new UsersExport, 'invoices.xlsx');
    }
}
