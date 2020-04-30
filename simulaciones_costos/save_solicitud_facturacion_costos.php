<?php

//require_once '../layouts/bodylogin.php';
require_once 'conexion.php';
require_once 'functions.php';
require_once 'configModule.php';
ini_set('display_errors',1);
$dbh = new Conexion();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//para mostrar errores en la ejecucion

try {//recibiendo datos
    $cod_simulacion = $_POST["cod_simulacion"];
    $cod_facturacion = $_POST["cod_facturacion"];    
    $cod_unidadorganizacional = $_POST["cod_uo"];
    $cod_area = $_POST["cod_area"];
    $fecha_registro = $_POST["fecha_registro"];
    $fecha_solicitudfactura = $_POST["fecha_solicitudfactura"];
    $cod_tipoobjeto = $_POST["cod_tipoobjeto"];  
    $cod_tipopago = $_POST["cod_tipopago"];  
    $ci_estudiante = $_POST["ci_estudiante"];
    $cod_personal = $_POST["cod_personal"];//resonsable
    $razon_social = $_POST["razon_social"];    
    $nit = $_POST["nit"];
    $observaciones = $_POST["observaciones"];    
    $persona_contacto = $_POST["persona_contacto"];

    $modal_totalmontos = $_POST["modal_totalmontos"];
    $modal_numeroservicio = $_POST["modal_numeroservicio"];


    // $servicioInsert=430;
    // $CantidadInsert=1;
    // $importeInsert=$monto_pagar;
    // $DescricpionInsert=$observaciones;

    if ($cod_facturacion == 0){//insertamos       
        $nro_correlativo=obtenerCorrelativoSolicitud();//correlativo
        $stmt = $dbh->prepare("INSERT INTO solicitudes_facturacion(cod_simulacion_servicio,cod_unidadorganizacional,cod_area,fecha_registro,fecha_solicitudfactura,cod_tipoobjeto,cod_tipopago,cod_cliente,cod_personal,razon_social,nit,observaciones,nro_correlativo,cod_estado,persona_contacto) 
        values ('$cod_simulacion','$cod_unidadorganizacional','$cod_area','$fecha_registro','$fecha_solicitudfactura','$cod_tipoobjeto','$cod_tipopago','$cod_cliente','$cod_personal','$razon_social','$nit','$observaciones','$nro_correlativo',1,'$persona_contacto')");
        $flagSuccess=$stmt->execute();        
        if($flagSuccess){
            //antes de insertar sacamos el codigo de la solicitud para el detalle
            $stmt = $dbh->prepare("SELECT codigo from solicitudes_facturacion where cod_simulacion_servicio=$cod_simulacion ORDER BY codigo desc LIMIT 1");
            $stmt->execute();
            while ($rowPre = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  $cod_facturacion=$rowPre['codigo'];
            }
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
                }
                if($servicioInsert!=0 || $servicioInsert!=""){
                    // echo " servicio:".$servicioInsert."<br>";
                    // echo " cantida:".$CantidadInsert."<br>";
                    // echo " importe:".$importeInsert."<br>";
                    // echo " Descricpion:".$DescricpionInsert."<br>";
                    $stmt = $dbh->prepare("INSERT INTO solicitudes_facturaciondetalle(cod_solicitudfacturacion,cod_claservicio,cantidad,precio,descripcion_alterna,descuento_por,descuento_bob,tipo_item) 
                    values ('$cod_facturacion','$servicioInsert','$CantidadInsert','$importeInsert','$DescricpionInsert','$descuento_por_Insert','$descuento_bob_Insert',1)");
                    $flagSuccess=$stmt->execute();
                }
            }            
        }
        showAlertSuccessError($flagSuccess,$urlSolicitudfactura."&cod=".$cod_simulacion);
        //$stmt->debugDumpParams();
    } else {//update
        $stmt = $dbh->prepare("UPDATE solicitudes_facturacion set fecha_registro='$fecha_registro',fecha_solicitudfactura='$fecha_solicitudfactura',cod_tipopago='$cod_tipopago',razon_social='$razon_social',nit='$nit',observaciones='$observaciones',persona_contacto='$persona_contacto'
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
                if(isset($_POST["servicio".$i])){
                    $servicioInsert=$_POST["servicio_a".$i];
                    $CantidadInsert=$_POST["cantidad_a".$i];
                    $importeInsert=$_POST["modal_importe".$i];                    
                    $DescricpionInsert=$_POST["descripcion_alterna".$i];
                    $descuento_por_Insert=$_POST["descuento_por".$i];
                    $descuento_bob_Insert=$_POST["descuento_bob".$i];
                }
                // echo " servicio:".$servicioInsert."<br>";
                    // echo " cantida:".$CantidadInsert."<br>";
                    // echo " importe:".$importeInsert."<br>";
                    // echo " Descricpion:".$DescricpionInsert."<br>";
                if($servicioInsert!=0 || $servicioInsert!=""){                    
                    $stmt = $dbh->prepare("INSERT INTO solicitudes_facturaciondetalle(cod_solicitudfacturacion,cod_claservicio,cantidad,precio,descripcion_alterna,descuento_por,descuento_bob,tipo_item) 
                    values ('$cod_facturacion','$servicioInsert','$CantidadInsert','$importeInsert','$DescricpionInsert','$descuento_por_Insert','$descuento_bob_Insert',1)");
                    $flagSuccess=$stmt->execute();
                }
            }

        }      
        showAlertSuccessError($flagSuccess,$urlSolicitudfactura."&cod=".$cod_simulacion);
    }//si es insert o update
    
} catch(PDOException $ex){
    //manejar error
    echo "Un error ocurrio".$ex->getMessage();

}
?>