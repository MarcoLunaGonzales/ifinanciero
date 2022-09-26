<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';
require_once 'functions.php';
require_once 'functionsGeneral.php';

$dbh = new Conexion();

$globalNombreGestion=$_SESSION["globalNombreGestion"];
$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalNombreUnidad=$_SESSION['globalNombreUnidad'];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];
//datos registrado de la simulacion en curso

$sql="SELECT sf.*,es.nombre as estado,DATE_FORMAT(sf.fecha_registro,'%d/%m/%Y')as fecha_registro_x,DATE_FORMAT(sf.fecha_solicitudfactura,'%d/%m/%Y')as fecha_solicitudfactura_x FROM solicitudes_facturacion sf join estados_solicitudfacturacion es on sf.cod_estadosolicitudfacturacion=es.codigo where 
  cod_estadosolicitudfacturacion in (3,4) order by codigo desc limit 0,100";
 //echo $sql;
  $stmt = $dbh->prepare($sql);

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
  $stmt->bindColumn('tipo_solicitud', $tipo_solicitud);//1 tcp - 2 capacitacion - 3 servicios - 4 manual - 5 venta de normas, 7 capcitacion estudaintes grupal
  ?>
  <div class="content">
    <div class="container-fluid">
      <div style="overflow-y:scroll;">
          <!-- <div class="row"  > -->
              <!-- <div class="col-md-12"> -->
                <div class="card">
                  <div class="card-header card-header-warning card-header-icon">
                    <div class="card-icon">
                      <i class="material-icons">polymer</i>
                    </div>
                    <h4 class="card-title"><b>Solicitudes de Facturación</b></h4>                    
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
                            <th><small>Razón Social</small></th>
                            <th><small>Concepto</small></th>                            
                            <th><small>Observaciones</small></th>
                            <th><small>Glosa Factura E.</small></th>
                            <!--th style="color:#ff0000;"><small>#Fact</small></th-->
                            <th style="color:#ff0000;" width="6%"><small>Forma<br>Pago</small></th>
                            <th class="text-right"><small>Actions</small></th>                            
                          </tr>
                        </thead>
                        <tbody>
                        <?php
                          $index=1;
                          $codigo_fact_x=0;
                          $cont= array();
                          $cont_pagosParciales= array();
                          while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {// para la parte de facturas parciales, items de sol_Fact
                            $observaciones_string=obtener_string_observaciones($obs_devolucion,$observaciones,$observaciones_2);
                            if($observaciones_2!=""){
                              $observaciones_2="<i class='material-icons text-alert'>info</i>".$observaciones_2;
                            }

                            switch ($codEstado) {
                              case 1:                                
                                // $label='<span style="padding:1;" class="badge badge-default">';
                                $btnEstado="btn-default";
                              break;
                              case 2:                                
                                // $label='<span style="padding:1;" class="badge badge-danger">';
                                $btnEstado="btn-danger";
                              break;
                              case 3:                                
                                // $label='<span style="padding:1;" class="badge badge-success">';
                                $btnEstado="btn-success";
                              break;
                              case 4:                                
                                // $label='<span style="padding:1;" class="badge badge-warning">';
                                $btnEstado="btn-info";
                              break;
                              case 5:                                
                                // $label='<span style="padding:1;" class="badge badge-warning">';
                                $btnEstado="btn-warning";
                              break;
                              case 6:                                
                                // $label='<span style="padding:1;" class="badge badge-default">';
                                $btnEstado="btn-default";
                              break;
                            }


                            //VERIFICAMOS SI YA TIENE FACTURA GENERADA Y ESTA ACTIVA                           
                            $stmtFact = $dbh->prepare("SELECT codigo,nro_factura,cod_estadofactura,razon_social,nit,nro_autorizacion,importe from facturas_venta where cod_solicitudfacturacion=$codigo_facturacion and cod_estadofactura in (1,4)");
                            $codigo_fact_x=0;
                            $nro_fact_x=0;
                            $cod_estado_factura_x=0;
                            $stmtFact->execute();
                            while($resultSimu = $stmtFact->fetch()){
                              $codigo_fact_x = $resultSimu['codigo'];
                              $nro_fact_x = $resultSimu['nro_factura'];
                              $cod_estado_factura_x = $resultSimu['cod_estadofactura'];
                              if ($nro_fact_x==null)$nro_fact_x="-";
                              else $nro_fact_x="F".$nro_fact_x;
                              if($cod_estado_factura_x==4){
                                $btnEstado="btn-warning";
                                $estado="FACTURA MANUAL";                            
                              }
                            }
                            //FIN VERIFICAR FACTURA
                            
                            //sacamos monto total de la factura para ver si es de tipo factura por pagos
                            $sqlMontos="SELECT codigo,importe,nro_factura,cod_estadofactura from facturas_venta where cod_solicitudfacturacion=$codigo_facturacion and cod_estadofactura in (1,4) ORDER BY codigo desc";
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
                              }else{
                                $cadenaFacturas.="F".$row_montos['nro_factura'].",";  
                              }
                              $importe_fact_x+=$row_montos['importe'];
                              
                              $cadenaCodFacturas.=$row_montos['codigo'].",";
                              $cont_facturas++;
                            }                      
                            
                            //sacamos nombre de los detalles
                            $stmtDetalleSol = $dbh->prepare("SELECT cantidad,precio,descripcion_alterna from solicitudes_facturaciondetalle where cod_solicitudfacturacion=$codigo_facturacion");
                            $stmtDetalleSol->execute();
                            $stmtDetalleSol->bindColumn('cantidad', $cantidad);  
                            $stmtDetalleSol->bindColumn('precio', $precio_unitario);
                            $stmtDetalleSol->bindColumn('descripcion_alterna', $descripcion_alterna); 
                            if($tipo_solicitud==2 || $tipo_solicitud==6 || $tipo_solicitud==7){
                              $concepto_contabilizacion="";
                            }else{
                              $concepto_contabilizacion=$codigo_alterno." - ";  
                            }
                            while ($row_det = $stmtDetalleSol->fetch()){
                              $precio=$precio_unitario*$cantidad;
                              $concepto_contabilizacion.=$descripcion_alterna." / ".trim($cadenaFacturas,',').",".trim($cadenaFacturasM,",")." / ".$razon_social."<br>\n";
                              $concepto_contabilizacion.="Cantidad: ".$cantidad." * ".formatNumberDec($precio_unitario)." = ".formatNumberDec($precio)."<br>\n";
                            }
                            $concepto_contabilizacion = (substr($concepto_contabilizacion, 0, 100))."..."; //limite de string
                            $cod_area_simulacion=$cod_area;                           
                            $name_area_simulacion=trim(abrevArea($cod_area_simulacion),'-');
                            // --------
                            
                            $responsable=namePersonal($cod_personal);//nombre del personal
                            // $nombre_tipopago=nameTipoPagoSolFac($cod_tipopago);
                            $string_formaspago=obtnerFormasPago($codigo_facturacion);
                            $nombre_area=trim(abrevArea($cod_area),'-');//nombre del area
                            $nombre_uo=trim(abrevUnidad($cod_unidadorganizacional),' - ');//nombre de la oficina
                            //los registros de la factura
                            
                              $sumaTotalImporte=obtenerSumaTotal_solicitudFacturacion($codigo_facturacion);
                              $saldo=0;
                              $saldo=$sumaTotalImporte-$importe_fact_x;
                              $datos_FacManual=$codigo_facturacion."/0/".$saldo."/".$index."/".$nit."/".$razon_social;//dato para modal
                              if($cont_facturas>1){                              
                                $estado="FACTURA PARCIAL";
                                $nro_fact_x=trim($cadenaFacturas,',');
                              }
                              $cadenaFacturasM=trim($cadenaFacturasM,',');
                              ?>
                              <tr>
                                <td><small><?=$nombre_uo;?> - <?=$nombre_area;?></small></td>
                                <td class="text-right"><small><?=$nro_correlativo;?></small></td>
                                <td class="text-left"><small><?=$responsable;?></small></td>
                                <td class="text-left"><small><?=$codigo_alterno?></small></td>
                                <td><small><?=$fecha_registro;?></small></td>                                                              
                                <td class="text-right"><small><?=formatNumberDec($sumaTotalImporte);?></small></td>                              
                                <td class="text-left text-uppercase font-weight-bold"><small><?=$razon_social;?></small></td>
                                <td class="text-left"><small><small><?=$concepto_contabilizacion;?></small></small></td>                                
                                <td>
                                  <?php if($cod_estado_factura_x==3){
                                      $estadofactura=obtener_nombreestado_factura($cod_estadofactura);?>
                                      <span class="badge badge-dark"><small><?=$estadofactura?></small></span><?php
                                  }else{?><small><?=$observaciones_string;?></small><?php 
                                  }?>
                                </td>
                                <td class="text-left font-weight-bold" style="color:#ff0000;">
                                  <small><?=$observaciones_2;?></small></td>
                                <!--td style="color:#298A08;"><small><?=$nro_fact_x;?><br><span style="color:#DF0101;"><?=$cadenaFacturasM;?></span></small></td-->
                                <td class="text-left" style="color:#ff0000;"><small><small><?=$string_formaspago;?></small></small></td>
                                <td class="td-actions text-right">
                                  <?php
                                  if($globalAdmin==1){
                                      if($codEstado==3){ ?>                                          
                                        <div class="btn-group dropdown">
                                          <button type="button" class="btn <?=$btnEstado?> dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                             <small>Generar</small>
                                          </button>
                                          <div class="dropdown-menu">
                                            <?php   
                                            $patron15 = "/[^a-zA-Z0-9]+/";//solo numeros,letras M y m, tildes y la ñ
                                            $patron1="[\n|\r|\n\r]";
                                            $razon_social = preg_replace($patron1, ", ", $razon_social);//quitamos salto de linea
                                            $razon_social = str_replace('"', " ", $razon_social);//quitamos comillas dobles
                                            $razon_social = str_replace("'", " ", $razon_social);//quitamos comillas simples

                                              $cod_tipopago=obtenemosformaPagoSolfact($codigo_facturacion);//fomra pago
                                              $cod_tipopago_deposito_cuenta=obtenerValorConfiguracion(55);
                                              $cod_tipopago_anticipo=obtenerValorConfiguracion(64);
                                              $cod_tipopago_credito=obtenerValorConfiguracion(48);//creidto
                                              $cuenta_defecto_cliente=obtenerValorConfiguracion(78);//creidto
                                              // $cod_cliente_x=obtenerCodigoCuentaAuxiliarProveedorClienteCuenta(2,$cod_cliente,$cuenta_defecto_cliente);//solo par credito nos sirve
                                              // $cod_tipopago_aux=obtnerFormasPago_codigo($cod_tipopago_credito,$codigo_facturacion);//
                                              
                                                $cont_de_tipos_pago=0;//cuando el contador sea 0 exite deposito y anticipo
                                                $cod_tipopago_aux=obtnerFormasPago_codigo($cod_tipopago_deposito_cuenta,$codigo_facturacion);//verificamos si en nuestra solicitud se hizo alguna distribucion de formas de pago y sacamos el de dep cuenta. devolvera 0 en caso de q no exista
                                                if($cod_tipopago_aux!=0){
                                                  $cont_de_tipos_pago++;
                                                  $cod_tipopago=$cod_tipopago_aux;
                                                  $saldo_dc=obtenerMontoporcentaje_formapago($cod_tipopago_deposito_cuenta,$codigo_facturacion);//
                                                  $datos_FacManual_de=$codigo_facturacion."/0/".$saldo_dc."/".$index."/".$nit."/".$razon_social;//dato para modal
                                                }
                                                // verifiacamos si pertenece a tipo de Pago anticipo
                                                $cod_tipopago_aux=obtnerFormasPago_codigo($cod_tipopago_anticipo,$codigo_facturacion);
                                                if($cod_tipopago_aux!=0){
                                                  $cont_de_tipos_pago++;
                                                  $cod_tipopago=$cod_tipopago_aux;
                                                  $saldo_dc=obtenerMontoporcentaje_formapago($cod_tipopago_anticipo,$codigo_facturacion);//
                                                  $datos_FacManual_anticipo=$codigo_facturacion."/0/".$saldo_dc."/".$index."/".$nit."/".$razon_social;//dato para modal
                                                  if(isset($datos_FacManual_de)){
                                                    $datos_FacManual_de.="/".$saldo_dc;//adicionamos el saldo de la libreta
                                                  }                                              
                                                }
                                                if($cont_de_tipos_pago==2){?>
                                                  <a href='#' title="Generar Factura" class="dropdown-item" onclick="abrirLibretaBancaria('<?=$datos_FacManual_de;?>','<?=$urlGenerarFacturas2;?>','4')">
                                                      <i class="material-icons text-success">receipt</i> Generar Factura
                                                    </a>
                                                    <!-- <a href='#' title="Generar Factura Manual" class="dropdown-item" onclick="abrirLibretaBancaria('<?=$datos_FacManual_de;?>','<?=$urlGenerarFacturas2;?>','5')">
                                                      <i class="material-icons text-info">receipt</i>Generar Factura Manual
                                                    </a> --><?php
                                                }else{                                                
                                                  if($cod_tipopago==$cod_tipopago_deposito_cuenta){//si es deposito se activa la libreta bancaria?>
                                                    <a href='#' title="Generar Factura" class="dropdown-item" onclick="abrirLibretaBancaria('<?=$datos_FacManual_de;?>','<?=$urlGenerarFacturas2;?>','1')">
                                                      <i class="material-icons text-success">receipt</i> Generar Factura
                                                    </a>
                                                    <!-- <a href='#' title="Generar Factura Manual" class="dropdown-item" onclick="abrirLibretaBancaria('<?=$datos_FacManual_de;?>','<?=$urlGenerarFacturas2;?>','3')">
                                                      <i class="material-icons text-info">receipt</i>Generar Factura Manual
                                                    </a> --><?php                                               
                                                  }elseif($cod_tipopago==$cod_tipopago_anticipo){ //echo ?>
                                                    <a href='#' title="Generar Factura" class="dropdown-item" onclick="abrirEstadoCuenta('<?=$datos_FacManual_anticipo;?>','<?=$urlGenerarFacturas2;?>','1','0')">
                                                      <i class="material-icons text-success">receipt</i> Generar Factura
                                                    </a>
                                                    <!-- <a href='#' title="Generar Factura Manual" class="dropdown-item" onclick="abrirEstadoCuenta('<?=$datos_FacManual_anticipo;?>','<?=$urlGenerarFacturas2;?>','3','0')">
                                                      <i class="material-icons text-info">receipt</i>Generar Factura Manual
                                                    </a> -->
                                                    <?php
                                                  }elseif($cod_tipopago==$cod_tipopago_credito){//si es a credito y no tiene cuenta auxiliar
                                                    $datos_sf_credito=$codigo_facturacion."/".$cod_cliente;?>
                                                    <a href='#' title="Registrar Cuenta Auxiliar" class="dropdown-item" onclick="abrirRegistroCuentaAuxiliar('<?=$datos_sf_credito;?>','1')">
                                                      <i class="material-icons text-success">receipt</i>Generar Factura</a><?php 
                                                  }else{
                                                    ?>
                                                    <a href='#' title="Generar Factura" class="dropdown-item" onclick="alerts.showSwal('warning-message-and-confirmation-generar-factura','<?=$urlGenerarFacturas2;?>?codigo=<?=$codigo_facturacion;?>')">
                                                      <i class="material-icons text-success">receipt</i> Generar Factura
                                                    </a>                                                  
                                                    <!-- <button title="Generar Factura Manual" class="dropdown-item" type="button" data-toggle="modal" data-target="#modalFacturaManual" onclick="agregaDatosFactManual('<?=$datos_FacManual;?>')">
                                                      <i class="material-icons text-info">receipt</i> Generar Factura Manual
                                                    </button> --><?php 
                                                  }  
                                                }
                                              
                                              ?>                                             
                                          </div>
                                        </div>                           
                                        <?php 
                                      }
                                  }
                                  ?>
                                <!--   <button class="btn <?=$btnEstado?> btn-sm btn-link" style="padding:0;"><small><?=$estado;?></small></button><br> -->
                                  <div class="btn-group dropdown">
                                  <button type="button" class="btn <?=$btnEstado?> dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                                     <i class="material-icons" >list</i><small><small><?=$estado;?></small></small>
                                  </button>
                                  <div class="dropdown-menu" > 
                                  <?php
                                    if($globalAdmin==1){
                                      if($codEstado==6 || $codEstado==4){?>
                                        <a title="Aceptar Solicitud" href='#'  class="btn btn-default" onclick="alerts.showSwal('warning-message-and-confirmationGeneral','<?=$urlEdit2Sol?>?cod=<?=$codigo_facturacion?>&estado=3&admin=0')">
                                         <i class="material-icons">send</i>
                                        </a><?php                                        
                                      }
                                      $datos_devolucion=$codigo_facturacion."###".$nro_correlativo."###".$codigo_alterno."###1###10###".$urlEdit2Sol."###"; 
                                      $datos_edit=$nro_correlativo."###".$cod_tipopago."###".$codigo_facturacion."###".$nit."###".$razon_social;
                                      ?>
                                      <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modalDevolverSolicitud" onclick="modalDevolverSolicitud('<?=$datos_devolucion;?>')">
                                          <i class="material-icons" title="Devolver Solicitud de Facturación">settings_backup_restore</i>
                                      </button>
                                      <a class="btn btn-danger" href='<?=$urlPrintSolicitud;?>?codigo=<?=$codigo_facturacion;?>' target="_blank"><i class="material-icons" title="Imprimir Solicitud">print</i></a>
                                      <a href="<?=$urlVer_SF;?>?codigo=<?=$codigo_facturacion;?>" target="_blank" class="btn btn-info" title="Ver Solicitud">
                                        <i class="material-icons">remove_red_eye</i>
                                      </a>
                                      <a href='#' title="Archivos Adjuntos" class="btn btn-primary" onclick="abrirArchivosAdjuntos('<?=$datos_FacManual;?>')"><i class="material-icons" ><?=$iconFile?></i></a>
                                      <?php
                                      $variable_configuracion_edit=obtenerValorConfiguracion(76);
                                      if($variable_configuracion_edit==1){
                                        $datos_edit=$nro_correlativo."###".$sumaTotalImporte."###".$codigo_facturacion."###".$nit."###".$razon_social;?>
                                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalEditarSolFac" onclick="modal_editar_sf_conta('<?=$datos_edit;?>')">
                                          <i class="material-icons" title="Editar Forma De Pago">edit</i>
                                        </button>
                                      <?php }
                                    }
                                  ?>
                                </div></div>
                                </td>
                              </tr>
                            <?php //}else{
                            //   $index--;
                            // }
                            ?>                        
                          <?php
                              $index++;
                            }
                          ?>
                        </tbody>
                      </table>
                  </div>
                  <div class="card-footer fixed-bottom col-sm-9">
                    <a href='<?=$urlListHistoricoContabilidad;?>' class="btn btn-info float-right"><i class="material-icons">history</i> Histórico</a>
                  </div>    
                </div>     
              <!-- </div> -->
         <!--  </div>  --> 
      </div>
    </div>
  </div>

