<?php
$nuevaDistribucion=verificarHayAmbasDistribucionesSolicitudRecurso($codigo);
$debeUnidad=$debe;
if($nuevaDistribucion==3){
  $debeUnidad=$debe*(0.40);
  $debeArea=$debe*(0.60);
}else{
	if($nuevaDistribucion==1){
	  $debeUnidad=$debe;
	  $debeArea=0;
	}else{
	  $debeUnidad=0;
	  $debeArea=$debe;
	} 
}

$datosDistribucion=obtenerDistribucionGastoSolicitudRecurso($codigo,1,$debeUnidad);
while ($rowUnidadD = $datosDistribucion->fetch(PDO::FETCH_ASSOC)) {
  $unidadDis=$rowUnidadD['oficina_area'];
  $debeUnidadPor=$rowUnidadD['monto_porcentaje'];
  if($rowUnidadD['porcentaje']>0){
     $codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
     $sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) 
     VALUES ('$codComprobanteDetalle','$codComprobante', '$cuenta', '$cuentaAuxiliar', '$unidadDis', '$area', '$debeUnidadPor', '$haber', '$glosaDetalle', '$i')";
     $stmtDetalle = $dbh->prepare($sqlDetalle);
     $flagSuccessDetalle=$stmtDetalle->execute();   
  }
}

$datosDistribucionArea=obtenerDistribucionGastoSolicitudRecurso($codigo,2,$debeArea);
while ($rowAreaD = $datosDistribucionArea->fetch(PDO::FETCH_ASSOC)) {
  $areaDis=$rowAreaD['oficina_area'];
  $debeUnidadPor=$rowAreaD['monto_porcentaje'];
  if($rowAreaD['porcentaje']>0){
     $codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
     $sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) 
     VALUES ('$codComprobanteDetalle','$codComprobante', '$cuenta', '$cuentaAuxiliar', '$unidadDetalle', '$areaDis', '$debeUnidadPor', '$haber', '$glosaDetalle', '$i')";
     $stmtDetalle = $dbh->prepare($sqlDetalle);
     $flagSuccessDetalle=$stmtDetalle->execute();   
  }
}