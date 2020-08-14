<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';

$dbh = new Conexion();
$globalAdmin=$_SESSION["globalAdmin"];
$globalPersonal=$_SESSION["globalUser"];


  //datos registrado de la simulacion en curso
  $stmt = $dbh->prepare("SELECT f.*,DATE_FORMAT(f.fecha_factura,'%d/%m/%Y')as fecha_factura_x,DATE_FORMAT(f.fecha_factura,'%H:%i:%s')as hora_factura_x,(select s.abreviatura from unidades_organizacionales s where s.cod_sucursal=f.cod_sucursal limit 1)as sucursal
 from facturas_venta f where cod_estadofactura in (1,2,3) order by  f.fecha_factura desc limit 50");
  $stmt->execute();
  $stmt->bindColumn('codigo', $codigo_factura);
  $stmt->bindColumn('cod_sucursal', $cod_sucursal);
  $stmt->bindColumn('cod_area', $cod_area);
  $stmt->bindColumn('cod_solicitudfacturacion', $cod_solicitudfacturacion);  
  $stmt->bindColumn('fecha_factura_x', $fecha_factura);
  $stmt->bindColumn('fecha_factura', $fecha_factura_xy);
  $stmt->bindColumn('hora_factura_x', $hora_factura);
  $stmt->bindColumn('fecha_limite_emision', $fecha_limite_emision);
  $stmt->bindColumn('cod_tipopago', $cod_tipopago);
  $stmt->bindColumn('cod_cliente', $cod_cliente);
  $stmt->bindColumn('cod_personal', $cod_personal);
  $stmt->bindColumn('razon_social', $razon_social);
  $stmt->bindColumn('nit', $nit);
  $stmt->bindColumn('cod_dosificacionfactura', $cod_dosificacionfactura);
  $stmt->bindColumn('nro_factura', $nro_factura);
  $stmt->bindColumn('nro_autorizacion', $nro_autorizacion);
  $stmt->bindColumn('codigo_control', $codigo_control);
  $stmt->bindColumn('importe', $importe);
  $stmt->bindColumn('observaciones', $observaciones);
  $stmt->bindColumn('cod_estadofactura', $cod_estadofactura);
  $stmt->bindColumn('sucursal', $sucursal);
  // $stmt->bindColumn('cliente', $cliente);
  // $stmt->bindColumn('estadofactura', $estadofactura);
  $stmt->bindColumn('cod_comprobante', $cod_comprobante);

  date_default_timezone_set('America/La_Paz');
  if(isset($_GET['interno'])){
    $interno=$_GET['interno'];
  }else{
    $interno=0;
  }
  ?>
  <input type="hidden" name="interno" value="<?=$interno?>" id="interno"/>
  <div class="content">
    <div class="container-fluid">
      <div style="overflow-y:scroll;">
          <div class="row">
              <div class="col-md-12">
                <div class="card">
                  <div class="card-header card-header-warning card-header-icon">
                    <div class="card-icon">
                      <i class="material-icons">polymer</i>
                    </div>
                    <h4 class="card-title"><b>Facturas Generadas</b></h4>                    
                  </div>
                  <div class="row">
                    <div class="col-sm-12">
                      <div class="form-group" align="right">
                          <button type="button" class="btn btn-warning btn-round btn-fab btn-sm" data-toggle="modal" data-target="#modalBuscadorFacturas">
                              <i class="material-icons" title="Buscador Avanzado">search</i>
                          </button>                               
                      </div>
                    </div>
                  </div>
                  <div class="card-body" id="data_facturas_generadas">
                    <table class="table" id="tablePaginator50NoFinder">
                      <thead>
                        <tr>
                          <!-- <th class="text-center"></th> -->
                          <th width="6%">#Fac</th>
                          <th width="10%">Personal</th>
                          <th width="8%">Fecha<br>Factura</th>
                          <th width="25%">Razón Social</th>
                          <th width="9%">Nit</th>
                          <th width="8%">Importe<br>Factura</th>
                          <th>Detalle</th>
                          <th width="12%">Observaciones</th>
                          <th width="10%" class="text-right">Opciones</th>                            
                        </tr>
                      </thead>                        
                      <tbody>
                      <?php
                        $index=1;
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                          //para la anulacion de facturas
                          if(isset($_GET['interno'])){
                            $sw_anular=true;
                            // echo "aqui";
                          }else{
                            $dias_alargo=obtenerValorConfiguracion(75);//defecto                            
                            $fecha_inicio=date('Y-m-1'); 
                            $fecha_fin = date("Y-m-t", strtotime($fecha_inicio)); 
                            $fecha_inicio_x= date("Y-m-d",strtotime($fecha_inicio."- ".$dias_alargo." days")); 
                            $fechaComoEntero = strtotime($fecha_factura_xy);
                            $fecha_factura = date("Y-m-d", $fechaComoEntero);
                            // echo $fecha_inicio_x."-".$fecha_fin."<br>";
                            $sw_anular=verificar_fecha_rango($fecha_inicio_x, $fecha_fin, $fecha_factura);
                          }
                          //==
                          $nombre_personal=namePersonalCompleto($cod_personal);
                          if($cod_personal==0){
                            $nombre_personal="Tienda Virtual";
                          }                          
                          $cadenaFacturas='F '.$nro_factura;
                          $codigos_facturas=$codigo_factura;                          
                          $importe=sumatotaldetallefactura($codigo_factura);
                          $correosEnviados=obtenerCorreosEnviadosFactura($codigo_factura);
                          if($correosEnviados!=""){
                            $correosEnviados="\nFactura enviada a: \n *".$correosEnviados;
                          }
                          $estadofactura=obtener_nombreestado_factura($cod_estadofactura);
                          $cliente=nameCliente($cod_cliente);
                          //correos de contactos
                          $tipo_solicitud=obtenerTipoSolicitud($cod_solicitudfacturacion);
                          if($tipo_solicitud==2 || $tipo_solicitud==6 || $tipo_solicitud==7){
                            $correos_string=obtenerCorreoEstudiante($nit);
                          }else $correos_string=obtenerCorreosCliente($cod_cliente);                            
                          //colores de estados                            
                          $observaciones_solfac="";
                          switch ($cod_estadofactura) {
                            case 1://activo
                              $label='btn-success';
                              break;
                            case 2://anulado
                              $label='btn-danger';
                              $observaciones_solfac = obtener_observacion_factura($cod_solicitudfacturacion);
                              break;
                            case 3://enviado
                              $label='btn-info';
                              break;
                          }
                          $cod_tipopago_anticipo=obtenerValorConfiguracion(48);//tipo pago credito
                          $cod_tipopago_aux=obtnerFormasPago_codigo($cod_tipopago_anticipo,$cod_solicitudfacturacion);//verificamos si en nuestra solicitud se hizo alguna distribucion de formas de pago y sacamos el de dep cuenta. devolvera 0 en caso de q no exista                            
                          $datos=$codigo_factura.'/'.$cod_solicitudfacturacion.'/'.$nro_factura.'/'.$correos_string.'/'.$razon_social.'/'.$interno;
                          ?>
                          <tr>
                            <!-- <td align="center"><?=$index;?></td> -->
                            <td><?=$nro_factura;?></td>
                            <td><?=$nombre_personal;?></td>
                            <td><?=$fecha_factura?><br><?=$hora_factura?></td>
                            <td class="text-left"><small><?=mb_strtoupper($razon_social);?></small></td>
                            <td class="text-right"><?=$nit;?></td>
                            <td class="text-right"><?=formatNumberDec($importe);?></td>
                            <td><small><?=strtoupper($observaciones);?></small></td>                            
                            <td style="color: #ff0000;"><?=strtoupper($observaciones_solfac)?></td>
                            <td class="td-actions text-right">
                              <button class="btn <?=$label?> btn-sm btn-link" style="padding:0;"><small><?=$estadofactura;?></small></button><br>
                              <?php                                
                                if(($cod_estadofactura==1)){?>
                                  <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modalEnviarCorreo" onclick="agregaformEnviarCorreo('<?=$datos;?>')">
                                    <i class="material-icons" title="Enviar Correo">email</i>
                                  </button>
                                  <?php
                                } if($cod_estadofactura!=4){?>  

                                  <div class="btn-group dropdown">
                                    <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Formato 1">
                                       <i class="material-icons" title="Imprimir Factura <?=$correosEnviados?>">print</i>
                                    </button>
                                    <div class="dropdown-menu">                                      
                                      <a class="dropdown-item" href='<?=$urlGenerarFacturasPrint;?>?codigo=<?=$codigo_factura;?>&tipo=1&admin=1' target="_blank"><i class="material-icons text-success">print</i> Original Cliente y Copia Contabilidad</a>
                                      <a class="dropdown-item" href='<?=$urlGenerarFacturasPrint;?>?codigo=<?=$codigo_factura;?>&tipo=1&admin=2' target="_blank"><i class="material-icons text-success">print</i> Original Cliente</a>
                                      <a class="dropdown-item" href='<?=$urlGenerarFacturasPrint;?>?codigo=<?=$codigo_factura;?>&tipo=1&admin=3' target="_blank"><i class="material-icons text-success">print</i>Copia Contabilidad</a>                                    
                                    </div>
                                  </div>
                                  <div class="btn-group dropdown">
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Formato 2">
                                       <i class="material-icons" title="Imprimir Factura <?=$correosEnviados?>">print</i>
                                    </button>
                                    <div class="dropdown-menu">                                      
                                      <a class="dropdown-item" href='<?=$urlGenerarFacturasPrint;?>?codigo=<?=$codigo_factura;?>&tipo=1&admin=4' target="_blank"><i class="material-icons text-success">print</i> Original Cliente y Copia Contabilidad</a>
                                      <a class="dropdown-item" href='<?=$urlGenerarFacturasPrint;?>?codigo=<?=$codigo_factura;?>&tipo=1&admin=5' target="_blank"><i class="material-icons text-success">print</i> Original Cliente</a>
                                      <a class="dropdown-item" href='<?=$urlGenerarFacturasPrint;?>?codigo=<?=$codigo_factura;?>&tipo=1&admin=6' target="_blank"><i class="material-icons text-success">print</i>Copia Contabilidad</a>                                    
                                    </div>
                                  </div> 
                                  <?php                               
                                }
                                                                
                                $datos_devolucion=$cod_solicitudfacturacion."###".$cadenaFacturas."###".$razon_social."###".$urllistFacturasServicios."###".$codigos_facturas."###".$cod_comprobante."###".$cod_tipopago_aux."###".$interno;                                
                                if($cod_estadofactura!=4 && $cod_estadofactura!=2 && $sw_anular){?>
                                  <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modalDevolverSolicitud" onclick="modal_rechazarFactura('<?=$datos_devolucion;?>')">
                                    <i class="material-icons" title="Anular Factura">delete</i>
                                  </button>
                                <?php } ?>
                            </td>
                          </tr>
                          <?php
                            $index++;
                        }
                        ?>
                      </tbody>
                    </table>
                  </div>
                  <div class="card-footer fixed-bottom col-sm-9">
                    <a href='<?=$urlListFacturasGeneradasManuales;?>' class="btn btn-info float-right"><i class="material-icons">list</i>Facturas Manuales</a>
                  </div>   
                </div>                
              </div>
          </div>  
      </div>
    </div>
  </div>

