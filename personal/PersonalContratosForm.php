<?php

require_once 'conexion.php';
require_once 'rrhh/configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];

$cod_personal=$codigo;
$dbh = new Conexion();


$stmtPersonal = $dbh->prepare("SELECT * from personal where codigo=:codigo");
$stmtPersonal->bindParam(':codigo',$cod_personal);
//ejecutamos
$stmtPersonal->execute();
$result=$stmtPersonal->fetch();
$cod_personal_1=$result['codigo'];
$ci=$result['identificacion'];
$primer_nombre=$result['primer_nombre'];
$paterno=$result['paterno'];
$materno=$result['materno'];
//SELECT
$stmt = $dbh->prepare("SELECT *,DATE_FORMAT(fecha_iniciocontrato,'%d/%m/%Y')as fecha_iniciocontrato_x,DATE_FORMAT(fecha_fincontrato,'%d/%m/%Y')as fecha_fincontrato_x,DATE_FORMAT(fecha_evaluacioncontrato,'%d/%m/%Y')as fecha_evaluacioncontrato_x,DATE_FORMAT(fecha_finalizado,'%d/%m/%Y')as fecha_finalizado_x,
  (select ec.nombre from estados_contrato ec where ec.codigo=cod_estadocontrato)as estado_contrato,
(select c.nombre from tipos_contrato_personal c where c.codigo=cod_tipocontrato)as nombre_contrato,
(select ct.duracion_meses from tipos_contrato_personal ct where ct.codigo=cod_tipocontrato)as meses_contrato
from personal_contratos 
where cod_personal=:codigo and cod_estadoreferencial=1
ORDER BY 1" );
$stmt->bindParam(':codigo',$cod_personal);
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $codigo_contrato);
$stmt->bindColumn('cod_tipocontrato', $cod_tipocontrato);
$stmt->bindColumn('fecha_iniciocontrato_x', $fecha_iniciocontrato);
$stmt->bindColumn('fecha_iniciocontrato', $fecha_iniciocontrato_x);
$stmt->bindColumn('fecha_fincontrato_x', $fecha_fincontrato);
$stmt->bindColumn('fecha_fincontrato', $fecha_fincontrato_x);
$stmt->bindColumn('fecha_evaluacioncontrato_x', $fecha_evaluacioncontrato);
$stmt->bindColumn('fecha_evaluacioncontrato', $fecha_evaluacioncontrato_x);
$stmt->bindColumn('cod_estadocontrato', $cod_estadocontrato);
$stmt->bindColumn('estado_contrato', $estado_contrato);
$stmt->bindColumn('fecha_finalizado_x', $fecha_finalizado);
$stmt->bindColumn('nombre_contrato', $nombre_contrato);
$stmt->bindColumn('meses_contrato', $meses_contrato);
$stmt->bindColumn('cod_finiquito', $cod_finiquito);




//listado de contratos
$query_contrato = "SELECT * from tipos_contrato_personal where cod_estadoreferencial=1 order by 1";
$statementTiposContrato = $dbh->query($query_contrato);
//listado de editar areas
$query_contratoE = "SELECT * from tipos_contrato_personal where cod_estadoreferencial=1 order by 1";
$statementTiposContratoE = $dbh->query($query_contratoE);
//listado de retiro personal
$query_retiro = "SELECT * from tipos_retiro_personal where cod_estadoreferencial=1 order by 2";
$statementTiposRetiro = $dbh->query($query_retiro);

$fecha_actual=date("d/m/Y");
?>

