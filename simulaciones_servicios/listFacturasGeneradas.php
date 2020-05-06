<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';

$dbh = new Conexion();
$globalAdmin=$_SESSION["globalAdmin"];



  //datos registrado de la simulacion en curso
  $stmt = $dbh->prepare("SELECT *,(select s.abreviatura from unidades_organizacionales s where s.codigo=cod_sucursal)as sucursal,(select t.nombre from estados_factura t where t.codigo=cod_estadofactura)as estadofactura from facturas_venta order by  fecha_factura desc");
  $stmt->execute();
  $stmt->bindColumn('codigo', $codigo_facturacion);
  $stmt->bindColumn('cod_sucursal', $cod_sucursal);
  $stmt->bindColumn('cod_area', $cod_area);
  $stmt->bindColumn('cod_solicitudfacturacion', $cod_solicitudfacturacion);
  $stmt->bindColumn('fecha_factura', $fecha_factura);
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
                    <h4 class="card-title"><b>Facturas Generadas</b></h4>
                    <!-- <h4 class="card-title" align="center"><b><?=$nombre_simulacion?> - <?=$name_area_simulacion?></b></h4> -->
                  </div>
                  <div class="card-body">
                      <table class="table" id="tablePaginator">
                        <thead>
                          <tr>
                            <th class="text-center">#</th>                          
                            <th>Nro Factura</th>
                            <th>Sucursal</th>
                            <th>F.Fact.</th>
                            <th>Cliente</th>
                            <th>Importe</th>
                            <th>Obs.</th>
                            <th>Estado</th>
                            <th class="text-right">Opciones</th>                            
                          </tr>
                        </thead>
                        <tbody>
                        <?php
                          $index=1;
                          while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                            
                            $cliente=nameCliente($cod_cliente);
                            //colores de estados                         
                            switch ($cod_estadofactura) {
                              case 1://activo
                                $label='<span class="badge badge-success">';
                                break;
                              case 2://anulado
                                $label='<span class="badge badge-danger">';
                                break;
                              case 3://enviado
                                $label='<span class="badge badge-info" style="">';
                                break;
                                                          
                            }
                            $datos=$codigo_facturacion.'/'.$cod_solicitudfacturacion.'/'.$nro_factura;
                            ?>
                          <tr>
                            <td align="center"><?=$index;?></td>
                            <td><?=$nro_factura;?></td>
                            <td><?=$sucursal;?></td>
                            <td><?=$fecha_factura?></td>
                            <td><?=$cliente;?></td>
                            <td class="text-right"><?=formatNumberDec($importe);?></td>
                            <td><?=$observaciones;?></td>                            
                            <td><?=$label.$estadofactura."</span>";?></td>
                            <td class="td-actions text-right">
                              <?php
                                if($globalAdmin==1 and $cod_estadofactura==1 ){?>                                
                                  <a class="btn btn-success" href='<?=$urlGenerarFacturasPrint;?>?codigo=<?=$cod_solicitudfacturacion;?>&tipo=2' target="_blank"><i class="material-icons" title="Imprimir Facturas">print</i></a>
                                 <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modalEnviarCorreo" onclick="agregaformEnviarCorreo('<?=$datos;?>')">
                                    <i class="material-icons" title="Enviar Correo">email</i>
                                  </button>
                                  <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation-anular-factura','<?=$urlAnularFactura;?>&codigo=<?=$codigo_facturacion;?>')">
                                  <i class="material-icons" title="Anular Factura">clear</i>
                                  </button>
                                <?php  
                                }elseif($globalAdmin==1 and $cod_estadofactura==3){?>
                                  <a class="btn btn-success" href='<?=$urlGenerarFacturasPrint;?>?codigo=<?=$cod_solicitudfacturacion;?>&tipo=2' target="_blank"><i class="material-icons" title="Imprimir Facturas">print</i></a>

                                  <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation-anular-factura','<?=$urlAnularFactura;?>&codigo=<?=$codigo_facturacion;?>')">
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
                </div>                
              </div>
          </div>  
    </div>
  </div>

<!-- Modal enviar correo-->
<div class="modal fade" id="modalEnviarCorreo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Enviar Correo</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="codigo_facturacion" id="codigo_facturacion" value="0">
        <input type="hidden" name="cod_solicitudfacturacion" id="cod_solicitudfacturacion" value="0">
        <input type="hidden" name="nro_factura" id="nro_factura" value="0">
        <?php
          // $texto_cuerpo="Estimado cliente,\n\n Le Hacemos el envío de la Factura.\n\nSaludos.";
          // $asunto="ENVIO FACTURA - IBNORCA";

        ?>
        <h6> Correo Destino : </h6>
        <input class="form-control" type="email" name="correo_destino" id="correo_destino" required="true" />
        <!-- <h6> Asunto : </h6>
        <input class="form-control" type="text" name="asunto" id="asunto" value="<?=$asunto?>" required="true"/>
        <h6> Mensaje : </h6>
        <textarea class="form-control" name="mensaje" id="mensaje" required="true"><?=$texto_cuerpo?></textarea> -->
        <!-- <input class="form-control" type="text" /> -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="EnviarCorreo" name="EnviarCorreo" data-dismiss="modal">Aceptar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"> <-- Volver </button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    $('#EnviarCorreo').click(function(){    
      codigo_facturacion=document.getElementById("codigo_facturacion").value;
      cod_solicitudfacturacion=document.getElementById("cod_solicitudfacturacion").value;
      nro_factura=document.getElementById("nro_factura").value;      
      correo_destino=$('#correo_destino').val();
      // asunto=$('#asunto').val();
      // mensaje=$('#mensaje').val();
      asunto=null;
      mensaje=null;
      if(correo_destino==null || correo_destino == "" ||correo_destino == 0){
        // alert("Por Favor Agregue Un correo para el envío de la Factura!");
        Swal.fire("Informativo!", "Por Favor Agregue Un correo válido para el envío de la Factura!", "warning");
      }else{
        EnviarCorreoAjax(codigo_facturacion,nro_factura,cod_solicitudfacturacion,correo_destino,asunto,mensaje);  
      }
      
    });
  });
</script>