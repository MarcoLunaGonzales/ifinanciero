<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';

$dbh = new Conexion();
$globalAdmin=$_SESSION["globalAdmin"];



  //datos registrado de la simulacion en curso
  $stmt = $dbh->prepare("SELECT f.*,DATE_FORMAT(f.fecha_factura,'%d/%m/%Y')as fecha_registro_x,(select s.abreviatura from unidades_organizacionales s where s.cod_sucursal=f.cod_sucursal limit 1)as sucursal,(select t.nombre from estados_factura t where t.codigo=f.cod_estadofactura)as estadofactura 
 from facturas_venta f where cod_estadofactura in (4,5) order by  f.fecha_factura desc");
  $stmt->execute();
  $stmt->bindColumn('codigo', $codigo_factura);
  $stmt->bindColumn('cod_sucursal', $cod_sucursal);
  $stmt->bindColumn('cod_area', $cod_area);
  $stmt->bindColumn('cod_solicitudfacturacion', $cod_solicitudfacturacion);
  $stmt->bindColumn('fecha_registro_x', $fecha_factura);
  $stmt->bindColumn('fecha_limite_emision', $fecha_limite_emision);
  $stmt->bindColumn('cod_tipopago', $cod_tipopago);
  $stmt->bindColumn('cod_cliente', $cod_cliente);
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
  $stmt->bindColumn('estadofactura', $estadofactura);
  $stmt->bindColumn('cod_comprobante', $cod_comprobante);
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
                    <h4 class="card-title"><b>Facturas Generadas Manualmente</b></h4>
                    <!-- <h4 class="card-title" align="center"><b><?=$nombre_simulacion?> - <?=$name_area_simulacion?></b></h4> -->
                  </div>
                  <div class="card-body">
                      <table class="table" id="tablePaginator">
                        <thead>
                          <tr>
                            <th width="6%">#Fac</th>
                            <!-- <th>Sucursal</th> -->
                            <th width="8%">Fecha<br>Factura</th>
                            <th width="25%">Razón Social</th>
                            <th width="9%">Nit</th>
                            <th width="8%">Importe<br>Factura</th>                           
                            <th>Detalle</th>
                            <th width="12%">Observaciones</th>
                            <th width="5%" class="text-right">Opciones</th>
                          </tr>
                        </thead>
                        <tbody>
                        <?php
                          $index=1;
                          while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {                          
                            $cliente=nameCliente($cod_cliente);          
                            $datos_FacManual=$cliente."/".$razon_social."/".$nit."/".$nro_factura."/".$nro_autorizacion."/".$importe;
                            //colores de estados  
                            $observaciones_solfac="";
                            switch ($cod_estadofactura) {                              
                              case 5://anulado
                                $label='btn-danger';
                                $observaciones_solfac = obtener_observacion_factura($cod_solicitudfacturacion);
                                break;                              
                              case 4://factura manual
                                $label='btn-warning';
                                break;
                            }
                            // $datos=$codigo_factura.'/'.$cod_solicitudfacturacion.'/'.$nro_factura.'/'.$correos_string;
                            ?>
                          <tr>                            
                            <td><?=$nro_factura;?></td>                            
                            <td><?=$fecha_factura?></td>
                            <td class="text-left"><small><?=$razon_social;?></small></td>
                            <td class="text-right"><?=$nit;?></td>
                            <td class="text-right"><?=formatNumberDec($importe);?></td>
                            <td><?=$observaciones;?></td>                            
                            <td style="color: #ff0000;"><?=$observaciones_solfac?></td>
                            <td class="td-actions text-right"><button class="btn <?=$label?> btn-sm btn-link" style="padding:0;"><small><?=$estadofactura;?></small></button><br>
                              <button title="Detalles" class="btn btn-success" type="button" data-toggle="modal" data-target="#modalDetalleFacturaManual" onclick="agregaDatosDetalleFactManual('<?=$datos_FacManual;?>')">
                                <i class="material-icons">list</i>
                              </button>
                              <?php if($cod_estadofactura==1 || $cod_estadofactura==3 || $cod_estadofactura==4){
                                $datos_devolucion=$cod_solicitudfacturacion."###".$nro_factura."###".$razon_social."###".$urlListFacturasGeneradasManuales."###".$codigo_factura."###".$cod_comprobante;
                                ?>
                                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modalDevolverSolicitud" onclick="modal_rechazarFactura('<?=$datos_devolucion;?>')">
                                    <i class="material-icons" title="Anular Factura">clear</i>
                                  </button>
                              <?php }
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
                  <div class="card-footer fixed-bottom col-sm-9">
                    <a href='<?=$urllistFacturasServicios;?>' class="btn btn-danger float-right"><i class="material-icons">keyboard_return</i>Volver</a>
                  </div>   
                </div>                
              </div>
          </div>  
    </div>
</div>
<?php  require_once 'simulaciones_servicios/modal_facturacion.php';?>
<script type="text/javascript">
  $(document).ready(function(){
    $('#rechazarSolicitud').click(function(){
      var q=0;var s=0;var u=0;var v=0;
      var cod_solicitudfacturacion=document.getElementById("cod_solicitudfacturacion").value;
      var estado=document.getElementById("estado").value;
      var admin=document.getElementById("admin").value;
      var direccion=document.getElementById("direccion").value;
      var observaciones=$('#observaciones').val();
      var codigo_factura=$('#codigo_factura').val();
      var codigo_comprobante=$('#codigo_comprobante').val();
      var estado_factura=5;//manual anulado
      if(observaciones==null || observaciones==0 || observaciones=='' || observaciones==' '){
        Swal.fire("Informativo!", "Por favor introduzca la observación.", "warning");
      }else{        
        registrarRechazoFactura(cod_solicitudfacturacion,observaciones,estado,admin,direccion,codigo_factura,codigo_comprobante,estado_factura);
      }      
    }); 

  });
</script>