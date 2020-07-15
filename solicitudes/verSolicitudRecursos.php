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
  if($_GET['admin']==2){
    $urlList=$urlList4;
  }else{
    $urlList=$urlList2;
  }
}

if(isset($_GET['reg'])){
  $urlList=$urlList3;
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
            $stmt->bindColumn('idServicio', $idServicioX);

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
					<div class="row" id="">
				        <?php 
                            while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                                $solicitante=namePersonal($codPersonalX);
                                $fechaSolicitud=strftime('%d/%m/%Y',strtotime($fechaX));
                                $codigoServicio=obtenerCodigoServicioPorIdServicio($idServicioX);
                                ?>

                        
					
                    <label class="col-sm-1 col-form-label" style="color:#000000; ">Oficina :</label>
<div class="col-sm-2">
  <div class="form-group">
  	<input type="text" class="form-control" readonly="true" value="<?=$unidadX?>" style="background-color:#E3CEF6;text-align: left" >
  </div>
</div>  
<label class="col-sm-1 col-form-label" style="color:#000000; ">Area :</label>
<div class="col-sm-1">
  <div class="form-group">
  	<input type="text" class="form-control" readonly="true" value="<?=$areaX?>" style="background-color:#E3CEF6;text-align: left">
  </div>
</div>  
<label class="col-sm-1 col-form-label" style="color:#000000; ">Servicio :</label>
<div class="col-sm-2">
  <div class="form-group">
  	<input type="text" class="form-control" readonly="true" value="<?=$codigoServicio?>" style="background-color:#E3CEF6;text-align: left" >
  </div>
</div>  
<label class="col-sm-1 col-form-label" style="color:#000000; ">Fecha Solicitud:</label>
<div class="col-sm-2">
  <div class="form-group">
  	<input type="text" class="form-control" readonly="true" value="<?=$fechaSolicitud?>" style="background-color:#E3CEF6;text-align: left" >
  </div>
</div> 
</div>
<div class="row">
<label class="col-sm-1 col-form-label" style="color:#000000; ">N&uacute;mero</label>
<div class="col-sm-2">
  <div class="form-group">
  	<input type="text" class="form-control" readonly="true" value="<?=$numeroX?>" style="background-color:#E3CEF6;text-align: left" >
  </div>
</div> 
<label class="col-sm-1 col-form-label" style="color:#000000; ">Estado</label>
<div class="col-sm-1">
  <div class="form-group">
  	<input type="text" class="form-control" readonly="true" value="<?=$estadoX?>" style="background-color:#E3CEF6;text-align: left" >
  </div>
</div> 
<label class="col-sm-1 col-form-label" style="color:#000000; ">Solicitante</label>
<div class="col-sm-3">
  <div class="form-group">
  	<input type="text" class="form-control" readonly="true" value="<?=$solicitante?>" style="background-color:#E3CEF6;text-align: left" >
  </div>
</div>         <?php

                  } ?>
                    </div>
                    <div class="col-sm-12"><center><h3>ARCHIVOS ADJUNTOS DE LA CABECERA</h3></center></div>
					<div class="row col-sm-12">
                        
						<div class="div-center">
							<?php 
							obtenerDirectoriosSol("../assets/archivos-respaldo/archivos_solicitudes/SOL-".$codigo);
							?>
						</div>
						<div class="col-sm-8" id="cont_archivos">						
						</div>	
					</div>
					<div class="col-sm-4 div-center"><center><h3>Detalle de la Solicitud de Recursos</h3></center></div>
					<div class="col-sm-12 div-center">	
						<table class="table table-bordered table-condensed">
							<thead>
								<tr class="text-dark bg-plomo">
									<th>#</th>
									<th>Numero</th>
									<th class="text-left">Nombre Cuenta</th>
									<th>Detalle</th>
									<th>Retenci&oacute;n</th>
									<th class="text-right">Presupuestado</th>
									<th class="text-right">Importe</th>			
									<th>Proveedor</th>
									<th>Archivos Adjuntos</th>
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
                                    <td><?=obtenerDirectoriosSol("../assets/archivos-respaldo/archivos_solicitudes/SOL-".$codigo."/DET-".$index);?></td>
                                </tr><?php
                              $index++;
                             }
                        	?>
                        	  <tr class="font-weight-bold bg-white text-dark">
                        	  	    <td colspan="5" class="text-left">Total</td>
                                    <td class="text-right"><?=number_format($totalImportePres, 2, '.', ',')?></td>
                                    <td class="text-right"><?=number_format($totalImporte, 2, '.', ',')?></td>
                                    <td></td>
                                    <td></td>
                        	  </tr>
							</tbody>
						</table>
					</div>	
					<br><br><br>
					<hr>
					<div class="col-sm-12 text-info"><center><h4 id="titulo_vista_previa">VISTA PREVIA</h4></center></div>
					<div class="row col-sm-12">
                      <iframe src="../vista_file.html"  id="vista_previa_frame" width="800" class="div-center" height="600" scrolling="yes" style="border:none; border: #8CF5E3 solid 3px;border-radius:10px;">
                      	No hay vista disponible
                      </iframe>
					</div>	
					<br>
					<br>
				  	<div class="card-footer fixed-bottom col-sm-12">
						
						<?php 
					if(isset($_GET['q'])){
	                 $q=$_GET['q'];
	                 $s=$_GET['s'];
	                 $u=$_GET['u'];
	                 
	                 if(isset($_GET['r'])){
                      $r=$_GET['r'];
                      ?><a href="../<?=$urlList;?>&q=<?=$q?>&r=<?=$r?>&s=<?=$s?>&u=<?=$u?>" class="btn btn-danger">Volver</a><?php
                    }else{
                    	$v=$_GET['v'];
                      ?><a href="../<?=$urlList;?>&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>" class="btn btn-danger">Volver</a><?php
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
                          	?><!--<a href="../<?=$urlVerificarSolicitud?>?cod=<?=$codigo?>" class="btn btn-success">Verificar Solicitud</a>
                            <a href="../<?=$urlEdit2?>?cod=<?=$codigo?>&estado=2" class="btn btn-danger">Anular Solicitud</a>
                            <a href="../<?=$urlEdit2?>?cod=<?=$codigo?>&estado=1" class="btn btn-default">Rechazar Solicitud</a> -->
                           <?php
                          }else{
                            ?><!--<a href="../<?=$urlEdit2?>?cod=<?=$codigo?>&estado=2" class="btn btn-info">Deshacer Cambios</a>--><?php
                          }	
                        }	 
	                }	   
						?>
						<div class="row col-sm-9 float-right">
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label class="bmd-label-static fondo-boton">Presupuestado</label>  
                          <input class="form-control fondo-boton-active text-center" style="border-radius:10px;" type="number" step=".01" placeholder="0" value="<?=$totalImportePres?>" id="total_presupuestado" readonly="true"> 
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                          <label class="bmd-label-static fondo-boton">Solicitado</label> 
                          <input class="form-control fondo-boton-active text-center" style="border-radius:10px;" type="number" step=".01" placeholder="0" value="<?=$totalImporte?>" id="total_solicitado" readonly="true"> 
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