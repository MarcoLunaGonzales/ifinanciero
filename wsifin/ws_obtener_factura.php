<?php
// SERVICIO WEB PARA FACTURAS

//estados
require 'htmlFacCliente.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $datos = json_decode(file_get_contents("php://input"), true); 
    //Parametros de consulta
    $accion=NULL;
    if(isset($datos['accion'])&&isset($datos['sIdentificador'])&&isset($datos['sKey']))
        if($datos['sIdentificador']=="facifin"&&$datos['sKey']=="rrf656nb2396k6g6x44434h56jzx5g6"){
        $accion=$datos['accion']; //recibimos la accion
        $codFactura=$datos['idFactura'];//recibimos el codigo del proyecto
        $estado=0;
        $mensaje="";
        if($accion=="ObtenerFacturaPDF"){
            try{
            
                $html=generarHTMLFacCliente($codFactura);
                if($html=="ERROR"){
                 $estado=2;
                 $mensaje = "No se pudo obtener la factura";
                 $resultado=array("estado"=>$estado, 
                            "mensaje"=>$mensaje, 
                            "factura64"=>array(),
                            "totalComponentes"=>0);
                }else{
                $estado=1;
                $factura = datosPDFFacturasVenta($html); 
                $resultado=array(
                            "estado"=>$estado,
                            "mensaje"=>"Factura generada correctamente", 
                            "factura64"=>$factura['base64'], 
                            "totalComponentes"=>1     
                            );            
                }
            }catch(Exception $e){
                $estado=2;
                $mensaje = "No se pudo obtener la factura";
                $resultado=array("estado"=>$estado, 
                            "mensaje"=>$mensaje, 
                            "factura64"=>array(),
                            "totalComponentes"=>0);
            }
            
         }else{
           $resultado=array("estado"=>3, 
                            "mensaje"=>"No existe el metodo");
         }
        }else{
            $resultado=array("estado"=>4, 
                            "mensaje"=>"Error: las credenciales sKey y sIde no son correctas");
        }
            header('Content-type: application/json');
            echo json_encode($resultado);
}else{
    $resp=array("estado"=>5, 
                "mensaje"=>"No tiene acceso al WS");
    header('Content-type: application/json');
    echo json_encode($resp);
}
