<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';
$globalAdmin=$_SESSION["globalAdmin"];
$globalUser=$_SESSION["globalUser"];
$globalNombreGestion=$_SESSION["globalNombreGestion"];
$globalMesActivo=$_SESSION['globalMes'];
$userAdmin=obtenerValorConfiguracion(74);
$dbh = new Conexion();
if(isset($_GET['q'])){
  $q=$_GET['q'];
  $item_3=$_GET['r'];
  $s=$_GET['s'];
  $u=$_GET['u'];

    $arraySql=explode("IdArea=",$s);
    $codigoArea=trim($arraySql[1]);
    $sqlAreas="and sr.cod_area=".$codigoArea;

    // $sqlAreas=""; quitar cuando se registre la unidad y el area de la solicitud propuesta
  ?>
  <input type="hidden" name="id_servicioibnored" value="<?=$q?>" id="id_servicioibnored"/>
  <input type="hidden" name="id_servicioibnored_rol" value="<?=$item_3?>" id="id_servicioibnored_rol"/>
  <input type="hidden" name="id_servicioibnored_s" value="<?=$s?>" id="id_servicioibnored_s"/>
  <input type="hidden" name="id_servicioibnored_u" value="<?=$u?>" id="id_servicioibnored_u"/>
<?php
}else{
  //$item_3=obtenerIdRolDeIbnorca($globalUser);
  $item_3=79;
  $s=0;
  $u=0;
  $sqlAreas="";
}
$stmt = $dbh->prepare("SELECT l.* FROM (SELECT sr.*,es.nombre as estado,u.abreviatura as unidad,a.abreviatura as area,(select count(*) from solicitud_recursosdetalle where cod_solicitudrecurso=sr.codigo and (cod_unidadorganizacional=3000 or cod_area=1235)) as sis_detalle 
  from solicitud_recursos sr join estados_solicitudrecursos es on sr.cod_estadosolicitudrecurso=es.codigo join unidades_organizacionales u on sr.cod_unidadorganizacional=u.codigo join areas a on sr.cod_area=a.codigo 
  where sr.cod_estadoreferencial=1 and sr.cod_estadosolicitudrecurso in (5,8,9)) l  
where !(l.cod_unidadorganizacional=3000 or l.cod_area=1235 or l.sis_detalle>0)  order by l.revisado_contabilidad,l.numero desc limit 50");
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('unidad', $unidad);
$stmt->bindColumn('area', $area);
$stmt->bindColumn('fecha', $fecha);
$stmt->bindColumn('cod_personal', $codPersonal);
$stmt->bindColumn('cod_simulacion', $codSimulacion);
$stmt->bindColumn('cod_proveedor', $codProveedor);
$stmt->bindColumn('cod_estadosolicitudrecurso', $codEstado);
$stmt->bindColumn('estado', $estado);
$stmt->bindColumn('cod_comprobante', $codComprobante);
$stmt->bindColumn('cod_simulacionservicio', $codSimulacionServicio);
$stmt->bindColumn('numero', $numeroSol);
$stmt->bindColumn('idServicio', $idServicio);
$stmt->bindColumn('devengado', $devenX);
$item_1=2708;
?>
<div class="cargar-ajax d-none">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold" id="texto_ajax_titulo">Procesando Datos</h4>
     <p class="text-white">Aguard&aacute; un momento por favor</p>  
  </div>
</div>

 <style>
  #tablePaginator100NoFidexHead_filter{
         display: none !important;
       }      
</style>

<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header card-header-primary card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">history</i>
                  </div>
                  <h4 class="card-title"><b><b style="color:#732590;">HISTÓRICO SR CONTABILIDAD</b>
                     <a href="#" class="btn btn-warning btn-round btn-fab btn-sm" data-toggle="modal" data-target="#modalBuscarSolicitudRecurso">
                      <i class="material-icons" title="Buscador Avanzado">search</i>
                    </a>
                  </h4>
                </div>
                <div class="card-body table-responsive">
                     <table class="table table-condesed" id="tablePaginator100NoFidexHead">
                      <thead>
                        <tr style="background:#732590;color:white;">
                          <th>Of. - Area</th>
                          <th>Nº Sol.</th>
                          <th>Cod. Servicio</th>
                          <!--<th>Cliente</th>-->
                          <th>Proveedor</th>
                          <th>Cuenta</th>
                          <th>Solicitante</th>
                          <th>Fecha</th>
                          <th>Personal Pago</th>
                          <th class="text-right">Actions</th>
                        </tr>
                      </thead>
                      <tbody id="cuerpo_historico">
