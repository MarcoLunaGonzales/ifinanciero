<?php
session_start();
$dbh = new Conexion();
$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();
if(isset($_GET['cod'])){
  $codigo=$_GET['cod'];
}else{
  $codigo=0;
}
$usd=6.96;

$nombreClienteX=obtenerNombreClienteSimulacion($codigo);

$stmt1 = $dbh->prepare("SELECT sc.*,es.nombre as estado from simulaciones_servicios sc join estados_simulaciones es on sc.cod_estadosimulacion=es.codigo where sc.cod_estadoreferencial=1 and sc.codigo='$codigo'");
      $stmt1->execute();
      $stmt1->bindColumn('codigo', $codigoX);
            $stmt1->bindColumn('nombre', $nombreX);
            $stmt1->bindColumn('fecha', $fechaX);
            $stmt1->bindColumn('cod_responsable', $codResponsableX);
            $stmt1->bindColumn('estado', $estadoX);
            $stmt1->bindColumn('cod_plantillaservicio', $codigoPlan);
            $stmt1->bindColumn('dias_auditoria', $diasSimulacion);
            $stmt1->bindColumn('utilidad_minima', $utilidadIbnorcaX);
            $stmt1->bindColumn('productos', $productosX);
            $stmt1->bindColumn('sitios', $sitiosX);
            $stmt1->bindColumn('anios', $anioX);
            $stmt1->bindColumn('porcentaje_fijo', $porcentajeFijoX);
            $stmt1->bindColumn('afnor', $afnorX);
            $stmt1->bindColumn('porcentaje_afnor', $porcentajeAfnorX);
            $stmt1->bindColumn('id_tiposervicio', $idTipoServicioX);
            $stmt1->bindColumn('alcance_propuesta', $alcanceSimulacionX);
            $stmt1->bindColumn('descripcion_servicio', $descripcionServSimulacionX);

      while ($row1 = $stmt1->fetch(PDO::FETCH_BOUND)) {
        $descripcionServSimulacionX=$descripcionServSimulacionX;
        $anioX=$anioX;
        $anioLetra=strtolower(CifrasEnLetras::convertirNumeroEnLetras($anioX));

        $gestionInicio=(int)strftime('%Y',strtotime($fechaX));
      }
/*                        archivo HTML                      */

