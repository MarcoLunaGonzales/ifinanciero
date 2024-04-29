<?php

$start_time = microtime(true);

session_start();
set_time_limit(0);
error_reporting(-1);
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../styles.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';
//
setlocale(LC_TIME, "Spanish");

$dbh = new Conexion();
$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

$globalNombreGestion=$_SESSION["globalNombreGestion"];
$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalNombreUnidad=$_SESSION['globalNombreUnidad'];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];

$fechaActual=date("Y-m-d");
$dbh = new Conexion();
if(isset($_GET['cod'])){
	$codigo=$_GET['cod'];
  $codigoSimulacionSuper=$_GET['cod'];
}else{
	$codigo=0;
}
$lista= obtenerPaisesServicioIbrnorca();
if(isset($_GET['q'])){
 $idServicioX=$_GET['q'];
 $s=$_GET['s'];
 $u=$_GET['u'];
 ?>
  <input type="hidden" name="id_servicioibnored" value="<?=$idServicioX?>" id="id_servicioibnored"/>
  <input type="hidden" name="id_servicioibnored_s" value="<?=$s?>" id="id_servicioibnored_s"/>
  <input type="hidden" name="id_servicioibnored_u" value="<?=$u?>" id="id_servicioibnored_u"/>
 <?php
 if(isset($_GET['u'])){
  $u=$_GET['u'];
 ?>
  <input type="hidden" name="idPerfil" value="<?=$u?>" id="idPerfil"/>
 <?php
 }
}else{
  $idServicioX=0; 
}

/*VARIABLE DE CONVERSION A MODEDA USD*/
$usd=6.96;
/*FIN*/

$nombreClienteX=obtenerNombreClienteSimulacion($codigo);


$estiloUtilidadIbnorca="";
$simulacionEn="";


/******************************************************/
/*              ORGANISMO CERTIFICADOR                */
/******************************************************/
$array_organismo_certificador = [];
$sqlOrgCer = "SELECT soc.cod_orgnismocertificador, oc.nombre
          FROM simulaciones_servicios_organismocertificador soc 
          LEFT JOIN organismo_certificador oc ON oc.codigo = soc.cod_orgnismocertificador
          WHERE soc.cod_simulacionservicio = '$codigo'";
$stmtOrgCer = $dbh->prepare($sqlOrgCer);
$stmtOrgCer->execute();

/***********************************/
/*              IAF                */
/***********************************/
$array_cod_iaf = [];
$sqlIAF = "SELECT si.cod_iaf, i.nombre
          FROM simulaciones_servicios_iaf si 
          LEFT JOIN iaf i ON i.codigo = si.cod_iaf
          WHERE si.cod_simulacionservicio = '$codigo'";
$stmtIAF = $dbh->prepare($sqlIAF);
$stmtIAF->execute();

/***************************************************/
/*              Categoria Inocuidad                */
/***************************************************/
$array_cod_categoriainocuidad = [];
$sqlCatIno = "SELECT sci.cod_categoriainocuidad, ci.nombre
          FROM simulaciones_servicios_categoriasinocuidad sci 
          LEFT JOIN categorias_inocuidad ci ON ci.codigo = sci.cod_categoriainocuidad
          WHERE sci.cod_simulacionservicio = '$codigo'";
$stmtCatIno = $dbh->prepare($sqlCatIno);
$stmtCatIno->execute();

/***************************************************/
/*           SELECCION DE SERVICIOS                */
/***************************************************/
$array_servicios = [];
$sqlServicio = "SELECT sss.cod_simulacionservicio, sss.cod_servicio 
          FROM simulaciones_servicios_serv sss
          WHERE sss.cod_simulacionservicio = '$codigo'";
$stmtServicio = $dbh->prepare($sqlServicio);
$stmtServicio->execute();
while ($row_serv = $stmtServicio->fetch(PDO::FETCH_ASSOC)) {
  $array_servicios[]   = $row_serv['cod_servicio'];
}

/***************************************************/
/*              Normas Nacionales                 */
/***************************************************/
$array_norma_nac = [];
$sqlNorNac = "SELECT ssn.cod_norma, vn.abreviatura, vn.nombre FROM simulaciones_servicios_normas ssn
          LEFT JOIN v_normas vn ON vn.codigo = ssn.cod_norma
          WHERE ssn.cod_simulacionservicio = '$codigo'
          AND ssn.catalogo = 'L'";
$stmtNorNac = $dbh->prepare($sqlNorNac);
$stmtNorNac->execute();

/***************************************************/
/*              Normas Internacionales            */
/***************************************************/
$array_norma_int = [];
$sqlNorInt = "SELECT ssn.cod_norma, vni.abreviatura, vni.nombre FROM simulaciones_servicios_normas ssn
          LEFT JOIN v_normas_int vni ON vni.codigo = ssn.cod_norma
          WHERE ssn.cod_simulacionservicio = '$codigo'
          AND ssn.catalogo = 'I'";
$stmtNorInt = $dbh->prepare($sqlNorInt);
$stmtNorInt->execute();

/****************************************/
/*              Otras Normas            */
/****************************************/
$array_norma_otra = [];
$sqlNorOtras = "SELECT ssn.observaciones
          FROM simulaciones_servicios_normas ssn
          WHERE ssn.cod_simulacionservicio = '$codigo'
          AND ssn.catalogo IS NULL";
$stmtNorOtras = $dbh->prepare($sqlNorOtras);
$stmtNorOtras->execute();


$precioLocalX=obtenerPrecioServiciosSimulacion($codigo);
$precioLocalInputX=number_format($precioLocalX, 2, '.', '');

$precioLocalInputXUSD=number_format($precioLocalX/$usd, 2, '.', '');
$alumnosX=obtenerCantidadTotalPersonalSimulacionEditado($codigo);

/**
 * obtiene porcentaje de ajuste de la plantilla
 */
$sql_porcentaje = "SELECT p.porcentaje_ajuste, p.porcentaje_ajuste2, p.porcentaje_ajuste_ing
                   FROM plantillas_servicios p
                   LEFT JOIN simulaciones_servicios ss ON ss.cod_plantillaservicio = p.codigo
                   WHERE ss.codigo = '$codigo'
                   LIMIT 1";
$stmt_porcentaje = $dbh->prepare($sql_porcentaje);
$stmt_porcentaje->execute();
$resultado_pa = $stmt_porcentaje->fetch(PDO::FETCH_ASSOC);
$porcentaje_ajusteX1   = 0;
$porcentaje_ajusteX2   = 0;
$porcentaje_ajuste_ing = 0;

if ($resultado_pa) {
    $porcentaje_ajusteX1   = $resultado_pa['porcentaje_ajuste'];
    $porcentaje_ajusteX2   = $resultado_pa['porcentaje_ajuste2'];
    $porcentaje_ajuste_ing = $resultado_pa['porcentaje_ajuste_ing'];
}
$costoVariablePersonal = obtenerCostosPersonalSimulacionEditado($codigo);

