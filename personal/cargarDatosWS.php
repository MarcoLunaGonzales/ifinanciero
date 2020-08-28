<?php 
set_time_limit(0);
require_once 'conexion.php';
require_once 'functions.php';
require_once 'rrhh/configModule.php';
$dbh = new Conexion();
$direccion=obtenerValorConfiguracion(42);//direccion des servicio web
$sIde = "monitoreo"; 
$sKey = "837b8d9aa8bb73d773f5ef3d160c9b17";

$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "lista"=>"HijoPadre", "padre"=>"1580");
$url=$direccion."clasificador/ws-clasificador-post.php";

$jsons=callService($parametros, $url);

// header('Content-type: application/json'); 	
// 		print_r($jsons);	
$obj=json_decode($jsons);//decodificando json

$detalle=$obj->lista;
foreach ($detalle as $objDet){
	$codigoTI = $objDet->IdClasificador;
	$nombre = $objDet->Descripcion;
	$abreviatura = $objDet->Abrev;
	$cod_estadoreferencial=1;
	//echo $codigo;
	$stmtBusquedaTipoId = $dbh->prepare("SELECT codigo from tipos_identificacion_personal where codigo=:codigo");
    $stmtBusquedaTipoId->bindParam(':codigo',$codigoTI);
    $stmtBusquedaTipoId->execute();	    
    $resultP=$stmtBusquedaTipoId->fetch();
    $codigoTI2=$resultP['codigo'];
    if($codigoTI==$codigoTI2){//UPDATE
    	$stmtTIU = $dbh->prepare("UPDATE tipos_identificacion_personal set codigo=:codigo,nombre=:nombre, abreviatura=:abreviatura
        where codigo = :codigo");
        //bind
        $stmtTIU->bindParam(':codigo', $codigoTI);        
        $stmtTIU->bindParam(':nombre', $nombre);    
        $stmtTIU->bindParam(':abreviatura', $abreviatura);        
        $flag=$stmtTIU->execute(); 
    }
    else{//instert
    	$stmtTIU = $dbh->prepare("INSERT INTO tipos_identificacion_personal(codigo,nombre,abreviatura,cod_estadoreferencial) values(:codigo,:nombre,:abreviatura,:cod_estadoreferencial)");
        //bind
        $stmtTIU->bindParam(':codigo', $codigoTI);        
        $stmtTIU->bindParam(':nombre', $nombre);        
        $stmtTIU->bindParam(':abreviatura', $abreviatura);
        $stmtTIU->bindParam(':cod_estadoreferencial', $cod_estadoreferencial);
        $flag=$stmtTIU->execute(); 
    }
}
if($flag) echo "Tipos Identificación CORRECTO.<br>";
else echo "Tipos Identificación ERROR.<br>";

//lista de departamentos Persona
$codigo_pais=26;//bolivia
$lista= obtenerDepartamentoServicioIbrnorca($codigo_pais);
$contador_deptos=0;
foreach ($lista->lista as $listas) {
    if($contador_deptos==0){
        $sql="DELETE FROM personal_departamentos;";
        $stmt = $dbh->prepare($sql);
        $stmt->execute(); 
    }
    $codigo_ciudad=$listas->idEstado;
    $nombre_ciudad=$listas->estNombre;
    $abrev_ciudad=$listas->abrev;
    $cod_pais=$listas->estIdPais;
    $stmtDptos = $dbh->prepare("INSERT INTO personal_departamentos (codigo, nombre,abreviatura,cod_pais,cod_estadoreferencial) VALUES ($codigo_ciudad, '$nombre_ciudad','$abrev_ciudad','$cod_pais','1')");    
    $stmtDptos->execute();
    $contador_deptos++;
}
?>