<div class="content">
	<div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons"><?=$iconCard;?></i>
                  </div>
                  <h4 class="card-title">Contratos Del Personal</h4>
                  <h4 class="card-title"><small>Fecha Actual: <?=$fecha_actual?><br>Personal: <?=$paterno." ".$materno." ".$primer_nombre;?><br>Identificación: <?=$ci;?></small></h4>                  
                  
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-bordered table-condensed" id="tablePaginatorFixed">
                      <thead>
                        <tr class="bg-dark text-white">
                        	<th>#</th>                          
              						<th>Tipo Contrato</th>
                          <th>Duración(mes)</th>
              						<th>F. Ini. Contrato</th>
                          <th>F. Fin. Contrato</th>
                          <th>F. Revisión</th>
                          <th>Estado</th>
                          <th>F.Cerrado</th>
              						<th></th>                                                   
                        </tr>
                      </thead>
                      <tbody>
                        <?php $index=1;                      
                        $datos=$cod_personal_1;
                        $sw_control_retiros=0;
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                          if($cod_estadocontrato==1){//si hay algun abierto marcamos
                            $sw_control_retiros=1;
                          }
                        	$datos=$cod_personal_1."/".$codigo_contrato."/".$fecha_iniciocontrato_x."/".$fecha_evaluacioncontrato_x."/".$fecha_fincontrato_x."/".$cod_tipocontrato;
                          $cod_defecto_contrato_otros=obtenerValorConfiguracion(79);
                          $cod_contradoidefinido=obtenerValorConfiguracion(80);
                          if($cod_tipocontrato==$cod_defecto_contrato_otros){//este tipo de contrato no tiene definido cantidad de meses
                            $datetime1=new DateTime($fecha_iniciocontrato_x);
                            $datetime2=new DateTime($fecha_fincontrato_x);
                            # obtenemos la diferencia entre las dos fechas
                            $interval=$datetime2->diff($datetime1);
                            # obtenemos la diferencia en meses
                            $intervalMeses=$interval->format("%m");
                            # obtenemos la diferencia en años y la multiplicamos por 12 para tener los meses
                            $intervalAnos = $interval->format("%y")*12;
                            $meses_contrato=$intervalMeses+$intervalAnos;
                            // echo "hay una diferencia de ".($intervalMeses+$intervalAnos)." meses";                            
                          }
                        	?>
                            <tr>
                                <td><?=$index;?></td>                                
                                <td><?=$nombre_contrato;?></td>
                                <td><?=$meses_contrato;?></td>
                                <td><?=$fecha_iniciocontrato;?></td>
                                <?php
                                  $porcionesActual = explode("/", $fecha_actual);
                                  $anioActual= $porcionesActual[2]; // porción1
                                  $mesActual= $porcionesActual[1]; // porción2                                  
                                  $diaActual= $porcionesActual[0]; // porción2 
                                  // $cadena = "uno,dos,tres,cuatro,cinco";
                                  // $array = explode(",", $fecha_fincontrato);
                                  if($fecha_fincontrato_x=="INDEFINIDO"){
                                    $fecha_fincontrato=$fecha_fincontrato_x;
                                    $label='<span class="badge badge-success">';
                                    //fecha evaluacion
                                    $porcionesEvaluacion = explode("/", $fecha_evaluacioncontrato);
                                    $anioEvaluacion= $porcionesEvaluacion[2]; // porción1
                                    $mesEvaluacion= $porcionesEvaluacion[1]; // porción2 
                                    $diaEvaluacion= $porcionesEvaluacion[0]; // porción2
                                    if($anioActual==$anioEvaluacion){
                                      if($mesActual-$mesEvaluacion==-1){
                                        $label='<span class="badge badge-warning">';
                                      }elseif ($mesActual-$mesEvaluacion==0) {
                                        if ($diaActual<$diaEvaluacion) {
                                          $labelEvaluacion='<span class="badge badge-warning">';
                                        }else{
                                          $labelEvaluacion='<span class="badge badge-danger">';
                                        }
                                      }elseif ($mesActual-$mesEvaluacion>0) {
                                        $labelEvaluacion='<span class="badge badge-danger">';
                                      }else{
                                        $labelEvaluacion='<span class="badge badge-success">';
                                      }
                                    }elseif($anioActual<$anioEvaluacion){
                                      $labelEvaluacion='<span class="badge badge-success">';
                                    }else{
                                      $labelEvaluacion='<span class="badge badge-danger">';
                                    }

                                  }else{
                                    $porcionesFin = explode("/", $fecha_fincontrato);
                                    $anioFin= $porcionesFin[2]; // porción1
                                    $mesFin= $porcionesFin[1]; // porción2 
                                    $diaFin= $porcionesFin[0]; // porción2
                                    if($anioActual==$anioFin){
                                      if($mesActual-$mesFin==-1){
                                        $label='<span class="badge badge-warning">';
                                      }elseif ($mesActual-$mesFin==0) {
                                        if ($diaActual<$diaFin) {
                                          $label='<span class="badge badge-warning">';
                                        }else{
                                          $label='<span class="badge badge-danger">';
                                        }
                                      }elseif ($mesActual-$mesFin>0) {
                                        $label='<span class="badge badge-danger">';
                                      }else{
                                        $label='<span class="badge badge-success">';
                                      }
                                    }elseif($anioActual<$anioFin){
                                      $label='<span class="badge badge-success">';
                                    }else{
                                      $label='<span class="badge badge-danger">';
                                    }

                                    //para la evaluacion de contrato
                                    $porcionesEvaluacion = explode("/", $fecha_evaluacioncontrato);
                                    $anioEvaluacion= $porcionesEvaluacion[2]; // porción1
                                    $mesEvaluacion= $porcionesEvaluacion[1]; // porción2 
                                    $diaEvaluacion= $porcionesEvaluacion[0]; // porción2
                                    if($anioActual==$anioEvaluacion){
                                      if($mesActual-$mesEvaluacion==-1){
                                        $label='<span class="badge badge-warning">';
                                      }elseif ($mesActual-$mesEvaluacion==0) {
                                        if ($diaActual<$diaEvaluacion) {
                                          $labelEvaluacion='<span class="badge badge-warning">';
                                        }else{
                                          $labelEvaluacion='<span class="badge badge-danger">';
                                        }
                                      }elseif ($mesActual-$mesEvaluacion>0) {
                                        $labelEvaluacion='<span class="badge badge-danger">';
                                      }else{
                                        $labelEvaluacion='<span class="badge badge-success">';
                                      }
                                    }elseif($anioActual<$anioEvaluacion){
                                      $labelEvaluacion='<span class="badge badge-success">';
                                    }else{
                                      $labelEvaluacion='<span class="badge badge-danger">';
                                    }
                                  }
                                  if($cod_estadocontrato==1){
                                      $labelEstado='<span class="badge badge-success">';
                                    }else{
                                      $labelEstado='<span class="badge badge-danger">';
                                      $label='<span class="badge badge-danger">';
                                    }                                                                    
                                ?>
                                <td><?=$label.$fecha_fincontrato."</span>";?></td>
                                <td class="td-actions text-left"><?=$labelEvaluacion.$fecha_evaluacioncontrato."</span>";?>
                                <?php if($cod_estadocontrato==1){?>
                                  <button type="button" style="background-color: #ffffff;border: none;" data-toggle="modal" data-target="#modalEditarEva" onclick="agregaformEditEva('<?=$datos;?>')">
                                    <i class="material-icons" style="color:#464f55" title="Editar Fecha Revisión">notifications</i>
                                  </button>
                                <?php }?>
                                  
                                </td>
                                <td><?=$labelEstado.$estado_contrato."</span>";?></td>
                                <td><?=$fecha_finalizado;?></td>
                                <td class="td-actions text-right">
                                  <?php
                                  if($cod_estadocontrato==1){ ?>
                                  	<button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modalContratoFin" onclick="agregaContratoFin('<?=$datos;?>')">
                                      <i class="material-icons" title="Finalizar Contrato">play_for_work</i>
                                    </button>
                                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalEditar" onclick="agregaformPCE('<?=$datos;?>')">
                                  		<i class="material-icons" title="Editar"><?=$iconEdit;?></i>
                                    </button>
                                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modalEliminar" onclick="agregaformPCB('<?=$datos;?>')">
                                      <i class="material-icons" title="Eliminar"><?=$iconDelete;?></i>
                                    </button><?php 
                                  }else{
                                    if($cod_finiquito==null && $cod_tipocontrato!=$cod_contradoidefinido){?>
                                      <button class="btn btn-primary" onClick="location.href='<?=$urlFormFiniquitos;?>&codigo=0&codigo_contrato=<?=$codigo_contrato?>'"><i class="material-icons" title="Registrar Finiquito">play_for_work</i></button>
                                    <?php }elseif($cod_finiquito==null && $cod_tipocontrato==$cod_contradoidefinido){?>
                                      <button type="button" class="btn btn-primary btn-round" data-toggle="modal" data-target="#modalRetirarPersonal" onclick="agregaformRetiroPersonal('<?=$datos;?>',2)">
                                        <i class="material-icons" title="Retirar Personal y Registrar Finiquito">play_for_work</i>
                                     </button>
                                    <?php }
                                  }?>

                                </td>
                            </tr>
                        <?php $index++; } ?>            				
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <div class="card-footer fixed-bottom">
                <?php
                if($globalAdmin==1){
                ?>            
                  <button type="button" class="btn btn-warning btn-round btn-fab" data-toggle="modal" data-target="#modalAgregarC" onclick="agregaformPC('<?=$datos;?>')">
                      <i class="material-icons" title="Agregar Contrato">add</i>
  		             </button>
                   <?php
                   if($sw_control_retiros==0){?>
                     <button type="button" class="btn btn-primary btn-round btn-fab" data-toggle="modal" data-target="#modalRetirarPersonal" onclick="agregaformRetiroPersonal('<?=$datos;?>',1)">
                      <i class="material-icons" title="Retirar Personal">play_for_work</i>
                   </button>                             
                   <?php } ?>
                  
                <?php
                }
                ?>
                <a href="<?=$urlListPersonal;?>" class="btn btn-danger btn-round btn-fab">
                  <i class="material-icons" title="Volver">keyboard_return</i>
                </a> 
              </div>
            </div>
          </div>  
        </div>
    </div>


