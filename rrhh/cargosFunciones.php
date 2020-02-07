<?php

require_once 'conexion.php';
require_once 'rrhh/configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];

$cod_cargo=$codigo;
$dbh = new Conexion();


$stmtPersonal = $dbh->prepare("SELECT nombre from cargos where codigo=$cod_cargo");
//ejecutamos
$stmtPersonal->execute();
$result=$stmtPersonal->fetch();
$nombre_cargo=$result['nombre'];
//SELECT
$stmt = $dbh->prepare("SELECT *
from cargos_funciones
where cod_estadoreferencial=1 and cod_cargo=$cod_cargo
ORDER BY nombre_funcion" );
$stmt->bindParam(':codigo',$cod_personal);
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $cod_cargo_funcion);
$stmt->bindColumn('nombre_funcion', $nombre_funcion);
$stmt->bindColumn('peso', $peso);

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
                  <h4 class="card-title">Funciones Cargo</h4>
                  <h4 class="card-title" align="center"><?=$nombre_cargo;?></h4>                  
                  
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-bordered table-condensed" id="tablePaginatorFixed">
                      <thead>
                        <tr class="bg-dark text-white">
                        	<th>#</th>                          
              						<th>Nombre</th>
                          <th>Peso</th>
              						<th></th>                                                   
                        </tr>
                      </thead>
                      <tbody>
                        <?php $index=1;                      
                        $datos=$cod_cargo;
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {                       	
                        	$datos=$cod_cargo."/".$cod_cargo_funcion."/".$nombre_funcion."/".$peso;
                        	?>
                            <tr>
                                <td><?=$index;?></td>
                                <td><?=$nombre_funcion;?></td>
                                <td><?=$peso;?></td>
                                
                                <td class="td-actions text-right">
                                  <?php
                                    if($globalAdmin==1){
                                  ?>                                	
                                  <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalEditar" onclick="agregaFuncionCargoE('<?=$datos;?>')">
                                		<i class="material-icons" title="Editar"><?=$iconEdit;?></i>
                                  </button>
                                  <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modalEliminar" onclick="agregaFuncionCargoB('<?=$datos;?>')">
                                    <i class="material-icons" title="Eliminar"><?=$iconDelete;?></i>
                                  </button>
                                <?php } ?>
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
                  <button type="button" class="btn btn-warning btn-round btn-fab" data-toggle="modal" data-target="#modalAgregarFuncionCargo" onclick="agregaFuncionCargo('<?=$datos;?>')">
                      <i class="material-icons" title="Agregar Contrato">add</i>
  		             </button>                                            
                <?php
                }
                ?>
                <a href="<?=$urlListCargos;?>" class="btn btn-danger btn-round btn-fab">
                  <i class="material-icons" title="Volver">keyboard_return</i>
                </a> 
              </div>
            </div>
          </div>  
        </div>
    </div>


<!-- Modal agregar -->
<div class="modal fade" id="modalAgregarFuncionCargo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Registrar Función a Cargo</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="cod_cargoA" id="cod_cargoA" value="0">                      
        <h6> Nombre Función </h6>
        <input class="form-control" type="text" name="nombre_funcionA" id="nombre_funcionA" onkeyup="javascript:this.value=this.value.toUpperCase();" required="true" />

        <h6> Peso </h6>
        <input class="form-control" type="number" name="pesoA" id="pesoA" required="true" />
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="registrarFC" name="registrarPC" data-dismiss="modal">Aceptar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="material-icons" title="Volver">keyboard_return</i> Volver </button>
      </div>
    </div>
  </div>
</div>

<!-- Editar -->
<div class="modal fade" id="modalEditar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Editar Función a Cargo </h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="cod_cargo_funcionE" id="cod_cargo_funcionE" value="0">
        <input type="hidden" name="cod_cargoE" id="cod_cargoE" value="0">        
        <h6> Nombre Función </h6>
        <input class="form-control" type="text" name="nombre_funcionE" id="nombre_funcionE" onkeyup="javascript:this.value=this.value.toUpperCase();" required="true" />

        <h6> Peso </h6>
        <input class="form-control" type="number" name="pesoE" id="pesoE" required="true" />
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="EditarFC"  data-dismiss="modal">Aceptar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="material-icons" title="Volver">keyboard_return</i> Volver </button>
      </div>
    </div>
  </div>
</div>
<!-- finalizar contrato-->
<div class="modal fade" id="modalEliminar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Eliminar Función Del Cargo</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="cod_cargo_funcionB" id="cod_cargo_funcionB" value="0">
        <input type="hidden" name="cod_cargoB" id="cod_cargoB" value="0">               
        Esta acción Eliminará La función del cargo. ¿Deseas Continuar?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="EliminarFC"  data-dismiss="modal">Aceptar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="material-icons" title="Volver">keyboard_return</i> Volver </button>
      </div>
    </div>
  </div>
</div>


<script type="text/javascript">
  $(document).ready(function(){
    $('#registrarFC').click(function(){    
      cod_cargoA=document.getElementById("cod_cargoA").value;
      nombre_funcionA=$('#nombre_funcionA').val();
      pesoA=$('#pesoA').val();
      RegistrarCargoFuncion(cod_cargoA,nombre_funcionA,pesoA);
    });
    $('#EditarFC').click(function(){
      cod_cargoE=document.getElementById("cod_cargoE").value;
      cod_cargo_funcionE=document.getElementById("cod_cargo_funcionE").value;
      nombre_funcionE=$('#nombre_funcionE').val();
      pesoE=$('#pesoE').val();
      EditarCargoFuncion(cod_cargoE,cod_cargo_funcionE,nombre_funcionE,pesoE);
    });
    $('#EliminarFC').click(function(){    
      cod_cargoB=document.getElementById("cod_cargoB").value;
      cod_cargo_funcionB=document.getElementById("cod_cargo_funcionB").value;
      EliminarCargoFuncion(cod_cargoB,cod_cargo_funcionB);    
    });
  });
</script>