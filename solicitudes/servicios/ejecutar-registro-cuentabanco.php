<?php 
/*REGISTRO, EDICION Y MODIFICAR VIGENCIA DE CUENTAS BANCARIAS DE PROVEEDORES O CLIENTES*/
//Creado 06-05-2020
//LLAVES DE ACCESO AL WS
$sIde = "ifinanciero";
$sKey = "ce94a8dabdf0b112eafa27a5aa475751";
$parametros=array();

/*PARAMETROS PARA REGISTRAR UNA CUENTA BANCARIA

 		$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
						"accion"=>"RegistrarCuentaBanco",
						"IdCliente" => 34661, //Id del Proveedor o Cliente
						"IdBanco"=>2601, //valor numerico determinado por id del clasificador perteneciente a Entidades Bancarias (idpadre=319), poner 0 en caso de que sea otra entidad financiera
						"OtroBanco"=>NULL, // valor textual empleado en caso de no encontrar el Banco en el clasificador Entidades Bancarias. Caso contrario enviar NULL
						"IdTipoCuenta"=>2842, // valor numerico determinado por Id del clasificador de Tipo Cuenta Bancaria (idPadre=2841), poner o en caso de no encontrar el tipo requerido						"OtroTipoCuenta"=>NULL, 
						"OtroTipoCuenta"=>NULL, // valor textual empleado en caso de requerir otro tipo de cuenta que no este en el clasificador Tipo Cuenta. Caso contrario enviar NULL
						"IdTipoMoneda"=> 322, // valor numerico determinado por el Id del clasificador Monedas (idPadre=320)
						"NroCuenta"=>"123-456789-4", //valor textual para el envio del numero de cuenta
						"BeneficiarioNombre"=>"Nombre Beneficiario", 
						"BeneficiarioApellido"=>"Apellido Beneficiario", 
						"BeneficiarioIdentificacion"=>NULL, // valor textual en el caso de requerir el registro de la identificacion. Caso contrario enviar NULL
						"BancoIntermediario"=>NULL, // valor textual en caso de hacer uso del campo. Caso contrario NULL
						"IdUsuarioReg"=>90 // valor numerico obtenido del id del usuario autenticado. Usar 0 en caso de no tener el id
						);
*/
/*PARAMETROS PARA EDITAR UNA CUENTA BANCARIA

 		$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
						"accion"=>"EditarCuentaBanco",
						"IdCuentaBanco" => 1, //Id del registro de cuenta bancaria
						"IdCliente" => 34661, //Id del Proveedor o Cliente
						"IdBanco"=>2601, //valor numerico determinado por id del clasificador perteneciente a Entidades Bancarias (idpadre=319), poner 0 en caso de que sea otra entidad financiera
						"OtroBanco"=>NULL, // valor textual empleado en caso de no encontrar el Banco en el clasificador Entidades Bancarias. Caso contrario enviar NULL
						"IdTipoCuenta"=>2842, // valor numerico determinado por Id del clasificador de Tipo Cuenta Bancaria (idPadre=2841), poner o en caso de no encontrar el tipo requerido						"OtroTipoCuenta"=>NULL, 
						"OtroTipoCuenta"=>NULL, // valor textual empleado en caso de requerir otro tipo de cuenta que no este en el clasificador Tipo Cuenta. Caso contrario enviar NULL
						"IdTipoMoneda"=> 322, // valor numerico determinado por el Id del clasificador Monedas (idPadre=320)
						"NroCuenta"=>"123-456789-4", //valor textual para el envio del numero de cuenta
						"BeneficiarioNombre"=>"Nombre Modificado", 
						"BeneficiarioApellido"=>"Apellido Beneficiario", 
						"BeneficiarioIdentificacion"=>NULL, // valor textual en el caso de requerir el registro de la identificacion. Caso contrario enviar NULL
						"BancoIntermediario"=>NULL, // valor textual en caso de hacer uso del campo. Caso contrario NULL
						"IdUsuarioReg"=>90, // valor numerico obtenido del id del usuario autenticado. Usar 0 en caso de no tener el id
						"Vigencia"=>1 //valor recuperado de los datos de cuenta
						);
*/
/*PARAMETROS EDITAR VIGENCIA DE CUENTA BANCO  (0=No Vigente; 1=Vigente)

 		$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
						"accion"=>"EditarVigenciaCuentaBanco", 
						"IdCuentaBanco" => 1, //Id del registro de cuenta bancaria
						"IdCliente" => 34661, //Id del Proveedor o Cliente 
						"Vigencia"=>0, //0=No Vigente; 1=Vigente 
						"IdUsuarioReg"=>90 // valor del id del usuario retornado en el login						  
						);
*/

		$parametros=json_encode($parametros);
		// abrimos la sesión cURL
		$ch = curl_init();
		// definimos la URL a la que hacemos la petición
		//curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/wsibno/registro/ws-cuentabanco.php"); // OFFICIAL
		curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/wsibnob/registro/ws-cuentabanco.php"); // PRUEBA
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