?>
<!-- formato cabeza fija para pdf-->
<html><head>
    <link href="../assets/libraries/plantillaPDFOfertaPropuesta.css" rel="stylesheet" />
   </head><body>
   <header class="header">            
            <div id="header_titulo_texto"><center><label class="text-muted font-weight-bold">
              <small><small><i><u><?=obtenerValorOferta($codOferta,1,$default,1)?></u></i></small></small>
              <b><br>REGISTRO<br><?=obtenerValorOferta($codOferta,2,$default,1)?></b>
            </label></center>
          </div>
          <img class="imagen-logo-der" src="../assets/img/ibnorca2.jpg">
    </header>
    <footer class="footer">
        <table class="table" style="height:30px;width:100%;">
          <tr class="text-muted s-8 font-weight-bold">
            <td width="25%"></td>
            <td class="s-10 text-center" width="15%" style="border-right:1px solid #9c9c9c;padding:2px;">IBNORCA ©</td>
            <td class="text-center" width="30%" style="border-right:1px solid #9c9c9c;padding:2px;">Código: REG-PRO-TCS-03-05_02</td>
            <td class="text-center" width="15%" style="border-right:1px solid #9c9c9c;padding:2px;">V: 2019-11-06</td>
            <td class="text-center" width="15%" style="padding:2px;"></td>
          </tr>
       </table>
     </footer>

  <div class="pagina">
    <div class="container" style="width:100% !important;">
       <div class="float-left pl-6 pt-2">
         <div class="s-9 text-left"><label class="">Nuestra Fecha</label><br> <?=obtenerValorOferta($codOferta,4,$default,1)?></div>
       </div> 
       <div class="float-left pl-20 pt-2">
         <div class="s-9 text-left"><label class="">Nuestra Referencia</label><br> <?=obtenerValorOferta($codOferta,5,$default,1)?></div>  
       </div> 
    </div>

    <div class="pt-8 s-10 pl-6">
        <div class="">Señores: </div>
        <div class=""><?=$nombreClienteX?></div>
        <div class="">Ciudad | Bolivia.- </div>
        
    </div>
    <!--<div class="pt-2 s-10 pl-6 font-weight-bold">
        <div class="">Atn.: &nbsp;Nombre</div>
        <div class="pl-6">Cargo</div>
        
    </div>-->
    <div class="pt-2">
        <div class="s-11 font-weight-bold text-justificar text-right">Ref: <u><?=strtoupper($descripcionServSimulacionX)?></u></div>
    </div>
    <div class="pt-2 pl-6 pr-6 text-justificar s-9">
        <p class="pb-2 s-9">De nuestra consideración:</p>
        <p><?=obtenerValorOferta($codOferta,8,$default,1)?> <?=$descripcionServSimulacionX?>.</p>
        <p><?=obtenerValorOferta($codOferta,8,$default,2)?></p>
        <p><?=obtenerValorOferta($codOferta,8,$default,3)?></p>
        <p class="pt-2"><?=obtenerValorOferta($codOferta,8,$default,4)?></p>
        <p class="pt-2 text-right">Saluda a usted muy atentamente,</p>

        <p class="pt-8 text-right"><?=ucfirst(namePersonalCompleto(obtenerValorConfiguracion(68)));?><br>
           DIRECTOR NACIONAL DE EVALAUCIÓN<br> 
           DE LA CONFORMIDAD
        </p>
    </div>
    <div class="saltopagina"></div>
    <?php 
    $tituloIndex=obtenerValorOferta($codOferta,23,$default,1);
    $estiloTitulo="";
    if(trim($tituloIndex)==""){
      $estiloTitulo="style='display:none;'";
    }
    ?>
    <div class="s-9 "<?=$estiloTitulo?>>
        <p class="font-weight-bold bg-danger text-white">1. &nbsp;&nbsp;<?=$tituloIndex?></p>
        <p class="font-weight-bold">1.1. &nbsp;&nbsp;Quienes somos</p>
          <table>
          <tr>
            <td width="30%"><div class="card-imagen"><img src="../assets/img/ibnorca2.jpg" alt="NONE" width="200px" height="150px"></div></td>
            <td class="text-justificar"><p><?=str_replace("\n", "</p><p>",obtenerValorOferta($codOferta,10,$default,1))?></p></td>
          </tr>
        </table>
    </div>

    <div class="s-9 "<?=$estiloTitulo?>>
        <p class="font-weight-bold">1.2. &nbsp;&nbsp;Proceso de Certificación</p>
        <table>
          <tr>
            <td width="38%"><div class="card-imagen"><img src="../assets/libraries/img/logos_oferta/certificacion.jpg" alt="NONE" width="200px" height="100px"></div></td>
            <td class="text-justificar"><p><?=str_replace("\n", "</p><p>",obtenerValorOferta($codOferta,11,$default,1))?></p></td>
          </tr>
        </table>
        <br>
        <div class="pl-6 pr-6 text-justificar">
            <p class="text-danger">COMERCIAL</p>
            <img src="../assets/libraries/img/logos_oferta/cert.jpg" alt="NONE" width="100%" height="150px">
            <p class="text-danger">CERTIFICACIÓN – Ciclo de certificación (<?=$anioX?> años)</p>
        </div>
        <table class="table pt-4">
                <tr class="s-12 text-white bg-plomo">
                    <td width="35%">CONCEPTO</td>
                    <td width="65%">DESCRIPCIÓN</td>
                </tr>
                <tr>
                    <td class="border-b">
                       <div class="card">
                           <div class="card-imagen"><img src="../assets/libraries/img/logos_oferta/1.jpg" alt="NONE"></div>
                           <div class="card-titulo font-weight-bold">AUDITORIA DE CERTIFICACION DE PRODUCTO CON SELLO IBNORCA / EVALUACION DE CONFORMIDAD DE PRODUCTO SEGÚN REGLAMENTO TÉCNICO </div>
                       </div>   
                    </td>
                    <td class="border-b">
                        <p>Proceso mediante el cual se evalúa la conformidad del producto y el proceso de producción en base a los criterios establecidos en la norma técnica, Especificación Técnica Disponible o Reglamento Técnico.  Las actividades a realizar son:</p>
                        <div class="pl-4">
                            <div>-  Evaluación de materias primas y materiales</div>
                            <div>-  Evaluación del registro histórico</div>
                            <div>-  Toma de muestras de planta y mercado</div>
                            <div>-  Realización de ensayos en laboratorio designado/acreditado</div>
                            <div>-   Evaluación del Sistema de Gestión conforme a la Especificación    ESP-TCP-04A_00.</div>
                        </div>
                        <p>Resultado del proceso:  Informe de auditoría</p>
                    </td>
                </tr>
                <tr>
                    <td class="border-b">
                       <div class="card">
                           <div class="card-imagen"><img src="../assets/libraries/img/logos_oferta/2.jpg" alt="NONE"></div>
                           <div class="card-titulo font-weight-bold">DECISIÓN DE LA CERTIFICACION DE PRODUCTO CON SELLO IBNORCA / OTORGAMIENTO DEL DOCUMENTO DE CONFORMIDAD DE PRODUCTO SEGÚN REGLAMENTO TÉCNICO </div>
                       </div>   
                    </td>
                    <td class="border-b">
                        <p>El informe de auditoría, es presentado al Consejo Rector de Certificación para su evaluación y recomendación a la Dirección Ejecutiva de IBNORCA, instancia que aprueba y otorga:</p>
                        <div class="pl-4">
                            <div>-  La certificación de producto con SELLO IBNORCA. Resultado: Certificado, Resolución administrativa y Contrato de Autorización de Uso del Sello.</div>
                            <div>-  La conformidad del producto según Reglamento Técnico. Resultado: Documento de conformidad de producto. </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="border-b">
                       <div class="card">
                           <div class="card-imagen"><img src="../assets/libraries/img/logos_oferta/3.jpg" alt="NONE"></div>
                           <div class="card-titulo font-weight-bold">MANTENIMIENTO DE LA CERTIFICACION DE PRODUCTO CON SELLO IBNORCA / EVALUACION DE CONFORMIDAD SEGÚN REGLAMENTO TÉCNICO </div>
                       </div>   
                    </td>
                    <td class="border-b">
                        <p>El certificado de producto con Sello IBNORCA / documento de conformidad de producto es válida por tres años, durante este periodo se realizará auditorias de seguimiento como mínimo una vez al año para asegurar el mantenimiento de la conformidad del producto y del sistema de gestión. Estas auditorías siguen las mismas actividades que una auditoria de <b class="text-danger">certificación/renovación<b>. </p>
                        <p>Resultado del proceso: Informe de auditoría, y resolución del mantenimiento de la certificación de producto con sello IBNORCA y en caso de la evaluación según Reglamento técnico se emitirá un Documento de mantenimiento de conformidad de Producto</p>
                    </td>
                </tr>
                <tr>
                    <td class="border-b">
                       <div class="card">
                           <div class="card-imagen"><img src="../assets/libraries/img/logos_oferta/4.jpg" alt="NONE"></div>
                           <div class="card-titulo font-weight-bold">RENOVACIÓN DE LA CERTIFICACIÓN DE PRODUCTO CON SELLO IBNORCA /DOCUMENTO DE CONFORMIDAD DEL PRODUCTO.</div>
                       </div>   
                    </td>
                    <td class="border-b">
                        <p>Tres meses antes del vencimiento del certificado de producto con Sello IBNORCA / documento de conformidad de producto, IBNORCA se pone en contacto con la organización para acordar la renovación del mismo.  La auditoría de renovación tiene características similares a la auditoria de certificación.</p>
                    </td>
                </tr>
        </table>
    </div>
    <?php 
    $tituloIndex=obtenerValorOferta($codOferta,23,$default,2);
    $estiloTitulo="";
    if(trim($tituloIndex)==""){
      $estiloTitulo="style='display:none;'";
    }
    ?>
    <div class="s-9 "<?=$estiloTitulo?>>
        <p class="font-weight-bold bg-danger text-white">2. &nbsp;&nbsp;<?=$tituloIndex?></p>
        <div class="pl-6 pr-6 text-justificar">
            <p class="font-weight-bold">La certificación de producto con SELLO IBNORCA, aplica para los productos: </p>
            <p class="pl-2">
            <?php 

             $stmtAtributos = $dbh->prepare("SELECT * from simulaciones_servicios_atributos where cod_simulacionservicio=$codigo");
             $stmtAtributos->execute();
             $codigoFilaAtrib=0;
             while ($rowAtributo = $stmtAtributos->fetch(PDO::FETCH_ASSOC)) {
               $nombreAtrib=$rowAtributo['nombre'];
               $marcaAtrib=$rowAtributo['marca'];
               $normaXAtrib=$rowAtributo['norma']; 
               ?>
               -   <?=$nombreAtrib?>, Marca <?=$marcaAtrib?>, bajo Norma <?=$normaXAtrib?><br>
               <?php
             }
            ?>
            </p>
        </div>
    </div>
    <?php 
    $tituloIndex=obtenerValorOferta($codOferta,23,$default,3);
    $estiloTitulo="";
    if(trim($tituloIndex)==""){
      $estiloTitulo="style='display:none;'";
    }
    ?>
    <div class="s-9 "<?=$estiloTitulo?>>
        <p class="font-weight-bold bg-danger text-white">3. &nbsp;&nbsp;<?=$tituloIndex?></p>
        <div class="pl-6 pr-6 text-justificar">
            <p><?=str_replace("\n", "</p><p>",obtenerValorOferta($codOferta,12,$default,1))?></p>
        </div>
    </div>
    <?php 
    $tituloIndex=obtenerValorOferta($codOferta,23,$default,4);
    $estiloTitulo="";
    if(trim($tituloIndex)==""){
      $estiloTitulo="style='display:none;'";
    }
    ?>
    <div class="s-9 "<?=$estiloTitulo?>>
        <p class="font-weight-bold bg-danger text-white">4. &nbsp;&nbsp;<?=$tituloIndex?></p>
        <div class="pl-6 pr-6 text-justificar">
            <p>En la tabla siguiente se muestra el presupuesto para <b>los <?=$anioLetra?> años que dura el ciclo de certificación.</b> Dicho presupuesto ha sido elaborado teniendo en cuenta el tamaño de la organización postulante, las recomendaciones que a tal efecto tiene establecidas el IBNORCA por su propia experiencia y las tarifas vigentes del proceso de certificación.</p>
            <p>Para la certificación, los montos a cancelar son:</p>
        </div>
        <?php 
         for ($i=1; $i <=$anioX ; $i++) { 
             $ordinal=ordinalSuffix($i);
             ?>
        <table class="table pt-2 table-bordered">
                <tr class="s-10 text-white bg-plomo text-center font-weight-bold">
                    <td width="27%">SERVICIO</td>
                    <td width="27%">COSTO</td>
                    <td width="10%">DÍAS</td>
                    <td width="16%">AUDITORES</td>
                    <td width="20%">TOTAL COSTO USD</td>
                </tr>
                <tr class="bg-plomo-claro">
                   <td colspan="5"><?=$ordinal?> año (GESTIÓN <?=$gestionInicio?>)</td>  
                </tr>
                <?php 
                $queryPr="SELECT s.*,t.Descripcion as nombre_serv FROM simulaciones_servicios_tiposervicio s, cla_servicios t where s.cod_simulacionservicio=$codigo and s.cod_claservicio=t.IdClaServicio and s.habilitado=1 and s.cod_anio=$i order by t.nro_orden";
                $stmt = $dbh->prepare($queryPr);
                $stmt->execute();
                $modal_totalmontopre=0;$modal_totalmontopretotal=0;
                while ($rowPre = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  $codigoPre=$rowPre['codigo'];
                  $codCS=$rowPre['cod_claservicio'];
                  $tipoPre=strtoupper($rowPre['nombre_serv']);
                  $tipoPreEdit=strtoupper($rowPre['observaciones']);
                  $cantidadPre=$rowPre['cantidad'];
                  $cantidadEPre=$rowPre['cantidad_editado'];
                  $montoPre=$rowPre['monto'];
                  $montoPreTotal=$montoPre*$cantidadEPre;
                  $codTipoUnidad=$rowPre['cod_tipounidad'];
                  $codAnioPre=$rowPre['cod_anio'];
                  $modal_totalmontopre+=$montoPre;
                  $modal_totalmontopretotal+=$montoPreTotal;
                  $montoPreUSD=number_format($montoPre/$usd,2,".","");
                  $montoPreTotalUSD=number_format($montoPreTotal/$usd,2,".","");
                  $montoPre=number_format($montoPre,2,".","");
                  $montoPreTotal=number_format($montoPreTotal,2,".","");
                 ?>
                 <tr>
                    <td><?=$tipoPreEdit?></td>
                    <td><p><?=$montoPreUSD?> USD</p></td>
                    <td class="text-right"><?=$cantidadEPre?></td>
                    <td></td>
                    <td class="text-right"><?=$montoPreTotalUSD?></td>
                </tr>
                 <?php
                }
                ?>
                
                <tr class="font-weight-bold">
                   <td colspan="4">Total, Gestión <?=$gestionInicio?></td>
                   <td class="text-right"><?=number_format($modal_totalmontopretotal/$usd,2, ',', '')?></td>  
                </tr>
        </table>
             <?php
             $gestionInicio++;
         }
        ?>
        
        <div class="pl-6 pr-6 pt-1 text-justificar">
            <p><?=str_replace("\n", "</p><p>",obtenerValorOferta($codOferta,13,$default,1))?></p>
            </p>

            <p><i>AMPLIACIÓN DEL ALCANCE DE LA CERTIFICACIÓN</i></p>
            <p>Luego de obtener la certificación, la organización puede solicitar la ampliación del alcance de la certificación de productos. Estas ampliaciones deberán realizarse conforme a lo establecido en el Reglamento de Certificación, para ello la organización deberá actualizar los datos remitidos en su solicitud, y el IBNORCA presentará una nueva oferta para el proceso. </p>
            <p>ACLARACIONES </p>
            <p>La presenta oferta contempla los servicios de Certificación de producto con SELLO IBNORCA y la evaluación de la conformidad del producto según el Reglamento Técnico. </p>
            <p>Para la auditoria de certificación y/o evaluación de la conformidad según el Reglamento Técnico RM 261.2018, se requerirá que la planta de producción se encuentre funcionando, a fin de que el equipo auditor pueda evaluar la fabricación del producto solicitado en el alcance, además, la empresa deberá contar con el producto disponible para la toma de muestras.</p>
            <p>El costo del Derecho de uso del Sello IBNORCA incluye también la emisión del DOCUMENTO DE CONFORMIDAD DE PRODUCTO según Reglamento Técnico.</p>
            <p>Asimismo, el costo del Derecho de uso de Sello IBNORCA se mantendrá el segundo y tercer año siempre y cuando se mantengan las condiciones declaradas en el contrato de uso de sello y tarifario vigente. </p>
        </div>  
    </div>
    <?php 
    $tituloIndex=obtenerValorOferta($codOferta,23,$default,5);
    $estiloTitulo="";
    if(trim($tituloIndex)==""){
      $estiloTitulo="style='display:none;'";
    }
    ?>
    <div class="s-9 "<?=$estiloTitulo?>>
        <p class="font-weight-bold bg-danger text-white">5. &nbsp;&nbsp;<?=$tituloIndex?></p>
        <div class="pl-6 pr-6 text-justificar">
            <p>Adicionalmente para el proceso de certificación de producto con Sello IBNORCA o emisión del DOCUMENTO DE CONFORMIDAD DE PRODUCTO, la empresa debe considerar los costos por muestreo y ensayos realizados durante la auditoria a muestras tomadas en la fábrica y en el mercado.</p>
            <p>Para ello debe considerar lo siguiente:</p>
              <p class="pl-2">1. Si la empresa cuenta con un laboratorio designado por la entidad regulatoria, debe cubrir el costo de muestreo y ensayos con testigo.<p>
              <p class="pl-2">2. Si la empresa cuenta con un laboratorio acreditado     por la entidad regulatoria, entonces debe cubrir el costo de muestreo.<p>
              <p class="pl-2">3. Si la empresa no cuenta con ninguna de las anteriores, entonces debe cubrir con los costos de muestreo y el costo de la ejecución de ensayos en un laboratorio Designado/Acreditado. </p>
        </div>
    </div>
    <?php 
    $tituloIndex=obtenerValorOferta($codOferta,23,$default,6);
    $estiloTitulo="";
    if(trim($tituloIndex)==""){
      $estiloTitulo="style='display:none;'";
    }
    ?>
    <div class="s-9 "<?=$estiloTitulo?>>
        <p class="font-weight-bold bg-danger text-white">6. &nbsp;&nbsp;<?=$tituloIndex?></p>
        <div class="pl-6 pr-6 text-justificar">
            <p>Todos los miembros del equipo que participan en la auditoria han sido calificados por IBNORCA de acuerdo a sus procedimientos internos.</p>
            <p>Los procedimientos internos de IBNORCA de calificación de auditores satisfacen los requerimientos de la Norma NB/ISO/IEC 17021 "Evaluación de la conformidad-Requisitos para los organismos que realizan la auditoria y certificación de Sistemas de gestión”.</p>
        </div>
    </div>
    <?php 
    $tituloIndex=obtenerValorOferta($codOferta,23,$default,7);
    $estiloTitulo="";
    if(trim($tituloIndex)==""){
      $estiloTitulo="style='display:none;'";
    }
    ?>
    <div class="s-9 "<?=$estiloTitulo?>>
        <p class="font-weight-bold bg-danger text-white">7. &nbsp;&nbsp;<?=$tituloIndex?></p>
        <div class="pl-6 pr-6 text-justificar">
            <p>IBNORCA mantiene la confidencialidad de los datos e información a los que pudiera tener acceso como consecuencia de su actividad de certificación.</p>
            <p>Además, IBNORCA mantiene el compromiso de salvaguardia del nombre de la organización postulante que se encuentran en fase de evaluación hasta que obtienen el correspondiente certificado, momento en el cual se registra y publica su nombre en la lista de empresas certificadas.</p>
        </div>
    </div> 
    <?php 
    $tituloIndex=obtenerValorOferta($codOferta,23,$default,8);
    $estiloTitulo="";
    if(trim($tituloIndex)==""){
      $estiloTitulo="style='display:none;'";
    }
    ?>
    <div class="s-9 "<?=$estiloTitulo?>>
        <p class="font-weight-bold bg-danger text-white">8. &nbsp;&nbsp;<?=$tituloIndex?></p>
        <div class="pl-6 pr-6 text-justificar">
            <p>En un plazo no superior a 7 días desde la aceptación de la oferta contrato de certificación, IBNORCA se pondrá en contacto con el representante de la organización postulante a objeto de coordinar las fechas de ejecución de la certificación/renovación.</p>
        </div>
    </div>  
      <?php 
    $tituloIndex=obtenerValorOferta($codOferta,23,$default,9);
    $estiloTitulo="";
    if(trim($tituloIndex)==""){
      $estiloTitulo="style='display:none;'";
    }
    ?>
    <div class="s-9 "<?=$estiloTitulo?>>
        <p class="font-weight-bold bg-danger text-white">9. &nbsp;&nbsp;<?=$tituloIndex?></p>
        <div class="pl-6 pr-6 text-justificar">
            <p><?=str_replace("\n", "</p><p>",obtenerValorOferta($codOferta,14,$default,1))?></p>
        </div>
    </div>
    <?php 
    $tituloIndex=obtenerValorOferta($codOferta,23,$default,10);
    $estiloTitulo="";
    if(trim($tituloIndex)==""){
      $estiloTitulo="style='display:none;'";
    }
    ?>
    <div class="s-9 "<?=$estiloTitulo?>>
        <p class="font-weight-bold bg-danger text-white">10. &nbsp;&nbsp;<?=$tituloIndex?></p>
        <div class="pl-6 pr-6 text-justificar">
            <p>La presente oferta contrato tiene un periodo de validez para su aceptación de treinta (30) días calendario a partir de la fecha de emisión.</p>
            <p>La presente oferta contrato estará vigente desde la fecha de su suscripción hasta concluir las etapas del proceso de certificación y sus correspondientes plazos de ejecución que serán coordinados entre <b>IBNORCA</b> y el <b>CLIENTE</b> de acuerdo a lo establecido en el punto 4.</p>
        </div>
    </div>  
    <?php 
    $tituloIndex=obtenerValorOferta($codOferta,23,$default,11);
    $estiloTitulo="";
    if(trim($tituloIndex)==""){
      $estiloTitulo="style='display:none;'";
    }
    ?>
    <div class="s-9 "<?=$estiloTitulo?>>
        <p class="font-weight-bold bg-danger text-white">11. &nbsp;&nbsp;<?=$tituloIndex?></p>
        <div class="pl-6 pr-6 text-justificar">
            <p>Para el caso en que la organización determine modificar la fecha de realización de auditoria ya prevista, deberá comunicar esta determinación con una antelación de <b>10 días</b> calendario, antes de la fecha prevista para la auditoria, si no se comunicase en el tiempo determinado la organización deberá abonar el lucro cesante y todos los costos de programación de esta actividad.</p>
        </div>
    </div>    
    <?php 
    $tituloIndex=obtenerValorOferta($codOferta,23,$default,12);
    $estiloTitulo="";
    if(trim($tituloIndex)==""){
      $estiloTitulo="style='display:none;'";
    }
    ?>
    <div class="s-9 "<?=$estiloTitulo?>>
        <p class="font-weight-bold bg-danger text-white">12. &nbsp;&nbsp;<?=$tituloIndex?></p>
        <div class="pl-6 pr-6 text-justificar">
            <p>•   La organización postulante deberá cumplir las disposiciones del Reglamento de Certificación de producto RMT-TCP-01 y la Guía de Uso de Marca ESP-TCP-0X, documentos que se encuentran disponibles en su versión vigente en la página web <a href="www.ibnorca.org" target="_blank" class="text-azul">www.ibnorca.org</a>, y que serán proporcionados por el personal de certificación. En ese sentido, en caso de operar alguna sanción que implique suspensión o revocatoria de la Certificación, el <b>CLIENTE</b> no podrá usar las marcas registradas de <b>IBNORCA</b> a partir del momento en el que opere la suspensión o revocatoria de la certificación.</p>
            <p>•   Excepcionalmente, la organización debe permitir a requerimiento de IBNORCA, la participación de representantes de organismos de acreditación, en calidad de observadores, durante la auditoría.</p>
            <p>•   Durante los procesos de auditoría, no se permite la intervención del consultor del Sistema de Gestión de la organización. De ser requerida su participación, su rol será únicamente de observador.</p>
            <p>•   <b>IBNORCA</b> podrá realizar auditorías sea de oficio o por que medie alguna denuncia por parte de terceros. El costo de dichas auditorías será pagado por el CLIENTE de acuerdo a los aranceles vigentes. En caso que la organización no acepte la realización de la auditoría antes referida, IBNORCA procederá a la suspensión de la certificación por el tiempo que establezca.</p>
            <p>•   El IBNORCA podrá sugerir la modalidad de auditorías remotas.</p>
        </div>
    </div>    
    <div class="saltopagina"></div>
    <div class="s-9">
        <div class="titulo_texto_inf text-danger"><u>ANEXO 1</u></div>
        <div class="text-justificar">
            <p class="s-10 bg-danger text-white"><u>RESOLUCIÓN DE LA OFERTA CONTRATO</u></p>
            <p>En caso que cualquiera de las Partes incumpla sus obligaciones sustanciales asumidas en la presente oferta contrato y con lo establecido en el Reglamento de Certificación de Sistemas de Gestión de <b>IBNORCA</b>, la parte afectada con el incumplimiento comunicará dicho aspecto a la otra parte otorgándole un plazo razonable para su debido cumplimiento. Si vencido el plazo otorgado no se cumple la obligación, el presente contrato quedará resuelto de pleno derecho y sin necesidad de comunicación previa ni actuación judicial o extrajudicial alguna.</p>
            <p class="s-10 bg-danger text-white"><u>IMPOSIBILIDAD SOBREVENIDA</u></p>
            <p>Ninguna de las Partes será considerada responsable, cuando dicho incumplimiento sea ocasionado por imposibilidad sobreviniente no imputable a la Parte que incumpliere sus obligaciones. Se entiende como imposibilidad sobreviniente a los eventos de caso fortuito y fuerza mayor, sean éstos de cualquier naturaleza, como ser: catástrofes, descargas atmosféricas, incendios, inundaciones, epidemias, y a hechos provocados por los hombres, tales como y de manera enunciativa, actos de terrorismo o de vandalismo, huelgas, bloqueos de caminos, guerra, sabotajes, actos del Gobierno como entidad soberana o persona privada que alteren substancialmente los derechos y/o obligaciones de las Partes, siempre que tales eventos no sean previsibles, o de serlo, sean imposibles de evitar y por tanto, no sean imputables a la Parte afectada e impidan el cumplimiento de sus obligaciones contraídas en virtud al presente Oferta contrato, de manera general, cualquier causal fuera del control de la Parte que incumpla y no atribuible a ella. La Parte afectada deberá comunicar a la otra, en forma escrita, dentro de los dos (2) días hábiles de conocido el evento proporcionando toda la información disponible que permita corroborar la imposibilidad sobreviniente. Si la imposibilidad sobreviniente persiste por más de treinta (30) días, las Partes tendrán la posibilidad de decidir si continúan con el presente Oferta contrato o lo resuelven sin penalidad alguna.</p>
            <p class="s-10 bg-danger text-white"><u>SOLUCION DE CONTROVERSIAS CERTIFICACIÓN IBNORCA</u></p>
            <p>Las Partes expresan que los términos de la presente Oferta contrato y las obligaciones que de él emergen, se encuentran bajo la jurisdicción de las leyes y autoridades bolivianas. Todo litigio, discrepancia, cuestión y reclamación resultante de la ejecución o interpretación de la presente Oferta contrato o relacionado con él, directa o indirectamente, se someterá previamente a la negociación directa entre Partes. Si agotada la negociación entre Partes o expirado el plazo máximo de 10 (Diez) días calendario, la controversia no fuese resuelta amigablemente, la misma se resolverá definitivamente mediante arbitraje en el marco de la Ley No. 708 de 25 de junio de 2015 Ley de Conciliación y Arbitraje o de la ley que regule dicho medio alternativo de solución de controversias. El arbitraje se sujetará a las autoridades, reglas y al procedimiento contenido en el Reglamento de Arbitraje del Centro de Conciliación y Arbitraje de la Cámara Nacional de Comercio de la ciudad de La Paz. Igualmente, las Partes hacen constar expresamente su compromiso de cumplir el Laudo Arbitral que se dicte, renunciando en la medida permitida por Ley, a cualquier tipo de recurso contra el mismo. Los costos emergentes del proceso de arbitraje serán asumidos en su totalidad por la parte que resulte perdedora. En caso de que se pudiera llegar a una conciliación antes de emitirse el Laudo Arbitral, los costos en los que se hubieran incurrido serán cubiertos por ambas partes en iguales porcentajes (50%). Las Partes excluyen de la presente cláusula la verificación por parte de la autoridad competente, la comisión de infracciones en las que incurra LA EMPRESA a los derechos de propiedad intelectual de IBNORCA. No obstante, de ello, una vez verificada la infracción, los daños y perjuicios que genere dicha infracción serán calculados en negociación o en arbitraje conforme lo establece la presenta clausula.</p>
            <p class="s-10 bg-danger text-white"><u>ACEPTACIÓN DE LA OFERTA Y REGLAMENTO DE CERTIFICACIÓN POR PARTE DE LA ORGANIZACIÓN POSTULANTE</u></p>
            <p><?=str_replace("\n", "</p><p>",obtenerValorOferta($codOferta,15,$default,1))?></p>
            
        </div>
    </div>
    
    <div class="s-9">
        <table class="table-grande pt-1">
                <tr class="s-11 font-weight-bold">
                    <td colspan="2" width="50%">FIRMA</td>
                    <td colspan="2" width="50%">FIRMA</td>
                </tr>   
                <tr class="s-11">
                    <td class="text-left">CLIENTE</td>
                    <td class="text-right text-info">________________________</td>
                    <td class="text-left">IBNORCA</td>
                    <td class="text-right text-info">________________________</td>
                </tr>
                <tr class="s-11 pt-4">
                    <td class="text-left">FECHA: </td>
                    <td class="text-right text-info">________________________</td>
                    <td class="text-left">FECHA: </td>
                    <td class="text-right text-info">________________________</td>
                </tr>   
        </table>
    </div>

    <!--<div class="saltopagina"></div>-->
 </div>  

</body></html>