<div class="modal fade" id="modalEditarSolFac" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <form id="formSoliFact_modal" class="form-horizontal" action="simulaciones_servicios/ajax_tipopago_edit_conta_save.php" method="post" onsubmit="return valida_xy(this)" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h3 class="modal-title" id="myModalLabel"><b>Porcentaje de Distribución del Ingreso por Forma de Pago</b></h3>
        </div>
        <div class="modal-body">
          <input type="hidden" name="cod_solicitud_e" id="cod_solicitud_e" value="0">        
          <div class="row">
            <label class="col-sm-2 text-right col-form-label" style="color:#424242">Nro. de Solicitud: </label>
            <div class="col-sm-2">
              <div class="form-group">
                <input type="text" name="nro_correlativo_e" id="nro_correlativo_e" class="form-control" readonly="true">
              </div>
            </div>
            <input type="hidden" name="nit_e_sf" id="nit_e_sf" class="form-control" readonly="true">
            <label class="col-sm-1 text-right col-form-label" style="color:#424242">Razón Social: </label>
            <div class="col-sm-6">
              <div class="form-group">
                <input type="text" name="razon_social_e_sf" id="razon_social_e_sf" class="form-control" readonly="true">
              </div>
            </div>    
          </div>        
            <div id="contenedor_formapago_edit">
              
            </div>             
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success" >Guardar</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal"> Volver </button>
        </div>
      </div>
    </form>
  </div>
