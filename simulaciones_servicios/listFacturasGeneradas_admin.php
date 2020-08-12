<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';

$dbh = new Conexion();
$globalAdmin=$_SESSION["globalAdmin"];
$globalPersonal=$_SESSION["globalUser"];


  //datos registrado de la simulacion en curso
  $stmt = $dbh->prepare("SELECT f.*,DATE_FORMAT(f.fecha_factura,'%d/%m/%Y')as fecha_factura_x,DATE_FORMAT(f.fecha_factura,'%H:%i:%s')as hora_factura_x,(select s.abreviatura from unidades_organizacionales s where s.cod_sucursal=f.cod_sucursal limit 1)as sucursal
 from facturas_venta f order by  f.fecha_factura desc limit 50");
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
  $mes_actual=date('m');

  ?>
  <input type="hidden" name="interno" value="0" id="interno"/>
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
                    <h4 class="card-title"><b>Facturas Generadas ADMIN</b></h4>                    
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
                          //==
                          $nombre_personal=namePersonalCompleto($cod_personal);
                          if($cod_personal==0){
                            $nombre_personal="Tienda Virtual";
                          }                          
                          $cadenaFacturas='F '.$nro_factura;
                          $codigos_facturas=$codigo_factura;                          
                          $importe=sumatotaldetallefactura($codigo_factura);
                          // $correosEnviados=obtenerCorreosEnviadosFactura($codigo_factura);
                          // if($correosEnviados!=""){
                          //   $correosEnviados="\nFactura enviada a: \n *".$correosEnviados;
                          // }
                          $estadofactura=obtener_nombreestado_factura($cod_estadofactura);
                          $cliente=nameCliente($cod_cliente);
                          //correos de contactos
                          $tipo_solicitud=obtenerTipoSolicitud($cod_solicitudfacturacion);
                          // if($tipo_solicitud==2 || $tipo_solicitud==6 || $tipo_solicitud==7){
                          //   $correos_string=obtenerCorreoEstudiante($nit);
                          // }else $correos_string=obtenerCorreosCliente($cod_cliente);                            
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
                          // $cod_tipopago_anticipo=obtenerValorConfiguracion(48);//tipo pago credito
                          // $cod_tipopago_aux=obtnerFormasPago_codigo($cod_tipopago_anticipo,$cod_solicitudfacturacion);//verificamos si en nuestra solicitud se hizo alguna distribucion de formas de pago y sacamos el de dep cuenta. devolvera 0 en caso de q no exista                            
                          // $datos=$codigo_factura.'/'.$cod_solicitudfacturacion.'/'.$nro_factura.'/'.$correos_string.'/'.$razon_social;
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
                                // $datos_devolucion=$cod_solicitudfacturacion."###".$cadenaFacturas."###".$razon_social."###".$urllistFacturasServicios."###".$codigos_facturas."###".$cod_comprobante."###".$cod_tipopago_aux."###".$interno;
                              $datos_edit=$cadenaFacturas."###".$razon_social."###".$codigos_facturas;
                                if($cod_estadofactura!=2 && $globalAdmin==1){?>
                                  <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalEditarFactura" onclick="modal_editarFactura_sf('<?=$datos_edit;?>')">
                                    <i class="material-icons" title="Editar Factura">edit</i>
                                  </button>
                                  <!-- <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modalDevolverSolicitud" onclick="modal_rechazarFactura('<?=$datos_devolucion;?>')">
                                    <i class="material-icons" title="Anular Factura">delete</i>
                                  </button> -->
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
                    <!-- <a href='<?=$urlListFacturasGeneradasManuales;?>' class="btn btn-info float-right"><i class="material-icons">list</i>Facturas Manuales</a> -->
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
        <button type="button" class="btn btn-success" id="botonBuscarComprobante" name="botonBuscarComprobante" onclick="botonBuscar_facturas_admin()">Buscar</button>
        <!-- <button type="button" class="btn btn-danger" data-dismiss="modal"> Cerrar </button> -->
      </div>
    </div>
  </div>
</div>
<!-- Modal editar-->
<div class="modal fade" id="modalEditarFactura" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title" id="myModalLabel"><b>Editar Factura</b></h3>
      </div>
      <div class="modal-body">
        <input type="hidden" name="cod_facturaventa_e" id="cod_facturaventa_e" value="0">        
        <div class="row">
          <label class="col-sm-3 text-right col-form-label" style="color:#424242">Numero de Factura: </label>
          <div class="col-sm-8">
            <div class="form-group">
              <input type="text" name="nro_factura_e" id="nro_factura_e" class="form-control" readonly="true">
            </div>
          </div>
        </div>                
        <div class="row">
          <label class="col-sm-3 text-right col-form-label" style="color:#424242">Razón Social </label>
          <div class="col-sm-8">
            <div class="form-group">
              <textarea name="razon_social_e" id="razon_social_e" class="form-control"></textarea>
            </div>
          </div>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="guardarFacturaEdit" name="guardarFacturaEdit">Guardar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"> Volver </button>
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
  $(document).ready(function(){  
    $('#guardarFacturaEdit').click(function(){          
      cod_facturaventa_e=document.getElementById("cod_facturaventa_e").value;
      razon_social_e=$('#razon_social_e').val();      
      // asunto=$('#asunto').val();
      // mensaje=$('#mensaje').val();
      asunto=null;
      mensaje=null;
      if(razon_social_e==null || razon_social_e == "" ||razon_social_e == 0){
        // alert("Por Favor Agregue Un correo para el envío de la Factura!");
        Swal.fire("Informativo!", "La Razón Social No debe ir Vacío!", "warning");
      }else{
        actualizar_factura(cod_facturaventa_e,razon_social_e);  
      }
    });   
  });
</script>
