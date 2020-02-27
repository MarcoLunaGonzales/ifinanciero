<?php

set_time_limit(0);
session_start();

//require_once '../layouts/bodylogin.php';

require_once __DIR__.'/../conexion.php';
require_once __DIR__.'/../functionsGeneral.php';
require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsPlanillas.php';
require_once '../layouts/bodylogin2.php';

require_once '../rrhh/configModule.php';

$array_personal;
$dbh = new Conexion();
$codigo_planilla=$_GET['codigo_planilla'];
// echo "planilla:".$codigo_planilla;
//comprobamos ue todos los sueldos del personal estén correctamente distribuidos

$sqlPersonalDistribucion="SELECT cod_personal, SUM(porcentaje) as porcentaje from personal_area_distribucion where cod_estadoreferencial=1 GROUP BY cod_personal
";
$stmtPersonalDistribucion = $dbh->prepare($sqlPersonalDistribucion);
$stmtPersonalDistribucion->execute();
$sw_auxiliar=0;
while ($rowPersonal = $stmtPersonalDistribucion->fetch(PDO::FETCH_ASSOC)) 
{
   if($rowPersonal['porcentaje']!=100){
      $sw_auxiliar++;
      $array_personal[]=$rowPersonal['cod_personal'];
   } 
}
if($sw_auxiliar==0){//sin  distribucion de sueldos pendientes
   //SE DEBE PARAMETRIZAR ESTE CODIGO DE CUENTA PARA LA DEPRECIACION
   $codCuentaDepreciacion=298;
   $codCuentaDepreciacionAF=256;

   //HACEMOS EL COMPROBANTE DE todos las oficnias
   $sqlUnidadX="SELECT codigo,nombre from unidades_organizacionales where cod_estado=1 and centro_costos=1";
   $stmtUnidadX = $dbh->prepare($sqlUnidadX);
   $stmtUnidadX->execute();
   while ($rowUnidadX = $stmtUnidadX->fetch(PDO::FETCH_ASSOC)) {
      $globalUnidadX=$rowUnidadX['codigo'];//sacamos codigo de las unidades
      $nombreUnidadX=$rowUnidadX['nombre']; //nombre de la sunidad
      $tipoComprobante=3;
      $codEmpresa=1;
      $codAnio=$_SESSION["globalNombreGestion"];
      $codMoneda=1;
      $codEstadoComprobante=1;
      $fechaActual=date("Y-m-d H:i:s");

      $mesPlanilla=$_GET["cod_mes"];
      $gestionPlanilla=$_GET["cod_gestion"];
      $anioPlanilla=nameGestion($gestionPlanilla);

      $mesTrabajo=$_SESSION['globalMes'];
      $gestionTrabajo=$_SESSION['globalNombreGestion'];

      $numeroComprobante=obtenerCorrelativoComprobante($tipoComprobante, $globalUnidadX, $gestionTrabajo, $mesTrabajo);

      $glosaCabecera="Sueldos Correspondientes a: ".$mesPlanilla." ".$anioPlanilla." Unidad: ".$nombreUnidadX;
      $codComprobante=obtenerCodigoComprobante();

      $sqlInsertCab="INSERT INTO comprobantes (codigo, cod_empresa, cod_unidadorganizacional, cod_gestion, cod_moneda, cod_estadocomprobante, cod_tipocomprobante, fecha, numero, glosa) values ('$codComprobante','$codEmpresa','$globalUnidadX','$codAnio','$codMoneda','$codEstadoComprobante','$tipoComprobante','$fechaActual','$numeroComprobante','$glosaCabecera')";
      $stmtInsertCab = $dbh->prepare($sqlInsertCab);
      $flagSuccess=$stmtInsertCab->execute();

      //INSERTAMOS LA RELACION ENTRE COMPROBANTES Y DEPRECIACIONES.
      /*
      $sqlInsertCabRelacion="INSERT INTO comprobantes_depreciaciones (cod_comprobante, cod_depreciacion) values ('$codComprobante','$codDepreciacion')";
      $stmtInsertCabRelacion = $dbh->prepare($sqlInsertCabRelacion);
      $flagSuccessRelacion=$stmtInsertCabRelacion->execute();*/

      $totalGanadoDN=totalGanadoDN($gestionPlanilla, $mesPlanilla, $globalUnidadX);//total ganado de toda la unidad
      $totalPersonalSIS=totalPersonalProyectos(1);//sumatoroia de todo el subsidio de proySIS

      $totalAportesSIS=$totalPersonalSIS*0.1671; 

      //echo $totalGanadoDN;
      //echo $totalPersonalSIS;
      $totalDistribuirDN=$totalGanadoDN-$totalPersonalSIS;
      //La contabilizacion de todas las unidades lo hará solo la DN 
      $codigo_x_defecto_dn=obtenerValorConfiguracion(15);

      $ordenDetalle=1;//<--
      if($codigo_x_defecto_dn==$globalUnidadX){
         $sqlDistribucion="SELECT d.cod_unidadorganizacional, d.porcentaje from distribucion_gastosporcentaje d where d.porcentaje>0 order by d.porcentaje desc";
         $stmtDistribucion = $dbh->prepare($sqlDistribucion);
         $stmtDistribucion->execute();
         $centroCostosDN=obtenerValorConfiguracion(29);//DN 
         // $ordenDetalle=1;
         while ($rowDistribucion = $stmtDistribucion->fetch(PDO::FETCH_ASSOC)) {
            $codUODistX=$rowDistribucion['cod_unidadorganizacional'];
            $nombreUODistX=nameUnidad($codUODistX);

            $porcentajeDistX=$rowDistribucion['porcentaje'];

            $montoUODist=$totalDistribuirDN*($porcentajeDistX/100);
            $montoAportesUODist=$montoUODist*0.1671+($totalAportesSIS*($porcentajeDistX/100));

            //echo $codUODistX." ".$montoUODist." ".$montoAportesUODist."<br>";
            $numeroCuenta=cuentaCorrienteUO($codUODistX);

            $glosaDetalle1="Aportes Personal. Planilla de sueldos correspondiente a: ".$nombreUODistX." ".$mesPlanilla."/".$anioPlanilla;
            $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$numeroCuenta','0','$codUODistX','$centroCostosDN','$montoUODist','0','$glosaDetalle1','$ordenDetalle')";
            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
            $flagSuccessDet=$stmtInsertDet->execute();

            $ordenDetalle++;

            $glosaDetalle1="Aportes Personal2. Planilla de sueldos correspondiente a: ".$nombreUODistX." ".$mesPlanilla."/".$gestionPlanilla;
            $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$numeroCuenta','0','$codUODistX','$centroCostosDN','$montoAportesUODist','0','$glosaDetalle1','$ordenDetalle')";
            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
            $flagSuccessDet=$stmtInsertDet->execute();

            $ordenDetalle++;
         }
      }else{
         $centroCostosDN=obtenerValorConfiguracion(29);//DN
         // $ordenDetalle=1;
         $porcentajeDistX=100;//100 % para las unidades diferentes a DN
         $montoUODist=$totalDistribuirDN*($porcentajeDistX/100);
         $montoAportesUODist=$montoUODist*0.1671+($totalAportesSIS*($porcentajeDistX/100));

         //echo $codUODistX." ".$montoUODist." ".$montoAportesUODist."<br>";
         $codUODistX=$globalUnidadX;
         $numeroCuenta=cuentaCorrienteUO($codUODistX);
         $nombreUODistX=nameUnidad($globalUnidadX);

         $glosaDetalle1="Aportes Personal. Planilla de sueldos correspondiente a: ".$nombreUODistX." ".$mesPlanilla."/".$anioPlanilla;
         $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$numeroCuenta','0','$globalUnidadX','$centroCostosDN','$montoUODist','0','$glosaDetalle1','$ordenDetalle')";
         $stmtInsertDet = $dbh->prepare($sqlInsertDet);
         $flagSuccessDet=$stmtInsertDet->execute();

         $ordenDetalle++;

         $glosaDetalle1="Aportes Personal2. Planilla de sueldos correspondiente a: ".$nombreUODistX." ".$mesPlanilla."/".$gestionPlanilla;
         $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$numeroCuenta','0','$codUODistX','$centroCostosDN','$montoAportesUODist','0','$glosaDetalle1','$ordenDetalle')";
         $stmtInsertDet = $dbh->prepare($sqlInsertDet);
         $flagSuccessDet=$stmtInsertDet->execute();
      }

      //DESDE ACA HACEMOS PERSONAL PROYECTOS
      $sqlPersonalProyectos="SELECT p.cod_personal, p.monto_subsidio from personal_proyectosfinanciacionexterna p where p.cod_estado_referencial=1 and p.cod_proyecto<>0";
      $stmtPersonalProyectos = $dbh->prepare($sqlPersonalProyectos);
      $stmtPersonalProyectos->execute();

      $centroCostosSIS=obtenerValorConfiguracion(30);//SIS
      while ($rowPersonalProyectos = $stmtPersonalProyectos->fetch(PDO::FETCH_ASSOC)) {
         $codPersonalX=$rowPersonalProyectos['cod_personal'];
         $nombrePersonalX=obtenerNombrePersonal($codPersonalX);
         $monto_subsidioX=$rowPersonalProyectos['monto_subsidio'];
         
         $numeroCuenta="387";//CUENTAS COBRAR PROYECTOS
         $glosaDetalle2=$nombrePersonalX." Sueldo correspondiente a: ".$mesPlanilla."/".$gestionPlanilla;
         $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$numeroCuenta','0','$codUODistX','$centroCostosSIS','$monto_subsidioX','0','$glosaDetalle2','$ordenDetalle')";
         $stmtInsertDet = $dbh->prepare($sqlInsertDet);
         $flagSuccessDet=$stmtInsertDet->execute();

         $ordenDetalle++;
      }
      //FIN PERSONAL PROYECTOS

      $glosaDetalleGeneral="Sueldo correspondiente a: ".$mesPlanilla."/".$gestionPlanilla;
      //$codUOCentroCosto="829";
      $codUOCentroCosto=$globalUnidadX;
      $codAreaCentroCosto="501";

      //SUELDOS POR PAGAR
      $totalLiquidoPagable=totalLiquidoPagable($gestionPlanilla, $mesPlanilla, $globalUnidadX);
      $numeroCuenta="110";
      $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$numeroCuenta','0','$codUOCentroCosto','$codAreaCentroCosto','0','$totalLiquidoPagable','$glosaDetalleGeneral','$ordenDetalle')";
      $stmtInsertDet = $dbh->prepare($sqlInsertDet);
      $flagSuccessDet=$stmtInsertDet->execute();
      $ordenDetalle++;

      //CAJA PETROLERA
      // $totalCajaSalud=17819.96;
      $totalCajaSalud=obtenerTotalCPS($gestionPlanilla,$mesPlanilla,$globalUnidadX);
      $numeroCuenta="120";//por defecto
      $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$numeroCuenta','0','$codUOCentroCosto','$codAreaCentroCosto','0','$totalCajaSalud','$glosaDetalleGeneral','$ordenDetalle')";
      $stmtInsertDet = $dbh->prepare($sqlInsertDet);
      $flagSuccessDet=$stmtInsertDet->execute();
      $ordenDetalle++;

      //AFP PREVISION BBV
      // $totalAFPPrevision=15514.25;
      $totalAFPPrevision=obtenerTotalAFP_prev1($gestionPlanilla,$mesPlanilla,$globalUnidadX);
      $numeroCuenta="121";//por defecto
      $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$numeroCuenta','0','$codUOCentroCosto','$codAreaCentroCosto','0','$totalAFPPrevision','$glosaDetalleGeneral','$ordenDetalle')";
      $stmtInsertDet = $dbh->prepare($sqlInsertDet);
      $flagSuccessDet=$stmtInsertDet->execute();
      $ordenDetalle++;

      //AFP PREVISION BBV
      // $totalAFPPrevision=298.24;
      $totalAFPPrevision=obtenerTotalAFP_prev2($gestionPlanilla,$mesPlanilla,$globalUnidadX);
      $numeroCuenta="121";//por defecto
      $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$numeroCuenta','0','$codUOCentroCosto','$codAreaCentroCosto','0','$totalAFPPrevision','$glosaDetalleGeneral','$ordenDetalle')";
      $stmtInsertDet = $dbh->prepare($sqlInsertDet);
      $flagSuccessDet=$stmtInsertDet->execute();
      $ordenDetalle++;


      //AFP FUTURO
      // $totalAFPFuturo=15528.13;
      $totalAFPFuturo=obtenerTotalAFP_prev3($gestionPlanilla,$mesPlanilla,$globalUnidadX);
      $numeroCuenta="121";//por defecto
      $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$numeroCuenta','0','$codUOCentroCosto','$codAreaCentroCosto','0','$totalAFPFuturo','$glosaDetalleGeneral','$ordenDetalle')";
      $stmtInsertDet = $dbh->prepare($sqlInsertDet);
      $flagSuccessDet=$stmtInsertDet->execute();
      $ordenDetalle++;

      //AFP PREVISION BBV
      $totalAFPFuturo=0;
      $numeroCuenta="121";
      $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$numeroCuenta','0','$codUOCentroCosto','$codAreaCentroCosto','0','$totalAFPFuturo','$glosaDetalleGeneral','$ordenDetalle')";
      $stmtInsertDet = $dbh->prepare($sqlInsertDet);
      $flagSuccessDet=$stmtInsertDet->execute();
      $ordenDetalle++;

      //PROVIVIENDA
      // $totalProVivienda=1781.20;
      $totalProVivienda=obtenerTotalprovivienda($gestionPlanilla,$mesPlanilla,$globalUnidadX);
      $numeroCuenta="121";
      $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$numeroCuenta','0','$codUOCentroCosto','$codAreaCentroCosto','0','$totalProVivienda','$glosaDetalleGeneral','$ordenDetalle')";
      $stmtInsertDet = $dbh->prepare($sqlInsertDet);
      $flagSuccessDet=$stmtInsertDet->execute();
      $ordenDetalle++;

      //PROVIVIENDA
      // $totalProVivienda=1782.79;
      $totalProVivienda=obtenerTotalprovivienda2($gestionPlanilla,$mesPlanilla,$globalUnidadX);
      $numeroCuenta="121";
      $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$numeroCuenta','0','$codUOCentroCosto','$codAreaCentroCosto','0','$totalProVivienda','$glosaDetalleGeneral','$ordenDetalle')";
      $stmtInsertDet = $dbh->prepare($sqlInsertDet);
      $flagSuccessDet=$stmtInsertDet->execute();
      $ordenDetalle++;


      //RC IVA
      // $totalRCIVA=108.03;
      $totalRCIVA=obtenerTotalOtrosdescuentos($gestionPlanilla,$mesPlanilla,$globalUnidadX);
      $numeroCuenta="131";
      $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$numeroCuenta','0','$codUOCentroCosto','$codAreaCentroCosto','0','$totalRCIVA','$glosaDetalleGeneral','$ordenDetalle')";
      $stmtInsertDet = $dbh->prepare($sqlInsertDet);
      $flagSuccessDet=$stmtInsertDet->execute();
      $ordenDetalle++;
      //indicamos que ya se realizo el comprbante      
      $stmtUdatePlanilla = $dbh->prepare("UPDATE planillas set comprobante=1 where codigo=$codigo_planilla");
      $stmtUdatePlanilla->execute();

   }?>
   <script>
      $(document).ready(function()
      {           
         $("#mostrarmodal1").modal("show");         
      });
   </script>
<?php }else{ 
   ?>
   <script>
      $(document).ready(function()
      {           
         $("#mostrarmodal2").modal("show");         
      });
   </script>
<?php }
?>

