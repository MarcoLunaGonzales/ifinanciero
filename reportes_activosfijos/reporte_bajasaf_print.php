<?php

error_reporting(-1);

require_once __DIR__.'/../conexion.php';
require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';

require_once '../layouts/bodylogin2.php';


$dbh = new Conexion();
set_time_limit(0);


$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();


//recibimos las variables
$unidadOrganizacional=$_POST["unidad_organizacional"];
$areas=$_POST["areas"];
$rubros=$_POST["rubros"];
$tipo=$_POST["tipo"];

$alta_baja=$_POST["alta_baja"];
// $alta_baja=1;//altas
$unidadOrgString=implode(",", $unidadOrganizacional);
$areaString=implode(",", $areas);
$rubrosString=implode(",", $rubros);

$gestion=$_POST["gestion"];

$nameGestion=nameGestion($gestion);

$fecha_inicio=$_POST["fecha_desde"];
$fecha_fin=$_POST["fecha_hasta"];

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
?>

<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header  card-header-icon">
                  <div class="float-right col-sm-2">
                    <h6 class="card-title">Exportar como:</h6>
                  </div>
                  <h4 class="card-title"> <img  class="card-img-top"  src="../marca.png" style="width:100%; max-width:50px;">  Reporte De Activos Fijos Altas y Bajas</h4>
                  <h6 class="card-title">Oficinas: <?=$stringUnidades; ?></h6>                        
                  <h6 class="card-title">Areas: <?=$stringAreas;?></h6>
                  <h6 class="card-title">Rubros: <?=$stringRubros?></h6>
                  <h6 class="card-title">De: <?=$fecha_inicio;?>  a: <?=$fecha_fin;?></h6>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <?php
                    if($alta_baja==1){
                      ?>
                      <table class="table table-condensed table-bordered" id="tablePaginatorFixed_af_baja">
                        <thead><tr class="bg-secondary text-white">
                             <td width="1%" class="text-center">-</td>
                            <td width="3%"><small>Cod<br>Interno</small></td>
                            <td width="3%"><small>Cod<br>Activo</small></td>
                            <td width="4%"><small>Of/Area</small></td>
                            <td width="3%"><small>CodRubro</small></td>
                            <td width="3%"><small>Rubro</small></td>
                            <td width="25%"><small>Activo</small></td>
                            <td width="3%"><small>F.Alta</small></td>
                            <td width="20%"><small>Responsable</small></td>
                            <td width="5%"><small>Valor Inicial</small></td>
                          </tr>
                        </thead>
                        <tbody>
                          <tr class="bg-primary text-white">
                            <td style="display: none;"></td>
                            <td style="display: none;"></td>
                            <td style="display: none;"></td>
                            <td style="display: none;"></td>
                            <td style="display: none;"></td>
                            <td style="display: none;"></td>
                            <td style="display: none;"></td>
                            <td style="display: none;"></td>
                            <td colspan="10"><b>ALTAS</b></td>
                          </tr>
                          <?php  
                          $contador = 0;
                          $suma_residual_altas=0;
                          $suma_inicial_altas=0;
                          $suma_inicial_altas_depre=0;
                          $sqlActivosAlta="SELECT codigo,codigoactivo,activo,(select uo.abreviatura from unidades_organizacionales uo where uo.codigo=cod_unidadorganizacional)as cod_unidadorganizacional, (select a.abreviatura from areas a where a.codigo=cod_area) as cod_area, (select d.abreviatura from depreciaciones d where d.codigo=cod_depreciaciones) as cod_depreciaciones, (select d.nombre from depreciaciones d where d.codigo=cod_depreciaciones) as nombre_rubro, DATE_FORMAT(fechalta, '%d/%m/%Y')as fechaltax, (select CONCAT_WS(' ',r.paterno,r.materno,r.primer_nombre) from personal r where r.codigo=cod_responsables_responsable) as cod_responsables_responsable, cod_estadoactivofijo,fecha_baja,obs_baja,valorinicial,valorresidual,depreciacionacumulada
                          from activosfijos 
                          where tipo_af=1 and cod_unidadorganizacional in ($unidadOrgString) and cod_area in ($areaString) $sqladd
                          and fechalta  BETWEEN '$fecha_inicio 00:00:00' and '$fecha_fin 23:59:59' 
                          order by fechalta";  
                          // echo $sqlActivosAlta;
                          $stmtActivosAlta = $dbh->prepare($sqlActivosAlta);
                          $stmtActivosAlta->execute();
                          $stmtActivosAlta->bindColumn('codigo', $codigoInternoX);
                          $stmtActivosAlta->bindColumn('codigoactivo', $codigoActivoX);
                          $stmtActivosAlta->bindColumn('activo', $activoX);
                          $stmtActivosAlta->bindColumn('cod_unidadorganizacional', $cod_unidadorganizacional);
                          $stmtActivosAlta->bindColumn('cod_area', $cod_area);
                          $stmtActivosAlta->bindColumn('cod_depreciaciones', $cod_depreciaciones);
                          $stmtActivosAlta->bindColumn('nombre_rubro', $nombreRubroX);
                          $stmtActivosAlta->bindColumn('fechaltax', $fecha_alta);
                          $stmtActivosAlta->bindColumn('valorresidual', $valorresidual);
                          $stmtActivosAlta->bindColumn('valorinicial', $valorinicial);
                          $stmtActivosAlta->bindColumn('depreciacionacumulada', $depreciacionacumulada);
                          $stmtActivosAlta->bindColumn('cod_responsables_responsable', $responsables_responsable);
                          // $stmtActivosAlta->bindColumn('cod_responsables_responsable2', $responsables_responsable2);
                          $stmtActivosAlta->bindColumn('cod_estadoactivofijo', $cod_estadoactivofijo);
                          $stmtActivosAlta->bindColumn('fecha_baja', $fecha_baja);
                          $stmtActivosAlta->bindColumn('obs_baja', $obs_baja);
                          while ($rowActivos = $stmtActivosAlta->fetch(PDO::FETCH_ASSOC)) {
                            $suma_residual_altas+=$valorresidual;
                            $suma_inicial_altas+=$valorinicial;
                            $suma_inicial_altas_depre+=$depreciacionacumulada;
                            if($fecha_baja!=null){
                              $fecha_baja=date('d/m/Y',strtotime($fecha_baja));
                            }
                            $contador++;?>
                            <tr>
                              <td class="text-center small"><small><?=$contador;?></small></td>
                              <td class="text-center small"><small><?=$codigoInternoX;?></small></td>
                              <td class="text-center small"><small><?=$codigoActivoX;?></small></td>
                              <td class="text-center small"><small><?=$cod_unidadorganizacional;?>/<?=$cod_area;?></small></td>
                              <td class="text-left small"><small><?= $cod_depreciaciones; ?></small></td>
                              <td class="text-left small"><small><?= $nombreRubroX; ?></small></td>
                              <td class="text-left small"><small><?= $activoX; ?></small></td>
                              <td class="text-center small"><small><?= $fecha_alta; ?></small></td>
                              <td class="text-left small"><small><?= $responsables_responsable;?> </small></td>
                              
                              <td class="text-right small"><small><?=formatNumberDec($valorinicial);?></small></td>
                                                            
                            </tr><?php 
                          }  ?>
                          <tr class="bg-secondary text-white">
                            <td class="text-center small"><small></small></td>
                            <td class="text-center small"><small></small></td>
                            <td class="text-center small"><small></small></td>
                            <td class="text-left small"><small></small></td>
                            <td class="text-left small"><small></small></td>
                            <td class="text-left small"><small></small></td>
                            <td class="text-left small"><small></small></td>
                            <td class="text-left small"><small></small></td>
                            <td class="text-left small"><small>TOTAL ALTAS</small></td>
                            <td class="text-right small"><small><?= formatNumberDec($suma_inicial_altas);?></small></td>                           
                          </tr>
                        </tbody>
                      </table><?php 
                    }else{?>
                      <table class="table table-condensed table-bordered" id="tablePaginatorFixed_af_baja">
                        <thead><tr class="bg-secondary text-white">
                             <td width="1%" class="text-center">-</td>
                            <td width="3%"><small>Cod<br>Interno</small></td>
                            <td width="3%"><small>Cod<br>Activo</small></td>
                            <td width="4%"><small>Of/Area</small></td>
                            <td width="3%"><small>Cod<br>Rubro</small></td>
                            <td width="3%"><small>Rubro</small></td>
                            <td width="25%"><small>Activo</small></td>
                            <td width="3%"><small>F.Alta</small></td>
                            <td width="10%"><small>Responsable</small></td>
                            <!-- <td width="5%"><small>Valor Residual</small></td>
                            <td width="5%"><small>Valor Neto</small></td> -->

                            <td width="5%"><small>Valor Actualizado</small></td>
                              <td width="5%"><small>Depreciacion Acum</small></td>
                            <td width="5%"><small>Valor Neto</small></td>


                            <td width="4%"><small>F.Baja</small></td>
                            <td width="25"><small>Obs</small></td>
                          </tr>
                        </thead>
                        <tbody>
                          <tr class="bg-primary text-white">
                            <td style="display: none;"></td>
                            <td style="display: none;"></td>
                            <td style="display: none;"></td>
                            <td style="display: none;"></td>
                            <td style="display: none;"></td>
                            <td style="display: none;"></td>
                            <td style="display: none;"></td>
                            <td style="display: none;"></td>
                            <td style="display: none;"></td>
                            <td style="display: none;"></td>
                            <td style="display: none;"></td>
                            <td style="display: none;"></td>
                            <td style="display: none;"></td>
                            <td colspan="14"><b>BAJAS</b></td>
                          </tr>
                          <?php  
                          $contador = 0;
                          $sqlActivosBaja="SELECT codigo,codigoactivo,activo,(select uo.abreviatura from unidades_organizacionales uo where uo.codigo=cod_unidadorganizacional)as cod_unidadorganizacional, (select a.abreviatura from areas a where a.codigo=cod_area) as cod_area, (select d.abreviatura from depreciaciones d where d.codigo=cod_depreciaciones) as cod_depreciaciones, (select d.nombre from depreciaciones d where d.codigo=cod_depreciaciones) as nombre_rubro, DATE_FORMAT(fechalta, '%d/%m/%Y')as fechalta, (select CONCAT_WS(' ',r.paterno,r.materno,r.primer_nombre) from personal r where r.codigo=cod_responsables_responsable) as cod_responsables_responsable, cod_estadoactivofijo,fecha_baja,obs_baja
                          from activosfijos 
                          where cod_estadoactivofijo = 3 and tipo_af=1 and cod_unidadorganizacional in ($unidadOrgString) and cod_area in ($areaString) $sqladd
                          and fecha_baja BETWEEN '$fecha_inicio 00:00:00' and '$fecha_fin 23:59:59'
                          order by fecha_baja";
                          // echo $sqlActivosBaja;
                          $stmtActivosBajas = $dbh->prepare($sqlActivosBaja);
                          $stmtActivosBajas->execute();
                          $stmtActivosBajas->bindColumn('codigo', $codigoX);
                          $stmtActivosBajas->bindColumn('codigoactivo', $codigoActivoX);
                          $stmtActivosBajas->bindColumn('activo', $activoX);
                          $stmtActivosBajas->bindColumn('cod_unidadorganizacional', $cod_unidadorganizacional);
                          $stmtActivosBajas->bindColumn('cod_area', $cod_area);
                          $stmtActivosBajas->bindColumn('cod_depreciaciones', $cod_depreciaciones);
                          $stmtActivosBajas->bindColumn('nombre_rubro', $nombreRubroX);
                          $stmtActivosBajas->bindColumn('fechalta', $fecha_alta);
                          // $stmtActivosBajas->bindColumn('valorinicial', $valor_inicial);
                          $stmtActivosBajas->bindColumn('cod_responsables_responsable', $responsables_responsable);
                          
                          $stmtActivosBajas->bindColumn('cod_estadoactivofijo', $cod_estadoactivofijo);
                          $stmtActivosBajas->bindColumn('fecha_baja', $fecha_baja);
                          $stmtActivosBajas->bindColumn('obs_baja', $obs_baja);
                          $suma_residual_bajas=0;
                          $suma_inicial_bajas=0;
                          $suma_actualizado_bajas=0;
                            $suma_depreante_bajas=0;
                          while ($rowActivos = $stmtActivosBajas->fetch(PDO::FETCH_ASSOC)) {
                            $sql="SELECT d2_valorresidual,d4_valoractualizado,d9_depreciacionacumuladaactual,d10_valornetobs 
                            from mesdepreciaciones m, mesdepreciaciones_detalle md
                            WHERE m.codigo = md.cod_mesdepreciaciones 
                            and md.cod_activosfijos = $codigoX and m.estado=1 order by m.codigo desc limit 1";
                            // echo $sql;
                            $stmt2 = $dbh->prepare($sql);
                            $stmt2->execute();
                            $row2 = $stmt2->fetch();
                            $d2_valorresidual = $row2["d2_valorresidual"];
                            $d4_valoractualizado = $row2["d4_valoractualizado"];
                            $d9_depreciacionacumuladaactual = $row2["d9_depreciacionacumuladaactual"];
                            $d10_valornetobs = $row2["d10_valornetobs"];
                            $suma_actualizado_bajas+=$d4_valoractualizado;
                            $suma_depreante_bajas+=$d9_depreciacionacumuladaactual;
                            $suma_inicial_bajas+=$d10_valornetobs;
                            $fecha_baja=date('d/m/Y',strtotime($fecha_baja));
                            $contador++;?>
                            <tr> 
                              <td class="text-center small"><small><?=$contador;?></small></td>
                              <td class="text-center small"><small><?=$codigoX;?></small></td>
                              <td class="text-center small"><small><?=$codigoActivoX;?></small></td>
                              <td class="text-center small"><small><?=$cod_unidadorganizacional;?>/<?=$cod_area;?></small></td>
                              <td class="text-left small"><small><?= $cod_depreciaciones; ?></small></td>
                              <td class="text-left small"><small><?= $nombreRubroX; ?></small></td>
                              <td class="text-left small"><small><?= $activoX; ?></small></td>
                              <td class="text-center small"><small><?= $fecha_alta; ?></small></td>
                              <td class="text-left small"><small><?= $responsables_responsable; ?> </small></td>
                              <td class="text-right small"><small><?= formatNumberDec($d4_valoractualizado);?></small></td>
                              <td class="text-right small"><small><?= formatNumberDec($d9_depreciacionacumuladaactual);?></small></td>
                              <td class="text-right small"><small><?= formatNumberDec($d10_valornetobs);?></small></td>
                              <td class="text-left small"><small><?= $fecha_baja;?></small></td>
                              <td class="text-left small"><small><?= $obs_baja;?></small></td>
                            </tr><?php 
                          }  ?>
                          <tr class="bg-secondary text-white">
                            <td class="text-center small"><small></small></td>
                            <td class="text-center small"><small></small></td>
                            <td class="text-center small"><small></small></td>
                            <td class="text-center small"><small></small></td>
                            <td class="text-center small"><small></small></td>
                            <td class="text-left small"><small></small></td>
                            <td class="text-left small"><small></small></td>
                            <td class="text-center small"><small></small></td>
                            <td class="text-left small"><small>TOTAL BAJAS</small></td>
                            <td class="text-right small"><small><?= formatNumberDec($suma_actualizado_bajas);?></small></td>
                            <td class="text-right small"><small><?= formatNumberDec($suma_depreante_bajas);?></small></td>
                            <td class="text-right small"><small><?= formatNumberDec($suma_inicial_bajas);?></small></td>
                            <td class="text-left small"><small></small></td>
                            <td class="text-left small"><small></small></td>
                          </tr>
                        </tbody>
                      </table>
                      <?php 
                    } ?>
                      
                    
                  </div>
                </div>
              </div>
            </div>
          </div>  
        </div>
    </div>