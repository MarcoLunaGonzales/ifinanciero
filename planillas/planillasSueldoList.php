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
  $stmtAdmnin = $dbh->prepare("SELECT codigo,cod_gestion,cod_mes,cod_uo,cod_estadoplanilla,
  (select uo.abreviatura from unidades_organizacionales uo where uo.codigo=cod_uo) as nombre_uo_admin,
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
  $stmtAdmnin->bindColumn('cod_uo', $cod_uo);
  $stmtAdmnin->bindColumn('nombre_uo_admin', $nombre_uo_admin);
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
                      <th>Oficina</th>
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
                    if($cod_estadoplanilla==1){
                      $label='<span class="badge badge-warning">';
                    }
                    if($cod_estadoplanilla==2){
                      $label='<span class="badge badge-success">';
                    }
                    if($cod_estadoplanilla==3){
                      $label='<span class="badge badge-danger">';
                    }                  
                    ?>
                    <tr>                    
                      <td><?=$gestion?></td>
                      <td><?=$mes;?></td>              
                      <td><?=$nombre_uo_admin;?></td>
                      <td><?=$label.$estadoplanilla."</span>";?></td>                    
                      
                      <td class="td-actions text-right">
                        <?php
                        if($cod_estadoplanilla==1){    ?>
             
                        <!-- <button type="button" class="btn" style="background-color:#3b83bd;color:#ffffff;" data-toggle="modal" data-target="#modalProcesar" onclick="agregaformPre('<?=$datosX;?>')">
                          <i class="material-icons" title="Procesar Planilla">perm_data_setting</i>
                        </button>  -->                     
                        <?php }                                                                          
                        if($cod_estadoplanilla==2){    ?>
                        <!-- <a href='<?=$urlDetallePlanillaPrerequisitos;?>?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>' target="_blank" rel="tooltip" class="btn btn-warning">
                          <i class="material-icons" title="Prerequisitos">chrome_reader_mode</i>
                        </a> -->
                                            
                        <!-- <button type="button" class="btn" style="background-color:#3b83bd;color:#ffffff;" data-toggle="modal" data-target="#modalreProcesar" onclick="agregaformRP('<?=$datosX;?>')">
                          <i class="material-icons" title="Reprocesar Planilla">autorenew</i>                        
                        </button>   -->                                                                            
                      
                        <!-- <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modalCerrar" onclick="agregaformCP('<?=$datosX;?>')">
                          <i class="material-icons" title="Cerrar Planilla">assignment_returned</i>
                        </button> -->
                        <!--<a href='<?=$urlPlanillaSueldoPersonalActual;?>?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>' target="_blank" rel="tooltip" class="btn btn-success">            
                          <i class="material-icons" title="Ver Planilla">remove_red_eye</i>                        
                        </a>-->
                        <a role="item" href="<?=$urlPlanillaSueldoPersonalReporte;?>?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>&codigo_uo=<?=$cod_uo;?>" target="_blank" rel="tooltip" class="btn btn-success"><i class="material-icons" title="Ver Planilla Sueldos">remove_red_eye</i></a>
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
                        <a role="item" href="<?=$urlPlanillaSueldoPersonalReporte;?>?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>&codigo_uo=<?=$cod_uo;?>" target="_blank" rel="tooltip" class="btn btn-success"><i class="material-icons" title="Ver Planilla Sueldos">remove_red_eye</i></a>                      
                        <a href='<?=$urlPlanillaSueldoPersonalActualPDF;?>?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>' target="_blank" rel="tooltip" class="btn btn-danger">            
                          <i class="material-icons" title="Ver Planilla Sueldos PDF">remove_red_eye</i>                        
                        </a>
                        <a href='<?=$urlPlanillaTribPersonalPDF;?>?codigo_trib=<?=$codigoTrib;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>' target="_blank" rel="tooltip" class="btn btn-success">            
                            <i class="material-icons" title="Ver Planilla Triburaria">remove_red_eye</i> PT                       
                        </a>

                                                                                
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
          <!-- <div class="card-footer fixed-bottom">
            <a href='<?=$urlGenerarPlanillaSueldoPrevia;?>' rel="tooltip" class="btn btn-success">
              Registrar Planilla Del Mes Actual
            </a>                                            
          </div> -->  
        </div>
      </div>
    </div>
  </div>
  <!--modal procesar-->
  <!-- <div class="modal fade" id="modalProcesar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
  </div> -->
  <!--modal Reprocesar-->
  <!-- <div class="modal fade" id="modalreProcesar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
  </div> -->
  <!--modal Cerrra-->
  <!-- <div class="modal fade" id="modalCerrar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
  </div> -->
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
      // $('#AceptarProceso').click(function(){      
      //   var cod_planilla=document.getElementById("codigo_planilla").value;      
      //   ProcesarPlanilla(cod_planilla);
      // });
      // $('#AceptarReProceso').click(function(){      
      //   cod_planilla=document.getElementById("codigo_planillaRP").value;      

      //   ReprocesarPlanilla(cod_planilla);
      // });
      // $('#AceptarCerrar').click(function(){      
      //   cod_planilla=document.getElementById("codigo_planillaCP").value;      

      //   CerrarPlanilla(cod_planilla);
      // });
      
    });
  </script>
  <?php 
}else
{ //para personal no admin
  $stmt = $dbh->prepare("SELECT codigo,cod_gestion,cod_mes,cod_estadoplanilla,cod_uo,
    (select m.nombre from meses m where m.codigo=cod_mes)as mes,
    (select g.nombre from gestiones g where g.codigo=cod_gestion) as gestion,
    (select ep.nombre from estados_planilla ep where ep.codigo=cod_estadoplanilla) as estadoplanilla
   from planillas where cod_uo=$globalCodUnidad");
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
                      <!-- <th></th>  -->
                    </tr>
                </thead>
                <tbody>
                  <?php $index=1;
                  // $datos="";
                  // $cont= array();
                  $datosX="";
                  while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                    $datosX =$codigo_planilla."-";
                    if($cod_estadoplanilla==1){
                      $label='<span class="badge badge-warning">';
                    }
                    if($cod_estadoplanilla==2){
                      $label='<span class="badge badge-success">';
                    }
                    if($cod_estadoplanilla==3){
                      $label='<span class="badge badge-danger">';
                    }                  
                    ?>
                    <tr>                    
                      <td><?=$gestion?></td>
                      <td><?=$mes;?></td>                  
                      <td><?=$label.$estadoplanilla."</span>";?></td>                    
                      
                      <td class="td-actions text-right">
                        <?php
                        if($cod_estadoplanilla==1){    ?>
             
                        <button type="button" class="btn" style="background-color:#3b83bd;color:#ffffff;" data-toggle="modal" data-target="#modalProcesarNA" onclick="agregaformPreNA('<?=$datosX;?>')">
                          <i class="material-icons" title="Procesar Planilla Sueldos">perm_data_setting</i>
                        </button>                      
                        <?php }                                                                          
                        if($cod_estadoplanilla==2){    ?>
                        <a href='<?=$urlDetallePlanillaPrerequisitos;?>?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>' target="_blank" rel="tooltip" class="btn btn-warning">
                          <i class="material-icons" title="Prerequisitos Suedos">chrome_reader_mode</i>
                        </a>
                                            
                        <button type="button" class="btn" style="background-color:#3b83bd;color:#ffffff;" data-toggle="modal" data-target="#modalreProcesarNA" onclick="agregaformRPNA('<?=$datosX;?>')">
                          <i class="material-icons" title="Reprocesar Planilla Sueldos">autorenew</i>                        
                        </button>                                                                              
                      
                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modalCerrarNA" onclick="agregaformCPNA('<?=$datosX;?>')">
                          <i class="material-icons" title="Cerrar Planilla Sueldos">assignment_returned</i>
                        </button>
                        <a role="item" href="<?=$urlPlanillaSueldoPersonalReporte;?>?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>&codigo_uo=<?=$globalCodUnidad;?>" target="_blank" rel="tooltip" class="btn btn-success"><i class="material-icons" title="Ver Planilla Sueldos">remove_red_eye</i></a>
                        <a href='<?=$urlPlanillaSueldoPersonalActualPDF;?>?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>' target="_blank" rel="tooltip" class="btn btn-danger">            
                          <i class="material-icons" title="Ver Planilla Sueldos PDF">remove_red_eye</i>                        
                        </a>                      
                        <label class="text-danger">|</label>
                        <?php 
                        $stmtTrib=$dbh->prepare("SELECT codigo,modified_at,modified_by from planillas_tributarias where cod_mes=$cod_mes and cod_gestion=$cod_gestion");
                           //ejecutamos
                        $stmtTrib->execute();
                        $sinTrib=0;$codigoTrib=0;
                        setlocale(LC_TIME, "Spanish");
                        while ($rowTrib = $stmtTrib->fetch(PDO::FETCH_ASSOC)) {
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
                          <button type="button" class="btn btn-info"  data-toggle="modal" data-target="#modalreProcesarPlanillaTributariaNA" onclick="agregaformPreTrib('<?=$codigoTrib;?>','<?=$datosX;?>','<?=$mes?>','<?=$modified_at?>','<?=$modified_by?>')">
                            <i class="material-icons" title="Procesar Planilla Tributaria">perm_data_setting</i>  PT                      
                          </button> 
                           <?php
                        }else{
                           ?>
                          <button type="button" class="btn"  style="background-color:#3b83bd;color:#ffffff;" data-toggle="modal" data-target="#modalreProcesarPlanillaTributariaNA" onclick="agregaformPreTrib('<?=$codigoTrib;?>','<?=$datosX;?>','<?=$mes?>','<?=$modified_at?>','<?=$modified_by?>')">
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
                        <!-- <a href='<?=$urlPlanillaSueldoPersonalActual;?>?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>' target="_blank" rel="tooltip" class="btn btn-success">            
                          <i class="material-icons" title="Ver Planilla">remove_red_eye</i>                        
                        </a> -->
                        <a role="item" href="<?=$urlPlanillaSueldoPersonalReporte;?>?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>&codigo_uo=<?=$globalCodUnidad;?>" target="_blank" rel="tooltip" class="btn btn-success"><i class="material-icons" title="Ver Planilla Sueldos">remove_red_eye</i></a>
                        <a href='<?=$urlPlanillaSueldoPersonalActualPDF;?>?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>' target="_blank" rel="tooltip" class="btn btn-danger">            
                          <i class="material-icons" title="Ver Planilla sueldos PDF">remove_red_eye</i>                        
                        </a>
                        <a href='<?=$urlPlanillaTribPersonalPDF;?>?codigo_trib=<?=$codigoTrib;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>' target="_blank" rel="tooltip" class="btn btn-success">            
                            <i class="material-icons" title="Ver Planilla Triburaria">remove_red_eye</i> PT                       
                        </a>
                        <?php }?>                                                                
                      </td>

                      <!-- <td class="td-actions text-right">
                        <?php                      
                        if($cod_estadoplanilla==2){    ?>                                                                                                                                 
                        <div class="dropdown">
                          <button class="btn btn-primary dropdown-toggle" type="button" id="reporte_sueldos" data-toggle="dropdown" aria-extended="true">
                            <i class="material-icons" title="Ver Planilla sueldos">remove_red_eye</i>                        
                            <span class="caret"></span>
                          </button>
                          <ul class="dropdown-menu" role="menu" aria-labelledby="reporte_sueldos">
                            <li role="presentation" class="dropdown-header"><small>OFICINA</small></li>
                            <li role="presentation"><a role="item" href="<?=$urlPlanillaSueldoPersonalReporte;?>?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>&codigo_uo=<?=$globalCodUnidad;?>" target="_blank"><small><?=$globalNombreUnidad;?></small></a></li>
                                                            
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
                            <li role="presentation"><a role="item" href="<?=$urlPlanillaSueldoPersonalReporte;?>?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>&codigo_uo=<?=$globalCodUnidad;?>" target="_blank"><small><?=$globalNombreUnidad;?></small></a></li>                     
                          </ul>
                        </div>                                                                 
                        <?php }?>                          
                      </td> -->
              
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
  <div class="modal fade" id="modalProcesarNA" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">¿Estás Seguro?</h4>
        </div>
        <div class="modal-body">
          <input type="hidden" name="codigo_planillaNA" id="codigo_planillaNA" value="0">           
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
  </div>
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
        ProcesarPlanillaNA(cod_planilla);
      });
      $('#AceptarReProcesoNA').click(function(){      
        cod_planilla=document.getElementById("codigo_planillaRPNA").value;      
        ReprocesarPlanillaNA(cod_planilla);
      });
      $('#AceptarCerrarNA').click(function(){      
        cod_planilla=document.getElementById("codigo_planillaCPNA").value;      

        CerrarPlanillaNA(cod_planilla);
      });
      
    });
  </script>
  <?php 
}
?>