<?php  //require_once 'simulaciones_servicios/modal_facturacion.php';?>
<div class="modal fade" id="modalBuscadorFacturas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Buscar Facturas</h4>
      </div>
      <div class="modal-body ">
        <div class="row">        
          <label class="col-sm-6 col-form-label text-center">Razón Social</label> 
          <label class="col-sm-6 col-form-label text-center">Fechas</label>
        </div> 
        <div class="row">
          <div class="form-group col-sm-6">
            <input class="form-control input-sm" type="text" name="razon_social_f" id="razon_social_f">
          </div>
          <div class="form-group col-sm-3">
            <input class="form-control input-sm" type="date" name="fechaBusquedaInicio" id="fechaBusquedaInicio">
          </div>
          <div class="form-group col-sm-3">
            <input class="form-control input-sm" type="date" name="fechaBusquedaFin" id="fechaBusquedaFin">
          </div>          
        </div>
        <div class="row">
            <label class="col-sm-6 col-form-label text-center">Detalle</label> 
            <label class="col-sm-3 col-form-label text-center">Nit</label> 
            <label class="col-sm-2 col-form-label text-center">Nro. Factura</label>                                 
        </div> 
        <div class="row">                   
          <div class="form-group col-sm-6">
            <input class="form-control input-sm" type="text" name="detalle_f" id="detalle_f">
          </div>
          <div class="form-group col-sm-3">            
            <input class="form-control input-sm" type="text" name="nit_f" id="nit_f">          
          </div>              
          <div class="form-group col-sm-2">            
            <input class="form-control input-sm" type="text" name="nro_f" id="nro_f">
          </div>
        </div> 
        <div class="row">                   
          <label class="col-sm-3 col-form-label text-center">Personal</label> 
          <div class="form-group col-sm-8">            
            <?php
              $sqlUO="SELECT cod_personal from facturas_venta where cod_estadofactura<>2 and cod_personal<>0 GROUP BY cod_personal";
              $stmt = $dbh->prepare($sqlUO);
              $stmt->execute();
              ?>
                <select class="selectpicker form-control form-control-sm" name="personal_p[]" id="personal_p" data-style="select-with-transition" multiple data-actions-box="true" required data-live-search="true">
                  <option value="0">Tienda Virtual</option>
                <?php 
                  while ($row = $stmt->fetch()){ 
                    $cod_personal=$row["cod_personal"];
                    $nombre_personal=namePersonalCompleto($cod_personal);
                    ?>
                    <option value="<?=$cod_personal?>" ><?=$nombre_personal?></option><?php 
                  } ?>
                </select>     
          </div>
        </div> 
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="botonBuscarComprobante" name="botonBuscarComprobante" onclick="botonBuscar_facturas()">Buscar</button>
        <!-- <button type="button" class="btn btn-danger" data-dismiss="modal"> Cerrar </button> -->
      </div>
    </div>
  </div>