<?php
            $index=1;
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                          $solicitante=namePersonal($codPersonal);
                          switch ($codEstado) {
                            case 1:
                              $nEst=40;$barEstado="progress-bar-default";$btnEstado="btn-default";
                            break;
                            case 2:
                              $nEst=10;$barEstado="progress-bar-danger";$btnEstado="btn-danger";
                            break;
                            case 3:
                              $nEst=100;$barEstado="progress-bar-success";$btnEstado="btn-success";
                            break;
                            case 4:
                              $nEst=60;$barEstado="progress-bar-warning";$btnEstado="btn-warning";
                            break;
                            case 5:
                              $nEst=100;$barEstado="progress-bar-primary";$btnEstado="btn-primary";
                            break;
                            case 6:
                              $nEst=50;$barEstado="progress-bar-default";$btnEstado="btn-default";
                            break;
                            case 7:
                              $nEst=55;$barEstado="progress-bar-info";$btnEstado="btn-info";
                            break;
                            case 8:
                              $nEst=100;$barEstado="progress-bar-default";$btnEstado="btn-deafult";
                            break;
                            case 9:
                              $nEst=100;$barEstado="progress-bar-default";$btnEstado="btn-deafult";
                            break;
                          }
                          if($codSimulacion!=0){
                           $nombreCliente="Sin Cliente";
                           $nombreSimulacion=nameSimulacion($codSimulacion);
                          }else{
                           $nombreCliente=nameClienteSimulacionServicio($codSimulacionServicio);
                           $nombreSimulacion=nameSimulacionServicio($codSimulacionServicio);
                          }
                          $codigoServicio="-";
                          $sql="SELECT codigo FROM ibnorca.servicios where idServicio=$idServicio";
                          $stmt1=$dbh->prepare($sql);
                          $stmt1->execute();
                           while ($row1 = $stmt1->fetch(PDO::FETCH_ASSOC)) {
                             $codigoServicio=$row1['codigo'];
                           }
                      $numeroSolTitulo=$numeroSol;
                       if(verificarMontoPresupuestadoSolicitadoSR($codigo)==1){
                        $numeroSolTitulo='<a href="#" title="El Monto Solicitado es Mayor al Presupuestado" class="btn btn-warning btn-sm btn-round">'.$numeroSol.'</a>';
                       }
                       $otrosPagosCuenta=comprobarCuentasOtrosPagosDeSolicitudRecursos($codigo);
                       $montoDetalleSoliditud=number_format(obtenerSumaDetalleSolicitud($codigo),2,'.',',');
                       $arrayEnc=implode(',',obtenerPersonalEncargadoSolicitud($codigo)[0]);

                       $nombreProveedor=obtenerNombreConcatenadoProveedorDetalleSolicitudRecurso($codigo);
                       
                       $estiloComprobante="btn btn-primary";
                       if($devenX==0){
                          $estiloComprobante="btn btn-info";
                       }

                       if(obtenerUnidadSolicitanteRecursos($codigo)==3000||obtenerAreaSolicitanteRecursos($codigo)==obtenerValorConfiguracion(65)||obtenerDetalleRecursosSIS($codigo)>0){
                        $numeroSolTitulo='<a href="#" title="SOLICITUD DE RECURSOS SIS" class="btn btn-rose btn-sm btn-round">'.$numeroSol.'</a>';
                       }
                       $codCajaChica=0;                              
                       $codigoDetalleCajaChica=obtenerCodigosCajaChicaSolicitudRecursos($codigo);
                       $tituloComprobanteDev="COMPROBANTE - DEVENGADO";
                       if($codEstado==9){
                         $codCajaChica=obtenerCodigoCajaChicaString($codigoDetalleCajaChica);  
                         $codComprobante=obtenerComprobanteCajaChicaRelacionado($codCajaChica);
                         $tituloComprobanteDev="COMPROBANTE - CAJA CHICA";
                       }

