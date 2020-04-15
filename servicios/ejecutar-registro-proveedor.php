<?php 
/*REGISTRO DE CLIENTES*/
//21-01-2020, 28-01-2020, 13-04-2020
//LLAVES DE ACCESO AL WS
$sIde = "ifinanciero";
$sKey = "ce94a8dabdf0b112eafa27a5aa475751";
$parametros=array();

/*PARAMETROS PARA VERIFICAR PERSONA O EMPRESA PROVEEDORA

 		$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
						  "accion"=>"VerificaIdentificacionPersonaEmpresa", 
						  "identificacion"=>1023053020, //NIT de la empresa o Identificacion de Persona
						  "tipoCliente"=>"E" // P= persona, E = empresa
						  );
*/
/*PARAMETROS PARA REGISTRAR UNA EMPRESA PROVEEDORA

 		$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
						  "accion"=>"RegistrarProveedor", //0 para nuevo registro
						  "tipoCliente"=>"E", // Tipo E=Empresa
						  "claseCliente"=>"N", // Clase N=Nacional, I=Internacional
						  "nombreRazon"=>"Empresa Proveedora Dos", //Nombre de la empresa
						  "identificacion"=>1028443027, //NIT de la empresa
						  "pais"=>26, //Valor numerico determinado por el ws-paises opcion pais, el valor = 26 para Bolivia
						  "depto"=>480, //Valor numerico determinado por el ws-paises opcion estados y id pais =26 para deptos de bolivia(Ej. 480=La Paz)
						  "ciudad"=>72, //Valor numerico determinado por el ws-paises opcion ciudad y idEstado=480 para La Paz (Ej. 72=ciudad El Alto)
						  "ciudadOtro"=>NULL, // campo VARCHAR, se emplea en el caso de seleccionar otro en el campo Ciudad 
						  "direccion"=>"Miraflores, Av. Saavedra", //Direccion del Cliente, campo VARCHAR 
						  "telefono"=>"2457896", //Telefono fijo del cliente, campo VARCHAR 
						  "correo"=>"mail@dosempresa.com", //correo de la empresa
						  "nombreContacto"=>"Javier", //Nombre del contacto que manejara la cuenta de la empresa
						  "apellidoContacto"=>"Prueba", //Apellido del contacto que manejara la cuenta de la empresa
						  "cargoContacto"=>"Gerente", //Cargo que ocupa el contacto dentro la empresa
						  "correoContacto"=>"mailotrocontacto@empresa.com", //correo campo varchar
						  
						  "optFactura"=>1 // 1=datos de factura, 0 = sin datos de factura
						  "facturaRazon"=>"Nombre para la Factura", // Razon Social de la factura Puede ser el mismo nombre empresa, NULL en caso de optFactura=0
						  "facturaNIT"=>1028443027 // NIT para factura el mismo del IDENTIFICACION, NULL en caso de optFactura=0
						  );
*/
/*PARAMETROS PARA REGISTRAR UNA PERSONA PROVEEDORA NACIONAL 
		$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
						  "accion"=>"RegistrarProveedor", 
						  "tipoCliente"=>"P", // Tipo P=Persona
						  "claseCliente"=>"N", // Clase N=Nacional, I=Internacional
						  "nombreRazon"=>"Marcelo", //Nombre de la persona o empresa
						  "paterno"=>"Selaes", //Apellido paterno del cliente persona
						  "materno"=>"", //Apellido materno del cliente persona
						  "tipoId"=>1581, //tipo de identificacion valor numerico determinado por el clasificador tipo de documento id padre 1580 (1581=CI, 1582=Pasaporte)
						  "tipoIdOtro"=>NULL, // Otro tipo de identificacion campo VARCHAR, se debe habilitar en el caso de seleccionar otro en el campo tipoId
						  "identificacion"=>10705473010, // Numero de identificacion, campo VARCHAR
						  "emision"=>480, //Lugar de emision en Bolivia, Valor numerico determinado por el ws-paises opcion estados y idPais 26=Bolivia(Ej. 480=La Paz)
						  "emisionOtro"=>NULL, //Otro lugar de emision campo VARCHAR, se emplea en el caso de seleccionar otro en el campo emision
						  "nacionalidad"=>26, //Pais de origen valor numerico, determinado por el listado de paises (web service paises) 26=Bolivia
						  "pais"=>26, //Valor numerico determinado por el ws-paises opcion pais, el valor = 26 para Bolivia
						  "depto"=>480, //Valor numerico determinado por el ws-paises opcion estados y id pais =26 para deptos de bolivia(Ej. 480=La Paz)
						  "ciudad"=>72, //Valor numerico determinado por el ws-paises opcion ciudad y idEstado=480 para La Paz (Ej. 72=ciudad El Alto)
						  "ciudadOtro"=>NULL, // campo VARCHAR, se emplea en el caso de seleccionar otro en el campo Ciudad 
						  "direccion"=>"Av. Capriles", //Direccion del Cliente, campo VARCHAR 
						  "telefono"=>"2445596", //Telefono fijo del cliente, campo VARCHAR 
						  "movil"=>NULL, //Telefono Movil o Celular, campo VARCHAR 
						  "correo"=>"mailpreuba@valido.com", //correo que posteriormente servira de nombre de usuario para el acceso a la cuenta, campo varchar
						  "optFactura"=>1 // 1=datos de factura, 0 = sin datos de factura
						  "facturaRazon"=>"Nombre para la Factura", // Razon Social de la factura, NULL en caso de optFactura=0
						  "facturaNIT"=>1234567 // NIT para factura, NULL en caso de optFactura=0
						  );
	*/	
