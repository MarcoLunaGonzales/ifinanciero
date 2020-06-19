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

$codigo=0;
$lista=obtenerObtenerLibretaBancaria($codigo);
// var_dump($lista->datos->detalle);
?>
<center><b><p>monto DEPOSITO EN CUENTA a facturar <?=number_format($saldo,2)?> Bs.</p></b></center>
<table  class="table table-bordered table-condensed table-sm">
  <thead>
    <tr style="background:#21618C; color:#fff;">
      <td class="text-center">#</td>
      <td class="small">Fecha</td>
      <td class="small">Hora</td>
      <td class="small">Descripcion</td>
      <td class="small" width="50%">Informacion C.</td>
      <td class="small">Sucursal</td>
      <td class="small">Monto</td>      
      <td class="small">Nro Documento</td>
      <td class="text-right" width="3%"></td>
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
				<td align="center" colspan="9" style="background:#e58400; color:#fff;"><button title="Detalles" id="botonLibreta<?=$j?>" style="border:none; background:#e58400; color:#fff;" onclick="activardetalleLibreta(<?=$j?>)"><small><?=$Banco;?> - <?=$Nombre;?></small></button></td>
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
				$monto=$v_detalle->monto; ?>
			    <tr>
			      <td style="display:none" class="libretaDetalles_<?=$j?> small" align="center"><?=$index;?></td>
			      <td style="display:none" class="libretaDetalles_<?=$j?> text-center small"><?=strftime('%d/%m/%Y',strtotime($FechaHoraCompleta))?></td>
			      <td style="display:none" class="libretaDetalles_<?=$j?> text-center small"><?=strftime('%H:%M:%S',strtotime($FechaHoraCompleta))?></td>
			      <td style="display:none" class="libretaDetalles_<?=$j?> text-left small"><?=$Descripcion?></td>
			      <td style="display:none" class="libretaDetalles_<?=$j?> text-left small"><?=$InformacionComplementaria?></td>      
			      <td style="display:none" class="libretaDetalles_<?=$j?> text-left small"><?=$Agencia?></td>
			      <td style="display:none" class="libretaDetalles_<?=$j?> text-center small"><?=number_format($monto,2)?></td>			      
			      <td style="display:none" class="libretaDetalles_<?=$j?> text-left small"><?=$NumeroDocumento?></td>
			      <td style="display:none" class="libretaDetalles_<?=$j?> td-actions text-right small">	      
			        <a href="#" onclick="seleccionar_libretaBancaria(<?=$CodLibretaDetalle?>)" class="btn btn-fab btn-success btn-sm" title="Seleccionar Item"><i class="material-icons">done</i></a>
			      </td>
			    </tr>
				<?php
				$index++;
	  		}
	  		$j++;
		}
	}
	?>
  </tbody>
</table>
