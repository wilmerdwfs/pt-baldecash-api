<?php  
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class PedidosController extends Controller
{

    public function crearPedido(Request $request)
    {
    
        return response()->json([
                           'mensaje' => 'Credenciales incorrectas',
                           'estatus' => '401'
                        ], 401);
    }

    public function preDatos(Request $request)
    {
          $datConfig = DB::table('c_general')
                    ->select('controlado', 
                        'manLote', 
                        'manDesCom',
                        'manDesComP',
                        'manDesFin',
                        'busEmp',
                        'filtroPres'
                    )->first();
    
        return response()->json([
                           'mensaje' => 'Credenciales incorrectas',
                           'estatus' => '200'
                        ], 200);
    }

    public function buscarCliente($parametro)
    {
      
        $dtClientes = DB::table('c_terceros')
                    ->select('id', 'nit', 'nombre')
                     ->where('nombre', 'like', '%' . $parametro . '%') 
                    ->orWhere('nit', 'like', '%' . $parametro . '%') 
                    ->get();

        return response()->json([
                           'mensaje' => '',
                           'dtClientes' => $dtClientes,
                        ], 200);
    }

    public function buscarArticulos($parametro)
    {
     

        $buscar = strtoupper(ltrim(rtrim($parametro)));

        $datConfig = DB::table('c_general')
                    ->select('controlado', 
                        'manLote', 
                        'manDesCom',
                        'manDesComP',
                        'manDesFin',
                        'busEmp',
                        'filtroPres'
                    )->first();

       
        $controlado = $datConfig->controlado; 
        $manLote = $datConfig->manLote; 
        $manDesCom = $datConfig->manDesCom; 
        $manDesComP = $datConfig->manDesComP; 
        $manDesFin = $datConfig->manDesFin; 
        $filtroPres = $datConfig->filtroPres;

        if ($controlado==0)
        {  

            $con = " ( MATCH(a.codigo, a.nombre) AGAINST ('+".str_replace(" ", "* +", $buscar)."*' IN BOOLEAN MODE) )";

            if ($filtroPres==0){

                $dtArticulos = DB::select("CALL pr_busq_art_venta_fv('".$buscar."',0,0,0)");

            }else{

                $con = "( a.nombre like '%".$buscar."%' or a.codigo like '%".$buscar."%')" ;

               
                $dtArticulos = DB::select('CALL pr_busq_art_venta_copy( "'.$con.'" ,0,0,0,0)');
            }
        }

        return response()->json([
                           'mensaje' => '',
                           'dtArticulos' => $dtArticulos,
                        ], 200);
    }

    public function lista(Request $request)
    {
    
        return response()->json([
                           'mensaje' => 'Credenciales incorrectas',
                        
                        ], 200);
    }

}