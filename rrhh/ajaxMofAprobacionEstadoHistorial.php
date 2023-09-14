<?php
require_once '../conexion.php';


$dbh = new Conexion();

$cod_mof = $_POST['cod_mof'];

$sql_apr = "SELECT ma.codigo, ma.nro_version, ma.fecha_inicio, ma.fecha_fin, ma.cod_estado
            FROM mof_aprobacion ma
            WHERE ma.cod_mof = '$cod_mof'
            ORDER BY ma.nro_version DESC";
$stmt_apr = $dbh->prepare($sql_apr);
$stmt_apr->execute();
$results_apr = $stmt_apr->fetchAll(PDO::FETCH_ASSOC);

// Primer Collapse abierto (SW)
$sw_collapse = 'show';
foreach($results_apr as $row_apr){
    $number = $row_apr['codigo'];
?>
<div id="accordion<?=$number?>">
    <div class="card m-2">
        <div class="card-header p-0" id="heading<?=$number?>">
            <h5 class="mb-0">
                <?php if($row_apr['cod_estado'] == 1){ ?>
                <!-- En Proceso -->
                <a class="btn btn-secondary btn-block text-black m-0" style="border: 1px solid rgba(0, 0, 0, 0.2);" data-toggle="collapse" data-target="#collapse<?=$number?>" aria-expanded="true" aria-controls="collapse<?=$number?>">
                    Revisión <?=$row_apr['nro_version']?> - <span class="badge badge-warning"><i class="fas fa-clock mr-2 text-white"></i> En Proceso</span>
                </a>
                <?php }else if($row_apr['cod_estado'] == 2){ ?>
                <!-- Aprobado -->
                <a class="btn btn-secondary btn-block text-black m-0" style="border: 1px solid rgba(0, 0, 0, 0.2);" data-toggle="collapse" data-target="#collapse<?=$number?>" aria-expanded="true" aria-controls="collapse<?=$number?>">
                    Revisión <?=$row_apr['nro_version']?> - <span class="badge badge-success"><i class="fas fa-check-circle mr-2 text-white"></i> Aprobado</span>
                </a>
                <?php }else if($row_apr['cod_estado'] == 3){ ?>
                <!-- Rechazado -->
                <a class="btn btn-secondary btn-block text-black m-0" style="border: 1px solid rgba(0, 0, 0, 0.2);" data-toggle="collapse" data-target="#collapse<?=$number?>" aria-expanded="true" aria-controls="collapse<?=$number?>">
                    Revisión <?=$row_apr['nro_version']?> - <span class="badge badge-danger"><i class="fas fa-times-circle mr-2 text-white"></i> Rechazado</span>
                </a>
                <?php } ?>
            </h5>
        </div>
        <div id="collapse<?=$number?>" class="collapse <?=$sw_collapse?>" aria-labelledby="heading<?=$number?>" data-parent="#accordion<?=$number?>">
            <div class="card-body pb-0">
                <?php
                    $sw_collapse = ''; // cerrar Collapse de aqui en adelante
                    $cod_mof_aprobacion = $row_apr['codigo'];
                    $sql = "SELECT CONCAT(p.primer_nombre, ' ', p.paterno, ' ', p.materno) as personal,
                                    DATE_FORMAT(mas.fecha,'%d-%m-%Y %H:%m') as fecha,
                                    mase.codigo as estado,
                                    mase.nombre as estado_nombre,
                                    mase.color as estado_color,
                                    mas.observacion,
                                    mae.nombre as etapa_nombre,
                                    mae.descripcion as etapa_descripcion,
                                    pi.imagen as personal_imagen
                            FROM mof_aprobacion_seguimiento mas
                            LEFT JOIN mof_aprobacion_seguimiento_estados mase ON mase.codigo = mas.cod_seguimiento_estado
                            LEFT JOIN personal p ON p.codigo = mas.cod_personal
                            LEFT JOIN mof_aprobacion_etapas mae ON mae.codigo = mas.cod_etapa
                            LEFT JOIN personalimagen pi ON pi.codigo = mas.cod_personal
                            WHERE mas.cod_mof = '$cod_mof_aprobacion'
                            ORDER BY mas.codigo DESC";
                            // echo $sql;
                    $stmt = $dbh->prepare($sql);
                    $stmt->execute();
                    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    // Ruta Imagenes
                    $archivo = "personal/imagenes/";
                    if(empty($results)){
                ?>
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <i class="material-icons mr-2 text-danger">not_interested</i>
                                    <p class="card-text">No se realizó ningún cambio de estado.</p>
                                </div>
                            </div>
                        </div>

                <?php
                    }else{
                        
                        foreach ($results as $row) {
                            // Iconos
                            $color = $row['estado_color'];
                            if($row['estado'] == 1){
                                $icono = 'check_circle';
                                $color = 'cancel';
                            }else if($row['estado'] == 2){
                                $icono = 'cancel';
                            }else{
                                $icono = 'info';
                                $color = 'info';
                            }
                        ?>
                        <div class="card mt-0 mb-2 state-<?=$row["estado_color"];?>">
                            <div class="card-body ">
                                <div class="row">
                                    <!-- Primera Sección -->
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-center pb-1">
                                            <img src="<?=$archivo;?><?=$row['personal_imagen'];?>" alt="Imagen" class="mr-3" style="width: 30px; height: 30px; border-radius: 50%;">
                                            <div>
                                                <h6 class="card-title"><?=$row["personal"];?></h6>
                                            </div>
                                        </div>
                                        <div>
                                            <p class="card-text mb-0"><b>Fecha:</b> <?=$row["fecha"];?></p>
                                        </div>
                                    </div>

                                    <!-- Segunda Sección -->
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center text-primary">
                                            <i class="material-icons mr-1">timeline</i>
                                            <h6 class="text-dark"><b class="text-primary">Etapa:</b> <?=$row["etapa_descripcion"];?></h6>
                                        </div>
                                        <div>
                                            <p class="card-text"><b>Observación:</b> <?=$row["observacion"];?></p>
                                        </div>
                                    </div>

                                    <!-- Tercera Sección -->
                                    <div class="col-md-2">
                                        <div class="d-flex align-items-center text-<?=$row["estado_color"];?>">
                                            <i class="material-icons mr-1"><?=$icono?></i>
                                            <b><?=$row["estado_nombre"];?></b>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php 
                        }
                    }
                ?>
            </div>
        </div>
    </div>
</div>
<?php
    }
?>