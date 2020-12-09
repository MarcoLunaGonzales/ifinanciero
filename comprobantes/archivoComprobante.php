<?php
session_start();
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../styles.php';

require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once 'configModule.php';

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


if(isset($_GET['codigo'])){
	$codigo=$_GET['codigo'];
}else{
	$codigo=0;
}

$codPadreArchivos=obtenerValorConfiguracion(84);
?>

<div class="content">
	<div class="container-fluid">


			<div class="card">
				<div class="card-header <?=$colorCard;?> card-header-text">
					<div class="card-text">
					  <h4 class="card-title">Vista de Archivos</h4>
					</div>
				</div>
				<div class="card-body ">
					<p>Archivos de respaldo almacenados en el servidor</p>
					<div class="h-divider"></div>
					<div class="row">

						<div class="col-sm-4">
							<?php 
							obtenerDirectorios("../assets/archivos-respaldo/COMP-".$codigo);
							?>
						</div>
						<div class="col-sm-8" id="cont_archivos">
							
						</div>
						
					</div>
					<div class="h-divider"></div>

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
                  $stmtArchivo = $dbh->prepare("SELECT * from ibnorca.vw_plantillaDocumentos where idTipoServicio=$codPadreArchivos"); //$codPadreArchivos //$codPadreArchivos localhost
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
                     //$codPadreArchivos cabecera //$codPadreArchivos0 detalle
                     $verificarArchivo=verificarArchivoAdjuntoExistente($codPadreArchivos,$codigo,0,$codigoX);
                     //$nombreX=$verificarArchivo[1];
                     $urlArchivo=$verificarArchivo[2];
                     $codigoArchivoX=$verificarArchivo[3];
                     $arrayArchivo=explode("/",$urlArchivo);
                     $nombreArchivo=$arrayArchivo[count($arrayArchivo)-1];

                     $downloadFile='download="Doc - IFINANCIERO ('.$nombreX.')"';
                     $onClick='onClick="quitarArchivoSistemaAdjunto('.$filaA.','.$codigoArchivoX.',0)"';
                     if(obtenerValorConfiguracion(93)==1){
                      $banderaArchivo=obtenerBanderaArchivoIbnorca('archivos_adjuntos',$codigoArchivoX);
                      if($banderaArchivo>0){
                         $urlArchivo=obtenerValorConfiguracion(95)."?idR=".$banderaArchivo;
                         $downloadFile='target="_blank"';
                         $globalServerDelete=obtenerValorConfiguracion(94);
                         $onClick='onClick="ajaxDeleteArchivoIbnorca(\''.$globalServerDelete.'\',\''.$banderaArchivo.'\',\'divArchivo\',15,\''.$codigoArchivoX.'\','.$filaA.','.$codigoArchivoX.',0);"';
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
                               /* if ( navigator.userAgent.indexOf("MSIE")>0|| navigator.userAgent.indexOf("Firefox")>0){
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
                  $stmtArchivo = $dbh->prepare("SELECT * from archivos_adjuntos where cod_tipoarchivo=-100 and cod_tipopadre=$codPadreArchivos and cod_objeto=$codigo and cod_padre=0"); //$codPadreArchivos //$codPadreArchivos localhost
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
                         $globalServerDelete=obtenerValorConfiguracion(94);
                         $onClick='onClick="ajaxDeleteArchivoIbnorca(\''.$globalServerDelete.'\',\''.$banderaArchivo.'\',\'divArchivo\',15,\''.$codigoArchivoX.'\','.$filaA.','.$codigoArchivoX.',1);"';
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


				</div>
				<div class="card-footer fixed-bottom">
						<a href="../<?=$urlListReg;?>" class="<?=$buttonCancel;?>"> Volver </a>

				  	</div>
			</div>	
	</div>
</div>