?>
                        <tr>
                          <td><?=$unidad;?> - <?=$area;?></td>
                          <td class="font-weight-bold"><?=$numeroSolTitulo;?></td>
                          <td><?=$codigoServicio;?></td>
                          <!--<td><?=$nombreCliente;?></td>-->
                          <td><small><?=obtenerNombreConcatenadoProveedorDetalleSolicitudRecurso($codigo)?></small></td>
                          <td><small><?=obtenerNombreConcatenadoCuentaDetalleSolicitudRecurso($codigo)?></small></td>
                          <td>
                                 <img src="assets/img/faces/persona1.png" width="20" height="20"/><?=$solicitante;?>
                          </td>
                          <td><?=strftime('%d/%m/%Y',strtotime($fecha));?></td>
                          <td class="text-muted font-weight-bold"><small><b><?=obtenerNombreConcatenadoEncargadoSolicitudRecurso($codigo)?></b></small></td>
                          <td class="td-actions text-right">
                            <a title="Imprimir" href='#' onclick="javascript:window.open('<?=$urlImp;?>?sol=<?=$codigo;?>&mon=1')" class="<?=$buttonEdit;?>">
                              <i class="material-icons"><?=$iconImp;?></i>
                            </a>
                            <?php 
                            if($codEstado==9&&$codCajaChica>0){
                              ?><a title="Imprimir Caja Chica" href='#' onclick="javascript:window.open('<?=$urlImpCaja;?>?codigo=<?=$codCajaChica;?>')" class="btn btn-default">
                                  <i class="material-icons"><?=$iconImp;?></i>
                               </a><?php                               
                            }                                                                
                                   if($codComprobante!=0&&($codEstado==5||$codEstado==8||$codEstado==9)){
                                   ?>
                                   <div class="btn-group dropdown">
                                     <button type="button" class="btn <?=$estiloComprobante?> dropdown-toggle" title="<?=$tituloComprobanteDev?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                       <i class="material-icons"><?=$iconImp;?></i>
                                     </button>
                                    <div class="dropdown-menu menu-fixed-sm-table">
                                       <a href="#" onclick="javascript:window.open('<?=$urlImpComp;?>?comp=<?=$codComprobante;?>&mon=-1')" class="dropdown-item">
                                                 <i class="material-icons text-muted">monetization_on</i> BIMONETARIO (Bs - Usd)
                                      </a>
                                      <div class="dropdown-divider"></div>
                                      <?php
                                        $stmtMoneda = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM monedas where cod_estadoreferencial=1 order by 2");
                                       $stmtMoneda->execute();
                                       while ($row = $stmtMoneda->fetch(PDO::FETCH_ASSOC)) {
                                         $codigoX=$row['codigo'];
                                         $nombreX=$row['nombre'];
                                         $abrevX=$row['abreviatura'];
                                            ?>
                                             <a href="#" onclick="javascript:window.open('<?=$urlImpComp;?>?comp=<?=$codComprobante;?>&mon=<?=$codigoX?>')" class="dropdown-item">
                                                 <i class="material-icons">keyboard_arrow_right</i> <?=$abrevX?>
                                             </a> 
                                           <?php
                                         }
                                         ?>
                                    </div>
                                  </div> 
                                   <?php 
                                   //opciones Admin
                                    if(verificarEdicionComprobanteUsuario($globalUser)!=0&&$codEstado!=9){
                                    ?>
                                    <a title="Editar Personal Procesar Pago" onclick="contabilizarSolicitudRecursoModal(<?=$codigo?>,2,<?=$numeroSol?>,'<?=$montoDetalleSoliditud?>','<?=obtenerNombreConcatenadoCuentaDetalleSolicitudRecurso($codigo)?>','<?=$urlEncargado?>?admin=0&cod=<?=$codigo?>','<?=$nombreProveedor?>','<?=$arrayEnc?>');return false;" target="_blank" class="btn btn-default">
                                      <i class="material-icons text-dark">people_alt</i>
                                    </a> 
                                    <?php  
                                      if(verificarMesEnCursoSolicitudRecursos($codigo)!=0){
                                         if($codEstado==8&&$devenX==0&&$otrosPagosCuenta==0){
                                      ?>
                                      <a title="Contabilizar Solicitud a Pagado" onclick="contabilizarSolicitudRecursoModal(<?=$codigo?>,1,<?=$numeroSol?>,'<?=$montoDetalleSoliditud?>','<?=obtenerNombreConcatenadoCuentaDetalleSolicitudRecurso($codigo)?>','<?=$urlConta?>?admin=0&cod=<?=$codigo?>&existe=<?=$codComprobante?>&deven=0','<?=$nombreProveedor?>','<?=$arrayEnc?>');return false;" href='#'  class="btn btn-info">
                                      <i class="material-icons">assignment_turned_in</i>
                                       </a>
                                      <?php
                                      }else{
                                     ?>
                                    <a title="Editar Solicitud" href="<?=$urlVerificarSolicitud?>?cod=<?=$codigo?>&admin=2" target="_blank" class="btn btn-warning">
                                      <i class="material-icons text-dark">vpn_key</i><i class="material-icons text-dark">lock_open</i>
                                    </a>
                                    <?php 
                                       if($otrosPagosCuenta==0){
                                       ?>
                                       <a title="Contabilizar Solicitud a Devengado" onclick="contabilizarSolicitudRecursoModal(<?=$codigo?>,1,<?=$numeroSol?>,'<?=$montoDetalleSoliditud?>','<?=obtenerNombreConcatenadoCuentaDetalleSolicitudRecurso($codigo)?>','<?=$urlConta?>?admin=0&cod=<?=$codigo?>&existe=<?=$codComprobante?>&deven=1','<?=$nombreProveedor?>','<?=$arrayEnc?>');return false;" href='#'  class="btn btn-danger">
                                         <i class="material-icons">assignment_turned_in</i>
                                       </a>
                                      <?php                                     
                                        }  
                                      } 
                                     
                                     }else{//if mes en curso
                                       //si tiene permisos pero no está en el mes en curso
                                      ?><a title="Solicitud No Editable" href='#'  class="btn <?=$estiloComprobante?>">
                                      <i class="material-icons text-dark">lock</i>
                                    </a><?php
                                      }
                                    }     
                                   }
                              ?>
                            <div class="btn-group dropdown">
                              <button type="button" class="btn <?=$btnEstado?> dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="material-icons">list</i> <?=$estado;?>
                              </button>
                              <div class="dropdown-menu menu-fixed-sm-table">
                                <?php
                                if($codEstado==9){
                                  $codigoDetalleCajaChica=obtenerCodigosCajaChicaSolicitudRecursos($codigo);
                                  $sqlCaja="SELECT codigo,cod_cuenta,fecha,DATE_FORMAT(fecha,'%d/%m/%Y')as fecha_x,cod_tipodoccajachica,cod_uo,cod_area,
                                           (select pc.nombre from plan_cuentas pc where pc.codigo=cod_cuenta) as nombre_cuenta,
                                           (select td.numero from caja_chica td where td.codigo=cod_cajachica) as nombre_cajachica,
                                           (select td.abreviatura from configuracion_retenciones td where td.codigo=cod_tipodoccajachica) as nombre_tipodoccajachica,nro_documento,(select CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=cod_personal)as cod_personal,monto,monto_rendicion,observaciones,cod_estado,(select c.nombre from af_proveedores c where c.codigo=cod_proveedores)as cod_proveedores,nro_recibo
                                         from caja_chicadetalle 
                                         where codigo in ($codigoDetalleCajaChica) and cod_estadoreferencial=1 ORDER BY nro_documento desc";
                                  $stmtCaja=$dbh->prepare($sqlCaja);
                                  $stmtCaja->execute();
                                   while ($rowCaja = $stmtCaja->fetch(PDO::FETCH_ASSOC)) {
                                     $numeroRecibo=$rowCaja['nro_recibo'];
                                     $nombre_tipodoccajachica=$rowCaja['nombre_tipodoccajachica'];
                                     $numeroCaja=$rowCaja['nombre_cajachica'];
                                     ?><a href="#" target="_blank"  class="dropdown-item">
                                        <i class="material-icons" style="color:#37474f;">home_work</i> C. CHICA: <?=$nombre_tipodoccajachica?> (<?=$numeroCaja?>)    Recibo:<?=$numeroRecibo?>
                                       </a>
                                    <?php 
                                   }
                                  
                                }
                              if(isset($_GET['q'])){
                                ?><a href="<?=$urlVer;?>?cod=<?=$codigo;?>&admin=2&q=<?=$q?>&r=<?=$item_3?>&s=<?=$s?>&u=<?=$u?>" target="_blank"  class="dropdown-item">
                                    <i class="material-icons text-info">bar_chart</i> Ver Solicitud
                                 </a>
                              
                                <?php 
                                if($codEstado==4){
                                 ?>
                                 <a href="#" targetonclick="mostrarCambioEstadoObjeto(<?=$codigo?>)" class="dropdown-item">
                                    <i class="material-icons text-warning">dns</i> Cambiar Estado
                                 </a>
                                 <a targethref="<?=$urlVerificarSolicitud?>?cod=<?=$codigo?>&admin=2&q=<?=$q?>&r=<?=$item_3?>&s=<?=$s?>&u=<?=$u?>&v=<?=$idServicio?>" class="dropdown-item" target="_blank">
                                    <i class="material-icons text-success">edit</i> Editar Solicitud
                                 </a><?php 
                                }else{
                                  ?>
                                 
                                 <?php
                                ?>
                                 <?php 
                                }
                              }else{
                                ?><a href="<?=$urlVer;?>?cod=<?=$codigo;?>&admin=2" class="dropdown-item" target="_blank">
                                    <i class="material-icons text-info">bar_chart</i> Ver Solicitud
                                 </a>
                                 <?php 
                                if($otrosPagosCuenta>0&&($codEstado==5)){
                                 ?>
                                 <a title="Pagar Solicitud"  href="#" onclick="alerts.showSwal('warning-message-and-confirmationGeneral','<?=$urlEdit2?>?cod=<?=$codigo?>&conta=2&estado=8')" class="dropdown-item">
                                      <i class="material-icons text-info">dns</i> <b class="text-muted">Cambiar a <u class="text-info">Pagado</u></b>
                                    </a>
                                <?php 
                                  
                                }
                                if($codEstado==4){
                                 ?>
                                 <a href="#" onclick="mostrarCambioEstadoObjeto(<?=$codigo?>)" class="dropdown-item">
                                    <i class="material-icons text-warning">dns</i> Cambiar Estado
                                 </a>
                                 <a href="<?=$urlVerificarSolicitud?>?cod=<?=$codigo?>&admin=2" target="_blank" class="dropdown-item">
                                    <i class="material-icons text-success">edit</i> Editar Solicitud
                                 </a><?php 
                                }else{
                                ?>
                                 <?php 
                                }
                                
                              }
                                 ?>
                                
                              </div>
                             </div>
                          </td> 
                        </tr>
