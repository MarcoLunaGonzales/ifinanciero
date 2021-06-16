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
$codigo=$_GET['proveedor'];
$fila=$_GET['fila'];
$cantidad_modal=$_GET['cantidad_modal'];
$contador_items=$cantidad_modal+1;//contador de todos los intems

$lista=listaObligacionesPagoDetalleSolicitudRecursosProveedor($codigo);
$totalPagadoX=0;

$index=1;$cont=0;$totalImporte=0;

while ($row = $lista->fetch(PDO::FETCH_ASSOC)) {
  $codDetalle=$row['codigo'];
  $unidad=$row['unidad'];
  $area=$row['area'];
  $solicitante=namePersonal($row['cod_personal']);
  $fecha=$row['fecha'];
  $numero=$row['numero'];
  $detalle=$row['detalle'];
  $importe=$row['importe'];
  $proveedor=$row['proveedor'];
  $codProveedor=$row['cod_proveedor'];
  $codPlancuenta=$row['cod_plancuenta'];
  $codSol=$row['cod_solicitudrecurso'];
  $codSolDet=$codDetalle;

  $dias=obtenerCantidadDiasCredito($codProveedor);
  $pagadoFila=obtenerMontoPagadoDetalleSolicitud($codSol,$codDetalle);
  if($dias==0){
    $tituloDias="Sin Registro";
  }else{
    $tituloDias="".$dias;
  }
  $totalImporte+=$importe;
  $saldoImporte=abs($pagadoFila-$importe);
  $pagado=$importe-$saldoImporte;
  
  $numeroComprobante=nombreComprobante($row['cod_comprobante']);
  $codTipoPago=$row['cod_tipopagoproveedor'];
  $nomBen=$row['nombre_beneficiario'];
  $apellBen=$row['apellido_beneficiario']; ?>
  <tr class="fila_proveedor<?=$fila?>">  
  <td class="text-left">
    <input type="hidden" value="<?=$codigo?>" id="codigo_proveedor_modal<?=$fila?>" name="codigo_proveedor_modal<?=$fila?>">
    <?php 
    if($index==1){
      ?><input type="hidden" value="" id="cantidad_filas<?=$fila?>" name="cantidad_filas<?=$fila?>"><?php
    }
    ?>
    <input type="hidden" value="<?=$detalle?>" id="glosa_detalle<?=$index?>PPPP<?=$fila?>" name="glosa_detalle<?=$index?>PPPP<?=$fila?>">
    <input type="hidden" value="<?=$codProveedor?>" id="codigo_proveedor<?=$index?>PPPP<?=$fila?>" name="codigo_proveedor<?=$index?>PPPP<?=$fila?>">
    <input type="hidden" value="<?=$codSol?>" id="codigo_solicitud<?=$index?>PPPP<?=$fila?>" name="codigo_solicitud<?=$index?>
    PPPP<?=$fila?>">
    <input type="hidden" value="<?=$codSolDet?>" id="codigo_solicitudDetalle<?=$contador_items?>" name="codigo_solicitudDetalle<?=$contador_items?>">
    <input type="hidden" value="<?=$codPlancuenta?>" id="codigo_plancuenta<?=$index?>PPPP<?=$fila?>" name="codigo_plancuenta<?=$index?>PPPP<?=$fila?>">
    <?=$proveedor;?></td>
  <td class="text-left"><?=$detalle;?></td>
  <td class="text-left"><?=strftime('%d/%m/%Y',strtotime($fecha));?></td>  
  <td class=""><?=$numero;?></td>
  <td><?=$numeroComprobante?></td>
  <td><?=$unidad?></td>
  <td class="bg-warning text-dark text-right font-weight-bold"><?=number_format($importe,2,".","")?></td>
  <td class="text-right font-weight-bold" style="background:#07B46D; color:#F7FF5A;"><?=number_format($pagado,2,".","")?></td>
  <td id="saldo_pago<?=$index?>PPPP<?=$fila?>" class="text-right font-weight-bold"><?=number_format($importe-$pagado,2,".","")?></td> 
  <td class="text-right">
    <div class="togglebutton">
       <label>
         <input type="checkbox"  id="pagos_seleccionados<?=$contador_items?>" name="pagos_seleccionados<?=$contador_items?> ">
         <span class="toggle"></span>
       </label>
   </div>
  </td>
</tr>
  <?php
    $index++;
    $contador_items++;
}

if($index>1){
  $proveedor_nombre=$_GET['proveedor_nombre'];?>
   <script>
    // var html='<tr id="f_proveedor<?=$fila?>"><td class="text-left"><?=$proveedor_nombre?></td><td><div class="btn-group"><button class="btn btn-sm btn-fab btn-danger" title="Eliminar" onclick="removeListaPago(<?=$fila?>);"><i class="material-icons">delete</i></button></div></td></tr>';
    // $("#tabla_proveedor").append(html);
    $("#cantidad_filas<?=$fila?>").val(<?=$index-1?>);
  </script><?php
} ?>
<script>$("#cantidad_proveedores_modal").val(parseInt($("#cantidad_proveedores_modal").val())+<?=$index-1?>);
  //$("#cantidad_proveedores").val(parseInt($("#cantidad_proveedores").val())+<?=$index-1?>);
</script>
<script type="text/javascript">
  $(document).ready(function(e) {
    if(!($("body").hasClass("sidebar-mini"))){
     	$("#minimizeSidebar").click()
    } 
  });
</script>