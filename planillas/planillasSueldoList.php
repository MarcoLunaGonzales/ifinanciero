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

$sql = "SELECT p.codigo,p.cod_gestion,p.cod_mes,p.cod_estadoplanilla,p.cod_estado_documento,p.comprobante,
(select m.nombre from meses m where m.codigo=p.cod_mes)as mes,
(select g.nombre from gestiones g where g.codigo=p.cod_gestion) as gestion,
(select ep.nombre from estados_planilla ep where ep.codigo=p.cod_estadoplanilla) as estadoplanilla,p.cod_comprobante_prevision
from planillas p order by cod_gestion desc,cod_mes desc";
  $stmtAdmnin = $dbh->prepare($sql);
  $stmtAdmnin->execute();
  $stmtAdmnin->bindColumn('codigo', $codigo_planilla);
  $stmtAdmnin->bindColumn('gestion', $gestion);
  $stmtAdmnin->bindColumn('cod_gestion', $cod_gestion);
  $stmtAdmnin->bindColumn('cod_mes', $cod_mes);
  $stmtAdmnin->bindColumn('mes', $mes);
  $stmtAdmnin->bindColumn('cod_estadoplanilla', $cod_estadoplanilla);
  $stmtAdmnin->bindColumn('cod_estado_documento', $cod_estado_documento);
  $stmtAdmnin->bindColumn('estadoplanilla', $estadoplanilla);
  $stmtAdmnin->bindColumn('comprobante', $comprobante_x);
  $stmtAdmnin->bindColumn('cod_comprobante_prevision', $cod_comprobante_prevision);
  

  $modified_at="";
  $modified_by="";
  ?>

<div class="cargar-ajax d-none">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold" id="texto_ajax_titulo">Procesando Datos</h4>
     <p class="text-white">Aguard&aacute; un momento por favor</p>  
  </div>
