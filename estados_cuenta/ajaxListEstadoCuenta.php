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
<table class="table table-bordered table-condensed table-warning">
	<thead>
	  <tr class="">
	  	<th class="text-left">Fecha</th>
	  	<th class="text-left">Proveedor/Cliente</th>
	  	<th class="text-left">Glosa</th>
	  	<th class="text-right">D&eacute;bito</th>
	  	<th class="text-right">Cr&eacute;dito</th>
	  	<th class="text-right">Saldo</th>
	  </tr>
	</thead>
	<tbody id="tabla_estadocuenta">
<?php
	$sqlEstadoCuenta="SELECT e.*,d.glosa,d.haber,d.debe,d.cod_cuentaauxiliar FROM estados_cuenta e,comprobantes_detalle d where e.cod_comprobantedetalle=d.codigo and (d.cod_cuenta=$codCuenta or e.cod_plancuenta=$codCuenta) and e.cod_comprobantedetalleorigen=0 order by e.fecha";
  $stmt = $dbh->prepare($sqlEstadoCuenta);
  
  $stmt->execute();
  $i=0;$saldo=0;
  $indice=0;
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

	//SACAMOS CUANTO SE PAGO DEL ESTADO DE CUENTA.
    $sqlContra="SELECT sum(monto)as monto from estados_cuenta e where e.cod_comprobantedetalleorigen='$codCompDetX'";
    $stmtContra = $dbh->prepare($sqlContra);
    $stmtContra->execute();
    $montoContra=0;
    while ($rowContra = $stmtContra->fetch(PDO::FETCH_ASSOC)) {
      $montoContra=$rowContra['monto'];
    }
    $debeX=$montoContra;
    //FIN SACAR LOS PAGOS
    

	 $saldo=$saldo+$haberX-$debeX;
	 $codProveedor=$row['cod_proveedor'];

	 $nombreProveedorX=nameProveedor($codProveedor);

  if(($row['cod_cuentaaux']!=""||$row['cod_cuentaaux']!=0)){
   if($tipoProveedorCliente==1){
      $proveedorX=obtenerProveedorCuentaAux($row['cod_cuentaaux']);
    }else{
    if(($row['cod_cuentaauxiliar']!=0)){
     $proveedorX=obtenerClienteCuentaAux($row['cod_cuentaauxiliar']);
    }else{
     $proveedorX="Sin Cliente";
    }	    
   }
  }else{
    if($tipoProveedorCliente==1){
         $proveedorX="Sin Proveedor";
      }else{
       $proveedorX="Sin Cliente";
      }
  }	
	 if($haberX>0){
       ?>
		<tr class="bg-white det-estados">
	  	   	<td class="text-left small font-weight-bold"><?=$fechaX?></td>
	  	   	<td class="text-left small"><?=$nombreProveedorX;?></td>
	  	   	<td class="text-left small">

		    <div id="accordion<?=$indice;?>" role="tablist">
		        <div class="card-collapse">
		          <div class="card-header" role="tab" id="heading<?=$indice;?>">
		            <p class="mb-0">
		              <small>
		              <a data-toggle="collapse" href="#collapse<?=$indice;?>" aria-expanded="false" aria-controls="collapse<?=$indice;?>" class="collapsed">

				  	   	<?=$glosaX?>
	   	                
	   	                <i class="material-icons">keyboard_arrow_down</i>
		              </a>
		          		</small>
		            </p>
		          </div>
		          <div id="collapse<?=$indice;?>" class="collapse" role="tabpanel" aria-labelledby="heading<?=$indice;?>" data-parent="#accordion<?=$indice;?>" style="">
		            <div class="card-body">
		            	<?php
                  			$sqlDetalleX="SELECT e.fecha, e.monto, (select cd.glosa from comprobantes_detalle cd where cd.codigo=e.cod_comprobantedetalle)as glosa from estados_cuenta e where e.cod_comprobantedetalleorigen=$codCompDetX"; 	                                 
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
                                    <td class="text-left small"><?=$fechaDetalle;?></td>
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
	  	   	<td class="text-right small"><?=formatNumberDec($montoContra)?></td>
	  	   	<td class="text-right small"><?=formatNumberDec($montoX)?></td>
	  	   	<td class="text-right small font-weight-bold"><?=formatNumberDec($saldo)?></td>
	   	</tr>

  	   <?php
	 }else{
           //$saldo=$saldo+$debeX-$haberX;
            
        ?>
  	   <!--<tr class="bg-white det-estados">
  	   		<td class="text-left font-weight-bold"><?=$fechaX?></td>
  	   		<td class="text-left"><?=$nombreProveedorX;?></td>
  	   		<td class="text-left"><?=$glosaX?></td>
  	   		<td class="text-right"></td>
  	   		<td class="text-right"><?=formatNumberDec($montoX)?></td>
  	   		<td class="text-right font-weight-bold"><?=formatNumberDec($saldo)?></td>
  	   	</tr>-->
  	   	<tr class="bg-white det-estados">
	  	   	<td class="text-left small font-weight-bold"><?=$fechaX?></td>
	  	   	<td class="text-left small"><?=$proveedorX;?></td>
	  	   	<td class="text-left small">

		    <div id="accordion<?=$indice;?>" role="tablist">
		        <div class="card-collapse">
		          <div class="card-header" role="tab" id="heading<?=$indice;?>">
		            <p class="mb-0">
		              <small>
		              <a data-toggle="collapse" href="#collapse<?=$indice;?>" aria-expanded="false" aria-controls="collapse<?=$indice;?>" class="collapsed">

				  	   	<?=$glosaX?>
	   	                
	   	                <i class="material-icons">keyboard_arrow_down</i>
		              </a>
		          		</small>
		            </p>
		          </div>
		          <div id="collapse<?=$indice;?>" class="collapse" role="tabpanel" aria-labelledby="heading<?=$indice;?>" data-parent="#accordion<?=$indice;?>" style="">
		            <div class="card-body">
		            	<?php
                  			$sqlDetalleX="SELECT e.fecha, e.monto, (select cd.glosa from comprobantes_detalle cd where cd.codigo=e.cod_comprobantedetalle)as glosa from estados_cuenta e where e.cod_comprobantedetalleorigen=$codCompDetX"; 	                                 
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
                                    <td class="text-left small"><?=$fechaDetalle;?></td>
                                    <td class="text-left small"><?=$glosaDetalle;?></td>
                                    <td class="text-left small"><?=$montoDetalle;?></td>
                              	</tr>
                              	<?php    
                              	}
                              $haberX=$montoContra;
                              $saldo=$montoX-$haberX;	
                              	?>
                            </table>
		            </div>
		          </div>
		        </div>
	      	</div>

	      	</td>  	   	
	  	   	<td class="text-right small"><?=formatNumberDec($montoX)?></td>
	  	   	<td class="text-right small"><?=formatNumberDec($montoContra)?></td>
	  	   	<td class="text-right small font-weight-bold"><?=formatNumberDec($saldo)?></td>
	   	</tr>

  	   <?php
	 }
	 
	 $i++;
	 $indice++;
  }
?>
	</tbody>
</table>
<?php
echo "@".$saldo;