</div>
<!-- modal devolver solicitud -->
<div class="modal fade" id="modalDevolverSolicitud" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h2 class="modal-title" id="myModalLabel"><b>Anular Factura<b></h2>
      </div>
      <div class="modal-body">        
        <form id="form_anular_facturas" action="simulaciones_servicios/anular_facturaGenerada.php" method="post"  onsubmit="return valida(this)" enctype="multipart/form-data">
        <input type="hidden" name="cod_solicitudfacturacion" id="cod_solicitudfacturacion" value="0">
        <input type="hidden" name="estado" id="estado" value="0">
        <input type="hidden" name="admin" id="admin" value="0">
        <input type="hidden" name="direccion" id="direccion" value="0">
        <input type="hidden" name="codigo_factura" id="codigo_factura" value="0">
        <input type="hidden" name="codigo_comprobante" id="codigo_comprobante" value="0">
        <input type="hidden" name="estado_factura" id="estado_factura" value="0">
        <input type="hidden" name="interno_delete" id="interno_delete" value="0">
        
        <div class="row">
          <label class="col-sm-1 col-form-label" style="color:#7e7e7e"><span id="campo_nro_fact"><b><small>Nro(s)<br>Factura(s)</small></b></span></label>
          <div class="col-sm-3">
            <div class="form-group" >
              <input type="text" class="form-control" name="nro_solicitud" id="nro_solicitud" readonly="true" style="background-color:#e2d2e0">              
            </div>
          </div>
          <label class="col-sm-1 col-form-label" style="color:#7e7e7e"><span id="campo_rs_fact"><b><small>Razón<br>Social</small></b></span></label>
          <div class="col-sm-7">
            <div class="form-group" >              
              <input type="text" class="form-control" name="codigo_servicio" id="codigo_servicio" readonly="true" style="background-color:#e2d2e0">
            </div>
          </div>
        </div> 
        <div class="row">
            <div class="col-sm-12">
                <div class="row col-sm-11 div-center">
                  <table class="table table-warning table-bordered table-condensed">
                    <thead>
                      <tr>
                        <th class="small" width="30%">Tipo de Documento <a href="#" title="Otro Documento" class="btn btn-primary btn-round btn-sm btn-fab float-left" onClick="agregarFilaArchivosAdjuntosCabecera()"><i class="material-icons">add</i></a></th>
                        <th class="small">Obligatorio</th>
                        <th class="small" width="35%">Archivo</th>
                        <th class="small">Descripción</th>                  
                      </tr>
                    </thead>
                    <tbody id="tabla_archivos">
                      <?php
                        $filaE=0;
                      ?>       
                    </tbody>
                  </table>
                  <input type="hidden" value="<?=$filaE?>" id="cantidad_archivosadjuntos" name="cantidad_archivosadjuntos">
                </div>
                </center>
            </div>
        </div>   
        <div class="row">
          <label class="col-sm-12 col-form-label" style="color:#7e7e7e"><small>Observaciones</small></label>
        </div>
        <div class="row">
          <div class="col-sm-12" style="background-color:#f9edf7">
            <div class="form-group" >              
              <textarea type="text" name="observaciones" id="observaciones" class="form-control" required="true"></textarea>
            </div>
          </div>
        </div>        
      </div>
      <div class="modal-footer">
        <div id="boton_registrar_anticipo">
          <button type="submit" class="btn btn-success" onclick="registrarRechazoFactura(2)">Registrar Como Anticipo</button>  
        </div>
        <button type="submit" class="btn btn-warning" onclick="registrarRechazoFactura(1)">Transacción No Válida</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"> Volver </button>
      </div>
      </form>

    </div>
  </div>
