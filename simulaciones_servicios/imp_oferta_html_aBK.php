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
        $alcanceSimulacionX=$alcanceSimulacionX;
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
    <div class="s-9" <?=$estiloTitulo?>>
        <p class="font-weight-bold bg-danger text-white">1. &nbsp;&nbsp;<?=$tituloIndex?></p>
        <p class="font-weight-bold">1.1. &nbsp;&nbsp;Quienes somos</p>
          <table>
          <tr>
            <td width="30%"><div class="card-imagen"><img src="../assets/img/ibnorca2.jpg" alt="NONE" width="200px" height="150px"></div></td>
            <td class="text-justificar"><p><?=str_replace("\n", "</p><p>",obtenerValorOferta($codOferta,10,$default,1))?></p></td>
          </tr>
        </table>
    </div>
    <div class="s-9" <?=$estiloTitulo?>>
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
            <p><br></p>
            <p class="text-danger">CERTIFICACIÓN</p>

            <img src="<?=$pdf_tipo?>" alt="NONE" width="100%" height="200px">
        </div>
        
    </div>
    <?php 
    $tituloIndex=obtenerValorOferta($codOferta,23,$default,2);
    $estiloTitulo="";
    if(trim($tituloIndex)==""){
      $estiloTitulo="style='display:none;'";
    }
    ?>
    <div class="s-9" <?=$estiloTitulo?>>
        <p class="font-weight-bold bg-danger text-white">2. &nbsp;&nbsp;<?=$tituloIndex?></p>
        <div class="pl-6 pr-6 text-justificar">
            <p class="font-weight-bold"><?=$alcanceSimulacionX?></p>
            <p class="font-weight-bold">En el/los sitio(s)</p>
            <p class="pl-2">
            <?php 

             $stmtAtributos = $dbh->prepare("SELECT * from simulaciones_servicios_atributos where cod_simulacionservicio=$codigo");
             $stmtAtributos->execute();
             $codigoFilaAtrib=0;
             while ($rowAtributo = $stmtAtributos->fetch(PDO::FETCH_ASSOC)) {
               $nombreAtrib=$rowAtributo['nombre'];
               $dirAtrib=$rowAtributo['direccion'];
               $normaXAtrib=$rowAtributo['norma']; 
               ?>
               -   <?=$nombreAtrib?>, Dirección <?=$dirAtrib?><br>
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
    <div class="s-9" <?=$estiloTitulo?>>
        <p class="font-weight-bold bg-danger text-white">3. &nbsp;&nbsp;<?=$tituloIndex?></p>
        <div class="pl-6 pr-6 text-justificar">
            <p>La presente oferta describe los servicios prestados por IBNORCA, desde la recepción de la solicitud del cliente hasta la toma de decisión sobre el proceso de certificación.</p>
            <p>A continuación, se describe cada una de las fases antes mencionadas:</p>
        </div>

        <p class="font-weight-bold bg-plomo text-white s-12"><i>(1) &nbsp;&nbsp;AUDITORÍA DE CERTIFICACIÓN ETAPA I</i></p>
        <div class="pl-6 pr-6 text-justificar">
            <p><?=str_replace("\n", "</p><p>",obtenerValorOferta($codOferta,16,$default,1))?></p>
        </div>
        <p class="font-weight-bold bg-plomo text-white s-12"><i>(2) &nbsp;&nbsp;AUDITORÍA DE CERTIFICACIÓN ETAPA II</i></p>
        <div class="pl-6 pr-6 text-justificar">
            <p><?=str_replace("\n", "</p><p>",obtenerValorOferta($codOferta,17,$default,1))?></p>
        </div>
        <p class="font-weight-bold bg-plomo text-white s-12"><i>(3) &nbsp;&nbsp;DECISIÓN</i></p>
        <div class="pl-6 pr-6 text-justificar">
            <p><?=str_replace("\n", "</p><p>",obtenerValorOferta($codOferta,18,$default,1))?></p>
        </div>
        <p class="font-weight-bold bg-plomo text-white s-12"><i>(4) &nbsp;&nbsp;AUDITORÍAS DE SEGUIMIENTO </i></p>
        <div class="pl-6 pr-6 text-justificar">
            <p><?=str_replace("\n", "</p><p>",obtenerValorOferta($codOferta,19,$default,1))?></p>
        </div>
        <p class="font-weight-bold bg-plomo text-white s-12"><i>(5) &nbsp;&nbsp;AUDITORÍA RENOVACIÓN</i></p>
        <div class="pl-6 pr-6 text-justificar">
            <p><?=str_replace("\n", "</p><p>",obtenerValorOferta($codOferta,20,$default,1))?></p>
            <b><p><?=str_replace("\n", "</p><p>",obtenerValorOferta($codOferta,13,$default,1))?></p></b>
        </div>
        <p class="font-weight-bold bg-plomo text-white s-12"><i>(6) &nbsp;&nbsp;AMPLIACIÓN DEL ALCANCE DE LA CERTIFICACIÓN</i></p>
        <div class="pl-6 pr-6 text-justificar">
            <p><?=str_replace("\n", "</p><p>",obtenerValorOferta($codOferta,21,$default,1))?></p>
        </div>
        <p class="font-weight-bold bg-plomo text-white s-12"><i>(7) &nbsp;&nbsp;AUDITORÍAS MULTI SITIO</i></p>
        <div class="pl-6 pr-6 text-justificar">
            <p><?=str_replace("\n", "</p><p>",obtenerValorOferta($codOferta,22,$default,1))?></p>
        </div>

    </div>
    <?php 
    $tituloIndex=obtenerValorOferta($codOferta,23,$default,4);
    $estiloTitulo="";
    if(trim($tituloIndex)==""){
      $estiloTitulo="style='display:none;'";
    }
    ?>
    <div class="s-9" <?=$estiloTitulo?>>
        <p class="font-weight-bold bg-danger text-white">4. &nbsp;&nbsp;<?=$tituloIndex?></p>
        <div class="pl-6 pr-6 text-justificar">
            <p>Todos los miembros del equipo que participan en la auditoria han sido calificados por IBNORCA de acuerdo a sus procedimientos internos.</p>
            <p>Los procedimientos internos de IBNORCA de calificación de auditores satisfacen los requerimientos de la Norma NB/ISO/IEC 17021 "Evaluación de la conformidad-Requisitos para los organismos que realizan la auditoria y certificación de Sistemas de gestión”.</p>
        </div>
    </div>
    <?php 
    $tituloIndex=obtenerValorOferta($codOferta,23,$default,5);
    $estiloTitulo="";
    if(trim($tituloIndex)==""){
      $estiloTitulo="style='display:none;'";
    }
    ?>

    <div class="s-9" <?=$estiloTitulo?>>
        <p class="font-weight-bold bg-danger text-white">5. &nbsp;&nbsp;<?=$tituloIndex?></p>
        <div class="pl-6 pr-6 text-justificar">
            <p>IBNORCA mantiene la confidencialidad de los datos e información a los que pudiera tener acceso como consecuencia de su actividad de certificación.</p>
            <p>Además, IBNORCA mantiene el compromiso de salvaguardia del nombre de la organización postulante que se encuentran en fase de evaluación hasta que obtienen el correspondiente certificado, momento en el cual se registra y publica su nombre en la lista de empresas certificadas.</p>
        </div>
    </div> 
    <?php 
    $tituloIndex=obtenerValorOferta($codOferta,23,$default,6);
    $estiloTitulo="";
    if(trim($tituloIndex)==""){
      $estiloTitulo="style='display:none;'";
    }
    ?>
    <div class="s-9" <?=$estiloTitulo?>>
        <p class="font-weight-bold bg-danger text-white">6. &nbsp;&nbsp;<?=$tituloIndex?></p>
        <div class="pl-6 pr-6 text-justificar">
            <p>En un plazo no superior a 7 días desde la aceptación de la oferta contrato de certificación, IBNORCA se pondrá en contacto con el representante de la organización postulante a objeto de coordinar las fechas de ejecución de la certificación/renovación.</p>
        </div>
    </div>  
    <?php 
    $tituloIndex=obtenerValorOferta($codOferta,23,$default,7);
    $estiloTitulo="";
    if(trim($tituloIndex)==""){
      $estiloTitulo="style='display:none;'";
    }
    ?>  
    <div class="s-9" <?=$estiloTitulo?>>
        <p class="font-weight-bold bg-danger text-white">7. &nbsp;&nbsp;<?=$tituloIndex?></p>
        <div class="pl-6 pr-6 text-justificar">
            <p><?=str_replace("\n", "</p><p>",obtenerValorOferta($codOferta,14,$default,1))?></p>
        </div>
    </div>
    <?php 
    $tituloIndex=obtenerValorOferta($codOferta,23,$default,8);
    $estiloTitulo="";
    if(trim($tituloIndex)==""){
      $estiloTitulo="style='display:none;'";
    }
    ?>
    <div class="s-9" <?=$estiloTitulo?>>
        <p class="font-weight-bold bg-danger text-white">8. &nbsp;&nbsp;<?=$tituloIndex?></p>
        <div class="pl-6 pr-6 text-justificar">
            <p>La presente oferta contrato tiene un periodo de validez para su aceptación de treinta (30) días calendario a partir de la fecha de emisión.</p>
            <p>La presente oferta contrato estará vigente desde la fecha de su suscripción hasta concluir las etapas del proceso de certificación y sus correspondientes plazos de ejecución que serán coordinados entre <b>IBNORCA</b> y el <b>CLIENTE</b> de acuerdo a lo establecido en el punto 4.</p>
        </div>
    </div>  
    <?php 
    $tituloIndex=obtenerValorOferta($codOferta,23,$default,9);
    $estiloTitulo="";
    if(trim($tituloIndex)==""){
      $estiloTitulo="style='display:none;'";
    }
    ?>
    <div class="s-9" <?=$estiloTitulo?>>
        <p class="font-weight-bold bg-danger text-white">9. &nbsp;&nbsp;<?=$tituloIndex?></p>
        <div class="pl-6 pr-6 text-justificar">
            <p>Para el caso en que la organización determine modificar la fecha de realización de auditoria ya prevista, deberá comunicar esta determinación con una antelación de <b>10 días</b> calendario, antes de la fecha prevista para la auditoria, si no se comunicase en el tiempo determinado la organización deberá abonar el lucro cesante y todos los costos de programación de esta actividad.</p>
        </div>
    </div>    
    <?php 
    $tituloIndex=obtenerValorOferta($codOferta,23,$default,10);
    $estiloTitulo="";
    if(trim($tituloIndex)==""){
      $estiloTitulo="style='display:none;'";
    }
    ?>
    <div class="s-9" <?=$estiloTitulo?>>
        <p class="font-weight-bold bg-danger text-white">10. &nbsp;&nbsp;<?=$tituloIndex?></p>
        <div class="pl-6 pr-6 text-justificar">
            <p>•   La organización postulante deberá cumplir las disposiciones del Reglamento de Certificación de producto RMT-TCP-01 y la Guía de Uso de Marca ESP-TCP-0X, documentos que se encuentran disponibles en su versión vigente en la página web <a href="www.ibnorca.org" target="_blank" class="text-azul">www.ibnorca.org</a>, y que serán proporcionados por el personal de certificación. En ese sentido, en caso de operar alguna sanción que implique suspensión o revocatoria de la Certificación, el <b>CLIENTE</b> no podrá usar las marcas registradas de <b>IBNORCA</b> a partir del momento en el que opere la suspensión o revocatoria de la certificación.</p>
            <p>•   Excepcionalmente, la organización debe permitir a requerimiento de IBNORCA, la participación de representantes de organismos de acreditación, en calidad de observadores, durante la auditoría.</p>
            <p>•   Durante los procesos de auditoría, no se permite la intervención del consultor del Sistema de Gestión de la organización. De ser requerida su participación, su rol será únicamente de observador.</p>
            <p>•   <b>IBNORCA</b> podrá realizar auditorías sea de oficio o por que medie alguna denuncia por parte de terceros. El costo de dichas auditorías será pagado por el CLIENTE de acuerdo a los aranceles vigentes. En caso que la organización no acepte la realización de la auditoría antes referida, IBNORCA procederá a la suspensión de la certificación por el tiempo que establezca.</p>
            <p>•   El IBNORCA podrá sugerir la modalidad de auditorías remotas.</p>
        </div>
    </div>
    <?php 
    $tituloIndex=obtenerValorOferta($codOferta,23,$default,11);
    $estiloTitulo="";
    if(trim($tituloIndex)==""){
      $estiloTitulo="style='display:none;'";
    }
    ?>
    <div class="s-9" <?=$estiloTitulo?>>
        <p class="font-weight-bold bg-danger text-white">11. &nbsp;&nbsp;<?=$tituloIndex?></p>
        <div class="pl-6 pr-6 text-justificar">
            <p>En la tabla siguiente se muestra el presupuesto para los tres años que dura el ciclo de certificación. Dicho presupuesto ha sido elaborado teniendo en cuenta el tamaño de la organización postulante, las recomendaciones que a tal efecto tiene establecidas el IBNORCA por su propia experiencia y las tarifas vigentes del proceso de certificación.</p>

            
            <?php 
         for ($i=1; $i <=$anioX ; $i++) { 
             $ordinal=ordinalSuffix($i);
             $tituloTabla="el seguimiento ".($i-1);
             $sqlAnio="and s.cod_anio=".$i;
             if($i==1){
              $tituloTabla="la certificación";
              $sqlAnio="and s.cod_anio in(".$i.",0)";
             }
             ?>
             <p>Para <?=$tituloTabla?>, los montos a cancelar son:</p>
        <table class="table table-bordered">
                <tr class="s-10 text-white bg-danger text-center font-weight-bold">
                    <td width="27%">CONCEPTO</td>
                    <td width="27%">DÍAS  AUDITOR</td>
                    <td width="10%">COSTO BOB</td>
                </tr>
                <?php 
                $queryPr="SELECT s.*,t.Descripcion as nombre_serv FROM simulaciones_servicios_tiposervicio s, cla_servicios t where s.cod_simulacionservicio=$codigo and s.cod_claservicio=t.IdClaServicio and s.habilitado=1 $sqlAnio order by t.nro_orden";
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
                    <td class="text-right"><?=$cantidadEPre?></td>
                    <td class="text-right"><?=$montoPreTotal?></td>
                </tr>
                 <?php
                }
                ?>
                
                <tr class="font-weight-bold">
                   <td colspan="2">Total, (<?=$ordinal?>) año</td>
                   <td class="text-right"><?=number_format($modal_totalmontopretotal,2, ',', '')?></td>  
                </tr>
        </table>
             <?php
             $gestionInicio++;
         }
        ?>  

            <p><b>Nota 1: Considerar que esta propuesta puede ser modificada si luego de haberse realizado la auditoria de etapa I se detecta que los datos brindados en el cuestionario no son exactos y existieron cambios en el alcance de la certificación que cubre esta propuesta. En el caso de una auditoria combinada se deberá confirmar el nivel de integración de sistemas de gestión integrado. 
            </p><p>Nota 2: Todos los precios de las auditorias INCLUYEN los impuestos de ley correspondientes y se facturan.
               La presente oferta NO INCLUYE: Todos los gastos directos e indirectos (como ser, pasajes, traslado, hospedaje, alimentación, transporte al punto de auditoría viáticos de auditores), serán de responsabilidad y consecuentemente asumidos por el CLIENTE, en forma separada o incluida en el costo total del servicio
             </b></p>
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
