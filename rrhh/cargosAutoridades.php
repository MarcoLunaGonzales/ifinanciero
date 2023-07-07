<?php

require_once 'conexion.php';
require_once 'styles.php';

$globalAdmin = $_SESSION["globalAdmin"];

$cod_cargo   = $codigo;
$dbh         = new Conexion();

$stmtPersonal = $dbh->prepare("SELECT nombre from cargos where codigo=$cod_cargo");
$stmtPersonal->execute();
$result=$stmtPersonal->fetch();
$nombre_cargo=$result['nombre'];
//SELECT
$stmt = $dbh->prepare("SELECT *
                    FROM cargos_autoridades
                    WHERE cod_estadoautoridad = 1 
                    AND cod_cargo = '$cod_cargo'
                    ORDER BY orden" );
$stmt->execute();
$stmt->bindColumn('cod_cargo', $cod_cargo);
$stmt->bindColumn('cod_autoridad', $cod_autoridad);
$stmt->bindColumn('nombre_autoridad', $nombre_autoridad);
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
                  <h4 class="card-title"> Autoridades del Cargo</h4>
                  <h4 class="card-title" align="center"><?=$nombre_cargo;?></h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <!-- codigo de Cargo -->
                    <input type="hidden" id="cod_cargo" value="<?= $cod_cargo; ?>">
                    <table class="table" id="tablePaginator">
                      <thead>
                        <tr>
                        	<th width="20">#</th>
                          <th width="100">Nombre</th>
                          <th width="10">Orden</th>
              						<th width="20"></th>                                                   
                        </tr>
                      </thead>
                      <tbody>
                        <?php $index=1;                      
                        $datos=$cod_cargo;
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {                       	
                        	$datos=$cod_cargo."/".$cod_autoridad."/".$nombre_autoridad."/".$orden;
                        	?>
                            <tr>
                                <td><?=$index;?></td>
                                <td><?=$nombre_autoridad;?></td>
                                <td><?=$orden;?></td>
                                
                                <td class="td-actions text-right">
                                  <?php
                                    if($globalAdmin==1){
                                  ?>                                	
                                  <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalEditar" onclick="agregaAutoridadCargoE('<?=$datos;?>')">
                                		<i class="material-icons" title="Editar autoridad"><?=$iconEdit;?></i>
                                  </button>
                                  <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modalEliminar" onclick="eliminarAutoridadCargoE('<?=$datos;?>')">
                                    <i class="material-icons" title="Eliminar Autoridad"><?=$iconDelete;?></i>
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
                  <button type="button" class="btn btn-warning btn-round btn-fab" data-toggle="modal" data-target="#modalAgregarAutoridadCargo">
                      <i class="material-icons" title="Agregar Autoridad">add</i>
  		             </button>                                            
                <?php
                }
                ?>
                <a href="index.php?opcion=cargosLista" class="btn btn-danger btn-round btn-fab">
                  <i class="material-icons" title="Volver">keyboard_return</i>
                </a> 
                <!-- Copiar Responsabilidades de OTRO cargo -->
                <button type="button" class="btn btn-success btn-round btn-fab" onclick="$('#modalCopyAutoridad').modal('show')">
                  <i class="material-icons" title="Copiar Resp. de otro Cargo">keyboard</i>
                </button> 
                <!-- Mover Responsabilidades de OTRO cargo -->
                <button type="button" class="btn btn-primary btn-round btn-fab" onclick="$('#modalMoveAutoridad').modal('show')">
                  <i class="material-icons" title="Mover Resp. a otro Cargo">keyboard</i>
                </button> 
              </div>
            </div>
          </div>  
        </div>
    </div>


<!-- Modal agregar -->
<div class="modal fade" id="modalAgregarAutoridadCargo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Registrar Autoridad del Cargo</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="cod_cargoA" id="cod_cargoA" value="0">
        <h6> Autoridad </h6>
        <textarea rows="4" class="form-control" name="nombre_autoridadA" id="nombre_autoridadA" onkeyup="javascript:this.value=this.value.toUpperCase();" required="true">
        </textarea>
        <h6> Orden </h6>
        <input type="number" class="form-control" name="ordenA" id="ordenA" placehorder="Introduzca el nro orden del registro">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="registrarA" name="registrarPC" data-dismiss="modal">Aceptar</button>
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
        <h4 class="modal-title" id="myModalLabel">Editar Autoridad del Cargo </h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="cod_autoridadE" id="cod_autoridadE" value="0">    
        <h6> Autoridad </h6>
        <textarea rows="4"  class="form-control" name="nombre_autoridadE" id="nombre_autoridadE" onkeyup="javascript:this.value=this.value.toUpperCase();" required="true">
        </textarea>
        <h6> Orden </h6>
        <input type="number" class="form-control" name="ordenE" id="ordenE" placehorder="Introduzca el nro orden del registro">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="EditarA"  data-dismiss="modal">Aceptar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="material-icons" title="Volver">keyboard_return</i> Volver </button>
      </div>
    </div>
  </div>
</div>
<!-- Eliminar Respnosabilidad de Cargo-->
<div class="modal fade" id="modalEliminar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Eliminar Autoridad Del Cargo</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="cod_autoridadB" id="cod_autoridadB" value="0">           
        Esta acción Eliminará La autoridad del cargo. ¿Deseas Continuar?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="EliminarFC"  data-dismiss="modal">Aceptar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="material-icons" title="Volver">keyboard_return</i> Volver </button>
      </div>
    </div>
  </div>
</div>
<!-- Modal Copiar Responsabilidades de Cargo -->
<div class="modal fade" id="modalCopyAutoridad" tabindex="-1" role="dialog" aria-labelledby="myModalLabel3">
  <div class="modal-dialog modal-lg">
      <div class="modal-content">
          <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title text-primary font-weight-bold" id="myModalLabel3">Copiar Responsabilidades de Cargo de:</h4>
          </div>
          <div class="modal-body" id="modal-lista_documentos">
            <div class="row">
                <label class="col-sm-3 col-form-label">Cargo</label>
                <div class="col-sm-9">
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
                  <button type="button" class="btn btn-success" id="cargo_copia_autoridad">Guardar</button>
                </div>
            </div>
          
          </div>
      </div>
  </div>
</div>
<!-- Modal Mover Responsabilidades de Cargo -->
<div class="modal fade" id="modalMoveAutoridad" tabindex="-1" role="dialog" aria-labelledby="myModalLabel3">
  <div class="modal-dialog modal-lg">
      <div class="modal-content">
          <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title text-primary font-weight-bold" id="myModalLabel3">Mover Responsabilidades de Cargo a:</h4>
          </div>
          <div class="modal-body" id="modal-lista_documentos">
            <div class="row">
                <label class="col-sm-3 col-form-label">Cargo</label>
                <div class="col-sm-9">
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
                  <button type="button" class="btn btn-success" id="autoridad_mover_responsabilidad">Guardar</button>
                </div>
            </div>
          
          </div>
      </div>
  </div>
</div>



<script type="text/javascript">
  // Editar Registro
  function agregaAutoridadCargoE(datos){
    var d = datos.split('/');
    document.getElementById("cod_autoridadE").value    = d[1];
    document.getElementById("nombre_autoridadE").value = d[2];
    document.getElementById("ordenE").value            = d[3];
  }
  // Eliminar Registros
  function eliminarAutoridadCargoE(datos){
    var d = datos.split('/');
    document.getElementById("cod_autoridadB").value    = d[1];
  }
  $(document).ready(function(){
    // Registro
    $('#registrarA').click(function(){
      var cod_cargo     = $('#cod_cargo').val();
      nombre_autoridadA = $('#nombre_autoridadA').val();
      ordenA            = $('#ordenA').val();
      $.ajax({
        type:"POST",
        data:"cod_cargo="+cod_cargo+"&nombre_autoridad="+nombre_autoridadA+"&orden="+ordenA+"&cod_estadoreferencial=1&orden="+ordenA,
        url:"rrhh/cargosAutoridadSave.php",
        success:function(r){
          console.log(r)
          let ruta = "index.php?opcion=cargosAutoridades&codigo=" + cod_cargo;
          if(r==1){
            alerts.showSwal('success-message', ruta);
          }else{
            alerts.showSwal('error-message', ruta);
          } 
        }
      });
    });
    // Edición
    $('#EditarA').click(function(){
      var cod_cargo     = $('#cod_cargo').val();
      cod_autoridadE    = $('#cod_autoridadE').val();
      nombre_autoridadE = $('#nombre_autoridadE').val();
      ordenE            = $('#ordenE').val();
      $.ajax({
        type:"POST",
        data:"cod_autoridad="+cod_autoridadE+"&nombre_autoridad="+nombre_autoridadE+"&cod_estadoreferencial=2&orden="+ordenE,
        url:"rrhh/cargosAutoridadSave.php",
        success:function(r){
          let ruta = 'index.php?opcion=cargosAutoridades&codigo='+cod_cargo;

          if(r==1){
            alerts.showSwal('success-message', ruta);
          }else{
            alerts.showSwal('error-message', ruta);
          } 
        }
      });
    });
    // Estado
    $('#EliminarFC').click(function(){
      var cod_cargo     = $('#cod_cargo').val();
      cod_autoridadB = document.getElementById("cod_autoridadB").value;
      $.ajax({
        type:"POST",
        data:"cod_autoridad="+cod_autoridadB+"&cod_estadoreferencial=3",
        url:"rrhh/cargosAutoridadSave.php",
        success:function(r){
          let ruta = 'index.php?opcion=cargosAutoridades&codigo='+cod_cargo;
          if(r==1){
            alerts.showSwal('success-message',ruta);
          }else{      
              alerts.showSwal('error-message',ruta);
          } 
        }
      });   
    });
  });
  // Copia de Responsabilidades de Cargo
  $('body').on('click','#cargo_copia_autoridad', function(){
    let formData = new FormData();
    formData.append('cod_cargo', <?=$cod_cargo?>);
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
                url:"rrhh/ajaxAutoridadesCargoCopia.php",
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
  $('body').on('click','#autoridad_mover_responsabilidad', function(){
    let formData = new FormData();
    formData.append('cod_cargo', <?=$cod_cargo?>);
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
                url:"rrhh/ajaxAutoridadesCargoMover.php",
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
</script>