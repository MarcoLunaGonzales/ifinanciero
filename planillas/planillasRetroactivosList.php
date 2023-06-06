<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'functionsGeneral.php';
require_once 'rrhh/configModule.php';

$globalUser=$_SESSION["globalUser"];
$globalCodUnidad=$_SESSION["globalUnidad"];
$globalNombreUnidad=$_SESSION["globalNombreUnidad"];

$dbh = new Conexion();
  $sql="SELECT pr.codigo,pr.cod_gestion,pr.cod_estadoplanilla,pr.cod_comprobante1,pr.cod_comprobante2,pr.cod_comprobante3,pr.cod_comprobante4,
  (select g.nombre from gestiones g where g.codigo=pr.cod_gestion) as gestion,CONCAT_WS(' ',p.primer_nombre,p.paterno,p.materno)as persona_creacion,
  (select ep.nombre from estados_planilla ep where ep.codigo=pr.cod_estadoplanilla) as nombre_estadoplanilla,DATE_FORMAT(pr.created_at,'%d/%m/%Y')as fecha_creacion
  from planillas_retroactivos pr join personal p on pr.created_by=p.codigo
   order by pr.cod_gestion desc";
  
  $stmtAdmnin = $dbh->prepare($sql);
  $stmtAdmnin->execute();
  $stmtAdmnin->bindColumn('codigo', $codigo_planilla);
  $stmtAdmnin->bindColumn('gestion', $gestion);
  $stmtAdmnin->bindColumn('cod_gestion', $cod_gestion);
  $stmtAdmnin->bindColumn('fecha_creacion', $fecha_creacion);
  $stmtAdmnin->bindColumn('persona_creacion', $persona_creacion);
  $stmtAdmnin->bindColumn('cod_estadoplanilla', $cod_estadoplanilla);
  $stmtAdmnin->bindColumn('nombre_estadoplanilla', $nombre_estadoplanilla);

  $stmtAdmnin->bindColumn('cod_comprobante1', $comprobante_x1);
  $stmtAdmnin->bindColumn('cod_comprobante2', $comprobante_x2);
  $stmtAdmnin->bindColumn('cod_comprobante3', $comprobante_x3);
  $stmtAdmnin->bindColumn('cod_comprobante4', $comprobante_x4);

  ?>
  <div class="content">
    <div class="container-fluid">
      <div class="col-md-12">     
        <div class="card">
          <div class="card-header  card-header-text"> 
            <div class="card-icon" style="background: #dc7633;">
              <i class="material-icons"><?=$iconCard;?></i>
            </div>
            <!-- <h3 class="card-title">Planilla De Retroactivos</h3> -->
            <h3 style="color:#2c3e50;"><b>Planilla De Retroactivos<hr style="background:#dc7633;height:10px;" align="left" width="350px"></b></h3>
          </div>
          <div class="card-body ">
              <table class="table" id="tablePaginator">
                <thead>
                    <tr>
                      <th>Gestión</th>
                      <th>Creado por</th>
                      <th>Fecha Creación</th>
                      <th>Estado</th>
                      <th></th> 
                      <th></th>
                    </tr>
                </thead>
                <tbody>
                  <?php $index=1;                  
                  $datosX="";
                  while ($row = $stmtAdmnin->fetch(PDO::FETCH_BOUND)) {
                    $datosX =$codigo_planilla."-";
                    $estilo_boton="";

                    $clase_boton_procesar="";
                    $clase_boton_reprocesar="";
                    $clase_boton_cerrar="";
                    $clase_boton_ver="";
                    if($cod_estadoplanilla==1){//estado registrado
                      $label='<span class="badge badge-dark">';
                      $estilo_boton="style='background-color:#3b83bd;color:#ffffff;'";
                      // $clase_boton_procesar="d-none";
                      $clase_boton_reprocesar="d-none";
                      $clase_boton_cerrar="d-none";
                      $clase_boton_ver="d-none";
                    }
                    if($cod_estadoplanilla==2){//estado procesado
                      $label='<span class="badge badge-warning">';
                      $estilo_boton="style='background-color:#3b83bd;color:#ffffff;'";
                       $clase_boton_procesar="d-none";
                      // $clase_boton_reprocesar="d-none";
                      // $clase_boton_cerrar="d-none";
                       // $clase_boton_ver="d-none";
                    }
                    if($cod_estadoplanilla==3){//estado cerrado   
                      $label='<span class="badge badge-success">';
                      $clase_boton_procesar="d-none";
                      $clase_boton_reprocesar="d-none";
                      $clase_boton_cerrar="d-none";
                      // $clase_boton_ver="d-none";
                    }                  
                    ?>
                    <tr>                    
                      <td><?=$gestion?></td>
                      <td><?=$persona_creacion?></td>
                      <td><?=$fecha_creacion?></td>
                      <td><?=$label.$nombre_estadoplanilla."</span>";?></td>
                      <td class="td-actions text-right">
                        <button type="button" class="btn <?=$clase_boton_procesar?>" <?=$estilo_boton?> data-toggle="modal" data-target="#modalProcesar" onclick="agregaformPre('<?=$datosX;?>')">
                          <i class="material-icons" title="Procesar Planilla Retroactivo">perm_data_setting</i>
                        </button>
                        <button type="button" class="btn <?=$clase_boton_reprocesar?>" <?=$estilo_boton?> data-toggle="modal" data-target="#modalreProcesar" onclick="agregaformRP('<?=$datosX;?>')">
                          <i class="material-icons" title="Reprocesar Planilla Retroactivo">autorenew</i>                   
                        </button>
                        <button type="button" class="btn btn-success <?=$clase_boton_cerrar?>" data-toggle="modal" data-target="#modalCerrar" onclick="agregaformCP('<?=$datosX;?>')">
                          <i class="material-icons" title="Cerrar Planilla Retroactivo">assignment_returned</i>
                        </button>
                      </td>
                      <td class="td-actions text-right">
                         <div class="dropdown <?=$clase_boton_ver?>">
                          <button class="btn btn-danger dropdown-toggle" type="button" id="reporte_sueldos" data-toggle="dropdown" aria-extended="true">
                            <i class="material-icons" title="Formatos de Planillas 1">remove_red_eye</i>
                            <span class="caret"></span>
                          </button>
                          <ul class="dropdown-menu" role="menu" aria-labelledby="reporte_sueldos">
                            <li role="presentation" ><a role="item" href="planillas/planillasRetroactivosPrintMes.php?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&tipo=1&mes=1" target="_blank"><i class="material-icons text-primary">assignment_turned_in</i><small>PLANILLA ENERO PDF</small></a></li>
                            <li role="presentation" ><a role="item" href="planillas/planillasRetroactivosPrintMes.php?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&tipo=1&mes=2" target="_blank"><i class="material-icons text-primary">assignment_turned_in</i><small>PLANILLA FEBRERO PDF</small></a></li>
                            <li role="presentation" ><a role="item" href="planillas/planillasRetroactivosPrintMes.php?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&tipo=1&mes=3" target="_blank"><i class="material-icons text-primary">assignment_turned_in</i><small>PLANILLA MARZO PDF</small></a></li>
                            <li role="presentation" ><a role="item" href="planillas/planillasRetroactivosPrintMes.php?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&tipo=1&mes=4" target="_blank"><i class="material-icons text-primary">assignment_turned_in</i><small>PLANILLA ABRIL PDF</small></a></li>
                            <li role="presentation" ><a role="item" href="planillas/planillasRetroactivosPrint.php?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&tipo=1" target="_blank"><i class="material-icons text-warning">assignment_turned_in</i><small>PLANILLA GRAL. PDF</small></a></li>
                            <li role="presentation" ><a role="item" href="planillas/planillasRetroactivosPrint.php?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&tipo=2" target="_blank"><i class="material-icons text-success">assignment_turned_in</i><small>PLANILLA GRAL. EXCEL</small></a></li>
                            <!-- <li role="presentation"><a role="item" href="boletas/boletas_retroactivo_print.php?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>" target="_blank"><i class="material-icons text-rose">class</i><small>BOLETAS</small></a></li> -->
                            <!--<li role="presentation"><a role="item" href="planillas/planillasRetroactivos_concuenta.php?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&tipo=1" target="_blank"><i class="material-icons text-success">verified_user</i><small>CON CUENTA</small></a></li>
                            <li role="presentation"><a role="item" href="planillas/planillasRetroactivos_concuenta.php?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&tipo=2" target="_blank"><i class="material-icons text-danger">unpublished</i><small>SIN CUENTA</small></a></li>
                            <php if($cod_estadoplanilla==3){?>
                            <li role="presentation"><a role="item" href="planillas/planillasRetroactivos_concuenta_trasnfer.php?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>" target="_blank"><i class="material-icons text-success">local_atm</i><small>TRANFER BANCO</small></a></li>
                            <php }?>-->

                          </ul>
                        </div>
                         <div class="dropdown <?=$clase_boton_ver?>">
                          <button class="btn dropdown-toggle" style="background:#c0392b;" type="button" id="reporte_sueldos" data-toggle="dropdown" aria-extended="true">
                            <i class="material-icons" title="Formatos de Planillas 2">remove_red_eye</i>
                            <span class="caret"></span>
                          </button>
                          <ul class="dropdown-menu" role="menu" aria-labelledby="reporte_sueldos">

                            <!--<li role="presentation"><a role="item" href="planillas/planillasRetroactivosAFPF.php?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=1" target="_blank"><i class="material-icons text-info">home_work</i><small>AFP FUTURO 1</small></a></li>

                           
                            <li role="presentation"><a role="item" href="planillas/planillasRetroactivosAFPF.php?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=2" target="_blank"><i class="material-icons text-info">home_work</i><small>AFP FUTURO 2</small></a></li>
                            <li role="presentation"><a role="item" href="planillas/planillasRetroactivosAFPF.php?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=3" target="_blank"><i class="material-icons text-info">home_work</i><small>AFP FUTURO 3</small></a></li>
                            <li role="presentation"><a role="item" href="planillas/planillasRetroactivosAFPF.php?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=4" target="_blank"><i class="material-icons text-info">home_work</i><small>AFP FUTURO 4</small></a></li>-->
                            <!--<li role="presentation"><a role="item" href="planillas/planillasRetroactivosAFPP.php?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=1" target="_blank"><i class="material-icons text-dark">home_work</i><small>AFP PREVISION 1</small></a></li>
                            <li role="presentation"><a role="item" href="planillas/planillasRetroactivosAFPP.php?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=2" target="_blank"><i class="material-icons text-dark">home_work</i><small>AFP PREVISION 2</small></a></li>
                            <li role="presentation"><a role="item" href="planillas/planillasRetroactivosAFPP.php?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=3" target="_blank"><i class="material-icons text-dark">home_work</i><small>AFP PREVISION 3</small></a></li>
                            <li role="presentation"><a role="item" href="planillas/planillasRetroactivosAFPP.php?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=4" target="_blank"><i class="material-icons text-dark">home_work</i><small>AFP PREVISION 4</small></a></li>-->
                            <li role="presentation"><a role="item" href="planillas/planillasRetroactivosOVT.php?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>" target="_blank"><i class="material-icons text-info">article</i><small>PLANILLA OVT</small></a></li>
                            <!--<li role="presentation"><a role="item" href="planillas/planillasRetroactivosCPS.php?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&tipo=2" target="_blank"><i class="material-icons text-danger">add_business</i><small>PLANILLA CPS</small></a></li>
                            <li role="presentation"><a role="item" href="planillas/planillasRetroactivosCNS.php?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&tipo=1" target="_blank"><i class="material-icons text-danger">add_business</i><small>PLANILLA CNS</small></a></li>-->

                             <?php if($comprobante_x1==0){ ?>
                            <li>
                              <a role="item" href="#" onclick="alerts.showSwal('warning-message-and-confirmationGeneral','planillas/executeComprobanteRetroactivos.php?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=1')"> 
                                <i class="material-icons" title="Generar Comprobante Enero" style="color:red">input</i>Comprobante Enero
                              </a>
                            </li>
                            <?php } ?>
                            <?php if($comprobante_x2==0){ ?>
                            <li>
                              <a role="item" href="#" onclick="alerts.showSwal('warning-message-and-confirmationGeneral','planillas/executeComprobanteRetroactivos.php?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=2')"> 
                                <i class="material-icons" title="Generar Comprobante Febrero" style="color:red">input</i>Comprobante Febrero
                              </a>
                            </li>
                            <?php } ?>
                            <?php if($comprobante_x3==0){ ?>
                            <li>
                              <a role="item" href="#" onclick="alerts.showSwal('warning-message-and-confirmationGeneral','planillas/executeComprobanteRetroactivos.php?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=3')"> 
                                <i class="material-icons" title="Generar Comprobante Marzo" style="color:red">input</i>Comprobante Marzo
                              </a>
                            </li>
                            <?php } ?>
                            <?php if($comprobante_x4==0){ ?>
                            <li>
                              <a role="item" href="#" onclick="alerts.showSwal('warning-message-and-confirmationGeneral','planillas/executeComprobanteRetroactivos.php?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=4')"> 
                                <i class="material-icons" title="Generar Comprobante Abril" style="color:red">input</i>Comprobante Abril
                              </a>
                            </li>
                            <?php } ?>
                          </ul>
                        </div>
                      </td>
                    </tr>
                  <?php $index++; }
                  $dbh=null;
                  $stmtAdmnin=null;
                  ?>
                </tbody>                                      
              </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--modal procesar-->
  <div class="modal fade" id="modalProcesar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header" style="background: #45b39d;">
          <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> -->
          <!-- <h4 class="modal-title" id="myModalLabel">¿Estás Segur@?</h4> -->
        </div>
        <div class="modal-body">
          <input type="hidden" name="codigo_planilla" id="codigo_planilla" value="0">        
          <h3>Estás a punto de PROCESAR La planilla de retroactivos de la gestión <?=date('Y')?>.<br><b>¿Deseas Continuar?</b></h3>
          <div id="cargaP" style="display:none">
            <h3><b>Por favor espere...</b></h3>
          </div>
        </div>       
        <div class="modal-footer">
          <button type="button" class="btn" style="background: #45b39d;" id="AceptarProceso">Si, Continuar</button>
          <button type="button" class="btn btn-danger" id="CancelarProceso" data-dismiss="modal">Cancelar </button>
        </div>
      </div>
    </div>
  </div>
  <!--modal Reprocesar-->
  <div class="modal fade" id="modalreProcesar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header" style="background: #45b39d;">
          <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> -->
          <!-- <h4 class="modal-title" id="myModalLabel">¿Estás Seguro?</h4> -->
        </div>
        <div class="modal-body">
          <input type="hidden" name="codigo_planillaRP" id="codigo_planillaRP" value="0">        
          <h3>Estás a punto de REPROCESAR La planilla de retroactivos de la gestión <?=date('Y')?>.<br><b>¿Deseas Continuar?</b></h3>
          <div id="cargaR" style="display:none">
            <h3><b>Por favor espere...</b></h3>
          </div>
        </div>    
        <div class="modal-footer">
          <button type="button" class="btn" style="background: #45b39d;" id="AceptarReProceso" >Si, Continuar</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal" id="CancelarReProceso"> Cancelar </button>
        </div>
      </div>
    </div>
  </div>
  <!--modal Cerrra-->
  <div class="modal fade" id="modalCerrar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header" style="background: #45b39d;">
          <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">¿Estás Seguro?</h4> -->
        </div>
        <div class="modal-body">
          <input type="hidden" name="codigo_planillaCP" id="codigo_planillaCP" value="0">        
          <h3>Estás a punto de CERRAR la planilla de retroactivos de la gestión <?=date('Y')?>.<br><b>¿Deseas Continuar?</b></h3>
        </div>       
        <div class="modal-footer">
          <button type="button" class="btn" style="background: #45b39d;" id="AceptarCerrar" data-dismiss="modal">Si, Continuar</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal"> Cancelar </button>
        </div>
      </div>
    </div>
  </div>
  <script type="text/javascript">
    $(document).ready(function(){
      $('#AceptarProceso').click(function(){      
        var cod_planilla=document.getElementById("codigo_planilla").value;      
        ProcesarPlanillaRetroactivo(cod_planilla);
      });
      $('#AceptarReProceso').click(function(){      
        cod_planilla=document.getElementById("codigo_planillaRP").value;      
        ReprocesarPlanillaRetroactivo(cod_planilla);
      });
      $('#AceptarCerrar').click(function(){      
        cod_planilla=document.getElementById("codigo_planillaCP").value;      
        CerrarPlanillaRetroactivo(cod_planilla);
      });
      
    });
  </script>
  