</div>




<div class="modal fade" id="modalListCuentasAux_sf" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content card">
      <div class="card-header card-header-success card-header-text">
        <div class="card-text">
          <h5>Cuenta Auxiliar Con La Que Se Contabilizará la Factura</h5> 
        </div>
        <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
          <i class="material-icons">close</i>
        </button>
      </div>
      <input type="hidden" name="cod_solicitudfacturacion_cred" id="cod_solicitudfacturacion_cred"/>
      <div class="card-body">
        <div class="row">
          <label class="col-sm-2 col-form-label">Cuenta</label>
          <div class="col-sm-4">
            <div class="form-group">                  
              <select name="cod_cuenta_list" id="cod_cuenta_list" class="selectpicker form-control form-control-sm" data-style="btn btn-primary" data-show-subtext="true" data-live-search="true" >
                <?php 
                $cod_defecto_clientes=obtenerValorConfiguracion(78);
                $sql="SELECT codigo,numero,nombre from plan_cuentas where cuenta_auxiliar=1  order by nombre";
                $stmt = $dbh->prepare($sql);
                $stmt->bindParam(':codigo', $codigo);
                $stmt->bindParam(':nombre', $nombre);
                $stmt->bindParam(':numero', $numero);
                $stmt->execute();
                while ($row = $stmt->fetch()){ ?>
                  <option <?=($cod_defecto_clientes==$row["codigo"])?"selected":"disabled";?> value="<?=$row["codigo"];?>"><?=$row["numero"];?> - <?=$row["nombre"];?></option><?php 
                } 
                ?>
             </select>
            </div>
          </div>
        </div>
        <!-- <div class="row">
          <label class="col-sm-2 col-form-label">Tipo</label>
            <div class="col-sm-4">
                <div class="form-group">
                <select class="selectpicker form-control form-control-sm" name="tipo_x_list" id="tipo_x_list" data-style="<?=$comboColor;?>" required="true" onChange="ajaxTipoProveedorCliente_comprobante(this);">
                <option disabled selected value="">Seleccionar una opcion</option>
              <option disabled value="1">Proveedor</option>  
              <option selected value="2">Cliente</option>  
            </select>
            </div>
              </div>
        </div> -->

        <div id="divCuentaAuxiliar_cliente">
          
        </div>
        <div class="form-group float-right">
            <button type="button" class="btn btn-success btn-round" onclick="generarFacturaCredito('1')">Generar Factura </button>
            <!-- <button type="button" class="btn btn-info btn-round" onclick="generarFacturaCredito('2')">Generar Factura Manual </button> -->
        </div>         
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="modalRegisterCuentasAux_sf" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content card">
      <div class="card-header card-header-warning card-header-text">
        <div class="card-text">
          <h5>Nueva Cuenta Auxiliar</h5> 
        </div>
        <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
          <i class="material-icons">close</i>
        </button>
      </div>
      <input type="hidden" name="cod_solicitudfacturacion_cred" id="cod_solicitudfacturacion_cred"/>
      <input type="hidden" name="cod_cliente_cred" id="cod_cliente_cred"/>
      <div class="card-body">
        <div class="row">
          <label class="col-sm-2 col-form-label">Cuenta</label>
          <div class="col-sm-4">
            <div class="form-group">                  
              <select name="cod_cuenta" id="cod_cuenta" class="selectpicker form-control form-control-sm" data-style="btn btn-primary" data-show-subtext="true" data-live-search="true" >
                <option value="">SELECCIONAR UNA OPCION</option><?php 
                $cod_defecto_clientes=obtenerValorConfiguracion(78);
                $sql="SELECT codigo,numero,nombre from plan_cuentas where cuenta_auxiliar=1  order by nombre";
                $stmt = $dbh->prepare($sql);
                $stmt->bindParam(':codigo', $codigo);
                $stmt->bindParam(':nombre', $nombre);
                $stmt->bindParam(':numero', $numero);
                $stmt->execute();
                while ($row = $stmt->fetch()){ ?>
                  <option <?=($cod_defecto_clientes==$row["codigo"])?"selected":"disabled";?> value="<?=$row["codigo"];?>"><?=$row["numero"];?> - <?=$row["nombre"];?></option><?php 
                } 
                ?>
             </select>
            </div>
          </div>
        </div>

        <div class="row">
          <label class="col-sm-2 col-form-label">Nombre</label>
          <div class="col-sm-7">
          <div class="form-group">
            <input class="form-control" type="text" name="nombre_x" id="nombre_x" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
          </div>
          </div>
        </div>

        <div class="row">
          <label class="col-sm-2 col-form-label">Tipo</label>
            <div class="col-sm-4">
                  <div class="form-group">
                  <select class="selectpicker form-control form-control-sm" name="tipo_x" id="tipo_x" data-style="<?=$comboColor;?>" required="true" onChange="ajaxTipoProveedorCliente_comprobante(this);">
                  <option disabled selected value="">Seleccionar una opcion</option>
                <option disabled value="1">Proveedor</option>  
                <option selected value="2">Cliente</option>  
              </select>
              </div>
                </div>
        </div>

        <div class="row">
          <label class="col-sm-2 col-form-label">Proveedor/Cliente</label>
          <div class="col-sm-7">
          <div class="form-group" id="divProveedorCliente">
            
          </div>
          </div>
        </div>
        <div class="form-group float-right">
            <button type="button" class="btn btn-warning btn-round" onclick="guardarNuevaCuentaAuxi_facturacion()">Guardar</button>
        </div>         
      </div>
    </div>
  </div>
