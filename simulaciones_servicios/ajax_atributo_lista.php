<?php
require_once '../conexion.php';

$codigo 	= $_POST['cod_simulacion_servicio'];
$tipo_modal = $_POST['tipo_modal'];

$dbh = new Conexion();
$sqlArrayAtributos="SELECT
					s.codigo,
					sa.codigo as cod_simulacionservicio_atributo,
					sa.nombre,
					sa.direccion,
					sa.procesos,
					sa.marca,
					sa.nro_sello,
					GROUP_CONCAT(DISTINCT vi.codigo SEPARATOR ', ') AS atr_norma_int,
					COALESCE(atr_nac_subquery.atr_norma_nac_codigo, '') AS atr_norma_nac,
					CONCAT(
								'<ul>',
								IFNULL(GROUP_CONCAT(DISTINCT CONCAT('<li>', vi.nombre, '</li>') SEPARATOR ''), ''),
								IFNULL(atr_nac_subquery.atr_norma_lista_html, ''),
								'</ul>'
						) AS atr_norma_html

					FROM simulaciones_servicios s
					LEFT JOIN simulaciones_servicios_atributos sa ON sa.cod_simulacionservicio = s.codigo
					LEFT JOIN simulaciones_servicios_atributosnormas sni ON sni.cod_simulacionservicioatributo = sa.codigo AND sni.catalogo = 'I'
					LEFT JOIN normas vi ON sni.cod_norma = vi.codigo
					LEFT JOIN (
						SELECT
							snl.cod_simulacionservicioatributo,
							GROUP_CONCAT(DISTINCT vn.codigo SEPARATOR ', ') AS atr_norma_nac_codigo,
							CONCAT('<li>', GROUP_CONCAT(DISTINCT vn.nombre SEPARATOR '</li><li>'), '</li>') AS atr_norma_lista_html
						FROM simulaciones_servicios_atributosnormas snl
						LEFT JOIN normas vn ON snl.cod_norma = vn.codigo
						WHERE snl.catalogo = 'L'
						GROUP BY snl.cod_simulacionservicioatributo
					) atr_nac_subquery ON atr_nac_subquery.cod_simulacionservicioatributo = sa.codigo
					WHERE s.codigo = '$codigo'
					AND sa.cod_estado = 1
					GROUP BY s.codigo, sa.nombre, sa.direccion, sa.marca
					ORDER BY sa.codigo DESC";
$stmtArrayAtributos = $dbh->prepare($sqlArrayAtributos);
$stmtArrayAtributos->execute();
$atributos = $stmtArrayAtributos->fetchAll(PDO::FETCH_ASSOC);
?>
<!-- Lista de Sitios en una tabla -->
<div class="row">
    <div class="col-sm-12">
        <div class="float-right mb-3">
            <button type="button" class="btn btn-success btn-sm" onclick="agregarDatosAtributo()">
                <i class="material-icons" title="Nuevo">add</i> Agregar
            </button>
        </div>
        <table class="table table-striped" style="font-size: 11px;">
            <thead>
				<?php if($tipo_modal == 1){ ?>
	                <tr>
						<th width="5%">#</th>
						<th width="25%">Nombre</th>
						<th width="30%">Dirección</th>
						<th width="30%">Proceso</th>
						<th width="10%">Acciones</th>
					</tr>
				<?php }else if($tipo_modal == 2){ ?>
	                <tr>
						<th width="5%">#</th>
						<th width="10%">Nombre</th>
						<th width="10%">Dirección</th>
						<th width="15%">Marca</th>
						<th width="50%">Norma</th>
						<th width="5%">Sello</th>
						<th width="5%">Acciones</th>
					</tr>
				<?php } ?>
            </thead>
            <tbody>
				<?php 
				$index = 1;
				if($tipo_modal == 1){ 
					if (count($atributos) > 0) {
                		foreach ($atributos as $atributo) {
							$index++;
				?>
	                <tr>
						<td><?=$index?></td>
						<td><?=$atributo['nombre']?></td>
						<td><?=$atributo['direccion']?></td>
						<td><?=$atributo['procesos']?></td>
						<td>
							<button type="button" class="btn btn-info btn-sm btn-fab editarFormAtributoSitio"
								data-cod_simulacionservicio_atributo="<?=$atributo['cod_simulacionservicio_atributo']?>"
								data-nombre="<?=$atributo['nombre']?>"
								data-direccion="<?=$atributo['direccion']?>">
								<i class="material-icons" title="Editar Plantilla">edit</i>
							</button>

							<button type="button" onclick="AtEliminarDatosAtributo(<?=$atributo['cod_simulacionservicio_atributo']?>)" class="btn btn-danger btn-sm btn-fab">
								<i class="material-icons" title="Eliminar Plantilla">delete</i>
							</button>
						</td>
					</tr>
				<?php 
						}
					} else {
						echo '<tr><td colspan="4">No se encontraron registros.</td></tr>';
					}
				}else if($tipo_modal == 2){ 
					if (count($atributos) > 0) {
						foreach ($atributos as $atributo) {	
							$index++;	
				?>
				<tr>
					<td><?=$index?></td>
					<td><?=$atributo['nombre']?></td>
					<td><?=$atributo['direccion']?></td>
					<td><?=$atributo['marca']?></td>
					<td><?=$atributo['atr_norma_html']?></td>
					<td><?=$atributo['nro_sello']?></td>
					<td>
						<button type="button" class="btn btn-info btn-sm btn-fab editarFormAtributoProd"
								data-cod_simulacionservicio_atributo="<?=$atributo['cod_simulacionservicio_atributo']?>"
								data-nombre="<?=$atributo['nombre']?>"
								data-marca="<?=$atributo['marca']?>"
								data-sello="<?=$atributo['nro_sello']?>"
								data-atr_norma_int="<?=$atributo['atr_norma_int']?>"
								data-atr_norma_nac="<?=$atributo['atr_norma_nac']?>"
								data-direccion="<?=$atributo['direccion']?>"
								data-procesos="<?=$atributo['procesos']?>">
							<i class="material-icons" title="Editar Plantilla">edit</i>
						</button>

						<button type="button" onclick="AtEliminarDatosAtributo(<?=$atributo['cod_simulacionservicio_atributo']?>)" class="btn btn-danger btn-sm btn-fab">
							<i class="material-icons" title="Eliminar Plantilla">delete</i>
						</button>
					</td>
				</tr>
				<?php 
						}
						
					} else {
						echo '<tr><td colspan="7">No se encontraron registros.</td></tr>';
					}
				} 
				?>
            </tbody>
        </table>
    </div>
</div>