<?php
              $index++;
            }
?>
                      </tbody>
                    </table>
                    <br><br><br><br><br><br><br><br>
                </div>
              </div>
              <div class="card-footer fixed-bottom col-sm-9">
                <a href="<?=$urlList4?>" class="btn btn-danger float-right"> Volver</a>
              </div>    
            </div>
          </div>  
        </div>
    </div>
    <!-- small modal -->
<div class="modal fade modal-arriba modal-primary" id="modalEstadoObjeto" tabindex="-1" style="z-index:99999" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-notice" style="max-width: 50% !important;">
    <div class="modal-content card">
                <div class="card-header card-header-warning card-header-text">
                  <div class="card-text">
                    <h4>Cambiar de Estado</h4>
                  </div>
                  <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
                    <i class="material-icons">close</i>
                  </button>
                </div>
                <input type="hidden" class="form-control" name="modal_adminconta" id="modal_adminconta" value="">
                <input type="hidden" class="form-control" name="modal_codigopropuesta" id="modal_codigopropuesta" value="">
                <input type="hidden" class="form-control" name="modal_tipoobjeto" id="modal_tipoobjeto" value="<?=$item_1?>">
                <input type="hidden" class="form-control" name="modal_rolpersona" id="modal_rolpersona" value="<?=$item_3?>">
                <div class="card-body">
                 <div class="card-body">
                      <div class="row">
                       <label class="col-sm-2 col-form-label">Estado</label>
                       <div class="col-sm-10">
                        <div class="form-group">
                             <select class="selectpicker form-control" name="modal_codigoestado" id="modal_codigoestado" data-style="btn btn-primary">
                                  
                             </select>
                         </div>
                        </div>
                      </div>
                      <div class="row">
                          <label class="col-sm-2 col-form-label">Observaciones</label>
                           <div class="col-sm-10">                     
                             <div class="form-group">
                               <textarea type="text" class="form-control" name="modal_observacionesestado" id="modal_observacionesestado"></textarea>
                             </div>
                           </div>  
                      </div> 
                      <div class="form-group float-right">
                        <button type="button" id="boton_guardarsim" class="btn btn-default" onclick="cambiarEstadoObjetoSol()">Cambiar Estado</button>
                      </div> 
                </div>   
                </div>
      </div>  
    </div>
  </div>

