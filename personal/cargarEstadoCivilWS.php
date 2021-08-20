<?php 
set_time_limit(0);

// require_once '../conexion.php';
// require_once '../functions.php';
// require_once '../rrhh/configModule.php';

require_once 'conexion.php';
require_once 'functions.php';
require_once 'rrhh/configModule.php';

$dbh = new Conexion();
$direccion=obtenerValorConfiguracion(42);//direccion des servicio web
$sIde = "monitoreo"; 
$sKey = "837b8d9aa8bb73d773f5ef3d160c9b17";

$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "lista"=>"HijoPadre", "padre"=>"1574");
$url=$direccion."clasificador/ws-clasificador-post.php";

$jsons=callService($parametros, $url);

// header('Content-type: application/json'); 	
// 		print_r($jsons);	
$obj=json_decode($jsons);//decodificando json

$detalle=$obj->lista;

foreach ($detalle as $objDet){
	$codigoG = $objDet->IdClasificador;
	$nombre = $objDet->Descripcion;
	$abreviatura = $objDet->Abrev;
	$auxiliar = $objDet->Auxiliar;
	$cod_estadoreferencial=1;
	
	//echo $codigo;
	$stmtBusquedaTipoGd = $dbh->prepare("SELECT codigo from tipos_estado_civil where codigo=:codigo");
    $stmtBusquedaTipoGd->bindParam(':codigo',$codigoG);
    $stmtBusquedaTipoGd->execute();	    
    $resultP=$stmtBusquedaTipoGd->fetch();
    $codigoG2=$resultP['codigo'];
    if($codigoG==$codigoG2){//UPDATE
    	$stmtGU = $dbh->prepare("UPDATE tipos_estado_civil set codigo=:codigo,nombre=:nombre, abreviatura=:abreviatura
        where codigo = :codigo");
        //bind
        $stmtGU->bindParam(':codigo', $codigoG);        
        $stmtGU->bindParam(':nombre', $nombre);    
        $stmtGU->bindParam(':abreviatura', $abreviatura);        
        $flag=$stmtGU->execute(); 
    }
    else{//instert

    	$stmtGI = $dbh->prepare("INSERT INTO tipos_estado_civil(codigo,nombre,abreviatura,cod_estadoreferencial) values(:codigo,:nombre,:abreviatura,:cod_estadoreferencial)");
        //bind
        $stmtGI->bindParam(':codigo', $codigoG);        
        $stmtGI->bindParam(':nombre', $nombre);        
        $stmtGI->bindParam(':abreviatura', $abreviatura);
        $stmtGI->bindParam(':cod_estadoreferencial', $cod_estadoreferencial);
        $flag=$stmtGI->execute(); 
    }
}
if($flag) echo "Estado Civil CORRECTO.<br>";
else echo "Estado Civil ERROR.<br>";


?>

