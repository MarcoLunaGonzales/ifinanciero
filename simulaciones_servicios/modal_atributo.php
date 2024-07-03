<?php
    /****************************************
     * Nueva sección para ADICIONAR O QUITAR
     * SITIOS o PRODUCTOS
     ****************************************/
    $array_excepciona_atr    = ['4822'];
    $tipo_servicio           = $idTipoServicioX;
    $cod_simulacion_servicio = $codigo;
    $tipo_modal              = 1;               // 1: SITIOS, 2: PRODUCTOS
    $titulo_form             = '';
    $tipo_atributo           = 0;

    if($codigoPlan == 3){
        // TCS
        $tipo_atributo = 2;
    }else if($codigoPlan == 2 || $codigoPlan == 10){
        // TCP
        $tipo_atributo = 1;
    }

    if ($codigoPlan == 3 || (in_array($tipo_servicio, $array_excepciona_atr) && ($codigoPlan == 2 || $codigoPlan == 10))) {
        $tipo_modal = 1;
        $campos_modal_sitio      = '';
        $campos_modal_producto   = 'hidden';
        $titulo_form             = 'Sitio';
    } else if ($codigoPlan == 2 || $codigoPlan == 10) {
        $tipo_modal = 2;
        $campos_modal_sitio      = 'hidden';
        $campos_modal_producto   = '';
        $titulo_form             = 'Producto';
    }
?>
<!-- MODAL LISTA PARA PRODUCTOS|SITIOS -->
<div class="modal fade modal-primary" id="modal_lista_atributo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content card">
            <div class="card-header card-header-primary card-header-text">
                <div class="card-text">
                    <h4 class="card-title">LISTA DE 
                        <?php 
                        if ($tipo_modal == 1) {
                            echo 'SITIOS';
                        } else if ($tipo_modal == 2) {
                            echo 'PRODUCTOS';
                        } 
                        ?>
                    </h4>
                </div>
                <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
                    <i class="material-icons">close</i>
                </button>
            </div>
            <div class="card-body" id="card_lista_atributo" style="overflow-y: auto;">
            </div>
        </div>
    </div>
</div>


