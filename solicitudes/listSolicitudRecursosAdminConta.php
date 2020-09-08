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
// Preparamos
$stmt = $dbh->prepare("SELECT l.* FROM (SELECT sr.*,es.nombre as estado,u.abreviatura as unidad,a.abreviatura as area,(select count(*) from solicitud_recursosdetalle where cod_solicitudrecurso=sr.codigo and (cod_unidadorganizacional=3000 or cod_area=1235)) as sis_detalle 
  from solicitud_recursos sr join estados_solicitudrecursos es on sr.cod_estadosolicitudrecurso=es.codigo join unidades_organizacionales u on sr.cod_unidadorganizacional=u.codigo join areas a on sr.cod_area=a.codigo 
  where sr.cod_estadoreferencial=1 and sr.cod_estadosolicitudrecurso in (3)) l  
where !(l.cod_unidadorganizacional=3000 or l.cod_area=1235 or l.sis_detalle>0)  order by l.revisado_contabilidad,l.numero desc");

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
$stmt->bindColumn('glosa_estado', $glosa_estadoX);
$stmt->bindColumn('revisado_contabilidad', $estadoContabilidadX);


//Solicitudes SIS
// Preparamos
$stmtSIS = $dbh->prepare("SELECT l.* FROM (SELECT sr.*,es.nombre as estado,u.abreviatura as unidad,a.abreviatura as area,(select count(*) from solicitud_recursosdetalle where cod_solicitudrecurso=sr.codigo and (cod_unidadorganizacional=3000 or cod_area=1235)) as sis_detalle 
  from solicitud_recursos sr join estados_solicitudrecursos es on sr.cod_estadosolicitudrecurso=es.codigo join unidades_organizacionales u on sr.cod_unidadorganizacional=u.codigo join areas a on sr.cod_area=a.codigo 
  where sr.cod_estadoreferencial=1 and sr.cod_estadosolicitudrecurso in (3,5,8)) l  
where (l.cod_unidadorganizacional=3000 or l.cod_area=1235 or l.sis_detalle>0) order by l.cod_comprobante,l.numero desc");
// Ejecutamos
$stmtSIS->execute();
// bindColumn
$stmtSIS->bindColumn('codigo', $codigo);
$stmtSIS->bindColumn('unidad', $unidad);
$stmtSIS->bindColumn('area', $area);
$stmtSIS->bindColumn('fecha', $fecha);
$stmtSIS->bindColumn('cod_personal', $codPersonal);
$stmtSIS->bindColumn('cod_simulacion', $codSimulacion);
$stmtSIS->bindColumn('cod_proveedor', $codProveedor);
$stmtSIS->bindColumn('cod_estadosolicitudrecurso', $codEstado);
$stmtSIS->bindColumn('estado', $estado);
$stmtSIS->bindColumn('cod_comprobante', $codComprobante);
$stmtSIS->bindColumn('cod_simulacionservicio', $codSimulacionServicio);
$stmtSIS->bindColumn('numero', $numeroSol);
$stmtSIS->bindColumn('idServicio', $idServicio);
$stmtSIS->bindColumn('glosa_estado', $glosa_estadoX);
$stmtSIS->bindColumn('revisado_contabilidad', $estadoContabilidadX);
$stmtSIS->bindColumn('devengado', $devenX);
$item_1=2708;
?>
<div class="cargar-ajax d-none">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold" id="texto_ajax_titulo">Procesando Datos</h4>
     <p class="text-white">Aguard&aacute; un momento por favor</p>  
  </div>
</div>

