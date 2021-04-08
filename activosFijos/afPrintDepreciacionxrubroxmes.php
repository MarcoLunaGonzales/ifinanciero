<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';

require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';
require_once  __DIR__.'/../fpdf_html.php';
require_once '../layouts/bodylogin2.php';
$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$gestion = $_POST["gestion"];
$mes2 = $_POST["mes"];
$unidadOrganizacional=$_POST["unidad_organizacional"];
$cod_depreciaciones=$_POST["cod_depreciaciones"];

$unidadOrgString=implode(",", $unidadOrganizacional);
$depreciacionesString=implode(",", $cod_depreciaciones);
// echo $areaString;
$stringUnidades="";
foreach ($unidadOrganizacional as $valor ) {    
    $stringUnidades.=" ".abrevUnidad($valor)." ";
}
$stringDepreciaciones="";
foreach ($cod_depreciaciones as $valor ) {    
    $stringDepreciaciones.=" ".abrevDepreciacion($valor)." ";
}

$gestion=nameGestion($gestion);


$sql="SELECT (select nombre from unidades_organizacionales where codigo=af.cod_unidadorganizacional) as nombre_unidadO,
af.cod_unidadorganizacional as cod_unidadorganizacional
from activosfijos af
where af.cod_unidadorganizacional in ($unidadOrgString)
GROUP BY (nombre_unidadO)";
$stmtUO = $dbh->prepare($sql);
$stmtUO->execute();
$stmtUO->bindColumn('nombre_unidadO', $nombre_unidadO);
$stmtUO->bindColumn('cod_unidadorganizacional', $cod_unidadorganizacional);

$totalValorAnterior=0;
$total_rubro_actualizacion=0;
$total_valor_actualizado=0;
$total_depreAcumAnt=0;
$total_actDepAcum=0;
$total_deprePeriodo=0;
$totalrubro_depreciacion=0;
$total_valorNeto=0;
?>

