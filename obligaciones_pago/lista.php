<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';
$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();

// Preparamos
$stmt = $dbh->prepare("SELECT sr.*,es.glosa from pagos_proveedores sr join comprobantes es on sr.cod_comprobante=es.codigo order by sr.codigo");
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('fecha', $fecha);
$stmt->bindColumn('glosa', $descripcion);
$stmt->bindColumn('observaciones', $observaciones);
$stmt->bindColumn('cod_comprobante', $codComprobante);

?>

<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header card-header-danger card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">attach_money</i>
                  </div>
                  <h4 class="card-title"><b>Pagos</b></h4>
                </div>
                <div class="card-body">
                    <table class="table table-condesed" id="tablePaginator">
                      <thead>
                        <tr>
                          <th class="text-center">#</th>
                          <th>Descripci&oacute;n</th>
                          <th>Fecha</th>
                          <th>Observaciones</th>
                          <th class="text-right">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
<?php
            $index=1;
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {


?>
                        <tr>
                          <td align="center"><?=$index;?></td>
                          <td><?=substr($descripcion, 0, 50)."..."?></td>
                          <td><?=strftime('%d/%m/%Y',strtotime($fecha));?></td>
                          <td><?=$observaciones;?></td>
                          <td class="td-actions text-right">
                            <div class="btn-group dropdown">
                                     <button type="button" class="btn btn-primary dropdown-toggle" title="COMPROBANTE - DEVENGADO" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                       <i class="material-icons"><?=$iconImp;?></i>
                                     </button>
                                    <div class="dropdown-menu">
                                      <?php
                                        $stmtMoneda = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM monedas where cod_estadoreferencial=1 order by 2");
                                       $stmtMoneda->execute();
                                       while ($row = $stmtMoneda->fetch(PDO::FETCH_ASSOC)) {
                                         $codigoX=$row['codigo'];
                                         $nombreX=$row['nombre'];
                                         $abrevX=$row['abreviatura'];
                                            ?>
                                             <a href="#" onclick="javascript:window.open('<?=$urlImpComp;?>?comp=<?=$codComprobante;?>&mon=<?=$codigoX?>')" class="dropdown-item">
                                                 <i class="material-icons">list_alt</i> <?=$abrevX?>
                                             </a> 
                                           <?php
                                         }
                                         ?>
                                    </div>
                                  </div>
                          </td>
                        </tr>
<?php
              $index++;
            }
?>
                      </tbody>
                    </table>
                </div>
              </div>
              <div class="card-footer fixed-bottom">
                <a href="#" onclick="javascript:window.open('<?=$urlRegister2;?>')" class="<?=$buttonNormal;?>">Nuevo Pago Proveedor</a>
              </div>      
            </div>
          </div>  
        </div>
    </div>