</div>

    <!-- Modal Reporte Viáticos Personal Interno -->
    <div class="modal fade" id="modalViatico" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><b>Viáticos Personal Interno</b></h4>
                </div>
                <div class="modal-body" id="modal-body-viatico">
                </div>    
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

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
                      <th class="text-right">P. Tributaria</th> 
                      <th class="text-center">Archivos</th>
                      <th class="text-center">Procesos <br> Planilla</th> 
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
                      /***************************************************************************************/
                      /*                 SECCIÓN COMENTADA PARA QUITAR LAS UNIDADES DE SPAN                  */
                      /***************************************************************************************/
                      // $estadoplanilla='';
                      // $stmtAdmninUOAux = $dbh->prepare("SELECT cod_uo,abreviatura from configuraciones_planilla_sueldo
                      // GROUP BY abreviatura");
                      // $stmtAdmninUOAux->execute();
                      // $stmtAdmninUOAux->bindColumn('cod_uo', $cod_uo_aux);
                      // $stmtAdmninUOAux->bindColumn('abreviatura', $nombre_uo_aux);
                      // while ($row = $stmtAdmninUOAux->fetch(PDO::FETCH_BOUND)) {
                      //   $stmtAdmninUOAux2 = $dbh->prepare("SELECT cod_uo
                      //      from planillas_uo_cerrados where cod_planilla=$codigo_planilla and cod_uo=$cod_uo_aux");
                      //   $stmtAdmninUOAux2->execute();
                      //   $resultAdmninUOAux2=$stmtAdmninUOAux2->fetch();
                      //   // Corrección por tener valor vacio
                      //   $cod_uo_aux2=empty($resultAdmninUOAux2['cod_uo'])?'':$resultAdmninUOAux2['cod_uo'];                    
                      //   if($cod_uo_aux==$cod_uo_aux2){
                      //     $label_uo_aux.='<span class="badge badge-success">'.$nombre_uo_aux.'</span>';
                      //   }else{
                      //     $label_uo_aux.='<span class="badge badge-warning">'.$nombre_uo_aux.'</span>';
                      //   }
                      // }
                    }
                    if($cod_estadoplanilla==3){                      
                      $label='<span class="badge badge-success">';
                    }    
                    
                    // Verificación de Estado

                    // Estado Cerrado Vacio
                    if($cod_estadoplanilla==4){
                      $label_uo_aux.='<span class="badge badge-success">CERRADO VACIO</span>';
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
                          <!-- PRE-REQUISITOS -->
                        <!-- <a href='<?=$urlDetallePlanillaPrerequisitos;?>?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>' target="_blank" rel="tooltip" class="btn btn-warning">
                          <i class="material-icons" title="Prerequisitos">chrome_reader_mode</i>
                        </a> -->
                        
                        <!-- REPROCESAR PLANILLAS SUELDOS -->
                        <!-- <button type="button" class="btn" style="background-color:#3b83bd;color:#ffffff;" data-toggle="modal" data-target="#modalreProcesar" onclick="agregaformRP('<?=$datosX;?>')">
                          <i class="material-icons" title="Reprocesar Planilla Sueldos">autorenew</i>                   
                        </button> -->
                        <!-- CERRAR PLANILLA SUELDOS -->
                        <!-- <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalCerrar" onclick="agregaformCP('<?=$datosX;?>')">
                          <i class="material-icons" title="Cerrar Planilla Sueldos">assignment_returned</i>
                        </button>                         -->
                        <!-- <label class="text-danger">|</label> -->
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
                            <i class="material-icons" title="Ver Planilla Tributaria">remove_red_eye</i> PT                       
                          </a> -->
                          <a href='<?=$urlPlanillaTribPersonalReport;?>?codigo_trib=<?=$codigoTrib;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>' target="_blank" rel="tooltip" class="btn btn-success">
                            <i class="material-icons" title="Ver Planilla Tributaria">remove_red_eye</i> PT                       
                          </a>
                          <a href='<?=$urlPlanillaTribPersonalPDF;?>?codigo_trib=<?=$codigoTrib;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>' target="_blank" rel="tooltip" class="btn btn-danger">
                            <i class="material-icons" title="Ver Planilla Tributaria PDF">remove_red_eye</i> PT                       
                          </a>
                           <?php
                        }
                        ?>
                        <?php }?>

                        <?php if($cod_estadoplanilla==3){    ?>  
                        <!-- <a href='<?=$urlPlanillaTribPersonalPDF;?>?codigo_trib=<?=$codigoTrib;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>' target="_blank" rel="tooltip" class="btn btn-success">            
                            <i class="material-icons" title="Ver Planilla Tributaria">remove_red_eye</i> PT                       
                        </a> -->
                        <a href='<?=$urlPlanillaTribPersonalReport;?>?codigo_trib=<?=$codigoTrib;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>' target="_blank" rel="tooltip" class="btn btn-success">
                            <i class="material-icons" title="Ver Planilla Tributaria">remove_red_eye</i> PT                       
                          </a>
                          <a href='<?=$urlPlanillaTribPersonalPDF;?>?codigo_trib=<?=$codigoTrib;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>' target="_blank" rel="tooltip" class="btn btn-danger">
                            <i class="material-icons" title="Ver Planilla Tributaria PDF">remove_red_eye</i> PT                       
                          </a>
                        <?php }?>   


                      </td>
                      <td class="td-actions text-center">
                        
                        <?php if($cod_estado_documento == 1){    ?>  
                          <!-- CAMBIAR DE ESTADO DE DOCUMENTOS -->
                          <button type="button" class="btn btn-warning update_estado_documento" title="Cerrar acción de Ajuntar Archivos" data-codigo="<?=$codigo_planilla;?>">
                            <i class="material-icons">folder</i>                       
                          </button>   
                        <?php }?>   

                        <?php if(!in_array($cod_estadoplanilla, [4])){    ?>
                          <!-- CAMBIAR DE ESTADO DE PLANILLA A CERRADO VACIO -->
                          <button type="button" class="btn btn-default update_planilla_cerrado_vacio" title="Cerrar planilla en vacío" data-codigo="<?=$codigo_planilla;?>">
                            <i class="material-icons">perm_data_setting</i>     
                          </button>  
                        <?php }?>  
                         
                    </td>
                    
                    <td class="text-center td-actions ">
                      
                        <!-- VISUALIZACIÓN DE CAJA NACIONAL DE SALUD y OVT -->
                        <?php                      
                        if(($cod_estadoplanilla==2 || $cod_estadoplanilla==3)){?>
                        <div class="dropdown">
                          <button class="btn btn-danger dropdown-toggle" type="button" id="reporte_sueldos" data-toggle="dropdown" aria-extended="true">
                            <i class="material-icons" title="Planillas - Formato Interno">remove_red_eye</i>
                            <span class="caret"></span>
                          </button>
                          <ul class="dropdown-menu menu-fixed-sm-table" role="menu" aria-labelledby="reporte_sueldos">
                            <li role="presentation"><a role="item" href="planillas/reportePlanillasSueldos_CPS.php?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>&tipo=1" target="_blank"><i class="material-icons text-danger">add_business</i><small>PLANILLA CPS</small></a></li>
                            <li role="presentation"><a role="item" href="planillas/reportePlanillasSueldos_OBT.php?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>" target="_blank"><i class="material-icons text-info">article</i><small>PLANILLA OVT</small></a></li>
                          </ul>
                        </div>
                        <?php } ?>
                        <!-- ============================================= -->

                      <?php                      
                          if($cod_estadoplanilla==2){    ?>                                                                                                                                 
                          <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" id="reporte_sueldos" data-toggle="dropdown" aria-extended="true">
                              <i class="material-icons" title="Ver Planilla sueldos">remove_red_eye</i>
                              <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu" aria-labelledby="reporte_sueldos">
                              <!-- <li role="presentation" class="dropdown-header"><small>OFICINA</small></li>
                              <?php
                              while ($row = $stmtAdmninUO->fetch(PDO::FETCH_BOUND)) {
                                if($cod_uo_x>0){?>
                                  <li role="presentation"><a role="item" href="<?=$urlPlanillaSueldoPersonalReporte;?>?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>&codigo_uo=<?=$cod_uo_x;?>" target="_blank"><small><?=$nombre_uo_x;?></small></a></li>
                                  <?php 
                                }
                              }
                            ?>  -->
                            <li role="presentation"><a role="item" href="<?=$urlPlanillaSueldoPersonalReporte;?>?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>&codigo_uo=-100" target="_blank"><small>Ver Todo con % Persona</small></a></li>
                            <li role="presentation"><a role="item" href="planillas/planillaSueldoPersonalReporte2.php?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>&codigo_uo=-100" target="_blank"><small>Ver Todo</small></a></li>
                            </ul>
                          </div>
                          
                          <!-- COMENTADO - SIN ACCIÓN -->
                          <!-- <div class="dropdown">
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
                          </div> -->

                          <?php }?>
                          <?php if($cod_estadoplanilla==3){    ?>      
                          <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" id="reporte_sueldos" data-toggle="dropdown" aria-extended="true">
                              <i class="material-icons" title="Ver Planilla por OF">remove_red_eye</i>
                              <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu" aria-labelledby="reporte_sueldos">
                              <!-- <li role="presentation" class="dropdown-header"><small>OFICINA</small></li>
                              <?php
                              while ($row = $stmtAdmninUO->fetch(PDO::FETCH_BOUND)) {
                                if($cod_uo_x>0){?>                                                                
                                    <li role="presentation"><a role="item" href="<?=$urlPlanillaSueldoPersonalReporte;?>?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>&codigo_uo=<?=$cod_uo_x;?>" target="_blank"><small><?=$nombre_uo_x;?></small></a></li>
                                  <?php 
                                }
                              }
                            ?> -->
                            <li role="presentation"><a role="item" href="<?=$urlPlanillaSueldoPersonalReporte;?>?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>&codigo_uo=-100" target="_blank"><small>TODOS</small></a></li>
                            <li role="presentation"><a role="item" href="planillas/planillaSueldoPersonalReporte2.php?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>&codigo_uo=-100" target="_blank"><small>TODOS 100</small></a></li>
                          </ul>
                          <!-- COMENTADO - SIN ACCIÓN -->
                          <!-- <div class="dropdown">
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
                          </div> -->
                        </div>                                                                 
                        <?php }?> 

                        <div class="dropdown">
                          <button class="btn btn-info dropdown-toggle" type="button" id="reporte_sueldos" data-toggle="dropdown" aria-extended="true">
                            <i class="material-icons" title="Comprobante">chrome_reader_mode</i>
                            <span class="caret"></span>
                          </button>
                          
                          <ul class="dropdown-menu" role="menu" aria-labelledby="reporte_sueldos">
                            <?php if($cod_estadoplanilla != 4){ ?>
                            <li>
                              <button type="button" class="dropdown-item" data-toggle="modal" data-target="#modalVistaPreviaPlanilla" onclick="agregarDatosVistaPreviaPlanilla('<?=$codigo_planilla;?>','<?=$cod_mes?>','<?=$cod_gestion?>')">
                              <i class="material-icons">list</i>Vista Previa
                            </button> 
                            </li>
                            <!-- REPROCESAR PLANILLAS SUELDOS -->
                            <?php
                              if($cod_estadoplanilla==2){
                            ?>
                            <li>
                              <button type="button" class="dropdown-item" data-toggle="modal" data-target="#modalreProcesar" onclick="agregaformRP('<?=$datosX;?>')">
                              <i class="material-icons">autorenew</i> Reprocesar Planilla Sueldos
                              </button>
                            </li>
                            <?php
                              }
                            ?>
                            <!--li role="presentation">
                              <a role="item" href="boletas/boletas_print_nuevo.php?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>" target="_blank"><i class="material-icons text-rose">preview</i><small>Boletas</small></a>
                            </li-->
                            <li role="presentation" onclick="sendEmailBoleta(<?=$codigo_planilla;?>)">
                              <a role="item" 
                                href="#">
                                <i class="material-icons text-rose">email</i><small>Enviar Boletas por Correo</small></a>
                            </li>
                            <li role="presentation">
                              <a role="item" href="index.php?opcion=planillasSueldoPersonalDetail&codigo_planilla=<?=$codigo_planilla;?>">
                                <i class="material-icons text-success">assessment</i><small> Reporte Visitas</small></a>
                            </li>
                            <?php } ?>
                            
                            <?php if($cod_estado_documento == 1){ ?>
                              <li>
                                  <button type="button" class="dropdown-item adjuntarNuevoArchivo" data-cod_planilla="<?=$codigo_planilla;?>">
                                      <i class="material-icons">archive</i>Adjuntar Archivo
                                  </button> 
                              </li>
                            <?php } ?>

                            <li>
                                <button type="button" class="dropdown-item listarArchivos" data-cod_planilla="<?=$codigo_planilla;?>" data-cod_estado_documento="<?=$cod_estado_documento;?>">
                                    <i class="material-icons">list</i>Lista Archivos
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
                            <!-- CERRAR PLANILLA SUELDOS -->
                            <?php
                              if($cod_estadoplanilla==2){
                            ?>
                            <li>
                              <button type="button" class="dropdown-item" data-toggle="modal" data-target="#modalCerrar" onclick="agregaformCP('<?=$datosX;?>')">
                              <i class="material-icons">assignment_returned</i> Cerrar Planilla Sueldos
                              </button>
                            </li>
                            <?php
                              }
                            ?>
                          </ul>
                        </div>

                        <!-- Ver Reporte Viáticos Personal Interno -->
                        <button type="button" 
                                class="btn btn-danger abreModalViatico" 
                                title="Reporte de Viáticos Personal Interno"
                                data-gestion="<?= $gestion ?>"
                                data-mes="<?= $cod_mes ?>"
                            ><i class="material-icons">flight_takeoff</i>
                        </button>

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
          Esta acción Procesará La planilla Del Mes En Curso. ¿Desea Continuar?
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
          Esta acción ReProcesará La planilla Del Mes En Curso. ¿Desea Continuar?
          <div id="cargaR" style="display:none">
            <h3><b>Por favor espere...</b></h3>
          </div>
            <div class="form-group" id="cargaPersonal">
                <select name="cod_personal" id="cod_personal" class="selectpicker form-control form-control-sm" data-style="btn btn-primary"  data-show-subtext="true" data-live-search="true" required="true">
                    <option selected value="">TODO LOS REGISTROS</option>
                    <?php 
                    $sql = "SELECT p.codigo, UPPER(CONCAT(p.primer_nombre, ' ', p.paterno, ' ', p.materno)) as nombre_personal 
                            FROM personal p 
                            WHERE p.cod_estadoreferencial = 1 
                            AND p.bandera = 1
                            ORDER BY p.primer_nombre";
                    $stmt = $dbh->query($sql);
                    while ($row = $stmt->fetch()){ ?>
                        <option value="<?=$row["codigo"];?>"><?=$row["nombre_personal"];?></option>
                    <?php } ?>
                </select>
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
          Esta acción Cerrará La planilla Del Mes En Curso. ¿Desea Continuar?
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
          Esta acción Procesará La planilla TRIBUTARIA Del Mes <b class="font-weight-bold" id="mes_cursotitulo">En Curso</b>. ¿Desea Continuar?
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

    <!-- Modal Lista Documentos -->
    <div class="modal fade" id="modalListaDocumentosPlanilla" tabindex="-1" role="dialog" aria-labelledby="myModalLabel3">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title text-primary font-weight-bold" id="myModalLabel3">Lista Documentos Respaldo</h4>
                </div>
                <div class="modal-body" id="modal-lista_documentos">
                    <h2>porueba</h2>
                </div>
            </div>
        </div>
    </div>

  <div class="modal fade" id="modalVistaPreviaPlanilla" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title text-danger font-weight-bold" id="myModalLabel">Formular</h4>
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
    <!-- Modal Detalle de Formulario Documento -->
    <div class="modal fade" id="modalNuevoDocumento" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title text-primary font-weight-bold" id="myModalLabel">Documentos Respaldo</h4>
                </div>
            <div class="modal-body">
                <form id="doc_form">
                    <div class="row">
                        <input type="hidden" id="doc_cod_planilla"/>
                        <label class="col-sm-2"><span class="text-danger">*</span> Archivo :</label>
                        <div class="col-sm-10">
                            <input class="form-control" type="file" id="doc_file" placeholder="Agrear Archivo"/>
                        </div>
                        <label class="col-sm-2"><span class="text-danger">*</span> Descripción :</label>
                        <div class="col-sm-10">
                            <input class="form-control" type="text" id="doc_descripcion" placeholder="Agregar una descripción del Archivo"/>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="save_documento" class="btn btn-success">Guardar</button>
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
        $('#cargaPersonal').hide();    
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

    
function sendEmailBoleta(cod_planilla){
    let formData = new FormData();
    formData.append('cod_planilla', cod_planilla);
    swal({
        title: '¿Esta Seguro de Continuar?',
        text: "Se enviará un correo a todo el personal de la institución.",
        type: 'warning',
        showCancelButton: true,
        confirmButtonClass: 'btn btn-warning',
        cancelButtonClass: 'btn btn-danger',
        confirmButtonText: 'Si',
        cancelButtonText: 'No',
        buttonsStyling: false
    }).then((result) => {
        if (result.value) {
            $(".cargar-ajax").removeClass("d-none");
            $.ajax({
                url:"sendEmailPlanilla.php",
                type:"POST",
                contentType: false,
                processData: false,
                data: formData,
                success:function(response){
                let resp = JSON.parse(response);
                if(resp.status){        
                    // Mensaje
                    Swal.fire({
                        type: 'success',
                        title: 'Correcto!',
                        text: 'El proceso se completo correctamente!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                        
                    setTimeout(function(){
                        location.reload()
                    }, 1550);
                }else{
                    Swal.fire('ERROR!','El proceso tuvo un problema!. Contacte con el administrador!','error'); 
                    }
                }
            });
        }
    });

}
    // Apertura de Modal
    $('.adjuntarNuevoArchivo').on('click', function(){
        $("#doc_form")[0].reset();
        $('#doc_cod_planilla').val($(this).data('cod_planilla'));
        $('#modalNuevoDocumento').modal('show');
    })
    // Lista de Archivos Adjuntos
    $('.listarArchivos').on('click', function(){
        let formData = new FormData();
        formData.append('cod_planilla', $(this).data('cod_planilla'));
        formData.append('cod_estado_documento', $(this).data('cod_estado_documento'));
        $.ajax({
            url:"planillas/ajax_listaDocumentos.php",
            type:"POST",
            contentType: false,
            processData: false,
            data: formData,
            success:function(response){
                $('#modal-lista_documentos').html(response);
                $('#modalListaDocumentosPlanilla').modal('show');
            }
        });
    });
    // Guardar Archivo
    $('#save_documento').on('click', function(){
        if($("#descripcion").val() != '' && $("#file").val() != ''){
            let formData = new FormData();
            formData.append('cod_planilla', $('#doc_cod_planilla').val());
            formData.append('descripcion', $('#doc_descripcion').val());
            formData.append('file', $("#doc_file")[0].files[0]);
            $.ajax({
                url:"planillas/savePlanillaDocumento.php",
                type:"POST",
                contentType: false,
                processData: false,
                data: formData,
                success:function(response){
                    let resp = JSON.parse(response);
                    $('#modalNuevoDocumento').modal('toggle');
                    if(resp.status){
                        Swal.fire({
                            type: 'success',
                            title: 'Correcto!',
                            text: 'El proceso se completo correctamente!',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }else{
                        Swal.fire(
                            'Oops...',
                            'Ocurrio un error inesperado',
                            'error'
                        );
                    }
                }
            });
        }else{
            Swal.fire(
                'Oops...',
                '¡Debe completar el formulario!',
                'warning'
            );
        }
    });
    // Eliminacion de archivo (Lógico)
    $('body').on('click','.eliminar_archivo', function(){
      let formData = new FormData();
      // codigo Planilla
      formData.append('codigo', $(this).data('codigo'));
      swal({
          title: '¿Esta seguro de Eliminar?',
          text: "Se eliminará el archivo seleccionado.",
          type: 'warning',
          showCancelButton: true,
          confirmButtonClass: 'btn btn-success',
          cancelButtonClass: 'btn btn-danger',
          confirmButtonText: 'Si',
          cancelButtonText: 'No',
          buttonsStyling: false
      }).then((result) => {
          if (result.value) {
              $.ajax({
                  url:"planillas/ajax_eliminarDocumento.php",
                  type:"POST",
                  contentType: false,
                  processData: false,
                  data: formData,
                  success:function(response){
                  $('#modalListaDocumentosPlanilla').modal('toggle');
                  let resp = JSON.parse(response);
                  if(resp.status){        
                      // Mensaje
                      Swal.fire({
                          type: 'success',
                          title: 'Correcto!',
                          text: 'El proceso se completo correctamente!',
                          showConfirmButton: false,
                          timer: 1500
                      });
                  }else{
                      Swal.fire('ERROR!','El proceso tuvo un problema!. Contacte con el administrador!','error'); 
                      }
                  }
              });
          }
      });
    });

    // Cambiar Estado de proceso de administración de Documentos
    $('body').on('click','.update_estado_documento', function(){
      let formData = new FormData();
      // codigo Planilla
      formData.append('codigo', $(this).data('codigo'));
      swal({
          title: '¿Esta seguro de Cerrar Archivos?',
          text: "Se cerrará el proceso de adjunción de archivos, no se podrá revertir la acción.",
          type: 'warning',
          showCancelButton: true,
          confirmButtonClass: 'btn btn-success',
          cancelButtonClass: 'btn btn-danger',
          confirmButtonText: 'Si',
          cancelButtonText: 'No',
          buttonsStyling: false
      }).then((result) => {
          if (result.value) {
              $.ajax({
                  url:"planillas/ajax_updateEstadoDocumento.php",
                  type:"POST",
                  contentType: false,
                  processData: false,
                  data: formData,
                  success:function(response){
                  let resp = JSON.parse(response);
                  if(resp.status){        
                      // Mensaje
                      Swal.fire({
                          type: 'success',
                          title: 'Correcto!',
                          text: 'El proceso se completo correctamente!',
                          showConfirmButton: false,
                          timer: 1500
                      });
                      
                      setTimeout(function(){
                          location.reload()
                      }, 1550);
                  }else{
                      Swal.fire('ERROR!','El proceso tuvo un problema!. Contacte con el administrador!','error'); 
                      }
                  }
              });
          }
      });
    });

    // Cambiar Estado de Planilla a Cerrado en Vacio
    $('body').on('click','.update_planilla_cerrado_vacio', function(){
      let formData = new FormData();
      // codigo Planilla
      formData.append('codigo', $(this).data('codigo'));
      swal({
          title: '¿Esta seguro de cerrar Planilla en Vacio?',
          text: "Se cerrará la planilla sin contenido, no se podrá revertir la acción.",
          type: 'warning',
          showCancelButton: true,
          confirmButtonClass: 'btn btn-success',
          cancelButtonClass: 'btn btn-danger',
          confirmButtonText: 'Si',
          cancelButtonText: 'No',
          buttonsStyling: false
      }).then((result) => {
          if (result.value) {
              $.ajax({
                  url:"planillas/ajax_updateEstadoPlanilla.php",
                  type:"POST",
                  contentType: false,
                  processData: false,
                  data: formData,
                  success:function(response){
                  let resp = JSON.parse(response);
                  if(resp.status){        
                      // Mensaje
                      Swal.fire({
                          type: 'success',
                          title: 'Correcto!',
                          text: 'El proceso se completo correctamente!',
                          showConfirmButton: false,
                          timer: 1500
                      });
                      
                      setTimeout(function(){
                          location.reload()
                      }, 1550);
                  }else{
                      Swal.fire('ERROR!','El proceso tuvo un problema!. Contacte con el administrador!','error'); 
                      }
                  }
              });
          }
      });
    });

    /**
     * Carga la lista de Viaticos por personal
     */
    $('.abreModalViatico').on('click', function(){
        let formData = new FormData();
        formData.append('gestion', $(this).data('gestion'));
        formData.append('mes', $(this).data('mes'));
        $.ajax({
            url:"planillas/ajax_listaViaticosPersonal.php",
            type:"POST",
            contentType: false,
            processData: false,
            data: formData,
            success:function(response){
                $('#modal-body-viatico').html(response);
                $('#modalViatico').modal('show');
            }
        });
    });
    
  </script>
