<?php
require_once 'generar_factura.php';
require_once '../conexion.php';
require_once '../functions.php';
function check($x) {
    if (date('Y-m-d', strtotime($x)) == $x) {
      return true;
    } else {
      return false;
    }
}
function insertarlogFacturas_entrada($json,$mensaje){
    $dbh = new Conexion();
    // date_default_timezone_set('America/La_Paz');
    // $fecha =date('Y-m-d H:i:s');
    $sql="INSERT INTO log_facturas(fecha,detalle_error,json) values(NOW(),'$mensaje','$json')";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();    
}
function InsertlogFacturas_salida($cod_error,$detalle_error,$json){  
    $dbh = new Conexion();
    // $fecha =date('Y-m-d H:i:s');
    $sql="INSERT INTO log_facturas(fecha,cod_error,detalle_error,json) values(NOW(),'$cod_error','$detalle_error','$json')";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();    
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Decodificando formato Json
    $json=file_get_contents("php://input");
    insertarlogFacturas_entrada($json,'Entrada json');
    $datos = json_decode($json, true);    
    //Parametros de consulta
    $accion=NULL;
    $estado='false';
    $mensaje="ERROR";
    $sw_cod_libreta=true;
    $sucursalId=null;$pasarelaId=null;$fechaFactura=null;$nitciCliente=null;$razonSocial=null;$importeTotal=null;$items=null;$CodLibretaDetalle=null;$tipoPago=null;
    if(isset($datos['accion'])&&isset($datos['sIdentificador'])&&isset($datos['sKey']))
    {
        if($datos['sIdentificador']=="facifin"&&$datos['sKey']=="rrf656nb2396k6g6x44434h56jzx5g6")
        {
            if(isset($datos['accion'])){
                $accion=$datos['accion']; //recibimos la accion
                if($accion=="GenerarFactura"){//nombre de la accion
                    if(isset($datos['sucursalId'])) $sucursalId=$datos['sucursalId'];//recibimos el codigo de la sucursal
                    if(isset($datos['sucursalId'])) $pasarelaId=$datos['pasarelaId'];//recibimos paralela
                    if(isset($datos['fechaFactura'])) $fechaFactura=strval($datos['fechaFactura']);//recibimos fecha de factura
                    if(isset($datos['nitciCliente'])) $nitciCliente=$datos['nitciCliente'];//recibimos ci o nit del cliente
                    if(isset($datos['razonSocial'])) $razonSocial=strval($datos['razonSocial']);//recibimos razon social
                    if(isset($datos['importeTotal'])) $importeTotal=$datos['importeTotal'];//recibimos el importe total
                    if(isset($datos['tipoPago'])) $tipoPago=$datos['tipoPago'];//recibimos el tipo de pago
                    if(isset($datos['codLibretaDetalle']))$CodLibretaDetalle=$datos['codLibretaDetalle'];//recibimos el importe total
                    if(isset($datos['items'])) $items=$datos['items'];//recibimos array de detalle
                    $cont_items=0;
                    $importeTotal_x=0;
                    $sw=true;
                    date_default_timezone_set('America/La_Paz');
                    $fechaFactura_actual=date('Y-m-d');
                    $cod_tipopago_deposito_cuenta=obtenerValorConfiguracion(55);
                    $normas=0;
                    foreach ($items as $valor) {  
                        $cont_items++;
                        $suscripcionId=$valor['suscripcionId'];
                        $pagoCursoId=$valor['pagoCursoId'];                    
                        $detalle=strval($valor['detalle']);
                        $precioUnitario=$valor['precioUnitario'];
                        $cantidad=$valor['cantidad'];

                        //$importeTotal_x=$importeTotal_x+($precioUnitario*$cantidad);                        
                        // echo $suscripcionId." - ".$pagoCursoId."<br>";
                        if($suscripcionId<=0 && $pagoCursoId<=0 && !is_numeric($suscripcionId) && !is_numeric($pagoCursoId)){
                            $sw=false;
                            $estado=7;
                            $mensaje = "algún item con Id Suscripcion y Id PagoCurso vacíos";
                        }elseif($detalle==null){
                            $sw=false;
                            $estado=8;
                            $mensaje = "algún item con detalle vacío";
                        }elseif($precioUnitario<=0 || !is_numeric($precioUnitario)){
                            $sw=false;
                            $estado=9;
                            $mensaje = "algún item con precio incorrecto";
                        }elseif($cantidad<=0 || !is_numeric($cantidad)){
                            $sw=false;
                            $estado=10;
                            $mensaje = "algún item con cantidad incorrecta";
                        }
                        if($suscripcionId>0 || $pagoCursoId==0){
                            $normas=1;
                        }

                        if($precioUnitario>0 && is_numeric($precioUnitario)){
                            $importeTotal_x=$importeTotal_x+($precioUnitario*$cantidad);                        
                        }
                    }
                    // $sw=false;
                    //     $estado=7;
                    //     $mensaje = $importeTotal_x."omporgte";

                    if($sucursalId==null || $sucursalId!=1){
                        $estado=1;
                        $mensaje = "Id Sucural incorrecta";
                    }elseif($pasarelaId==null || $pasarelaId!=1){
                        $estado=2;
                        $mensaje = "Id Paralela incorrecta";
                    }elseif(!check($fechaFactura) || $fechaFactura!=$fechaFactura_actual){
                        $estado=3;
                        $mensaje = "Fecha incorrecta o no actual";
                    }elseif($nitciCliente==null || $nitciCliente<0 || !is_numeric($nitciCliente) ||strlen($nitciCliente)>10){
                        $estado=4;
                        $mensaje = "Nit incorrecto";
                    }elseif($razonSocial==null || $razonSocial=='' || $razonSocial==' '){
                        $estado=5;
                        $mensaje = "Razón Social vacía";
                    }elseif($tipoPago==null || $tipoPago=='' || $tipoPago==' '){
                        $estado=16;
                        $mensaje = "Tipo de Pago no encontrado";
                    }elseif(($CodLibretaDetalle==null || $CodLibretaDetalle=='' || $CodLibretaDetalle==' ')&&$CodLibretaDetalle!='0'){
                        $estado=17;
                        $mensaje = "CodLibretaDetalle no encontrado.";
                    }elseif($items==null || $cont_items<=0){
                        $estado=6;
                        $mensaje = "Items Vacío";
                    }elseif(!$sw){
                        //mostrará el error de los items 
                    }else{
                        if($tipoPago==5 || $tipoPago==6){
                            if($CodLibretaDetalle=='0'){
                                $estado=17;
                                $mensaje = "CodLibretaDetalle no encontrado.";
                                $sw_cod_libreta=false;
                            }else{
                                $controlador_libreta=verificarLibretaBancarias($CodLibretaDetalle);                                
                                if($controlador_libreta==0){
                                    $sw_cod_libreta=true;
                                }else{
                                    $estado=17;
                                    $mensaje = "CodLibretaDetalle no encontrado.";
                                    $sw_cod_libreta=false;
                                }
                            }
                        }
                        if($sw_cod_libreta){                            
                            $rspString = ejecutarGenerarFactura($sucursalId,$pasarelaId,$fechaFactura,$nitciCliente,$razonSocial,$importeTotal_x,$items,$CodLibretaDetalle,$tipoPago,$normas);//llamamos a la funcion                 
                            $rspArray = explode("###", $rspString);
                            $rsp=$rspArray[0];
                            if($rsp=='0'){
                                $cod_factura=$rspArray[1];
                                $estado='0';
                                $mensaje = "Factura Generada Correctamente";
                            }elseif($rsp==11){
                                $estado=11;
                                $mensaje = "Hubo un error al generar la factura, contáctese con el administrador.";
                            }elseif($rsp==17){
                                $estado=17;
                                $mensaje = "CodLibretaDetalle no encontrado.";
                            }elseif($rsp==18){
                                $estado=18;
                                $mensaje = "La Suma del Monto de las Libretas es menor al de la factura.";
                            }else{
                                $estado=12;//no encuentro el error
                                $mensaje = "Error interno del servicio";
                            }
                        }
                            
                    }            
                }else{
                    $estado=14;
                    $mensaje="Acción no encontrada";
                }
            }else{
                $estado=14;
                $mensaje="Acción no encontrada";
            }
        }else{
            $estado=15;
            $mensaje="Error en las credenciales sKey y sIde";            
        }
    }else{
        $estado=15;
        $mensaje="Error en las credenciales sKey y sIde";
    }
    if(isset($cod_factura)){
        $mensaje.=" IdFactura: ".$cod_factura;
    }
    InsertlogFacturas_salida($estado,$mensaje,$json);
    if($estado=='0'){$resultado=array("estado"=>$estado,"mensaje"=>$mensaje,"IdFactura"=>$cod_factura);}
    else $resultado=array("estado"=>$estado,"mensaje"=>$mensaje);
    header('Content-type: application/json');
    echo json_encode($resultado);
}else{
    $estado=15;
    $mensaje="Error en las credenciales sKey y sIde";
    $json="";
    insertarlogFacturas_entrada($json,$mensaje);    
    $resp=array("estado"=>$estado, 
                "mensaje"=>$mensaje);
    header('Content-type: application/json');
    echo json_encode($resp);
}

?>
