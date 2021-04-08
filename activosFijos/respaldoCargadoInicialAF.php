<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';

require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';
require_once  __DIR__.'/../fpdf_html.php';
require_once '../layouts/bodylogin2.php';
$dbh = new Conexion();

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
                      Detalle Cargado Inicial Gestión 2019
                  </h4>
                  <h6 class="card-title">
                    Gestion: 2019 - Mes: 12 <br>
                  </h6>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-bordered table-condensed" id="tablePaginatorFixed">
                        <tbody>
                            <tr class="bg-info text-white">
                                <th class=" small bg-primary ">Rubro</th>
                                <th class=" small bg-primary ">Oficina</th>
                                <th class=" small bg-primary ">Codigo</th>
                                <th class=" small bg-primary ">Nombre</th>
                                <th class=" small bg-primary ">Fecha Alta</th>
                                <th class=" small bg-primary font-weight-bold">Valor Inicial</th>
                                <th class=" small bg-primary font-weight-bold">Depreciacion Acumulada</th>
                                <th class=" small bg-primary font-weight-bold">Valor Residual</th>                                    
                            </tr>
                            <?php
                                $sql="SELECT a.codigo, a.activo, 
                                (select d.nombre from depreciaciones d where d.codigo=a.cod_depreciaciones)as rubro, a.fechalta, a.fecha_reevaluo, 
                                (select u.abreviatura from unidades_organizacionales u where u.codigo=a.cod_unidadorganizacional)as oficina, a.valorinicial, a.depreciacionacumulada, a.valorresidual from activosfijos a where a.cod_proy_financiacion=0 and a.fechalta<='2019-12-31' order by rubro, oficina, fechalta, activo";
                                $stmt_rubro = $dbh->prepare($sql);
                                $stmt_rubro->execute();
                                $totalValorInicial=0;
                                $totalDepreciacionAcum=0;
                                $totalResidual=0;
                                while ($row = $stmt_rubro->fetch(PDO::FETCH_ASSOC)) {
                                    $codigoActivo=$row['codigo'];
                                    $nombreActivo=$row['activo'];
                                    $nombreRubro=$row['rubro'];
                                    $fechaAlta=$row['fechalta'];
                                    $nombreOficina=$row['oficina'];
                                    $valorInicial=$row['valorinicial'];
                                    $valorDepreciacionAcum=$row['depreciacionacumulada'];
                                    $valorresidual=$row['valorresidual'];

                                    $totalValorInicial+=$valorInicial;
                                    $totalDepreciacionAcum+=$valorDepreciacionAcum;
                                    $totalResidual+=$valorresidual;

                                ?>
                                <tr class="">
                                    <td class="small text-left"><?=$nombreRubro?></td>
                                    <td class="small text-left"><?=$nombreOficina?></td>
                                    <td class="small text-left"><?=$codigoActivo?></td>
                                    <td class="small text-left"><small><?=$nombreActivo?></small></td>
                                    <td class="small text-left"><?=$fechaAlta?></td>
                                    <td class="small bg-success text-white"><small><?=formatNumberDec($valorInicial);?></small></td>
                                    <td class="small bg-success text-white"><small><?=formatNumberDec($valorDepreciacionAcum);?></small></td>
                                    <td class="small bg-success text-white"><small><?=formatNumberDec($valorresidual);?></small></td>
                                </tr>
                                <?php 
                                }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr class="bg-dark text-white">
                                <th colspan="5">Total :</th>
                                <td class="small"><?=formatNumberDec($totalValorInicial); ?></td>
                                <td class="small"><?=formatNumberDec($totalDepreciacionAcum); ?></td>
                                <td class="small"><?=formatNumberDec($totalResidual); ?></td>
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

