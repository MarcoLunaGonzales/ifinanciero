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

$fechaActual=date("Y-m-d");
$dbh = new Conexion();


$sumaModulos=0;
$sumaIngresosPropuesta=0;
$sumaCostoFijo=0;
$sumaCostoVariable=0;
$sumaIngresosPropuestaEjecutado=0;
$sumaCostoFijoEjecutado=0;
$sumaCostoVariableEjecutado=0;
$mesesProrrateo=obtenerValorConfiguracion(89);
 
 $stringMeses="";
 if($mesesProrrateo>0){
  $arrayMeses=[];$ejecutadoEnMeses=0;$presupuestoEnMeses=0;$presupuestoEnMeses=100;
  $porcentPreciosEnMeses=obtenerValorConfiguracion(91);
  $stringMeses=implode("-",$arrayMeses);
 }
$idServicioX=0; 
if(isset($_GET['cod'])){
  $tipoCursoArray=$_GET['cod'];
}else{
  $tipoCursoArray=0;
}

$tipoCursoAbrev=abrevCodigoCursoIbnorca($tipoCursoArray);

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
           <div class="row">
             <div class="col-sm-12">
        <div class="card">
        <div class="card-header card-header-warning card-header-text text-center">
          <div class="card-text">
            <h4 class="card-title"><b id="titulo_curso">CURSO: <?=$tipoCursoAbrev?></b></h4>
          </div>
        </div>
  <div class="card-body" id="div_simulacion">
<?php
//INICIO DEL BUCLE
$stmtCursosPropuestas = $dbh->prepare("SELECT codigo from simulaciones_costos where cod_estadosimulacion=3 and IdCurso=$tipoCursoArray");
$stmtCursosPropuestas->execute();
while ($rowCursosPropuestas = $stmtCursosPropuestas->fetch(PDO::FETCH_ASSOC)) { 
 $codigo=$rowCursosPropuestas['codigo'];
//obtener datos fecha de la propuesta
 $fechaSimulacion=obtenerFechaSimulacionCosto($codigo);
 $fechaSim=explode("-", $fechaSimulacion);
 $anioSimulacion=$fechaSim[0];
 $mesSimulacion=$fechaSim[1];
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
            $stmt1->bindColumn('IdModulo', $IdModuloX);

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
           $nombreSimulacion.=" ".obtenerDatosContratoSolicitudCapacitacion($codigoX)[2];
           $mesConf=$cantidadCursosMesX;

           $nombreTipoCurso=nameTipoCurso($codTipoCursoX);
           $codigoPrecioSimulacion=$codPrecioPlan;
           $ingresoAlternativo=obtenerPrecioAlternativoDetalle($codigoPrecioSimulacion);
           $codigoSimulacionSuper=$codigoX;
           $diasCursoXX=$diasCursoX;
           $IdModulo=$IdModuloX;
           $ejecutadoIngresoX=obtenerMontoEjecutadoIngresosSF($IdModulo);
           $alumnosEjecutadoX=15;
           $ejecutadoEgresoX=obtenerMontoEjecutadoEgresoSR($IdModulo);
           if($ejecutadoIngresoX==0){
            $ejecutadoIngresoX=1;
           }
           
           if($diasCursoX==0){
             $diasCursoXX=1; 
           }
           $fechaCurso=strftime('%d/%m/%Y',strtotime($fechaCursoX));
           $codigoPropuesta=$codigoX;
      }
  if($ibnorcaC==1){
    $checkIbnorca="checked";
    $simulacionEn="IBNORCA";
  }else{
    $checkIbnorca="";
    $simulacionEn="FUERA DE IBNORCA";
  }   
$responsable=namePersonal($codResponsableX);
  while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { 
 }  
