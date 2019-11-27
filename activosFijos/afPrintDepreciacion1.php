<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';
require '../assets/phpqrcode/qrlib.php';

//require_once 'configModule.php';
require_once __DIR__.'/../functions.php';



$dbh = new Conexion();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
//RECIBIMOS LAS VARIABLES

$codigo = $_GET["codigo"];//codigoactivofijo
try{
    $stmt = $dbh->prepare("select * from v_activosfijos WHERE codigo=:codigo");
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
    $nombre_personal = $result['nombre_personal'];
    $nombre_depreciaciones = $result['nombre_depreciaciones'];
    $tipo_bien = $result['tipo_bien'];
    $edificio = $result['edificio'];
    $oficina = $result['oficina'];
    $nombre_uo = $result['nombre_uo'];
    $nombre_uo2 = $result['nombre_uo2'];

    //==================================================================================================================
    //imagen
    $stmtIM = $dbh->prepare("SELECT * FROM activosfijosimagen  where codigo =:codigo");
    $stmtIM->bindParam(':codigo',$codigo);
    $stmtIM->execute();
    $resultIM = $stmtIM->fetch();
    //$codigo = $result['codigo'];
    $imagen = $resultIM['imagen'];


    //==================================================================================================================

    //$gestion2 = $_POST["gestion"];
    $stmt2 = $dbh->prepare("select * 
    from mesdepreciaciones m, mesdepreciaciones_detalle md
    WHERE m.codigo = md.cod_mesdepreciaciones 
    and md.cod_activosfijos = :codigo");
    // Ejecutamos
    //$stmt2->bindParam(':mes',$mes2);
    $stmt2->bindParam(':codigo',$codigo);

    $stmt2->execute();
    //resultado
    //$stmt2->bindColumn('codigoactivo', $codigoactivo);
    //$stmt2->bindColumn('activo', $activo);


    $stmt2->bindColumn('mes', $mes3);
    $stmt2->bindColumn('gestion', $gestion3);
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


    //asignaciones
    $query2 = "SELECT afa.fechaasignacion,afa.estadobien_asig,
    (select nombre from personal2 where codigo= afa.cod_personal)as nombre_personal,
    (select nombre from unidades_organizacionales where codigo=afa.cod_unidadorganizacional) as u_o,
    (select nombre from areas where codigo=afa.cod_area)as area FROM activofijos_asignaciones afa 
    where cod_activosfijos = ".$codigo;
    $statement2 = $dbh->query($query2);


$html = '';
$html.='<html>'.
         '<head>'.
             '<!-- CSS Files -->'.
             '<link rel="icon" type="image/png" href="../assets/img/favicon.png">'.
             '<link href="../assets/libraries/plantillaPDF.css" rel="stylesheet" />'.
           '</head>';
$html.='<body>'.
        '<script type="text/php">'.
      'if ( isset($pdf) ) {'. 
        '$font = Font_Metrics::get_font("helvetica", "normal");'.
        '$size = 9;'.
        '$y = $pdf->get_height() - 24;'.
        '$x = $pdf->get_width() - 15 - Font_Metrics::get_text_width("1/1", $font, $size);'.
        '$pdf->page_text($x, $y, "{PAGE_NUM}/{PAGE_COUNT}", $font, $size);'.
      '}'.
    '</script>';
$html.=  '<header class="header">'.            
            '<img class="imagen-logo-izq" src="../assets/img/ibnorca2.jpg">'.
            '<div id="header_titulo_texto">Ficha De Activo Fijo</div>'.

            '<br><br><br><br>'.
            '<table align="center">'.
                '<tbody>';
                    // while ($row = $stmt2->fetch()) { 
                    //     $d2_valorresidual_aux = $row["d2_valorresidual"];
                    //     $d10_valornetobs_aux = $row["d10_valornetobs"];
                    // }

                    $row = $stmt2->fetch();
                        $d2_valorresidual_aux = $row["d2_valorresidual"];
                        $d10_valornetobs_aux = $row["d10_valornetobs"];
                    $html.='<tr>'.
                        '<td class="text-left small">'.
                            '<p>'.
                                '<b>Código Activo : </b>'.$codigoactivo.'<br>'.
                                '<b>Descripción : </b>'.$activo.'<br>'.
                                '<b>Unidad Organizacional : </b>'.$nombre_uo2.' <br>'.
                                '<b>Rubro : </b>'.$nombre_depreciaciones.' <br>'.
                                '<b>Responsable : </b>'.$nombre_personal.' <br>'.
                                '<b>Tipo alta : </b>'.$tipoalta.'<br>'.
                                '<b>Ubicacion : </b>'.$edificio.'<br>'.
                                '<b>Estado Bien : </b>'.$estadobien.' <br>'.
                                '<b>Fecha alta : </b>'.$fechalta.'<br>'.
                                '<b>Tipo Bien : </b>'.$tipo_bien.'<br>'.
                                '<b>Valor Residual : </b> '.$d2_valorresidual_aux.'<br>'.
                                '<b>Valor Neto Bs : </b>'.$d10_valornetobs_aux.
                            '</p>'.
                        '</td>'.
                        '<td class="text-right small">'.
                            '<img src="imagenes/'.$imagen.'" style="width: 150px; height: 150px;"><br>';
                            
                                $dir = 'qr_temp/';
                                if(!file_exists($dir)){
                                    mkdir ($dir);}
                                $fileName = $dir.'test.png';
                                $tamanio = 4; //tamaño de imagen que se creará
                                $level = 'Q'; //tipo de precicion Baja L, mediana M, alta Q, maxima H
                                $frameSize = 1; //marco de qr
                                $contenido = $codigoactivo;
                                QRcode::png($contenido, $fileName, $level, $tamanio,$frameSize);
                                echo '<img src="'.$fileName.'"/>';
                        $html.='</td>'.
                    '</tr>'.
                    '<hr>'.
                    '<tr>'. 
                        '<td>'.
                        '</td>'.
                        '<td>'.
                        '</td>'.
                        
                    '</tr>'.

                    '<hr>'.

                '</tbody>'.

            '</table>'.
            '<br><br><br><br>'.
            '<h4> Asignaciones</h4>'.
            '<table class="table">'.
                '<thead>'.
                    '<tr>'.
                        '<th class="font-weight-bold">Fecha</th>'.
                        '<th class="font-weight-bold">Estado</th>'.
                        '<th class="font-weight-bold">Personal</th>'.
                        '<th class="font-weight-bold">UO</th>'.
                        '<th class="font-weight-bold">Area</th>'.
                    '</tr>'.
                '</thead>'.
                '<tbody>';
                    while ($row = $statement2->fetch()) {
                       $html.='<tr>'.
                            '<td class="text-left small">'.$row["fechaasignacion"].'</td>'.
                            '<td class="text-left small">'.$row["estadobien_asig"].'</td>'.
                 
                            '<td class="text-left small">'.$row["nombre_personal"].'</td>'.
                            '<td class="text-left small">'.$row["u_o"].'</td>'.
                         '<td class="text-left small">'.$row["area"].'</td>'.
                     '</tr>';
                     } 
                $html.='</tbody>'.
            '</table>'.        
        '</body>'.
      '</html>';           
descargarPDF("IBNORCA - ".$unidadC." (".$tipoC.", ".$numeroC.")",$html);

?>

<?php 
} catch(PDOException $ex){
    echo "Un error ocurrio".$ex->getMessage();
}
?>
