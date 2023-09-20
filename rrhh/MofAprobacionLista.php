<?php

require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin = $_SESSION["globalAdmin"];

$globalArea  = $_SESSION["globalArea"];
$globalCargo = $_SESSION["globalCargo"];

$dbh = new Conexion();

/**
 * Obtiene lista de ETAPAS SUPERIORES en base al "Codigo de Cargo"
 */
$sql = "SELECT
        m.codigo,
        UPPER(m.nombre) AS nombre,
        m.archivo,
        ma.codigo as ma_codigo,
        ma.cod_etapa as ma_cod_etapa,
        ma.cod_estado as ma_cod_estado,
        mae.nombre as ma_nombre_estado,
        mae.color as ma_color_estado
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
        AND ma.cod_estado = 1
        AND eta.cod_cargo = '$globalCargo'
        ORDER BY m.nombre";
// echo $sql;
$stmt = $dbh->prepare($sql);
//ejecutamos
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
            <h4 class="card-title">Lista de Aprobación de MOF</h4>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table" id="tablePaginator">

                <thead>
                  <tr>
                    <th width="10">#</th>
                    <th width="200">Nombre</th>
                    <th width="10" class="text-center">Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                    $index = 1;
                    foreach ($results as $row) {
                  ?>
                    <tr>
                      <td><?=$index;?></td>
                      <td><?=$row['nombre'];?></td>
                      <td class="td-actions text-center">
                        
                        <!-- MANUAL DE APROBACIÓN -->
                        <!-- Aprobar Manual -->
                        <button class="btn btn-md btn-success btnFormularioAprobacion" data-cod_mof="<?=$row['codigo'];?>" data-cod_mof_aprobacion="<?=$row['ma_codigo'];?>" data-archivo="<?=$row['archivo'];?>" title="Procesar">
                          <i class="material-icons">autorenew</i>
                        </button>
                      
                      </td>
                    </tr>
                  <?php $index++; } ?>
                </tbody>
              
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<!-- MODAL DE SEGUIMIENTO DE ESTADO -->
<div class="modal fade modal-primary" id="modalAprobacion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content card">
      <div class="card-header card-header-warning card-header-icon">
        <div class="card-icon">
          <i class="material-icons">settings_applications</i>
        </div>
        <button type="button" class="close pt-2" data-dismiss="modal" aria-hidden="true">
          <i class="material-icons">close</i>
        </button>
        <h4 class="card-title modal_titulo" style="color: #333;font-weight: bold;">Modificar Estado de Manual - MOF</h4>
      </div>

      <div class="card-body">
        <form id="formularioModificacionEtapa">
          <div class="mb-3">
            <div class="d-flex align-items-center pb-2  ">
              <i class="material-icons text-danger mr-2">picture_as_pdf</i>
              <b>Documento de Manual de Cargo:</b>
            </div>
            <iframe id="pdfIframe" src="rrhh/pdfGeneracion.php?codigo=35" class="w-100" style="height: 500px;"></iframe>
          </div>
          <!-- CODIGO DE MANUAL APROBACIÓN -->
          <input type="hidden" name="cod_mof_aprobacion" id="cod_mof_aprobacion" value="0">
          <div class="row">
            <div class="col-md-4">
              <div class="card">
                <div class="card-header card-header-info">
                  <h4 class="card-title">Último estado</h4>
                </div>
                <div class="card-body historial1">
                  <div class="d-flex align-items-center">
                    <i class="material-icons text-primary mr-2">info</i>
                    <b>Estado:</b> 
                    <span id="textEstado"></span>
                  </div>
                  <div class="d-flex align-items-center">
                    <i class="material-icons text-info mr-2">format_list_numbered</i>
                    <b>Nro. revisión</b>
                    <span id="textVersion" class="badge badge-secondary"></span>
                  </div>
                  <div class="d-flex align-items-center">
                    <i class="material-icons text-warning mr-2">event</i>
                    <b>Fecha de Modificación:</b>
                    <span id="textFecha"></span>
                  </div>
                  <div class="d-flex align-items-center">
                    <i class="material-icons text-success mr-2">person</i>
                    <b>Encargado:</b>
                    <span id="textPersonal"></span>
                  </div>
                  <div class="d-flex align-items-center">
                    <i class="material-icons text-danger mr-2">comment</i>
                    <b>Observación:</b>
                  </div>
                  <div class="align-items-center">
                    <p id="textObservacion"></p>
                  </div>
                </div>
                <div class="card-body historial2">
                  <p><i class="material-icons text-default mr-2">comment</i> No se han efectuado cambios de estado en el Manual.</p>
                </div>
              </div>
            </div>

            <div class="col-md-8">
              <div class="card">
                <div class="card-header card-header-success">
                  <h4 class="card-title"><i class="material-icons">timeline</i> Proceso de Aprobación</h4>
                </div>
                <div class="card-body">
                  <div class="row">
                    <!-- Campos -->
                    <div class="col-md-9">
                      <div class="row">
                        <label class="col-sm-2 col-form-label">Estado (*):</label>
                        <div class="col-sm-9">
                          <div class="form-group">
                            <select name="estado" id="estado" data-style="btn btn-default" class="selectpicker form-control form-control-sm" data-show-subtext="true" data-live-search="true">
                              <?php 
                                $sql="SELECT ase.codigo, ase.nombre, ase.descripcion
                                      FROM mof_aprobacion_seguimiento_estados ase
                                      WHERE estado = 1
                                      ORDER BY ase.codigo ASC";
                                $stmt = $dbh->query($sql);
                                while ($row = $stmt->fetch()) { ?>
                                  <option value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                              <?php } ?>
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <label class="col-sm-2 col-form-label">Observación:</label>
                        <div class="col-sm-9">
                          <div class="form-group">
                            <textarea class="form-control" name="observacion" id="observacion" rows="3" required></textarea>
                          </div>
                        </div>
                      </div>
                    </div>
                    <!-- Botones -->
                    <div class="col-md-3">
                      <div class="row">
                        <div class="col-md-12 mb-1">
                          <button type="button" class="btn btn-primary btn-block btnModificarEstado">Actualizar</button>
                        </div>
                        <div class="col-md-12">
                          <button type="button" class="btn btn-secondary btn-block" style="border: 1px solid #A9A9A9;" data-dismiss="modal">Cancelar</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>



