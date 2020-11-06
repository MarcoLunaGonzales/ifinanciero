<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();
$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();
set_time_limit(300);
$globalMes=$_SESSION['globalMes'];
$globalNombreGestion=$_SESSION["globalNombreGestion"];

$codigo=$_GET['codigo'];
$codCajaChica=$_GET['cod_cajachica'];
$fechaActual=date("d/m/Y");
//fecha hora actual para el comprobante (SESIONES)
$anioActual=date("Y");
$mesActual=date("m");
$diaActual=date("d");
$codMesActiva=$_SESSION['globalMes']; 
$month = $globalNombreGestion."-".$codMesActiva;
$aux = date('Y-m-d', strtotime("{$month} + 1 month"));
$diaUltimo = date('d', strtotime("{$aux} - 1 day"));
if((int)$globalNombreGestion<(int)$anioActual){
  $fechaHoraActual=$diaUltimo."/".$codMesActiva."/".$globalNombreGestion;
}else{
  if((int)$mesActual==(int)$codMesActiva){
      $fechaHoraActual=date("d/m/Y");
  }else{
    $fechaHoraActual=$diaUltimo."/".$codMesActiva."/".$globalNombreGestion;
  } 
}

// FIN DE LA FECHA
?>
<div class="row">
  <table class="table table-bordered table-condensed">
		<thead>
			<tr class="text-white bg-caja-chica small">
				<th>#</th>
                <th>Recibo</th>
                <th>Cuenta</th>
				<th>Fecha</th>
                <th>Tipo</th>
                <th>Entregado a</th>
			    <th>Monto</th>
                <th>Detalle</th>
				<th>Of/Area</th>			
			</tr>
		</thead>
		<tbody>
<?php 
$solicitudDetalle=obtenerSolicitudRecursosDetalleAgrupadas($codigo);
$instancia=obtenerCodigoInstanciaPorCajaChica($codCajaChica);
$numeroRecibo=obtenerNumeroReciboInstancia($instancia);
$index=1;

$idServicioX=obtenerServicioCodigoSolicitudRecursos($codigo);
$codSimulacionServicioX=obtenerSimulacionServicioCodigoSolicitudRecursos($codigo);
$IdTipo=0;//obtenerTipoServicioPorIdServicio($idServicioX);
$codObjeto=obtenerCodigoObjetoServicioPorIdSimulacion($codSimulacionServicioX);
$datosServicio=obtenerServiciosTipoObjetoNombre($codObjeto)." - ".obtenerServiciosClaServicioTipoNombre($IdTipo);
$nombreCliente=obtenerNombreClienteSimulacion($codSimulacionServicioX);
$numeroSR="SR ".obtenerNumeroSolicitudRecursos($codigo);
    while ($rowDetalles = $solicitudDetalle->fetch(PDO::FETCH_ASSOC)) {
    	$numeroCuentaX=trim($rowDetalles['numero']);
		$nombreCuentaX=trim($rowDetalles['nombre']);
		$proveedorX=nameProveedor($rowDetalles["cod_proveedor"]);
        
		$importeSolX=$rowDetalles["importe"];
    $retencionX=$rowDetalles["cod_confretencion"];
        if($retencionX!=0){
              $tituloImporte=abrevRetencion($retencionX);
              $porcentajeRetencion=100-porcentRetencionSolicitud($retencionX);
              $montoImporte=$importeSolX*($porcentajeRetencion/100);       
              if(($retencionX==8)||($retencionX==10)){ //validacion del descuento por retencion
                $montoImporte=$importeSolX;
              }
              $montoImporteRes=$importeSolX-$montoImporte;
            }else{
             $tituloImporte="Ninguno";
             $montoImporte=$importeSolX;
             $montoImporteRes=0; 
            }
		$detalleX=$rowDetalles["detalle"];
		$codAreaXX=$rowDetalles['cod_area'];
        $codOficinaXX=$rowDetalles['cod_unidadorganizacional'];

        $nombreOficinaXX=abrevUnidad_solo($codOficinaXX);
        $nombreAreaXX=abrevArea_solo($codAreaXX);
    
     $codActividadX=$rowDetalles["cod_actividadproyecto"];
            $tituloActividad="";
            //$tituloActividad=obtenerCodigoActividadesServicioImonitoreo($codActividadX);   
            $detalleActividadFila="";
            if($codActividadX>0){
              if(obtenerNombreDirectoActividadServicio($codActividadX)[0]!=""){
                $detalleActividadFila.="<br><small class='text-dark small'> Actividad: ".obtenerNombreDirectoActividadServicio($codActividadX)[0]." - ".obtenerNombreDirectoActividadServicio($codActividadX)[1]."</small>";
             }
            }
            $codAccNum=$rowDetalles["acc_num"]; 
            if($codAccNum>0){
              if(obtenerNombreDirectoActividadServicioAccNum($codAccNum)[0]!=""){
                $detalleActividadFila.="<br><small class='text-dark small'> Acc Num: ".obtenerNombreDirectoActividadServicioAccNum($codAccNum)[0]." - ".obtenerNombreDirectoActividadServicioAccNum($codAccNum)[1]."</small>";
              }
            }
     ?>
       <tr>
           <td class="small"><?=$index?></td>
           <td class="font-weight-bold"><?=$numeroRecibo?></td>
           <td class="small"><?=$nombreCuentaX?></td>
           <td class="small"><?=$fechaHoraActual?></td>
           <td class="small"><?=$tituloImporte?></td>
           <td class="small"><?=$proveedorX?></td>
           <td class="small"><?=number_format($montoImporte, 2, '.', ',')?></td>
           <td class="small"><?=" ".$proveedorX." ".str_replace("-", "", $detalleX)." ".$datosServicio." ".$nombreCliente." ".$detalleActividadFila." ".$numeroSR?></td>
           <td class="small"><?=$nombreOficinaXX?>/<?=$nombreAreaXX?></td>
        </tr>
      <?php 
      $index++;
      $numeroRecibo++;
     } 
     ?>
	 </tbody>
  </table>
	<center><p class="text-muted"><small>Los Detalles de la Solicitud de Recursos se agrupan por Oficina, Area, Cuenta y Proveedor automáticamente.</small></p></center>				
</div>					