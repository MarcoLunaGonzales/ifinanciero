<?php


require_once __DIR__.'/../conexion.php';
require_once __DIR__.'/../functions.php';
require_once  __DIR__.'/../fpdf_html.php';

$dbh = new Conexion();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
//RECIBIMOS LAS VARIABLES

$codigo = $_GET["codigo"];//codigoactivofijo
try{
    $stmt = $dbh->prepare("SELECT afa.cod_activosfijos,(SELECT activo from activosfijos where codigo=afa.cod_activosfijos) as nombre_af,
        afa.fechaasignacion,(SELECT abreviatura from unidades_organizacionales where codigo=afa.cod_unidadorganizacional)as cod_unidadorganizacional,
        (SELECT abreviatura from areas where codigo=afa.cod_area) as cod_area,
        (SELECT CONCAT_WS(' ',paterno,materno,primer_nombre) from personal where codigo=afa.cod_personal)as cod_personal,afa.estadobien_asig,
        (SELECT nombre from estados_asignacionaf where codigo=afa.cod_estadoasignacionaf)as cod_estadoasignacionaf,
        afa.fecha_recepcion,afa.observaciones_recepcion,afa.fecha_devolucion,afa.observaciones_devolucion
        from activofijos_asignaciones afa
        where cod_activosfijos= :codigo ORDER BY codigo desc");
    //Ejecutamos;
    $stmt->bindParam(':codigo',$codigo);
    $stmt->execute();

    $result = $stmt->fetch();
    $cod_activosfijos = $result['cod_activosfijos'];
    $nombre_af = $result['nombre_af'];
    $fecha_asignacion = $result['fechaasignacion'];
    $cod_unidadorganizacional = $result['cod_unidadorganizacional'];
    $cod_area = $result['cod_area'];
    $cod_personal = $result['cod_personal'];
    $estadobien_asig = $result['estadobien_asig'];
    $cod_estadoasignacionaf = $result['cod_estadoasignacionaf'];
    $fecha_recepcion = $result['fecha_recepcion'];
    $observaciones_recepcion = $result['observaciones_recepcion'];

    $fecha_devolucion = $result['fecha_devolucion'];
    $observaciones_devolucion = $result['observaciones_devolucion'];
    //imagen
    $stmtIM = $dbh->prepare("SELECT * FROM activosfijosimagen  where codigo =:codigo");
    $stmtIM->bindParam(':codigo',$codigo);
    $stmtIM->execute();
    $resultIM = $stmtIM->fetch();
    //$codigo = $result['codigo'];
    $imagen = $resultIM['imagen'];

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
            '<div id="header_titulo_texto">Ficha Activo Fijo Asignado</div>'.

            '<br><br><br><br>'.
            '<table align="center">'.
               '<tbody>'.
                '<tr>'.
                    '<td  >'.
                        '<table class="table table-condensed">'.
                            
                                '<tr>'.
                                    '<td  class="text-left" >Codigo AF :</td>'.
                                    '<td class="text-left" >'.$cod_activosfijos.'</td>'.
                                '</tr>'.
                                '<tr>'.
                                    '<td class="text-left" >Activo Fijo :</td>'.
                                    '<td  class="text-left" >'.$nombre_af.'</td>'.
                                '</tr>'.

                                '</tr>'.
                                '<tr>'.
                                    '<td class="text-left" >Fecha Asignación :</td>'.
                                    '<td class="text-left" >'.$fecha_asignacion.'</td>'.
                                '</tr>'.
                                '<tr>'.
                                    '<td class="text-left" >Oficina :</td>'.
                                    '<td class="text-left" >'.$cod_unidadorganizacional.'</td>'.
                                '</tr>'.
                                '<tr>'.
                                    '<td class="text-left" >Area :</td>'.
                                    '<td  class="text-left" >'.$cod_area.'</td>'.
                                    
                                '</tr>'.
                                '<tr>'.
                                    '<td class="text-left" >Personal Asignado :</td>'.
                                    '<td class="text-left" >'.$cod_personal.'</td>'.
                                '</tr>'.
                                '<tr>'.
                                    '<td class="text-left" >Estado Bien </td>'.
                                    '<td class="text-left" >'.$estadobien_asig.'</td>'.
                                '</tr>'.
                                '<tr>'.
                                    '<td class="text-left" >Estado En Asignación :</td>'.
                                    '<td class="text-left" >'.$cod_estadoasignacionaf.'</td>'.
                                '</tr>'.
                                '<tr>'.
                                    '<td class="text-left" >Fecha Recepcion : </td>'.
                                    '<td class="text-left" >'.$fecha_recepcion.'</td>'.
                                '</tr>'.
                                '<tr>'.
                                    '<td class="text-left" >Obervaciones Recepcion:</td>'.
                                    '<td class="text-left" >'.$observaciones_recepcion.'</td>'.
                                '</tr>'.
                                '<tr>'.
                                    '<td class="text-left" >Fecha Devolución : </td>'.
                                    '<td class="text-left" >'.$fecha_devolucion.'</td>'.
                                '</tr>'.
                                '<tr>'.
                                    '<td class="text-left" >Obervaciones Devolución:</td>'.
                                    '<td class="text-left" >'.$observaciones_devolucion.'</td>'.
                                '</tr>'.
                                    
                                '<hr>'.
                        '</table>'.

                    '</td>'.
                    '<td class="text-center" >'.'<img src="imagenes/'.$imagen.'" style="width: 200px; height:200px;"><br>'.'</td>'.
                '</tr>'.
                
                '</tbody>'.
            '</table>'.  
            '</header>'.      
    '</body>'.
      '</html>';           
descargarPDF("IBNORCA - ",$html);

?>




<?php 
} catch(PDOException $ex){
    echo "Un error ocurrio".$ex->getMessage();
}
?>


<!-- <div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card" style="align-items: center;" >
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <h4 class="card-title"> 
                    <img  class="card-img-top"  src="../marca.png" style="width:100%; max-width:250px;">
                      Ficha Activo Fijo Asignado
                  </h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table>
                            <tr>
                                <td>
                                    <table class=" table-bordered table-condensed">
                                        <thead>
                                            <tr>
                                                <th>-</th>
                                                <th>-</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th  class="text-left" >Codigo af :</th>
                                                <td class="text-left" ><?php echo $cod_activosfijos; ?></td>
                                            </tr>
                                            <tr>
                                                <th class="text-left" >Activo Fijo :</th>
                                                <td  class="text-left" ><?php echo $nombre_af; ?></td>
                                            </tr>

                                            </tr>
                                            <tr>
                                                <th class="text-left" >Fecha Asignación :</th>
                                                <td class="text-left" ><?php echo $fecha_asignacion; ?></td>
                                            </tr>
                                            <tr>
                                                <th class="text-left" >Unidad Organizacional :</th>
                                                <td class="text-left" ><?php echo $cod_unidadorganizacional; ?></td>
                                            </tr>
                                            <tr>
                                                <th class="text-left" >Area :</th>
                                                <td  class="text-left" ><?php echo $cod_area; ?></td>
                                                
                                            </tr>
                                            <tr>
                                                <th class="text-left" >Personal Responsable :</th>
                                                <td class="text-left" ><?php echo $cod_personal; ?></td>
                                            </tr>
                                            <tr>
                                                <th class="text-left" >Estado Bien </td>
                                                <td class="text-left" ><?php echo $estadobien_asig; ?></th>
                                            </tr>
                                            <tr>
                                                <th class="text-left" >Estado En Asignación :</th>
                                                <td class="text-left" ><?php echo $cod_estadoasignacionaf; ?></td>
                                            </tr>
                                            <tr>
                                                <th class="text-left" >Fecha Recepcion : </th>
                                                <td class="text-left" ><?php echo  $fecha_recepcion; ?></td>
                                            </tr>
                                            <tr>
                                                <th class="text-left" >Obervaciones :</th>
                                                <td class="text-left" ><?php echo $observaciones_recepcion; ?></td>
                                            </tr>
                                                
                                            <hr>
                                        </tbody>
                                    </table>
                                </td>
                                <td><img src="imagenes/<?php echo $imagen;?>" ><br></td>
                            </tr>
                        </table>

                        
                    </div>
                </div>
              </div>
            </div>
          </div>  
        </div>
    </div> -->