<script>
  /**
   * Segunda etapa de Aprobación segun las ETAPAS CONFIGURADAS
   */
  $(".btnFormularioAprobacion").click(function(){
    let archivo = $(this).data('archivo');
    let cod_mof = $(this).data('cod_mof');
    let cod_mof_aprobacion = $(this).data('cod_mof_aprobacion');
    $('#cod_mof_aprobacion').val(cod_mof_aprobacion);
    // Actualiza documento de visualización
    $.ajax({
        url: "rrhh/ajaxMofAprobacionSeguimiento.php",
        method: "POST",
        dataType: "json",
        data: {
          cod_mof_aprobacion : cod_mof_aprobacion
        },
        success: function(response) {
            // console.log(response.data)
            // Verificación de existencia de registros
            if(response.data.verf_row == 1){
              $('#textEstado').html(response.data.estado);
              $('#textFecha').html(response.data.fecha);
              $('#textPersonal').html(response.data.personal);
              $('#textObservacion').html(response.data.observacion);
              $('#textVersion').html(response.data.nro_version);
              $(".historial1").show();
              $(".historial2").hide();
            }else if(response.data.verf_row == 0){
              $(".historial2").show();
              $(".historial1").hide();
            }

            var nuevaURL = 'assets/archivos_mof/' + archivo;
            $('#pdfIframe').attr('src', nuevaURL);
            $('#modalAprobacion').modal('show')
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
  /**
   * Almacenamiento de estado
   */
  $(".btnModificarEstado").click(function() {
    var cod_mof_aprobacion = $('#cod_mof_aprobacion').val();
    var estado             = $('#estado').val();
    var observacion        = $('#observacion').val();
    Swal.fire({
      title: '¿Desea actualizar estado?',
      text: 'Esta acción modificará el estado de la Aprobación del Manual. ¿Estás seguro?',
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
            url: "rrhh/ajaxMofAprobacionEstadoSave.php",
            method: "POST",
            dataType: "json",
            data: {
              cod_mof_aprobacion: cod_mof_aprobacion,
              estado: estado,
              observacion: observacion
            },
            success: function(response) {
                // console.log(response)
                // return;
                $('#modalAprobacion').modal('toggle');
                if (response.status) {
                    Swal.fire({
                        type: "success",
                        title: response.message,
                        showConfirmButton: false,
                        timer: 1500,
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
                $('#modalAprobacion').modal('toggle');
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
</script>