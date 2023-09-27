<?php

require_once 'conexion.php';
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();

$stmt = $dbh->prepare("SELECT rg.codigo, rg.nombre, rg.estado
                    FROM responsabilidades_generales rg
                    ORDER BY rg.codigo DESC");
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('estado', $estado);

?>

<!-- ESTILO DE ANIMACIÓN DE CARGANDO -->
<style>
  .loading-dot {
    margin: 3px;
    width: 10px;
    height: 10px;
    background-color: white;
    border-radius: 50%;
    opacity: 0;
    animation: pulse 1s infinite;
  }

  @keyframes pulse {
    0%, 100% {
      opacity: 0;
    }
    50% {
      opacity: 1;
    }
  }
  /* Estilo de bordes de CARDS */
  .state-success{
    border: 1px solid #4caf50;
    border-left-width: 6px;
  }
  .state-warning{
    border: 1px solid #ff9800;
    border-left-width: 6px;
  }
  .state-danger{
    border: 1px solid #f44336;
    border-left-width: 6px;
  }
  .state-info{
    border: 1px solid #00bcd4;
    border-left-width: 6px;
  }
</style>
<div class="content">
	<div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header card-header-primary card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">assignment</i>
                  </div>
                  <h4 class="card-title">Lista Responsabilidades Ibnorca</h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table" id="tablePaginator">

                      <thead>
                        <tr>
                          <th width="5%">#</th>
                          <th width="75%">Nombre</th>
                          <th width="10%">Estado</th>
                          <th width="10%" class="text-center">Acciones</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php $index=1;
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { ?>
                          <tr>
                            <td><?=$index;?></td>
                            <td><?=$nombre;?></td>
                              <td>
                                <?php if($estado == 1){ ?>
                                <span class="badge badge-md badge-success">Activo</span>
                                <?php }else{ ?>
                                <span class="badge badge-md badge-danger">Inactivo</span>
                                <?php } ?>
                              </td>
                              <td class="td-actions text-center">
                                <?php
                                    if($globalAdmin==1){
                                ?>
                                    <!-- Editar -->
                                    <button type="button" class="btn btn-info form_edit" data-codigo="<?=$codigo;?>" data-nombre="<?=$nombre;?>"><i class="material-icons" title="Editar"><?=$iconEdit;?></i></button>

                                    <!-- Modificar Estado -->                                    
                                    <button class="btn btn-<?=$estado == 1?'danger':'success'?> form_del" title="<?=$estado == 1?'Desactivar':'Activar'?>"  data-codigo="<?=$codigo;?>">
                                    <i class="material-icons"><?=$estado == 1?'close':'check'?></i>
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
                    <a href="?opcion=areasLista" class="btn btn-danger">
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

<!-- Modal REGISTRAR O EDITAR -->
<div class="modal fade" id="modalAgregarEditar" tabindex="-1" role="dialog" aria-labelledby="modalAgregarEditarLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAgregarEditarLabel">Agregar/Editar Registro</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formularioRegistro">
                    <input type="hidden" id="registroId" name="registroId" value="">
                    <div class="row">
                        <label class="col-sm-3"><span class="text-danger">*</span> Descripción :</label>
                        <div class="col-sm-9">
                            <textarea class="form-control" name="nombre" id="nombre" rows="4" placeholder="Agregar descripción de responsabilidad"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" id="guardarRegistro" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Función para abrir el modal con campos en blanco
    $("#form_reg").click(function() {
        $("#modalAgregarEditarLabel").text("Agregar Registro");
        $("#registroId").val("");
        $("#nombre").val("");
        $("#modalAgregarEditar").modal("show");
    });

    // Función para editar un registro existente
    $(".form_edit").click(function() {
        var registroId = $(this).data("codigo");
        var nombre = $(this).data("nombre");

        $("#modalAgregarEditarLabel").text("Editar Registro");
        $("#registroId").val(registroId);
        $("#nombre").val(nombre);
        $("#modalAgregarEditar").modal("show");
    });

    // Función para guardar el registro mediante AJAX
    $("#guardarRegistro").click(function() {
        let formData = new FormData();
        formData.append('codigo', $('#registroId').val());
        formData.append('nombre', $('#nombre').val());
        $.ajax({
            url: "responsabilidad_ibnorca/responsabilidadSave.php",
            method: "POST",
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json', // Indica que esperas una respuesta JSON
            success: function(response) {
                // console.log(response);

                if (response.status) {
                    Swal.fire({
                        type: "success",
                        title: response.message,
                        showConfirmButton: false,
                        timer: 2000,
                        onClose: function() {
                            location.reload();
                        }
                    });
                } else {
                    Swal.fire({
                        type: "error",
                        title: response.message,
                    });
                }

                $("#modalAgregarEditar").modal("hide");
            },
            error: function(xhr, textStatus, errorThrown) {
                // Maneja los errores de la solicitud AJAX
                console.error(textStatus);
            }
        });

    });
    
    // Función para modificar estado
    $(".form_del").click(function() {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¿Deseas cambiar el estado del registro?",
            type: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí',
            cancelButtonText: 'No',
        }).then((result) => {
            if (result.value) {
                // Si el usuario confirma la acción, procede con la solicitud AJAX
                let formData = new FormData();
                formData.append('codigo', $(this).data('codigo'));
                $.ajax({
                    url: "responsabilidad_ibnorca/responsabilidadEstado.php",
                    method: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                type: "success",
                                title: response.message,
                                showConfirmButton: false,
                                timer: 2000,
                                onClose: function() {
                                    location.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                type: "error",
                                title: response.message,
                            });
                        }
                        $("#modalAgregarEditar").modal("hide");
                    },
                    error: function(xhr, textStatus, errorThrown) {
                        console.error(textStatus);
                    }
                });
            }
        });
    });

});
</script>
