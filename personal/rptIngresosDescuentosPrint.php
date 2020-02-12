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
$cod_personal=$_POST["cod_personal"];
// $areas=$_POST["cod_area"];

$nombre_personal=namePersonal($cod_personal);
$sqlActivos="SELECT pp.dias_trabajados,pp.haber_basico,pp.monto_bonos,pp.total_ganado,pp.monto_descuentos,pp.liquido_pagable,
(select g.nombre from gestiones g where g.codigo=p.cod_gestion)as gestion,
(select m.nombre from meses m where m.codigo =p.cod_mes)as mes
from planillas_personal_mes pp,planillas p
where p.codigo=pp.cod_planilla and pp.cod_personalcargo=$cod_personal ORDER BY gestion desc";  
$stmtActivos = $dbh->prepare($sqlActivos);
$stmtActivos->execute();
// bindColumn
$stmtActivos->bindColumn('dias_trabajados', $dias_trabajados);
$stmtActivos->bindColumn('haber_basico', $haber_basico);
$stmtActivos->bindColumn('monto_bonos', $monto_bonos);
$stmtActivos->bindColumn('total_ganado', $total_ganado);
$stmtActivos->bindColumn('monto_descuentos', $monto_descuentos);
$stmtActivos->bindColumn('liquido_pagable', $liquido_pagable);
$stmtActivos->bindColumn('gestion', $gestion);
$stmtActivos->bindColumn('mes', $mes);

?>
<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  
                  <h4 class="card-title"> 
                    <img  class="card-img-top"  src="../marca.png" style="width:100%; max-width:250px;">
                      Reporte De Ingresos y Descuentos
                  </h4>

                  <!-- <h4 class="card-title text-center">Reporte De Activos Fijos Por Unidad</h4> -->
                  
                  <h6 class="card-title">Personal: <?=strtoupper($nombre_personal);?></h6>
                </div>
                <div class="card-body">
                  <div class="table-responsive">

                    <?php
                    $html='<table class="table table-bordered table-condensed" id="" cellpadding="0" cellspacing="0">'.
                      '<thead class="bg-secondary text-white">'.
                        '<tr >'.                          
                          '<th class="font-weight-bold">Gestion</th>'.
                          '<th class="font-weight-bold">Mes</th>'.
                          '<th class="font-weight-bold">Dias Trabajados</th>'.
                          '<th class="font-weight-bold">Haber Básico</th>'.
                          '<th class="font-weight-bold">Total Bonos</th>'.
                          '<th class="font-weight-bold">Total Ganado</th>'.
                          '<th class="font-weight-bold">Total Descuentos</th>'.
                          '<th class="font-weight-bold">Liquido Pagable</th>'.
                        '</tr>'.
                      '</thead>'.
                      '<tbody>';
                        //<?php  
                          $contador = 0;
                          while ($rowActivos = $stmtActivos->fetch(PDO::FETCH_ASSOC)) {
                            
                          $contador++;   
                        $html.='<tr>'.
                          '<td class="text-left small">'.$gestion.'</td>'.
                          '<td class="text-left small">'.$mes.'</td>'.
                          '<td class="text-center small">'.$dias_trabajados.'</td>'.
                          '<td class="text-center small">'.number_format($haber_basico, 2, '.', ',').'</td>'.
                          '<td class="text-center small">'.number_format($monto_bonos, 2, '.', ',').'</td>'.
                          '<td class="text-center small">'.number_format($total_ganado, 2, '.', ',').'</td>'.
                          '<td class="text-center small">'.number_format($monto_descuentos, 2, '.', ',').'</td>'.
                          '<td class="text-center small">'.number_format($liquido_pagable, 2, '.', ',').'</td>'.
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
