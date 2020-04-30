<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';

$dbh = new Conexion();
$globalAdmin=$_SESSION["globalAdmin"];



  //datos registrado de la simulacion en curso
  $stmt = $dbh->prepare("SELECT sf.* FROM solicitudes_facturacion sf where cod_estado=1 order by fecha_solicitudfactura desc");
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
  $stmt->bindColumn('cod_estado', $cod_estado);
  $stmt->bindColumn('nro_correlativo', $nro_correlativo);
  $stmt->bindColumn('persona_contacto', $persona_contacto);
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
                            <th>Of.</th>
                            <th>Area</th>
                            <th>#Sol.</th>
                            <th>Propuesta</th>
                            <!-- <th>Responsable</th> -->
                            <th>F. Registro</th>
                            <th>F. a Facturar</th>
                            <th style="color:#cc4545;">#Fact</th>
                            <!-- <th>Precio (BOB)</th>                            
                            <th>Descu (%)</th>  
                            <th>Descu (BOB)</th>   -->
                            <th>Importe (BOB)</th>  
                            <th>Per.Contacto</th>  
                            <th>Razón Social</th>                            
                            
                            <th class="text-right">Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                        <?php
                          $index=1;
                          $codigo_fact_x=0;
                          $cont= array();
                          while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {                            
                            //verificamos si ya tiene factua generada y esta activa                           
                            $stmtFact = $dbh->prepare("SELECT codigo,nro_factura from facturas_venta where cod_solicitudfacturacion=$codigo_facturacion and cod_estadofactura=1");
                            $stmtFact->execute();
                            $resultSimu = $stmtFact->fetch();
                            $codigo_fact_x = $resultSimu['codigo'];
                            $nro_fact_x = $resultSimu['nro_factura'];
                            if ($nro_fact_x==null)$nro_fact_x="-";
                            //obtenemos datos de la simulacion
                            $sql="SELECT sc.nombre,ps.cod_area,ps.cod_unidadorganizacional
                            from simulaciones_servicios sc,plantillas_servicios ps
                            where sc.cod_plantillaservicio=ps.codigo and sc.cod_estadoreferencial=1 and sc.codigo=$cod_simulacion_servicio";                            
                            $stmtSimu = $dbh->prepare($sql);
                            $stmtSimu->execute();
                            $resultSimu = $stmtSimu->fetch();
                            $nombre_simulacion = $resultSimu['nombre'];
                            $cod_area_simulacion = $resultSimu['cod_area'];
                            //si es nulo, verificamos si pertenece a capacitacion
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
                            //verificamos si pertence a propuestas y servicios
                            if($nombre_simulacion==null || $nombre_simulacion == ''){
                              $sqlCostos="SELECT Descripcion,IdArea,IdOficina from servicios s where s.IdServicio=$cod_simulacion_servicio";
                              $stmtSimuCostos = $dbh->prepare($sqlCostos);
                              $stmtSimuCostos->execute();
                              $resultSimu = $stmtSimuCostos->fetch();
                              $nombre_simulacion = $resultSimu['Descripcion'];
                              $cod_area_simulacion = $resultSimu['IdArea'];
                            }

                            $name_area_simulacion=trim(abrevArea($cod_area_simulacion),'-');

                            // --------
                            $responsable=namePersonal($cod_personal);//nombre del personal
                            $nombre_area=trim(abrevArea($cod_area),'-');//nombre del area
                            $nombre_uo=nameUnidad($cod_unidadorganizacional);//nombre de la oficina

                            //los registros de la factura
                            $dbh1 = new Conexion();
                            $sqlA="SELECT sf.*,t.descripcion as nombre_serv from solicitudes_facturaciondetalle sf,cla_servicios t 
                                where sf.cod_claservicio=t.idclaservicio and sf.cod_solicitudfacturacion=$codigo_facturacion";
                            $stmt2 = $dbh1->prepare($sqlA);                                   
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
                            <td><?=$nombre_simulacion?> - <?=$name_area_simulacion?></td>
                            <!-- <td><?=$responsable;?></td> -->
                            <td><?=$fecha_registro;?></td>
                            <td><?=$fecha_solicitudfactura;?></td>                            
                            <td style="color:#cc4545;"><?=$nro_fact_x;?></td>                             
                            <td class="text-right"><?=formatNumberDec($sumaTotalImporte) ;?></td>
                            <td class="text-left"><?=$persona_contacto;?></td>
                            <td><?=$razon_social;?></td>
                            <!-- <td><?=$nit;?></td> -->

                            <td class="td-actions text-right">
                              <?php
                                if($globalAdmin==1){ 
                                  if($codigo_fact_x>0){//print facturas
                                    ?>
                                    <a class="btn btn-success" href='<?=$urlGenerarFacturasPrint;?>?codigo=<?=$codigo_facturacion;?>&tipo=2' target="_blank"><i class="material-icons" title="Imprimir Factura">print</i></a>
                                    <!-- <a class="btn btn-danger" href='<?=$urlAnularFactura;?>&codigo=<?=$codigo_facturacion;?>' ><i class="material-icons" title="Anular Factura">delete</i></a> -->
                                    
                                  <?php }else{// generar facturas
                                    ?>
                                    <button title="Generar Factura" target="_blank" class="btn btn-success" onclick="alerts.showSwal('warning-message-and-confirmation-generar-factura','<?=$urlGenerarFacturas2;?>?codigo=<?=$codigo_facturacion;?>')">
                                      <i class="material-icons">receipt</i>
                                    </button>
                                    <a href='#' rel="tooltip" class="btn btn-warning" onclick="filaTablaAGeneral($('#tablasA_registradas'),<?=$index?>,'<?=$stringCabecera?>')">
                                      <i class="material-icons" title="Ver Detalle">settings_applications</i>
                                    </a>

                                    <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation-anular-solicitud','<?=$urlAnular_SoliciutdFacturacion;?>&codigo=<?=$codigo_facturacion;?>')">
                                      <i class="material-icons" title="Anular Solicitud">clear</i>
                                    </button>
                                  
                                  <?php }                           
                                  ?>
                                  <a class="btn btn-danger" href='<?=$urlPrintSolicitud;?>?codigo=<?=$codigo_facturacion;?>' target="_blank"><i class="material-icons" title="Imprimir Solicitud">print</i></a>
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
                    <th>Item</th>
                    <th>Cantidad</th>
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


  