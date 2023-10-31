<?php
require_once '../conexion.php';
require_once '../assets/tcpdf/tcpdf.php';

// Clase personalizada que hereda de TCPDF
class CustomTCPDF extends TCPDF {
    // Método para personalizar la cabecera
    public function Header() {
        global $resp_cargo;
        // Datos
        $codigo_control   = empty($resp_cargo['codigo_doc']) ? '-' : $resp_cargo['codigo_doc'];

        // Obtener los márgenes establecidos
        $leftMargin  = $this->getOriginalMargins()['left'];
        $rightMargin = $this->getOriginalMargins()['right'];
        // Establecer la posición vertical para el encabezado
        $this->SetY(1); // Posición vertical para el encabezado

        // HTML del encabezado personalizado
        $titulo_cargo = rtrim(empty($resp_cargo['nombre']) ? '' : $resp_cargo['nombre']);
        $header = '<table style="border: 1px solid white; margin-left: ' . $leftMargin . 'mm; margin-right: ' . $rightMargin . 'mm;">
                        <tr>
                            <td style="border: 1px solid white; width: 88%;">
                                <div style="text-align: center;flex-grow: 1; padding-left: 20px;">
                                    <h3 style="font-size: 10pt;font-style: italic;text-decoration: underline;text-align: center;">Instituto Boliviano de Normalización y Calidad</h3>
                                    <h3 style="font-size: 12pt; font-weight: bold;text-align: center;">MANUAL DE CARGOS</h3>
                                    <h3 style="font-size: 12pt; font-weight: bold;text-align: center; color: #808080;">"'.$titulo_cargo.'"</h3>
                                </div>
                            </td>
                            <td style="border: 1px solid white; width: 12%; display: flex; align-items: center; justify-content: center;">
                                <div>
                                    <br>
                                    <img src="../assets/img/ibnorca2.jpg" alt="Ibnorca" class="Ibnorca">
                                </div>
                            </td>
                        </tr>
                    </table>';
        $this->writeHTML($header, true, false, true, false, '');
    }
    // Método para personalizar el pie de página
    public function Footer() {
        global $resp_manual_aprobacion;
        global $resp_control_cambio;
        // Datos
        $fecha_aprobacion = empty($resp_manual_aprobacion['fecha_fin']) ? '-' : $resp_manual_aprobacion['fecha_fin'];
        $codigo_control   = empty($resp_control_cambio['codigo_doc']) ? '-' : $resp_control_cambio['codigo_doc'];


        // Obtener los márgenes establecidos
        $leftMargin = $this->getOriginalMargins()['left'];
        $rightMargin = $this->getOriginalMargins()['right'];
        // Establecer la posición vertical para el pie de página
        $this->SetY(-15); // Posición vertical para el pie de página (aquí se establece la distancia desde la parte inferior)
        // HTML del pie de página personalizado con estilos
        $footer = '<table style="border: 1px solid #c0c0c0; border-radius: 5px; margin-left: ' . $leftMargin . 'mm; margin-right: ' . $rightMargin . 'mm;">
                        <tr style="padding: 18px; ">
                            <td style="text-align: center; border: 1px solid #c0c0c0;"><b>IBNORCA ©</b></td>
                            <td style="text-align: center; border: 1px solid #c0c0c0;"><b>Código:</b> '.$codigo_control.'</td>
                            <td style="text-align: center; border: 1px solid #c0c0c0;"><b>V:</b> '.$fecha_aprobacion.'</td>
                            <td style="text-align: center; border: 1px solid #c0c0c0;"><b> Página ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages() . '</b></td>
                        </tr>
                    </table>';
        // Imprimir el pie de página personalizado
        $this->SetFont('helvetica', 'I', 10); // Establece la fuente y el tamaño de letra para el pie de página
        $this->writeHTML($footer, true, false, true, false, ''); // Convierte el HTML a PDF manteniendo las hojas de estilo
    }
}
/**
 * Realiza la busqueda del Area relacionado o que tenga referencia del Direccion Ejecutiva
 */
