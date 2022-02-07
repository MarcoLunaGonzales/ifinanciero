<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';
require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';

require_once '../layouts/bodylogin2.php';

$dbh = new Conexion();
$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

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


//listamos las oficinas


$sql="SELECT (select uo.abreviatura from unidades_organizacionales uo where uo.codigo=af.cod_unidadorganizacional)oficina,(select d.abreviatura from  depreciaciones d where d.codigo=af.cod_depreciaciones)rubro,af.codigo,af.activo,md.d2_valorresidual,md.d5_incrementoporcentual,md.d4_valoractualizado,md.d6_depreciacionacumuladaanterior,md.d7_incrementodepreciacionacumulada,md.d8_depreciacionperiodo,md.d9_depreciacionacumuladaactual,md.d10_valornetobs,md.d11_vidarestante
from mesdepreciaciones m, mesdepreciaciones_detalle md, activosfijos af
WHERE  m.codigo = md.cod_mesdepreciaciones and md.cod_activosfijos = af.codigo
 and af.cod_unidadorganizacional in ($unidadOrgString) and af.cod_depreciaciones in ($depreciacionesString)  and m.mes=$mes2 and m.gestion=$gestion ORDER BY 1,2";
 // echo $sql;
 $stmt2 = $dbh->prepare($sql);
// Ejecutamos                                        
$stmt2->execute();
//resultado

$stmt2->bindColumn('oficina', $oficina_x);
$stmt2->bindColumn('rubro', $rubro_x);
$stmt2->bindColumn('codigo', $codigoactivo);
$stmt2->bindColumn('activo', $activo);
$stmt2->bindColumn('d2_valorresidual', $d2_valorresidual);
// $stmt2->bindColumn('d3_factoractualizacion', $d3_factoractualizacion);
$stmt2->bindColumn('d4_valoractualizado', $d4_valoractualizado);
$stmt2->bindColumn('d5_incrementoporcentual', $d5_incrementoporcentual);
$stmt2->bindColumn('d6_depreciacionacumuladaanterior', $d6_depreciacionacumuladaanterior);
$stmt2->bindColumn('d7_incrementodepreciacionacumulada', $d7_incrementodepreciacionacumulada);
$stmt2->bindColumn('d8_depreciacionperiodo', $d8_depreciacionperiodo);
$stmt2->bindColumn('d9_depreciacionacumuladaactual', $d9_depreciacionacumuladaactual);
$stmt2->bindColumn('d10_valornetobs', $d10_valornetobs);
$stmt2->bindColumn('d11_vidarestante', $d11_vidarestante);

?>

