<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';

$dbh = new Conexion();
$globalAdmin=$_SESSION["globalAdmin"];



  //datos registrado de la simulacion en curso
  $stmt = $dbh->prepare("SELECT f.*,DATE_FORMAT(f.fecha_factura,'%d/%m/%Y')as fecha_factura_x,DATE_FORMAT(f.fecha_factura,'%H:%i:%s')as hora_factura_x,(select s.abreviatura from unidades_organizacionales s where s.cod_sucursal=f.cod_sucursal limit 1)as sucursal,(select t.nombre from estados_factura t where t.codigo=f.cod_estadofactura)as estadofactura 
 from facturas_venta f where cod_estadofactura<>4 order by  f.fecha_factura desc");
  $stmt->execute();
  $stmt->bindColumn('codigo', $codigo_factura);
  $stmt->bindColumn('cod_sucursal', $cod_sucursal);
  $stmt->bindColumn('cod_area', $cod_area);
  $stmt->bindColumn('cod_solicitudfacturacion', $cod_solicitudfacturacion);  
  $stmt->bindColumn('fecha_factura_x', $fecha_factura);
  $stmt->bindColumn('hora_factura_x', $hora_factura);
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
                    <h4 class="card-title"><b>Facturas Generadas</b></h4>
                    <!-- <h4 class="card-title" align="center"><b><?=$nombre_simulacion?> - <?=$name_area_simulacion?></b></h4> -->
                  </div>
                  <div class="card-body">
                      <table class="table" id="tablePaginator">
                        <thead>
                          <tr>
                            <!-- <th class="text-center"></th> -->
                            <th width="8%">#Fac</th>
                            <!-- <th>Sucursal</th> -->
                            <th width="8%">Fecha<br>Factura</th>
                            <th width="25%">Razón Social</th>
                            <th width="10%">Nit</th>
                            <th width="8%">Importe<br>Factura</th>
                            <th>Obs.</th>
                            <th width="5%">Estado</th>
                            <th width="10%" class="text-right">Opciones</th>                            
                          </tr>
                        </thead>
                        <tbody>
                        <?php
                          $index=1;
                          while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                            
                            $cliente=nameCliente($cod_cliente);
                            //correos de contactos
                            $sqlCorreo="SELECT correo from clientes_contactos where correo!='null' and cod_cliente=$cod_cliente";
                            // echo $sqlCorreo;
                            $stmtCorreos = $dbh->prepare($sqlCorreo);
                            $stmtCorreos->execute();
                            $stmtCorreos->bindColumn('correo', $correo);
                            $correos_string= '';                            
                            while ($row = $stmtCorreos->fetch(PDO::FETCH_BOUND)) {
                              $correos_string.=$correo.',';
                            }
                            

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
                            $datos=$codigo_factura.'/'.$cod_solicitudfacturacion.'/'.$nro_factura.'/'.$correos_string;
                            ?>
                          <tr>
                            <!-- <td align="center"><?=$index;?></td> -->
                            <td><?=$nro_factura;?></td>
                            <!-- <td><?=$sucursal;?></td> -->
                            <td><?=$fecha_factura?><br><?=$hora_factura?></td>
                            <td class="text-left"><small><?=$razon_social;?></small></td>
                            <td class="text-right"><?=$nit;?></td>
                            <td class="text-right"><?=formatNumberDec($importe);?></td>
                            <td><small><?=$observaciones;?></small></td>                            
                            <td><?=$label.$estadofactura."</span>";?></td>
                            <td class="td-actions text-right">
                              <?php
                                if($globalAdmin==1 and $cod_estadofactura==1 ){?>                                
                                  <a class="btn btn-success" href='<?=$urlGenerarFacturasPrint;?>?codigo=<?=$codigo_factura;?>&tipo=1' target="_blank"><i class="material-icons" title="Imprimir Facturas">print</i></a>
                                 <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modalEnviarCorreo" onclick="agregaformEnviarCorreo('<?=$datos;?>')">
                                    <i class="material-icons" title="Enviar Correo">email</i>
                                  </button>
                                  <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation-anular-factura','<?=$urlAnularFactura;?>&codigo=<?=$codigo_factura;?>&cod_solicitudfacturacion=<?=$cod_solicitudfacturacion?>&cod_comprobante=<?=$cod_comprobante?>')">
                                  <i class="material-icons" title="Anular Factura">clear</i>
                                  </button>
                                <?php  
                                }elseif($globalAdmin==1 and $cod_estadofactura==3){?>
                                  <a class="btn btn-success" href='<?=$urlGenerarFacturasPrint;?>?codigo=<?=$codigo_factura;?>&tipo=1' target="_blank"><i class="material-icons" title="Imprimir Facturas">print</i></a>

                                  <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation-anular-factura','<?=$urlAnularFactura;?>&codigo=<?=$codigo_factura;?>&cod_solicitudfacturacion=<?=$cod_solicitudfacturacion?>&cod_comprobante=<?=$cod_comprobante?>')">
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
                    <a href='<?=$urlListFacturasGeneradasManuales;?>' class="btn btn-info float-right"><i class="material-icons">list</i>Facturas Manuales</a>
                  </div>   
                </div>                
              </div>
          </div>  
    </div>
  </div>

<!-- Modal enviar correo-->
<div class="modal fade" id="modalEnviarCorreo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" style="background-color:#b7c8bf">
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
        <!-- <input class="form-control" type="email" name="correo_destino" id="correo_destino" required="true" value="" /> -->
        <div class="row">
          <div class="col-sm-12">
            <div class="form-group" id="contenedor_correos" >
              <input type="text" name="correo_destino" id="correo_destino" class="form-control tagsinput" data-role="tagsinput" data-color="info" required="true" >  
            </div>
          </div>
        </div>
        
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
<div class="cargar-ajax d-none">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold" id="texto_ajax_titulo">Enviando Correo..</h4>
     <p class="text-white">Aguarde un momento por favor.</p>  
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