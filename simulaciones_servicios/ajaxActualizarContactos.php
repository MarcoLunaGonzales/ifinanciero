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
set_time_limit(1000);

//lista de contactos
$lista=obtenerListaClientesWS();
$contador_contactos=0;
$contador_clientes=0;
$idUsuario=$_SESSION['globalUser'];
foreach ($lista->lista as $listaCliente) {
	if($contador_clientes==0){
		$sql="DELETE FROM clientes";
        $stmt = $dbh->prepare($sql);
        $stmt->execute(); 
	}
	$codigoX=$listaCliente->IdCliente;
	$nombreX=strtoupper($listaCliente->NombreRazon);
	if(isset($listaCliente->IdCiudad))
		$idCiudad=strtoupper($listaCliente->IdCiudad);		
	else $idCiudad=0;
	if(isset($listaCliente->Vigencia))
		$estadoX=$listaCliente->Vigencia;
	else $estadoX=0;
	if(isset($listaCliente->Identificacion))
		$identificacionX=$listaCliente->Identificacion;
	else $identificacionX=0;
	if(isset($listaCliente->DescuentoValor))
		$descuentoX=$listaCliente->DescuentoValor;
	else $descuentoX=0;
	//sacamos la unidad para insertar
	$stmt = $dbh->prepare("SELECT codigo, nombre, cod_unidad FROM ciudades where codigo=:codigo");
	$stmt->bindParam(':codigo',$idCiudad);
	$stmt->execute();
	$codigoUnidadX=0;
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$codigoUnidadX=$row['cod_unidad'];
	}
	// echo "codigo:".$codigoX." - ";
	// echo "nombre:".$nombreX." - ";
	// echo "estado:".$estadoX." - ";
	// echo "identificacion:".$identificacionX."<br>";

	$stmt = $dbh->prepare("INSERT INTO clientes (codigo, nombre,cod_unidad,identificacion,descuento,cod_estadoreferencial) VALUES (:codigo, :nombre, :cod_unidad, :identificacion, :descuento, :cod_estado)");
	$stmt->bindParam(':codigo', $codigoX);
	$stmt->bindParam(':nombre', $nombreX);
	$stmt->bindParam(':cod_unidad', $codigoUnidadX);
	$stmt->bindParam(':identificacion', $identificacionX);
	$stmt->bindParam(':descuento', $descuentoX);
	$stmt->bindParam(':cod_estado', $estadoX);
	$flagSuccess=$stmt->execute();
	//contactos clientes
	// $lista=obtenerListaContactosEmpresaDelServicio($codigoX);		
	// foreach ($lista->contactos as $listas) {		
	// 	if($contador_contactos==0){
	// 		$sql="DELETE FROM clientes_contactos";
	//         $stmt = $dbh->prepare($sql);
	//         $stmt->execute(); 
	// 	}
	// 	$vigencia=$listas->Vigencia;
	// 	if($vigencia==1){
	// 		$codigo=$listas->IdContacto;	
	// 		$cod_cliente=$listas->IdCliente;
	// 		if(isset($listas->NombreContacto))$nombre=$listas->NombreContacto;
	// 		else $nombre='';
	// 		if(isset($listas->PaternoContacto))$paterno=$listas->PaternoContacto;
	// 		else $paterno='';
	// 		if(isset($listas->MaternoContacto))$materno=$listas->MaternoContacto;
	// 		else $materno='';
	// 		if(isset($listas->CargoContacto))$cargo=$listas->CargoContacto;
	// 		else $cargo='';
	// 		if(isset($listas->FonoContacto))$telefono=$listas->FonoContacto;
	// 		else $telefono='';
	// 		if(isset($listas->Identificacion))$identificacion=$listas->Identificacion;
	// 		else $identificacion='';
	// 		if(isset($listas->Correo))$correo=$listas->Correo;
	// 		else $correo='';

	// 		$sql="INSERT INTO clientes_contactos(codigo,cod_cliente,nombre,paterno,materno,cargo,telefono,identificacion,correo,cod_estadoreferencial)
	// 	        VALUES ('$codigo','$cod_cliente','$nombre','$paterno','$materno','$cargo','$telefono','$identificacion','$correo','$vigencia')";
	// 	     $stmt = $dbh->prepare($sql);
	// 	     $stmt->execute();  
	// 	     $contador_contactos++;
	// 	}
	// }
	$listaPersona=obtenerListaContactosClientesDelServicio($codigoX);
	foreach ($listaPersona->contactos as $listas) {		
		if($contador_contactos==0){
			$sql="DELETE FROM clientes_contactos";
	        $stmt = $dbh->prepare($sql);
	        $stmt->execute(); 
		}
		$vigencia=$listas->Vigencia;	
		if($vigencia==1) ){						
			$cod_cliente=$codigoX;
			if(isset($listas->IdContacto))$codigo=$listas->IdContacto;
			else $codigo='';
			if(isset($listas->NombreContacto))$nombre=$listas->NombreContacto;
			else $nombre='';
			if(isset($listas->PaternoContacto))$paterno=$listas->PaternoContacto;
			else $paterno='';
			if(isset($listas->MaternoContacto))$materno=$listas->MaternoContacto;
			else $materno='';
			if(isset($listas->CargoContacto))$cargo=$listas->CargoContacto;
			else $cargo='';
			if(isset($listas->FonoContacto))$telefono=$listas->FonoContacto;
			else $telefono='';
			if(isset($listas->Identificacion))$identificacion=$listas->Identificacion;
			else $identificacion='';
			if(isset($listas->Correo))$correo=$listas->Correo;
			else $correo='';			

			// echo "codigo:".$codigo." - ";
			// echo "nombre:".$nombre." - ";
			// echo "correo:".$correo." - ";
			// echo "identificacion:".$identificacion." <br><br> ";			
			

			$sql="INSERT INTO clientes_contactos(codigo,cod_cliente,nombre,paterno,materno,cargo,telefono,identificacion,correo,cod_estadoreferencial)
		        VALUES ('$codigo','$cod_cliente','$nombre','$paterno','$materno','$cargo','$telefono','$identificacion','$correo','$vigencia')";
		     $stmt = $dbh->prepare($sql);
		     $stmt->execute();  
		     $contador_contactos++;
		}
	}
	$contador_clientes++;
}
?>
<?php
// session_start();
// require_once '../conexion.php';
// require_once '../functionsGeneral.php';
// require_once '../functions.php';
// require_once '../styles.php';

// $dbh = new Conexion();

// $direccion=obtenerValorConfiguracion(42);//direccion des servicio web
//   $sIde = "ifinanciero";
//   $sKey = "ce94a8dabdf0b112eafa27a5aa475751"; 
//   /*Datos de Clientes Empresa*/
//   //$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "accion"=>"DatosClienteEmpresa", "IdCliente"=>4084); //
//   $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "accion"=>"ContactosCliente", "IdCliente"=>4084); //  
//   //$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "lista"=>"Clientes");
//     $parametros=json_encode($parametros);
//     // abrimos la sesión cURL
//     $ch = curl_init();
//     // definimos la URL a la que hacemos la petición
//     curl_setopt($ch, CURLOPT_URL,$direccion."cliente/ws-cliente-listas.php");     
//     // indicamos el tipo de petición: POST
//     curl_setopt($ch, CURLOPT_POST, TRUE);
//     // definimos cada uno de los parámetros
//     curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
//     // recibimos la respuesta y la guardamos en una variable
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//     $remote_server_output = curl_exec ($ch);
//     // cerramos la sesión cURL
//     curl_close ($ch);  
//     // return json_decode($remote_server_output);       
//     header('Content-type: application/json'); 	
// 	print_r($remote_server_output); 	
?>

