<?php

require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();

$stmt = $dbh->prepare("SELECT
                        m.codigo,
                        UPPER(m.nombre) AS nombre,
                        ma.codigo as ma_codigo,
                        ma.cod_etapa as ma_cod_etapa,
                        ma.cod_estado as ma_cod_estado,
                        mae.nombre as ma_nombre_estado,
                        mae.color as ma_color_estado,
                        CONCAT(eta.descripcion, ' (', eta.nombre, ')') as eta_etapa
                      FROM mof m
                      LEFT JOIN (
                          SELECT
                              ma1.codigo,
                              ma1.cod_mof,
                              ma1.cod_etapa,
                              ma1.cod_estado
                          FROM mof_aprobacion ma1
                          INNER JOIN (
                              SELECT
                                cod_mof,
                                MAX(codigo) AS max_codigo
                              FROM mof_aprobacion
                              GROUP BY cod_mof
                          ) max_ma ON ma1.cod_mof = max_ma.cod_mof AND ma1.codigo = max_ma.max_codigo
                      ) ma ON ma.cod_mof = m.codigo
                      LEFT JOIN mof_aprobacion_estados mae ON mae.codigo = ma.cod_estado
                      LEFT JOIN mof_aprobacion_etapas eta ON eta.codigo = ma.cod_etapa
                      WHERE m.estado = 1
                      ORDER BY m.nombre");
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('ma_codigo', $ma_codigo);
$stmt->bindColumn('ma_cod_etapa', $ma_cod_etapa);
$stmt->bindColumn('ma_cod_estado', $ma_cod_estado);
$stmt->bindColumn('ma_nombre_estado', $ma_nombre_estado);
$stmt->bindColumn('ma_color_estado', $ma_color_estado);
$stmt->bindColumn('eta_etapa', $eta_etapa);

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
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons"><?=$iconCard;?></i>
                  </div>
                  <h4 class="card-title">Lista MOF</h4>                  
                  <h4 align="right" >
                <!-- Lista de Configuracion Etapas -->
                <a style="height:10px;width: 10px; color: #9e38b4;background-color: #9e38b4;border-radius: 3px;border: 2px solid #9e38b4;" title="Configuración Etapas MOF" href='?opcion=listaConfiguracionEtapasMof'>
                  <i class="material-icons" style="color: #ffffff;">settings</i>
                </a>

                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table" id="tablePaginator">

                      <thead>
                        <tr>
                          <th width="10">#</th>
                          <th width="150">Nombre</th>
                          <th width="10">Estado de Mof</th>
                          <th width="30" class="text-center">Acciones</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php $index=1;
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { ?>
                          <tr>
                            <td><?=$index;?></td>
                            <td><?=$nombre;?></td>
                              <td>
                                <?php if(empty($ma_cod_estado)){ ?>
                                <span class="badge badge-md badge-warning">Sin procesar</span>
                                <?php }else{ ?>
                                <span class="badge badge-md badge-<?=$ma_color_estado?> btnVerHistorial" data-cod_mof="<?=$codigo;?>"><?=$ma_nombre_estado?></span>
                                <?php } ?>
                              </td>
                              <td class="td-actions text-right">
                              <?php
                                if($globalAdmin==1){
                              ?>
                                <!-- MANUAL DE APROBACIÓN -->
                                <?php
                                  if(empty($ma_cod_estado) || in_array($ma_cod_estado, [2, 3])){
                                ?>
                                  <!-- Inicializar -->
                                  <button class="btn btn-md btn-success btnIniciarAprobacion" data-cod_mof="<?=$codigo;?>" title="Iniciar Proceso de Aprobación">
                                    <i class="material-icons">play_for_work</i>
                                  </button>
                                <?php
                                  }else{
                                ?>
                                  <!-- En proceso de Aprobación -->
                                  <button class="btn btn-info btnVerHistorial" title="<?=$ma_nombre_estado?>: <?=$eta_etapa?>" data-cod_mof="<?=$codigo;?>">
                                    <div class="loading-dot"></div>
                                  </button>
                                <?php
                                  }
                                ?>

                                
                                <?php
                                  // No aparecerán las opciones en caso de tener el MOF
                                  // en proceso de APROBACIÓN
                                  if(empty($ma_cod_estado) || in_array($ma_cod_estado, [2, 3])){
                                ?>
                                
                                <!-- Editar -->
                                <button type="button" class="btn btn-info form_edit" data-codigo="<?=$codigo;?>" data-nombre="<?=$nombre;?>"><i class="material-icons" title="Editar"><?=$iconEdit;?></i></button>

                                <!-- Eliminar -->
                                <button class="<?=$buttonDelete;?> form_del">
                                  <i class="material-icons" title="Borrar"><?=$iconDelete;?></i>
                                </button>
                                <?php
                                  }
                                ?>

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
              </div>
              <?php
              }
              ?>
		  
            </div>
          </div>  
        </div>
    </div>
<!-- MODAL DE SEGUIMIENTO -->
<div class="modal fade modal-primary" id="modalHistorial" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content card">
      <div class="card-header card-header-info card-header-icon">
        <div class="card-icon">
          <i class="material-icons">track_changes</i>
        </div>
        <button type="button" class="close pt-2" data-dismiss="modal" aria-hidden="true">
          <i class="material-icons">close</i>
        </button>
        <h4 class="card-title" style="color: #333;font-weight: bold;">Seguimiento de Etapas MOF</h4>
      </div>

      <div class="card-body content-historial">
        <!-- CONTENIDO -->
      </div>

      <div class="modal-footer justify-content-end pt-0">
        <button type="button" class="btn btn-secondary" style="border: 1px solid #A9A9A9;" data-dismiss="modal">Cerrar</button>
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
                      <label class="col-sm-3"><span class="text-danger">*</span> Nombre :</label>
                      <div class="col-sm-9">
                          <input class="form-control" type="text" name="nombre" id="nombre" placeholder="Agregar nombre"/>
                      </div>
                      <br>
                      <label class="col-sm-3"><span class="text-danger">*</span> Archivo :</label>
                      <div class="col-sm-9">
                          <input class="form-control" type="file" id="archivo" placeholder="Agrear Archivo" accept=".pdf"/>
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
  /**
   * Primera etapa de probación
   */
  $(".btnIniciarAprobacion").click(function() {
    var cod_mof = $(this).data('cod_mof');
    Swal.fire({
      title: '¿Deseas iniciar la etapa de aprobación?',
      text: 'Esta acción iniciará la etapa de aprobación. ¿Estás seguro?',
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Sí, iniciar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.value) {
        // PROCESO
        $.ajax({
            url: "rrhh/ajaxMofAprobacionInicializacion.php",
            method: "POST",
            dataType: "json",
            data: {
              cod_mof: cod_mof
            },
            success: function(response) {
                // console.log(response)
                // return;
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
                        title: "Error",
                        text: response.message,
                        showConfirmButton: false,
                        timer: 3000
                    });
                }
            },
            error: function() {
                Swal.fire({
                    type: "error",
                    title: "Error",
                    text: "Ocurrió un error en la comunicación con el servidor",
                    showConfirmButton: false,
                    timer: 3000
                });
            }
        });
      }
    });
  });

  /**
   * Ver historial de estado de Mof de Aprobación
   */
  $('.btnVerHistorial').click(function(){
    let cod_mof = $(this).data('cod_mof');
    $.ajax({
      url: "rrhh/ajaxMofAprobacionEstadoHistorial.php",
      method: "POST",
      dataType: "html",
      data: {
        cod_mof: cod_mof
      },
      success: function(response) {
        console.log(response)
        $('.content-historial').html(response);
        $('#modalHistorial').modal('show');
      },
      error: function() {
          Swal.fire({
              type: "error",
              title: "Error",
              text: "Ocurrió un error en la comunicación con el servidor",
              showConfirmButton: false,
              timer: 3000
          });
      }
    });
  });
</script>
<script>
$(document).ready(function() {
  // Función para abrir el modal con campos en blanco
  $("#form_reg").click(function() {
    $("#modalAgregarEditarLabel").text("Agregar Registro");
    $("#registroId").val("");
    $("#nombre").val("");
    $("#archivo").val("");
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
    formData.append('archivo', $('#archivo')[0].files[0]);
    $.ajax({
        url: "rrhh/mofSave.php",
        method: "POST",
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json', // Indica que esperas una respuesta JSON
        success: function(response) {
            console.log(response);

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
});
</script>
