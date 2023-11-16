<?php

require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'styles.php';
date_default_timezone_set('America/La_Paz');

$dbh = new Conexion();
// Credenciales de INTRANET
$accesos_externos = '';
$q = isset($_GET['q']) ? $_GET['q'] : '';

if (isset($q)) {
    $accesos_externos = "?q=" . $q;
}

$globalUsuario = '';
$globalArea  = '';
$globalCargo = '';

if (empty($q)) {
    $globalUsuario = $_SESSION["globalUser"];
    $globalArea    = $_SESSION["globalArea"];
    $globalCargo   = $_SESSION["globalCargo"];
} else {
    $sql = "SELECT p.codigo, p.cod_area, p.cod_cargo
            FROM personal p
            WHERE p.codigo = :codigo
            LIMIT 1";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':codigo', $q, PDO::PARAM_STR);
    $stmt->execute();
    $registro = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($registro) {
        $globalUsuario = $registro['codigo'];
        $globalArea    = $registro['cod_area'];
        $globalCargo   = $registro['cod_cargo'];
    }
}
/**
 * Verificación de asginación de cargo Interino
 * En caso de encontrar un valor, se adecua al 
 * cambio del cargo interino asignado
 */
$fecha_actual = date('Y-m-d');
$sql = "SELECT cih.cod_cargo as cargo_interino
        FROM cargos_interinos_historicos cih
        WHERE cih.cod_personal = '$globalUsuario'
        AND cih.fecha_inicio <= '$fecha_actual'
        AND cih.fecha_fin >= '$fecha_actual'
        AND cih.estado = 1
        LIMIT 1";
$stmt = $dbh->prepare($sql);
$stmt->bindParam(':codigo', $q, PDO::PARAM_STR);
$stmt->execute();
$registro = $stmt->fetch(PDO::FETCH_ASSOC);
if ($registro) {
  $globalCargo = $registro['cargo_interino'];
}

/**
 * Obtiene lista de ETAPA 1 en base al "Codigo de Area"
 */
$sql = "SELECT
        c.codigo,
        UPPER(c.nombre) AS nombre,
        c.objetivo,
        c.abreviatura,
        c.cod_tipo_cargo,
        tc.nombre AS nombre_tipo_cargo,
        UPPER(cpadre.nombre) AS nombre_dependencia,
        UPPER(cfuncional.nombre) AS nombre_dependencia_funcional,
        ma.codigo as ma_codigo,
        ma.cod_etapa as ma_cod_etapa,
        ma.cod_estado as ma_cod_estado,
        mae.nombre as ma_nombre_estado,
        mae.color as ma_color_estado
        FROM cargos c
        LEFT JOIN cargos cpadre ON cpadre.codigo = c.cod_padre
        LEFT JOIN cargos cfuncional ON cfuncional.codigo = c.cod_dep_funcional
        LEFT JOIN tipos_cargos_personal tc ON tc.codigo = c.cod_tipo_cargo
        LEFT JOIN (
            SELECT
                ma1.codigo,
                ma1.cod_cargo,
                ma1.cod_etapa,
                ma1.cod_area,
                ma1.cod_estado
            FROM manuales_aprobacion ma1
            INNER JOIN (
                SELECT
                    cod_cargo,
                    MAX(codigo) AS max_codigo
                FROM manuales_aprobacion
                GROUP BY cod_cargo
            ) max_ma ON ma1.cod_cargo = max_ma.cod_cargo AND ma1.codigo = max_ma.max_codigo
        ) ma ON ma.cod_cargo = c.codigo
        LEFT JOIN manuales_aprobacion_estados mae ON mae.codigo = ma.cod_estado
        WHERE c.cod_estadoreferencial = 1
        AND ma.cod_estado = 1
        AND ma.cod_etapa = 1
        AND ma.cod_area = '$globalArea'
        ORDER BY c.nombre";
// echo $sql;
$stmt = $dbh->prepare($sql);
//ejecutamos
$stmt->execute();
$resultadoBaseArea = $stmt->fetchAll(PDO::FETCH_ASSOC);

/**
 * Obtiene lista de ETAPAS SUPERIORES en base al "Codigo de Cargo"
 */
$sql = "SELECT
        c.codigo,
        UPPER(c.nombre) AS nombre,
        c.objetivo,
        c.abreviatura,
        c.cod_tipo_cargo,
        tc.nombre AS nombre_tipo_cargo,
        UPPER(cpadre.nombre) AS nombre_dependencia,
        UPPER(cfuncional.nombre) AS nombre_dependencia_funcional,
        ma.codigo as ma_codigo,
        ma.cod_etapa as ma_cod_etapa,
        ma.cod_estado as ma_cod_estado,
        mae.nombre as ma_nombre_estado,
        mae.color as ma_color_estado
        FROM cargos c
        LEFT JOIN cargos cpadre ON cpadre.codigo = c.cod_padre
        LEFT JOIN cargos cfuncional ON cfuncional.codigo = c.cod_dep_funcional
        LEFT JOIN tipos_cargos_personal tc ON tc.codigo = c.cod_tipo_cargo
        LEFT JOIN (
            SELECT
                ma1.codigo,
                ma1.cod_cargo,
                ma1.cod_etapa,
                ma1.cod_area,
                ma1.cod_estado
            FROM manuales_aprobacion ma1
            INNER JOIN (
                SELECT
                    cod_cargo,
                    MAX(codigo) AS max_codigo
                FROM manuales_aprobacion
                GROUP BY cod_cargo
            ) max_ma ON ma1.cod_cargo = max_ma.cod_cargo AND ma1.codigo = max_ma.max_codigo
        ) ma ON ma.cod_cargo = c.codigo
        LEFT JOIN manuales_aprobacion_estados mae ON mae.codigo = ma.cod_estado
        LEFT JOIN manuales_aprobacion_etapas eta ON eta.codigo = ma.cod_etapa
        WHERE c.cod_estadoreferencial = 1
        AND ma.cod_estado = 1
        AND ma.cod_etapa > 1
        AND eta.cod_cargo = '$globalCargo'
        ORDER BY c.nombre";
