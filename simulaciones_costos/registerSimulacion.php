<?php
session_start();

error_reporting(-1);

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
$urlR="";
            if(isset($_GET['admin'])){
              $urlList=$urlList2;
              if(isset($_GET['r'])){
                $urlR="&r=".$_GET['r'];  
              }      
            }
            
$fechaActual=date("Y-m-d");
$dbh = new Conexion();
if(isset($_GET['cod'])){
	$codigo=$_GET['cod'];
}else{
	$codigo=0;
}

$mesesProrrateo=obtenerValorConfiguracion(89);
 //obtener datos fecha de la propuesta
 $fechaSimulacion=obtenerFechaSimulacionCosto($codigo);
 $fechaSim=explode("-", $fechaSimulacion);
 $anioSimulacion=$fechaSim[0];
 $mesSimulacion=$fechaSim[1];
 $stringMeses="";
 if($mesesProrrateo>0){
  $arrayMeses=[];$ejecutadoEnMeses=0;$presupuestoEnMeses=0;$presupuestoEnMeses=100;
  //for ($mm=((int)$mesSimulacion-((int)$mesesProrrateo-1)); $mm <= (int)$mesSimulacion ; $mm++) { 
  //  $arrayMeses[$mm]=abrevMes($mm);
  //  $datosIngresos=ejecutadoPresupuestadoEgresosMes(0,$anioSimulacion,$mm,13,1,"");
  //  $ejecutadoEnMeses+=$datosIngresos[0];
  //  $presupuestoEnMeses+=$datosIngresos[1];
  //}
  //if($presupuestoEnMeses>0){
  //  $porcentPreciosEnMeses=number_format(($ejecutadoEnMeses/$presupuestoEnMeses)*100,2,'.','');
  //}
  $porcentPreciosEnMeses=80;
  $stringMeses=implode("-",$arrayMeses);
 }
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