<!-- Modal agregar contrato-->
<div class="modal fade" id="modalAgregarC" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Agregar Contrato a Personal</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="codigo_personalA" id="codigo_personalA" value="0">
        <div class="row">
          <label class="col-sm-2 col-form-label" style="color:#424242">Tipo Contrato</label>
          <div class="col-sm-8">
            <div class="form-group">
                <select name="cod_tipocontratoA" id="cod_tipocontratoA" class="selectpicker form-control form-control-sm" data-style="btn btn-primary" required data-show-subtext="true" data-live-search="true" onChange="ajax_tiposContratoPersonal(this,1);">
                  <?php while ($row = $statementTiposContrato->fetch()){ ?>
                      <option value="<?=$row["codigo"];?>"><?=$row["nombre"];?> - <?=$row["duracion_meses"];?> meses</option>
                  <?php } ?>
                </select>
            </div>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-2 col-form-label" style="color:#424242"> Fecha Inicio</label>
          <div class="col-sm-8">
            <div class="form-group">
              <input class="form-control" type="date" name="fecha_inicioA" id="fecha_inicioA"/>
            </div>
          </div>
        </div>
        <div  id="div_contenedor_fecha_fin">          
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="registrarPC" name="registrarPC" data-dismiss="modal">Guardar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"> <i class="material-icons" title="Volver">keyboard_return</i> Volver </button>
      </div>
    </div>
  </div>
