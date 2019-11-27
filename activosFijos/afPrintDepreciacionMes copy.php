<?php

require_once __DIR__.'/../conexion.php';
require_once __DIR__.'/../conexion.php';
//require_once 'styles.php';
//require_once 'configModule.php';

//require_once  __DIR__.'/../fpdf.php';
require_once  __DIR__.'/../fpdf_html.php';




$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$codigo=$_GET["codigo"];

//echo "n".$codigo."n";

$stmt2 = $dbh->prepare("select * from mesdepreciaciones WHERE codigo=:codigo");
// Ejecutamos
$stmt2->bindParam(':codigo',$codigo);
$stmt2->execute();
$result2 = $stmt2->fetch();
$codigo2 = $result2['codigo'];
$mes = $result2['mes'];
$gestion = $result2['gestion'];
$ufvinicio = $result2['ufvinicio'];
$ufvfinal = $result2['ufvfinal'];
$estado2 = $result2['estado'];

//echo $mes;

$query = "select * from mesdepreciaciones_detalle, activosfijos where activosfijos.codigo = mesdepreciaciones_detalle.cod_activosfijos and mesdepreciaciones_detalle.cod_mesdepreciaciones=".$codigo;
$statement = $dbh->query($query);

//echo $codigoactivo;
$pdf = new PDF_HTML();
$pdf->AddPage();
$pdf->SetFont('Arial','B',10);

    $pdf->SetFont('Arial', 'B', 15); //arial bold
    $pdf->SetFillColor(0); 
    $pdf->SetTextColor(225); 
    $pdf->Cell(0, 10, "Depreciacion de Activos Fijos Total por Mes", 0, 1, 'C', true); //de la linea 1 a la 20
    
    //otra fila
    /*
    $pdf->SetFont(Arial, '',10);
    
    $pdf->Cell(50, 13, "Rubro:"); 
    $pdf->Cell(50, 13, $nombre2);
    */
    //otra fila
    $pdf->SetTextColor(0);//verde
    $pdf->Ln();
    $pdf->Cell(50, 13, "Mes");//ancho, alto, valor
    $pdf->Cell(50, 13, $mes);
    $pdf->Cell(50, 13, "Gestion");
    $pdf->Cell(50, 13, $gestion);
    
    $pdf->Ln();
    $pdf->SetFillColor(0); 
    //$pdf->SetTextColor(225); 
    $pdf->Cell(0, 0.2, "", 0, 1, 'C', true); //de la linea 1 a la 20

    $pdf->SetFont(Arial, '',7)  ;
    //tabla
    $ancho = 20;
    $alto = 10;
    $pdf->Ln();
    $pdf->Cell($ancho, $alto, "Activo");
    $pdf->Cell($ancho, $alto, "Valor Residual");
    $pdf->Cell($ancho, $alto, "Factor Actual.");
    $pdf->Cell($ancho, $alto, "Valor Actual.");
    $pdf->Cell($ancho, $alto, "Inc. %");
    $pdf->Cell($ancho, $alto, "Depr Acm. Ant.");
    $pdf->Cell($ancho, $alto, "Incr. Depr. Acum.");
    $pdf->Cell($ancho, $alto, "Depr. Acum. Act."); 
    $pdf->Cell($ancho, $alto, "Valor Neto Bs");
    $pdf->Cell($ancho, $alto, "Rest. Meses");

    
    $index=1;
    while ($row = $statement->fetch()){ 
        $pdf->Ln();
        $pdf->Cell(50, $alto, $codigoactivo);
        $pdf->Cell(50, $alto, $activo);
        
     
        $pdf->Ln();
        $pdf->Cell($ancho, $alto, $row["codigoactivo"]);
        $pdf->Cell($ancho, $alto, $row["d2_valorresidual"]);
        $pdf->Cell($ancho, $alto, $row["d3_factoractualizacion"]);
        $pdf->Cell($ancho, $alto, $row["d4_valoractualizado"]);
        $pdf->Cell($ancho, $alto, $row["d5_incrementoporcentual"]);
        $pdf->Cell($ancho, $alto, $row["d6_depreciacionacumuladaanterior"]);
        $pdf->Cell($ancho, $alto, $row["d7_incrementodepreciacionacumulada"]);
        $pdf->Cell($ancho, $alto, $row["d9_depreciacionacumuladaactual"]);
        $pdf->Cell($ancho, $alto, $row["d10_valornetobs"]);
        $pdf->Cell($ancho, $alto, $row["d11_vidarestante"]);
    }


//$html = "<h1>Activo>:".$codigoactivo."</h1></br>";
//$html += "<h2>Vida Util (Meses):".$vidautilmeses."</h2>";

//$pdf->WriteHTML2($html);
$pdf->Output();

?>
