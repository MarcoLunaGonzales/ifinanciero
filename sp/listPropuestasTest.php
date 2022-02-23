<?php

require_once '../conexion.php';
require_once '../styles.php';

$dbh = new Conexion();

$stmt = $dbh->prepare("select s.codigo, s.cod_estadosimulacion, e.nombre as nombreestado
 from simulaciones_servicios s, estados_simulaciones e where s.cod_estadosimulacion=e.codigo;");

$stmt->execute();

$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('cod_estadosimulacion', $codEstado);
$stmt->bindColumn('nombreestado', $nombreEstado);

?>

<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header <?= $colorCard; ?> card-header-icon">
            <div class="card-icon">
              <i class="material-icons"><?= $iconCard; ?></i>
            </div>
            <h4 class="card-title">TestPropuestas</h4>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table id="tablePaginator" class="table table-condensed">
                <thead>
                  <tr>
                    <th class="text-left">#</th>
                    <th class="text-center">cod estado</th>
                    <th class="text-center">nombre estado</th>
                    <th class="text-center">ESTADO EXT</th>
                    <th class="text-center">NOMBRE ESTADO EXT</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                     $index = 1;
                     $idEstadoExt=0;
                     $nombreEstadoExt="";
                      while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {

                          $sql2="SELECT ibnorca.id_estadoobjeto(2707, $codigo) AS IdEstado, ibnorca.d_clasificador(ibnorca.id_estadoobjeto(2707, $codigo)) AS descr";
                          $stmt2 = $dbh -> prepare($sql2);
                           $stmt2 -> execute();
                           if($row2 = $stmt2 -> fetch(PDO::FETCH_ASSOC)){
                              $idEstadoExt=$row2['IdEstado'];
                              $nombreEstadoExt=$row2['descr'];
                           }

                  ?>
                    <tr>
                      <td class="text-center"><?= $codigo; ?></td>
                      <td class="text-center"><?= $codEstado; ?></td>
                      <td class="text-center"><?= $nombreEstado; ?></td>
                      <td class="text-center"><?= $idEstadoExt; ?></td>
                      <td class="text-center"><?= $nombreEstadoExt; ?></td>
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