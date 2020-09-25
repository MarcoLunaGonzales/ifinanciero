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

$codigo=$_GET['codigo'];
$codCajaChica=$_GET['cod_cajachica'];
$fechaActual=date("d/m/Y");
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
$solicitudDetalle=obtenerSolicitudRecursosDetalle($codigo);
$numeroRecibo=obtenerNumeroReciboCajaChica($codCajaChica);
$index=1;
    while ($rowDetalles = $solicitudDetalle->fetch(PDO::FETCH_ASSOC)) {
    	$numeroCuentaX=trim($rowDetalles['numero']);
		$nombreCuentaX=trim($rowDetalles['nombre']);
		$proveedorX=nameProveedor($rowDetalles["cod_proveedor"]);
        $retencionX=$rowDetalles["cod_confretencion"];
        if($retencionX!=0){
		  $tituloImporte=nameRetencion($retencionX);
		}else{
		  $tituloImporte="Ninguno";	
		}
		$importeSolX=$rowDetalles["importe"];
		$detalleX=$rowDetalles["detalle"];
		$codAreaXX=$rowDetalles['cod_area'];
        $codOficinaXX=$rowDetalles['cod_unidadorganizacional'];

        $nombreOficinaXX=abrevUnidad_solo($codOficinaXX);
        $nombreAreaXX=abrevArea_solo($codAreaXX);
     ?>
       <tr>
           <td class="small"><?=$index?></td>
           <td class="font-weight-bold"><?=$numeroRecibo?></td>
           <td class="small"><?=$nombreCuentaX?></td>
           <td class="small"><?=$fechaActual?></td>
           <td class="small"><?=$retencionX?></td>
           <td class="small"><?=$proveedorX?></td>
           <td class="small"><?=$importeSolX?></td>
           <td class="small"><?=$detalleX?></td>
           <td class="small"><?=$nombreOficinaXX?>/<?=$nombreAreaXX?></td>
        </tr>
      <?php 
      $index++;
      $numeroRecibo++;
     } 
     ?>
	 </tbody>
  </table>
					
</div>					