<!-- modal devolver solicitud -->
<div class="modal fade" id="modalDevolverSolicitudRecurso" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Rechazar Solicitud</h4>
      </div>
      <div class="modal-body">        
        <input type="hidden" name="urlEnvioModal" id="urlEnvioModal" value="">
        <div class="row">
          <label class="col-sm-1 col-form-label" style="color:#7e7e7e"><span id="campo_nro_fact"><small>Nro.<br>Solicitud.</small></span></label>
          <div class="col-sm-2">
            <div class="form-group" >
              <input type="text" class="form-control" name="nro_solicitud" id="nro_solicitud" readonly="true" style="background-color:#e2d2e0">              
            </div>
          </div>
          <label class="col-sm-1 col-form-label" style="color:#7e7e7e"><span id="campo_rs_fact"><small >Código<br>Servicio</small></span></label>
          <div class="col-sm-8">
            <div class="form-group" >              
              <input type="text" class="form-control" name="codigo_servicio" id="codigo_servicio" readonly="true" style="background-color:#e2d2e0">
            </div>
          </div>
        </div> 
        <div class="row">
          <label class="col-sm-1 col-form-label" style="color:#7e7e7e"><span id="campo_proveedor"><small>Proveedor</small></span></label>
          <div class="col-sm-11">
            <div class="form-group" >
              <input type="text" class="form-control" name="proveedor_nombre" id="proveedor_nombre" readonly="true" style="background-color:#e2d2e0">              
            </div>
          </div>
        </div>                
        <div class="row">
          <label class="col-sm-12 col-form-label" style="color:#7e7e7e"><small>Observaciones</small></label>
        </div>
        <div class="row">
          <div class="col-sm-12" style="background-color:#f9edf7">
            <div class="form-group" >              
              <textarea type="text" name="observaciones_modal" id="observaciones_modal" class="form-control" required="true"></textarea>
            </div>
          </div>
        </div>        
      </div>
      <div class="modal-footer">
        <a href="#" class="btn btn-success" onclick="devolverSolicitudRecursoModal()">Aceptar</a>
        <button type="button" class="btn btn-danger" data-dismiss="modal"> Volver </button>
      </div>
    </div>
  </div>
