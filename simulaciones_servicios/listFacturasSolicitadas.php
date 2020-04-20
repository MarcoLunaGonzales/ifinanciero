<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';

$dbh = new Conexion();
$globalAdmin=$_SESSION["globalAdmin"];



  //datos registrado de la simulacion en curso
  $stmt = $dbh->prepare("SELECT sf.* FROM solicitudes_facturacion sf");
  $stmt->execute();
  $stmt->bindColumn('codigo', $codigo_facturacion);
  $stmt->bindColumn('cod_simulacion_servicio', $cod_simulacion_servicio);
  $stmt->bindColumn('cod_unidadorganizacional', $cod_unidadorganizacional);
  $stmt->bindColumn('cod_area', $cod_area);
  $stmt->bindColumn('fecha_registro', $fecha_registro);
  $stmt->bindColumn('fecha_solicitudfactura', $fecha_solicitudfactura);
  $stmt->bindColumn('cod_tipoobjeto', $cod_tipoobjeto);
  $stmt->bindColumn('cod_tipopago', $cod_tipopago);
  $stmt->bindColumn('cod_cliente', $cod_cliente);
  $stmt->bindColumn('cod_personal', $cod_personal);
  $stmt->bindColumn('razon_social', $razon_social);
  $stmt->bindColumn('nit', $nit);
  $stmt->bindColumn('observaciones', $observaciones);
  // $stmt->bindColumn('nombre_cliente', $nombre_cliente);

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
                    <h4 class="card-title"><b>Solicitudes de Facturación</b></h4>
                    <!-- <h4 class="card-title" align="center"><b><?=$nombre_simulacion?> - <?=$name_area_simulacion?></b></h4> -->
                  </div>
                  <div class="card-body">
                      <table class="table" id="tablePaginator">
                        <thead>
                          <tr>
                            <th class="text-center">#</th>                          
                            <th>Oficina</th>
                            <th>Area</th>
                            <th>Nombre Simulación</th>
                            <th>F. Registro</th>
                            <th>F. a Facturar</th>
                            <th>Cliente</th>
                            <th>Personal</th>
                            <th class="text-right">Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                        <?php
                          $index=1;
                          while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                            //verificamos si ya tiene factua generada                            
                            $stmtFact = $dbh->prepare("SELECT codigo from facturas_venta where cod_solicitudfacturacion=$codigo_facturacion");
                            $stmtFact->execute();
                            $resultSimu = $stmtFact->fetch();
                            $codigo_fact_x = $resultSimu['codigo'];

                            //obtenemos datos de la simulacion
                            $sql="SELECT sc.nombre,ps.cod_area,ps.cod_unidadorganizacional
                            from simulaciones_servicios sc,plantillas_servicios ps
                            where sc.cod_plantillaservicio=ps.codigo and sc.cod_estadoreferencial=1 and sc.codigo=$cod_simulacion_servicio";                            
                            $stmtSimu = $dbh->prepare($sql);
                            $stmtSimu->execute();
                            $resultSimu = $stmtSimu->fetch();
                            $nombre_simulacion = $resultSimu['nombre'];
                            $cod_area_simulacion = $resultSimu['cod_area'];
                            if($nombre_simulacion==null || $nombre_simulacion == ''){
                              $sqlCostos="SELECT sc.nombre,sc.cod_responsable,ps.cod_area,ps.cod_unidadorganizacional
                              from simulaciones_costos sc,plantillas_servicios ps
                              where sc.cod_plantillacosto=ps.codigo and sc.cod_estadoreferencial=1 and sc.codigo=$cod_simulacion_servicio order by sc.codigo";
                              $stmtSimuCostos = $dbh->prepare($sqlCostos);
                              $stmtSimuCostos->execute();
                              $resultSimu = $stmtSimuCostos->fetch();
                              $nombre_simulacion = $resultSimu['nombre'];
                              $cod_area_simulacion = $resultSimu['cod_area'];
                            }

                            $name_area_simulacion=abrevArea($cod_area_simulacion);

                            // --------
                            $responsable=namePersonal($cod_personal);//nombre del personal
                            $nombre_area=abrevArea($cod_area);//nombre del personal
                            $nombre_uo=nameUnidad($cod_unidadorganizacional);//nombre del personal
                            ?>
                          <tr>
                            <td align="center"><?=$index;?></td>
                            <td><?=$nombre_uo;?></td>
                            <td><?=$nombre_area;?></td>
                            <td><?=$nombre_simulacion?> - <?=$name_area_simulacion?></td>
                            <td><?=$fecha_registro;?></td>
                            <td><?=$fecha_solicitudfactura;?></td>
                            <!-- <td><?=$nombre_cliente;?></td> -->
                            <td><?=$razon_social;?></td>
                            <td><?=$responsable;?></td>

                            <td class="td-actions text-right">
                              <?php
                                if($globalAdmin==1){ 
                                  if($codigo_fact_x>0){//print facturas
                                    ?>
                                    <div class="dropdown">
                                      <button class="btn btn-success dropdown-toggle" type="button" id="reporte_sueldos" data-toggle="dropdown" aria-extended="true">
                                        <i class="material-icons" title="Imprimir Facturas">print</i>
                                        <span class="caret"></span>
                                      </button>
                                      <ul class="dropdown-menu" role="menu" aria-labelledby="reporte_sueldos">
                                        <li role="presentation" class="dropdown-header"><small>IMPRIMIR</small></li>
                                        <li role="presentation"><a role="item" href='<?=$urlGenerarFacturasPrint;?>?codigo=<?=$codigo_facturacion;?>&tipo=1' target="_blank"><small>Facturas</small></a>
                                        </li>
                                        <li role="presentation"><a role="item" href='<?=$urlGenerarFacturasPrint;?>?codigo=<?=$codigo_facturacion;?>&tipo=2' target="_blank"><small>Facturas Con Descripción  Alterna</small></a>
                                        </li>
                                                                     
                                      </ul>
                                    </div>
                                    
                                   <!--  <a title="Ver Factura" href='<?=$urlGenerarFacturasPrint;?>?codigo=<?=$codigo_facturacion;?>' target="_blank" class="btn btn-success">
                                      <i class="material-icons">description</i>
                                    </a> -->
                                  <?php }else{// generar facturas
                                    ?>
                                    <button title="Generar Factura"  target="blank" class="btn btn-success" onclick="alerts.showSwal('warning-message-and-confirmationGeneral','<?=$urlGenerarFacturas2;?>?codigo=<?=$codigo_facturacion;?>')">
                                      <i class="material-icons">monetization_on</i>
                                    </button>
                                  
                                  <?php }                           
                                  ?>
                                <?php  
                                }
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
                </div>
                <!-- <div class="card-footer fixed-bottom">
                 <?php 
                if($globalAdmin==1){              
                    ?><a href="<?=$urlRegisterSolicitudfactura;?>&cod_s=<?=$codigo_simulacion?>&cod_f=0&cod_sw=1" target="_self" class="<?=$buttonNormal;?>">Registrar</a>
                    <a href='<?=$urlList;?>' class="<?=$buttonCancel;?>"><i class="material-icons" title="Volver">keyboard_return</i> Volver </a>
                    <?php                
                } 
                 ?>
                </div> -->      
              </div>
          </div>  
    </div>
  </div>



  