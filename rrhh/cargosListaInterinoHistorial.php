<?php

require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();

// Codigo del Cargo seleccionado
$cod_cargo = $_GET['codigo'];

// LISTA DE HISTORIAL INTERINO
$stmt = $dbh->prepare("SELECT cih.codigo, cih.fecha_inicio, cih.fecha_fin, cih.cod_personal, UPPER(CONCAT(p.primer_nombre, ' ', p.paterno, ' ', p.materno)) as personal_nombre
                    FROM cargos_interinos_historicos cih
                    LEFT JOIN personal p ON p.codigo = cih.cod_personal
                    WHERE cih.estado = 1
                    AND cih.cod_cargo = '$cod_cargo'
                    ORDER BY cih.codigo DESC");
$stmt->execute();
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('cod_personal', $cod_personal);
$stmt->bindColumn('fecha_inicio', $fecha_inicio);
$stmt->bindColumn('fecha_fin', $fecha_fin);
$stmt->bindColumn('personal_nombre', $personal_nombre);

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

<input type="hidden" id="cod_cargo" value="<?=$cod_cargo?>">

<div class="content">
	<div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons"><?=$iconCard;?></i>
                  </div>
                  <h4 class="card-title">Historial Interinato <b class="text-danger">[<?=$nombre_cargo?>]</b></h4>                  
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table" id="tablePaginator">

                      <thead>
                        <tr>
                          <th style="width: 10%;">#</th>
                          <th style="width: 30%;">Personal</th>
                          <th style="width: 25%;">Fecha de Inicio</th>
                          <th style="width: 25%;">Fecha de Fin</th>
                          <th style="width: 10%;" class="text-center">Acciones</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php $index=1;
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { ?>
                          <tr>
                            <td><?=$index;?></td>
                            <td><?=$personal_nombre;?></td>
                            <td><?=$fecha_inicio;?></td>
                            <td><?=$fecha_fin;?></td>
                            <td class="td-actions text-center">
                              <?php
                                if($globalAdmin==1){
                              ?>
                              <!-- Editar -->
                              <button type="button" class="btn btn-info form_edit" data-codigo="<?=$codigo;?>" data-fecha_inicio="<?=$fecha_inicio;?>" data-fecha_fin="<?=$fecha_fin;?>" data-cod_personal="<?=$cod_personal;?>" ><i class="material-icons" title="Editar"><?=$iconEdit;?></i></button>

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
<div class="modal fade" id="modalInterinoHistorial" tabindex="-1" role="dialog" aria-labelledby="modalAgregarEditarLabel" aria-hidden="true">
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
                      <label class="col-sm-3"><span class="text-danger">*</span> Fecha Inicio:</label>
                      <div class="col-sm-9">
                        <input class="form-control" type="date" name="fecha_inicio" id="fecha_inicio" placeholder="Agregar fecha inicio"/>
                      </div>
                      <br>
                      <label class="col-sm-3"><span class="text-danger">*</span> Fecha Fin:</label>
                      <div class="col-sm-9">
                        <input class="form-control" type="date" name="fecha_fin" id="fecha_fin" placeholder="Agregar fecha fin"/>
                      </div>
                      <br>
                      <label class="col-sm-3"><span class="text-danger">*</span> Personal:</label>
                      <div class="col-sm-9">
                        <div class="form-group">
                          <select name="cod_personal" id="cod_personal" data-style="btn btn-info" class="selectpicker form-control form-control-sm" data-show-subtext="true" data-live-search="true">
                              <option value="" disabled>SELECCIONAR</option>
                              <?php 
                                $sqlPersonal="SELECT p.codigo, UPPER(CONCAT(p.primer_nombre, ' ', p.paterno, ' ', p.materno)) as nombre_personal, c.abreviatura as cargo_abreviatura
                                          FROM personal p
                                          LEFT JOIN cargos c ON c.codigo = p.cod_cargo
                                          WHERE p.cod_estadopersonal = 1
                                          ORDER BY nombre_personal ASC";
                                $stmtPersonal=$dbh->query($sqlPersonal);
                                while ($rowPersonal = $stmtPersonal->fetch()) { ?>
                                  <option value="<?=$rowPersonal["codigo"];?>"><?=$rowPersonal["nombre_personal"];?> [<?=$rowPersonal["cargo_abreviatura"];?>]</option>
                              <?php } ?>
                          </select>
                        </div>
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
   * Registro de Historial Interino
   */
  $('#form_reg').click(function(){
    $("#codigo").val("");
    
    $("#fecha_inicio").val("");
    $("#fecha_fin").val("");

    $('#modalTituloCambio').html('Nuevo - Historial Interino');
    $('#modalInterinoHistorial').modal('show');
  });

  // Función para editar un registro existente
  $(".form_edit").click(function() {
    var codigo       = $(this).data("codigo");
    var fecha_inicio = $(this).data("fecha_inicio");
    var fecha_fin    = $(this).data("fecha_fin");
    var cod_personal = $(this).data("cod_personal");

    $("#modalAgregarEditarLabel").text("Editar Registro");
    $("#codigo").val(codigo);
    
    $("#fecha_inicio").val(fecha_inicio);
    $("#fecha_fin").val(fecha_fin);
    $("#cod_personal").val(cod_personal).trigger('change');
    
    $('#modalTituloCambio').html('Editar - Historial Interino');
    $("#modalInterinoHistorial").modal("show");
  });

  /**
   * Registro de formulario Historial Interino
   */
  $("#formGuardarControl").click(function() {
    let codigo       = $('#codigo').val();
    let cod_cargo    = $('#cod_cargo').val();
    let fecha_inicio = $('#fecha_inicio').val();
    let fecha_fin    = $('#fecha_fin').val();
    let cod_personal = $('#cod_personal').val();
     // Realizar validaciones
     if (fecha_inicio.trim() === '' && fecha_fin.trim() === '') {
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
              url: "rrhh/ajaxCargoInterinoHistorialSave.php",
              data: {
                codigo: codigo,
                cod_cargo: cod_cargo,
                fecha_inicio: fecha_inicio,
                fecha_fin: fecha_fin,
                cod_personal: cod_personal
              },
              dataType: "json",
              success: function(response) {
                $('#modalInterinoHistorial').modal('toggle');
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
              url: "rrhh/ajaxCargoInterinoHistorialEstado.php",
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
