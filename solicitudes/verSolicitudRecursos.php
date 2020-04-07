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
	$urlList=$urlList2;
}

$stmt = $dbh->prepare("SELECT p.*,e.nombre as estado_solicitud, u.abreviatura as unidad,a.abreviatura as area 
        from solicitud_recursos p,unidades_organizacionales u, areas a,estados_solicitudrecursos e
  where p.cod_unidadorganizacional=u.codigo and p.cod_area=a.codigo and e.codigo=p.cod_estadosolicitudrecurso and p.codigo='$codigo' order by codigo");
      $stmt->execute();
      $stmt->bindColumn('codigo', $codigoX);
            $stmt->bindColumn('cod_personal', $codPersonalX);
            $stmt->bindColumn('fecha', $fechaX);
            $stmt->bindColumn('cod_unidadorganizacional', $codUnidadX);
            $stmt->bindColumn('cod_area', $codAreaX);
            $stmt->bindColumn('area', $areaX);
            $stmt->bindColumn('unidad', $unidadX);
            $stmt->bindColumn('estado_solicitud', $estadoX);
            $stmt->bindColumn('cod_estadosolicitudrecurso', $codEstadoX);
            $stmt->bindColumn('numero', $numeroX);
            $stmt->bindColumn('cod_simulacion', $codSimulacionX);
            $stmt->bindColumn('cod_proveedor', $codProveedorX);

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
					  <h4 class="card-title"><b>SOLICITUD RECURSOS</b></h4>
					</div>
				</div>
				<div class="card-body">
				
					<div class=""> 	
					<div class="col-sm-8 div-center">	
						<table class="table table-bordered table-condensed table-warning">
							<thead>
								<tr class="">
									<th>Solicitante</th>
									<th>Fecha</th>
									<th>Numero</th>
									<th>Unidad</th>
									<th>Area</th>
								</tr>
							</thead>
							<tbody>
								<tr>
							<?php 
                            while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                                $solicitante=namePersonal($codPersonalX);
                                $fechaSolicitud=strftime('%d/%m/%Y',strtotime($fechaX));
                                ?><td><?=$solicitante?></td><td><?=$fechaSolicitud?></td><td><?=$numeroX?></td><td><?=$unidadX?></td><td><?=$areaX?></td><?php

                            }
                        	?>
                        	</tr>
							</tbody>
						</table>
					</div>
					<div class="col-sm-4 div-center"><center><h4>Detalle de la Solicitud de Recursos</h4></center></div>
					<div class="col-sm-12 div-center">	
						<table class="table table-bordered table-condensed table-warning">
							<thead>
								<tr class="">
									<th>#</th>
									<th>Numero</th>
									<th class="text-left">Nombre Cuenta</th>
									<th>Detalle</th>
									<th>Retenci&oacute;n</th>
									<th class="text-right">Presupuestado</th>
									<th class="text-right">Importe</th>			
									<th>Proveedor</th>
								</tr>
							</thead>
							<tbody>
							<?php 
							$solicitudDetalle=obtenerSolicitudRecursosDetalle($codigo);
							$index=1;$totalImportePres=0;$totalImporte=0;
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

                                ?>
                                <tr>
                                    <td><?=$index?></td>
                                	<td class="font-weight-bold"><?=$numeroCuentaX?></td>
                                    <td class="text-left"><?=$nombreCuentaX?></td>
                                    <td><?=$detalleX?></td>
                                    <td><?=$tituloImporte?></td>
                                    <td class="text-right"><?=number_format($importeX, 2, '.', ',')?></td>
                                    <td class="text-right"><?=number_format($importeSolX, 2, '.', ',')?></td>
                                    <td><?=$proveedorX?></td>
                                </tr><?php
                              $index++;
                             }
                        	?>
                        	  <tr class="font-weight-bold bg-white text-dark">
                        	  	    <td colspan="5" class="text-left">Total</td>
                                    <td class="text-right"><?=number_format($totalImportePres, 2, '.', ',')?></td>
                                    <td class="text-right"><?=number_format($totalImporte, 2, '.', ',')?></td>
                                    <td></td>
                        	  </tr>
							</tbody>
						</table>
					</div>		
				  	<div class="card-footer fixed-bottom">
						
						<?php 
					if(isset($_GET['q'])){
	                 $q=$_GET['q'];
	                 if(isset($_GET['r'])){
                      $r=$_GET['r'];
                      ?><a href="../<?=$urlList;?>&q=<?=$q?>&r=<?=$r?>" class="btn btn-danger">Volver</a><?php
                    }else{
                      ?><a href="../<?=$urlList;?>&q=<?=$q?>" class="btn btn-danger">Volver</a><?php
                    }
	                 
	                 if(isset($_GET['admin'])){
                          if($codEstadoX==4){
                          	?><!--<a href="../<?=$urlVerificarSolicitud?>?cod=<?=$codigo?>&q=<?=$q?>" class="btn btn-success">Verificar Solicitud</a>
                            <a href="../<?=$urlEdit2?>?cod=<?=$codigo?>&estado=2&q=<?=$q?>" class="btn btn-danger">Anular Solicitud</a>
                            <a href="../<?=$urlEdit2?>?cod=<?=$codigo?>&estado=1&q=<?=$q?>" class="btn btn-default">Rechazar Solicitud</a>--> 
                           <?php
                          }else{
                            ?><!--<a href="../<?=$urlEdit2?>?cod=<?=$codigo?>&estado=2&q=<?=$q?>" class="btn btn-info">Deshacer Cambios</a>--><?php
                          }	
                        }
	                }else{
	                	?><a href="../<?=$urlList;?>" class="btn btn-danger">Volver</a><?php
	                  if(isset($_GET['admin'])){
                          if($codEstadoX==4){
                          	?><a href="../<?=$urlVerificarSolicitud?>?cod=<?=$codigo?>&q=<?=$q?>" class="btn btn-success">Verificar Solicitud</a>
                            <a href="../<?=$urlEdit2?>?cod=<?=$codigo?>&estado=2" class="btn btn-danger">Anular Solicitud</a>
                            <a href="../<?=$urlEdit2?>?cod=<?=$codigo?>&estado=1" class="btn btn-default">Rechazar Solicitud</a> 
                           <?php
                          }else{
                            ?><!--<a href="../<?=$urlEdit2?>?cod=<?=$codigo?>&estado=2" class="btn btn-info">Deshacer Cambios</a>--><?php
                          }	
                        }	 
	                }	   
						?>
				  	</div>
				 </div>
			    </div><!--div end card-->			
               </div>
            </div>
	</div>
</div>