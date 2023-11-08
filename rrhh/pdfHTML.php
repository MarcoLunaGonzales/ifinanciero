<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MANUAL DE CARGOS</title>
    <style>
        /* Estilos CSS para el contenido */
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt; /* Tamaño de letra promedio */
        }
        .title {
            font-size: 10pt;
            font-style: italic;
            text-decoration: underline;
            text-align: center;
        }
        .subtitle {
            font-weight: bold;
            text-align: center;
        }
        .green {
            color: green;
        }
        .gray_soft {
            color: #808080;
        }

        .logo {
            width: 3cm;
            height: 3cm;
            float: right;
        }
        .header {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .header-text {
            text-align: center;
            flex-grow: 1;
        }
        .section-title {
            font-weight: bold;
        }
        /* Primera tabla */
        .numbered-list {
            counter-reset: item;
            padding-left: 0;
        }

        .numbered-list > li {
            list-style: none;
            counter-increment: item;
        }

        .numbered-list > li::before {
            content: counter(item) ")";
            font-weight: bold;
            margin-right: 5px;
        }
        /* Hojas de estilo para las tablas en general*/
        table {
            border-collapse: separate;
            border-spacing: 0;
            border: 1px solid #c0c0c0;
            border-radius: 5px;
            width: 100%;
        }

        th, td {
            border: 1px solid #c0c0c0;
            padding: 8px;
        }

        th {
            background-color: red;
            text-transform: uppercase;
            color: white;
        }
        .half-width {
            width: 50%;
        }
        .col-custom{
            background-color: white; 
            color: black;
        }

        .text-center {
            text-align: center;
        }
        /*  Modelo de listado de parrafos con el formato 1) */
        .custom-numbered-list {
            counter-reset: custom-counter;
            list-style: none;
            padding-left: 0;
        }

        .custom-numbered-list li {
            position: relative;
            padding-bottom: 5px;
            padding-left: 25px; /* Ajusta el valor según el espacio deseado */
        }

        .custom-numbered-list li::before {
            counter-increment: custom-counter;
            content: counter(custom-counter)") ";
            font-weight: bold;
            position: absolute;
            left: 0;
        }

    </style>
</head>
<body>
    <ol class="numbered-list">
        <!-- Objetivos -->
        <li class="section-title">OBJETIVOS</li>
        <p style="text-align: justify;"><?=empty($resp_cargo['objetivo'])?'':$resp_cargo['objetivo'];?></p>
        <!-- Descripción del Cargo -->
        <li class="section-title">DESCRIPCIÓN DEL CARGO</li><br><br>
        <table>
            <tr>
                <td style="background-color: red;color: white; font-weight: bold; text-align: center;">ÁREA</td>
                <td style="background-color: red;color: white; font-weight: bold; text-align: center;">UNIDAD</td>
                <td style="background-color: red;color: white; font-weight: bold; text-align: center;">NIVEL DEL CARGO</td>
                <td style="background-color: red;color: white; font-weight: bold; text-align: center;">SIGLA</td>
            </tr>
            <tr>
                <td class="text-center"><?=empty($resp_areanivel2['nombre']) ? '' : $resp_areanivel2['nombre'];?></td>
                <td class="text-center"><?= (empty($resp_unidad['nombre_unidad']) || $resp_areanivel2['nombre'] == $resp_unidad['nombre_unidad']) ?'N/A':$resp_unidad['nombre_unidad'];?></td>
                <td class="text-center"><?=empty($resp_cargo['nivel_cargo'])?'':$resp_cargo['nivel_cargo'];?></td>
                <td class="text-center"><?=empty($resp_cargo['abreviatura'])?'':$resp_cargo['abreviatura'];?></td>
            </tr>
            <tr>
                <th colspan="2" class="half-width" style="background-color: red;color: white; font-weight: bold; text-align: center;">INMEDIATO SUPERIOR (REPORTA A)</th>
                <th colspan="2" class="half-width" style="background-color: red;color: white; font-weight: bold; text-align: center;">DEPENDIENTES (LE REPORTAN)</th>
            </tr>
            <tr>
                <td colspan="2" class="half-width"><?=empty($resp_cargo_sup['nombre']) ? '' : $resp_cargo_sup['nombre'];?></td>
                <td colspan="2" class="half-width"><?php 
                    if (empty($resp_cargosDep)) {
                        echo 'N/A';
                    } else {
                        $total_cargos = count($resp_cargosDep);
                        foreach ($resp_cargosDep as $key => $row) { 
                            echo $row['nombre'];
                            if ($key < $total_cargos - 1) {
                                echo '<br>';
                            }
                        }
                    }
                    ?>
                </td>
            </tr>
        </table><br>
        <!-- Responsabilidades Generales IBNORCA -->
        <!-- <li class="section-title">RESPONSABILIDADES GENERALES.</li><br> -->
        <!-- <p style="text-align: justify;">Cada área en IBNORCA cumple una función específica para el logro de los objetivos institucionales. Sin embargo, se establecen funciones transversales que deben cumplir todas las áreas y unidades de la Institución:</p> -->
        <!-- <ol class="custom-numbered-list" style="text-align: justify;">
            <?php 
                foreach($resp_resposabilidadesGenerales as $row){
            ?>
                <li class="list-item"><?=$row['nombre'];?></li>
            <?php 
                }
            ?>
        </ol><br> -->
        <!-- Responsabilidades del Cargo -->
        <li class="section-title">RESPONSABILIDADES DEL CARGO</li><br>
        <!-- <p style="text-align: justify;">En esta sección, se enumeran las responsabilidades del puesto. Estas responsabilidades están diseñadas para garantizar un desempeño eficiente y efectivo en la consecución de los objetivos establecidos. A continuación, se presenta la lista de responsabilidades del cargo:</p> -->
        <ol class="custom-numbered-list" style="text-align: justify;">
            <?php 
                foreach($resp_resposabilidadesCargos as $row){
            ?>
                <li class="list-item"><?=$row['nombre_funcion'];?></li>
            <?php 
                }
            ?>
        </ol><br>
        <!-- Autoridades del cargo -->
        <!-- <li class="section-title">AUTORIDADES DEL CARGO</li> -->
        <!-- <p style="text-align: justify;">A continuación se presenta una lista de las autoridades y posiciones clave relacionadas con el puesto:</p> -->
        <!-- <ol class="custom-numbered-list" style="text-align: justify;">
            <?php 
                foreach($resp_autoridadesCargos as $row){
            ?>
                <li class="list-item"><?=$row['nombre_autoridad'];?></li>
            <?php 
                }
            ?>
        </ol><br> -->
        <!-- Control de cambios -->
        <li class="section-title">CONTROL DE CAMBIOS</li><br><br>
        <?php 
            if(count($resp_control_versiones) > 0){
        ?>
        <table>
            <tr>
                <th style="background-color: red;color: white; font-weight: bold; text-align: center; width: 20%;">Versión</th>
                <th style="background-color: red;color: white; font-weight: bold; text-align: center; width: 20%;">Fecha</th>
                <th style="background-color: red;color: white; font-weight: bold; text-align: center; width: 60%;">Descripción de cambios</th>
            </tr>

            <?php 
                foreach($resp_control_versiones as $row){
            ?>
                <tr>
                    <td class="text-center"><?=empty($row['nro_version']) ? '' : $row['nro_version'];?></td>
                    <td class="text-center"><?=$row['fecha'];?></td>
                    <td class="text-center" style=" text-align: justify;"><?=$row['descripcion_cambios'];?></td>
                </tr>
            <?php 
                }
            ?>
        </table>
        <?php 
            }
        ?>
        <br>
        <!-- Gestión de Documento -->
        <li class="section-title">GESTIÓN DEL DOCUMENTO</li><br><br>
        <?php 
            if(count($resp_manual_seguimiento) > 0){
        ?>
        <table>
            <tr>
                <th style="background-color: red;color: white; font-weight: bold; text-align: center;"></th>
                <th style="background-color: red;color: white; font-weight: bold; text-align: center;">Fecha</th>
                <th style="background-color: red;color: white; font-weight: bold; text-align: center;">Responsable</th>
                <th style="background-color: red;color: white; font-weight: bold; text-align: center;">Firma</th>
            </tr>
            
            <?php 
                foreach($resp_manual_seguimiento as $row){
            ?>
                <tr>
                    <td class="text-center" style="background-color: red;color: white; font-weight: bold; text-align: center;"><?=$row['nombre_etapa'];?></td>
                    <td class="text-center"><?=$row['fecha'];?></td>
                    <td class="text-center"><?=$row['personal'];?> <br><b><?=$row['cargo'];?></b></td>
                    <td class="text-center"><?=$row['cod_etapa'] == 0 ? 'Elaborado desde sistema' : 'Aprobado desde sistema';?></td>
                </tr>
            <?php 
                }
            ?>
        </table>
        <?php 
            }
        ?>
    </ol>
</body>
</html>