<?php
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../styles.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';


$dbh = new Conexion();
$stmt = $dbh->prepare("SELECT f.codigo,f.nro_factura,f.fecha_factura,f.razon_social,f.nit
FROM facturas_venta f where f.cod_solicitudfacturacion=-100 "); /*and sf.cod_estadosolicitudfacturacion!=5*/
$stmt->execute();
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('nro_factura', $nro_factura);
$stmt->bindColumn('razon_social', $razon_social);
$stmt->bindColumn('nit', $nit);
?>

  <div class="content">
    <div class="container-fluid">
          <div class="row">
              <div class="col-md-12">
                <div class="card">
                  <div class="card-header card-header-info card-header-icon">
                    <div class="card-icon">
                      <i class="material-icons">content_paste</i>
                    </div>
                    <h4 class="card-title"><b>FActuras</b></h4>
            
                  </div>
                  <div class="card-body">
                      <table class="table" id="tablePaginator">
                        <thead>
                          <tr>                            
                            <th><small>codigo</small></th>
                            <th><small>nro_factura</small></th>
                            <!-- <th><small>fecha</small></th> -->
                            <th><small>razon_social</small></th>
                            <th><small>nit</small></th>                            
                            <th><small> json_nit</small></th>  

                          </tr>
                        </thead>
                        <tbody>
                        <?php
                        $index=1;                        
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {   
                        $codigo_error='Factura Generada Correctamente IdFactura'.$codigo;
                        	$sqlInfo="SELECT codigo,json FROM log_facturas where detalle_error = '$codigo_error' ";
				                $stmtInfo = $dbh->prepare($sqlInfo);
				                // echo $sqlInfo;
				                $stmtInfo->execute();
				                $resultInfo = $stmtInfo->fetch();  
				                $json = $resultInfo['json']; 
                        $arra_json=json_decode($json,true);
                        // $datos = json_decode($json, true);  
                        // $var_nit=explode('nitciCliente', $json);
                        $var_nit_x=$arra_json['nitciCliente'];
                        $codigo_j = $resultInfo['codigo']; 
                            ?>
                          <tr>
                           <td><small><?=$codigo?></small></td>
                            <td><small><?=$nro_factura?></small></td>
                            <!-- <td><small><?=$fecha_factura?></small></td> -->
                            <td><small><?=$razon_social?></small></td>
                            <td><small><?=$nit?></small></td>
                            <?php
                            if($nit!=$var_nit_x){?>
                              <td><small><span style="background-color: #fff000"><?=$var_nit_x?><span></small></td>  
                              <?php }else{?>                            
                            <td><small><?=$var_nit_x?></small></td>  
                          <?php }?>
                          </tr>
                          <?php
                              $index++;
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