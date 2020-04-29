<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();
set_time_limit(300);

$lista=obtenerListaProveedoresDelServicio();
$contador=0;
$idUsuario=$_SESSION['globalUser'];
foreach ($lista->lstProveedor as $listas) {
	if($contador==0){
		$sql="DELETE FROM af_proveedores";
        $stmt = $dbh->prepare($sql);
        $stmt->execute(); 
	}
	$codigo=$listas->IdCliente;
	$cod_empresa=1;
	$nombre=$listas->NombreRazon;
    $nit=$listas->Identificacion;
	$direccion=$listas->Direccion;
	$telefono=$listas->Telefono;
	$email=$listas->Correo;
	$personacontacto="";
	$email_personacontacto=$listas->Correo;
	$cod_estado=$listas->Vigencia;

	$sql="INSERT INTO af_proveedores (codigo,cod_empresa,nombre,nit,created_by,modified_by,direccion,telefono,email,personacontacto,email_personacontacto,cod_estado)
        VALUES ('$codigo','$cod_empresa','$nombre','$nit','$idUsuario','$idUsuario','$direccion','$telefono','$email','$personacontacto','$email_personacontacto','$cod_estado')";
     $stmt = $dbh->prepare($sql);
     $stmt->execute();  
     $contador++;
}
$listaPersona=obtenerListaProveedoresTipoPersona();
$idUsuario=$_SESSION['globalUser'];
foreach ($listaPersona->lstPersona as $listas) {
	if($contador==0){
		$sql="DELETE FROM af_proveedores";
        $stmt = $dbh->prepare($sql);
        $stmt->execute(); 
	}
	$codigo=$listas->IdCliente;
	$cod_empresa=1;
	$NombreRazon=$listas->NombreRazon;
	$paterno=$listas->Paterno;
	$materno=$listas->Materno;
    $nit=$listas->Identificacion;
	$direccion=$listas->Direccion;
	$telefono=$listas->Telefono;
	$email=$listas->Correo;
	$personacontacto="";
	$email_personacontacto=$listas->Correo;
	$cod_estado=$listas->Vigencia;
 	$nombre=$NombreRazon." ".$paterno." ".$materno." (Persona)";
	$sql="INSERT INTO af_proveedores (codigo,cod_empresa,nombre,nit,created_by,modified_by,direccion,telefono,email,personacontacto,email_personacontacto,cod_estado)
        VALUES ('$codigo','$cod_empresa','$nombre','$nit','$idUsuario','$idUsuario','$direccion','$telefono','$email','$personacontacto','$email_personacontacto','$cod_estado')";
     $stmt = $dbh->prepare($sql);
     $stmt->execute();  
     $contador++;
}
// $listaDocente=obtenerListaPersonalDocenteServicio();
// foreach ($listaDocente->lstPersona as $listas) {
// 	if($contador==0){
// 		$sql="DELETE FROM af_proveedores";
//         $stmt = $dbh->prepare($sql);
//         $stmt->execute(); 
// 	}
// 	$codigo=$listas->IdCliente;
// 	$cod_empresa=1;
// 	$NombreRazon=$listas->NombreRazon;
// 	$paterno=$listas->Paterno;
// 	$materno=$listas->Materno;
//     $nit=$listas->Identificacion;
// 	$direccion=$listas->Direccion;
// 	$telefono=$listas->Telefono;
// 	$email=$listas->Correo;
// 	$personacontacto="";
// 	$email_personacontacto=$listas->Correo;
// 	$cod_estado=$listas->Vigencia;

// 	$nombre=$NombreRazon." ".$paterno." ".$materno." (Docente)";

// 	$sql="INSERT INTO af_proveedores (codigo,cod_empresa,nombre,nit,created_by,modified_by,direccion,telefono,email,personacontacto,email_personacontacto,cod_estado)
//         VALUES ('$codigo','$cod_empresa','$nombre','$nit','$idUsuario','$idUsuario','$direccion','$telefono','$email','$personacontacto','$email_personacontacto','$cod_estado')";
//      $stmt = $dbh->prepare($sql);
//      $stmt->execute();  
//      $contador++;
// }
// $listaAuditor=obtenerListaPersonalAuditorServicio();
// foreach ($listaAuditor->lstPersona as $listas) {
// 	if($contador==0){
// 		$sql="DELETE FROM af_proveedores";
//         $stmt = $dbh->prepare($sql);
//         $stmt->execute(); 
// 	}
// 	$codigo=$listas->IdCliente;
// 	$cod_empresa=1;
// 	$NombreRazon=$listas->NombreRazon;
// 	$paterno=$listas->Paterno;
// 	$materno=$listas->Materno;
//     $nit=$listas->Identificacion;
// 	$direccion=$listas->Direccion;
// 	$telefono=$listas->Telefono;
// 	$email=$listas->Correo;
// 	$personacontacto="";
// 	$email_personacontacto=$listas->Correo;
// 	$cod_estado=$listas->Vigencia;

// 	$nombre=$NombreRazon." ".$paterno." ".$materno." (Auditor)";

// 	$sql="INSERT INTO af_proveedores (codigo,cod_empresa,nombre,nit,created_by,modified_by,direccion,telefono,email,personacontacto,email_personacontacto,cod_estado)
//         VALUES ('$codigo','$cod_empresa','$nombre','$nit','$idUsuario','$idUsuario','$direccion','$telefono','$email','$personacontacto','$email_personacontacto','$cod_estado')";
//      $stmt = $dbh->prepare($sql);
//      $stmt->execute();  
//      $contador++;
// }
// $listaConsultor=obtenerListaPersonalConsultorServicio();
// foreach ($listaConsultor->lstPersona as $listas) {
// 	if($contador==0){
// 		$sql="DELETE FROM af_proveedores";
//         $stmt = $dbh->prepare($sql);
//         $stmt->execute(); 
// 	}
// 	$codigo=$listas->IdCliente;
// 	$cod_empresa=1;
// 	$NombreRazon=$listas->NombreRazon;
// 	$paterno=$listas->Paterno;
// 	$materno=$listas->Materno;
//     $nit=$listas->Identificacion;
// 	$direccion=$listas->Direccion;
// 	$telefono=$listas->Telefono;
// 	$email=$listas->Correo;
// 	$personacontacto="";
// 	$email_personacontacto=$listas->Correo;
// 	$cod_estado=$listas->Vigencia;

// 	$nombre=$NombreRazon." ".$paterno." ".$materno." (Consultor)";

// 	$sql="INSERT INTO af_proveedores (codigo,cod_empresa,nombre,nit,created_by,modified_by,direccion,telefono,email,personacontacto,email_personacontacto,cod_estado)
//         VALUES ('$codigo','$cod_empresa','$nombre','$nit','$idUsuario','$idUsuario','$direccion','$telefono','$email','$personacontacto','$email_personacontacto','$cod_estado')";
//      $stmt = $dbh->prepare($sql);
//      $stmt->execute();  
//      $contador++;
// }

