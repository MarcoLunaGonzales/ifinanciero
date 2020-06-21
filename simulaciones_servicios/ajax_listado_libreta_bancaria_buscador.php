<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../styles.php';
require_once '../layouts/bodylogin2.php';
$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();
set_time_limit(300);
$saldo=$_GET['saldo'];
$informacion_l=$_GET['informacion'];
$monto_l=$_GET['monto'];
$razon_social_l=$_GET['razon_social'];

$codigo=0;
$lista=obtenerObtenerLibretaBancaria($codigo);
// var_dump($lista->datos->detalle);
?>
<center><b><p>monto DEPÓSITO EN CUENTA a facturar <?=number_format($saldo,2)?> Bs.</p></b></center>
<table id="libro_mayor_rep" class="table table-condensed small" style="width:100% !important;">
  <thead>
    <tr style="background:#21618C; color:#fff;">
      <td class="text-center">#</td>
      <td class="small" width="5%">Fecha</td>      
      <td class="small" width="40%">Información Complementaria</td>      
      <td class="small" width="5%">Monto</td>
      <td class="small" width="3%">N° Documento</td>      
      <td class="small bg-success" width="4%">Fecha<br>Asoc.</td>
      <td class="small bg-success" width="3%">N° Fact</td>      
      <td class="small bg-success">Nit</td>
      <td class="small bg-success">Razón Social</td>
      <td class="small bg-success" width="5%">Monto<br>Factura</td>
      <td class="text-right bg-success" width="3%"></td>
    </tr>
  </thead>
  <tbody>
	<?php
	if($lista->estado==1){
		$j=1;
	  	foreach ($lista->libretas as $v) {
	  		$Nombre=$v->Nombre;
	  		$Banco=$v->Banco;
	  		$detalle=$v->detalle;
	  		$index=1;?>
	  		<tr>
				<td align="center" colspan="12" style="background:#e58400; color:#fff;"><button title="Detalles" id="botonLibreta<?=$j?>" style="border:none; background:#e58400; color:#fff;" onclick="activardetalleLibreta(<?=$j?>)"><small><?=$Banco;?> - <?=$Nombre;?></small></button></td>
			</tr>			
			<?php
	  		foreach ($detalle as $v_detalle) {
	  			$CodLibretaDetalle=$v_detalle->CodLibretaDetalle;
  				$Descripcion=$v_detalle->Descripcion;
  				$InformacionComplementaria=$v_detalle->InformacionComplementaria;
  				$Agencia=$v_detalle->Agencia;
  				$NumeroCheque=$v_detalle->NumeroCheque;
  				$NumeroDocumento=$v_detalle->NumeroDocumento;
  				$Fecha=$v_detalle->Fecha;
  				$Hora=$v_detalle->Hora;
  				$FechaHoraCompleta=$v_detalle->FechaHoraCompleta;
  				$monto=$v_detalle->monto;

          $CodFactura=$v_detalle->CodFactura;
          $FechaFactura=$v_detalle->FechaFactura;
          $NumeroFactura=$v_detalle->NumeroFactura;
          $NitFactura=$v_detalle->NitFactura;
          $RSFactura=$v_detalle->RSFactura;
          $MontoFactura=$v_detalle->MontoFactura; 

          // $cadena="true";
          // if($informacion_l!="" && $monto_l!="" && $razon_social_l!=""){
          //   $sw_info = strpos($InformacionComplementaria, $informacion_l);
          //   if($monto_l == $monto){ $sw_monto=true}
          //   $sw_rs=strpos($RSFactura,$razon_social_l);
          //   if($sw_info && $sw_monto && $sw_rs)
          //     $cadena=true;
          //   else $cadena=false;
          // }
          $contador=0;
          $contador_aux=0;
          if($informacion_l!="") $contador++;
          if($monto_l!="") $contador++;
          if($razon_social_l!="") $contador++;

          if($informacion_l!=""){                      
            $sw_info = strpos($InformacionComplementaria, $informacion_l);
            if($sw_info) $contador_aux++;
          }
          if($monto_l!=""){
            if($monto_l == $monto)$contador_aux++;            
          }
          if($razon_social_l!=""){
            $sw_rs=strpos($RSFactura,$razon_social_l);
            if($sw_rs)$contador_aux++;
          }
          if($contador==$contador_aux){
            $sw_general=true;
          }else $sw_general=false;
          // if($cadena=="")$cadena=true;
          // echo $contador."-".$contador_aux."<br>";
          if($sw_general){?>
            <tr>
              <td class="libretaDetalles_<?=$j?> small" align="center"><small><?=$index;?></small></td>
              <td class="libretaDetalles_<?=$j?> text-center "><small><small><span style="padding:0px;border: 0px;"><?=strftime('%d/%m/%Y',strtotime($FechaHoraCompleta))?><br><?=strftime('%H:%M:%S',strtotime($FechaHoraCompleta))?></span></small></small></td>           
              <td class="libretaDetalles_<?=$j?> text-left "><small><small><?=$InformacionComplementaria?></small></small></td>
              <td class="libretaDetalles_<?=$j?> text-right small"><small><?=number_format($monto,2)?></small></td>
              <td class="libretaDetalles_<?=$j?> text-left small"><small><?=$NumeroDocumento?></small></td>
              <td style=" color: #ff0000;" class="libretaDetalles_<?=$j?> text-center small"><small><?=$FechaFactura?></small></td>
              <td style=" color: #ff0000;" class="libretaDetalles_<?=$j?> text-right small"><small><?=$NumeroFactura?></small></td>            
              <td style=" color: #ff0000;" class="libretaDetalles_<?=$j?> text-right small"><small><?=$NitFactura?></small></td>
              <td style=" color: #ff0000;" class="libretaDetalles_<?=$j?> text-left"><small><small><?=$RSFactura?></small></small></td>
              <td style=" color: #ff0000;" class="libretaDetalles_<?=$j?> text-right small"><small><?=$MontoFactura?></small></td>
              <td style=" color: #ff0000;" class="libretaDetalles_<?=$j?> td-actions text-right small">
                <?php
                if($CodFactura==null || $CodFactura==''){?>
                  <a href="#" onclick="seleccionar_libretaBancaria(<?=$CodLibretaDetalle?>)" class="btn btn-fab btn-success btn-sm" title="Seleccionar Item"><i class="material-icons">done</i></a>
                <?php }?>
              </td>
            </tr>
          <?php $index++;}
	  		}
	  		$j++;
		}
	}
	?>
  </tbody>
</table>

