<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';

$dbh = new Conexion();
$globalAdmin=$_SESSION["globalAdmin"];



if(isset($_GET['q'])){
  $q=$_GET['q'];
  $v=$_GET['v'];
  $s=$_GET['s'];
  $u=$_GET['u'];
  $globalUser=$_GET['q'];
}else{
  $globalUser=$_SESSION["globalUser"];
  $q=0;
  $v=0;
  $s=0;
  $u=0;
}
if(isset($_GET['s'])){
  $s=$_GET['s'];
  // echo $s;
  // $sqlFilter1 = str_replace("IdOficina", "p.cod_unidadorganizacional", $_GET['s']);
  // $sqlFilter2 = "and ".str_replace("IdArea", "p.cod_area", $sqlFilter1);
  $arraySql=explode("IdArea",$s);
  $codigoArea='0';  
  if(isset($arraySql[1])){    
    $codigoArea=trim($arraySql[1]);
  }   
  if($codigoArea=='0'){    
    $sqlAreas="and sf.cod_area=0";    
  }else{
    $sqlAreas="and sf.cod_area ".$codigoArea;  
  } 
}else{
  $globalArea=$_SESSION["globalArea"];
  $sqlAreas="and sf.cod_area =".$globalArea;
}

// echo $sqlAreas;
?>
<input type="hidden" name="q" value="<?=$q?>" id="q"/>
<input type="hidden" name="s" value="<?=$s?>" id="s"/>
<input type="hidden" name="u" value="<?=$u?>" id="u"/>
<input type="hidden" name="v" value="<?=$v?>" id="v"/>

<?php

