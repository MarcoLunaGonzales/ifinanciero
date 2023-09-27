<?php
require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codigo=$_GET["cod"];
$estado=$_GET["estado"];
session_start();

$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];

$fechaHoraActual=date("Y-m-d H:i:s");
if(isset($_GET["conta"])){
  $urlList2=$urlList4;
}

$datosSolicitud=obtenerDatosSolicitudRecursos($codigo);
$correoPersonal=$datosSolicitud['email_empresa'];
$descripcionEstado=obtenerNombreEstadoSol($estado);
if($correoPersonal!=""){
  $envioCorreoPersonal=enviarCorreoSimple($correoPersonal,'CAMBIO DE ESTADO - SOLICITUD DE RECURSOS, Nº : '.$datosSolicitud['numero'],'Estimado(a) '.$datosSolicitud['solicitante'].', el sistema IFINANCIERO le notifica que su Solicitud de Recursos cambio del estado <b>'.$datosSolicitud['estado'].'</b> a <b>'.$descripcionEstado.'</b>. <br> Personal que realizo el cambio:'.namePersonalCompleto($globalUser)."<br>Numero de Solicitud:".$datosSolicitud['numero']."<br>Estado Anterior: <b>".$datosSolicitud['estado']."</b><br>Estado Actual: <b>".$descripcionEstado."</b><br><br>Saludos - IFINANCIERO");  
}


