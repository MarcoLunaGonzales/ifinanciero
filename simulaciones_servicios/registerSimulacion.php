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
            $stmt1->bindColumn('idServicio', $idServicioX);
            $stmt1->bindColumn('anios', $anioX);
            $stmt1->bindColumn('porcentaje_fijo', $porcentajeFijoX);
            $stmt1->bindColumn('afnor', $afnorX);
            $stmt1->bindColumn('porcentaje_afnor', $porcentajeAfnorX);

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
           }else{
            $valorC=18;
           }
      
      }    
?>
<input type="hidden" name="porcentaje_fijo" readonly value="<?=$porcentajeFijoSim?>" id="porcentaje_fijo"/>
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
          <button type="button" onclick="editarDatosSimulacion()" class="btn btn-success btn-sm btn-fab float-right">
             <i class="material-icons" title="Editar Simulación">edit</i>
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
            <div class="col-sm-2">
              <div class="form-group">
                  <label class="bmd-label-static">D&iacute;as Servicio</label>
                  <input class="form-control" type="text" name="dias_plan" readonly value="<?=$diasSimulacion?>" id="dias_plan"/>
                  <input class="form-control" type="hidden" name="productos_sim" readonly value="<?=$productosX?>" id="productos_sim"/>
                  <input class="form-control" type="hidden" name="sitios_sim" readonly value="<?=$sitiosX?>" id="sitios_sim"/>
              </div>
            </div>
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
            <div class="col-sm-2">
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
                 $nAuditorias=obtenerCantidadAuditoriasPlantilla($codigoPX); 
                 $precioRegistrado=obtenerPrecioRegistradoPlantilla($codigoPX);  
                 $totalFijo=obtenerTotalesPlantillaServicio($codigoPX,1,$nAuditorias); //tipo de costo 1:fijo,2:variable desde la plantilla
                 $porcentPrecios=($precioLocalX*100)/$precioRegistrado;
                 $totalFijoPlan=$totalFijo[0]*($porcentPrecios/100);
                 $totalFijoPlan=$totalFijoPlan*$anioGeneral;
                 //total variable desde simulacion cuentas
                  $totalVariable=obtenerTotalesSimulacionServicio($codigo);
                  //
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
                          <div class="btn-group dropdown">
                              <button type="button" title="Editar Variables de Costo" class="btn btn-sm btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="material-icons">edit</i>
                              </button>
                              <div class="dropdown-menu">
                                <?php
                                  for ($an=1; $an<=$anioGeneral; $an++) { 
                                      ?>
                                       <a href="#" onclick="modificarMontosPeriodo(<?=$an?>)" class="dropdown-item">
                                           <i class="material-icons">keyboard_arrow_right</i> A&ntilde;o <?=$an?>
                                       </a> 
                                     <?php
                                  }
                                  ?>
                              </div>
                            </div>
           
                            <div class="btn-group dropdown">
                              <button type="button" title="Listar Detalle Costo Fijo" class="btn btn-sm btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="material-icons">list</i> CF
                              </button>
                              <div class="dropdown-menu">
                                <?php
                                  for ($an=1; $an<=$anioGeneral; $an++) { 
                                      ?>
                                       <a href="#" onclick="listarCostosFijosPeriodo(<?=$an?>)" class="dropdown-item">
                                           <i class="material-icons">keyboard_arrow_right</i> A&ntilde;o <?=$an?>
                                       </a> 
                                     <?php
                                  }
                                  ?>
                              </div>
                            </div>
           <!--<a href="#" title="Listar Detalle Costo Fijo" onclick="listarCostosFijos()" class="btn btn-sm btn-info"><i class="material-icons">list</i> CF</a>-->
                           <div class="btn-group dropdown">
                              <button type="button" title="Listar Detalle Costo Variable" class="btn btn-sm btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="material-icons">list</i> CV
                              </button>
                              <div class="dropdown-menu">
                                <?php
                                  for ($an=1; $an<=$anioGeneral; $an++) { 
                                      ?>
                                       <a href="#" onclick="listarCostosVariblesPeriodo(<?=$an?>)" class="dropdown-item">
                                           <i class="material-icons">keyboard_arrow_right</i> A&ntilde;o <?=$an?>
                                       </a> 
                                     <?php
                                  }
                                  ?>
                              </div>
                            </div>
          <br>
          <div class="row">
            <p class="font-weight-bold float-left">PRESUPUESTO POR PERIODO DE CERTIFICACION</p>
           <?php 
           $costoFijoPrincipalPeriodo=0;
           for ($an=1; $an<=$anioGeneral; $an++) { 
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
                 <td rowspan="<?=$rospanAnio?>" width="6%" class="bg-table-primary text-white font-weight-bold">Año <?=$an?></td>    <!--ROWSPAN = CANTIDAD DE SERVICIOS + 2 -->
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

               

               /*if($codAreaX==39){
                  $codigoAreaServ=108;
                  $costoTotalAuditoriaUsd=0;
                  $costoTotalAuditoria=0;
                }else{
                  $costoTotalAuditoriaUsd=$costoTotalLocal/$usd;
                  $costoTotalAuditoria=$costoTotalLocal;
                }*/
                $precioLocalXPeriodo=obtenerPrecioServiciosSimulacionPeriodo($codigo,$an);
                $costoVariablePersonalPeriodo=obtenerCostosPersonalSimulacionEditadoPeriodo($codigo,$an);
                $totalVariablePeriodo=obtenerTotalesSimulacionServicioPeriodo($codigo,$an);
                
                if($anioGeneral==0){
                  $anioGeneral=1;
                } 
                //costos fijos porcentaje configuracion ***************************************************************************************
                $porCreAn=($porcentajeFijoSim/100)*($an-1);
                $costoFijoInicio=$totalFijoPlan/$anioGeneral;
                $costoFijoFinal=$costoFijoInicio+($costoFijoInicio*$porCreAn);
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
               <?php 
               /*if($codAreaX==39){
                  $codigoAreaServ=108;
                 if($an<=3){
                   $queryPr="SELECT s.*,t.descripcion as nombre_serv,c.descripcion,c.numero_anio FROM simulaciones_servicios_tiposervicio s, cla_servicios t JOIN configuraciones_servicios c on c.cod_claservicio=t.idclaservicio where s.cod_simulacionservicio=$codigoSimulacionSuper and s.cod_claservicio=t.idclaservicio and c.numero_anio=$an order by c.numero_anio";
                 }else{
                   $queryPr="SELECT s.*,t.descripcion as nombre_serv,c.descripcion,c.numero_anio FROM simulaciones_servicios_tiposervicio s, cla_servicios t JOIN configuraciones_servicios c on c.cod_claservicio=t.idclaservicio where s.cod_simulacionservicio=$codigoSimulacionSuper and s.cod_claservicio=t.idclaservicio and c.numero_anio=4 order by c.numero_anio";
                 } 
                
                $stmtCalculo = $dbh->prepare($queryPr);
                $stmtCalculo->execute();
                while ($rowCal = $stmtCalculo->fetch(PDO::FETCH_ASSOC)) {
                  $nombreServicioCal=$rowCal['nombre_serv'];

                  $costoTotalAuditoriaUsd=$costoTotalLocal/$usd;
                  $costoTotalAuditoria=$costoTotalLocal;

                  $precioAuditoriaUsd=$rowCal['monto']/$usd;
                  $precioAuditoria=$rowCal['monto'];

                  $utilidadAuditoriaUsd=$precioAuditoriaUsd-$costoTotalAuditoriaUsd;
                  $utilidadAuditoria=$precioAuditoria-$costoTotalAuditoria;

                  $impuestosAuditoriaUsd=(($iva+$it)/100)*$precioAuditoriaUsd;
                  $impuestosAuditoria=(($iva+$it)/100)*$precioAuditoria;

                  $utilidadNetaAuditoriaUsd=$utilidadAuditoriaUsd-$impuestosAuditoriaUsd;
                  $utilidadNetaAuditoria=$utilidadAuditoria-$impuestosAuditoria;*/

                  //suma de totales
               /* $totalIngresoUsd+=$precioAuditoriaUsd;
                $totalIngreso+=$precioAuditoria;
                $totalCostoTotalUsd+=$costoTotalAuditoriaUsd;
                $totalCostoTotal+=$costoTotalAuditoria;
                $totalUtilidadBrutaUsd+=$utilidadAuditoriaUsd;
                $totalUtilidadBruta+=$utilidadAuditoria;
                $totalImpuestosUsd+=$impuestosAuditoriaUsd;
                $totalImpuestos+=$impuestosAuditoria;
                $totalUtilidadNetaUsd+=$utilidadNetaAuditoriaUsd;
                $totalUtilidadNeta+=$utilidadNetaAuditoria;*/
                 ?>
                <!-- <tr>
                 <td class="small text-left"><?=$nombreServicioCal?></td>
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
               </tr>-->
                 <?php
                // }
                //}
                ?>
               <!--<tr>
                 <td class="small text-left">Otros</td>
                 <td class="small text-right">-</td>
                 <td class="small text-right">-</td>
                 <td class="small text-right">-</td>
                 <td class="small text-right">-</td>
                 <td class="small text-right">-</td>
                 <td class="small text-right">-</td>
                 <td class="small text-right">-</td>
                 <td class="small text-right">-</td>
                 <td class="small text-right">-</td>
                 <td class="small text-right">-</td>
               </tr>-->
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
                  <td class="text-left small bg-table-primary text-white">COSTO VARIABLE TOTAL</td>
                  <td class="text-right font-weight-bold"><?=number_format(($totalVariable[2]*$alumnosX), 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold"><?=number_format(($totalVariable[2]*$alumnosX)/$usd, 2, '.', ',')?></td>
                </tr>
                <tr>
                  <td class="text-left small bg-table-primary text-white">COSTO HONORARIOS PERSONAL</td>
                  <td class="text-right font-weight-bold"><?=number_format($costoVariablePersonal, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold"><?=number_format($costoVariablePersonal/$usd, 2, '.', ',')?></td>
                </tr>
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
            if($idServicioX==0||$idServicioX==""){
             ?><a onclick="guardarServicioSimulacion()" class="btn btn-success text-white"><i class="material-icons">send</i> Enviar Propuesta</a>
            <a href="../<?=$urlList;?>" class="btn btn-danger">Volver</a><?php
            }else{
            ?><a onclick="guardarServicioSimulacion()" class="btn btn-success text-white"><i class="material-icons">send</i> Enviar Propuesta</a>
            <a href="../<?=$urlList;?>&q=<?=$idServicioX?>" class="btn btn-danger">Volver</a><?php
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