</div>
<!--eliminar contrato-->
<div class="modal fade" id="modalEliminar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Esta acción eliminará El contrato.</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="codigo_personalB" id="codigo_personalB" value="0">
        <input type="hidden" name="codigo_contratoB" id="codigo_contratoB" value="0">
        <P align="center">
             <b>POR FAVOR, VERIFIQUE QUE NO ESTÉ VIGENTE (Finalice Contrato).</b><br> ¿Deseas Continuar?
        </P>
        
      </div>       
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="EliminarPC" data-dismiss="modal">Aceptar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"> <-- Volver </button>
      </div>
    </div>
  </div>
</div>
<!-- Editar contrato-->
<div class="modal fade" id="modalEditar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Editar Contrato Personal </h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="codigo_personalE" id="codigo_personalE" value="0">
        <input type="hidden" name="codigo_contratoE" id="codigo_contratoE" value="0">
        <div class="row">
          <label class="col-sm-2 col-form-label" style="color:#424242">Tipo Contrato</label>
          <div class="col-sm-8">
            <div class="form-group">
                <select name="cod_tipocontratoE" id="cod_tipocontratoE" class="selectpicker form-control form-control-sm" data-style="btn btn-primary" required data-show-subtext="true" data-live-search="true" onChange="ajax_tiposContratoPersonal(this,2);">
                  <?php while ($row = $statementTiposContratoE->fetch()){ ?>
                      <option <?=($cod_tipocontrato==$row["codigo"])?"selected":"";?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?> - <?=$row["duracion_meses"];?> meses</option>
                  <?php } ?>
                </select>
            </div>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-2 col-form-label" style="color:#424242"> Fecha Inicio</label>
          <div class="col-sm-8">
            <div class="form-group">
              <input class="form-control" type="date" name="fecha_inicioE" id="fecha_inicioE"/>
            </div>
          </div>
        </div>
        <div  id="div_contenedor_fecha_fin_e">          
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="EditarPC"  data-dismiss="modal">Guardar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="material-icons" title="Volver">keyboard_return</i> Volver </button>
      </div>
    </div>
  </div>
</div>
<!-- Editar evaluacion-->
<div class="modal fade" id="modalEditarEva" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Editar Fecha de Evaluación</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="codigo_personalEv" id="codigo_personalEv" value="0">
        <input type="hidden" name="codigo_contratoEv" id="codigo_contratoEv" value="0">                
        <h6> Fecha Evaluación : </h6>
        <input class="form-control" type="date" name="fecha_EvaluacionEv" id="fecha_EvaluacionEv" />
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="EditarEva"  data-dismiss="modal">Aceptar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"> <-- Volver </button>
      </div>
    </div>
  </div>
