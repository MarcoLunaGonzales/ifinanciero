<?php

error_reporting(-1);

require_once __DIR__.'/../conexion.php';
require_once __DIR__.'/../functions.php';
require_once  __DIR__.'/../fpdf_html.php';
require '../assets/phpqrcode/qrlib.php';
require_once __DIR__.'/../functionsGeneral.php';
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
$unidadOrgString=implode(",", $unidadOrganizacional);
$areaString=implode(",", $areas);
$rubrosString=implode(",", $rubros);

$fecha_desde=$_POST["fecha_desde"];
$fecha_hasta=$_POST["fecha_hasta"];


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

$sqlActivos="SELECT a.codigo, a.codigoactivo, a.activo, (select uo.abreviatura from unidades_organizacionales uo where uo.codigo=a.cod_unidadorganizacional)as cod_unidadorganizacional,
(select ar.abreviatura from areas ar where ar.codigo=a.cod_area) as cod_area,
(select d.nombre from depreciaciones d where d.codigo=a.cod_depreciaciones) as cod_depreciaciones,
a.tipoalta, DATE_FORMAT(a.fechalta,'%d/%m/%Y')as fechalta_x, a.valorinicial, a.valorresidual,
(select CONCAT_WS(' ',r.paterno,r.materno,r.primer_nombre) from personal r where r.codigo=a.cod_responsables_responsable) as cod_responsables_responsable, ea.nombre as estado_af, t.tipo_bien, 
(SELECT mdd.d10_valornetobs from mesdepreciaciones_detalle mdd where mdd.cod_activosfijos=a.codigo ORDER BY mdd.cod_mesdepreciaciones desc limit 0,1)as valorresidual_v
from activosfijos a
LEFT JOIN tiposbienes t on t.codigo=a.cod_tiposbienes
LEFT JOIN estados_activofijo ea on ea.codigo=a.cod_estadoactivofijo
where a.cod_estadoactivofijo = 1 and a.cod_unidadorganizacional in ($unidadOrgString) and a.cod_area in ($areaString) and a.cod_depreciaciones in ($rubrosString) and a.fechalta between '$fecha_desde' and '$fecha_hasta'";  

//echo $sqlActivos;

$stmtActivos = $dbh->prepare($sqlActivos);
$stmtActivos->execute();

// bindColumn
$stmtActivos->bindColumn('codigo', $codigoX);
$stmtActivos->bindColumn('codigoactivo', $codigoActivoX);
$stmtActivos->bindColumn('activo', $activoX);
$stmtActivos->bindColumn('cod_unidadorganizacional', $cod_unidadorganizacional);
$stmtActivos->bindColumn('cod_area', $cod_area);
$stmtActivos->bindColumn('cod_depreciaciones', $cod_depreciaciones);
$stmtActivos->bindColumn('fechalta_x', $fecha_alta);
$stmtActivos->bindColumn('tipoalta', $tipo_alta);
$stmtActivos->bindColumn('valorinicial', $valor_inicial);
$stmtActivos->bindColumn('valorresidual', $valor_residual);
$stmtActivos->bindColumn('cod_responsables_responsable', $responsables_responsable);
$stmtActivos->bindColumn('estado_af', $estado_af);
// $stmtActivos->bindColumn('nombre_uo2', $nombre_uo2);
$stmtActivos->bindColumn('tipo_bien', $tipo_bien);
$stmtActivos->bindColumn('valorresidual_v', $valor_residualV);
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
                  <h6 class="card-title">Oficinas: <?=$stringUnidades; ?></h6>                        
                  <h6 class="card-title">Areas: <?=$stringAreas;?></h6>
                  <h6 class="card-title">Rubros: <?=$stringRubros?></h6>
                </div>
                
                <div class="card-body">
                  <div class="table-responsive">

                    <table class="table table-condensed" id="tablePaginatorFixed2">
                      <thead class="bg-secondary text-white">
                        <tr >
                          <th class="text-center">-</th>
                          <th class="font-weight-bold">CodSis</th>
                          <th class="font-weight-bold">Codigo</th>
                          <th class="font-weight-bold">Of/Area</th>                          
                          <th class="font-weight-bold">Rubro</th>
                          <th class="font-weight-bold">Activo</th>
                          <th class="font-weight-bold">Tipo De Alta</th>
                          <th class="font-weight-bold">Fecha De Alta</th>
                          <th class="font-weight-bold">Valor Ini.</th>
                          <th class="font-weight-bold">Valor Res.</th>
                          <th class="font-weight-bold">Responsable</th>
                          <th class="font-weight-bold">Estado AF</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php  
                          $contador = 0;
                          while ($rowActivos = $stmtActivos->fetch(PDO::FETCH_ASSOC)) {
                            $contador++;   
                        ?>
                        <tr>
                          <!-- <td class="text-center small"><?=$contador;?></td> -->
                          <td class="text-center small"></td>
                          <td class="text-center small"><?=$codigoX;?></td>
                          <td class="text-center small"><?=$codigoActivoX;?></td>
                          <!-- <td class="text-center small"> -->
                            <!-- <?php
                              $dir = 'qr_temp/';
                              if(!file_exists($dir)){
                                  mkdir ($dir);}
                              $fileName = $dir.'test.png';
                              $tamanio = 1.5; //tamaño de imagen que se creará
                              $level = 'L'; //tipo de precicion Baja L, mediana M, alta Q, maxima H
                              $frameSize = 1; //marco de qr                            
                              $contenido = "Cod:".$codigoActivoX."\nRubro:".$cod_depreciaciones."\nDesc:".$activoX."\nRespo.:".$cod_unidadorganizacional.' - '.$responsables_responsable;                               
                              QRcode::png($contenido, $fileName, $level,$tamanio,$frameSize);
                              echo '<img src="'.$fileName.'"/>';
                            ?> -->
                          <!-- </td> -->
                          <td class="text-center small"><?=$cod_unidadorganizacional?>/<?=$cod_area?></td>                          
                          <td class="text-left small"><?=$cod_depreciaciones?></td>
                          <td class="text-left small"><?=$activoX?></td>
                          <td class="text-left small"><?=$tipo_alta?></td>
                          <td class="text-center small"><?=$fecha_alta?></td>
                          <td class="text-left small"><?=formatNumberDec($valor_inicial)?></td>
                          <td class="text-left small"><?=formatNumberDec($valor_residualV)?></td>
                          <td class="text-left small"><?=$responsables_responsable?></td>
                          <td class="text-left small"><?=$estado_af?></td>
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