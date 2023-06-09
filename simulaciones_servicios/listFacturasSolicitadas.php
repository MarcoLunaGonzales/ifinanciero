<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';

$dbh = new Conexion();
$globalAdmin=$_SESSION["globalAdmin"];

$url_list_siat=obtenerValorConfiguracion(103);



if(isset($_GET['q'])){
  $q=$_GET['q'];
  if(isset($_GET['v']))
    $v=$_GET['v'];
  else $v=0;
  
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

//echo "Q:".$q;
?>
<input type="hidden" name="q" value="<?=$q?>" id="q"/>
<input type="hidden" name="s" value="<?=$s?>" id="s"/>
<input type="hidden" name="u" value="<?=$u?>" id="u"/>
<input type="hidden" name="v" value="<?=$v?>" id="v"/>
<?php

//datos registrado de la simulacion en curso
$sqlDatos="SELECT sf.*,es.nombre as estado,DATE_FORMAT(sf.fecha_registro,'%d/%m/%Y')as fecha_registro_x,DATE_FORMAT(sf.fecha_facturacion,'%d/%m/%Y')as fecha_solicitudfactura_x FROM solicitudes_facturacion sf join estados_solicitudfacturacion es on sf.cod_estadosolicitudfacturacion=es.codigo where sf.cod_personal=$globalUser order by codigo desc limit 50";
//echo $sqlDatos;

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

  // para la busqueda
  $stmtUO = $dbh->prepare("SELECT cod_unidadorganizacional,(select u.nombre from unidades_organizacionales u where u.codigo =cod_unidadorganizacional)as nombre, (select u.abreviatura from unidades_organizacionales u where u.codigo =cod_unidadorganizacional)as abreviatura FROM solicitudes_facturacion  GROUP BY nombre");
  $stmtUO->execute();
  $stmtUO->bindColumn('cod_unidadorganizacional', $codigo_uo_b);
  $stmtUO->bindColumn('nombre', $nombre_uo_b);
  $stmtUO->bindColumn('abreviatura', $abreviatura_uo_b);
  $stmtCliente = $dbh->prepare("SELECT cod_cliente,(SELECT c.nombre from  clientes c where c.codigo=cod_cliente) as nombre from solicitudes_facturacion GROUP BY nombre");
  $stmtCliente->execute();
  $stmtCliente->bindColumn('cod_cliente', $codigo_cli_b);
  $stmtCliente->bindColumn('nombre', $nombre_cli_b);
  ?>
  <!-- EFECTO LOADING -->
  <div class="cargar-ajax d-none">
    <div class="div-loading text-center">
      <h4 class="text-warning font-weight-bold" id="texto_ajax_titulo">Procesando Datos</h4>
      <p class="text-white">Aguard&aacute; un momento por favor</p>  
    </div>
  </div>

  <div class="content">
    <div class="container-fluid">
          <div style="overflow-y:scroll;">
              <!-- <div class="col-md-12"> -->
                <div class="card">
                  <div class="card-header card-header-warning card-header-icon">
                    <div class="card-icon">
                      <i class="material-icons">polymer</i>
                    </div>
                    <h4 class="card-title"><b>Solicitudes de Facturación</b>
                      <button type="button" class="btn btn-info btn-round btn-fab btn-sm proceso_fusion">
                        <i class="material-icons" title="Método para Fusionar SF">merge_type</i>
                      </button>
                    </h4>                    
                  </div>
                  <div class="row">
                    <div class="col-sm-12">
                      <div class="form-group" align="right">
                        <button type="button" class="btn btn-warning btn-round btn-fab btn-sm" data-toggle="modal" data-target="#modalBuscador_solicitudes">
                          <i class="material-icons" title="Buscador Avanzado">search</i>
                        </button>                               
                      </div>
                    </div>
                  </div>
                  <div class="card-body" id="data_solicitudes_facturacion">
                      <table class="table" id="tablePaginator50NoFinder">
                        <thead>
                          <tr>  
                            <th><small>#</small></th>
                            <th><small>Of - Area</small></th>
                            <th><small>#Sol.</small></th>
                            <!--th><small>Responsable</small></th-->
                            <th><small>Codigo<br>Servicio</small></th>
                            <!--th><small>Cliente</small></th-->
                            <th><small><small>Fecha<br>Registro<br>/Factura</small></small></th>
                            <th><small>Importe<br>(BOB)</small></th>                              
                            <th width="15%"><small>Razón Social</small></th>
                            <th width="35%"><small>Concepto</small></th>                            
                            <th width="12%"><small>Observaciones</small></th>
                            <th><small>Concepto Especial Factura</small></th>
                            <th style="color:#ff0000;"><small>#Fact</small></th>
                            <th style="color:#ff0000;" width="6%"><small>Forma<br>Pago</small></th>
                            <th class="text-right"><small>Actions</small></th>
                          </tr>
                        </thead>
                        <tbody >
                        <?php
                          $index=1;
                          $codigo_fact_x=0;

                          $nombreNroFacX="";
                          
                          $cont= array();
                          while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                            $cliente_x=nameCliente($cod_cliente);
                            $observaciones_string=obtener_string_observaciones($obs_devolucion,$observaciones,$observaciones_2);

                            /*GLOSA ESPECIAL PARA LA FACTURA*/
                            if($observaciones_2!=""){
                              $observaciones_2="<i class='material-icons text-alert'>info</i>".$observaciones_2;
                            }


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
                            $sqlFact="SELECT codigo, nro_factura, fecha_factura, cod_estadofactura, IFNULL(idTransaccion_siat,0)as facturasiat from facturas_venta where cod_solicitudfacturacion='$codigo_facturacion' order by codigo desc limit 1";
                            //echo $sqlFact;
                            $stmtFact = $dbh->prepare($sqlFact);

                            $stmtFact->execute();
                            $nro_fact_x="0";
                            $nombreNroFacX="";
                            $cod_estado_factura_x=0;
                            $fechaFacturaX="";
                            $codigo_fact_x=0;
                            $facturaSIAT=0;
                            while($resultSimu = $stmtFact->fetch()){
                              //echo "entra facturas";
                              $codigo_fact_x = $resultSimu['codigo'];
                              $nro_fact_x = $resultSimu['nro_factura'];
                              $cod_estado_factura_x = $resultSimu['cod_estadofactura'];
                              $fechaFacturaX=$resultSimu['fecha_factura'];
                              $facturaSIAT=$resultSimu['facturasiat'];

                              //echo $nro_fact_x;

                              if ($nro_fact_x==null) $nombreNroFacX="-";
                              else $nombreNroFacX="F".$nro_fact_x;
                              if($cod_estado_factura_x==2){
                                $label='<span class="badge badge-warning">';
                                $estado="ANULADO";                              
                              }
                            }

                            /*ARMAMOS LA URL PARA LA VISTA DE LAS FACTURAS*/
                            $urlFacturaImprimir="";
                            if($facturaSIAT==0){
                                $urlFacturaImprimir="simulaciones_servicios/generarFacturasPrint.php?codigo=".$codigo_facturacion."&tipo=2";
                            }else{
                                $urlFacturaImprimir=$url_list_siat."formatoFacturaOnLine.php?codVenta=".$facturaSIAT."";
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
                                // $cadenaFacturasM.="FM".$row_montos['nro_factura'].",";
                                $cadenaFacturas.="FM".$row_montos['nro_factura'].", ";
                                $cadenaCodFacturas.="0,";
                              }elseif($cod_estadofactura==2){
                                $cadenaFacturas.="FA".$row_montos['nro_factura'].", ";  
                                $cadenaCodFacturas.=$row_montos['codigo'].",";
                              }else{
                                $cadenaFacturas.="F".$row_montos['nro_factura'].", ";  
                                $cadenaCodFacturas.=$row_montos['codigo'].",";
                              }
                              $importe_fact_x+=$row_montos['importe'];                            
                              $cont_facturas++;
                            }                  
                            // echo $cont_facturas."<br>";
                            //sacamos nombre de los detalles
                            $stmtDetalleSol = $dbh->prepare("SELECT cantidad,precio,descripcion_alterna,ci_estudiante from solicitudes_facturaciondetalle where cod_solicitudfacturacion=$codigo_facturacion");
                            $stmtDetalleSol->execute();
                            $stmtDetalleSol->bindColumn('cantidad', $cantidad);  
                            $stmtDetalleSol->bindColumn('precio', $precio_unitario);
                            $stmtDetalleSol->bindColumn('descripcion_alterna', $descripcion_alterna);                
                            $stmtDetalleSol->bindColumn('ci_estudiante', $ci_estudiante_x); 
                            $concepto_contabilizacion="";
                          
                            while ($row_det = $stmtDetalleSol->fetch()){
                              
                              if($tipo_solicitud==2 || $tipo_solicitud==6 || $tipo_solicitud==7){                              
                                $concepto_contabilizacion="CI: ".$ci_estudiante_x." / "; 
                              }
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
                           
                            $sumaTotalImporte=obtenerSumaTotal_solicitudFacturacion($codigo_facturacion);

                              if($cont_facturas>1){      
                                // $estado="FACTURA PARCIAL";
                                $nro_fact_x=trim($cadenaFacturas,',');
                              }
                              // $cadenaFacturasM=trim($cadenaFacturasM,',');
                              //datos para el envio de facturas

                              // echo "--".$codigo_facturacion;
                              $cod_factura=verificamosFacturaDuplicada($codigo_facturacion);//codigo de factura
                              if($tipo_solicitud==2 || $tipo_solicitud==6 || $tipo_solicitud==7){
                                $correos_string=obtenerCorreoEstudiante($cod_cliente);
                              }else $correos_string=obtenerCorreosCliente($cod_cliente);
                              if($cod_factura!=''){
                                $nombreNroFacX=obtenerNroFactura($cod_factura);
                              }else{
                                $nombreNroFacX="";
                              }
                              
                              $datos_factura_envio=$cod_factura.'/'.$codigo_facturacion.'/'.$nombreNroFacX.'/'.$correos_string.'/'.$razon_social;
                            ?>
                            <tr>
                              <td>
                                <?php
                                  $array_estadoFusion = [1]; // Facturas en estado: Registrado
                                  if(in_array($codEstado, $array_estadoFusion)){ 
                                ?>
                                <div class="form-group">
                                  <div class="form-check">
                                    <label class="form-check-label">
                                      <input class="form-check-input" type="checkbox" name="fusion[]" value="<?=$codigo_facturacion;?>">
                                      <span class="form-check-sign">
                                        <span class="check"></span>
                                      </span>
                                    </label>
                                  </div>
                                </div>
                                <?php } ?>
                              </td>
                              <td><small><?=$nombre_uo;?> - <?=$nombre_area;?></small></td>
                              <td class="text-right"><small><?=$nro_correlativo;?></small></td>
                              <!--td><small><?=$responsable;?></small></td-->
                              <td><small><?=$codigo_alterno?></small></td>
                              <!--td><small><small><?=$cliente_x?></small></small></td-->
                              <td><small><?=$fecha_registro;?><br><span style="color:#ff0000;"><?=$fecha_solicitudfactura;?></span></small></td>
                              <td class="text-right"><small><?=formatNumberDec($sumaTotalImporte);?></small></td>                            
                              <td><small><small><?=$razon_social;?></small></small></td>
                              <td><small><small><?=$concepto_contabilizacion?></small></small></td>
                              <td><small><?=$observaciones_string;?></small></td>

                              <td class="text-left" style="color:#ff0000;"><small><small><?=$observaciones_2;?></small></small></td>

                              <td style="color:#298A08;"><small><?=$nombreNroFacX;?>
                              </td>
                              
                              
                              <td class="text-left" style="color:#ff0000;"><small><small><?=$string_formaspago;?></small></small></td>
                              <td class="td-actions text-right">                              
                                
                                <?php
                                  if($codigo_fact_x>0 && $cod_estado_factura_x!=2 && $cod_estado_factura_x!=5){//print facturas
                                      // echo "entra";//solo para facturas mayores a uno
                                      if($cont_facturas>1){ //para factura parcial?>
                                        <div class="btn-group dropdown">
                                          <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><small><small><small>Facturas</small></small></small></button>
                                          <div class="dropdown-menu"><?php 
                                            $arrayCodFacturas = explode(",",trim($cadenaCodFacturas,','));
                                            $arrayFacturas = explode(",",trim($cadenaFacturas,','));
                                            for ($i=0; $i < $cont_facturas; $i++) { 
                                              $cod_factura_x= $arrayCodFacturas[$i];
                                              $nro_factura_x= $arrayFacturas[$i];
                                              if($cod_factura_x!=0){?>
                                                <a class="dropdown-item" type="button" href='<?=$urlFacturaImprimir;?>' target="_blank"><i class="material-icons text-success" title="Imprimir Factura">print</i> Factura <?=$i+1;?> - Nro <?=$nro_factura_x?></a>
                                              <?php }else{?>
                                                <a class="dropdown-item" type="button" href='#'><i class="material-icons text-success" title="Factura">list</i> Factura <?=$i+1;?> - Nro <?=$nro_factura_x?></a>
                                              <?php }                                           
                                            }?>
                                          </div>
                                        </div> <?php 
                                      }                                    
                                    }
                                ?>
                                <div class="btn-group dropdown">
                                  <button type="button" class="btn <?=$btnEstado?> dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                                     <i class="material-icons" >list</i><small><small><?=$estado;?></small></small>
                                  </button>
                                  <div class="dropdown-menu" >   
                                <?php        
                                $patron15 = "/[^a-zA-Z0-9]+/";//solo numeros,letras M y m, tildes y la ñ
                                $patron1="[\n|\r|\n\r]";
                                $obs_devolucion = preg_replace($patron1, ", ", $obs_devolucion);//quitamos salto de linea
                                $obs_devolucion = str_replace('"', " ", $obs_devolucion);//quitamos comillas dobles
                                $obs_devolucion = str_replace("'", " ", $obs_devolucion);//quitamos comillas simples
                                // echo $obs_devolucion;
                                if($cod_estado_factura_x!=4){
                                    // echo $codigo_fact_x."-";
                                    if($codigo_fact_x>0 && $cod_estado_factura_x!=2 && $cod_estado_factura_x!=5){
                                      //echo "ENTRA CON FACTURAS".$nro_correlativo;
                                      //print facturas
                                      // echo "entra";
                                      if($cont_facturas<2){
                                        ?>
                                        <a class="btn btn-success" href='<?=$urlFacturaImprimir;?>' target="_blank"><i class="material-icons" title="Imprimir Factura">print</i></a>          
                                        
                                       <?php               
                                      }
                                      
                                    }else{    // generar facturas                                        
                                      //echo "ENTRA SIN FACTURAS".$nro_correlativo;
                                      if($codEstado==1){
                                        $cod_tipopago=obtenemosformaPagoSolfact($codigo_facturacion);//fomra pago
                                        $cod_tipopago_cred=obtenerValorConfiguracion(48);
                                        // echo $cod_tipopago_cred; 
                                        $cod_tipopago_aux=obtnerFormasPago_codigo($cod_tipopago_cred,$codigo_facturacion);//verificamos si en nuestra solicitud se hizo alguna distribucion de formas de pago y sacamos el de credito. devolvera 0 en caso de q no exista
                                        if($cod_tipopago_aux!=0){
                                          $cod_tipopago=$cod_tipopago_aux;
                                        }
                                        
                                        if( ($cod_tipopago==$cod_tipopago_cred) || ($cod_area==38 || $cod_area==39) ){//si es igual a credito cambia de flujo y tambien si es TCP O TCS
                                          if(isset($_GET['q'])){
                                            if($obs_devolucion==null || $obs_devolucion==''){//cuado se hace el rechazo de la fac y volvemos a enviar
                                              ?>                                             
                                              <a title="Enviar a Regional(En Revisión)" onclick="alerts.showSwal('warning-message-and-confirmationGeneral','<?=$urlEdit2Sol?>?cod=<?=$codigo_facturacion?>&estado=6&admin=0&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>')" href='#' class="btn btn-default">
                                               <i class="material-icons">send</i>
                                             </a>
                                              <?php 
                                            }else{                                            
                                              
                                              $datos_devolucion=$codigo_facturacion."###".$nro_correlativo."###".$codigo_alterno."###6###0###".$urlEdit2Sol."###".$obs_devolucion;?>
                                              <button type="button" class="btn btn-default" data-toggle="modal" data-target="#modalReenviarSolicitudDevuelto" onclick="modalReenviarSolicitudDevuelto('<?=$datos_devolucion;?>')">
                                                <i class="material-icons" title="Enviar a Regional(En Revisión)">send</i>
                                              </button><?php
                                            }
                                          }else{
                                            if($obs_devolucion==null || $obs_devolucion==''){//cuado se hace el rechazo de la fac y volvemos a enviar
                                              ?>                                             
                                              <a title="Enviar a Regional(En Revisión)" onclick="alerts.showSwal('warning-message-and-confirmationGeneral','<?=$urlEdit2Sol?>?cod=<?=$codigo_facturacion?>&estado=6&admin=0')" href='#'  class="btn btn-default">
                                                 <i class="material-icons">send</i>
                                              </a>
                                              <?php 
                                            }else{
                                              $datos_devolucion=$codigo_facturacion."###".$nro_correlativo."###".$codigo_alterno."###6###0###".$urlEdit2Sol."###".$obs_devolucion;?>
                                              <button type="button" class="btn btn-default" data-toggle="modal" data-target="#modalReenviarSolicitudDevuelto" onclick="modalReenviarSolicitudDevuelto('<?=$datos_devolucion;?>')">
                                                <i class="material-icons" title="Enviar a Regional(En Revisión)">send</i>
                                              </button><?php
                                            }?>                                             
                                            <?php
                                          } 
                                        }else{
                                          if(isset($_GET['q'])){ 
                                            if($obs_devolucion==null || $obs_devolucion==''){//cuado se hace el rechazo de la fac y volvemos a enviar                                              
                                              ?>                                             
                                              <a title="Enviar a contabilidad(Revisado)" onclick="alerts.showSwal('warning-message-and-confirmationGeneral','<?=$urlEdit2Sol?>?cod=<?=$codigo_facturacion?>&estado=4&admin=0&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>')" href='#' class="btn btn-default">
                                               <i class="material-icons">send</i>
                                             </a>
                                              <?php 
                                            }else{
                                              $datos_devolucion=$codigo_facturacion."###".$nro_correlativo."###".$codigo_alterno."###4###0###".$urlEdit2Sol."###".$obs_devolucion;?>
                                              <button type="button" class="btn btn-default" data-toggle="modal" data-target="#modalReenviarSolicitudDevuelto" onclick="modalReenviarSolicitudDevuelto('<?=$datos_devolucion;?>')">
                                                <i class="material-icons" title="Enviar a contabilidad(Revisado)">send</i>
                                              </button><?php 
                                            }
                                          }else{
                                            if($obs_devolucion==null || $obs_devolucion==''){//cuado se hace el rechazo de la fac y volvemos a enviar                                              
                                              ?>                                             
                                              <a title="Enviar a contabilidad(Revisado)" onclick="alerts.showSwal('warning-message-and-confirmationGeneral','<?=$urlEdit2Sol?>?cod=<?=$codigo_facturacion?>&estado=4&admin=0')" href='#'  class="btn btn-default">
                                                 <i class="material-icons">send</i>
                                              </a>                                              
                                              <?php 
                                            }else{
                                              $datos_devolucion=$codigo_facturacion."###".$nro_correlativo."###".$codigo_alterno."###4###0###".$urlEdit2Sol."###".$obs_devolucion;?>
                                              <button type="button" class="btn btn-default" data-toggle="modal" data-target="#modalReenviarSolicitudDevuelto" onclick="modalReenviarSolicitudDevuelto('<?=$datos_devolucion;?>')">
                                                <i class="material-icons" title="Enviar a contabilidad(Revisado)">send</i>
                                              </button><?php
                                            }
                                          } 
                                        }//fin valida tipo pago credito
                                        
                                        //echo "DEBERIA ENTRAR A EDITAR";
                                        if(isset($_GET['q'])){
                                        ?>
                                          <a title="Editar Solicitud Facturación" href='<?=$urlEditSolicitudfactura;?>&codigo_s=<?=$codigo_facturacion?>&q=<?=$q?>&v=<?=$v?>&s=<?=$s?>&u=<?=$u?>' class="btn btn-success">
                                            <i class="material-icons"><?=$iconEdit;?></i>
                                          </a>
                                        <?php
                                        }else{
                                        ?>
                                          <a title="Editar Solicitud Facturación" href='<?=$urlEditSolicitudfactura;?>&codigo_s=<?=$codigo_facturacion?>' class="btn btn-success">
                                            <i class="material-icons"><?=$iconEdit;?></i>
                                          </a>
                                        <?php 
                                        }
                                        ?>
                                        <?php 
                                      }
                                    }
                                  }else{//factura manual                                   
                                    ?>
                                    <!--button title="Detalles Factura Manual" class="btn btn-success" type="button" data-toggle="modal" data-target="#modalDetalleFacturaManual" onclick="agregaDatosDetalleFactManual('<?=$datos_FacManual;?>')">
                                      <i class="material-icons">list</i>
                                    </button-->                                    
                                     <?php 
                                  }

                                  if($codEstado!=2){?>
                                    <a class="btn btn-danger" href='<?=$urlPrintSolicitud;?>?codigo=<?=$codigo_facturacion;?>' target="_blank"><i class="material-icons" title="Imprimir Solicitud">print</i></a>
                                    <a href="<?=$urlVer_SF;?>?codigo=<?=$codigo_facturacion;?>" target="_blank" class="btn btn-info" title="Ver Solicitud">
                                      <i class="material-icons">remove_red_eye</i>
                                    </a>
                                    <a href='#' title="Archivos Adjuntos" class="btn btn-primary" onclick="abrirArchivosAdjuntos('<?=$datos_otros;?>')"><i class="material-icons" ><?=$iconFile?></i></a>
                                  <?php }
                                  if($codEstado==1){
                                    if(isset($_GET['q'])){?>
                                      <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmationGeneral','<?=$urlAnular_SoliciutdFacturacion;?>?codigo=<?=$codigo_facturacion;?>&q=<?=$q?>&v=<?=$v?>&s=<?=$s?>&u=<?=$u?>')">
                                        <i class="material-icons" title="Anular Solicitud Facturación">delete</i>
                                      </button>
                                    <?php }else{?>
                                      <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmationGeneral','<?=$urlAnular_SoliciutdFacturacion;?>?codigo=<?=$codigo_facturacion;?>')">
                                        <i class="material-icons" title="Anular Solicitud Facturación">delete</i>
                                      </button>
                                    <?php }
                                  }
                                  if($codigo_fact_x>0 && $cod_estado_factura_x==3 && $cont_facturas<2){
                                    $cadenaFacturas=trim($cadenaFacturas,",");
                                    $cadenaCodFacturas=trim($cadenaCodFacturas,",");
                                    $correosEnviados=obtenerCorreosEnviadosFactura($cadenaCodFacturas);
                                    if($correosEnviados!=""){
                                      $correosEnviados="\nFactura enviada a: \n *".$correosEnviados;

                                    }
                                    $correosEnviados = preg_replace("[\n|\r|\n\r]", ", ", $correosEnviados);     
                                    $correosEnviados=trim($correosEnviados,"Factura enviada a:,");

                                    $datos_envio_correo=$cadenaFacturas."######".$correosEnviados;?>
                                    <a href='#' title="Información de envío de Correo" class="btn btn-primary" onclick="modal_info_enviocorreo_f('<?=$datos_envio_correo;?>')"><i class="material-icons" >email</i></a>
                                   
                                  <?php }
                                ?>
                              </div></div>
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
                    if(isset($_GET['q'])){?>
                      <a href="<?=$urlRegister_solicitudfacturacion_manual;?>&q=<?=$q?>&v=<?=$v?>&s=<?=$s?>&u=<?=$u?>" class="btn btn-primary">SF Manual</a>
                      <a href="<?=$urlListSolicitud_facturacion_normas;?>&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>" class="btn btn-warning">SF Normas</a>

                      <a href="<?=$urlSolicitudfactura_estudiante;?>&q=<?=$q?>&u=<?=$u?>&r=<?=$v?>&s=<?=$s?>" class="btn btn-success">SF Estudiantes</a>
                      <a href="<?=$urlSolicitudfactura_empresa;?>&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&r=<?=$v?>" class="btn btn-danger">SF Empresas</a>                      
                      <?php 
                    }else{?>
                      <a href="<?=$urlRegister_solicitudfacturacion_manual;?>" class="btn btn-primary">SF Manual</a>
                      <a href="<?=$urlListSolicitud_facturacion_normas;?>" class="btn btn-warning">SF Normas</a>
                      <a href="<?=$urlSolicitudfactura_estudiante;?>" class="btn btn-success">SF Estudiantes</a>
                      <a href="<?=$urlSolicitudfactura_empresa;?>" class="btn btn-danger">SF Empresas</a>                      
                      <?php 
                    }              
                  ?>
                </div>        
              <!-- </div> -->
          </div>  
    </div>
  </div>

<?php  require_once 'simulaciones_servicios/modal_facturacion.php';?>
<?php  require_once 'simulaciones_servicios/modal_facturacion_2.php';?>
<?php  require_once 'simulaciones_servicios/modal_subir_archivos.php';?>
<!-- para modal -->
<script type="text/javascript">
  $(document).ready(function(){
    $('#EnviarCorreo').click(function(){    
      codigo_facturacion=document.getElementById("codigo_facturacion_sf").value;
      cod_solicitudfacturacion=document.getElementById("cod_solicitudfacturacion_sf").value;
      nro_factura=document.getElementById("nro_factura_sf").value;
      correo_copia=$('#correo_copia').val();
      if(correo_copia!=""){
        correo_destino=$('#correo_destino_sf').val()+","+correo_copia;
      }else{
        correo_destino=$('#correo_destino_sf').val();        
      } 
      
      // asunto=$('#asunto').val();
      // mensaje=$('#mensaje').val();
      asunto=null;
      mensaje=null;
      if(correo_destino==null || correo_destino == "" ||correo_destino == 0){
        // alert("Por Favor Agregue Un correo para el envío de la Factura!");
        Swal.fire("Informativo!", "Por Favor Agregue Un correo válido para el envío de la Factura!", "warning");
      }else{
        EnviarCorreoAjaxSolFac(codigo_facturacion,nro_factura,cod_solicitudfacturacion,correo_destino,asunto,mensaje);  
      }
      
    });
    $('#rechazarSolicitud').click(function(){      
      var cod_solicitudfacturacion=document.getElementById("cod_solicitudfacturacion").value;
      var estado=document.getElementById("estado").value;
      var admin=document.getElementById("admin").value;
      var direccion=document.getElementById("direccion").value;
      var observaciones=$('#observaciones').val();
      if(observaciones==null || observaciones==0 || observaciones=='' || observaciones==' '){
        Swal.fire("Informativo!", "Por favor introduzca la observación.", "warning");
      }else{        
        registrarRechazoSolicitud(cod_solicitudfacturacion,observaciones,estado,admin,direccion);
      }      
    });     
    $('#ReenviarSolicitud').click(function(){      
      var q=document.getElementById("q").value;
      var s=document.getElementById("s").value;
      var u=document.getElementById("u").value;
      var v=document.getElementById("v").value;

      var cod_solicitudfacturacion=document.getElementById("cod_solicitudfacturacion_r").value;
      var estado=document.getElementById("estado_r").value;
      var admin=document.getElementById("admin_r").value;
      var observaciones=$('#observaciones_r').val();
      var direccion=document.getElementById("direccion_r").value;
      if(observaciones==null || observaciones==0 || observaciones=='' || observaciones==' '){
        Swal.fire("Informativo!", "Por favor introduzca la observación.", "warning");
      }else{        
        registrarRechazoSolicitud(cod_solicitudfacturacion,observaciones,estado,admin,direccion,q,s,u,v);
      }      
    }); 
  });
</script>

<!-- Proceso de Fusión -->
<script>
  $('.proceso_fusion').on('click', function(){
    let array_sf = [];
    // Obtener todos los checkboxes con el atributo name="fusion[]"
    var checkboxes = $('input[type="checkbox"][name="fusion[]"]');
    // Iterar sobre los checkboxes
    checkboxes.each(function() {
      // Verificar si el checkbox actual está seleccionado
      if ($(this).is(':checked')) {
        array_sf.push($(this).val());
      }
    });
    if(array_sf.length > 1){
      swal({
          title: '¿Estás seguro?',
          text: 'Se procedera a la fusion de las SF seleccionadas.',
          type: 'warning',
          showCancelButton: true,
          confirmButtonClass: 'btn btn-success',
          cancelButtonClass: 'btn btn-danger',
          confirmButtonText: 'Si',
          cancelButtonText: 'No',
          buttonsStyling: false
      }).then((result) => {
          if (result.value) {
              
              let formData = new FormData();
              formData.append('array_sf', array_sf);

              $(".cargar-ajax").removeClass("d-none");
              $.ajax({
                url:"simulaciones_servicios/saveFusion.php",
                type:"POST",
                contentType: false,
                processData: false,
                data: formData,
                success:function(response){
                  let resp = JSON.parse(response);
                  $(".cargar-ajax").addClass("d-none");// Mensaje
                  if(resp.status){
                      Swal.fire({
                          type: 'success',
                          title: 'Correcto!',
                          text: resp.message,
                          showConfirmButton: false,
                          timer: 1500
                      });
                      
                      setTimeout(function(){
                          location.reload()
                      }, 1550);
                  }else{
                    Swal.fire({
                        type: 'error',
                        title: 'Error!',
                        text: resp.message
                    });
                    // Swal.fire('ERROR!','El proceso tuvo un problema!. Contacte con el administrador!','error'); 
                  }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        type: 'error',
                        title: 'Error!',
                        text: 'Ocurrió un error inesperado, verifique con el soporte técnico.'
                    });
                }
              });
          }
      });
    }else{
      $(".cargar-ajax").addClass("d-none");// Mensaje
      swal({
        title: 'Ops!',
        text: 'Falta seleccionar por lo menos dos SF para continuar con el proceso de fusión',
        type: 'warning',
        confirmButtonClass: 'btn btn-warning',
        confirmButtonText: 'Aceptar',
        buttonsStyling: false
      });
    }
  });
</script>