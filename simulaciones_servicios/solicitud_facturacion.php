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
$stmtCantidad = $dbh->prepare("SELECT count(codigo) as cantidad FROM solicitudes_facturacion where cod_simulacion_servicio=$codigo_simulacion ");//and cod_estado=1
$stmtCantidad->execute();
$resutCanitdad = $stmtCantidad->fetch();
$cantidad_items = $resutCanitdad['cantidad'];
if(isset($_GET['q'])){
  $q=$_GET['q'];
  $v=$_GET['v'];
  $s=$_GET['s'];
  $u=$_GET['u'];
}
if($cantidad_items>0){
  //datos registrado de la simulacion en curso

  $stmt = $dbh->prepare("SELECT sf.*,es.nombre as estado,t.nombre as nombre_cliente,(select s.nombre from estados_solicitudfacturacion s where s.codigo = sf.cod_estadosolicitudfacturacion) as estado,DATE_FORMAT(sf.fecha_registro,'%d/%m/%Y')as fecha_registro_x,DATE_FORMAT(sf.fecha_solicitudfactura,'%d/%m/%Y')as fecha_solicitudfactura_x FROM solicitudes_facturacion sf join estados_solicitudfacturacion es on sf.cod_estadosolicitudfacturacion=es.codigo,clientes t  where sf.cod_cliente=t.codigo and sf.cod_simulacion_servicio=$codigo_simulacion order by nro_correlativo desc");//and  sf.cod_estado=1
  $stmt->execute();
  $stmt->bindColumn('codigo', $codigo_facturacion);
  $stmt->bindColumn('cod_simulacion_servicio', $cod_simulacion_servicio);
  $stmt->bindColumn('cod_unidadorganizacional', $cod_unidadorganizacional);
  $stmt->bindColumn('cod_area', $cod_area);
  $stmt->bindColumn('fecha_registro_x', $fecha_registro);
  $stmt->bindColumn('fecha_solicitudfactura_x', $fecha_solicitudfactura);
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
  $stmt->bindColumn('codigo_alterno', $codigo_alterno);
  $stmt->bindColumn('cod_estadosolicitudfacturacion', $codEstado);
  $stmt->bindColumn('estado', $estado);
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
                    <h4 class="card-title"><b>Solicitud de Facturación TCP-TCS</b></h4>
                    <h4 class="card-title" align="center"><b>Propuesta : <?=$nombre_simulacion?> - <?=$name_area_simulacion?></b></h4>
                  </div>
                  <div class="card-body">
                      <table class="table" id="tablePaginator">
                        <thead>
                          <tr>
                            <!-- <th class="text-center"></th>         -->                  
                            <th><small>Of - Area</small></th>
                            <th><small>#Sol.</small></th>
                            <th><small>Responsable</small></th> 
                            <th><small>Código<br>Servicio</small></th>
                            <th><small>Fecha<br>Registro</small></th>
                            <!-- <th><small>Fecha a<br>Facturar</small></th> -->
                            <th style="color:#cc4545;"><small>#Fact.</small></th>                            
                            <th width="8%"><small>Importe (BOB)</small></th>  
                            <th><small>Persona<br>Contacto</small></th>  
                            <th width="35%"><small>Razón Social</small></th>                            
                            <th width="5%"><small>Estado</small></th>    
                            <th width="5%" class="text-right"><small>Actions</small></th>
                          </tr>
                        </thead>
                        <tbody>
                        <?php
                          $index=1;
                          $stringCabecera="";
                          $codigo_fact_x=0;
                          $cont= array();
                          while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                            switch ($codEstado) {
                            case 1:
                             $label='<span class="badge badge-default">';
                            break;
                            case 2:
                             $label='<span class="badge badge-danger">';
                            break;
                            case 3:
                             $label='<span class="badge badge-success">';
                            break;
                            case 4:
                             $label='<span class="badge badge-warning">';
                            break;
                            case 5:
                             $label='<span class="badge badge-warning">';
                            break;
                            case 6:
                             $label='<span class="badge badge-default">';
                            break;
                          }

                            //verificamos si ya tiene factura generada y esta activa                           
                            $stmtFact = $dbh->prepare("SELECT codigo,nro_factura,cod_estadofactura from facturas_venta where cod_solicitudfacturacion=$codigo_facturacion and cod_estadofactura in (1,4)");
                            $stmtFact->execute();
                            $resultSimu = $stmtFact->fetch();
                            $codigo_fact_x = $resultSimu['codigo'];
                            $nro_fact_x = $resultSimu['nro_factura'];
                            $cod_estado_factura_x = $resultSimu['cod_estadofactura'];
                            if ($nro_fact_x==null)$nro_fact_x="-";
                            else $nro_fact_x="F".$nro_fact_x;
                            if($cod_estado_factura_x==4){
                              // $btnEstado="btn-warning";
                              $label='<span class="badge badge-warning">';
                              $estado="FACTURA MANUAL";
                            }
                            //sacamos monto total de la factura para ver si es de tipo factura por pagos
                            $sqlMontos="SELECT codigo,importe,nro_factura from facturas_venta where cod_solicitudfacturacion=$codigo_facturacion and cod_estadofactura=1 ORDER BY codigo desc";
                            // echo $sqlMontos;
                            $stmtFactMontoTotal = $dbh->prepare($sqlMontos);
                            $stmtFactMontoTotal->execute();
                            $importe_fact_x=0;$cont_facturas=0;$cadenaFacturas="";$cadenaCodFacturas="";
                            while ($row_montos = $stmtFactMontoTotal->fetch()){
                              $importe_fact_x+=$row_montos['importe'];
                              $cadenaFacturas.="F".$row_montos['nro_factura']." - ";
                              $cadenaCodFacturas.=$row_montos['codigo'].",";
                              $cont_facturas++;
                            }  

                            $responsable=namePersonal($cod_personal);//nombre del personal
                            $nombre_area=trim(abrevArea($cod_area),'-');//nombre de area
                            $nombre_uo=trim(abrevUnidad($cod_unidadorganizacional),' - ');//nombre de oficina
                            $nombre_contacto=nameContacto($persona_contacto);//nombre del personal
                            //los registros de la factura                            
                            $sqlA="SELECT sf.*,t.Descripcion as nombre_serv from solicitudes_facturaciondetalle sf,cla_servicios t 
                                where sf.cod_claservicio=t.IdClaServicio and sf.cod_solicitudfacturacion=$codigo_facturacion";
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
                            if($cont_facturas>1){                              
                              $estado="FACTURA PARCIAL";
                              $nro_fact_x=trim($cadenaFacturas,' - ');
                            }

                            ?>
                          <tr>
                            <!-- <td align="center"></td> -->
                            <td><small><?=$nombre_uo;?> - <?=$nombre_area;?></small></td>
                            <td class="text-right"><small><?=$nro_correlativo;?></small></td>
                            <td><small><?=$responsable;?></small></td>
                            <td><small><small><?=$codigo_alterno;?></small></small></td>
                            <td><small><?=$fecha_registro;?></small></td>
                            <!-- <td><small><?=$fecha_solicitudfactura;?></small></td> -->
                            <td style="color:#cc4545;"><small><?=$nro_fact_x;?></small></td>                            
                            <td class="text-right"><small><?=formatNumberDec($sumaTotalImporte) ;?></small></td>
                            <td class="text-left"><small><small><?=$nombre_contacto;?></small></small></td>
                            <td><small><small><?=$razon_social;?></small></small></td>
                            <td><?=$label?><small><?=$estado;?></small></span></td>                            
                            <td class="td-actions text-right">
                              <?php
                                // if($globalAdmin==1){
                                if($codEstado==1){
                                  if(isset($_GET['q'])){?>
                                    <a class="btn btn-danger" href='<?=$urlPrintSolicitud;?>?codigo=<?=$codigo_facturacion;?>' target="_blank"><i class="material-icons" title="Imprimir Solicitud">print</i></a>
                                    <a title="Editar Solicitud Facturación" href='<?=$urlRegisterSolicitudfactura;?>&cod_s=<?=$codigo_simulacion?>&cod_f=<?=$codigo_facturacion?>&cod_sw=1&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>' class="btn btn-info">
                                      <i class="material-icons"><?=$iconEdit;?></i>
                                    </a><?php                                
                                  }else{?>
                                    <a class="btn btn-danger" href='<?=$urlPrintSolicitud;?>?codigo=<?=$codigo_facturacion;?>' target="_blank"><i class="material-icons" title="Imprimir Solicitud">print</i></a>
                                    <a title="Editar Solicitud Facturación" href='<?=$urlRegisterSolicitudfactura;?>&cod_s=<?=$codigo_simulacion?>&cod_f=<?=$codigo_facturacion?>&cod_sw=1' class="btn btn-info">
                                      <i class="material-icons"><?=$iconEdit;?></i>
                                    </a><?php      
                                  }?>
                                  <a href='#' rel="tooltip" class="btn btn-warning" onclick="filaTablaAGeneral($('#tablasA_registradas'),<?=$index?>,'<?=$stringCabecera?>')"><i class="material-icons" title="Ver Detalle">settings_applications</i></a><?php 
                                }else{
                                  if($codEstado==4||$codEstado==3||$codEstado==5 ||$codEstado==6){
                                    if($codigo_fact_x>0 && $cod_estado_factura_x==1 && $cont_facturas<2){//print facturas
                                    ?>
                                      <a class="btn btn-success" href='<?=$urlGenerarFacturasPrint;?>?codigo=<?=$codigo_facturacion;?>&tipo=2' target="_blank"><i class="material-icons" title="Imprimir Factura">print</i></a>
                                      <?php 
                                    }elseif($cont_facturas>1){?>
                                      <div class="btn-group dropdown">
                                        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><small>Facturas</small></button>
                                        <div class="dropdown-menu"><?php 
                                          $arrayCodFacturas = explode(",",trim($cadenaCodFacturas,','));
                                          $arrayFacturas = explode(" - ",trim($cadenaFacturas,' - '));
                                          for ($i=0; $i < $cont_facturas; $i++) { $cod_factura_x= $arrayCodFacturas[$i];$nro_factura_x= $arrayFacturas[$i];?>
                                            
                                            <a class="dropdown-item" type="button" href='<?=$urlGenerarFacturasPrint;?>?codigo=<?=$cod_factura_x;?>&tipo=1' target="_blank"><i class="material-icons text-success" title="Imprimir Factura">print</i> Factura <?=$i+1;?> - Nro <?=$nro_factura_x?></a>                                        
                                            <?php 

                                          }?>
                                        </div>
                                      </div> <?php 
                                    }
                                    ?>
                                    <a class="btn btn-danger" href='<?=$urlPrintSolicitud;?>?codigo=<?=$codigo_facturacion;?>' target="_blank"><i class="material-icons" title="Imprimir Solicitud">print</i></a><?php 
                                  }?>
                                  <a href='#' rel="tooltip" class="btn btn-warning" onclick="filaTablaAGeneral($('#tablasA_registradas'),<?=$index?>,'<?=$stringCabecera?>')"><i class="material-icons" title="Ver Detalle">settings_applications</i></a><?php 
                                }
                                // }
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
                // if($globalAdmin==1){
                  if(isset($_GET['q'])){
                   ?><a href="<?=$urlRegisterSolicitudfactura;?>&cod_s=<?=$codigo_simulacion?>&cod_f=0&cod_sw=1&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>" target="_self" class="<?=$buttonNormal;?>">Registrar</a>
                    <a href='<?=$urlList;?>&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>' class="<?=$buttonCancel;?>"><i class="material-icons" title="Volver">keyboard_return</i> Volver </a>
                    <?php 
                  }else{
                   ?><a href="<?=$urlRegisterSolicitudfactura;?>&cod_s=<?=$codigo_simulacion?>&cod_f=0&cod_sw=1" target="_self" class="<?=$buttonNormal;?>">Registrar</a>
                    <a href='<?=$urlList;?>' class="<?=$buttonCancel;?>"><i class="material-icons" title="Volver">keyboard_return</i> Volver </a>
                    <?php 
                  }              
                                   
                // } 
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
                      <!-- <th>Precio(BOB)</th>  
                      <th>Desc(%)</th> 
                      <th>Desc(BOB)</th>  -->
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
        location = "<?=$urlRegisterSolicitudfactura;?>&cod_s=<?=$codigo_simulacion?>&cod_f=0&cod_sw=0&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>"
      </script>
   <?php
  }else{
   ?>
      <script type="text/javascript">
        location = "<?=$urlRegisterSolicitudfactura;?>&cod_s=<?=$codigo_simulacion?>&cod_f=0&cod_sw=0"
      </script>
   <?php
  }
    
}?>