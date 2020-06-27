<?php
session_start();
set_time_limit(0);
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../styles.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

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
           $anioGeneral=$anioX;
           $nombreSimulacion=$nombreX;
           $porcentajeFijoSim=$porcentajeFijoX;

           $porcentajeAfnor=$porcentajeAfnorX;
           if($afnorX==0){
            $precioAfnorX=0;
            $tituloAfnor="SIN AFNOR";
           }else{
            $iva=obtenerValorConfiguracion(1);
            $it=obtenerValorConfiguracion(2);
            $precioAfnorX=((($iva+$it)/100)*$precioLocalX)*($porcentajeAfnorX/100);
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
      }    
?>
<script>
  var itemAtributos=[];
  var itemAtributosDias=[];
</script>
<?php 
  $stmtAtributos = $dbh->prepare("SELECT * from simulaciones_servicios_atributos where cod_simulacionservicio=$codigo");
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
  $normaAtrib=explode(",", $normaXAtrib);
  if($tipoXAtrib==1){
    $stmtAtributosNorma = $dbh->prepare("SELECT * from simulaciones_servicios_atributosnormas where cod_simulacionservicioatributo=$codigoXAtrib");
    $stmtAtributosNorma->execute();
    $ni=0;$normaFila=[];
    while ($rowAtributoNorma = $stmtAtributosNorma->fetch(PDO::FETCH_ASSOC)) {
     $normaFila[$ni]=$rowAtributoNorma['cod_norma'];
     $existeNorma=-1;
     for ($nr=0; $nr < count($normaAtrib); $nr++) { 
        if($normaAtrib[$nr]==nameNorma($rowAtributoNorma['cod_norma'])){
          $existeNorma=$nr; 
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
    $sqlAuditoresAtrib="SELECT sa.*,s.descripcion FROM simulaciones_servicios_atributosauditores sa join simulaciones_servicios_auditores s on s.codigo=sa.cod_auditor join tipos_auditor t on s.cod_tipoauditor=t.codigo where s.cod_simulacionservicio=$codigoSimulacionSuper and s.cod_anio=$an and sa.cod_simulacionservicioatributo=$codigoXAtrib order by t.nro_orden";
    $stmtAuditoresAtrib=$dbh->prepare($sqlAuditoresAtrib);
    $stmtAuditoresAtrib->execute();
    ?>
    <div class="d-none"><select class="form-control selectpicker form-control-sm" data-style="fondo-boton fondo-boton-active" multiple name="auditores<?=$an?>EEEE<?=$codigoFilaAtrib?>[]" id="auditores<?=$an?>EEEE<?=$codigoFilaAtrib?>"><?php
     while ($rowAuditoresAtrib = $stmtAuditoresAtrib->fetch(PDO::FETCH_ASSOC)) {
      $codigoTipoEE=$rowAuditoresAtrib['cod_auditor'];
      $nombreTipoEE=$rowAuditoresAtrib['descripcion'];
      $habilitadoEE=$rowAuditoresAtrib['estado'];
      $words = explode(" ", $nombreTipoEE);
      $acronym = "";
      foreach ($words as $w) {
       if(!(strtolower($w)=="de"||strtolower($w)=="el"||strtolower($w)=="la"||strtolower($w)=="y"||strtolower($w)=="en")){
          $acronym .= $w[0];
       }        
      }
      $abreviatura = $acronym;
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
    $stmtAtributosDias = $dbh->prepare("SELECT * from simulaciones_servicios_atributosdias where cod_simulacionservicioatributo=$codigoXAtrib");
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
	<div id="" class="container-fluid">
      <div class="row">
        <div class="card col-sm-12">
				<div class="card-header card-header-success card-header-text">
					<div class="card-text">
					  <h4 class="card-title">Detalle de Sitios</h4>
					</div>
          <button type="button" onclick="actualizarSimulacionSitios()" class="btn btn-default btn-sm btn-fab float-right">
             <i class="material-icons" title="Actualizar la Pagina">refresh</i><span id="narch" class="bg-warning"></span>
          </button>
				</div>
				<div class="card-body ">
          <?php 
          while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
               if($codAreaX==39){
                $inicioAnio=1;
                }else{
                 $inicioAnio=0;
                }
            ?>
                  <input class="form-control" type="hidden" name="codigo_area" value="<?=$codAreaX?>" id="codigo_area" readonly/>
                  
                  <input class="form-control" type="hidden" name="anio_simulacion" readonly value="<?=$anioGeneral?>" id="anio_simulacion"/>
                <?php } ?>
          <div id="modalEditPlantilla"></div>
          <div id="sinEdicionModal"></div>
          <div id="productos_div" class="d-none"></div>
          <div id="divResultadoListaAtributos">
            
          </div>
				</div>
			</div>

    </div>
  </div>
</div>   
<script>
listarAtributo();
</script>