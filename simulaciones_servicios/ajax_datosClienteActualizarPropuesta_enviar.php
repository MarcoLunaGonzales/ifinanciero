<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();
$direccion=obtenerValorConfiguracion(42);//direccion des servicio web
$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();
 
 $globalPersonal=$_SESSION["globalUser"];

//LLAVES DE ACCESO AL WS
$sIde = "ifinanciero";
$sKey = "ce94a8dabdf0b112eafa27a5aa475751";

  // mae_nombre;
  // mae_cargo;
  // mae_telefono;
  // mae_email;
  // contacto_nombre;
  // contacto_cargo;
  // contacto_telefono;
  // contacto_email;

$tipo=$_GET['tipo'];//tipo actualizacion

switch ($tipo) {
	case 0://todo
		
		$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
		  "accion"=>"EditarClienteXLS", 
		  "IdCliente" =>$_GET['codigo_cliente'], // Id de registro cliente
		  "nit"=>$_GET['nit_cliente'], //Numero de NIT
		  "razonSocial"=>$_GET['razon_social_cliente'], 
		  "pais"=>$_GET['pais_cliente'], //Enviar "Bolivia" o el nombre de otro pais
		  "depto"=>"'".$_GET['departamento_cliente']."'", //Nombre del departamento o estado de un pais
		  "ciudad"=>$_GET['ciudad_cliente'], //Nombre de un ciudad dentro de un departamento o estado
		  "direccion"=>$_GET['direccion_cliente'], //Direccion del Cliente
		  "telefono"=>$_GET['telefono_cliente'], //Telefono cliente
		  "correo"=>$_GET['email_cliente'], //correo general de la empresa
		  "web"=>$_GET['web_cliente'],
		  "IdUsuario"=>$globalPersonal //Id del usuario en el sistema que modifica el registro
		  );
		$parametros=json_encode($parametros);
		// abrimos la sesión cURL
		$ch = curl_init();
		//curl_setopt($ch, CURLOPT_URL,$direccion."registro/ws-registro-proveedor.php"); // OFFICIAL
		curl_setopt($ch, CURLOPT_URL,$direccion."registro/ws-fin-cliente.php"); // PRUEBA
		// indicamos el tipo de petición: POST
		curl_setopt($ch, CURLOPT_POST, TRUE);
		// definimos cada uno de los parámetros
		curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
		// recibimos la respuesta y la guardamos en una variable
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$remote_server_output = curl_exec ($ch);
		curl_close ($ch);				
		$respuesta=json_decode($remote_server_output);
		// imprimir en formato JSON
		//header('Content-type: application/json'); 	
		// print_r($respuesta); 	
		$estado = $respuesta->estado;
		$mensaje_error=trim($respuesta->mensaje);
		$alerta="";
		if($estado==1){
			if(isset($respuesta->alerta)){
				$alerta="Sin Embargo, ".trim($respuesta->alerta);
			}
			//**ingresamos contacto cliente
			$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
        "accion"=>"RegistrarContactoEmpresaXLS", //Nuevo contacto de empresa
        "IdCliente"=>$_GET['codigo_cliente'], //ID del registrado de la tabla cliente, recuperado de los datos de cliente
        "IdUsuarioReg"=>$globalPersonal, //ID_USUARIO_REG,//ID del usuario que crea el registro; 0 (cero) en caso de no tenerlo
        "NombreContacto"=>$_GET['contacto_nombre'], //Nombre del contacto de la empresa
        "CargoContacto"=>$_GET['contacto_cargo'], //Cargo que ocupa el contacto dentro la empresa
        "Telefono"=>$_GET['contacto_telefono'], //Telefono o celular de contacto
        "CorreoContacto"=>$_GET['contacto_email'], //correo se usa como nombre de usuario para acceso a la cuenta (Usuario Visor)
        "IdTipoContacto"=>0, //Id Clasificador IdPadre=2817, tipos de contacto; 0 en caso de no requerir el dato
        "IdArea"=>$_GET['cod_area_contacto'] //id de area de servicio asignado al contacto (obtener del clasificador area idPadre=6)
        );  
      $datos=json_encode($parametros);
			// abrimos la sesión cURL
			$ch = curl_init();
			// definimos la URL a la que hacemos la petición
			//curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/wsibno/registro/ws-fin-cliente-contacto.php"); // on line 
			curl_setopt($ch, CURLOPT_URL,$direccion."registro/ws-fin-cliente-contacto.php"); // 
			//curl_setopt($ch, CURLOPT_URL,"http://localhost/wsibno/registro/ws-fin-cliente-contacto.php"); // local
			// indicamos el tipo de petición: POST
			curl_setopt($ch, CURLOPT_POST, TRUE);
			// definimos cada uno de los parámetros
			curl_setopt($ch, CURLOPT_POSTFIELDS, $datos);
			// recibimos la respuesta y la guardamos en una variable
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$remote_server_output = curl_exec ($ch);
			// cerramos la sesión cURL
			curl_close ($ch);
			//$respuesta=json_decode($remote_server_output);
			echo $mensaje_error."####".$alerta;
		}else{
			echo $mensaje_error."####".$alerta;
		}
		


	break;
	
	case 1: //actualizar datos cliente
		$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
		  "accion"=>"EditarClienteXLS", 
		  "IdCliente" =>$_GET['codigo_cliente'], // Id de registro cliente
		  "nit"=>$_GET['nit_cliente'], //Numero de NIT
		  "razonSocial"=>$_GET['razon_social_cliente'], 
		  "pais"=>$_GET['pais_cliente'], //Enviar "Bolivia" o el nombre de otro pais
		  "depto"=>"'".$_GET['departamento_cliente']."'", //Nombre del departamento o estado de un pais
		  "ciudad"=>$_GET['ciudad_cliente'], //Nombre de un ciudad dentro de un departamento o estado
		  "direccion"=>$_GET['direccion_cliente'], //Direccion del Cliente
		  "telefono"=>$_GET['telefono_cliente'], //Telefono cliente
		  "correo"=>$_GET['email_cliente'], //correo general de la empresa
		  "web"=>$_GET['web_cliente'],
		  "IdUsuario"=>$globalPersonal //Id del usuario en el sistema que modifica el registro
		  );
		$parametros=json_encode($parametros);
		// abrimos la sesión cURL
		$ch = curl_init();
		//curl_setopt($ch, CURLOPT_URL,$direccion."registro/ws-registro-proveedor.php"); // OFFICIAL
		curl_setopt($ch, CURLOPT_URL,$direccion."registro/ws-fin-cliente.php"); // PRUEBA
		// indicamos el tipo de petición: POST
		curl_setopt($ch, CURLOPT_POST, TRUE);
		// definimos cada uno de los parámetros
		curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
		// recibimos la respuesta y la guardamos en una variable
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$remote_server_output = curl_exec ($ch);
		curl_close ($ch);				
		$respuesta=json_decode($remote_server_output);
		// imprimir en formato JSON
		//header('Content-type: application/json'); 	
		// print_r($respuesta); 	
		$estado = $respuesta->estado;
		$mensaje_error=trim($respuesta->mensaje);
		$alerta="";
		if($estado==1){
			if(isset($respuesta->alerta)){
				$alerta="Sin Embargo, ".trim($respuesta->alerta);
			}
			echo $mensaje_error."####".$alerta;
		}else{
			echo $mensaje_error."####".$alerta;
		}	
	break;
	case 3: //actualizar datos contacto
		// echo $_GET['contacto_email'];
			//**ingresamos contacto cliente
			$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
        "accion"=>"RegistrarContactoEmpresaXLS", //Nuevo contacto de empresa
        "IdCliente"=>$_GET['codigo_cliente'], //ID del registrado de la tabla cliente, recuperado de los datos de cliente
        "IdUsuarioReg"=>$globalPersonal, //ID_USUARIO_REG,//ID del usuario que crea el registro; 0 (cero) en caso de no tenerlo
        "NombreContacto"=>$_GET['contacto_nombre'], //Nombre del contacto de la empresa
        "CargoContacto"=>$_GET['contacto_cargo'], //Cargo que ocupa el contacto dentro la empresa
        "Telefono"=>$_GET['contacto_telefono'], //Telefono o celular de contacto
        "CorreoContacto"=>$_GET['contacto_email'], //correo se usa como nombre de usuario para acceso a la cuenta (Usuario Visor)
        "IdTipoContacto"=>0, //Id Clasificador IdPadre=2817, tipos de contacto; 0 en caso de no requerir el dato
        "IdArea"=>$_GET['cod_area_contacto'] //id de area de servicio asignado al contacto (obtener del clasificador area idPadre=6)
        );  
      $datos=json_encode($parametros);
			// abrimos la sesión cURL
			$ch = curl_init();
			// definimos la URL a la que hacemos la petición
			//curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/wsibno/registro/ws-fin-cliente-contacto.php"); // on line 
			curl_setopt($ch, CURLOPT_URL,$direccion."registro/ws-fin-cliente-contacto.php"); // 
			//curl_setopt($ch, CURLOPT_URL,"http://localhost/wsibno/registro/ws-fin-cliente-contacto.php"); // local
			// indicamos el tipo de petición: POST
			curl_setopt($ch, CURLOPT_POST, TRUE);
			// definimos cada uno de los parámetros
			curl_setopt($ch, CURLOPT_POSTFIELDS, $datos);
			// recibimos la respuesta y la guardamos en una variable
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$remote_server_output = curl_exec ($ch);
			// cerramos la sesión cURL
			curl_close ($ch);
			$respuesta=json_decode($remote_server_output);

			// imprimir en formato JSON
		//header('Content-type: application/json'); 	
		// print_r($respuesta); 	
		$estado = $respuesta->estado;
		$mensaje_error=trim($respuesta->mensaje);
		$alerta="";
		if($estado==1){
			if(isset($respuesta->alerta)){
				$alerta="Sin Embargo, ".trim($respuesta->alerta);
			}
			echo $mensaje_error."####".$alerta;
		}else{
			echo $mensaje_error."####".$alerta;
		}	
	break;
}


		
					

?>