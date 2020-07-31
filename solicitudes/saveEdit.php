<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();
//$arrayFilesCabecera=json_decode($_POST['archivos_cabecera']);
//$arrayFilesDetalle=json_decode($_POST['archivos_detalle']);
$cantidadFilas=$_POST["cantidad_filas"];
$facturas= json_decode($_POST['facturas']);
session_start();

$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];


if(isset($_POST['numero'])){
    $observaciones=$_POST['observaciones_solicitud'];
    //numero correlativo de la solicitud
    $sql="SELECT IFNULL(max(c.numero)+1,1)as codigo from solicitud_recursos c";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $numero=$_POST['numero'];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $numero=$row['codigo'];
    }  
    $tipoSol=$_POST['tipo_solicitud'];
  if($tipoSol!=2){
    $codProv=0;
    if($tipoSol==3){
      //datos para solicitud recursos manual manual
      $codSim=0;
      $codSimServ=0;
      $globalArea=$_POST['area_solicitud'];
     $globalUnidad=$_POST['unidad_solicitud'];  
    }else{
      //datos para solicitud recursos SIMULACION (PROPUESTA)
     $simu=explode("$$$",$_POST['simulaciones']);
     if($simu[1]=="TCP"){
      //tcp o tcs
      $codSim=0;
      $codSimServ=$simu[0];
      $areaUnidad=obtenerUnidadAreaPorSimulacionServicio($codSimServ);
     }else{
      // sec
      $codSim=$simu[0];
      $codSimServ=0;
      $areaUnidad=obtenerUnidadAreaPorSimulacionCosto($codSim);
     }
     $globalArea=$areaUnidad[0];
     $globalUnidad=$areaUnidad[1];     
    }
  }else{
    //datos para solicitud recursos proveeedor
    $codProv=$_POST['proveedores'];
    $codSim=0;
    $codSimServ=0;
  }

  $codCont=0;//CODIGO DE CONTRATO
  $fecha= date("Y-m-d h:m:s");
  $codSolicitud=obtenerCodigoSolicitudRecursos();
  $dbh = new Conexion();
  if(isset($_POST['usuario_ibnored_v'])){
       $v=$_POST['usuario_ibnored_v'];
       $sqlInsert="INSERT INTO solicitud_recursos (codigo, cod_personal,cod_unidadorganizacional,cod_area,fecha,numero,cod_simulacion,cod_proveedor,cod_simulacionservicio,cod_contrato,idServicio,observaciones) 
       VALUES ('".$codSolicitud."','".$globalUser."','".$globalUnidad."', '".$globalArea."', '".$fecha."','".$numero."','".$codSim."','".$codProv."','".$codSimServ."','".$codCont."','".$v."','".$observaciones."')";
  }else{
    $v=obtenerIdServicioPorIdSimulacion($codSimServ);
    $sqlInsert="INSERT INTO solicitud_recursos (codigo, cod_personal,cod_unidadorganizacional,cod_area,fecha,numero,cod_simulacion,cod_proveedor,cod_simulacionservicio,cod_contrato,idServicio,observaciones) 
       VALUES ('".$codSolicitud."','".$globalUser."','".$globalUnidad."', '".$globalArea."', '".$fecha."','".$numero."','".$codSim."','".$codProv."','".$codSimServ."','".$codCont."','".$v."','".$observaciones."')";
  }
  print_r($sqlInsert);
  $stmtInsert = $dbh->prepare($sqlInsert);
  $stmtInsert->execute();

  //enviar propuestas para la actualizacion de ibnorca
  $fechaHoraActual=date("Y-m-d H:i:s");
  $idTipoObjeto=2708;
  $idObjeto=2721; //regristado
  $obs="Registro de Solicitud";
  if(isset($_POST['usuario_ibnored_u'])){
       $u=$_POST['usuario_ibnored_u'];
       actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$u,$codSolicitud,$fechaHoraActual,$obs);
  }else{
       actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$codSolicitud,$fechaHoraActual,$obs);
  }



  //insertamos la distribucion
  $sqlDel="DELETE FROM distribucion_gastos_solicitud_recursos where cod_solicitudrecurso=$codSolicitud";
  $stmtDel = $dbh->prepare($sqlDel);
  $stmtDel->execute();

  //borramos los archivos
  $sqlDel="DELETE FROM archivos_adjuntos where cod_objeto=$codSolicitud and cod_tipopadre=2708";
  $stmtDel = $dbh->prepare($sqlDel);
  $stmtDel->execute();
  $sqlDel="DELETE FROM archivos_adjuntos where cod_padre=$codSolicitud and cod_tipopadre=27080";
  $stmtDel = $dbh->prepare($sqlDel);
  $stmtDel->execute();
  
  $valorDist=$_POST['n_distribucion'];
  if($valorDist!=0){
      $array1=json_decode($_POST['d_oficinas']);
      $array2=json_decode($_POST['d_areas']);
      if($valorDist==1){
        guardarDatosDistribucion($array1,0,$codSolicitud); //dist x Oficina
      }else{
        if($valorDist==2){
          guardarDatosDistribucion(0,$array2,$codSolicitud); //dist x Area
        }else{
          guardarDatosDistribucion($array1,$array2,$codSolicitud); //dist x Oficina y Area
        }
      }   
  }
}




