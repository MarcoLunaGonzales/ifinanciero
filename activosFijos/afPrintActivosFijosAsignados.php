<?php

error_reporting(-1);

require_once __DIR__.'/../conexion.php';
require_once __DIR__.'/../functions.php';
require_once  __DIR__.'/../fpdf_html.php';
require '../assets/phpqrcode/qrlib.php';

require_once '../layouts/bodylogin2.php';


$dbh = new Conexion();


$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

/*
$gestion=$_POST["gestion"];
$nameGestion=nameGestion($gestion);
*/
//recibimos las variables
$estado_asignacion_af=$_POST["estado_asignacion_af"];

$estadoAsigAFString=implode(",", $estado_asignacion_af);




$sqlActivos="SELECT cod_activosfijos,(select af.activo from activosfijos af where af.codigo=cod_activosfijos) as activo,(select uo.abreviatura from unidades_organizacionales uo where uo.codigo=cod_unidadorganizacional)as cod_unidadorganizacional,(select a.abreviatura from areas a where a.codigo=cod_area)as cod_area,fechaasignacion,estadobien_asig,(select p.nombre from personal2 p where p.codigo=cod_personal)as cod_personal,cod_estadoasignacionaf,(select eaf.nombre from estados_asignacionaf eaf where eaf.codigo=cod_estadoasignacionaf) as estadoAsigAF,fecha_recepcion,observaciones_recepcion,fecha_devolucion,observaciones_devolucion
from activofijos_asignaciones
where cod_estadoasignacionaf in ($estadoAsigAFString)";  

//echo $sqlActivos;

$stmtActivos = $dbh->prepare($sqlActivos);
$stmtActivos->execute();

// bindColumn
$stmtActivos->bindColumn('cod_activosfijos', $codigoActivoX);
$stmtActivos->bindColumn('activo', $activoX);
$stmtActivos->bindColumn('cod_unidadorganizacional', $cod_unidadorganizacional);
$stmtActivos->bindColumn('cod_area', $cod_area);


$stmtActivos->bindColumn('fechaasignacion', $fecha_asignacion);
$stmtActivos->bindColumn('estadobien_asig', $estado_bien_asig);
$stmtActivos->bindColumn('cod_personal', $personal);
$stmtActivos->bindColumn('cod_estadoasignacionaf', $cod_estadoasignacionaf);
$stmtActivos->bindColumn('estadoAsigAF', $estado_asignacion);
$stmtActivos->bindColumn('fecha_recepcion', $fecha_recepcion);
$stmtActivos->bindColumn('observaciones_recepcion', $observacion_recepcion);
$stmtActivos->bindColumn('fecha_devolucion', $fecha_devolucion);
$stmtActivos->bindColumn('observaciones_devolucion', $observacion_devolucion);
?>


<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              

              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="float-right col-sm-2">
                    <h6 class="card-title">Exportar como:</h6>
                  </div>
                  <h4 class="card-title"> <img  class="card-img-top"  src="../marca.png" style="width:100%; max-width:250px;">  Reporte De Activos Fijos Asignados</h4>
                  <!--<h6 class="card-title">Gestion: <?=$nameGestion;?></h6>  -->
                </div>
                
                <div class="card-body">
                  <div class="table-responsive">

                    <table class="table table-condensed" id="tablePaginatorFixedAsignacion">
                      <thead class="bg-secondary text-white">
                        <tr >
                          <th class="text-center">-</th>
                          <th class="font-weight-bold">Codigo Activo</th>
                          <th class="font-weight-bold">U. O.</th>
                          <th class="font-weight-bold">Area</th>
                          <th class="font-weight-bold">Activo</th>

                          <th class="font-weight-bold">Fecha De Asig.</th>
                          <th class="font-weight-bold">Estado Bien Asig.</th>
                          <th class="font-weight-bold">Responsable</th>
                          <th class="font-weight-bold">Estado Asig.</th>
                          <th class="font-weight-bold">F. Recepción</th>
                          <th class="font-weight-bold">Obs. Recepción</th>
                          <th class="font-weight-bold">F. Devolución</th>
                          <th class="font-weight-bold">Obs. Devolución</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php  
                          $contador = 0;
                          while ($rowActivos = $stmtActivos->fetch(PDO::FETCH_ASSOC)) {
                            $contador++;
                            if($cod_estadoasignacionaf==1){
                              $label='<span class="badge badge-warning">';
                            }
                            if($cod_estadoasignacionaf==2){
                              $label='<span class="badge badge-success">';
                            }
                            if($cod_estadoasignacionaf==3){
                              $label='<span class="badge badge-danger">';
                            }
                            if($cod_estadoasignacionaf==4){
                              $label='<span class="badge badge-primary">';
                            }
                            if($cod_estadoasignacionaf==5){
                              $label='<span class="badge badge-dark">';
                            }   
                        ?>
                        <tr>
                          <td class="text-center small"><?=$contador;?></td>
                          <td class="text-center small">
                            <?php
                              $dir = 'qr_temp/';
                              if(!file_exists($dir)){
                                  mkdir ($dir);}
                              $fileName = $dir.'test.png';
                              $tamanio = 2; //tamaño de imagen que se creará
                              $level = 'Q'; //tipo de precicion Baja L, mediana M, alta Q, maxima H
                              $frameSize = 1; //marco de qr
                              $contenido = $codigoActivoX;
                              QRcode::png($contenido, $fileName, $level,$tamanio,$frameSize);
                              echo '<img src="'.$fileName.'"/>';
                            ?>
                          </td>
                          <td class="text-center small"><?=$cod_unidadorganizacional; ?></td>
                          <td class="text-center small"><?= $cod_area; ?></td>
                          <td class="text-left small"><?= $activoX; ?></td>

                          <td class="text-left small"><?= $fecha_asignacion; ?></td>
                          <td class="text-center small"><?= $estado_bien_asig; ?></td>
                          <td class="text-left small"><?= $personal; ?></td>
                          <td class="text-left small"><?=$label.$estado_asignacion."</span>";?></td>
                          <td class="text-left small"><?= $fecha_recepcion; ?></td>
                          <td class="text-left small"><?= $observacion_recepcion; ?></td>
                          <td class="text-left small"><?= $fecha_devolucion; ?></td>
                          <td class="text-left small"><?= $observacion_devolucion; ?></td>

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
        </div>
    </div>