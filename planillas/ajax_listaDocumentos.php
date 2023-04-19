<?php

require_once '../conexion.php';

$cod_planilla           = $_POST["cod_planilla"];
$cod_estado_documento   = $_POST["cod_estado_documento"];

$dbh = new Conexion();

$sql = "SELECT pd.codigo, pd.descripcion, pd.archivo, DATE_FORMAT(pd.fecha_registro, '%d-%m-%Y') as fecha FROM planillas_documentos pd WHERE pd.cod_planilla='$cod_planilla' AND cod_estado = 1";
$stmtPlanillaDocumento = $dbh->prepare($sql);
$stmtPlanillaDocumento->execute();
$stmtPlanillaDocumento->bindColumn('codigo', $codigo);
$stmtPlanillaDocumento->bindColumn('descripcion', $descripcion);
$stmtPlanillaDocumento->bindColumn('archivo', $archivo);
$stmtPlanillaDocumento->bindColumn('fecha', $fecha);

?>

<div class="card-body ">
    <?php 
        if ($stmtPlanillaDocumento->rowCount()) {
    ?>
    <table class="table" id="tablePaginator">
        <thead>
            <tr>                    
                <th>Nro</th>
                <th>Descripción</th>
                <th>Fecha Registro</th>
                <th class="text-center">Descargar</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                $index = 0;
                while ($rowDocumento = $stmtPlanillaDocumento->fetch(PDO::FETCH_BOUND)) {
                    $index++;
            ?>
            <tr>                    
                <td><?=$index?></td>
                <td><?=$descripcion;?></td>
                <td><?=$fecha;?></td>
                <td class="text-center">
                    <!-- Descargar Archivo -->
                    <a href="documentos_planilla/<?=$archivo;?>" download="<?=$descripcion;?>" rel="tooltip" class="btn btn-success btn-sm" title="Descargar Archivo">
                        <i class="material-icons" title="Ver Planilla Triburaria">download</i>                       
                    </a>
                    <!-- Eliminar registro -->
                    
                    <?php
                        if ($cod_estado_documento == 1) {
                    ?>
                        <button class="btn btn-danger btn-sm eliminar_archivo" title="Descargar Archivo" data-codigo="<?=$codigo;?>">
                            <i class="material-icons" title="Eliminar Archivo">delete</i> 
                        </button>
                    <?php
                        }
                    ?>
                    <!-- Ver registro -->
                    <a href="planillas/ver_archivo.php?cod_documento=<?=$codigo;?>" target="_blank" class="btn btn-primary btn-sm" title="Ver Archivo">
                    <i class="material-icons" title="Ver Planilla Triburaria PDF">remove_red_eye</i> 
                    </a>
                </td>
            <tr>  
            <?php 
                }
            ?>
        </tbody>                                      
    </table>
    
    <?php 
        }else{
    ?>
        <div class="text-center"><h4 class="text-danger"><b>No se encontró archivos adjuntos</b></h4></div>
    <?php 
        }
    ?>
</div>  