<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header card-header-text">
                  <a href="#" onclick="cambiarDosDivPantallaClase('list_div_1','list_div_2','bg-info')">
                    <div id="button_list_div_1" class="card-text bg-default bg-info text-white">
                      <h4 class="">SOL. IBNORCA <span class="badge bg-info d-none" id="n_list_div_1"></span></h4>
                    </div>
                  </a>
                  <a href="#" onclick="cambiarDosDivPantallaClase('list_div_2','list_div_1','bg-info')">
                     <div id="button_list_div_2" class="card-text bg-default text-white">
                        <h4 class="">SOL. PROYECTO SIS <span class="badge bg-info d-none" id="n_list_div_2"></span></h4>
                     </div>
                  </a>
                  <h4 class="card-title float-right"><b>Contabilización</b> - Mes y Gestión de Trabajo <b style="color:#FF0000;">[<?=nombreMes($globalMesActivo);?> - <?=$globalNombreGestion?>]</b></h4>
                </div>
                <!--<div class="card-header card-header-info card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">content_paste</i>
                  </div>
                  
                </div>-->
                <div class="card-body">
                  <div id="list_div_1">
                    <table class="table table-condesed" id="tablePaginator100">
                      <thead>
                        <tr class="bg-info text-white">
                          <th>Of. - Area</th>
                          <th>Nº Sol.</th>
                          <th>Cod. Servicio</th>
                          <!--<th>Cliente</th>-->
                          <th>Proveedor</th>
                          <th>Cuenta</th>
                          <th>Solicitante</th>
                          <th>Fecha</th>
                          <th>Observaciones</th>
                          <th>Personal Pago</th>
                          <th class="text-right" width="25%">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
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

                       $nombreProveedor=obtenerNombreConcatenadoProveedorDetalleSolicitudRecurso($codigo);
                       $otrosPagosCuenta=comprobarCuentasOtrosPagosDeSolicitudRecursos($codigo);
                       $montoDetalleSoliditud=number_format(obtenerSumaDetalleSolicitud($codigo),2,'.',',');
                       $arrayEnc=implode(',',obtenerPersonalEncargadoSolicitud($codigo)[0]);

                       $glosa_estadoX = preg_replace("[\n|\r|\n\r]", ", ", $glosa_estadoX);
                       $glosaArray=explode("####", $glosa_estadoX);
                       $glosa_estadoX = str_replace("####", " - ", $glosa_estadoX);
