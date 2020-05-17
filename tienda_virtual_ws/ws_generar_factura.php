
<?php
require_once 'generar_factura.php';

function listarComponentes($codigo_proyecto){
    require_once '../conexion.php';
    $dbh = new Conexion();
    // Preparamos
    $stmt = $dbh->prepare("SELECT codigo,nombre,abreviatura from componentessis where cod_estado=1 and cod_proyecto=$codigo_proyecto;");

    $resp = false;
    $filas = array();
    if($stmt->execute()){
        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $resp = true;
    }
    else{
        echo "Error: Listar Componentes";
        $resp=false;
        exit;       
    }
    return $filas;
}
/*
 SERVICIO WEB PARA OPERACIONES Componentes  */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Decodificando formato Json
    $datos = json_decode(file_get_contents("php://input"), true);    
    //Parametros de consulta
    $accion=NULL;
    if(isset($datos['accion'])){
        
        // $accion='GenerarFactura'; //recibimos la accion
        
        // $IdSucursal=5; // ID Sucursal
        // $FechaFactura='2020-05-09'; // fecha a factura
        // $Identificacion=1020113024; //nit o ci de cliente
        // $RazonSocial='Juan Gabriel'; //razon social
        // $ImporteTotal=260.5; //importe total

        // $Objeto_detalle = new stdClass();
        // $Objeto_detalle->suscripcionId = 1;
        // $Objeto_detalle->pagoCursoId = 1;
        // $Objeto_detalle->detalle = "detalle del item";
        // $Objeto_detalle->precioUnitario = 100;
        // $Objeto_detalle->cantidad = 1;

        // $Objeto_detalle2 = new stdClass();
        // $Objeto_detalle2->suscripcionId = 2;
        // $Objeto_detalle2->pagoCursoId = 2;
        // $Objeto_detalle2->detalle = "detalle del item2";
        // $Objeto_detalle2->precioUnitario = 100;
        // $Objeto_detalle2->cantidad = 1;
        // $Detalle= array($Objeto_detalle,$Objeto_detalle2);
        
        $accion=$datos['accion']; //recibimos la accion
        $IdSucursal=$datos['IdSucursal'];//recibimos el codigo de la sucursal

        $FechaFactura=$datos['FechaFactura'];//recibimos fecha de factura
        $Identificacion=$datos['Identificacion'];//recibimos ci o nit del cliente
        $RazonSocial=$datos['RazonSocial'];//recibimos razon social
        $ImporteTotal=$datos['ImporteTotal'];//recibimos el importe total
        $Detalle=$datos['Detalle'];//recibimos array de detalle



        $estado='false';
        $mensaje="";
        $total=0;
        $lista=array();
        if($accion=="GenerarFactura"){//nombre de la accion           
            try{                
                $rspString = ejecutarGenerarFactura($IdSucursal,$FechaFactura,$Identificacion,$RazonSocial,$ImporteTotal,$Detalle);//llamamos a la funcion                 
                $rspArray = explode("###", $rspString);
                $rsp=$rspArray[0];
                $cod_factura=$rspArray[1];

                if($rsp==1){
                    $resultado=array(
                        "estado"=>1,
                        "mensaje"=>"Factura Generada Correctamente", 
                        "IdFactura"=>$cod_factura 
                        // "totalComponentes"=>$totalComponentes
                        );
                }elseif($rsp==2 || $rsp==3 || $rsp==6){                    
                    $estado=2;
                    $mensaje = "Hubo un error al generar la factura, contáctese con el administrador.";
                    $resultado=array("estado"=>$estado, 
                                "mensaje"=>$mensaje//, 
                                // "lstComponentes"=>array(),
                                // "totalComponentes"=>0);
                    );
                }elseif($rsp==4){                   
                    $estado=4;
                    $mensaje = "La Factura ya fue generada";
                    $resultado=array("estado"=>$estado, 
                                "mensaje"=>$mensaje//, 
                                // "lstComponentes"=>array(),
                                // "totalComponentes"=>0);
                    );

                }else{
                    $estado=5;//no encuentro el error
                    $mensaje = "no encuentro el error";
                    $resultado=array("estado"=>$estado, 
                                "mensaje"=>$mensaje//, 
                                // "lstComponentes"=>array(),
                                // "totalComponentes"=>0);
                    );


                }
            }catch(Exception $e){
                $estado=true;
                $mensaje = "No se pudo obtener la lista de Componentes".$e;
                $resultado=array("estado"=>$estado, 
                            "mensaje"=>$mensaje//, 
                            // "lstComponentes"=>array(),
                            // "totalComponentes"=>0);
                );

            }
            
            
        }else{
            $resultado=array("estado"=>'false', 
                            "mensaje"=>"Error: Operacion incorrecta!");
        }
        header('Content-type: application/json');
        echo json_encode($resultado);
    }else{

    }
}else{
    $resp=array("estado"=>'false', 
                "mensaje"=>"No tiene acceso al WS");
    header('Content-type: application/json');
    echo json_encode($resp);
}

?>
