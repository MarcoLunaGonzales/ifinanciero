<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();

// $sqlX="SET NAMES 'utf8'";
// $stmtX = $dbh->prepare($sqlX);
// $stmtX->execute();
$proveedoresString=$_GET['proveedor'];
$proveedoresString=implode(",", $proveedoresString);

$cuentas=$_GET['cuentas'];
$cuentas=implode(",", $cuentas);
// $fila=$_GET['fila'];
// $cantidad_modal=$_GET['cantidad_modal'];
//$contador_items=$cantidad_modal+1;//contador de todos los intems

//$lista=listaObligacionesPagoDetalleSolicitudRecursosProveedor($proveedoresString,$cuentas);
$totalPagadoX=0;

$index=1;$cont=0;$totalImporte=0;

///
$i=0;$saldo=0;
$indice=0;
$totalCredito=0;
$totalDebito=0;
$codPlanCuentaAuxiliarPivotX=-10000;
$ver_saldo=1;

$dbh = new Conexion();
$sql="SELECT e.*,d.glosa,d.haber,d.debe,(select concat(c.cod_tipocomprobante,'|',c.numero,'|',cd.cod_unidadorganizacional,'|',MONTH(c.fecha),'|',c.fecha) from comprobantes_detalle cd, comprobantes c where c.codigo=cd.cod_comprobante and cd.codigo=e.cod_comprobantedetalle)as extra, d.cod_cuenta, ca.nombre, cc.codigo as codigocomprobante, cc.cod_unidadorganizacional as cod_unidad_cab, d.cod_area as area_centro_costos FROM estados_cuenta e,comprobantes_detalle d, comprobantes cc, cuentas_auxiliares ca where e.cod_comprobantedetalle=d.codigo and cc.codigo=d.cod_comprobante and e.cod_cuentaaux=ca.codigo and cc.cod_estadocomprobante<>2 and d.cod_cuenta in ($cuentas) and e.cod_comprobantedetalleorigen=0 and e.cod_cuentaaux in ($proveedoresString) order by e.fecha";
$stmt = $dbh->prepare($sql);
echo $sql;
$stmt->execute();
while ($row = $stmt->fetch()) {


  $codigoX=$row['codigo'];
  $existeCuentas=0;
  $stmtCantidad = $dbh->prepare("SELECT count(*) as cantidad
    from estados_cuenta e, comprobantes_detalle d, comprobantes c where c.codigo=d.cod_comprobante and c.cod_estadocomprobante<>2 and e.cod_comprobantedetalle=d.codigo and e.cod_comprobantedetalleorigen=$codigoX");
  $stmtCantidad->execute();
  while ($rowCantidad = $stmtCantidad->fetch()) {
    $existeCuentas=$rowCantidad['cantidad'];
  }
  $existeCuentas2=0;
  $stmtCantidad = $dbh->prepare("SELECT count(*) as cantidad FROM estados_cuenta e,comprobantes_detalle d, comprobantes cc, cuentas_auxiliares ca  where e.cod_comprobantedetalle=d.codigo and cc.codigo=d.cod_comprobante and e.cod_cuentaaux=ca.codigo and cc.cod_estadocomprobante<>2 and d.cod_cuenta in ($cuentas) and e.cod_comprobantedetalleorigen=0 and e.cod_cuentaaux in ($proveedoresString) and e.codigo=$codigoX order by ca.nombre, cc.fecha");
  $stmtCantidad->execute();
  while ($rowCantidad = $stmtCantidad->fetch()) {
    $existeCuentas2=$rowCantidad['cantidad'];
  }
  $mostrarFilasEstado="";
  $estiloFilasEstado="";
  $estiloFilasEstadoSaldo="";
  $sqlFechaEstadoCuenta="";
  if($sqlFechaEstadoCuenta==""){
      if($existeCuentas==0){
        if($existeCuentas2==0){
           $mostrarFilasEstado="d-none";
        }
      }else{
          if($existeCuentas2==0){
           $estiloFilasEstado="style='background:#F9F9FC !important;color:#D6D6DA  !important;'";
           $estiloFilasEstadoSaldo="style='color:red !important;'";
          }      
      }
      
  }

  $codCompDetX=$row['cod_comprobantedetalle'];
  $codPlanCuentaX=$row['cod_cuenta'];
  $codProveedor=$row['cod_proveedor'];
  $montoX=$row['monto'];
  $fechaX=$row['fecha'];
  $fechaX=strftime('%d/%m/%Y',strtotime($fechaX));
  $glosaAuxiliar=$row['glosa_auxiliar'];
  $glosaX=$row['glosa'];
  $debeX=$row['debe'];
  $haberX=$row['haber'];
  $codigoExtra=$row['extra'];
  $codPlanCuentaAuxiliarX=$row['cod_cuentaaux'];
  $codigoComprobanteX=$row['codigocomprobante'];
  $codUnidadCabecera=$row['cod_unidad_cab'];
  $codAreaCentroCosto=$row['area_centro_costos'];

  $nombreComprobanteX=nombreComprobante($codigoComprobanteX);
  $nombreCuentaAuxiliarX=nameCuentaAuxiliar($codPlanCuentaAuxiliarX);
  $tipoDebeHaber=verificarTipoEstadoCuenta($codPlanCuentaX);


  if($codPlanCuentaAuxiliarX!=$codPlanCuentaAuxiliarPivotX){
    $saldo=0;
    $codPlanCuentaAuxiliarPivotX=$codPlanCuentaAuxiliarX;
  }
  $glosaMostrar="";
  if($glosaAuxiliar!=""){
      $glosaMostrar=$glosaAuxiliar;
  }else{
      $glosaMostrar=$glosaX;
  }
  list($tipoComprobante, $numeroComprobante, $codUnidadOrganizacional, $mesComprobante, $fechaComprobante)=explode("|", $codigoExtra);
  $nombreTipoComprobante=abrevTipoComprobante($tipoComprobante)."-".$mesComprobante;

  $nombreUnidadO=abrevUnidad_solo($codUnidadOrganizacional);
  $nombreUnidadCabecera=abrevUnidad_solo($codUnidadCabecera);
  $nombreAreaCentroCosto=abrevArea_solo($codAreaCentroCosto);

  $fechaComprobante=strftime('%d/%m/%Y',strtotime($fechaComprobante));
  //SACAMOS CUANTO SE PAGO DEL ESTADO DE CUENTA.
  $sqlContra="SELECT sum(e.monto)as monto from estados_cuenta e, comprobantes_detalle cd, comprobantes c where c.codigo=cd.cod_comprobante and cd.codigo=e.cod_comprobantedetalle and c.cod_estadocomprobante<>2 and e.cod_comprobantedetalleorigen='$codigoX'";
  //echo $sqlContra;
  $stmtContra = $dbh->prepare($sqlContra);
  $stmtContra->execute();                                    
  $saldo+=$montoX;                                            
  $montoEstado=0;$estiloEstados="";
  $stmtSaldo = $dbh->prepare("SELECT sum(e.monto) as monto
          from estados_cuenta e, comprobantes_detalle d, comprobantes c where c.codigo=d.cod_comprobante and c.cod_estadocomprobante<>2 and e.cod_comprobantedetalle=d.codigo and e.cod_comprobantedetalleorigen=$codigoX");
  $stmtSaldo->execute();
  while ($rowSaldo = $stmtSaldo->fetch()) {
      $montoEstado=$rowSaldo['monto'];
  }

   if(formatNumberDec($montoX)==formatNumberDec($montoEstado)&&$ver_saldo==1){
       //validacion para saldos 0 si esta filtrado
      $estiloEstados="d-none";
   }   

  if($tipoDebeHaber==2){//proveedor
      if($mostrarFilasEstado!="d-none"&&$estiloFilasEstado==""&&$estiloEstados==""){
         $totalCredito=$totalCredito+$montoX;
      }
      
      $nombreProveedorX=nameProveedor($codProveedor);
      ?>
      <tr class="bg-white det-estados <?=$estiloEstados?> <?=$mostrarFilasEstado?>" <?=$estiloFilasEstado?> >
          <td class="text-left small"><?=$nombreUnidadCabecera?></td>
          <td class="text-left small"><?=$nombreUnidadO?>-<?=$nombreAreaCentroCosto?></td>
          <td class="text-center small"><?=$nombreComprobanteX?></td>
          <td class="text-left small"><?=$fechaComprobante?></td>
          <td class="text-left small"><?=$fechaX?></td>          
          <td class="text-left small"><?=$nombreCuentaAuxiliarX?></td>
          <td class="text-left small"><?=$glosaMostrar?></td>
          <td class="text-right text-muted font-weight-bold small"><?=formatNumberDec($montoEstado)?></td>
          <td class="text-right small"><?=formatNumberDec($montoX)?></td>
          <td class="text-right small font-weight-bold" <?=$estiloFilasEstadoSaldo?>><?=formatNumberDec($montoX-$montoEstado)?></td>
          <td class="text-right">
            <div class="togglebutton">
               <label>
                 <input type="checkbox"  id="pagos_seleccionados<?=$contador_items?>" name="pagos_seleccionados<?=$contador_items?> ">
                 <span class="toggle"></span>
               </label>
           </div>
          </td>
      </tr>

  <?php }                                          
  $stmt_d = $dbh->prepare("SELECT e.*,d.glosa,d.haber,d.debe,(select concat(c.cod_tipocomprobante,'|',c.numero,'|',cd.cod_unidadorganizacional,'|',MONTH(c.fecha),'|',c.fecha) from comprobantes_detalle cd, comprobantes c where c.codigo=cd.cod_comprobante and cd.codigo=e.cod_comprobantedetalle)as extra, c.codigo as codigocomprobante, c.cod_unidadorganizacional as cod_unidad_cab, d.cod_area as area_centro_costos
      from estados_cuenta e, comprobantes_detalle d, comprobantes c where c.codigo=d.cod_comprobante and c.cod_estadocomprobante<>2and e.cod_comprobantedetalle=d.codigo and e.cod_comprobantedetalleorigen=$codigoX");
  $stmt_d->execute();
  while ($row_d = $stmt_d->fetch()) {
      $codigoX_d=$row_d['codigo'];
      $codCompDetX_d=$row_d['cod_comprobantedetalle'];
      $codPlanCuentaX_d=$row_d['cod_plancuenta'];
      $codProveedor_d=$row_d['cod_proveedor'];
      $montoX_d=$row_d['monto'];
      $fechaX_d=$row_d['fecha'];
      $fechaX_d=strftime('%d/%m/%Y',strtotime($fechaX_d));
      $glosaAuxiliar_d=$row_d['glosa_auxiliar'];
      $glosaX_d=$row_d['glosa'];
      $debeX_d=$row_d['debe'];
      $haberX_d=$row_d['haber'];
      $codigoExtra_d=$row_d['extra'];
      $codigoComprobanteY=$row_d['codigocomprobante'];
      $codUnidadCabeceraY=$row_d['cod_unidad_cab'];
      $codAreaCentroCostoY=$row_d['area_centro_costos'];
      
      $tituloMontoDebe=formatNumberDec($montoX_d);
      if($montoX_d!=$debeX_d){
          $tituloMontoDebe=formatNumberDec($montoX_d).' <b class="text-danger">(*'.formatNumberDec($debeX_d).'*)</b>';
      }
      $nombreComprobanteY=nombreComprobante($codigoComprobanteY);
      $glosaMostrar_d="";
      if($glosaAuxiliar_d!=""){
          $glosaMostrar_d=$glosaAuxiliar_d;
      }else{
          $glosaMostrar_d=$glosaX_d;
      }
      list($tipoComprobante_d, $numeroComprobante_d, $codUnidadOrganizacional_d, $mesComprobante_d, $fechaComprobante_d)=explode("|", $codigoExtra_d);
      $nombreTipoComprobante_d=abrevTipoComprobante($tipoComprobante_d)."-".$mesComprobante_d;
      $nombreUnidadO_d=abrevUnidad_solo($codUnidadOrganizacional_d);
      $nombreUnidadCabecera_d=abrevUnidad_solo($codUnidadCabeceraY);
      $nombreAreaCentroCosto_d=abrevArea_solo($codAreaCentroCostoY);

      $fechaComprobante_d=strftime('%d/%m/%Y',strtotime($fechaComprobante_d));
      $saldo=$saldo-$montoX_d;
      if($tipoDebeHaber==2){//proveedor
          $nombreProveedorX_d=nameProveedor($codProveedor_d);
          if($mostrarFilasEstado!="d-none"&&$estiloEstados==""){
            $totalDebito=$totalDebito+$montoX_d;    
          }?>
          <tr style="background-color:#ECCEF5;" class="<?=$estiloEstados?> <?=$mostrarFilasEstado?> text-muted">
              <td class="text-left small">&nbsp;&nbsp;&nbsp;&nbsp;<?=$nombreUnidadCabecera_d?></td>
              <td class="text-left small"><?=$nombreUnidadO_d?>-<?=$nombreAreaCentroCosto_d?></td>
              <td class="text-center small"><?=$nombreComprobanteY?></td>
              <td class="text-left small"><?=$fechaComprobante_d?></td>
              <td class="text-left small"><?=$fechaX_d?></td>
              <td class="text-left small"><?=$nombreProveedorX_d?></td>  
              <td class="text-left small"><?=$glosaMostrar_d?></td>
              <td class="text-right small"><?=$tituloMontoDebe?></td>
              <td class="text-right small"><?=formatNumberDec(0)?></td>
              <td class="text-right small font-weight-bold">aquiiiii</td>
              <td class="text-right">
                <div class="togglebutton">
                   <label>
                     <input type="checkbox"  id="pagos_seleccionados<?=$contador_items?>" name="pagos_seleccionados<?=$contador_items?> ">
                     <span class="toggle"></span>
                   </label>
               </div>
              </td>
          </tr>
      <?php }
  }
  $i++;
  $indice++;
                                    
}

if($index>1){
  //$proveedor_nombre=$_GET['proveedor_nombre'];
  ?>
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