<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card" >
                <div class="card-header <?=$colorCard;?> card-header-icon">
                    <h4 class="card-title"> 
                        <img  class="card-img-top"  src="../marca.png" style="width:100%; max-width:250px;">
                        Depreciación De Activos Fijos Detallada
                    </h4>
                    <h6 class="card-title">Mes: <?php echo nameMes($mes2); ?><br>
                        Gestion: <?php echo $gestion; ?>
                    </h6> 
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-bordered table-condensed" id="tablePaginatorFixed">
                        <thead>
                            <tr >
                                <th ><small>Oficina</small></th>
                                <th ><small>Rubro</small></th>
                                <th ><small>Cod.<br>Activo</small></th>
                                <th ><small>Activo</small></th>
                                <th ><small>Valor<br>Anterior</small></th>                                
                                <th ><small>Actualización</small></th>
                                <th ><small>Valor<br>Actualizado</small></th>
                                <th ><small>Depreciación<br>Acumulada Anterior</small></th>
                                <th ><small>Actualización<br>Depreciación Acumulada</small></th>
                                <th ><small>Depreciación Periodo</small></th>
                                <th ><small>Depreciacion Acumulada</small></th>
                                <th ><small>Valor Neto</small></th>
                                <th ><small>Vida útil Restante</small></th>            
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                //$nombre_depreciaciones=nameDepreciacion($cod_depreciaciones_rubros);
                                $sumrubro_depreciacion = 0;
                                $sumrubro_actualizacion=0;
                                $sumrubro_actDepreciacionAcum=0;
                                $sumrubro_depreciacionPeriodo=0;
                                
                                $sumRubroValorAnterior=0;
                                $sumRubroActualizacion=0;
                                $sumRubroValorActualizado=0;
                                $sumRubroDepreciacionAcumulada=0;
                                $sumRubroActDepreciacionAcumulada=0;
                                $sumRubroValorNeto=0;
                                while ($row = $stmt2->fetch()) {                                             
                                    //totales
                                    $sumrubro_depreciacion += $d9_depreciacionacumuladaactual;
                                    $sumrubro_actualizacion +=  $d5_incrementoporcentual;
                                    $sumrubro_actDepreciacionAcum += $d7_incrementodepreciacionacumulada;
                                    $sumrubro_depreciacionPeriodo += $d8_depreciacionperiodo;
                                    $sumRubroValorAnterior+=$d2_valorresidual;
                                    $sumRubroActualizacion+=$d5_incrementoporcentual;
                                    $sumRubroValorActualizado+=$d4_valoractualizado;
                                    $sumRubroDepreciacionAcumulada+=$d6_depreciacionacumuladaanterior;
                                    $sumRubroActDepreciacionAcumulada+=$d7_incrementodepreciacionacumulada;
                                    $sumRubroValorNeto+=$d10_valornetobs;
                                    ?>
                                   <tr>
                                        <td class="text-center small"><small><?=$oficina_x;?></td>
                                        <td class="text-center small"><small><?=$rubro_x;?></td>

                                        <td class="text-center small"><small><?=$codigoactivo;?></td>
                                        <td class="text-left small"><small><?=$activo; ?></td>
                                        <td class="text-center small"><small><?=formatNumberDec($d2_valorresidual); ?></small></td>
                                        <td class="text-center small bg-success"><small><?=formatNumberDec($d5_incrementoporcentual); ?></small></td>
                                        <td class="text-center small"><small><?=formatNumberDec($d4_valoractualizado); ?></small></td>
                                        <td class="text-center small"><small><?=formatNumberDec($d6_depreciacionacumuladaanterior); ?></small></td>
                                        <td class="text-center small bg-success"><small><?=formatNumberDec($d7_incrementodepreciacionacumulada);?></small></td>
                                        <td class="text-center small bg-success"><small><?=formatNumberDec($d8_depreciacionperiodo); ?></small></td>
                                        <td class="text-center small"><small><?=formatNumberDec($d9_depreciacionacumuladaactual); ?></small></td>
                                        <td class="text-center small"><small><?=formatNumberDec($d10_valornetobs); ?></small></td>
                                        <td class="text-center small"><small><?=$d11_vidarestante; ?></small></td>
                                    </tr>
                                    <?php 
                                }?>
                                <?php
                            ?>
                        </tbody>
                        <tfoot>
                            <tr class="bg-info text-white">
                                <th colspan="4">Total :</th>
                                <td class="text-center small"><?=formatNumberDec($sumRubroValorAnterior); ?></td>
                                <td class="text-center small bg-success"><?=formatNumberDec($sumRubroActualizacion); ?></td>
                                <td class="text-center small "><?=formatNumberDec($sumRubroValorActualizado); ?></td>
                                <td class="text-center small"><?=formatNumberDec($sumRubroDepreciacionAcumulada); ?></td>
                                <td class="text-center small bg-success"><?=formatNumberDec($sumRubroActDepreciacionAcumulada); ?></td>
                                <td class="text-center small bg-success"><?=formatNumberDec($sumrubro_depreciacionPeriodo); ?></td>
                                <td class="text-center small "><?=formatNumberDec($sumrubro_depreciacion); ?></td>
                                <td class="text-center small"><?=formatNumberDec($sumRubroValorNeto); ?></td>
                                <td>-</td>
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