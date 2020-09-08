<?php
function obtenerDatosAccNum($codigo){
    require_once '../conexion.php';
    $dbh = new Conexion();
    // Preparamos
    $stmt = $dbh->prepare("SELECT c.codigo, c.nombre, c.abreviatura from ibnmonitoreo.external_costs c where c.cod_estado=1 and c.codigo='$codigo'");
    $resp = false;
    $filas = array("nombre"=>"","abreviatura"=>"");
    if($stmt->execute()){
        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $resp = true;
    }
    else{
        echo "Error: Datos Acc";
        $resp=false;
        exit;       
    }
    return $filas;
}

function listarComponentes(){
    require_once '../conexion.php';
    $dbh = new Conexion();
    // Preparamos
    $sql="SELECT c.codigo, c.nombre, c.abreviatura from ibnmonitoreo.external_costs c where c.cod_estado=1 and c.cod_gestion in (select max(cc.cod_gestion) from ibnmonitoreo.external_costs cc) order by c.abreviatura";
    
    //echo $sql;
    
    $stmt = $dbh->prepare($sql);

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
    if(isset($datos['accion']))
        $accion=$datos['accion']; //recibimos la accion
        $estado=false;
        $mensaje="";
        $total=0;
        $lista=array();
        if($accion=="ListarAccNum"){
            try{                
                $lstComponentes = listarComponentes();//llamamos a la funcion 
                $totalComponentes=count($lstComponentes);
                $resultado=array(
                            "estado"=>true,
                            "mensaje"=>"Lista de Componentes obtenida correctamente", 
                            "lstComponentes"=>$lstComponentes, 
                            "totalComponentes"=>$totalComponentes
                            );
            }catch(Exception $e){
                $estado=true;
                $mensaje = "No se pudo obtener la lista de Componentes".$e;
                $resultado=array("estado"=>$estado, 
                            "mensaje"=>$mensaje, 
                            "lstComponentes"=>array(),
                            "totalComponentes"=>0);
            }
            
            
        }else{
            if($accion=="DatosAccNumProyecto"){
                $codigo=$datos['codigo'];//recibimos el codigo del proyecto
              try{                
                $lstComponentes = obtenerDatosAccNum($codigo);//llamamos a la funcion 
                $totalComponentes=count($lstComponentes);
                $resultado=array(
                            "estado"=>true,
                            "mensaje"=>"Datos de Componentes obtenida correctamente", 
                            "lstComponentes"=>$lstComponentes, 
                            "totalComponentes"=>$totalComponentes
                            );
              }catch(Exception $e){
                $estado=true;
                $mensaje = "No se pudo obtener los datos de Componentes".$e;
                $resultado=array("estado"=>$estado, 
                            "mensaje"=>$mensaje, 
                            "lstComponentes"=>array(),
                            "totalComponentes"=>0);
              }       
           }else{

            $resultado=array("estado"=>false, 
                            "mensaje"=>"Error: Operacion incorrecta!");
           }
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