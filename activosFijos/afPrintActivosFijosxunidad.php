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
$unidadOrganizacional=$_POST["unidad_organizacional"];
$areas=$_POST["areas"];
$personal=$_POST["personal"];


$unidadOrgString=implode(",", $unidadOrganizacional);
$areaString=implode(",", $areas);
$personalString=implode(",", $personal);

// echo $areaString;
$stringUnidades="";
foreach ($unidadOrganizacional as $valor ) {    
    $stringUnidades.=" ".abrevUnidad($valor)." ";
}
$stringAreas="";
foreach ($areas as $valor ) {    
    $stringAreas.=" ".abrevArea($valor)." ";
}
// $stringPersonal="";
// foreach ($personal as $valor ) {    
//     $stringPersonal.=" ".namesPersonal($valor)." ";
// }



$sqlActivos="SELECT a.codigo,a.codigoactivo,a.activo,(select uo.abreviatura from unidades_organizacionales uo where uo.codigo=a.cod_unidadorganizacional)as cod_unidadorganizacional,
(select ar.abreviatura from areas ar where ar.codigo=a.cod_area) as cod_area,
(select d.nombre from depreciaciones d where d.codigo=a.cod_depreciaciones) as cod_depreciaciones,
(select CONCAT_WS(' ',r.paterno,r.materno,r.primer_nombre) from personal r where r.codigo=a.cod_responsables_responsable) as cod_responsables_responsable,(select f.fechaasignacion from activofijos_asignaciones f where f.cod_activosfijos=a.codigo and f.cod_personal=a.cod_responsables_responsable order by f.codigo limit 1) as fechaasignacion,(select f2.fecha_recepcion from activofijos_asignaciones f2 where f2.cod_activosfijos=a.codigo and f2.cod_personal=a.cod_responsables_responsable order by f2.codigo limit 1) as fecha_recepcion,(select f3.cod_estadoasignacionaf from activofijos_asignaciones f3 where f3.cod_activosfijos=a.codigo and f3.cod_personal=a.cod_responsables_responsable order by f3.codigo limit 1) as estado_asignacionaf, a.cod_responsables_responsable as personalasignado
from activosfijos a
where a.cod_estadoactivofijo = 1 and a.cod_unidadorganizacional in ($unidadOrgString) and a.cod_area in ($areaString) and a.cod_responsables_responsable in ($personalString)";  

// echo $sqlActivos;

$stmtActivos = $dbh->prepare($sqlActivos);
$stmtActivos->execute();

// bindColumn
$stmtActivos->bindColumn('codigo', $codigoX);
$stmtActivos->bindColumn('codigoactivo', $codigoActivoX);
$stmtActivos->bindColumn('activo', $activoX);
$stmtActivos->bindColumn('cod_unidadorganizacional', $cod_unidadorganizacional);
$stmtActivos->bindColumn('cod_area', $cod_area);
$stmtActivos->bindColumn('cod_depreciaciones', $cod_depreciaciones);
$stmtActivos->bindColumn('cod_responsables_responsable', $responsables_responsable);
$stmtActivos->bindColumn('fechaasignacion', $fechaasignacion);
$stmtActivos->bindColumn('fecha_recepcion', $fecha_recepcion);
$stmtActivos->bindColumn('estado_asignacionaf', $estado_asignacionaf);
$stmtActivos->bindColumn('personalasignado', $codPersonalAsignado);


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
                  
                  <h4 class="card-title"> 
                    <img  class="card-img-top"  src="../marca.png" style="width:100%; max-width:250px;">
                      Reporte De Activos Fijos Por Oficina, Area y Responsable
                  </h4>
                  <h6 class="card-title">Oficinas: <?=$stringUnidades; ?></h6>                        
                  <h6 class="card-title">Areas: <?=$stringAreas;?></h6>
                  <!-- <h6 class="card-title">Personal: <?=$stringPersonal?></h6> -->
                
                </div>
                <div class="card-body">
                  <div class="table-responsive">

                    <?php
                    $html='<table class="table table-bordered table-condensed" id="tablePaginatorFixed">'.
                      '<thead class="bg-secondary text-white">'.
                        '<tr >'.
                          '<th class="font-weight-bold">-</th>'.
                          '<th class="font-weight-bold">CodSis</th>'.
                          '<th class="font-weight-bold">Codigo</th>'.
                          '<th class="font-weight-bold">Oficina</th>'.
                          '<th class="font-weight-bold">Area</th>'.
                          '<th class="font-weight-bold">Rubro</th>'.
                          '<th class="font-weight-bold">Activo</th>'.
                          '<th class="font-weight-bold">Responsable</th>'.
                          '<th class="font-weight-bold">Estado</th>'.
                          '<th class="font-weight-bold">F.Alta</th>'.
                          '<th class="font-weight-bold">F.Recep</th>'.
                        '</tr>'.
                      '</thead>'.
                      '<tbody>';
                        //<?php  
                          $contador = 0;
                          while ($rowActivos = $stmtActivos->fetch(PDO::FETCH_ASSOC)) {
                            $contador++;   
                            $nombre_estado=nameTipoAsignacion($estado_asignacionaf);
                                                        
                        $html.='<tr>'.
                          '<td class="text-center small">'.$contador.'</td>'.
                          '<td class="text-center small">'.$codigoX.'</td>'.
                          '<td class="text-center small">'.$codigoActivoX.'</td>'.
                          '<td class="text-center small">'.$cod_unidadorganizacional.'</td>'.
                          '<td class="text-center small">'.$cod_area.'</td>'.
                          '<td class="text-left small">'.$cod_depreciaciones.'</td>'.
                          '<td class="text-left small">'.$activoX.'</td>'.
                          '<td class="text-left small"><a href="../reportes_activosfijos/imp_actaEntrega_html_all.php?cod_personal='.$codPersonalAsignado.'" target="_blank">'.$responsables_responsable.'</a></td>'.
                          '<td class="text-left small">'.$nombre_estado.'</td>'.
                          '<td class="text-left small">'.$fechaasignacion.'</td>'.
                          '<td class="text-left small">'.$fecha_recepcion.'</td>'.
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
