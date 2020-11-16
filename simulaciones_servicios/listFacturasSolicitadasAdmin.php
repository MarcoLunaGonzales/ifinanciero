<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';

$dbh = new Conexion();
$globalAdmin=$_SESSION["globalAdmin"];
if(isset($_GET['q'])){
  $q=$_GET['q'];
  $r=$_GET['r'];  
  $s=$_GET['s'];
  $item_3=$_GET['r'];
  $u=$_GET['u'];
}else{
  $item_3=0;
  $s=0;
  $u=0;
  $q=0;
  // $sqlAreas="";
}
$v=0;
if(isset($_GET['v'])){
  $v=$_GET['v'];
}
// echo $globalUnidad;
if(isset($_GET['s'])){
  $s=$_GET['s'];
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
<input type="hidden" name="id_servicioibnored" value="<?=$q?>" id="id_servicioibnored"/>
<input type="hidden" name="id_servicioibnored_rol" value="<?=$item_3?>" id="id_servicioibnored_rol"/>
<input type="hidden" name="id_servicioibnored_s" value="<?=$s?>" id="id_servicioibnored_s"/>
<input type="hidden" name="id_servicioibnored_u" value="<?=$u?>" id="id_servicioibnored_u"/>
<?php


  //datos registrado de la simulacion en curso
  $sqlX="SELECT sf.*,es.nombre as estado,DATE_FORMAT(sf.fecha_registro,'%d/%m/%Y')as fecha_registro_x,DATE_FORMAT(sf.fecha_solicitudfactura,'%d/%m/%Y')as fecha_solicitudfactura_x FROM solicitudes_facturacion sf join estados_solicitudfacturacion es on sf.cod_estadosolicitudfacturacion=es.codigo where sf.cod_estadosolicitudfacturacion in (6) $sqlAreas order by codigo desc";
  // echo $sqlX;
  $stmt = $dbh->prepare($sqlX); /*and sf.cod_estadosolicitudfacturacion!=5*/
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
$item_1=2709;
  ?>
  <div class="content">
    <div class="container-fluid">
          <div style="overflow-y:scroll;">
              <!-- <div class="col-md-12"> -->
                <div class="card">
                  <div class="card-header card-header-info card-header-icon">
                    <div class="card-icon">
                      <i class="material-icons">content_paste</i>
                    </div>
                    <h4 class="card-title"><b>Gesti&oacute;n de Solicitudes de Facturación</b></h4>
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
                            $btnEstado="btn-default";
                          break;
                          case 2:
                            $btnEstado="btn-danger";
                          break;
                          case 3:
                            $btnEstado="btn-success";
                          break;
                          case 4:
                            $btnEstado="btn-info";
                          break;
                          case 5:
                            $btnEstado="btn-warning";
                          break;
                          case 6:
                            $btnEstado="btn-default";
                          break;
                        }

                        //verificamos si ya tiene factua generada y esta activa                           
                        $stmtFact = $dbh->prepare("SELECT codigo,nro_factura from facturas_venta where cod_solicitudfacturacion=$codigo_facturacion and cod_estadofactura=1");
                        $stmtFact->execute();
                        $resultSimu = $stmtFact->fetch();
                        $codigo_fact_x = $resultSimu['codigo'];
                        $nro_fact_x = $resultSimu['nro_factura'];
                        if ($nro_fact_x==null)$nro_fact_x="-";
                        else $nro_fact_x="F".$nro_fact_x;
                        //sacamos nombre de los detalles
                        $stmtDetalleSol = $dbh->prepare("SELECT cantidad,precio,descripcion_alterna from solicitudes_facturaciondetalle where cod_solicitudfacturacion=$codigo_facturacion");
                        $stmtDetalleSol->execute();
                        $stmtDetalleSol->bindColumn('cantidad', $cantidad);  
                        $stmtDetalleSol->bindColumn('precio', $precio);     
                        $stmtDetalleSol->bindColumn('descripcion_alterna', $descripcion_alterna);  
                        $concepto_contabilizacion="";
                        while ($row_det = $stmtDetalleSol->fetch()){
                          
                          if($tipo_solicitud==2 || $tipo_solicitud==6 || $tipo_solicitud==7){                              
                            $concepto_contabilizacion="CI: ".$ci_estudiante_x." / "; 
                          }
                          $concepto_contabilizacion.=$descripcion_alterna."<br>\n";
                          
                        }
                        $concepto_contabilizacion = (substr($concepto_contabilizacion, 0, 100))."..."; //limite de string
                          $cod_area_simulacion=$cod_area;
                          // $nombre_simulacion='OTROS';
                          $name_area_simulacion=trim(abrevArea($cod_area_simulacion),'-');
                          // --------
                          $responsable=namePersonal($cod_personal);//nombre del personal
                          $nombre_area=trim(abrevArea($cod_area),'-');//nombre del area
                          $nombre_uo=trim(abrevUnidad($cod_unidadorganizacional),' - ');//nombre de la oficina
                          
                          $string_formaspago=obtnerFormasPago($codigo_facturacion);

                          $sumaTotalImporte=obtenerSumaTotal_solicitudFacturacion($codigo_facturacion);
                          
                          ?>
                        <tr>
                         <td><small><?=$nombre_uo;?> - <?=$nombre_area;?></small></td>
                          <td class="text-right"><small><?=$nro_correlativo;?></small></td>
                          <td><small><?=$responsable;?></small></td>
                          <td><small><?=$codigo_alterno?></small></td>
                          <td><small><?=$fecha_registro;?></small></td>
                          <td class="text-right"><small><?=formatNumberDec($sumaTotalImporte);?></small></td>                            
                          <td><small><small><?=$razon_social;?></small></small></td>
                          <td><small><small><?=$concepto_contabilizacion?></small></small></td>
                          <td><button class="btn btn-danger btn-sm btn-link" style="padding:0;"><small><?=$obs_devolucion;?></small></button></td>
                          <td style="color:#298A08;"><small><?=$nro_fact_x;?><br><span style="color:#DF0101;">-</span></small></td>
                          <td class="text-left" style="color:#ff0000;"><small><?=$string_formaspago;?></small></td>
                          <td class="td-actions text-right">                              
                            <!-- <button class="btn <?=$btnEstado?> btn-sm btn-link"><small><?=$estado;?></small></button><br> -->
                            <div class="btn-group dropdown">
                              <button type="button" class="btn <?=$btnEstado?> dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                                 <i class="material-icons" >list</i><small><?=$estado;?></small>
                              </button>
                              <div class="dropdown-menu" >  
                            <?php                              
                              if($codigo_fact_x>0){//print facturas
                                  ?>
                                  <!-- <a class="btn btn-success" href='<?=$urlGenerarFacturasPrint;?>?codigo=<?=$codigo_facturacion;?>&tipo=2' target="_blank"><i class="material-icons" title="Imprimir Factura">print</i></a> -->
                                  <!-- <a class="btn btn-danger" href='<?=$urlAnularFactura;?>&codigo=<?=$codigo_facturacion;?>' ><i class="material-icons" title="Anular Factura">delete</i></a> -->
                                  
                              <?php }else{// generar facturas ?>                                      
                                    <?php
                                    if(isset($_GET['q'])){ ?>
                                          <a title="Enviar a contabilidad(Revisado)" href='#'  class="btn btn-info" onclick="alerts.showSwal('warning-message-and-confirmationGeneral','<?=$urlEdit2Sol?>?cod=<?=$codigo_facturacion?>&estado=4&admin=20&q=<?=$q?>&v=<?=$item_3?>&s=<?=$s?>&u=<?=$u?>')">
                                            <i class="material-icons">send</i>
                                          </a>                                            
                                        <?php
                                    }else{
                                        ?>
                                          <a title="Enviar a contabilidad(Revisado)" href='#'  class="btn btn-info" onclick="alerts.showSwal('warning-message-and-confirmationGeneral','<?=$urlEdit2Sol?>?cod=<?=$codigo_facturacion?>&estado=4&admin=20')">
                                            <i class="material-icons">send</i>
                                          </a>                                            
                                       <?php  
                                    } ?>
                                    <?php $datos_devolucion=$codigo_facturacion."###".$nro_correlativo."###".$codigo_alterno."###1###20###".$urlEdit2Sol;?>
                                    <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modalDevolverSolicitud_intranet" onclick="modalDevolverSolicitud_regional('<?=$datos_devolucion;?>')">
                                      <i class="material-icons" title="Devolver Solicitud Facturación">settings_backup_restore</i>
                                    </button>

                                 <?php                                      
                              }                                  
                              //}
                            ?>      
                            <a class="btn btn-danger" href='<?=$urlPrintSolicitud;?>?codigo=<?=$codigo_facturacion;?>' target="_blank"><i class="material-icons" title="Imprimir Solicitud Facturación">print</i></a>
                            <a href="<?=$urlVer_SF;?>?codigo=<?=$codigo_facturacion;?>" target="_blank" class="btn btn-info" title="Ver Solicitud">
                              <i class="material-icons">remove_red_eye</i>
                            </a>
                            <a href='#' title="Archivos Adjuntos" class="btn btn-primary" onclick="abrirArchivosAdjuntos('<?=$datos_otros;?>')"><i class="material-icons" ><?=$iconFile?></i></a>
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
                  <div class="card-footer fixed-bottom">
                    <?php                 
                      if(isset($_GET['q'])){?>                      
                        <a href='<?=$urlSolicitudfacturaAreas;?>&q=<?=$q?>&v=<?=$v?>&s=<?=$s?>&u=<?=$u?>' class="btn btn-default">SF Histórico Area</a>
                        <?php 
                      }else{?>                        
                        <a href='<?=$urlSolicitudfacturaAreas?>' class="btn btn-default">SF Histórico Area</a>
                        <?php 
                      }              
                    ?>
                  </div>        
                </div>     
              <!-- </div> -->
          </div>  
    </div>
  </div>