</div>
<!-- Modal enviar correo-->
<div class="modal fade" id="modalEnviarCorreo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" style="background-color:#e2e6e7">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Enviar Correo</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="codigo_facturacion" id="codigo_facturacion" value="0">
        <input type="hidden" name="cod_solicitudfacturacion" id="cod_solicitudfacturacion" value="0">
        <input type="hidden" name="interno_x" id="interno_x" value="0">
        <!-- <input type="text" name="nro_factura" id="nro_factura" value="0">
        <input type="text" name="razon_social" id="razon_social" value="0"> -->
        <?php
          // $texto_cuerpo="Estimado cliente,\n\n Le Hacemos el envío de la Factura.\n\nSaludos.";
          // $asunto="ENVIO FACTURA - IBNORCA";

        ?>
        <?php
        echo "<script>var array_correos=[];</script>";

        $i=0;
          $correoLista=obtenerCorreosListaPersonal(); //null para todas las iniciales del numero de cuenta obtenerCuentasLista(5,[5,4]);
           while ($rowCorreo = $correoLista->fetch(PDO::FETCH_ASSOC)) {
            $codigoX=$rowCorreo['codigo'];
            $correoX=strtolower($rowCorreo['email_empresa']);
            ?>
            <script>
             var obtejoLista={
               label:'<?=$correoX?>',
               value:'<?=$codigoX?>'};
               array_correos[<?=$i?>]=obtejoLista;
            </script>
            <?php
            $i=$i+1;
          }

          $sqlLogsCorreos="SELECT DISTINCT trim(correo) as correo from log_instancias_envios_correo where correo!='' order by correo desc";
          $stmtLogsCorreos = $dbh->prepare($sqlLogsCorreos);
          $stmtLogsCorreos->execute();                           
         while ($row = $stmtLogsCorreos->fetch(PDO::FETCH_ASSOC)) {
          $codigoX=$i;
          $correoX=trim($row['correo']);
          ?>
            <script>
             var obtejoLista={
               label:'<?=$correoX?>',
               value:'<?=$codigoX?>'};
               array_correos[<?=$i?>]=obtejoLista;
            </script>
            <?php
          $i++;
         }  
          ?>
        <div class="row">
          <label class="col-sm-1 col-form-label" style="color:#000000"><small>Nro. Factura</small></label>
          <div class="col-sm-2">
            <div class="form-group" >
              <input type="text" class="form-control" name="nro_factura" id="nro_factura" value="0" readonly="true" style="background-color:#e2d2e0">              
            </div>
          </div>
          <label class="col-sm-1 col-form-label" style="color:#000000"><small>Razón<br>Social</small></label>
          <div class="col-sm-8">
            <div class="form-group" >              
              <input type="text" class="form-control" name="razon_social" id="razon_social" value="0" readonly="true" style="background-color:#e2d2e0"> 
            </div>
          </div>
        </div>        
        <!-- <input class="form-control" type="email" name="correo_destino" id="correo_destino" required="true" value="" /> -->
        <div class="row">
          <div class="col-sm-12" >
            <h6> Correo Destino : </h6>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12" style="background-color:#FFFFFF">
            <div class="form-group" >
              <input type="text" name="correo_destino" id="correo_destino" class="form-control tagsinput" data-role="tagsinput" data-color="info" required="true" >  
            </div>
          </div>
        </div>
        <!-- <h6> Asunto : </h6>
        <input class="form-control" type="text" name="asunto" id="asunto" value="<?=$asunto?>" required="true"/>
        <h6> Mensaje : </h6>
        <textarea class="form-control" name="mensaje" id="mensaje" required="true"><?=$texto_cuerpo?></textarea> -->
        <!-- <input class="form-control" type="text" /> -->
        <?php 
         $sqlInstancia="SELECT codigo,descripcion from instancias_envios_correos where codigo=1";
         $stmtInstancia = $dbh->prepare($sqlInstancia);
         $stmtInstancia->execute();                           
         while ($row = $stmtInstancia->fetch(PDO::FETCH_ASSOC)) {
          $datoInstancia=obtenerCorreosInstanciaEnvio($row['codigo']);
          $correos=implode(",",$datoInstancia[0]);
          $nombres=implode(",",$datoInstancia[1]);
            ?>
         <div class="row">
          <div class="col-sm-12" >
            <h6> <?=$row['descripcion']?> (CC): </h6>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12" style="background-color:#FFFFFF">
            <div class="form-group" >
              <input type="text" readonly value="<?=$nombres?>" name="nombre_correo" id="nombre_correo" class="form-control">  
            </div>
          </div>
        </div> 
        <div class="row d-none">
          <div class="col-sm-12" style="background-color:#FFFFFF">
            <div class="form-group" >
              <input type="text" value="<?=$correos?>" name="correo_copia" id="correo_copia" class="form-control tagsinput" data-role="tagsinput" data-color="info" required="true" >  
              <input type="hidden" id="correo_autocompleteids">  
            </div>
          </div>
        </div> 
            <?php
         }   
        ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="EnviarCorreo" name="EnviarCorreo" data-dismiss="modal">Aceptar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"> <-- Volver </button>
      </div>
    </div>
  </div>
