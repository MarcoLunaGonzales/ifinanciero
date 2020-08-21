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

  $stmt = $dbh->prepare("SELECT sf.*,es.nombre as estado,DATE_FORMAT(sf.fecha_registro,'%d/%m/%Y')as fecha_registro_x,DATE_FORMAT(sf.fecha_solicitudfactura,'%d/%m/%Y')as fecha_solicitudfactura_x FROM solicitudes_facturacion sf join estados_solicitudfacturacion es on sf.cod_estadosolicitudfacturacion=es.codigo where cod_estadosolicitudfacturacion in (3,4) order by codigo desc");

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
                    <h4 class="card-title"><b>Solicitudes de Facturación Contabilidad ADMIN</b></h4>                    
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
                          $cont_pagosParciales= array();
                          while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {// para la parte de facturas parciales, items de sol_Fact
                            if($globalUser==90 || $globalUser==89 ){
                              $globalAdmin=1;
                            }else{
                              $globalAdmin=0;
                            }
                            
                            $observaciones_string=obtener_string_observaciones($obs_devolucion,$observaciones,$observaciones_2);
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
                            //verificamos si ya tiene factura generada y esta activa                           
                            $stmtFact = $dbh->prepare("SELECT codigo,nro_factura,cod_estadofactura,razon_social,nit,nro_autorizacion,importe from facturas_venta where cod_solicitudfacturacion=$codigo_facturacion and cod_estadofactura in (1,4)");
                            $stmtFact->execute();
                            $resultSimu = $stmtFact->fetch();
                            $codigo_fact_x = $resultSimu['codigo'];
                            $nro_fact_x = $resultSimu['nro_factura'];
                            $cod_estado_factura_x = $resultSimu['cod_estadofactura'];
                            if ($nro_fact_x==null)$nro_fact_x="-";
                            else $nro_fact_x="F".$nro_fact_x;
                            if($cod_estado_factura_x==4){
                              $btnEstado="btn-warning";
                              $estado="FACTURA MANUAL";                            
                            }
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
                            // $dbh1 = new Conexion();
                            // $sqlA="SELECT sf.*,(select t.Descripcion from cla_servicios t where t.IdClaServicio=sf.cod_claservicio) as nombre_serv from solicitudes_facturaciondetalle sf where sf.cod_solicitudfacturacion=$codigo_facturacion";
                            // $stmt2 = $dbh1->prepare($sqlA);                                   
                            // $stmt2->execute(); 
                            // $nc=0;
                            // $sumaTotalMonto=0;
                            // $sumaTotalDescuento_por=0;
                            // $sumaTotalDescuento_bob=0;
                            // while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                            //   // $dato = new stdClass();//obejto
                            //   // $codFila=(int)$row2['codigo'];
                            //   // $cod_claservicioX=trim($row2['nombre_serv']);
                            //   $cantidadX=trim($row2['cantidad']);                              
                            //   // $precioX=(trim($row2['precio'])*$cantidadX);
                            //   $precioX=(trim($row2['precio'])*$cantidadX)+trim($row2['descuento_bob']);
                            //   $descuento_porX=trim($row2['descuento_por']);
                            //   $descuento_bobX=trim($row2['descuento_bob']);
                            //   $nc++;
                            //   $sumaTotalMonto+=$precioX;
                            //   $sumaTotalDescuento_por+=$descuento_porX;
                            //   $sumaTotalDescuento_bob+=$descuento_bobX;
                            // }
                            // $sumaTotalImporte=$sumaTotalMonto-$sumaTotalDescuento_bob;
                            // $cont[$index-1]=$nc;
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
                                <td class="text-left"><small><small><?=$razon_social;?></small></small></td>
                                <td class="text-left"><small><?=$concepto_contabilizacion?></small></td>                                
                                <td>
                                  <?php if($cod_estado_factura_x==3){
                                      $estadofactura=obtener_nombreestado_factura($cod_estadofactura);?>
                                      <span class="badge badge-dark"><small><?=$estadofactura?></small></span><?php
                                  }else{?><small><?=$observaciones_string;?></small><?php 
                                  }?>
                                </td>
                                <td style="color:#298A08;"><small><?=$nro_fact_x;?><br><span style="color:#DF0101;"><?=$cadenaFacturasM;?></span></small></td>
                                <td class="text-left" style="color:#ff0000;"><small><small><?=$string_formaspago;?></small></small></td>
                                <td class="td-actions text-right">
                                  
                                  <div class="btn-group dropdown">
                                  <button type="button" class="btn <?=$btnEstado?> dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                                     <i class="material-icons" >list</i><small><small><?=$estado;?></small></small>
                                  </button>
                                  <div class="dropdown-menu" > 
                                  <?php
                                    if($globalAdmin==1){ 
                                      $datos_edit=$nro_correlativo."###".$sumaTotalImporte."###".$codigo_facturacion."###".$nit."###".$razon_social;?>
                                      <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalEditarSolFac" onclick="modal_editar_sf_conta('<?=$datos_edit;?>')">
                                        <i class="material-icons" title="Editar Forma De Pago">edit</i>
                                      </button>
                                    <?php }
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
                 <!--  <div class="card-footer fixed-bottom col-sm-9">
                    <a href='<?=$urlListHistoricoContabilidad;?>' class="btn btn-info float-right"><i class="material-icons">history</i> Histórico</a>
                  </div>  -->   
                </div>     
              <!-- </div> -->
         <!--  </div>  --> 
      </div>
    </div>
  </div>
<?php  //require_once 'simulaciones_servicios/modal_facturacion.php';?>
<?php  //require_once 'simulaciones_servicios/modal_subir_archivos.php';?>

<div class="modal fade" id="modalEditarSolFac" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <form id="formSoliFact_modal" class="form-horizontal" action="simulaciones_servicios/ajax_tipopago_edit_conta_save.php" method="post" onsubmit="return valida(this)" enctype="multipart/form-data">
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
<!-- para la factura manual -->
<script type="text/javascript">
  function valida(f) {
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
