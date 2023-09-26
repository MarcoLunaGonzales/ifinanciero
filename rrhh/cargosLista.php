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
                        ma.cod_estado as ma_cod_estado,
                        mae.nombre as ma_nombre_estado,
                        mae.color as ma_color_estado,
                        CONCAT(eta.descripcion, ' (', eta.nombre, ')') as eta_etapa
                      FROM cargos c
                      LEFT JOIN cargos cpadre ON cpadre.codigo = c.cod_padre
                      LEFT JOIN cargos cfuncional ON cfuncional.codigo = c.cod_dep_funcional
                      LEFT JOIN tipos_cargos_personal tc ON tc.codigo = c.cod_tipo_cargo
                      LEFT JOIN (
                          SELECT
                              ma1.codigo,
                              ma1.cod_cargo,
                              ma1.cod_etapa,
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
                          <th width="10">Estado de Manual</th>
                          <th width="80" class="text-center">Acciones</th>
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
                              <td>
                                <?php if(empty($ma_cod_estado)){ ?>
                                <span class="badge badge-md badge-warning">Sin procesar</span>
                                <?php }else{ ?>
                                <span class="badge badge-md badge-<?=$ma_color_estado?> btnVerHistorial" data-cod_cargo="<?=$codigo;?>"><?=$ma_nombre_estado?></span>
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
                                  <button class="btn btn-md btn-success btnIniciarAprobacion" data-cod_cargo="<?=$codigo;?>" title="Iniciar Proceso de Aprobación">
                                    <i class="material-icons">play_for_work</i>
                                  </button>
                                <?php
                                  }else{
                                ?>
                                  <!-- En proceso de Aprobación -->
                                  <button class="btn btn-info btnVerHistorial" title="<?=$ma_nombre_estado?>: <?=$eta_etapa?>" data-cod_cargo="<?=$codigo;?>">
                                    <div class="loading-dot"></div>
                                  </button>
                                <?php
                                  }
                                ?>

                                <a href='<?=$urlCargosEscalaSalarial;?>&codigo=<?=$codigo;?>' class="btn btn-primary">
                                    <i class="material-icons" title="Escala Salarial">trending_up</i>
                                </a>

                                <!-- Reporte PDF -->
                                <a href='rrhh/pdfGeneracion.php?codigo=<?=$codigo;?>' target="_blank" class="btn btn-danger" title="Manual de Cargo">
                                  <i class="material-icons">picture_as_pdf</i>
                                </a>
                                
                                <?php
                                  // No aparecerán las opciones en caso de tener el manual de cargo
                                  // en proceso de APROBACIÓN
                                  if(empty($ma_cod_estado) || in_array($ma_cod_estado, [2, 3])){
                                ?>
                                <!-- Responsabilidades -->
                                <a href='<?=$urlCargosFunciones;?>&codigo=<?=$codigo;?>' class="btn btn-warning" title="Responsabilidades del Cargo">
                                  <i class="material-icons">assignment</i>
                                </a>

                                <!-- Autoridades -->
                                <a href='index.php?opcion=cargosAutoridades&codigo=<?=$codigo;?>' class="btn btn-info" title="Autoridades del Cargo" hidden>
                                  <i class="material-icons">list</i>
                                </a>
                                
                                <!-- Editar -->
                                <a href='<?=$urlFormCargos;?>&codigo=<?=$codigo;?>' class="<?=$buttonEdit;?>">
                                  <i class="material-icons" title="Editar"><?=$iconEdit;?></i>
                                </a>

                                <!-- Eliminar -->
                                <button class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDeleteCargos;?>&codigo=<?=$codigo;?>')">
                                  <i class="material-icons" title="Borrar"><?=$iconDelete;?></i>
                                </button>
                                <?php
                                  }
                                ?>
                                
                                <!-- Control de version de cambios -->
                                <a href='index.php?opcion=listaControlVersiones&codigo=<?=$codigo;?>' class="btn btn-primary" title="Control de Versión">
                                  <i class="material-icons">track_changes</i>
                                </a>
                                
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
        <button type="button" class="btn btn-secondary" style="border: 1px solid #A9A9A9;" data-dismiss="modal">Cerrar</button>
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
</script>