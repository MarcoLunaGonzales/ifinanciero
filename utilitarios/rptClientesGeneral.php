<?php

require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../styles.php';


$dbh = new Conexion();

$table="clientes";
$moduleName="Clientes";

// Preparamos
$sql="SELECT a.codigo, a.nombre, (select u.nombre from unidades_organizacionales u where u.codigo=a.cod_unidad) as abreviatura, a.identificacion FROM $table a order by 2";
//echo $sql;
$stmt = $dbh->prepare($sql);
// Ejecutamos
$stmt->execute();

// bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('abreviatura', $abreviatura);
$stmt->bindColumn('identificacion', $identificacion);

?>

<div class="content">
	<div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">assignment</i>
                  </div>
                  <h4 class="card-title">Reporte <?=$moduleName?></h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-condensed" id="tablePaginatorReport">
                      <thead>
                        <tr>
                           <th class="text-center">#</th>
                          <th>Codigo</th>
                          <th>Nombre</th>
                          <th>Regional</th>
                          <th>NIT</th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php
                        $index=1;
                      	while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                      ?>
                        <tr>
                          <td align="center"><?=$index;?></td>
                          <td align="center"><?=$codigo;?></td>
                          <td class="text-left"><?=$nombre;?></td>
                          <td><?=$abreviatura;?></td>
                          <td><?=$identificacion;?></td>
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