<!-- MODAL PARA PRODUCTOS - SITIOS -->
<div class="modal fade modal-primary" id="modal_gestion_atributo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content card">
            <div class="card-header card-header-primary card-header-text">
                <div class="card-text">
                    <h4 class="card-title" id="modal_form_atr"></h4>
                </div>
                <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
                    <i class="material-icons">close</i>
                </button>
            </div>
            <div class="card-body">
                <!-- Tipo de modal: 1 Sitio; 2 Producto -->
                <input type="hidden" id="form_tipo_atr" value="<?=$tipo_modal?>">
                <!-- cod_simulacionservicios_atributo -->
                <input type="hidden" id="cod_simulacionservicio_atributo" value="0">
                <!-- CAMPOS DE PRODUCTOS -->
                <div class="row" <?=$campos_modal_producto?>>
                    <div class="row col-sm-12">
                        <label class="col-sm-2 col-form-label">Producto</label>
                        <div class="col-sm-9">                     
                            <div class="form-group bmd-form-group">
                                <input type="text" class="form-control" name="atr_map_producto" id="atr_map_producto" value="" onkeyup="javascript:this.value=this.value.toUpperCase();">
                            </div>
                        </div>
                    </div> 
                </div>
                <div class="row" <?=$campos_modal_producto?>>
                    <div class="row col-sm-12">
                        <label class="col-sm-2 col-form-label">Marca</label>
                        <div class="col-sm-9">                     
                            <div class="form-group bmd-form-group">
                                <input type="text" class="form-control" name="atr_map_marca" id="atr_map_marca" value="" onkeyup="javascript:this.value=this.value.toUpperCase();">
                            </div>
                        </div>  
                    </div>
                </div>
                <div class="row" <?=$campos_modal_producto?>>
                    <div class="row col-sm-12">
                        <label class="col-sm-2 col-form-label">Nº Sello</label>
                        <div class="col-sm-9">                     
                            <div class="form-group bmd-form-group">
                                <input type="number" class="form-control" name="atr_map_sello" id="atr_map_sello" value="" onkeyup="javascript:this.value=this.value.toUpperCase();">
                            </div>
                        </div> 
                    </div>
                </div>
                <!-- NORMAS -->
                <div class="row" <?=$campos_modal_producto?>>
                    <label class="col-sm-2 col-form-label">Normas Nacionales:</label>
                    <div class="col-sm-9">
                        <div class="form-group">
                            <select class="selectpicker form-control" name="atr_map_norma_nac_cod[]" id="atr_map_norma_nac_cod" multiple data-style="btn btn-warning" data-actions-box="true" data-live-search="true" data-size="6" required>
                            <?php
                                $stmt = $dbh->prepare("SELECT vn.codigo, vn.abreviatura, vn.nombre, 'L' as tipo from v_normas vn where vn.cod_estado=1 order by 4,2");
                                $stmt->execute();
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $codigoX    = $row['codigo'];
                                    $nombreX    = $row['nombre'];
                                    $tipoX      = $row['tipo'];
                                    $abrevX     = $row['abreviatura']." (".$tipoX.")";
                                    $nombreX    = substr($nombreX, 0, 70);
                            ?>
                            <option value="<?=$codigoX;?>" data-subtext="<?=$nombreX;?>"><?=$abrevX;?></option> 
                            <?php
                            }
                            ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row" <?=$campos_modal_producto?>>
                    <label class="col-sm-2 col-form-label">Normas Internacionales:</label>
                    <div class="col-sm-9">
                        <div class="form-group">
                            <select class="selectpicker form-control" name="atr_map_norma_int_cod[]" id="atr_map_norma_int_cod" multiple data-style="btn btn-warning" data-actions-box="true" data-live-search="true" data-size="6" required>
                            <?php
                                $stmt = $dbh->prepare("SELECT vi.codigo, vi.abreviatura, vi.nombre, 'I' as tipo from v_normas_int vi where vi.cod_estado=1 order by 4,2");
                                $stmt->execute();
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $codigoX    = $row['codigo'];
                                    $nombreX    = $row['nombre'];
                                    $tipoX      = $row['tipo'];
                                    $abrevX     = $row['abreviatura']." (".$tipoX.")";
                                    $nombreX    = substr($nombreX, 0, 70);
                            ?>
                            <option value="<?=$codigoX;?>" data-subtext="<?=$nombreX;?>"><?=$abrevX;?></option> 
                            <?php
                            }
                            ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row" <?=$campos_modal_producto?>>
                    <label class="col-sm-2 col-form-label">Dirección</label>
                    <div class="col-sm-9">                     
                        <div class="form-group">
                            <input type="text" class="form-control" name="atr_map_direccion" id="atr_map_direccion" value="" onkeyup="javascript:this.value=this.value.toUpperCase();">
                        </div>
                    </div>  
                </div>
                <!-- CAMPOS DE SITIOS -->
                <div class="row" <?=$campos_modal_sitio?>>
                    <label class="col-sm-2 col-form-label">Nombre</label>
                    <div class="col-sm-9">                     
                        <div class="form-group bmd-form-group">
                            <input type="text" class="form-control" name="atr_mas_nombre" id="atr_mas_nombre" value="" onkeyup="javascript:this.value=this.value.toUpperCase();">
                        </div>
                    </div>  
                </div>
                <div class="row" <?=$campos_modal_sitio?>>
                    <label class="col-sm-2 col-form-label">Dirección</label>
                    <div class="col-sm-9">                     
                        <div class="form-group bmd-form-group">
                            <input type="text" class="form-control" name="atr_mas_direccion" id="atr_mas_direccion" value="" onkeyup="javascript:this.value=this.value.toUpperCase();">
                        </div>
                    </div>  
                </div>
                <div class="row" <?=$campos_modal_sitio?>>
                    <label class="col-sm-2 col-form-label">Procesos</label>
                    <div class="col-sm-9">                     
                        <div class="form-group bmd-form-group">
                            <textarea class="form-control" name="atr_mas_procesos" id="atr_mas_procesos" row="2"></textarea>
                        </div>
                    </div>  
                </div>
                <hr>
                <div class="form-group float-right">
                    <button type="button" class="btn btn-default" id="guardarForm" onclick="guardarFormAtributo()">Guardar</button>
                    <button type="button" class="btn btn-default" id="actualizarForm" onclick="actualizarFormAtributo()">Actualizar</button>
                </div> 
            </div>
        </div>
    </div>