</div>
<div class="cargar-ajax d-none">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold" id="texto_ajax_titulo">Enviando Correo..</h4>
     <p class="text-white">Aguarde un momento por favor.</p>  
  </div>
</div>

<script type="text/javascript">
   function valida(f) {
      // alert("e");
        var ok = true;
        var msg = "Por favor introduzca la observación";
        var observaciones=f.elements["observaciones"].value;
        if(observaciones == 0 || observaciones < 0 || observaciones == '')
        {                
            ok = false;
        }
        var cantidad_archivosadjuntos=f.elements["cantidad_archivosadjuntos"].value;
        if(cantidad_archivosadjuntos>0){
          for (var ar=1; ar <= cantidad_archivosadjuntos ; ar++) {             
            var codigo_archivo=f.elements["codigo_archivo"+ar].value;
            if(codigo_archivo){
              var documentos_cabecera=f.elements["documentos_cabecera"+ar].value;
              if(documentos_cabecera){
                sw_adjuntos=true;
              }else{
                sw_adjuntos=false;
              }
            }else{
              sw_adjuntos=false;
            }
          }
        }else{
          sw_adjuntos=false;
        }
        if(!sw_adjuntos){
          var msg = "Por favor agregue Archivo Adjunto.";        
          ok = false;            
        }
        if(ok == false)    
            Swal.fire("Informativo!",msg, "warning");
        return ok;
    }