<?php  require_once 'simulaciones_servicios/modal_subir_archivos.php';?>

<!-- small modal -->
<!-- modal devolver solicitud -->
<div class="modal fade" id="modalDevolverSolicitud_intranet" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Devolver Solicitud</h4>
      </div>
      <div class="modal-body">        
        <input type="hidden" name="cod_solicitudfacturacion" id="cod_solicitudfacturacion" value="0">
        <input type="hidden" name="estado" id="estado" value="0">
        <input type="hidden" name="admin" id="admin" value="0">
        <input type="hidden" name="direccion" id="direccion" value="0">

        <input type="hidden" name="id_servicioibnored_modal" value="0" id="id_servicioibnored_modal"/>
        <input type="hidden" name="id_servicioibnored_rol_modal" value="0" id="id_servicioibnored_rol_modal"/>
        <input type="hidden" name="id_servicioibnored_s_modal" value="0" id="id_servicioibnored_s_modal"/>
        <input type="hidden" name="id_servicioibnored_u_modal" value="0" id="id_servicioibnored_u_modal"/>
        <div class="row">
          <label class="col-sm-1 col-form-label" style="color:#7e7e7e"><small>Nro. Solicitud</small></label>
          <div class="col-sm-2">
            <div class="form-group" >
              <input type="text" class="form-control" name="nro_solicitud" id="nro_solicitud" readonly="true" style="background-color:#e2d2e0">              
            </div>
          </div>
          <label class="col-sm-1 col-form-label" style="color:#7e7e7e"><small>Código<br>Servicio</small></label>
          <div class="col-sm-8">
            <div class="form-group" >              
              <input type="text" class="form-control" name="codigo_servicio" id="codigo_servicio" readonly="true" style="background-color:#e2d2e0">
            </div>
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
        <button type="button" class="btn btn-success" id="rechazarSolicitud" name="rechazarSolicitud" data-dismiss="modal">Aceptar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"> <-- Volver </button>
      </div>
    </div>
  </div>
</div>
<!-- para la factura manual -->
<script type="text/javascript">
  $(document).ready(function(){    
    $('#rechazarSolicitud').click(function(){      
      var cod_solicitudfacturacion=document.getElementById("cod_solicitudfacturacion").value;
      var estado=document.getElementById("estado").value;
      var admin=document.getElementById("admin").value;
      var direccion=document.getElementById("direccion").value;
      var observaciones=$('#observaciones').val();


      var q=document.getElementById("id_servicioibnored_modal").value;
      var r=document.getElementById("id_servicioibnored_rol_modal").value;
      var s=document.getElementById("id_servicioibnored_s_modal").value;
      var u=document.getElementById("id_servicioibnored_u_modal").value;
      if(observaciones==null || observaciones==0 || observaciones=='' || observaciones==' '){
        Swal.fire("Informativo!", "Por favor introduzca la observación.", "warning");
      }else{                
          registrarRechazoSolicitud_intranet(cod_solicitudfacturacion,observaciones,estado,admin,direccion,q,r,s,u);
      }      
    }); 
  });
</script>


  