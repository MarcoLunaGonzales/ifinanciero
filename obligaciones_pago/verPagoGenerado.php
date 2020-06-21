<?php
session_start();
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../styles.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';


setlocale(LC_TIME, "Spanish");
$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();


$globalNombreGestion=$_SESSION["globalNombreGestion"];
$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalNombreUnidad=$_SESSION['globalNombreUnidad'];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];

$fechaActual=date("Y-m-d");
$dbh = new Conexion();
if(isset($_GET['cod'])){
	$codigo=$_GET['cod'];
}else{
	$codigo=0;
}
if(isset($_GET['admin'])){
	$urlListPago=$urlListPagoAdmin;
}

      $stmt = $dbh->prepare("SELECT sr.*,e.nombre as estado from pagos_proveedores sr join estados_pago e on sr.cod_estadopago=e.codigo where sr.codigo=$codigo");
      $stmt->execute();
      $stmt->bindColumn('codigo', $codigo);
      $stmt->bindColumn('cod_pagolote', $nombre_lote);
      $stmt->bindColumn('fecha', $fecha);
      $stmt->bindColumn('observaciones', $observaciones);
      $stmt->bindColumn('cod_comprobante', $codComprobante);
      $stmt->bindColumn('estado', $estado);
      $stmt->bindColumn('cod_estadopago', $codEstado);
      $stmt->bindColumn('cod_ebisa', $cod_ebisa);
?>
<div id="logo_carga" class="logo-carga" style="display:none;"></div>
<div class="content">
	<div id="contListaGrupos" class="container-fluid">
			<input type="hidden" name="cod_solicitudrecursos" id="cod_solicitudrecursos" value="<?=$codigo?>">
           <div class="row">
             <div class="col-sm-12">
			  <div class="card">
				<div class="card-header card-header-deafult card-header-text text-center">
					<div class="card-text">
					  <h4 class="card-title"><b>PAGO PROVEEDORES</b></h4>
					</div>
				</div>
				<div class="card-body">
					<div class=""> 	
					<div class="row" id="">
				        <?php 
                            while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                                $datosArray=obtenerDatosProveedoresPagoDetalle($codigo);
                          $descripcion=obtenerGlosaComprobante($codComprobante);
                          if(strlen($descripcion)>50){
                            $descripcion=substr($descripcion, 0, 50)."...";
                          }
                          
                          switch ($codEstado) {
                            case 1:
                              $btnEstado="btn-default";
                            break;
                            case 2:
                              $btnEstado="btn-danger";
                            break;
                            case 3:
                              $btnEstado="btn-success";
                            break;
                            case 4:
                              $btnEstado="btn-warning";
                            break;
                            case 5:
                              $btnEstado="btn-info";
                            break;
                          }
                          $fechaPago=strftime('%d/%m/%Y',strtotime($fecha));
                                ?>      
					
                    <label class="col-sm-1 col-form-label" style="color:#000000; ">Proveedor :</label>
<div class="col-sm-4">
  <div class="form-group">
  	<input type="text" class="form-control" readonly="true" value="<?=$datosArray[0]?>" style="background-color:#E3CEF6;text-align: left" >
  </div>
</div>  
<label class="col-sm-1 col-form-label" style="color:#000000; ">Oficina:</label>
<div class="col-sm-2">
  <div class="form-group">
    <input type="text" class="form-control" readonly="true" value="<?=$datosArray[4]?>" style="background-color:#E3CEF6;text-align: left" >
  </div>
</div> 
<label class="col-sm-1 col-form-label" style="color:#000000; ">Fecha Pago:</label>
<div class="col-sm-2">
  <div class="form-group">
  	<input type="text" class="form-control" readonly="true" value="<?=$fechaPago?>" style="background-color:#E3CEF6;text-align: left" >
  </div>
</div> 
</div>
<div class="row">
<label class="col-sm-1 col-form-label" style="color:#000000; ">N Solicitud:</label>
<div class="col-sm-2">
  <div class="form-group">
  	<input type="text" class="form-control" readonly="true" value="<?=$datosArray[5]?>" style="background-color:#E3CEF6;text-align: left" >
  </div>
</div> 
<label class="col-sm-1 col-form-label" style="color:#000000; ">Estado</label>
<div class="col-sm-1">
  <div class="form-group">
  	<input type="text" class="form-control" readonly="true" value="<?=$estado?>" style="background-color:#E3CEF6;text-align: left" >
  </div>
</div> 
<label class="col-sm-1 col-form-label" style="color:#000000; ">Observaciones</label>
<div class="col-sm-5">
  <div class="form-group">
  	<input type="text" class="form-control" readonly="true" value="<?=$observaciones?>" style="background-color:#E3CEF6;text-align: left" >
  </div>
