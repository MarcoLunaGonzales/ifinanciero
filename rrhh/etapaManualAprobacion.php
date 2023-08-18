<?php

require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();

$sql  = "SELECT mae.codigo, mae.cod_etapa, mae.cod_cargo, maes.nombre as nombre_dependiente, c.nombre as cargo_nombre, mae.nombre, mae.descripcion, mae.nro_etapa, mae.estado
        FROM manuales_aprobacion_etapas mae
        LEFT JOIN manuales_aprobacion_etapas maes ON maes.codigo = mae.cod_etapa
        LEFT JOIN cargos c ON c.codigo = mae.cod_cargo";
$stmt = $dbh->prepare($sql);
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('cod_etapa', $cod_etapa);
$stmt->bindColumn('cod_cargo', $cod_cargo);
$stmt->bindColumn('nombre_dependiente', $nombre_dependiente);
$stmt->bindColumn('cargo_nombre', $cargo_nombre);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('descripcion', $descripcion);
$stmt->bindColumn('nro_etapa', $nro_etapa);
$stmt->bindColumn('estado', $estado);

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
                  <h4 class="card-title">Lista de Etapas</h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table" id="tablePaginator">

                      <thead>
                        <tr>
                          <th width="10">#</th>                        
                          <th width="150">Nombre</th>
                          <th width="230">Descripción</th>
                          <th width="10">Cargo</th>
                          <th width="10">Etapa dependiente</th>
                          <th width="10" class="text-center">Nro. Etapa</th>
                          <th width="80" class="text-center">Acciones</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php $index=1;
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { ?>
                          <tr>
                              <td><?=$index;?></td>
                              <td><?=$nombre;?></td>
                              <td><?=$descripcion;?></td>
                              <td><?=strlen($cargo_nombre) > 100 ? (substr($cargo_nombre, 0, 100) . "...") : $cargo_nombre;?></td>
                              <td><?=$nombre_dependiente;?></td>
                              <td class="text-center"><?=$nro_etapa;?></td>
                              <td class="td-actions text-center">
                              <?php
                                if($globalAdmin==1){
                              ?>
                                <button rel="tooltip" 
                                      class="btn btn-md btn-info editar" 
                                      data-codigo="<?=$codigo?>"
                                      data-cod_etapa="<?=$cod_etapa?>"
                                      data-cod_cargo="<?=$cod_cargo?>"
                                      data-nombre="<?=$nombre?>"
                                      data-descripcion="<?=$descripcion?>"
                                      data-nro_etapa="<?=$nro_etapa?>">
                                  <i class="material-icons" title="Borrar">edit</i>
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
                    <button class="<?=$buttonNormal;?>" id="registrar">Registrar</button>
                    <a href="?opcion=cargosLista" class="btn btn-danger">
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


<!-- MODAL REGISTRO Y ACTUALIZACIÓN -->
<div class="modal fade" id="modalFormulario" tabindex="-1" role="dialog" aria-labelledby="modal_titulo" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content card">
      <div class="card-header card-header-warning card-header-icon">
        <div class="card-icon">
          <i class="material-icons">settings_applications</i>
        </div>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          <i class="material-icons">close</i>
        </button>
        <h4 class="card-title modal_titulo"></h4>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-12">
            <form id="formularioRegistrarEtapa">
              <!-- CODIGO DE REGISTRO -->
              <input type="hidden" name="codigo" id="codigo" value="0"/>
              <input type="hidden" name="metodo" id="metodo" value="0"/>
              <div class="row">
                  <label class="col-sm-2 col-form-label">Dependiente de:</label>
                  <div class="col-sm-9">
                  <div class="form-group">
                    <!-- cod_etapa DEPENDIENTE -->
                    <select name="cod_etapa" id="cod_etapa" data-style="btn btn-info" class="selectpicker form-control form-control-sm" data-show-subtext="true" data-live-search="true">
                        <option value="">Ninguno</option>
                        <?php 
                          $sqlAreas="SELECT mae.codigo, mae.nombre
                                    FROM manuales_aprobacion_etapas mae 
                                    WHERE mae.estado = 1
                                    ORDER BY mae.codigo DESC";
                          $stmtListaCargos=$dbh->query($sqlAreas);
                          while ($row = $stmtListaCargos->fetch()) { ?>
                            <option value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                        <?php } ?>
                    </select>
                  </div>
                  </div>
              </div>
              <div class="row">
                <label class="col-sm-2 col-form-label">Cargo:</label>
                <div class="col-sm-9">
                  <div class="form-group">
                    <select name="cod_cargo" id="cod_cargo" data-style="btn btn-info" class="selectpicker form-control form-control-sm" data-show-subtext="true" data-live-search="true">
                        <option value="">Ninguno</option>
                        <?php 
                          $sqlAreas="SELECT codigo, nombre, abreviatura
                                    FROM cargos 
                                    WHERE cod_estadoreferencial = 1
                                    ORDER BY nombre";
                          $stmtListaCargos=$dbh->query($sqlAreas);
                          while ($row = $stmtListaCargos->fetch()) { ?>
                            <option value="<?=$row["codigo"];?>">[<?=$row["abreviatura"];?>] - <?=$row["nombre"];?></option>
                        <?php } ?>
                    </select>
                  </div>
                </div>
              </div>
              <div class="row">
                  <label class="col-sm-2 col-form-label">Nombre:</label>
                  <div class="col-sm-9">
                  <div class="form-group">
                      <input class="form-control" type="text" name="nombre" id="nombre" required="true" value="" onkeyup="javascript:this.value=this.value.toUpperCase();" placeholder="Ingresar nombre"/>
                  </div>
                  </div>
              </div>
              <div class="row">
                <label class="col-sm-2 col-form-label">Descripcion:</label>
                <div class="col-sm-9">
                <div class="form-group">
                    <input class="form-control" type="text" name="descripcion" id="descripcion" required="true" value="" onkeyup="javascript:this.value=this.value.toUpperCase();" placeholder="Ingresar descripción"/>
                </div>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-2 col-form-label">Nro. Etapa:</label>
                <div class="col-sm-9">
                  <div class="form-group">
                      <input class="form-control" type="text" name="nro_etapa" id="nro_etapa" required="true" placeholder="Ingresar número de etapa"/>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
      <div class="card-footer">
          <div class="col-md-12 text-right">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            <button type="button" class="btn btn-primary" id="btnGuardar">Guardar</button>
          </div>
      </div>
    </div>
  </div>
</div>



<script>
  /**
   * Abre modal Registro
   */
  $('#registrar').on('click', function(){
    // Valores
    $('#codigo').val('');
    $("#cod_etapa").val('').trigger("change");
    $('#cod_cargo').val('').trigger("change");
    $('#nombre').val('');
    $('#descripcion').val('');
    $('#nro_etapa').val('');
    // Valores FIN
    $('.modal_titulo').html('Registrar Etapa');
    $('#metodo').val(1);
    $('#modalFormulario').modal('show');
  });
  /**
   * Abre modal Actualización
   */
  $('.editar').on('click', function(){
    // Valores
    $('#codigo').val($(this).data('codigo'));
    $("#cod_etapa").val($(this).data('cod_etapa')).trigger("change");
    $('#cod_cargo').val($(this).data('cod_cargo')).trigger("change");
    $('#nombre').val($(this).data('nombre'));
    $('#descripcion').val($(this).data('descripcion'));
    $('#nro_etapa').val($(this).data('nro_etapa'));
    // Valores FIN
    $('.modal_titulo').html('Editar Etapa');
    $('#metodo').val(2);
    $('#modalFormulario').modal('show');
  });
  /**
   * Guardar o actualizar
   */
  $("#btnGuardar").click(function() {
      // Tipo formulario (1: Registro, 2: Actualización)
      var metodo      = $("#metodo").val();
      // Obtén los valores de los campos
      var codigo      = $("#codigo").val();
      var cod_etapa   = $("#cod_etapa").val();
      var cod_cargo   = $("#cod_cargo").val();
      var nombre      = $("#nombre").val();
      var descripcion = $("#descripcion").val();
      var nro_etapa   = $("#nro_etapa").val();
      // Verificar si algún campo está vacío
      if (nombre === '' || descripcion === '' || nro_etapa === '') {
        Swal.fire({
            type: "warning",
            title: "Ops!",
            text: "Llenas los campos requeridos.",
            showConfirmButton: false,
            timer: 3000
        });
        return;
      }
      // PROCESO
      $.ajax({
          url: "rrhh/ajaxManualAprobacionEtapaSave.php",
          method: "POST",
          dataType: "json",
          data: {
            codigo: codigo,
            metodo: metodo,
            cod_etapa: cod_etapa,
            cod_cargo: cod_cargo,
            nombre: nombre,
            descripcion: descripcion,
            nro_etapa: nro_etapa
          },
          success: function(response) {
              // console.log(response)
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
    });
</script>