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
 

//LLAVES DE ACCESO AL WS
$sIde = "ifinanciero";
$sKey = "ce94a8dabdf0b112eafa27a5aa475751";


$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
						  "accion"=>"RegistrarContactoEmpresa", //Nuevo contacto de empresa
						  "IdCliente"=>$_GET['cod_cliente'], //ID del registrado de la tabla cliente, recuperado de los datos de cliente
						  "IdUsuarioReg"=>$_GET['cod_personal'], //ID del usuario que crea el registro; 0 (cero) en caso de no tenerlo
						  "NombreContacto"=>$_GET['nombre_contacto'], //Nombre del contacto de la empresa
						  "Paterno"=>$_GET['paterno_contacto'], //Apellido del contacto de la empresa
						  // "Materno"=>$_GET['materno_contacto'], //Apellido del contacto de la empresa
						  "CargoContacto"=>$_GET['cargo_contacto'], //Cargo que ocupa el contacto dentro la empresa
						  "Telefono"=>$_GET['telefono_contacto'], //Telefono o celular de contacto
						  "CorreoContacto"=>$_GET['correo_contacto'], //correo se usa como nombre de usuario para acceso a la cuenta (Usuario Visor)
						  "IdTipoContacto"=>2817, //Id Clasificador IdPadre=2817, tipos de contacto; 0 en caso de no requerir el dato
						  "Identificacion"=>$_GET['identificacion_contacto'], // numero de carnet o identificacion
						  "IdentificacionExt"=>$_GET['departamento_contacto'] //Lugar de emision en Bolivia, Valor numerico determinado por el ws-paises opcion estados y idPais 26=Bolivia(Ej. 480=La Paz)
						  );		  

		$parametros=json_encode($parametros);
		// abrimos la sesión cURL
		$ch = curl_init();
		//curl_setopt($ch, CURLOPT_URL,$direccion."registro/ws-registro-proveedor.php"); // OFFICIAL
		curl_setopt($ch, CURLOPT_URL,$direccion."registro/ws-cliente-usuario-v2.php"); // PRUEBA
		// indicamos el tipo de petición: POST
		curl_setopt($ch, CURLOPT_POST, TRUE);
		// definimos cada uno de los parámetros
		curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
		// recibimos la respuesta y la guardamos en una variable
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$remote_server_output = curl_exec ($ch);
		curl_close ($ch);		
		// imprimir en formato JSON
		// header('Content-type: application/json'); 	
		// print_r($remote_server_output); 			
		$respuesta=json_decode($remote_server_output);
		// imprimir en formato JSON
		// header('Content-type: application/json'); 	
		// print_r($respuesta); 	
		$mensaje_error=$respuesta->mensaje;
		if($respuesta->estado==true){
				echo "1";
		}else{
          echo $mensaje_error;
		}
					

?>