<!-- modal  -->
<div class="modal fade" id="mostrarmodal1" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="myModalLabel" aria-labelledby="basicModal" >
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">        
        <h4 class="modal-title" id="myModalLabel" align="left"><b>El Proceso ha finalizado.</b></h4>
      </div>
      <div class="modal-body" align="left">
         Por favor seleccione una opción.
      </div>       
      <div class="modal-footer">          
        <a href="<?=$urlComprobantesLista2;?>" type="button" class="btn btn-success">Ir a Comprobantes</a>
        <a href="<?=$urlPlanillasSueldoList2;?>" type="button" class="btn btn-danger">Ir a Planillas</a>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="mostrarmodal2" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="myModalLabel" aria-labelledby="basicModal" >
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">        
        <h4 class="modal-title" id="myModalLabel" align="left"><b>No Se Pudo Completar El Proceso...</b></h4>
      </div>
      <div class="modal-body" align="left">
         Por favor verifique que toda la distribución de sueldos esté correctamente distribuidos.<br><br>
         <b>Error en personal:</b> <br>
         <?php
         // $longitud_array=count($array_personal);
         foreach ($array_personal as $cod_personal_x) {
            $nombre_personal_x=obtenerNombrePersonal($cod_personal_x);
            echo "<b>- Nombre: </b>".$nombre_personal_x."(Cod:".$cod_personal_x.").<br>";
         }

         ?>
      </div>       
      <div class="modal-footer">                  
        <a href="<?=$urlPersonalLista2;?>" type="button" class="btn btn-success">Ir a Personal</a>
        <a href="<?=$urlPlanillasSueldoList2;?>" type="button" class="btn btn-danger">Ir a Planillas</a>
      </div>
    </div>
  </div>
</div>


