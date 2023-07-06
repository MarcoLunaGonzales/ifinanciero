<?php
require_once '../conexion.php';
require_once '../assets/tcpdf/tcpdf.php';

// Clase personalizada que hereda de TCPDF
class CustomTCPDF extends TCPDF {
    // Método para personalizar el pie de página
    public function Footer() {
        // Obtener los márgenes establecidos
        $leftMargin = $this->getOriginalMargins()['left'];
        $rightMargin = $this->getOriginalMargins()['right'];
        // Establecer la posición vertical para el pie de página
        $this->SetY(-15); // Posición vertical para el pie de página (aquí se establece la distancia desde la parte inferior)
        // HTML del pie de página personalizado con estilos
        $footer = '<table style="border: 1px solid #c0c0c0; border-radius: 5px; margin-left: ' . $leftMargin . 'mm; margin-right: ' . $rightMargin . 'mm;">
                        <tr style="padding: 18px; ">
                            <td style="text-align: center; border: 1px solid #c0c0c0;"><b>IBNORCA ©</b></td>
                            <td style="text-align: center; border: 1px solid #c0c0c0;"><b>Código:</b> MP-GTH-39.00</td>
                            <td style="text-align: center; border: 1px solid #c0c0c0;"><b>V:</b> 2022-07-07</td>
                            <td style="text-align: center; border: 1px solid #c0c0c0;"><b> Página ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages() . '</b></td>
                        </tr>
                    </table>';
        // Imprimir el pie de página personalizado
        $this->SetFont('helvetica', 'I', 10); // Establece la fuente y el tamaño de letra para el pie de página
        $this->writeHTML($footer, true, false, true, false, ''); // Convierte el HTML a PDF manteniendo las hojas de estilo
    }
}

// Conexión BD
$dbh = new Conexion();
// Crea una instancia de CustomTCPDF en lugar de TCPDF
$pdf = new CustomTCPDF('P', 'mm', 'Letter', true, 'UTF-8'); // Cambia 'A4' por 'Letter' para establecer el tamaño de papel como carta

// Establece los márgenes
$pdf->SetMargins(15, 15, 15); // Establece el mismo margen en los cuatro lados del documento y habilita el margen inferior automático para el pie de página

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
$obj_descripcion = "[DESCRIPCIÓN EN TEXTO DE LA DESCRIPCIÓN DEL CARGO].";

// * Cargo
$sql = "SELECT c.codigo, UPPER(c.nombre) as nombre, c.abreviatura
        FROM cargos c
        WHERE c.codigo = 1
        LIMIT 1";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$resp_cargo = $stmt->fetch(PDO::FETCH_ASSOC);

// * Responsabilidades del Cargo
$sql = "SELECT cf.cod_cargo, cf.cod_funcion, cf.nombre_funcion
        FROM cargos_funciones cf
        WHERE cf.cod_cargo = 1";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$resp_resposabilidadesCargos = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