$flagSuccess=true;
//subir archivos al servidor
//Como el elemento es un arreglos utilizamos foreach para extraer todos los valores

$nArchivosCabecera=$_POST["cantidad_archivosadjuntos"];
for ($ar=1; $ar <= $nArchivosCabecera ; $ar++) { 
  if(isset($_POST['codigo_archivo'.$ar])){
    if($_FILES['documentos_cabecera'.$ar]["name"]){
      $filename = $_FILES['documentos_cabecera'.$ar]["name"]; //Obtenemos el nombre original del archivos
      $source = $_FILES['documentos_cabecera'.$ar]["tmp_name"]; //Obtenemos un nombre temporal del archivos    
      $directorio = '../assets/archivos-respaldo/archivos_solicitudes/SOL-'.$codSolicitud; //Declaramos una  variable con la ruta donde guardaremos los archivoss
      //Validamos si la ruta de destino existe, en caso de no existir la creamos
      if(!file_exists($directorio)){
                mkdir($directorio, 0777,true) or die("No se puede crear el directorio de extracci&oacute;n");    
      }
      $target_path = $directorio.'/'.$filename; //Indicamos la ruta de destino, así como el nombre del archivos
      //Movemos y validamos que el archivos se haya cargado correctamente
      //El primer campo es el origen y el segundo el destino
      if(move_uploaded_file($source, $target_path)) { 
        echo "ok";
        $tipo=$_POST['codigo_archivo'.$ar];
        $descripcion=$_POST['nombre_archivo'.$ar];
        $tipoPadre=2708;
        $sqlInsert="INSERT INTO archivos_adjuntos (cod_tipoarchivo,descripcion,direccion_archivo,cod_tipopadre,cod_padre,cod_objeto) 
        VALUES ('$tipo','$descripcion','$target_path','$tipoPadre',0,'$codSolicitud')";
        $stmtInsert = $dbh->prepare($sqlInsert);
        $stmtInsert->execute();    
        print_r($sqlInsert);
      } else {    
          echo "error";
      } 
    }
  }
}

//guardar las ediciones
    $fila=0;
