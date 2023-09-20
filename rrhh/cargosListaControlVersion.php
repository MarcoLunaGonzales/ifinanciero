<?php

require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();

// Codigo del Cargo seleccionado
$cod_cargo = $_GET['codigo'];

// LISTA DE CONTROL DE CAMBIO DE VERSIONES
$stmt = $dbh->prepare("SELECT cv.codigo, cv.cod_cargo, cv.nro_version, cv.descripcion_cambios, DATE_FORMAT(cv.fecha,'%d-%m-%Y') as fecha, CONCAT(p.primer_nombre, ' ', p.paterno, ' ', p.materno) as personal
                    FROM control_versiones cv
                    LEFT JOIN personal p ON p.codigo = cv.cod_personal
                    WHERE cv.estado = 1
                    AND cv.cod_cargo = '$cod_cargo'
                    ORDER BY cv.codigo DESC");
$stmt->execute();
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('nro_version', $nro_version);
$stmt->bindColumn('descripcion_cambios', $descripcion_cambios);
$stmt->bindColumn('fecha', $fecha);
$stmt->bindColumn('personal', $personal);

// CARGO
$stmtCargo = $dbh->prepare("SELECT c.codigo, c.nombre as nombre_cargo
                    FROM cargos c
                    WHERE c.codigo = :cod_cargo");
$stmtCargo->bindParam(':cod_cargo', $cod_cargo);
$stmtCargo->execute();
$registro = $stmtCargo->fetch(PDO::FETCH_ASSOC);
if ($registro) {
  $nombre_cargo = $registro['nombre_cargo'];
} else {
  $nombre_cargo = '';
}
?>

<input type="hidden" id="control_cod_cargo" value="<?=$cod_cargo?>">

<div class="content">
	<div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons"><?=$iconCard;?></i>
                  </div>
                  <h4 class="card-title">Lista Control de Versiones <b class="text-danger">[<?=$nombre_cargo?>]</b></h4>                  
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table" id="tablePaginator">

                      <thead>
                        <tr>
                          <th width="10">#</th>
                          <th width="10">Versión</th>
                          <th width="150">Descripción del Cambio</th>
                          <th width="15">Fecha</th>
                          <th width="20">Personal</th>
                          <th width="5" class="text-center">Acciones</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php $index=1;
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { ?>
                          <tr>
                            <td><?=$index;?></td>
                            <td><?=$nro_version;?></td>
                            <td><?=$descripcion_cambios;?></td>
                            <td><?=$fecha;?></td>
                            <td><?=$personal;?></td>
                            <td class="td-actions text-center">

                              <?php
                                // No aparecerán las opciones en caso de tener el MOF
                                // en proceso de APROBACIÓN
                                if(empty($ma_cod_estado) || in_array($ma_cod_estado, [2, 3])){
                              ?>
                              
                              <!-- Editar -->
                              <button type="button" class="btn btn-info form_edit" data-codigo="<?=$codigo;?>" data-nro_version="<?=$nro_version;?>" data-descripcion_cambios="<?=$descripcion_cambios;?>"><i class="material-icons" title="Editar"><?=$iconEdit;?></i></button>

                              <!-- Eliminar -->
                              <button class="<?=$buttonDelete;?> formEstado" data-codigo="<?=$codigo;?>">
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
                </div>
              </div>
              <?php

              if($globalAdmin==1){
              ?>
      				<div class="card-footer fixed-bottom">
                <button type="button" class="btn btn-success" id="form_reg">Registrar</button>
                
                <a href='index.php?opcion=cargosLista' class="btn btn-danger">
                  <i class="material-icons">arrow_back</i> Volver
                </a>
              </div>
              <?php
              }
              ?>
		  
            </div>
          </div>  
        </div>
    </div>

<!-- MODAL CONTROL DE VERSIONES (Cambios) -->
<div class="modal fade" id="modalControlVersion" tabindex="-1" role="dialog" aria-labelledby="modalAgregarEditarLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTituloCambio"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formularioRegistro">
                    <input type="hidden" id="codigo" name="codigo" value="">
                    <div class="row">
                      <label class="col-sm-3"><span class="text-danger">*</span> Número de Versión :</label>
                      <div class="col-sm-9">
                        <input class="form-control" type="number" name="control_nro_version" id="control_nro_version" placeholder="Agregar número de versión"/>
                      </div>
                      <br>
                      <label class="col-sm-3"><span class="text-danger">*</span> Descripción de Cambios :</label>
                      <div class="col-sm-9">
                        <textarea class="form-control" id="control_descripcion_cambios" name="control_descripcion_cambios" rows="4" placeholder="Agregar descripción de cambios"></textarea>
                      </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" id="formGuardarControl" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
  /**
   * Registro de Control de Versión
   * Aqui se determina los cambios realizados en 
   * el manual de cargos
   */
  $('#form_reg').click(function(){
    $("#codigo").val("");
    $("#control_nro_version").val("");
    $("#control_descripcion_cambios").val("");
    $('#modalTituloCambio').html('Nuevo - Control de Versión');
    $('#modalControlVersion').modal('show');
  });

  // Función para editar un registro existente
  $(".form_edit").click(function() {
    var codigo          = $(this).data("codigo");
    var nro_version         = $(this).data("nro_version");
    var descripcion_cambios = $(this).data("descripcion_cambios");

    $("#modalAgregarEditarLabel").text("Editar Registro");
    $("#codigo").val(codigo);
    $("#control_nro_version").val(nro_version);
    $("#control_descripcion_cambios").val(descripcion_cambios);
    $('#modalTituloCambio').html('Editar - Control de Versión');
    $("#modalControlVersion").modal("show");
  });

  /**
   * Registro de formulario CONTROL DE VERSIÓN DE CAMBIOS
   */
  $("#formGuardarControl").click(function() {
    let codigo          = $('#codigo').val();
    let cod_cargo           = $('#control_cod_cargo').val();
    let nro_version         = $('#control_nro_version').val();
    let descripcion_cambios = $('#control_descripcion_cambios').val();
     // Realizar validaciones
     if (descripcion_cambios.trim() === '' && nro_version.trim() === '') {
        Swal.fire({
            type: 'warning',
            title: 'Error',
            text: 'Por favor, complete el formulario.'
        });
        return;
    }
     // Mostrar SweetAlert de confirmación
     Swal.fire({
        type: 'question',
        title: 'Confirmación',
        text: '¿Estás seguro de guardar los cambios?',
        showCancelButton: true,
        confirmButtonText: 'Sí',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.value) {
          // Registrar Datos
          $.ajax({
              method: "POST",
              url: "rrhh/ajaxCargoControlVersionSave.php",
              data: {
                codigo: codigo,
                cod_cargo: cod_cargo,
                nro_version: nro_version,
                descripcion_cambios: descripcion_cambios
              },
              dataType: "json",
              success: function(response) {
                $('#modalControlVersion').modal('toggle');
                if (response.status) {
                  Swal.fire({
                      type: "success",
                      title: 'Exitoso',
                      text: response.message,
                      showConfirmButton: false,
                      timer: 2000,
                      onClose: function() {
                        location.reload();
                      }
                  });
                } else {
                  Swal.fire({
                    type: 'error',
                    title: 'Error',
                    text: response.message
                  });
                }
              },
              error: function() {
                  Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error en la solicitud AJAX.'
                  });
              }
          });
        }
    });
  });

  /**
   * Estado - Modificación de estado de registro
   */
  $(".formEstado").click(function() {
    let codigo = $(this).data('codigo');
     // Mostrar SweetAlert de confirmación
     Swal.fire({
        type: 'question',
        title: 'Confirmación',
        text: '¿Estás seguro de eliminar el registro?',
        showCancelButton: true,
        confirmButtonText: 'Sí',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.value) {
          // Registrar Datos
          $.ajax({
              method: "POST",
              url: "rrhh/ajaxCargoControlVersionEstado.php",
              data: {
                codigo: codigo,
              },
              dataType: "json",
              success: function(response) {
                if (response.status) {
                  Swal.fire({
                      type: "success",
                      title: 'Exitoso',
                      text: response.message,
                      showConfirmButton: false,
                      timer: 2000,
                      onClose: function() {
                        location.reload();
                      }
                  });
                } else {
                  Swal.fire({
                    type: 'error',
                    title: 'Error',
                    text: response.message
                  });
                }
              },
              error: function() {
                  Swal.fire({
                    type: 'error',
                    title: 'Error',
                    text: 'Error en la solicitud AJAX.'
                  });
              }
          });
        }
    });
  });
});
</script>
