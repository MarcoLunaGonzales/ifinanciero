<?php
set_time_limit(0);
require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();
$arrayFilesCabecera=json_decode($_POST['archivos_cabecera']);
$arrayFilesDetalle=json_decode($_POST['archivos_detalle']);
$codComprobanteDetalle=obtenerCodigoSolicitudDetalle();

$cantidadFilas=$_POST["cantidad_filas"];
$facturas= json_decode($_POST['facturas']);
session_start();

$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];

$fechaHoraActual=date("Y-m-d H:i:s");

$codSolicitud=$_POST['cod_solicitud'];

$unidad_solicitud=$_POST["unidad_solicitud"];
$area_solicitud=$_POST["area_solicitud"];
$observaciones_solicitud=$_POST["observaciones_solicitud"];

$sqlUpdate="UPDATE solicitud_recursos SET cod_unidadorganizacional=$unidad_solicitud,cod_area=$area_solicitud,observaciones='$observaciones_solicitud' where codigo=$codSolicitud";
$stmtUpdate = $dbh->prepare($sqlUpdate);
$flagSuccess=$stmtUpdate->execute();

// Preparamos
$stmtSolicitud = $dbh->prepare("SELECT sr.*,es.nombre as estado,u.abreviatura as unidad,a.abreviatura as area from solicitud_recursos sr join estados_solicitudrecursos es on sr.cod_estadosolicitudrecurso=es.codigo join unidades_organizacionales u on sr.cod_unidadorganizacional=u.codigo join areas a on sr.cod_area=a.codigo where sr.cod_estadoreferencial=1 and sr.codigo=$codSolicitud");
// Ejecutamos
$stmtSolicitud->execute();
// bindColumn
$stmtSolicitud->bindColumn('unidad', $unidadX);
$stmtSolicitud->bindColumn('area', $areaX);
$stmtSolicitud->bindColumn('cod_simulacion', $codSimulacion);
$stmtSolicitud->bindColumn('cod_proveedor', $codProveedor);
$stmtSolicitud->bindColumn('cod_simulacionservicio', $codSimulacionServicio);
$stmtSolicitud->bindColumn('numero', $numeroSol);

while ($rowSolicitud = $stmtSolicitud->fetch(PDO::FETCH_BOUND)) {
      $unidadX=$unidadX;
      $areaX=$areaX;
      $codSimulacion=$codSimulacion;
      $codProveedor=$codProveedor;
      $codSimulacionServicio=$codSimulacionServicio;
      $numeroSol=$numeroSol;

      if($codSimulacion!=0){
        $nombreCliente="Sin Cliente";
        $nombreSimulacion=nameSimulacion($codSimulacion);
      }else{
        $nombreCliente=nameClienteSimulacionServicio($codSimulacionServicio);
        $nombreSimulacion=nameSimulacionServicio($codSimulacionServicio);
      }
}

//insertamos la distribucion
  $sqlDel="DELETE FROM distribucion_gastos_solicitud_recursos where cod_solicitudrecurso=$codSolicitud";
  $stmtDel = $dbh->prepare($sqlDel);
  $stmtDel->execute();


if(isset($_POST["personal_encargado"])){
 //insertamos la distribucion
  $sqlDel="DELETE FROM solicitud_recursosencargado where cod_solicitudrecurso=$codSolicitud";
  $stmtDel = $dbh->prepare($sqlDel);
  $stmtDel->execute();
  
  if($_POST["personal_encargado"]>0){
  $codEncargado=$_POST["personal_encargado"];
  $sqlInsert="INSERT INTO solicitud_recursosencargado (cod_solicitudrecurso,cod_personal) 
        VALUES ('$codSolicitud','$codEncargado')";
  $stmtInsert = $dbh->prepare($sqlInsert);
  $stmtInsert->execute();  
  }

  /*
  //$encargados=$_POST["personal_encargado"];
  for ($encar=0; $encar < count($encargados); $encar++) { 
    $codEncargado=$encargados[$encar];
        $sqlInsert="INSERT INTO solicitud_recursosencargado (cod_solicitudrecurso,cod_personal) 
        VALUES ('$codSolicitud','$codEncargado')";
        $stmtInsert = $dbh->prepare($sqlInsert);
        $stmtInsert->execute();  
  }*/
  
}


  $sqlDeleteFact="SELECT * from solicitud_recursosdetalle where cod_solicitudrecurso=$codSolicitud";
  $stmtDeleteFac = $dbh->prepare($sqlDeleteFact);
  $stmtDeleteFac->execute();
  while ($rowEsta = $stmtDeleteFac->fetch(PDO::FETCH_ASSOC)) {
   $codigoDetalle=$rowEsta['codigo'];
   $sqlDelete="DELETE from facturas_compra where cod_solicitudrecursodetalle='$codigoDetalle'";
   $stmtDel = $dbh->prepare($sqlDelete);
   $stmtDel->execute();
  }


  //insertamos la distribucion
  /*$sqlDel="DELETE FROM facturas_compra where cod_solicitudrecursodetalle in (select codigo from solicitud_recursosdetalle where cod_solicitudrecurso=$codSolicitud)";
  $stmtDel = $dbh->prepare($sqlDel);
  $stmtDel->execute();*/

  
  $valorDist=$_POST['n_distribucion'];
  if($valorDist!=0){
      $array1=json_decode($_POST['d_oficinas']);
      $array2=json_decode($_POST['d_areas']);
      $array3=json_decode($_POST['d_areas_global']);
      $array4=json_decode($_POST['d_oficinas_global']);
      if($valorDist==1){
        guardarDatosDistribucion($array1,0,$codSolicitud); //dist x Oficina
      }else{
        if($valorDist==2){
          guardarDatosDistribucion(0,$array2,$codSolicitud); //dist x Area
        }else{
         if($valorDist==3){
           guardarDatosDistribucion($array1,$array2,$codSolicitud); //dist x Oficina y Area 
          }else{
           guardarDatosDistribucionGeneral($array3,$array4,$codSolicitud); //dist x Oficina y Area  
          }
        }
      }   
  }

