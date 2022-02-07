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

//datos de cabecera
$cantidadFilas=$_POST['cantidad_proveedores'];//total de intems
$tipo_pago=2;//trasnfer

$codigoCuentasAux=$_POST['codigo_proveedores'];//total de intems

$array_cuentasAuxiliares=explode(",", $codigoCuentasAux);
// echo $codigoCuentasAux."***";

$nombre_lote=$_POST['nombre_lote'];
$porFecha = explode("/", $_POST['fecha_pago']);
$fecha_pago=$porFecha[2]."-".$porFecha[1]."-".$porFecha[0];
$observaciones_pago=$_POST['observaciones_pago'];

$cod_pagolote=obtenerCodigoPagoLote();
$sqlInsert="INSERT INTO pagos_lotes (codigo,nombre,abreviatura, fecha,cod_comprobante,cod_estadopagolote,cod_ebisalote,cod_estadoreferencial) 
VALUES ('".$cod_pagolote."','".$nombre_lote."','','".$fecha_pago."','0',1,0,1)";
$stmtInsert = $dbh->prepare($sqlInsert);
$stmtInsert->execute();
//ya se insertó la cebecera
$totalPago=0;
$contadorCheque=0;$contadorChequeFilas=0;
$flagSuccess=false;
for ($i=0; $i <count($array_cuentasAuxiliares) ; $i++) { //
  $codigo_cuentaauxiliar=$array_cuentasAuxiliares[$i];
  // $nameCuentaAuxiliar=nameCuentaAuxiliar($codigo_cuentaauxiliar);
  // echo $codigo_cuentaauxiliar."**";
  $cod_pagoproveedor=obtenerCodigoPagoProveedor();
  // $observaciones_pago_x=$observaciones_pago." - ".$nameCuentaAuxiliar;
  $sqlInsert="INSERT INTO pagos_proveedores (codigo, fecha,observaciones,cod_comprobante,cod_estadopago,cod_ebisa,cod_cajachicadetalle,cod_pagolote) 
  VALUES ('".$cod_pagoproveedor."','".$fecha_pago."','".$observaciones_pago."','0',3,0,".$tipo_pago.",".$cod_pagolote.")";
  $stmtInsert = $dbh->prepare($sqlInsert);
  $stmtInsert->execute();
  //**
  
  for ($pro=1; $pro <= $cantidadFilas ; $pro++){  
    $cod_cuentaaux_s=$_POST["cod_proveedor_s".$pro];
    if($codigo_cuentaauxiliar==$cod_cuentaaux_s){
      $codigo_auxiliar_s=$_POST["codigo_auxiliar_s".$pro];
      $monto_pago_s=$_POST["monto_pago_s".$pro];
      
      if($monto_pago_s>0){
        // $sql="SELECT e.cod_comprobantedetalle,e.cod_plancuenta,e.cod_cuentaaux,e.cod_proveedor,e.glosa_auxiliar,cd.cod_comprobante
        //   from comprobantes_detalle cd join estados_cuenta e on e.cod_comprobantedetalle =cd.codigo
        //   where e.codigo='$codigo_auxiliar_s'";
        // //echo "<br>..".$sql;
        // $stmtEstaCueSele = $dbh->prepare($sql);
        // $stmtEstaCueSele->execute();                    
        // $stmtEstaCueSele->bindColumn('cod_comprobantedetalle', $cod_comprobantedetalle_x);
        // $stmtEstaCueSele->bindColumn('cod_plancuenta', $cod_plancuenta_x);
        // $stmtEstaCueSele->bindColumn('cod_proveedor', $cod_proveedor_x);
        // $stmtEstaCueSele->bindColumn('cod_cuentaaux', $cod_cuentaaux_x);

        // $stmtEstaCueSele->bindColumn('glosa_auxiliar', $glosa_auxiliar_x);
        // $stmtEstaCueSele->bindColumn('cod_comprobante', $cod_comprobante_x);
        // $cod_comprobantedetalle="";
        // $cod_plancuenta="";
        // $cod_proveedor="";
        // $cod_cuentaaux="";

        // $glosa_auxiliar="";
        // $cod_comprobante="";
        // while ($rowDetalleX = $stmtEstaCueSele->fetch(PDO::FETCH_BOUND)){ 
        //     $cod_comprobantedetalle=$cod_comprobantedetalle_x;
        //     $cod_plancuenta=$cod_plancuenta_x;
        //     $cod_proveedor=$cod_proveedor_x;
        //     $cod_cuentaaux=$cod_cuentaaux_x;

        //     $glosa_auxiliar=$glosa_auxiliar_x;
        //     $cod_comprobante=$cod_comprobante_x;
        // }
        //insertamos los estados de cuenta
        // $stmtContraCuenta = $dbh->prepare("INSERT INTO estados_cuenta(cod_comprobantedetalle,cod_plancuenta,monto,cod_proveedor,fecha,cod_comprobantedetalleorigen,cod_cuentaaux,cod_cajachicadetalle,glosa_auxiliar)values('0','$cod_plancuenta','$monto_pago_s','$cod_proveedor','$fecha_pago','$codigo_auxiliar_s','$cod_cuentaaux',0,'$observaciones_pago_x')");
        // $flagSuccess=$stmtContraCuenta->execute();

        $codigo_sr=0;
        // $sqlDetalleX="SELECT sd.codigo,sd.cod_solicitudrecurso,sd.cod_proveedor,sd.cod_tipopagoproveedor 
        // FROM solicitud_recursos s,solicitud_recursosdetalle sd
        // WHERE s.codigo=sd.cod_solicitudrecurso and s.cod_comprobante in (select cd.cod_comprobante from estados_cuenta e,comprobantes_detalle cd where e.cod_comprobantedetalle=cd.codigo and e.codigo=$codigo_auxiliar_s)";
        // $stmtDetalleX = $dbh->prepare($sqlDetalleX);
        // $stmtDetalleX->execute();                    
        // $stmtDetalleX->bindColumn('codigo', $codigo_sr);
        // $stmtDetalleX->bindColumn('cod_solicitudrecurso', $cod_solicitudrecurso_sr);
        // $stmtDetalleX->bindColumn('cod_proveedor', $cod_proveedor_sr);
        // $stmtDetalleX->bindColumn('cod_tipopagoproveedor', $cod_tipopagoproveedor_sr);
        // while ($rowDetalleX = $stmtDetalleX->fetch(PDO::FETCH_BOUND)){ 
        //     $codigo_sr=$codigo_sr;
        //     $cod_solicitudrecurso_sr=$cod_solicitudrecurso_sr;
        //     $cod_proveedor_sr=$cod_proveedor_sr;
        //     $cod_tipopagoproveedor_sr=$cod_tipopagoproveedor_sr;
        // }
         $sql="SELECT cod_comprobantedetalle,cod_plancuenta,cod_proveedor,cod_cuentaaux from estados_cuenta where codigo='$codigo_auxiliar_s'";
         $sql="SELECT e.cod_comprobantedetalle,e.cod_plancuenta,e.cod_cuentaaux,e.cod_proveedor,e.glosa_auxiliar,cd.cod_comprobante
            from comprobantes_detalle cd join estados_cuenta e on e.cod_comprobantedetalle =cd.codigo
            where e.codigo='$codigo_auxiliar_s'";
        // echo "<br>..".$sql;
        $stmtEstaCueSele = $dbh->prepare($sql);
        $stmtEstaCueSele->execute();                    
        $stmtEstaCueSele->bindColumn('cod_comprobantedetalle', $cod_comprobantedetalle);
        $stmtEstaCueSele->bindColumn('cod_plancuenta', $cod_plancuenta);
        $stmtEstaCueSele->bindColumn('cod_proveedor', $cod_proveedor);
        $stmtEstaCueSele->bindColumn('cod_cuentaaux', $cod_cuentaaux);
        $stmtEstaCueSele->bindColumn('glosa_auxiliar', $glosa_auxiliar_x);
        $stmtEstaCueSele->bindColumn('cod_comprobante', $cod_comprobante_x);
        $cod_comprobantedetalle="";
        $cod_plancuenta="";
        $cod_proveedor="";
        $cod_cuentaaux="";
        $glosa_auxiliar="";
        $cod_comprobante="";
        while ($rowDetalleX = $stmtEstaCueSele->fetch(PDO::FETCH_BOUND)){ 
          $cod_comprobantedetalle=$cod_comprobantedetalle;
          $cod_plancuenta=$cod_plancuenta;
          $cod_proveedor=$cod_proveedor;
          $cod_cuentaaux=$cod_cuentaaux;

          $glosa_auxiliar=$glosa_auxiliar_x;
          $cod_comprobante=$cod_comprobante_x;
        }

        $nombreComprobanteX=nombreComprobante($cod_comprobante);
        $observaciones_pago_x="Pago ".$nombreComprobanteX." ".$glosa_auxiliar;
        $cod_pagoproveedordetalle=obtenerCodigoPagoProveedorDetalle();
        $sqlInsert2="INSERT INTO pagos_proveedoresdetalle (codigo,cod_pagoproveedor,cod_proveedor,cod_solicitudrecursos,cod_solicitudrecursosdetalle,cod_tipopagoproveedor,monto,observaciones,fecha) 
         VALUES ('".$cod_pagoproveedordetalle."','".$cod_pagoproveedor."','".$cod_proveedor."','".$codigo_auxiliar_s."','".$cod_comprobantedetalle."','".$tipo_pago."','".$monto_pago_s."','".$observaciones_pago_x."','".$fecha_pago."')";
        // echo $sqlInsert2;
        $stmtInsert2 = $dbh->prepare($sqlInsert2);
        $flagSuccess=$stmtInsert2->execute();
        $stmtCambioEstadoSR = $dbh->prepare("UPDATE solicitud_recursos set cod_estadosolicitudrecurso=9 where codigo=:codigo");
        $stmtCambioEstadoSR->bindParam(':codigo', $cod_solicitudrecurso_sr);
        $flagSuccess=$stmtCambioEstadoSR->execute();   
      }
    }
  }
}

if($flagSuccess==true){
	showAlertSuccessError(true,"../".$urlListPagoLotes);	
}else{
	showAlertSuccessError(false,"../".$urlListPagoLotes);
}

?>
