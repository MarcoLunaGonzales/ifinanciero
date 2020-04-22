<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';
$codigo_simulacion=$cod;//codigo de simulacion
$dbh = new Conexion();
$globalAdmin=$_SESSION["globalAdmin"];
//obtenemos datos de la simulacion en curso
$sql="SELECT sc.nombre,ps.cod_area,ps.cod_unidadorganizacional
from simulaciones_servicios sc,plantillas_servicios ps
where sc.cod_plantillaservicio=ps.codigo and sc.cod_estadoreferencial=1 and sc.codigo=$codigo_simulacion";
$stmtSimu = $dbh->prepare($sql);
$stmtSimu->execute();
$resultSimu = $stmtSimu->fetch();
$nombre_simulacion = $resultSimu['nombre'];
$cod_area_simulacion = $resultSimu['cod_area'];
$name_area_simulacion=abrevArea($cod_area_simulacion);
//obtenemos la cantidad de datos registrados de la simulacion en curso
$stmtCantidad = $dbh->prepare("SELECT count(codigo) as cantidad FROM solicitudes_facturacion where cod_simulacion_servicio=$codigo_simulacion");
$stmtCantidad->execute();
$resutCanitdad = $stmtCantidad->fetch();
$cantidad_items = $resutCanitdad['cantidad'];
if(isset($_GET['q'])){
  $q=$_GET['q'];
}
if($cantidad_items>0){
  //datos registrado de la simulacion en curso
  $stmt = $dbh->prepare("SELECT sf.*,t.nombre as nombre_cliente FROM solicitudes_facturacion sf,clientes t  where sf.cod_cliente=t.codigo and sf.cod_simulacion_servicio=$codigo_simulacion");
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
  $stmt->bindColumn('nombre_cliente', $nombre_cliente);
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
                    <h4 class="card-title"><b>Solicitud de Facturación</b></h4>
                    <h4 class="card-title" align="center"><b>Propuesta : <?=$nombre_simulacion?> - <?=$name_area_simulacion?></b></h4>
                  </div>
                  <div class="card-body">
                      <table class="table" id="tablePaginator">
                        <thead>
                          <tr>
                            <th class="text-center">#</th>                          
                            <th>Oficina</th>
                            <th>Area</th>
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
                            $responsable=namePersonal($cod_personal);//nombre del personal
                            $nombre_area=abrevArea($cod_area);//nombre del personal
                            $nombre_uo=nameUnidad($cod_unidadorganizacional);//nombre del personal
                            //los registros de la factura
                            $dbh1 = new Conexion();
                            $sqlA="SELECT sf.*,t.descripcion as nombre_serv from solicitudes_facturaciondetalle sf,cla_servicios t 
                                where sf.cod_claservicio=t.idclaservicio and sf.cod_solicitudfacturacion=$codigo_facturacion";
                                   $stmt2 = $dbh1->prepare($sqlA);                                   
                                   $stmt2->execute(); 
                                   $nc=0;
                                   while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                                      $dato = new stdClass();//obejto
                                      $codFila=(int)$row2['codigo'];
                                      $cod_claservicioX=trim($row2['nombre_serv']);
                                      $cantidadX=trim($row2['cantidad']);
                                      $precioX=trim($row2['precio']);
                                      $descripcion_alternaX=trim($row2['descripcion_alterna']);
                                      $dato->codigo=($nc+1);
                                      $dato->cod_facturacion=$codFila;
                                      $dato->serviciox=$cod_claservicioX;
                                      $dato->cantidadX=$cantidadX;
                                      $dato->precioX=$precioX;
                                      $dato->descripcion_alternaX=$descripcion_alternaX;
                                      $datos[$index-1][$nc]=$dato;                           
                                      $nc++;
                                    }
                                $cont[$index-1]=$nc;  
                            ?>
                          <tr>
                            <td align="center"><?=$index;?></td>
                            <td><?=$nombre_uo;?></td>
                            <td><?=$nombre_area;?></td>
                            <td><?=$fecha_registro;?></td>
                            <td><?=$fecha_solicitudfactura;?></td>
                            <td><?=$nombre_cliente;?></td>
                            <td><?=$responsable;?></td>

                            <td class="td-actions text-right">
                              <?php
                                if($globalAdmin==1){                            
                                ?>
                              <a title="Editar Simulación - Detalle" href='<?=$urlRegisterSolicitudfactura;?>&cod_s=<?=$codigo_simulacion?>&cod_f=<?=$codigo_facturacion?>&cod_sw=1' class="btn btn-info">
                                <i class="material-icons"><?=$iconEdit;?></i>
                              </a>
                              <a href='#' rel="tooltip" class="btn btn-warning" onclick="filaTablaAGeneral($('#tablasA_registradas'),<?=$index?>)">
                              <i class="material-icons" title="Ver Detalle">settings_applications</i>
                            </a>
                              <!-- <button type="button" onclick="SolicitudFacturacionDetalle()" class="btn btn-success ">
                                 <i class="material-icons" title="Facturación Detalle">description</i>
                              </button> -->

                             <!--  <button title="Eliminar Simulación" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDelete;?>&codigo=<?=$codigo;?>')">
                                <i class="material-icons"><?=$iconDelete;?></i>
                              </button> -->                            
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
                <div class="card-footer fixed-bottom">
                 <?php 
                if($globalAdmin==1){              
                    ?><a href="<?=$urlRegisterSolicitudfactura;?>&cod_s=<?=$codigo_simulacion?>&cod_f=0&cod_sw=1" target="_self" class="<?=$buttonNormal;?>">Registrar</a>
                    <a href='<?=$urlList;?>' class="<?=$buttonCancel;?>"><i class="material-icons" title="Volver">keyboard_return</i> Volver </a>
                    <?php                
                } 
                 ?>
                </div>      
              </div>
          </div>  
    </div>
  </div>

  <!-- small modal -->
  <div class="modal fade modal-primary" id="modalDetalleFac" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content card">
                <div class="card-header card-header-warning card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">settings_applications</i>
                  </div>
                  <h4 class="card-title">Detalle Solicitud</h4>
                </div>
                <div class="card-body">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                  <i class="material-icons">close</i>
                </button>
                  <table class="table table-condensed">
                    <thead>
                      <tr class="text-dark bg-plomo">
                      <th>#</th>
                      <th>Item</th>
                      <th>Cantidad</th>
                      <th>Importe</th>  
                      <th>Descripción Alterna</th>                    
                      </tr>
                    </thead>
                    <tbody id="tablasA_registradas">
                      
                    </tbody>
                  </table>
                </div>
      </div>  
    </div>
  </div>
  <!--    end small modal -->

  <?php 
    $lan=sizeof($cont);
    error_reporting(0);
    for ($i=0; $i < $lan; $i++) {
      ?>
      <script>var detalle_fac=[];</script>
      <?php
         for ($j=0; $j < $cont[$i]; $j++) {     
             if($cont[$i]>0){
              ?><script>detalle_fac.push({codigo:<?=$datos[$i][$j]->codigo?>,cod_facturacion:<?=$datos[$i][$j]->cod_facturacion?>,serviciox:'<?=$datos[$i][$j]->serviciox?>',cantidadX:'<?=$datos[$i][$j]->cantidadX?>',precioX:'<?=$datos[$i][$j]->precioX?>',descripcion_alternaX:'<?=$datos[$i][$j]->descripcion_alternaX?>'});</script><?php         
              }          
            }
        ?><script>detalle_tabla_general.push(detalle_fac);</script><?php                    
    }
    ?>

<?php }else{
  if(isset($_GET['q'])){
    ?>
      <script type="text/javascript">
        location = "<?=$urlRegisterSolicitudfactura;?>&cod_s=<?=$codigo_simulacion?>&cod_f=0&cod_sw=0&q=<?=$q?>"
      </script>
   <?php
  }else{
   ?>
      <script type="text/javascript">
        location = "<?=$urlRegisterSolicitudfactura;?>&cod_s=<?=$codigo_simulacion?>&cod_f=0&cod_sw=0"
      </script>
   <?php
  }
    
  }
 ?>



  