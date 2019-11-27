<?php
set_time_limit(0);
require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';

//print_r($_POST);

$dbh = new Conexion();
$codigoIndicador=$_POST["cod_indicador"];

//SACAMOS LA CONFIGURACION PARA REDIRECCIONAR EL PON
$codigoIndicadorPON=obtenerCodigoPON();


$table="actividades_poaplanificacion";
$urlRedirect="../index.php?opcion=listActividadesPOA&codigo=$codigoIndicador&codigoPON=$codigoIndicadorPON&unidad=0&area=0";

session_start();

$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];

$fechaHoraActual=date("Y-m-d H:i:s");

$flagSuccessDetail=true;
foreach($_POST as $nombre_campo => $valor){ 
   	$asignacion = "\$" . $nombre_campo . "='" . $valor . "';"; 
   
	$cadenaBuscar='plan';
	$posicion = strpos($nombre_campo, $cadenaBuscar);

	if ($posicion === false) {
	}else{
		list($planX, $codActividadX, $mesX)=explode("|",$nombre_campo);

		$stmtDel = $dbh->prepare("DELETE FROM $table WHERE cod_actividad='$codActividadX' and mes='$mesX'");
		$flagSuccess=$stmtDel->execute();

    	$sql="INSERT INTO $table (cod_actividad, mes, value_numerico) VALUES (:cod_actividad, :cod_mes, :valor)";	    	
	    $stmt = $dbh->prepare($sql);
		$values = array( ':cod_actividad' => $codActividadX,
        ':cod_mes' => $mesX,
        ':valor' => $valor
    	);

		/*$exQuery=str_replace(array_keys($values), array_values($values), $sql);
		echo $exQuery.";<br>";*/

		$flagSuccess2=$stmt->execute($values);
		if($flagSuccess2==false){
			$flagSuccessDetail=false;
		}
	}
}

if($flagSuccessDetail==true){
	showAlertSuccessError(true,$urlRedirect);	
}else{
	showAlertSuccessError(false,$urlRedirect);
}

?>