function buscarArea($cod_area){
    $cod_direccion = 847;
    $dbh = new Conexion();
    $sql = "SELECT a.codigo, a.cod_padre
            FROM areas a
            WHERE a.codigo = '$cod_area'";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $res_busqueda = '';
    foreach ($resultados as $resultado) {
        $cod_area       = $resultado['codigo'];
        $cod_area_padre = $resultado['cod_padre'];
        if($cod_area == $cod_direccion || $cod_area_padre == $cod_direccion){
            $res_busqueda = $cod_area;
            return $cod_area;
        }else{
            $res_busqueda = buscarArea($cod_area_padre);
        }
    }
    return $res_busqueda;
}

global $resp_manual_aprobacion;
global $resp_control_cambio;
global $resp_cargo;
// CODIGO DE CARGO
$cod_cargo = $_GET['codigo'];
// Conexión BD
$dbh = new Conexion();

// * MANUAL DE APROBACIÓN - ULTIMA VERSIÓN
$sql = "SELECT  ma.codigo, ma.cod_etapa, ma.cod_cargo, ma.cod_area, ma.nro_version, DATE_FORMAT(ma.fecha_inicio, '%d-%m-%Y') as fecha_inicio, DATE_FORMAT(ma.fecha_fin, '%d-%m-%Y') as fecha_fin
        FROM manuales_aprobacion ma
        INNER JOIN (
                SELECT
                        cod_cargo,
                        MAX(nro_version) AS ultima_version
                FROM manuales_aprobacion
                GROUP BY cod_cargo
        ) ultima_version_ma ON ma.cod_cargo = ultima_version_ma.cod_cargo AND ma.nro_version = ultima_version_ma.ultima_version
        WHERE ma.cod_cargo = '$cod_cargo'
        -- AND ma.cod_estado = 3
        ORDER BY ma.codigo DESC
        LIMIT 1";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$resp_manual_aprobacion = $stmt->fetch(PDO::FETCH_ASSOC);

// * CONTROL DE CAMBIOS - SE OBTIENE ULTIMO REGISTRO
$sql = "SELECT cv.codigo, cv.nro_version, cv.codigo_doc, cv.descripcion_cambios, cv.fecha
        FROM control_versiones cv
        WHERE cv.cod_cargo = '$cod_cargo'
        ORDER BY cv.codigo DESC
        LIMIT 1";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$resp_control_cambio = $stmt->fetch(PDO::FETCH_ASSOC);

// * Cargo
$sql = "SELECT c.codigo, UPPER(c.nombre) as nombre, c.abreviatura, c.objetivo, c.cod_padre, tc.nombre as nivel_cargo, ca.cod_areaorganizacion as cod_area
        FROM cargos c
        LEFT JOIN tipos_cargos_personal tc ON tc.codigo = c.cod_tipo_cargo
        LEFT JOIN cargos_areasorganizacion ca ON ca.cod_cargo = c.codigo
        LEFT JOIN areas a ON a.codigo = ca.cod_areaorganizacion
        WHERE c.codigo = '$cod_cargo'
        AND c.cod_estadoreferencial = 1
        AND a.codigo IS NOT NULL
        LIMIT 1";
        // echo $sql;
$stmt = $dbh->prepare($sql);
$stmt->execute();
$resp_cargo = $stmt->fetch(PDO::FETCH_ASSOC);
// Crea una instancia de CustomTCPDF en lugar de TCPDF
$pdf = new CustomTCPDF('P', 'mm', 'Letter', true, 'UTF-8'); // Cambia 'A4' por 'Letter' para establecer el tamaño de papel como carta

// Establece los márgenes
$pdf->SetMargins(15, 40, 15); // Establece el mismo margen en los cuatro lados del documento y habilita el margen inferior automático para el pie de página

