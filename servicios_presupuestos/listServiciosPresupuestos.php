<?php
require_once 'conexion.php';
require_once 'conexion_externa.php';
require_once 'configModule.php';
require_once 'styles.php';
$dbh = new Conexion();
$dbhIBNO = new ConexionIBNORCA();
$globalAdmin=$_SESSION["globalAdmin"];
$stmtIBNO = $dbhIBNO->prepare("SELECT *, DATE_FORMAT(s.fecharegistro,'%d/%m/%Y')as fecharegistro_x from servicios s where s.IdArea=11 and YEAR(s.fecharegistro)=2020");
$stmtIBNO->execute();
$stmtIBNO->bindColumn('IdServicio', $IdServicio);
$stmtIBNO->bindColumn('IdArea', $IdArea);
$stmtIBNO->bindColumn('IdOficina', $IdOficina);
// $stmtIBNO->bindColumn('nombreTipo', $nombreTipo);  
$stmtIBNO->bindColumn('IdTipo', $IdTipo);
$stmtIBNO->bindColumn('IdCliente', $IdCliente);
// $stmtIBNO->bindColumn('nombreCliente', $nombreCliente);
$stmtIBNO->bindColumn('Descripcion', $Descripcion);
$stmtIBNO->bindColumn('fecharegistro_x', $fecharegistro);
$stmtIBNO->bindColumn('carpeta', $carpeta);
$stmtIBNO->bindColumn('Codigo', $Codigo_alterno);

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
                            <!-- <th>Cod. Serv.</th> -->
                            <th>Of</th>
                            <th>Area</th>
                            <th>Cod. Servicio</th>
                            <!-- <th>Tipo</th> -->                            
                            <th>Cliente</th>
                            <th>Fecha<br>Registro</th>
                            <th style="color:#cc4545;">Nro<br>Fact</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th class="text-right">Opciones</th>                            
                          </tr>
                        </thead>
                        <tbody>
                        <?php
                          $index=1;
                          $stringCabecera="";
                          $codigo_fact_x=0;
                          $cont= array();
                          while ($row = $stmtIBNO->fetch(PDO::FETCH_BOUND)) {
                            $nombreCliente=nameCliente($IdCliente);
                            $nombre_area=trim(abrevArea($IdArea),'-');
                            $nombre_uo=trim(abrevUnidad($IdOficina),' - ');

                            //buscamos a los propuestas que ya fueron solicitadas su facturacion
                            $codigo_facturacion=0;
                            $sqlFac="SELECT sf.codigo,sf.fecha_registro,sf.fecha_solicitudfactura,sf.razon_social,sf.nit,sf.cod_estadosolicitudfacturacion,(select s.nombre from estados_solicitudfacturacion s where s.codigo = sf.cod_estadosolicitudfacturacion) as estado from solicitudes_facturacion sf where sf.cod_estado=1 and sf.cod_simulacion_servicio=$IdServicio and sf.cod_cliente=$IdCliente";
                            $stmtSimuFact = $dbh->prepare($sqlFac);
                            $stmtSimuFact->execute();
                            $resultSimuFact = $stmtSimuFact->fetch();
                            $codigo_facturacion = $resultSimuFact['codigo'];                            
                            $nit = $resultSimuFact['nit'];
                            $fecha_registro = $resultSimuFact['fecha_registro'];
                            $fecha_solicitudfactura = $resultSimuFact['fecha_solicitudfactura'];
                            $razon_social = $resultSimuFact['razon_social'];
                            $codEstado = $resultSimuFact['cod_estadosolicitudfacturacion'];
                            $estado = $resultSimuFact['estado'];
                            switch ($codEstado) {
                              case 1:
                                $btnEstado="btn-default";
                              break;
                              case 2:
                                $btnEstado="btn-danger";
                              break;
                              case 3:
                                $btnEstado="btn-success";
                              break;
                              case 4:
                                $btnEstado="btn-warning";
                              break;
                              case 5:
                                $btnEstado="btn-warning";
                              break;
                              case 6:
                                $btnEstado="btn-default";
                              break;
                            }


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
                            <td><?=$nombre_uo?></td>
                            <td><?=$nombre_area?></td>
                            <td class="text-left"><?=$Codigo_alterno?></td>
                            <td class="text-left"><?=$nombreCliente?></td>                            
                            <td><?=$fecharegistro?></td>                            
                            <td style="color:#cc4545;"><b><?=$nro_fact_x;?></b></td>                            
                            <td class="text-left"><?=$Descripcion?></td>
                            <td width="5%"><button class="btn <?=$btnEstado?> btn-sm btn-link"><?=$estado;?></button></td>
                            <td class="td-actions text-right">                              
                              <?php
                                if($globalAdmin==1){                            
                                  if($codigo_facturacion>0){
                                    if($codigo_fact_x==0){ //no se genero factura 
                                      if($codEstado==1){?>
                                    <a title="Editar Solicitud de Facturación" href='<?=$urlRegisterSolicitudfactura?>&cod_simulacion=0&IdServicio=<?=$IdServicio?>&cod_facturacion=<?=$codigo_facturacion?>' class="btn btn-success">
                                      <i class="material-icons"><?=$iconEdit;?></i>
                                    </a>
                                  <?php }}else{//ya se genero factura ?>
                                    <a class="btn btn-success" href='<?=$urlGenerarFacturasPrint;?>?codigo=<?=$codigo_facturacion;?>&tipo=2' target="_blank"><i class="material-icons" title="Imprimir Factura">print</i></a>
                                  <?php }?>
                                  <a href='#' rel="tooltip" class="btn btn-warning" onclick="filaTablaAGeneral($('#tablasA_registradas'),<?=$index?>,'<?=$stringCabecera?>')">
                                    <i class="material-icons" title="Ver Detalle">settings_applications</i>
                                  </a>
                                  <a class="btn btn-danger" href='<?=$urlPrintSolicitud;?>?codigo=<?=$codigo_facturacion;?>' target="_blank"><i class="material-icons" title="Imprimir">print</i></a>         
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
                    <!--   <th>Precio(BOB)</th>  
                      <th>Desc(%)</th> 
                      <th>Desc(BOB)</th> --> 
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
