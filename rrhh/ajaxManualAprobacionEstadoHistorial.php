<?php
require_once '../conexion.php';


$dbh = new Conexion();

$cod_manual_aprobacion = $_POST['cod_manual_aprobacion'];

$sql = "SELECT CONCAT(p.primer_nombre, ' ', p.paterno, ' ', p.materno) as personal,
                DATE_FORMAT(mas.fecha,'%d-%m-%Y %H:%m') as fecha,
                mase.codigo as estado,
                mase.nombre as estado_nombre,
                mase.color as estado_color,
                mas.observacion,
                mae.nombre as etapa_nombre,
                mae.descripcion as etapa_descripcion
        FROM manuales_aprobacion_seguimiento mas
        LEFT JOIN manuales_aprobacion_seguimiento_estados mase ON mase.codigo = mas.cod_seguimiento_estado
        LEFT JOIN personal p ON p.codigo = mas.cod_personal
        LEFT JOIN manuales_aprobacion_etapas mae ON mae.codigo = mas.cod_etapa
        WHERE mas.cod_manual = '$cod_manual_aprobacion'
        ORDER BY mas.codigo DESC";
// echo $sql;
$stmt = $dbh->prepare($sql);
//ejecutamos
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if(empty($results)){
?>
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="material-icons mr-2 text-danger">not_interested</i>
                    <p class="card-text">No se realizaron cambios.</p>
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
            <div class="card mb-3 state-<?=$row["estado_color"];?>">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center text-<?=$row["estado_color"];?>">
                                <i class="material-icons mr-1"><?=$icono?></i>
                                <b><?=$row["estado_nombre"];?></b>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center text-primary">
                                <i class="material-icons mr-1">timeline</i>
                                <h6><?=$row["etapa_descripcion"];?></h6>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <!-- <img src="ruta_de_la_imagen_rechazo.jpg" alt="Imagen" class="mr-3" style="width: 50px; height: 50px; border-radius: 50%;"> -->
                                <div>
                                    <h6 class="card-title"><?=$row["personal"];?></h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div>
                                <p class="card-text mb-0"><b>Fecha:</b> <?=$row["fecha"];?></p>
                            </div>
                        </div>
                    </div>
                    <hr class="my-2">
                    <p class="card-text"><b>Observación:</b> <?=$row["observacion"];?></p>
                </div>
            </div>
<?php 
        }
    }
?>