//$flagSuccess=true;
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
        $datosArchivo=verificarExisteArchivoSolicitud($tipo,$descripcion,$tipoPadre,$codSolicitud);
        $codigoArchivo=$datosArchivo[0];
        $linkArchivo=$datosArchivo[1];
        if($codigoArchivo!=0){
          $sqlDel="DELETE FROM archivos_adjuntos where codigo='$codigoArchivo'";
          $stmtDel = $dbh->prepare($sqlDel);
          $stmtDel->execute();
          //borrar de la carpeta
          unlink($linkArchivo);
          if(obtenerValorConfiguracion(93)==1){
            $banderaArchivo=obtenerBanderaArchivoIbnorca('archivos_adjuntos',$codigoArchivo);
            if($banderaArchivo>0){
              $globalServerDelete=obtenerValorConfiguracion(94);
              ?><script>ajaxDeleteArchivo("<?=$globalServerDelete;?>","<?=$banderaArchivo?>","divArchivo",15,"<?=$codigoArchivo;?>");</script><?php   
            }          
          }
        }
        $codArchivoAdjunto=obtenerCodigoUltimoTabla('archivos_adjuntos');
        $sqlInsert="INSERT INTO archivos_adjuntos (codigo,cod_tipoarchivo,descripcion,direccion_archivo,cod_tipopadre,cod_padre,cod_objeto) 
        VALUES ($codArchivoAdjunto,'$tipo','$descripcion','$target_path','$tipoPadre',0,'$codSolicitud')";
        $stmtInsert = $dbh->prepare($sqlInsert);   
        $flagArchivo=$stmtInsert->execute();            
        if(obtenerValorConfiguracion(93)==1&&$flagArchivo){ //registrar en documentos de ibnorca al final se borra en documento del ifinanciero
          //sibir archivos al servidor de documentos
          $parametros=array(
            "idD" => 15,
            "idR" => $codArchivoAdjunto,
            "idusr" => $globalUser,
            "Tipodoc" => 176,
            "descripcion" => $descripcion,
            "codigo" => "",
            "observacion" => "-",
            "r" => "/",
            "v" => true
            );
           $resultado=enviarArchivoAdjuntoServidorIbnorca($parametros,$target_path);
           //unlink($target_path);
           //print_r($resultado);        
        }
      } else {    
          echo "error";
      } 
    }
  }
}

//guardar las ediciones
$auxServicio=0;
    $fila=0;
