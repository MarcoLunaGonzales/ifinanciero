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
    <table style="border: 1px solid white;">
        <tr>
            <td style="border: 1px solid white; width: 80%;">
                <div class="header-text" style="padding-left: 20px;">
                    <h3 class="title">Instituto Boliviano de Normalización y Calidad</h3>
                    <h3 class="subtitle">MANUAL DE CARGOS</h3>
                    <h3 class="subtitle gray_soft">"<?=$resp_cargo['nombre'];?>"</h3>
                </div>
            </td>
            <td style="border: 1px solid white; width: 20%; display: flex; align-items: center; justify-content: center;">
                <div>
                    <img src="../assets/img/ibnorca2.jpg" alt="Ibnorca" class="Ibnorca" style="max-width: 100%;">
                </div>
            </td>
        </tr>
    </table>

    <ol class="numbered-list">
        <!-- Objetivos -->
        <li class="section-title">OBJETIVOS</li>
        <p><?=$obj_descripcion;?></p>
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
                <td class="text-center">Evaluación de la Conformidad</td>
                <td class="text-center">Organismo de Inspección</td>
                <td class="text-center">Estratégico</td>
                <td class="text-center">JKN-KJS</td>
            </tr>
            <tr>
                <th colspan="2" class="half-width" style="background-color: red;color: white; font-weight: bold; text-align: center;">INMEDIATO SUPERIOR (REPORTA A)</th>
                <th colspan="2" class="half-width" style="background-color: red;color: white; font-weight: bold; text-align: center;">DEPENDIENTES (LE REPORTAN)</th>
            </tr>
            <tr>
                <td colspan="2" class="half-width">Seleccionar desde lista de cargos]</td>
                <td colspan="2" class="half-width">Seleccionar desde lista de cargos]</td>
            </tr>
        </table><br>
        <!-- Res´ponsabilidades del Cargo -->
        <li class="section-title">RESPONSABILIDADES DEL CARGO.</li>
        <p>[Jalar de las responsabilidades cargadas en el sistema] Ejm:</p>
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
        <li class="section-title">AUTORIDADES DEL CARGO</li>
        <p>[Jalar de las autoridades cargadas en el sistema]</p>
        <ol class="custom-numbered-list" style="text-align: justify;">
            <li class="list-item">Definir y cumplir con las metas establecidas en el POA y Presupuesto del área, en base a los lineamientos establecidos por Dirección Ejecutiva, para el logro de los objetivos institucionales.</li>
            <li class="list-item">Contar con un sistema de gestión transversal e integral con los sistemas informáticos, que cumpla con las normas técnicas y lineamientos estratégicos del IBNORCA.</li>
            <li class="list-item">Diseñar e implementar los instrumentos de gestión relacionados a la planificación y control. (PEI, POA, POAI, Programas y Proyectos Institucionales) en coordinación con DE, Gestión Estratégica y Tecnología de la Información, para una gestión eficiente.</li>
        </ol><br>
        <!-- Control de cambios -->
        <li class="section-title">CONTROL DE CAMBIOS</li>
        <p>[A ser llenada por nosotros]</p>
        <table>
            <tr>
                <th style="background-color: red;color: white; font-weight: bold; text-align: center;">Versión</th>
                <th style="background-color: red;color: white; font-weight: bold; text-align: center;">Fecha</th>
                <th style="background-color: red;color: white; font-weight: bold; text-align: center;">Descripción de cambios</th>
            </tr>
            <tr>
                <td class="text-center">00</td>
                <td class="text-center">2022-07-07</td>
                <td class="text-center">MP-GTH-39 Manual de Puestos: Jefe de Gestión de Calidad; Versión inicial del documento.</td>
            </tr>
        </table><br>
        <!-- Gestión de Documento -->
        <li class="section-title">GESTIÓN DEL DOCUMENTO</li>
        <p>[A ser llenado por nosotros]</p>
        <table>
            <tr>
                <th style="background-color: red; color: white; font-weight: bold;">Nombre del Archivo</th>
                <th style="background-color: white; color: black;">MP-GTH-39.00.docx</th>
            </tr>
        </table>
        <p>[A ser llenado por nosotros]</p>
        <table>
            <tr>
                <th style="background-color: red;color: white; font-weight: bold; text-align: center;"></th>
                <th style="background-color: red;color: white; font-weight: bold; text-align: center;">Fecha</th>
                <th style="background-color: red;color: white; font-weight: bold; text-align: center;">Responsable</th>
                <th style="background-color: red;color: white; font-weight: bold; text-align: center;">Firma</th>
            </tr>
            <tr>
                <td class="text-center" style="background-color: red;color: white; font-weight: bold; text-align: center;">Elaboración</td>
                <td class="text-center">2022-06-29</td>
                <td class="text-center">Iveth Aruquipa <br><b>Profesional de Talento Humano</b></td>
                <td class="text-center">Elaborado desde sistema</td>
            </tr>
            <tr>
                <td class="text-center"  style="background-color: red;color: white; font-weight: bold; text-align: center;">Aprobación</td>
                <td class="text-center">2022-06-29</td>
                <td class="text-center">José Jorge Durán Guillén<br><b>Director Ejecutivo</b></td>
                <td class="text-center">Aprobado desde sistema</td>
            </tr>
        </table>
    </ol>
</body>
</html>