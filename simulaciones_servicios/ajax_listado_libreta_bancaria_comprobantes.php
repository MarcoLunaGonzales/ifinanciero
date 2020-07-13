<?php
session_start();
require_once '../conexion.php';
require_once '../functions.php';
require_once '../styles.php';
require_once '../layouts/librerias.php';
$dbh = new Conexion();
//$saldo_solfac=$_GET['saldo'];
$tipo_listado=$_GET['tipo_listado'];
$listarLib=1;
?>
<?php  
  $lista=obtenerObtenerLibretaBancaria();  
?>
<style>
  tfoot input {
    width: 100%;
    padding: 3px;
  }
</style> 
<table id="libreta_bancaria_reporte_modal" class="table table-condensed table-bordered table-sm" style="width:100% !important;">
    <thead>
      <tr style="background:#21618C; color:#fff;">
        <th class="text-center" width="3%">#</th>
        <th class="small" width="5%"><small>Fecha</small></th>      
        <th class="small" width="30%"><small>Descripción</small></th>      
        <th class="small" width="5%"><small>Monto</small></th>
        <th class="small" width="5%"><small>Saldo</small></th>
        <th class="small" width="3%"><small><small>N° Ref</small></small></th>
        <th class="small bg-success" width="4%"><small>Fecha Fac.</small></th>
        <th class="small bg-success" width="4%"><small>N° Fac.</small></th>      
        <th class="small bg-success" width="7%"><small>Nit Fac.</small></th>
        <th class="small bg-success"><small>Razón Social Fac.</small></th>
        <th class="small bg-success" width="5%"><small>Monto Fac.</small></th>
        <th class="text-center" width="3%">*</th>
      </tr>
    </thead>
    <tbody>
      <?php
      // if($lista->estado==1){
        $j=1;
          foreach ($lista->libretas as $v) {
            $Nombre=$v->Nombre;
            $Banco=$v->Banco;
            $detalle=$v->detalle;
            $index=1;
            if(isset($_GET['codigo_lib'])){
              if($v->CodLibreta==$_GET['codigo_lib']){
                $listarLib=1;
              }else{
                if($_GET['codigo_lib']==0){
                 $listarLib=1;
                }else{
                  $listarLib=0; 
                }
              }
             }
             if($listarLib==1){
            ?>            
            <tr>
              <td class="d-none"></td>
              <td class="d-none"></td>
              <td class="d-none"></td>
              <td class="d-none"></td>                        
              <td class="d-none"></td>
              <td class="d-none"></td>
              <td class="d-none"></td>
              <td class="d-none"></td>
              <td class="d-none"></td>
              <td class="d-none"></td>
              <td class="d-none"></td>
              <td align="center" colspan="12" style="background:#e58400; color:#fff;"><button title="Detalles" id="botonLibreta<?=$j?>" style="border:none; background:#e58400; color:#fff;" onclick="activardetalleLibreta(<?=$j?>)"><small><?=$Banco;?> - <?=$Nombre;?></small></button></td>
            </tr>     
            <?php
              foreach ($detalle as $v_detalle) {
              $CodLibretaDetalle=$v_detalle->CodLibretaDetalle;
              $Descripcion=$v_detalle->Descripcion;
              $InformacionComplementaria=" Info: ".$v_detalle->InformacionComplementaria;
              $Agencia=$v_detalle->Agencia;
              $NumeroCheque=$v_detalle->NumeroCheque;
              $NumeroDocumento=$v_detalle->NumeroDocumento;
              $Fecha=$v_detalle->Fecha;
              $Hora=$v_detalle->Hora;
              $FechaHoraCompleta=$v_detalle->FechaHoraCompleta;
              $monto=$v_detalle->monto;
              $CodEstado=$v_detalle->CodEstado;
              $saldo=$v_detalle->Saldo;
              if($CodEstado==0)$color_aux="background-color: #d6dbdf;";
              else $color_aux="background-color:#f6ddcc;";

              if($tipo_listado==1 || $saldo>0){//todo ?>
                <tr style="<?=$color_aux?>">
                  <td class="libretaDetalles_<?=$j?> small" align="center"><?=$index;?></td>
                  <td class="libretaDetalles_<?=$j?> text-center small"><span style="padding:0px;border: 0px;"><?=$Fecha?><br><?=$Hora?></span></td>           
                  <td class="libretaDetalles_<?=$j?> text-left ">
                    <small><small><?=$Descripcion." ".$InformacionComplementaria?></small></small>
                  </td>
                  <td class="libretaDetalles_<?=$j?> text-right small"><?=number_format($monto,2)?></td>
                  <td class="libretaDetalles_<?=$j?> text-right small"><?=number_format($saldo,2)?></td>
                  <td class="libretaDetalles_<?=$j?> text-left small"><?=$NumeroDocumento?></td>
                  <?php                  
                    $cont_facturas=contarFacturasLibretaBancaria($CodLibretaDetalle);
                    if($cont_facturas>0){
                      $sqlDetalleX="SELECT * FROM facturas_venta where cod_libretabancariadetalle=$CodLibretaDetalle and cod_estadofactura !=2 order by codigo desc";
                      $stmtDetalleX = $dbh->prepare($sqlDetalleX);
                      $stmtDetalleX->execute();
                      $stmtDetalleX->bindColumn('fecha_factura', $fechaDetalle);
                      $stmtDetalleX->bindColumn('nro_factura', $nroDetalle);
                      $stmtDetalleX->bindColumn('nit', $nitDetalle);
                      $stmtDetalleX->bindColumn('razon_social', $rsDetalle);
                      $stmtDetalleX->bindColumn('observaciones', $obsDetalle);
                      $stmtDetalleX->bindColumn('importe', $impDetalle);
                      $facturaFecha=[];
                      $facturaNumero=[];
                      $facturaNit=[];
                      $facturaRazonSocial=[];
                      $facturaDetalle=[];
                      $facturaMonto=[];
                      $filaFac=0;  
                      while ($rowDetalleX = $stmtDetalleX->fetch(PDO::FETCH_BOUND)) {                        
                        $facturaFecha[$filaFac]=strftime('%d/%m/%Y',strtotime($fechaDetalle));
                        $facturaNumero[$filaFac]=$nroDetalle;
                        $facturaNit[$filaFac]=$nitDetalle;
                        $facturaRazonSocial[$filaFac]=$rsDetalle;
                        $facturaDetalle[$filaFac]=$obsDetalle;
                        $facturaMonto[$filaFac]=number_format($impDetalle,2,".",",");
                        $filaFac++;
                      }?>
                      <td class="text-right libretaDetalles_<?=$j?>" style="vertical-align: top;"><small><?=implode("<div style='border-bottom:1px solid #26BD3D;'></div>", $facturaFecha)?></small></td>
                      <td class="text-right libretaDetalles_<?=$j?>" style="vertical-align: top;"><small><?=implode("<div style='border-bottom:1px solid #26BD3D;'></div>", $facturaNumero)?></small></td>
                      <td class="text-right libretaDetalles_<?=$j?>" style="vertical-align: top;"><small><?=implode("<div style='border-bottom:1px solid #26BD3D;'></div>", $facturaNit)?></small></td>
                      <td class="text-left libretaDetalles_<?=$j?>" style="vertical-align: top;"><small><small><?=implode("<div style='border-bottom:1px solid #26BD3D;'></div>", $facturaRazonSocial)?></small></small></td>                      
                      <td class="text-right libretaDetalles_<?=$j?>" style="vertical-align: top;"><small><?=implode("<div style='border-bottom:1px solid #26BD3D;'></div>", $facturaMonto)?></small>
                      </td>
                      <td class="td-actions text-right libretaDetalles_<?=$j?>">
                        <?php
                        if($saldo>=$saldo_solfac){
                          if($cont_facturas==0)$label="btn btn-fab btn-success btn-sm";
                          else $label="btn btn-fab btn-warning btn-sm";
                          ?><a href="#" style="padding: 0;font-size:10px;width:25px;height:25px;" onclick="seleccionar_libretaBancaria(<?=$CodLibretaDetalle?>)" class="<?=$label?>" title="Seleccionar Item"><i class="material-icons">done</i></a>
                            <?php
                        }?>
                      </td>
                      <?php                    
                    }else{                  
                      $codFactura="";
                      $fechaDetalle_x="";
                      $nroDetalle_x="";
                      $nitDetalle_x="";
                      $rsDetalle_x="";
                      $obsDetalle_x="";
                      $impDetalle_x="";
                      ?>
                      <td style="" class="libretaDetalles_<?=$j?> text-center small"><?=$fechaDetalle_x?></td>
                      <td style=" " class="libretaDetalles_<?=$j?> text-right small"><?=$nroDetalle_x?></td>            
                      <td style="" class="libretaDetalles_<?=$j?> text-right small"><?=$nitDetalle_x?></td>
                      <td style="" class="libretaDetalles_<?=$j?> text-left"><small><small><?=$rsDetalle_x?></small></small></td>                    
                      <td style="" class="libretaDetalles_<?=$j?> text-right small"><?=$impDetalle_x?></td>
                      <td class="td-actions text-right libretaDetalles_<?=$j?>">
                      <?php
                        if($monto>=$saldo_solfac){
                          if($cont_facturas==0)$label="btn btn-fab btn-success btn-sm";
                          else $label="btn btn-fab btn-warning btn-sm"; ?>
                          <a href="#" style="padding: 0;font-size:10px;width:25px;height:25px;" onclick="seleccionar_libretaBancaria(<?=$CodLibretaDetalle?>)" class="<?=$label?>" title="Seleccionar Item"><i class="material-icons">done</i></a><?php
                        }?>
                      </td><?php                          
                    }
                  ?>
                </tr>
              <?php }
              }//if listarLib
              ?>

            <?php
            $index++;
            }
            $j++;
        }      
      ?>
    </tbody>
    <tfoot>
      <tr style="background:#21618C; color:#fff;">
        <td class="text-center" width="3%">#</td>
        <th class="small" width="5%"><small>Fecha</small></th>      
        <th class="small" width="30%"><small>Información Complementaria</small></th>      
        <th class="small" width="5%"><small>Monto</small></th>
        <th class="small" width="5%"><small>Saldo</small></th>
        <th class="small" width="3%"><small><small>N° Ref</small></small></th>
        <th class="small bg-success" width="4%"><small>Fecha<br>Fac.</small></th>
        <th class="small bg-success" width="4%"><small>N° Fac.</small></th>      
        <th class="small bg-success" width="7%"><small>Nit Fac.</small></th>
        <th class="small bg-success"><small>Razón Social Fac.</small></th>
        <th class="small bg-success" width="5%"><small>Monto Fac.</small></th>
        <td class="text-center" width="3%">*</td>        
      </tr>
    </tfoot>
</table>