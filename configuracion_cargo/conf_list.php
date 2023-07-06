<?php

require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';

$globalAdmin = $_SESSION["globalAdmin"];

$dbh = new Conexion();

$sql = "SELECT acc.codigo,
                acc.nombre,
                acc.fecha_registro,
                acc.fecha_aprobacion,
                acc.cod_personal_aprobacion,
                acc.cod_estadoaprobacion,
                efc.nombre as nombre_estado
                FROM aprobacion_configuraciones_cargos acc
                LEFT JOIN estados_funcionescargo efc ON efc.codigo = acc.cod_estadoaprobacion";
$stmt = $dbh->prepare($sql);
$stmt->execute();

$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('fecha_registro', $fecha_registro);
$stmt->bindColumn('fecha_aprobacion', $fecha_aprobacion);
$stmt->bindColumn('cod_personal_aprobacion', $cod_personal_aprobacion);
$stmt->bindColumn('cod_estadoaprobacion', $cod_estadoaprobacion);
$stmt->bindColumn('nombre_estado', $nombre_estado);

?>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header <?= $colorCard; ?> card-header-icon">
                        <div class="card-icon">
                            <i class="material-icons"><?= $iconCard; ?></i>
                        </div>
                            <h4 class="card-title"><?= $moduleNamePlural ?></h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-condensed">
                                    <thead>
                                        <tr>
                                            <th class="text-left"> # </th>
                                            <th class="text-left">Descripción</th>
                                            <th class="text-left">Fecha Registro</th>
                                            <th class="text-left">Fecha Aprobación</th>
                                            <th class="text-center">Estado</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $index = 1;
                                            while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                                        ?>
                                        <tr>
                                            <td class="text-center"><?= $index; ?></td>
                                            <td class="text-left"><?= $nombre; ?></td>
                                            <td class="text-left"><?= date('d-m-Y', strtotime($fecha_registro)); ?></td>
                                            <td class="text-left"><?= empty($fecha_aprobacion) ? '' : date('d-m-Y', strtotime($fecha_aprobacion)); ?></td>
                                            <td class="text-center">
                                                <span id="estadoRegistro" class="badge badge-<?= ($cod_estadoaprobacion == 3) ? 'success' : ($cod_estadoaprobacion == 1 ? 'danger' : ''); ?>"><?= $nombre_estado; ?></span>
                                            </td>
                                            <td class="td-actions text-center">
                                                <!-- Modificación de ESTADO -->
                                                <?php
                                                if($cod_estadoaprobacion == 1){
                                                ?>
                                                    <!-- PARA APROBACIÓN -->
                                                    <button class="btn btn-success configAprobar" title="Aprobar Configuración" data-codigo="<?= $codigo; ?>">
                                                        <i class="material-icons" style="color:white">done</i>
                                                    </button>
                                                <?php
                                                }else{
                                                ?>
                                                    <!-- APROBADO -->
                                                    <button class="btn btn-primary" title="Configuración Aprobada">
                                                        <i class="material-icons" style="color:white">loyalty</i>
                                                    </button>
                                                <?php
                                                }
                                                ?>
                                                <!-- Actualización de datos -->
                                                <button class="btn btn-info" title="Editar">
                                                    <i class="material-icons" style="color:white" onClick="location.href='<?=$urlEdit;?>&codigo=<?=$codigo;?>'">edit</i>
                                                </button>
                                                <!-- Lista de Cargos -->
                                                <button class="btn btn-warning" title="Lista de Cargos">
                                                    <i class="material-icons" style="color:white" onClick="location.href='index.php?opcion=configuracionCargosLista&cod_config_aprobacion=<?=$codigo;?>'">list</i>
                                                </button>
                                            </td>
                                        </tr>
                                            <?php
                                                    $index++;
                                                }
                                            ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Nuevo -->
                    <?php
                        if ($globalAdmin == 1) {
                    ?>
                        <div class="card-footer fixed-bottom">
                            <button class="<?=$buttonNormal;?>" onClick="location.href='<?=$urlRegister;?>'">Registrar</button>
                        </div>
                    <?php
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    // Cambiar estado de configuración
    $('body').on('click','.configAprobar', function(){
        let formData = new FormData();
        formData.append('codigo', $(this).data('codigo'));
        swal({
            title: '¿Esta seguro de Aprobarlo?',
            text: "Se aprobará la configuración seleccionada.",
            type: 'warning',
            showCancelButton: true,
            confirmButtonClass: 'btn btn-success',
            cancelButtonClass: 'btn btn-danger',
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            buttonsStyling: false
        }).then((result) => {
            if (result.value) {
                $(".cargar-ajax").removeClass("d-none");
                $.ajax({
                    url:"configuracion_cargo/ajaxEstado.php",
                    type:"POST",
                    contentType: false,
                    processData: false,
                    data: formData,
                    success:function(response){
                    let resp = JSON.parse(response);
                    if(resp.status){
                        $(".cargar-ajax").addClass("d-none");// Mensaje
                        Swal.fire({
                            type: 'success',
                            title: 'Correcto!',
                            text: 'El proceso se completo correctamente!',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        
                        setTimeout(function(){
                            location.reload()
                        }, 1550);
                    }else{
                        Swal.fire('ERROR!','El proceso tuvo un problema!. Contacte con el administrador!','error'); 
                        }
                    }
                });
            }
        });
    });
</script>