</div>

<script>
    // Cargar la lista de sitios usando AJAX
    function abrirAtributoLista() {
        $.ajax({
            url: 'ajax_atributo_lista.php',
            method: 'POST',
            data: { 
                cod_simulacion_servicio: <?=$cod_simulacion_servicio;?>,
                tipo_modal: <?=$tipo_modal;?> 
            },
            success: function (data) {
                $('#card_lista_atributo').html(data);
                $('#modal_lista_atributo').modal('show');
            },
            error: function (error) {
                console.error('Error al cargar la lista de sitios:', error);
            }
        });
    }
    // Abrir modal de Registro
    function agregarDatosAtributo(){
        // Producto
        $('#atr_map_producto').val("");
        $('#atr_map_direccion').val("");
        $('#atr_map_procesos').val("");
        $('#atr_map_marca').val("");
        $('#atr_map_norma_nac_cod').selectpicker('deselectAll');
        $('#atr_map_norma_int_cod').selectpicker('deselectAll');
        $('#atr_map_sello').val("");
        // Sitio
        $('#atr_mas_nombre').val("");
        $('#atr_mas_direccion').val("");
        // Modal
        $('#modal_lista_atributo').modal('toggle');
        $('#modal_gestion_atributo').modal('show');
        // Titulo
        $('#modal_form_atr').html('Agregar ' + '<?=$titulo_form;?>');
        // Boton 
        $('#guardarForm').show();
        $('#actualizarForm').hide();
    }
    // Guardar Form
    function guardarFormAtributo(){
        // Variables
        var norma_nac_cod = $("#atr_map_norma_nac_cod").val().join(",");
        var norma_int_cod = $("#atr_map_norma_int_cod").val().join(",");
        var nombre        = $('#atr_map_producto').val() || $('#atr_mas_nombre').val();
        var direccion     = $('#atr_map_direccion').val() || $('#atr_mas_direccion').val();
        var procesos      = $('#atr_mas_procesos').val() || '';
        var marca         = $('#atr_map_marca').val();
        var sello         = $('#atr_map_sello').val();
        var tipo_atributo = <?=$tipo_atributo?>;
        var cod_simulacion_servicio = <?=$cod_simulacion_servicio?>;

        // Datos a enviar en el cuerpo de la solicitud
        var data = {
            cod_simulacion_servicio: cod_simulacion_servicio,
            norma_nac_cod: norma_nac_cod,
            norma_int_cod: norma_int_cod,
            nombre: nombre,
            direccion: direccion,
            procesos: procesos,
            marca: marca,
            sello: sello,
            tipo_atributo:tipo_atributo
        };

        // Realizar la solicitud AJAX (GUARDAR)
        $.ajax({
            url: 'ajax_atributo_save.php',
            method: 'POST',
            data: data,
            success: function(response) {
                Swal.fire({
                    type: 'success',
                    title: 'Éxito',
                    text: 'Los datos se enviaron correctamente.',
                    showConfirmButton: false,
                    timer: 1500,
                }).then(() => {
                    $('#modal_gestion_atributo').modal('hide');
                    // abrirAtributoLista();
                });

            },
            error: function(error) {
                Swal.fire({
                    type: 'error',
                    title: 'Error',
                    text: 'Hubo un problema al enviar los datos. Por favor, inténtalo de nuevo.',
                });
                console.error('Error en la solicitud AJAX:', error);
            }
        });
    }
    // Actualizar Form PRODUCTO
    $('body').on('click', '.editarFormAtributoProd', function(){
        $('#cod_simulacionservicio_atributo').val($(this).data('cod_simulacionservicio_atributo'));
        $('#atr_map_producto').val($(this).data('nombre'));
        $('#atr_map_direccion').val($(this).data('direccion'));
        $('#atr_map_marca').val($(this).data('marca'));

        var norma_nac_cod = $(this).data('atr_norma_nac');
        var norma_nac_cod_array = String(norma_nac_cod).split(',').map(item => item.trim());
        $('#atr_map_norma_nac_cod').val(norma_nac_cod_array).selectpicker('refresh');

        var norma_int_cod = $(this).data('atr_norma_int');
        var norma_int_cod_array = String(norma_int_cod).split(',').map(item => item.trim());
        $('#atr_map_norma_int_cod').val(norma_int_cod_array).selectpicker('refresh');

        // Titulo
        $('#modal_form_atr').html('Editar ' + '<?=$titulo_form;?>');

        $('#atr_map_sello').val($(this).data('sello'));
        // Modal
        $('#modal_lista_atributo').modal('toggle');
        $('#modal_gestion_atributo').modal('show');
        // Boton 
        $('#guardarForm').hide();
        $('#actualizarForm').show();
    });
    // Actualizar Form SITIO
    $('body').on('click', '.editarFormAtributoSitio', function() {
        $('#cod_simulacionservicio_atributo').val($(this).data('cod_simulacionservicio_atributo'));
        $('#atr_mas_nombre').val($(this).data('nombre'));
        $('#atr_mas_direccion').val($(this).data('direccion'));
        $('#atr_mas_procesos').val($(this).data('procesos'));

        // Titulo
        $('#modal_form_atr').html('Editar ' + '<?=$titulo_form;?>');
        // Modal
        $('#modal_lista_atributo').modal('toggle');
        $('#modal_gestion_atributo').modal('show');
        // Boton 
        $('#guardarForm').hide();
        $('#actualizarForm').show();
    });
    // Actualizar Form
    function actualizarFormAtributo(){
        // Variables
        var norma_nac_cod = $("#atr_map_norma_nac_cod").val().join(",");
        var norma_int_cod = $("#atr_map_norma_int_cod").val().join(",");
        var nombre        = $('#atr_map_producto').val() || $('#atr_mas_nombre').val();
        var direccion     = $('#atr_map_direccion').val() || $('#atr_mas_direccion').val();
        var procesos      = $('#atr_mas_procesos').val() || '';
        var marca         = $('#atr_map_marca').val();
        var sello         = $('#atr_map_sello').val();
        var cod_simulacionservicio_atributo = $('#cod_simulacionservicio_atributo').val();

        // Datos a enviar en el cuerpo de la solicitud
        var data = {
            cod_simulacionservicio_atributo: cod_simulacionservicio_atributo,
            norma_nac_cod: norma_nac_cod,
            norma_int_cod: norma_int_cod,
            nombre: nombre,
            direccion: direccion,
            procesos: procesos,
            marca: marca,
            sello: sello
        };

        // Realizar la solicitud AJAX (ACTUALIZAR)
        $.ajax({
            url: 'ajax_atributo_update.php',
            method: 'POST',
            data: data,
            success: function(response) {
                Swal.fire({
                    type: 'success',
                    title: 'Éxito',
                    text: 'Los datos se actualizaron correctamente.',
                    showConfirmButton: false,
                    timer: 1500,
                }).then(() => {
                    $('#modal_gestion_atributo').modal('hide');
                    // abrirAtributoLista();
                });

            },
            error: function(error) {
                Swal.fire({
                    type: 'error',
                    title: 'Error',
                    text: 'Hubo un problema al enviar los datos. Por favor, inténtalo de nuevo.',
                });
                console.error('Error en la solicitud AJAX:', error);
            }
        });
    }
    // Cambia Estado
    function AtEliminarDatosAtributo(cod_simulacionservicio_atributo) {
        // Mostrar un cuadro de confirmación
        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Esta acción eliminará el registro. No podrás revertirlo.',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminarlo',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            // Si el usuario confirma
            if (result.value) {
                // Datos a enviar en el cuerpo de la solicitud
                var data = {
                    cod_simulacionservicio_atributo: cod_simulacionservicio_atributo
                };

                // Realizar la solicitud AJAX (ELIMINAR)
                $.ajax({
                    url: 'ajax_atributo_estado.php',
                    method: 'POST',
                    data: data,
                    success: function(response) {
                        Swal.fire({
                            type: 'success',
                            title: 'Éxito',
                            text: 'El registro fue eliminado exitosamente.',
                            showConfirmButton: false,
                            timer: 1500,
                        }).then(() => {
                            $('#modal_lista_atributo').modal('hide');
                        });
                    },
                    error: function(error) {
                        Swal.fire({
                            type: 'error',
                            title: 'Error',
                            text: 'Hubo un problema al enviar los datos. Por favor, inténtalo de nuevo.',
                        });
                        console.error('Error en la solicitud AJAX:', error);
                    }
                });
            }
        });
    }
</script>