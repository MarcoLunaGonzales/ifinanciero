<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';
require '../assets/phpqrcode/qrlib.php';

//require_once 'configModule.php';
require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';
$dbh = new Conexion();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
//RECIBIMOS LAS VARIABLES

$codigo_af = $_GET["codigo"];//codigoactivofijo
try{
    $stmtAF = $dbh->prepare("SELECT (select p.nombre from proyectos_financiacionexterna p where p.codigo=cod_proy_financiacion) as nom_proy_financiacion
    from activosfijos
     WHERE codigo=:codigo");
    $stmtAF->bindParam(':codigo',$codigo_af);
    $stmtAF->execute();
    $result = $stmtAF->fetch();
    $nom_proy_financiacion = $result['nom_proy_financiacion'];    

    $stmt = $dbh->prepare("SELECT a.codigo,a.codigoactivo,a.tipoalta,DATE_FORMAT(a.fechalta ,'%d/%m/%Y')as fechalta,a.activo,a.depreciacionacumulada,a.valorresidual,a.estadobien,(select d.nombre from depreciaciones d where d.codigo=a.cod_depreciaciones) as nombre_depreciaciones,(select CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=a.cod_responsables_responsable) as nombre_personal,(select t.tipo_bien from tiposbienes t where t.codigo=a.cod_tiposbienes)as tipo_bien,(select uo.nombre from unidades_organizacionales uo where uo.codigo=a.cod_unidadorganizacional) as nombre_uo2,(select uo.abreviatura from unidades_organizacionales uo where uo.codigo=a.cod_unidadorganizacional) as abrev_uo2,(select a.nombre from areas a where a.codigo=a.cod_area) as nombre_area,(select c.numero from comprobantes  c where c.codigo=a.cod_comprobante ) as comprobante,a.fecha_baja,a.obs_baja,(select CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=a.modified_by) as responsable_baja
        from activosfijos a WHERE a.codigo=$codigo_af");
    //Ejecutamos;    
    $stmt->execute();
    
    $result = $stmt->fetch();

    $codigo = $result['codigo'];
    $codigoactivo = $result['codigoactivo'];
    $tipoalta = $result['tipoalta'];
    $fechalta = $result['fechalta'];    
    $depreciacionacumulada = $result['depreciacionacumulada'];
    $valorresidual = $result['valorresidual'];    
    $estadobien = $result['estadobien'];
    $activo = $result['activo'];
    $comprobante = $result['comprobante'];    
    $nombre_personal = $result['nombre_personal'];
    $nombre_depreciaciones = $result['nombre_depreciaciones'];
    $tipo_bien = $result['tipo_bien'];
    $edificio = "";
    $oficina = "";    
    $nombre_uo2 = $result['nombre_uo2'];
    $abrev_uo2 = $result['abrev_uo2'];
    $nombre_area = $result['nombre_area'];

    $fecha_baja = $result['fecha_baja'];
    $obs_baja = $result['obs_baja'];
    $responsable_baja = $result['responsable_baja'];
    

    //==================================================================================================================
    //imagen
    $stmtIM = $dbh->prepare("SELECT * FROM activosfijosimagen  where codigo =:codigo");
    $stmtIM->bindParam(':codigo',$codigo_af);
    $stmtIM->execute();
    $resultIM = $stmtIM->fetch();
    //$codigo = $result['codigo'];
    $imagen = $resultIM['imagen'];
    //==================================================================================================================

    //$gestion2 = $_POST["gestion"];
    $stmt2 = $dbh->prepare("SELECT * 
    from mesdepreciaciones m, mesdepreciaciones_detalle md
    WHERE m.codigo = md.cod_mesdepreciaciones 
    and md.cod_activosfijos = :codigo order by m.codigo desc limit 0,5");
    // Ejecutamos
    //$stmt2->bindParam(':mes',$mes2);
    $stmt2->bindParam(':codigo',$codigo_af);

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
    $query2 = "SELECT (select uo.abreviatura from unidades_organizacionales uo where uo.codigo=cod_unidadorganizacional)as u_o,
    (select a.abreviatura from areas a where a.codigo=cod_area)as area,fechaasignacion,estadobien_asig,
    (select CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=cod_personal)as nombre_personal,cod_estadoasignacionaf,
    (select eaf.nombre from estados_asignacionaf eaf where eaf.codigo=cod_estadoasignacionaf) as estadoAsigAF,
    fecha_recepcion,observaciones_recepcion,fecha_devolucion,observaciones_devolucion
    from activofijos_asignaciones
    where cod_activosfijos =".$codigo_af." order by fechaasignacion desc";
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
            '<table align="center" >'.
                '<tbody>';                

                    $row = $stmt2->fetch();
                        $d2_valorresidual_aux = $row["d2_valorresidual"];
                        $d10_valornetobs_aux = $row["d10_valornetobs"];
                        $d10_formateado=formatNumberDec($d10_valornetobs_aux);
                    $html.='<tr>'.
                        '<td class="text-left small" >'.
                            '<p>'.
                                '<b>Código Activo : </b>'.$codigoactivo.'<br>'.
                                '<b>Descripción : </b>'.$activo.'<br>'.
                                '<b>Oficina : </b>'.$nombre_uo2.' <br>'.
                                '<b>Area : </b>'.$nombre_area.' <br>'.
                                '<b>Rubro : </b>'.$nombre_depreciaciones.' <br>'.
                                '<b>Responsable : </b>'.$nombre_personal.' <br>'.
                                '<b>Tipo alta : </b>'.$tipoalta.'<br>'.
                                // '<b>Ubicación : </b>'.$edificio.'<br>'.
                                '<b>Estado Bien : </b>'.$estadobien.' <br>'.
                                '<b>Fecha alta : </b>'.$fechalta.'<br>'.
                                '<b>Tipo Bien : </b>'.$tipo_bien.'<br>'.
                                '<b>Ultimo Mes Depreciado : </b>'.$mes3.' / '.$gestion3.'<br>'.
                                '<b>Valor Neto (Bs): </b>'.$d10_formateado.'<br>'.'<br>';
                                if($fecha_baja<>null || $fecha_baja<>""){
                                    $html.='<b>Fecha Baja : </b>'.date('d/m/Y',strtotime($fecha_baja)).'<br>'.
                                    '<b>Responsable Baja : </b>'.$responsable_baja.'<br>'.
                                    '<b>Obs Baja : </b>'.$obs_baja.'<br>'.'<br>';
                                }

                                
                                $html.='<b>Proyecto Financiación : </b>'.$nom_proy_financiacion.
                            '</p>'.
                        '</td>'.
                        '<td class="text-center small">'; if($imagen!="" || $imagen!=null){
                            $html.='<img src="imagenes/'.$imagen.'" style="width: 30%; "><br>';
                            }
                                                        
                                $dir = 'qr_temp/';
                                if(!file_exists($dir)){
                                    mkdir ($dir);}
                                $fileName = $dir.$codigoactivo.'.png';
                                $tamanio = 2; //tamaño de imagen que se creará
                                $level = 'L'; //tipo de precicion Baja L, mediana M, alta Q, maxima H
                                $frameSize = 1; //marco de qr                                
                                $contenido = "Cod:".$codigoactivo."\nRubro:".$nombre_depreciaciones."\nDesc:".$activo."\nRespo.:".$abrev_uo2." - ".$nombre_personal."\n NC:".$comprobante;
                                QRcode::png($contenido, $fileName, $level, $tamanio,$frameSize);
                                $html.='<img src="'.$fileName.'"/>';
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
                        '<th class="font-weight-bold">Fecha Asig.</th>'.
                        '<th class="font-weight-bold">Estado bien Asig.</th>'.
                        '<th class="font-weight-bold">Personal</th>'.
                        '<th class="font-weight-bold">Oficina</th>'.
                        '<th class="font-weight-bold">Area</th>'.

                        '<th class="font-weight-bold">Estado Asignación</th>'.
                        '<th class="font-weight-bold">F. Recepción</th>'.
                        '<th class="font-weight-bold">Obs. Recepción</th>'.
                        '<th class="font-weight-bold">F. Devolución</th>'.
                        '<th class="font-weight-bold">Obs. Devolución</th>'.
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

                            '<td class="text-left small">'.$row["estadoAsigAF"].'</td>'.
                            '<td class="text-left small">'.$row["fecha_recepcion"].'</td>'.
                            '<td class="text-left small">'.$row["observaciones_recepcion"].'</td>'.
                            '<td class="text-left small">'.$row["fecha_devolucion"].'</td>'.
                            '<td class="text-left small">'.$row["observaciones_devolucion"].'</td>'.
                     '</tr>';
                     } 
                $html.='</tbody>'.
            '</table>'.        
        '</body>'.
      '</html>';           
descargarPDF("Ficha Activo Fijo - ".$codigo,$html);

?>

<?php 
} catch(PDOException $ex){
    echo "Un error ocurrio".$ex->getMessage();
}
?>
