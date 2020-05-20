<?php
// SERVICIO WEB PARA FACTURAS
require 'htmlFacCliente.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $datos = json_decode(file_get_contents("php://input"), true); 
    //Parametros de consulta
    $accion=NULL;
    if(isset($datos['accion'])&&isset($datos['sIdentificador'])&&isset($datos['sKey']))
        if($datos['sIdentificador']=="facifin"&&$datos['sKey']=="rrf656nb2396k6g6x44434h56jzx5g6"){
        $accion=$datos['accion']; //recibimos la accion
        $codFactura=$datos['idFactura'];//recibimos el codigo del proyecto
        $estado=false;
        $mensaje="";
        if($accion=="ObtenerFacturaPDF"){
            try{
            
                $html=generarHTMLFacCliente($codFactura);
                if($html=="ERROR"){
                 $estado=true;
                 $mensaje = "No se pudo obtener la factura";
                 $resultado=array("estado"=>$estado, 
                            "mensaje"=>$mensaje, 
                            "factura64"=>array(),
                            "totalComponentes"=>0);
                }else{
                $estado=true;
                $factura = datosPDFFacturasVenta($html); 
                $resultado=array(
                            "estado"=>$estado,
                            "mensaje"=>"Factura generada correctamente", 
                            "factura64"=>$factura['base64'], 
                            "totalComponentes"=>1     
                            );            
                }
            }catch(Exception $e){
                $estado=true;
                $mensaje = "No se pudo obtener la lista de Componentes".$e;
                $resultado=array("estado"=>$estado, 
                            "mensaje"=>$mensaje, 
                            "factura64"=>array(),
                            "totalComponentes"=>0);
            }
            
         }else{
           $resultado=array("estado"=>false, 
                            "mensaje"=>"No tiene acceso al WS");
         }
        }else{
            $resultado=array("estado"=>false, 
                            "mensaje"=>"Error: Operacion incorrecta!");
        }
            header('Content-type: application/json');
            echo json_encode($resultado);
}else{
    $resp=array("estado"=>false, 
                "mensaje"=>"No tiene acceso al WS");
    header('Content-type: application/json');
    echo json_encode($resp);
}