//datos registrado de la simulacion en curso
$sqlDatos="SELECT sf.*,es.nombre as estado,DATE_FORMAT(sf.fecha_registro,'%d/%m/%Y')as fecha_registro_x,DATE_FORMAT(sf.fecha_solicitudfactura,'%d/%m/%Y')as fecha_solicitudfactura_x FROM solicitudes_facturacion sf join estados_solicitudfacturacion es on sf.cod_estadosolicitudfacturacion=es.codigo where sf.cod_estadosolicitudfacturacion in (2,3,4,5) $sqlAreas order by codigo desc ";
// echo $sqlDatos;
  $stmt = $dbh->prepare($sqlDatos);

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
  $stmt->bindColumn('observaciones_2', $observaciones_2);
  $stmt->bindColumn('cod_estadosolicitudfacturacion', $codEstado);
  $stmt->bindColumn('estado', $estado);
  $stmt->bindColumn('nro_correlativo', $nro_correlativo);
  $stmt->bindColumn('persona_contacto', $persona_contacto);
  $stmt->bindColumn('codigo_alterno', $codigo_alterno);
  $stmt->bindColumn('obs_devolucion', $obs_devolucion);
  $stmt->bindColumn('tipo_solicitud', $tipo_solicitud);//1 tcp - 2 capacitacion - 3 servicios - 4 manual - 5 venta de normas


  // $fecha_actual_cH=date('Y-m-d H:i:s');
  
  // $fecha_actual=date($fecha_actual_cH, strtotime("Y-m-d")); // gives 201101
  // echo $fecha_actual;
  ?>
  <div class="content">
    <div class="container-fluid">
          <div style="overflow-y:scroll;">
              <!-- <div class="col-md-12"> -->
                <div class="card">
                  <div class="card-header card-header-warning card-header-icon">
                    <div class="card-icon">
                      <i class="material-icons">polymer</i>
                    </div>
                    <h4 class="card-title"><b>Solicitudes de Facturación Areas</b></h4>                    
                  </div>
                  <div class="card-body">
                      <table class="table" id="tablePaginator">
                        <thead>
                          <tr>
                            <th><small>Of - Area</small></th>
                            <th><small>#Sol.</small></th>
                            <th><small>Responsable</small></th>
                            <th><small>Codigo<br>Servicio</small></th>                            
                            <th><small>Fecha<br>Registro</small></th>
                            <th><small>Importe<br>(BOB)</small></th>                              
                            <th width="15%"><small>Razón Social</small></th>
                            <th width="35%"><small>Concepto</small></th>                            
                            <th width="12%"><small>Observaciones</small></th>
                            <th style="color:#ff0000;"><small>#Fact</small></th>
                            <th style="color:#ff0000;" width="6%"><small>Forma<br>Pago</small></th>
                            <th class="text-right"><small>Actions</small></th>
                          </tr>
                        </thead>
                        <tbody>
                        <?php
                          $index=1;
                          $codigo_fact_x=0;
                          $cont= array();
                          while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {                            
                            $observaciones_string=obtener_string_observaciones($obs_devolucion,$observaciones,$observaciones_2);
                            $datos_otros=$codigo_facturacion."/0/0/0/".$nit."/".$razon_social;//dato 
                            switch ($codEstado) {
                              case 1:                                
                                $label='<span style="padding:1;" class="badge badge-default">';
                                $btnEstado="btn-default";
                              break;
                              case 2:                                
                                $label='<span style="padding:1;" class="badge badge-danger">';
                                $btnEstado="btn-danger";
                              break;
                              case 3:                                
                                $label='<span style="padding:1;" class="badge badge-success">';
                                $btnEstado="btn-success";
                              break;
                              case 4:                                
                                $label='<span style="padding:1;" class="badge badge-info">';
                                $btnEstado="btn-info";
                              break;
                              case 5:                                
                                $label='<span style="padding:1;" class="badge badge-warning">';
                                $btnEstado="btn-warning";
                              break;
                              case 6:                                
                                $label='<span style="padding:1;" class="badge badge-default">';
                                $btnEstado="btn-default";
                              break;
                            }
                            //verificamos si ya tiene factura generada y esta activa                           
                            $stmtFact = $dbh->prepare("SELECT codigo, nro_factura, cod_estadofactura, razon_social, nit, nro_autorizacion, importe, cod_comprobante from facturas_venta where cod_solicitudfacturacion=$codigo_facturacion order by codigo desc limit 1");
                            $stmtFact->execute();
                            $resultSimu = $stmtFact->fetch();
                            $codigo_fact_x = $resultSimu['codigo'];
                            $nro_fact_x = $resultSimu['nro_factura'];
                            $cod_estado_factura_x = $resultSimu['cod_estadofactura'];
                            $nit_x = $resultSimu['nit'];
                            $razon_social_x = $resultSimu['razon_social'];
                            $nro_autorizacion_x = $resultSimu['nro_autorizacion'];
                            $importe_x = $resultSimu['importe'];
                            $cod_comprobante_x = $resultSimu['cod_comprobante'];
                            if ($nro_fact_x==null)$nro_fact_x="-";
                            else $nro_fact_x="F".$nro_fact_x;
                            if($cod_estado_factura_x==4){
                              // $btnEstado="btn-warning";
                              $label='<span class="badge badge-warning">';
                              $estado="FACTURA MANUAL";

                              $cliente_x=nameCliente($cod_cliente);                              
                              $datos_FacManual=$cliente_x."/".$razon_social_x."/".$nit_x."/".$nro_fact_x."/".$nro_autorizacion_x."/".$importe_x;
                            }

                            //sacamos monto total de la factura para ver si es de tipo factura por pagos
                            $sqlMontos="SELECT codigo,importe,nro_factura,cod_estadofactura from facturas_venta where cod_solicitudfacturacion=$codigo_facturacion ORDER BY codigo desc";
                            // echo $sqlMontos;
                            $stmtFactMontoTotal = $dbh->prepare($sqlMontos);
                            $stmtFactMontoTotal->execute();
                            $importe_fact_x=0;$cont_facturas=0;$cadenaFacturas="";$cadenaFacturasM="";$cadenaCodFacturas="";
                            while ($row_montos = $stmtFactMontoTotal->fetch()){
                              $cod_estadofactura=$row_montos['cod_estadofactura'];
                              if($cod_estadofactura==4){
                                $btnEstado="btn-warning";
                                $estado="FACTURA MANUAL";
                                $cadenaFacturasM.="FM".$row_montos['nro_factura'].",";
                              }elseif($cod_estadofactura==2){
                                $cadenaFacturas.="FA".$row_montos['nro_factura'].",";  
                              }else{
                                $cadenaFacturas.="F".$row_montos['nro_factura'].",";  
                              }
                              $importe_fact_x+=$row_montos['importe'];
                              $cadenaCodFacturas.=$row_montos['codigo'].",";
                              $cont_facturas++;
                            }                  
                            // echo $cont_facturas."<br>";
                            //sacamos nombre de los detalles
                            $stmtDetalleSol = $dbh->prepare("SELECT cantidad,precio,descripcion_alterna from solicitudes_facturaciondetalle where cod_solicitudfacturacion=$codigo_facturacion");
                            $stmtDetalleSol->execute();
                            $stmtDetalleSol->bindColumn('cantidad', $cantidad);  
                            $stmtDetalleSol->bindColumn('precio', $precio_unitario);
                            $stmtDetalleSol->bindColumn('descripcion_alterna', $descripcion_alterna);                
                            $concepto_contabilizacion="";
                            // if($tipo_solicitud==2 || $tipo_solicitud==6 || $tipo_solicitud==7){
                            //   $concepto_contabilizacion="";
                            // }else{
                            //   $concepto_contabilizacion=$codigo_alterno." - ";  
                            // }
                            while ($row_det = $stmtDetalleSol->fetch()){
                              $precio=$precio_unitario*$cantidad;
                              // $concepto_contabilizacion.=$descripcion_alterna." / F ".$nro_fact_x." / ".$razon_social."<br>\n";
                              // $concepto_contabilizacion.=$descripcion_alterna." / ".trim($cadenaFacturas,',').",".trim($cadenaFacturasM,",")." / ".$razon_social."<br>\n";
                              // $concepto_contabilizacion.="Cantidad: ".$cantidad." * ".formatNumberDec($precio_unitario)." = ".formatNumberDec($precio)."<br>\n";
                              $concepto_contabilizacion.=$descripcion_alterna."<br>\n";
                            }
                            $concepto_contabilizacion = (substr($concepto_contabilizacion, 0, 100)); //limite de string
                            
                            $cod_area_simulacion=$cod_area;
                            $nombre_simulacion='OTROS';
                            $name_area_simulacion=trim(abrevArea($cod_area_simulacion),'-');
                            // --------
                            $responsable=namePersonal_2($cod_personal);//nombre del personal
                            // $nombre_tipopago=nameTipoPagoSolFac($cod_tipopago);//
                            //pude ver el caso que vea distibucion de formas de pago
                            $string_formaspago=obtnerFormasPago($codigo_facturacion);
                            $nombre_area=trim(abrevArea($cod_area),'-');//nombre del area
                            $nombre_uo=trim(abrevUnidad($cod_unidadorganizacional),' - ');//nombre de la oficina

                            //los registros de la factura
                            $dbh1 = new Conexion();
                            $sqlA="SELECT sf.*,(select t.Descripcion from cla_servicios t where t.IdClaServicio=sf.cod_claservicio) as nombre_serv from solicitudes_facturaciondetalle sf where sf.cod_solicitudfacturacion=$codigo_facturacion";
                            $stmt2 = $dbh1->prepare($sqlA);                                   
                            $stmt2->execute(); 
                            $nc=0;
                            $sumaTotalMonto=0;
                            $sumaTotalDescuento_por=0;
                            $sumaTotalDescuento_bob=0;
                            while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                              // $dato = new stdClass();//obejto
                              $codFila=(int)$row2['codigo'];
                              $cod_claservicioX=trim($row2['nombre_serv']);
                              $cantidadX=trim($row2['cantidad']);
                              $precioX=(trim($row2['precio'])*$cantidadX)+trim($row2['descuento_bob']);
                              $descuento_porX=trim($row2['descuento_por']);
                              $descuento_bobX=trim($row2['descuento_bob']);
                              $descripcion_alternaX=trim($row2['descripcion_alterna']);
                              // $dato->codigo=($nc+1);
                              // $dato->cod_facturacion=$codFila;
                              // $dato->serviciox=$cod_claservicioX;
                              // $dato->cantidadX=$cantidadX;
                              // $dato->precioX=$precioX;
                              // $dato->descuento_porX=$descuento_porX;
                              // $dato->descuento_bobX=$descuento_bobX;
                              // $dato->descripcion_alternaX=$descripcion_alternaX;
                              // $datos[$index-1][$nc]=$dato;                           
                              // $nc++;
                              $sumaTotalMonto+=$precioX;
                              $sumaTotalDescuento_por+=$descuento_porX;
                              $sumaTotalDescuento_bob+=$descuento_bobX;
                            }
                            $sumaTotalImporte=$sumaTotalMonto-$sumaTotalDescuento_bob;
                            $cont[$index-1]=$nc;
                            // $stringCabecera=$nombre_uo."##".$nombre_area."##".$nombre_simulacion."##".$name_area_simulacion."##".$fecha_registro."##".$fecha_solicitudfactura."##".$nit."##".$razon_social;
                            

                            if($cont_facturas>1){      
                                $estado="FACTURA PARCIAL";
                                $nro_fact_x=trim($cadenaFacturas,',');
                              }
                              $cadenaFacturasM=trim($cadenaFacturasM,',');
                              //datos para el envio de facturas
                              $cod_factura=verificamosFacturaDuplicada($codigo_facturacion);//codigo de factura
                              if($tipo_solicitud==2 || $tipo_solicitud==6 || $tipo_solicitud==7){
                                $correos_string=obtenerCorreoEstudiante($cod_cliente);
                              }else $correos_string=obtenerCorreosCliente($cod_cliente);
                              $nro_factura=obtenerNroFactura($cod_factura);
                              $datos_factura_envio=$cod_factura.'/'.$codigo_facturacion.'/'.$nro_factura.'/'.$correos_string.'/'.$razon_social;
                          ?>
                          <tr>
                            <!-- <td align="center"><?=$index;?></td> -->
                            <td><small><?=$nombre_uo;?> - <?=$nombre_area;?></small></td>
                            <td class="text-right"><small><?=$nro_correlativo;?></small></td>
                            <td><small><?=$responsable;?></small></td>
                            <td><small><?=$codigo_alterno?></small></td>
                            <td><small><?=$fecha_registro;?></small></td>
                            <td class="text-right"><small><?=formatNumberDec($sumaTotalImporte);?></small></td>                            
                            <td><small><small><?=$razon_social;?></small></small></td>
                            <td><small><small><?=$concepto_contabilizacion?></small></small></td>
                            <td>
                              <?php if($cod_estado_factura_x==3){
                                $estadofactura=obtener_nombreestado_factura($cod_estadofactura);
                                ?>
                                  <span class="badge badge-dark"><small><?=$estadofactura?></small></span><?php
                                }else{?><small><?=$observaciones_string;?></small><?php 
                              }?>
                            </td>
                            <td style="color:#298A08;"><small><?=$nro_fact_x;?><br><span style="color:#DF0101;"><?=$cadenaFacturasM;?></span></small></td>
                            <td class="text-left" style="color:#ff0000;"><small><small><?=$string_formaspago;?></small></small></td>
                            <td class="td-actions text-right">                              
                              <!-- <button class="btn <?=$btnEstado?> btn-sm btn-link"><small><?=$estado;?></small></button><br> -->
                              <div class="btn-group dropdown">
                                <button type="button" class="btn <?=$btnEstado?> dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                                   <i class="material-icons" >list</i><small><?=$estado;?></small>
                                </button>
                                <div class="dropdown-menu" >  
                                  <a class="btn btn-danger" href='<?=$urlPrintSolicitud;?>?codigo=<?=$codigo_facturacion;?>' target="_blank"><i class="material-icons" title="Imprimir Solicitud Facturación">print</i></a>
                                  <a href="<?=$urlVer_SF;?>?codigo=<?=$codigo_facturacion;?>" target="_blank" class="btn btn-info" title="Ver Solicitud">
                                    <i class="material-icons">remove_red_eye</i>
                                  </a>
                                  <a href='#' title="Archivos Adjuntos" class="btn btn-primary" onclick="abrirArchivosAdjuntos('<?=$datos_otros;?>')"><i class="material-icons" ><?=$iconFile?></i></a>
                                </div>
                              </div>
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
               
              <!-- </div> -->
          </div>  
    </div>
  </div>
