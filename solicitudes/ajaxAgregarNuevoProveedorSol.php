<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();
$direccionServ=obtenerValorConfiguracion(42);//direccion des servicio web
$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();
$globalUser=$_SESSION["globalUser"];


$sIde = "ifinanciero";
$sKey = "ce94a8dabdf0b112eafa27a5aa475751";
$ciudad=(int)$_GET['ciudad'];
$otra=NULL;
if($_GET['ciudad']==""){
	$ciudad="0";
	$otra=$_GET['otra'];
}

$nombre_contacto=null;
if($_GET['nombre_contacto']!=""){
  $nombre_contacto=$_GET['nombre_contacto'];
}
$apellido_contacto=null;
if($_GET['apellido_contacto']!=""){
  $apellido_contacto=$_GET['apellido_contacto'];
}
$cargo_contacto=null;
if($_GET['cargo_contacto']!=""){
  $cargo_contacto=$_GET['cargo_contacto'];
}
$correo_contacto=null;
if($_GET['correo_contacto']!=""){
  $correo_contacto=$_GET['correo_contacto'];
}

$direccion=null;
if($_GET['direccion']!=""){
  $direccion=$_GET['direccion'];
}

$telefono=null;
if($_GET['telefono']!=""){
  $telefono=$_GET['telefono'];
}
$correo=null;
if($_GET['correo']!=""){
  $correo=$_GET['correo'];
}




$mensajeDevuelta="";$errorDevuelta=0;
  // Tipo P=Persona, E=Empresa
