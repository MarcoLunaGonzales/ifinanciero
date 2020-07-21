<?php
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../styles.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';


$dbh = new Conexion();
$stmt = $dbh->prepare("SELECT cod_solicitudfacturacion,cod_unidadorganizacional,cod_area,fecha_factura,razon_social,nit,nro_factura,cod_estadofactura from facturas_venta"); /*and sf.cod_estadosolicitudfacturacion!=5*/
$stmt->execute();
$stmt->bindColumn('cod_solicitudfacturacion', $codigo_facturacion);
$stmt->bindColumn('cod_unidadorganizacional', $cod_unidadorganizacional);
$stmt->bindColumn('cod_area', $cod_area);
$stmt->bindColumn('fecha_factura', $fecha_factura);
$stmt->bindColumn('razon_social', $razon_social);
$stmt->bindColumn('nit', $nit);
$stmt->bindColumn('nro_factura', $nro_factura);
$stmt->bindColumn('cod_estadofactura', $cod_estadofactura);

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
                    <h4 class="card-title"><b>Gesti&oacute;n de Facturas</b></h4>
            
                  </div>
                  <div class="card-body">
                      <table class="table" id="tablePaginator">
                        <thead>
                          <tr>                            
                            <th><small>Nro Fac.</small></th>
                            <th><small>Fecha</small></th>
                            <th><small>Razón Social</small></th>
                            <th><small>Nit</small></th>
                            <th><small>Facturado Por</small></th>
                            <th><small>Estado</small></th>
                          </tr>
                        </thead>
                        <tbody>
                        <?php
                        $index=1;
                        
                        
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {           
                          $objeto_solfac=2709;               
                          $userprocesado=obtenerPersonaCambioEstado($objeto_solfac,$codigo_facturacion,2729);//procesado
                          $personal_procesado=namePersonal($userprocesado);    
                          $fecha_procesado=obtenerFechaCambioEstado($objeto_solfac,$codigo_facturacion,2729);
                          switch ($cod_estadofactura) {
                            case '1':                              
                            $estado="Válido";
                            break;
                            case '2':                              
                            $estado="Anulado";
                            break;
                            case '3':       
                            $estado="Enviado";                       
                            break;
                            case '4':       
                            $estado="Factura Manual";                       
                            break;
                          }                          

                            ?>
                          <tr>
                           <td><small><?=$nro_factura?></small></td>
                           <td><small><?=$fecha_factura;?></small></td>
                           <td><small><?=$razon_social;?></small></td>
                           <td><small><?=$nit;?></small></td>
                           <td><small><?=$personal_procesado;?></small></td>
                           <td><small><?=$estado;?></small></td>
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