$ibnorcaC=1;
$utilidadFueraX=1;
$mesConf=obtenerValorConfiguracion(6);
$stmt1 = $dbh->prepare("SELECT sc.*,es.nombre as estado 
                    FROM simulaciones_servicios sc 
                    JOIN estados_simulaciones es ON sc.cod_estadosimulacion=es.codigo 
                    WHERE sc.cod_estadoreferencial=1 
                    AND sc.codigo='$codigo'");
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
            $stmt1->bindColumn('cod_unidadorganizacional', $oficinaGlobalX);
            $stmt1->bindColumn('cod_iaf_primario', $codIAFX);
            $stmt1->bindColumn('cod_iaf_secundario', $codIAFSecX);

            $stmt1->bindColumn('cod_objetoservicio', $cod_objetoservicioX);
            $stmt1->bindColumn('idServicio', $idServicioSimX);
            $stmt1->bindColumn('cod_cliente', $cod_clienteX);
            $stmt1->bindColumn('cod_cliente', $cod_clienteX);
            $stmt1->bindColumn('cod_tipoclientenacionalidad', $cod_tipoclientenacionalidadX);
            $stmt1->bindColumn('cod_tipocliente', $cod_tipoclienteX);
            
            $stmt1->bindColumn('cod_servicio', $cod_servicio);
            $stmt1->bindColumn('cod_estadosimulacion', $cod_estadosimulacion);
            $stmt1->bindColumn('propuesta_gestion', $propuesta_gestion);
            $stmt1->bindColumn('propuesta_gestion2', $propuesta_gestion2);
            $stmt1->bindColumn('propuesta_gestion3', $propuesta_gestion3);

      while ($row1 = $stmt1->fetch(PDO::FETCH_BOUND)) {
         //plantilla datos      
			      $stmt = $dbh->prepare("SELECT p.*, u.abreviatura as unidad,a.abreviatura as area from plantillas_servicios p,unidades_organizacionales u, areas a where p.cod_unidadorganizacional=u.codigo and p.cod_area=a.codigo and p.codigo='$codigoPlan' order by codigo");
			      $stmt->execute();
			      $stmt->bindColumn('codigo', $codigoPX);
            $stmt->bindColumn('nombre', $nombreX);
            $stmt->bindColumn('abreviatura', $abreviaturaX);
            $stmt->bindColumn('cod_unidadorganizacional', $codUnidadX);        
            $stmt->bindColumn('dias_auditoria', $diasPlantilla);
            $stmt->bindColumn('cod_area', $codAreaX);
            $stmt->bindColumn('area', $areaX);
            $stmt->bindColumn('unidad', $unidadX);

            $oficinaGlobalX=$oficinaGlobalX;
            $codIAFX=$codIAFX;
            $codIAFSecX=$codIAFSecX;
            $cod_clienteX=$cod_clienteX;
            $cod_objetoservicioX=$cod_objetoservicioX;
            $idServicioSimX=$idServicioSimX;
            $cod_tipoclientenacionalidadX=$cod_tipoclientenacionalidadX;
            $cod_tipoclienteX=$cod_tipoclienteX;
            $existeNormaText=implode(",",obtenerNormasTextSimulacionServicio($codigoSimulacionSuper));
           $anioGeneral=$anioX;
           $nombreSimulacion=$nombreX;
           $porcentajeFijoSim=$porcentajeFijoX;
           $afnorX=$afnorX; 
           $porcentajeAfnor=$porcentajeAfnorX;
           if($afnorX==0){
            $precioAfnorX=0;
            $tituloAfnor="1";
           }else{
            $iva=obtenerValorConfiguracion(1);
            $it=obtenerValorConfiguracion(2);
            $precioAfnorX=(((100-($iva+$it))/100)*$precioLocalX)*($porcentajeAfnorX/100);
            $tituloAfnor=$porcentajeAfnorX." %";
           }

           if($codAreaX==39){
            $valorC=17;
            $inicioAnio=1;
           }else{
            $valorC=18;
            $inicioAnio=0;
           }
           
           $idTipoServGlobal=$idTipoServicioX;
           if($idTipoServGlobal==0){
             $idTipoServGlobal=309;
           }
           $alcanceSimulacionXX=$alcanceSimulacionX;
           $descripcionServSimulacionXX=$descripcionServSimulacionX;
      }    
      

/*************************************************/
/*           JSON | PRODUCTOS O SITIOS           */
/*************************************************/
// $sqlArrayAtributos="SELECT
//                 s.codigo,
//                 sa.nombre,
//                 sa.direccion,
//                 sa.marca,
//                 sa.nro_sello,
//                 GROUP_CONCAT(DISTINCT vi.codigo SEPARATOR ', ') AS atr_norma_int,
//                 GROUP_CONCAT(DISTINCT vn.codigo SEPARATOR ', ') AS atr_norma_nac,
//                 CONCAT('<ul>',
//                     GROUP_CONCAT(DISTINCT CONCAT('<li>', vi.nombre, '</li>') SEPARATOR ''),
//                     GROUP_CONCAT(DISTINCT CONCAT('<li>', vn.nombre, '</li>') SEPARATOR ''),
//                     '</ul>') AS atr_norma_html
//               FROM simulaciones_servicios s
//               LEFT JOIN simulaciones_servicios_atributos sa ON sa.cod_simulacionservicio = s.codigo
//               LEFT JOIN simulaciones_servicios_atributosnormas sni ON sni.cod_simulacionservicioatributo = sa.codigo AND sni.catalogo = 'I'
//               LEFT JOIN normas vi ON sni.cod_norma = vi.codigo
//               LEFT JOIN simulaciones_servicios_atributosnormas snl ON snl.cod_simulacionservicioatributo = sa.codigo AND snl.catalogo = 'L'
//               LEFT JOIN normas vn ON snl.cod_norma = vn.codigo
//               WHERE s.codigo = '$codigo'";
$sqlArrayAtributos="SELECT
                          s.codigo,
                          sa.nombre,
                          sa.direccion,
                          sa.marca,
                          sa.nro_sello,
                          GROUP_CONCAT(DISTINCT vi.codigo SEPARATOR ', ') AS atr_norma_int,
                          (SELECT GROUP_CONCAT(DISTINCT vn.codigo SEPARATOR ', ')
                          FROM simulaciones_servicios_atributosnormas snl
                          LEFT JOIN normas vn ON snl.cod_norma = vn.codigo
                          WHERE snl.cod_simulacionservicioatributo = sa.codigo AND snl.catalogo = 'L'
                          GROUP BY snl.cod_simulacionservicioatributo) AS atr_norma_nac,
                          CONCAT('<ul>',
                              GROUP_CONCAT(DISTINCT CONCAT('<li>', vi.nombre, '</li>') SEPARATOR ''),
                              (SELECT GROUP_CONCAT(DISTINCT CONCAT('<li>', vn.nombre, '</li>') SEPARATOR '')
                              FROM simulaciones_servicios_atributosnormas snl
                              LEFT JOIN normas vn ON snl.cod_norma = vn.codigo
                              WHERE snl.cod_simulacionservicioatributo = sa.codigo AND snl.catalogo = 'L'
                              GROUP BY snl.cod_simulacionservicioatributo),
                              '</ul>') AS atr_norma_html
                    FROM simulaciones_servicios s
                    LEFT JOIN simulaciones_servicios_atributos sa ON sa.cod_simulacionservicio = s.codigo
                    LEFT JOIN simulaciones_servicios_atributosnormas sni ON sni.cod_simulacionservicioatributo = sa.codigo AND sni.catalogo = 'I'
                    LEFT JOIN normas vi ON sni.cod_norma = vi.codigo
                    WHERE s.codigo = '$codigo'
                    GROUP BY s.codigo, sa.nombre, sa.direccion, sa.marca
                    ORDER BY sa.codigo ASC";
$stmtArrayAtributos = $dbh->prepare($sqlArrayAtributos);
$stmtArrayAtributos->execute();
?>
    <script>
      var atributosArrayGral = []; 
    </script>

<?php
  $index = 1;
  while ($row = $stmtArrayAtributos->fetch(PDO::FETCH_ASSOC)) {
?>
    <script>
    var atributo={
      codigo: <?=$index++?>,
      nombre: '<?=$row['nombre']?>',
      direccion: '<?=$row['direccion']?>',
      norma:'',
      atr_norma_nac: '<?=$row['atr_norma_nac']?>',
      atr_norma_int: '<?=$row['atr_norma_int']?>',
      atr_norma_html: '<?=$row['atr_norma_html']?>',
      marca: '<?=$row['marca']?>',
      sello: '<?=$row['nro_sello']?>',
      pais:0,
      estado:'',
      ciudad:0,
      nom_pais:'',
      nom_estado:'',
      nom_ciudad:''
    }
    atributosArrayGral.push(atributo);
    </script>

<?php
  }
?>
<script>
  var itemAtributos=[];
  var itemAtributosDias=[];
</script>
<?php 
  $sqlAtributos="SELECT * from simulaciones_servicios_atributos where cod_simulacionservicio=$codigo GROUP BY nombre, direccion, cod_tipoatributo, habilitado, marca, norma, nro_sello, cod_ciudad, cod_estado, cod_pais";
  
  //echo "sqlAtributos: ".$sqlAtributos;
  
  $stmtAtributos = $dbh->prepare($sqlAtributos);
  $stmtAtributos->execute();
  $codigoFilaAtrib=0;
  while ($rowAtributo = $stmtAtributos->fetch(PDO::FETCH_ASSOC)) {
   $codigoXAtrib=$rowAtributo['codigo'];
   $nombreXAtrib=$rowAtributo['nombre'];
   $direccionXAtrib=$rowAtributo['direccion'];
   $normaXAtrib=$rowAtributo['norma'];
   $marcaXAtrib=$rowAtributo['marca'];
   $selloXAtrib=$rowAtributo['nro_sello'];
   $tipoXAtrib=$rowAtributo['cod_tipoatributo'];
   $paisXAtrib=$rowAtributo['cod_pais'];
   $estadoXAtrib=$rowAtributo['cod_estado'];
   $ciudadXAtrib=$rowAtributo['cod_ciudad'];

  if($paisXAtrib==0){
    $nom_ciudadXAtrib="SIN REGISTRO";
    $nom_estadoXAtrib="SIN REGISTRO";
    $nom_paisXAtrib="SIN REGISTRO";
  }else{
   $lista= obtenerPaisesServicioIbrnorca();
   foreach ($lista->lista as $listas) {
      if($listas->idPais==$paisXAtrib){
        $nom_paisXAtrib=strtoupper($listas->paisNombre);
        $lista2= obtenerDepartamentoServicioIbrnorca($paisXAtrib);
        foreach ($lista2->lista as $listas2) {
          if($listas2->idEstado==$estadoXAtrib){
            $nom_estadoXAtrib=strtoupper($listas2->estNombre);
            $lista3= obtenerCiudadServicioIbrnorca($estadoXAtrib);
            foreach ($lista3->lista as $listas3) {
              if($listas3->idCiudad==$ciudadXAtrib){
                $nom_ciudadXAtrib=strtoupper($listas3->nomCiudad);
                break;
              }else{
                $nom_ciudadXAtrib="SIN REGISTRO";
              }     
           }
           break;
          }else{
            $nom_estadoXAtrib="SIN REGISTRO";
          }
        }
       break; 
      }else{
       $nom_paisXAtrib="SIN REGISTRO";
     }
    }
    
  }

//normas 
  $normaCodXAtrib="";
  $normaXAtribOtro="";
  $normaAtrib=[];
  $normaAtrib=explode(",", $normaXAtrib);

  if($tipoXAtrib==1){
    $sqlAtributosNormas="SELECT * from simulaciones_servicios_atributosnormas where cod_simulacionservicioatributo=$codigoXAtrib GROUP BY cod_simulacionservicioatributo, cod_norma, precio, cantidad";

    //echo "sql atributos normas: ".$sqlAtributosNormas;
    
    $stmtAtributosNorma = $dbh->prepare($sqlAtributosNormas);
    $stmtAtributosNorma->execute();
    $ni=0;$normaFila=[];
    while ($rowAtributoNorma = $stmtAtributosNorma->fetch(PDO::FETCH_ASSOC)) {
     $normaFila[$ni]=$rowAtributoNorma['cod_norma'];
     $existeNorma=-1;
     for ($nr=0; $nr < count($normaAtrib); $nr++) { 
      if($normaAtrib[$nr]!=null){
        if($normaAtrib[$nr]==nameNorma($rowAtributoNorma['cod_norma'],'N')){
          $existeNorma=$nr; 
        }
      }    
     }
     if($existeNorma>=0){
      unset($normaAtrib[$existeNorma]);
     }
     $ni++; 
    }
   $normaCodXAtrib=implode(",",$normaFila); 
   if(count($normaAtrib)>0){
    $normaXAtribOtro=implode(",",$normaAtrib); 
   }else{
    $normaXAtribOtro="";
   }
  }else{
//atributos auditores
  for ($an=0; $an<=$anioGeneral; $an++) { 
    $sqlAuditoresAtrib="SELECT sa.*,s.descripcion FROM simulaciones_servicios_atributosauditores sa join simulaciones_servicios_auditores s on s.codigo=sa.cod_auditor join tipos_auditor t on s.cod_tipoauditor=t.codigo where s.cod_simulacionservicio=$codigoSimulacionSuper and s.cod_anio=$an and sa.cod_simulacionservicioatributo=$codigoXAtrib and s.cod_tipoauditor!=-100 GROUP BY sa.cod_simulacionservicioatributo, sa.cod_auditor, sa.cod_anio, sa.estado, s.descripcion order by t.nro_orden";
    
    //echo "atributos auditores: ".$sqlAuditoresAtrib;

    $stmtAuditoresAtrib=$dbh->prepare($sqlAuditoresAtrib);
    $stmtAuditoresAtrib->execute();
    ?>
    <div class="d-none"><select class="form-control selectpicker form-control-sm" data-style="fondo-boton fondo-boton-active" multiple name="auditores<?=$an?>EEEE<?=$codigoFilaAtrib?>[]" id="auditores<?=$an?>EEEE<?=$codigoFilaAtrib?>"><?php
     while ($rowAuditoresAtrib = $stmtAuditoresAtrib->fetch(PDO::FETCH_ASSOC)) {
      $codigoTipoEE=$rowAuditoresAtrib['cod_auditor'];
      $nombreTipoEE=$rowAuditoresAtrib['descripcion'];
      $habilitadoEE=$rowAuditoresAtrib['estado'];
      $words = explode(" ", $nombreTipoEE);
      $numerox = explode("(", $nombreTipoEE);
      $acronym = "";
      foreach ($words as $w) {
       if(!(strtolower($w)=="de"||strtolower($w)=="el"||strtolower($w)=="la"||strtolower($w)=="y"||strtolower($w)=="en")){
          $acronym .= $w[0];
       }        
      }
      $abreviatura = $acronym;
      if(count($numerox)>1){
       $abreviatura.="(".$numerox[1];
      }
      //$cantidadTipo=$row['cantidad_editado'];
      if($habilitadoEE==1){
        ?>
         <option value="<?=$codigoTipoEE?>" selected><?=$abreviatura?></option>
       <?php 
      }else{
        ?>
         <option value="<?=$codigoTipoEE?>"><?=$abreviatura?></option>
       <?php 
      }
       
    }
   ?></select></div><?php 
}
//fin de atributos auditores
  }
   ?>
    <script>
    var atributo={
    codigo: '<?=$codigoFilaAtrib?>',  
    nombre: '<?=$nombreXAtrib?>',
    direccion: '<?=$direccionXAtrib?>',
    marca: '<?=$marcaXAtrib?>',
    norma: '<?=$normaXAtrib?>',
    norma_cod: '<?=$normaCodXAtrib?>',
    norma_otro: '<?=$normaXAtribOtro?>',
    sello: '<?=$selloXAtrib?>',
    pais: '<?=$paisXAtrib?>',
    estado: '<?=$estadoXAtrib?>',
    ciudad: '<?=$ciudadXAtrib?>',
    nom_pais: '<?=$nom_paisXAtrib?>',
    nom_estado: '<?=$nom_estadoXAtrib?>',
    nom_ciudad: '<?=$nom_ciudadXAtrib?>'
    }
    itemAtributos.push(atributo);
    </script>
   <?php
   //DIAS DE LOS SITIOS
   if($tipoXAtrib!=1){
    $stmtAtributosDias = $dbh->prepare("SELECT * from simulaciones_servicios_atributosdias where cod_simulacionservicioatributo=$codigoXAtrib GROUP BY dias, cod_anio");
    $stmtAtributosDias->execute();
    while ($rowAtributoDias = $stmtAtributosDias->fetch(PDO::FETCH_ASSOC)) {
      $nombreXAtribDias=$rowAtributoDias['dias'];
      $anioXAtribDias=$rowAtributoDias['cod_anio'];
      ?>
      <script>
      var atributoDias={
         codigo_atributo: '<?=$codigoFilaAtrib?>',  
         dias: '<?=$nombreXAtribDias?>',
         anio: '<?=$anioXAtribDias?>'
         }
       itemAtributosDias.push(atributoDias);
    </script>
      <?php
     } 
   } 
   $codigoFilaAtrib++;
  }

  
?>
<input type="hidden" name="anio_servicio" readonly value="<?=$anioX?>" id="anio_servicio"/>
<input type="hidden" name="porcentaje_fijo" readonly value="<?=$porcentajeFijoSim?>" id="porcentaje_fijo"/>
<input type="hidden" name="inicio_fijomodal" readonly value="0" id="inicio_fijomodal"/>
<input type="hidden" name="inicio_variablemodal" readonly value="0" id="inicio_variablemodal"/>
<input type="hidden" name="cambio_moneda" readonly value="<?=$usd?>" id="cambio_moneda"/>
<input type="hidden" name="alumnos_plan" readonly value="<?=$alumnosX?>" id="alumnos_plan"/>
<input type="hidden" name="utilidad_minlocal" readonly value="<?=$utilidadIbnorcaX?>" id="utilidad_minlocal"/>

<div class="cargar">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold">Procesando Datos</h4>
     <p class="text-white">Aguard&aacute; un momento por favor</p>  
  </div>
</div>
<div class="cargar-ajax d-none">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold" id="texto_ajax_titulo">Procesando Datos</h4>
     <p class="text-white">Aguard&aacute; un momento por favor</p>  
  </div>
</div>
<div class="content">
	<div id="contListaGrupos" class="container-fluid">
			<input type="hidden" name="cod_simulacion" id="cod_simulacion" value="<?=$codigo?>">
      <input type="hidden" name="cod_ibnorca" id="cod_ibnorca" value="1">
      <div class="row"><div class="card col-sm-5">
				<div class="card-header card-header-success card-header-text">
					<div class="card-text">
					  <h4 class="card-title">Informaci&oacute;n general de la Propuesta</h4>
					</div>
          <!--<button type="button" onclick="editarDatosSimulacion()" class="btn btn-success btn-sm btn-fab float-right">
             <i class="material-icons" title="Editar Simulación">edit</i>
          </button>-->
				</div>
				<div class="card-body ">
					<div class="row">
					<?php
                    $responsable=namePersonal($codResponsableX);
						?>
						<div class="col-sm-6">
							<div class="form-group">
						  		<label class="bmd-label-static">Numero</label>
					  			<input class="form-control" readonly type="text" name="nombre" readonly value="<?=$nombreX?>" id="nombre"/>
							</div>
						</div>

						<div class="col-sm-6">
							<div class="form-group">
						  		<label class="bmd-label-static">Responsable</label>
						  		<input class="form-control" type="text" name="responsable" readonly value="<?=$responsable?>" id="responsable"/>
							</div>
						</div>
          </div>
          <div class="row">
						<div class="col-sm-6">
							<div class="form-group">
						  		<label class="bmd-label-static">Fecha de Registro de Propuesta</label>
						  		<input class="form-control" type="text" name="fecha" value="<?=$fechaX?>" id="fecha" readonly/>
							</div>
						</div>

						<div class="col-sm-6">
				        	<div class="form-group">
						  		<label class="bmd-label-static">Estado</label>
						  		<input class="form-control" type="text" name="estado" value="<?=$estadoX?>" id="estado" readonly/>
							</div>
				    </div>
					  			<input class="form-control" type="hidden" readonly name="ibnorca" value="<?=$simulacionEn?>" id="ibnorca"/>
					</div>
				</div>
			</div>
			<div class="card col-sm-7">
				<div class="card-header card-header-info card-header-text">
					<div class="card-text">
					  <h4 class="card-title">Informaci&oacute;n a detalle de la Propuesta</h4>
					</div>
          <?php
            $array_excepciona_atr = ['4822'];
            $title_button         = '';        
            if ($codigoPlan == 3 || (in_array($idTipoServicioX, $array_excepciona_atr) && $codigoPlan == 2)) {
                $title_button             = 'Sitios';
            } else if ($codigoPlan == 2) {
                $title_button             = 'Productos';
            }
          ?>
          <button type="button" onclick="abrirAtributoLista()" class="btn btn-primary btn-sm btn-fab float-right" title="Lista de <?=$title_button?>">
            <i class="material-icons">label</i>
          </button>
          <button type="button" onclick="editarDatosPlantilla()" class="btn btn-success btn-sm btn-fab float-right">
             <i class="material-icons" title="Editar Plantilla">edit</i>
          </button>
          <button type="button" onclick="actualizarSimulacion()" class="btn btn-default btn-sm btn-fab float-right">
             <i class="material-icons" title="Actualizar la Simulación">refresh</i><span id="narch" class="bg-warning"></span>
          </button>
            
            <button type="button" class="btn btn-info btn-sm btn-fab float-right" id="modificar_gestion" title="Modificar Gestión de Propuesta de Presupuesto">
                <i class="material-icons">business</i>
            </button>
				</div>
				<div class="card-body ">
                     <div class="row">
					<?php while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
            if(!($oficinaGlobalX==""||$oficinaGlobalX==0)){
              $unidadX=abrevUnidad($oficinaGlobalX);
            }
               if($codAreaX==39){
                $inicioAnio=1;
                }else{
                 $inicioAnio=0;
                }
            ?>
					<input type="hidden" name="cod_plantilla" id="cod_plantilla" value="<?=$codigoPX?>">

						<div class="col-sm-6">
							<div class="form-group">
						  		<label class="bmd-label-static">Cliente</label>
					  			<input class="form-control" type="text" name="nombre_plan" value="<?=$nombreClienteX?>" id="nombre_plan" READONLY />
							</div>
						</div>
            <!-- Componente Oculto -->
						<div class="col-sm-2" hidden>
							<div class="form-group">
						  		<label class="bmd-label-static">Abreviatura</label>
						  		<input class="form-control" type="text" name="abreviatura_plan" value="<?=$abreviaturaX?>" READONLY id="abreviatura_plan"/>
							</div>
						</div>
						
						<div class="col-sm-2">
							<div class="form-group">
						  		<label class="bmd-label-static">Unidad</label>
						  		<input class="form-control" type="text" name="unidad_plan" value="<?=$unidadX?>" id="unidad_plan" readonly/>
							</div>
						</div>

						<div class="col-sm-2">
				        	<div class="form-group">
						  		<label class="bmd-label-static">Area</label>
                  <input class="form-control" type="hidden" name="codigo_area" value="<?=$codAreaX?>" id="codigo_area" readonly/>
						  		<input class="form-control" type="text" name="area_plan" value="<?=$areaX?>" id="area_plan" readonly/>
							</div>
				    </div>
            <!-- Nuevo campo Norma -->
            <div class="col-sm-2">
              <div class="form-group">
                  <button type="button" class="btn btn-info btn-round btn-fab btn-sm" onclick="abrir_modal_norma()">
                    <i class="material-icons" title="Ver más">dashboard</i>
                  </button>
                  <label class="label">Normas</label>
              </div>
            </div>
          </div>
           <div class="row">                
            <!--<div class="col-sm-2">
              <div class="form-group">
                  <label class="bmd-label-static">D&iacute;as Servicio</label>-->
                  <input class="form-control" type="hidden" name="dias_plan" readonly value="<?=$diasSimulacion?>" id="dias_plan"/>
                  <input class="form-control" type="hidden" name="productos_sim" readonly value="<?=$productosX?>" id="productos_sim"/>
                  <input class="form-control" type="hidden" name="sitios_sim" readonly value="<?=$sitiosX?>" id="sitios_sim"/>
              <!--</div>
            </div>-->
            <div class="col-sm-2">
              <div class="form-group">
                  <label class="bmd-label-static">AFNOR</label>
                  <input class="form-control" type="text" name="afnor_titulo" readonly value="<?=$tituloAfnor?>" id="afnor_titulo"/>
              </div>
            </div>
            <div class="col-sm-2">
              <div class="form-group">
                  <label class="bmd-label-static">Utilidad M&iacute;n %</label>
                  <input class="form-control" type="text" name="utilidad_minima_ibnorca" readonly value="<?=$utilidadIbnorcaX?>" id="utilidad_minima_ibnorca"/>
              </div>
            </div>
            <div class="col-sm-2">
              <div class="form-group">
                  <label class="bmd-label-static">A&ntilde;os</label>
                  <input class="form-control" type="text" name="anio_simulacion" readonly value="<?=$anioGeneral?>" id="anio_simulacion"/>
              </div>
            </div>
            <!-- Campo escondido -->
            <div class="col-sm-4" hidden>
              <div class="form-group">
                  <label class="bmd-label-static">Precio BOB</label>
                  <input class="form-control" type="text" name="precio_auditoria_ib" readonly value="<?=$precioLocalInputX?>" id="precio_auditoria_ib"/>
              </div>
            </div>
            <!-- Campo escondido-->
            <div class="col-sm-2" hidden>
              <div class="form-group">
                  <label class="bmd-label-static">Precio USD</label>
                  <input class="form-control" type="text" name="precio_auditoria_ibUSD" readonly value="<?=$precioLocalInputXUSD?>" id="precio_auditoria_ibUSD"/>
              </div>
            </div>
            <div class="col-sm-2">
              <div class="form-group">
                  <button type="button" class="btn btn-primary btn-round btn-fab btn-sm" onclick="abrir_modal_iaf()">
                    <i class="material-icons" title="Ver más">list</i>
                  </button>
                  <label class="label">IAF</label>
              </div>
            </div>
            <div class="col-sm-2">
              <div class="form-group" title="<?=$title_cod_categoriainocuidad?>">
                  <button type="button" class="btn btn-danger btn-round btn-fab btn-sm" onclick="abrir_modal_cat_ino()">
                    <i class="material-icons" title="Ver más">playlist_add_check</i>
                  </button>
                  <label class="bmd-label-static">Cat. Inocuidad</label>
              </div>
            </div>
            <div class="col-sm-2">
              <div class="form-group" title="<?=$title_orgnismo_certificador?>">
                  <button type="button" class="btn btn-warning btn-round btn-fab btn-sm" onclick="abrir_modal_org_cer()">
                    <i class="material-icons" title="Ver más">class</i>
                  </button>
                  <label class="bmd-label-static">Org. Certificador</label>
              </div>
            </div>
				      	<?php } ?>
					</div>      
             
				</div>
			</div>
		   </div>
           <div class="row">
             <div class="col-sm-12">
			  <div class="card">
				<div class="card-header card-header-warning card-header-text text-center">
					<div class="card-text">
					  <h4 class="card-title"><b id="titulo_curso">N: <?=$nombreSimulacion?></b></h4>
					</div>
				</div>
				<div class="card-body" id="div_simulacion">
			<?php
				//IVA y IT
				$iva=obtenerValorConfiguracion(1);
				$it=obtenerValorConfiguracion(2);
                $alumnosExternoX=1; 
                //modificar costos por alumnos

				//valores de la simulacion

                //total desde la plantilla 
                $nAuditorias=obtenerCantidadAuditoriasPlantilla($codigoPX); 
    
                /**********************************************
                * Obtiene Gestión de Propuesta de Presupuesto
                **********************************************/
                $simulacion_servicio_anio = $propuesta_gestion;
                // Obtiene Presupuesto
                if(empty($simulacion_servicio_anio)){ // SIN GESTIÓN
                    $precioRegistrado=(obtenerPrecioRegistradoPropuestaTCPTCS($codigoSimulacionSuper) * $porcentaje_ajuste_ing); // Con porcentaje de Ajuste Ingreso
                }else{
                    if($codAreaX == 5291){ // VERIFICA TVR
                        $codAreaX = 39;      // TVR pertenece a TCP
                    }
                    $precioRegistrado = (obtenerPresupuestoEjecucionPorAreaAcumulado(0,$codAreaX,$simulacion_servicio_anio,12,1)['presupuesto'] * $porcentaje_ajuste_ing);
                }
                
                if($precioRegistrado==0){
                    $precioRegistrado=1;
                }

                //tipo de costo 1:fijo,2:variable desde la plantilla
                $totalFijo=obtenerTotalesPlantillaServicio($codigoPX,1,$nAuditorias, $simulacion_servicio_anio);
                $porcentPrecios=($precioLocalX*100)/$precioRegistrado;
                $totalFijoPlan=$totalFijo[0]*($porcentPrecios/100);
                $totalFijoPlan=$totalFijoPlan*$anioGeneral;
                //total variable desde simulacion cuentas
                $totalVariable=obtenerTotalesSimulacionServicio($codigo);
                //
                if($precioLocalX==0){
                    $precioLocalX=1;
                }
                $alumnosRecoX=ceil((100*(-$totalFijoPlan-$totalVariable[2]))/(($utilidadIbnorcaX*$precioLocalX)-(100*$precioLocalX)+(($iva+$it)*$precioLocalX)));                    
                //if($alumnosX)
                $totalVariable[2]=$totalVariable[2]/$alumnosX;
                $totalVariable[3]=$totalVariable[3]/$alumnosExternoX;
                 //calcular cantidad alumnos si no esta registrado
                if($alumnosX==0){
                 	$porcentajeFinalLocal=0;$alumnosX=0;$alumnosExternoX=0;$porcentajeFinalExterno=0;
                 	while ($porcentajeFinalLocal < $utilidadIbnorcaX || $porcentajeFinalExterno<$utilidadFueraX) {
                 		$alumnosX++;
                 		include "calculoSimulacion.php";
                        $porcentajeFinalLocal=$pUtilidadLocal;
                        $porcentajeFinalExterno=$pUtilidadExterno;
                 	}                                 
                }else{
                	include "calculoSimulacion.php";
                }
 
                 if($ibnorcaC==1){
                 	$utilidadReferencial=$utilidadIbnorcaX;
                 	$ibnorca_title=""; // EN IBNORCA
                 }else{
                 	$utilidadReferencial=$utilidadFueraX;
                 	$ibnorca_title=""; //FUERA DE IBNORCA
                 }

                 //cambios para la nueva acortar la simulacion 
                 //$utilidadNetaLocal=$ingresoLocal-((($iva+$it)/100)*$ingresoLocal)-$totalFijoPlan-($totalVariable[2]*$alumnosX);
                 $utilidadNetaExterno=$ingresoExterno-((($iva+$it)/100)*$ingresoExterno)-$totalFijo[3]-($totalVariable[3]*$alumnosExternoX);

                 //$pUtilidadLocal=($utilidadNetaLocal*100)/$ingresoLocal;
                 $pUtilidadExterno=($utilidadNetaExterno*100)/$ingresoExterno;


                 //calculos en la simulacion SERVICIOS
                 $gastosOperacionNacional=($costoTotalLocal*(obtenerValorConfiguracion(19)/100));
                 $utilidadBruta=($precioLocalX)-($costoTotalLocal);   
                 $utilidadNetaLocal=$utilidadBruta-((($iva+$it)/100)*($precioLocalX))-($precioAfnorX);
                 $pUtilidadLocal=($utilidadNetaLocal*100)/($precioLocalX);

                 $codEstadoSimulacion=4; 
                 //echo "UTILIDAD LOCALXXXXX: ".$pUtilidadLocal." utilidadIbnorcaXXXXX: ".$utilidadIbnorcaX." utilidadNetaExternoXXX: ".$pUtilidadExterno." utilidadFueraXXXXX: ".$utilidadFueraX;

                 /*AQUI SE MOSTRABAN LOS CALCULOS PARA LOS MENSAJES*/

				?>	
				<input type="hidden" id="cantidad_alibnorca" name="cantidad_alibnorca" readonly value="<?=$alumnosX?>">
				<input type="hidden" id="cantidad_alfuera" name="cantidad_alfuera" readonly value="<?=$alumnosExternoX?>">
				<input type="hidden" id="aprobado" name="aprobado" readonly value="<?=$codEstadoSimulacion?>">
                          <!--<div class="btn-group dropdown">
                              <button type="button" title="Editar Variables de Costo" class="btn btn-sm btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="material-icons">edit</i>
                              </button>
                              <div class="dropdown-menu">
                                <?php
                                  for ($an=$inicioAnio; $an<=$anioGeneral; $an++) {
                                    $tituloItem="Año ".$an;
                                    if(($an==0||$an==1)&&$codAreaX!=39){
                                     $tituloItem="Año 1 (ETAPA ".($an+1).")";
                                   }
                                      ?>
                                       <a href="#" onclick="modificarMontosPeriodo(<?=$an?>)"  class="dropdown-item">
                                           <i class="material-icons">keyboard_arrow_right</i><?=$tituloItem?>
                                       </a> 
                                     <?php
                                  }
                                  ?>
                              </div>
                            </div>-->
                            <?php 
                             $primeraVez="";
                             $funcionBoton="modificarMontosPeriodo(0)";
                             $tituloBoton="Editar Variables de Costo";
                             if(obtenerEntradaSimulacionServicio($codigoSimulacionSuper)==1){
                               $primeraVez="estado";
                               $tituloBoton="Editar Variables de Costo (Primera Vez)";
                             }
                             ?> 
                          <a href="#" title="<?=$tituloBoton?>" onclick="<?=$funcionBoton?>" class="btn btn-sm bg-table-total">
                              <i class="material-icons">edit</i><span class="bg-warning <?=$primeraVez?>"></span>
                          </a>  
                          <a href="#" onclick="listarCostosFijosPeriodo(<?=$anioGeneral?>)" class="btn btn-sm btn-info">
                                           <i class="material-icons">list</i> CF
                          </a>
                          <a href="#" onclick="listarCostosVariblesPeriodo(<?=$anioGeneral?>)" class="btn btn-sm btn-info">
                                           <i class="material-icons">list</i> CV
                          </a> 
           <!--<a href="#" title="Listar Detalle Costo Fijo" onclick="listarCostosFijos()" class="btn btn-sm btn-info"><i class="material-icons">list</i> CF</a>-->
          <br>
          <div class="row">
            <p class="font-weight-bold float-left">PRESUPUESTO POR PERIODO DE CERTIFICACION</p>
           <?php 
           $costoFijoPrincipalPeriodo=0;

            /**************************************************************
             * Permite controlar el limite de gestión para los incrementos
             * en gestiones futuras
             **************************************************************/
            $control_anio = 0;

            $anio = date('Y'); // Año limite es la "FECHA ACTUAL"
           for ($an=$inicioAnio; $an<=$anioGeneral; $an++) { 
            if($codAreaX!=39){
                $tituloItem="Año ".$an."(Seguimiento ".($an-1).")";
                if(($an==0||$an==1)&&$codAreaX!=39){
                  if($an==1){
                    $tituloItem="Año 1 (ETAPA ".($an+1)." / RENOVACIÓN)";
                  }else{
                    $tituloItem="Año 1 (ETAPA ".($an+1).")";
                  }      
                }
            }else{
                $tituloItem="Año ".$an;
            } 
           
            
            $totalIngresoUsd=0;$totalIngreso=0;
            $totalCostoTotalUsd=0;$totalCostoTotal=0;
            $totalUtilidadBrutaUsd=0;$totalUtilidadBruta=0;
            $totalImpuestosUsd=0;$totalImpuestos=0;
            $totalUtilidadNetaUsd=0;$totalUtilidadNeta=0;
                ?>
            <table class="table table-condensed table-bordered">
               <tr>
                <?php 
               if($codAreaX==39){
                  $rospanAnio="4";
                }else{
                  $rospanAnio="4";
                }
                ?>
                 <td rowspan="<?=$rospanAnio?>" width="6%" class="bg-table-primary text-white font-weight-bold"><?=$tituloItem?></td>    <!--ROWSPAN = CANTIDAD DE SERVICIOS + 2 -->
                 <td rowspan="2" width="14%" class="bg-table-primary text-white font-weight-bold"></td>
                 <td rowspan="2" width="5%" class="bg-table-primary text-white font-weight-bold">Dias Servicio</td>
                 <td colspan="2" class="bg-table-primary text-white font-weight-bold">INGRESO</td>
                 <td colspan="2" class="bg-table-primary text-white font-weight-bold">COSTO TOTAL</td>
                 <td colspan="2" class="bg-table-primary text-white font-weight-bold">UTILIDAD BRUTA</td>
                 <td colspan="2" class="bg-table-primary text-white font-weight-bold">IMPUESTOS</td>
                 <td colspan="2" class="bg-table-primary text-white font-weight-bold">UTILIDAD NETA</td>
                 <td rowspan="2"  width="8%" class="bg-table-primary text-white font-weight-bold">% UTILIDAD</td>
               </tr>
               <tr class="bg-table-primary text-white font-weight-bold">
                 <td>USD</td>
                 <td>BOB</td>
                 <td>USD</td>
                 <td>BOB</td>
                 <td>USD</td>
                 <td>BOB</td>
                 <td>USD</td>
                 <td>BOB</td>
                 <td>USD</td>
                 <td>BOB</td>
               </tr>
               <?php 
                $precioLocalXPeriodo=obtenerPrecioServiciosSimulacionPeriodo($codigo,$an);
                $costoVariablePersonalPeriodo=obtenerCostosPersonalSimulacionEditadoPeriodo($codigo,$an);
                $totalVariablePeriodo=obtenerTotalesSimulacionServicioPeriodo($codigo,$an);
                
                if($anioGeneral==0){
                  $anioGeneral=1;
                } 
                // echo "DATA VISUAL RONALD || anioGeneral:".$anioGeneral.'</br>';
                //costos fijos porcentaje configuracion ***************************************************************************************                                
                
                /**********************************************
                 * Obtiene Gestión de Propuesta de Presupuesto
                 **********************************************/
                // ? CAPTURA GESTIÓN
                // $anio = $propuesta_gestion + ($an == 0 ? $an : ($an - 1)); // Captura Gestión (Excepcion: 0 y 1 es AÑO1)
                if(($an == 0 || $an == 1) && !empty($propuesta_gestion)){
                    $anio = $propuesta_gestion;
                }else if($an == 2 && !empty($propuesta_gestion2)){
                    $anio = $propuesta_gestion2;
                }else if($an == 3 && !empty($propuesta_gestion3)){
                    $anio = $propuesta_gestion3;
                }else{
                    $anio = $anio + ($an == 0 ? $an : ($an - 1)); // Captura Gestión (Excepcion: 0 y 1 es AÑO1)
                }

                if($anio > date('Y')){
                    $anio = date('Y'); // Año limite es la "FECHA ACTUAL"
                    $control_anio++;
                }
                // Obtiene Presupuesto
                $precioRegistrado = (((!empty($propuesta_gestion) || !empty($propuesta_gestion2) || !empty($propuesta_gestion3)) 
                                    ? obtenerPresupuestoEjecucionPorAreaAcumulado(0, $codAreaX, $anio, 12, 1)['presupuesto']
                                    : obtenerPrecioRegistradoPropuestaTCPTCS($codigo)) * $porcentaje_ajuste_ing);
                /**********************************************************************************************************/

                $precioRegistradoAux=$precioRegistrado;
                // ? YA NO SE UTILIZA REEMPLAZO
                // if($an>1){
                //     for ($anioAumento=2; $anioAumento <= $an; $anioAumento++) {
                //       $precioRegistradoAux=$precioRegistradoAux+($precioRegistradoAux*($porcentajeFijoX/100));
                //     }
                // }
                /**********************************************************************************************************/
                // ? REEMPLAZADO DE DE PROCESO
                // * Si no se tiene la PROPUESTA GESTIÓN - Mantiene incremento 15% desde el segundo AÑO
                if(empty($propuesta_gestion) || empty($propuesta_gestion2) || empty($propuesta_gestion3)){
                    // Procesando desde el "Segundo Año"
                    if($an > 1){
                        for ($anioAumento=2; $anioAumento <= $an; $anioAumento++) {
                            $precioRegistradoAux=$precioRegistradoAux+($precioRegistradoAux*($porcentajeFijoX/100));
                        }
                    }
                }else{
                // * El incremento solo se aplica cuando la gestión es futura en comparación con la gestión actual
                    // Procesando gestiones futuras
                    if($control_anio > 0){
                        for ($anioAumento = 1; $anioAumento <= $control_anio; $anioAumento++) {
                            $precioRegistradoAux=$precioRegistradoAux+($precioRegistradoAux*($porcentajeFijoX/100));
                        }
                    }
                }
                /**********************************************************************************************************/  
                // echo "DATA VISUAL RONALD || porcentaje fijo:".$porcentajeFijoX.'</br>';
                // echo "DATA VISUAL RONALD || precioRegistradoAux:".$precioRegistradoAux.'</br>';
                $porcentPreciosPeriodo=(float)number_format(($precioLocalXPeriodo*100)/($precioRegistradoAux),2,'.','');

                // ? YA NO SE UTILIZA
                // $costoFijoRegistrado=$totalFijo[0];
                // ? Obtiene COSTO FIJO ORIGINAL DE ACUERDO A LA GESTIÓN
                $costoFijoRegistrado = obtenerTotalesPlantillaServicio($codigoPX,1,$nAuditorias, $anio)[0];

                /**********************************************************************************************************/
                // ? YA NO SE UTILIZA
                // if($an>1){
                //     for ($anioAumento=2; $anioAumento <= $an; $anioAumento++) { 
                //       $sumaCostoFijoRegistrado=$costoFijoRegistrado*($porcentajeFijoX/100);
                //       $costoFijoRegistrado=$costoFijoRegistrado+$sumaCostoFijoRegistrado;
                //     }
                // }
                // * Si no se tiene la PROPUESTA GESTIÓN - Mantiene incremento 15% desde el segundo AÑO
                if(empty($propuesta_gestion) || empty($propuesta_gestion2) || empty($propuesta_gestion3)){
                    // Procesando desde el "Segundo Año"
                    if($an>1){
                        for ($anioAumento=2; $anioAumento <= $an; $anioAumento++) {
                            $costoFijoRegistrado = $costoFijoRegistrado + ($costoFijoRegistrado * ($porcentajeFijoX / 100));
                        }
                    }
                }else{
                // * El incremento solo se aplica cuando la gestión es futura en comparación con la gestión actual
                    // Procesando gestiones futuras
                    if($an>1){
                        for ($anioAumento = 1; $anioAumento <= $control_anio; $anioAumento++) {
                            $costoFijoRegistrado = $costoFijoRegistrado + ($costoFijoRegistrado * ($porcentajeFijoX / 100));
                        }
                    }
                }
                /**********************************************************************************************************/
                // echo "DATA VISUAL RONALD || Total Fijos 'SIN PORCENTAJE FINAL':$costoFijoRegistrado <br>";

                $costoFijoFinal=$costoFijoRegistrado*($porcentPreciosPeriodo/100);
                $costoFijoPrincipalPeriodo+=$costoFijoFinal;  
                //fin datos para costo fijo             ***************************************************************************************
                // echo "DATA VISUAL RONALD || OPERACIÓN COSTO FIJO: $costoFijoFinal+(".$totalVariablePeriodo[2].")+$costoVariablePersonalPeriodo";
                $costoTotalLocalPeriodo=$costoFijoFinal+($totalVariablePeriodo[2])+$costoVariablePersonalPeriodo;

                $costoTotalAuditoriaUsd=$costoTotalLocalPeriodo/$usd;
                $costoTotalAuditoria=$costoTotalLocalPeriodo;

                $precioAuditoriaUsd=$precioLocalXPeriodo/$usd;
                $precioAuditoria=$precioLocalXPeriodo;

                $utilidadAuditoriaUsd=$precioAuditoriaUsd-$costoTotalAuditoriaUsd;
                $utilidadAuditoria=$precioAuditoria-$costoTotalAuditoria;

                $impuestosAuditoriaUsd=(($iva+$it)/100)*$precioAuditoriaUsd;
                $impuestosAuditoria=(($iva+$it)/100)*$precioAuditoria;

                $utilidadNetaAuditoriaUsd=$utilidadAuditoriaUsd-$impuestosAuditoriaUsd;
                $utilidadNetaAuditoria=$utilidadAuditoria-$impuestosAuditoria;

                //suma de totales
                $totalIngresoUsd+=$precioAuditoriaUsd;
                $totalIngreso+=$precioAuditoria;
                $totalCostoTotalUsd+=$costoTotalAuditoriaUsd;
                $totalCostoTotal+=$costoTotalAuditoria;
                $totalUtilidadBrutaUsd+=$utilidadAuditoriaUsd;
                $totalUtilidadBruta+=$utilidadAuditoria;
                $totalImpuestosUsd+=$impuestosAuditoriaUsd;
                $totalImpuestos+=$impuestosAuditoria;
                $totalUtilidadNetaUsd+=$utilidadNetaAuditoriaUsd;
                $totalUtilidadNeta+=$utilidadNetaAuditoria;

                if($totalIngresoUsd==0){
                  $totalIngresoUsd=1;
                }
                ?>
                 <tr>
                 <td class="small text-left">Precio del Servicio</td>
                 
                  <!-- Contador de DIAS, la unidad de medida seleccionada es DIAS para la contabilización de DIAS -->
                  <?php 
                    $sqlCountD="SELECT SUM(sst.cantidad_editado) as cantidad_dias
                    FROM simulaciones_servicios_tiposervicio sst
                    WHERE sst.cod_simulacionservicio='$codigo'
                    AND sst.habilitado!=0 
                    AND sst.cod_anio=$an
                    AND sst.cod_tipounidad = 2";
                    $stmtCountD = $dbh->prepare($sqlCountD);
                    $stmtCountD->execute(); 
                    $cantidadDias = 0;
                    $rowTotal     = $stmtCountD->fetch(PDO::FETCH_ASSOC);
                    $cantidadDias = empty($rowTotal['cantidad_dias']) ? 0 : $rowTotal['cantidad_dias'];
                  ?>
                 <td class="small text-left"><?=$cantidadDias?></td>
                 <td class="small text-right"><?=number_format($precioAuditoriaUsd, 2, ',', '.')?></td>
                 <td class="small text-right"><?=number_format($precioAuditoria, 2, ',', '.')?></td>

                 <td class="small text-right"><?=number_format($costoTotalAuditoriaUsd, 2, ',', '.')?></td>
                 <td class="small text-right"><?=number_format($costoTotalAuditoria, 2, ',', '.')?></td>

                 <td class="small text-right"><?=number_format($utilidadAuditoriaUsd, 2, ',', '.')?></td>
                 <td class="small text-right"><?=number_format($utilidadAuditoria, 2, ',', '.')?></td>
                 <td class="small text-right"><?=number_format($impuestosAuditoriaUsd, 2, ',', '.')?></td>
                 <td class="small text-right"><?=number_format($impuestosAuditoria, 2, ',', '.')?></td>
                 <td class="small text-right"><?=number_format($utilidadNetaAuditoriaUsd, 2, ',', '.')?></td>
                 <td class="small text-right"><?=number_format($utilidadNetaAuditoria, 2, ',', '.')?></td>
                 
               </tr>
               <tr class="bg-plomo">
                 <td class="font-weight-bold small text-left">TOTAL</td>
                 <td class="font-weight-bold small text-left"></td>
                 <td class="font-weight-bold small text-right <?=$estiloUtilidadIbnorca?>"><?=number_format($totalIngresoUsd, 2, ',', '.')?></td>
                 <td class="font-weight-bold small text-right <?=$estiloUtilidadIbnorca?>"><?=number_format($totalIngreso, 2, ',', '.')?></td>
                 <td class="font-weight-bold small text-right <?=$estiloUtilidadIbnorca?>"><?=number_format($totalCostoTotalUsd, 2, ',', '.')?></td>
                 <td class="font-weight-bold small text-right <?=$estiloUtilidadIbnorca?>"><?=number_format($totalCostoTotal, 2, ',', '.')?></td>
                 <td class="font-weight-bold small text-right <?=$estiloUtilidadIbnorca?>"><?=number_format($totalUtilidadBrutaUsd, 2, ',', '.')?></td>
                 <td class="font-weight-bold small text-right <?=$estiloUtilidadIbnorca?>"><?=number_format($totalUtilidadBruta, 2, ',', '.')?></td>
                 <td class="font-weight-bold small text-right <?=$estiloUtilidadIbnorca?>"><?=number_format($totalImpuestosUsd, 2, ',', '.')?></td>
                 <td class="font-weight-bold small text-right <?=$estiloUtilidadIbnorca?>"><?=number_format($totalImpuestos, 2, ',', '.')?></td>
                 <td class="font-weight-bold small text-right <?=$estiloUtilidadIbnorca?>"><?=number_format($totalUtilidadNetaUsd, 2, ',', '.')?></td>
                 <td class="font-weight-bold small text-right <?=$estiloUtilidadIbnorca?>"><?=number_format($totalUtilidadNeta, 2, ',', '.')?></td>
                 <td rowspan="<?=$rospanAnio-2?>" class="font-weight-bold small text-right <?=$estiloUtilidadIbnorca?>"><?=number_format(($totalUtilidadNetaUsd*100)/$totalIngresoUsd, 2, ',', '.')?> %</td>
               </tr>
                
            </table>
                <?php
            }
           $costoTotalLocal=$costoFijoPrincipalPeriodo+(($totalVariable[2]*$alumnosX)+$costoVariablePersonal);
           $utilidadBruta=($precioLocalX)-($costoTotalLocal);
           $utilidadNetaLocal=$utilidadBruta-((($iva+$it)/100)*($precioLocalX))-($precioAfnorX);
           $pUtilidadLocal=($utilidadNetaLocal*100)/($precioLocalX);


           /*NUEVO LUGAR MENSAJES*/
           if($pUtilidadLocal>=$utilidadIbnorcaX&&$pUtilidadExterno>=$utilidadFueraX){
              $estiloUtilidad="bg-success text-white";
              $mensajeText="La simulación SI CUMPLE con la UTILIDAD MINIMA REQUERIDA DEL ".$utilidadReferencial." % ".$ibnorca_title;
              $estiloMensaje="text-success font-weight-bold";
              $codEstadoSimulacion=3;  
           }else{
              if($pUtilidadLocal>=$utilidadIbnorcaX){
                  $estiloUtilidadIbnorca="bg-success text-white";
                  if($ibnorcaC==1){
                   $mensajeText="La simulación SI CUMPLE con la UTILIDAD MINIMA REQUERIDA DEL ".$utilidadReferencial." % ".$ibnorca_title;
                   $estiloMensaje="text-success font-weight-bold";
                   $codEstadoSimulacion=3;
                  }                 
              }else{
                  $estiloUtilidadIbnorca="bg-danger text-white";
                  if($ibnorcaC==1){
                   $mensajeText="La simulación NO CUMPLE con la UTILIDAD MINIMA REQUERIDA DEL ".$utilidadReferencial." % ".$ibnorca_title;
                   $estiloMensaje="text-danger font-weight-bold";
                  }                      
              }
              if($pUtilidadExterno>=$utilidadFueraX){
                  $estiloUtilidadFuera="bg-success text-white";
                  if($ibnorcaC!=1){
                   $mensajeText="La simulación SI CUMPLE con la UTILIDAD MINIMA REQUERIDA DEL ".$utilidadReferencial." % ".$ibnorca_title;
                   $estiloMensaje="text-success font-weight-bold";
                   $codEstadoSimulacion=3;
                  }
              }else{
                  $estiloUtilidadFuera="bg-danger text-white";
                  if($ibnorcaC!=1){
                   $mensajeText="La simulación NO CUMPLE con la UTILIDAD MINIMA REQUERIDA DEL ".$utilidadReferencial." % ".$ibnorca_title;
                   $estiloMensaje="text-danger font-weight-bold";
                  }                      
              }
           }

           ?> 
          </div>
          <br>
				  <div class="d-flex justify-content-center"> 	
					<div class="col-sm-6" hidden>
            <p class="font-weight-bold float-left">RESUMEN DE LA PROPUESTA</p>
            <table class="table table-bordered table-condensed">
              <tbody>
                <tr class="">
                  <td  style="font-size:9px !important;"></td>
                  <td class="bg-table-primary text-white" colspan="2">IMPORTE</td>
                </tr>
                <tr class="">
                  <td  style="font-size:9px !important;"></td>
                  <td class="bg-table-primary text-white">BOB</td>
                  <td class="bg-table-primary text-white">USD</td>
                </tr>
                <tr>
                  <td class="text-left small bg-table-primary text-white">COSTO FIJO TOTAL</td>
                  <td class="text-right font-weight-bold"><?=number_format($costoFijoPrincipalPeriodo, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold"><?=number_format($costoFijoPrincipalPeriodo/$usd, 2, '.', ',')?></td>
                </tr>
                <tr>
                  <td class="text-left small bg-table-primary text-white">COSTO VARIABLE TOTAL + HONORARIOS</td>
                  <td class="text-right font-weight-bold"><?=number_format((($totalVariable[2]*$alumnosX)+$costoVariablePersonal), 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold"><?=number_format((($totalVariable[2]*$alumnosX)+$costoVariablePersonal)/$usd, 2, '.', ',')?></td>
                </tr>
                <!--<tr>
                  <td class="text-left small bg-table-primary text-white">COSTO HONORARIOS PERSONAL</td>
                  <td class="text-right font-weight-bold"><?=number_format($costoVariablePersonal, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold"><?=number_format($costoVariablePersonal/$usd, 2, '.', ',')?></td>
                </tr>-->
                <tr class="bg-warning text-dark">
                  <td class="text-left small">COSTO TOTAL</td>
                  <td class="text-right font-weight-bold"><?=number_format($costoTotalLocal, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold"><?=number_format($costoTotalLocal/$usd, 2, '.', ',')?></td>
                </tr>
                 <?php 
                  
                 ?>
                <tr>
                  <td class="text-left small bg-table-primary text-white">MARGEN DE GANANCIA ESPERADA</td>
                  <td class="text-right font-weight-bold"><?=number_format($utilidadIbnorcaX, 2, '.', ',')?> %</td>
                  <td class="text-right font-weight-bold"><?=number_format($utilidadIbnorcaX, 2, '.', ',')?> %</td>
                </tr>
                <?php
             $precioVentaUnitario=(($costoTotalLocal/$alumnosX)/(1-($utilidadIbnorcaX/100)));
             $precioVentaRecomendado=$precioVentaUnitario/(1-(($iva+$it)/100));
                ?>
                <tr class="bg-warning text-dark">
                  <td class="text-left small">PRECIO DE SERVICIO</td>
                  <td class="text-right font-weight-bold"><?=number_format($precioLocalX, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold"><?=number_format($precioLocalX/$usd, 2, '.', ',')?></td>
                </tr>
              </tbody>
            </table>
           </div>
          <div class="col-sm-8 bg-blanco2">
            <p class="font-weight-bold float-left">DATOS DEL CALCULO</p>
            <img src="../assets/img/f_abajo2.gif" alt="" height="30px" class="float-right">
						<table class="table table-bordered table-condensed">
								<tr class="">
									<td></td>
                  <td class="bg-table-primary2 text-white" colspan="2">EN IBNORCA</td>
                  <td class="bg-table-primary2 text-white"></td>
								</tr>
                <tr class="">
                  <td></td>
                  <td class="bg-table-primary2 text-white">BOB</td>
                  <td class="bg-table-primary2 text-white">USD</td>
                  <td class="bg-table-primary2 text-white"></td>
                </tr>
							<tbody>
                
                <tr>
                  <td class="text-left small bg-table-primary2 text-white">TOTAL INGRESOS</td>
                 <!-- <td class="text-right font-weight-bold"><?=number_format($precioLocalX, 2, '.', ',')?></td>-->
                  <td class="text-right font-weight-bold"><?=number_format($precioLocalX, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold"><?=number_format($precioLocalX/$usd, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold">100 %</td>
                </tr>
                <tr>
                  <td class="text-left small bg-table-primary text-white">COSTO FIJO TOTAL</td>
                  <td class="text-right font-weight-bold"><?=number_format($costoFijoPrincipalPeriodo, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold"><?=number_format($costoFijoPrincipalPeriodo/$usd, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold"><?=number_format((($costoFijoPrincipalPeriodo)*100)/($precioLocalX), 2, '.', ',')?> %</td>
                </tr>
                <tr>
                  <td class="text-left small bg-table-primary text-white">COSTO VARIABLE TOTAL + HONORARIOS</td>
                  <td class="text-right font-weight-bold"><?=number_format((($totalVariable[2]*$alumnosX)+$costoVariablePersonal), 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold"><?=number_format((($totalVariable[2]*$alumnosX)+$costoVariablePersonal)/$usd, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold"><?=number_format(((($totalVariable[2]*$alumnosX)+$costoVariablePersonal)*100)/($precioLocalX), 2, '.', ',')?> %</td>
                </tr>
                <tr>
                  <td class="text-left small bg-table-primary2 text-white">TOTAL COSTOS</td>
                  <td class="text-right font-weight-bold"><?=number_format($costoTotalLocal, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold"><?=number_format($costoTotalLocal/$usd, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold"><?=number_format((($costoTotalLocal)*100)/($precioLocalX), 2, '.', ',')?> %</td>
                </tr>
                <?php 
                  
                ?>
                <tr class="bg-warning text-dark">
                  <td class="text-left small">UTILIDAD BRUTA</td>
                  <td class="text-right font-weight-bold"><?=number_format($utilidadBruta, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold"><?=number_format($utilidadBruta/$usd, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold"><?=number_format(($utilidadBruta/($precioLocalX))*100, 2, '.', ',')?> %</td>
                </tr>
                <tr>
                  <td class="text-left small bg-table-primary2 text-white">PAGO IMPUESTOS ( <?=$iva+$it?> %)</td>
                  <td class="text-right font-weight-bold"><?=number_format((($iva+$it)/100)*($precioLocalX), 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold"><?=number_format(((($iva+$it)/100)*($precioLocalX))/$usd, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold"><?=number_format($iva+$it, 2, '.', ',')?> %</td>
                </tr>
                <tr>
                  <td class="text-left small bg-table-primary2 text-white">PORCENTAJE A AFNOR (<?=$porcentajeAfnor?> %)</td>
                  <td class="text-right font-weight-bold"><?=number_format($precioAfnorX, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold"><?=number_format($precioAfnorX/$usd, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold"><?=number_format($porcentajeAfnor, 2, '.', ',')?> %</td>
                </tr>
                <tr class="<?=$estiloUtilidad?>">
                  <td class="text-left small bg-table-primary2 text-white">UTILIDAD NETA</td>
                  <td class="text-right font-weight-bold <?=$estiloUtilidadIbnorca?>"><?=number_format($utilidadNetaLocal, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold <?=$estiloUtilidadIbnorca?>"><?=number_format($utilidadNetaLocal/$usd, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold <?=$estiloUtilidadIbnorca?>"><?=number_format($pUtilidadLocal, 2, '.', ',')?> %</td>
                </tr>
							</tbody>
						</table>
					  <div class="row div-center">
						   <h5><p class="<?=$estiloMensaje?>"><?=$mensajeText?></p></h5>
					  </div>	
					</div>
				  </div>
        
				  	<div class="card-footer fixed-bottom">
            <?php 
            if(!(isset($_GET['q']))){
            //if(!isset($_GET['edit'])){
              if($pUtilidadLocal>0){
              ?><a onclick="guardarServicioSimulacion()" class="btn btn-warning text-white"><i class="material-icons">send</i> Enviar Propuesta UT <?=number_format($pUtilidadLocal, 2, '.', ',')?> %</a><?php    
              }else{
                ?><a href="#" title="No se puede enviar Propuesta" class="btn btn-danger text-white"><i class="material-icons">warning</i> UTILIDAD NETA <?=number_format($pUtilidadLocal, 2, '.', ',')?> %</a><?php
              }
            //}
             ?>   
            <a href="../<?=$urlList;?>" class="btn btn-danger">Volver</a><?php
            }else{
             //if(!isset($_GET['edit'])){
              if($pUtilidadLocal>0){
              ?><a onclick="guardarServicioSimulacion()" class="btn btn-success text-white"><i class="material-icons">send</i> Enviar Propuesta UT <?=number_format($pUtilidadLocal, 2, '.', ',')?> %</a><?php    
              }else{
                ?><a href="#" title="No se puede enviar Propuesta" class="btn btn-danger text-white"><i class="material-icons">warning</i> UTILIDAD NETA <?=number_format($pUtilidadLocal, 2, '.', ',')?> %</a><?php
              }
            //}
            ?>
            <a href="../<?=$urlList;?>&q=<?=$idServicioX?>&s=<?=$s?>&u=<?=$u?>" class="btn btn-danger">Volver</a><?php
            }
            ?>
             
            </div>
				 </div>
			 </div>
      </div>
    </div>
	</div>
</div>

<!-- MODAL DE MODIFICACIÓN DE GESTIÓN -->
<div class="modal fade modal-primary" id="modal_modificacion_gestion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content card">
            <div class="card-header card-header-primary card-header-text">
                <div class="card-text">
                    <h4 class="card-title">MODIFICACIÓN DE GESTIÓN</h4>
                </div>
                <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
                    <i class="material-icons">close</i>
                </button>
            </div>
            <div class="card-body">
                <!-- AÑO 1 -->
                <div class="row">
                    <label class="col-sm-12 col-form-label text-center"><b>AÑO 1:</b></label>                       
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">   
                        <select class="form-control form-control-sm selectpicker" id="select_gestion1">
                            <option value="">Ninguna</option>
                            <?php
                            $sql="SELECT g.nombre
                                FROM gestiones g
                                WHERE g.cod_estado = 1
                                ORDER BY g.codigo DESC";
                            $stmt = $dbh->prepare($sql);
                            $stmt->execute();
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { 
                                $selected = $propuesta_gestion == $row['nombre'] ? 'selected' : ''; 
                            ?>
                            <option value="<?=$row['nombre']?>" <?=$selected?>><?=$row['nombre']?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <!-- AÑO 2 -->
                <div class="row">
                    <label class="col-sm-12 col-form-label text-center"><b>AÑO 2:</b></label>                       
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">   
                        <select class="form-control form-control-sm selectpicker" id="select_gestion2">
                            <option value="">Ninguna</option>
                            <?php
                            $sql="SELECT g.nombre
                                FROM gestiones g
                                WHERE g.cod_estado = 1
                                ORDER BY g.codigo DESC";
                            $stmt = $dbh->prepare($sql);
                            $stmt->execute();
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                $selected = $propuesta_gestion2 == $row['nombre'] ? 'selected' : ''; 
                            ?>
                            <option value="<?=$row['nombre']?>" <?=$selected?>><?=$row['nombre']?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <!-- AÑO 3 -->
                <div class="row">
                    <label class="col-sm-12 col-form-label text-center"><b>AÑO 3:</b></label>                       
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">   
                        <select class="form-control form-control-sm selectpicker" id="select_gestion3">
                            <option value="">Ninguna</option>
                            <?php
                            $sql="SELECT g.nombre
                                FROM gestiones g
                                WHERE g.cod_estado = 1
                                ORDER BY g.codigo DESC";
                            $stmt = $dbh->prepare($sql);
                            $stmt->execute();
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { 
                                $selected = $propuesta_gestion3 == $row['nombre'] ? 'selected' : ''; 
                            ?>
                            <option value="<?=$row['nombre']?>" <?=$selected?>><?=$row['nombre']?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <!-- Botón de actualizar -->
                <button type="button" class="btn btn-primary" id="btn_actualizar_gestion">Actualizar</button>
            </div>
        </div>
    </div>
</div>


<?php

/** NOTA: No se debe mover la posición de 
 * ModalDetalle y Modal por la preparación de datos 
 */

// Se tiene este modal para ver el listado de
// IAF, Categoria Inocuidad, Org. Certificador
require_once 'modalDetalle.php';
// MODAL DETALLE
require_once 'modal.php';
// MODAL TRIBUTOS
require_once 'modal_atributo.php';

$end_time = microtime(true);
$duration=$end_time-$start_time;
$hours = (int)($duration/60/60);
$minutes = (int)($duration/60)-$hours*60;
$seconds = (int)$duration-$hours*60*60-$minutes*60;

echo $hours.' h, '.$minutes.' m y '.$seconds.' s';

?>

<script>
    // Abre modal de modificación de gestión
    $('#modificar_gestion').on('click', function(){
        $('#modal_modificacion_gestion').modal('show');
    });
    // Modificación de Gestión
    $('#btn_actualizar_gestion').on('click', function(){
        // Realiza una solicitud AJAX con la gestión ingresada
        let gestion1 = $('#select_gestion1').val();
        let gestion2 = $('#select_gestion2').val();
        let gestion3 = $('#select_gestion3').val();

        // Validacion de gestión
        // * Gestión 2
        if(gestion2){
            if(!gestion1){
                Swal.fire({
                    type: 'warning',
                    title: 'Debe completar el año 1',
                });
                return false;
            }
            if (gestion2 < gestion1) {
                Swal.fire({
                    type: 'warning',
                    title: 'El año 2 debe ser mayor o igual al año 1',
                });
                return false;
            }
        }
        // * Gestión 3
        if(gestion3){
            if(!gestion2){
                Swal.fire({
                    type: 'warning',
                    title: 'Debe completar el año 2',
                });
                return false;
            }
            if (gestion3 < gestion2) {
                Swal.fire({
                    type: 'warning',
                    title: 'El año 3 debe ser mayor o igual al año 2',
                });
                return false;
            }
        }

        $('#modal_modificacion_gestion').modal('toggle');
        Swal.fire({
            title: '¿Estás seguro de modificar la gestión?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí',
            cancelButtonText: 'No'
        }).then(function (result) {
            if (result.value) {

                // Actualiza la gestión
                $.ajax({
                url: 'ajax_actualizar_gestion_simulacionServicio.php',
                type: 'POST',
                data: {
                    gestion1: gestion1,
                    gestion2: gestion2,
                    gestion3: gestion3,
                    cod_simulacionservicio: <?=$codigo?>
                },
                success: function (response) {
                    let resp = JSON.parse(response);
                    // Verifica si la actualización fue correcta
                    if (resp.status === true) {
                        // Muestra una alerta de actualización correcta
                        Swal.fire({
                            type: 'success',
                            title: '¡Mensaje!',
                            text: resp.message,
                        }).then(function () {
                            location.reload();
                        });
                    } else {
                        throw new Error(resp.message || 'Error en la actualización');
                    }
                },
                error: function (xhr, status, error) {
                    Swal.fire({
                        type: 'error',
                        title: 'Error',
                        text: 'Error en la solicitud',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
                });
            }
        });
    });
</script>