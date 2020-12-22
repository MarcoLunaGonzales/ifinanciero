<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';
require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';

require_once '../layouts/bodylogin2.php';

//require_once 'styles.php';
//require_once 'configModule.php';


$dbh = new Conexion();
$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

//RECIBIMOS LAS VARIABLES

$id = $_GET["codigo"];
$stmt = $dbh->prepare("select * from mesdepreciaciones WHERE codigo=:codigo");
$stmt->bindParam(':codigo',$id);
$stmt->execute();
$result2 = $stmt->fetch();
$mes = $result2['mes'];
$gestion = $result2['gestion'];


$sql="SELECT af.nombre_uo as nombre_unidadO
    from mesdepreciaciones m, mesdepreciaciones_detalle md, v_activosfijos af
    WHERE m.codigo = md.cod_mesdepreciaciones and md.cod_activosfijos = af.codigo
    and m.codigo =".$id." GROUP BY (nombre_unidadO) order by nombre_uo";

//echo $sql;

$stmt1 = $dbh->prepare($sql);
$stmt1->execute();
$stmt1->bindColumn('nombre_unidadO', $nombre_unidadO);

?>

<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card" >
                <div class="card-header <?=$colorCard;?> card-header-icon">
                    <h4 class="card-title"> 
                        <img  class="card-img-top"  src="../marca.png" style="width:100%; max-width:250px;">
                        Depreciación De Activos Fijos Por Mes Y Gestión
                    </h4>
                    <h6 class="card-title">Mes: <?php echo nameMes($mes); ?><br>
                        Gestion: <?php echo $gestion; ?>
                    </h6> 
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <?php
                    while ($row = $stmt1->fetch()) {        
                    ?>
                    <?php
                        //echo $nombre_unidadO;
                        //$gestion2 = $_POST["gestion"];
                        $sqlActivos="SELECT * 
                                from mesdepreciaciones m, mesdepreciaciones_detalle md, v_activosfijos af
                                WHERE m.codigo = md.cod_mesdepreciaciones and md.cod_activosfijos = af.codigo
                                 and af.nombre_uo=:nombre_unidadO and m.codigo = ".$id." order by af.nombre_depreciaciones, af.activo";
                        //echo $sqlActivos;
                        $stmt2 = $dbh->prepare($sqlActivos);//nombre_depreciaciones es rubro
                        // Ejecutamos
                        $stmt2->bindParam(':nombre_unidadO',$nombre_unidadO);
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

                        $stmt2->bindColumn('nombre_depreciaciones', $nombre_depreciaciones);//rubros
                        $stmt2->bindColumn('nombre_uo2', $nombre_uo);//unidades organizacionales
                    ?>
                    <table class="table table-bordered table-condensed" >
                        <thead>
                        <?php  
                                $ultimouo = "";//control
                                $ultimorubro = "";//control
                                $sumrubro_depreciacion = 0;

                                $sumRubroValorAnterior=0;
                                $sumRubroActualizacion=0;
                                $sumRubroValorActualizado=0;
                                
                                $sumRubroDepreciacionAcumulada=0;
                                $sumRubroActDepreciacionAcumulada=0;
                                $sumRubroValorNeto=0;
    

                                $contador = 0; //control
                                while ($row = $stmt2->fetch()) { //?>    
                            
                                <?php
                                    $contador++;               
                                    if ($ultimorubro != $nombre_depreciaciones) { //crea una fila mas , no hay else... pero ademas crea una nueva fila
                                        //1. mostrar totales del anterior
                                        if ($contador != 1)
                                            {?>
                                            <tr class="bg-info text-white">
                                                <th colspan="2">Total :</th>
                                                <td class="text-center small"><?=formatNumberDec($sumRubroValorAnterior); ?></td>
                                                <td class="text-center small bg-success"><?=formatNumberDec($sumRubroActualizacion); ?></td>
                                                <td class="text-center small"><?=formatNumberDec($sumRubroValorActualizado); ?></td>
                                    <td class="text-center small"><?=formatNumberDec($sumRubroDepreciacionAcumulada); ?></td>
                                    <td class="text-center small bg-success"><?=formatNumberDec($sumRubroActDepreciacionAcumulada); ?></td>
                                                <td class="text-center small bg-success"><?=formatNumberDec($sumrubro_depreciacionPeriodo); ?></td>
                                                <td class="text-center small"><?=formatNumberDec($sumrubro_depreciacion); ?></td>
                                                <td class="text-center small"><?=formatNumberDec($sumRubroValorNeto); ?></td>
                                                <td>-</td>
                                            </tr>
                                               <?php    
                                        }
                                        if ($ultimouo != $nombre_uo) {
                                            //mostrar nueva cabecera
                                            $ultimouo = $nombre_uo;
                                            //si cambia, tengo q obligar q cambie rubro... porque se da la paz, edif: cambia tarija y el primer es edif
                                            //ya no sale
                                            $ultimorubro = "";
                                            ?>
                                            <tr class="bg-dark text-white">
                                                <th colspan="11" >Oficina : <?php echo $ultimouo; ?></th>
                                            </tr>
                                            <?php
                                        }
                                        //2.mostrar nueva cabecera
                                        $ultimorubro = $nombre_depreciaciones;
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
        
                                        ?>
                                        <tr class="bg-secondary text-white">
                                            <th colspan="11">Rubro : <?php echo $nombre_depreciaciones; ?></th>
                                        </tr>
                                        <tr >
                                            <th ><small>Cod.<br>Activo</small></th>
                                            <th ><small>Activo</small></th>
                                            <th ><small>Valor<br>Anterior</small></th>
                                            <!--th >Factor Actual.</th-->
                                            <th ><small>Actualización</small></th>
                                            <th ><small>Valor<br>Actualizado</small></th>
                                            <th ><small>Depreciación<br>Acumulada Anterior</small></th>
                                            <th ><small>Actualización<br>Depreciación Acumulada</small></th>
                                            <th ><small>Depreciación Periodo</small></th>
                                            <th ><small>Depreciacion Acumulada</small></th>
                                            <th ><small>Valor Neto</small></th>
                                            <th ><small>Vida útil Restante</small></th>            
                                        </tr>
                                        <?php
                                    }
                                    $sumrubro_depreciacion =  $sumrubro_depreciacion + $d9_depreciacionacumuladaactual;
                                    $sumrubro_actualizacion = $sumrubro_actualizacion + $d5_incrementoporcentual;
                                    $sumrubro_actDepreciacionAcum += $d7_incrementodepreciacionacumulada;
                                    $sumrubro_depreciacionPeriodo += $d8_depreciacionperiodo;
                                    $sumRubroValorAnterior+=$d2_valorresidual;
                                    $sumRubroActualizacion+=$d5_incrementoporcentual;
                                    $sumRubroValorActualizado+=$d4_valoractualizado;

                                    $sumRubroDepreciacionAcumulada+=$d6_depreciacionacumuladaanterior;
                                    $sumRubroActDepreciacionAcumulada+=$d7_incrementodepreciacionacumulada;
                                    $sumRubroValorNeto+=$d10_valornetobs;

                                    ?>
                                </thead>
                                <tbody>
                                <tr>
                                    <td class="text-center small"><small><?=$codigoactivo;?></td>
                                    <td class="text-left small"><small><?=$activo; ?></td>
                                    <td class="text-center small"><small><?=formatNumberDec($d2_valorresidual); ?></small></td>
                                    <td class="text-center small"><small><?=formatNumberDec($d5_incrementoporcentual); ?></small></td>
                                    <td class="text-center small"><small><?=formatNumberDec($d4_valoractualizado); ?></small></td>
                                    <td class="text-center small"><small><?=formatNumberDec($d6_depreciacionacumuladaanterior); ?></small></td>
                                    <td class="text-center small"><small><?=formatNumberDec($d7_incrementodepreciacionacumulada);?></small></td>
                                    <td class="text-center small"><small><?=formatNumberDec($d8_depreciacionperiodo); ?></small></td>
                                    <td class="text-center small"><small><?=formatNumberDec($d9_depreciacionacumuladaactual); ?></small></td>
                                    <td class="text-center small"><small><?=formatNumberDec($d10_valornetobs); ?></small></td>
                                    <td class="text-center small"><small><?=$d11_vidarestante; ?></small></td>
                                </tr>
                        <?php } ?>
                                  <!-- el ultimo no sale -->
                                <tr class="bg-info text-white">
                                    <th colspan="2">Total :</th>
                                    <td class="text-center small"><?=formatNumberDec($sumRubroValorAnterior); ?></td>
                                    <td class="text-center small bg-success"><?=formatNumberDec($sumRubroActualizacion); ?></td>
                                    <td class="text-center small "><?=formatNumberDec($sumRubroValorActualizado); ?></td>
                                    <td class="text-center small"><?=formatNumberDec($sumRubroDepreciacionAcumulada); ?></td>
                                    <td class="text-center small"><?=formatNumberDec($sumRubroActDepreciacionAcumulada); ?></td>
                                    <td class="text-center small bg-success"><?=formatNumberDec($sumrubro_depreciacionPeriodo); ?></td>
                                    <td class="text-center small bg-success"><?=formatNumberDec($sumrubro_depreciacion); ?></td>
                                    <td class="text-center small"><?=formatNumberDec($sumRubroValorNeto); ?></td>
                                    <td>-</td>
                                </tr>
                            </tbody>
                    </table>

                    <?php } ?>
                  </div>
                </div>
              </div>
            </div>
          </div>  
        </div>
    </div>