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
$name_area_simulacion=trim(abrevArea($cod_area_simulacion),'-');
//obtenemos la cantidad de datos registrados de la simulacion en curso
$stmtCantidad = $dbh->prepare("SELECT count(codigo) as cantidad FROM solicitudes_facturacion where cod_simulacion_servicio=$codigo_simulacion and cod_estado=1");
$stmtCantidad->execute();
$resutCanitdad = $stmtCantidad->fetch();
$cantidad_items = $resutCanitdad['cantidad'];
if(isset($_GET['q'])){
  $q=$_GET['q'];
}
if($cantidad_items>0){
  //datos registrado de la simulacion en curso
  $stmt = $dbh->prepare("SELECT sf.*,t.nombre as nombre_cliente FROM solicitudes_facturacion sf,clientes t  where sf.cod_cliente=t.codigo and sf.cod_simulacion_servicio=$codigo_simulacion and  sf.cod_estado=1");
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
  $stmt->bindColumn('nro_correlativo', $nro_correlativo);
  $stmt->bindColumn('persona_contacto', $persona_contacto);
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
                            <th>Of.</th>
                            <th>Area</th>                            
                            <th>#Soli.</th>
                            <th>F. Registro</th>
                            <th>F. a Facturar</th>
                            <th>#Fact</th>
                            <!-- <th>Precio (BOB)</th>                            
                            <th>Desc(%)</th>  
                            <th>Desc(BOB)</th>   -->
                            <th width="8%">Importe (BOB)</th>  
                            <th>Per.Contacto</th>  
                            <th width="35%">Razón Social</th>                            
                            <th class="text-right">Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                        <?php
                          $index=1;
                          $stringCabecera="";
                          $codigo_fact_x=0;
                          $cont= array();
                          while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                            //verificamos si ya tiene factua generada                            
                            $stmtFact = $dbh->prepare("SELECT codigo, nro_factura from facturas_venta where cod_solicitudfacturacion=$codigo_facturacion and cod_estadofactura=1");
                            $stmtFact->execute();
                            $resultSimu = $stmtFact->fetch();
                            $codigo_fact_x = $resultSimu['codigo'];
                            $nro_fact_x = $resultSimu['nro_factura'];
                            if ($nro_fact_x==null)$nro_fact_x="-";

                            $responsable=namePersonal($cod_personal);//nombre del personal
                            $nombre_area=trim(abrevArea($cod_area),'-');//nombre de area
                            $nombre_uo=trim(abrevUnidad($cod_unidadorganizacional),' - ');//nombre de oficina
                            //los registros de la factura                            
                            $sqlA="SELECT sf.*,t.descripcion as nombre_serv from solicitudes_facturaciondetalle sf,cla_servicios t 
                                where sf.cod_claservicio=t.idclaservicio and sf.cod_solicitudfacturacion=$codigo_facturacion";
                            $stmt2 = $dbh->prepare($sqlA);                                   
                            $stmt2->execute(); 
                            $nc=0;
                            $sumaTotalMonto=0;
                            $sumaTotalDescuento_por=0;
                            $sumaTotalDescuento_bob=0;

                            while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                              $dato = new stdClass();//obejto
                              $codFila=(int)$row2['codigo'];
                              $cod_claservicioX=trim($row2['nombre_serv']);
                              $cantidadX=trim($row2['cantidad']);
                              $precioX=trim($row2['precio'])+trim($row2['descuento_bob']);
                              $descuento_porX=trim($row2['descuento_por']);
                              $descuento_bobX=trim($row2['descuento_bob']);
                              $descripcion_alternaX=trim($row2['descripcion_alterna']);
                              $dato->codigo=($nc+1);
                              $dato->cod_facturacion=$codFila;
                              $dato->serviciox=$cod_claservicioX;
                              $dato->cantidadX=$cantidadX;
                              $dato->precioX=$precioX;
                              $dato->descuento_porX=$descuento_porX;
                              $dato->descuento_bobX=$descuento_bobX;
                              $dato->descripcion_alternaX=$descripcion_alternaX;
                              $datos[$index-1][$nc]=$dato;                           
                              $nc++;
                              $sumaTotalMonto+=$precioX;
                              $sumaTotalDescuento_por+=$descuento_porX;
                              $sumaTotalDescuento_bob+=$descuento_bobX;

                            }
                            $sumaTotalImporte=$sumaTotalMonto-$sumaTotalDescuento_bob;
                            $cont[$index-1]=$nc;  
                            $stringCabecera=$nombre_uo."##".$nombre_area."##".$nombre_simulacion."##".$name_area_simulacion."##".$fecha_registro."##".$fecha_solicitudfactura."##".$nit."##".$razon_social;
                            ?>
                          <tr>
                            <td align="center"><?=$index;?></td>
                            <td><?=$nombre_uo;?></td>
                            <td><?=$nombre_area;?></td>
                            <td><?=$nro_correlativo;?></td>
                            <td><?=$fecha_registro;?></td>
                            <td><?=$fecha_solicitudfactura;?></td>
                            <td><?=$nro_fact_x;?></td>
                            <!-- <td class="text-right"><?=formatNumberDec($sumaTotalMonto) ;?></td>
                            <td class="text-right"><?=formatNumberDec($sumaTotalDescuento_por) ;?></td>
                            <td class="text-right"><?=formatNumberDec($sumaTotalDescuento_bob) ;?></td> -->
                            <td class="text-right"><?=formatNumberDec($sumaTotalImporte) ;?></td>
                            <td class="text-left"><?=$persona_contacto;?></td>
                            <td><?=$razon_social;?></td>                            
                            <td class="td-actions text-right">
                              <?php
                                if($globalAdmin==1){
                                  if($codigo_fact_x==0){?>
                                    <a title="Editar Simulación - Detalle" href='<?=$urlRegisterSolicitudfactura;?>&cod_s=<?=$codigo_simulacion?>&cod_f=<?=$codigo_facturacion?>&cod_sw=1' class="btn btn-info">
                                      <i class="material-icons"><?=$iconEdit;?></i>
                                    </a>
                                    
                                  <?php }else{?>
                                    <a class="btn btn-success" href='<?=$urlGenerarFacturasPrint;?>?codigo=<?=$codigo_facturacion;?>&tipo=2' target="_blank"><i class="material-icons" title="Imprimir Factura">print</i></a>
                                  <?php }
                                ?>
                                <a href='#' rel="tooltip" class="btn btn-warning" onclick="filaTablaAGeneral($('#tablasA_registradas'),<?=$index?>,'<?=$stringCabecera?>')">
                                  <i class="material-icons" title="Ver Detalle">settings_applications</i>
                                </a>
                                <a class="btn btn-danger" href='<?=$urlPrintSolicitud;?>?codigo=<?=$codigo_facturacion;?>' target="_blank"><i class="material-icons" title="Imprimir Solicitud">print</i></a>
                              
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
                  <div class="row" id="div_cabecera" >
                    
                  </div>
                  
                  <table class="table table-condensed">
                    <thead>
                      <tr class="text-dark bg-plomo">
                      <th>#</th>
                      <th width="20%">Item</th>
                      <th>Canti.</th>
                      <th>Precio(BOB)</th>  
                      <th>Desc(%)</th> 
                      <th>Desc(BOB)</th> 
                      <th width="10%">Importe(BOB)</th> 
                      <th width="45%">Glosa</th>                    
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
              ?><script>detalle_fac.push({codigo:<?=$datos[$i][$j]->codigo?>,cod_facturacion:<?=$datos[$i][$j]->cod_facturacion?>,serviciox:'<?=$datos[$i][$j]->serviciox?>',cantidadX:'<?=$datos[$i][$j]->cantidadX?>',precioX:'<?=$datos[$i][$j]->precioX?>',descuento_porX:'<?=$datos[$i][$j]->descuento_porX?>',descuento_bobX:'<?=$datos[$i][$j]->descuento_bobX?>',descripcion_alternaX:'<?=$datos[$i][$j]->descripcion_alternaX?>'});</script><?php         
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



  