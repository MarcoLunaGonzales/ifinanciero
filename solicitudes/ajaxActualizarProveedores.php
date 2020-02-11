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

/*{"IdCliente":"34655","NombreRazon":"Empresa Proveedora Dos","Paterno":null,"Materno":null,"IdTipoIdentificacion":"0","TipoIdentificacion":null,"TipoIdentificacionOtro":null,"Identificacion":"124578","IdLugarEmision":"0","LugarEmision":"La Paz","LugarEmisionOtro":null,"IdNacionalidad":"0","Nacionalidad":null,"FechaNacimiento":null,"IdGenero":"0","Genero":null,"IdEstadoCivil":"0","EstadoCivil":null,"IdPais":"26","Pais":"Bolivia","IdDepartamento":"480","Departamento":"La Paz","IdCiudad":"72","Ciudad":"El Alto","CiudadOtro":null,"Direccion":"Miraflores, Av. Saavedra","Correo":"mail@dosempresa.com","Telefono":"2457896","Movil":null,"Vigencia":"1"}*/



