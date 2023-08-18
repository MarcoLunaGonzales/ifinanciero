<?php

require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();

$stmt = $dbh->prepare("SELECT
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
                        ma.estado as ma_estado
                      FROM cargos c
                      LEFT JOIN cargos cpadre ON cpadre.codigo = c.cod_padre
                      LEFT JOIN cargos cfuncional ON cfuncional.codigo = c.cod_dep_funcional
                      LEFT JOIN tipos_cargos_personal tc ON tc.codigo = c.cod_tipo_cargo
                      LEFT JOIN (
                          SELECT
                              codigo,
                              cod_cargo,
                              cod_etapa,
                              estado
                          FROM
                              manuales_aprobacion
                          ORDER BY
                              codigo DESC
                          LIMIT 1
                      ) ma ON ma.cod_cargo = c.codigo
                      WHERE c.cod_estadoreferencial = 1
                      ORDER BY c.nombre");
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('objetivo', $objetivo);
$stmt->bindColumn('abreviatura', $abreviatura);
$stmt->bindColumn('cod_tipo_cargo', $cod_tipo_cargo);
$stmt->bindColumn('nombre_tipo_cargo', $nombre_tipo_cargo);
$stmt->bindColumn('nombre_dependencia', $nombre_dependencia);
$stmt->bindColumn('nombre_dependencia_funcional', $nombre_dependencia_funcional);
$stmt->bindColumn('ma_codigo', $ma_codigo);
$stmt->bindColumn('ma_cod_etapa', $ma_cod_etapa);
$stmt->bindColumn('ma_estado', $ma_estado);

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
                  <h4 class="card-title"><?=$nombrePluralCargos?></h4>                  
                  <h4 align="right" >
                <a  style="height:10px;width: 10px; color: #ffffff;background-color: #1883ba;border-radius: 3px;border: 2px solid #1883ba;" href='<?=$urlCargoEscalaSalarialGeneral;?>' >
                  <i class="material-icons" title="Lista Escala Salarial General">trending_up</i>
                </a>  
                <!-- Lista de Cargos Inactivos -->
                <a  style="height:10px;width: 10px; color: #ffffff;background-color: #f44336;border-radius: 3px;border: 2px solid #f44336;" href='?opcion=cargosListaInactivo' title="Cargos Inactivos">
                  <i class="material-icons">list</i>
                </a>
                <!-- Lista de Configuracion Etapas -->
                <a style="height:10px;width: 10px; color: #9e38b4;background-color: #9e38b4;border-radius: 3px;border: 2px solid #9e38b4;" title="Configuración Etapas" href='?opcion=listaConfiguracionEtapas'>
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
                          <th width="230">Objetivo</th>
                          <th width="10">Abreviatura</th>
                          <th width="10">Nivel del Cargo</th>
                          <th width="10">Dependencia Jerárquica</th>
                          <th width="10">Dependencia Funcional</th>
                          <th width="80"></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php $index=1;
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { ?>
                          <tr>
                            <td><?=$index;?></td>
                            <td><?=$nombre;?></td>
                            <td><?=strlen($objetivo) > 100 ? (substr($objetivo, 0, 100) . "...") : $objetivo;?></td>
                              <td><?=$abreviatura;?></td>
                              <td><?=$nombre_tipo_cargo;?></td>
                              <td><?=$nombre_dependencia;?></td>
                              <td><?=$nombre_dependencia_funcional;?></td>
                              <td class="td-actions text-right">
                              <?php
                                if($globalAdmin==1){
                              ?>
                                <!-- MANUAL DE APROBACIÓN -->
                                <!-- Inicializar -->
                                <button class="btn btn-md btn-success btnIniciarAprobacion" data-cod_cargo="<?=$codigo;?>" title="Iniciar Proceso de Aprobación">
                                  <i class="material-icons">play_for_work</i>
                                </button>
                                <!-- Aprobar Manual -->
                                <button class="btn btn-md btn-success btnFormularioAprobacion" data-cod_cargo="<?=$codigo;?>" data-cod_manual_aprobacion="<?=$ma_codigo;?>" title="Procesar">
                                  <i class="material-icons">check</i>
                                </button>

                                <!-- Responsabilidades -->
                                <a href='<?=$urlCargosFunciones;?>&codigo=<?=$codigo;?>' class="btn btn-warning" title="Responsabilidades del Cargo">
                                  <i class="material-icons">assignment</i>
                                </a>
                                <!-- Autoridades -->
                                <a href='index.php?opcion=cargosAutoridades&codigo=<?=$codigo;?>' class="btn btn-info" title="Autoridades del Cargo">
                                  <i class="material-icons">list</i>
                                </a>

                                <a href='<?=$urlCargosEscalaSalarial;?>&codigo=<?=$codigo;?>' class="btn btn-primary">
                                    <i class="material-icons" title="Escala Salarial">trending_up</i>
                                </a>
                                <!-- Reporte PDF -->
                                <a href='rrhh/pdfGeneracion.php?codigo=<?=$codigo;?>' target="_blank" class="btn btn-danger" title="Manual de Cargo">
                                  <i class="material-icons">picture_as_pdf</i>
                                </a>

                                <a href='<?=$urlFormCargos;?>&codigo=<?=$codigo;?>' class="<?=$buttonEdit;?>">
                                  <i class="material-icons" title="Editar"><?=$iconEdit;?></i>
                                </a>
                                <button class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDeleteCargos;?>&codigo=<?=$codigo;?>')">
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
                    <!--<button class="<?=$buttonNormal;?>" onClick="location.href='index.php?opcion=registerUbicacion'">Registrar</button>-->
                    <button class="<?=$buttonNormal;?>" onClick="location.href='<?=$urlFormCargos;?>&codigo=0'">Registrar</button>
              </div>
              <?php
              }
              ?>
		  
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
      </div>

      <div class="card-body">
        <form id="formularioModificacionEtapa">
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
                    <i class="material-icons text-warning mr-2">event</i>
                    <b>Fecha de Modificación:</b>
                    <span id="textFecha"></span>
                  </div>
                  <div class="d-flex align-items-center">
                    <i class="material-icons text-success mr-2">person</i>
                    <b>Personal:</b>
                    <span id="textPersonal"></span>
                  </div>
                  <div class="d-flex align-items-center">
                    <i class="material-icons text-success mr-2">comment</i>
                    <b>Observación:</b>
                  </div>
                  <div class="align-items-center">
                    <p id="textObservacion"></p>
                  </div>
                </div>
                <div class="card-body historial2">
                  <p>No se han realizado cambios en el estado, la fecha de modificación, el personal ni la observación.</p>
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
                    <label class="col-sm-2 col-form-label">Estado:</label>
                    <div class="col-sm-9">
                      <div class="form-group">
                        <select name="manual_estado" id="manual_estado" data-style="btn btn-info" class="selectpicker form-control form-control-sm" data-show-subtext="true" data-live-search="true">
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
                <div class="card-footer">
                  <div class="col-md-12 text-right">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary btnModificarEstado">Guardar</button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="mb-3">
            <label for="pdfIframe" class="form-label">(PDF) Manual de Cargos:</label>
            <iframe id="pdfIframe" src="rrhh/pdfGeneracion.php?codigo=35" class="w-100" style="height: 500px;"></iframe>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>



<script>
  /**
   * Primera etapa de probación
   */
  $(".btnIniciarAprobacion").click(function() {
    var cod_cargo = $(this).data('cod_cargo');
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
            url: "rrhh/ajaxManualAprobacionInicializacion.php",
            method: "POST",
            dataType: "json",
            data: {
              cod_cargo: cod_cargo
            },
            success: function(response) {
                // console.log(response)
                // return;
                if (response.status) {
                    Swal.fire({
                        type: "success",
                        title: response.message,
                        showConfirmButton: false,
                        timer: 3000,
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
   * Segunda etapa de Aprobación segun las ETAPAS CONFIGURADAS
   */
  $(".btnFormularioAprobacion").click(function(){
    let cod_cargo = $(this).data('cod_cargo');
    let cod_manual_aprobacion = $(this).data('cod_manual_aprobacion');
    $('#cod_manual_aprobacion').val(cod_manual_aprobacion);
    // Actualiza documento de visualización
    $.ajax({
        url: "rrhh/ajaxManualAprobacionSeguimiento.php",
        method: "POST",
        dataType: "json",
        data: {
          cod_manual_aprobacion : cod_manual_aprobacion
        },
        success: function(response) {
            console.log(response.data)
            // Verificación de existencia de registros
            if(response.data.verf_row == 1){
              $('#textEstado').html(response.data.estado);
              $('#textFecha').html(response.data.fecha);
              $('#textPersonal').html(response.data.personal);
              $('#textObservacion').html(response.data.observacion);
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
      text: 'Esta acción el estado de aprobación del manual. ¿Estás seguro?',
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
            url: "rrhh/ajaxManualAprobacionEstadoSave.php",
            method: "POST",
            dataType: "json",
            data: {
              cod_manual_aprobacion: cod_manual_aprobacion,
              manual_estado: manual_estado,
              manual_observacion: manual_observacion
            },
            success: function(response) {
                console.log(response)
                return;
                if (response.status) {
                    Swal.fire({
                        type: "success",
                        title: response.message,
                        showConfirmButton: false,
                        timer: 3000,
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
</script>