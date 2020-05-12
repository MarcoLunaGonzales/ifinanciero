<?php

//require_once '../layouts/bodylogin.php';
require_once 'conexion.php';
require_once 'functions.php';
require_once 'configModule.php';
ini_set('display_errors',1);
$globalUser=$_SESSION["globalUser"];
$dbh = new Conexion();

$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//para mostrar errores en la ejecucion

try {
    $codigo_alterno = 'SIN CODIGO';
    $cod_simulacion = $_POST["cod_simulacion"];
    $cod_facturacion = $_POST["cod_facturacion"];    
    $cod_unidadorganizacional = $_POST["cod_uo"];
    $cod_area = $_POST["cod_area"];
    $fecha_registro = $_POST["fecha_registro"];
    $fecha_solicitudfactura = $_POST["fecha_solicitudfactura"];
        
    $cod_cliente = $_POST["cod_cliente"];
    $cod_personal = $_POST["cod_personal"];
    $razon_social = $_POST["razon_social"];
    $nit = $_POST["nit"];
    $observaciones = $_POST["observaciones"];
    $observaciones_2 = $_POST["observaciones_2"];
    $persona_contacto = $_POST["persona_contacto"];
    

    $modal_totalmontos = $_POST["modal_totalmontos"];
    $modal_numeroservicio = $_POST["modal_numeroservicio"];
    if(isset($_POST["cod_tipoobjeto"])){
        $cod_tipoobjeto = $_POST["cod_tipoobjeto"];
    }else $cod_tipoobjeto=0;
    if(isset($_POST["cod_tipopago"])){
        $cod_tipopago = $_POST["cod_tipopago"];
    }else $cod_tipopago=0;
    if(isset($_POST['u'])){
        $cod_personal=$_POST['u'];
    }

        $nro_correlativo=obtenerCorrelativoSolicitud();//correlativo

        $stmt = $dbh->prepare("INSERT INTO solicitudes_facturacion(cod_simulacion_servicio,cod_unidadorganizacional,cod_area,fecha_registro,fecha_solicitudfactura,cod_tipoobjeto,cod_tipopago,cod_cliente,cod_personal,razon_social,nit,observaciones,observaciones_2,nro_correlativo,cod_estado,persona_contacto,cod_estadosolicitudfacturacion,codigo_alterno,tipo_solicitud) 
        values ('$cod_simulacion','$cod_unidadorganizacional','$cod_area','$fecha_registro','$fecha_solicitudfactura','$cod_tipoobjeto','$cod_tipopago','$cod_cliente','$cod_personal','$razon_social','$nit','$observaciones','$observaciones_2','$nro_correlativo',1,'$persona_contacto',1,'$codigo_alterno',5)");//5 tipo solicitud de normas
        $flagSuccess=$stmt->execute();
         // $flagSuccess=true;
        if($flagSuccess){
            //  sacamos el codigo insertado
           $stmt = $dbh->prepare("SELECT codigo from solicitudes_facturacion where cod_simulacion_servicio=$cod_simulacion ORDER BY codigo desc LIMIT 1");
           $stmt->execute();           
            while ($rowPre = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  $cod_facturacion=$rowPre['codigo'];
            }
            //actualuzamos el codigo de solicitud a la base ibnorca
            for ($i=1;$i<=$modal_numeroservicio-1;$i++){
                $servicioInsert="";
                $CantidadInsert="";
                $importeInsert="";
                $DescricpionInsert="";
                // echo "i:".$i;

                if(isset($_POST["servicio".$i])){
                    
                    $cod_serv_a=$_POST["cod_serv_tiposerv_a".$i];//idItem
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
                    // echo " descuento_por_:".$descuento_por_Insert."<br>";
                    // echo " descuento_bob_:".$descuento_bob_Insert."<br>";
                    $stmtIbnorca = $dbh->prepare("UPDATE ibnorca.ventanormas set idSolicitudfactura=$cod_facturacion where IdVentaNormas=$cod_serv_a");//5 tipo solicitud de normas
                    $flagSuccess=$stmtIbnorca->execute();


                    $stmt = $dbh->prepare("INSERT INTO solicitudes_facturaciondetalle(cod_solicitudfacturacion,cod_claservicio,cantidad,precio,descripcion_alterna,descuento_por,descuento_bob,tipo_item) 
                    values ('$cod_facturacion','$servicioInsert','$CantidadInsert','$importeInsert','$DescricpionInsert','$descuento_por_Insert','$descuento_bob_Insert',1)");
                    $flagSuccess=$stmt->execute();
                }
            }
                    
        }
        //enviar propuestas para la actualizacion de ibnorca
        $fechaHoraActual=date("Y-m-d H:i:s");
        $idTipoObjeto=2709;
        $idObjeto=2726; //regristado
        $obs="Registro de Solicitud Facturación";
        if(isset($_POST['u'])){
            $u=$_POST['u'];
            actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$u,$cod_facturacion,$fechaHoraActual,$obs);
        }else{
           actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$cod_facturacion,$fechaHoraActual,$obs);
        }
        if(isset($_POST['usuario_ibnored'])){
          $q=$_POST['usuario_ibnored'];
          $s=$_POST['usuario_ibnored_s'];
          $u=$_POST['usuario_ibnored_u'];
          $v=$_POST['usuario_ibnored_v'];
          showAlertSuccessError($flagSuccess,$urlListSolicitud_facturacion_normas."&q=".$q."&s=".$s."&u=".$u."&v=".$v);
        }else{
          showAlertSuccessError($flagSuccess,$urlListSolicitud_facturacion_normas);  
        }
        

        //$stmt->debugDumpParams();
    
    
    } catch(PDOException $ex){
        //manejar error
        echo "Un error ocurrio".$ex->getMessage();

    }
?>