/*PARAMETROS PARA REGISTRAR UNA PERSONA PROVEEDORA INTERNACIONAL
		$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
						  "accion"=>"RegistrarProveedor", 
						  "tipoCliente"=>"P", // Tipo P=Persona
						  "claseCliente"=>"N", // Clase N=Nacional, I=Internacional
						  "nombreRazon"=>"Marcelo", //Nombre de la persona o empresa
						  "paterno"=>"Selaes", //Apellido paterno del cliente persona
						  "materno"=>"", //Apellido materno del cliente persona
						  "tipoId"=>1581, //tipo de identificacion valor numerico determinado por el clasificador tipo de documento id padre 1580 (1581=CI, 1582=Pasaporte)
						  "tipoIdOtro"=>NULL, // Otro tipo de identificacion campo VARCHAR, se debe habilitar en el caso de seleccionar otro en el campo tipoId
						  "identificacion"=>324466, // Numero de identificacion, campo VARCHAR
						  "nacionalidad"=>26, //Pais de origen valor numerico, determinado por el listado de paises (web service paises) 26=Bolivia
						  "pais"=>26, //Valor numerico determinado por el ws-paises opcion pais, el valor = 26 para Bolivia
						  "depto"=>480, //Valor numerico determinado por el ws-paises opcion estados y id pais =26 para deptos de bolivia(Ej. 480=La Paz)
						  "ciudad"=>72, //Valor numerico determinado por el ws-paises opcion ciudad y idEstado=480 para La Paz (Ej. 72=ciudad El Alto)
						  "ciudadOtro"=>NULL, // campo VARCHAR, se emplea en el caso de seleccionar otro en el campo Ciudad 
						  "direccion"=>"Av. Capriles", //Direccion del Cliente, campo VARCHAR 
						  "telefono"=>"2445596", //Telefono fijo del cliente, campo VARCHAR 
						  "movil"=>NULL, //Telefono Movil o Celular, campo VARCHAR 
						  "correo"=>"mailpreuba@valido.com", //correo que posteriormente servira de nombre de usuario para el acceso a la cuenta, campo varchar
						  "optFactura"=>1 // 1=datos de factura, 0 = sin datos de factura
						  "facturaRazon"=>"Nombre para la Factura", // Razon Social de la factura, NULL en caso de optFactura=0
						  "facturaNIT"=>1234567 // NIT para factura, NULL en caso de optFactura=0
						  );
*/

/*PARAMETROS PARA ASIGNAR ATRIBUTO PROVEEDOR

 		$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
						  "accion"=>"AsignarAtributoProveedor", 
						  "IdCliente"=>36457, 
						  "IdUsuario"=>1 // valor del id del usuario retornado en el login						  
						  );
*/

		$parametros=json_encode($parametros);
		// abrimos la sesión cURL
		$ch = curl_init();
		// definimos la URL a la que hacemos la petición
		//curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/wsibno/registro/ws-registro-proveedor.php"); // OFFICIAL
		curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/wsibnob/registro/ws-registro-proveedor.php"); // PRUEBA
		// indicamos el tipo de petición: POST
		curl_setopt($ch, CURLOPT_POST, TRUE);
		// definimos cada uno de los parámetros
		curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
		// recibimos la respuesta y la guardamos en una variable
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$remote_server_output = curl_exec ($ch);
		// cerramos la sesión cURL
		curl_close ($ch);
		
		// imprimir en formato JSON
		header('Content-type: application/json'); 	
		print_r($remote_server_output); 			

?>