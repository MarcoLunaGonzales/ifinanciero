<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();
$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();
$globalNombreGestion=$_SESSION["globalNombreGestion"];
$globalMes=$_SESSION['globalMes'];

$codigo=$_POST['codigo'];
$tipo=$_POST['tipo'];

$sqlDetalle="SELECT sd.codigo,sd.cod_plancuenta,sd.detalle,sd.importe,sd.cod_proveedor,sd.nombre_beneficiario,sd.apellido_beneficiario,sd.cod_actividadproyecto,sd.acc_num FROM solicitud_recursosdetalle sd where sd.cod_solicitudrecurso=$codigo";
$stmt = $dbh->prepare($sqlDetalle);
$stmt->execute();
$cantidad=0;

$listaActividad= obtenerActividadesServicioImonitoreo(1); 
$listaAcc= obtenerAccServicioImonitoreo(1);

?>
<table class="table table-sm table-condensed table-bordered small">
  <thead>
    <tr class="btn-orange">
      <td>#</td>
      <td>DETALLE</td>
      <td>CUENTA</td>
      <td>PROVEEDOR</td>
      <td>IMPORTE</td>
      <td>ACTIVIDAD PROYECTO</td>
    </tr>
  </thead>
  <tbody>
<?php
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $codigoDetalle=$row['codigo'];
  $cuenta=strtoupper(nameCuenta($row['cod_plancuenta']));
  $detalle=strtoupper($row['detalle']);
  $importe=number_format($row['importe'],2,'.',',');
  $proveedor=nameProveedor($row['cod_proveedor']);
  $beneficiario=strtoupper($row['nombre_beneficiario']." ".$row['apellido_beneficiario']);

  $codActividad=$row['cod_actividadproyecto'];
  $codAccNum=$row['acc_num'];
  ?>
  <tr class="small">
    <td width="4%"><?=($cantidad+1)?></td>
    <td><?=$detalle?></td>
    <td width="15%"><?=$cuenta?></td>
    <td width="15%"><?=$proveedor?></td>
    <td width="7%" class="font-weight-bold text-right"><b><?=$importe?></b></td>
    <td width="30%">  
 <select data-size="6" data-live-search="true" class="selectpicker form-control form-control-sm col-sm-12" name="actividades_detalle<?=$cantidad?>" id="actividades_detalle<?=$cantidad?>" data-style="btn btn-info">                                  
 <option disabled selected value="">--SELECCIONE ACTIVIDAD--</option>
<?php
    foreach ($listaActividad as $listas) { 
      if($tipo==1){
        ?>
          <option value="<?=$listas->codigo?>" <?=($listas->codigo==$codActividad)?"selected":"";?> class="text-right"><?=$listas->abreviatura?> - <?=substr($listas->nombre, 0, 85)?></option>

       <?php
      }else{
        if($listas->codigo==$codActividad){
          ?><option value="<?=$listas->codigo?>" selected class="text-right"><?=$listas->abreviatura?> - <?=substr($listas->nombre, 0, 85)?></option><?php
        }
      }
       }?>
</select>
 <select data-size="6" data-live-search="true" class="selectpicker form-control form-control-sm col-sm-12" name="acc_detalle<?=$cantidad?>" id="acc_detalle<?=$cantidad?>" data-style="btn btn-danger">                                  
 <option disabled selected value="">--SELECCIONE ACC--</option>
<?php
    foreach ($listaAcc as $listasacc) { ?>
      <option value="<?=$listasacc->codigo?>" <?=($listasacc->codigo==$codAccNum)?"selected":"";?> class="text-right"><?=$listasacc->abreviatura?> - <?=substr($listasacc->nombre, 0, 85)?></option>

<?php }?>
</select>
<input type="hidden" id="codigo_detalle<?=$cantidad?>" value="<?=$codigoDetalle?>">
    </td>
  </tr>
  <?php
  $cantidad++;
}

?></tbody></table>
<script>$("#cantidad_registros_detalle_sis").val('<?=$cantidad?>');</script>
