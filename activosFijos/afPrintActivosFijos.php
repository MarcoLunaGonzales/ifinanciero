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
$unidadOrganizacional=$_POST["unidad_organizacional"];
$areas=$_POST["areas"];
$rubros=$_POST["rubros"];


$unidadOrgString=implode(",", $unidadOrganizacional);
$areaString=implode(",", $areas);
$rubrosString=implode(",", $rubros);




$sqlActivos="SELECT codigoactivo,activo,(select uo.abreviatura from unidades_organizacionales uo where uo.codigo=cod_unidadorganizacional)as cod_unidadorganizacional,
(select a.abreviatura from areas a where a.codigo=cod_area) as cod_area,
(select d.nombre from depreciaciones d where d.codigo=cod_depreciaciones) as cod_depreciaciones,
fechalta,(select r.nombre from personal2 r where r.codigo=cod_responsables_responsable) as cod_responsables_responsable
from activosfijos 
where cod_estadoactivofijo = 1 and cod_unidadorganizacional in ($unidadOrgString) and cod_area in ($areaString) and cod_depreciaciones in ($rubrosString)";  

//echo $sqlActivos;

$stmtActivos = $dbh->prepare($sqlActivos);
$stmtActivos->execute();

// bindColumn
$stmtActivos->bindColumn('codigoactivo', $codigoActivoX);
$stmtActivos->bindColumn('activo', $activoX);
$stmtActivos->bindColumn('cod_unidadorganizacional', $cod_unidadorganizacional);
$stmtActivos->bindColumn('cod_area', $cod_area);
$stmtActivos->bindColumn('cod_depreciaciones', $cod_depreciaciones);
$stmtActivos->bindColumn('fechalta', $fecha_alta);
$stmtActivos->bindColumn('cod_responsables_responsable', $responsables_responsable);
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
                  <h4 class="card-title"> <img  class="card-img-top"  src="../marca.png" style="width:100%; max-width:250px;">  Reporte De Activos Fijos</h4>
                  <!--<h6 class="card-title">Gestion: <?=$nameGestion;?></h6>  -->
                </div>
                
                <div class="card-body">
                  <div class="table-responsive">

                    <table class="table table-condensed" id="tablePaginatorFixed2">
                      <thead class="bg-secondary text-white">
                        <tr >
                          <th class="text-center">-</th>
                          <th class="font-weight-bold">Codigo Activo</th>
                          <th class="font-weight-bold">Unidad O.</th>
                          <th class="font-weight-bold">Area</th>
                          <th class="font-weight-bold">Rubro</th>
                          <th class="font-weight-bold">Activo</th>
                          <th class="font-weight-bold">Fecha de Alta</th>
                          <th class="font-weight-bold">Responsable</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php  
                          $contador = 0;
                          while ($rowActivos = $stmtActivos->fetch(PDO::FETCH_ASSOC)) {
                            $contador++;   
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
                          <td class="text-left small"><?= $cod_depreciaciones; ?></td>
                          <td class="text-left small"><?= $activoX; ?></td>
                          <td class="text-center small"><?= $fecha_alta; ?></td>
                          <td class="text-left small"><?= $responsables_responsable; ?></td>
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