// Establece la información del documento PDF
$pdf->SetCreator('TCPDF');
$pdf->SetAuthor('IBNORCA');
$pdf->SetTitle('Manual de Cargo - IBNORCA');

// Agrega una página al PDF
$pdf->AddPage();

// Lee el contenido HTML desde un archivo o una cadena
$html = file_get_contents('pdfHTML.php');

/**********************************/
/*      PREPARACIÓN DE DATOS      */
/**********************************/

// CONTROL DE VERSIONES - CAMBIOS
$sql = "SELECT cv.codigo, DATE_FORMAT(cv.fecha, '%d-%m-%Y') as fecha, cv.nro_version, cv.descripcion_cambios
        FROM control_versiones cv
        WHERE cv.cod_cargo = '$cod_cargo'
        AND cv.estado = 1
        ORDER BY cv.codigo ASC";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$resp_control_versiones = $stmt->fetchAll(PDO::FETCH_ASSOC);

// MAUNAL //
// MANUAL DE APROBACIÓN - REVISIONES
// $sql = "SELECT  ma.codigo, ma.cod_etapa, ma.cod_cargo, ma.cod_area, ma.nro_version, DATE_FORMAT(ma.fecha_inicio, '%d-%m-%Y') as fecha_inicio, DATE_FORMAT(ma.fecha_fin, '%d-%m-%Y') as fecha_fin
//         FROM manuales_aprobacion ma
//         WHERE ma.cod_cargo = '$cod_cargo'
//         AND ma.cod_estado = 3
//         ORDER BY ma.codigo ASC";
// $stmt = $dbh->prepare($sql);
// $stmt->execute();
// $resp_manual_aprobacion_revisiones = $stmt->fetchAll(PDO::FETCH_ASSOC);

