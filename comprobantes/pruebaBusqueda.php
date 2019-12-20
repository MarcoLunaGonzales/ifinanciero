<?php
$dbh = new Conexion();

$sqlBusqueda="SELECT p.codigo, p.numero, p.nombre from plan_cuentas p where p.nivel=5 ";
$sqlBusqueda.=" order by p.numero";


$stmt = $dbh->prepare($sqlBusqueda);
$stmt->execute();
$stmt->bindColumn('codigo', $codigoCuenta);
$stmt->bindColumn('numero', $numeroCuenta);
$stmt->bindColumn('nombre', $nombreCuenta);

?>

<div class="col-md-12">
	<div class="table-responsive">
		<table id="tableCuentasBuscar" class="table table-condensed" width="100%">
			<thead>
				<tr>
					<th>Nro. Cuenta</th>
	      			<th>Nombre</th>
	      			<th>Auxiliar</th>
	  			</tr>
			</thead>
			<tbody>
		<?php
		$cont=0;$contAux=0;
		while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {

			$numeroCuenta=trim($numeroCuenta);
			$nombreCuenta=trim($nombreCuenta);

			$sqlCuentasAux="SELECT codigo, nombre FROM cuentas_auxiliares where cod_cuenta='$codigoCuenta' order by 2";
			$stmtAux = $dbh->prepare($sqlCuentasAux);
			$stmtAux->execute();
			$stmtAux->bindColumn('codigo', $codigoCuentaAux);
			$stmtAux->bindColumn('nombre', $nombreCuentaAux);
			$txtAuxiliarCuentas="<table class='table table-condensed'>";
			while ($rowAux = $stmtAux->fetch(PDO::FETCH_BOUND)) {
				$txtAuxiliarCuentas.="<tr>
				<td class='text-left small'>$codigoCuentaAux</td>
				<td class='text-left small'><a href='javascript:setBusquedaCuenta(\"$codigoCuenta\",\"$numeroCuenta\",\"$nombreCuenta\",\"$codigoCuentaAux\",\"$nombreCuentaAux\");'>$nombreCuentaAux</a></td>
				</tr>";
				$contAux++;
			}  	
			$txtAuxiliarCuentas.="</table>";
		?>
		<tr>
			<td class="text-left"><?=$numeroCuenta;?></td>
	      	<td class="text-left"><a href="javascript:setBusquedaCuenta('<?=$codigoCuenta;?>','<?=$numeroCuenta;?>','<?=$nombreCuenta;?>','0','');"><?=$nombreCuenta;?></a></td>
	      	<td class="text-left"><?=$txtAuxiliarCuentas;?></td>
		</tr>
		<?php
		$cont++;
		}
		?>
	</tbody>
	<tfoot>
            <tr>
                <th>Numero</th>
                <th>Nombre</th>
                <th>Auxiliar</th>
            </tr>
        </tfoot>
		</table>
	</div>
</div>