?>
                        <tr>
                          <td><?=$unidad;?>- <?=$area;?></td>
                          <td class="font-weight-bold"><?=$numeroSolTitulo;?></td>
                          <td><?=$codigoServicio;?></td>
                          <!--<td><?=$nombreCliente;?></td>-->
                          <td><small><?=$nombreProveedor?></small></td>
                          <td><small><?=obtenerNombreConcatenadoCuentaDetalleSolicitudRecurso($codigo)?></small></td>

                          <td>
                                 <img src="assets/img/faces/persona1.png" width="20" height="20"/><?=$solicitante;?>
                          </td>
                          <td><?=strftime('%d/%m/%Y',strtotime($fecha));?></td>
                          <td class="text-muted font-weight-bold"><small><b><?php if(isset($glosaArray[1])){
                                echo "".$glosaArray[0].""."<u class='text-muted'> ".$glosaArray[1]."</u>";
                            }else{
                                echo $glosa_estadoX;
                            }?></b></small></td>
                          <td class="text-muted font-weight-bold"><small><b><?=obtenerNombreConcatenadoEncargadoSolicitudRecurso($codigo)?></b></small></td>
                          <td class="td-actions text-right">
                            <a title="Imprimir" href='#' onclick="javascript:window.open('<?=$urlImp;?>?sol=<?=$codigo;?>&mon=1')" class="<?=$buttonEdit;?>">
                              <i class="material-icons"><?=$iconImp;?></i>
                            </a>
                            <?php 
                            if($estadoContabilidadX==1){
                              if(isset($_GET['q'])){
                                ?>
                                <a title="Quitar la Revisión" href='<?=$urlEdit2?>?cod=<?=$codigo?>&estado=10&q=<?=$q?>&r=<?=$item_3?>&s=<?=$s?>&u=<?=$u?>'  class="btn btn-rose" style="background:#661E1B">
                                       <i class="material-icons">check_box</i><!--check_box-->
                                </a>
                                <?php
                              }else{
                                ?>
                                <a title="Quitar la Revisión" href='<?=$urlEdit2?>?cod=<?=$codigo?>&estado=10'  class="btn btn-rose" style="background:#661E1B">
                                       <i class="material-icons">check_box</i><!--check_box-->
                                </a>
                                <?php
                              }
                            }else{
                              $iconRevisado="check_box_outline_blank";
                              $estiloIconRevisado="btn-default";
                              if($estadoContabilidadX==2){
                                $iconRevisado="adjust";
                                $estiloIconRevisado="btn-info";
                              }
                              if(isset($_GET['q'])){
                                ?>
                                <a title="Marcar como Revisado" href='<?=$urlEdit2?>?cod=<?=$codigo?>&estado=11&q=<?=$q?>&r=<?=$item_3?>&s=<?=$s?>&u=<?=$u?>'  class="btn <?=$estiloIconRevisado?>">
                                       <i class="material-icons"><?=$iconRevisado?></i>
                                </a>
                                <?php
                              }else{
                                ?>
                                <a title="Marcar como Revisado" href='<?=$urlEdit2?>?cod=<?=$codigo?>&estado=11'  class="btn <?=$estiloIconRevisado?>">
                                       <i class="material-icons"><?=$iconRevisado?></i>
                                </a>
                                <?php
                              }
                            }
                            
                            if($codEstado==4){
                              if(isset($_GET['q'])){
                                ?>
                                <a title="Volver al Estado Registro" href='<?=$urlEdit2?>?cod=<?=$codigo?>&estado=1&q=<?=$q?>&r=<?=$item_3?>&s=<?=$s?>&u=<?=$u?>'  class="btn btn-danger">
                                       <i class="material-icons">refresh</i>
                                </a>
                                <?php
                              }else{
                                ?>
                                <a title="Volver al Estado Registro" href='<?=$urlEdit2?>?cod=<?=$codigo?>&estado=1'  class="btn btn-danger">
                                       <i class="material-icons">refresh</i>
                                </a>
                                <?php
                              }
                            }

                                   if($codComprobante!=0&&$codEstado==5){
                                   ?>
                                   <div class="btn-group dropdown">
                                     <button type="button" class="btn btn-primary dropdown-toggle" title="COMPROBANTE - DEVENGADO" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                       <i class="material-icons"><?=$iconImp;?></i>
                                     </button>
                                    <div class="dropdown-menu">
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
                                   }
                              ?>
                            <div class="btn-group dropdown">
                              <button type="button" class="btn <?=$btnEstado?> dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="material-icons">list</i> <?=$estado;?>
                              </button>
                              <div class="dropdown-menu menu-fixed-sm-table">
                                <?php
                              if(isset($_GET['q'])){
                                ?><a href="<?=$urlVer;?>?cod=<?=$codigo;?>&admin=2&q=<?=$q?>&r=<?=$item_3?>&s=<?=$s?>&u=<?=$u?>" target="_blank" class="dropdown-item">
                                    <i class="material-icons text-info">bar_chart</i> Ver Solicitud
                                 </a>
                              
                                <?php 
                                if($codEstado==4){
                                 ?>
                                 <a href="#" onclick="mostrarCambioEstadoObjeto(<?=$codigo?>)" class="dropdown-item">
                                    <i class="material-icons text-warning">dns</i> Cambiar Estado
                                 </a>
                                 <a href="<?=$urlVerificarSolicitud?>?cod=<?=$codigo?>&admin=2&q=<?=$q?>&r=<?=$item_3?>&s=<?=$s?>&u=<?=$u?>&v=<?=$idServicio?>" target="_blank" class="dropdown-item">
                                    <i class="material-icons text-success">edit</i> Editar Solicitud
                                 </a><?php 
                                }else{
                                  if($codEstado==3){
                                    ?>
                                    <a href="<?=$urlVerificarSolicitud?>?cod=<?=$codigo?>&admin=2&q=<?=$q?>&r=<?=$item_3?>&s=<?=$s?>&u=<?=$u?>&v=<?=$idServicio?>" target="_blank" class="dropdown-item">
                                    <i class="material-icons text-success">edit</i> Editar Solicitud
                                   </a>
                                   <?php 
                                  if($otrosPagosCuenta==0){
                                    ?>
                                   <a title="Contabilizar Solicitud" onclick="alerts.showSwal('contabilizar-solicitud-recurso','<?=$urlConta?>?admin=0&cod=<?=$codigo?>&q=<?=$q?>&r=<?=$item_3?>&s=<?=$s?>&u=<?=$u?>&v=<?=$idServicio?>')" href='#'  class="dropdown-item">
                                      <i class="material-icons text-danger">assignment_turned_in</i> Contabilizar Solicitud
                                    </a>
                                    <?php
                                  }else{
                                    ?>
                                   <a title="Contabilizar Solicitud"  href="#" onclick="javascript:window.open('<?=$urlRegisterCompro;?>')"  class="dropdown-item">
                                      <i class="material-icons text-warning">assignment_turned_in</i> Contabilización Manual Solicitud
                                    </a>
                                    <?php
                                  }
                                  }else{
                                  ?><a href="#" onclick="mostrarCambioEstadoObjeto(<?=$codigo?>)" class="dropdown-item">
                                    <i class="material-icons text-warning">dns</i> Cambiar Estado
                                 </a><?php  
                                  }
                                ?>
                                 <?php 
                                }
                              }else{
                                ?><a href="<?=$urlVer;?>?cod=<?=$codigo;?>&admin=2" target="_blank" class="dropdown-item">
                                    <i class="material-icons text-info">bar_chart</i> Ver Solicitud
                                 </a>
                              
                                <?php 
                                if($codEstado==4){
                                 ?>
                                 <a href="#" onclick="mostrarCambioEstadoObjeto(<?=$codigo?>)" class="dropdown-item">
                                    <i class="material-icons text-warning">dns</i> Cambiar Estado
                                 </a>
                                 <a href="<?=$urlVerificarSolicitud?>?cod=<?=$codigo?>&admin=2" target="_blank" class="dropdown-item">
                                    <i class="material-icons text-success">edit</i> Editar Solicitud
                                 </a>
                                 <?php 
                                }else{
                                  if($codEstado==3){
                                    ?>
                                    <a href="<?=$urlVerificarSolicitud?>?cod=<?=$codigo?>&admin=2" target="_blank" class="dropdown-item">
                                    <i class="material-icons text-success">edit</i> Editar Solicitud
                                   </a>
                                   <a onclick="devolverSolicitudRecurso(<?=$numeroSol?>,'<?=$codigoServicio?>','<?=$urlEdit2?>?cod=<?=$codigo?>&conta=2&estado=1','<?=$nombreProveedor?>')" href="#" class="dropdown-item">
                                    <i class="material-icons text-danger">reply</i> Devolver Solicitud
                                  </a>
                                   <?php 
                                  if($otrosPagosCuenta==0){
                                    
                                    ?>
                                    <!--onclick="alerts.showSwal('contabilizar-solicitud-recurso','<?=$urlConta?>?admin=0&cod=<?=$codigo?>')"-->
                                   <a title="Contabilizar Solicitud" onclick="contabilizarSolicitudRecursoModal(<?=$codigo?>,1,<?=$numeroSol?>,'<?=$montoDetalleSoliditud?>','<?=obtenerNombreConcatenadoCuentaDetalleSolicitudRecurso($codigo)?>','<?=$urlConta?>?admin=0&cod=<?=$codigo?>&deven=1','<?=$nombreProveedor?>','<?=$arrayEnc?>');return false;" href='#'  class="dropdown-item">
                                      <i class="material-icons text-danger">assignment_turned_in</i> Contabilizar a Devengado
                                    </a>
                                    <a title="Contabilizar Solicitud" onclick="contabilizarSolicitudRecursoModal(<?=$codigo?>,1,<?=$numeroSol?>,'<?=$montoDetalleSoliditud?>','<?=obtenerNombreConcatenadoCuentaDetalleSolicitudRecurso($codigo)?>','<?=$urlConta?>?admin=0&cod=<?=$codigo?>&deven=0','<?=$nombreProveedor?>','<?=$arrayEnc?>');return false;" href='#'  class="dropdown-item">
                                      <i class="material-icons text-info">assignment_turned_in</i> Contabilizar a Pagado
                                    </a>
                                    <?php
                                  }else{
                                    ?>
                                   <a title="Contabilizar Solicitud"  href="#" onclick="alerts.showSwal('warning-message-and-confirmationGeneral','<?=$urlEdit2?>?cod=<?=$codigo?>&conta=2&estado=5')" class="dropdown-item">
                                      <i class="material-icons text-dark">dns</i> <b class="text-muted">Cambiar a <u class="text-dark">Contabilizado</u></b>
                                    </a>
                                    <a title="Pagar Solicitud"  href="#" onclick="alerts.showSwal('warning-message-and-confirmationGeneral','<?=$urlEdit2?>?cod=<?=$codigo?>&conta=2&estado=8')" class="dropdown-item">
                                      <i class="material-icons text-info">dns</i> <b class="text-muted">Cambiar a <u class="text-info">Pagado</u></b>
                                    </a>
                                    <?php
                                   }
                                  }else{
                                  ?><a href="#" onclick="mostrarCambioEstadoObjeto(<?=$codigo?>)" class="dropdown-item">
                                    <i class="material-icons text-warning">dns</i> Cambiar Estado
                                 </a><?php  
                                  }

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
                   </div><!--Fin List 1--> 
                   <div id="list_div_2" class="d-none">
                     <table class="table table-condesed" id="tablePaginator100NoFidexHead">
                      <thead>
                        <tr class="bg-info text-white">
                          <th>.</th>
                          <th>Of. - Area</th>
                          <th>Nº Sol.</th>
                          <th>Cod. Servicio</th>
                          <!--<th>Cliente</th>-->
                          <th>Proveedor</th>
                          <th>Cuenta</th>
                          <th>Solicitante</th>
                          <th>Fecha</th>
                          <th>Observaciones</th>
                          <th>Personal Pago</th>
                          <th class="text-right" width="25%">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
<?php
            $index=1;
                        while ($row = $stmtSIS->fetch(PDO::FETCH_BOUND)) {
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
                      $numeroSolTitulo='<a href="#" title="SOLICITUD DE RECURSOS SIS" class="btn btn-default btn-sm btn-round">'.$numeroSol.'</a>';

                       $nombreProveedor=obtenerNombreConcatenadoProveedorDetalleSolicitudRecurso($codigo);
                       $otrosPagosCuenta=comprobarCuentasOtrosPagosDeSolicitudRecursos($codigo);
                       $montoDetalleSoliditud=number_format(obtenerSumaDetalleSolicitud($codigo),2,'.',',');
                       $arrayEnc=implode(',',obtenerPersonalEncargadoSolicitud($codigo)[0]);

                       $glosa_estadoX = preg_replace("[\n|\r|\n\r]", ", ", $glosa_estadoX);
                       $glosaArray=explode("####", $glosa_estadoX);
                       $glosa_estadoX = str_replace("####", " - ", $glosa_estadoX);
                       if($codComprobante!=0&&($codEstado==5||$codEstado==8)){
                        $numeroSolTitulo='<a href="#" title="SOLICITUD DE RECURSOS SIS" class="btn btn-rose btn-sm btn-round">'.$numeroSol.'</a>';
                       }

                       $estiloComprobante="btn btn-primary";
                       if($devenX==0){
                          $estiloComprobante="btn btn-info";
                       }
?>
                        <tr>
                          <td class="text-center" width="8%">
                            <?php
                            if($codComprobante!=0&&($codEstado==5||$codEstado==8)){
                                    ?>
                                    <?php
                            }else{
                              ?>
                          <div class="form-check">
                            <label class="form-check-label">
                              <input class="form-check-input" type="checkbox" id="solicitudes_sis<?=$index?>" onclick="sendChekedCR(<?=$index?>,'<?=$nombreProveedor?>','<?=$numeroSol?>','<?=$unidad;?>- <?=$area;?>','<?=obtenerNombreConcatenadoCuentaDetalleSolicitudRecurso($codigo)?>','<?=strftime('%d/%m/%Y',strtotime($fecha));?>','<img src=\'assets/img/faces/persona1.png\' width=\'20\' height=\'20\'/><?=$solicitante;?>')" name="solicitudes_sis[]" value="<?=$codigo?>" >
                              <span class="form-check-sign">
                                <span class="check"></span>
                              </span>
                            </label>
                          </div><?php
                            }
                             ?>
                            
                          </td>
                          <td><?=$unidad;?>- <?=$area;?></td>
                          <td class="font-weight-bold"><?=$numeroSolTitulo;?></td>
                          <td><?=$codigoServicio;?></td>
                          <!--<td><?=$nombreCliente;?></td>-->
                          <td><small><?=$nombreProveedor?></small></td>
                          <td><small><?=obtenerNombreConcatenadoCuentaDetalleSolicitudRecurso($codigo)?></small></td>

                          <td>
                                 <img src="assets/img/faces/persona1.png" width="20" height="20"/><?=$solicitante;?>
                          </td>
                          <td><?=strftime('%d/%m/%Y',strtotime($fecha));?></td>
                          <td class="text-muted font-weight-bold"><small><b><?php if(isset($glosaArray[1])){
                                echo "".$glosaArray[0].""."<u class='text-muted'> ".$glosaArray[1]."</u>";
                            }else{
                                echo $glosa_estadoX;
                            }?></b></small></td>
                          <td class="text-muted font-weight-bold"><small><b><?=obtenerNombreConcatenadoEncargadoSolicitudRecurso($codigo)?></b></small></td>
                          <td class="td-actions text-right">
                            <a title="Imprimir" href='#' onclick="javascript:window.open('<?=$urlImp;?>?sol=<?=$codigo;?>&mon=1')" class="<?=$buttonEdit;?>">
                              <i class="material-icons"><?=$iconImp;?></i>
                            </a>
                            <?php 
                            if($estadoContabilidadX==1){
                              if(!($codComprobante!=0&&($codEstado==5||$codEstado==8))){
                              if(isset($_GET['q'])){
                                ?>
                                <a title="Quitar la Revisión" href='<?=$urlEdit2?>?cod=<?=$codigo?>&estado=10&q=<?=$q?>&r=<?=$item_3?>&s=<?=$s?>&u=<?=$u?>'  class="btn btn-rose" style="background:#661E1B">
                                       <i class="material-icons">check_box</i><!--check_box-->
                                </a>
                                <?php
                              }else{
                                ?>
                                <a title="Quitar la Revisión" href='<?=$urlEdit2?>?cod=<?=$codigo?>&estado=10'  class="btn btn-rose" style="background:#661E1B">
                                       <i class="material-icons">check_box</i><!--check_box-->
                                </a>
                                <?php
                              }
                             }
                            }else{
                              $iconRevisado="check_box_outline_blank";
                              $estiloIconRevisado="btn-default";
                              if($estadoContabilidadX==2){
                                $iconRevisado="adjust";
                                $estiloIconRevisado="btn-info";
                              }
                              if(!($codComprobante!=0&&($codEstado==5||$codEstado==8))){
                              if(isset($_GET['q'])){
                                ?>
                                <a title="Marcar como Revisado" href='<?=$urlEdit2?>?cod=<?=$codigo?>&estado=11&q=<?=$q?>&r=<?=$item_3?>&s=<?=$s?>&u=<?=$u?>'  class="btn <?=$estiloIconRevisado?>">
                                       <i class="material-icons"><?=$iconRevisado?></i>
                                </a>
                                <?php
                              }else{
                                ?>
                                <a title="Marcar como Revisado" href='<?=$urlEdit2?>?cod=<?=$codigo?>&estado=11'  class="btn <?=$estiloIconRevisado?>">
                                       <i class="material-icons"><?=$iconRevisado?></i>
                                </a>
                                <?php
                              }
                             }
                            }
                            
                            if($codEstado==4){
                              if(isset($_GET['q'])){
                                ?>
                                <a title="Volver al Estado Registro" href='<?=$urlEdit2?>?cod=<?=$codigo?>&estado=1&q=<?=$q?>&r=<?=$item_3?>&s=<?=$s?>&u=<?=$u?>'  class="btn btn-danger">
                                       <i class="material-icons">refresh</i>
                                </a>
                                <?php
                              }else{
                                ?>
                                <a title="Volver al Estado Registro" href='<?=$urlEdit2?>?cod=<?=$codigo?>&estado=1'  class="btn btn-danger">
                                       <i class="material-icons">refresh</i>
                                </a>
                                <?php
                              }
                            }

                                if($codComprobante!=0&&($codEstado==5||$codEstado==8)){
                                   ?>
                                   <div class="btn-group dropdown">
                                     <button type="button" class="btn <?=$estiloComprobante?> dropdown-toggle" title="COMPROBANTE - DEVENGADO" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                       <i class="material-icons"><?=$iconImp;?></i>
                                     </button>
                                    <div class="dropdown-menu">
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
                                   }

                           
                            //if($codComprobante!=0&&($codEstado==5||$codEstado==8)){
                              if(verificarEdicionComprobanteUsuario($globalUser)!=0){
                                    ?>
                                    <a title="Cambiar Actividad Proyecto" onclick="cambiarActividadesProyectoSolicitudRecursoModal(<?=$codigo?>,<?=$numeroSol?>,'<?=$montoDetalleSoliditud?>','<?=obtenerNombreConcatenadoCuentaDetalleSolicitudRecurso($codigo)?>','<?=$nombreProveedor?>');return false;" target="_blank" class="btn btn-orange">
                                      <i class="material-icons">assignment</i>
                                    </a> 

                                <?php
                                } 
                            //}       
                              ?>
                            <div class="btn-group dropdown">
                              <button type="button" class="btn <?=$btnEstado?> dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="material-icons">list</i> <?=$estado;?>
                              </button>
                              <div class="dropdown-menu menu-fixed-sm-table">
                                <?php
                              if(isset($_GET['q'])){
                                ?><a href="<?=$urlVer;?>?cod=<?=$codigo;?>&admin=2&q=<?=$q?>&r=<?=$item_3?>&s=<?=$s?>&u=<?=$u?>" target="_blank" class="dropdown-item">
                                    <i class="material-icons text-info">bar_chart</i> Ver Solicitud
                                 </a>
                              
                                <?php 
                                if($codEstado==4){
                                 ?>
                                 <a href="<?=$urlVerificarSolicitud?>?cod=<?=$codigo?>&admin=2&q=<?=$q?>&r=<?=$item_3?>&s=<?=$s?>&u=<?=$u?>&v=<?=$idServicio?>" target="_blank" class="dropdown-item">
                                    <i class="material-icons text-success">edit</i> Editar Solicitud
                                 </a><?php 
                                }else{
                                  if($codEstado==3){
                                    ?>
                                    <a href="<?=$urlVerificarSolicitud?>?cod=<?=$codigo?>&admin=2&q=<?=$q?>&r=<?=$item_3?>&s=<?=$s?>&u=<?=$u?>&v=<?=$idServicio?>" target="_blank" class="dropdown-item">
                                    <i class="material-icons text-success">edit</i> Editar Solicitud
                                   </a>
                                   <?php 
                                  if($otrosPagosCuenta==0){
                                    ?>
                                   <!--<a title="Contabilizar Solicitud" onclick="alerts.showSwal('contabilizar-solicitud-recurso','<?=$urlConta?>?admin=0&cod=<?=$codigo?>&q=<?=$q?>&r=<?=$item_3?>&s=<?=$s?>&u=<?=$u?>&v=<?=$idServicio?>')" href='#'  class="dropdown-item">
                                      <i class="material-icons text-danger">assignment_turned_in</i> Contabilizar Solicitud
                                    </a>-->
                                    <?php
                                  }else{
                                    ?>
                                    <!--<a title="Contabilizar Solicitud"  href="#" onclick="javascript:window.open('<?=$urlRegisterCompro;?>')"  class="dropdown-item">
                                      <i class="material-icons text-warning">assignment_turned_in</i> Contabilización Manual Solicitud
                                    </a>-->
                                    <?php
                                  }
                                  }else{
                                  ?><?php  
                                  }
                                ?>
                                 <?php 
                                }
                              }else{
                                ?><a href="<?=$urlVer;?>?cod=<?=$codigo;?>&admin=2" target="_blank" class="dropdown-item">
                                    <i class="material-icons text-info">bar_chart</i> Ver Solicitud
                                 </a>
                              
                                <?php 
                                if($codEstado==4){
                                 ?>
                                 <a href="<?=$urlVerificarSolicitud?>?cod=<?=$codigo?>&admin=2" target="_blank" class="dropdown-item">
                                    <i class="material-icons text-success">edit</i> Editar Solicitud
                                 </a>
                                 <?php 
                                }else{
                                  if($codEstado==3){
                                    ?>
                                    <a href="<?=$urlVerificarSolicitud?>?cod=<?=$codigo?>&admin=2" target="_blank" class="dropdown-item">
                                    <i class="material-icons text-success">edit</i> Editar Solicitud
                                   </a>
                                   <a onclick="devolverSolicitudRecurso(<?=$numeroSol?>,'<?=$codigoServicio?>','<?=$urlEdit2?>?cod=<?=$codigo?>&conta=2&estado=1','<?=$nombreProveedor?>')" href="#" class="dropdown-item">
                                    <i class="material-icons text-danger">reply</i> Devolver Solicitud
                                  </a>
                                    <a title="Pagar Solicitud"  href="#" onclick="alerts.showSwal('warning-message-and-confirmationGeneral','<?=$urlEdit2?>?cod=<?=$codigo?>&conta=2&estado=8')" class="dropdown-item">
                                      <i class="material-icons text-info">dns</i> <b class="text-muted">Cambiar a <u class="text-info">Pagado</u></b>
                                    </a>
                                   <?php 
                                  
                                  }else{
                                  ?><?php  
                                  }

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
              </div>
              <div class="card-footer fixed-bottom col-sm-9">
                <a href="#" onclick="filaTablaSIS($('#tablas_registradas'));" class="btn btn-rose d-none" id="boton_generar_comprobante"><i class="material-icons">assignment_turned_in</i> Contabilizar Solicitudes <span class='badge bg-white text-rose'> 0</span></a>
                <a href="<?=$urlList6?>" target="_blank" class="btn btn-primary float-right"><i class="material-icons">history</i> <small id="cantidad_eliminados"></small> Histórico</a>
                <!--<a href="#" onclick="abrirModal('modalListSolEliminados');moverModal('modalListSolEliminados');" class="btn btn-info float-right"><i class="material-icons">history</i> <small id="cantidad_eliminados"></small> Histórico</a>-->
              </div>    
            </div>
          </div>  
        </div>
    </div>


<!-- small modal -->
<div class="modal fade modal-primary" id="modalSolSis" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content card">
                <div class="card-header card-header-rose card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">assignment_turned_in</i>
                  </div>
                  <h4 class="card-title text-rose"><b>Contabilizar Solicitudes SIS</b></h4>
                </div>
                <div class="card-body">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                  <i class="material-icons">close</i>
                </button>
                  <table class="table table-condensed">
                    <thead>
                      <tr class="text-dark bg-plomo">
                      <th>#</th>
                      <th>OF/AREA</th>
                      <th>Numero</th>
                      <th>Proveedor</th>
                      <th>Cuenta</th>
                      <th>Solicitante</th>
                      <th>Fecha</th>  
                      </tr>
                    </thead>
                    <tbody id="tablas_registradas">
                      
                    </tbody>
                  </table>

                  <div>
                    <a class="btn btn-rose float-right" href="#" onclick="generarComprobanteSolicitudRecursoSIS()">CONTABILIZAR</a>
                  </div>
                </div>
      </div>  
    </div>
  </div>
<!--    end small modal -->

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
<!--    end small modal -->

<?php
$stmt = $dbh->prepare("SELECT count(*) as cantidad_historico from solicitud_recursos sr join estados_solicitudrecursos es on sr.cod_estadosolicitudrecurso=es.codigo join unidades_organizacionales u on sr.cod_unidadorganizacional=u.codigo join areas a on sr.cod_area=a.codigo where sr.cod_estadoreferencial=1 and (sr.cod_estadosolicitudrecurso in (5,8)) order by sr.numero desc");
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('cantidad_historico', $cantidad_historico);
while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
  ?> <script>$("#cantidad_eliminados").html("("+<?=$cantidad_historico-1?>+")");</script> <?php
}
?>

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
<div class="modal fade" id="modalCambiarActividadesProyecto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background:#DA053C !important;color:#fff;">
        <h4 class="modal-title">Actividades del Proyecto</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> 
      </div>
      <div class="modal-body">        
        <div class="row">
          <label class="col-sm-1 col-form-label" style="color:#7e7e7e"><span><small>Nro.<br>Solicitud.</small></span></label>
          <div class="col-sm-2">
            <div class="form-group" >
              <input type="text" class="form-control" name="nro_solicitud_conta_2" id="nro_solicitud_conta_2" readonly="true" style="background-color:#e2d2e0">              
            </div>
          </div>
          <label class="col-sm-1 col-form-label" style="color:#7e7e7e"><span><small >Cuentas</small></span></label>
          <div class="col-sm-8">
            <div class="form-group" >              
              <input type="text" class="form-control" name="cuenta_conta_2" id="cuenta_conta_2" readonly="true" style="background-color:#e2d2e0">
            </div>
          </div>
        </div> 
        <div class="row">
          <label class="col-sm-1 col-form-label" style="color:#7e7e7e"><span><small>Proveedor</small></span></label>
          <div class="col-sm-8">
            <div class="form-group" >
              <input type="text" class="form-control" name="proveedor_nombre_conta_2" id="proveedor_nombre_conta_2" readonly="true" style="background-color:#e2d2e0">              
            </div>
          </div>
           <label class="col-sm-1 col-form-label" style="color:#7e7e7e"><span><small>Monto</small></span></label>
          <div class="col-sm-2">
            <div class="form-group" >
              <input type="text" class="form-control" name="monto_nombre_conta_2" id="monto_nombre_conta_2" readonly="true" style="background-color:#e2d2e0">              
            </div>
          </div>
        </div>  
        <br><br>              
        <div class="row" id="solicitud_recurso_detalle_sis">
          
        </div>
        
         <input type="hidden" id="cantidad_registros_detalle_sis" value="0">
         <input type="hidden" id="codigo_solicitud" value="0">                                          
      </div>
      <div class="modal-footer">
        <a href="#" class="btn btn-success" onclick="saveActividadProyectoSolicitudRecursoModal()">Guardar</a>
        <button type="button" class="btn btn-danger" data-dismiss="modal"> Volver </button>
      </div>
    </div>
  </div>
</div>
<!-- modal reenviar solicitud devuelto -->

