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

$stringUnidades="";
foreach ($unidadOrganizacional as $valor ) {    
    $stringUnidades.=" ".abrevUnidad($valor)." ";
}
$stringDepreciaciones="";
foreach ($cod_depreciaciones as $valor ) {    
    $stringDepreciaciones.=" ".abrevDepreciacion($valor)." ";
}
$gestion=nameGestion($gestion);

    
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
                      Depreciación De Activos Fijos Por Rubro Por Mes Totales
                  </h4>
                  <h6 class="card-title">
                    Gestión: <?php echo $gestion; ?> - Mes: <?php echo nameMes($mes2);?><br>
                    Oficinas: <?=$stringUnidades; ?><br>
                    Rubros: <?=$stringDepreciaciones; ?>
                  </h6>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-bordered table-condensed" id="tablePaginatorFixed">
                        <tbody>                                                        
                            <tr class="bg-info text-white">
                                <th class=" small bg-primary ">Rubro</th>
                                <?php
                                $sql_UO="SELECT (select abreviatura from unidades_organizacionales where codigo=af.cod_unidadorganizacional) as nombre_unidadO,af.cod_unidadorganizacional as cod_unidadorganizacional
                                    from activosfijos af
                                    where af.cod_unidadorganizacional in ($unidadOrgString)
                                    GROUP BY (nombre_unidadO)";
                                $stmt_UO = $dbh->prepare($sql_UO);
                                $stmt_UO->execute();
                                $stmt_UO->bindColumn('nombre_unidadO', $nombre_unidadO);
                                $i=0;
                                $stmt_UO->bindColumn('cod_unidadorganizacional', $cod_unidadorganizacional);
                                while ($row = $stmt_UO->fetch()) { 
                                    //iniciamos el contador de totales                                    
                                    $TOTAL_V[$i]=0;
                                    $i++;
                                    ?>
                                    <th class=" small bg-primary font-weight-bold"><?=$nombre_unidadO?></th><?php 
                                } ?>
                            </tr>
                            <?php                            
                                $sql="SELECT af.cod_depreciaciones from mesdepreciaciones m, mesdepreciaciones_detalle md, activosfijos af WHERE af.cod_estadoactivofijo=1 and m.codigo = md.cod_mesdepreciaciones and md.cod_activosfijos = af.codigo
                                             and af.cod_unidadorganizacional in ($unidadOrgString) and m.mes = $mes2 and m.gestion=$gestion and af.cod_depreciaciones in ($depreciacionesString) GROUP BY af.cod_depreciaciones";
                                $stmt_rubro = $dbh->prepare($sql);
                                $stmt_rubro->execute();
                                $stmt_rubro->bindColumn('cod_depreciaciones', $cod_depreciaciones_rubros);
                                while ($row = $stmt_rubro->fetch()) {
                                        $i=0;
                                        $nombreRubros=nameDepreciacion($cod_depreciaciones_rubros);
                                        ?>
                                        <tr>
                                            <td class="small bg-primary text-left text-white"><small><?=$nombreRubros?></small></td>
                                            <?php
                                            $stmt_UO_2 = $dbh->prepare($sql_UO);
                                            $stmt_UO_2->execute();
                                            $stmt_UO_2->bindColumn('nombre_unidadO', $nombre_unidadO);
                                            $stmt_UO_2->bindColumn('cod_unidadorganizacional', $cod_unidadorganizacional);                  
                                            while ($row = $stmt_UO_2->fetch()) { 
                                                $stmt2 = $dbh->prepare("SELECT sum(md.d9_depreciacionacumuladaactual)totalDepreAcumu
                                                from mesdepreciaciones m, mesdepreciaciones_detalle md, activosfijos af
                                                WHERE af.cod_estadoactivofijo=1 and m.codigo = md.cod_mesdepreciaciones and md.cod_activosfijos = af.codigo
                                                 and af.cod_unidadorganizacional=$cod_unidadorganizacional and m.mes = $mes2 and m.gestion=$gestion and af.cod_depreciaciones=$cod_depreciaciones_rubros");
                                                $stmt2->execute();
                                                //resultado                                                
                                                $stmt2->bindColumn('totalDepreAcumu', $totalDepreAcumu);
                                                while ($row = $stmt2->fetch()) {
                                                    $TOTAL_V[$i]=$TOTAL_V[$i]+$totalDepreAcumu;             
                                                    $i++;
                                                    ?>
                                                    <th class=" small"><?=formatNumberDec($totalDepreAcumu)?></th><?php 
                                                }
                                            } ?>
                                        </tr>
                                        <?php 
                                }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr class="bg-dark text-white">
                                <th >TOTAL : </th>
                                <?php
                                $sql_UO="SELECT (select abreviatura from unidades_organizacionales where codigo=af.cod_unidadorganizacional) as nombre_unidadO,af.cod_unidadorganizacional as cod_unidadorganizacional
                                    from activosfijos af
                                    where af.cod_unidadorganizacional in ($unidadOrgString)
                                    GROUP BY (nombre_unidadO)";
                                $stmt_UO = $dbh->prepare($sql_UO);
                                $stmt_UO->execute();
                                $stmt_UO->bindColumn('nombre_unidadO', $nombre_unidadO);
                                $i=0;
                                $stmt_UO->bindColumn('cod_unidadorganizacional', $cod_unidadorganizacional);
                                while ($row = $stmt_UO->fetch()) { 
                                    ?>
                                    <th class=" small"><?=formatNumberDec($TOTAL_V[$i])?></th><?php
                                    $i++; 
                                } 
                                ?>
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

