<?php

require_once 'conexion.php';
require_once 'styles.php';

$globalAdmin = $_SESSION["globalAdmin"];

$cod_cargo   = $codigo;
$dbh         = new Conexion();

// CODIGO GET: Config Aprobación
$cod_aprobacion = $_GET['cod_config_aprobacion'];

$stmtPersonal = $dbh->prepare("SELECT nombre from cargos where codigo=$cod_cargo");
$stmtPersonal->execute();
$result=$stmtPersonal->fetch();
$nombre_cargo=$result['nombre'];
//SELECT
$stmt = $dbh->prepare("SELECT *
                    FROM cargos_autoridades
                    WHERE cod_estadoautoridad = 1 
                    AND cod_cargo = '$cod_cargo'
                    AND cod_configuracion = '$cod_aprobacion'
                    ORDER BY orden" );
$stmt->execute();
$stmt->bindColumn('cod_autoridad', $cod_autoridad);
$stmt->bindColumn('nombre_autoridad', $nombre_autoridad);

/********************************/
/*    DATOS DE TABLA EXTERNA    */
/********************************/
$acc_sql = "SELECT acc.codigo,
              acc.nombre
              FROM  aprobacion_configuraciones_cargos acc 
              WHERE acc.codigo = :codigo";
$acc_stmt = $dbh->prepare($acc_sql);
$acc_stmt->bindParam(':codigo', $cod_aprobacion);
$acc_stmt->execute();
while ($row = $acc_stmt->fetch(PDO::FETCH_ASSOC)) {
	$config_codigo = $row['codigo'];
	$config_nombre = $row['nombre'];
}
/************************************/
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
                  <h4 class="card-title"> <b><?=$config_nombre;?> - </b> Autoridades del Cargo</h4>
                  <h4 class="card-title" align="center"><?=$nombre_cargo;?></h4>
                </div>
                <div class="card-body">
                    <input type="hidden" value="<?=$config_codigo;?>" id="cod_config">
                  <div class="table-responsive">
                    <table class="table" id="tablePaginator">
                      <thead>
                        <tr>
                        	<th>#</th>                          
              						<th>Nombre</th>
              						<th></th>                                                   
                        </tr>
                      </thead>
                      <tbody>
                        <?php $index=1;                      
                        $datos=$cod_cargo;
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {                       	
                        	$datos=$cod_cargo."/".$cod_autoridad."/".$nombre_autoridad;
                        	?>
                            <tr>
                                <td><?=$index;?></td>
                                <td><?=$nombre_autoridad;?></td>
                                
                                <td class="td-actions text-right">
                                  <?php
                                    if($globalAdmin==1){
                                  ?>                                	
                                  <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalEditar" onclick="agregaAutoridadCargoE('<?=$datos;?>')">
                                		<i class="material-icons" title="Editar autoridad"><?=$iconEdit;?></i>
                                  </button>
                                  <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modalEliminar" onclick="agregaAutoridadCargoB('<?=$datos;?>')">
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
                  <button type="button" class="btn btn-warning btn-round btn-fab" data-toggle="modal" data-target="#modalAgregarAutoridadCargo" onclick="agregaAutoridadCargo('<?=$datos;?>')">
                      <i class="material-icons" title="Agregar Autoridad">add</i>
  		             </button>                                            
                <?php
                }
                ?>
                <a href="index.php?opcion=configuracionCargosLista&cod_config_aprobacion=<?=$config_codigo;?>" class="btn btn-danger btn-round btn-fab">
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
        <h6> Orde </h6>
        <input type="number" class="form-control" name="ordenA" id="ordenA" placehorder="Introduzca el nro orden del registro">
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
        <h4 class="modal-title" id="myModalLabel">Editar Autoridad del Cargo </h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="cod_cargoE" id="cod_cargoE" value="0">
        <input type="hidden" name="cod_cargoE" id="cod_cargoE" value="0">        
        <h6> Autoridad </h6>
        <textarea rows="4"  class="form-control" name="nombre_autoridadE" id="nombre_autoridadE" onkeyup="javascript:this.value=this.value.toUpperCase();" required="true">
        </textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="EditarFC"  data-dismiss="modal">Aceptar</button>
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
        <input type="hidden" name="cod_cargoB" id="cod_cargoB" value="0">
        <input type="hidden" name="cod_cargoB" id="cod_cargoB" value="0">               
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
                <label class="col-sm-3 col-form-label">Configuración Cargos</label>
                <div class="col-sm-9">
                  <div class="form-group">
                      <select name="cod_config_copia" id="cod_config_copia" data-style="btn btn-info" required class="selectpicker form-control form-control-sm" required data-show-subtext="true" data-live-search="true">
                          <?php 
                            $sqlConfig="SELECT acc.codigo,
                                              acc.nombre,
                                              efc.nombre as nombre_estado
                                              FROM aprobacion_configuraciones_cargos acc
                                              LEFT JOIN estados_funcionescargo efc ON efc.codigo = acc.cod_estadoaprobacion";
                            $stmtConfig=$dbh->query($sqlConfig);
                            while ($row = $stmtConfig->fetch()) { ?>
                              <option value="<?=$row["codigo"];?>"><?=$row["nombre"].' ['.$row["nombre_estado"].']';?></option>
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
                <label class="col-sm-3 col-form-label">Configuración Cargos</label>
                <div class="col-sm-9">
                  <div class="form-group">
                      <select name="cod_config_mover" id="cod_config_mover" data-style="btn btn-info" required class="selectpicker form-control form-control-sm" required data-show-subtext="true" data-live-search="true">
                          <?php 
                            $sqlConfig="SELECT acc.codigo,
                                              acc.nombre,
                                              efc.nombre as nombre_estado
                                              FROM aprobacion_configuraciones_cargos acc
                                              LEFT JOIN estados_funcionescargo efc ON efc.codigo = acc.cod_estadoaprobacion";
                            $stmtConfig=$dbh->query($sqlConfig);
                            while ($row = $stmtConfig->fetch()) { ?>
                              <option value="<?=$row["codigo"];?>"><?=$row["nombre"].' ['.$row["nombre_estado"].']';?></option>
                          <?php } ?>
                      </select>
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
    // Nuevo Registro
    function agregaAutoridadCargo(datos){
      var d=datos.split('/');
      document.getElementById("cod_cargoA").value=d[0];
    }
    // Editar Registro
    function agregaAutoridadCargoE(datos){
      var d=datos.split('/');
      document.getElementById("cod_cargoE").value=d[0];
      document.getElementById("cod_cargo_funcionE").value=d[1];
      document.getElementById("nombre_funcionE").value=d[2];
      document.getElementById("pesoE").value=d[3];
    }
    // Estado Registro
    function agregaAutoridadCargoB(datos){
      var d=datos.split('/');
      document.getElementById("cod_cargoB").value=d[0];
      document.getElementById("cod_cargo_funcionB").value=d[1];
    }
    // Registro
    $('#registrarFC').click(function(){   
      let cod_config = $('#cod_config').val(); 
      cod_cargoA=document.getElementById("cod_cargoA").value;
      nombre_autoridadA=$('#nombre_autoridadA').val();
      $.ajax({
        type:"POST",
        data:"cod_funcion=0&cod_cargo="+cod_cargoA+"&nombre_autoridad="+nombre_autoridadA+"&cod_estadoreferencial=1&config_aprob="+config_aprob,
        url:"configuracion_cargo/cargosAutoridadSave.php",
        success:function(r){
          let ruta_cargos = 'index.php?opcion=configuracionCargosFunciones&codigo='+cod_cargoA+'&cod_config_aprobacion='+config_aprob;
          
          console.log(ruta_cargos)

          if(r==1){
            alerts.showSwal('success-message', ruta_cargos);
          }else{
            alerts.showSwal('error-message', ruta_cargos);
          } 
        }
      });
    });
    // Edición
    $('#EditarFC').click(function(){
      let cod_config = $('#cod_config').val(); 
      cod_cargoE=document.getElementById("cod_cargoE").value;
      cod_cargoE=document.getElementById("cod_cargoE").value;
      nombre_autoridadE=$('#nombre_autoridadE').val();
      $.ajax({
        type:"POST",
        data:"cod_funcion="+cod_cargo_funcionE+"&cod_cargo="+cod_cargoE+"&nombre_autoridad="+nombre_autoridadE+"&cod_estadoreferencial=2",
        url:"configuracion_cargo/cargosAutoridadSave.php",
        success:function(r){
          let ruta_cargos = 'index.php?opcion=configuracionCargosFunciones&codigo='+cod_cargoE+'&cod_config_aprobacion='+config_aprob;

          if(r==1){
            alerts.showSwal('success-message', ruta_cargos);
          }else{
            alerts.showSwal('error-message', ruta_cargos);
          } 
        }
      });
    });
    // Estado
    $('#EliminarFC').click(function(){   
      let cod_config = $('#cod_config').val();  
      cod_cargoB=document.getElementById("cod_cargoB").value;
      cod_cargoB=document.getElementById("cod_cargoB").value;
      $.ajax({
        type:"POST",
        data:"cod_funcion="+cod_cargo_funcionB+"&cod_cargo="+cod_cargoB+"&nombre_autoridad=''&cod_estadoreferencial=3&peso=0",
        url:"configuracion_cargo/cargosAutoridadSave.php",
        success:function(r){
          let ruta_cargos = 'index.php?opcion=configuracionCargosFunciones&codigo='+cod_cargoB+'&cod_config_aprobacion='+config_aprob;
          if(r==1){
            alerts.showSwal('success-message',ruta_cargos);
          }else{      
              alerts.showSwal('error-message',ruta_cargos);
          } 
        }
      });   
    });
  });
  // Copia de Responsabilidades de Cargo
  $('body').on('click','#cargo_copia_responsabilidad', function(){
    let formData = new FormData();
    formData.append('cod_cargo', <?=$codigo?>);
    formData.append('cod_cargo_copia', $('#cod_cargo_copia').val());
    formData.append('cod_config_copia', $('#cod_config_copia').val());
    formData.append('cod_config_actual', $('#cod_config').val());
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
                url:"configuracion_cargo/ajaxFuncionesCargoCopia.php",
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
    formData.append('cod_config_mover', $('#cod_config_mover').val());
    formData.append('cod_config_actual', $('#cod_config').val());
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
                url:"configuracion_cargo/ajaxFuncionesCargoMover.php",
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