</div>
<!-- finalizar contrato-->
<div class="modal fade" id="modalContratoFin" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Finalizar Contrato de Personal </h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="cod_personalCF" id="cod_personalCF" value="0">
        <input type="hidden" name="cod_contratoCf" id="cod_contratoCf" value="0">        
        Esta acción Finalizará El contrato Del Personal. ¿Deseas Continuar?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="Finalizar_contrato"  data-dismiss="modal">Aceptar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"> <-- Volver </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal retirar personal-->
<div class="modal fade" id="modalRetirarPersonal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Retirar Personal</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="codigo_personalR" id="codigo_personalR" value="0">
        <input type="hidden" name="codigo_contratoR" id="codigo_contratoR" value="0">
        <input type="hidden" name="indiceR" id="indiceR" value="0">
        <div class="row">
          <label class="col-sm-3 col-form-label" style="color:#424242">Tipo De Retiro : </label>
          <div class="col-sm-8">
            <div class="form-group">              
                <select name="cod_tiporetiro" id="cod_tiporetiro" class="selectpicker form-control form-control-sm" data-style="btn btn-primary" required data-show-subtext="true" data-live-search="true">
                <?php while ($row = $statementTiposRetiro->fetch()){ ?>
                    <option value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                <?php } ?>
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-3 col-form-label" style="color:#424242"> Fecha De Retiro : </label>
          <div class="col-sm-8">
            <div class="form-group">
              <input class="form-control" type="date" name="fecha_retiro" id="fecha_retiro" required="true"/>
            </div>
          </div>
        </div>
         <div class="row">
          <label class="col-sm-3 col-form-label" style="color:#424242"> Observación : </label>
          <div class="col-sm-8">
            <div class="form-group">
              <input class="form-control" type="text" name="observaciones" id="observaciones" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
            </div>
          </div>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="registraRetiro" name="registraRetiro" data-dismiss="modal">Aceptar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"> <-- Volver </button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    $('#registrarPC').click(function(){    
      cod_personalA=document.getElementById("codigo_personalA").value;
      cod_tipocontratoA=$('#cod_tipocontratoA').val();
      fecha_inicioA=$('#fecha_inicioA').val();
      fecha_finA=$('#fecha_finA').val();
      RegistrarContratoPersonal(cod_personalA,cod_tipocontratoA,fecha_inicioA,fecha_finA);
    });
    $('#EditarPC').click(function(){
      codigo_contratoE=document.getElementById("codigo_contratoE").value;
      codigo_personalE=document.getElementById("codigo_personalE").value;
      cod_tipocontratoE=$('#cod_tipocontratoE').val();
      fecha_inicioE=$('#fecha_inicioE').val();
      fecha_finE=$('#fecha_finE').val();
      EditarContratoPersonal(codigo_contratoE,codigo_personalE,cod_tipocontratoE,fecha_inicioE,fecha_finE);
    });
    $('#EditarEva').click(function(){
      codigo_contratoEv=document.getElementById("codigo_contratoEv").value;
      codigo_personalEv=document.getElementById("codigo_personalEv").value;      
      fecha_EvaluacionEv=$('#fecha_EvaluacionEv').val();
      EditarEvaluacionPersonal(codigo_contratoEv,codigo_personalEv,fecha_EvaluacionEv);
    });
    $('#EliminarPC').click(function(){    
      codigo_contratoB=document.getElementById("codigo_contratoB").value; 
      codigo_personalB=document.getElementById("codigo_personalB").value;
      EliminarContratoPersonal(codigo_contratoB,codigo_personalB);      
    });
    $('#registraRetiro').click(function(){    
      cod_personal=document.getElementById("codigo_personalR").value;
      codigo_contratoR=document.getElementById("codigo_contratoR").value;
      indiceR=document.getElementById("indiceR").value;
      cod_tiporetiro=$('#cod_tiporetiro').val();
      fecha_Retiro=$('#fecha_retiro').val();
      observaciones=$('#observaciones').val();
      RetirarPersonal(cod_personal,cod_tiporetiro,fecha_Retiro,observaciones,codigo_contratoR,indiceR);
    });

    $('#Finalizar_contrato').click(function(){
      codigo_contratoCF=document.getElementById("cod_contratoCf").value;
      codigo_personalCf=document.getElementById("cod_personalCF").value;      
      FinalizarContratoPersonal(codigo_contratoCF,codigo_personalCf);
    });
    
    

  });
</script>