?>


        
      <?php
        //IVA y IT
        $iva=obtenerValorConfiguracion(1);
        $it=obtenerValorConfiguracion(2);
        $alumnosExternoX=1; 
        //modificar costos por alumnos




        //valores de la simulacion
                 $totalFijoManual=obtenerTotalesPlantilla($codigoPX,3,$mesConf);
                  //total desde la plantilla  
                 $totalFijo=obtenerTotalesPlantilla($codigoPX,1,$mesConf); //tipo de costo 1:fijo,2:variable desde la plantilla
                  //total variable desde la plantilla
                 //$totalVariable=obtenerTotalesPlantilla($codigoPX,2,18);
                 //total variable desde simulacion cuentas
                  $totalVariable=obtenerTotalesSimulacion($codigo);
                  //$alumnosX=round((100*($totalFijoPlan*(0.87+($iva/100))))/((100*(($precioLocalX*(1-($it/100)))-($totalVariable[2]*(1+($iva/100)))))-($utilidadIbnorcaX*$precioLocalX)));  
                
               // $alumnosX=($utilidadIbnorcaX+($totalFijoPlan+))
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
                  $alumnosRecoX=ceil((100*(-$totalFijoPlan-$totalVariable[2]))/(($utilidadIbnorcaX*$precioLocalX)-(100*$precioLocalX)+(($iva+$it)*$precioLocalX)));                    

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

        //datos Adicionales         
        $precioVentaUnitario=(-($totalVariable[2]*$alumnosX)*$precioRegistrado)/(((((0/100)-1+(($iva+$it)/100))*$precioRegistrado)+$totalFijo[0])*$alumnosX);   
        $precioVentaRecomendado=(-($totalVariable[2]*$alumnosX)*$precioRegistrado)/((((($utilidadIbnorcaX/100)-1+(($iva+$it)/100))*$precioRegistrado)+$totalFijo[0])*$alumnosX);   
        $puntoEquilibrio=($totalFijoPlan/($precioLocalX-$totalVariable[2]));

        $utilidadNetaEjecutado=$ejecutadoIngresoX-$totalFijoPlan-$ejecutadoEgresoX-((($iva+$it)/100)*$ejecutadoIngresoX);
        
        $sumaIngresosPropuesta+=$ingresoLocal;
        $sumaIngresosPropuestaEjecutado+=$ejecutadoIngresoX;

        $sumaCostoFijo+=$totalFijoPlan;
        $sumaCostoFijoEjecutado+=$totalFijoPlan;

        $sumaCostoVariable+=$totalVariable[2]*$alumnosX;
        $sumaCostoVariableEjecutado+=$ejecutadoEgresoX;
        $sumaModulos++;
        ?>  
          <div class="row">   
          <div class="col-sm-8 bg-blanco2 div-center">
            <p class="font-weight-bold float-left"><a href="../<?=$urlVer;?>?cod=<?=$codigo;?>&q=<?=$globalUser?>" target="_blank" title="Ir al Detalle de la Propuesta">DATOS DEL CALCULO x MODULO (<?=$nombreSimulacion?>)</a></p>
            <img src="../assets/img/f_abajo2.gif" alt="" height="30px" class="float-right">
            <table class="table table-bordered table-condensed">
              <thead>
                <tr class="">
                  <td></td>
                  <td colspan="2" class=" text-white" style="background:#C70039">PRESUPUESTADO</td>
                  <td colspan="2" class=" text-white" style="background:#C70039">EJECUTADO</td>
                </tr>
              </thead>
              <tbody>
                <!--<tr>
                  <td class="text-left small  text-white" style="background:#C70039">CANTIDAD ESTUDIANTES</td>
                  <td class="text-right font-weight-bold"><?=number_format($alumnosX, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold"></td>
                  <td class="text-right font-weight-bold"><?=number_format($alumnosEjecutadoX, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold"></td>
                </tr>-->
                <tr>
                  <td class="text-left small  text-white" style="background:#C70039">INGRESOS POR VENTAS</td>
                  <td class="text-right font-weight-bold"><?=number_format($ingresoLocal, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold">100 %</td>
                  <td class="text-right font-weight-bold"><?=number_format($ejecutadoIngresoX, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold">100 %</td>
                </tr>
                <tr>
                  <td class="text-left small  text-white" style="background:#C70039">TOTAL COSTO FIJO</td>
                  <td class="text-right font-weight-bold"><?=number_format($totalFijoPlan, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold"><?=number_format(($totalFijoPlan/$ingresoLocal)*100, 2, '.', ',')?> %</td>
                  <td class="text-right font-weight-bold"><?=number_format($totalFijoPlan, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold"><?=number_format(($totalFijoPlan/$ejecutadoIngresoX)*100, 2, '.', ',')?> %</td>
                </tr>
                <tr>
                  <td class="text-left small  text-white" style="background:#C70039">TOTAL COSTO VARIABLE</td>
                  <td class="text-right font-weight-bold"><?=number_format(($totalVariable[2]*$alumnosX), 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold"><?=number_format($pCostoLocal, 2, '.', ',')?> %</td>
                  <td class="text-right font-weight-bold"><?=number_format(($ejecutadoEgresoX), 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold"><?=number_format(($ejecutadoEgresoX/$ejecutadoIngresoX)*100, 2, '.', ',')?> %</td>
                </tr>
                <tr>
                  <td class="text-left small  text-white" style="background:#C70039">PAGO IMPUESTOS (IVA  <?=$iva?> % + IT <?=$it?> % = <?=$iva+$it?> %)</td>
                  <td class="text-right font-weight-bold"><?=number_format((($iva+$it)/100)*$ingresoLocal, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold"><?=number_format($iva+$it, 2, '.', ',')?> %</td>
                  <td class="text-right font-weight-bold"><?=number_format((($iva+$it)/100)*$ejecutadoIngresoX, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold"><?=number_format($iva+$it, 2, '.', ',')?> %</td>
                </tr>
                <tr class="<?=$estiloUtilidad?>">
                  <td class="text-left small  text-white" style="background:#C70039">UTILIDAD NETA</td>
                  <td class="text-right font-weight-bold <?=$estiloUtilidadIbnorca?>"><?=number_format($utilidadNetaLocal, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold <?=$estiloUtilidadIbnorca?>"><?=bcdiv($pUtilidadLocal, '1', 2)?> %</td>
                  <td class="text-right font-weight-bold <?=$estiloUtilidadIbnorca?>"><?=number_format($utilidadNetaEjecutado, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold <?=$estiloUtilidadIbnorca?>"><?=bcdiv(($utilidadNetaEjecutado/$ejecutadoIngresoX)*100, '1', 2)?> %</td>
                </tr>
              </tbody>
            </table>
          <div class="row div-center">
            <!--<h5><p class="<?=$estiloMensaje?>"><?=$mensajeText?></p></h5>-->
          </div>  
          </div>
          </div>
          <?php 
      //FIN DEL BUCLE
      }

         $utilidadNetaLocal=$sumaIngresosPropuesta-$sumaCostoFijo-$sumaCostoVariable-((($iva+$it)/100)*$sumaIngresosPropuesta);
         $utilidadNetaEjecutado=$sumaIngresosPropuestaEjecutado-$sumaCostoFijoEjecutado-$sumaCostoVariableEjecutado-((($iva+$it)/100)*$sumaIngresosPropuestaEjecutado);

         $pUtilidadLocal=($utilidadNetaLocal/($sumaIngresosPropuesta))*100;
         $pUtilidadEjecutado=($utilidadNetaEjecutado/($sumaIngresosPropuestaEjecutado))*100;
         ?>
         <br><br><br><hr><br>
         <center><h4>Curso: <?=$tipoCursoAbrev?></h4></center>
          <div class="col-sm-10 bg-blanco2 div-center">
            <p class="font-weight-bold float-left">DATOS DEL CALCULO PARA <?=$sumaModulos?> <?php if($sumaModulos>1){ echo "MODULOS";}else{ echo "MODULO";} ?></p>
            <img src="../assets/img/f_abajo2.gif" alt="" height="30px" class="float-right">
            <table class="table table-bordered table-condensed">
              <thead>
                <tr class="">
                  <td></td>
                  <td colspan="2" class="bg-table-primary2 text-white">PRESUPUESTADO</td>
                  <td colspan="2" class="bg-table-primary2 text-white">EJECUTADO</td>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="text-left small bg-table-primary2 text-white">MODULOS</td>
                  <td class="text-right font-weight-bold"><?=$sumaModulos?></td>
                  <td class="text-right font-weight-bold <?=$estiloUtilidadIbnorca?>"></td>
                  <td class="text-right font-weight-bold"><?=$sumaModulos?></td>
                  <td class="text-right font-weight-bold <?=$estiloUtilidadIbnorca?>"></td>
                </tr>
                <tr>
                  <td class="text-left small bg-table-primary2 text-white">INGRESOS POR VENTAS</td>
                  <td class="text-right font-weight-bold"><?=number_format($sumaIngresosPropuesta, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold">100 %</td>
                  <td class="text-right font-weight-bold"><?=number_format($sumaIngresosPropuestaEjecutado, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold">100 %</td>
                </tr>
                <tr>
                  <td class="text-left small bg-table-primary2 text-white">TOTAL COSTO FIJO</td>
                  <td class="text-right font-weight-bold"><?=number_format($sumaCostoFijo, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold"><?=number_format(($sumaCostoFijo/($sumaIngresosPropuesta))*100, 2, '.', ',')?> %</td>
                  <td class="text-right font-weight-bold"><?=number_format($sumaCostoFijoEjecutado, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold"><?=number_format(($sumaCostoFijoEjecutado/$sumaIngresosPropuestaEjecutado)*100, 2, '.', ',')?> %</td>
                </tr>
                <tr>
                  <td class="text-left small bg-table-primary2 text-white">TOTAL COSTO VARIABLE</td>
                  <td class="text-right font-weight-bold"><?=number_format($sumaCostoVariable, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold"><?=number_format(($sumaCostoVariable/($sumaIngresosPropuesta))*100, 2, '.', ',')?> %</td>
                  <td class="text-right font-weight-bold"><?=number_format($sumaCostoVariableEjecutado, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold"><?=number_format(($sumaCostoVariableEjecutado/($sumaIngresosPropuestaEjecutado))*100, 2, '.', ',')?> %</td>
                </tr>
                <tr>
                  <td class="text-left small bg-table-primary2 text-white">PAGO IMPUESTOS (IVA  <?=$iva?> % + IT <?=$it?> % = <?=$iva+$it?> %)</td>
                  <td class="text-right font-weight-bold"><?=number_format(((($iva+$it)/100)*$sumaIngresosPropuesta), 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold"><?=number_format($iva+$it, 2, '.', ',')?> %</td>
                  <td class="text-right font-weight-bold"><?=number_format(((($iva+$it)/100)*$sumaIngresosPropuestaEjecutado), 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold"><?=number_format($iva+$it, 2, '.', ',')?> %</td>
                </tr>
                <tr class="<?=$estiloUtilidad?>">
                  <td class="text-left small bg-table-primary2 text-white">UTILIDAD NETA</td>
                  <td class="text-right font-weight-bold <?=$estiloUtilidadIbnorca?>"><?=number_format($utilidadNetaLocal, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold <?=$estiloUtilidadIbnorca?>"><?=number_format($pUtilidadLocal, 2, '.', ',')?> %</td>
                  <td class="text-right font-weight-bold <?=$estiloUtilidadIbnorca?>"><?=number_format($utilidadNetaEjecutado, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold <?=$estiloUtilidadIbnorca?>"><?=number_format($pUtilidadEjecutado, 2, '.', ',')?> %</td>
                </tr>
              </tbody>
            </table>
          
       </div>
      </div><!-- FIN CAR BODY BUBLE-->

   

    </div>
  </div>
</div>




