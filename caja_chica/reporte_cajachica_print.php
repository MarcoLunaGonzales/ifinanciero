<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';
require '../assets/phpqrcode/qrlib.php';

//require_once 'configModule.php';
require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';
require_once '../layouts/bodylogin2.php';

$dbh = new Conexion();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
//RECIBIMOS LAS VARIABLES

$codigo = $_POST["caja_chica"];//codigoactivofijo

    $stmt = $dbh->prepare("SELECT codigo,monto,fecha,observaciones,nro_recibo,cod_uo,cod_area,cod_tipodoccajachica from caja_chicadetalle where cod_estadoreferencial=1 and cod_cajachica=$codigo 
    UNION
    SELECT codigo,monto,fecha,observaciones,0 as nro_recibo,0 as cod_uo,0 as cod_area,0 as cod_tipodoccajachica from caja_chicareembolsos where cod_estadoreferencial=1 and cod_cajachica=$codigo ORDER BY nro_recibo");
    $stmt->execute();    
        //==================================================================================================================
    //datos caja chica
    $stmtInfo = $dbh->prepare("SELECT tc.nombre,tc.cod_uo,c.monto_inicio,c.observaciones,c.numero,c.fecha,c.fecha_cierre from caja_chica c,tipos_caja_chica tc where c.cod_tipocajachica=tc.codigo and c.codigo=$codigo");
    $stmtInfo->execute();
    $resultInfo = $stmtInfo->fetch();
    //$codigo = $result['codigo'];
    $nombre_tcc = $resultInfo['nombre'];
    $cod_uo = $resultInfo['cod_uo'];
    $monto_inicio_cc = $resultInfo['monto_inicio'];
    $detalle_cc = $resultInfo['observaciones'];
    $numero_cc = $resultInfo['numero'];
    $fecha_inicio_cc = $resultInfo['fecha'];
    $fecha_cierre_cc = $resultInfo['fecha_cierre'];
    $cod_uo_x=$resultInfo['cod_uo'];
    // $cod_tipodoccajachica_x=$resultInfo['cod_tipodoccajachica'];
    $nombre_uo_x=nameUnidad($cod_uo_x);
    // $contenido='CAJA CHICA N° '.$numero_cc." De Fecha: ".$fecha_inicio_cc." a ".$fecha_cierre_cc;
    $contenido='CAJA CHICA N° '.$numero_cc; 

?>
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header <?=$colorCard;?> card-header-icon">
            <div class="card-icon bg-blanco">
              <img class="" width="40" height="40" src="../assets/img/logoibnorca.png">
            </div>            
            <div class="row">               
            </div> 
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table  class="table table-bordered table-condensed" >
                <thead>
                  <tr class="bold table-title text-center">
                    <td colspan="11"><small><?=$nombre_tcc?></td>
                  </tr>
                  <tr class="bold table-title text-center">
                    <td colspan="11"><small><?=$contenido?></td>
                  </tr>
                  <tr class="bold table-title text-center">
                    <td width="3%"><small>Fecha</small></td> 
                    <td width="4%"><small>Área</small></td>                   
                    <td width="40%"><small>Descripción</small></td>
                    <td width="4%"><small>N° Recibo</small></td>
                    <td width="5%"><small>Factura</small></td>                    
                    <td width="5%"><small>Retención</small></td>
                    <td width="5%"><small>% Mayor</small></td>
                    <td width="5%"><small>Ingreso</small></td>                    
                    <td width="5%"><small>Egreso</small></td>
                    <td width="5%"><small>Saldo</small></td>
                    <td width="5%"><small>Importe <br>+ retención</small></td>
                  </tr>
                </thead>
                <tbody><?php
                  $index=1;
                  $saldo_inicial=$monto_inicio_cc;?>
                  <tr>
                      <td class="text-left small"></td>
                      <td class="text-left small"></td>
                      <td class="text-center small"><b>ASIGNACION DE FONDO</b></td>
                      <td class="text-center small"></td>
                      <td class="text-center small"></td>
                      <td class="text-center small"></td>
                      <td class="text-center small"></td>
                      <td class="text-center small"><?=formatNumberDec($monto_inicio_cc)?></td>
                      <td class="text-center small"></td>
                      <td class="text-right small"><b><?=formatNumberDec($monto_inicio_cc)?></b></td>
                      <td class="text-right small"><b></b></td>
                  </tr><?php
                  $ingresos='';
                  $total_ingresos=$monto_inicio_cc;
                  $total_egresos=0;
                  $total_retencion=0;
                  while ($row = $stmt->fetch()) 
                  {
                    $sw_rembolso=false;//indicamos que es un reembolso
                    if($row['nro_recibo']==0 && $row['cod_uo']==0 && $row['cod_area']==0 ){
                      $sw_rembolso=true;
                    }
                    $cod_cajachicadetalle=$row['codigo'];
                    $nombre_uo=abrevUnidad($row['cod_uo']);
                    $nombre_area=abrevArea($row['cod_area']);
                    $nro_recibo=$row['nro_recibo'];
                                        
                    $tipo_retencion=abrevRetencion($row['cod_tipodoccajachica']);
                    $porcentaje_retencion=porcentRetencion($row['cod_tipodoccajachica']);
                    if($porcentaje_retencion>100)
                      $porcentaje_retencion=$porcentaje_retencion-100;
                    else $porcentaje_retencion=0;
                    $importe_retencion=$row['monto']+($row['monto']*$porcentaje_retencion/100);
                    $total_retencion=$total_retencion+$importe_retencion;
                    //nro factura
                    if(!$sw_rembolso){
                      $stmtFactura = $dbh->prepare("SELECT nro_factura from facturas_detalle_cajachica where cod_cajachicadetalle=$cod_cajachicadetalle");
                      $stmtFactura->execute();
                      $cont_facturas=0;
                      $nro_factura='';
                      while ($rowFacturas = $stmtFactura->fetch()) 
                      {
                        $nro_factura=$rowFacturas['nro_factura'];
                        $cont_facturas++;
                      }
                      if($cont_facturas>1)$nro_factura="VARIOS";
                      $saldo_inicial=$saldo_inicial-$row['monto'];                  
                      $total_egresos+=$row['monto'];
                    }else{
                      $nombre_uo="";
                      $nombre_area="";
                      $total_ingresos+=$row['monto'];
                      $saldo_inicial=$saldo_inicial+$row['monto'];                
                      $nro_recibo='';
                    }

                    ?>
                    <tr>                      
                      <td class="text-center small"><?=$row['fecha']?></td>
                      <?php
                      if(!$sw_rembolso){?>
                        <td class="text-left small"><?=$nombre_uo."/".$nombre_area?></td>
                      <?php }else{?>
                        <td class="text-left small"></td>
                      <?php }?>
                      <td class="text-left small"><small><?=$row['observaciones']?></small></td>
                      <td class="text-center small"><?=$nro_recibo?></td>
                      <td class="text-center small"><?=$nro_factura?></td>
                      <td class="text-center small"><?=$tipo_retencion?></td>
                      <td class="text-center small"><?=$porcentaje_retencion?></td>
                      <?php if(!$sw_rembolso){ ?>
                        <td class="text-right small"><?=$ingresos?>.</td>
                        <td class="text-right small"><?=formatNumberDec($row['monto'])?></td>
                      <?php }else{?>
                        <td class="text-right small"><?=formatNumberDec($row['monto'])?></td>
                        <td class="text-right small"></td>
                      <?php }?>
                      <td class="text-right small"><?=formatNumberDec($saldo_inicial)?></td>
                      <td class="text-right small"><?=formatNumberDec($importe_retencion)?></td>
                    </tr>
                  <?php } ?>
                  <tr>                      
                    <td class="text-left small"></td>
                    <td class="text-center small"></td>
                    <td class="text-left small"></td>
                    <td class="text-center small"></td>
                    <td class="text-center small"></td>
                    <td class="text-center small"></td>
                    <td class="text-right small"></td>
                    <td class="text-right small"></td>
                    <td class="text-right small"></td>
                    <td class="text-right small"><?= formatNumberDec($saldo_inicial)?></td>
                    <td class="text-right small"><?= formatNumberDec($total_retencion)?></td>
                  </tr>
                </tbody>
              </table>
              <table class="table">
                  <tbody>
                  <tr>              
                    <td width="3%"></td> 
                    <td width="4%"></td>                   
                    <td width="40%" class="text-left small"><b>SUBTOTALES</b></td>
                    <td width="4%"></td>
                    <td width="5%"></td>                    
                    <td width="5%"></td>                    
                    <td width="5%" class="text-right small"><?=formatNumberDec($total_ingresos)?></td>
                    <td width="5%" class="text-right small"><?=formatNumberDec($total_egresos)?></td>
                    <td width="5%"></td>
                    <td width="5%"></td>
                  </tr>
                  <tr>
                    <td ></td> 
                    <td ></td>                   
                    <td  class="text-left small"><b>TOTAL RENDICIÓN DE FONDO</b></td>
                    <td ></td>
                    <td ></td>                    
                    <td ></td>
                    <td ></td>
                    <td class="text-right small"><b><?=formatNumberDec($total_egresos)?></b></td>
                    <td ></td>
                    <td ></td>
                  </tr>
                  <tr>
                    <td ></td> 
                    <td ></td>                   
                    <td ><b>SALDO A RESPONDER</b></td>
                    <td ></td>
                    <td ></td>                    
                    <td ></td>
                    <td ></td>
                    <td  class="text-right small"><b><?=formatNumberDec($saldo_inicial)?></b></td>
                    <td ></td>
                    <td ></td>
                  </tr>
                 </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>  
  </div>
</div>