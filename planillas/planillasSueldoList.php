<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'functionsGeneral.php';
require_once 'rrhh/configModule.php';

$globalAdmin=$_SESSION["globalAdmin"];
$globalCodUnidad=$_SESSION["globalUnidad"];
$globalNombreUnidad=$_SESSION["globalNombreUnidad"];

$dbh = new Conexion();

if($globalAdmin==1){//para personal admin
  $stmtAdmnin = $dbh->prepare("SELECT codigo,cod_gestion,cod_mes,cod_estadoplanilla,
  (select m.nombre from meses m where m.codigo=cod_mes)as mes,
  (select g.nombre from gestiones g where g.codigo=cod_gestion) as gestion,
  (select ep.nombre from estados_planilla ep where ep.codigo=cod_estadoplanilla) as estadoplanilla
  from planillas 
  ");
  $stmtAdmnin->execute();
  $stmtAdmnin->bindColumn('codigo', $codigo_planilla);
  $stmtAdmnin->bindColumn('gestion', $gestion);
  $stmtAdmnin->bindColumn('cod_gestion', $cod_gestion);
  $stmtAdmnin->bindColumn('cod_mes', $cod_mes);
  $stmtAdmnin->bindColumn('mes', $mes);
  $stmtAdmnin->bindColumn('cod_estadoplanilla', $cod_estadoplanilla);
  $stmtAdmnin->bindColumn('estadoplanilla', $estadoplanilla);

  $stmtAdmninUO = $dbh->prepare("SELECT cod_uo,(select uo.abreviatura from unidades_organizacionales uo where uo.codigo=cod_uo) as nombre_uo from personal_area_distribucion where cod_estadoreferencial=1
  GROUP BY cod_uo");
  $stmtAdmninUO->execute();
  $stmtAdmninUO->bindColumn('cod_uo', $cod_uo_x);
  $stmtAdmninUO->bindColumn('nombre_uo', $nombre_uo_x);
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
                          <i class="material-icons" title="Procesar Planilla">perm_data_setting</i>
                        </button>                      
                        <?php }                                                                          
                        if($cod_estadoplanilla==2){    ?>
                        <a href='<?=$urlDetallePlanillaPrerequisitos;?>?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>' target="_blank" rel="tooltip" class="btn btn-warning">
                          <i class="material-icons" title="Prerequisitos">chrome_reader_mode</i>
                        </a>
                                            
                        <button type="button" class="btn" style="background-color:#3b83bd;color:#ffffff;" data-toggle="modal" data-target="#modalreProcesar" onclick="agregaformRP('<?=$datosX;?>')">
                          <i class="material-icons" title="Reprocesar Planilla">autorenew</i>                   
                        </button>                                                                            
                      
                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalCerrar" onclick="agregaformCP('<?=$datosX;?>')">
                          <i class="material-icons" title="Cerrar Planilla">assignment_returned</i>
                        </button>                        
                        <a href='<?=$urlPlanillaSueldoPersonalActualPDF;?>?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>' target="_blank" rel="tooltip" class="btn btn-danger">            
                          <i class="material-icons" title="Ver Planilla Sueldos PDF">remove_red_eye</i>
                        </a>                      
                        <label class="text-danger">|</label>
                        <?php 
                        $stmtAdmninTrib=$dbh->prepare("SELECT codigo,modified_at,modified_by from planillas_tributarias where cod_mes=$cod_mes and cod_gestion=$cod_gestion");
                           //ejecutamos
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
                          <a href='<?=$urlPlanillaTribPersonalPDF;?>?codigo_trib=<?=$codigoTrib;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>' target="_blank" rel="tooltip" class="btn btn-success">
                            <i class="material-icons" title="Ver Planilla Triburaria">remove_red_eye</i> PT                       
                          </a>
                           <?php
                        }
                        ?>
                        <?php }?>
                        <?php if($cod_estadoplanilla==3){    ?>  
                                             
                        <a href='<?=$urlPlanillaSueldoPersonalActualPDF;?>?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>' target="_blank" rel="tooltip" class="btn btn-danger">            
                          <i class="material-icons" title="Ver Planilla Sueldos PDF">remove_red_eye</i>                        
                        </a>
                        <a href='<?=$urlPlanillaTribPersonalPDF;?>?codigo_trib=<?=$codigoTrib;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>' target="_blank" rel="tooltip" class="btn btn-success">            
                            <i class="material-icons" title="Ver Planilla Triburaria">remove_red_eye</i> PT                       
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
                          </ul>
                        </div>                                                                 
                        <?php }?>                          
                      </td>
                      <td class="text-center">
                        <a href="<?=$urlPlanillaContabilizacion;?>?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>">
                          <i class="material-icons" title="Generar Comprobante" style="color:red">input</i>
                        </a>
                      </td>

                    </tr>
                  <?php $index++; } ?>
                </tbody>                                      
              </table>
          </div>
          <div class="card-footer fixed-bottom">
            <a href='<?=$urlGenerarPlanillaSueldoPrevia;?>' rel="tooltip" class="btn btn-success">
              Registrar Planilla Del Mes Actual
            </a>                                            
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
          <button type="button" class="btn btn-danger" id="CancelarProceso" data-dismiss="modal" >Cancelar</button>
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
          <button type="button" class="btn btn-danger" data-dismiss="modal" id="CancelarReProceso">Cancelar</button>
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
          <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
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
          <button type="button" class="btn btn-danger" data-dismiss="modal" id="CancelarReProcesoTrib">Cancelar</button>
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
  </script>
  <?php 
}else
{ //para personal no admin
  $stmt = $dbh->prepare("SELECT codigo,cod_gestion,cod_mes,cod_estadoplanilla,
  (select m.nombre from meses m where m.codigo=cod_mes)as mes,
  (select g.nombre from gestiones g where g.codigo=cod_gestion) as gestion,
  (select ep.nombre from estados_planilla ep where ep.codigo=cod_estadoplanilla) as estadoplanilla
  from planillas");
  //ejecutamos
  $stmt->execute();
  //bindColumn
  $stmt->bindColumn('codigo', $codigo_planilla);
  $stmt->bindColumn('gestion', $gestion);
  $stmt->bindColumn('cod_gestion', $cod_gestion);
  $stmt->bindColumn('cod_mes', $cod_mes);
  $stmt->bindColumn('mes', $mes);
  $stmt->bindColumn('cod_estadoplanilla', $cod_estadoplanilla);
  $stmt->bindColumn('estadoplanilla', $estadoplanilla);

  $stmtAdmninUO = $dbh->prepare("SELECT cod_uo,(select uo.abreviatura from unidades_organizacionales uo where uo.codigo=cod_uo) as nombre_uo from personal_area_distribucion where cod_estadoreferencial=1
  GROUP BY cod_uo");
  $stmtAdmninUO->execute();
  $stmtAdmninUO->bindColumn('cod_uo', $cod_uo_x);
  $stmtAdmninUO->bindColumn('nombre_uo', $nombre_uo_x);
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
            <h4 class="card-title"><center>OFICINA: <?=$globalNombreUnidad;?></center></h4>            
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
                      
                    </tr>
                </thead>
                <tbody>
                  <?php $index=1;
                  // $datos="";
                  // $cont= array();
                  $datosX="";
                  while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                    $datosX =$codigo_planilla."-".$globalCodUnidad;
                    $sw_aux=false;
                    $label_uo_aux='';
                    if($cod_estadoplanilla==1){
                      $label='<span class="badge badge-danger">';

                    }
                    if($cod_estadoplanilla==2){                      
                        $stmtAdmninUOAux2 = $dbh->prepare("SELECT cod_uo
                           from planillas_uo_cerrados where cod_planilla=$codigo_planilla and cod_uo=$globalCodUnidad");
                        $stmtAdmninUOAux2->execute();
                        $resultAdmninUOAux2=$stmtAdmninUOAux2->fetch();
                        $cod_uo_aux2=$resultAdmninUOAux2['cod_uo'];                    
                        if($globalCodUnidad==$cod_uo_aux2){
                          $label_uo_aux.='<span class="badge badge-success">CERRADO</span>';
                          $estadoplanilla='';
                          $sw_aux=true;
                          $label='';
                        }else{
                          $label_uo_aux.='<span class="badge badge-warning">ABIERTO</span>';
                          $label='<span class="badge badge-warning">';
                          $estadoplanilla='';
                        }
                      
                    }
                    if($cod_estadoplanilla==3){
                      $label='<span class="badge badge-success">';
                    }                  
                    ?>
                    <tr>                    
                      <td><?=$gestion?></td>
                      <td><?=$mes;?></td>                  
                      <td><?=$label.$estadoplanilla."</span>";?><?=$label_uo_aux;?></td>                    
                      
                      <td class="td-actions text-right">
                        <?php

                        
                                                                                                 
                        if($cod_estadoplanilla==2){ if($sw_aux==false){    ?>
                        <a href='<?=$urlDetallePlanillaPrerequisitos;?>?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>' target="_blank" rel="tooltip" class="btn btn-warning">
                          <i class="material-icons" title="Prerequisitos Suedos">chrome_reader_mode</i>
                        </a>
                                            
                        <button type="button" class="btn" style="background-color:#3b83bd;color:#ffffff;" data-toggle="modal" data-target="#modalreProcesarNA" onclick="agregaformRPNA('<?=$datosX;?>')">
                          <i class="material-icons" title="Reprocesar Planilla Sueldos">autorenew</i>                        
                        </button>                                                                              
                      
                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalCerrarNA" onclick="agregaformCPNA('<?=$datosX;?>')">
                          <i class="material-icons" title="Cerrar Planilla Sueldos">assignment_returned</i>
                        </button>                        
                        <a href='<?=$urlPlanillaSueldoPersonalActualPDF;?>?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>' target="_blank" rel="tooltip" class="btn btn-danger">            
                          <i class="material-icons" title="Ver Planilla Sueldos PDF">remove_red_eye</i>                 
                        </a>                      
                        
                        <?php }else{?>
                        <a href='<?=$urlPlanillaSueldoPersonalActualPDF;?>?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>' target="_blank" rel="tooltip" class="btn btn-danger">            
                          <i class="material-icons" title="Ver Planilla Sueldos PDF">remove_red_eye</i>                 
                        </a>                      
                        
                        <?php

                        }

                        }?>
                        <?php if($cod_estadoplanilla==3){    ?>
                        <!-- <a href='<?=$urlPlanillaSueldoPersonalActual;?>?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>' target="_blank" rel="tooltip" class="btn btn-success">            
                          <i class="material-icons" title="Ver Planilla">remove_red_eye</i>                        
                        </a> -->
                        <a href='<?=$urlPlanillaSueldoPersonalActualPDF;?>?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>' target="_blank" rel="tooltip" class="btn btn-danger">  
                          <i class="material-icons" title="Ver Planilla sueldos PDF">remove_red_eye</i>               
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
                          </ul>
                        </div>                                                                 
                        <?php }?>                          
                      </td>
              

                    </tr>
                  <?php $index++; } ?>
                </tbody>                                      
              </table>
          </div>
           
        </div>
      </div>
    </div>
  </div>
  <!--modal procesar-->
  <!-- <div class="modal fade" id="modalProcesarNA" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">¿Estás Seguro?</h4>
        </div>
        <div class="modal-body">
          <input type="hidden" name="codigo_planillaNA" id="codigo_planillaNA" value="0">
          <input type="hidden" name="codigo_uoNA" id="codigo_uoNA" value="0"> 
          Esta acción Procesará La planilla Del Mes En Curso. ¿Deseas Continuar?
          <div id="cargaPNA" style="display:none">
            <h3><b>Por favor espere...</b></h3>
          </div>
        </div>       
        <div class="modal-footer">
          <button type="button" class="btn btn-success" id="AceptarProcesoNA">Aceptar</button>
          <button type="button" class="btn btn-danger" id="CancelarProcesoNA" data-dismiss="modal" >Cancelar</button>
        </div>
      </div>
    </div>
  </div> -->
  <!--modal Reprocesar-->
  <div class="modal fade" id="modalreProcesarNA" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">¿Estás Seguro?</h4>
        </div>
        <div class="modal-body">
          <input type="hidden" name="codigo_planillaRPNA" id="codigo_planillaRPNA" value="0">        
          <input type="hidden" name="codigo_uoRPNA" id="codigo_uoRPNA" value="0">        
          Esta acción ReProcesará La planilla Del Mes En Curso. ¿Deseas Continuar?
          <div id="cargaRNA" style="display:none">
            <h3><b>Por favor espere...</b></h3>
          </div>
        </div>    
        <div class="modal-footer">
          <button type="button" class="btn btn-success" id="AceptarReProcesoNA" >Aceptar</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal" id="CancelarReProcesoNA">Cancelar</button>
        </div>
      </div>
    </div>
  </div>
  <!--modal Cerrra-->
  <div class="modal fade" id="modalCerrarNA" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">¿Estás Seguro?</h4>
        </div>
        <div class="modal-body">
          <input type="hidden" name="codigo_planillaCPNA" id="codigo_planillaCPNA" value="0">        
          <input type="hidden" name="codigo_uoCPNA" id="codigo_uoCPNA" value="0">        
          Esta acción Cerrará La planilla Del Mes En Curso. ¿Deseas Continuar?
        </div>       
        <div class="modal-footer">
          <button type="button" class="btn btn-success" id="AceptarCerrarNA" data-dismiss="modal">Aceptar</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </div>
  </div>
  <!--Modal planilla tributarua-->
  <div class="modal fade" id="modalreProcesarPlanillaTributariaNA" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
          <button type="button" class="btn btn-danger" data-dismiss="modal" id="CancelarReProcesoTrib">Cancelar</button>
        </div>
      </div>
    </div>
  </div>
  <script type="text/javascript">
    $(document).ready(function(){
      $('#AceptarProcesoNA').click(function(){      
        var cod_planilla=document.getElementById("codigo_planillaNA").value;
        var cod_uo=document.getElementById("codigo_uoNA").value;            
        ProcesarPlanillaNA(cod_planilla,cod_uo);
      });
      $('#AceptarReProcesoNA').click(function(){      
        cod_planilla=document.getElementById("codigo_planillaRPNA").value; 
        cod_uo=document.getElementById("codigo_uoRPNA").value;      
        ReprocesarPlanillaNA(cod_planilla,cod_uo);
      });
      $('#AceptarCerrarNA').click(function(){      
        cod_planilla=document.getElementById("codigo_planillaCPNA").value;
        cod_uo=document.getElementById("codigo_uoCPNA").value;      

        CerrarPlanillaNA(cod_planilla,cod_uo);
      });
      
    });
  </script>
  <?php 
}
?>
