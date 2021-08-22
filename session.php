<?php
require_once 'functions.php';
require_once 'functionsGeneral.php';
require_once 'conexion.php';
// tiempo de la sesion por 8 horas

$dbh = new Conexion();
session_start();

date_default_timezone_set('America/La_Paz');

$user=$_POST["user"];
$password=$_POST["password"];

//OBTENEMOS EL VALOR DE LA CONFIGURACION 1 -> LOGIN PROPIO DE MONITOREO    2-> LOGIN POR SERVICIO WEB
$tipoLogin=obtieneValorConfig(-10);
$banderaLogin=0;
if($tipoLogin==2){
	$sIdentificador = "ifinanciero";
	$sKey="ce94a8dabdf0b112eafa27a5aa475751";
	$nombreuser=$user;
	$claveuser=$password;
	$claveuser=md5($password);
	$datos=array("sIdentificador"=>$sIdentificador, "sKey"=>$sKey, 
				 "operacion"=>"Login", "nombreUser"=>$nombreuser, "claveUser"=>$claveuser);
	$datos=json_encode($datos);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/wsibno/verifica/ws-user-personal.php");
	curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $datos);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$remote_server_output = curl_exec ($ch);
	curl_close ($ch);
	$obj=json_decode($remote_server_output);

	//header('Content-type: application/json'); 	
	//print_r($remote_server_output); 

	$banderaLogin=$obj->estado;
	if($banderaLogin=="true"){
		$banderaLogin=1;
	}
	$idUsuarioSW=$obj->usuario->IdUsuario;

	if($idUsuarioSW==49299){
		$idUsuarioSW=47770;
	}
	//echo $banderaLogin;
}

if($banderaLogin==1 || $tipoLogin==1){
	$sql="";
	if($tipoLogin==1){
		$sql="SELECT p.codigo, CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre)as nombre, p.cod_area, p.cod_unidadorganizacional, pd.perfil 
			from personal p, personal_datosadicionales pd 
			where p.codigo=pd.cod_personal and pd.usuario='$user' and pd.contrasena='$password'";		
	}else{
		$sql="SELECT p.codigo, CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre)as nombre, p.cod_area, p.cod_unidadorganizacional, 1 as perfil
			from personal p 
			where p.codigo='$idUsuarioSW' ";		
	}


	/*if($idUsuarioSW==47770){
		$idUsuarioSW=49299;
	}*/
	//echo $idUsuarioSW;

	$stmt = $dbh->prepare($sql);
	$stmt->execute();
	$stmt->bindColumn('codigo', $codigo);
	$stmt->bindColumn('nombre', $nombre);
	$stmt->bindColumn('cod_area', $codArea);
	$stmt->bindColumn('cod_unidadorganizacional', $codUnidad);
	$stmt->bindColumn('perfil', $perfil);


	if($idUsuarioSW==47770){
		$idUsuarioSW=49299;
		$codUnidad=3000;
	}
	while ($rowDetalle = $stmt->fetch(PDO::FETCH_BOUND)) {
		//echo "ENTRO A DETALLE";
		
		//echo "usuario: ".$idUsuarioSW." unidad: ".$codUnidad;
		if($codUnidad>0){
			$nombreUnidad=abrevUnidad($codUnidad);
		}
		if($codArea>0){
			$nombreArea=abrevArea($codArea);
		}

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

		if($idUsuarioSW==49299){
			$codGestionActiva=1205;
			$nombreGestion="2020";
			$codMesActiva=12;
			$codUnidad=3000;
			$nombreUnidad="SIS";
		}

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

		if( $codigo==90 || $codigo==89 || $codigo==227 || $codigo==195 || $codigo==72 || $codigo==41 || $codigo==50 || $codigo==58 || $codigo==42 ){
			$_SESSION['globalAdmin']=1;
			if($codigo==90 || $codigo==89 || $codigo==227 || $codigo==195 || $codigo==72){
				$_SESSION['globalNombreUnidad']="RLP";	
				$_SESSION['globalUnidad']="5";				
			}
		}else{
			$_SESSION['globalAdmin']=0;	
		}
		
		$_SESSION['globalServerArchivos']="http://ibnored.ibnorca.org/itranet/documentos/";


		$sIdentificador = "ifinanciero";
		$sKey="ce94a8dabdf0b112eafa27a5aa475751";
		$datos=array("sIdentificador"=>$sIdentificador, "sKey"=>$sKey, "operacion"=>"Menu", "IdUsuario"=>$idUsuarioSW);
		$datos=json_encode($datos);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/wsibno/verifica/ws-user-personal.php");
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $datos);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$remote_server_output = curl_exec ($ch);
		curl_close ($ch);
		//header('Content-type: application/json');   
		//print_r($remote_server_output);       
		$obj=json_decode($remote_server_output);
		$_SESSION['globalMenuJson']=$obj;
	}
}

 header("location:index.php");

?>