if($estado==10||$estado==11||$estado==12){
   
   if($estado==12){
      $urlList2=$urlList3;
   }else{
      $urlList2=$urlList4;  
   }
  
   $estado=$estado-10;

   $revisado_contabilidad_at = date('Y-m-d H:i:s');
   $revisado_contabilidad_by = $globalUser;
   $sqlUpdate="UPDATE solicitud_recursos SET revisado_contabilidad = '$estado',
               revisado_contabilidad_at = '$revisado_contabilidad_at',
               revisado_contabilidad_by = '$revisado_contabilidad_by'
               WHERE codigo='$codigo'";

   $stmtUpdate = $dbh->prepare($sqlUpdate);
   $flagSuccess=$stmtUpdate->execute();
}else{
   //AQUI ENVIAMOS LA SR A PROYECTOS
   $oficinaSR=obtenerUnidadSolicitanteRecursos($codigo);
   $areaSR=obtenerAreaSolicitanteRecursos($codigo);

   $stringOficinasProyectosExt=obtenerValorConfiguracion(69);
   $stringAreasProyectosExt=obtenerValorConfiguracion(65);

   $arrayOficinasProyectos = explode(",", $stringOficinasProyectosExt);
   $arrayAreasProyectos = explode(",", $stringAreasProyectosExt);

   if(in_array($usuario, $arrayAdmins)){
      setcookie("global_admin_cargo", 1);   
   }else{
      setcookie("global_admin_cargo", 0);   
   }

   $banderaProyectosExt=0;
   if(in_array($oficinaSR, $arrayOficinasProyectos)){
      $banderaProyectosExt=1;
   }

   if(in_array($areaSR, $arrayAreasProyectos)){
      $banderaProyectosExt=1; 
   }
   if($banderaProyectosExt==1 || obtenerDetalleRecursosSIS($codigo)>0){ 
      if(isset($_GET["reg"])){
         if($estado==4&&$_GET['reg']!=2){
            $estado=7;
         //enviar correos sis
         $datoInstancia=obtenerCorreosInstanciaEnvio(2);
         $correos=implode(",",$datoInstancia[0]);
         $nombres=implode(",",$datoInstancia[1]); 
          
         $envioCorreo=enviarCorreoSimple($correos,'Nueva SR para Proyectos de Financiamiento. Nº : '.$datosSolicitud['numero'],'Estimado(a) '.$nombres.', el sistema IFinanciero le notifica que tiene una nueva SR para ser aprobada. <br> Solicitante:'.$datosSolicitud['solicitante']."<br>Nro: ".$datosSolicitud['numero']."<br><br>Saludos - IFinanciero");
         }   
      }  
   }else{
      $montoCaja=obtenerValorConfiguracion(85);
      $montoDetalleSoliditud=obtenerSumaDetalleSolicitud($codigo);
      if($montoDetalleSoliditud<=$montoCaja&&$estado==4){
         $estado=3;
      }
   }
    

   $sqlUpdate="UPDATE solicitud_recursos SET  cod_estadosolicitudrecurso=$estado where codigo=$codigo";
   if($estado==8){
      //enviar propuestas para la actualizacion de ibnorca
      $fechaHoraActual=date("Y-m-d H:i:s");
      $idTipoObjeto=2708;
      $idObjeto=2725; //regristado
      $obs="Solicitud Contabilizada";
      if(isset($_GET['u'])){
         $u=$_GET['u'];
         actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$u,$codigo,$fechaHoraActual,$obs);    
      }else{
         actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$codigo,$fechaHoraActual,$obs);    
      }
   }

   if(isset($_GET['obs'])){
      $obs=$_GET['obs'];
      /***************************************************************/
      //             ENVIO DE CORREO - RECHAZAR SOLICITUD            //
      /***************************************************************/
      $sql = "SELECT sr.cod_personal, 
                     sr.numero as nro_solicitud, 
                     CONCAT(p.primer_nombre, ' ', p.paterno, ' ', p.materno) as nombre_personal, 
                     DATE_FORMAT(sr.fecha, '%d-%m-%Y') as fecha_solicitud,
                     p.email_empresa as email_personal
               FROM solicitud_recursos sr
               LEFT JOIN personal p ON p.codigo = sr.cod_personal
               WHERE sr.codigo = '$codigo'";
      $stmt = $dbh->prepare($sql);
      $stmt->execute();
      $registro = $stmt->fetch(PDO::FETCH_ASSOC);
      $nombre_personal = $registro['nombre_personal'];
      $nro_solicitud   = $registro['nro_solicitud'];
      $fecha_solicitud = $registro['fecha_solicitud'];
      $motivo          = $obs;
      $personal_email  = $registro['email_personal']; // "roalmollericona@gmail.com";
      $personal_email_copia = ""; // "mluna@minkasoftware.com"
      $asunto          = "Rechazo de Solicitud de Recursos Nro. ".$nro_solicitud;
      $usuario_rechazo = $_SESSION['globalNameUser'];
      enviarCorreoSolicitud($asunto, $nombre_personal, $nro_solicitud, $fecha_solicitud, $motivo, $personal_email, $personal_email_copia, $usuario_rechazo);
      // echo $nro_solicitud;
      // exit;
      /***************************************************************/

      if(isset($_GET["ll"])){
         $sqlUpdate="UPDATE solicitud_recursos SET  cod_estadosolicitudrecurso=$estado,glosa_estado=CONCAT(glosa_estado,'####','$obs') where codigo=$codigo";  
      }else{
         $sqlUpdate="UPDATE solicitud_recursos SET  cod_estadosolicitudrecurso=$estado,glosa_estado='$obs' where codigo=$codigo";
      }   
   }

   $stmtUpdate = $dbh->prepare($sqlUpdate);
   $flagSuccess=$stmtUpdate->execute();

   if($estado!=1){
  	   //actualziar los estados del servidor ibnorca
  	   if($estado==4){
         //enviar propuestas para la actualizacion de ibnorca
         $fechaHoraActual=date("Y-m-d H:i:s");
         $idTipoObjeto=2708;
         $idObjeto=2722; //regristado
         $obs="En Aprobacion Solicitud";
         if(isset($_GET['q'])){
            $u=$_GET['q'];
            actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$u,$codigo,$fechaHoraActual,$obs);    
         }else{
            actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$codigo,$fechaHoraActual,$obs);    
         } 
     	}else{
         if($estado==6){
            //enviar propuestas para la actualizacion de ibnorca
            $fechaHoraActual=date("Y-m-d H:i:s");
            $idTipoObjeto=2708;
            $idObjeto=2822; //regristado
            $obs="En Pre Aprobacion Solicitud";
            if(isset($_GET['u'])){
               $u=$_GET['u'];
               actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$u,$codigo,$fechaHoraActual,$obs);    
            }else{
               actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$codigo,$fechaHoraActual,$obs);    
            }   
         }else{
            if($estado==7){
               //enviar propuestas para la actualizacion de ibnorca
               $fechaHoraActual=date("Y-m-d H:i:s");
               $idTipoObjeto=2708;
               $idObjeto=3107; //ESTADO PARA PROYECTO SIS
               $obs="Enviado a Gestión Proyectos";
               if(isset($_GET['u'])){
                  $u=$_GET['u'];
                  actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$u,$codigo,$fechaHoraActual,$obs);    
               }else{
                  actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$codigo,$fechaHoraActual,$obs);    
               } 
            }else{
               if($estado==5){
                  //enviar propuestas para la actualizacion de ibnorca
                  $fechaHoraActual=date("Y-m-d H:i:s");
                  $idTipoObjeto=2708;
                  $idObjeto=2725; //regristado
                  $obs="Solicitud Contabilizada";
                  if(isset($_GET['u'])){
                     $u=$_GET['u'];
                     actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$u,$codigo,$fechaHoraActual,$obs);    
                  }else{
                     actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$codigo,$fechaHoraActual,$obs);    
                  }
               }else{
                  if($estado==3){
                     //se envia directo costos menores a 1000
                     //enviar propuestas para la actualizacion de ibnorca
                     $fechaHoraActual=date("Y-m-d H:i:s");
                     $idTipoObjeto=2708;
                     $idObjeto=2722; //regristado
                     $obs="En Aprobacion Solicitud";
                     if(isset($_GET['q'])){
                        $u=$_GET['q'];
                        actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$u,$codigo,$fechaHoraActual,$obs);    
                     }else{
                        actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$codigo,$fechaHoraActual,$obs);   
                     }
                  }   
               }
            }
         }        
      }
   }else{
   	//enviar propuestas para la actualizacion de ibnorca
      $fechaHoraActual=date("Y-m-d H:i:s");
      $idTipoObjeto=2708;
      $idObjeto=2721; //regristado
      $obs="Registro de Solicitud";
      if(isset($_GET['u'])){
         $u=$_GET['u'];
         actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$u,$codigo,$fechaHoraActual,$obs);
      }else{
         actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$codigo,$fechaHoraActual,$obs);
      } 
   }
}  //else Estado Contabilidad



