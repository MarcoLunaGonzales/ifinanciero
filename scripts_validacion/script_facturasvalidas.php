<?php
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../styles.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';


$dbh = new Conexion();
$stmt = $dbh->prepare("SELECT codigo,nro_factura from facturas_venta where cod_estadofactura<>2 order by nro_factura desc"); /*and sf.cod_estadosolicitudfacturacion!=5*/
$stmt->execute();
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('nro_factura', $nro_factura);
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
                    <h4 class="card-title"><b>Gesti&oacute;n de Solicitudes de Facturación</b></h4>
            
                  </div>
                  <div class="card-body">
                      <table class="table" id="tablePaginator">
                        <thead>
                          <tr>                            
                            <th><small>Tipo</small></th>
                            <th><small>nro</small></th>
                            <th><small>monto</small></th>                            
                          </tr>
                        </thead>
                        <tbody>
                        <?php
                        $index=1;
                        $codigo_fact_x=0;
                        $cont= array();
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {                          
                        	$sumaTotal=0;
                        	$sqlInfo="SELECT sum(fvd.precio) as precio, sum(fvd.cantidad) as cantidad,sum(fvd.descuento_bob) as descuento from facturas_ventadetalle fvd where fvd.cod_facturaventa=$codigo";
				                $stmtInfo = $dbh->prepare($sqlInfo);
				                // echo $sqlInfo;
				                $stmtInfo->execute();
				                $resultInfo = $stmtInfo->fetch();  
				                $precio = $resultInfo['precio']; 
				                $cantidad = $resultInfo['cantidad']; 
				                $descuento = $resultInfo['descuento']; 
				                $sumaTotal+=$precio;
                            ?>
                          <tr>
                           <td><small>FACTURA</small></td>
                           <td><small><?=$nro_factura;?></small></td>
                           <td><small><?=$sumaTotal;?></small></td>
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