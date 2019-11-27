<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';

//print_r($_POST);

$dbh = new Conexion();


//SACAMOS LA CONFIGURACION PARA REDIRECCIONAR EL PON
$stmt = $dbh->prepare("SELECT valor_configuracion FROM configuraciones where id_configuracion=6");
$stmt->execute();
$codigoIndicadorPON=0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $codigoIndicadorPON=$row['valor_configuracion'];
}


$codigoIndicador=$_POST["cod_indicador"];

$table="actividades_poaejecucion";
$urlRedirect="../index.php?opcion=listActividadesPOAEjecucion&codigo=$codigoIndicador&codigoPON=$codigoIndicadorPON&area=0&unidad=0";

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
   
   	//echo "CAMPOS: ".$asignacion."<br>";

	$cadenaBuscar='plan';
	$posicion = strpos($nombre_campo, $cadenaBuscar);

	if ($posicion === false) {
	}else{
		list($planX, $codActividadX, $mesX)=explode("|",$nombre_campo);

		$ejeSistema=$_POST["ejsistema|".$codActividadX."|".$mesX];
		$explicacionLogro=$_POST["explicacion|".$codActividadX."|".$mesX];
	    
	    $sql="";
    	$sql="UPDATE $table SET value_numerico=:valor, descripcion=:descripcion, value_numericosistema=:value_numericosistema where cod_actividad=:cod_actividad and mes=:cod_mes";	    	

	    $stmt = $dbh->prepare($sql);
		$values = array( ':cod_actividad' => $codActividadX,
        ':cod_mes' => $mesX,
        ':valor' => $valor,
        ':descripcion' => $explicacionLogro,
        ':value_numericosistema'=> $ejeSistema
    	);

    	$exQuery=str_replace(array_keys($values), array_values($values), $sql);
		
		//echo $exQuery.";<br>";
		
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
