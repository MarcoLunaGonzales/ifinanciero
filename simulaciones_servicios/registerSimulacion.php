<?php
session_start();
set_time_limit(0);
error_reporting(-1);
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


$precioLocalX=obtenerPrecioServiciosSimulacion($codigo);
$precioLocalInputX=number_format($precioLocalX, 2, '.', '');

$precioLocalInputXUSD=number_format($precioLocalX/$usd, 2, '.', '');
$alumnosX=obtenerCantidadTotalPersonalSimulacionEditado($codigo);

$costoVariablePersonal=obtenerCostosPersonalSimulacionEditado($codigo);
$ibnorcaC=1;
$utilidadFueraX=1;
$mesConf=obtenerValorConfiguracion(6);
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
            $stmt1->bindColumn('cod_unidadorganizacional', $oficinaGlobalX);
            $stmt1->bindColumn('cod_iaf_primario', $codIAFX);
            $stmt1->bindColumn('cod_iaf_secundario', $codIAFSecX);

            $stmt1->bindColumn('cod_objetoservicio', $cod_objetoservicioX);
            $stmt1->bindColumn('idServicio', $idServicioSimX);
            $stmt1->bindColumn('cod_cliente', $cod_clienteX);
            $stmt1->bindColumn('cod_cliente', $cod_clienteX);
            $stmt1->bindColumn('cod_tipoclientenacionalidad', $cod_tipoclientenacionalidadX);
            $stmt1->bindColumn('cod_tipocliente', $cod_tipoclienteX);

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
           $alcanceSimulacionXX=$alcanceSimulacionX;
           $descripcionServSimulacionXX=$descripcionServSimulacionX;
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
  $normaAtrib=[];
  $normaAtrib=explode(",", $normaXAtrib);

  if($tipoXAtrib==1){
    $stmtAtributosNorma = $dbh->prepare("SELECT * from simulaciones_servicios_atributosnormas where cod_simulacionservicioatributo=$codigoXAtrib");
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
    $sqlAuditoresAtrib="SELECT sa.*,s.descripcion FROM simulaciones_servicios_atributosauditores sa join simulaciones_servicios_auditores s on s.codigo=sa.cod_auditor join tipos_auditor t on s.cod_tipoauditor=t.codigo where s.cod_simulacionservicio=$codigoSimulacionSuper and s.cod_anio=$an and sa.cod_simulacionservicioatributo=$codigoXAtrib and s.cod_tipoauditor!=-100 order by t.nro_orden";
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
						  		<label class="bmd-label-static">Fecha</label>
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
          <button type="button" onclick="editarDatosPlantilla()" class="btn btn-success btn-sm btn-fab float-right">
             <i class="material-icons" title="Editar Plantilla">edit</i>
          </button>
          <button type="button" onclick="actualizarSimulacion()" class="btn btn-default btn-sm btn-fab float-right">
             <i class="material-icons" title="Actualizar la Simulación">refresh</i><span id="narch" class="bg-warning"></span>
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
             
						<div class="col-sm-2">
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
            <div class="col-sm-4">
              <div class="form-group">
                  <label class="bmd-label-static">Precio BOB</label>
                  <input class="form-control" type="text" name="precio_auditoria_ib" readonly value="<?=$precioLocalInputX?>" id="precio_auditoria_ib"/>
              </div>
            </div>
            <div class="col-sm-2">
              <div class="form-group">
                  <label class="bmd-label-static">Precio USD</label>
                  <input class="form-control" type="text" name="precio_auditoria_ibUSD" readonly value="<?=$precioLocalInputXUSD?>" id="precio_auditoria_ibUSD"/>
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
                 $precioRegistrado=obtenerPrecioRegistradoPropuestaTCPTCS($codigoSimulacionSuper);  
                 if($precioRegistrado==0){
                  $precioRegistrado=1;
                 }
                 $totalFijo=obtenerTotalesPlantillaServicio($codigoPX,1,$nAuditorias); //tipo de costo 1:fijo,2:variable desde la plantilla
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
                //costos fijos porcentaje configuracion ***************************************************************************************                                
                $precioRegistradoAux=$precioRegistrado;
                if($an>1){
                    for ($anioAumento=2; $anioAumento <= $an; $anioAumento++) { 
                      $sumaPrecioRegistrado=$precioRegistradoAux*($porcentajeFijoX/100);
                      $precioRegistradoAux=$precioRegistradoAux+$sumaPrecioRegistrado;
                    }
                  }
                 $porcentPreciosPeriodo=(float)number_format(($precioLocalXPeriodo*100)/($precioRegistradoAux),2,'.','');

                 $costoFijoRegistrado=$totalFijo[0];
                if($an>1){
                    for ($anioAumento=2; $anioAumento <= $an; $anioAumento++) { 
                      $sumaCostoFijoRegistrado=$costoFijoRegistrado*($porcentajeFijoX/100);
                      $costoFijoRegistrado=$costoFijoRegistrado+$sumaCostoFijoRegistrado;
                    }
                  }
                 $costoFijoFinal=$costoFijoRegistrado*($porcentPreciosPeriodo/100);
                 $costoFijoPrincipalPeriodo+=$costoFijoFinal;  
                //fin datos para costo fijo             ***************************************************************************************

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
           ?> 
          </div>
          <br>
				  <div class="row"> 	
					<!--<div class="col-sm-3">
            <p class="font-weight-bold float-right">DATOS ADICIONALES PARA EL CALCULO</p>
            <table class="table table-bordered table-condensed">
              <tbody>
								<tr class="">
									<td  style="font-size:9px !important;"></td>
									<td class="bg-table-primary text-white">IMPORTE</td>
								</tr>
								<tr>
									<td class="text-left small bg-table-primary text-white">COSTO FIJO TOTAL</td>
                  <td class="text-right font-weight-bold"><?=number_format($totalFijoPlan, 2, '.', ',')?></td>
								</tr>
                <tr>
                  <td class="text-left small bg-table-primary text-white">COSTO VARIABLE TOTAL</td>
                  <td class="text-right font-weight-bold"><?=number_format(($totalVariable[2]*$alumnosX), 2, '.', ',')?></td>
                </tr>
								<tr>
                  <td class="text-left small bg-table-primary text-white">COSTO HONORARIOS PERSONAL</td>
                  <td class="text-right font-weight-bold"><?=number_format($costoVariablePersonal, 2, '.', ',')?></td>
                </tr>
                <tr class="">
                  <td  style="font-size:9px !important;"></td>
                  <td class="bg-table-primary text-white">CANTIDAD</td>
                </tr>
              
                <tr class="bg-warning text-dark">
                  <td class="text-left small">DIAS Servicio</td>
                  <td class="text-right font-weight-bold"><?=$diasSimulacion?></td>
                </tr>
                <?php
               // $puntoEquilibrio=($totalFijoPlan/($precioLocalX-$totalVariable[2]));
                 ?>
              </tbody>
            </table>
					</div>-->
					<div class="col-sm-6">
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
          <div class="col-sm-6 bg-blanco2">
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

<?php
require_once 'modal.php';
?>
