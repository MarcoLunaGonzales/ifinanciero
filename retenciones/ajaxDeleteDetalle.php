<?php
session_start();
require_once '../conexion.php';
require_once '../styles.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once 'configModule.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

if(isset($_GET['codigo'])){
  $codigo=$_GET['codigo'];
  $sqlCod="SELECT cod_configuracionretenciones from configuracion_retencionesdetalle where codigo=$codigo";
  $stmt = $dbh->prepare($sqlCod);
  $stmt->execute();
  $codigoX=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $codigoX=$row['cod_configuracionretenciones'];
  }
  $debehaber=debeHaberRetencionDetalle($codigo);
  $porcent=porcentRetencionDetalle($codigo);
  if($debehaber==1){
    $porcentajeOrigen=porcentRetencion($codigoX)+$porcent;
  }else{
    $porcentajeOrigen=porcentRetencion($codigoX)-$porcent;
  }
  $sqlDelete="DELETE FROM configuracion_retencionesdetalle where codigo=$codigo";
  $stmtDelete = $dbh->prepare($sqlDelete);
  $stmtDelete->execute();

  $stmt = $dbh->prepare("UPDATE configuracion_retenciones set porcentaje_cuentaorigen='$porcentajeOrigen' where codigo=$codigoX");
  $stmt->execute();
  
}
?>
<script>$("#cuenta_origen").val(<?=$porcentajeOrigen?>)</script>
<table class="table table-striped table-condensed table-bordered">
                     <thead>
                       <tr>
                         <th>#</th>
                         <th>Cuenta</th>
                         <th>%</th>
                         <th>Debe</th>
                         <th>Haber</th>
                         <th>Glosa</th>
                         <th>Action</th>
                       </tr>
                     </thead>
                     <tbody>
                   <?php 
                   $index=1;
                   $stmt = $dbh->prepare("SELECT cd.* from configuracion_retenciones c join configuracion_retencionesdetalle cd on cd.cod_configuracionretenciones=c.codigo where cd.cod_configuracionretenciones=$codigoX order by cd.codigo");
                         $stmt->execute();
                         while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            if($row['cod_cuenta']==0){
                              $cuenta="Sin cuenta";
                            }else{
                              $numeroX=obtieneNumeroCuenta($row['cod_cuenta']);
                              $cuentaX=nameCuenta($row['cod_cuenta']);
                              $cuenta="[".$numeroX."] ".$cuentaX;
                            }                          

                          $codigoX=$row['codigo'];
                          
                          $porcentajeX=$row['porcentaje'];
                          
                          $glosaX=$row['glosa'];
                          $debehaberX=$row['debe_haber'];
                          if($debehaberX==1){
                            $debe="<i class='material-icons text-info'>check_circle_outline</i>";
                            $haber="";
                          }else{
                            $debe="";
                            $haber="<i class='material-icons text-info'>check_circle_outline</i>";
                          }
                        ?>
                         <tr>
                           <td><?=$index?></td>
                           <td><?=$cuenta?></td>
                           <td class="font-weight-bold"><?=$porcentajeX?></td>
                           <td><?=$debe?></td>
                           <td><?=$haber?></td>
                           <td><?=$glosaX?></td>
                           <td><a href="#" onclick="borrarRetencionDetalle(<?=$codigoX?>)" class="btn btn-danger btn-link"><i class='material-icons'>clear</i></a></td>
                         </tr>  
                         <?php             
                          $index++; 
                        }
                           ?>   
                     </tbody>
                  </table>
