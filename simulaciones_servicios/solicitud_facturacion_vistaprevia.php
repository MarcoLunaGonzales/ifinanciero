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
if(isset($_GET['codigo'])){
	$codigo=$_GET['codigo'];
}else{
	$codigo=0;
}
// if(isset($_GET['admin'])){
//   if($_GET['admin']==2){
//     $urlList=$urlList4;
//   }else{
//     $urlList=$urlList2;
//   }
// }

// if(isset($_GET['reg'])){
//   $urlList=$urlList3;
//   if($_GET['reg']==2){
//     $urlList=$urlList5;  
//   }
// }

$stmt = $dbh->prepare("SELECT p.*,e.nombre as estado_solicitud, u.abreviatura as unidad,a.abreviatura as area 
        from solicitudes_facturacion p,unidades_organizacionales u, areas a,estados_solicitudfacturacion e
  where p.cod_unidadorganizacional=u.codigo and p.cod_area=a.codigo and e.codigo=p.cod_estadosolicitudfacturacion and p.codigo='$codigo' order by codigo");
      $stmt->execute();
      $stmt->bindColumn('codigo', $codigoX);
            $stmt->bindColumn('cod_personal', $codPersonalX);
            $stmt->bindColumn('fecha_registro', $fechaX);
            $stmt->bindColumn('cod_unidadorganizacional', $codUnidadX);
            $stmt->bindColumn('cod_area', $codAreaX);
            $stmt->bindColumn('area', $areaX);
            $stmt->bindColumn('unidad', $unidadX);
            $stmt->bindColumn('estado_solicitud', $estadoX);
            $stmt->bindColumn('cod_estadosolicitudfacturacion', $codEstadoX);
            $stmt->bindColumn('nro_correlativo', $numeroX);
            $stmt->bindColumn('cod_simulacion_servicio', $codSimulacionX);
            $stmt->bindColumn('cod_cliente', $codProveedorX);
            $stmt->bindColumn('cod_tipopago', $codProveedorX);
            $stmt->bindColumn('cod_cliente', $cod_clienteX);            
            $stmt->bindColumn('observaciones', $observacionesX);
            $stmt->bindColumn('codigo_alterno', $codigoServicio);
            

?>
<div id="logo_carga" class="logo-carga" style="display:none;"></div>
<div class="content">
	<div id="contListaGrupos" class="container-fluid">
		<input type="hidden" name="cod_solicitudFacturacion" id="cod_solicitudFacturacion" value="<?=$codigo?>">
    <div class="row">
      <div class="col-sm-12">
			  <div class="card">
				<div class="card-header card-header-deafult card-header-text text-center card-header-primary">
					<div class="card-text">
					  <h4 class="card-title"><b>SOLICITUD FACTURACIÓN</b></h4>
					</div>
				</div>
				<div class="card-body">
					<div class=""> 	
					<div class="row" id="">
				    <?php 
            while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
              $solicitante=namePersonal($codPersonalX);
              $fechaSolicitud=strftime('%d/%m/%Y',strtotime($fechaX));
              // $codigoServicio=obtenerCodigoServicioPorIdServicio($idServicioX);

              $anioSol=strftime('%Y',strtotime($fechaX));
              $mesSol=strftime('%m',strtotime($fechaX));
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
              </div><?php

            } ?>
              </div>
               
          <br>
					<div class="col-sm-12 div-center"><center><h3>Detalle de la Solicitud de Facturación</h3></center></div>
					<div class="col-sm-12 div-center">	
						<table class="table table-bordered table-condensed">
							<thead>
								<tr class="text-dark bg-plomo">
                  <th>#</th>
                  <th width="40%">Item</th>
                  <th width="5%">Cant.</th>
                  <th width="5%">Precio(BOB)</th>                  
                  <!-- <th>Desc(BOB)</th>
                  <th width="6%">Importe<br>(BOB)</th>
                  <th width="6%">Importe<br>Pagado</th> -->
                  <th width="6%" class="text-right text-white" style="background:#741C89;">Importe</th>  
                  <th width="40%">Glosa</th>									
								</tr>
							</thead>
							<tbody>
							<?php 
  							$solicitudDetalle=obtenerSolicitudFacturacionDetalle($codigo);
  							$index=1;$totalpu=0;$totalcantidad=0;$totalImporte=0;
                while ($rowDetalles = $solicitudDetalle->fetch(PDO::FETCH_ASSOC)) {
                 	$cod_claservicioX=$rowDetalles['cod_claservicio'];
                 	$cantidadX=$rowDetalles["cantidad"];
                 	$precioX=$rowDetalles["precio"];
							    $descripcion_alternaX=$rowDetalles["descripcion_alterna"];							    
							    $descuento_bobX=$rowDetalles["descuento_bob"];
                  $descuento_porX=$rowDetalles["descuento_por"];
                  $cod_cursoX=$rowDetalles["cod_curso"];
                  $ci_estudianteX=$rowDetalles["ci_estudiante"];

                  $importeX=$precioX*$cantidadX;
                  $descripcion_Item=$codigoServicio." - ".descripcionClaServicio($cod_claservicioX);
                  $totalpu+=$precioX;
                  $totalcantidad+=$cantidadX;
							    $totalImporte+=$importeX;
							    ?>
                  <tr>
                    <td><?=$index?></td>
                  	<td class="font-weight-bold"><?=$descripcion_Item?></td>
                    <td class="text-left"><?=$cantidadX?></td>                    
                    <td class="text-left"><?=$precioX?></td>
                    <td class="text-right" style="background:#C100F1;"><?=number_format($importeX, 2, '.', ',')?></td>
                    <td><?=$descripcion_alternaX?></td>                      
                  </tr><?php $index++;
                } ?>
            	  <tr class="font-weight-bold bg-white text-dark">
            	  	    <td colspan="2" class="text-left">Total</td>
                        <td class="text-right"><?=number_format($totalcantidad, 2, '.', ',')?></td>
                        <td class="text-right"><?=number_format($totalpu, 2, '.', ',')?></td>
                        <td class="text-right"><?=number_format($totalImporte, 2, '.', ',')?></td>
                        <td></td>
            	  </tr>
							</tbody>
						</table>
					</div>
               <div class="col-sm-12"><center><h3>ARCHIVOS ADJUNTOS</h3></center></div>
          <div class="row col-sm-12">
                        
            <div class="col-sm-12">
              <div class="row col-sm-12 div-center">
              <table class="table table-warning table-bordered table-condensed">
                <thead>
                  <tr>
                    <th class="small" width="30%">Tipo de Documento </th>
                    <th class="small">Obligatorio</th>
                    <th class="small" width="35%">Archivo</th>
                    <th class="small">Descripción</th>                  
                  </tr>
                </thead>
                <tbody id="tabla_archivos">
                  <?php
                  // $stmtArchivo = $dbh->prepare("SELECT * from ibnorca.vw_plantillaDocumentos where idTipoServicio=2708"); //2708 //2708 localhost
                  // $stmtArchivo->execute();
                  $filaA=0;
                  
                  $stmtArchivo = $dbh->prepare("SELECT * from archivos_adjuntos_solicitud_facturacion where cod_solicitud_facturacion=$codigo"); //2708 //2708 localhost
                  $stmtArchivo->execute();
                  $filaE=0;
                  while ($rowArchivo = $stmtArchivo->fetch(PDO::FETCH_ASSOC)) {
                     $filaE++;
                     $filaA++;
                     $codigoArchivoX=$rowArchivo['codigo'];
                     $codigoX=$rowArchivo['cod_tipoarchivo'];
                     $nombreX=$rowArchivo['descripcion'];
                     $urlArchivo=$rowArchivo['direccion_archivo'];
                     $ObligatorioX=0;
                     $Obli='NO';
                     if($ObligatorioX==1){
                      $Obli='SI';
                     }
                     if($nombreX==""){
                      $nombreX="Registrado";
                     }
                  ?>
                  <tr id="fila_archivo<?=$filaA?>">
                    <td class="text-left"><input type="hidden" name="codigo_archivoregistrado<?=$filaE?>" id="codigo_archivoregistrado<?=$filaE?>" value="<?=$codigoArchivoX;?>">Otros Documentos</td>
                    <td class="text-center"><?=$Obli?></td>
                    <td class="text-right">
                      <small id="existe_archivo_cabecera<?=$filaA?>"></small>

                        <small id="label_txt_documentos_cabecera<?=$filaA?>"></small> 
                        <span class="input-archivo">
                          <input type="file" class="archivo" name="documentos_cabecera<?=$filaA?>" id="documentos_cabecera<?=$filaA?>"/>
                        </span>
                      <div class="btn-group">
                        <!--<a href="#" class="btn btn-button btn-sm" >Registrado</a>  
                        <a class="btn btn-button btn-info btn-sm" href="<?=$urlArchivo?>" title="Descargar: Doc - IFINANCIERO (<?=$nombreX?>)" download="Doc - IFINANCIERO (<?=$nombreX?>)"><i class="material-icons">get_app</i></a>  -->
                        <a class='btn btn-sm btn-info btn-block' href='<?=$urlArchivo?>' target='_blank'><?=$nombreX?></a>
                        <a class='btn btn-sm btn-default' href='<?=$urlArchivo?>' download='Descargar: Doc - IFINANCIERO (<?=$nombreX?>)'><i class='material-icons'>vertical_align_bottom</i></a>
                        <a class='btn btn-sm btn-primary' href='#' onclick='vistaPreviaArchivoSol("<?=$urlArchivo?>","Descargar: Doc - IFINANCIERO (<?=$nombreX?>)"); return false;'><i class='material-icons'>remove_red_eye</i></a>
                      
                      </div>     
                    </td>    
                    <td><?=$nombreX;?></td>
                  </tr> 
                  <?php
                   }
                      ?>     
                </tbody>
              </table>

            </div>
            <div class="" id="cont_archivos">           
            </div>  
          </div>	
					<br><br><br>
					<hr>
					<div class="col-sm-12 text-info font-weight-bold"><center><label id="titulo_vista_previa"><b>SELECCIONE UN ARCHIVO</b></label></center></div>
					<div class="row col-sm-12">
                      <iframe src="../vista_file.html"  id="vista_previa_frame" width="800" class="div-center" height="600" scrolling="yes" style="border:none; border: #741899 solid 9px;border-radius:10px;">
                      	No hay vista disponible
                      </iframe>
					</div>	
					<br>
					<br>				  
				 </div>
			    </div><!--div end card-->			
               </div>
            </div>
	</div>
</div>