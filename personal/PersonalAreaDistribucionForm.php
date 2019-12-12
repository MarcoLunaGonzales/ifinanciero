<?php

require_once 'conexion.php';
require_once 'rrhh/configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();

//SELECT
$stmt = $dbh->prepare("SELECT *,(select p.primer_nombre from personal p where p.codigo=cod_personal) as nombre_personal,
(select p.paterno from personal p where p.codigo=cod_personal) as paterno_personal,
(select a.nombre from areas a where a.codigo=cod_area) as nombre_area
from personal_area_distribucion
where cod_personal=:codigo");
$stmt->bindParam(':codigo',$codigo);
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('cod_personal', $cod_personal);
$stmt->bindColumn('cod_area', $cod_area);
$stmt->bindColumn('porcentaje', $porcentaje);
$stmt->bindColumn('nombre_personal', $nombre_personal);
$stmt->bindColumn('paterno_personal', $paterno_personal);
$stmt->bindColumn('nombre_area', $nombre_area);

//listado para area registro de distribucion
$query_areas = "select * from areas order by 2";
$statementAREAS = $dbh->query($query_areas);
//listado para area Edicion de distribucion
$query_areas = "select * from areas order by 2";
$statementAREASE = $dbh->query($query_areas);

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
                    <table class="table table-bordered table-condensed" id="tablePaginatorFixed">

                    <thead>
                      <tr class="bg-dark text-white">
                      	<th>Codigo</th>
                      	<th>Personal</th>
						<th>Area</th>
						<th>Porcentaje</th>
						<th></th>                                                   
                      </tr>
                  </thead>
                  <tbody>
                  <?php $index=1;
                  $sumPorcentaje=0;
                  while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { 
                  	$sumPorcentaje=$sumPorcentaje+$porcentaje;
                  	$datos =$cod_personal."-".$codigo."-".$cod_area."-".$porcentaje;
                  	?>

                      <tr>
                          <td><?=$codigo;?></td>
                          <td><?=$nombre_personal." ".$paterno_personal;?></td>
                          <td><?=$nombre_area;?></td>
                          <td><?=$porcentaje;?></td>
                          <td>
                          	<button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalEditar" onclick="agregaformPADE('<?=$datos;?>')">
                          		<i class="material-icons" title="Editar"><?=$iconEdit;?></i>                             
                            </button>
                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modalEliminar" onclick="agregaformPADB('<?=$datos;?>')">
                              <i class="material-icons" title="Eliminar"><?=$iconDelete;?></i>
                            </button>
                          </td>
                      </tr>
                  <?php $index++; } ?>

					<tr class="bg-info text-white">
						<th colspan="2">Total :</th>
						<td class="text-center small">-</td>
						<?php
						if($sumPorcentaje!=100){
								$stringClass="class='text-center small bg-danger text-white'";
								$stringLabel="CORREGIR PORCENTAJE!!!  :(";
							}else{
								$stringClass="class='text-center small bg-success text-white'";
								$stringLabel="PORCENTAJE CORRECTO!!!  :)";
							}

							?>
						<td <?=$stringClass?>><?=formatNumberDec($sumPorcentaje); ?></td>
						<td <?=$stringClass?>><?=$stringLabel?></td>
						
					</tr>
                  </tbody>
                    
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
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Rechazar Activo Fijo</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="codigo_personal" id="codigo_personal" value="0">
        <input type="hidden" name="codigo_distribucion" id="codigo_distribucion" value="0">
        
        <h6> Area : </h6>
        <select name="cod_area" id="cod_area" class="selectpicker" data-style="btn btn-primary" >            
            <?php while ($row = $statementAREAS->fetch()){ ?>
                <option value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
            <?php } ?>
        </select>       
        <h6> Porcentaje : </h6>
        <input type="number" name="porcentaje" id="porcentaje" class="form-control input-sm">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="aceptarPAD"  data-dismiss="modal">Aceptar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>
<!--eliminar Distribucion-->
<div class="modal fade" id="modalEliminar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-sm" role="document">
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
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>
<!-- Editar Distribucion-->
<div class="modal fade" id="modalEditar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Rechazar Activo Fijo</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="codigo_personalE" id="codigo_personalE" value="0">
        <input type="hidden" name="codigo_distribucionE" id="codigo_distribucionE" value="0">     
        <h6> Area : </h6>
        <select name="cod_areaE" id="cod_areaE" class="selectpicker" data-style="btn btn-primary" >            
            <?php while ($row = $statementAREASE->fetch()){ ?>
                <option value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
            <?php } ?>
        </select>       
        <h6> Porcentaje : </h6><br>
        <input type="number" name="porcentajeE" id="porcentajeE" class="form-control input-sm">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="EditarPAD"  data-dismiss="modal">Aceptar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    $('#aceptarPAD').click(function(){    
      cod_personal=document.getElementById("codigo_personal").value;
      cod_area=$('#cod_area').val();
      porcentaje=$('#porcentaje').val();
      RegistrarDistribucion(cod_personal,cod_area,porcentaje);
    }); 
    $('#EliminarPAD').click(function(){    
      cod_distribucion=document.getElementById("codigo_distribucionB").value;
      cod_personal=document.getElementById("codigo_personalB").value;   
      EliminarDistribucion(cod_personal,cod_distribucion);
    }); 
    $('#EditarPAD').click(function(){    
      cod_distribucion=document.getElementById("codigo_distribucionE").value;
      cod_personal=document.getElementById("codigo_personalE").value;
      cod_area=$('#cod_areaE').val();
      porcentaje=$('#porcentajeE').val();
      EditarDistribucion(cod_personal,cod_distribucion,cod_area,porcentaje);
    });  

  });
</script>