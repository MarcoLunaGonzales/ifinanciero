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
$codigos_sr=$_GET['codigos_sr'];
$contador_check=$_GET['contador_check'];
$codigo_formateado = str_replace("PPPPP", ",", $codigos_sr);
$codigo_formateado=trim($codigo_formateado,",");
$lista=listaObligacionesPagoDetalleSolicitudRecursosProveedor_seleccionados($codigo_formateado);
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
  <tr >
    <td class="text-left">            
      <input type="hidden" value="<?=$detalle?>" id="glosa_detalle_s<?=$index?>" name="glosa_detalle_s<?=$index?>">
      <input type="hidden" value="<?=$codProveedor?>" id="codigo_proveedor_s<?=$index?>" name="codigo_proveedor_s<?=$index?>">
      <input type="hidden" value="<?=$codSol?>" id="codigo_solicitud_s<?=$index?>" name="codigo_solicitud_s<?=$index?>">
      <input type="hidden" value="<?=$codSolDet?>" id="codigo_solicitudDetalle_s<?=$index?>" name="codigo_solicitudDetalle_s<?=$index?>">
      <input type="hidden" value="<?=$codPlancuenta?>" id="codigo_plancuenta_s<?=$index?>" name="codigo_plancuenta_s<?=$index?>">
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
      <?php 
      if(($importe-$pagado)>0){
        ?>
        <input type="number" step="any" required min="1000" class="form-control text-right text-success" value="0" id="monto_pago_s<?=$index?>" name="monto_pago_s<?=$index?>"><?php
      }else{ ?>
        <input type="number" step="any" required min="1000" class="form-control text-right text-success" readonly value="0" id="monto_pago_s<?=$index?>" name="monto_pago_s<?=$index?>"> <?php
      } ?>
    </td>
    <td><input type="text" class="form-control datepicker" value="<?=date('d/m/Y')?>" id="fecha_pago_s<?=$index?>" name="fecha_pago_s<?=$index?>"></td>
    <td>
    	<div class="form-group">
        <select class="selectpicker form-control form-control-sm" onchange="mostrarDatosChequeDetalle('<?=$index?>')" data-live-search="true" name="tipo_pago_s<?=$index?>" id="tipo_pago_s<?=$index?>" data-style="btn btn-danger" required>
              <option disabled value="">--TIPO--</option>
              <?php 
               $stmt3 = $dbh->prepare("SELECT * from tipos_pagoproveedor where codigo=2 and cod_estadoreferencial=1");
               $stmt3->execute();
               while ($rowSel = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                $codigoSel=$rowSel['codigo'];
                $nombreSelX=$rowSel['nombre'];
                $abrevSelX=$rowSel['abreviatura'];
                if($codTipoPago==$codigoSel){
                   ?><option selected value="<?=$codigoSel;?>"><?=$abrevSelX?></option><?php 
                }else{
                   ?><option value="<?=$codigoSel;?>" selected="selected"><?=$abrevSelX?></option><?php 
                } 
               }
              ?>
            </select>
       </div>
    </td>
    <td>
    	<div class="d-none" id="div_cheques_s<?=$index?>">                    
          <div class="form-group">
               <select class="selectpicker form-control form-control-sm" onchange="cargarChequesPagoDetalle('<?=$index?>')" data-live-search="true" name="banco_pago_s<?=$index?>" id="banco_pago_s<?=$index?>" data-style="btn btn-danger">
              <option disabled selected="selected" value="">--BANCOS--</option>
              <?php 
               $stmt3 = $dbh->prepare("SELECT * from bancos where cod_estadoreferencial=1");
               $stmt3->execute();
               while ($rowSel = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                $codigoSel=$rowSel['codigo'];
                $nombreSelX=$rowSel['nombre'];
                $abrevSelX=$rowSel['abreviatura'];
                //if($codBanco==$codigoSel){
                 
                //}else{
                 ?><option value="<?=$codigoSel;?>"><?=$abrevSelX?></option><?php 
                //}
               }
              ?>
                </select>
            </div>
       </div>
    </td>
    <td>
    	<div id="div_chequesemitidos_s<?=$index?>">                    
      </div>
    </td>
    <td>
    	<input type="number" readonly class="form-control text-right" readonly value="0" id="numero_cheque_s<?=$index?>" name="numero_cheque_s<?=$index?>">
    </td>
    <td>
    	<input type="text" readonly class="form-control" readonly value="<?=$nomBen?> <?=$apellBen?>" id="beneficiario_s<?=$index?>" name="beneficiario_s<?=$index?>">
    </td>
  </tr>
  <script>mostrarDatosChequeDetalle('<?=$index?>');</script>
  <?php
    $index++;
}
?>
<script>$("#cantidad_proveedores").val(parseInt($("#cantidad_proveedores").val())+<?=$index-1?>);
</script>
