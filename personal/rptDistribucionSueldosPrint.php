<?php

error_reporting(-1);

require_once __DIR__.'/../conexion.php';
require_once __DIR__.'/../functions.php';
require_once  __DIR__.'/../fpdf_html.php';
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
// $cod_uo=$_POST["unidad_organizacional"];
$unidad_organizacional=$_POST["unidad_organizacional"];
$cod_uo_String=implode(",", $unidad_organizacional);
// $nombre_uo=nameUnidad($cod_uo);
$sqlActivos="SELECT cod_personal,porcentaje,
(select CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=cod_personal) as nombre_personal,
(select a.nombre from areas a where a.codigo=cod_area) as nombre_area,
(select uo.nombre from unidades_organizacionales uo where uo.codigo=cod_uo) as nombre_uo
from personal_area_distribucion
where cod_estadoreferencial=1 and cod_uo in ($cod_uo_String) ORDER BY nombre_personal";  
$stmtActivos = $dbh->prepare($sqlActivos);
$stmtActivos->execute();
// bindColumn
$stmtActivos->bindColumn('cod_personal', $cod_personal);
$stmtActivos->bindColumn('porcentaje', $porcentaje);
$stmtActivos->bindColumn('nombre_personal', $nombre_personal);
$stmtActivos->bindColumn('nombre_area', $nombre_area);
$stmtActivos->bindColumn('nombre_uo', $nombre_uo);
?>
<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  
                  <h4 class="card-title"> 
                    <img  class="card-img-top"  src="../marca.png" style="width:100%; max-width:250px;">
                      Reporte De Distribución Planilla Por Area
                  </h4>

                  <!-- <h4 class="card-title text-center">Reporte De Activos Fijos Por Unidad</h4> -->
                  
                  <!-- <h6 class="card-title">Oficina: <?=$nombre_uo;?></h6> -->
                </div>
                <div class="card-body">
                  <div class="table-responsive">

                    <?php
                    $html='<table class="table table-bordered table-condensed" id="" cellpadding="0" cellspacing="0">'.
                      '<thead class="bg-secondary text-white">'.
                        '<tr >'.
                          '<th class="font-weight-bold">#</th>'.
                          '<th class="font-weight-bold">Cod personal</th>'.
                          '<th class="font-weight-bold">Personal</th>'.
                          '<th class="font-weight-bold">Oficina</th>'.
                          '<th class="font-weight-bold">Area</th>'.
                          '<th class="font-weight-bold">porcentaje</th>'.
                          
                        '</tr>'.
                      '</thead>'.
                      '<tbody>';
                        //<?php  
                          $contador = 0;
                          while ($rowActivos = $stmtActivos->fetch(PDO::FETCH_ASSOC)) {
                            if($porcentaje!=100){
                              $label='<span class="badge badge-warning">';
                            }else $label="";
                          $contador++;   
                        $html.='<tr>'.
                          '<td class="text-center small">'.$contador.'</td>'.
                          '<td class="text-center small">'.$cod_personal.'</td>'.
                          '<td class="text-left small">'.strtoupper($nombre_personal).'</td>'.
                          '<td class="text-left small">'.$nombre_uo.'</td>'.
                          '<td class="text-left small">'.$nombre_area.'</td>'.
                          '<td class="text-center small">'.$label.$porcentaje.'</span></td>'.                          
                        '</tr>';
                         
                          } 
                      $html.='</tbody>'.
                      
                    '</table>';
                    echo $html;
                    ?>

                  </div>
                </div>
              </div>
            </div>
          </div>  
        </div>
    </div>
