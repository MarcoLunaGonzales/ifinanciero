<?php
require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();


session_start();

$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];

$codigo=$_GET["codigo"];
$estado=$_GET["estado"];

$iEstado=obtenerEstadoIfinancieroPropuestas($estado);
$fechaHoraActual=date("Y-m-d H:i:s");

if(obtenerServicioPorPropuesta($codigo)!=0){
  $iEstado=5;
  $estado=2718;
}

$sqlUpdate="UPDATE simulaciones_servicios SET  cod_estadosimulacion=$iEstado where codigo=$codigo";
$stmtUpdate = $dbh->prepare($sqlUpdate);
$flagSuccess=$stmtUpdate->execute();
$id_perfil=$_GET["id_perfil"];


    if($estado==5 || $estado==2718){    
        //crear la solicitud
        $simulacion=obtenerDatosCompletosPorSimulacionServicios($codigo);
        while ($row = $simulacion->fetch(PDO::FETCH_ASSOC)) {
            $IdArea=$row['cod_area'];
            $IdOficina=$row['unidad_serv'];
            if($IdOficina==0||$IdOficina==""){
               $IdOficina=$row['cod_unidadorganizacional'];
            }
            $IdTipo=$row['id_tiposervicio'];
            $IdCliente=$row['cod_cliente'];
            $codObjeto=$row['cod_objetoservicio'];

            $Descripcion=obtenerServiciosClaServicioTipoNombre($IdTipo)."  ".obtenerServiciosTipoObjetoNombre($codObjeto);
            $IdUsuarioRegistro=$row['cod_responsable'];
            $fecharegistro=date("Y-m-d");
            if(!($row['idServicio']>0)){
             $idServicio=obtenerCodigoServicioIbnorca();
            // Prepare
            
            //TEMPORALMENTE PONEMOS EL USUARIO DE CINTYA ZARATE LOS OTROS ESTAN CON PROBLEMAS. 
            $IdUsuarioRegistroXXX=177;
            $sqlInsertServicio="INSERT INTO ibnorca.servicios (idServicio,IdArea,IdOficina,IdTipo,IdCliente,Descripcion,IdUsuarioRegistro,fecharegistro,IdPropuesta) 
            VALUES ('$idServicio','$IdArea','$IdOficina','$IdTipo','$IdCliente','$Descripcion','$IdUsuarioRegistroXXX','$fecharegistro','$codigo')";
            $stmt = $dbh->prepare($sqlInsertServicio);
            $flagSuccessServicio=$stmt->execute();
            
            //echo $sqlInsertServicio." ".$flagSuccessServicio;
            
            // Bind
            if($flagSuccessServicio==true){
            //enviar propuestas para la actualizacion de ibnorca
            $fechaHoraActual=date("Y-m-d H:i:s");
            $idTipoObjeto=195;
            $idObjeto=204; //regristado
            $obs="En ejecución";
            //id de perfil para cambio de estado en ibnorca
      
            if(isset($_GET['u'])){
              $id_perfil=$_GET['u'];
              actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$idServicio,$fechaHoraActual,$obs);
            }else{
              actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$id_perfil,$idServicio,$fechaHoraActual,$obs);
            }
            
            
            $stmt2 = $dbh->prepare("UPDATE simulaciones_servicios SET idServicio=$idServicio where codigo=$codigo");
            $flagSuccess2=$stmt2->execute();  
            }else{
              $flagSuccess=$flagSuccessServicio;
            }

            }
            //enviar propuestas para la actualizacion de ibnorca
            $fechaHoraActual=date("Y-m-d H:i:s");
            $idTipoObjeto=2707;
            $idObjeto=2718; //regristado
            $obs="Ejecutada";
            if(!isset($_GET['u'])){
             actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$codigo,$fechaHoraActual,$obs);    
            }else{
             $u=$_GET["u"];
             actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$u,$codigo,$fechaHoraActual,$obs);     
            }
        }
        //fin crear servicio
    }else{
    
        //enviar propuestas para la actualizacion de ibnorca
        $fechaHoraActual=date("Y-m-d H:i:s");
        $idTipoObjeto=2707;
        $idObjeto=$estado; //variable desde get
        $obs=$_GET['obs']; //$obs="Registro de propuesta";
        if($id_perfil==0){
          actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$codigo,$fechaHoraActual,$obs);
        }else{
          actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$id_perfil,$codigo,$fechaHoraActual,$obs);
        }       
    
    }


?>
