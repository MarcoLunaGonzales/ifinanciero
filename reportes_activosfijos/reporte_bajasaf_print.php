<?php

error_reporting(-1);

require_once __DIR__.'/../conexion.php';
require_once __DIR__.'/../functions.php';
require_once  __DIR__.'/../fpdf_html.php';
require '../assets/phpqrcode/qrlib.php';

require_once '../layouts/bodylogin2.php';


$dbh = new Conexion();
set_time_limit(300);


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
$tipo=$_POST["tipo"];
$unidadOrgString=implode(",", $unidadOrganizacional);
$areaString=implode(",", $areas);
$rubrosString=implode(",", $rubros);


// echo $areaString;
$stringUnidades="";
foreach ($unidadOrganizacional as $valor ) {    
    $stringUnidades.=" ".abrevUnidad($valor)." ";
}
$stringAreas="";
foreach ($areas as $valor ) {    
    $stringAreas.=" ".abrevArea($valor)." ";
}
$stringRubros="";
foreach ($rubros as $valor ) {    
    $stringRubros.=" ".abrevDepreciacion($valor)." ";
}
if($tipo==1){
  $sqladd=" and tipo_af=1 and cod_depreciaciones in ($rubrosString)";
}else{
  $sqladd="and tipo_af=2";
}

$sqlActivos="SELECT codigoactivo,otrodato,(select uo.abreviatura from unidades_organizacionales uo where uo.codigo=cod_unidadorganizacional)as cod_unidadorganizacional,
(select a.abreviatura from areas a where a.codigo=cod_area) as cod_area,
(select d.abreviatura from depreciaciones d where d.codigo=cod_depreciaciones) as cod_depreciaciones,
DATE_FORMAT(fechalta, '%d/%m/%Y')as fechalta,valorinicial,valorresidual,
(select CONCAT_WS(' ',r.paterno,r.materno,r.primer_nombre) from personal r where r.codigo=cod_responsables_responsable) as cod_responsables_responsable,
(select CONCAT_WS(' ',r.paterno,r.materno,r.primer_nombre) from personal r where r.codigo=cod_responsables_responsable2) as cod_responsables_responsable2,
(select e.nombre from estados_activofijo e where e.codigo=cod_estadoactivofijo) as estado_af,
(select t.tipo_bien from tiposbienes t where t.codigo=cod_tiposbienes)as tipo_bien,fecha_baja,obs_baja
from activosfijos 
where cod_estadoactivofijo = 3 and cod_unidadorganizacional in ($unidadOrgString) and cod_area in ($areaString) $sqladd ";  

//echo $sqlActivos;

$stmtActivos = $dbh->prepare($sqlActivos);
$stmtActivos->execute();

// bindColumn
$stmtActivos->bindColumn('codigoactivo', $codigoActivoX);
$stmtActivos->bindColumn('otrodato', $activoX);
$stmtActivos->bindColumn('cod_unidadorganizacional', $cod_unidadorganizacional);
$stmtActivos->bindColumn('cod_area', $cod_area);
$stmtActivos->bindColumn('cod_depreciaciones', $cod_depreciaciones);
$stmtActivos->bindColumn('fechalta', $fecha_alta);
$stmtActivos->bindColumn('valorinicial', $valor_inicial);
$stmtActivos->bindColumn('valorresidual', $valor_residual);
$stmtActivos->bindColumn('cod_responsables_responsable', $responsables_responsable);
$stmtActivos->bindColumn('cod_responsables_responsable2', $responsables_responsable2);
$stmtActivos->bindColumn('estado_af', $estado_af);
$stmtActivos->bindColumn('tipo_bien', $tipo_bien);
$stmtActivos->bindColumn('fecha_baja', $fecha_baja);
$stmtActivos->bindColumn('obs_baja', $obs_baja);
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
                  <h4 class="card-title"> <img  class="card-img-top"  src="../marca.png" style="width:100%; max-width:50px;">  Reporte De Activos Fijos Dados de Baja</h4>
                  <h6 class="card-title">Oficinas: <?=$stringUnidades; ?></h6>                        
                  <h6 class="card-title">Areas: <?=$stringAreas;?></h6>
                  <h6 class="card-title">Rubros: <?=$stringRubros?></h6>
                </div>
                
                <div class="card-body">
                  <div class="table-responsive">

                    <table class="table table-condensed" id="tablePaginatorFixed_af_baja">
                      <thead class="bg-secondary text-white">
                        <tr >
                          <th width="1%" class="text-center">-</th>
                          <th width="3%" class="font-weight-bold"><small>Codigo</small></th>
                          <th width="4%" class="font-weight-bold"><small>Of/Area</small></th>
                          <th width="3%" class="font-weight-bold"><small>Rubro</small></th>
                          <th width="25%" class="font-weight-bold"><small>Activo</small></th>
                          <th width="3%" class="font-weight-bold"><small>F.Alta</small></th>
                          <th width="15%" class="font-weight-bold"><small>Respo1</small></th>
                          <th width="15%" class="font-weight-bold"><small>Respo2</small></th>
                          <th width="4%" class="font-weight-bold"><small>F.Baja</small></th>
                          <th width="25" class="font-weight-bold"><small>Obs</small></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php  
                          $contador = 0;
                          while ($rowActivos = $stmtActivos->fetch(PDO::FETCH_ASSOC)) {
                            $fecha_baja=date('d/m/Y',strtotime($fecha_baja));
                            $contador++;   
                        ?>
                        <tr>
                          <td class="text-center small"><small><?=$contador;?></small></td>
                          <td class="text-center small"><small><?=$codigoActivoX;?></small></td>
                          <td class="text-center small"><small><?=$cod_unidadorganizacional;?>/<?=$cod_area;?></small></td>
                          <td class="text-left small"><small><?= $cod_depreciaciones; ?></small></td>
                          <td class="text-left small"><small><?= $activoX; ?></small></td>
                          <td class="text-center small"><small><?= $fecha_alta; ?></small></td>
                          <td class="text-left small"><small><?= $responsables_responsable; ?></small></td>
                          <td class="text-left small"><small><?= $responsables_responsable2; ?></small></td>
                          <td class="text-left small"><small><?= $fecha_baja;?></small></td>
                          <td class="text-left small"><small><?= $obs_baja;?></small></td>
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