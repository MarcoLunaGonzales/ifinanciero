<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';

$dbh = new Conexion();
$globalAdmin=$_SESSION["globalAdmin"];



  //datos registrado de la simulacion en curso
  $stmt = $dbh->prepare("SELECT f.*,DATE_FORMAT(f.fecha_factura,'%d/%m/%Y')as fecha_factura_x,DATE_FORMAT(f.fecha_factura,'%H:%i:%s')as hora_factura_x,(select s.abreviatura from unidades_organizacionales s where s.cod_sucursal=f.cod_sucursal limit 1)as sucursal
 from facturas_venta f where cod_estadofactura in (1,2,3) order by  f.fecha_factura desc");
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
  // $stmt->bindColumn('estadofactura', $estadofactura);
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
                            <th width="6%">#Fac</th>
                            <!-- <th>Sucursal</th> -->
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
                            $estadofactura=obtener_nombreestado_factura($cod_estadofactura);
                            $cliente=nameCliente($cod_cliente);
                            //correos de contactos
                            $tipo_solicitud=obtenerTipoSolicitud($cod_solicitudfacturacion);
                            if($tipo_solicitud==2 || $tipo_solicitud==6 || $tipo_solicitud==7){
                              $correos_string=obtenerCorreoEstudiante($cod_cliente);
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
                            $datos=$codigo_factura.'/'.$cod_solicitudfacturacion.'/'.$nro_factura.'/'.$correos_string.'/'.$razon_social;
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
                            <td style="color: #ff0000;"><?=$observaciones_solfac?></td>
                            <td class="td-actions text-right">
                              <button class="btn <?=$label?> btn-sm btn-link" style="padding:0;"><small><?=$estadofactura;?></small></button><br>
                              <?php
                                if($globalAdmin==1 and $cod_estadofactura==1 ){?>
                                  <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modalEnviarCorreo" onclick="agregaformEnviarCorreo('<?=$datos;?>')">
                                    <i class="material-icons" title="Enviar Correo">email</i>
                                  </button><?php  
                                }
                                if($globalAdmin==1 and ($cod_estadofactura==1 || $cod_estadofactura==3)){?>
                                  <div class="btn-group dropdown">
                                    <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                       <i class="material-icons" title="Imprimir Factura">print</i>
                                    </button>
                                    <div class="dropdown-menu">                                      
                                      <a class="dropdown-item" href='<?=$urlGenerarFacturasPrint;?>?codigo=<?=$codigo_factura;?>&tipo=1&admin=1' target="_blank"><i class="material-icons text-success">print</i> Original Cliente y Copia Contabilidad</a>
                                      <a class="dropdown-item" href='<?=$urlGenerarFacturasPrint;?>?codigo=<?=$codigo_factura;?>&tipo=1&admin=2' target="_blank"><i class="material-icons text-success">print</i> Original Cliente</a>
                                      <a class="dropdown-item" href='<?=$urlGenerarFacturasPrint;?>?codigo=<?=$codigo_factura;?>&tipo=1&admin=3' target="_blank"><i class="material-icons text-success">print</i>Copia Contabilidad</a>
                                    </div>
                                  </div>
                                  <?php
                                   $datos_devolucion=$cod_solicitudfacturacion."###".$nro_factura."###".$razon_social."###".$urllistFacturasServicios."###".$codigo_factura."###".$cod_comprobante;
                                  ?>
                                  <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modalDevolverSolicitud" onclick="modal_rechazarFactura('<?=$datos_devolucion;?>')">
                                    <i class="material-icons" title="Anular Factura">clear</i>
                                  </button>
                                  <!-- <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation-anular-factura','<?=$urlAnularFactura;?>&codigo=<?=$codigo_factura;?>&cod_solicitudfacturacion=<?=$cod_solicitudfacturacion?>&cod_comprobante=<?=$cod_comprobante?>')">
                                  <i class="material-icons" title="Anular Factura">clear</i>
                                  </button> -->
                                  <?php 
                                } ?>
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

<?php  require_once 'simulaciones_servicios/modal_facturacion.php';?>
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
        <!-- <input type="text" name="nro_factura" id="nro_factura" value="0">
        <input type="text" name="razon_social" id="razon_social" value="0"> -->
        <?php
          // $texto_cuerpo="Estimado cliente,\n\n Le Hacemos el envío de la Factura.\n\nSaludos.";
          // $asunto="ENVIO FACTURA - IBNORCA";

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
  $(document).ready(function(){
    $('#EnviarCorreo').click(function(){    
      codigo_facturacion=document.getElementById("codigo_facturacion").value;
      cod_solicitudfacturacion=document.getElementById("cod_solicitudfacturacion").value;
      nro_factura=document.getElementById("nro_factura").value;
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
        EnviarCorreoAjax(codigo_facturacion,nro_factura,cod_solicitudfacturacion,correo_destino,asunto,mensaje);  
      }
    });
    $('#rechazarSolicitud').click(function(){
      var q=0;var s=0;var u=0;var v=0;
      var cod_solicitudfacturacion=document.getElementById("cod_solicitudfacturacion").value;
      var estado=document.getElementById("estado").value;
      var admin=document.getElementById("admin").value;
      var direccion=document.getElementById("direccion").value;
      var observaciones=$('#observaciones').val();
      var codigo_factura=$('#codigo_factura').val();
      var codigo_comprobante=$('#codigo_comprobante').val();
      var estado_factura=2;
      if(observaciones==null || observaciones==0 || observaciones=='' || observaciones==' '){
        Swal.fire("Informativo!", "Por favor introduzca la observación.", "warning");
      }else{        
        registrarRechazoFactura(cod_solicitudfacturacion,observaciones,estado,admin,direccion,codigo_factura,codigo_comprobante,estado_factura);
      }      
    }); 

  });
</script>