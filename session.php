<?php
require_once 'functions.php';
require_once 'functionsGeneral.php';
require_once 'conexion.php';

$dbh = new Conexion();
session_start();
$user=$_POST["user"];
$password=$_POST["password"];

$sql="SELECT p.codigo, p.nombre, p.cod_area, p.cod_unidad, pd.perfil, pd.usuario_pon 
			from personal2 p, personal_datosadicionales pd 
			where p.codigo=pd.cod_personal and pd.usuario='$user' and pd.contrasena='$password'";
//echo $sql;
$stmt = $dbh->prepare($sql);
$stmt->execute();
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('cod_area', $codArea);
$stmt->bindColumn('cod_unidad', $codUnidad);
$stmt->bindColumn('perfil', $perfil);

while ($rowDetalle = $stmt->fetch(PDO::FETCH_BOUND)) {
	echo "ENTRO A DETALLE";
	$nombreUnidad=abrevUnidad($codUnidad);
	$nombreArea=abrevArea($codArea);

	//echo $codUnidad." ".$nombreUnidad;
	
	/*$codAreaTrabajo=buscarAreasAdicionales($codigo, 1);
	if($codAreaTrabajo!="" && $codAreaTrabajo!=0){
		$codAreaTrabajo=substr($codAreaTrabajo, 1); ;
	}
	$codUnidadTrabajo=buscarUnidadesAdicionales($codigo,1);
	if($codUnidadTrabajo!="" && $codUnidadTrabajo!=0){
		$codUnidadTrabajo=substr($codUnidadTrabajo, 1);
	}
	//echo $codAreaTrabajo;
	*/

	//echo $nombreArea;
	//SACAMOS LA GESTION ACTIVA
	$sqlGestion="SELECT cod_gestion FROM gestiones_datosadicionales where cod_estado=1";
	$stmtGestion = $dbh->prepare($sqlGestion);
	$stmtGestion->execute();
	while ($rowGestion = $stmtGestion->fetch(PDO::FETCH_ASSOC)) {
		$codGestionActiva=$rowGestion['cod_gestion'];

		$sql1="SELECT cod_mes from meses_trabajo where cod_gestion='$codGestionActiva' and cod_estadomesestrabajo=3";
        $stmt1 = $dbh->prepare($sql1);
        $stmt1->execute();
        while ($row1= $stmt1->fetch(PDO::FETCH_ASSOC)) {
          $codMesActiva=$row1['cod_mes'];
        }
	}
	$nombreGestion=nameGestion($codGestionActiva);

	$_SESSION['globalUser']=$codigo;
	$_SESSION['globalNameUser']=$nombre;
	$_SESSION['globalGestion']=$codGestionActiva;
	$_SESSION['globalMes']=$codMesActiva;
	$_SESSION['globalNombreGestion']=$nombreGestion;


	$_SESSION['globalUnidad']=$codUnidad;
	$_SESSION['globalNombreUnidad']=$nombreUnidad;

	$_SESSION['globalArea']=$codArea;
	$_SESSION['globalNombreArea']=$nombreArea;
	$_SESSION['logueado']=1;
	$_SESSION['globalPerfil']=$perfil;


	if($codigo==90 || $codigo==89 || $codigo==227){
		$_SESSION['globalAdmin']=1;			
	}else{
		$_SESSION['globalAdmin']=0;	
	}
	
	$_SESSION['globalServerArchivos']="http://ibnored.ibnorca.org/itranet/documentos/";


	/*$sIdentificador = "monitoreo";
	$sKey="837b8d9aa8bb73d773f5ef3d160c9b17";
	$datos=array("sIdentificador"=>$sIdentificador, "sKey"=>$sKey, "operacion"=>"Menu", "IdUsuario"=>183);
	$datos=json_encode($datos);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/wsibno19/verifica/ws-user-personal.php");
	curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $datos);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$remote_server_output = curl_exec ($ch);
	curl_close ($ch);
	//header('Content-type: application/json');   
	//print_r($remote_server_output);       
	$obj=json_decode($remote_server_output);
	$_SESSION['globalMenuJson']=$obj;*/

}

header("location:index.php");

?>