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
                                Gestion: <?php echo $gestion; ?><br>
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
                        $stmt2 = $dbh->prepare("SELECT * 
                                from mesdepreciaciones m, mesdepreciaciones_detalle md, v_activosfijos af
                                WHERE m.codigo = md.cod_mesdepreciaciones and md.cod_activosfijos = af.codigo
                                 and af.nombre_uo=:nombre_unidadO and m.codigo = ".$id." order by af.activo
                        ");//nombre_depreciaciones es rubro
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
                    <table class="table table-bordered table-condensed" id="tablePaginatorFixed">
                        <thead>
                        <?php  
                                $ultimouo = "";//control
                                $ultimorubro = "";//control
                                $sumrubro_depreciacion = 0;
                                $contador = 0; //control
                                while ($row = $stmt2->fetch()) { //?>    
                            
                                <?php
                                    $contador++;               
                                    if ($ultimorubro != $nombre_depreciaciones) { //crea una fila mas , no hay else... pero ademas crea una nueva fila
                                        //1. mostrar totales del anterior
                                        if ($contador != 1)
                                            {?>
                                            <tr class="bg-info text-white">
                                                <th colspan="5">Total :</th>
                                                <td class="text-center small"><?php echo formatNumberDec($sumrubro_actualizacion); ?></td>
                                                <td colspan="2">-</td>
                                                <td class="text-center small"><?php echo formatNumberDec($sumrubro_depreciacion); ?></td>                                                
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
                                                <th colspan="11" >Unidad Organizacional : <?php echo $ultimouo; ?></th>
                                            </tr>
                                            <?php
                                        }
                                        //2.mostrar nueva cabecera
                                        $ultimorubro = $nombre_depreciaciones;
                                        $sumrubro_depreciacion = 0;
                                        $sumrubro_actualizacion=0;
                                        $sumrubro_actDepreciacionAcum=0;
                                        $sumrubro_depreciacionPeriodo=0;
                                        ?>
                                        <tr class="bg-secondary text-white">
                                            <th colspan="11">Rubro : <?php echo $nombre_depreciaciones; ?></th>
                                        </tr>
                                        <tr >
                                            <th class="font-weight-bold">Codigo Activo</th>
                                            <th class="font-weight-bold">Activo</th>
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
                                    ?>
                                </thead>
                                <tbody>
                                <tr>
                                    <td class="text-center small"><?php echo  $codigoactivo; ?></td>
                                    <td class="text-left small"><?php echo  $activo; ?></td>
                                    <td class="text-center small"><?=formatNumberDec($d2_valorresidual); ?></td>
                                    <!--td class="text-center small"><?php echo  $d3_factoractualizacion; ?></td-->
                                    <td class="text-center small"><?=formatNumberDec($d5_incrementoporcentual); ?></td>
                                    <td class="text-center small"><?=formatNumberDec($d4_valoractualizado); ?></td>
                                    <td class="text-center small"><?=formatNumberDec($d6_depreciacionacumuladaanterior); ?></td>
                                    <td class="text-center small"><?=formatNumberDec($d7_incrementodepreciacionacumulada); ?></td>
                                    <td class="text-center small"><?=formatNumberDec($d8_depreciacionperiodo); ?></td>
                                    <td class="text-center small"><?=formatNumberDec($d9_depreciacionacumuladaactual); ?></td>
                                    <td class="text-center small"><?=formatNumberDec($d10_valornetobs); ?></td>
                                    <td class="text-center small"><?=formatNumberDec($d11_vidarestante); ?></td>
                                </tr>
                        <?php } ?>
                                  <!-- el ultimo no sale -->
                                <tr class="bg-info text-white">
                                    <th colspan="3">Total :</th>
                                    <td class="text-center small"><?=formatNumberDec($sumrubro_actualizacion); ?></td>
                                    <td colspan="2">-</td>
                                    <td class="text-center small"><?=formatNumberDec($sumrubro_actDepreciacionAcum); ?></td>
                                    <td class="text-center small"><?=formatNumberDec($sumrubro_depreciacionPeriodo); ?></td>
                                    <td class="text-center small"><?=formatNumberDec($sumrubro_depreciacion); ?></td>
                                    <td colspan="2">-</td>
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