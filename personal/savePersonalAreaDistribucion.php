<?php
require_once '../conexion.php';

require_once '../functions.php';
require_once '../perspectivas/configModule.php';

$result=0;


$dbhU = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbhU->prepare($sqlX);
$stmtX->execute();
$stmtU=false;

//RECIBIMOS LAS VARIABLES
$cod_personal=$_POST['cod_personal'];
$cod_uo=$_POST['cod_uo'];
$cod_area=$_POST['cod_area'];
$porcentaje=$_POST['porcentaje'];
$cod_estadoreferencial=$_POST['cod_estadoreferencial'];
// echo "cod_personal: ".$cod_personal."<br>";
// echo "cod_uo: ".$cod_uo."<br>";
// echo "cod_area: ".$cod_area."<br>";
// echo "procentaje: ".$porcentaje."<br>";
// echo "estado: ".$cod_estadoreferencial."<br>";

$fecha_recepcion=date("Y-m-d H:i:s");
	//cuando devuelve AF
	// Prepare
if($cod_estadoreferencial==1){//insertar	
	$sql="INSERT INTO personal_area_distribucion(cod_personal,cod_uo,cod_area,porcentaje,cod_estadoreferencial) values(:cod_personal,:cod_uo,:cod_area,:porcentaje,:cod_estadoreferencial)";
	$stmtU = $dbhU->prepare($sql);
	// Bind
	$stmtU->bindParam(':cod_personal', $cod_personal);
	$stmtU->bindParam(':cod_uo', $cod_uo);
	$stmtU->bindParam(':cod_area', $cod_area);
	$stmtU->bindParam(':porcentaje', $porcentaje);
	$stmtU->bindParam(':cod_estadoreferencial', $cod_estadoreferencial);
}elseif($cod_estadoreferencial==2){//actualizar
	$sql="UPDATE personal_area_distribucion set cod_uo=:cod_uo,cod_area=:cod_area,porcentaje=:porcentaje where codigo=:cod_distribucion";
	$stmtU = $dbhU->prepare($sql);
	// Bind
	$stmtU->bindParam(':cod_distribucion', $cod_personal);
	$stmtU->bindParam(':cod_uo', $cod_uo);
	$stmtU->bindParam(':cod_area', $cod_area);
	$stmtU->bindParam(':porcentaje', $porcentaje);


}elseif ($cod_estadoreferencial==3) {//eliminar
	$sql="UPDATE personal_area_distribucion set cod_estadoreferencial=2 where codigo=:cod_distribucion";
	$stmtU = $dbhU->prepare($sql);
	// Bind	
	$stmtU->bindParam(':cod_distribucion', $cod_personal);	
}
if($stmtU->execute()){
      $result =1;
    }
echo $result;
$dbhU=null;

?>