// * Detalle de Historial
if(empty($resp_manual_aprobacion)){
    $resp_manual_seguimiento = [];
}else{
    $sql = "SELECT mas.codigo, mas.cod_manual, mas.cod_etapa, CONCAT(p.primer_nombre, ' ', p.paterno, ' ', p.materno) as personal, mas.cod_seguimiento_estado,
        DATE_FORMAT(mas.fecha,'%d-%m-%Y') as fecha, mas.observacion, mas.detalle_descriptivo, c.nombre as cargo, COALESCE(mae.descripcion, 'ELABORADO POR:') as nombre_etapa
        FROM manuales_aprobacion_seguimiento mas
        LEFT JOIN personal p ON p.codigo = mas.cod_personal
        LEFT JOIN cargos c ON c.codigo = p.cod_cargo
        LEFT JOIN manuales_aprobacion_etapas mae ON mae.codigo = mas.cod_etapa
        WHERE mas.cod_manual = '".$resp_manual_aprobacion['codigo']."'";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $resp_manual_seguimiento = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// * Responsabilidades GENERALES DE IBNORCA
$sql = "SELECT r.codigo, r.nombre
        FROM responsabilidades_generales r
        WHERE r.estado = 1
        ORDER BY r.codigo ASC";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$resp_resposabilidadesGenerales = $stmt->fetchAll(PDO::FETCH_ASSOC);

// * Responsabilidades del Cargo
$sql = "SELECT cf.cod_cargo, cf.cod_funcion, cf.nombre_funcion
        FROM cargos_funciones cf
        WHERE cf.cod_cargo = '$cod_cargo'
        AND cf.cod_estado = 1
        ORDER BY cf.orden ASC";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$resp_resposabilidadesCargos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// * Autoridades del Cargo
$sql = "SELECT ca.cod_cargo, ca.cod_autoridad, ca.nombre_autoridad
        FROM cargos_autoridades ca
        WHERE ca.cod_cargo = '$cod_cargo'
        AND ca.cod_estadoautoridad = 1
        ORDER BY ca.orden ASC";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$resp_autoridadesCargos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// * AREAS nivel 2 del Cargo seleccionado 
$sql = "SELECT a.codigo, a.cod_padre
        FROM areas a
        WHERE a.codigo = '".$resp_cargo['cod_area']."'";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$registro = $stmt->fetch(PDO::FETCH_ASSOC);

$cod_area_nivel2 = '';
// echo "hola";
// exit;
if ($registro) {
    $cod_direccion = 847;
    if($registro['codigo'] == $cod_direccion || $registro['cod_padre'] == $cod_direccion){
        // Primer Nivel
        $cod_area_nivel2 = 847;
    }else{
        // Niveles Inferiores
        $cod_area_nivel2 = buscarArea($resp_cargo['cod_area']);
    }
}
$sql = "SELECT a.codigo, a.nombre, a.abreviatura
        FROM areas a
        WHERE a.codigo = '$cod_area_nivel2'";
        // echo $sql;
$stmt = $dbh->prepare($sql);
$stmt->execute();
$resp_areanivel2 = $stmt->fetch(PDO::FETCH_ASSOC);

// * UNIDAD
// $sql = "SELECT u.nombre as unidad_organizacional
//         FROM cargos_areasorganizacion cao
//         LEFT JOIN areas_organizacion ao ON ao.codigo = cao.cod_areaorganizacion
//         LEFT JOIN unidades_organizacionales u ON u.codigo = ao.cod_unidad
//         WHERE cao.cod_cargo = '$cod_cargo'";
// $stmt = $dbh->prepare($sql);
// $stmt->execute();
// $resp_unidad = $stmt->fetch(PDO::FETCH_ASSOC);
// QUERY EN EL SISTEMA OFICIAL
$sql = "SELECT a.nombre as nombre_unidad
        FROM areas a 
        LEFT JOIN areas_organizacion ao ON ao.cod_area = a.codigo
        LEFT JOIN cargos_areasorganizacion ca ON ca.cod_areaorganizacion = a.codigo 
        where ca.cod_cargo = '$cod_cargo'
        limit 1";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$resp_unidad = $stmt->fetch(PDO::FETCH_ASSOC);


// * Cargos Superiores (En base al Padres)
$sql = "SELECT c.codigo, c.nombre as nombre, c.abreviatura, c.objetivo
            FROM cargos c
            WHERE c.codigo = '".$resp_cargo['cod_padre']."'
            AND c.cod_estadoreferencial = 1
            LIMIT 1";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$resp_cargo_sup = $stmt->fetch(PDO::FETCH_ASSOC);

// * Cargos Dependientes
$sql = "SELECT c.codigo, c.nombre as nombre, c.abreviatura, c.objetivo
            FROM cargos c
            WHERE c.cod_padre = '$cod_cargo'
            AND c.cod_estadoreferencial = 1
            ORDER BY c.cod_tipo_cargo ASC";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$resp_cargosDep = $stmt->fetchAll(PDO::FETCH_ASSOC);

/***********************************/
/*      CARGA DE DATOS EN PDF      */
/***********************************/
ob_start();             // Inicia el almacenamiento en búfer de salida
include('pdfHTML.php'); // Incluye el archivo PHP con el HTML
$html = ob_get_clean(); // Obtiene el contenido del búfer de salida y lo asigna a la variable $html
/**********************************/


$pdf->SetAutoPageBreak(true, 20);   // Establece el salto automático de página con el margen especificado
$pdf->SetFont('helvetica', '', 10); // Establece la fuente y el tamaño de letra predeterminados para el contenido
$pdf->writeHTML($html, true, false, true, false, ''); // Convierte el HTML a PDF manteniendo las hojas de estilo

// Genera el PDF y lo muestra en el navegador
$pdf->Output('manual_de_cargos_Ibnorca.pdf', 'I'); // Cambia 'nombre_del_archivo.pdf' por el nombre que deseas asignarle al archivo PDF generado
?>