<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="float-right col-sm-2">
                    <!-- <h6 class="card-title">Exportar como:</h6> -->
                  </div>
                  <h4 class="card-title"> 
                    <img  class="card-img-top"  src="../marca.png" style="width:100%; max-width:150px;">
                      Depreciación De Activos Fijos Por Rubro Por Mes
                  </h4>
                  <h6 class="card-title">
                    Gestion: <?php echo $gestion; ?> - Mes: <?php echo nameMes($mes2);?><br>
                    Oficinas: <?=$stringUnidades; ?><br>
                    Rubros: <?=$stringDepreciaciones; ?>
                  </h6>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-bordered table-condensed" id="tablePaginatorFixed">
                        <tbody>
                            <?php
                            while ($row = $stmtUO->fetch()) { ?>
                                <tr class="bg-dark text-white">
                                    <th colspan="11" >Oficina : <?php echo $nombre_unidadO; ?></th>
                                </tr>
                                <tr class="bg-info text-white">
                                    <th class=" small bg-primary ">Rubro</th>
                                    <th class=" small bg-primary font-weight-bold">Valor<br>Anterior</th>
                                    <!--th class=" small bg-primary font-weight-bold">Factor Actual.</th-->
                                    <th class=" small bg-primary font-weight-bold">Actualización</th>
                                    <th class=" small bg-primary font-weight-bold">Valor<br>Actualizado</th>
                                    <th class=" small bg-primary font-weight-bold">Depreciación<br>Acumulada Anterior</th>
                                    <th class=" small bg-primary font-weight-bold">Actualización<br>Depreciación Acumulada</th>
                                    <th class=" small bg-primary font-weight-bold">Depreciación Periodo</th>
                                    <th class=" small bg-primary font-weight-bold">Depreciación Acumulada</th>
                                    <th class=" small bg-primary font-weight-bold">Valor Neto</th>                                    
                                </tr>
                                <?php
                                    $sql="SELECT af.cod_depreciaciones from mesdepreciaciones m, mesdepreciaciones_detalle md, activosfijos af WHERE af.cod_estadoactivofijo=1 and m.codigo = md.cod_mesdepreciaciones and md.cod_activosfijos = af.codigo
                                             and af.cod_unidadorganizacional=$cod_unidadorganizacional and m.mes = $mes2 and m.gestion=$gestion and af.cod_depreciaciones in ($depreciacionesString) GROUP BY af.cod_depreciaciones";
                                    $stmt_rubro = $dbh->prepare($sql);
                                    $stmt_rubro->execute();
                                    $stmt_rubro->bindColumn('cod_depreciaciones', $cod_depreciaciones_rubros);
                                    while ($row = $stmt_rubro->fetch()) { 
                                        $nombreRubros=nameDepreciacion($cod_depreciaciones_rubros);

                                        $stmt2 = $dbh->prepare("SELECT sum(md.d10_valornetobs)valorNeto,sum(md.d9_depreciacionacumuladaactual)totalDepreAcumu,sum(md.d8_depreciacionperiodo)deprePeriodo,sum(md.d7_incrementodepreciacionacumulada)actDepAcum,sum(md.d6_depreciacionacumuladaanterior)depreAcumAnt,sum(md.d5_incrementoporcentual)actualizacion_porcentual,sum(md.d4_valoractualizado)valorActualizado,sum(md.d2_valorresidual)valorresidual
                                            from mesdepreciaciones m, mesdepreciaciones_detalle md, activosfijos af
                                            WHERE af.cod_estadoactivofijo=1 and m.codigo = md.cod_mesdepreciaciones and md.cod_activosfijos = af.codigo
                                             and af.cod_unidadorganizacional=$cod_unidadorganizacional and m.mes = $mes2 and m.gestion=$gestion and af.cod_depreciaciones=$cod_depreciaciones_rubros");
                                        // Ejecutamos
                                        // $stmt2->bindParam(':cod_unidadorganizacional',$cod_unidadorganizacional);
                                        $stmt2->execute();
                                        //resultado
                                        
                                        $stmt2->bindColumn('valorresidual', $valorresidual);
                                        $stmt2->bindColumn('actualizacion_porcentual', $actualizacion_porcentual);
                                        $stmt2->bindColumn('valorActualizado', $valorActualizado);
                                        $stmt2->bindColumn('depreAcumAnt', $depreAcumAnt);
                                        
                                        $stmt2->bindColumn('actDepAcum', $actDepAcum);
                                        $stmt2->bindColumn('deprePeriodo', $deprePeriodo);
                                        $stmt2->bindColumn('totalDepreAcumu', $totalDepreAcumu);
                                        $stmt2->bindColumn('valorNeto', $valorNeto);
                                        while ($row = $stmt2->fetch()) {                                             
                                            //totales
                                            $totalValorAnterior+=$valorresidual;
                                            $total_rubro_actualizacion+=$actualizacion_porcentual;
                                            $total_valor_actualizado+=$valorActualizado;
                                            $total_depreAcumAnt+=$depreAcumAnt;
                                            $total_actDepAcum+=$actDepAcum;
                                            $total_deprePeriodo+=$deprePeriodo;
                                            $totalrubro_depreciacion+=$totalDepreAcumu;
                                            $total_valorNeto+=$valorNeto;
                                            ?>
                                            <tr class="">
                                                <td class="small bg-primary text-left text-white"><small><?=$nombreRubros?></small></td>
                                                <td class="small"><small><?=formatNumberDec($valorresidual);?></small></td>
                                                <td class="small bg-success text-white"><small><?=formatNumberDec($actualizacion_porcentual);?></small></td>
                                                <td class="small"><small><?=formatNumberDec($valorActualizado);?></small></td>
                                                <td class="small"><small><?=formatNumberDec($depreAcumAnt); ?></small></td>
                                                <td class="small bg-success text-white"><small><?=formatNumberDec($actDepAcum); ?></small></td>
                                                <td class="small bg-success text-white"><small><?=formatNumberDec($deprePeriodo); ?></small></td>
                                                <td class="small"><small><?=formatNumberDec($totalDepreAcumu); ?></small></td>
                                                <td class="small"><small><?=formatNumberDec($valorNeto); ?></small></td>
                                                </tr>
                                            <?php 
                                        }
                                        
                                    }
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr class="bg-dark text-white">
                                <th colspan="1">Total :</th>
                                <td class="small"><?=formatNumberDec($totalValorAnterior); ?></td>
                                <td class="bg-secondary text-white small"><?=formatNumberDec($total_rubro_actualizacion); ?></td>
                                <td class="small"><?=formatNumberDec($total_valor_actualizado); ?></td>
                                <td class="small"><?=formatNumberDec($total_depreAcumAnt); ?></td>
                                <td class="bg-secondary text-white small"><?=formatNumberDec($total_actDepAcum); ?></td>
                                <td class="bg-secondary text-white small"><?=formatNumberDec($total_deprePeriodo); ?></td>
                                <td class="small"><?=formatNumberDec($totalrubro_depreciacion); ?></td>
                                <td class="small"><?=formatNumberDec($total_valorNeto); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>  
        </div>
    </div>

