<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'functionsGeneral.php';
require_once 'rrhh/configModule.php';


$dbh = new Conexion();

$stmt = $dbh->prepare("SELECT *,(select m.nombre from meses m where m.codigo=cod_mes)as mes,(select g.nombre from gestiones g where g.codigo=cod_gestion) as gestion,
  (select ep.nombre from estados_planilla ep where ep.codigo=cod_estadoplanilla) as estadoplanilla
 from planillas
 ");
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $codigo_planilla);
$stmt->bindColumn('gestion', $cod_gestion);
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
				  <h4 class="card-title">Planillas Anteriores De Sueldos</h4>				
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
                  </tr>
              </thead>
              <tbody>
                <?php $index=1;
                $datos="";
                $cont= array();
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


                  //descuentos
                  $dbh1 = new Conexion();
                  $sql2="SELECT count(codigo) as cantidad_descuentos from planillas_personal_mes where total_descuentos>0";
                  $stmt2 = $dbh1->prepare($sql2);
                  $stmt2->execute(); 
                  $nc=0;
                  $result2= $stmt2->fetch();
                  $cantidad_descuentosX=trim($result2['cantidad_descuentos']);

                  //bonos
                  $sql3="SELECT count(codigo) as cantidad_bonos from planillas_personal_mes where monto_bonos>0";
                  $stmt3 = $dbh1->prepare($sql3);
                  $stmt3->execute();     
                  $result3= $stmt3->fetch();
                  $cantidad_bonosX=trim($result3['cantidad_bonos']);                                            
                  //atrasos
                  $sql4="SELECT count(codigo) as cantidad_atrasos from planillas_personal_mes_detalle where atrasos>0";
                  $stmt4 = $dbh1->prepare($sql4);
                  $stmt4->execute();
                  $result4= $stmt4->fetch();
                  $cantidad_atrasosX=trim($result4['cantidad_atrasos']);
                  //anticipos
                  $sql5="SELECT count(codigo) as cantidad_anticipos from planillas_personal_mes_detalle where anticipo>0";
                  $stmt5 = $dbh1->prepare($sql5);
                  $stmt5->execute(); 
                  $result5= $stmt5->fetch();
                  $cantidad_anticiposX=trim($result5['cantidad_anticipos']);

                  $dato = new stdClass();//obejto

                  $dato->codigo=($nc+1);
                  $dato->descuentos=$cantidad_descuentosX;
                  $dato->bonos=$cantidad_bonosX;
                  $dato->atrasos=$cantidad_atrasosX;
                  $dato->anticipos=$cantidad_anticiposX;
                  
                  $datos[$index-1][$nc]=$dato;              

                  $nc++;
                  $cont[$index-1]=$nc; 
                  ?>
                  <tr>                    
                    <td><?=$cod_gestion?></td>
                    <td><?=$mes;?></td>                  
                    <td><?=$label.$estadoplanilla."</span>";?></td>                    
                    
                    <td class="td-actions text-right">
                      <?php
                      if($cod_estadoplanilla==1){    ?>
                      <a href='#' rel="tooltip" class="btn btn-dark" onclick="filaTablaGeneralPlanillasSueldo($('#card_prerequisitos'),<?=$index?>)">
                        <i class="material-icons" title="Prerequisitos">chrome_reader_mode</i>
                      </a>
                                          
                      <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modalProcesar" onclick="agregaformPre('<?=$datosX;?>')">
                        <i class="material-icons" title="Procesar Planilla">perm_data_setting</i>
                      </button>
                      <a href='<?=$urlPlanillaSueldoPersonalActual;?>?codigo_planilla=<?=$codigo_planilla;?>' target="_blank" rel="tooltip" class="btn btn-success">            
                        <i class="material-icons" title="Ver Planilla">remove_red_eye</i>                        
                      </a>
                      <?php }                                                                          
                      if($cod_estadoplanilla==2){    ?>
                      <a href='<?=$urlPlanillaSueldoPersonalActual;?>?codigo_planilla=<?=$codigo_planilla;?>' target="_blank" rel="tooltip" class="btn btn-success">            
                        <i class="material-icons" title="Ver Planilla">remove_red_eye</i>                        
                      </a>                            
                    
                      <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modalCerrar" onclick="agregaformPre('<?=$datosX;?>')">
                        <i class="material-icons" title="Cerrar Planilla">assignment_returned</i>
                      </button>
                      <?php }?>
                      <?php if($cod_estadoplanilla==3){    ?>
                      <a href='<?=$urlPlanillaSueldoPersonalActual;?>?codigo_planilla=<?=$codigo_planilla;?>' target="_blank" rel="tooltip" class="btn btn-success">            
                        <i class="material-icons" title="Ver Planilla">remove_red_eye</i>                        
                      </a>                                                                    
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
            Generar Planilla Previa Mes Actual
          </a>                                            
        </div>  
      </div>
    </div>
	</div>
</div>


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
  require_once 'planillas/modal.php';
?>



<!--modal-->
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
      </div>       
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="AceptarProceso" data-dismiss="modal">Aceptar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>
<!--modal-->
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
    $('#AceptarCerrar').click(function(){      
      cod_planilla=document.getElementById("codigo_planilla").value;      

      CerrarPlanilla(cod_planilla);
    });

  });
</script>