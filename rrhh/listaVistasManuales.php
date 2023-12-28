<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'functions.php';
require_once 'functionsGeneral.php';
require_once 'rrhh/configModule.php';

$globalAdmin        =$_SESSION["globalAdmin"];
$globalCodUnidad    =$_SESSION["globalUnidad"];
$globalNombreUnidad =$_SESSION["globalNombreUnidad"];

$dbh = new Conexion();

$sql = "SELECT ma.codigo, c.nombre as cargo_nombre, c.abreviatura as cargo_abreviatura, DATE_FORMAT(ma.fecha_inicio,'%d-%m-%Y %H:%i:%s'), DATE_FORMAT(ma.fecha_fin,'%d-%m-%Y %H:%i:%s') as fecha_fin
    FROM manuales_aprobacion ma
    LEFT JOIN cargos c ON c.codigo = ma.cod_cargo
    WHERE ma.cod_estado = 2
    AND ma.nro_version = (SELECT MAX(nro_version) FROM manuales_aprobacion WHERE cod_cargo = ma.cod_cargo)";
$stmtAdmnin = $dbh->prepare($sql);
$stmtAdmnin->execute();
  
  ?>

  <div class="content">
    <div class="container-fluid">
      <div class="col-md-12">     
        <div class="card">
          <div class="card-header <?=$colorCard;?> card-header-text">
            <div class="card-icon">
              <i class="material-icons"><?=$iconCard;?></i>
            </div>
            <h4 class="card-title">Reporte de Visitas - Manuales Aprobados</h4>       
          </div>
          <div class="card-body">
              <table class="table table-striped" id="tablePaginator">
                <thead>
                    <tr>
                        <th>#</th>               
                        <th>Cargo</th>
                        <th>Abreviatura</th>   
                        <th>Nro. de Visualizaciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $index=1;
                        while ($row = $stmtAdmnin->fetch(PDO::FETCH_ASSOC)) {              
                    ?>
                    <tr>
                        <td><?=$index++?></td>
                        <td><?=$row['cargo_nombre']?></td>
                        <td><?=$row['cargo_abreviatura']?></td>
                        <td>
                            <?php
                                $cod_manual_aprobacion = $row['codigo'];
                                $sql = "SELECT
                                            mav.cod_manual_aprobacion,
                                            CONCAT(p.primer_nombre, ' ', p.paterno, ' ', p.materno) as personal,
                                            COUNT(*) as total_vista,
                                            DATE_FORMAT(MAX(mav.fecha), '%d-%m-%Y %H:%s:%i') as ultima_fecha
                                        FROM manuales_aprobacion_vistas mav
                                        LEFT JOIN personal p ON p.codigo = mav.cod_personal
                                        WHERE mav.cod_manual_aprobacion = '$cod_manual_aprobacion'
                                        GROUP BY mav.cod_manual_aprobacion, personal";
                                $stmtPersonal = $dbh->prepare($sql);
                                $stmtPersonal->execute();
                                if ($stmtPersonal->rowCount() > 0) {
                                    while ($rowPersonal = $stmtPersonal->fetch(PDO::FETCH_ASSOC)) {  
                            ?>
                            <span class="badge badge-primary mb-1" style="display: inline-block;" title="Ultima vista: <?=$rowPersonal['ultima_fecha']?>">
                                <?=$rowPersonal['personal']?>
                                <span class="badge badge-light" style="color: black; display: inline-block; vertical-align: middle;">
                                    <i class="material-icons" title="Visitas" style="font-size: 16px; vertical-align: middle;">remove_red_eye</i> <?=$rowPersonal['total_vista']?>
                                </span>
                            </span><br>
                            <?php
                                    }
                                }else{
                            ?>
                                <strong class="text-danger">
                                    <i class="material-icons" style="vertical-align: middle;">info_outline</i>
                                    No se encontraron vistas
                                </strong>

                            <?php
                                }
                            ?>
                        </td>
                    </tr>
                    <?php
                        }
                    ?>
                </tbody>                                      
              </table>
          </div> 
        </div>
      </div>
    </div>
  </div>