</div>         <?php

                  } ?>
                    </div>

					<div class="col-sm-4 div-center"><center><h3>Detalle de Pagos</h3></center></div>
					<div class="col-sm-12 div-center">	
						<table class="table table-bordered table-condensed">
							<thead>
								<tr style="background:#21618C; color:#fff;">
									<th>#</th>
									<th>Numero</th>
									<th class="text-left">Nombre Cuenta</th>
									<th>Detalle</th>
									<th>Tipo Pago</th>
									<th class="text-right">Presupuestado</th>
									<th class="text-right">Solicitado</th>
                  <th class="text-right">Pagado</th>			
									<th>Proveedor</th>
								</tr>
							</thead>
							<tbody>
							<?php 
							$solicitudDetalle=obtenerPagoProveedorDetalle($codigo);
							$index=1;$totalImportePres=0;$totalImporte=0;$totalPago=0;
                             while ($rowDetalles = $solicitudDetalle->fetch(PDO::FETCH_ASSOC)) {
                             	$codCuentaX=$rowDetalles['cod_plancuenta'];
                             	$detalleX=$rowDetalles["detalle"];
                             	$importeX=$rowDetalles["importe_presupuesto"];
							    $importeSolX=$rowDetalles["importe"];
							    $proveedorX=nameProveedor($rowDetalles["cod_proveedor"]);
							    $retencionX=$rowDetalles["cod_confretencion"];
							    $totalImportePres+=$importeX;
							    $totalImporte+=$importeSolX;
							    if($retencionX!=0){
							   	  $tituloImporte="<strong>".nameRetencion($retencionX)."</strong>";
							    }else{
							      $tituloImporte="Ninguno";	
							    }
							    $numeroCuentaX=trim($rowDetalles['numero']);
							    $nombreCuentaX=trim($rowDetalles['nombre']);
                  $pagoX=$rowDetalles['pago'];
                  $totalPago+=$pagoX;
                                ?>
                                <tr>
                                    <td><?=$index?></td>
                                	<td class="font-weight-bold"><?=$numeroCuentaX?></td>
                                    <td class="text-left"><?=$nombreCuentaX?></td>
                                    <td><?=$detalleX?></td>
                                    <td><?=$rowDetalles['tipo_pago']?></td>
                                    <td class="text-right"><?=number_format($importeX, 2, '.', ',')?></td>
                                    <td class="text-right"><?=number_format($importeSolX, 2, '.', ',')?></td>
                                    <td class="text-right"><?=number_format($pagoX, 2, '.', ',')?></td>
                                    <td><?=$proveedorX?></td>
                                    
                                </tr><?php
                              $index++;
                             }
                        	?>
                        	  <tr class="font-weight-bold bg-white text-dark">
                        	  	    <td colspan="5" class="text-left">Total</td>
                                    <td class="text-right"><?=number_format($totalImportePres, 2, '.', ',')?></td>
                                    <td class="text-right"><?=number_format($totalImporte, 2, '.', ',')?></td>
                                    <td class="text-right"><?=number_format($totalPago, 2, '.', ',')?></td>
                                    <td></td>
                        	  </tr>
							</tbody>
						</table>
					</div>		
				  	<div class="card-footer fixed-bottom col-sm-12">
						
						<?php 
	          ?><a href="../<?=$urlListPago;?>" class="btn btn-danger">Volver</a><?php	   
						?>
						<div class="row col-sm-9 float-right">
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label class="bmd-label-static text-white" style="background:#21618C;">Solicitado</label>  
                          <input class="form-control bg-info text-white text-center" style="border-radius:10px;" type="number" step=".01" placeholder="0" value="<?=$totalImporte?>" id="total_presupuestado" readonly="true"> 
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                          <label class="bmd-label-static text-white" style="background:#21618C;">Pagado</label> 
                          <input class="form-control bg-info text-white text-center" style="border-radius:10px;" type="number" step=".01" placeholder="0" value="<?=$totalPago?>" id="total_solicitado" readonly="true"> 
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                          <label class="bmd-label-static text-white" style="background:#21618C;">Saldo del pago</label> 
                          <input class="form-control bg-info text-white text-center" style="border-radius:10px;" type="number" step=".01" placeholder="0" value="<?=$totalImporte-$totalPago?>" id="total_solicitado" readonly="true"> 
                        </div>
                    </div>
              </div>
				  	</div>
				 </div>
			    </div><!--div end card-->			
               </div>
            </div>
	</div>
</div>