</div>
<?php  require_once 'simulaciones_servicios/modal_facturacion.php';?>
<?php  require_once 'simulaciones_servicios/modal_subir_archivos.php';?>


<!-- para la factura manual -->
<script type="text/javascript">
  $(document).ready(function(){
    $('#guardarFacturaManual').click(function(){      
      var cod_solicitudfacturacion_factmanual=document.getElementById("cod_solicitudfacturacion_factmanual").value;
      var cod_libreta_manual=document.getElementById("cod_libreta_manual").value;
      var cod_estadocuenta_manual=document.getElementById("cod_estadocuenta_manual").value;
      var cuenta_auxiliar_manual=document.getElementById("cuenta_auxiliar_manual").value;

      var nro_factura=$('#nro_factura').val();
      var nro_autorizacion=$('#nro_autorizacion').val();
      var nit_cliente=$('#nit_cliente').val();
      var razon_social=$('#razon_social').val();
      fecha_factura=$('#fecha_factura').val();
      if(nro_factura==null || nro_factura<=0){
        Swal.fire("Informativo!", "Por favor introduzca el Número de Factura.", "warning");
      }else{
        if(nro_autorizacion==null || nro_autorizacion<=0){
          Swal.fire("Informativo!", "Por favor introduzca el Número de Autorización.", "warning");
        }else{
          if(fecha_factura==null || fecha_factura=='' || fecha_factura==' '){
            Swal.fire("Informativo!", "Por favor introduzca la Fecha de Factura", "warning");
          }else{
            if(nit_cliente==null || nit_cliente=='' || nit_cliente==' '){
              Swal.fire("Informativo!", "Por favor introduzca el Nit del cliente", "warning");
            }else{
              if(razon_social==null || razon_social=='' || razon_social==' '){
                Swal.fire("Informativo!", "Por favor introduzca la Razón Social", "warning");
              }else{
                RegistrarFacturaManual(cod_solicitudfacturacion_factmanual,nro_factura,nro_autorizacion,fecha_factura,nit_cliente,razon_social,cod_libreta_manual,cod_estadocuenta_manual,cuenta_auxiliar_manual);
              }          
            }          
          }          
        }
      }      
    });
    $('#rechazarSolicitud').click(function(){
      var q=0;var s=0;var u=0;var v=0;
      var cod_solicitudfacturacion=document.getElementById("cod_solicitudfacturacion").value;
      var estado=document.getElementById("estado").value;
      var admin=document.getElementById("admin").value;
      var direccion=document.getElementById("direccion").value;
      var observaciones=$('#observaciones').val();
      if(observaciones==null || observaciones==0 || observaciones=='' || observaciones==' '){
        Swal.fire("Informativo!", "Por favor introduzca la observación.", "warning");
      }else{        
        registrarRechazoSolicitud(cod_solicitudfacturacion,observaciones,estado,admin,direccion,q,s,u,v);
      }      
    });    
  });
</script>
<script type="text/javascript">
  function valida_xy(f) {
      var ok = true;      
      if(f.elements["total_diferencia_bob_tipopago"].value != 0 )
      {
        var msg = "EL porcentaje de los montos difiere del 100%.";
        ok = false;
      }      
      if(ok == false)    
        Swal.fire("Informativo!",msg, "warning");
      return ok;
    }
</script>
<!-- objeto tipo de pago -->
