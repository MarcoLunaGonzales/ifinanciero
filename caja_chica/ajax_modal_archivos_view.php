<?php
require_once '../conexion.php';
require_once 'configModule.php';
require_once '../functions.php';
//header('Content-Type: application/json');
//ini_set("display_errors", "1");
$dbh = new Conexion();
$codigo=$_GET['codigo'];
?>

<table class="table table-warning table-bordered table-condensed">
  <thead>
    <tr>
      <th class="small" width="30%">Tipo de Documento</th>      
      <th class="small" width="35%">Archivo</th>
      <th class="small">Descripción</th>                  
    </tr>
  </thead>
  <tbody id="tabla_archivos">
  <?php
  $sql="SELECT * From archivos_adjuntos_cajachica where cod_cajachica_detalle=$codigo";
  $stmtArchivo = $dbh->prepare($sql); //2708 //2708 localhost
  $stmtArchivo->execute();
  $filaE=0;
  while ($rowArchivo = $stmtArchivo->fetch(PDO::FETCH_ASSOC)) {    
    $filaE++;
    $codigoArchivoX=$rowArchivo['codigo'];
    $codigoX=$rowArchivo['cod_tipoarchivo'];
    $nombreX=$rowArchivo['descripcion'];
    $urlArchivo=$rowArchivo['direccion_archivo'];
    $ObligatorioX=0;    
    $Obli='<i class="material-icons text-danger">clear</i> NO';
    $downloadFile='download="Doc - IFINANCIERO ('.$nombreX.')"';
                     if(obtenerValorConfiguracion(93)==1){
                      $banderaArchivo=obtenerBanderaArchivoIbnorca('archivos_adjuntos_cajachica',$codigoArchivoX);
                      if($banderaArchivo>0){
                         $urlArchivo=obtenerValorConfiguracion(95)."?idR=".$banderaArchivo;
                         $downloadFile='target="_blank"';                                            
                      }                      
                     }
    ?>
    <tr id="fila_archivo<?=$filaE?>">
                    <td class="text-left"><input type="hidden" name="codigo_archivoregistrado<?=$filaE?>" id="codigo_archivoregistrado<?=$filaE?>" value="<?=$codigoArchivoX;?>">Otros Documentos</td>                
                    <td class="text-right">
                        <small id="existe_archivo_cabecera<?=$filaE?>"></small>
                          <small id="label_txt_documentos_cabecera<?=$filaE?>"></small> 
                          <span class="input-archivo">
                            <input type="file" class="archivo" name="documentos_cabecera<?=$filaE?>" id="documentos_cabecera<?=$filaE?>"/>
                          </span>                         
                        <div class="btn-group">
                          <a href="#" class="btn btn-button btn-sm" >Registrado</a>  
                          <a class="btn btn-button btn-info btn-sm" href="<?=$urlArchivo?>" title="Descargar: Doc - IFINANCIERO (<?=$nombreX?>)" <?=$downloadFile?>><i class="material-icons">get_app</i></a>  
                            <!-- Mostrar Archivo -->
                            <button class="btn btn-primary btn-sm verArchivo" title="Ver Archivo" data-archivo="/ifinanciero/<?=$rowArchivo['direccion_archivo']?>">
                                <i id="toggleIcon" class="material-icons">visibility</i>
                            </button>
                        </div> 
                      </td>    
                    <td><?=$nombreX;?></td>
                  </tr> 
      <?php                   
  }
  ?>       
</tbody>
</table> 
<div class="fileViewerContainer d-none">
    <h4 class="file-title"><strong>Visualización de archivo</strong></h4>
    <iframe id="fileViewer" style="width: 100%; height: 400px;" src=""></iframe>
</div>