</script>
<script type="text/javascript">
  $(document).ready(function(){
    $(".bootstrap-tagsinput input").attr("id","tag_inputcorreo");
    autocompletar("tag_inputcorreo","correo_autocompleteids",array_correos);

    $('#EnviarCorreo').click(function(){    
      codigo_facturacion=document.getElementById("codigo_facturacion").value;
      cod_solicitudfacturacion=document.getElementById("cod_solicitudfacturacion").value;
      nro_factura=document.getElementById("nro_factura").value;
      razon_social=document.getElementById("razon_social").value;
      interno=document.getElementById("interno_x").value;
      
      correo_copia=$('#correo_copia').val();
      if(correo_copia!=""){
        correo_destino=$('#correo_destino').val()+","+correo_copia;        
      }else{
        correo_destino=$('#correo_destino').val();        
      } 
      // asunto=$('#asunto').val();
      // mensaje=$('#mensaje').val();
      asunto=null;
      mensaje=null;
      if(correo_destino==null || correo_destino == "" ||correo_destino == 0){
        // alert("Por Favor Agregue Un correo para el envío de la Factura!");
        Swal.fire("Informativo!", "Por Favor Agregue Un correo válido para el envío de la Factura!", "warning");
      }else{
        EnviarCorreoAjax(codigo_facturacion,nro_factura,cod_solicitudfacturacion,correo_destino,asunto,mensaje,razon_social,interno);  
      }
    });   
  });
</script>
