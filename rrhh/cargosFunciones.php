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
where cod_estado=1 and cod_cargo=$cod_cargo
ORDER BY nombre_funcion" );
$stmt->bindParam(':codigo',$cod_personal);
$stmt->execute();
$stmt->bindColumn('cod_funcion', $cod_cargo_funcion);
$stmt->bindColumn('nombre_funcion', $nombre_funcion);
$stmt->bindColumn('orden', $orden);

?>

<div class="cargar-ajax d-none">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold" id="texto_ajax_titulo">Procesando Datos</h4>
     <p class="text-white">Aguard&aacute; un momento por favor</p>  
  </div>
</div>

<div class="content">
	<div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons"><?=$iconCard;?></i>
                  </div>
                  <h4 class="card-title">Responsabilidades del Cargo</h4>
                  <h4 class="card-title" align="center"><?=$nombre_cargo;?></h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table" id="tablePaginator">
                      <thead>
                        <tr>
                        	<th>#</th>
                          <th>Nombre</th>
                          <th class="texto-center">Orden</th>
              						<th></th>                                                   
                        </tr>
                      </thead>
                      <tbody>
                        <?php $index=1;                      
                        $datos=$cod_cargo;
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {                       	
                        	$datos=$cod_cargo."/".$cod_cargo_funcion."/".$nombre_funcion."/".$orden;
                        	?>
                            <tr>
                                <td><?=$index;?></td>
                                <td><?=$nombre_funcion;?></td>
                                <td class="text-center"><?=$orden;?></td>
                                
                                <td class="td-actions text-right">
                                  <?php
                                    if($globalAdmin==1){
                                  ?>
                                  <!-- <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalEditar" onclick="agregaFuncionCargoE('<?=$datos;?>')">
                                		<i class="material-icons" title="Editar responsabilidad"><?=$iconEdit;?></i>
                                  </button> -->
                                  <button type="button" class="btn btn-success editar" 
                                        data-codCargoE="<?=$cod_cargo;?>"
                                        data-codCargo_funcionE="<?=$cod_cargo_funcion;?>"
                                        data-nombreFuncionE="<?=$nombre_funcion;?>"
                                        data-ordenE="<?=$orden;?>">
                                		<i class="material-icons" title="Editar responsabilidad"><?=$iconEdit;?></i>
                                  </button>

                                  <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modalEliminar" onclick="agregaFuncionCargoB('<?=$datos;?>')">
                                    <i class="material-icons" title="Eliminar Responsabilidad"><?=$iconDelete;?></i>
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
                      <i class="material-icons" title="Agregar Responsabilidad">add</i>
  		             </button>                                            
                <?php
                }
                ?>
                <a href="<?=$urlListCargos;?>" class="btn btn-danger btn-round btn-fab">
                  <i class="material-icons" title="Volver">keyboard_return</i>
                </a> 
                <!-- Copiar Responsabilidades de OTRO cargo -->
                <button type="button" class="btn btn-success btn-round btn-fab" onclick="$('#modalCopyFunciones').modal('show')">
                  <i class="material-icons" title="Copiar Resp. de otro Cargo">keyboard</i>
                </button> 
                <!-- Mover Responsabilidades de OTRO cargo -->
                <button type="button" class="btn btn-primary btn-round btn-fab" onclick="$('#modalMoveFunciones').modal('show')">
                  <i class="material-icons" title="Mover Resp. a otro Cargo">keyboard</i>
                </button> 
              </div>
            </div>
          </div>  
        </div>
    </div>


<!-- Modal agregar -->
<div class="modal fade" id="modalAgregarFuncionCargo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Registrar Responsabilidad del Cargo</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="cod_cargoA" id="cod_cargoA" value="0">                      
        <h6> Responsabilidad </h6>
        <!-- <input class="form-control" type="text" name="nombre_funcionA" id="nombre_funcionA" onkeyup="javascript:this.value=this.value.toUpperCase();" required="true" /> -->
        <textarea rows="4" class="form-control" name="nombre_funcionA" id="nombre_funcionA" required="true"></textarea>

        <h6> Orden </h6>
        <input class="form-control" type="number" name="ordenA" id="ordenA" required="true" />
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
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Editar Responsabilidad del Cargo </h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="cod_cargo_funcionE" id="cod_cargo_funcionE" value="0">
        <input type="hidden" name="cod_cargoE" id="cod_cargoE" value="0">        
        <h6> Responsabilidad </h6>
        <textarea rows="4"  class="form-control" name="nombre_funcionE" id="nombre_funcionE" required="true">
        </textarea>

        <!-- <input class="form-control" type="text" name="nombre_funcionE" id="nombre_funcionE" onkeyup="javascript:this.value=this.value.toUpperCase();" required="true" /> -->

        <h6> Orden </h6>
        <input class="form-control" type="number" name="ordenE" id="ordenE" required="true" />
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
        <h4 class="modal-title" id="myModalLabel">Eliminar Responsabilidad Del Cargo</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="cod_cargo_funcionB" id="cod_cargo_funcionB" value="0">
        <input type="hidden" name="cod_cargoB" id="cod_cargoB" value="0">               
        Esta acción Eliminará La responsabilidad del cargo. ¿Deseas Continuar?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="EliminarFC"  data-dismiss="modal">Aceptar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="material-icons" title="Volver">keyboard_return</i> Volver </button>
      </div>
    </div>
  </div>
</div>
<!-- Modal Copiar Responsabilidades de Cargo -->
<div class="modal fade" id="modalCopyFunciones" tabindex="-1" role="dialog" aria-labelledby="myModalLabel3">
  <div class="modal-dialog modal-lg">
      <div class="modal-content">
          <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title text-primary font-weight-bold" id="myModalLabel3">Copiar Responsabilidades de Cargo de:</h4>
          </div>
          <div class="modal-body" id="modal-lista_documentos">
            <div class="row">
                <label class="col-sm-2 col-form-label">Cargo</label>
                <div class="col-sm-10">
                  <div class="form-group">
                      <select name="cod_cargo_copia" id="cod_cargo_copia" data-style="btn btn-info" required class="selectpicker form-control form-control-sm" required data-show-subtext="true" data-live-search="true">
                          <?php 
                            $sqlListaCargos="SELECT codigo,nombre,abreviatura,cod_tipo_cargo,
                            (select tc.nombre from tipos_cargos_personal tc where tc.codigo=cod_tipo_cargo)as nombre_tipo_cargo
                          from cargos where cod_estadoreferencial=1 order by nombre";
                            $stmtListaCargos=$dbh->query($sqlListaCargos);
                            while ($row = $stmtListaCargos->fetch()) { ?>
                              <option value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                          <?php } ?>
                      </select>
                  </div>
                </div>
                <div class="col-md-12 text-center">
                  <button type="button" class="btn btn-success" id="cargo_copia_responsabilidad">Guardar</button>
                </div>
            </div>
          
          </div>
      </div>
  </div>
</div>
<!-- Modal Mover Responsabilidades de Cargo -->
<div class="modal fade" id="modalMoveFunciones" tabindex="-1" role="dialog" aria-labelledby="myModalLabel3">
  <div class="modal-dialog modal-lg">
      <div class="modal-content">
          <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title text-primary font-weight-bold" id="myModalLabel3">Mover Responsabilidades de Cargo a:</h4>
          </div>
          <div class="modal-body" id="modal-lista_documentos">
            <div class="row">
                <label class="col-sm-2 col-form-label">Cargo</label>
                <div class="col-sm-10">
                  <div class="form-group">
                      <select name="cod_cargo_mover" id="cod_cargo_mover" data-style="btn btn-info" required class="selectpicker form-control form-control-sm" required data-show-subtext="true" data-live-search="true">
                          <?php 
                            $sqlListaCargos="SELECT codigo,nombre,abreviatura,cod_tipo_cargo,
                            (select tc.nombre from tipos_cargos_personal tc where tc.codigo=cod_tipo_cargo)as nombre_tipo_cargo
                          from cargos where cod_estadoreferencial=1 order by nombre";
                            $stmtListaCargos=$dbh->query($sqlListaCargos);
                            while ($row = $stmtListaCargos->fetch()) { ?>
                              <option value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                          <?php } ?>
                      </select>
                  </div>
                </div>
                <div class="col-md-12 text-center">
                  <button type="button" class="btn btn-success" id="cargo_mover_responsabilidad">Guardar</button>
                </div>
            </div>
          
          </div>
      </div>
  </div>
</div>



<script type="text/javascript">
  $(document).ready(function(){
    $('#registrarFC').click(function(){    
      cod_cargoA       = document.getElementById("cod_cargoA").value;
      nombre_funcionA  = $('#nombre_funcionA').val();
      ordenA           = $('#ordenA').val();
      RegistrarCargoFuncion(cod_cargoA,nombre_funcionA,ordenA);
    });
    $('#EditarFC').click(function(){
      cod_cargoE         = document.getElementById("cod_cargoE").value;
      cod_cargo_funcionE = document.getElementById("cod_cargo_funcionE").value;
      nombre_funcionE    = $('#nombre_funcionE').val();
      ordenE             = $('#ordenE').val();
      EditarCargoFuncion(cod_cargoE,cod_cargo_funcionE,nombre_funcionE,ordenE);
    });
    $('#EliminarFC').click(function(){    
      cod_cargoB         = document.getElementById("cod_cargoB").value;
      cod_cargo_funcionB = document.getElementById("cod_cargo_funcionB").value;
      EliminarCargoFuncion(cod_cargoB,cod_cargo_funcionB);    
    });
  });
  // Copia de Responsabilidades de Cargo
  $('body').on('click','#cargo_copia_responsabilidad', function(){
    let formData = new FormData();
    formData.append('cod_cargo', <?=$codigo?>);
    formData.append('cod_cargo_copia', $('#cod_cargo_copia').val());
    swal({
        title: '¿Esta seguro de hacer una copia?',
        text: "Se realizará una copia de las responsabilidades de cargo, de acuerdo al cargo seleccionado.",
        type: 'warning',
        showCancelButton: true,
        confirmButtonClass: 'btn btn-success',
        cancelButtonClass: 'btn btn-danger',
        confirmButtonText: 'Si',
        cancelButtonText: 'No',
        buttonsStyling: false
    }).then((result) => {
        if (result.value) {
            $(".cargar-ajax").removeClass("d-none");
            $.ajax({
                url:"rrhh/ajaxFuncionesCargoCopia.php",
                type:"POST",
                contentType: false,
                processData: false,
                data: formData,
                success:function(response){
                let resp = JSON.parse(response);
                if(resp.status){
                    $(".cargar-ajax").addClass("d-none");// Mensaje
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
  // Mover Responsabilidades de Cargo
  $('body').on('click','#cargo_mover_responsabilidad', function(){
    let formData = new FormData();
    formData.append('cod_cargo', <?=$codigo?>);
    formData.append('cod_cargo_mover', $('#cod_cargo_mover').val());
    swal({
        title: '¿Esta seguro de mover?',
        text: "Se moveran todas las Responsabilidades de Cargo al Cargo seleccionado.",
        type: 'warning',
        showCancelButton: true,
        confirmButtonClass: 'btn btn-success',
        cancelButtonClass: 'btn btn-danger',
        confirmButtonText: 'Si',
        cancelButtonText: 'No',
        buttonsStyling: false
    }).then((result) => {
        if (result.value) {
            $(".cargar-ajax").removeClass("d-none");
            $.ajax({
                url:"rrhh/ajaxFuncionesCargoMover.php",
                type:"POST",
                contentType: false,
                processData: false,
                data: formData,
                success:function(response){
                let resp = JSON.parse(response);
                if(resp.status){
                    $(".cargar-ajax").addClass("d-none");// Mensaje
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
     * Mostrar datos para Editar
     */
    $('body').on('click', '.editar', function(){
        let cod_cargoE         = $(this).data('codcargoe');
        let cod_cargo_funcionE = $(this).data('codcargo_funcione');
        let nombre_funcionE    = $(this).data('nombrefuncione');
        let ordenE             = $(this).data('ordene');

        $('#cod_cargoE').val(cod_cargoE);
        $('#cod_cargo_funcionE').val(cod_cargo_funcionE);
        $('#nombre_funcionE').val(nombre_funcionE);
        $('#ordenE').val(ordenE);
        
        $('#modalEditar').modal('show');
    });
</script>