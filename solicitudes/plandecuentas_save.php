<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

session_start();

//$cuentasX=$_POST["cuentas"];
// $cuentasX=json_decode($_POST["cuentas2"]);

$cuentasX=json_decode($_POST["cuentas2"]);
// $codPartida=$_POST["cod_partida"];
$stmtPasivo = $dbh->prepare("SELECT cod_cuenta,cod_cuentapasivo from solicitud_recursoscuentas where cod_cuentapasivo!=null or cod_cuentapasivo!=''");
// Ejecutamos
$stmtPasivo->execute();
$index=0;
while ($rowPasivo = $stmtPasivo->fetch(PDO::FETCH_ASSOC)) {
	$cuentaCodigo[$index]=$rowPasivo['cod_cuenta'];
	$cuentaCodigoPasivo[$index]=$rowPasivo['cod_cuentapasivo'];
	$index++;
}

//Cuentas que tienen configurada division pago
$stmtPasivo = $dbh->prepare("SELECT cod_cuenta from solicitud_recursoscuentas where division_porcentaje=1");
// Ejecutamos
$stmtPasivo->execute();
$indexDiv=0;
while ($rowPasivo = $stmtPasivo->fetch(PDO::FETCH_ASSOC)) {
	$cuentaCodigoDiv[$indexDiv]=$rowPasivo['cod_cuenta'];
	$indexDiv++;
}


$stmtDel = $dbh->prepare("DELETE FROM solicitud_recursoscuentas ");
$stmtDel->execute();
$flagSuccessDetail=true;


for ($i=0;$i<count($cuentasX);$i++){ 	    
	 echo $cuentasX[$i]->codigo."<br>";
	 $tienePasivo=0;$cuentaPasivo=0;
	 for ($j=0; $j < $index; $j++) { 
	 	if(buscarCuentaAnterior($cuentasX[$i]->numero)==$cuentaCodigo[$j]){
	 		$tienePasivo=1;
	 		$cuentaPasivo=$cuentaCodigoPasivo[$j];
	 		break;
	 	}
	 }

	if($tienePasivo!=0){
      $stmt = $dbh->prepare("INSERT INTO solicitud_recursoscuentas(cod_cuenta,cod_cuentapasivo) VALUES (:cod_cuenta,:cod_cuentapasivo)");
	  $stmt->bindParam(':cod_cuenta', buscarCuentaAnterior($cuentasX[$i]->numero));
	  $stmt->bindParam(':cod_cuentapasivo', $cuentaPasivo);
	}else{
	  $stmt = $dbh->prepare("INSERT INTO solicitud_recursoscuentas(cod_cuenta) VALUES (:cod_cuenta)");
	  $stmt->bindParam(':cod_cuenta', buscarCuentaAnterior($cuentasX[$i]->numero));	
	} 
	
	$flagSuccess2=$stmt->execute();
	if($flagSuccess2==false){
		$flagSuccessDetail=false;
	}
}
for ($i=0; $i < count($cuentaCodigoDiv) ; $i++) { 
	$codigoCuenta=$cuentaCodigoDiv[$i];
	$stmtActDiv = $dbh->prepare("UPDATE solicitud_recursoscuentas SET division_porcentaje=1 where cod_cuenta=$codigoCuenta");
	$stmtActDiv->execute();
}

if($flagSuccessDetail==true){
	showAlertSuccessError(true,"../".$urlListCC2);	
}else{
	showAlertSuccessError(false,"../".$urlListCC2);
}
?>
