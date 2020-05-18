<?php

require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once '../styles.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

session_start();
$globalAdmin=$_SESSION["globalAdmin"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];

$fechaActual=date("d/m/Y");
$codCuenta=$_GET['cod_cuenta'];
$codCuentaAux=$_GET['cod_cuentaaux'];
$tipo=$_GET['tipo'];
$tipoProveedorCliente=$_GET['tipo_proveedorcliente'];
$mes=$_GET['mes'];
?>
<div class="table-responsive">

<table class="table table-condensed" id="tablePaginatorReport">
	<thead>
	  <tr class="">
	  	<th class="text-left">Of</th>
	  	<th class="text-left">Tipo/#</th>
	  	<th class="text-left">FechaComp</th>
	  	<th class="text-left">FechaEC</th>
	  	<th class="text-left">Proveedor/Cliente</th>
	  	<th class="text-left">Glosa</th>
	  	<th class="text-right">D&eacute;bito</th>
	  	<th class="text-right">Cr&eacute;dito</th>
	  	<th class="text-right">Saldo</th>
	  </tr>
	</thead>
	<tbody id="tabla_estadocuenta">
<?php
	$sqlEstadoCuenta="SELECT e.*,d.glosa,d.haber,d.debe,(select concat(c.cod_tipocomprobante,'|',c.numero,'|',cd.cod_unidadorganizacional,'|',MONTH(c.fecha),'|',c.fecha) from comprobantes_detalle cd, comprobantes c where c.codigo=cd.cod_comprobante and cd.codigo=e.cod_comprobantedetalle)as extra, c.codigo as codigocomprobante FROM estados_cuenta e,comprobantes_detalle d, comprobantes c where c.codigo=d.cod_comprobante and c.cod_estadocomprobante<>2 and e.cod_comprobantedetalle=d.codigo and (d.cod_cuenta=$codCuenta) and e.cod_comprobantedetalleorigen=0 order by e.fecha, c.numero";
  	
  	//echo $sqlEstadoCuenta;

  	$stmt = $dbh->prepare($sqlEstadoCuenta);


  
  $stmt->execute();
  $i=0;$saldo=0;
  $indice=0;
  $totalSaldo=0;
  $totalDebe=0;
  $totalHaber=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	 $codigoX=$row['codigo'];
	 $codPlanCuentaX=$row['cod_plancuenta'];
	 $codCompDetX=$row['cod_comprobantedetalle'];
	 $fechaX=$row['fecha'];
	 $fechaX=strftime('%d/%m/%Y',strtotime($fechaX));
	 $montoX=$row['monto'];
	 $glosaX=$row['glosa'];
	 $debeX=$row['debe'];
	 $haberX=$row['haber'];
	 $codigoExtra=$row['extra'];
	 $glosaAuxiliar=$row['glosa_auxiliar'];
	 $codCuentaAuxDet=$row['cod_cuentaaux'];
	 $codigoComprobante=$row['codigocomprobante'];

	 $nombreComprobante=nombreComprobante($codigoComprobante);

	 if($tipo==1){
	 	$totalDebe+=$montoX;
	 }else{
	 	$totalHaber+=$montoX;
	 }

	 $glosaMostrar="";
	 if($glosaAuxiliar!=""){
	 	$glosaMostrar=$glosaAuxiliar;
	 }else{
	 	$glosaMostrar=$glosaX;
	 }

	list($tipoComprobante, $numeroComprobante, $codUnidadOrganizacional, $mesComprobante, $fechaComprobante)=explode("|", $codigoExtra);
	$nombreTipoComprobante=abrevTipoComprobante($tipoComprobante)."-".$mesComprobante;
	$nombreUnidadO=abrevUnidad_solo($codUnidadOrganizacional);

	$fechaComprobante=strftime('%d/%m/%Y',strtotime($fechaComprobante));

	//SACAMOS CUANTO SE PAGO DEL ESTADO DE CUENTA.
    $sqlContra="SELECT sum(e.monto)as monto from estados_cuenta e, comprobantes_detalle cd, comprobantes c where c.codigo=cd.cod_comprobante and c.cod_estadocomprobante<>2 and cd.codigo=e.cod_comprobantedetalle and e.cod_comprobantedetalleorigen='$codigoX'";
    //echo $sqlContra;
    $stmtContra = $dbh->prepare($sqlContra);
    $stmtContra->execute();
    $montoContra=0;
    while ($rowContra = $stmtContra->fetch(PDO::FETCH_ASSOC)) {
      $montoContra=$rowContra['monto'];
    }
    if($tipo==1){
    	$totalHaber+=$montoContra;
    }else{
    	$totalDebe+=$montoContra;
    }
    $debeX=$montoContra;
    //FIN SACAR LOS PAGOS
    

	 $saldo+=$montoX-$montoContra;
	 $totalSaldo=$totalSaldo+$saldo;
	 $codProveedor=$row['cod_proveedor'];


  	$nombreProveedorClienteX=nameProveedorCliente($tipoProveedorCliente,$codProveedor);
  	$nombreCuentaAux=nameCuentaAuxiliar($codCuentaAuxDet);
 

       ?>
		<tr class="bg-white det-estados">
	  	   	<td class="text-center small"><?=$nombreUnidadO;?></td>
	  	   	<td class="text-center small"><?=$nombreComprobante;?></td>
	  	   	<td class="text-center small"><?=$fechaComprobante;?></td>
	  	   	<td class="text-center small"><?=$fechaX;?></td>
	  	   	<td class="text-left small">[<?=$nombreCuentaAux;?>] - <?=$nombreProveedorClienteX;?></td>
	  	   	<td class="text-left small">

		    <div id="accordion<?=$indice;?>" role="tablist">
		        <div class="card-collapse">
		          <div class="card-header" role="tab" id="heading<?=$indice;?>">
		            <p class="mb-0">
		              <small>
		              <a data-toggle="collapse" href="#collapse<?=$indice;?>" aria-expanded="false" aria-controls="collapse<?=$indice;?>" class="collapsed">

				  	   	<?=$glosaMostrar;?>
	   	                
	   	                <i class="material-icons">keyboard_arrow_down</i>
		              </a>
		          		</small>
		            </p>
		          </div>
		          <div id="collapse<?=$indice;?>" class="collapse" role="tabpanel" aria-labelledby="heading<?=$indice;?>" data-parent="#accordion<?=$indice;?>" style="">
		            <div class="card-body">
		            	<?php
                  			$sqlDetalleX="SELECT e.fecha, e.monto, (select cd.glosa from comprobantes_detalle cd where  cd.codigo=e.cod_comprobantedetalle)as glosa from estados_cuenta e, comprobantes_detalle cd, comprobantes c where c.codigo=cd.cod_comprobante and c.cod_estadocomprobante<>2 and cd.codigo=e.cod_comprobantedetalle and e.cod_comprobantedetalleorigen=$codigoX"; 	                                 
	                     	$stmtDetalleX = $dbh->prepare($sqlDetalleX);
		                    $stmtDetalleX->execute();

		                      $stmtDetalleX->bindColumn('fecha', $fechaDetalle);
		                      $stmtDetalleX->bindColumn('monto', $montoDetalle);
		                      $stmtDetalleX->bindColumn('glosa', $glosaDetalle);

                      	?>
                        	<table width="100%">
                    	        <tr>
                             		<th>Fecha</th>
		                            <th>Glosa</th>
		                            <th>Monto</th>
                                </tr>
                                <?php
                                while ($rowDetalleX = $stmtDetalleX->fetch(PDO::FETCH_BOUND)) {
                                ?>
                              	<tr>
                                    <td class="text-center small"><?=$fechaDetalle;?></td>
                                    <td class="text-left small"><?=$glosaDetalle;?></td>
                                    <td class="text-left small"><?=$montoDetalle;?></td>
                              	</tr>
                              	<?php    
                              	}
                              	?>
                            </table>
		            </div>
		          </div>
		        </div>
	      	</div>

	      	</td>
	      	<?php
	      	if($tipo==2){
	      	?>
	      	<td class="text-right small"><?=formatNumberDec($montoContra)?></td>
	  	   	<td class="text-right small"><?=formatNumberDec($montoX)?></td>
	      	<?php	
	      	}else{
	      	?>
	  	   	<td class="text-right small"><?=formatNumberDec($montoX)?></td>
	      	<td class="text-right small"><?=formatNumberDec($montoContra)?></td>
	      	<?php	
	      		
	      	}
	      	?>
	  	   	<td class="text-right small font-weight-bold"><?=formatNumberDec($saldo)?></td>
	   	</tr>

  	   <?php
	 $i++;
	 $indice++;
  }
?>
		<tr>
	  	   	<td class="text-right small" colspan="6">Totales:</td>
	  	   	<td class="text-right small font-weight-bold"><?=formatNumberDec($totalDebe);?></td>
	  	   	<td class="text-right small font-weight-bold text-success"><?=formatNumberDec($totalHaber);?></td>
	  	   	<td class="text-right small font-weight-bold"><?=formatNumberDec($saldo);?></td>
	   	</tr>
	</tbody>
</table>
</div>
<?php
echo "@".$saldo;