$stmt1 = $dbh->prepare("SELECT sc.*,es.nombre as estado,pa.venta_local,pa.venta_externo from simulaciones_costos sc join estados_simulaciones es on sc.cod_estadosimulacion=es.codigo join precios_simulacioncosto pa on sc.cod_precioplantilla=pa.codigo where sc.cod_estadoreferencial=1 and sc.codigo='$codigo'");
			$stmt1->execute();
			$stmt1->bindColumn('codigo', $codigoX);
            $stmt1->bindColumn('nombre', $nombreX);
            $stmt1->bindColumn('fecha', $fechaX);
            $stmt1->bindColumn('cod_responsable', $codResponsableX);
            $stmt1->bindColumn('estado', $estadoX);
            $stmt1->bindColumn('cod_plantillacosto', $codigoPlan);
            $stmt1->bindColumn('venta_local', $precioLocalX);
            $stmt1->bindColumn('venta_externo', $precioExternoX);
            $stmt1->bindColumn('cod_precioplantilla', $codPrecioPlan);
            $stmt1->bindColumn('ibnorca', $ibnorcaC);
            $stmt1->bindColumn('cantidad_alumnoslocal', $alumnosX);
            $stmt1->bindColumn('utilidad_minimalocal', $utilidadIbnorcaX);
            $stmt1->bindColumn('cantidad_modulos', $cantidadModuloX);
            $stmt1->bindColumn('monto_norma', $montoNormaX);
            $stmt1->bindColumn('habilitado_norma', $habilitadoNormaX);
            $stmt1->bindColumn('cantidad_cursosmes', $cantidadCursosMesX);
            $stmt1->bindColumn('cod_tipocurso', $codTipoCursoX);
            $stmt1->bindColumn('dias_curso', $diasCursoX);
            $stmt1->bindColumn('fecha_curso', $fechaCursoX);

      while ($row1 = $stmt1->fetch(PDO::FETCH_BOUND)) {
         //plantilla datos      
			      $stmt = $dbh->prepare("SELECT p.*, u.abreviatura as unidad,a.abreviatura as area from plantillas_costo p,unidades_organizacionales u, areas a where p.cod_unidadorganizacional=u.codigo and p.cod_area=a.codigo and p.codigo='$codigoPlan' order by codigo");
			      $stmt->execute();
			      $stmt->bindColumn('codigo', $codigoPX);
            $stmt->bindColumn('nombre', $nombreX);
            $stmt->bindColumn('abreviatura', $abreviaturaX);
            $stmt->bindColumn('cod_unidadorganizacional', $codUnidadX);        
            //$stmt->bindColumn('cantidad_alumnoslocal', $alumnosX);
            $stmt->bindColumn('cantidad_alumnosexterno', $alumnosExternoX);
            $stmt->bindColumn('cod_area', $codAreaX);
            $stmt->bindColumn('area', $areaX);
            $stmt->bindColumn('unidad', $unidadX);
            //$stmt->bindColumn('utilidad_minimalocal', $utilidadIbnorcaX);
            $stmt->bindColumn('utilidad_minimaexterno', $utilidadFueraX);
           
           $nombreSimulacion=$nombreX;
           $mesConf=$cantidadCursosMesX;

           $nombreTipoCurso=nameTipoCurso($codTipoCursoX);
           $codigoPrecioSimulacion=$codPrecioPlan;
           $ingresoAlternativo=obtenerPrecioAlternativoDetalle($codigoPrecioSimulacion);
           $codigoSimulacionSuper=$codigoX;
           $diasCursoXX=$diasCursoX;
           if($diasCursoX==0){
             $diasCursoXX=1; 
           }
           $fechaCurso=strftime('%d/%m/%Y',strtotime($fechaCursoX));
      }
  if($ibnorcaC==1){
  	$checkIbnorca="checked";
  	$simulacionEn="IBNORCA";
  }else{
  	$checkIbnorca="";
  	$simulacionEn="FUERA DE IBNORCA";
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
     <h4 class="text-warning font-weight-bold">Procesando Datos</h4>
     <p class="text-white">Aguard&aacute; un momento por favor</p>  
  </div>
</div>
<div class="content">
	<div id="contListaGrupos" class="container-fluid">
			<input type="hidden" name="cod_simulacion" id="cod_simulacion" value="<?=$codigo?>">
      <input type="hidden" name="cod_precioplantilla" id="cod_precioplantilla" value="<?=$codPrecioPlan?>">
      <input type="hidden" name="cod_ibnorca" id="cod_ibnorca" value="<?=$ibnorcaC?>">
            <div class="row"><div class="card col-sm-5">
				<div class="card-header card-header-success card-header-text">
					<div class="card-text">
					  <h4 class="card-title">Datos de la Propuesta</h4>
					</div>
          <button type="button" onclick="editarDatosSimulacion()" class="btn btn-success btn-sm btn-fab float-right">
             <i class="material-icons" title="Editar Propuesta">edit</i>
          </button>
				</div>
				<div class="card-body ">
					<div class="row">
					<?php
                    $responsable=namePersonal($codResponsableX);
						?>
						<div class="col-sm-6">
							<div class="form-group">
						  		<label class="bmd-label-static">Nombre</label>
					  			<input class="form-control" type="text" name="nombre" readonly value="<?=$nombreX?>" id="nombre"/>
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
						  		<label class="bmd-label-static">Fecha Creación</label>
						  		<input class="form-control" type="text" name="fecha" value="<?=$fechaX?>" id="fecha" readonly/>
                  <input class="form-control" type="hidden" name="fecha_curso" value="<?=$fechaCurso?>" id="fecha_curso" readonly/>
							</div>
						</div>

						<div class="col-sm-6">
				        	<div class="form-group">
						  		<label class="bmd-label-static">Estado</label>
						  		<input class="form-control" type="text" name="estado" value="<?=$estadoX?>" id="estado" readonly/>
							</div>
				    </div>
						<!--<div class="col-sm-4 bg-warning text-dark">
              <label class="">Simulaci&oacute;n para</label>
							<h4><b id="tipo_ibnorca"><?=$simulacionEn?></b></h4>-->
					  			<input class="form-control" type="hidden" readonly name="ibnorca" value="<?=$simulacionEn?>" id="ibnorca"/>
							
						<!--</div>-->
					</div>
				</div>
			</div>
			<div class="card col-sm-7">
				<div class="card-header card-header-info card-header-text">
					<div class="card-text">
					  <h4 class="card-title">Datos de la Propuesta</h4>
					</div>
          <button type="button" onclick="editarDatosPlantillaSec()" class="btn btn-success btn-sm btn-fab float-right">
             <i class="material-icons" title="Editar Plantilla">edit</i>
          </button>
          <button type="button" onclick="actualizarSimulacion()" class="btn btn-default btn-sm btn-fab float-right">
             <i class="material-icons" title="Actualizar la Propuesta">refresh</i><span id="narch" class="bg-warning"></span>
          </button>
				</div>
				<div class="card-body ">
                     <div class="row">
					<?php while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {?>
					<input type="hidden" name="cod_plantilla" id="cod_plantilla" value="<?=$codigoPX?>">

						<div class="col-sm-6">
							<div class="form-group">
						  		<label class="bmd-label-static">Nombre Plantilla</label>
					  			<input class="form-control" type="text" name="nombre_plan" value="<?=$nombreX?>" id="nombre_plan" READONLY />
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
						  		<input class="form-control" type="text" name="area_plan" value="<?=$areaX?>" id="area_plan" readonly/>
							</div>
				    </div>
          </div>
           <div class="row">
             <div class="col-sm-4">
              <div class="form-group">
                  <label class="bmd-label-static">Tipo Curso</label>
                  <input class="form-control" type="text" name="tipo_curso" readonly value="<?=$nombreTipoCurso?>" id="tipo_curso"/>
              </div>
            </div>                
          <div class="col-sm-2">
              <div class="form-group">
                  <label class="bmd-label-static">N. Estudiantes</label>
                  <input class="form-control" type="text" name="alumnos_plan" readonly value="<?=$alumnosX?>" id="alumnos_plan"/>
              </div>
            </div>
            <!--<div class="col-sm-2">
              <div class="form-group">
                  <label class="bmd-label-static">Alumnos Fu</label>-->
                  <input class="form-control" type="hidden" name="alumnos_plan_fuera" readonly value="<?=$alumnosExternoX?>" id="alumnos_plan_fuera"/>
              <!--</div>
            </div>-->
            <div class="col-sm-2">
              <div class="form-group">
                  <label class="bmd-label-static">Ut. M&iacute;n %</label>
                  <input class="form-control" type="text" name="utilidad_minlocal" readonly value="<?=$utilidadIbnorcaX?>" id="utilidad_minlocal"/>
              </div>
            </div>
            <!--<div class="col-sm-2">
              <div class="form-group">
                  <label class="bmd-label-static">UT. Min Fu %</label>-->
                  <input class="form-control" type="hidden" name="utilidad_minext" readonly value="<?=$utilidadFueraX?>" id="utilidad_minext"/>
              <!--</div>
            </div>-->
            <div class="col-sm-2">
              <div class="form-group">
                  <label class="bmd-label-static">Precio</label>
                  <input class="form-control" type="number" name="precio_local" readonly value="<?=$precioLocalX?>" id="precio_local"/>
              </div>
            </div>
            <div class="col-sm-2">
              <div class="form-group">
                  <label class="bmd-label-static">Días Curso</label>
                  <input class="form-control" type="number" name="dias_curso" readonly value="<?=$diasCursoXX?>" id="dias_curso"/>
              </div>
            </div>
            <!--<div class="col-sm-2">
              <div class="form-group">
                  <label class="bmd-label-static">Importe Fu</label>-->
                  <input class="form-control" type="hidden" name="precio_externo" readonly value="<?=$precioExternoX?>" id="precio_externo"/>
              <!--</div>
            </div>-->
				      	<?php } ?>
					</div>
                   <!-- <div class="row">
                    	<div class="col-sm-6">
                    		 <div class="form-group">
                                <select class="selectpicker form-control" onchange="presioneBoton();listarPreciosPlantilla(this.value,'sin',<?=$ibnorcaC?>);" name="plantilla_costo" id="plantilla_costo" data-style="<?=$comboColor;?>"  data-live-search="true" title="-- Elija una plantilla --" data-style="select-with-transition" data-actions-box="true"required>
                                <?php
                                 $stmt = $dbh->prepare("SELECT p.*, u.abreviatura as unidad,a.abreviatura as area from plantillas_costo p,unidades_organizacionales u, areas a where p.cod_unidadorganizacional=u.codigo and p.cod_area=a.codigo and p.cod_estadoreferencial!=2 and p.cod_estadoplantilla=3 order by codigo");
                                 $stmt->execute();
                                  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                  $codigoX=$row['codigo'];
                                  $nombreX=$row['nombre'];
                                  $abrevX=$row['abreviatura'];
                                  $unidadX=$row['unidad'];
                                  $areaX=$row['area'];
                                   ?>
                                  <option value="<?=$codigoX;?>"><?=$nombreX;?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; #<?=$unidadX?> @<?=$areaX?></option> 
                                  <?php
                                    }
                                    ?>
                                </select>
                              </div>
                              <div id="mensaje"></div>
                    	</div>
                    	<div class="col-sm-6" id="lista_precios">
                       </div>
                    </div>-->
                    <!--<div class="row">
                        <div class="col-sm-8" id="check_simular">
                          <div class="row">
                           <label class="col-sm-4 col-form-label">Cantidad Alumnos Autom&aacute;ticos</label>
                           <div class="col-sm-8">	
                    	       <div class="form-group">
      	             	          <div class="form-check">
                                    <label class="form-check-label">
                                      <input class="form-check-input" type="checkbox" id="alumnos_auto" name="alumnos_auto" checked value="1">
                                      <span class="form-check-sign">
                                        <span class="check"></span>
                                      </span>
                                    </label>
                                  </div>
                               </div>
                             </div>  		
                          </div>
                        </div>
                        <div class="col-sm-4 d-none" id="boton_simular">
                    	  <div class="form-group">	
                    		<a href="#" onclick="cargarPlantillaSimulacion(18,<?=$ibnorcaC?>); return false;" class="btn btn-warning text-dark btn-block">Simular Plantilla</a>
                    	  </div>
                        </div>
                    </div>-->
                      
             
				</div>
			</div>
		   </div>
           <div class="row">
             <div class="col-sm-12">
			  <div class="card">
				<div class="card-header card-header-warning card-header-text text-center">
					<div class="card-text">
					  <h4 class="card-title"><b id="titulo_curso"><?=$nombreSimulacion?></b></h4>
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
                 $totalFijoManual=obtenerTotalesPlantilla($codigoPX,3,$mesConf);
                 $totalFijo=obtenerTotalesPlantilla($codigoPX,1,$mesConf); //tipo de costo 1:fijo,2:variable desde la plantilla

                  //total variable desde la plantilla
                 //$totalVariable=obtenerTotalesPlantilla($codigoPX,2,18);
                 //total variable desde simulacion cuentas
                  $totalVariable=obtenerTotalesSimulacion($codigo);
                  //$alumnosX=round((100*($totalFijoPlan*(0.87+($iva/100))))/((100*(($precioLocalX*(1-($it/100)))-($totalVariable[2]*(1+($iva/100)))))-($utilidadIbnorcaX*$precioLocalX)));  
                
               // $alumnosX=($utilidadIbnorcaX+($totalFijoPlan+))
                  //$precioLocalX=$ingresoAlternativo;
                 $precioRegistrado=obtenerPrecioRegistradoPlantillaCosto($codigoPX);
                 if($ingresoAlternativo!=0){
                  $porcentPrecios=(($ingresoAlternativo)*100)/$precioRegistrado; 
                 }else{
                  $porcentPrecios=(($precioLocalX*$alumnosX)*100)/$precioRegistrado;
                 }
                 
                 if($mesesProrrateo>0){
                  $totalFijoPlan=($totalFijo[0]*($porcentPreciosEnMeses/100))*($porcentPrecios/100)+$totalFijoManual[0]; 
                 }else{
                  $totalFijoPlan=$totalFijo[0]*($porcentPrecios/100)+$totalFijoManual[0];
                 }

                 

                 $totalFijoPlanModulos=$totalFijoPlan*$cantidadModuloX;

                  //
                  /*$il=$precioLocalX*$alumnosX; 
                  $uti=$il-((($iva+$it)/100)*$il)-$totalFijoPlan-($totalVariable[2]);
                  $porl=($uti*100)/$il;*/
                  //
                  //$alumnosRecoX=ceil((100*(-$totalFijoPlan-$totalVariable[2]))/(($utilidadIbnorcaX*$precioLocalX)-(100*$precioLocalX)+(($iva+$it)*$precioLocalX)));                    
                  $porcentajeIva=($iva+$it)/100;
                  $porcentajeUtil=$utilidadIbnorcaX/100;
                  //$alumnosRecoX=ceil(($totalFijoPlan+$totalVariable[2])/(($precioLocalX)*(1-$porcentajeIva-$porcentajeUtil)));  
                  
                  $alumnosRecoX=ceil(($precioRegistrado*$totalVariable[2])/(($precioLocalX) * ( ((1-$porcentajeIva-$porcentajeUtil) * ($precioRegistrado)) - $totalFijoPlan)) );  

                  //if($alumnosX)
                 /*if($habilitadoNormaX==1){
                  $totalVariable[2]=$totalVariable[2]+$montoNormaX;
                 } */

                $totalVariable[2]=$totalVariable[2]/$alumnosX;
                $totalVariable[3]=$totalVariable[3]/$alumnosExternoX;
                 //calcular cantidad alumnos si no esta registrado
                $alumnosXAntes=$alumnosX;
               if($alumnosX==0){
                 	$porcentajeFinalLocal=0;$alumnosX=0;$alumnosExternoX=0;$porcentajeFinalExterno=0;
                 	while ($porcentajeFinalLocal < $utilidadIbnorcaX) {
                    $alumnosX++;
                 		include "calculoSimulacion.php";
                       if($ibnorcaC==1){
                             $utilidadReferencial=$utilidadIbnorcaX;
                             $ibnorca_title=""; // EN IBNORCA
                         }else{
                             $utilidadReferencial=$utilidadFueraX;
                             $ibnorca_title=""; //FUERA DE IBNORCA
                         }
                         //cambios para la nueva acortar la simulacion 
                         $utilidadNetaLocal=$ingresoLocal-((($iva+$it)/100)*$ingresoLocal)-$totalFijoPlan-($totalVariable[2]*$alumnosX);
                         $utilidadNetaExterno=$ingresoExterno-((($iva+$it)/100)*$ingresoExterno)-$totalFijo[3]-($totalVariable[3]*$alumnosExternoX);

                         $pUtilidadLocal=($utilidadNetaLocal*100)/$ingresoLocal;
                         $pUtilidadExterno=($utilidadNetaExterno*100)/$ingresoExterno;

                        $porcentajeFinalLocal=$pUtilidadLocal;
                        $porcentajeFinalExterno=$pUtilidadExterno;
                 	}
                  $alumnosRecoX=$alumnosX;
                  $alumnosX=$alumnosXAntes;

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
                 $utilidadNetaLocal=$ingresoLocal-((($iva+$it)/100)*$ingresoLocal)-$totalFijoPlan-($totalVariable[2]*$alumnosX);
                 $utilidadNetaExterno=$ingresoExterno-((($iva+$it)/100)*$ingresoExterno)-$totalFijo[3]-($totalVariable[3]*$alumnosExternoX);

                 $pUtilidadLocal=($utilidadNetaLocal*100)/$ingresoLocal;
                 $pUtilidadExterno=($utilidadNetaExterno*100)/$ingresoExterno;

                 $codEstadoSimulacion=4; 
                 if($pUtilidadLocal>=$utilidadIbnorcaX&&$pUtilidadExterno>=$utilidadFueraX){
                    $estiloUtilidad="bg-success text-white";
                    $mensajeText="La Propuesta SI CUMPLE con la UTILIDAD MINIMA REQUERIDA DEL ".$utilidadReferencial." % ".$ibnorca_title;
                    $estiloMensaje="text-success font-weight-bold";
                    $codEstadoSimulacion=3;  
                 }else{
                    if($pUtilidadLocal>=$utilidadIbnorcaX){
                        $estiloUtilidadIbnorca="bg-success text-white";
                        if($ibnorcaC==1){
                         $mensajeText="La Propuesta SI CUMPLE con la UTILIDAD MINIMA REQUERIDA DEL ".$utilidadReferencial." % ".$ibnorca_title;
                         $estiloMensaje="text-success font-weight-bold";
                         $codEstadoSimulacion=3;
                        }                 
                    }else{
                        $estiloUtilidadIbnorca="bg-danger text-white";
                        if($ibnorcaC==1){
                         $mensajeText="La Propuesta NO CUMPLE con la UTILIDAD MINIMA REQUERIDA DEL ".$utilidadReferencial." % ".$ibnorca_title;
                         $estiloMensaje="text-danger font-weight-bold";
                        }                      
                    }
                    if($pUtilidadExterno>=$utilidadFueraX){
                        $estiloUtilidadFuera="bg-success text-white";
                        if($ibnorcaC!=1){
                         $mensajeText="La Propuesta SI CUMPLE con la UTILIDAD MINIMA REQUERIDA DEL ".$utilidadReferencial." % ".$ibnorca_title;
                         $estiloMensaje="text-success font-weight-bold";
                         $codEstadoSimulacion=3;
                        }
                    }else{
                        $estiloUtilidadFuera="bg-danger text-white";
                        if($ibnorcaC!=1){
                         $mensajeText="La Propuesta NO CUMPLE con la UTILIDAD MINIMA REQUERIDA DEL ".$utilidadReferencial." % ".$ibnorca_title;
                         $estiloMensaje="text-danger font-weight-bold";
                        }                      
                    }
                 }

				?>	
				<input type="hidden" id="cantidad_alibnorca" name="cantidad_alibnorca" readonly value="<?=$alumnosX?>">
				<input type="hidden" id="cantidad_alfuera" name="cantidad_alfuera" readonly value="<?=$alumnosExternoX?>">
				<input type="hidden" id="aprobado" name="aprobado" readonly value="<?=$codEstadoSimulacion?>">
        <input type="hidden" id="porcentaje_fijo" name="porcentaje_fijo" value="<?=$porcentPrecios?>">
        <a href="#" title="Editar Variables de Costo" onclick="modificarMontos()" class="btn btn-sm btn-danger btn-fab"><i class="material-icons">edit</i></a>  
           <a href="#" title="Listar Detalle Costo Fijo" onclick="listarCostosFijos()" class="btn btn-sm btn-info"><i class="material-icons">list</i> CF</a>
           <a href="#" title="Listar Detalle Costo Variable" onclick="listarCostosVaribles()" class="btn btn-sm btn-info"><i class="material-icons">list</i> CV</a>   
          <div class="row"> 	
					<div class="col-sm-3">
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
                  <td class="text-left small bg-table-primary text-white">COSTO VARIABLE UNITARIO</td>
                  <td class="text-right font-weight-bold"><?=number_format($totalVariable[2], 2, '.', ',')?></td>
                </tr>
                <tr class="">
                  <td  style="font-size:9px !important;"></td>
                  <td class="bg-table-primary text-white">CANTIDAD</td>
                </tr>
                <!--<tr class="">
                  <td class="text-left small bg-table-primary text-white">CANTIDAD DE PARTICIPANTES MINIMA "UTILIZADO"</td>
                  <td class="text-right font-weight-bold"><?=$alumnosX?></td>
                </tr>-->
                
                <tr class="">
                  <td class="text-left small bg-table-primary text-white">CANTIDAD DE PARTICIPANTES</td>
                  <td class="text-right font-weight-bold"><?=$alumnosX?></td>
                </tr>
                <tr class="bg-warning text-dark">
                  <td class="text-left small">CANTIDAD DE PARTICIPANTES MINIMA</td>
                  <td class="text-right font-weight-bold"><?=$alumnosRecoX?></td>
                </tr>
                <?php
                $puntoEquilibrio=($totalFijoPlan/($precioLocalX-$totalVariable[2]));
                 ?>
                <tr class="bg-danger text-white">
                  <td class="text-left small">PUNTO DE EQUILIBRIO FINANCIERO</td>
                  <td class="text-right font-weight-bold"><?=number_format($puntoEquilibrio, 2, '.', ',')?></td>
                </tr>
              </tbody>
            </table>
					</div>
					<div class="col-sm-4">
            <p class="font-weight-bold float-left">&nbsp;</p>
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
                <tr class="bg-warning text-dark">
                  <td class="text-left small">COSTO TOTAL</td>
                  <td class="text-right font-weight-bold"><?=number_format($costoTotalLocal, 2, '.', ',')?></td>
                </tr>
                <tr>
                  <td class="text-left small bg-table-primary text-white">MARGEN DE GANANCIA ESPERADA</td>
                  <td class="text-right font-weight-bold"><?=number_format($utilidadIbnorcaX, 2, '.', ',')?> %</td>
                </tr>
                <?php
             //$precioVentaUnitario=(($costoTotalLocal/$alumnosX)/(1-($utilidadIbnorcaX/100)));
             //;
             //$precioVentaRecomendado=$precioVentaUnitario/(1-(($iva+$it)/100));
             $precioVentaUnitario=(-($totalVariable[2]*$alumnosX)*$precioRegistrado)/(((((0/100)-1+(($iva+$it)/100))*$precioRegistrado)+$totalFijoPlan)*$alumnosX);   
             $precioVentaRecomendado=(-($totalVariable[2]*$alumnosX)*$precioRegistrado)/((((($utilidadIbnorcaX/100)-1+(($iva+$it)/100))*$precioRegistrado)+$totalFijoPlan)*$alumnosX);   

                ?>
                <tr>
                  <td class="text-left small bg-table-primary text-white">PRECIO DE VENTA UNITARIO MINIMO</td>
                  <td class="text-right font-weight-bold"><?=number_format($precioVentaUnitario, 2, '.', ',')?></td>
                </tr>
                <tr class="bg-danger text-white">
                  <td class="text-left small">PRECIO DE VENTA CON FACTURA "RECOMENDADO"</td>
                  <td class="text-right font-weight-bold"><?=number_format(ceil($precioVentaRecomendado), 2, '.', ',')?></td>
                </tr>
                <tr class="bg-warning text-dark">
                  <td class="text-left small">PRECIO DE VENTA CON FACTURA "UTILIZADO"</td>
                  <td class="text-right font-weight-bold"><?=number_format($precioLocalX, 2, '.', ',')?></td>
                </tr>
              </tbody>
            </table>
           </div>
          <div class="col-sm-5 bg-blanco2">
            <p class="font-weight-bold float-left">DATOS DEL CALCULO x MODULO</p>
            <img src="../assets/img/f_abajo2.gif" alt="" height="30px" class="float-right">
						<table class="table table-bordered table-condensed">
							<thead>
								<tr class="">
									<td></td>
									<td colspan="2" class="bg-table-primary2 text-white">EN IBNORCA</td>
								</tr>
							</thead>
							<tbody>
                <tr>
                  <td class="text-left small bg-table-primary2 text-white">INGRESOS POR VENTAS</td>
                  <td class="text-right font-weight-bold"><?=number_format($ingresoLocal, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold">100 %</td>
                </tr>
                <tr>
                  <td class="text-left small bg-table-primary2 text-white">TOTAL COSTO FIJO</td>
                  <td class="text-right font-weight-bold"><?=number_format($totalFijoPlan, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold"><?=number_format(($totalFijoPlan/$ingresoLocal)*100, 2, '.', ',')?> %</td>
                </tr>
                <tr>
                  <td class="text-left small bg-table-primary2 text-white">TOTAL COSTO VARIABLE</td>
                  <td class="text-right font-weight-bold"><?=number_format(($totalVariable[2]*$alumnosX), 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold"><?=number_format($pCostoLocal, 2, '.', ',')?> %</td>
                </tr>
                <tr>
                  <td class="text-left small bg-table-primary2 text-white">PAGO IMPUESTOS (IVA  <?=$iva?> % + IT <?=$it?> % = <?=$iva+$it?> %)</td>
                  <td class="text-right font-weight-bold"><?=number_format((($iva+$it)/100)*$ingresoLocal, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold"><?=number_format($iva+$it, 2, '.', ',')?> %</td>
                </tr>
                <tr class="<?=$estiloUtilidad?>">
                  <td class="text-left small bg-table-primary2 text-white">UTILIDAD NETA</td>
                  <td class="text-right font-weight-bold <?=$estiloUtilidadIbnorca?>"><?=number_format($utilidadNetaLocal, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold <?=$estiloUtilidadIbnorca?>"><?=bcdiv($pUtilidadLocal, '1', 2)?> %</td>
                </tr>
							</tbody>
						</table>
					<div class="row div-center">
						<h5><p class="<?=$estiloMensaje?>"><?=$mensajeText?></p></h5>
					</div>	
					</div>
				  </div>
          <div class="col-sm-5 bg-blanco2 div-center">
            <p class="font-weight-bold float-left">DATOS DEL CALCULO PARA <?=$cantidadModuloX?> <?php if($cantidadModuloX>1){ echo "MODULOS";}else{ echo "MODULO";} ?></p>
            <img src="../assets/img/f_abajo2.gif" alt="" height="30px" class="float-right">
            <table class="table table-bordered table-condensed">
              <thead>
                <tr class="">
                  <td></td>
                  <td colspan="2" class="bg-table-primary2 text-white">EN IBNORCA</td>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="text-left small bg-table-primary2 text-white">MODULOS</td>
                  <td class="text-right font-weight-bold"><?=$cantidadModuloX?></td>
                  <td class="text-right font-weight-bold <?=$estiloUtilidadIbnorca?>"></td>
                </tr>
                <tr>
                  <td class="text-left small bg-table-primary2 text-white">INGRESOS POR VENTAS</td>
                  <td class="text-right font-weight-bold"><?=number_format($ingresoLocal*$cantidadModuloX, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold">100 %</td>
                </tr>
                <tr>
                  <td class="text-left small bg-table-primary2 text-white">TOTAL COSTO FIJO</td>
                  <td class="text-right font-weight-bold"><?=number_format($totalFijoPlan*$cantidadModuloX, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold"><?=number_format((($totalFijoPlan*$cantidadModuloX)/($ingresoLocal*$cantidadModuloX))*100, 2, '.', ',')?> %</td>
                </tr>
                <tr>
                  <td class="text-left small bg-table-primary2 text-white">TOTAL COSTO VARIABLE</td>
                  <td class="text-right font-weight-bold"><?=number_format(($totalVariable[2]*$alumnosX)*$cantidadModuloX, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold"><?=number_format($pCostoLocalTotal, 2, '.', ',')?> %</td>
                </tr>
                <tr>
                  <td class="text-left small bg-table-primary2 text-white">PAGO IMPUESTOS (IVA  <?=$iva?> % + IT <?=$it?> % = <?=$iva+$it?> %)</td>
                  <td class="text-right font-weight-bold"><?=number_format(((($iva+$it)/100)*$ingresoLocal*$cantidadModuloX), 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold"><?=number_format($iva+$it, 2, '.', ',')?> %</td>
                </tr>
                <tr class="<?=$estiloUtilidad?>">
                  <td class="text-left small bg-table-primary2 text-white">UTILIDAD NETA</td>
                  <td class="text-right font-weight-bold <?=$estiloUtilidadIbnorca?>"><?=number_format($utilidadNetaLocal*$cantidadModuloX, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold <?=$estiloUtilidadIbnorca?>"><?=number_format($pUtilidadLocal, 2, '.', ',')?> %</td>
                </tr>
              </tbody>
            </table>
          
			 </div>
       <div class="row div-center">
            <div class="card-footer fixed-bottom">
              <?php 
            if(!(isset($_GET['q']))){
              ?><a onclick="guardarSimulacion()" class="btn btn-warning text-white"><i class="material-icons">send</i> Enviar Propuesta UT <?=number_format($pUtilidadLocal, 2, '.', ',')?> %</a><?php    
             ?>   
            <a href="../<?=$urlList;?>" class="btn btn-danger">Volver</a><?php
            }else{
              ?><a onclick="guardarSimulacion()" class="btn btn-success text-white"><i class="material-icons">send</i> Enviar Propuesta UT <?=number_format($pUtilidadLocal, 2, '.', ',')?> %</a><?php    
            ?>
            <!--<a href="../<?=$urlList;?>&q=<?=$idServicioX?>&s=<?=$s?>&u=<?=$u?>" class="btn btn-danger">Volver</a>--><?php
            }
            ?>

            </div>
         </div>
      </div>
    </div>
	</div>
</div>

<?php
require_once 'modal.php';

?>