if($_GET['tipo']=='E'){
	$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
						  "accion"=>"RegistrarProveedor", //0 para nuevo registro
						  //REQUERIDO
						  "tipoCliente"=>$_GET['tipo'], // Tipo E=Empresa
						  "claseCliente"=>$_GET['nacional'], // Clase N=Nacional, I=Internacional
						  "nombreRazon"=>$_GET['nombre'], //Nombre de la empresa
						  "identificacion"=>$_GET['nit'], //Repetir NIT de la empresa o alguna identificacion
						  "pais"=>(int)$_GET['pais'], //Valor numerico determinado por el ws-paises opcion pais, el valor = 26 para Bolivia
						  "depto"=>(int)$_GET['estado'], //Valor numerico determinado por el ws-paises opcion estados y id pais =26 para deptos de bolivia(Ej. 480=La Paz)
						  "ciudad"=>$ciudad, //Valor numerico determinado por el ws-paises opcion ciudad y idEstado=480 para La Paz (Ej. 72=ciudad El Alto)
						  "ciudadOtro"=>$otra, // campo VARCHAR, se emplea en el caso de seleccionar otro en el campo Ciudad 
						  "direccion"=>$direccion, //Direccion del Cliente, campo VARCHAR 
						  //OPCIONAL
						  "telefono"=>$telefono, //Telefono fijo del cliente, campo VARCHAR 
						  "correo"=>$correo, //correo de la empresa
						  //OPCIONAL CONTACTOS
						  "nombreContacto"=>$nombre_contacto, //"Javier", //Nombre del contacto que manejara la cuenta de la empresa
						  "apellidoContacto"=>$apellido_contacto, //"Prueba", //Apellido del contacto que manejara la cuenta de la empresa
						  "cargoContacto"=>$cargo_contacto, //"Gerente", //Cargo que ocupa el contacto dentro la empresa
						  "correoContacto"=>$correo_contacto, //"mailotrocontacto@empresa.com", //correo campo varchar
						  //REQUERIDO
						  "optFactura"=>1, // 1=datos de factura, 0 = sin datos de factura
						  "facturaRazon"=>$_GET['nombre'], // Razon Social de la factura Puede ser el mismo nombre empresa, NULL en caso de optFactura=0
						  "facturaNIT"=>$_GET['nit']  // NIT para factura el mismo del IDENTIFICACION, NULL en caso de optFactura=0
						  );

}else{//para el cliente
	if($_GET['nacional']=='N'){
		/*PARAMETROS PARA REGISTRAR UNA PERSONA PROVEEDORA NACIONAL */
		$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
						  "accion"=>"RegistrarProveedor", 
						  //REQUERIDO
						  "tipoCliente"=>"P", // Tipo P=Persona
						  "claseCliente"=>$_GET['nacional'], // Clase N=Nacional, I=Internacional
						  "nombreRazon"=>$_GET['nombre'], //Nombre de la persona o empresa
						  "paterno"=>$_GET['paterno_p'], //Apellido paterno del cliente persona
						  "materno"=>$_GET['materno_p'], //(Opcional) Apellido materno del cliente persona
						  "tipoId"=>1581, //(Opcional) tipo de identificacion valor numerico determinado por el clasificador tipo de documento id padre 1580 (1581=CI, 1582=Pasaporte)
						  "tipoIdOtro"=>NULL, // Otro tipo de identificacion campo VARCHAR, se debe habilitar en el caso de seleccionar otro en el campo tipoId
						  "identificacion"=>$_GET['identificacion'], // Numero de identificacion, campo VARCHAR
						  "emision"=>480, //(Opcional) Lugar de emision en Bolivia, Valor numerico determinado por el ws-paises opcion estados y idPais 26=Bolivia(Ej. 480=La Paz)
						  "emisionOtro"=>NULL, //Otro lugar de emision campo VARCHAR, se emplea en el caso de seleccionar otro en el campo emision
						  "nacionalidad"=>(int)$_GET['pais'], //(Opcional)Pais de origen valor numerico, determinado por el listado de paises (web service paises) 26=Bolivia
						  "pais"=>(int)$_GET['pais'], //Valor numerico determinado por el ws-paises opcion pais, el valor = 26 para Bolivia
						  "depto"=>(int)$_GET['estado'], //Valor numerico determinado por el ws-paises opcion estados y id pais =26 para deptos de bolivia(Ej. 480=La Paz)
						  "ciudad"=>$ciudad, //Valor numerico determinado por el ws-paises opcion ciudad y idEstado=480 para La Paz (Ej. 72=ciudad El Alto)
						  "ciudadOtro"=>$otra, // campo VARCHAR, se emplea en el caso de seleccionar otro en el campo Ciudad 
						  "direccion"=>$direccion, //Direccion del Cliente, campo VARCHAR 
						  //OPCIONAL
						  "telefono"=>$telefono, //Telefono fijo del cliente, campo VARCHAR 
						  "movil"=>NULL, //Telefono Movil o Celular, campo VARCHAR 
						  "correo"=>$correo, //correo que posteriormente servira de nombre de usuario para el acceso a la cuenta, campo varchar
						  //REQUERIDO
						  "optFactura"=>1, // 1=datos de factura, 0 = sin datos de factura
						  "facturaRazon"=>$_GET['nombre'], // Razon Social de la factura, NULL en caso de optFactura=0
						  "facturaNIT"=>$_GET['nit']// NIT para factura, NULL en caso de optFactura=0
						  );
	}else{
		/*PARAMETROS PARA REGISTRAR UNA PERSONA PROVEEDORA INTERNACIONAL*/

		$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
						  "accion"=>"RegistrarProveedor", 
						  //REQUERIDO
						  "tipoCliente"=>"P", // Tipo P=Persona
						  "claseCliente"=>$_GET['nacional'], // Clase N=Nacional, I=Internacional
						  "nombreRazon"=>$_GET['nombre'], //Nombre de la persona o empresa
						  "paterno"=>$_GET['paterno_p'], //Apellido paterno del cliente persona
						  "materno"=>$_GET['materno_p'], //(Opcional)Apellido materno del cliente persona
						  "tipoId"=>1581, //(Opcional)tipo de identificacion valor numerico determinado por el clasificador tipo de documento id padre 1580 (1581=CI, 1582=Pasaporte)
						  "tipoIdOtro"=>NULL, // Otro tipo de identificacion campo VARCHAR, se debe habilitar en el caso de seleccionar otro en el campo tipoId
						  "identificacion"=>$_GET['identificacion'], //324466, // Numero de identificacion, campo VARCHAR
						  "nacionalidad"=>(int)$_GET['pais'], //(Opcional)Pais de origen valor numerico, determinado por el listado de paises (web service paises) (int)$_GET['pais']=Bolivia
						  "pais"=>(int)$_GET['pais'], //Valor numerico determinado por el ws-paises opcion pais, el valor = 26 para Bolivia
						  "depto"=>(int)$_GET['estado'], //Valor numerico determinado por el ws-paises opcion estados y id pais =26 para deptos de bolivia(Ej. 480=La Paz)
						  "ciudad"=>$ciudad, //Valor numerico determinado por el ws-paises opcion ciudad y idEstado=480 para La Paz (Ej. 72=ciudad El Alto)
						  "ciudadOtro"=>$otra, // campo VARCHAR, se emplea en el caso de seleccionar otro en el campo Ciudad 
						  "direccion"=>$direccion, //Direccion del Cliente, campo VARCHAR 
						  //OPCIONAL
						  "telefono"=>$telefono, //Telefono fijo del cliente, campo VARCHAR 
						  "movil"=>NULL, //Telefono Movil o Celular, campo VARCHAR 
						  "correo"=>$correo, //correo que posteriormente servira de nombre de usuario para el acceso a la cuenta, campo varchar
						  //REQUERIDO
						  "optFactura"=>1, // 1=datos de factura, 0 = sin datos de factura
						  "facturaRazon"=>$_GET['nombre'], // Razon Social de la factura, NULL en caso de optFactura=0
						  "facturaNIT"=>$_GET['nit'], //1234567 // NIT para factura, NULL en caso de optFactura=0
						  );

	}
}		


