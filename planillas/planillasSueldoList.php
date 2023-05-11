<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'functionsGeneral.php';
require_once 'rrhh/configModule.php';

$globalAdmin=$_SESSION["globalAdmin"];
$globalCodUnidad=$_SESSION["globalUnidad"];
$globalNombreUnidad=$_SESSION["globalNombreUnidad"];


$cod_mes_global=$_SESSION['globalMes'];
$nombre_mes=nombreMes($cod_mes_global);
$codGestionActiva=$_SESSION['globalGestion'];
$globalNombreGestion=$_SESSION['globalNombreGestion'];


$dbh = new Conexion();

  $stmtAdmnin = $dbh->prepare("SELECT codigo,cod_gestion,cod_mes,cod_estadoplanilla,comprobante,
  (select m.nombre from meses m where m.codigo=cod_mes)as mes,
  (select g.nombre from gestiones g where g.codigo=cod_gestion) as gestion,
  (select ep.nombre from estados_planilla ep where ep.codigo=cod_estadoplanilla) as estadoplanilla,cod_comprobante_prevision
  from planillas order by cod_gestion desc,cod_mes desc");
  $stmtAdmnin->execute();
  $stmtAdmnin->bindColumn('codigo', $codigo_planilla);
  $stmtAdmnin->bindColumn('gestion', $gestion);
  $stmtAdmnin->bindColumn('cod_gestion', $cod_gestion);
  $stmtAdmnin->bindColumn('cod_mes', $cod_mes);
  $stmtAdmnin->bindColumn('mes', $mes);
  $stmtAdmnin->bindColumn('cod_estadoplanilla', $cod_estadoplanilla);
  $stmtAdmnin->bindColumn('estadoplanilla', $estadoplanilla);
  $stmtAdmnin->bindColumn('comprobante', $comprobante_x);
  $stmtAdmnin->bindColumn('cod_comprobante_prevision', $cod_comprobante_prevision);
  

  $modified_at="";
  $modified_by="";
  ?>
  <div class="content">
    <div class="container-fluid">
      <div class="col-md-12">     
        <div class="card">
          <div class="card-header <?=$colorCard;?> card-header-text">
            <div class="card-icon">
              <i class="material-icons"><?=$iconCard;?></i>
            </div>
            <h4 class="card-title">Planilla De Sueldos</h4>       
          </div>
          <div class="card-body ">
              <table class="table" id="tablePaginator">
                <thead>
                    <tr>                    
                      <th>Gestión</th>
                      <th>Mes</th>   
                      <th>Estado</th>
                      <th></th> 
                      <th></th>
                      <th></th> 
                    </tr>
                </thead>
                <tbody>
                  <?php $index=1;                  
                  $datosX="";
                  while ($row = $stmtAdmnin->fetch(PDO::FETCH_BOUND)) {
                    //para planilla tributaria
                    $stmtAdmninTrib=$dbh->prepare("SELECT codigo,modified_at,modified_by from planillas_tributarias where cod_mes=$cod_mes and cod_gestion=$cod_gestion");
                    $stmtAdmninTrib->execute();
                    $sinTrib=0;$codigoTrib=0;
                    setlocale(LC_TIME, "Spanish");
                    while ($rowTrib = $stmtAdmninTrib->fetch(PDO::FETCH_ASSOC)) {
                      $sinTrib++;
                      $codigoTrib=$rowTrib['codigo'];
                      $modified_at=strftime('%d de %B de %Y %H:%M:%S',strtotime($rowTrib['modified_at']));
                      $modified_by=nombrePersona($rowTrib['modified_by']);
                      if($modified_by==null){
                        $modified_by="No se encontró";
                      }
                    }
                    //==termina planilla tributaria

                    //para los dropdows de planillas
                    $stmtAdmninUO = $dbh->prepare("SELECT cod_uo,(select uo.abreviatura from unidades_organizacionales uo where uo.codigo=cod_uo) as nombre_uo from personal_area_distribucion where cod_estadoreferencial=1
                    GROUP BY cod_uo");
                    $stmtAdmninUO->execute();
                    $stmtAdmninUO->bindColumn('cod_uo', $cod_uo_x);
                    $stmtAdmninUO->bindColumn('nombre_uo', $nombre_uo_x);
                    $stmtAdmninUOPDF = $dbh->prepare("SELECT cod_uo,(select uo.abreviatura from unidades_organizacionales uo where uo.codigo=cod_uo) as nombre_uo from personal_area_distribucion where cod_estadoreferencial=1
                    GROUP BY cod_uo");
                    $stmtAdmninUOPDF->execute();
                    $stmtAdmninUOPDF->bindColumn('cod_uo', $cod_uo_x);
                    $stmtAdmninUOPDF->bindColumn('nombre_uo', $nombre_uo_x);
                    //termina las consultas de dropdows

                    $label_uo_aux='';
                    $datosX =$codigo_planilla."-";
                    if($cod_estadoplanilla==1){
                      $label='<span class="badge badge-danger">';
                    }
                    if($cod_estadoplanilla==2){
                      $label='<span class="badge badge-warning">';
                      $estadoplanilla='';
                      $stmtAdmninUOAux = $dbh->prepare("SELECT cod_uo,abreviatura from configuraciones_planilla_sueldo
                      GROUP BY abreviatura");
                      $stmtAdmninUOAux->execute();
                      $stmtAdmninUOAux->bindColumn('cod_uo', $cod_uo_aux);
                      $stmtAdmninUOAux->bindColumn('abreviatura', $nombre_uo_aux);
                      while ($row = $stmtAdmninUOAux->fetch(PDO::FETCH_BOUND)) {
                        $stmtAdmninUOAux2 = $dbh->prepare("SELECT cod_uo
                           from planillas_uo_cerrados where cod_planilla=$codigo_planilla and cod_uo=$cod_uo_aux");
                        $stmtAdmninUOAux2->execute();
                        $resultAdmninUOAux2=$stmtAdmninUOAux2->fetch();
                        $cod_uo_aux2=$resultAdmninUOAux2['cod_uo'];                    
                        if($cod_uo_aux==$cod_uo_aux2){
                          $label_uo_aux.='<span class="badge badge-success">'.$nombre_uo_aux.'</span>';
                        }else{
                          $label_uo_aux.='<span class="badge badge-warning">'.$nombre_uo_aux.'</span>';
                        }
                      }
                    }
                    if($cod_estadoplanilla==3){                      
                      $label='<span class="badge badge-success">';
                    }                  
                    ?>
                    <tr>                    
                      <td><?=$gestion?></td>
                      <td><?=$mes;?></td>
                      <td><?=$label.$estadoplanilla."</span>";?><?=$label_uo_aux?></td>
                      <td class="td-actions text-right">
                        <?php
                        if($cod_estadoplanilla==1){    ?>
                          <button type="button" class="btn" style="background-color:#3b83bd;color:#ffffff;" data-toggle="modal" data-target="#modalProcesar" onclick="agregaformPre('<?=$datosX;?>')">
                            <i class="material-icons" title="Procesar Planilla Sueldos">perm_data_setting</i>
                          </button>                      
                          <?php 
                        }                                                                          
                        if($cod_estadoplanilla==2){    ?>
                        <a href='<?=$urlDetallePlanillaPrerequisitos;?>?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>' target="_blank" rel="tooltip" class="btn btn-warning">
                          <i class="material-icons" title="Prerequisitos">chrome_reader_mode</i>
                        </a>
                                            
                        <button type="button" class="btn" style="background-color:#3b83bd;color:#ffffff;" data-toggle="modal" data-target="#modalreProcesar" onclick="agregaformRP('<?=$datosX;?>')">
                          <i class="material-icons" title="Reprocesar Planilla Sueldos">autorenew</i>                   
                        </button>
                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalCerrar" onclick="agregaformCP('<?=$datosX;?>')">
                          <i class="material-icons" title="Cerrar Planilla Sueldos">assignment_returned</i>
                        </button>                        
                        <label class="text-danger">|</label>
                        <?php 
                        
                        if($sinTrib==0){
                           ?>
                          <button type="button" class="btn btn-info"  data-toggle="modal" data-target="#modalreProcesarPlanillaTributaria" onclick="agregaformPreTrib('<?=$codigoTrib;?>','<?=$datosX;?>','<?=$mes?>','<?=$modified_at?>','<?=$modified_by?>')">
                            <i class="material-icons" title="Procesar Planilla Tributaria">perm_data_setting</i>  PT                      
                          </button> 
                           <?php
                        }else{
                           ?>
                          <button type="button" class="btn"  style="background-color:#3b83bd;color:#ffffff;" data-toggle="modal" data-target="#modalreProcesarPlanillaTributaria" onclick="agregaformPreTrib('<?=$codigoTrib;?>','<?=$datosX;?>','<?=$mes?>','<?=$modified_at?>','<?=$modified_by?>')">
                            <i class="material-icons" title="ReProcesar Planilla Tributaria">autorenew</i> PT                       
                          </button> 
                          <!-- <a href='<?=$urlPlanillaTribPersonalPDF;?>?codigo_trib=<?=$codigoTrib;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>' target="_blank" rel="tooltip" class="btn btn-success">
                            <i class="material-icons" title="Ver Planilla Triburaria">remove_red_eye</i> PT                       
                          </a> -->
                          <a href='<?=$urlPlanillaTribPersonalReport;?>?codigo_trib=<?=$codigoTrib;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>' target="_blank" rel="tooltip" class="btn btn-success">
                            <i class="material-icons" title="Ver Planilla Triburaria">remove_red_eye</i> PT                       
                          </a>
                          <a href='<?=$urlPlanillaTribPersonalPDF;?>?codigo_trib=<?=$codigoTrib;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>' target="_blank" rel="tooltip" class="btn btn-danger">
                            <i class="material-icons" title="Ver Planilla Triburaria PDF">remove_red_eye</i> PT                       
                          </a>
                           <?php
                        }
                        ?>
                        <?php }?>

                        <?php if($cod_estadoplanilla==3){    ?>  
                        <!-- <a href='<?=$urlPlanillaTribPersonalPDF;?>?codigo_trib=<?=$codigoTrib;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>' target="_blank" rel="tooltip" class="btn btn-success">            
                            <i class="material-icons" title="Ver Planilla Triburaria">remove_red_eye</i> PT                       
                        </a> -->
                        <a href='<?=$urlPlanillaTribPersonalReport;?>?codigo_trib=<?=$codigoTrib;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>' target="_blank" rel="tooltip" class="btn btn-success">
                            <i class="material-icons" title="Ver Planilla Triburaria">remove_red_eye</i> PT                       
                          </a>
                          <a href='<?=$urlPlanillaTribPersonalPDF;?>?codigo_trib=<?=$codigoTrib;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>' target="_blank" rel="tooltip" class="btn btn-danger">
                            <i class="material-icons" title="Ver Planilla Triburaria PDF">remove_red_eye</i> PT                       
                          </a>
                        <?php }?>                                                                
                      </td>
                      <td class="td-actions text-right">
                        <?php                      
                        if($cod_estadoplanilla==2){    ?>                                                                                                                                 
                        <div class="dropdown">
                          <button class="btn btn-primary dropdown-toggle" type="button" id="reporte_sueldos" data-toggle="dropdown" aria-extended="true">
                            <i class="material-icons" title="Ver Planilla sueldos">remove_red_eye</i>
                            <span class="caret"></span>
                          </button>
                          <ul class="dropdown-menu" role="menu" aria-labelledby="reporte_sueldos">
                            <li role="presentation" class="dropdown-header"><small>OFICINA</small></li>
                            <?php
                            while ($row = $stmtAdmninUO->fetch(PDO::FETCH_BOUND)) {
                              if($cod_uo_x>0){?>
                                <li role="presentation"><a role="item" href="<?=$urlPlanillaSueldoPersonalReporte;?>?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>&codigo_uo=<?=$cod_uo_x;?>" target="_blank"><small><?=$nombre_uo_x;?></small></a></li>
                                <?php 
                              }
                            }
                          ?> 
                          <li role="presentation"><a role="item" href="<?=$urlPlanillaSueldoPersonalReporte;?>?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>&codigo_uo=-100" target="_blank"><small>Ver Todo con % Persona</small></a></li>
                          <li role="presentation"><a role="item" href="planillas/planillaSueldoPersonalReporte2.php?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>&codigo_uo=-100" target="_blank"><small>Ver Todo</small></a></li>


                          <li role="presentation"><a role="item" href="boletas/boletas_print_nuevo.php?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>" target="_blank"><i class="material-icons text-rose">class</i><small>BOLETAS</small></a></li>


                          </ul>
                        </div>
                        <div class="dropdown">
                          <button class="btn btn-danger dropdown-toggle" type="button" id="reporte_sueldos" data-toggle="dropdown" aria-extended="true">
                            <i class="material-icons" title="Ver Planilla sueldos PDF">remove_red_eye</i>
                            <span class="caret"></span>
                          </button>
                          <ul class="dropdown-menu" role="menu" aria-labelledby="reporte_sueldos">
                            <li role="presentation" class="dropdown-header"><small>OFICINA</small></li>
                            <?php
                            while ($row = $stmtAdmninUOPDF->fetch(PDO::FETCH_BOUND)) {
                              if($cod_uo_x>0){?>                                                                
                                  <li role="presentation"><a role="item" href="<?=$urlPlanillaSueldoPersonalActualPDF;?>?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>&codigo_uo=<?=$cod_uo_x;?>" target="_blank"><small><?=$nombre_uo_x;?></small></a>
                                  </li>
                                <?php 
                              }
                            }
                          ?>                              
                          </ul>
                        </div>

                        <?php }?>
                        <?php if($cod_estadoplanilla==3){    ?>      
                        <div class="dropdown">
                          <button class="btn btn-primary dropdown-toggle" type="button" id="reporte_sueldos" data-toggle="dropdown" aria-extended="true">
                            <i class="material-icons" title="Ver Planilla por OF">remove_red_eye</i>
                            <span class="caret"></span>
                          </button>
                          <ul class="dropdown-menu" role="menu" aria-labelledby="reporte_sueldos">
                            <li role="presentation" class="dropdown-header"><small>OFICINA</small></li>
                            <?php
                            while ($row = $stmtAdmninUO->fetch(PDO::FETCH_BOUND)) {
                              if($cod_uo_x>0){?>                                                                
                                  <li role="presentation"><a role="item" href="<?=$urlPlanillaSueldoPersonalReporte;?>?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>&codigo_uo=<?=$cod_uo_x;?>" target="_blank"><small><?=$nombre_uo_x;?></small></a></li>
                                <?php 
                              }
                            }
                          ?>
                          <li role="presentation"><a role="item" href="<?=$urlPlanillaSueldoPersonalReporte;?>?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>&codigo_uo=-100" target="_blank"><small>TODOS</small></a></li>
                          <li role="presentation"><a role="item" href="planillas/planillaSueldoPersonalReporte2.php?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>&codigo_uo=-100" target="_blank"><small>TODOS 100</small></a></li>
                        </ul>
                        <div class="dropdown">
                          <button class="btn btn-danger dropdown-toggle" type="button" id="reporte_sueldos" data-toggle="dropdown" aria-extended="true">
                            <i class="material-icons" title="Ver Planilla sueldos PDF">remove_red_eye</i>
                            <span class="caret"></span>
                          </button>
                          <ul class="dropdown-menu" role="menu" aria-labelledby="reporte_sueldos">
                            <li role="presentation" class="dropdown-header"><small>OFICINA</small></li>
                            <?php
                            while ($row = $stmtAdmninUOPDF->fetch(PDO::FETCH_BOUND)) {
                              if($cod_uo_x>0){?>                                                                
                                  <li role="presentation"><a role="item" href="<?=$urlPlanillaSueldoPersonalActualPDF;?>?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>&codigo_uo=<?=$cod_uo_x;?>" target="_blank"><small><?=$nombre_uo_x;?></small></a>
                                  </li>
                                <?php 
                              }
                            }
                          ?>                              
                          </ul>
                        </div>
                      </div>                                                                 
                      <?php }?>                          
                    </td>
                    
                    <td class="text-center td-actions ">
                      
                        

                        <div class="dropdown">
                          <button class="btn btn-info dropdown-toggle" type="button" id="reporte_sueldos" data-toggle="dropdown" aria-extended="true">
                            <i class="material-icons" title="Comprobante">chrome_reader_mode</i>
                            <span class="caret"></span>
                          </button>
                          
                          <ul class="dropdown-menu" role="menu" aria-labelledby="reporte_sueldos">
                            
                            <li>
                              <button type="button" class="dropdown-item" data-toggle="modal" data-target="#modalVistaPreviaPlanilla" onclick="agregarDatosVistaPreviaPlanilla('<?=$codigo_planilla;?>','<?=$cod_mes?>','<?=$cod_gestion?>')">
                              <i class="material-icons">list</i>Vista Previa
                            </button> 
                            </li>
                            <?php if($comprobante_x==0){ ?>
                            <li>
                              <a role="item" href="#" onclick="alerts.showSwal('warning-message-and-confirmationGeneral','<?=$urlPlanillaContabilizacion;?>?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>')"> 
                                <i class="material-icons" title="Generar Comprobante" style="color:red">input</i>Comprobante Planilla
                              </a>
                            </li>
                            <?php } ?>
                            <?php if($cod_comprobante_prevision==0){ ?>
                            <li>
                              <a role="item" href="#" onclick="alerts.showSwal('warning-message-and-confirmationGeneral','planillas/executeComprobanteProvisionAguinaldos.php?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>')"> 
                                <i class="material-icons" title="Generar Comprobante Previsión" style="color:red">input</i>Comprobante Previsión
                              </a>
                            </li>
                            <?php } ?>
                          </ul>
                        </div>


                        
                      
                    </td>

                    </tr>
                  <?php $index++; } ?>
                </tbody>                                      
              </table>
          </div>
          <div class="card-footer fixed-bottom">
            <!-- <a href='<?=$urlGenerarPlanillaSueldoPrevia;?>' rel="tooltip" class="btn btn-success">
              Registrar Planilla Del Mes Actual
            </a> -->

            <?php
              if($globalAdmin==1){
              ?>
                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modalGenerarPlanilla">Registrar Planilla Actual (1)</button> 
                <a href="bonos/descargarExcelGlobal.php" target="_blank" class="btn btn-warning"><span class="material-icons">download</span>Descargar Plantilla (2)</a>
                <button class="btn btn-success" onClick="location.href='index.php?opcion=subirBonoExcel_global_from'"><span class="material-icons">file_upload</span>Cargar Plantilla (3)</button>

                <!-- <a href="bonos/plantilla_sueldos_editar.php" target="_blank" class="btn btn-primary btn-sm"><span class="material-icons">edit</span>Editar Plantilla (3.1)</a> -->
                
              <?php
              }
            ?>
          </div>  
        </div>
      </div>
    </div>
  </div>
  <!--modal procesar-->
  <div class="modal fade" id="modalProcesar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">¿Estás Seguro?</h4>
        </div>
        <div class="modal-body">
          <input type="hidden" name="codigo_planilla" id="codigo_planilla" value="0">        
          Esta acción Procesará La planilla Del Mes En Curso. ¿Deseas Continuar?
          <div id="cargaP" style="display:none">
            <h3><b>Por favor espere...</b></h3>
          </div>
        </div>       
        <div class="modal-footer">
          <button type="button" class="btn btn-success" id="AceptarProceso">Aceptar</button>
          <button type="button" class="btn btn-danger" id="CancelarProceso" data-dismiss="modal" > <-- Volver </button>
        </div>
      </div>
    </div>
  </div>
  <!--modal Reprocesar-->
  <div class="modal fade" id="modalreProcesar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">¿Estás Seguro?</h4>
        </div>
        <div class="modal-body">
          <input type="hidden" name="codigo_planillaRP" id="codigo_planillaRP" value="0">        
          Esta acción ReProcesará La planilla Del Mes En Curso. ¿Deseas Continuar?
          <div id="cargaR" style="display:none">
            <h3><b>Por favor espere...</b></h3>
          </div>
        </div>    
        <div class="modal-footer">
          <button type="button" class="btn btn-success" id="AceptarReProceso" >Aceptar</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal" id="CancelarReProceso"> <-- Volver </button>
        </div>
      </div>
    </div>
  </div>
  <!--modal Cerrra-->
  <div class="modal fade" id="modalCerrar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">¿Estás Seguro?</h4>
        </div>
        <div class="modal-body">
          <input type="hidden" name="codigo_planillaCP" id="codigo_planillaCP" value="0">        
          Esta acción Cerrará La planilla Del Mes En Curso. ¿Deseas Continuar?
        </div>       
        <div class="modal-footer">
          <button type="button" class="btn btn-success" id="AceptarCerrar" data-dismiss="modal">Aceptar</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal"> <-- Volver </button>
        </div>
      </div>
    </div>
  </div>
  <!--Modal planilla tributarua-->
  <div class="modal fade" id="modalreProcesarPlanillaTributaria" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title text-danger font-weight-bold" id="myModalLabel">PLANILLA TRIBUTARIA</h4>
        </div>
        <div class="modal-body">
          <input type="hidden" name="codigo_planilla2" id="codigo_planilla2" value="0">
          <input type="hidden" name="codigo_planilla_trib" id="codigo_planilla_trib" value="0">        
          
          <table class="table table-condensed">
            <thead>
              <tr class="text-danger">
                <th>Ultimo Proceso Realizado</th>
                <th>Usuario</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td id="modified_at">No hay datos</td>
                <td id="modified_by">No hay datos</td>
              </tr>
            </tbody>
          </table>
          <hr>
          Esta acción Procesará La planilla TRIBUTARIA Del Mes <b class="font-weight-bold" id="mes_cursotitulo">En Curso</b>. ¿Deseas Continuar?
          <div id="cargaR2" style="display:none">
            <h3><b>Por favor espere...</b></h3>
          </div>
        </div>   
        <div class="modal-footer">
          <button type="button" class="btn btn-success" id="AceptarReProcesoTrib" >Aceptar</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal" id="CancelarReProcesoTrib"> <-- Volver </button>
        </div>
      </div>
    </div>
  </div>


    <!--Generar Planilla-->
  <div class="modal fade" id="modalGenerarPlanilla" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">Registrar Planilla<br> <?=$nombre_mes?>/<?=$globalNombreGestion?></h4>
          
        </div>
        <div class="modal-body">
            <div class="row">
              <label class="form-group col-sm-12 text-center"><b>Introduzca Días trabajados L-V</b></label>
            </div>
            <div class="row">
              <div class="form-group col-sm-12">
                <input class="form-control input-sm" type="text" name="dias_trabajado" id="dias_trabajado">
              </div>
            </div>
        </div>       
        <div class="modal-footer">
          <button type="button" class="btn btn-success" id="AceptarRegistro" onClick="registrar_planilla_sueldos()">Guardar</button>
          <button type="button" class="btn btn-danger" id="CancelarResgistro" data-dismiss="modal"> Cancelar </button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalVistaPreviaPlanilla" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title text-danger font-weight-bold" id="myModalLabel">CONTABILIZACION PLANILLA - VISTA PREVIA</h4>
        </div>
        <div class="modal-body" id="div_contenedor_vistaprevia_planilla">
          <table class="table table-condensed" >
            <thead>
              <tr class="text-danger">
                <th>CC</th>
                <th>PERSONAL</th>
                <th>MONTO</th>
                <th>PORCENTAJE</th>
                <!-- <th>HABER</th> -->
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>

        </div>
      </div>
    </div>
  </div>


  <script type="text/javascript">
    $(document).ready(function(){
      $('#AceptarProceso').click(function(){      
        var cod_planilla=document.getElementById("codigo_planilla").value;      
        ProcesarPlanilla(cod_planilla);
      });
      $('#AceptarReProceso').click(function(){      
        cod_planilla=document.getElementById("codigo_planillaRP").value;      
        ReprocesarPlanilla(cod_planilla);
      });
      $('#AceptarCerrar').click(function(){      
        cod_planilla=document.getElementById("codigo_planillaCP").value;
        CerrarPlanilla(cod_planilla);
      });
      
    });

    function mostrarFilaTablaHorario(codigo){   
      var mostrar=0; 
      $(".fila_"+codigo).each(function(){
          if($(this).hasClass("d-none")){
            $(this).removeClass("d-none");
            mostrar++;
          }else{
            $(this).addClass("d-none");
          }
      }); 

      if(mostrar>0){      
        if(!$("#icono_"+codigo).hasClass("text-danger")){
          $("#icono_"+codigo).removeClass("text-success");
          $("#icono_"+codigo).addClass("text-danger");
        }
        $("#icono_"+codigo).html("do_not_disturb_on");      
      }else{
        if(!$("#icono_"+codigo).hasClass("text-success")){
          $("#icono_"+codigo).removeClass("text-danger");
          $("#icono_"+codigo).addClass("text-success");
        }  
        $("#icono_"+codigo).html("add_circle"); 
      }   
    }
  </script>
