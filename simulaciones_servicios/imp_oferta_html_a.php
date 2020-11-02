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
        $correoResponsable=obtenerCorreoPersonal($codResponsableX);
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
              <b><br><br><?=obtenerValorOferta($codOferta,2,$default,1)?></b>
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
         <div class="s-9 text-left"><label class="">Nuestra Referencia</label><br> <?=obtenerCodigoServicioPorPropuestaTCPTCS($codigo)?></div><!--<?=obtenerValorOferta($codOferta,5,$default,1)?>-->  
       </div> 
    </div>

    <div class="pt-8 s-10 pl-6">
        <div class="">Señores: </div>
        <div class=""><?=$nombreClienteX?></div>
        <div class=""><?=obtenerValorOferta($codOferta,5,$default,1)?> | BOLIVIA.- </div>
        
    </div>
    <div class="pt-2 s-10 pl-6">
        <div class="">A/A: &nbsp;<?=obtenerValorOferta($codOferta,8,$default,5)?></div>
        <div class="pl-6 font-weight-bold"><?=obtenerValorOferta($codOferta,8,$default,6)?></div>
        
    </div>
    <div class="pt-2">
        <div class="s-11 font-weight-bold text-justificar text-right">Ref: <u><?=strtoupper($descripcionServSimulacionX)?></u></div>
    </div>
    <div class="pt-2 pl-6 pr-6 text-justificar s-9">
        <p class="pb-2 s-9">De nuestra consideración:</p>
        <p><?=obtenerValorOferta($codOferta,8,$default,1)?> <?=$descripcionServSimulacionX?>, conforme los requisitos de la Norma <?=obtenerValorOferta($codOferta,8,$default,11)?></p>
        <!--<p><?=obtenerValorOferta($codOferta,8,$default,11)?></p>-->
        <p><?=obtenerValorOferta($codOferta,8,$default,2)?></p>
        <p><?=obtenerValorOferta($codOferta,8,$default,3)." <b>".$correoResponsable?></b></p>
        <p><?=obtenerValorOferta($codOferta,8,$default,4)?></p>
        <p class="pt-2 text-left">Atentamente,</p>

        <p class="pt-6 text-left"><?=ucfirst(namePersonalCompleto(obtenerValorConfiguracion(68)));?><br>
           <b>DIRECTOR NACIONAL DE EVALAUCIÓN<br> 
           DE LA CONFORMIDAD</b>
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
        <p class="font-weight-bold bg-danger text-white">&nbsp;&nbsp;<?=$tituloIndex?></p>
        <!--<p class="font-weight-bold">1.1. </p>-->
          <table>
          <tr>
            <td width="30%"><div class="card-imagen"><img src="../assets/img/ibnorca2.jpg" alt="NONE" width="200px" height="150px"></div></td>
            <td class="text-justificar"><p><?=str_replace("\n", "</p><p>",obtenerValorOferta($codOferta,10,$default,1))?></p></td>
          </tr>
        </table>
    </div>

    <div class="s-9 "<?=$estiloTitulo?>>
        <center><p class="font-weight-bold s-11" style="color:#AC1904;;"><u>PROPUESTA TÉCNICA</u></p></center>
        <!--<p class="font-weight-bold">1.2. &nbsp;&nbsp;Proceso de Certificación</p>
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
        -->
    </div>
    <?php 
    $tituloIndex=obtenerValorOferta($codOferta,23,$default,2);
    $estiloTitulo="";
    if(trim($tituloIndex)==""){
      $estiloTitulo="style='display:none;'";
    }
    ?>
    <div class="s-9" <?=$estiloTitulo?>>
        <p class="font-weight-bold bg-danger text-white">1. &nbsp;&nbsp;<?=$tituloIndex?></p>
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
    $tituloIndex=obtenerValorOferta($codOferta,23,$default,6);
    $estiloTitulo="";
    if(trim($tituloIndex)==""){
      $estiloTitulo="style='display:none;'";
    }
    ?>
    <div class="s-9 "<?=$estiloTitulo?>>
        <p class="font-weight-bold bg-danger text-white">2. &nbsp;&nbsp;<?=$tituloIndex?></p>
        <div class="pl-6 pr-6 text-justificar">
            <p>Todos los miembros del equipo que participan en la auditoria han sido calificados por IBNORCA de acuerdo a sus procedimientos internos.</p>
            <p>IBNORCA podrá incluir en el equipo auditor, un auditor en formación, a cuyo efecto comunicará a la organización con la oportunidad debida.</p>
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
        <p class="font-weight-bold bg-danger text-white">3. &nbsp;&nbsp;<?=$tituloIndex?></p>
        <div class="pl-6 pr-6 text-justificar">
            <p>IBNORCA mantiene la confidencialidad de los datos e información a los que pudiera tener acceso como consecuencia de su actividad de certificación. Así mismo, mantiene el compromiso de salvaguardar el nombre de la organización postulante que se encuentra en fase de evaluación hasta que obtenga el correspondiente certificado, momento en el cual se registra y publica su nombre en la lista de empresas certificadas.</p>
            <p>IBNORCA mantendrá en todo momento absoluta imparcialidad en la prestación del servicio, cumpliendo los lineamientos establecidos en los Reglamentos específicos y Código de Ética.</p>
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
        <p class="font-weight-bold bg-danger text-white">4. &nbsp;&nbsp;<?=$tituloIndex?></p>
        <div class="pl-6 pr-6 text-justificar">
            <p>La presente oferta tiene un periodo de validez para su aceptación de <b><?=obtenerValorOferta($codOferta,8,$default,10)?></b> días calendario a partir de la fecha de emisión.</p>
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
        <p class="font-weight-bold bg-danger text-white">5. &nbsp;&nbsp;<?=$tituloIndex?></p>
        <div class="pl-6 pr-6 text-justificar">
            <p>En la tabla siguiente se muestra la propuesta económica para <b>los <?=$anioLetra?> años del ciclo de certificación.</p>
        </div>
        <?php 
         for ($i=1; $i <=$anioX ; $i++) { 
             $ordinal=ordinalSuffix($i);
             $tituloRomano="";
             for ($ff=0; $ff < ($i-1); $ff++) { 
                $tituloRomano.="I";
             }
             $tituloTabla="seguimiento ".$tituloRomano;
             $sqlAnio="and s.cod_anio=".$i;
             if($i==1){
              $tituloTabla="certificación/renovación";
              $sqlAnio="and s.cod_anio in(".$i.",0)";
             }
             ?>
             <p>Para la auditoria de <b><?=$tituloTabla?></b>:</p>
        <table class="table table-bordered">
                <tr class="s-10 text-white bg-danger text-center font-weight-bold">
                    <td width="27%">CONCEPTO</td>
                    <td width="27%">DÍAS <br> AUDITOR</td>
                    <td width="10%">COSTO USD</td>
                </tr>
                <?php 
                $queryPr="SELECT s.*,t.Descripcion as nombre_serv FROM simulaciones_servicios_tiposervicio s, cla_servicios t where s.cod_simulacionservicio=$codigo and s.cod_claservicio=t.IdClaServicio and s.habilitado=1 $sqlAnio order by t.nro_orden";
                $stmt = $dbh->prepare($queryPr);
                $stmt->execute();
                $modal_totalmontopre=0;$modal_totalmontopretotal=0;$modal_totalmontopretotalUSD=0;
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
                  $modal_totalmontopretotalUSD+=$montoPreTotal/$usd;
                  $montoPreUSD=number_format($montoPre/$usd,2,".","");
                  $montoPreTotalUSD=number_format($montoPreTotal/$usd,2,".","");
                  $montoPre=number_format($montoPre,2,".","");
                  $montoPreTotal=number_format($montoPreTotal,2,".","");
                 ?>
                 <tr>
                    <td><?=$tipoPreEdit?></td>
                    <td class="text-right"><?=$cantidadEPre?></td>
                    <td class="text-right"><?=$montoPreTotalUSD?></td>
                </tr>
                 <?php
                }
                ?>
                
                <tr class="font-weight-bold">
                   <td colspan="2">Total, (<?=$ordinal?>) año</td>
                   <td class="text-right"><?=number_format($modal_totalmontopretotalUSD,2, ',', '')?></td>  
                </tr>
        </table>
             <?php
             $gestionInicio++;
         }
        ?>  
 
        <div class="pl-6 pr-6 pt-2 text-justificar">
            <p><?=str_replace("\n", "</p><p>",obtenerValorOferta($codOferta,13,$default,1))?></p>
            </p>
        </div>  
       

    </div>    
    <div class="saltopagina"></div>
    <div class="s-9">
        <div class="titulo_texto_inf text-danger s-11" style="color:#AC1904;"><u>ANEXO I</u></div>
        <?php 
    $tituloIndex=obtenerValorOferta($codOferta,23,$default,1);
    $estiloTitulo="";
    if(trim($tituloIndex)==""){
      $estiloTitulo="style='display:none;'";
    }
    ?>
        <div class="s-9 "<?=$estiloTitulo?>>
            <p class="s-10 bg-danger text-white">PROCESO DE CERTIFICACIÓN</p>
           <div class="text-justificar">
            <p>En un mundo competitivo la certificación hace la diferencia, el objetivo principal es proporcionar confianza a todas las partes interesadas de que una certificación de producto cumple con los requisitos especificados. </p>
            <p>A continuación, se muestra el proceso de certificación IBNORCA.</p>
           </div>   
        <br>
          <div class="text-justificar">
            <p class="s-11" style="color:#AC1904;"><u>COMERCIAL</u></p>
            <img src="../assets/libraries/img/logos_oferta/certificacion_new.jpg" alt="NONE" width="100%" height="75px">
            <p class="s-11" style="color:#AC1904;"><u>CERTIFICACIÓN</u></p>
            <?php
            $tituloImagenTCS="oferta_a_new.jpg";
            if(isset($_GET["of"])){
               if ($_GET["of"]=="b") {
                  $tituloImagenTCS="oferta_b_new.jpg"; 
               }
            }?>
            <img src="../assets/libraries/img/logos_oferta/<?=$tituloImagenTCS?>" alt="NONE" width="100%" height="375px">
          </div>


    </div>
         <div class="text-justificar pt-2">
            <p class="s-10 bg-danger text-white">DESCRIPCIÓN DE LOS PROCESOS DE CERTIFICACIÓN</p>
            <p>Las etapas del proceso de certificación se detallan en el RMT-TC-01 Reglamento de Certificación de Producto, documento disponible en la página web www.ibnorca.org.</p>
            <p><b>NOTA: Si durante los 3 años de vigencia del certificado hubiese algún cambio en la organización que afecte al producto, sistema de gestión o la información brindada al inicio del proceso, es responsabilidad de la organización comunicar de inmediato a IBNORCA para actualizar la propuesta.</b></p>
        </div>
     <div class="saltopagina"></div>
     <div class="titulo_texto_inf text-danger s-11" style="color:#AC1904;"><u>ANEXO II</u></div>
     <div class="titulo_texto_inf text-danger s-11" style="color:#AC1904;"><u>CONTRATO DE PRESTACIÓN DE SERVICIO</u></div>  
        <div class="text-justificar">
            <p>Conste por el presente documento privado que al sólo reconocimiento de firmas podrá ser elevado a instrumento público, un Contrato Civil de Servicio que se suscribe al amparo de lo previsto por los Art. 519, 568, 732 del Código Civil, así como otras disposiciones concordantes con la materia al tenor de las siguientes cláusulas</p>
            <p class="s-10 bg-danger text-white">PRIMERA: PARTES</p>
            <p>Constituyen partes integrantes del presente contrato:</p>
            <p class="pl-2"><?=str_replace("\n", "</p><p class='pl-2'>",obtenerValorOferta($codOferta,15,$default,1))?></p>
            <p>A efectos del presente contrato, y según el contexto de cada cláusula se podrá referir como “Partes” a ambos suscribientes cuando actúen de manera conjunta y simplemente como “Parte” cuando la referencia sea a uno solo de ellos.</p>
            
            <p class="s-10 bg-danger text-white">SEGUNDA: OBJETO Y ALCANCE</p>
            <p>El objeto del presente contrato es establecer los términos y condiciones por los que IBNORCA prestará sus servicios para la realización de la auditoría correspondiente para la Certificación de producto en favor del CLIENTE¸ en adelante simplemente los “Servicios”, el resultado de todo el proceso podrá culminar con la otorgación o no de la Certificación o mantenimiento de la certificación, según corresponda.</p>
            <p>Forman parte del presente contrato:</p>
            <p>1) La Propuesta Técnica que forma parte del presente documento<br>2)  Reglamento de Certificación de Producto (disponible en la página web www.ibnorca.org)<br>3)  Guía de Uso de Marca (disponible en la página web www.ibnorca.org)</p>
            <p>El Alcance de la Certificación se encuentra definido en el punto 1 de la propuesta técnica. La modificación de este alcance podrá ser solicitado por el CLIENTE o cuando el resultado de las auditorías así lo determine. El alcance definitivo estará debidamente consensuado y plasmado en el Certificado.</p>
            
            <p class="s-10 bg-danger text-white">TERCERA: VIGENCIA Y PLAZOS DE EJECUCIÓN</p>
            <p>El presente contrato estará vigente desde la fecha de su suscripción hasta concluir las etapas del proceso de certificación y sus correspondientes plazos de ejecución que serán coordinados entre IBNORCA y el CLIENTE de acuerdo a lo establecido en la en la propuesta económica.<br>Para el caso en que el CLIENTE requiera modificar la fecha de inicio de cualquier etapa o auditorías ya previstas y coordinadas, deberá comunicar esta determinación con una antelación de veinte (20) días calendario a la fecha de inicio. En caso de no comunicar dicha modificación dentro del plazo señalado, el CLIENTE deberá abonar a IBNORCA todos los costos y gastos en los que se haya incurrido.</p>    
            
            <p class="s-10 bg-danger text-white">CUARTA: CONTRAPRESTACIÓN</p>
            <p>El CLIENTE se obliga a cancelar en favor de IBNORCA, la contraprestación de acuerdo a los establecido en el punto 5 de la Propuesta Técnica.</p>    

            <p class="s-10 bg-danger text-white">QUINTA: FORMA DE PAGO</p>
            <p>Concluida la auditoría, IBNORCA emitirá la correspondiente factura, debiendo el CLIENTE realizar el pago correspondiente a más tardar dentro de los siguientes 15 días de recibida la misma.<br>La factura correspondiente por Derecho de uso de Sello IBNORCA, se emitirá cada gestión en el mes aniversario del certificado emitido, debiendo el CLIENTE realizar el pago correspondiente a más tardar dentro de los siguientes 15 días de recibida la misma.<br>En caso que el CLIENTE no pague el monto de la factura en el plazo señalado, el CLIENTE pagará a IBNORCA, el 2 % de interés sobre el monto adeudado.</p>     
            <p class="s-10 bg-danger text-white">SEXTA: NATURALEZA DEL CONTRATO E INEXISTENCIA DE RELACIÓN LABORAL</p>
            <p>Se deja plenamente establecido que el presente contrato es de naturaleza estrictamente civil debiendo someterse a las normas del Código Civil, aclarándose en consecuencia que entre el CLIENTE e IBNORCA y entre cada una de las Partes con el personal de la otra no existe absolutamente ninguna relación ni vinculación laboral como tampoco de seguridad social. </p>    
            <p class="s-10 bg-danger text-white">SEPTIMA: AUTORIZACIÓN DE USO DE MARCA</p>
            <p>En caso que el CLIENTE obtenga la Certificación por parte de IBNORCA o la renovación de la misma, IBNORCA autoriza al CLIENTE al uso de las marcas y signos distintivos que son propios de IBNORCA.<br>La autorización contenida en el presente documento, solo permanecerá vigente en tanto la Certificación otorgada al CLIENTE se encuentre vigente. <br>El uso de los signos distintivos y marcas de IBNORCA por parte del CLIENTE fuera de las condiciones establecidas en el presente documento y en la Guía de Uso de Marca, será causal de retiro de la Certificación y en su defecto infracción a la normativa legal aplicable.<br>En caso de operar alguna sanción que implique suspensión o retiro de la Certificación, el CLIENTE no podrá usar las marcas registradas de IBNORCA a partir del momento en el que opere la suspensión o retiro de la certificación.</p>    
            <p class="s-10 bg-danger text-white">OCTAVA: RÉGIMEN SANCIONATORIO</p>
            <p>El CLIENTE se somete al régimen de suspensión, retiro de la Certificación y de sanciones establecido en el Reglamento de Certificación de Producto de IBNORCA.</p>    
            <p class="s-10 bg-danger text-white">NOVENA: APLICACIÓN DE REGLAMENTOS DE IBNORCA</p>
            <p>El CLIENTE declara conocer todas y cada una de las condiciones y estipulaciones del Reglamento de Certificación de Producto de IBNORCA, disponible en la página web www.ibnorca.org.<br>En este sentido, el CLIENTE se obliga a cumplir todas y cada una de las cláusulas, condiciones, artículos, obligaciones y otras establecidas en dicho reglamento. IBNORCA podrá modificar unilateralmente dicho reglamento. En caso de modificaciones, éstas serán comunicadas y se tendrá disponible en la página web www.ibnorca.org para su debido cumplimiento.</p>    
            <p class="s-10 bg-danger text-white">DÉCIMA: VERIFICACIÓN DE CUMPLIMIENTO</p>
            <p>Las Partes acuerdan que IBNORCA podrá, en cualquier momento, realizar acciones de verificación de cumplimiento del presente contrato y de los reglamentos de IBNORCA. El CLIENTE se obliga a proporcionar cualquier información que requiera IBNORCA, así como a permitir el acceso a sus instalaciones sin limitación alguna.<br>Entre dichas acciones IBNORCA podrá realizar auditorías sea de oficio o por que medie alguna denuncia por parte de terceros. El costo de dichas auditorías será pagado por el CLIENTE de acuerdo a los aranceles vigentes.<br>En caso que la organización no acepte la realización de la auditoría antes referida, IBNORCA procederá a la suspensión de la certificación por el tiempo que establezca, durante este periodo el CLIENTE deberá someterse a la auditoría de verificación mencionada; pasado este periodo IBNORCA retirará la certificación.</p>    
            <p class="s-10 bg-danger text-white">DÉCIMA PRIMERA: RESOLUCIÓN DEL CONTRATO</p>
            <p>En caso que cualquiera de las Partes incumpla sus obligaciones sustanciales asumidas en el presente contrato y con lo establecido en el Reglamento de Certificación de Producto de IBNORCA, la parte afectada con el incumplimiento comunicará dicho aspecto a la otra parte otorgándole un plazo razonable para su debido cumplimiento. Si vencido el plazo otorgado no se cumple la obligación, el presente contrato quedará resuelto de pleno derecho y sin necesidad de comunicación previa ni actuación judicial o extrajudicial alguna. </p>    
            <p class="s-10 bg-danger text-white">DÉCIMA SEGUNDA: IMPOSIBILIDAD SOBREVENIDA</p>
            <p>Ninguna de las Partes será considerada responsable, cuando dicho incumplimiento sea ocasionado por imposibilidad sobreviniente no imputable a la Parte que incumpliere sus obligaciones.<br>Se entiende como imposibilidad sobreviniente a los eventos de caso fortuito y fuerza mayor, sean éstos de cualquier naturaleza, como ser: catástrofes, descargas atmosféricas, incendios, inundaciones, epidemias, y a hechos provocados por los hombres, tales como y de manera enunciativa, actos de terrorismo o de vandalismo, huelgas, bloqueos de caminos, guerra, sabotajes, actos del Gobierno como entidad soberana o persona privada que alteren substancialmente los derechos y/o obligaciones de las Partes, siempre que tales eventos no sean previsibles, o de serlo, sean imposibles de evitar y por tanto, no sean imputables a la Parte afectada e impidan el cumplimiento de sus obligaciones contraídas en virtud al presente Contrato o, de manera general, cualquier causal fuera del control de la Parte que incumpla y no atribuible a ella. <br>La Parte afectada deberá comunicar a la otra, en forma escrita, dentro de los dos (2) días hábiles de conocido el evento proporcionando toda la información disponible que permita corroborar la imposibilidad sobreviniente.<br>Si la imposibilidad sobreviniente persiste por más de treinta (30) días, las Partes tendrán la posibilidad de decidir si continúan con el presente Contrato o lo resuelven sin penalidad alguna.</p>    
            <p class="s-10 bg-danger text-white">DÉCIMA TERCERA: SOLUCIÓN DE CONTROVERSIAS CERTIFICACIÓN IBNORCA</p>
            <p>Las Partes expresan que los términos del presente Contrato y las obligaciones que de él emergen, se encuentran bajo la jurisdicción de las leyes y autoridades bolivianas. Todo litigio, discrepancia, cuestión y reclamación resultante de la ejecución o interpretación del presente Contrato o relacionado con él, directa o indirectamente, se someterá previamente a la negociación directa entre Partes. <br>Si agotada la negociación entre Partes o expirado el plazo máximo de 10 (Diez) días calendario, la controversia no fuese resuelta amigablemente, la misma se resolverá definitivamente mediante arbitraje en el marco de la Ley No. 708 de 25 de junio de 2015 Ley de Conciliación y Arbitraje o de la ley que regule dicho medio alternativo de solución de controversias. <br>El arbitraje se sujetará a las autoridades, reglas y al procedimiento contenido en el Reglamento de Arbitraje del Centro de Conciliación y Arbitraje de la Cámara Nacional de Comercio de la ciudad de La Paz. Igualmente, las Partes hacen constar expresamente su compromiso de cumplir el Laudo Arbitral que se dicte, renunciando en la medida permitida por Ley, a cualquier tipo de recurso contra el mismo.<br>Los costos emergentes del proceso de arbitraje serán asumidos en su totalidad por la parte que resulte perdedora. En caso de que se pudiera llegar a una conciliación antes de emitirse el Laudo Arbitral, los costos en los que se hubieran incurrido serán cubiertos por ambas partes en iguales porcentajes (50%).<br>Las Partes excluyen de la presente cláusula la verificación por parte de la autoridad competente, la comisión de infracciones en las que incurra EL CLIENTE a los derechos de propiedad intelectual de IBNORCA. No obstante, de ello, una vez verificada la infracción, los daños y perjuicios que genere dicha infracción serán calculados en negociación o en arbitraje conforme lo establece la presenta cláusula.</p>    
            <p class="s-10 bg-danger text-white">DÉCIMA CUARTA: CONDICIONES GENERALES</p>
            <p>EL CLIENTE debe permitir a requerimiento de IBNORCA, la participación de representantes de organismos de acreditación, en calidad de observadores, durante la auditoría.<br>Durante los procesos de auditoría, no se permite la intervención del consultor del Sistema de Gestión de la organización. De ser requerida su participación, su rol será únicamente de observador.<br>El IBNORCA podrá sugerir la modalidad de auditorías remotas para evaluar los procesos, cuando corresponda.</p>    
            <p class="s-10 bg-danger text-white">DÉCIMA QUINTA: ACEPTACIÓN Y CONSENTIMIENTO</p>
            <p>Las Partes, cuyas generales de ley se encuentran identificadas en la primera cláusula del presente contrato, declaran y reconocen que el mismo ha sido leído y comprendido en su integridad, así como los documentos relacionados al mismo, aceptando el contenido y manifestando su pleno consentimiento, sin que medie vicio alguno del consentimiento.</p>    

            
        </div>
    </div>
    
    <div class="s-9 pt-6">
      <table class="table-grande pt-1">
                <tr class="s-11">
                    <td class="text-center text-info" width="25%">________________________</td>
                    <td class="text-center text-white" width="25%">________</td>
                    <td class="text-center text-white" width="25%">________</td>
                    <td class="text-center text-info" width="25%">________________________</td>
                </tr>
                <tr class="s-11 font-weight-bold">
                    <td class="text-center" width="25%">FIRMA<br>CLIENTE</td>
                    <td class="text-center text-white" width="25%">________</td>
                    <td class="text-center text-white" width="25%">________</td>
                    <td class="text-center" width="25%">FIRMA<br>IBNORCA</td>
                </tr>    
        </table>
    </div>

    <!--<div class="saltopagina"></div>-->
 </div>  

</body></html>
