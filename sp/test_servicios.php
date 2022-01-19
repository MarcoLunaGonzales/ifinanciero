<?php 
/*REGISTRO DE CONTACTOS XLS FINANCIERO
WS: ws-fin-cliente-contacto.php 
MOD: 2022-01-05
*/
//LLAVES DE ACCESO AL WS
$sIde = "ifinanciero";
$sKey = "ce94a8dabdf0b112eafa27a5aa475751";

/*PARAMETROS PARA EJECUTAR LAS OPERACIONES*/
  /*Para el registro de Contacto de Cliente Empresa 
  
  $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
            "accion"=>"RegistrarContactoEmpresaXLS", //Nuevo contacto de empresa
            "IdCliente"=>41335, //ID del registrado de la tabla cliente, recuperado de los datos de cliente
            "IdUsuarioReg"=>15, //ID_USUARIO_REG, //ID del usuario que crea el registro; 0 (cero) en caso de no tenerlo
            "NombreContacto"=>"Nombre Prueba Financiero", //Nombre del contacto de la empresa
            "CargoContacto"=>"Cargo Prueba Financiero", //Cargo que ocupa el contacto dentro la empresa
            "Telefono"=>"234567-72595432", //Telefono o celular de contacto
            "CorreoContacto"=>"servicios@prueba.com", //correo se usa como nombre de usuario para acceso a la cuenta (Usuario Visor)
            "IdTipoContacto"=>2820, //Id Clasificador IdPadre=2817, tipos de contacto; 0 en caso de no requerir el dato
            "IdArea"=>11 //id de area de servicio asignado al contacto (obtener del clasificador area idPadre=6)
            );  
  */
  
  /*Editar el registro de Contacto de Cliente Empresa 
    
  $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
            "accion"=>"EditarContactoEmpresaXLS", //Nuevo contacto de empresa
            "IdContacto" => 31282, //id del registro de contacto
            "IdCliente"=>41335, //ID del registrado de la tabla cliente, recuperado de los datos de cliente
            "IdUsuarioReg"=>1, //ID_USUARIO_REG, //ID del usuario que modifica el registro; 0 (cero) en caso de no tenerlo
            "NombreContacto"=>"Nombre Prueba", //Nombres y apellidos del contacto de la empresa
            "CargoContacto"=>"Cargo Prueba", //Cargo que ocupa el contacto dentro de su empresa
            "Telefono"=>"234567-72598686", //Telefono o celular de contacto
            "CorreoContacto"=>'gerente@prueba.com', //correo se usa como nombre de usuario.No permitir el cambioEn caso de tener una cuenta (IdUsuario recuperado de las lista o datos de contacto>0) a sistemas ibnorca
            "IdTipoContacto"=>4233, //2820, //Id Clasificador IdPadre=2817, tipos de contacto; 0 en caso de no requerir el dato
            "IdArea"=>array(11,38) //arreglo de ids de area (en base al clasificador de area) asignados al contacto
            );  
  */
  /*Listar Contactos de Cliente Empresa 
    
  $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
            "accion"=>"ListarContactosEmpresaXLS", 
            "IdCliente"=>41335, //ID del registrado de la tabla cliente, recuperado de los datos de cliente
            );
  */
  
  /*Datos Contacto de Cliente Empresa 
    
  $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
            "accion"=>"DatosContactoEmpresaXLS", 
            "IdContacto"=>31282, //Id contacto 
            "IdCliente"=>41335, //ID del registrado de la tabla cliente, recuperado de los datos de cliente
            );
  */
  
  /*Datos Contacto de Cliente Empresa x Tipo 2022-01-05 */
    // $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
    //       "accion"=>"ListarContactosEmpresaXLS", 
    //       "IdCliente"=>41335 //ID del registrado de la tabla cliente, recuperado de los datos de cliente
    //       );


  $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
            "accion"=>"ListaContactoEmpresaxTipoXLS", 
            "IdCliente"=>4064, //ID del registrado de la tabla cliente, recuperado de los datos de cliente
            "IdTipoContacto"=>4233 //4233 MAE. Puede ser el id recuperado del clasificador de tipos de contacto empresa idPadre=2817
            );
  

  $datos=json_encode($parametros);
  
  // abrimos la sesión cURL
  $ch = curl_init();
  // definimos la URL a la que hacemos la petición
  //curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/wsibno/registro/ws-fin-cliente-contacto.php"); // on line 
  curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/wsibnob/registro/ws-fin-cliente-contacto.php"); // db pruebas
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
  
  // imprimir en formato JSON
  header('Content-type: application/json');   
  print_r($remote_server_output);       
  
?>
