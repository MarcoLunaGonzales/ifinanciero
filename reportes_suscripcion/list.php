<?php
require_once 'conexion.php';
require_once 'styles.php';
setlocale(LC_TIME, "Spanish");

$dbh = new Conexion();
$lista = $dbh->prepare("SELECT fs.*, DATE_FORMAT(fs.fecha_inicio_suscripcion, '%d-%m-%Y') as fecha_inicio,  c.nombre as nombre_cliente
                  FROM facturas_suscripcionestienda fs
                  LEFT JOIN clientes c ON c.codigo = fs.id_cliente
                  ORDER BY codigo DESC");
$lista->execute();
?>

<div class="content">
	<div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header card-header-info card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">today</i>
                  </div>
                  <h4 class="card-title">Lista de Suscripciones</h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive" id="data_comprobantes">
                    <table id="tablePaginator" class="table table-condensed">
                      <thead>
                        <tr>
                          <th class="text-center">#</th>
                          <th>Codigo Sucripción</th>
                          <th>Glosa</th>
                          <th>Codigo Factura</th>
                          <th>Codigo Catalogo</th>
                          <th>Cliente</th>
                          <th>Tipo Venta</th>
                          <th class="text-right">Fecha Suscripción</th>
                        </tr>
                      </thead>
                      <tbody>
<?php
						     $index=1;

                      	while ($row = $lista->fetch(PDO::FETCH_ASSOC)) {
                          $cod_suscripcion  = $row['cod_suscripcion'];
                          $glosa            = $row['glosa'];
                          $codFactura       = $row['cod_factura'];
                          $catalogo         = $row['catalogo'];
                          $nombre_cliente   = $row['nombre_cliente'];
                          $id_tipo_venta    = $row['id_tipo_venta'];
                          $fecha_inicio = $row['fecha_inicio'];
?>
                        <tr>
                          <td align="center"><?=$index;?></td>                          
                          <td class="text-left font-weight-bold"><?=empty($cod_suscripcion)?'Sin suscripción':$cod_suscripcion;?></td>
                          <td><?=empty($glosa)?'-':$glosa;?></td>
                          <td><?=empty($codFactura)?'-':$codFactura;?></td>
                          <td><?=$catalogo;?></td>
                          <td><?=$nombre_cliente;?></td>
                          <td><?=empty($id_tipo_venta)?'Impreso':'Digital';?></td>
                          
                          <td class="td-actions text-right">
                            <?=$fecha_inicio;?>
                          </td>
                        </tr>
<?php
							$index++;
                      }
?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>  
        </div>
    </div>
