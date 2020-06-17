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
set_time_limit(300);

$codigo=4;
$lista=obtenerObtenerLibretaBancaria($codigo);
// var_dump($lista->datos->detalle);
?>
<table id="tablePaginator" class="table table-condensed">
  <thead>
    <tr style="background:#21618C; color:#fff;">
      <td class="text-center">#</td>
      <td>Fecha</td>
      <td>Hora</td>
      <td>Descripcion</td>
      <td>Informacion C.</td>
      <td>Sucursal</td>
      <td>Monto</td>
      <!-- <td>Nro Cheque</td> -->
      <td>Nro Documento</td>
      <td class="text-right">Acciones</td>
    </tr>
  </thead>
  <tbody>
	<?php
	$index=1;
	if($lista->estado==1){
	  	foreach ($lista->datos->detalle as $v) {
			$CodLibretaDetalle=$v->CodLibretaDetalle;
			$Descripcion=$v->Descripcion;
			$InformacionComplementaria=$v->InformacionComplementaria;
			$Agencia=$v->Agencia;
			$NumeroCheque=$v->NumeroCheque;
			$NumeroDocumento=$v->NumeroDocumento;
			$Fecha=$v->Fecha;
			$Hora=$v->Hora;
			$FechaHoraCompleta=$v->FechaHoraCompleta;
			$monto=$v->monto; ?>
		    <tr>
		      <td align="center"><?=$index;?></td>
		      <td class="text-center"><?=strftime('%d/%m/%Y',strtotime($FechaHoraCompleta))?></td>
		      <td class="text-center"><?=strftime('%H:%M:%S',strtotime($FechaHoraCompleta))?></td>
		      <td class="text-left"><?=$Descripcion?></td>
		      <td class="text-left"><?=$InformacionComplementaria?></td>      
		      <td class="text-left"><?=$Agencia?></td>
		      <td class="text-center"><?=number_format($monto,2,".","")?></td>
		      <!-- <td class="text-left"><?=$NumeroCheque?></td> -->
		      <td class="text-left"><?=$NumeroDocumento?></td>
		      <td class="td-actions text-right">	      
		        <a href="#" onclick="seleccionar_libretaBancaria(<?=$CodLibretaDetalle?>)" class="btn btn-fab btn-success btn-sm" title="Seleccionar Item"><i class="material-icons">done</i></a>
		      </td>
		    </tr>
			<?php
			$index++;
		}
	}
	?>
  </tbody>
</table>