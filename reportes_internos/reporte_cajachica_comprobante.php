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

$tipo_cajachica = $_POST["tipo_cajachica"];
$sql="SELECT tcc.nombre,cc.numero,cc.observaciones,ccd.codigo,ccd.nro_recibo,cc.cod_comprobante,(select fdcc.nro_factura from facturas_detalle_cajachica fdcc where fdcc.cod_cajachicadetalle=ccd.codigo limit 1) as nro_factura,ccd.monto,ccd.monto_rendicion
From tipos_caja_chica tcc, caja_chica cc,caja_chicadetalle ccd where tcc.codigo=cc.cod_tipocajachica and cc.codigo=ccd.cod_cajachica  and cc.cod_comprobante>0 and cc.cod_estadoreferencial=1 and tcc.codigo=$tipo_cajachica and ccd.monto_rendicion=ccd.monto GROUP BY ccd.codigo ORDER BY ccd.nro_recibo";
// echo $sql;
$stmt = $dbh->prepare($sql);
    $stmt->execute();    

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
                    <td><small>-</small></td> 
                    <td><small>Caja<br>Chica</small></td> 
                    <td><small>Nro</small></td>                   
                    <td><small>Observaciones</small></td>                  
                    <td><small>Nro <br>recibo</small></td>                    
                    <td><small>codigo<br>comprobante</small></td>
                    <td><small>NRO <br>factura</small></td>                    
                    <td><small>Codigo<br> Comprobante<br> Detalle</small></td>   
                  </tr>
                </thead>
                <tbody><?php
                  $index=1;                  
                  while ($row = $stmt->fetch()) 
                  {
                    $nombre=$row['nombre'];
                    $numero=$row['numero'];
                    $observaciones=$row['observaciones'];
                    $codigo=$row['codigo'];
                    $nro_recibo=$row['nro_recibo'];
                    $cod_comprobante=$row['cod_comprobante'];
                    $nro_factura=$row['nro_factura'];
                    $monto=$row['monto'];
                    $monto_rendicion=$row['monto_rendicion'];
                    
                    $stmtComprobantes = $dbh->prepare("SELECT cd.codigo From comprobantes_detalle cd
                    where cd.cod_comprobante=$cod_comprobante and cd.glosa like '%$nro_recibo%' and cd.glosa like '%$nro_factura%' limit 1");
                    $stmtComprobantes->execute();    
                    $cod_comprobante_detalle=0;                
                    while ($rowFacturas = $stmtComprobantes->fetch()) 
                    {
                      $cod_comprobante_detalle=$rowFacturas['codigo'];                      
                    }
                    ?>
                    <tr>                      
                      <td class="text-center small"><?=$index?></td>
                      <td class="text-center small"><?=$nombre?></td>
                      <td class="text-center small"><?=$numero?></td>
                      <td class="text-center small"><?=$observaciones?></td>
                      <td class="text-center small"><?=$nro_recibo?></td>
                      <td class="text-center small"><?=$cod_comprobante?></td>
                      <td class="text-center small"><?=$nro_factura?></td>
                      <td class="text-center small"><?=$cod_comprobante_detalle?></td>
                    </tr>
                  <?php } ?>                
                </tbody>
              </table>       
            </div>
          </div>
        </div>
      </div>
    </div>  
  </div>
</div>