// echo $sql;
$stmt = $dbh->prepare($sql);
//ejecutamos
$stmt->execute();
$resultadoBaseCargo = $stmt->fetchAll(PDO::FETCH_ASSOC);

$results = array_merge($resultadoBaseArea, $resultadoBaseCargo);
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
            <h4 class="card-title">Lista de Aprobación de Manuales</h4>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table" id="tablePaginator">

                <thead>
                  <tr>
                    <th width="10">#</th>                        
                    <th width="150">Nombre</th>
                    <th width="230">Objetivo</th>
                    <th width="10">Abreviatura</th>
                    <th width="10">Nivel del Cargo</th>
                    <th width="10">Dependencia Jerárquica</th>
                    <th width="10">Dependencia Funcional</th>
                    <th width="80" class="text-center">Acciones</th>
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
                      <td><?=strlen($row['objetivo']) > 100 ? (substr($row['objetivo'], 0, 100) . "...") : $row['objetivo'];?></td>
                        <td><?=$row['abreviatura'];?></td>
                        <td><?=$row['nombre_tipo_cargo'];?></td>
                        <td><?=$row['nombre_dependencia'];?></td>
                        <td><?=$row['nombre_dependencia_funcional'];?></td>
                        <td class="td-actions text-center">
                          
                          <!-- MANUAL DE APROBACIÓN -->
                          <!-- Aprobar Manual -->
                          <button class="btn btn-md btn-success btnFormularioAprobacion" data-cod_cargo="<?=$row['codigo'];?>" data-cod_manual_aprobacion="<?=$row['ma_codigo'];?>" title="Procesar">
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
        <h4 class="card-title modal_titulo" style="color: #333;font-weight: bold;">Modificar Estado de Manual</h4>
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
          <input type="hidden" name="cod_manual_aprobacion" id="cod_manual_aprobacion" value="0">
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
                <button type="button" class="btn btn-secondary btn-block btnVerHistorial" style="border: 1px solid #A9A9A9;" data-dismiss="modal"><i class="material-icons">schedule</i> Ver más</button>
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
                            <select name="manual_estado" id="manual_estado" data-style="btn btn-default" class="selectpicker form-control form-control-sm" data-show-subtext="true" data-live-search="true">
                              <?php 
                                $sql="SELECT ase.codigo, ase.nombre, ase.descripcion
                                      FROM manuales_aprobacion_seguimiento_estados ase
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
                            <textarea class="form-control" name="manual_observacion" id="manual_observacion" rows="3" required></textarea>
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
        <h4 class="card-title" style="color: #333;font-weight: bold;">Seguimiento de Etapas</h4>
      </div>

      <div class="card-body content-historial">
        <!-- CONTENIDO -->
      </div>

      <div class="modal-footer justify-content-end pt-0">
        <button type="button" class="btn btn-secondary modal_etapa_cerrar" style="border: 1px solid #A9A9A9;" data-dismiss="modal">Volver</button>
      </div>
    </div>
  </div>
</div>


<script>
  /**
   * Segunda etapa de Aprobación segun las ETAPAS CONFIGURADAS
   */
  $(".btnFormularioAprobacion").click(function(){
    let cod_cargo = $(this).data('cod_cargo');
    let cod_manual_aprobacion = $(this).data('cod_manual_aprobacion');
    $('#cod_manual_aprobacion').val(cod_manual_aprobacion);
    // Se actualiza boton de estado de Manual de Aprobación
    $(".btnVerHistorial").attr("data-cod_cargo", cod_cargo);
    // Actualiza documento de visualización
    $.ajax({
        url: "rrhh/ajaxManualAprobacionSeguimiento.php",
        method: "POST",
        dataType: "json",
        data: {
          cod_manual_aprobacion : cod_manual_aprobacion
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

            var nuevaURL = 'rrhh/pdfGeneracion.php?codigo=' + cod_cargo;
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
    var cod_manual_aprobacion = $('#cod_manual_aprobacion').val();
    var manual_estado         = $('#manual_estado').val();
    var manual_observacion    = $('#manual_observacion').val();
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
            url: "rrhh/ajaxManualAprobacionEstadoSave.php<?=$accesos_externos;?>",
            method: "POST",
            dataType: "json",
            data: {
              cod_manual_aprobacion: cod_manual_aprobacion,
              manual_estado: manual_estado,
              manual_observacion: manual_observacion
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
  
  /**
   * Ver historial de estado de Manual de Aprobación
   */
  $('.btnVerHistorial').click(function(){
    let cod_cargo = $(this).data('cod_cargo');
    $.ajax({
      url: "rrhh/ajaxManualAprobacionEstadoHistorial.php",
      method: "POST",
      dataType: "html",
      data: {
        cod_cargo: cod_cargo
      },
      success: function(response) {
        // console.log(response)
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

  /**
   * Cierra modal de Historial de Seguimiento de Etapas
   */
  $('.modal_etapa_cerrar').click(function(){
    setTimeout(function() {
        $('#modalAprobacion').modal('show');
    }, 500);
  });
</script>