
<?php
require_once 'generar_factura.php';
require_once '../conexion.php';
function check($x) {
    if (date('Y-m-d', strtotime($x)) == $x) {
      return true;
    } else {
      return false;
    }
}
function insertarlogFacturas($cod_error,$detalle_error,$json){
    $dbh = new Conexion();
    $fecha =date('Y-m-d H:i:s');
    $sql="INSERT INTO log_facturas(fecha,cod_error,detalle_error,json) values('$fecha','$cod_error','$detalle_error','$json')";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();    
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Decodificando formato Json
    $json=file_get_contents("php://input");
    $datos = json_decode($json, true);    
    //Parametros de consulta
    $accion=NULL;
    $estado='false';
    $mensaje="ERROR";
    $sucursalId=null;$pasarelaId=null;$fechaFactura=null;$nitciCliente=null;$razonSocial=null;$importeTotal=null;$items=null;
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
                    if(isset($datos['items'])) $items=$datos['items'];//recibimos array de detalle
                    $cont_items=0;
                    $importeTotal_x=0;
                    $sw=true;
                    $fechaFactura_actual=date('Y-m-d');
                    foreach ($items as $valor) {  
                        $cont_items++;
                        $suscripcionId=$valor['suscripcionId'];
                        $pagoCursoId=$valor['pagoCursoId'];
                        $detalle=strval($valor['detalle']);
                        $precioUnitario=$valor['precioUnitario'];
                        $cantidad=$valor['cantidad'];
                        $importeTotal_x=$importeTotal_x+($precioUnitario*$cantidad);
                        // echo $suscripcionId." - ".$pagoCursoId."<br>";
                        if($suscripcionId<=0 && $pagoCursoId<=0 && !is_numeric($suscripcionId) && !is_numeric($pagoCursoId)){
                            $sw=false;
                            $estado=7;
                            $mensaje = "algún item con Id Suscripcion y Id PagoCurso vacíos";
                        }elseif($detalle==null){
                            $sw=false;
                            $estado=8;
                            $mensaje = "algún item con detalle vacío";
                        }elseif($precioUnitario<=0 && !is_numeric($precioUnitario)){
                            $sw=false;
                            $estado=9;
                            $mensaje = "algún item con precio incorrecto";
                        }elseif($cantidad<=0 && !is_numeric($cantidad)){
                            $sw=false;
                            $estado=10;
                            $mensaje = "algún item con cantidad incorrecta";
                        }
                    }
                    if($sucursalId==null || $sucursalId!=1){
                        $estado=1;
                        $mensaje = "Id Sucural incorrecta";
                    }elseif($pasarelaId==null || $pasarelaId!=1){
                        $estado=2;
                        $mensaje = "Id Paralela incorrecta";
                    }elseif(!check($fechaFactura) || $fechaFactura!=$fechaFactura_actual){
                        $estado=3;
                        $mensaje = "Fecha incorrecta o no actual";
                    }elseif($nitciCliente==null || $nitciCliente<0 || !is_numeric($nitciCliente)){
                        $estado=4;
                        $mensaje = "Nit incorrecto";
                    }elseif($razonSocial==null || $razonSocial=='' || $razonSocial==' '){
                        $estado=5;
                        $mensaje = "Razón Social vacía";
                    }elseif($items==null || $cont_items<=0){
                        $estado=6;
                        $mensaje = "Items Vacío";
                    }elseif(!$sw){
                        //mostrará el error de los items 
                    }else{
                        // $estado=0;
                        // $mensaje = "todo ok";
                        $rspString = ejecutarGenerarFactura($sucursalId,$pasarelaId,$fechaFactura,$nitciCliente,$razonSocial,$importeTotal_x,$items);//llamamos a la funcion                 
                        $rspArray = explode("###", $rspString);
                        $rsp=$rspArray[0];
                        $cod_factura=$rspArray[1];
                        if($rsp==0){
                            $estado=0;
                            $mensaje = "Factura Generada Correctamente";
                        }elseif($rsp==11){
                            $estado=11;
                            $mensaje = "Hubo un error al generar la factura, contáctese con el administrador.";
                        }else{
                            $estado=12;//no encuentro el error
                            $mensaje = "Error interno del servicio";
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
    insertarlogFacturas($estado,$mensaje,$json);
    if($estado==0){$resultado=array("estado"=>$estado,"mensaje"=>$mensaje,"IdFactura"=>$cod_factura);}
    else $resultado=array("estado"=>$estado,"mensaje"=>$mensaje);
    header('Content-type: application/json');
    echo json_encode($resultado);
}else{
    $estado=15;
    $mensaje="Error en las credenciales sKey y sIde";
    $json="";
    insertarlogFacturas($estado,$mensaje,$json);
    $resp=array("estado"=>$estado, 
                "mensaje"=>$mensaje);
    header('Content-type: application/json');
    echo json_encode($resp);
}

?>
