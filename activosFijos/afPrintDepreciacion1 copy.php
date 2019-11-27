<?php
//LA HISTORIA DE UN ACTIVO
require_once __DIR__.'/../conexion.php';
//require_once 'styles.php';
//require_once 'configModule.php';

//require_once  __DIR__.'/../fpdf.php';
require_once  __DIR__.'/../fpdf_html.php';
$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$codigo=$_GET["codigo"];

$stmt = $dbh->prepare("SELECT * FROM activosfijos where codigo = :codigo");
//Ejecutamos;
$stmt->bindParam(':codigo',$codigo);
$stmt->execute();
$result = $stmt->fetch();
$codigo = $result['codigo'];
$codigoactivo = $result['codigoactivo'];
$tipoalta = $result['tipoalta'];
$fechalta = $result['fechalta'];
$indiceufv = $result['indiceufv'];
$tipocambio = $result['tipocambio'];
$moneda = $result['moneda'];
$valorinicial = $result['valorinicial'];
$depreciacionacumulada = $result['depreciacionacumulada'];
$valorresidual = $result['valorresidual'];
$cod_depreciaciones = $result['cod_depreciaciones'];
$cod_tiposbienes = $result['cod_tiposbienes'];
$vidautilmeses = $result['vidautilmeses'];
$estadobien = $result['estadobien'];
$otrodato = $result['otrodato'];
$cod_ubicaciones = $result['cod_ubicaciones'];
$cod_empresa = $result['cod_empresa'];
$activo = $result['activo'];
$cod_responsables_responsable = $result['cod_responsables_responsable'];
$cod_responsables_autorizadopor = $result['cod_responsables_autorizadopor'];
$created_at = $result['created_at'];
$created_by = $result['created_by'];
$modified_at = $result['modified_at'];
$modified_by = $result['modified_by'];
$vidautilmeses_restante = $result['vidautilmeses_restante'];




$stmt2 = $dbh->prepare("select * from mesdepreciaciones m, mesdepreciaciones_detalle md
    where m.codigo = md.cod_mesdepreciaciones and md.cod_activosfijos =:codigo");
// Ejecutamos
$stmt2->bindParam(':codigo',$codigo);
$stmt2->execute();
//resultado
$stmt2->bindColumn('mes', $mes);
$stmt2->bindColumn('gestion', $gestion);
$stmt2->bindColumn('ufvinicio', $ufvinicio);
$stmt2->bindColumn('ufvfinal', $ufvfinal);
$stmt2->bindColumn('estado', $estado);
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
    $pdf->Cell(0, 10, "Depreciacion de Activos Fijos", 0, 1, 'C', true); //de la linea 1 a la 20

    //otra fila
    //$pdf->SetFont(Arial, '',10);
    $pdf->SetTextColor(0);//verde
    $pdf->Cell(50, 13, "Activo Numero:"); 
    $pdf->Cell(50, 13, $codigoactivo);

    //otra fila
    $pdf->Ln();
    $pdf->Cell(50, 13, "Valor Inicial");//ancho, alto, valor
    $pdf->Cell(50, 13, $valorinicial);
    $pdf->Cell(50, 13, "Depreciacion Acumulada");
    $pdf->Cell(50, 13, $depreciacionacumulada);
    
    $pdf->Ln();
    $pdf->SetFillColor(0); 
    //$pdf->SetTextColor(225); 
    $pdf->Cell(0, 0.2, "", 0, 1, 'C', true); //de la linea 1 a la 20

    //$pdf->SetFont(Arial, '',7)  ;
    //tabla
    $ancho = 20;
    $alto = 10;
    $pdf->Ln();
    $pdf->Cell($ancho, $alto, "Mes Gestion");//ancho, alto, valor
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
        $pdf->Cell($ancho, $alto, $mes." ".$gestion);//ancho, alto, valor
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
