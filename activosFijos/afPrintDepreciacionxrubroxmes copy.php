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


$stmtG = $dbh->prepare("SELECT * from gestiones WHERE codigo=:codigo");
$stmtG->bindParam(':codigo',$gestion);
$stmtG->execute();
$resultG = $stmtG->fetch();
$gestion = $resultG['nombre'];


$sql="SELECT (select nombre from unidades_organizacionales where codigo=af.cod_unidadorganizacional) as nombre_unidadO,
af.cod_unidadorganizacional as cod_unidadorganizacional
from activosfijos af
where af.cod_unidadorganizacional in ($unidadOrgString)
GROUP BY (nombre_unidadO)";
$stmtUO = $dbh->prepare($sql);
$stmtUO->execute();
$stmtUO->bindColumn('nombre_unidadO', $nombre_unidadO);
$stmtUO->bindColumn('cod_unidadorganizacional', $cod_unidadorganizacional);
$sumrubro_depreciacion = 0;
$sumrubro_actualizacion=0;
$sumrubro_actDepreciacionAcum=0;
$sumrubro_depreciacionPeriodo=0;

$sum_valorresidual =0;
$sum_valoractualizado =0;
$sum_depreciacionacumuladaanterior=0;
$sum_valornetobs=0;
$sum_vidarestante=0;
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
                      Depreciación De Activos Fijos Por Rubro Por Mes
                  </h4>

                  <!-- <h4 class="card-title text-center">Reporte De Activos Fijos Por Unidad</h4> -->
                  <h6 class="card-title">
                    Gestion: <?php echo $gestion; ?><br>
                    Mes: <?php echo nameMes($mes2); ?><br>
                  </h6>
                  <h6 class="card-title">Oficinas: <?=$stringUnidades; ?></h6>                                          
                  <h6 class="card-title">Rubros: <?=$stringDepreciaciones?></h6>                
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <?php
                      while ($row = $stmtUO->fetch()) {
                        
                      $stmt2 = $dbh->prepare("SELECT *
                                from mesdepreciaciones m, mesdepreciaciones_detalle md, activosfijos af
                                WHERE af.cod_estadoactivofijo=1 and m.codigo = md.cod_mesdepreciaciones and md.cod_activosfijos = af.codigo
                                 and af.cod_unidadorganizacional=:cod_unidadorganizacional and m.mes = ".$mes2." and m.gestion= ".$gestion);
                        // Ejecutamos
                        $stmt2->bindParam(':cod_unidadorganizacional',$cod_unidadorganizacional);
                        $stmt2->execute();
                        //resultado
                        $stmt2->bindColumn('codigoactivo', $codigoactivo);
                        $stmt2->bindColumn('activo', $activo);


                        $stmt2->bindColumn('mes', $mes3);
                        $stmt2->bindColumn('gestion', $gestion3);
                        $stmt2->bindColumn('ufvinicio', $ufvinicio);
                        $stmt2->bindColumn('ufvfinal', $ufvfinal);
                        //$stmt2->bindColumn('estado', $estado);
                        //$stmt2->bindColumn('codigo1', $codigo1);
                        $stmt2->bindColumn('cod_mesdepreciaciones', $cod_mesdepreciaciones);
                        $stmt2->bindColumn('cod_activosfijos', $cod_activosfijos);
                        $stmt2->bindColumn('d2_valorresidual', $d2_valorresidual);
                        $stmt2->bindColumn('d3_factoractualizacion', $d3_factoractualizacion);
                        $stmt2->bindColumn('d4_valoractualizado', $d4_valoractualizado);
                        $stmt2->bindColumn('d5_incrementoporcentual', $d5_incrementoporcentual);
                        $stmt2->bindColumn('d6_depreciacionacumuladaanterior', $d6_depreciacionacumuladaanterior);
                        $stmt2->bindColumn('d7_incrementodepreciacionacumulada', $d7_incrementodepreciacionacumulada);
                        $stmt2->bindColumn('d8_depreciacionperiodo', $d8_depreciacionperiodo);
                        $stmt2->bindColumn('d9_depreciacionacumuladaactual', $d9_depreciacionacumuladaactual);
                        $stmt2->bindColumn('d10_valornetobs', $d10_valornetobs);
                        $stmt2->bindColumn('d11_vidarestante', $d11_vidarestante);

                        $stmt2->bindColumn('cod_depreciaciones', $nombre_depreciaciones);//rubros
                        $stmt2->bindColumn('cod_unidadorganizacional', $nombre_uo);//unidades organizacionales        
                    ?>

                        <table class="table table-bordered table-condensed" id="tablePaginatorFixed">
                            <thead>
                                <?php  
                                $ultimouo = "";//control
                                $ultimorubro = "";//control
                                $sumrubro_depreciacion = 0;
                                $contador = 0; //control
                                while ($row = $stmt2->fetch()) { 
                                    $contador++;               
                                    if ($ultimorubro != $nombre_depreciaciones) { //crea una fila mas , no hay else... pero ademas crea una nueva fila
                                        //1. mostrar totales del anterior
                                        if ($contador != 1){?>
                                            <tr class="bg-info text-white">
                                                <th colspan="5">Total :</th>
                                                <td class="text-center small"><?php echo formatNumberDec($sumrubro_actualizacion); ?></td>
                                                <td colspan="2">-</td>
                                                <td class="text-center small"><?php echo formatNumberDec($sumrubro_depreciacion); ?></td>             
                                                <td class="text-center small"></td>
                                            </tr><?php    
                                        }
                                        if ($ultimouo != $nombre_uo) {
                                            //mostrar nueva cabecera
                                            $ultimouo = $nombre_uo;
                                            //si cambia, tengo q obligar q cambie rubro... porque se da la paz, edif: cambia tarija y el primer es edif
                                            //ya no sale
                                            $ultimorubro = "";
                                            ?>
                                            <tr class="bg-dark text-white">
                                                <th colspan="11" >Oficina : <?php echo $nombre_unidadO; ?></th>
                                            </tr>
                                            <?php
                                        }

                                        $stmtRubros = $dbh->prepare("SELECT nombre from depreciaciones where codigo=:nombre_depreciaciones");
                                        // Ejecutamos
                                        $stmtRubros->bindParam(':nombre_depreciaciones',$nombre_depreciaciones);
                                        $stmtRubros->execute();
                                        //resultado
                                        $stmtRubros->bindColumn('nombre', $nombreRubros);


                                        //2.mostrar nueva cabecera
                                        $ultimorubro = $nombre_depreciaciones;
                                        $sumrubro_depreciacion = 0;
                                        $sumrubro_actualizacion=0;
                                        $sumrubro_actDepreciacionAcum=0;
                                        $sumrubro_depreciacionPeriodo=0;

                                        $sum_valorresidual =0;
                                        $sum_valoractualizado =0;
                                        $sum_depreciacionacumuladaanterior=0;
                                        $sum_valornetobs=0;
                                        $sum_vidarestante=0;
                                        ?>
                                        <tr class="bg-secondary text-white">
                                            <?php while ($row = $stmtRubros->fetch()) {?>
                                            <th colspan="11">Rubro : <?php echo $nombreRubros; ?></th>
                                            <?php
                                              }?>

                                        </tr>
                                        <tr >
                                            <th class="font-weight-bold">-----</th>
                                            
                                            <th class="font-weight-bold">Valor<br>Anterior</th>
                                            <!--th class="font-weight-bold">Factor Actual.</th-->
                                            <th class="font-weight-bold">Actualizacion</th>
                                            <th class="font-weight-bold">Valor<br>Actualizado</th>
                                            <th class="font-weight-bold">Depreciacion<br>Acumulada Anterior</th>
                                            <th class="font-weight-bold">Actualizacion<br>Depreciacion Acumulada</th>
                                            <th class="font-weight-bold">Depreciacion Periodo</th>
                                            <th class="font-weight-bold">Depreciacion Acumulada</th>
                                            <th class="font-weight-bold">Valor Neto</th>
                                            <th class="font-weight-bold">Vida Util Restante</th>            

                                        </tr>
                                    <?php
                                    }
                                    $sumrubro_depreciacion =  $sumrubro_depreciacion + $d9_depreciacionacumuladaactual;
                                    $sumrubro_actualizacion = $sumrubro_actualizacion + $d5_incrementoporcentual;
                                    $sumrubro_actDepreciacionAcum += $d7_incrementodepreciacionacumulada;
                                    $sumrubro_depreciacionPeriodo += $d8_depreciacionperiodo;

                                    $sum_valorresidual +=$d2_valorresidual;
                                    $sum_valoractualizado +=$d4_valoractualizado;
                                    $sum_depreciacionacumuladaanterior+=$d6_depreciacionacumuladaanterior;
                                    $sum_valornetobs+=$d10_valornetobs;
                                    $sum_vidarestante+=$d11_vidarestante;
                                }
                                    ?>
                            </thead>
                            <tbody>
                                  <!-- el ultimo no sale -->
                                <tr class="bg-dark text-white">
                                    <th colspan="1">Total :</th>
                                    <td class="bg-secondary text-white"><?=formatNumberDec($sum_valorresidual); ?></td>
                                    <td class="text-center small  bg-success text-white"><?=formatNumberDec($sumrubro_actualizacion); ?></td>
                                    <td class="bg-secondary text-white"><?=formatNumberDec($sum_valoractualizado); ?></td>
                                    <td class="bg-secondary text-white"><?=formatNumberDec($sum_depreciacionacumuladaanterior); ?></td>
                                    <td class="text-center small  bg-success text-white"><?=formatNumberDec($sumrubro_actDepreciacionAcum); ?></td>
                                    <td class="text-center small bg-success text-white"><?=formatNumberDec($sumrubro_depreciacionPeriodo); ?></td>
                                    <td class="text-center small bg-success text-white"><?=formatNumberDec($sumrubro_depreciacion); ?></td>
                                    <td class="bg-secondary text-white"><?=formatNumberDec($sum_valornetobs); ?></td>
                                    <td class="bg-secondary text-white"><?=formatNumberDec($sum_vidarestante); ?></td>
                                </tr>
                            </tbody>
                        </table>

                    <?php
                      }
                    ?>
                    

                  </div>
                </div>
              </div>
            </div>
          </div>  
        </div>
    </div>

