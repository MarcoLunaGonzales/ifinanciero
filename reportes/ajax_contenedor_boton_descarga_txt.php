<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../styles.php';

$url=$_GET['url'];

?>

 <div class="modal-footer">
  <a type="button" href="reportes/archivos_txt/<?=$url?>" download="<?=$url?>" class="btn btn-success" id="guardarFacturaPagos" name="guardarFacturaPagos">Descargar TXT</a>

  <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar </button>
  
</div> 



