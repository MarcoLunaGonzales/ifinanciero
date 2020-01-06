<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'functionsGeneral.php';
require_once 'rrhh/configModule.php';


$dbh = new Conexion();

$stmt = $dbh->prepare("SELECT *,(select m.nombre from meses m where m.codigo=cod_mes)as mes,
  (select g.nombre from gestiones g where g.codigo=cod_gestion) as gestion,
  (select ep.nombre from estados_planilla ep where ep.codigo=cod_estadoplanilla) as estadoplanilla
 from planillas
 ");
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


$stmtUO = $dbh->prepare("SELECT cod_uo from personal_area_distribucion where cod_estadoreferencial=1
GROUP BY cod_uo");
$stmtUO->execute();
$stmtUO->bindColumn('cod_uo', $cod_uo_x);
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
          <div class="table-responsive">
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
           
                      <button type="button" class="btn" style="background-color:#3b83bd;color:#ffffff;" data-toggle="modal" data-target="#modalProcesar" onclick="agregaformPre('<?=$datosX;?>')">
                        <i class="material-icons" title="Procesar Planilla">perm_data_setting</i>
                      </button>                      
                      <?php }                                                                          
                      if($cod_estadoplanilla==2){    ?>
                      <a href='<?=$urlDetallePlanillaPrerequisitos;?>?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>' target="_blank" rel="tooltip" class="btn btn-warning">
                        <i class="material-icons" title="Prerequisitos">chrome_reader_mode</i>
                      </a>
                                          
                      <button type="button" class="btn" style="background-color:#3b83bd;color:#ffffff;" data-toggle="modal" data-target="#modalreProcesar" onclick="agregaformPre('<?=$datosX;?>')">
                        <i class="material-icons" title="Reprocesar Planilla">autorenew</i>                        
                      </button>                                                                              
                    
                      <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modalCerrar" onclick="agregaformPre('<?=$datosX;?>')">
                        <i class="material-icons" title="Cerrar Planilla">assignment_returned</i>
                      </button>
                      <a href='<?=$urlPlanillaSueldoPersonalActual;?>?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>' target="_blank" rel="tooltip" class="btn btn-success">            
                        <i class="material-icons" title="Ver Planilla">remove_red_eye</i>                        
                      </a>
                      <?php }?>
                      <?php if($cod_estadoplanilla==3){    ?>
                      <a href='<?=$urlPlanillaSueldoPersonalActual;?>?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>' target="_blank" rel="tooltip" class="btn btn-success">            
                        <i class="material-icons" title="Ver Planilla">remove_red_eye</i>                        
                      </a>
                                                                              
                      <?php }?>                                                                
                    </td>

                    <td class="td-actions text-right">
                      <?php                      
                      if($cod_estadoplanilla==2){    ?>                                                                                                                                 
                      <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" id="reporte_sueldos" data-toggle="dropdown" aria-extended="true">
                          <i class="material-icons" title="Ver Planilla por UO">remove_red_eye</i>                        
                          <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu" aria-labelledby="reporte_sueldos">
                          <li role="presentation" class="dropdown-header"><small>U.O.</small></li>
                          <?php
                            while ($row = $stmtUO->fetch(PDO::FETCH_BOUND)) {
                              if($cod_uo_x>0){?>                                                                
                                  <li role="presentation"><a role="item" href="<?=$urlPlanillaSueldoPersonalReporte;?>?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>&codigo_uo=<?=$cod_uo_x;?>" target="_blank"><small><?=$cod_uo_x;?></small></a></li>
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
                          <i class="material-icons" title="Ver Planilla por UO">remove_red_eye</i>                        
                          <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu" aria-labelledby="reporte_sueldos">
                          <li role="presentation" class="dropdown-header"><small>U.O.</small></li>
                          <?php
                            while ($row = $stmtUO->fetch(PDO::FETCH_BOUND)) {
                              if($cod_uo_x>0){?>                                                                
                                  <li role="presentation"><a role="item" href="<?=$urlPlanillaSueldoPersonalReporte;?>?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&cod_mes=<?=$cod_mes;?>&codigo_uo=<?=$cod_uo_x;?>" target="_blank"><small><?=$cod_uo_x;?></small></a></li>
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
        <div class="card-footer fixed-bottom">
          <a href='<?=$urlGenerarPlanillaSueldoPrevia;?>' rel="tooltip" class="btn btn-success">
            Registrar Planilla Del Mes Actual
          </a>                                            
        </div>  
      </div>
    </div>
	</div>
</div>
<!-- 
<?php 
  $lan=sizeof($cont);
  for ($i=0; $i < $lan; $i++) {?>
    <script>var planilla_sueldo=[];</script><?php
      for ($j=0; $j < $cont[$i]; $j++)
      { 
        if($cont[$i]>0)
        {?>          
          <script>planilla_sueldo.push({codigo:<?=$datos[$i][$j]->codigo?>,descuentos:'<?=$datos[$i][$j]->descuentos?>',bonos:'<?=$datos[$i][$j]->bonos?>',atrasos:'<?=$datos[$i][$j]->atrasos?>',anticipos:'<?=$datos[$i][$j]->anticipos?>'});</script><?php         
        }          
      }
      ?><script>planillas_tabla_general.push(planilla_sueldo);</script><?php                    
  }
  //require_once 'planillas/modal.php';
?> -->



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
        <input type="hidden" name="codigo_planilla" id="codigo_planilla" value="0">        
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
        <input type="hidden" name="codigo_planilla" id="codigo_planilla" value="0">        
        Esta acción Cerrará La planilla Del Mes En Curso. ¿Deseas Continuar?
      </div>       
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="AceptarCerrar" data-dismiss="modal">Aceptar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  $(document).ready(function(){
    $('#AceptarProceso').click(function(){      
      cod_planilla=document.getElementById("codigo_planilla").value;      

      ProcesarPlanilla(cod_planilla);
    });
    $('#AceptarReProceso').click(function(){      
      cod_planilla=document.getElementById("codigo_planilla").value;      

      ReprocesarPlanilla(cod_planilla);
    });
    $('#AceptarCerrar').click(function(){      
      cod_planilla=document.getElementById("codigo_planilla").value;      

      CerrarPlanilla(cod_planilla);
    });

  });
</script>