</div>
<!-- modal reenviar solicitud devuelto -->


<!-- modal devolver solicitud -->
<div class="modal fade" id="modalContabilizarSolicitudRecurso" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" id="cabecera_conta" style="background:#DA053C !important;color:#fff;">
        <h4 class="modal-title" id="titulo_conta">Contabilizar Solicitud Recurso</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> 
      </div>
      <div class="modal-body">        
        <input type="hidden" name="urlEnvioModalConta" id="urlEnvioModalConta" value="">
        <div class="row">
          <label class="col-sm-1 col-form-label" style="color:#7e7e7e"><span id="campo_nro_fact_conta"><small>Nro.<br>Solicitud.</small></span></label>
          <div class="col-sm-2">
            <div class="form-group" >
              <input type="text" class="form-control" name="nro_solicitud_conta" id="nro_solicitud_conta" readonly="true" style="background-color:#e2d2e0">              
            </div>
          </div>
          <label class="col-sm-1 col-form-label" style="color:#7e7e7e"><span id="campo_cuentas_conta"><small >Cuentas</small></span></label>
          <div class="col-sm-8">
            <div class="form-group" >              
              <input type="text" class="form-control" name="cuenta_conta" id="cuenta_conta" readonly="true" style="background-color:#e2d2e0">
            </div>
          </div>
        </div> 
        <div class="row">
          <label class="col-sm-1 col-form-label" style="color:#7e7e7e"><span id="campo_proveedor_conta"><small>Proveedor</small></span></label>
          <div class="col-sm-8">
            <div class="form-group" >
              <input type="text" class="form-control" name="proveedor_nombre_conta" id="proveedor_nombre_conta" readonly="true" style="background-color:#e2d2e0">              
            </div>
          </div>
           <label class="col-sm-1 col-form-label" style="color:#7e7e7e"><span id="campo_monto_conta"><small>Monto</small></span></label>
          <div class="col-sm-2">
            <div class="form-group" >
              <input type="text" class="form-control" name="monto_nombre_conta" id="monto_nombre_conta" readonly="true" style="background-color:#e2d2e0">              
            </div>
          </div>
        </div>                
        <div class="row">
          <label class="col-sm-12 col-form-label" style="color:#7e7e7e"><small>Persona que Procesará el Pago</small></label>
        </div>
        <div class="row">
          <div class="col-sm-12" style="background-color:#f9edf7">
            <div class="form-group" >              
              <select class="selectpicker form-control form-control-sm" name="personal_encargado" id="personal_encargado" data-live-search="true" data-size="6" data-style="btn btn-default text-dark">
                         <option value="-1">Ninguno</option>
                        <?php 
                         $stmt3 = $dbh->prepare("SELECT p.codigo,CONCAT_WS(' ',p.primer_nombre,p.paterno,p.materno)as nombre from personal p join configuracion_encargado c on c.cod_personal=p.codigo order by 2");
                         $stmt3->execute();
                          while ($rowSel = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                           $codigoSel=$rowSel['codigo'];
                          $nombreSelX=$rowSel['nombre'];
                          ?><option value="<?=$codigoSel;?>"><?=$nombreSelX?></option><?php 
                          }
                        ?>
                    </select>
            </div>
          </div>
        </div>        
      </div>
      <div class="modal-footer">
        <a href="#" class="btn btn-success" onclick="saveContaSolicitudRecursoModal()">Guardar</a>
        <button type="button" class="btn btn-danger" data-dismiss="modal"> Volver </button>
      </div>
    </div>
  </div>
