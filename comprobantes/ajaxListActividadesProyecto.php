<?php
session_start();
require_once '../conexion.php';
require_once '../functions.php';
$dbh = new Conexion();

$cod_actividad=$_GET['codigo'];
$acc_num=$_GET['acc'];


 $listaActividad= obtenerActividadesServicioImonitoreo(1); 
 $listaAcc= obtenerAccServicioImonitoreo(1); 
?>
<tr>
<td>-</td>
<td>
 <select data-size="6" data-live-search="true" class="selectpicker form-control form-control-sm col-sm-12" name="actividades_detalle" id="actividades_detalle" data-style="btn btn-info">                                  
 <option disabled selected value="">--SELECCIONE ACTIVIDAD--</option>
<?php
    foreach ($listaActividad as $listas) { 
      ?>
      <option value="<?=$listas->codigo?>" <?=($listas->codigo==$cod_actividad)?"selected":"";?> class="text-right"><?=$listas->abreviatura?> - <?=substr($listas->nombre, 0, 85)?></option>

<?php }?>
</select>
 <select data-size="6" data-live-search="true" class="selectpicker form-control form-control-sm col-sm-12" name="acc_detalle" id="acc_detalle" data-style="btn btn-danger">                                  
 <option disabled selected value="">--SELECCIONE ACC--</option>
<?php
    foreach ($listaAcc as $listasacc) { ?>
      <option value="<?=$listasacc->codigo?>" <?=($listasacc->codigo==$acc_num)?"selected":"";?> class="text-right"><?=$listasacc->abreviatura?> - <?=substr($listasacc->nombre, 0, 85)?></option>

<?php }?>
</select>
</td>
<td>-</td>
</tr>