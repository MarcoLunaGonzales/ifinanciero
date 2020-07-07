<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';
require '../assets/phpqrcode/qrlib.php';

//require_once 'configModule.php';
require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';

$dbh = new Conexion();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
//RECIBIMOS LAS VARIABLES

$codigo_facturacion = $_GET["codigo"];//codigoactivofijo
try{
  //datos de solicitud de facturacion
  $stmtInfo = $dbh->prepare("SELECT *,DATE_FORMAT(fecha_solicitudfactura,'%d/%m/%Y')as fecha_solicitudfactura_x from solicitudes_facturacion where codigo=$codigo_facturacion");
  $stmtInfo->execute();
  $resultInfo = $stmtInfo->fetch();
  $cod_simulacion_servicio = $resultInfo['cod_simulacion_servicio'];  
  $cod_unidadorganizacional = $resultInfo['cod_unidadorganizacional'];  
  $cod_area = $resultInfo['cod_area'];    
  $fecha_solicitudfactura = $resultInfo['fecha_solicitudfactura_x'];  
  $cod_tipoobjeto = $resultInfo['cod_tipoobjeto'];  
  $cod_tipopago = $resultInfo['cod_tipopago'];  
  $cod_cliente = $resultInfo['cod_cliente'];  
  $cod_personal = $resultInfo['cod_personal'];  //responsable
  $razon_social = $resultInfo['razon_social'];  
  $nit = $resultInfo['nit'];  
  $observaciones = $resultInfo['observaciones'];  
  $nro_correlativo = $resultInfo['nro_correlativo'];  
  $codigo_alterno = $resultInfo['codigo_alterno'];  
  $tipo_solicitud = $resultInfo['tipo_solicitud'];  
  $dias_credito = $resultInfo['dias_credito'];  
  $persona_contacto = $resultInfo['persona_contacto'];  
  
  $nombre_unidad=nameUnidad($cod_unidadorganizacional);
  $abrev_area=trim(abrevArea($cod_area),'-');
  $nombre_cliente=nameCliente($cod_cliente);
  $nombre_responsable=namePersonal($cod_personal);
  $tc=obtenerValorTipoCambio(2,strftime('%Y-%m-%d',strtotime($fecha_solicitudfactura)));
  $usd=$tc;
  $codigo_servicio="$codigo_alterno";

$html = '';
$html.='<html>'.
            '<head>'.
                '<!-- CSS Files -->'.
                '<link rel="icon" type="image/png" href="../assets/img/favicon.png">'.
                '<link href="../assets/libraries/plantillaPDFCajaChica.css" rel="stylesheet" />'.
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
          '<table class="table">
            <tr>
              <td align="center" class="table-title"><b>'.obtenerValorConfiguracionFactura(1).'</b></td>
              <td rowspan="3" width="5%"><img width="100px"  src="../assets/img/ibnorca2.jpg"></td>
            </tr>
            <tr>
              <td align="center" class="table-title"><b>REGISTRO</b></td>
            </tr>
            <tr>
              <td align="center" class="table-title"><b>SOLICITUD FACTURACIÓN</b></td>            
            </tr>          
          </table>'.
          '<br>'.
          '<table class="table">
            <tr>
              <td align="left" width="20%" class="td-color-celeste"><b>Ciudad Y Fecha: </b></td>
              <td width="30%">'.$nombre_unidad.'</td>
              <td align="left" width="30%">'.$fecha_solicitudfactura.'</td>
              <td width="5%" class="td-color-celeste"><b>N°: </b></td>
              <td width="15%">'.$nro_correlativo.'</td>
            </tr>                    
          </table>'.
          '<br>'.
          '<table class="table">
            <tr>
              <td width="20%" height="8%" class="td-color-celeste"><b>Solicitante:</b></td>
              <td width="10%" colspan="2" >'.$nombre_responsable.'</td>
              <td width="3%" colspan="3" valign="top">Firma del solicitante:</td>
            </tr>
             <tr>
              <td class="td-color-celeste"><b>Cliente:</b></td>';
              $tipo_solicitud = $resultInfo['tipo_solicitud'];  
              if($tipo_solicitud==2 || $tipo_solicitud==7){                
                $nombre_cliente=obtenerNombreEstudiante($cod_cliente);
              }              
              $html.='<td width="10%" colspan="2" align="left">'.$nombre_cliente.'</td>
              <td width="3%" colspan="2" class="td-color-celeste"><b>Tipo Cambio:</b></td>
              <td width="4%" align="center" >'.$usd.'</td>              
            </tr>
            <tr>
              <td class="text-center td-color-celeste" colspan="2"><b>Factura a nombre de:</b></td>
              <td width="60%">'.$razon_social.'</td>
              <td width="3%" class="td-color-celeste"><b>NIT/CI: </b></td>
              <td width="3%" colspan="2">'.$nit.'</td>              
            </tr>    
            <tr>
              <td colspan="2" class="text-center td-color-celeste"><b>Código de servicio:</b></td>              
              <td width="55%" colspan="4"><b>'.$codigo_servicio.'</b></td>              
            </tr>                
          </table>'.
          '<br>'.
          '<table class="table">
            <thead>
              <tr class="td-color-celeste">
                <td rowspan="2" rowspan="2" width="5%" class="text-center"><b><b>N°</b></td>
                <td rowspan="2" width="6%" class="text-center"><b>C.Costo</b></td>
                <td rowspan="2" colspan="2" class="text-center"><b>Detalle</b></td>                
                <td rowspan="2" width="5%" class="text-center"><b>Cantidad</b></td>
                <td rowspan="2" width="8%" class="text-center"><b>P.U.</b></td>
                <td colspan="2" class="text-center"><b>Importe</b></td>
              </tr>
              <tr class="td-color-celeste">
                <td width="8%" class="text-center"><b>BOB</b></td>
                <td width="8%" class="text-center"><b>USD</b></td>
              </tr>                    
            </thead>
            <tbody>';
            // $sqlA="SELECT sf.*,t.descripcion as nombre_serv ,t.Codigo from solicitudes_facturaciondetalle sf,cla_servicios t 
            //     where sf.cod_claservicio=t.idclaservicio and sf.cod_solicitudfacturacion=$codigo_facturacion";

            $sqlA="SELECT *,(select t.Codigo from cla_servicios t where t.idclaservicio=cod_claservicio) as Codigo_alterno  from solicitudes_facturaciondetalle where cod_solicitudfacturacion=$codigo_facturacion";

            $stmt2 = $dbh->prepare($sqlA);                                   
            $stmt2->execute();
            $index=1;
            $sumaTotal_bob=0;
            $sumaTotal_sus=0;
            while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
              if($tipo_solicitud==2 || $tipo_solicitud==6 || $tipo_solicitud==7) $precio_unitario=$row2["precio"];
              else $precio_unitario=$row2["precio"]-$row2["descuento_bob"];
              
              if($usd>0)$precio_sus=($precio_unitario*$row2["cantidad"])/$usd;
              else $precio_sus=0;
              if($tipo_solicitud==2 || $tipo_solicitud==6 || $tipo_solicitud==7){
                $codigo_alterno_detalle=obtener_codigo_modulo_IBnorca($row2['cod_claservicio']);
              }else{
                $codigo_alterno_detalle=$row2['Codigo_alterno'];
              }
              
              $html.='<tr>
                <td  class="text-center"><b>'.$index.'</b></td>
                <td  class="text-center">'.$abrev_area.'</td>
                <td  class="text-left" width="15%">'.$codigo_alterno_detalle.'</td>
                <td  class="text-left"><small>'.$row2["descripcion_alterna"].'</small></td>
                <td  class="text-right">'.formatNumberDec($row2["cantidad"]).'</td>
                <td  class="text-right">'.formatNumberDec($precio_unitario).'</td>
                <td  class="text-right">'.formatNumberDec($precio_unitario*$row2["cantidad"]).'</td>
                <td  class="text-right">'.formatNumberDec($precio_sus).'</td>
              </tr>';
              $index++;
              $sumaTotal_bob+=$precio_unitario*$row2["cantidad"];
              $sumaTotal_sus+=$precio_sus;

            }
            //total de detalles
            $html.='<tr>                
                <td  class="text-right td-color-celeste" colspan="6"><b>TOTAL</b></td>
                <td  class="text-right "><b>'.formatNumberDec($sumaTotal_bob).'</b></td>
                <td  class="text-right "><b>'.formatNumberDec($sumaTotal_sus).'</b></td>
              </tr>';          
            $html.='</tbody>
          </table>'.
          '<br>';

          //tipos de pago
          $sqlTipoPago="SELECT cod_tipopago,porcentaje from solicitudes_facturacion_tipospago where cod_solicitudfacturacion=$codigo_facturacion ";
          $stmtTipoPago = $dbh->prepare($sqlTipoPago);                                   
          $stmtTipoPago->execute();          
          $html.='<table class="table" >
                  <tr class="td-color-celeste"><td class="text-center"><b>Forma de Pago</b></td></tr>';
          while ($rowTipoPago = $stmtTipoPago->fetch(PDO::FETCH_ASSOC)) {
              $cod_tipopago=$rowTipoPago['cod_tipopago'];
              $porcentaje=$rowTipoPago['porcentaje'];
              if($cod_tipopago==48){
                $html.='<tr><td class="text-left"><b>EN EFECTIVO('.$porcentaje.' %)</b></td></tr>';
              }elseif($cod_tipopago==47){//cheque
                $html.='<tr><td class="text-left"><b>CHEQUE('.$porcentaje.' %)</b></td></tr>';
              }elseif($cod_tipopago==217){//credito
                $datosContacto=obtenerDatosContactoSolFac($persona_contacto);                
                if($datosContacto!=''){                  
                  $array_datos = explode("#####", $datosContacto);
                  $nombre_contacto=$array_datos[0];
                  $telefono_contacto=$array_datos[1];
                  $Correo_contacto=$array_datos[2];
                }else{
                  $nombre_contacto='';
                  $telefono_contacto='';
                  $Correo_contacto='';
                }
                $html.='<tr>
                  <td>
                    <table class="table">
                      <tr>
                        <td class="td-border-none" colspan="2"><b>CRÉDITO('.$porcentaje.' %)</b></td>
                        <td class="td-border-none" colspan="2" align="right"><b>Días de Crédito : '.$dias_credito.'</b></td>
                      </tr>
                      <tr><td class="td-border-none" colspan="4"><br>Nota: El siguiente espacio se debe llenar en caso de que la factura sea solicitada con crédito.</td></tr>
                      <tr><td class="td-border-none" colspan="4"><b>DATOS DE LA EMPRESA A LA CUAL SE OTORGA EL CRÉDITO</b></td></tr>
                      <tr>
                        <td colspan="4">
                        <table class="table">
                          <tr>
                            <td width="30%"><b>Nombre Persona de Contacto:</b></td>
                            <td>'.$nombre_contacto.'</td>
                          </tr>
                          <tr>
                            <td><b>Nombre contacto Personal Administrativo:</b></td>
                            <td></td>
                          </tr>
                          <tr>
                            <td><b>Teléfono:</b></td>
                            <td>'.$telefono_contacto.'</td>
                          </tr>
                          <tr>
                            <td><b>Dirección:</b></td>
                            <td></td>
                          </tr>
                          <tr>
                            <td><b>Correo Electrónico:</b></td>
                            <td>'.$Correo_contacto.'</td>
                          </tr>
                        </table>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>';
              }elseif($cod_tipopago==49){
                $html.='<tr><td class="text-left"><b>DEPOSITO EN CUENTA('.$porcentaje.' %)</b></td></tr>';
              }else{
                $html.='<tr><td class="text-left">&nbsp</td></tr>';
              }    
          }
          $html.='</table><br>';

          $html.='<table class="table">
                  <tr class="td-color-celeste"><td class="text-center"><b>Observaciones</b></td></tr>
                  <tr><td>&nbsp;'.$observaciones.'</td></tr>
                </table><br><br><br>';
          // $cod_tipopago=3;
          //efectivo
          

          $personal_solicitante=namePersonal($cod_personal);
          $fecha_solicitud=obtenerFechaCambioEstado(2709,$codigo_facturacion,2726);

          $cod_tipopago_cred=obtenerValorConfiguracion(48);
          
          $userRevision=obtenerPersonaCambioEstado(2709,$codigo_facturacion,2727);  //en aprobacion          
          if($userRevision==0){
             $fecha_revision="Sin registro";    
             $personal_revision="Sin registro";    
          }else{
             $personal_revision=namePersonal($userRevision);
             $fecha_revision=obtenerFechaCambioEstado(2709,$codigo_facturacion,2727);
          }
          
          $userAprobado=obtenerPersonaCambioEstado(2709,$codigo_facturacion,2728);
          if($userAprobado==0){
             $fecha_aprobacion="Sin registro";    
             $personal_aprobacion="Sin registro";    
          }else{
             $personal_aprobacion=namePersonal($userAprobado);
             $fecha_aprobacion=obtenerFechaCambioEstado(2709,$codigo_facturacion,2728);
          }
          $userprocesado=obtenerPersonaCambioEstado(2709,$codigo_facturacion,2729);//procesado        
          if($userprocesado==0){
             $personal_procesado="Sin registro";    
             $fecha_procesado="Sin registro";
          }else{
             $personal_procesado=namePersonal($userprocesado);    
             $fecha_procesado=obtenerFechaCambioEstado(2709,$codigo_facturacion,2729);
          }


            
          $html.='
          <table class="table">
            <tr>
                <td class=" text-center" height="80px"></td>
                <td class=" text-center" height="80px"></td>
                <td class=" text-center" height="80px"></td>
                <td class=" text-center" height="80px"></td>
            </tr>
            <tr>
                <td class=" text-center" width="25%">Solicitante: '.$personal_solicitante.'</td>
                <td class=" text-center" width="25%">Revisión: '.$personal_revision.'</td>
                <td class=" text-center" width="25%">Aprobación: '.$personal_aprobacion.'</td>
                <td class=" text-center" width="25%">Procesado:' .$personal_procesado.'</td>
            </tr>
            <tr>
                <td class=" text-left">Fecha: '.$fecha_solicitud.'</td>
                <td class=" text-left">Fecha: '.$fecha_revision.'</td>
                <td class=" text-left">Fecha: '.$fecha_aprobacion.'</td>
                <td class=" text-left">Fecha: '.$fecha_procesado.'</td>
            </tr>
         </table>';


            // $html.='<table style="width:100%">
            //   <tr>
            //     <td class="text-center"><p>_______________________________<br>Aprobado Por<br></p></td>
            //     <td class="text-center"><p>_______________________________<br>Recepción<br>Nombre:</p></td>
            //   </tr>              
            // </table><br><br><br>';


          '</header>';

          $html.='<footer>

          <table class="table">
            <tr>
              <td class="s4 text-left" width="25%">IBNORCA</td>
              <td class="s4 text-left" width="25%">CÓDIGO</td>
              <td class="s4 text-left" width="25%">V:</td>
              <td class="s4 text-left" width="25%">Página 1 de 1</td>
            </tr>
          </table>


          </footer>';



$html.='</body>'.
      '</html>';           
descargarPDFCajaChica("IBNORCA - ".$unidadC." (".$tipoC.", ".$numeroC.")",$html);

?>

<?php 
} catch(PDOException $ex){
    echo "Un error ocurrio".$ex->getMessage();
}
?>