/*INTEDAMOS DATOS DEL PROVEEDOR EN EL LOG*/
$codLog=obtenerCodigoLogRegistroProveedor();
$fechaActual=date("Y-m-d H:i:s");
//$json_datos=implode(",",$parametros);
$json_datos=array2json($parametros);
$sqlDetalle2="INSERT INTO log_registro_proveedores (codigo, fecha,cod_error_devuelto,detalle_error_devuelto,json_datos) 
VALUES ('$codLog', '$fechaActual', '0', 'REGISTRO SATISFACTORIO - IFINANCIERO', '$json_datos')";
$stmtDetalle2 = $dbh->prepare($sqlDetalle2);
$stmtDetalle2->execute();


/*FIN DE LOG*/


		$parametros=json_encode($parametros);
		// abrimos la sesion cURL
		$ch = curl_init();
		//curl_setopt($ch, CURLOPT_URL,$direccion."registro/ws-registro-proveedor.php"); // OFFICIAL
		curl_setopt($ch, CURLOPT_URL,$direccionServ."registro/ws-registro-proveedor.php"); // PRUEBA
		// indicamos el tipo de peticiรณn: POST
		curl_setopt($ch, CURLOPT_POST, TRUE);
		// definimos cada uno de los parรกmetros
		curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
		// recibimos la respuesta y la guardamos en una variable
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$remote_server_output = curl_exec ($ch);
		curl_close ($ch);
		
		$respuesta=json_decode($remote_server_output);
		//imprimir en formato JSON
		//header('Content-type: application/json'); 	
		//print_r($respuesta->existe); 	
        $mensajeDevuelta.=$respuesta->mensaje;
		if($respuesta->estado=="1"||$respuesta->estado==1||$respuesta->estado==true){
		  if(isset($respuesta->existe)){
			if($respuesta->existe=="1"||$respuesta->existe==1||$respuesta->existe==true){
				if(isset($respuesta->IdCliente)){
					if(isset($respuesta->Identificacion)){
				       $mensajeDevuelta.=" (".$respuesta->NombreRazon.")";	
				  }
				}	
				echo "2";
				$errorDevuelta=2;
			}else{
				echo "1";
				$errorDevuelta=1;		
			}	
		  }else{
		  	echo "1";
		  	$errorDevuelta=1;
		  }	
		}else{
          echo "0";
          $errorDevuelta=0;
		}

		$sqlDetalle2="UPDATE log_registro_proveedores SET detalle_error_devuelto='$mensajeDevuelta',cod_error_devuelto='$errorDevuelta' where codigo=$codLog";
        $stmtDetalle2 = $dbh->prepare($sqlDetalle2);
        $stmtDetalle2->execute();

		echo "####".$mensajeDevuelta;
		//print_r($respuesta); 
		/*PARAMETROS PARA ASIGNAR ATRIBUTO PROVEEDOR

		 		$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
								  "accion"=>"AsignarAtributoProveedor", 
								  "IdCliente"=>36457, 
								  "IdUsuario"=>1 // valor del id del usuario retornado en el login						  
								  );
		*/
function array2json($data){
    $data = json_encode($data);
    
    $tabCount = 0;
    $result = '';
    $quotes = false;
    $separator = "\t";
    $newLine = "\n";

    for($i=0;$i<strlen($data);$i++){
        $c = $data[$i];
        if($c=='"' && $data[$i-1]!='\\') $quotes = !$quotes;
        if($quotes){
            $result .= $c;
            continue;
        }
        switch($c){
            case '{':
            case '[':
                $result .= $c . $newLine . str_repeat($separator, ++$tabCount);
                break;
            case '}':
            case ']':
                $result .= $newLine . str_repeat($separator, --$tabCount) . $c;
                break;
            case ',':
                $result .= $c;
                if($data[$i+1]!='{' && $data[$i+1]!='[') $result .= $newLine . str_repeat($separator, $tabCount);
                break;
            case ':':
                $result .= $c . ' ';
                break;
            default:
                $result .= $c;
        }
    }
    return  $result;
}

?>