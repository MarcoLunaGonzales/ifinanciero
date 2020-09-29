<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';
require '../assets/phpqrcode/qrlib.php';

//require_once 'configModule.php';
require_once __DIR__.'/../functions.php';
$dbh = new Conexion();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
set_time_limit(300);
//RECIBIMOS LAS VARIABLES
$unidadOrganizacional=$_POST["unidad_organizacional"];
$areas=$_POST["areas"];
$rubros=$_POST["rubros"];
$personal_x=$_POST["personal"];


$unidadOrgString=implode(",", $unidadOrganizacional);
$areaString=implode(",", $areas);
$rubrosString=implode(",", $rubros);
$personalString=implode(",", $personal_x);

try{
    $sqlActivos="SELECT codigo,codigoactivo,activo,
(select uo.abreviatura from unidades_organizacionales uo where uo.codigo=cod_unidadorganizacional)as abr_uo,
(select a.abreviatura from areas a where a.codigo=cod_area) as abr_area,
(select concat_ws(' ',r.paterno,r.materno,r.primer_nombre) from personal r where r.codigo=cod_responsables_responsable) as nombre_responsable,(select c.numero from comprobantes  c where c.codigo=cod_comprobante ) as comprobante
from activosfijos 
where cod_estadoactivofijo = 1 and cod_unidadorganizacional in ($unidadOrgString) and cod_area in ($areaString) and cod_depreciaciones in ($rubrosString) and cod_responsables_responsable in ($personalString)";  

//echo $sqlActivos;

$stmtActivos = $dbh->prepare($sqlActivos);
$stmtActivos->execute();

// bindColumn
$stmtActivos->bindColumn('codigo', $codigoX);
$stmtActivos->bindColumn('codigoactivo', $codigoActivoX);
$stmtActivos->bindColumn('activo', $activoX);
$stmtActivos->bindColumn('abr_uo', $abr_uoX);
$stmtActivos->bindColumn('abr_area', $abr_areaX);
$stmtActivos->bindColumn('nombre_responsable', $nombre_responsableX);
$stmtActivos->bindColumn('comprobante', $comprobanteX);
    


$html = '';
$html.='<html>'.
         '<head>'.
             '<!-- CSS Files -->'.
             '<link rel="icon" type="image/png" href="../assets/img/favicon.png">'.
             '<link href="../assets/libraries/plantillaPDF3.css" rel="stylesheet" />'.
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
$html.='<table class="table">'.
                '<tbody><tr>';
                    $cont=0;
                    while ($rowActivos = $stmtActivos->fetch(PDO::FETCH_ASSOC)) {
                       $stmtRubro = $dbh->prepare("SELECT (select d.nombre from depreciaciones d where d.codigo=cod_depreciaciones) as nombreRubro
                        from activosfijos where codigo=$codigoX");
                        $stmtRubro->execute();
                        $resultRubro = $stmtRubro->fetch();
                        $nombreRubro = $resultRubro['nombreRubro'];

                       if($cont<3){
                        $html.='<td style="border-bottom: 0px solid black;" height="2,5cm" width="6,7cm">'.
                                    '<table class="tabla" align="center" style="border:hidden">'.
                                        '<tr>'.
                                        '<td style="border: hidden">';
                                            $dir = 'qr_temp/';
                                            if(!file_exists($dir)){
                                                mkdir ($dir);}
                                            $fileName = $dir.$codigoActivoX.'.png';
                                            $tamanio = 1; //tamaño de imagen que se creará
                                            $level = 'Q'; //tipo de precicion Baja L, mediana M, alta Q, maxima H
                                            $frameSize = 1; //marco de qr
                                            // $contenido = $codigoActivoX;
                                            $contenido = "Cod:".$codigoX."\nRubro:".$nombreRubro."\nDesc:".$activoX."\nRespo.:".$abr_uoX." - ".$nombre_responsableX."\n NC:".$comprobanteX;
                                            //QRcode::png($contenido, $fileName, $level, $tamanio,$frameSize);
                                            QRcode::png($contenido, $fileName, $level, $tamanio,$frameSize);
                                            $html.= '<img src="'.$fileName.'"/>';

                                        $html.='</td>'.
                                        '<td style="border: hidden"><small><p align="left">Codigo: '.$codigoActivoX.'<br>Comprobante: '.$comprobanteX.'</p></small></td>'.
                                        '<td style="border: hidden"><img src="../assets/img/logo_ibnorca1.fw.png" width="30" /></td>'.
                                        '</tr>'.
                                        '<tr>'.
                                        '<td align="center" style="border: hidden" colspan=3><small>'.(substr($activoX, 0, 40))."...".'</small></td>'.                            
                                        '</tr>'.
                                    '</table>'.
                                '</td>';
                                if($cont<2){
                                    $html.='<td style="border-top: hidden;border-bottom: hidden;" height="2,5cm" width="0,3cm"></td>';
                                }
                                $cont++;
                       }else{
                        $html.='</tr><tr>'.
                                    '<td style="border-bottom: 0px solid black;"  height="2,5cm" width="6,7cm">
                                        <table align="center" style="border:hidden">
                                            <tr>
                                            <td style="border: hidden">';
                                                $dir = 'qr_temp/';
                                                if(!file_exists($dir)){
                                                    mkdir ($dir);}
                                                $fileName = $dir.$codigoActivoX.'.png';
                                                $tamanio = 1; //tamaño de imagen que se creará
                                                $level = 'L'; //tipo de precicion Baja L, mediana M, alta Q, maxima H
                                                $frameSize = 1; //marco de qr
                                                $contenido = "Cod:".$codigoX."\nRubro:".$nombreRubro."\nDesc:".$activoX."\nRespo.:".$abr_uoX.' - '.$nombre_responsableX;
                                                //QRcode::png($contenido, $fileName, $level, $tamanio,$frameSize);
                                                QRcode::png($contenido, $fileName, $level, $tamanio,$frameSize);
                                                $html.= '<img src="'.$fileName.'"/>';

                                            $html.='</td>
                                            <td style="border: hidden"><small><p align="left">Codigo: '.$codigoActivoX.' <br>Oficina: '.$abr_uoX.' <br>Area: '.$abr_areaX.' <br>Responsable: '.$nombre_responsableX.'</p></small></td>
                                            <td style="border: hidden"><img src="../assets/img/logo_ibnorca1.fw.png" width="30" /></td>
                                            </tr>
                                            <tr>
                                            <td align="center" style="border: hidden" colspan=3><small>'.(substr($activoX, 0, 40))."...".'</small></td>                         
                                            </tr>
                                        </table>
                                    </td>'.
                                    '<td style="border-top: hidden;border-bottom: hidden;" height="2,5cm" width="0,3cm"></td>';
                                    $cont=1;
                       }
                       
                    }
                    
                $html.='</tr>'.
                '</tbody>'.
            '</table>'. 
                               
        '</body>'.
      '</html>';           
descargarPDF1("IBNORCA-ETIQUETAS-AF",$html);

?>

<?php 
} catch(PDOException $ex){
    echo "Un error ocurrio".$ex->getMessage();
}
?>