for ($i=1;$i<=$cantidadFilas;$i++){	
    if(isset($_POST["habilitar".$i])){ 

      $cod_plancuenta=$_POST["partida_cuenta_id".$i];
      $cod_unidadorganizacional=$_POST["unidad_fila".$i];
      $cod_area=$_POST["area_fila".$i];
      $detalle=$_POST["detalle_detalle".$i];
      $importe_presupuesto=$_POST["importe_presupuesto".$i];
      $importe=$_POST["importe".$i];
      $numero_factura=0;
      $archivo="";
      $cod_proveedor=$_POST["proveedor".$i];
      $cod_detalleplantilla=$_POST["cod_detalleplantilla".$i];
      $cod_servicioauditor=$_POST["cod_servicioauditor".$i];
      $cod_confretencion=$_POST["cod_retencion".$i];
      $cod_tipopagoproveedor=$_POST["cod_tipopago".$i];
      $nombre_beneficiario=$_POST["nombre_beneficiario".$i];
      $apellido_beneficiario=$_POST["apellido_beneficiario".$i];
      $nro_cuenta_beneficiario=$_POST["cuenta_beneficiario".$i];
      $cod_cuentabancaria=$_POST["cod_cuentaBancaria".$i];

      $cod_actividadproyecto=$_POST["cod_actividadproyecto".$i];
      $cod_accproyecto=$_POST["cod_accproyecto".$i];


      $codComprobanteDetalle=obtenerCodigoSolicitudDetalle();
      $sqlDetalle="INSERT INTO solicitud_recursosdetalle (codigo,cod_solicitudrecurso,cod_plancuenta,cod_unidadorganizacional,cod_area,detalle,importe_presupuesto,
        importe,numero_factura,archivo,cod_proveedor,cod_detalleplantilla,cod_servicioauditor,cod_confretencion,cod_tipopagoproveedor,
        nombre_beneficiario,apellido_beneficiario,nro_cuenta_beneficiario,cod_cuentabancaria,cod_actividadproyecto,acc_num) 
       VALUES ('$codComprobanteDetalle','$codSolicitud','$cod_plancuenta','$cod_unidadorganizacional','$cod_area','$detalle','$importe_presupuesto','$importe',
        '$numero_factura','$archivo','$cod_proveedor','$cod_detalleplantilla','$cod_servicioauditor','$cod_confretencion','$cod_tipopagoproveedor',
        '$nombre_beneficiario','$apellido_beneficiario','$nro_cuenta_beneficiario','$cod_cuentabancaria','$cod_actividadproyecto','$cod_accproyecto') ";
      $stmtDetalle = $dbh->prepare($sqlDetalle);
      $flagSuccessDetalle=$stmtDetalle->execute(); 

       $nF=cantidadF($facturas[$i-1]);
    for($j=0;$j<$nF;$j++){
      $nit=$facturas[$i-1][$j]->nit;
      $nroFac=$facturas[$i-1][$j]->nroFac;
      
      $fecha=$facturas[$i-1][$j]->fechaFac;
      $porciones = explode("/", $fecha);
      $fechaFac=$porciones[2]."-".$porciones[1]."-".$porciones[0];
      
      $razonFac=$facturas[$i-1][$j]->razonFac;
      $impFac=$facturas[$i-1][$j]->impFac;
      $exeFac=$facturas[$i-1][$j]->exeFac;
      $autFac=$facturas[$i-1][$j]->autFac;
      $conFac=$facturas[$i-1][$j]->conFac;

      $sqlDetalle2="INSERT INTO facturas_compra (cod_solicitudrecursodetalle, nit, nro_factura, fecha, razon_social, importe, exento, nro_autorizacion, codigo_control) 
      VALUES ('$codComprobanteDetalle', '$nit', '$nroFac', '$fechaFac', '$razonFac', '$impFac', '$exeFac', '$autFac', '$conFac')";
      $stmtDetalle2 = $dbh->prepare($sqlDetalle2);
      $flagSuccessDetalle2=$stmtDetalle2->execute();
      echo $sqlDetalle2;
    }
    //insertar cambios en el servicio web CUENTAS BANCARIAS
    $sIde = "ifinanciero";
    $sKey = "ce94a8dabdf0b112eafa27a5aa475751";
    $direccion=obtenerValorConfiguracion(42);
    $lista=obtenerDatosCuentaBancoProveedorWS($_POST["proveedor".$i],$_POST["cod_cuentaBancaria".$i]);
    $listas=$lista->datos;
    $codBanco=$listas->IdBanco;
    $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
            "accion"=>"EditarCuentaBanco",
            "IdCuentaBanco" => $_POST["cod_cuentaBancaria".$i], //Id del registro de cuenta bancaria
            "IdCliente" => $_POST["proveedor".$i], //Id del Proveedor o Cliente
            "IdBanco"=>$codBanco, //valor numerico determinado por id del clasificador perteneciente a Entidades Bancarias (idpadre=319), poner 0 en caso de que sea otra entidad financiera
            "OtroBanco"=>NULL, // valor textual empleado en caso de no encontrar el Banco en el clasificador Entidades Bancarias. Caso contrario enviar NULL
            "IdTipoCuenta"=>2842, // valor numerico determinado por Id del clasificador de Tipo Cuenta Bancaria (idPadre=2841), poner o en caso de no encontrar el tipo requerido           "OtroTipoCuenta"=>NULL, 
            "OtroTipoCuenta"=>NULL, // valor textual empleado en caso de requerir otro tipo de cuenta que no este en el clasificador Tipo Cuenta. Caso contrario enviar NULL
            "IdTipoMoneda"=> 322, // valor numerico determinado por el Id del clasificador Monedas (idPadre=320)
            "NroCuenta"=>$_POST["cuenta_beneficiario".$i], //valor textual para el envio del numero de cuenta
            "BeneficiarioNombre"=>$_POST["nombre_beneficiario".$i], 
            "BeneficiarioApellido"=>$_POST["apellido_beneficiario".$i], 
            "BeneficiarioIdentificacion"=>NULL, // valor textual en el caso de requerir el registro de la identificacion. Caso contrario enviar NULL
            "BancoIntermediario"=>NULL, // valor textual en caso de hacer uso del campo. Caso contrario NULL
            "IdUsuarioReg"=>$globalUser, // valor numerico obtenido del id del usuario autenticado. Usar 0 en caso de no tener el id
            "Vigencia"=>1 //valor recuperado de los datos de cuenta
            );
    $parametros=json_encode($parametros);
    // abrimos la sesión cURL
    $ch = curl_init();
    // definimos la URL a la que hacemos la petición
    //curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/wsibno/registro/ws-cuentabanco.php"); // OFFICIAL
    curl_setopt($ch, CURLOPT_URL,$direccion."registro/ws-cuentabanco.php"); // PRUEBA
    // indicamos el tipo de petición: POST
    curl_setopt($ch, CURLOPT_POST, TRUE);
    // definimos cada uno de los parámetros
    curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
    // recibimos la respuesta y la guardamos en una variable
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $remote_server_output = curl_exec ($ch);
    // cerramos la sesión cURL
    curl_close ($ch);
    //$dataInsert  
    $fila++;

    $nArchivosDetalle=$_POST["cantidad_archivosadjuntosdetalle".$i];
    for ($ar=1; $ar <= $nArchivosDetalle ; $ar++) { 
     if(isset($_POST['codigo_archivodetalle'.$ar."FFFF".$i])){
        if($_FILES['documentos_detalle'.$ar."FFFF".$i]["name"]){
          $filename = $_FILES['documentos_detalle'.$ar."FFFF".$i]["name"]; //Obtenemos el nombre original del archivos
          $source = $_FILES['documentos_detalle'.$ar."FFFF".$i]["tmp_name"]; //Obtenemos un nombre temporal del archivos    
          $directorio = '../assets/archivos-respaldo/archivos_solicitudes/SOL-'.$codSolicitud.'/DET-'.$fila; //Declaramos una  variable con la ruta donde guardaremos los archivoss
          //Validamos si la ruta de destino existe, en caso de no existir la creamos
        if(!file_exists($directorio)){
                mkdir($directorio, 0777,true) or die("No se puede crear el directorio de extracci&oacute;n");    
        }
        $target_path = $directorio.'/'.$filename; //Indicamos la ruta de destino, así como el nombre del archivos
        //Movemos y validamos que el archivos se haya cargado correctamente
        //El primer campo es el origen y el segundo el destino
        if(move_uploaded_file($source, $target_path)) { 
          echo "ok";
          $tipo=$_POST['codigo_archivodetalle'.$ar."FFFF".$i];
          $descripcion=$_POST['nombre_archivodetalle'.$ar."FFFF".$i];
          $tipoPadre=27080;//clasificador para detalle de solicitudes
          $sqlInsert="INSERT INTO archivos_adjuntos (cod_tipoarchivo,descripcion,direccion_archivo,cod_tipopadre,cod_padre,cod_objeto) 
                    VALUES ('$tipo','$descripcion','$target_path','$tipoPadre','$codSolicitud','$codComprobanteDetalle')";
                    $stmtInsert = $dbh->prepare($sqlInsert);
                    $stmtInsert->execute();    
                    print_r($sqlInsert);
        } else {    
          echo "error";
        } 
       }
      }//FIN IF
     }

      /*foreach($_FILES["archivos".$i]['tmp_name'] as $key => $tmp_name)
      {
        //Validamos que el archivos exista
        if($_FILES["archivos".$i]["name"][$key]) {
            $filename = $_FILES["archivos".$i]["name"][$key]; //Obtenemos el nombre original del archivos
            $source = $_FILES["archivos".$i]["tmp_name"][$key]; //Obtenemos un nombre temporal del archivos
            $directorio = '../assets/archivos-respaldo/archivos_solicitudes/SOL-'.$codSolicitud.'/DET-'.$fila; //Declaramos una  variable con la ruta donde guardaremos los archivoss
            //Validamos si la ruta de destino existe, en caso de no existir la creamos
            if(!file_exists($directorio)){
                mkdir($directorio, 0777,true) or die("No se puede crear el directorio de extracci&oacute;n");    
            }
            $target_path = $directorio.'/'.$filename; //Indicamos la ruta de destino, así como el nombre del archivos
            
            //Movemos y validamos que el archivos se haya cargado correctamente
            //El primer campo es el origen y el segundo el destino
            if(move_uploaded_file($source, $target_path)) { 
                echo "ok";
                for ($a=0; $a < count($arrayFilesDetalle[$i-1]); $a++) {         
                  if($arrayFilesDetalle[$i-1][$a]->nombre==$filename){
                    
                    //insertamos a la tabla de archivos
                    $tipo=$arrayFilesDetalle[$i-1][$a]->tipo;
                    $descripcion=$arrayFilesDetalle[$i-1][$a]->nombre_tipo;
                    $tipoPadre=27080; //clasificador para detalle de solicitudes
                    $sqlInsert="INSERT INTO archivos_adjuntos (cod_tipoarchivo,descripcion,direccion_archivo,cod_tipopadre,cod_padre,cod_objeto) 
                    VALUES ('$tipo','$descripcion','$target_path','$tipoPadre','$codSolicitud','$codComprobanteDetalle')";
                    $stmtInsert = $dbh->prepare($sqlInsert);
                    $stmtInsert->execute();    
                    print_r($sqlInsert);
                  }
                }
            } else {    
                echo "error";
            }
        }  
      }*/
    }
} 
if(isset($_POST['usuario_ibnored'])){
    $q=$_POST['usuario_ibnored'];
    $s=$_POST['usuario_ibnored_s'];
    $u=$_POST['usuario_ibnored_u'];
    $v=$_POST['usuario_ibnored_v'];
  if($flagSuccess==true){
    showAlertSuccessError(true,"../".$urlList."&q=".$q."&s=".$s."&u=".$u."&v=".$v); 
  }else{
    showAlertSuccessError(false,"../".$urlList."&q=".$q."&s=".$s."&u=".$u."&v=".$v);
  }
}else{
  if($flagSuccess==true){
    showAlertSuccessError(true,"../".$urlList); 
  }else{
    showAlertSuccessError(false,"../".$urlList);
  }
}


function guardarDatosDistribucion($array1,$array2,$codigoSol){
  $dbh = new Conexion();
 if($array1!=0){
  for ($i=0; $i < count($array1); $i++) { 
    $unidad=$array1[$i]->unidad;
    $porcentaje=$array1[$i]->porcentaje;
    $sqlInsert="INSERT INTO distribucion_gastos_solicitud_recursos (tipo_distribucion,oficina_area,porcentaje,cod_solicitudrecurso) 
    VALUES ('1','$unidad','$porcentaje','$codigoSol')";
    $stmtInsert = $dbh->prepare($sqlInsert);
    $stmtInsert->execute();
  }   
}
if($array2!=0){
  for ($i=0; $i < count($array2); $i++) { 
    $area=$array2[$i]->area;
    $porcentaje=$array2[$i]->porcentaje;
    $sqlInsert="INSERT INTO distribucion_gastos_solicitud_recursos (tipo_distribucion,oficina_area,porcentaje,cod_solicitudrecurso) 
    VALUES ('2','$area','$porcentaje','$codigoSol')";
    $stmtInsert = $dbh->prepare($sqlInsert);
    $stmtInsert->execute();
  }
 } 
}
  
?>