if(isset($_GET['q'])){
  $q=$_GET['q'];
  $s=$_GET['s'];
  $u=$_GET['u'];
  $v=$_GET['v'];
}
$urlc="";
if(isset($_GET['admin'])){
  $urlList2=$urlList;
  if(isset($_GET['q'])){
    $urlc="&q=".$q."&s=".$s."&u=".$u."&v=".$v;  
  }  
  if(isset($_GET['reg'])){
    $urlList2=$urlList3;
    if($_GET['reg']==2){
     $urlList2=$urlList5;
    }
    
  }
}else{
  if(isset($_GET['q'])){ 
    $urlc="&q=".$q."&s=".$s."&u=".$u;
    if(isset($_GET['r'])){
       $urlc=$urlc."&r=".$_GET['r'];
    }
  }
}
if(isset($_GET["ladmin"])){
  $urlList2=$urlList2Auxiliar;
}

if(isset($_GET["conta_men"])){
  $urlList2=$urlList7;
}

if(isset($_GET['q'])){
	$q=$_GET['q'];
  $s=$_GET['s'];
  $u=$_GET['u'];
  $v=$_GET['v'];
  if($flagSuccess==true){
	showAlertSuccessError(true,"../".$urlList2.$urlc);	
   }else{
	showAlertSuccessError(false,"../".$urlList2.$urlc);
   }
}else{
	if($flagSuccess==true){
	showAlertSuccessError(true,"../".$urlList2);	
   }else{
	showAlertSuccessError(false,"../".$urlList2);
   }
}

?>