<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';

$dbh = new Conexion();
$globalAdmin=$_SESSION["globalAdmin"];

  $stmt = $dbh->prepare("SELECT *, (select c.descripcion_n2 from cla_servicios c where c.IdTipo=s.IdTipo LIMIT 1) as nombreTipo, (select cc.nombre from clientes cc where cc.codigo=s.IdCliente) as nombreCliente from servicios s where s.IdArea=11 and YEAR(s.fecharegistro)=2020");
  $stmt->execute();
  $stmt->bindColumn('IdServicio', $IdServicio);
  $stmt->bindColumn('IdArea', $IdArea);
  $stmt->bindColumn('IdOficina', $IdOficina);
  $stmt->bindColumn('nombreTipo', $nombreTipo);
  $stmt->bindColumn('Codigo', $Codigo);
  $stmt->bindColumn('IdCliente', $IdCliente);
  $stmt->bindColumn('nombreCliente', $nombreCliente);
  $stmt->bindColumn('Descripcion', $Descripcion);
  $stmt->bindColumn('fecharegistro', $fecharegistro);
  $stmt->bindColumn('carpeta', $carpeta);
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
                    <h4 class="card-title"><b>Servicios & Presupuestos</b></h4>
                    <!-- <h4 class="card-title" align="center"><b><?=$nombre_simulacion?> - <?=$name_area_simulacion?></b></h4> -->
                  </div>
                  <div class="card-body">
                      <table class="table" id="tablePaginator">
                        <thead>
                          <tr>
                            <th class="text-center">#</th>                          
                            <!-- <th>IdServicio</th> -->
                            <th>Area</th>
                            <th>Of</th>
                            <th>Tipo</th>
                            <!-- <th>Codigo</th> -->
                            <th>Cliente</th>
                            <th>Fecha R.</th>
                            <th>#Fact</th>
                            <!--<th>Precio (BOB)</th>                            
                            <th>Desc(%)</th>  
                            <th>Desc(BOB)</th>  
                            <th>Importe (BOB)</th>   -->
                            <th>Descripción</th>
                            <th class="text-right">Opciones</th>                            
                          </tr>
                        </thead>
                        <tbody>
                        <?php
                          $index=1;
                          $stringCabecera="";
                          $codigo_fact_x=0;
                          $cont= array();
                          while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                            $nombre_area=trim(abrevArea($IdArea),'-');
                            $nombre_uo=trim(abrevUnidad($IdOficina),' - ');

                            //buscamos a los propuestas que ya fueron solicitadas su facturacion
                            $codigo_facturacion=0;
                            $sqlFac="SELECT sf.codigo,sf.fecha_registro,sf.fecha_solicitudfactura,sf.razon_social,sf.nit from solicitudes_facturacion sf where sf.cod_simulacion_servicio=$IdServicio and sf.cod_cliente=$IdCliente";
                            $stmtSimuFact = $dbh->prepare($sqlFac);
                            $stmtSimuFact->execute();
                            $resultSimuFact = $stmtSimuFact->fetch();
                            $codigo_facturacion = $resultSimuFact['codigo'];                            
                            $nit = $resultSimuFact['nit'];
                            $fecha_registro = $resultSimuFact['fecha_registro'];
                            $fecha_solicitudfactura = $resultSimuFact['fecha_solicitudfactura'];
                            $razon_social = $resultSimuFact['razon_social'];

                            //verificamos si ya tiene factura generada                            
                            $stmtFact = $dbh->prepare("SELECT codigo, nro_factura from facturas_venta where cod_solicitudfacturacion=$codigo_facturacion and cod_estadofactura=1");
                            $stmtFact->execute();
                            $resultSimu = $stmtFact->fetch();
                            $codigo_fact_x = $resultSimu['codigo'];
                            $nro_fact_x = $resultSimu['nro_factura'];
                            if ($nro_fact_x==null)$nro_fact_x="-";
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
                            $nombre_simulacion=$Descripcion;
                            $stringCabecera=$nombre_uo."##".$nombre_area."##".$nombre_simulacion."##-##".$fecha_registro."##".$fecha_solicitudfactura."##".$nit."##".$razon_social;

                            ?>
                            
                          <tr>
                            <td align="center"><?=$index;?></td>
                            <!-- <td><?=$IdServicio?></td> -->
                            <td><?=$nombre_area?></td>
                            <td><?=$nombre_uo?></td>
                            <td><?=$nombreTipo?></td>
                            <!-- <td><?=$Codigo?></td>     -->                        
                            <td><?=$nombreCliente?></td>                            
                            <td><?=$fecharegistro?></td>                            
                            <td><?=$nro_fact_x;?></td>
                            <!-- <td class="text-right"><?=formatNumberDec($sumaTotalMonto) ;?></td>
                            <td class="text-right"><?=formatNumberDec($sumaTotalDescuento_por) ;?></td>
                            <td class="text-right"><?=formatNumberDec($sumaTotalDescuento_bob) ;?></td>
                            <td class="text-right"><?=formatNumberDec($sumaTotalImporte) ;?></td> -->
                            <td><?=$Descripcion?></td>
                            <td class="td-actions text-right">                              
                              <?php
                                if($globalAdmin==1){                            
                                  if($codigo_facturacion>0){
                                    if($codigo_fact_x==0){ //no se genero factura ?>
                                    <a title="Editar Solicitud de Facturación" href='<?=$urlRegisterSolicitudfactura?>&cod_simulacion=0&IdServicio=<?=$IdServicio?>&cod_facturacion=<?=$codigo_facturacion?>' class="btn btn-success">
                                      <i class="material-icons"><?=$iconEdit;?></i>
                                    </a>
                                  <?php }else{//ya se genero factura ?>
                                    <a class="btn btn-success" href='<?=$urlGenerarFacturasPrint;?>?codigo=<?=$codigo_facturacion;?>&tipo=2' target="_blank"><i class="material-icons" title="Imprimir Factura">print</i></a>
                                  <?php }?>
                                  <a href='#' rel="tooltip" class="btn btn-warning" onclick="filaTablaAGeneral($('#tablasA_registradas'),<?=$index?>,'<?=$stringCabecera?>')">
                                    <i class="material-icons" title="Ver Detalle">settings_applications</i>
                                  </a>         
                                <?php }else{//no se hizo solicitud de factura ?>
                                    <a href='<?=$urlRegisterSolicitudfactura?>&cod_simulacion=0&IdServicio=<?=$IdServicio?>&cod_facturacion=0' rel="tooltip" class="btn" style="background-color: #0489B1;">
                                    <i class="material-icons" title="Solicitar Facturación">receipt</i>
                                  </a>                                                    
                                  <?php }                                
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
                      <th width="20%">Item</th>
                      <th>Canti.</th>
                      <th>Precio(BOB)</th>  
                      <th>Desc(%)</th> 
                      <th>Desc(BOB)</th> 
                      <th width="10%">Importe(BOB)</th> 
                      <th width="45%">Descripción Alterna</th>                    
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
