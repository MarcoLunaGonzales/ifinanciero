<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';

$dbh = new Conexion();
$globalAdmin=$_SESSION["globalAdmin"];



  //datos registrado de la simulacion en curso
  $stmt = $dbh->prepare("SELECT f.*,(select s.abreviatura from unidades_organizacionales s where s.cod_sucursal=f.cod_sucursal limit 1)as sucursal,(select t.nombre from estados_factura t where t.codigo=f.cod_estadofactura)as estadofactura 
 from facturas_venta f where cod_estadofactura=4 order by  f.fecha_factura desc");
  $stmt->execute();
  $stmt->bindColumn('codigo', $codigo_facturacion);
  $stmt->bindColumn('cod_sucursal', $cod_sucursal);
  $stmt->bindColumn('cod_area', $cod_area);
  $stmt->bindColumn('cod_solicitudfacturacion', $cod_solicitudfacturacion);
  $stmt->bindColumn('fecha_factura', $fecha_factura);
  $stmt->bindColumn('fecha_limite_emision', $fecha_limite_emision);
  $stmt->bindColumn('cod_tipopago', $cod_tipopago);
  $stmt->bindColumn('cod_cliente', $cod_cliente);
  $stmt->bindColumn('razon_social', $razon_social);
  $stmt->bindColumn('nit', $nit);
  $stmt->bindColumn('cod_dosificacionfactura', $cod_dosificacionfactura);
  $stmt->bindColumn('nro_factura', $nro_factura);
  $stmt->bindColumn('nro_autorizacion', $nro_autorizacion);
  $stmt->bindColumn('codigo_control', $codigo_control);
  $stmt->bindColumn('importe', $importe);
  $stmt->bindColumn('observaciones', $observaciones);
  $stmt->bindColumn('cod_estadofactura', $cod_estadofactura);
  $stmt->bindColumn('sucursal', $sucursal);
  // $stmt->bindColumn('cliente', $cliente);
  $stmt->bindColumn('estadofactura', $estadofactura);
  ?>
  <div class="content">
    <div class="container-fluid">
          <div class="row">
              <div class="col-md-12">
                <div class="card">
                  <div class="card-header card-header-warning card-header-icon">
                    <div class="card-icon">
                      <i class="material-icons">polymer</i>
                    </div>
                    <h4 class="card-title"><b>Facturas Generadas Manualmente</b></h4>
                    <!-- <h4 class="card-title" align="center"><b><?=$nombre_simulacion?> - <?=$name_area_simulacion?></b></h4> -->
                  </div>
                  <div class="card-body">
                      <table class="table" id="tablePaginator">
                        <thead>
                          <tr>
                            <!-- <th class="text-center"></th> -->
                            <th>#Factura</th>
                            <th>Sucursal</th>
                            <th>Fecha<br>Factura</th>
                            <th>Cliente</th>
                            <th>Importe</th>
                            <th>Obs.</th>
                            <th>Estado</th>
                            <th class="text-right">Opciones</th>                            
                          </tr>
                        </thead>
                        <tbody>
                        <?php
                          $index=1;
                          while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                            $cliente=nameCliente($cod_cliente);
                            //correos de contactos                          

                            //colores de estados                         
                            switch ($cod_estadofactura) {
                              case 1://activo
                                $label='<span class="badge badge-success">';
                                break;
                              case 2://anulado
                                $label='<span class="badge badge-danger">';
                                break;
                              case 3://enviado
                                $label='<span class="badge badge-info" style="">';
                                break;
                              case 4://enviado
                                $label='<span class="badge badge-warning" style="">';
                                break;
                                                          
                            }
                            // $datos=$codigo_facturacion.'/'.$cod_solicitudfacturacion.'/'.$nro_factura.'/'.$correos_string;
                            ?>
                          <tr>
                            <!-- <td align="center"><?=$index;?></td> -->
                            <td><?=$nro_factura;?></td>
                            <td><?=$sucursal;?></td>
                            <td><?=$fecha_factura?></td>
                            <td><?=$cliente;?></td>
                            <td class="text-right"><?=formatNumberDec($importe);?></td>
                            <td><?=$observaciones;?></td>                            
                            <td><?=$label.$estadofactura."</span>";?></td>
                            <td class="td-actions text-right">
                              <?php
                                if($globalAdmin==1 and $cod_estadofactura==1 ){?>                                
                                  
                                <?php  
                                }elseif($globalAdmin==1 and $cod_estadofactura==3){?>
                                  
                                <?php }

                              ?>

                              
                            </td>
                            
                          </tr>
                          <?php
                              $index++;
                            }
                          ?>
                        </tbody>
                      </table>
                  </div>
                  <div class="card-footer fixed-bottom col-sm-9">
                    <a href='<?=$urllistFacturasServicios;?>' class="btn btn-danger float-right"><i class="material-icons">keyboard_return</i>Volver</a>
                  </div>   
                </div>                
              </div>
          </div>  
    </div>
  </div>
