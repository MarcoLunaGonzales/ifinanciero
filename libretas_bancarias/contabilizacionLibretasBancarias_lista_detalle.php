<?php

require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'styles.php';
$globalAdmin=$_SESSION["globalAdmin"];
$codigo=$_GET['codigo'];

$dbh = new Conexion();
$stmtDepositosNoFac = $dbh->prepare("SELECT cod_libretabancariadetalle,monto from depositos_no_facturados_detalle where cod_depositonofacturado=$codigo");
//ejecutamos
$stmtDepositosNoFac->execute();
//bindColumn
$stmtDepositosNoFac->bindColumn('cod_libretabancariadetalle', $cod_libretabancaria);
$stmtDepositosNoFac->bindColumn('monto', $monto);
$stringCod_libreta="";
while ($row = $stmtDepositosNoFac->fetch(PDO::FETCH_BOUND)) {
  $stringCod_libreta.=$cod_libretabancaria.",";
}
$stringCod_libreta=trim($stringCod_libreta,",");
?>
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header <?=$colorCard;?> card-header-icon">
            <div class="card-icon">
              <i class="material-icons"><?=$iconCard;?></i>
            </div>
            <h4 class="card-title">Lista Depositos No Facturados Detalle</h4>
            <h4>Libreta bancaria <?=obtenerNombreDepositoNoFacturado($codigo)?></h4>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table id="libreta_bancaria_reporte" class="table table-condensed small" style="width:100% !important;">
                <thead>
                  <tr style="background:#F36329; color:#fff;">
                    <td>#</td>
                    <td>Fecha</td>
                    <td>Hora</td>
                    <td width="35%">Descripción</td>
                    <!--<td>Información C.</td>-->
                    <td>Sucursal</td>
                    <td>Monto</td>
                    <td width="10%">Nro Doc / Nro Ref</td>
                  </tr>
                </thead> 
                <?php
                  $html='<tbody>';
                  $sqlDetalle="SELECT codigo, descripcion, informacion_complementaria, agencia, nro_cheque, nro_documento, fecha_hora, monto, cod_factura from libretas_bancariasdetalle where codigo in ($stringCod_libreta);";
                  $stmt = $dbh->prepare($sqlDetalle);
                  //echo $sqlDetalle;

                  // Ejecutamos
                  $stmt->execute();
                  // bindColumn
                  $stmt->bindColumn('codigo', $codigo);
                  $stmt->bindColumn('descripcion', $descripcion);
                  $stmt->bindColumn('informacion_complementaria', $informacion_complementaria);
                  $stmt->bindColumn('agencia', $agencia);
                  $stmt->bindColumn('nro_cheque', $nro_cheque);
                  $stmt->bindColumn('nro_documento', $nro_documento);
                  $stmt->bindColumn('fecha_hora', $fecha);
                  $stmt->bindColumn('monto', $monto);
                  $stmt->bindColumn('cod_factura', $codFactura);

                  $index=1;$totalMonto=0;$totalMontoFac=0;
                  while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                    if($codFactura==""||$codFactura==0){
                      $estiloFac=""; 
                    }else{
                      $sqlDetalleX="SELECT * FROM facturas_venta where cod_libretabancariadetalle=$codigo";                                   
                      $stmtDetalleX = $dbh->prepare($sqlDetalleX);
                      $stmtDetalleX->execute();

                      $stmtDetalleX->bindColumn('fecha_factura', $fechaDetalle);
                      $stmtDetalleX->bindColumn('nro_factura', $nroDetalle);
                      $stmtDetalleX->bindColumn('nit', $nitDetalle);
                      $stmtDetalleX->bindColumn('razon_social', $rsDetalle);
                      $stmtDetalleX->bindColumn('observaciones', $obsDetalle);
                      $stmtDetalleX->bindColumn('importe', $impDetalle);
                      $montoAux=0;
                      while ($rowDetalleX = $stmtDetalleX->fetch(PDO::FETCH_BOUND)) {
                        $montoAux+=$impDetalle;  
                      }
                      $monto=($monto-$montoAux);
                      
                      $estiloFac="text-danger";
                    }
                    $totalMonto+=$monto; ?>
                    <tr>
                      <td class="text-left"><?=$index?>
                       <input type="hidden" id="cod_libretadetalle<?=$index?>" name="cod_libretadetalle<?=$index?>" value="<?=$codigo?>">
                      </td>
                      <td class="text-center font-weight-bold"><?=strftime('%d/%m/%Y',strtotime($fecha))?></td>
                      <td class="text-center"><?=strftime('%H:%M:%S',strtotime($fecha))?></td>
                      <td class="text-left">
                        <?=$descripcion?> info: <?=$informacion_complementaria?>
                        
                      </td>      
                      <td class="text-left"><?=$agencia?></td>
                      <td class="text-right <?=$estiloFac?>"><?=number_format($monto,2,".",",")?></td>
                      <td class="text-right"><?=$nro_documento?></td>
                    </tr><?php
                    $index++;
                  } ?>
                <?php
                    $html.=    '</tbody>';
                    echo $html; ?>
                <tfoot>
                  <tr style="background:#F36329; color:#fff; font-size:20px !important;">
                      <td></td> 
                      <td></td>
                      <td></td>
                      <td>Cantidad de Registros: <?=$index-1?></td>
                      <td>Total Monto: </td>
                      <td><?=number_format($totalMonto,2,".",",")?></td>
                      <td></td>
                  </tr>
                </tfoot>
              </table>        
            </div>
          </div>
          <div class="card-footer fixed-bottom">              
            <a href="<?=$urlList_2?>" class="btn btn-danger">VOLVER</a>  
          </div>
        </div>          
      </div>
    </div>  
  </div>
</div>
