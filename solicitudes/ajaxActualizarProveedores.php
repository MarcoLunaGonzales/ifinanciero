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

$lista=obtenerListaProveedoresDelServicio();//empresa
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
$listaPersona=obtenerListaProveedoresTipoPersona();//persona
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
$listaDocente=obtenerListaPersonalDocenteServicio();//docente
foreach ($listaDocente->lstPersona as $listas) {
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

	$nombre=$NombreRazon." ".$paterno." ".$materno." (Docente)";

	$sql="INSERT INTO af_proveedores (codigo,cod_empresa,nombre,nit,created_by,modified_by,direccion,telefono,email,personacontacto,email_personacontacto,cod_estado)
        VALUES ('$codigo','$cod_empresa','$nombre','$nit','$idUsuario','$idUsuario','$direccion','$telefono','$email','$personacontacto','$email_personacontacto','$cod_estado')";
     $stmt = $dbh->prepare($sql);
     $stmt->execute();  
     $contador++;
}
$listaAuditor=obtenerListaPersonalAuditorServicio();//auditor
foreach ($listaAuditor->lstPersona as $listas) {
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

	$nombre=$NombreRazon." ".$paterno." ".$materno." (Auditor)";

	$sql="INSERT INTO af_proveedores (codigo,cod_empresa,nombre,nit,created_by,modified_by,direccion,telefono,email,personacontacto,email_personacontacto,cod_estado)
        VALUES ('$codigo','$cod_empresa','$nombre','$nit','$idUsuario','$idUsuario','$direccion','$telefono','$email','$personacontacto','$email_personacontacto','$cod_estado')";
     $stmt = $dbh->prepare($sql);
     $stmt->execute();  
     $contador++;
}
$listaConsultor=obtenerListaPersonalConsultorServicio();//consultor
foreach ($listaConsultor->lstPersona as $listas) {
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

	$nombre=$NombreRazon." ".$paterno." ".$materno." (Consultor)";

	$sql="INSERT INTO af_proveedores (codigo,cod_empresa,nombre,nit,created_by,modified_by,direccion,telefono,email,personacontacto,email_personacontacto,cod_estado)
        VALUES ('$codigo','$cod_empresa','$nombre','$nit','$idUsuario','$idUsuario','$direccion','$telefono','$email','$personacontacto','$email_personacontacto','$cod_estado')";
     $stmt = $dbh->prepare($sql);
     $stmt->execute();  
     $contador++;
}

//personal interno
$direccion=obtenerValorConfiguracion(42);//direccion des servicio web
$sIde = "monitoreo"; 
$sKey = "837b8d9aa8bb73d773f5ef3d160c9b17";
$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "accion"=>"ListarPersonal");
$url=$direccion."rrhh/ws-personal-listas.php";
$json=callService($parametros, $url);
$obj=json_decode($json);//decodificando json
$detalle=$obj->lstPersonal;
foreach ($detalle as $objDet){
	if($contador==0){
		$sql="DELETE FROM af_proveedores";
        $stmt = $dbh->prepare($sql);
        $stmt->execute(); 
	}
	$codigo = $objDet->IdCliente;
	$primer_nombre = $objDet->NombreRazon;
	$paterno = $objDet->Paterno;
	$materno = $objDet->Materno;
	$cod_tipoIdentificacion = $objDet->IdTipoIdentificacion;
	$TipoIdentificacionOtro = $objDet->TipoIdentificacionOtro;
	$identificacion = $objDet->Identificacion;
	$cod_lugar_emision = $objDet->IdLugarEmision;
	$LugarEmisionOtro = $objDet->LugarEmisionOtro;
	$cod_nacionalidad = $objDet->IdNacionalidad;
	$fecha_nacimiento = $objDet->FechaNacimiento;
	$cod_genero = $objDet->IdGenero;
	$cod_estadoCivil = $objDet->IdEstadoCivil;
	$cod_pais = $objDet->IdPais;
	$cod_departamento = $objDet->IdDepartamento;
	$cod_ciudad = $objDet->IdCiudad;
	$CiudadOtro = $objDet->CiudadOtro;
	$direccion = $objDet->Direccion;
	$email = $objDet->Correo;
	$telefono = $objDet->Telefono;
	$celular = $objDet->Movil;

	$nombre=$primer_nombre." ".$paterno." ".$materno." (Personal Interno)";
    if(existeProveedor($codigo)==0){
    	$sql="INSERT INTO af_proveedores (codigo,cod_empresa,nombre,nit,created_by,modified_by,direccion,telefono,email,personacontacto,email_personacontacto,cod_estado)
        VALUES ('$codigo','1','$nombre','$identificacion','$idUsuario','$idUsuario','$direccion','$telefono','$email','0','0','1')";
       $stmt = $dbh->prepare($sql);
       $stmt->execute();
    }
     $contador++;
}


