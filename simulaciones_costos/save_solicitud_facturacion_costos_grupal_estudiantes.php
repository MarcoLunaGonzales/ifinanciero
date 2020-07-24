<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';
ini_set('display_errors',1);
session_start();
$globalUser=$_SESSION["globalUser"];
$dbh = new Conexion();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//para mostrar errores en la ejecucion

try {//recibiendo datos    
    $codigo_alterno = $_POST["Codigo_alterno"];
    // $cod_simulacion = $_POST["IdCurso"];
    // $ci_estudiante = $_POST["ci_estudiante"];
    // $codigo_alterno = 0;
    $cod_simulacion = 0;
    $ci_estudiante = 0;

    $cod_facturacion = $_POST["cod_facturacion"];    
    $cod_unidadorganizacional = $_POST["cod_uo"];
    $cod_area = $_POST["cod_area"];
    $fecha_registro = $_POST["fecha_registro"];
    $fecha_solicitudfactura = $_POST["fecha_solicitudfactura"];    
    if(isset($_POST["cod_tipoobjeto"])){
        $cod_tipoobjeto = $_POST["cod_tipoobjeto"];
    }else $cod_tipoobjeto=0;
    $cod_tipopago = $_POST["cod_tipopago"];
    $cod_cliente = $_POST["cod_cliente"];
    $cod_personal = $_POST["cod_personal"];//resonsable
    $razon_social = $_POST["razon_social"];    
    $nit = $_POST["nit"];
    $observaciones = $_POST["observaciones"];    
    $observaciones_2 = $_POST["observaciones_2"];        
    $modal_totalmontos = $_POST["modal_totalmontos"];
    $modal_numeroservicio = $_POST["modal_numeroservicio"];

    
    if(isset($_POST['persona_contacto'])){
        $persona_contacto = $_POST["persona_contacto"];
    }else{
        $persona_contacto = 0;
    }
    if(isset($_POST['q'])){
        $cod_personal=$_POST['q'];
    }
    if (isset($_POST["dias_credito"]))$dias_credito = $_POST["dias_credito"];
    else $dias_credito = '';
    // $servicioInsert=430;
    // $CantidadInsert=1;
    // $importeInsert=$monto_pagar;
    // $DescricpionInsert=$observaciones;
    // $estado_ibnorca=0;
    if ($cod_facturacion == 0){//insertamos       
        $nro_correlativo=obtenerCorrelativoSolicitud();//correlativo
        $stmt = $dbh->prepare("INSERT INTO solicitudes_facturacion(cod_simulacion_servicio,cod_unidadorganizacional,cod_area,fecha_registro,fecha_solicitudfactura,cod_tipoobjeto,cod_tipopago,cod_cliente,cod_personal,razon_social,nit,observaciones,observaciones_2,nro_correlativo,persona_contacto,cod_estadosolicitudfacturacion,codigo_alterno,tipo_solicitud,dias_credito) 
        values ('$cod_simulacion','$cod_unidadorganizacional','$cod_area','$fecha_registro','$fecha_solicitudfactura','$cod_tipoobjeto','$cod_tipopago','$cod_cliente','$cod_personal','$razon_social','$nit','$observaciones','$observaciones_2','$nro_correlativo','$persona_contacto',1,'$codigo_alterno',7,'$dias_credito')");//7 tipo capacitacion grupal
        $flagSuccess=$stmt->execute();
        if($flagSuccess){
            //antes de insertar sacamos el codigo de la solicitud para el detalle
            $stmt = $dbh->prepare("SELECT codigo from solicitudes_facturacion where cod_simulacion_servicio=$cod_simulacion ORDER BY codigo desc LIMIT 1");
            $stmt->execute();
            while ($rowPre = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  $cod_facturacion=$rowPre['codigo'];
            }
            // //insertamos la cadena de estudiantes
            // $IdCurso = $_POST["IdCurso"];
            // $ci_estudiante = $_POST["ci_estudiante"];
            // $array_idcurso = explode(',',$IdCurso);
            // $array_ci_estudiante = explode(',',$ci_estudiante);
            // $lan_ci=sizeof($array_idcurso);//filas si lo hubiese
            // for($p=0;$p<$lan_ci;$p++){
            //     $id_curso_x=$array_idcurso[$p];
            //     $ci_estudiante_x=$array_ci_estudiante[$p];
            //     $stmtGrupal = $dbh->prepare("INSERT INTO solicitudes_facturacion_grupal(cod_solicitudfacturacion,cod_curso,ci_estudiante) 
            //         values ($cod_facturacion,$id_curso_x,$ci_estudiante_x)");
            //     $stmtGrupal->execute();
            // }
            for ($i=1;$i<=$modal_numeroservicio-1;$i++){
                $servicioInsert="";
                $CantidadInsert="";
                $importeInsert="";
                $DescricpionInsert="";            
                // echo "i:".$i;
                if(isset($_POST["servicio".$i])){
                    $servicioInsert=$_POST["servicio_a".$i];
                    $CantidadInsert=$_POST["cantidad_a".$i];
                    $importeInsert=$_POST["modal_importe".$i];
                    $DescricpionInsert=$_POST["descripcion_alterna".$i];
                    $descuento_por_Insert=$_POST["descuento_por".$i];
                    $descuento_bob_Insert=$_POST["descuento_bob".$i];
                    $cod_curso_x=$_POST["cod_curso_x".$i];
                    $ci_estudiante=$_POST["ci_estudiante".$i];
                    $importe_a_pagar_Insert=$_POST["importe_a_pagar".$i];
                    // $monto_pagado=$_POST["modal_importe_pagado_dos_a".$i];
                }
                if($servicioInsert!=0 || $servicioInsert!=""){
                    // echo " servicio:".$servicioInsert."<br>";
                    // echo " cantida:".$CantidadInsert."<br>";
                    // echo " importe:".$importeInsert."<br>";
                    // echo " Descricpion:".$DescricpionInsert."<br>";
                    $stmt = $dbh->prepare("INSERT INTO solicitudes_facturaciondetalle(cod_solicitudfacturacion,cod_claservicio,cantidad,precio,descripcion_alterna,descuento_por,descuento_bob,tipo_item,cod_curso,ci_estudiante) 
                    values ('$cod_facturacion','$servicioInsert','$CantidadInsert','$importe_a_pagar_Insert','$DescricpionInsert','$descuento_por_Insert','$descuento_bob_Insert',1,$cod_curso_x,$ci_estudiante)");
                    $flagSuccess=$stmt->execute();                    
                }
            }            
            //======================================
            //para distribucion de tipo de pagos y areas
            $tipo_solicitud=7;
            require_once '../simulaciones_servicios/save_distribucion_montos_solfac.php';             
            //borramos los archivos
            $sqlDel="DELETE FROM archivos_adjuntos_solicitud_facturacion where cod_solicitud_facturacion=$cod_facturacion";
            $stmtDel = $dbh->prepare($sqlDel);
            $stmtDel->execute();
            //subir archivos al servidor
            //Como el elemento es un arreglos utilizamos foreach para extraer todos los valores
            require_once '../simulaciones_servicios/insertar_archivosadjuntos.php';
                    
        
        }   date_default_timezone_set('America/La_Paz');
            $fechaHoraActual=date("Y-m-d H:i:s");
            $idTipoObjeto=2709;
            $idObjeto=2726; //regristado
            $obs="Registro de Solicitud Facturación";
             if(isset($_POST['q'])){
                $q=$_POST['q'];
                actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$q,$cod_facturacion,$fechaHoraActual,$obs);
             }else{
               actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$cod_facturacion,$fechaHoraActual,$obs);
            }


            if(isset($_POST['q'])){
              $q=$_POST['q'];
              $s=$_POST['s'];
              $u=$_POST['u'];
              $v=$_POST['r'];
              showAlertSuccessError($flagSuccess,"../".$urlListSol."&q=".$q."&s=".$s."&u=".$u."&v=".$v);
            }else{
              showAlertSuccessError($flagSuccess,"../".$urlListSol);
            }

        
            // if(isset($_POST['q'])){
            //   $q=$_POST['q'];
            //   $r=$_POST['r'];          
            //   showAlertSuccessError($flagSuccess,"../".$urlListSol."&q=".$q."&r=".$r);
            // }else{
            //   showAlertSuccessError($flagSuccess,"../".$urlListSol);
            // } 


        
        //$stmt->debugDumpParams();
    }else {//update
        //actualizamos los campos estaticos
        $stmt = $dbh->prepare("UPDATE solicitudes_facturacion set cod_unidadorganizacional='$cod_unidadorganizacional',cod_area='$cod_area',fecha_registro='$fecha_registro',fecha_solicitudfactura='$fecha_solicitudfactura',cod_tipoobjeto='$cod_tipoobjeto',cod_tipopago='$cod_tipopago',cod_cliente='$cod_cliente',cod_personal='$cod_personal',razon_social='$razon_social',nit='$nit',observaciones='$observaciones',observaciones_2='$observaciones_2',persona_contacto='$persona_contacto',dias_credito = '$dias_credito'
        where codigo = $cod_facturacion");      
        $flagSuccess=$stmt->execute();
        if($flagSuccess){
            //borramos los datos y insertados en detalle
            $stmtDelete = $dbh->prepare("DELETE from solicitudes_facturaciondetalle where cod_solicitudfacturacion= $cod_facturacion");      
            $stmtDelete->execute();
            for ($i=1;$i<=$modal_numeroservicio-1;$i++){
                $servicioInsert="";
                $CantidadInsert="";
                $importeInsert="";
                $DescricpionInsert="";            
                // echo "i:".$i;
                if(isset($_POST["servicio".$i])){
                    $servicioInsert=$_POST["servicio_a".$i];
                    $CantidadInsert=$_POST["cantidad_a".$i];
                    $importeInsert=$_POST["modal_importe".$i];                
                    $DescricpionInsert=$_POST["descripcion_alterna".$i];
                    $descuento_por_Insert=$_POST["descuento_por".$i];
                    $descuento_bob_Insert=$_POST["descuento_bob".$i];
                    $cod_curso_x=$_POST["cod_curso_x".$i];
                    $ci_estudiante=$_POST["ci_estudiante".$i];
                    $importe_a_pagar_Insert=$_POST["importe_a_pagar".$i];
                    // $monto_pagado=$_POST["modal_importe_pagado_dos_a".$i];
                }
                if($servicioInsert!=0 || $servicioInsert!=""){
                    // echo " servicio:".$servicioInsert."<br>";
                    // echo " cantida:".$CantidadInsert."<br>";
                    // echo " importe:".$importeInsert."<br>";
                    // echo " Descricpion:".$DescricpionInsert."<br>";                    
                    $stmt = $dbh->prepare("INSERT INTO solicitudes_facturaciondetalle(cod_solicitudfacturacion,cod_claservicio,cantidad,precio,descripcion_alterna,descuento_por,descuento_bob,tipo_item,cod_curso,ci_estudiante) 
                        values ('$cod_facturacion','$servicioInsert','$CantidadInsert','$importe_a_pagar_Insert','$DescricpionInsert','$descuento_por_Insert','$descuento_bob_Insert',1,$cod_curso_x,$ci_estudiante)");
                    $flagSuccess=$stmt->execute();                    
                }
            }
            
            
            //======================================
            //para distribucion de tipo de pagos y areas
            $tipo_solicitud=7;
            require_once '../simulaciones_servicios/save_distribucion_montos_solfac.php';
            //borramos los archivos
            // $sqlDel="DELETE FROM archivos_adjuntos_solicitud_facturacion where cod_solicitud_facturacion=$cod_facturacion";
            // $stmtDel = $dbh->prepare($sqlDel);
            // $stmtDel->execute();
            //subir archivos al servidor
            //Como el elemento es un arreglos utilizamos foreach para extraer todos los valores
            require_once '../simulaciones_servicios/insertar_archivosadjuntos.php';
        }
        if(isset($_POST['q'])){
          $q=$_POST['q'];
          $s=$_POST['s'];
          $u=$_POST['u'];
          $v=$_POST['r'];
          showAlertSuccessError($flagSuccess,"../".$urlListSol."&q=".$q."&s=".$s."&u=".$u."&v=".$v);
        }else{
          showAlertSuccessError($flagSuccess,"../".$urlListSol);
        }
    }//si es insert o update
 
} catch(PDOException $ex){
    //manejar error
    echo "Un error ocurrio".$ex->getMessage();

}
?>