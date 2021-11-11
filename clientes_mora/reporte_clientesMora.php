<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';
require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';
require_once '../layouts/bodylogin2.php';

$dbh = new Conexion();
$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();



$sql="SELECT cm.codigo,cm.fecha,DATE_FORMAT(cm.fecha,'%d/%m/%Y')as fecha_x,cm.dias_mora,cm.monto_mora,ca.nombre,ca.glosa from clientes_mora cm join cuentas_auxiliares ca on cm.cod_cuentaauxiliar=ca.codigo where cm.cod_estado=1 order by ca.nombre,cm.fecha";
 // echo $sql;
$stmt2 = $dbh->prepare($sql);
$stmt2->execute();
//resultado
$stmt2->bindColumn('codigo', $codigo_x);
$stmt2->bindColumn('fecha', $fecha_x);
$stmt2->bindColumn('fecha_x', $fecha_formateada);
$stmt2->bindColumn('dias_mora', $dias_mora_x);
$stmt2->bindColumn('monto_mora', $monto_mora_x);
$stmt2->bindColumn('nombre', $nombre_x);
$stmt2->bindColumn('glosa', $glosa_x);
?>

<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card" >
                <div class="card-header <?=$colorCard;?> card-header-icon">
                    <h4 class="card-title"> 
                        <img  class="card-img-top"  src="../marca.png" style="width:100%; max-width:150px;">
                        <b>Clientes Mora</b>
                    </h4>                    
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-bordered table-condensed table-striped" id="tablePaginatorFixed">
                        <thead>
                            <tr>
                                <th width="5%"><small>-</small></th>
                                <th ><small>Cliente</small></th>
                                <th ><small>Glosa</small></th>
                                <th ><small>Fecha</small></th>
                                <th ><small>Días</small></th>
                                <th ><small>Monto</small></th>                                
                            </tr>   
                        </thead>
                        <tbody>
                            <?php
                                $index=0;
                                $total_mora=0;
                                $fecha_actual=date('Y-m-d');
                                while ($row = $stmt2->fetch()) {  
                                    $date1 = new DateTime($fecha_x);
                                    $date2 = new DateTime($fecha_actual);
                                    $diff = $date1->diff($date2);        
                                    $dias_mora=$diff->days;
                                    switch ($dias_mora) {
                                        case $dias_mora>30 and $dias_mora<=60:
                                            $fondo_tr="style='background:#fadbd8;'";
                                            break;
                                        case $dias_mora>60 and $dias_mora<=90:
                                            $fondo_tr="style='background:#f5b7b1;'";
                                            break;
                                        case $dias_mora>90:
                                            $fondo_tr="style='background:#f1948a;'";
                                            break;                                        
                                    }

                                    $index++;
                                    $total_mora+=$monto_mora_x;

                                    ?>
                                    <tr <?=$fondo_tr?>>
                                        <td class="text-center small"><?=$index;?>&nbsp;&nbsp;<a href='#' onclick="silenciarClientesMora('<?=$codigo_x?>','<?=$index?>');return false;" class="btn btn-danger" style="padding-top:4px;padding-left:0;padding-right:0;font-size:10px;width:23px;height:23px;">
                                          <i class="material-icons" title="Desactivar Clientes Mora" style="color:black">notifications_off</i>
                                        </a></td>
                                        <td class="text-left small"><?=$nombre_x;?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b><a href="#" class="et-cart-info-<?=$index?>" style="color:#3498db;font-size: 14px;"><span></span></a></b></td>
                                        <td class="text-center small"><?=$glosa_x;?></td>
                                        <td class="text-center small"><?=$fecha_formateada;?></td>
                                        <td class="text-right small"><?=$dias_mora_x; ?></td>
                                        <td class="text-right small"><?=formatNumberDec($monto_mora_x); ?></td>
                                    </tr>
                                    <?php 
                                }?>
                                <?php
                            ?>
                        </tbody>
                        <tfoot>
                            <tr class="bg-dark text-white">
                                <th ></th>
                                <th colspan="4">Total :</th>
                                <td class="text-right small"><?=formatNumberDec($total_mora); ?></td>
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