for ($i=1;$i<=$cantidadFilas;$i++){	
  if(isset($_POST["partida_cuenta_id".$i])){      
    $data[$fila][0]=$_POST["partida_cuenta_id".$i];
    $data[$fila][1]=$_POST["unidad_fila".$i]; 
    $data[$fila][2]=$_POST["area_fila".$i];  
    $data[$fila][3]=$_POST["detalle_detalle".$i]; 
    $data[$fila][4]=$_POST["importe_presupuesto".$i]; 
    $data[$fila][5]=$_POST["importe".$i];           
    $data[$fila][6]=0; 
    $data[$fila][7]="";
    $data[$fila][8]=$_POST["proveedor".$i];
    $data[$fila][9]=$_POST["cod_detalleplantilla".$i];
    $data[$fila][10]=$_POST["cod_servicioauditor".$i];
    $data[$fila][11]=$_POST["cod_retencion".$i];
    $data[$fila][12]=$_POST["cod_tipopago".$i];
    $data[$fila][13]=$_POST["nombre_beneficiario".$i];
    $data[$fila][14]=$_POST["apellido_beneficiario".$i];
    $data[$fila][15]=$_POST["cuenta_beneficiario".$i];
    $data[$fila][16]=$_POST["cod_cuentaBancaria".$i];
    $data[$fila][17]=$_POST["cod_actividadproyecto".$i];
    $data[$fila][18]=$_POST["cod_accproyecto".$i];
    $data[$fila][19]=$_POST["cod_servicio".$i];
    $data[$fila][20]=$_POST["cod_divisionpago".$i];
    $cod_servicio=$_POST["cod_servicio".$i];
    if(!($cod_servicio==""||$cod_servicio==0)&&$auxServicio==0){
        $auxServicio=$cod_servicio;
        $sqlUpdateSol="UPDATE solicitud_recursos SET idServicio=$cod_servicio where codigo=$codSolicitud";
        $stmtUpdateSol = $dbh->prepare($sqlUpdateSol);
        $stmtUpdateSol->execute(); 
      }  

    //$dataInsert  
    echo $data[$fila][11];
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
      $codComprobanteDetalle++;   
    }
} 
$cab[0]="cod_plancuenta";
$cab[1]="cod_unidadorganizacional";
$cab[2]="cod_area";
$cab[3]="detalle";
$cab[4]="importe_presupuesto";
$cab[5]="importe";
$cab[6]="numero_factura";
$cab[7]="archivo";
$cab[8]="cod_proveedor";
$cab[9]="cod_detalleplantilla";
$cab[10]="cod_servicioauditor";
$cab[11]="cod_confretencion";
$cab[12]="cod_tipopagoproveedor";
$cab[13]="nombre_beneficiario";
$cab[14]="apellido_beneficiario";
$cab[15]="nro_cuenta_beneficiario";
$cab[16]="cod_cuentabancaria";
$cab[17]="cod_actividadproyecto";
$cab[18]="acc_num";
$cab[19]="idServicio";
$cab[20]="cod_divisionpago";
$solDet=contarSolicitudDetalle($codSolicitud);
$solDet->bindColumn('total', $contador);
while ($row = $solDet->fetch(PDO::FETCH_BOUND)) {
 $cont1=$contador;
}

$stmt1 = obtenerSolicitudesDet($codSolicitud);
editarComprobanteDetalle($codSolicitud,'cod_solicitudrecurso',$cont1,$fila,$stmt1,'solicitud_recursosdetalle',$cab,$data,$facturas);

if($flagSuccess==true){

if(!isset($_POST['control_admin'])){
 $urlList2=$urlList;
 if(isset($_POST['control_adminreg'])){
  $urlList2=$urlList3;
  if($_POST['control_adminreg']==2){
   $urlList2=$urlList5; 
  }
 }
}else{
  if($_POST['control_admin']==1){
    $urlList2=$urlList4;
  }else{
    if($_POST['control_admin']==4){
    $urlList2=$urlList7;
    }
  }
}   

  if(isset($_POST['usuario_ibnored'])){
    $q=$_POST['usuario_ibnored'];
    $r=$_POST['usuario_ibnored_rol'];
    $s=$_POST['usuario_ibnored_s'];
    $u=$_POST['usuario_ibnored_u'];
    $v=$_POST['usuario_ibnored_v'];
    showAlertSuccessError(true,"../".$urlList2."&q=".$q."&r=".$r."&s=".$s."&u=".$u."&v=".$v);  
  }else{
  showAlertSuccessError(true,"../".$urlList2); 
   }     
	
}else{
  if(isset($_POST['usuario_ibnored'])){
    $q=$_POST['usuario_ibnored'];
    $r=$_POST['usuario_ibnored_rol'];
    $s=$_POST['usuario_ibnored_s'];
    $u=$_POST['usuario_ibnored_u'];
    $v=$_POST['usuario_ibnored_v'];
   showAlertSuccessError(false,"../".$urlList2."&q=".$q."&r=".$r."&s=".$s."&u=".$u."&v=".$v);
  }else{
  showAlertSuccessError(false,"../".$urlList2);
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

function guardarDatosDistribucionGeneral($array3,$array4,$codigoSol){
  $dbh = new Conexion();
 if($array3!=0){
  for ($i=0; $i < count($array3); $i++) { 
    $area=$array3[$i]->area;
    $porcentaje=$array3[$i]->porcentaje;
    $fila=$array3[$i]->fila;
    
    $sqlInsert="INSERT INTO distribucion_gastos_solicitud_recursos (tipo_distribucion,oficina_area,porcentaje,cod_solicitudrecurso,padre_oficina_area) 
    VALUES ('2','$area','$porcentaje','$codigoSol',0)";
    $stmtInsert = $dbh->prepare($sqlInsert);
    $stmtInsert->execute();
    echo $sqlInsert;
    for ($k=0; $k < count($array4) ; $k++) { 
      if($fila==$array4[$k]->cod_fila){
        $unidad=$array4[$k]->unidad;
        $porcentaje=$array4[$k]->porcentaje;
        if($porcentaje>0){
          $sqlInsert="INSERT INTO distribucion_gastos_solicitud_recursos (tipo_distribucion,oficina_area,porcentaje,cod_solicitudrecurso,padre_oficina_area) 
          VALUES ('1','$unidad','$porcentaje','$codigoSol','$fila')";
          $stmtInsert = $dbh->prepare($sqlInsert);
          $stmtInsert->execute();   
        }
      }
    }
  }   
 }
}
?>
