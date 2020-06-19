<?php
require_once '../conexion.php';
require_once 'configModule.php';
require_once '../functions.php';
//header('Content-Type: application/json');
//ini_set("display_errors", "1");
$dbh = new Conexion();
$cod_facturacion=$_GET['cod_facturacion'];

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
  $stmtArchivo = $dbh->prepare("SELECT * From archivos_adjuntos_solicitud_facturacion where cod_solicitud_facturacion=$cod_facturacion"); //2708 //2708 localhost
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
    ?>
    <tr>
      <td class="text-left"><input type="hidden" name="codigo_archivoregistrado<?=$filaE?>" id="codigo_archivoregistrado<?=$filaE?>" value="<?=$codigoArchivoX;?>">Otros Documentos</td>      
      <td class="text-right">
        <div class="div-center">
          <?php 
          obtenerDirectoriosSol_solfac("../assets/archivos-respaldo/archivos_solicitudes_facturacion/SOLFAC-".$cod_facturacion);
          ?>
        </div>
        <!-- <div class="btn-group">
          <a href="#" class="btn btn-button btn-sm">Registrado</a>  
          <a class="btn btn-button btn-danger btn-sm" href="ifinanciero/<?=$urlArchivo?>" title="Descargar: Doc - IFINANCIERO (<?=$nombreX?>)" download="Doc - IFINANCIERO (<?=$nombreX?>)"><i class="material-icons">get_app</i></a>  
        </div>    -->  
      </td>    
      <td><?=$nombreX;?></td>
    </tr> 
      <?php                   
  }
  ?>       
</tbody>
</table> 

