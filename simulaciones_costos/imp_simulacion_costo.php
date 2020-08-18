<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../assets/libraries/CifrasEnLetras.php';

session_start();

$codigo=$_GET['cod'];
$dbh = new Conexion();
$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();
?>
<html><head>
    <link href="../assets/libraries/plantillaPDFSolicitudesRecursos.css" rel="stylesheet" />
   </head><body>
<?php


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
<?php
                    $responsable=namePersonal($codResponsableX);
            ?>
<div class="content">
  <div id="contListaGrupos" class="container-fluid">
    <div class="bg-celeste">
          <div class="card-text">
            <center><h4 class="card-title">Datos de la Propuesta</h4></center>
          </div>
          <img class="" src="../assets/img/logo_ibnorca_origen_3.jpg" width="70" height="70" style="position:fixed;">
          <img class="" src="../assets/img/logo_ibnorca_origen_3.jpg" width="70" height="70" style="position:fixed;right:0px;">
        </div>

    <table class="table" border="0">
        <tr>
            <td>Nombre</td>
            <td><?=$nombreX?></td>  
            <td>Responsable</td>
            <td><?=$responsable?></td>  
        </tr>
        <tr>
            <td>Fecha Creación</td>
            <td><?=$fechaX?></td>  
            <td>Estado</td>
            <td><?=$estadoX?></td>  
        </tr>
        <?php while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {?>
        <tr>
            <td>Area</td>
            <td><?=$areaX?></td>  
            <td>Tipo Curso</td>
            <td><?=$nombreTipoCurso?></td>  
        </tr>
        <tr>
            <td>N. Estudiantes</td>
            <td><?=$alumnosX?></td>  
            <td>Ut. M&iacute;n %</td>
            <td><?=$utilidadIbnorcaX?></td>  
        </tr>
        <tr>
            <td>Precio</td>
            <td><?=$precioLocalX?></td>  
            <td>Días Curso</td>
            <td><?=$diasCursoXX?></td>  
        </tr>
      <?php } ?>
    </table>

           <div class="row">
             <div class="col-sm-12">
        <div class="card">
        <div class="card-header card-header-warning card-header-text text-center">
          <div class="card-text">
            <h4 class="card-title"><b id="titulo_curso">PROPUESTA "<?=$nombreSimulacion?>"</b></h4>
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
                 $totalFijo=obtenerTotalesPlantilla($codigoPX,1,$mesConf); //tipo de costo 1:fijo,2:variable desde la plantilla
                  //total variable desde la plantilla
                 //$totalVariable=obtenerTotalesPlantilla($codigoPX,2,18);
                 //total variable desde simulacion cuentas
                  $totalVariable=obtenerTotalesSimulacion($codigo);
                  //$alumnosX=round((100*($totalFijoPlan*(0.87+($iva/100))))/((100*(($precioLocalX*(1-($it/100)))-($totalVariable[2]*(1+($iva/100)))))-($utilidadIbnorcaX*$precioLocalX)));  
                
               // $alumnosX=($utilidadIbnorcaX+($totalFijoPlan+))
                 $precioRegistrado=obtenerPrecioRegistradoPlantillaCosto($codigoPX);
                 $porcentPrecios=(($precioLocalX*$alumnosX)*100)/$precioRegistrado;
                 $totalFijoPlan=$totalFijo[0]*($porcentPrecios/100);
                 $totalFijoPlanModulos=$totalFijoPlan*$cantidadModuloX;

                  //
                  /*$il=$precioLocalX*$alumnosX; 
                  $uti=$il-((($iva+$it)/100)*$il)-$totalFijoPlan-($totalVariable[2]);
                  $porl=($uti*100)/$il;*/
                  //
                  $alumnosRecoX=ceil((100*(-$totalFijoPlan-$totalVariable[2]))/(($utilidadIbnorcaX*$precioLocalX)-(100*$precioLocalX)+(($iva+$it)*$precioLocalX)));                    
                  //if($alumnosX)
                 /*if($habilitadoNormaX==1){
                  $totalVariable[2]=$totalVariable[2]+$montoNormaX;
                 } */

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
                    $estiloUtilidadIbnorca="bg-success text-white";
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

          <div class="row">   
          <div class="col-sm-3">
            <p class="font-weight-bold float-right">DATOS ADICIONALES PARA EL CALCULO</p>
            <table class="table table-bordered " style="font-size:11px;">
                <tr class="bg-celeste">
                  <td  style="font-size:9px !important;"></td>
                  <td class="bg-table-primary text-white">IMPORTE</td>
                </tr>
                <tr>
                  <td class="text-left bg-table-primary text-white">COSTO FIJO TOTAL</td>
                  <td class="text-right font-weight-bold"><?=number_format($totalFijoPlan, 2, '.', ',')?></td>
                </tr>
                <tr>
                  <td class="text-left bg-table-primary text-white">COSTO VARIABLE UNITARIO</td>
                  <td class="text-right font-weight-bold"><?=number_format($totalVariable[2], 2, '.', ',')?></td>
                </tr>
                <tr class="bg-celeste">
                  <td  style="font-size:9px !important;"></td>
                  <td class="bg-table-primary text-white">CANTIDAD</td>
                </tr>
                
                <tr class="bg-celeste">
                  <td class="text-left bg-table-primary text-white">CANTIDAD DE PARTICIPANTES</td>
                  <td class="text-right font-weight-bold"><?=$alumnosX?></td>
                </tr>
                <tr class="bg-warning text-dark">
                  <td class="text-left">CANTIDAD DE PARTICIPANTES MINIMA</td>
                  <td class="text-right font-weight-bold"><?=$alumnosRecoX?></td>
                </tr>
                <?php
                $puntoEquilibrio=($totalFijoPlan/($precioLocalX-$totalVariable[2]));
                 ?>
                <tr class="bg-danger text-white">
                  <td class="text-left">PUNTO DE EQUILIBRIO FINANCIERO</td>
                  <td class="text-right font-weight-bold"><?=number_format($puntoEquilibrio, 2, '.', ',')?></td>
                </tr>
            </table>
          </div>
          <div class="col-sm-4">
            <p class="font-weight-bold float-left">&nbsp;</p>
            <table class="table table-bordered " style="font-size:11px;">
                <tr class="bg-celeste">
                  <td  style="font-size:9px !important;"></td>
                  <td class="bg-table-primary text-white">IMPORTE</td>
                </tr>
                <tr>
                  <td class="text-left bg-table-primary text-white">COSTO FIJO TOTAL</td>
                  <td class="text-right font-weight-bold"><?=number_format($totalFijoPlan, 2, '.', ',')?></td>
                </tr>
                <tr>
                  <td class="text-left bg-table-primary text-white">COSTO VARIABLE TOTAL</td>
                  <td class="text-right font-weight-bold"><?=number_format(($totalVariable[2]*$alumnosX), 2, '.', ',')?></td>
                </tr>
                <tr class="bg-warning text-dark">
                  <td class="text-left">COSTO TOTAL</td>
                  <td class="text-right font-weight-bold"><?=number_format($costoTotalLocal, 2, '.', ',')?></td>
                </tr>
                <tr>
                  <td class="text-left bg-table-primary text-white">MARGEN DE GANANCIA ESPERADA</td>
                  <td class="text-right font-weight-bold"><?=number_format($utilidadIbnorcaX, 2, '.', ',')?> %</td>
                </tr>
                <?php
             //$precioVentaUnitario=(($costoTotalLocal/$alumnosX)/(1-($utilidadIbnorcaX/100)));
             //;
             //$precioVentaRecomendado=$precioVentaUnitario/(1-(($iva+$it)/100));
             $precioVentaUnitario=(-($totalVariable[2]*$alumnosX)*$precioRegistrado)/(((((0/100)-1+(($iva+$it)/100))*$precioRegistrado)+$totalFijo[0])*$alumnosX);   
             $precioVentaRecomendado=(-($totalVariable[2]*$alumnosX)*$precioRegistrado)/((((($utilidadIbnorcaX/100)-1+(($iva+$it)/100))*$precioRegistrado)+$totalFijo[0])*$alumnosX);   

                ?>
                <tr>
                  <td class="text-left bg-table-primary text-white">PRECIO DE VENTA UNITARIO MINIMO</td>
                  <td class="text-right font-weight-bold"><?=number_format($precioVentaUnitario, 2, '.', ',')?></td>
                </tr>
                <tr class="bg-danger text-white">
                  <td class="text-left">PRECIO DE VENTA CON FACTURA "RECOMENDADO"</td>
                  <td class="text-right font-weight-bold"><?=number_format(ceil($precioVentaRecomendado), 2, '.', ',')?></td>
                </tr>
                <tr class="bg-warning text-dark">
                  <td class="text-left">PRECIO DE VENTA CON FACTURA "UTILIZADO"</td>
                  <td class="text-right font-weight-bold"><?=number_format($precioLocalX, 2, '.', ',')?></td>
                </tr>
            </table>
           </div>
           <br><br><br><br><br><br><br>
          <div class="col-sm-5 bg-blanco2">
            <p class="font-weight-bold float-left">DATOS DEL CALCULO x MODULO</p>
            <table class="table table-bordered " style="font-size:11px;">
                <tr class="bg-celeste">
                  <td colspan="3" class="bg-table-primary2 text-white">EN IBNORCA</td>
                </tr>
                <tr>
                  <td class="text-left bg-table-primary2 text-white">INGRESOS POR VENTAS</td>
                  <td class="text-right font-weight-bold"><?=number_format($ingresoLocal, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold">100 %</td>
                </tr>
                <tr>
                  <td class="text-left bg-table-primary2 text-white">TOTAL COSTO FIJO</td>
                  <td class="text-right font-weight-bold"><?=number_format($totalFijoPlan, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold"><?=number_format(($totalFijoPlan/$ingresoLocal)*100, 2, '.', ',')?> %</td>
                </tr>
                <tr>
                  <td class="text-left bg-table-primary2 text-white">TOTAL COSTO VARIABLE</td>
                  <td class="text-right font-weight-bold"><?=number_format(($totalVariable[2]*$alumnosX), 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold"><?=number_format($pCostoLocal, 2, '.', ',')?> %</td>
                </tr>
                <tr>
                  <td class="text-left bg-table-primary2 text-white">PAGO IMPUESTOS (IVA  <?=$iva?> % + IT <?=$it?> % = <?=$iva+$it?> %)</td>
                  <td class="text-right font-weight-bold"><?=number_format((($iva+$it)/100)*$ingresoLocal, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold"><?=number_format($iva+$it, 2, '.', ',')?> %</td>
                </tr>
                <tr class="">
                  <td class="text-left bg-table-primary2 text-white">UTILIDAD NETA</td>
                  <td class="text-right font-weight-bold <?=$estiloUtilidadIbnorca?>"><?=number_format($utilidadNetaLocal, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold <?=$estiloUtilidadIbnorca?>"><?=bcdiv($pUtilidadLocal, '1', 2)?> %</td>
                </tr>
            </table>
          <div class="row div-center">
            <h5><p class="<?=$estiloMensaje?>"><?=$mensajeText?></p></h5>
          </div>  
          </div>
          </div>
          <div class="col-sm-5 bg-blanco2 div-center">
            <p class="font-weight-bold float-left">DATOS DEL CALCULO PARA <?=$cantidadModuloX?> <?php if($cantidadModuloX>1){ echo "MODULOS";}else{ echo "MODULO";} ?></p>
            <table class="table table-bordered " style="font-size:11px;">
                <tr class="bg-celeste">
                  <td colspan="3" class="bg-table-primary2 text-white">EN IBNORCA</td>
                </tr>
                <tr>
                  <td class="text-left bg-table-primary2 text-white">MODULOS</td>
                  <td class="text-right font-weight-bold"><?=$cantidadModuloX?></td>
                  <td class="text-right font-weight-bold <?=$estiloUtilidadIbnorca?>"></td>
                </tr>
                <tr>
                  <td class="text-left bg-table-primary2 text-white">INGRESOS POR VENTAS</td>
                  <td class="text-right font-weight-bold"><?=number_format($ingresoLocal*$cantidadModuloX, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold">100 %</td>
                </tr>
                <tr>
                  <td class="text-left bg-table-primary2 text-white">TOTAL COSTO FIJO</td>
                  <td class="text-right font-weight-bold"><?=number_format($totalFijoPlan*$cantidadModuloX, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold"><?=number_format((($totalFijoPlan*$cantidadModuloX)/($ingresoLocal*$cantidadModuloX))*100, 2, '.', ',')?> %</td>
                </tr>
                <tr>
                  <td class="text-left bg-table-primary2 text-white">TOTAL COSTO VARIABLE</td>
                  <td class="text-right font-weight-bold"><?=number_format(($totalVariable[2]*$alumnosX)*$cantidadModuloX, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold"><?=number_format($pCostoLocalTotal, 2, '.', ',')?> %</td>
                </tr>
                <tr>
                  <td class="text-left bg-table-primary2 text-white">PAGO IMPUESTOS (IVA  <?=$iva?> % + IT <?=$it?> % = <?=$iva+$it?> %)</td>
                  <td class="text-right font-weight-bold"><?=number_format(((($iva+$it)/100)*$ingresoLocal*$cantidadModuloX), 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold"><?=number_format($iva+$it, 2, '.', ',')?> %</td>
                </tr>
                <tr class="">
                  <td class="text-left bg-table-primary2 text-white">UTILIDAD NETA</td>
                  <td class="text-right font-weight-bold <?=$estiloUtilidadIbnorca?>"><?=number_format($utilidadNetaLocal*$cantidadModuloX, 2, '.', ',')?></td>
                  <td class="text-right font-weight-bold <?=$estiloUtilidadIbnorca?>"><?=number_format($pUtilidadLocal, 2, '.', ',')?> %</td>
                </tr>
            </table>
          
       </div>
        <?php
$j=1;
   $stmtUpdate = $dbh->prepare("SELECT distinct c.cod_partidapresupuestaria as codPartida, p.nombre from cuentas_simulacion c,partidas_presupuestarias p where p.codigo=c.cod_partidapresupuestaria and c.cod_simulacioncostos=$codigo");
   $stmtUpdate->execute();
    while ($rowUpdate = $stmtUpdate->fetch(PDO::FETCH_ASSOC)) {
        $codigoPartida=$rowUpdate['codPartida'];
        $nombrePartida=$rowUpdate['nombre'];

 $montoTotal=obtenerMontoPlantillaDetalle($codigoPX,$codigoPartida,$ibnorcaC);
 $montoTotal=number_format($montoTotal, 2, '.', '');
 $montoEditado=obtenerMontoSimulacionCuenta($codigo,$codigoPartida,$ibnorcaC);
 $montoEditado=number_format($montoEditado, 2, '.', '');


 $query="SELECT p.nombre,p.numero,c.* FROM cuentas_simulacion c, plan_cuentas p where c.cod_plancuenta=p.codigo and c.cod_simulacioncostos=$codigo and c.cod_partidapresupuestaria=$codigoPartida order by codigo";
    $stmt = $dbh->prepare($query);
    $stmt->execute();
    $i=1;
    ?>
    
    <h4 class="font-weight-bold"><center>PARTIDA: <?=$nombrePartida?></center>
    </h4>
   <table class="table  table-bordered" style="font-size:11px;">
         <tr class="text-white bg-naranja">
        <td width="25%">CUENTA</td>
        <td width="35%">DETALLE</td>
        <td width="8%">CANTIDAD</td>
        <td>MONTO</td>
        <td>TOTAL</td>
        </tr>
    <?php
    $totalMontoDetalle=0;$totalMontoDetalleAl=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $codX=$row['codigo'];
    $nomX=$row['nombre'];
    $numX=$row['numero'];
    $detallesPlantilla=obtenerDetalleSimulacionCostosPartida($codigo,$codigoPartida);
     while ($rowDetalles = $detallesPlantilla->fetch(PDO::FETCH_ASSOC)) {
      $bandera=$rowDetalles['habilitado'];
        if($rowDetalles['cod_cuenta']==$row['cod_plancuenta']){
          $codigoCuenta=$rowDetalles['cod_cuenta'];
          $codigoDetalle=$rowDetalles['codigo'];
          $cantidadItem=$rowDetalles['cantidad'];
          $codTipoX=$rowDetalles['cod_tipo'];
          $montoDetalle=number_format($rowDetalles['monto_total'], 2, '.', '');
          if($ibnorcaC==1){
          $montoDetalleAl=number_format($rowDetalles['editado_alumno'], 2, '.', '');       
          }else{
          $montoDetalleAl=number_format($montoModX, 2, '.', '');        
          } 
          if($codTipoX==4){
            $montoDetalle=$rowDetalles['editado_alumno']*$cantidadItem*$diasCursoXX;
          }else{
            $montoDetalle=$rowDetalles['editado_alumno']*$cantidadItem;
          }
         if($bandera==1){
          $totalMontoDetalle+=$montoDetalle;
          $totalMontoDetalleAl+=$montoDetalleAl; 
           $montoDetalle=number_format($montoDetalle, 2, '.', '');  
          ?><tr>
              <td class="text-left small text-white bg-info"><?=$nomX?></td>
              <td class="text-left small font-weight-bold"><?=strtoupper($rowDetalles['glosa'])?></td>
              <td class="text-right"><?=$cantidadItem?></td>
              <td class="text-right"><?=$montoDetalleAl?></td>
              <td class="text-right text-white bg-info"><?=$montoDetalle?></td>  
             </tr> 
           <?php
           $i++;       
         } 
                 
        }         
     }
    }
  ?>
      

      <tr>
        <td colspan="3" class="text-center font-weight-bold">Total</td>
        <td id="total_tabladetalleAl<?=$j?>" class="text-right"><?=$totalMontoDetalleAl?></td>
        <td id="total_tabladetalle<?=$j?>" class="text-right font-weight-bold"><?=$totalMontoDetalle?></td>
      </tr>
  </table>

  <?php 
  $j++; 
  }
  ?>
      </div>
    </div>
   </div>
  </div> 
 </div> 
</div> 
</body></html>