<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';
require_once  __DIR__.'/../fpdf_html.php';
require_once '../layouts/bodylogin2.php';
$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$codigo_depreciacion=$_POST["cod_depreciaciones"];


$stmt = $dbh->prepare("SELECT * FROM depreciaciones where codigo =:codigo");
//Ejecutamos;
$stmt->bindParam(':codigo',$codigo_depreciacion);
$stmt->execute();
$result = $stmt->fetch();
$codigo2 = $result['codigo'];
$cod_empresa = $result['cod_empresa'];
$nombre2 = $result['nombre'];
$vida_util = $result['vida_util'];
$cod_estado = $result['cod_estado'];
$cod_cuentacontable = $result['cod_cuentacontable'];

$mes2 = $_POST["mes"];
$gestion2 = $_POST["gestion"];
$stmt2 = $dbh->prepare("select * 
    from mesdepreciaciones m, mesdepreciaciones_detalle md, activosfijos af
    WHERE m.codigo = md.cod_mesdepreciaciones and md.cod_activosfijos = af.codigo 
    and m.mes = :mes and m.gestion = :gestion");
// Ejecutamos
$stmt2->bindParam(':mes',$mes2);
$stmt2->bindParam(':gestion',$gestion2);

$stmt2->execute();
//resultado
$stmt2->bindColumn('codigoactivo', $codigoactivo);
$stmt2->bindColumn('activo', $activo);

$stmt2->bindColumn('mes', $mes);
$stmt2->bindColumn('gestion', $gestion);
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
                    Gestion: <?php echo $_POST["gestion"]; ?><br>
                    Mes: <?php echo $_POST["mes"]; ?><br>
                    Rubro: <?php echo $nombre2; ?>
                  </h6>
                
                </div>
                <div class="card-body">
                  <div class="table-responsive">

                    <table class="table table-condensed" id="tablePaginatorFixed3">
                      <thead class="bg-secondary text-white">
                        <tr >
                          <th class="text-center">-</th>
                          <th class="font-weight-bold">Codigo Activo</th>
                          <th class="font-weight-bold">Valor Residual</th>
                          <th class="font-weight-bold">Factor Actual.</th>
                          <th class="font-weight-bold">Valor Actual.</th>
                          <th class="font-weight-bold">Inc. %</th>
                          <th class="font-weight-bold">Depr Acm. Ant.</th>
                          <th class="font-weight-bold">Incr. Depr. Acum.</th>
                          <th class="font-weight-bold">Depr. Acum. Act.</th>
                          <th class="font-weight-bold">V. Neto Bs</th>
                          <th class="font-weight-bold">Rest. Meses</th>
                        </tr>
                      </thead>
                      <tbody>

 <?php  
            $contador = 0;
            while ($row = $stmt2->fetch()) { $contador++;   ?>
            <?php } ?>

                        <?php  
                          $contador = 0;
                          while ($row = $stmt2->fetch()) { $contador++;   ?>
                        <tr>
                          <td class="text-center small"><?=$contador;?></td>
                          <td class="text-center small"><?=$codigoactivo; ?></td>
                          <td class="text-center small"><?= $d2_valorresidual; ?></td>
                          <td class="text-left small"><?= $d3_factoractualizacion; ?></td>
                          <td class="text-left small"><?= $d4_valoractualizado; ?></td>
                          <td class="text-center small"><?= $d5_incrementoporcentual; ?></td>
                          <td class="text-left small"><?= $d6_depreciacionacumuladaanterior; ?></td>
                          <td class="text-left small"><?= $d7_incrementodepreciacionacumulada; ?></td>
                          <td class="text-left small"><?= $d9_depreciacionacumuladaactual; ?></td>
                          <td class="text-left small"><?= $d10_valornetobs; ?></td>
                          <td class="text-left small"><?= $d11_vidarestante; ?></td>
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
<!-- <body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
        <tr  class="heading">
                <td colspan="11"  style="text-align:center">DEPRECIACION DE ACTIVOS FIJOS POR RUBRO POR MES</td>
            </tr>
            <tr class="top">
                <td colspan="11">
                    <table>
                        <tr>
                            <td class="title">
                                <img src="../marca.png" style="width:100%; max-width:300px;">
                            </td>
                            
                            <td>
                                Mes: <?php echo $_POST["mes"]; ?><br>
                                Gestion: <?php echo $_POST["gestion"]; ?><br>
                                Rubro: <?php echo $nombre2; ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
                     
            <tr class="heading">
                <td>Codigo Activo</td>
                <td>Activo</td>
                <td>Valor Residual</td>
                <td>Factor Actual.</td>
                <td>Valor Actual.</td>
                <td>Inc. %</td>
                <td>Depr Acm. Ant.</td>
                <td>Incr. Depr. Acum.</td>
                <td>Depr. Acum. Act.</td>
                <td>V. Neto Bs</td>
                <td>Rest. Meses</td>
                
            </tr>
            <?php  
            $contador = 0;
            while ($row = $stmt2->fetch()) { $contador++;   ?>
            
            <tr class="item">
                <td><?php echo  $codigoactivo; ?></td>
                <td><?php echo  $activo; ?></td>
                <td><?php echo  $d2_valorresidual; ?></td>
                <td><?php echo  $d3_factoractualizacion; ?></td>
                <td><?php echo  $d4_valoractualizado; ?></td>
                <td><?php echo  $d5_incrementoporcentual; ?></td>
                <td><?php echo  $d6_depreciacionacumuladaanterior; ?></td>
                <td><?php echo  $d7_incrementodepreciacionacumulada; ?></td>
                <td><?php echo  $d9_depreciacionacumuladaactual; ?></td>
                <td><?php echo  $d10_valornetobs; ?></td>
                <td><?php echo  $d11_vidarestante; ?></td>
            </tr>
            <?php } ?>
            
          
            
            <tr class="total">
                <td colspan="4">
                    Cantidad de Items: <?php echo $contador; ?>
                </td>
            </tr>
        </table>
    </div>
</body>
 -->
