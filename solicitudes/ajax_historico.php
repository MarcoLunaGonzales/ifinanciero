<?php
session_start();
require_once '../conexion.php';
require_once 'configModule.php';
require_once '../functions.php';
require_once '../styles.php';
$globalAdmin=$_SESSION["globalAdmin"];
$globalUser=$_SESSION["globalUser"];
$globalNombreGestion=$_SESSION["globalNombreGestion"];
$globalMesActivo=$_SESSION['globalMes'];
$userAdmin=obtenerValorConfiguracion(74);
$dbh = new Conexion();

$sqlNumero="";
if(isset($_POST['numero'])&&$_POST['numero']!=""){
  $sqlNumero="and l.numero in (".$_POST['numero'].")";
}

$stringCuentaX="";
if(isset($_POST['cuentas'])&&count($_POST['cuentas'])>0){
  $stringCuentaX=implode(",",$_POST['cuentas']);
}

$stringOficinasX="";
if(isset($_POST['oficinas'])&&count($_POST['oficinas'])>0){
  $stringOficinasX=implode(",",$_POST['oficinas']);
}

$stringAreasX="";
if(isset($_POST['areas'])&&count($_POST['areas'])>0){
  $stringAreasX=implode(",",$_POST['areas']);
}

$stringSolicitanteX="";
if(isset($_POST['personal'])&&count($_POST['personal'])>0){
  $stringSolicitanteX=implode(",",$_POST['personal']);
}

$sql="SELECT l.* FROM (SELECT sr.*,es.nombre as estado,u.abreviatura as unidad,a.abreviatura as area,(select count(*) from solicitud_recursosdetalle where cod_solicitudrecurso=sr.codigo and (cod_unidadorganizacional=3000 or cod_area=1235)) as sis_detalle,
  (SELECT count(*) from solicitud_recursosdetalle where cod_solicitudrecurso=sr.codigo AND cod_plancuenta in ($stringCuentaX)) as detalle_sr 
  from solicitud_recursos sr join estados_solicitudrecursos es on sr.cod_estadosolicitudrecurso=es.codigo join unidades_organizacionales u on sr.cod_unidadorganizacional=u.codigo join areas a on sr.cod_area=a.codigo 
  where sr.cod_estadoreferencial=1 and sr.cod_estadosolicitudrecurso in (5,8,9)) l  
where !(l.cod_unidadorganizacional=3000 or l.cod_area=1235 or l.sis_detalle>0) $sqlNumero and detalle_sr>0 
and l.cod_unidadorganizacional in ($stringOficinasX)
and l.cod_area in ($stringAreasX)
and l.cod_personal in ($stringSolicitanteX)
order by l.revisado_contabilidad,l.numero desc";
$stmt = $dbh->prepare($sql);
//echo $sql;
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
                                

                                 ?>
                                
                              </div>
                             </div>
                          </td> 
                        </tr>
<?php
              $index++;
            }
?>
