<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';
//require_once 'styles.php';
//require_once 'configModule.php';

//require_once  __DIR__.'/../fpdf.php';
require_once  __DIR__.'/../fpdf_html.php';
$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$codigo_depreciacion=$_POST["cod_depreciaciones"];


$stmt = $dbh->prepare("SELECT * FROM depreciaciones where codigo =:codigo");
//Ejecutamos;
$stmt->bindParam(':codigo',$codigo_depreciacion);
$stmt->execute();
$result = $stmt->fetch();
$codigo2 = $result['codigo'];
$cod_empresa = $result['cod_empresa'];
$nombre2 = $result['nombre'];
$vida_util = $result['vida_util'];
$cod_estado = $result['cod_estado'];
$cod_cuentacontable = $result['cod_cuentacontable'];

$mes2 = $_POST["mes"];
$gestion2 = $_POST["gestion"];
$stmt2 = $dbh->prepare("select * 
    from mesdepreciaciones m, mesdepreciaciones_detalle md, activosfijos af
    WHERE m.codigo = md.cod_mesdepreciaciones and md.cod_activosfijos = af.codigo 
    and m.mes = :mes and m.gestion = :gestion");
// Ejecutamos
$stmt2->bindParam(':mes',$mes2);
$stmt2->bindParam(':gestion',$gestion2);

$stmt2->execute();
//resultado
$stmt2->bindColumn('codigoactivo', $codigoactivo);
$stmt2->bindColumn('activo', $activo);


$stmt2->bindColumn('mes', $mes);
$stmt2->bindColumn('gestion', $gestion);
$stmt2->bindColumn('ufvinicio', $ufvinicio);
$stmt2->bindColumn('ufvfinal', $ufvfinal);
//$stmt2->bindColumn('estado', $estado);
//$stmt2->bindColumn('codigo1', $codigo1);
$stmt2->bindColumn('cod_mesdepreciaciones', $cod_mesdepreciaciones);
$stmt2->bindColumn('cod_activosfijos', $cod_activosfijos);
$stmt2->bindColumn('d2_valorresidual', $d2_valorresidual);
$stmt2->bindColumn('d3_factoractualizacion', $d3_factoractualizacion);
$stmt2->bindColumn('d4_valoractualizado', $d4_valoractualizado);
$stmt2->bindColumn('d5_incrementoporcentual', $d5_incrementoporcentual);
$stmt2->bindColumn('d6_depreciacionacumuladaanterior', $d6_depreciacionacumuladaanterior);
$stmt2->bindColumn('d7_incrementodepreciacionacumulada', $d7_incrementodepreciacionacumulada);
$stmt2->bindColumn('d8_depreciacionperiodo', $d8_depreciacionperiodo);
$stmt2->bindColumn('d9_depreciacionacumuladaactual', $d9_depreciacionacumuladaactual);
$stmt2->bindColumn('d10_valornetobs', $d10_valornetobs);
$stmt2->bindColumn('d11_vidarestante', $d11_vidarestante);


//echo $codigoactivo;
$pdf = new PDF_HTML();
$pdf->AddPage();
$pdf->SetFont('Arial','B',10);

    $pdf->SetFont('Arial', 'B', 15); //arial bold
    $pdf->SetFillColor(0); 
    $pdf->SetTextColor(225); 
    $pdf->Cell(0, 10, "Depreciacion de Activos Fijos por Rubro por Mes", 0, 1, 'C', true); //de la linea 1 a la 20

    //otra fila
    $pdf->SetFont(Arial, '',10);
    $pdf->SetTextColor(0);//verde
    $pdf->Cell(50, 13, "Rubro:"); 
    $pdf->Cell(50, 13, $nombre2);

    //otra fila
    $pdf->Ln();
    $pdf->Cell(50, 13, "Mes");//ancho, alto, valor
    $pdf->Cell(50, 13, $_POST["mes"]);
    $pdf->Cell(50, 13, "Gestion");
    $pdf->Cell(50, 13, $_POST["gestion"]);
    
    $pdf->Ln();
    $pdf->SetFillColor(0); 
    //$pdf->SetTextColor(225); 
    $pdf->Cell(0, 0.2, "", 0, 1, 'C', true); //de la linea 1 a la 20

    $pdf->SetFont(Arial, '',7)  ;
    //tabla
    $ancho = 20;
    $alto = 10;
    $pdf->Ln();
    $pdf->Cell($ancho, $alto, "Valor Residual");
    $pdf->Cell($ancho, $alto, "Factor Actual.");
    $pdf->Cell($ancho, $alto, "Valor Actual.");
    $pdf->Cell($ancho, $alto, "Inc. %");
    $pdf->Cell($ancho, $alto, "Depr Acm. Ant.");
    $pdf->Cell($ancho, $alto, "Incr. Depr. Acum.");
    $pdf->Cell($ancho, $alto, "Depr. Acum. Act."); 
    $pdf->Cell($ancho, $alto, "Valor Neto Bs");
    $pdf->Cell($ancho, $alto, "Rest. Meses");
    while ($row = $stmt2->fetch(PDO::FETCH_BOUND)) {
        $pdf->Ln();
        $pdf->Cell(50, $alto, $codigoactivo);
        $pdf->Cell(50, $alto, $activo);
        
     
        $pdf->Ln();
        $pdf->Cell($ancho, $alto, $d2_valorresidual);
        $pdf->Cell($ancho, $alto, $d3_factoractualizacion);
        $pdf->Cell($ancho, $alto, $d4_valoractualizado);
        $pdf->Cell($ancho, $alto, $d5_incrementoporcentual);
        $pdf->Cell($ancho, $alto, $d6_depreciacionacumuladaanterior);
        $pdf->Cell($ancho, $alto, $d7_incrementodepreciacionacumulada);
        $pdf->Cell($ancho, $alto, $d9_depreciacionacumuladaactual);
        $pdf->Cell($ancho, $alto, $d10_valornetobs);
        $pdf->Cell($ancho, $alto, $d11_vidarestante);
    }


//$html = "<h1>Activo>:".$codigoactivo."</h1></br>";
//$html += "<h2>Vida Util (Meses):".$vidautilmeses."</h2>";

//$pdf->WriteHTML2($html);
$pdf->Output();

?>
