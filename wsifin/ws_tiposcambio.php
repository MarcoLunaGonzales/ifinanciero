<?php
    require_once '../conexion.php';
    require_once '../functions.php';
    $dbh = new Conexion();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Decodificando formato Json
    $datos = json_decode(file_get_contents("php://input"), true);    
    //Parametros de consulta
    $accion=NULL;
    if(isset($datos['accion'])&&isset($datos['sIdentificador'])&&isset($datos['sKey'])){
        if($datos['sIdentificador']=="facifin"&&$datos['sKey']=="AX546321asbhy347bhas191001bn0rc4654"){
                $accion=$datos['accion']; //recibimos la accion
                $estado=false;
                $mensaje="";
                $tipoCambio=0;
                $lista=array();
                if($accion=="solicitarTipoCambio"){
                    $idMoneda=$datos['idMoneda'];
                    $fecha=$datos['fecha'];
                    $tipoCambio=obtenerValorTipoCambio($idMoneda,$fecha);
                    $nameMoneda=nameMoneda($idMoneda);
                    $resultado=array(
                                "estado"=>true,
                                "mensaje"=>"Correcto",
                                "fecha"=>$fecha, 
                                "nombreDivisa"=>$nameMoneda, 
                                "tipoCambio"=>$tipoCambio
                                );
                }else{
                    $resultado=array("estado"=>false, 
                                    "mensaje"=>"Error: Parametros incorrectos o sin datos!");
                }
        }else{
            $resultado=array("estado"=>false, 
                                "mensaje"=>"Error: No tiene acceso al WS!");
        }        
    }else{
        $resultado=array("estado"=>false, 
                                "mensaje"=>"Error: No tiene acceso al WS!");
    } 
    header('Content-type: application/json');
    echo json_encode($resultado);     
}else{
    $resp=array("estado"=>false, 
                "mensaje"=>"No tiene acceso al WS");
    header('Content-type: application/json');
    echo json_encode($resp);
}
?>