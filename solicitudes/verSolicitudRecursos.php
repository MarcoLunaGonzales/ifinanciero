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
    if($_GET['admin']==4){
    $urlList=$urlList7;
    }else{
      $urlList=$urlList2;  
    }
  }
}

if(isset($_GET['reg'])){
  $urlList=$urlList3;
  if($_GET['reg']==2){
    $urlList=$urlList5;  
  }
}
$observacionGlobal="";
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
            $stmt->bindColumn('glosa_estado', $glosa_estadoX);

?>
<div id="logo_carga" class="logo-carga" style="display:none;"></div>
<div class="content">
	<div id="contListaGrupos" class="container-fluid">
			<input type="hidden" name="cod_solicitudrecursos" id="cod_solicitudrecursos" value="<?=$codigo?>">
           <div class="row">
             <div class="col-sm-12">
			  <div class="card">
				<div class="card-header card-header-deafult card-header-text text-center card-header-primary">
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
                                $anioSol=strftime('%Y',strtotime($fechaX));
                                $mesSol=strftime('%m',strtotime($fechaX));
                                $observacionGlobal=$glosa_estadoX;
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
    <input type="hidden" id="codigo_estado_solicitud" value="<?=$codEstadoX?>">
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
               
          <br>
					<div class="col-sm-12 div-center"><center><h3>Detalle de la Solicitud de Recursos</h3></center></div>
					<div class="col-sm-12 div-center">	
						<table class="table table-bordered table-condensed">
							<thead>
								<tr class="text-dark bg-plomo">
									<th>#</th>
                  <th>Servicio</th>
                  <!--<th>Descripción Servicio</th>-->
									<th>Numero</th>
                  <th class="text-left">Cuenta</th>
                  <th class="text-left">C.C.</th>
									<th>Detalle</th>
									<th>Retenci&oacute;n</th>
                  <th>Proveedor</th>
									<th class="text-right">Ppto.</th>
                  <th class="text-right bg-info text-white">Seguimiento Ppto.</th>
                  <th class="text-right bg-info text-white">%</th>
									<th class="text-right text-white" style="background:#741C89;">Importe</th>			
									<!--<th>Archivos Adjuntos</th>-->
								</tr>
							</thead>
							<tbody>
							<?php 
							$solicitudDetalle=obtenerSolicitudRecursosDetalle($codigo);
							$index=1;$totalImportePres=0;$totalImporte=0;$segPres=0;$porcentSegPres=0;
                             while ($rowDetalles = $solicitudDetalle->fetch(PDO::FETCH_ASSOC)) {
                             	$codCuentaX=$rowDetalles['cod_plancuenta'];
                             	$detalleX=$rowDetalles["detalle"];
                             	$importeX=$rowDetalles["importe_presupuesto"];
							    $importeSolX=$rowDetalles["importe"];
							    $proveedorX=nameProveedor($rowDetalles["cod_proveedor"]);
							    $retencionX=$rowDetalles["cod_confretencion"];
                  $codServicio=$rowDetalles["idServicio"];
							    $totalImportePres+=$importeX;
							    $totalImporte+=$importeSolX;
							    if($retencionX!=0){
							   	  $tituloImporte=nameRetencion($retencionX);
							    }else{
							      $tituloImporte="Ninguno";	
							    }
							    $numeroCuentaX=trim($rowDetalles['numero']);
							    $nombreCuentaX=trim($rowDetalles['nombre']);
                  
                  $codAreaXX=$rowDetalles['cod_area'];
                  $codOficinaXX=$rowDetalles['cod_unidadorganizacional'];

                  $nombreOficinaXX=abrevUnidad_solo($codOficinaXX);
                  $nombreAreaXX=abrevArea_solo($codAreaXX);

                  $datosSeg=obtenerPresupuestoEjecucionDelServicio($codOficinaXX,$codAreaXX,$anioSol,(int)$mesSol,$numeroCuentaX);
            
                  if(!($datosSeg->presupuesto==null||$datosSeg->presupuesto==0)){
                     $segPres=$datosSeg->presupuesto;
                     $porcentSegPres=($datosSeg->ejecutado*100)/$datosSeg->presupuesto; 
                  }
                  $codActividadX=$rowDetalles["cod_actividadproyecto"]; 

                  $tituloActividad=obtenerCodigoActividadesServicioImonitoreo($codActividadX);   

                  $detalleActividadFila="";
                  if($codActividadX>0){
                    if(obtenerNombreDirectoActividadServicio($codActividadX)[0]!=""){
                      $detalleActividadFila.="<br><b class='text-dark small'> Actividad: ".obtenerNombreDirectoActividadServicio($codActividadX)[0]." - ".obtenerNombreDirectoActividadServicio($codActividadX)[1]."</b>";
                    }  
                  }
                  $codAccNum=$rowDetalles["acc_num"]; 

                  if($codAccNum>0){
                   if(obtenerNombreDirectoActividadServicioAccNum($codAccNum)[0]!=""){
                      $detalleActividadFila.="<br><b class='text-dark small'> Acc Num: ".obtenerNombreDirectoActividadServicioAccNum($codAccNum)[0]." - ".obtenerNombreDirectoActividadServicioAccNum($codAccNum)[1]."</b>";
                    }   
                  }
                  
                  $codDivisionX=$rowDetalles["cod_divisionpago"];  
                  $detalleDivisionFila="";
                  if($codDivisionX>0){
                    if(obtenerNombreDivisionPago($codDivisionX)!=""){
                      $detalleDivisionFila.="<b class='text-dark small'>(".obtenerNombreDivisionPago($codDivisionX).")</b>";
                    }  
                  }
                                ?>
                                <tr>
                                    <td><?=$index?></td>
                                    <td  class="text-left small font-weight-bold"><?=obtenerDatosServicioCodigo($codServicio)[0]?></td>
                                    <!--<td  class="text-left small"><?=obtenerDatosServicioCodigo($codServicio)[1]?></td>-->
                                	<td class="text-center small"><?=$numeroCuentaX?></td>
                                    <td class="text-left small font-weight-bold"><?=$nombreCuentaX?></td>
                                    <td class="text-left small font-weight-bold text-primary"><?=$nombreOficinaXX?>-<?=$nombreAreaXX;?></td>
                                    <td class="text-left font-weight-bold text-primary"><?=$detalleX?> <?=$detalleActividadFila?> <?=$detalleDivisionFila?></td>
                                    <td class="text-left small"><?=$tituloImporte?></td>
                                    <td class="text-left text-primary font-weight-bold"><?=$proveedorX?></td>
                                    <td class="text-right"><?=number_format($importeX, 2, '.', ',')?></td>
                                    <td class="text-center" style="background:#6CE2F0;"><?=number_format($segPres, 0, '.', ',')?></td>
                                    <td class="text-center" style="background:#6CE2F0;"><?=number_format($porcentSegPres, 0, '.', '')?> %</td>
                                    <td class="text-right" style="background:#C100F1;"><?=number_format($importeSolX, 2, '.', ',')?></td>
                                    <!--<td><?=obtenerDirectoriosSol("../assets/archivos-respaldo/archivos_solicitudes/SOL-".$codigo."/DET-".$index);?></td>-->
                                </tr><?php
                              $index++;
                             }
                        	?>
                        	  <tr class="font-weight-bold bg-white text-dark">
                        	  	    <td colspan="10" class="text-left">Total</td>
                                    <td class="text-right"><?=number_format($totalImportePres, 2, '.', ',')?></td>
                                    <td class="text-right"><?=number_format($totalImporte, 2, '.', ',')?></td>
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
                  $stmtArchivo = $dbh->prepare("SELECT * from ibnorca.vw_plantillaDocumentos where idTipoServicio=2708"); //2708 //2708 localhost
                  $stmtArchivo->execute();
                  $filaA=0;
                  while ($rowArchivo = $stmtArchivo->fetch(PDO::FETCH_ASSOC)) {
                     $filaA++;
                     $codigoX=$rowArchivo['idClaDocumento'];
                     $nombreX=$rowArchivo['Documento'];
                     $ObligatorioX=$rowArchivo['Obligatorio'];
                     $Obli='NO';
                     if($ObligatorioX==1){
                      $Obli='SI<input type="hidden" id="obligatorio_file'.$filaA.'" value="1">';
                     }
                     //2708 cabecera //27080 detalle
                     $verificarArchivo=verificarArchivoAdjuntoExistente(2708,$codigo,0,$codigoX);
                     //$nombreX=$verificarArchivo[1];
                     $urlArchivo=$verificarArchivo[2];
                     $codigoArchivoX=$verificarArchivo[3];
                     $downloadFile='download="Doc - IFINANCIERO ('.$nombreX.')"';
                     $onClick='onClick="quitarArchivoSistemaAdjunto('.$filaA.','.$codigoArchivoX.',0)"';
                     
                     $arrayArchivo=explode("/",$urlArchivo);
                     $nombreArchivo=$arrayArchivo[count($arrayArchivo)-1];
                     if(obtenerValorConfiguracion(93)==1){
                      $banderaArchivo=obtenerBanderaArchivoIbnorca('archivos_adjuntos',$codigoArchivoX);
                      if($banderaArchivo>0){
                         $urlArchivo=obtenerValorConfiguracion(95)."?idR=".$banderaArchivo;
                         $downloadFile='target="_blank"';
                         $globalServerDelete=obtenerValorConfiguracion(94);
                         $onClick='onClick="ajaxDeleteArchivo(\''.$globalServerDelete.'\',\''.$banderaArchivo.'\',\'divArchivo\',15,\''.$codigoArchivoX.'\')"';
                      }                      
                     } 
                  ?>
                  <tr>
                    <td class="text-left"><input type="hidden" name="codigo_archivo<?=$filaA?>" id="codigo_archivo<?=$filaA?>" value="<?=$codigoX;?>"><input type="hidden" name="nombre_archivo<?=$filaA?>" id="nombre_archivo<?=$filaA?>" value="<?=$nombreX;?>"><?=$nombreX;?></td>
                    <td class="text-center"><?=$Obli?></td>
                    <td class="text-right">
                      <?php
                      if($verificarArchivo[0]==0){
                       ?>
                       No existe
                       <?php
                      }else{
                        ?>
                        <small id="existe_archivo_cabecera<?=$filaA?>"></small>

                        <small id="label_txt_documentos_cabecera<?=$filaA?>"></small> 
                        <span class="input-archivo">
                          <input type="file" class="archivo" name="documentos_cabecera<?=$filaA?>" id="documentos_cabecera<?=$filaA?>"/>
                        </span>
                        <div class="btn-group" id="existe_div_archivo_cabecera<?=$filaA?>">
                          <div class='btn-group'>
                            <a class='btn btn-sm btn-info btn-block' href='<?=$urlArchivo?>' target='_blank'><?=$nombreX?></a>
                            <a class='btn btn-sm btn-default' href='<?=$urlArchivo?>' <?=$downloadFile?>><i class='material-icons'>vertical_align_bottom</i></a>           
                            <a class='btn btn-sm btn-primary' id="boton_previo<?=$filaA?>" href='#' onclick='vistaPreviaArchivoSol("<?=$urlArchivo?>","Descargar: Doc - IFINANCIERO (<?=$nombreX?>)"); return false;'><i class='material-icons'>remove_red_eye</i></a>
                            <script>
                               >/* if ( navigator.userAgent.indexOf("MSIE")>0|| navigator.userAgent.indexOf("Firefox")>0){
                                    $("#boton_previo"+<?=$filaA?>).prepend("X").addClass("btn-danger").attr("title","Puede que su navegador no muestre las firmas digitales en PDF, Recomendamos usar Chrome");    
                                   }*/
                            </script>
                          </div>
                        <!--<a href="#" class="btn btn-button btn-sm">Registrado</a>
                        <a class="btn btn-button btn-info btn-sm" href="<?=$urlArchivo?>" title="Descargar: Doc - IFINANCIERO (<?=$nombreX?>)" download="Doc - IFINANCIERO (<?=$nombreX?>)"><i class="material-icons">get_app</i></a>  -->
                        </div> 
                        <?php
                      }
                    ?>  
                    </td>    
                    <td><?=$nombreX;?></td>
                  </tr> 
                  <?php
                   }
                  $stmtArchivo = $dbh->prepare("SELECT * from archivos_adjuntos where cod_tipoarchivo=-100 and cod_tipopadre=2708 and cod_objeto=$codigo and cod_padre=0"); //2708 //2708 localhost
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
                     $arrayArchivo=explode("/",$urlArchivo);
                     $nombreArchivo=$arrayArchivo[count($arrayArchivo)-1];

                     $onClick='onClick="quitarArchivoSistemaAdjunto('.$filaA.','.$codigoArchivoX.',1)"';
                     $downloadFile='download="Doc - IFINANCIERO ('.$nombreX.')"';
                     if(obtenerValorConfiguracion(93)==1){
                      $banderaArchivo=obtenerBanderaArchivoIbnorca('archivos_adjuntos',$codigoArchivoX);
                      if($banderaArchivo>0){
                         $urlArchivo=obtenerValorConfiguracion(95)."?idR=".$banderaArchivo;
                         $downloadFile='target="_blank"';
                         $onClick='onClick="ajaxDeleteArchivo(\''.$globalServerDelete.'\',\''.$banderaArchivo.'\',\'divArchivo\',15,\''.$codigoArchivoX.'\')"';
                      }                      
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
                        <a class='btn btn-sm btn-default' href='<?=$urlArchivo?>' <?=$downloadFile?>><i class='material-icons'>vertical_align_bottom</i></a>
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
          <div class="col-sm-12"><center><h3>HISTORIAL - CAMBIO DE ESTADOS</h3></center></div>
          <div class="col-sm-12 div-center">
              <table class="table table-condensed table-bordered">
                <thead>
                  <tr class="bg-primary text-white" style="border:none; border: #741899 solid 9px;background:#C10731 !important;border-radius:10px;">
                    <td>#</td>
                    <td>DEL ESTADO</td>
                    <td>AL ESTADO</td>
                    <td>PERSONAL</td>
                    <td>FECHA</td> 
                    <td>OBSERVACIONES</td>  
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $observacionGlobal = preg_replace("[\n|\r|\n\r]", ", ", $observacionGlobal);
                  $glosaArray=explode("####", $observacionGlobal);
                  $observacionGlobal = str_replace("####", " - ", $observacionGlobal);
                  
                  if(isset($glosaArray[1])){
                      $observacionGlobal= "".$glosaArray[0].""."<u class='text-muted'> ".$glosaArray[1]."</u>";
                  }

                  $stmtEstado = $dbh->prepare("SELECT e.*,c.Descripcion
                         FROM ibnorca.estadoobjeto e 
                         join ibnorca.clasificador c on c.idClasificador=e.idEstado
                         where e.idtipoobjeto=2708 and e.idobjeto=$codigo ORDER BY e.fechaestado;");
                  $stmtEstado->execute();
                  $stmtEstado->bindColumn('IdEstadoObjeto', $idEstadoObjetoX);
                  $stmtEstado->bindColumn('IdEstado', $idEstadoX);
                  $stmtEstado->bindColumn('idResponsable', $idResponsableX);
                  $stmtEstado->bindColumn('FechaEstado', $fechaEstadoX);
                  $stmtEstado->bindColumn('Descripcion', $observacionesX);
                  $index=0;
                  while ($rowEstado = $stmtEstado->fetch(PDO::FETCH_BOUND)) {
                    $index++;
                    $responsableX=namePersonal(obtenerPersonaClienteCambioEstado($idResponsableX));
                    $estadoActualX=obtenerNombreEstadoSol(obtenerEstadoIfinancieroSolicitudes($idEstadoX));
                    $estadoAnteriorX=obtenerEstadoAnteriorEstadoObjeto(2708,$codigo,$idEstadoObjetoX);
                    ?>
                    <tr id="<?=$index?>_fila_estado">
                      <td><?=$index?></td>
                      <td class="text-left"><?=$estadoAnteriorX?></td>
                      <td id="<?=$index?>_nombre_fila_estado" class="text-left font-weight-bold"><?=$estadoActualX?></td>
                      <td class="text-left font-weight-bold"><?=$responsableX?></td>         
                      <td><?=strftime('%d/%m/%Y %H:%M:%S',strtotime($fechaEstadoX));?></td>
                      <td id="<?=$index?>_nombre_observacion"></td>
                    </tr>
                    <?php
                  }
                  ?> 
                </tbody>
              </table>
          </div>  
          <script>
            $("#<?=$index?>_fila_estado").addClass("bg-principal text-white");
            if($("#codigo_estado_solicitud").val()<8){
              $("#<?=$index?>_nombre_fila_estado").append(" (ACTUAL)");
              $("#<?=$index?>_nombre_observacion").append("<small><small><?=$observacionGlobal?></small></small>");
            }
          </script>
					<br>
				  	<div class="card-footer fixed-bottom col-sm-12">
						
						<?php 
            if(isset($_GET['v_cajachica'])||isset($_GET['comp'])){

            }else{
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