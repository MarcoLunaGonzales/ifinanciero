<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'functionsGeneral.php';
require_once 'rrhh/configModule.php';

$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();


  $stmt = $dbh->prepare("SELECT codigo,cod_personal,fecha_ingreso,fecha_retiro,
(select CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=cod_personal) as nombre_personal,
(Select t.nombre from tipos_retiro_personal t where t.codigo=cod_tiporetiro) as motivo_retiro from finiquitos where cod_estadoreferencial=1");
  //ejecutamos
  $stmt->execute();
  //bindColumn
  $stmt->bindColumn('codigo', $codigo);
  $stmt->bindColumn('cod_personal', $cod_personal);
  $stmt->bindColumn('nombre_personal', $nombre_personal);
  $stmt->bindColumn('fecha_ingreso', $fecha_ingreso);
  $stmt->bindColumn('fecha_retiro', $fecha_retiro);
  $stmt->bindColumn('motivo_retiro', $motivo_retiro);
  ?>
  <div class="content">
    <div class="container-fluid">
      <div class="col-md-12">     
        <div class="card">
          <div class="card-header <?=$colorCard;?> card-header-text">
            <div class="card-icon">
              <i class="material-icons"><?=$iconCard;?></i>
            </div>
            <h4 class="card-title">Finiquitos</h4>
          </div>
          <div class="card-body ">
              <table class="table" id="tablePaginator">
                <thead>
                    <tr>                    
                      <th>#</th>
                      <th>Cód. Personal</th>      
                      <th>Nombre Personal</th>      
                      <th>Fecha Ingreso</th>                      
                      <th>Fecha Retiro</th>                      
                      <th>Motivo Retiro</th> 
                      <th></th>                                         
                      
                    </tr>
                </thead>
                <tbody>
                  <?php $index=1;
                  // $datos="";
                  // $cont= array();
                  $datosX="";
                  while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                    ?>
                    
                    <tr>                    
                      
                      <td><?=$index?></td>
                      <td><?=$cod_personal;?></td>                      
                      <td><?=strtoupper($nombre_personal);?></td>
                      <td><?=$fecha_ingreso;?></td>
                      <td><?=$fecha_retiro;?></td>
                      <td><?=$motivo_retiro;?></td>
                      <td class="td-actions text-right">
                        <?php
                          if($globalAdmin==1){
                        ?>
                          
                          <a href='<?=$urlprintFiniquitos;?>?codigo=<?=$codigo;?>' target="_blank" rel="tooltip" class="<?=$buttonEdit;?>">
                            <i class="material-icons" title="Imprimir">print</i>
                          </a>
                          <a href='<?=$urlFormFiniquitos;?>&codigo=<?=$codigo;?>' rel="tooltip" class="<?=$buttonEdit;?>">
                            <i class="material-icons" title="Editar"><?=$iconEdit;?></i>
                          </a>
                          <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDeleteFiniquito;?>&codigo=<?=$codigo;?>')">
                            <i class="material-icons" title="Borrar"><?=$iconDelete;?></i>
                          </button>
                          <?php
                            }
                          ?>
                        
                        </td>

                    </tr>
                  <?php $index++; } ?>
                </tbody>                                      
              </table>
          </div>
          <?php

              if($globalAdmin==1){
              ?>
              <div class="card-footer fixed-bottom">
                    <!--<button class="<?=$buttonNormal;?>" onClick="location.href='index.php?opcion=registerUbicacion'">Registrar</button>-->
                    <button class="<?=$buttonNormal;?>" onClick="location.href='<?=$urlFormFiniquitos;?>&codigo=0'">Registrar</button>
              </div>
              <?php
              }
              ?>
           
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
          <button type="button" class="btn btn-danger" id="CancelarProcesoNA" data-dismiss="modal" > <-- Volver </button>
        </div>
      </div>
    </div>
  </div> -->
  <!--modal Reprocesar-->
 <!--  <div class="modal fade" id="modalreProcesarNA" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
          <button type="button" class="btn btn-danger" data-dismiss="modal" id="CancelarReProcesoNA"> <-- Volver </button>
        </div>
      </div>
    </div>
  </div> -->
  <!--modal Cerrra-->
  <!-- <div class="modal fade" id="modalCerrarNA" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
          <button type="button" class="btn btn-danger" data-dismiss="modal"> <-- Volver </button>
        </div>
      </div>
    </div>
  </div> -->
  <!-- <script type="text/javascript">
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
  </script> -->

