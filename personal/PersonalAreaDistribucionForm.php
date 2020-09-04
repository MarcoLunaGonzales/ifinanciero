<?php

require_once 'conexion.php';
require_once 'rrhh/configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();
$cod_personal_1=$codigo;
//SELECT
$stmt = $dbh->prepare("SELECT *,
(select uo.nombre from unidades_organizacionales uo where uo.codigo=cod_uo) as nombre_uo,
(select a.nombre from areas a where a.codigo=cod_area) as nombre_area
from personal_area_distribucion
where cod_estadoreferencial=1 and cod_personal=:codigo");

$stmt->bindParam(':codigo',$codigo);
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('cod_area', $cod_area);
$stmt->bindColumn('cod_uo', $cod_uo);
$stmt->bindColumn('porcentaje', $porcentaje);
$stmt->bindColumn('monto', $monto_sueldo);
$stmt->bindColumn('nombre_uo', $nombre_uo);
$stmt->bindColumn('nombre_area', $nombre_area);


$stmtPersonal = $dbh->prepare("SELECT * from personal where codigo=:codigo");
$stmtPersonal->bindParam(':codigo',$cod_personal_1);
//ejecutamos
$stmtPersonal->execute();
$result=$stmtPersonal->fetch();
$cod_personal=$result['codigo'];
$ci=$result['identificacion'];
$nombre_personal=$result['primer_nombre'];
$paterno_personal=$result['paterno'];
$materno_personal=$result['materno'];
$haber_basico=$result['haber_basico'];

//listado para area registro de distribucion
// $query_areas = "SELECT * from areas where cod_estado=1 order by 2";
// $statementAREAS = $dbh->query($query_areas);


$query_uo = "SELECT * from unidades_organizacionales where cod_estado=1 order by 2";
$statementUO = $dbh->query($query_uo);
$query_uoE = "SELECT * from unidades_organizacionales where cod_estado=1 order by 2";
$statementUOE = $dbh->query($query_uoE);
//listado para area Edicion de distribucion
$query_areas = "SELECT * from areas where cod_estado=1 order by 2";
$stmtAreaR = $dbh->query($query_areas);

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
                  <h4 class="card-title">Personal Area Distribución</h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <input type="hidden" name="haber_basico" id="haber_basico" value="<?=$haber_basico?>">
                    <table class="table table-bordered table-condensed" id="tablePaginatorFixed">
                      <thead>
                        <tr class="bg-dark text-white">
                        	<th>Codigo</th>
                        	<th>Personal</th>
                          <th>Oficina</th>
              						<th>Area</th>
              						<th>Porcentaje</th>
                          <th>Monto</th>
  					             	<th></th>                                                   
                        </tr>
                      </thead>
                      <tbody>
                        <?php 
                        $index=1;
                        $sumPorcentaje=0;
                        $datos =$cod_personal;
                        $sumHAberBasico=0;
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { 
                          // $monto_sueldo=
                        	$sumPorcentaje=$sumPorcentaje+$porcentaje;
                          $sumHAberBasico+=$monto_sueldo;
                        	$datos =$cod_personal."-".$codigo."-".$cod_uo."-".$cod_area."-".$porcentaje."-".$monto_sueldo;
                        	?>
                            <tr>
                                <td><?=$codigo;?></td>
                                <td><?=$paterno_personal." ".$nombre_personal;?></td>
                                <td><?=$nombre_uo;?></td>
                                <td><?=$nombre_area;?></td>
                                <td><?=$porcentaje;?></td>
                                <td><?=formatNumberDec($monto_sueldo);?></td>
                                <td class="td-actions text-right">
                                	<button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalEditar" onclick="agregaformPADE('<?=$datos;?>')">
                                		<i class="material-icons" title="Editar"><?=$iconEdit;?></i>                             
                                  </button>
                                  <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modalEliminar" onclick="agregaformPADB('<?=$datos;?>')">
                                    <i class="material-icons" title="Eliminar"><?=$iconDelete;?></i>
                                  </button>
                                </td>
                            </tr>
                          <?php $index++; } ?>
                        </tbody>
                        <tfoot>
                          <tr class="bg-info text-white">
                            <th colspan="2">Total :</th>
                            <td class="text-center small">-</td>
                            <td class="text-center small">-</td>
                            <?php
                            $stringClass="";
                            $stringLabel="";
                            if($sumPorcentaje==100){
                                $stringClass="class='text-center small bg-success text-white'";
                                $stringLabel="PORCENTAJE CORRECTO!!!  :)";
                            }else{
                                $stringClass="class='text-center small bg-danger text-white'";
                                $stringLabel="CORREGIR PORCENTAJE!!!  :(";
                            }

                            ?>
                            <td <?=$stringClass?> ><?=formatNumberDec($sumPorcentaje); ?></td>
                            <td <?=$stringClass?> ><?=formatNumberDec($sumHAberBasico); ?></td>
                            <td <?=$stringClass?> ><?=$stringLabel?></td>                           
                          </tr>

                        </tfoot>
                    
                    </table>
                  </div>
                </div>
              </div>
              <?php

              if($globalAdmin==1){
              ?>
      			   <div class="card-footer fixed-bottom">
                  <button type="button" class="btn btn-warning btn-round btn-fab" data-toggle="modal" data-target="#modalAgregarD" onclick="agregaformPAD('<?=$datos;?>')">
                      		  <i class="material-icons" title="Agregar">add</i>
		             </button>
                <a href="<?=$urlListPersonal;?>" class="btn btn-danger btn-round btn-fab">
                  <i class="material-icons" title="Retornar">keyboard_return</i>
                </a>                 
              </div>
              <?php
              }
              ?>
		  
            </div>
          </div>  
        </div>
    </div>


<!-- Modal agregar distrbucion-->
<div class="modal fade" id="modalAgregarD" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Agregar Area Distribución</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="codigo_personal" id="codigo_personal" value="0">
        <input type="hidden" name="codigo_distribucion" id="codigo_distribucion" value="0">
        
        <div class="row">
          <label class="col-sm-2 col-form-label" style="color:#424242">Oficina :</label>
          <div class="col-sm-8">
            <div class="form-group">
              <select name="cod_uo" id="cod_uo" class="selectpicker form-control form-control-sm" data-style="btn btn-primary" onChange="ajaxPersonal_area_distribucion(this);" required data-show-subtext="true" data-live-search="true">
                  <?php while ($row = $statementUO->fetch()){ ?>
                      <option <?=($cod_uo==$row["codigo"])?"selected":"";?> value="<?=$row["codigo"];?>" data-subtext="<?=$row["codigo"];?>"><?=$row["abreviatura"];?> - <?=$row["nombre"];?></option>
                  <?php } ?>
              </select>
            </div>
          </div>
        </div>

        <div class="row">
          <label class="col-sm-2 col-form-label" style="color:#424242">Area :</label>
          <div class="col-sm-8">
            <div class="form-group">
              <div id="div_contenedor_area">
                
                <select name="cod_area" id="cod_area" data-style="btn btn-primary" class="selectpicker form-control form-control-sm" required data-show-subtext="true" data-live-search="true">
                  <option></option>
                  <?php 
                    while ($row = $stmtAreaR->fetch()){ ?>
                       <option value="<?=$row["codigo"];?>" data-subtext="<?=$row["codigo"];?>"><?=$row["abreviatura"];?> - <?=$row["nombre"];?></option>
                   <?php 
                  } 
                ?>
               </select>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-2 col-form-label" style="color:#424242">Porcentaje :</label>
          <div class="col-sm-8">
            <div class="form-group">
              <input type="number" step="any" name="porcentaje" id="porcentaje" class="form-control input-sm" onkeyup="convertir_sueldo_bob(1);">
            </div>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-2 col-form-label" style="color:#424242">Haber Básico :</label>
          <div class="col-sm-8">
            <div class="form-group">
              <input type="number" step="any" name="haber_basico_r" id="haber_basico_r" class="form-control input-sm" onkeyup="convertir_sueldo_por(1)">
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="aceptarPAD"  data-dismiss="modal">Aceptar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"> <-- Volver </button>
      </div>
    </div>
  </div>
</div>
<!--eliminar Distribucion-->
<div class="modal fade" id="modalEliminar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">¿Estás Seguro?</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="codigo_personalB" id="codigo_personalB" value="0">
        <input type="hidden" name="codigo_distribucionB" id="codigo_distribucionB" value="0">       
        Esta acción eliminará la distribución. ¿Deseas Continuar?
      </div>       
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="eliminarPAD" data-dismiss="modal">Aceptar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"> <-- Volver </button>
      </div>
    </div>
  </div>
</div>
<!-- Editar Distribucion-->
<div class="modal fade" id="modalEditar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Editar Area Distribución</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="codigo_personalE" id="codigo_personalE" value="0">
        <input type="hidden" name="codigo_distribucionE" id="codigo_distribucionE" value="0">     
        <!-- <input type="hidden" name="cod_uoE" id="cod_uoE" value="0"> -->
        <div class="row">
          <label class="col-sm-2 col-form-label" style="color:#424242">Oficina :</label>
          <div class="col-sm-8">
            <div class="form-group">
              <div id="div_contenedor_uo_x">
                
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-2 col-form-label" style="color:#424242">Area :</label>
          <div class="col-sm-8">
            <div class="form-group">
              <div id="div_contenedor_areaE">
             
              </div>  
            </div>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-2 col-form-label" style="color:#424242">Porcentaje :</label>
          <div class="col-sm-8">
            <div class="form-group">
              <input type="number" step="any" name="porcentajeE" id="porcentajeE" class="form-control input-sm" onkeyup="convertir_sueldo_bob(2)">
            </div>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-2 col-form-label" style="color:#424242">Haber Básico :</label>
          <div class="col-sm-8">
            <div class="form-group">
              <input type="number" step="any" name="haber_basico_e" id="haber_basico_e" class="form-control input-sm" onkeyup="convertir_sueldo_por(2)">
            </div>
          </div>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="EditarPAD"  data-dismiss="modal">Aceptar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"> <-- Volver </button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    $('#aceptarPAD').click(function(){    
      cod_personal=document.getElementById("codigo_personal").value;
      cod_uo=$('#cod_uo').val();
      cod_area=$('#cod_area').val();
      porcentaje=$('#porcentaje').val();
      haber_basico_r=$('#haber_basico_r').val();
      RegistrarDistribucion(cod_uo,cod_personal,cod_area,porcentaje,haber_basico_r);
    }); 
    $('#eliminarPAD').click(function(){    
      cod_distribucion=document.getElementById("codigo_distribucionB").value;
      cod_personal=document.getElementById("codigo_personalB").value;   
      EliminarDistribucion(cod_personal,cod_distribucion);
    });
    $('#EditarPAD').click(function(){    
      cod_distribucion=document.getElementById("codigo_distribucionE").value;
      cod_personal=document.getElementById("codigo_personalE").value;
      haber_basico_e=document.getElementById("haber_basico_e").value;
      cod_uoE=$('#cod_uoE').val();
      cod_area=$('#cod_areaE').val();
      porcentaje=$('#porcentajeE').val();
      EditarDistribucion(cod_personal,cod_distribucion,cod_uoE,cod_area,porcentaje,haber_basico_e);
    });  

  });
</script>