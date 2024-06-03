<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'functionsGeneral.php';
require_once 'rrhh/configModule.php';

$globalUser=$_SESSION["globalUser"];
$globalCodUnidad=$_SESSION["globalUnidad"];
$globalNombreUnidad=$_SESSION["globalNombreUnidad"];

$cod_planilla = $_GET['cod_planilla'];

$dbh = new Conexion();

// Obtiene el personal registrado en Panilla Excepción
$sqlPersonal = "SELECT GROUP_CONCAT(cod_personal SEPARATOR ',') AS resultado 
                FROM planillas_retroactivos_excepciones
                WHERE cod_planilla = '$cod_planilla'";
$stmtPersonal = $dbh->prepare($sqlPersonal);
$stmtPersonal->execute();
$resultadoString = $stmtPersonal->fetch(PDO::FETCH_ASSOC)['resultado'];
?>
<div class="content">
    <div class="container-fluid">
        <div class="col-md-12">     
            <div class="card">
                <div class="card-header  card-header-text"> 
                    <div class="card-icon" style="background: #6350cf;">
                        <i class="material-icons">settings</i>
                    </div>
                    <h3 style="color:#2c3e50;"><b>Personal de Excepciones</b></h3>
                </div>
                    <div class="card-body">
                        <input type="hidden" name="cod_planilla" id="cod_planilla" value="<?=$cod_planilla?>">
                        <table class="table table-condensed">
                            <thead>
                                <tr style="background-color: #f2f2f2; padding: 10px;">
                                    <th class="text-left" style="padding: 10px; font-size: 13px;" width="45%">Personal</th>
                                    <th class="text-right" style="padding: 10px; font-size: 13px;" width="15%">Haber Básico</th>
                                    <th class="text-right" style="padding: 10px; font-size: 13px;" width="15%">Bono Antigüedad</th>
                                    <th class="text-right pr-2" style="padding: 10px; font-size: 13px;" width="15%">Otros Bonos</th>
                                    <th class=" text-center pr-2" style="padding: 10px; font-size: 13px;" width="10%">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $sql = "SELECT CONCAT(p.primer_nombre, ' ', p.paterno, ' ', p.materno) as personal, 
                                                pre.cod_personal,
                                                pre.haber_basico, 
                                                pre.bono_antiguedad, 
                                                pre.otros_bonos
                                            FROM planillas_retroactivos_excepciones pre
                                            LEFT JOIN personal p ON p.codigo = pre.cod_personal
                                            WHERE pre.cod_planilla = '$cod_planilla'";
                                    // echo $sql;
                                    $stmtDetalle = $dbh->prepare($sql);
                                    $stmtDetalle->execute();
                                    $resultados = $stmtDetalle->fetchAll(PDO::FETCH_ASSOC);

                                    if (empty($resultados)) {
                                        echo '<tr>
                                                <td colspan="4">
                                                    <div class="text-center">
                                                        <h4 class="text-danger">
                                                            <b>No se encontraron registros</b>
                                                        </h4>
                                                    </div>
                                                </td>
                                            </tr>';
                                    } else {
                                        foreach ($resultados as $resultado) {
                                ?>
                                            <tr>
                                                <td class="text-left"><?=$resultado['personal']?></td>
                                                <td class="text-right">
                                                    <b>
                                                        <?php if ($resultado['haber_basico'] == 1) { ?>
                                                            <i class="material-icons text-success" title="SI">check_circle</i>
                                                        <?php } else { ?>
                                                            <i class="material-icons text-danger" title="NO">cancel</i>
                                                        <?php } ?>
                                                    </b>
                                                </td>
                                                <td class="text-right">
                                                    <b>
                                                        <?php if ($resultado['bono_antiguedad'] == 1) { ?>
                                                            <i class="material-icons text-success" title="SI">check_circle</i>
                                                        <?php } else { ?>
                                                            <i class="material-icons text-danger" title="NO">cancel</i>
                                                        <?php } ?>
                                                    </b>
                                                </td>
                                                <td class="text-right pr-2">
                                                    <b>
                                                        <?php if ($resultado['otros_bonos'] == 1) { ?>
                                                            <i class="material-icons text-success" title="SI">check_circle</i>
                                                        <?php } else { ?>
                                                            <i class="material-icons text-danger" title="NO">cancel</i>
                                                        <?php } ?>
                                                    </b>
                                                </td>
                                                <td class="td-actions text-center">
                                                    <button type="button" class="btn btn-info btnEditar" title="Editar"
                                                    data-cod_personal="<?=$resultado['cod_personal']?>"
                                                    data-haber_basico="<?=$resultado['haber_basico']?>"
                                                    data-bono_antiguedad="<?=$resultado['bono_antiguedad']?>"
                                                    data-otros_bonos="<?=$resultado['otros_bonos']?>">
                                                        <i class="material-icons">edit</i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btnEliminar" data-cod_personal="<?=$resultado['cod_personal']?>" title="Eliminar">
                                                        <i class="material-icons">delete</i>
                                                    </button>
                                                </td>
                                            </tr>
                                <?php
                                        }
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                <div class="card-footer fixed-bottom">
                    <a href="index.php?opcion=planillasRetroactivoPersonal" class="btn btn-danger">
                        <i class="material-icons">arrow_back</i> 
                        Volver
                    </a>
                    <button type="button" class="btn btn-success" id="btnRegistrar">
                        <i class="material-icons">add</i> 
                        Nuevo
                    </button> 
                </div>  
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
                        <form id="myform"></form>
                        <!-- CODIGO DE REGISTRO -->
                        <input type="hidden" name="codigo" id="codigo" value="0"/>
                        <input type="hidden" name="metodo" id="metodo" value="0"/>
                        <div class="row">
                            <label class="col-sm-2 col-form-label">Personal:</label>
                            <div class="col-sm-9">
                            <div class="form-group">
                                <select name="cod_personal" id="cod_personal" data-style="btn btn-info" class="selectpicker form-control form-control-sm" data-show-subtext="true" data-live-search="true">
                                    <option value="">Ninguno</option>
                                    <?php 
                                        $sqlPersonal = "SELECT p.codigo,
                                                                UPPER(CONCAT(p.primer_nombre, ' ', p.paterno, ' ', p.materno)) as personal
                                                        FROM personal p
                                                        WHERE p.cod_estadoreferencial = 1
                                                        AND p.cod_estadopersonal = 1";
                                        if (!empty($resultadoString)) {
                                            // $sqlPersonal .= " AND p.codigo NOT IN ($resultadoString)";
                                        }
                                        $sqlPersonal .= " ORDER BY p.paterno ASC";

                                        $stmtListaPersonal = $dbh->query($sqlPersonal);
                                        while ($row = $stmtListaPersonal->fetch()) { 
                                    ?>
                                        <option value="<?=$row["codigo"];?>"><?=$row["personal"];?></option>
                                    <?php 
                                        } 
                                    ?>
                                </select>
                            </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="row">
                                    <label class="col-sm-6 col-form-label">Haber Básico:</label>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <div class="togglebutton">
                                                <label>
                                                    <input type="checkbox" name="haber_basico" id="haber_basico">
                                                    <span class="toggle"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="row">
                                    <label class="col-sm-6 col-form-label">Bono Antigüedad:</label>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <div class="togglebutton">
                                                <label>
                                                    <input type="checkbox" name="bono_antiguedad" id="bono_antiguedad">
                                                    <span class="toggle"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="row">
                                    <label class="col-sm-6 col-form-label">Otros Bonos:</label>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <div class="togglebutton">
                                                <label>
                                                    <input type="checkbox" name="otros_bonos" id="otros_bonos">
                                                    <span class="toggle"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
    /***********************
     * Abre modal Registrar
     ***********************/
    $('#btnRegistrar').on('click', function(){
        // Valores
        $('#codigo').val('');
        $("#cod_personal").val('').trigger("change");
        $("#haber_basico").prop("checked", false);
        $("#bono_antiguedad").prop("checked", false);
        $("#otros_bonos").prop("checked", false);
        // Valores FIN
        $('.modal_titulo').html('Nuevo Registro');
        $('#metodo').val(1);
        $('#modalFormulario').modal('show');
    });
    
    /***************************
     * Abre modal Actualización
     ***************************/
    $('.btnEditar').on('click', function(){
        let haber_basico    = $(this).data('haber_basico');
        let bono_antiguedad = $(this).data('bono_antiguedad');
        let otros_bonos     = $(this).data('otros_bonos');
        // Valores
        $('#codigo').val($(this).data('codigo'));
        $("#cod_personal").val($(this).data('cod_personal')).trigger("change");
        $("#haber_basico").prop("checked", haber_basico == 1 ? true : false);
        $("#bono_antiguedad").prop("checked", bono_antiguedad == 1 ? true : false);
        $("#otros_bonos").prop("checked", otros_bonos == 1 ? true : false);

        // Valores FIN
        $('.modal_titulo').html('Editar Registro');
        $('#metodo').val(2);
        $('#modalFormulario').modal('show');
    });
    /***********************
     * Guardar o actualizar
     ***********************/
    $("#btnGuardar").click(function() {
        // Tipo formulario (1: Registro, 2: Actualización)
        var metodo          = $("#metodo").val();
        // Obtén los valores de los campos
        var codigo          = $("#codigo").val();
        var cod_planilla    = $("#cod_planilla").val();
        var cod_personal    = $("#cod_personal").val();
        var haber_basico    = $("#haber_basico").prop("checked") ? 1 : 0;
        var bono_antiguedad = $("#bono_antiguedad").prop("checked") ? 1 : 0;
        var otros_bonos     = $("#otros_bonos").prop("checked") ? 1 : 0;
        // Verificar si algún campo está vacío
        if (cod_personal == '') {
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
            url: "planillas/ajax_RetroactivoExcepcionSave.php",
            method: "POST",
            dataType: "json",
            data: {
                codigo: codigo,
                metodo: metodo,
                cod_planilla: cod_planilla,
                cod_personal: cod_personal,
                haber_basico: haber_basico,
                bono_antiguedad: bono_antiguedad,
                otros_bonos: otros_bonos,
            },
            success: function(response) {
                // console.log(response)
                $('#modalFormulario').modal('toggle');
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
                $('#modalFormulario').modal('hidden');
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
    /***********************
     * Guardar o actualizar
     ***********************/
    $(".btnEliminar").click(function() {
        let cod_personal = $(this).data('cod_personal');
        let cod_planilla = $("#cod_planilla").val();
        // Mostrar una confirmación antes de eliminar
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción es irreversible. ¿Quieres eliminar este registro?",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.value) {
                // Si el usuario confirma, ejecutar la eliminación
                location.href = `index.php?opcion=eliminarPersonalExcepcion&cod_planilla=${cod_planilla}&cod_personal=${cod_personal}`;
            }
        });
    });
</script>