</div>
<!-- modal reenviar solicitud devuelto -->

<!-- modal devolver solicitud -->
<div class="modal fade" id="modalBuscarSolicitudRecurso" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" id="cabecera_conta" style="background:#732590; !important;color:#fff;">
        <h4 class="modal-title" id="titulo_conta">Buscar Solicitud Recurso</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> 
      </div>
      <div class="modal-body">        
        <div class="row">
          <label class="col-sm-1 col-form-label" style="color:#7e7e7e"><span id=""><small>Nro.<br>Solicitud.</small></span></label>
          <div class="col-sm-2">
            <div class="form-group" >
              <input type="text" class="form-control" name="buscar_nro_solicitud_conta" id="buscar_nro_solicitud_conta" style="background-color:#e2d2e0">              
            </div>
          </div>
          <label class="col-sm-1 col-form-label" style="color:#7e7e7e"><span id=""><small >Cuentas</small></span></label>
          <div class="col-sm-8">
            <div class="form-group" >              
                <select class="selectpicker form-control form-control-sm" data-live-search="true" title="-- Elija una cuenta --" name="buscar_cuenta[]" id="buscar_cuenta" multiple data-actions-box="true" data-style="select-with-transition" data-actions-box="true" required>
                                    <?php
                                                $cuentaLista=obtenerCuentasListaSolicitud(); //null para todas las iniciales del numero de cuenta obtenerCuentasLista(5,[5,4]);
                                              while ($rowCuenta = $cuentaLista->fetch(PDO::FETCH_ASSOC)) {
                                                $codigoX=$rowCuenta['codigo'];
                                                $numeroX=$rowCuenta['numero'];
                                                $nombreX=$rowCuenta['nombre'];
                                              ?>
                                              <option value="<?=$codigoX;?>" selected >[<?=$numeroX?>] <?=$nombreX;?></option>  
                                              <?php
                                                }
                                                ?>
                         </select>
            </div>
          </div>
        </div> 
        <div class="row">
                    <div class="col-sm-6">
                      <div class="row">
                       <label class="col-sm-2 col-form-label" style="color:#7e7e7e"><small>Oficina</small></label>
                       <div class="col-sm-10">
                        <div class="form-group">
                              <select class="selectpicker form-control form-control-sm" name="buscar_unidad_solicitud[]" id="buscar_unidad_solicitud" data-style="select-with-transition" multiple data-actions-box="true" required data-live-search="true">
                                      <?php
                                   $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM unidades_organizacionales where cod_estado=1 and centro_costos=1 order by 2");
                                   $stmt->execute();
                                   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $codigoX=$row['codigo'];
                                    $nombreX=$row['nombre'];
                                    $abrevX=$row['abreviatura'];
                                    ?><option selected value="<?=$codigoX;?>"><?=$abrevX;?></option><?php 
                                       
                                      }
                                    ?>
                                   </select>                           
                            </div>
                        </div>
                   </div>
                     </div>
                    <div class="col-sm-6">
                      <div class="row">
                       <label class="col-sm-2 col-form-label" style="color:#7e7e7e"><small>Area</small></label>
                       <div class="col-sm-10">
                        <div class="form-group">
                                <select class="selectpicker form-control form-control-sm" name="buscar_area_solicitud[]" id="buscar_area_solicitud" data-style="select-with-transition" multiple data-actions-box="true" required data-live-search="true">
                                     <?php
                                                             
                                           $stmt = $dbh->prepare("SELECT a.codigo, a.nombre, a.abreviatura FROM areas a where a.cod_estado=1 order by 2");
                                         $stmt->execute();
                                         $cont=0;
                                         while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                           $codigoX=$row['codigo'];
                                           $nombreX=$row['nombre'];
                                           $abrevX=$row['abreviatura'];
                                           ?><option selected value="<?=$codigoX;?>" selected><?=$abrevX;?></option><?php
                                            
                                         } 
                                         ?>
                                        </select>
                            </div>
                        </div>
                    </div>
              </div>
                  </div><!--div row-->
                
        <div class="row">
          <label class="col-sm-12 col-form-label" style="color:#7e7e7e"><small>Solicitante</small></label>
        </div>
        <div class="row">
          <div class="col-sm-12" style="background-color:#f9edf7">
            <div class="form-group" >              
              <select class="selectpicker form-control form-control-sm" name="buscar_personal[]" id="buscar_personal" data-live-search="true" data-style="select-with-transition" data-size="4" multiple data-actions-box="true" required>  
               <?php
             $stmt = $dbh->prepare("SELECT distinct s.cod_personal,UPPER(CONCAT(p.primer_nombre,' ',p.otros_nombres,' ',p.paterno,' ',p.materno)) as nombre from solicitud_recursos s
join personal p on p.codigo=s.cod_personal
where s.cod_estadoreferencial<>2 order by 2;");
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
              $codigoX=$row['cod_personal'];
              $nombreX=$row['nombre'];
            ?>
            <option value="<?=$codigoX;?>" selected><?=$nombreX;?></option> 
            <?php
               }
               ?>
            </select>
            </div>
          </div>
        </div>        
      </div>
      <br>  
      <div class="modal-footer">
        <a href="#" class="btn btn-success" style="background:#732590 !important;" onclick="buscarSolicitudesDeRecursosHistorial()"><i class="material-icons">search</i> BUSCAR SOLICITUDES</a>
        <!--<button type="button" class="btn btn-danger" data-dismiss="modal"> Volver </button>-->
      </div>
    </div>
  </div>
</div>
<!-- modal reenviar solicitud devuelto -->