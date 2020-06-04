<?php
session_start();
set_time_limit(0);
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../styles.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../assets/libraries/CifrasEnLetras.php';

$dbh = new Conexion();
// Preparamos
$globalNombreGestion=$_SESSION["globalNombreGestion"];
$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalNombreUnidad=$_SESSION['globalNombreUnidad'];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];

$fechaActual=date("Y-m-d");
$desdeInicioAnio="";
if($_POST["fecha_desde"]==""){
  $y=$globalNombreGestion;
  $desde=$y."-01-01";
  $hasta=$y."-12-31";
  $desdeInicioAnio=$y."-01-01";
}else{
  $porcionesFechaDesde = explode("-", $_POST["fecha_desde"]);
  $porcionesFechaHasta = explode("-", $_POST["fecha_hasta"]);
  $desdeInicioAnio=$porcionesFechaDesde[0]."-01-01";
  $desde=$porcionesFechaDesde[0]."-".$porcionesFechaDesde[1]."-".$porcionesFechaDesde[2];
  $hasta=$porcionesFechaHasta[0]."-".$porcionesFechaHasta[1]."-".$porcionesFechaHasta[2];
}

$moneda=1;//$_POST["moneda"]
$nombreMoneda=nameMoneda($moneda);
$tipoCurso=$_POST['tipo_curso'];

if(isset($_POST['resumido'])){
 $resumido=1; 
 $rowSpan=4;
 $sqlSolicitadosInicio="SELECT 1 as codigo,1 as cod_simulacion,'<i class=\'material-icons bg-primary\'>insert_chart</i>' as nombre,'$hasta' as fecha_curso,l.cod_cuenta,GROUP_CONCAT(CONCAT(l.glosa,' ',l.nombre_proveedor)) as glosa,sum(l.presupuestado)as presupuestado,sum(l.ejecutado) as ejecutado,null as proveedor,1 as codigo_ejecutado FROM (";
 $sqlSolicitadosFin=") l where l.codigo_ejecutado!='' group by l.cod_cuenta";
 $solicitados=0;
 $anchoPartida="3%";
}else{
  $resumido=0;
  $anchoPartida="30%"; 
  if(isset($_POST['solicitados'])){
    $rowSpan=5; 
    $sqlSolicitadosInicio="";
    $sqlSolicitadosFin="";
    $solicitados=1;
   }else{
    $rowSpan=4;
    $sqlSolicitadosInicio="SELECT * FROM (";
    $sqlSolicitadosFin=") as l where l.codigo_ejecutado!=''";
    $solicitados=0;
   }
}


$tipoCursoArray=implode(",", $tipoCurso);
$tipoCursoAbrev="";
$tipoCursoAbrev=abrevTipoCliente($tipoCursoArray);

$periodoTitle=" Del ".strftime('%d/%m/%Y',strtotime($desde))." al ".strftime('%d/%m/%Y',strtotime($hasta));

?>
<div class="cargar">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold">Generando Reporte</h4>
     <p class="text-white">Aguard&aacute; un momento por favor</p>  
  </div>
</div>
 <div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="card-icon bg-blanco">
                    <img class="" width="40" height="40" src="../assets/img/logoibnorca.png">
                  </div>
                   <!--<div class="float-right col-sm-2"><h6 class="card-title">Exportar como:</h6></div>       -->
                   <h4 class="card-title text-center">Reporte Planificacion - Ejecución</h4>
                </div>
<script> periodo_mayor='<?=$periodoTitle?>';
 </script>

                <div class="card-body">
                  <h6 class="card-title">Periodo: <?=$periodoTitle?></h6>
                  <h6 class="card-title">Clientes: <?=$tipoCursoAbrev;?></h6>
                  <h6 class="card-title">Evaluación de la Conformidad</h6>
                  <div class="table-responsive">
    <table id="libro_mayor_rep" class="table table-bordered table-condensed" style="width:100%">
           <thead >
           <tr class="text-center">
             <th colspan="<?=$rowSpan?>" class=""></th>
             <th colspan="3" class=""><?=$nombreMoneda?></th>
           </tr>
           <tr class="text-center">
             <th width="$anchoPartida?>">Propuesta</th>
             <th width="30%">Cuenta - Detalle</th>
        <?php 
        if($solicitados==1){
           ?><th width="5%">Estado</th>
        <?php   
        }?>      
       <th class="bg-blanco2" width="8%">% EJECUCION PRESUPUESTARIA CUENTA</th>
             <th width="6%">% EJECUCION DE PROPUESTA</th>
             <th width="5%">Presupuesto</th>
             <th width="5%">Ejecutado</th>
           </tr>
          </thead>
          <tbody>
<?php

  $query1=$sqlSolicitadosInicio."(select s.codigo as cod_simulacion,concat(a.abreviatura,' - ',s.nombre) as nombre,s.fecha as fecha_curso,sd.codigo,sd.cod_cuenta,sd.glosa,sd.monto_total as presupuestado,sd.cod_anio,plan.cod_area,
(SELECT d.importe from solicitud_recursosdetalle d join solicitud_recursos so on so.codigo=d.cod_solicitudrecurso where so.cod_simulacionservicio=s.codigo and d.cod_detalleplantilla=sd.codigo) as ejecutado, 
(SELECT d.cod_proveedor from solicitud_recursosdetalle d join solicitud_recursos so on so.codigo=d.cod_solicitudrecurso where so.cod_simulacionservicio=s.codigo and d.cod_detalleplantilla=sd.codigo) as proveedor,
(SELECT pro.nombre from solicitud_recursosdetalle d join solicitud_recursos so on so.codigo=d.cod_solicitudrecurso join af_proveedores pro on pro.codigo=d.cod_proveedor where so.cod_simulacionservicio=s.codigo and d.cod_detalleplantilla=sd.codigo) as nombre_proveedor, 
(SELECT d.cod_detalleplantilla from solicitud_recursosdetalle d join solicitud_recursos so on so.codigo=d.cod_solicitudrecurso where so.cod_simulacionservicio=s.codigo and d.cod_detalleplantilla=sd.codigo) as codigo_ejecutado 
from simulaciones_serviciodetalle sd 
join simulaciones_servicios s on s.codigo=sd.cod_simulacionservicio
join plantillas_servicios plan on plan.codigo=s.cod_plantillaservicio
join areas a on a.codigo=plan.cod_area 
WHERE s.cod_cliente in($tipoCursoArray) and sd.habilitado=1 and s.cod_estadosimulacion=5 and s.fecha BETWEEN '$desde' and '$hasta' order by s.nombre,sd.cod_cuenta)

UNION (select s.codigo as cod_simulacion,concat(a.abreviatura,' - ',s.nombre) as nombre,s.fecha as fecha_curso,sd.cod_detalle as codigo,sd.codigo as cod_cuenta,sd.glosa,sd.monto_total as presupuestado,sd.cod_anio,plan.cod_area,
(SELECT d.importe from solicitud_recursosdetalle d join solicitud_recursos so on so.codigo=d.cod_solicitudrecurso where so.cod_simulacionservicio=s.codigo and d.cod_servicioauditor=sd.cod_detalle) as ejecutado, 
(SELECT d.cod_proveedor from solicitud_recursosdetalle d join solicitud_recursos so on so.codigo=d.cod_solicitudrecurso where so.cod_simulacionservicio=s.codigo and d.cod_servicioauditor=sd.cod_detalle) as proveedor,
(SELECT pro.nombre from solicitud_recursosdetalle d join solicitud_recursos so on so.codigo=d.cod_solicitudrecurso join af_proveedores pro on pro.codigo=d.cod_proveedor where so.cod_simulacionservicio=s.codigo and d.cod_servicioauditor=sd.cod_detalle) as nombre_proveedor, 
(SELECT d.cod_servicioauditor from solicitud_recursosdetalle d join solicitud_recursos so on so.codigo=d.cod_solicitudrecurso where so.cod_simulacionservicio=s.codigo and d.cod_servicioauditor=sd.cod_detalle) as codigo_ejecutado 
from v_propuestas_detalle_honorarios sd 
join simulaciones_servicios s on s.codigo=sd.cod_simulacionservicio
join plantillas_servicios plan on plan.codigo=s.cod_plantillaservicio
join areas a on a.codigo=plan.cod_area 
WHERE s.cod_cliente in($tipoCursoArray) and sd.habilitado=1 and s.cod_estadosimulacion=5 and s.fecha BETWEEN '$desde' and '$hasta' order by s.nombre,sd.cod_detalle) order by nombre".$sqlSolicitadosFin;

//echo $query1;
  $stmt = $dbh->prepare($query1);
  // Ejecutamos
  $stmt->execute();
  $stmtCount = $dbh->prepare($query1);
  $stmtCount->execute();
  $contador=0;
  while ($rowCount = $stmtCount->fetch(PDO::FETCH_ASSOC)) {
    $contador++;
  }

  $tc=obtenerValorTipoCambio($moneda,strftime('%Y-%m-%d',strtotime($desdeInicioAnio)));
  if($tc==0){$tc=1;}

  $index=1; 
  $tPresupuesto=0;$tEjecutado=0;$tsaldo=0;
  $tPresupuestoS=0;$tEjecutadoS=0;$tsaldoS=0;    
  $cursoNombre="";
  $codPlan=0;
  $codigoSimulacion=0;
  $htmlFijos="";
  while ($rowComp = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $codigoX=$rowComp['codigo'];
    $codigoSimulacionX=$rowComp['cod_simulacion'];
    $nombreX=$rowComp['nombre'];
    $fechaX=$rowComp['fecha_curso'];
    $codCuenta=$rowComp['cod_cuenta'];  
    $glosaX=$rowComp['glosa'];
    $proveedorX=$rowComp['proveedor'];

    $presupuestadoX=$rowComp['presupuestado']/$tc;
    $ejecutadoX=$rowComp['ejecutado']/$tc;
    $nombreCuenta=nameCuenta($codCuenta);
    $numeroCuentaX=trim(obtieneNumeroCuenta($codCuenta));
    $anioSim=strftime('%Y',strtotime($fechaX));
    $mesSim=strftime('%m',strtotime($fechaX));
    $datosSeg=obtenerPresupuestoEjecucionDelServicio($globalUnidad,13,$anioSim,(int)$mesSim,$numeroCuentaX);           
    if($datosSeg->presupuesto!=0){
               $segPres=$datosSeg->presupuesto;
               $porcentSegPres=formatNumberDec(($datosSeg->ejecutado*100)/$datosSeg->presupuesto)." %"; 
    }else{
      $porcentSegPres="SIN PRESUPUESTO"; 
    }

    $estado="EJECUTADO";
    $claseEstado="";
    if($rowComp['codigo_ejecutado']==null){
      $estado="SIN SOLICITUD";
      $claseEstado="text-danger";
      $estadoEjecutado="";
      $fechaCurso='<br><b>Fecha Curso: </b>'.strftime('%d/%m/%Y',strtotime($fechaX));
      $etapas="";
      if($resumido==0){
        $anioDetalle=$rowComp['cod_anio'];
        if($rowComp['cod_area']!=39){
          $etapas="Seg ".($anioDetalle-1);
          if($rowComp['cod_area']!=39){
            if($anioDetalle==0||$anioDetalle==1){
             $etapas="Et ".($anioDetalle+1).""; 
            }
          }                                        
        }else{
         $etapas="Año ".$anioDetalle; 
        }
      }
      $glosaX="<br><b>Detalle: </b>".$glosaX." ".$rowComp['nombre_proveedor']." (".$etapas.")";
    }else{
      $estadoEjecutado="bg-success";
      if($presupuestadoX>$ejecutadoX){
        $estadoEjecutado="bg-warning";
      }else{
         if($presupuestadoX<$ejecutadoX){
            $estadoEjecutado="bg-danger";
         }
      }
      $estadoEjecutado.=" text-white";
      if($resumido==0){
        $anioDetalle=$rowComp['cod_anio'];
        if($rowComp['cod_area']!=39){
          $etapas="Seg ".($anioDetalle-1);
          if($rowComp['cod_area']!=39){
            if($anioDetalle==0||$anioDetalle==1){
             $etapas="Et ".($anioDetalle+1).""; 
            }
          }                                        
        }else{
         $etapas="Año ".$anioDetalle; 
        }
        $glosaX="<br><b>Detalle: </b>".$glosaX." ".$rowComp['nombre_proveedor']." (".$etapas.")";
        $fechaCurso='<br><b>Fecha Propuesta: </b>'.strftime('%d/%m/%Y',strtotime($fechaX));
      }else{
        $glosaX="";
        $fechaCurso="";
      }
    } 
    if($cursoNombre!=$nombreX&&$index>1&&$resumido==0){
      $codAnio=$rowComp['cod_anio'];
       //COSTOS FIJOS
        if(isset($_POST['costos_fijos'])) {
          ?>
          <tr class="text-white" style="background:#92E83B;color:white;">
                <td colspan="<?=(($rowSpan-1)+3)?>" class="text-center font-weight-bold">COSTOS FIJOS <?=$cursoNombre?></td>    
            </tr>
          <?php  
         //if(obtenerPlantillaCodigoSimulacion($codigoSimulacion)!=$codPlan){
          $htmlFijos='';
          $codPlan=obtenerPlantillaCodigoSimulacion($codigoSimulacion);
          $query_cuentas="SELECT cf.*,p.nombre,p.numero from simulaciones_cf cf join plan_cuentas p on p.codigo=cf.cod_cuenta where cf.cod_simulacionservicio=$codigoSimulacion order by cf.cod_anio,cf.cod_cuenta";
            $stmt_cuentas = $dbh->prepare($query_cuentas);
            $stmt_cuentas->execute();
            while ($row_cuentas = $stmt_cuentas->fetch(PDO::FETCH_ASSOC)) {
                 $nombreCuentaFijo=$row_cuentas['nombre'];
                 $numeroCuentaFijo=$row_cuentas['numero'];
                 $montoFijo=$row_cuentas['monto_total'];
                 
                 $precioLocalX=obtenerPrecioSimulacionCostoGeneral($codigoSimulacion);
                 $precioRegistrado=obtenerPrecioRegistradoPlantillaCosto(obtenerPlantillaCodigoSimulacion($codigoSimulacion));
                 $nCursos=obtenerCantidadCursosPlantillaCosto(obtenerPlantillaCodigoSimulacion($codigoSimulacion)); 
                 $porcentPrecios=($precioLocalX)/($precioRegistrado);
                 $datosSeg=obtenerPresupuestoEjecucionDelServicio($globalUnidad,13,$anioSim,(int)$mesSim,$numeroCuentaFijo);             
                 $tipoSim=obtenerValorConfiguracion(13);
                 //$monto=ejecutadoEgresosMes($globalUnidad,((int)$anioSim-1),(int)$mesSim,13,2,$numeroCuentaFijo);
                 $glosaFijo="<br><b>Precio Curso:</b> ".formatNumberDec($precioLocalX)." Bs. <b>Seguimiento Presupuestal:</b> ".formatNumberDec($precioRegistrado)." Bs. <b>Porcent. :</b> ".formatNumberDec(($porcentPrecios*100))." <b>%</b> ";
                 $estadoFijo="Monto:".formatNumberDec(($montoFijo*100)/$porcentPrecios);
                 if($datosSeg->presupuesto!=0){
                    $porcentSegPres=formatNumberDec((($datosSeg->ejecutado)*100)/($datosSeg->presupuesto))." %"; 
                  }else{
                     $porcentSegPres="SIN PRESUPUESTO"; 
                  }
                  if($montoFijo>0){
                 $htmlFijos.='<tr class="" style="background:#E4E4E4">'.
                    '<td class="font-weight-bold small text-left">'.$nombreX.' '.$fechaCurso.'</td>'.
                    '<td class="font-weight-bold small text-left">'.$nombreCuentaFijo.' '.$glosaFijo.'</td>';
                  if($solicitados==1){
                      $htmlFijos.= '<td class="font-weight-bold small">'.$estadoFijo.'</td>';
                  }                
                 $htmlFijos.='<td class="font-weight-bold small bg-blanco2 text-right">'.$porcentSegPres.'</td>'.
                    '<td class="font-weight-bold small text-right">'.formatNumberDec(100).' %</td>'.
                    '<td class="font-weight-bold small text-right">'.formatNumberDec($montoFijo).'</td>'.
                    '<td class="font-weight-bold small text-right">'.formatNumberDec(0).'</td>';
                   $htmlFijos.='</tr>';          
                  }
         
         }
        //}
        echo $htmlFijos;       
      }
        //FIN COSTOS FIJOS 

      if($tEjecutadoS>$tPresupuestoS){
          $claseSubTotal="bg-danger";
      }else{
        if($tEjecutadoS<$tPresupuestoS){
          $claseSubTotal="bg-warning";
        }else{
          $claseSubTotal="bg-success";
        }        
      }
      ?>
       <tr class="bg-plomo">
                <td colspan="<?=($rowSpan-1)?>" class="text-left font-weight-bold">Totales Curso <?=$cursoNombre?> </td>
                <td class="text-right font-weight-bold <?=$claseSubTotal?>"><?=formatNumberDec(($tEjecutadoS/$tPresupuestoS)*100)?> %</td>      
                <td class="text-right font-weight-bold"><?=formatNumberDec($tPresupuestoS)?></td>      
                <td class="text-right font-weight-bold"><?=formatNumberDec($tEjecutadoS)?></td> 
            </tr>
      <?php      
        $tPresupuestoS=0;$tEjecutadoS=0;$tsaldoS=0;  
    }

    if($cursoNombre!=$nombreX&&$resumido==0){
       ?><tr class="bg-info text-white">
                <td colspan="<?=(($rowSpan-1)+3)?>" class="text-center font-weight-bold">COSTOS VARIABLES <?=$nombreX?> </td>
            </tr>
     <?php       
    }
    $codigoSimulacion=$codigoSimulacionX; 
    $cursoNombre=$nombreX;
    if($presupuestadoX==0){
      $presupuestadoX=1;
    }
    $porcentajePresX=($ejecutadoX/$presupuestadoX)*100;
    $saldoX=$presupuestadoX-$ejecutadoX;

    $tPresupuesto+=$presupuestadoX;
    $tEjecutado+=$ejecutadoX;
    $tsaldo+=$saldoX;
    $tPresupuestoS+=$presupuestadoX;
    $tEjecutadoS+=$ejecutadoX;
    $tsaldoS+=$saldoX;
    ?>      
        <tr class="<?=$claseEstado?>">
                    <td class="font-weight-bold small text-left"><?=$nombreX?> <?=$fechaCurso?></td>
                    <td class="font-weight-bold small text-left"><?=$nombreCuenta?> <?=$glosaX?></td>
      <?php if($solicitados==1){
         ?>   <td class="font-weight-bold small"><?=$estado?></td>
       <?php  
        }   
        ?>             
               <td class="font-weight-bold small text-right bg-blanco2 text-dark"><?=$porcentSegPres?></td>
                    <td class="font-weight-bold small text-right <?=$estadoEjecutado?>"><?=formatNumberDec($porcentajePresX)?> %</td>
                    <td class="font-weight-bold small text-right"><?=formatNumberDec($presupuestadoX)?></td>
                    <td class="font-weight-bold small text-right"><?=formatNumberDec($ejecutadoX)?></td>
                    
        </tr>
    <?php    
      $index++; 
    }/* Fin del primer while*/
    if($resumido==0){
      //COSTOS FIJOS
        if(isset($_POST['costos_fijos'])) {
          ?>
          <tr class="text-white" style="background:#92E83B;color:white;">
                <td colspan="<?=(($rowSpan-1)+3)?>" class="text-center font-weight-bold">COSTOS FIJOS <?=$cursoNombre?></td>    
            </tr>
          <?php  
         //if(obtenerPlantillaCodigoSimulacion($codigoSimulacion)!=$codPlan){
          $htmlFijos='';
          $codPlan=obtenerPlantillaCodigoSimulacion($codigoSimulacion);
          $query_cuentas="SELECT cf.*,p.nombre,p.numero from simulaciones_cf cf join plan_cuentas p on p.codigo=cf.cod_cuenta where cf.cod_simulacionservicio=$codigoSimulacion order by cf.cod_anio,cf.cod_cuenta";
            $stmt_cuentas = $dbh->prepare($query_cuentas);
            $stmt_cuentas->execute();
            while ($row_cuentas = $stmt_cuentas->fetch(PDO::FETCH_ASSOC)) {
                 $nombreCuentaFijo=$row_cuentas['nombre'];
                 $numeroCuentaFijo=$row_cuentas['numero'];
                 $montoFijo=$row_cuentas['monto_total'];
                 
                 $precioLocalX=obtenerPrecioSimulacionCostoGeneral($codigoSimulacion);
                 $precioRegistrado=obtenerPrecioRegistradoPlantillaCosto(obtenerPlantillaCodigoSimulacion($codigoSimulacion));
                 $nCursos=obtenerCantidadCursosPlantillaCosto(obtenerPlantillaCodigoSimulacion($codigoSimulacion)); 
                 $porcentPrecios=($precioLocalX)/($precioRegistrado);
                 $datosSeg=obtenerPresupuestoEjecucionDelServicio($globalUnidad,13,$anioSim,(int)$mesSim,$numeroCuentaFijo);             
                 $tipoSim=obtenerValorConfiguracion(13);
                 //$monto=ejecutadoEgresosMes($globalUnidad,((int)$anioSim-1),(int)$mesSim,13,2,$numeroCuentaFijo);
                 $glosaFijo="<br><b>Precio Curso:</b> ".formatNumberDec($precioLocalX)." Bs. <b>Seguimiento Presupuestal:</b> ".formatNumberDec($precioRegistrado)." Bs. <b>Porcent. :</b> ".formatNumberDec(($porcentPrecios*100))." <b>%</b> ";
                 $estadoFijo="Monto:".formatNumberDec(($montoFijo*100)/$porcentPrecios);
                 if($datosSeg->presupuesto!=0){
                    $porcentSegPres=formatNumberDec((($datosSeg->ejecutado)*100)/($datosSeg->presupuesto))." %"; 
                  }else{
                     $porcentSegPres="SIN PRESUPUESTO"; 
                  }
                  if($montoFijo>0){
                 $htmlFijos.='<tr class="" style="background:#E4E4E4">'.
                    '<td class="font-weight-bold small text-left">'.$nombreX.' '.$fechaCurso.'</td>'.
                    '<td class="font-weight-bold small text-left">'.$nombreCuentaFijo.' '.$glosaFijo.'</td>';
                  if($solicitados==1){
                      $htmlFijos.= '<td class="font-weight-bold small">'.$estadoFijo.'</td>';
                  }                
                 $htmlFijos.='<td class="font-weight-bold small bg-blanco2 text-right">'.$porcentSegPres.'</td>'.
                    '<td class="font-weight-bold small text-right">'.formatNumberDec(100).' %</td>'.
                    '<td class="font-weight-bold small text-right">'.formatNumberDec($montoFijo).'</td>'.
                    '<td class="font-weight-bold small text-right">'.formatNumberDec(0).'</td>';
                   $htmlFijos.='</tr>';          
                  }
         
         }
        //}
        echo $htmlFijos;       
      }
        //FIN COSTOS FIJOS 
      if($tEjecutadoS>$tPresupuestoS){
          $claseSubTotal="bg-danger";
      }else{
        if($tEjecutadoS<$tPresupuestoS){
          $claseSubTotal="bg-warning";
        }else{
          $claseSubTotal="bg-success";
        }        
      }
      ?>
       <tr class="bg-plomo">
                <td colspan="<?=($rowSpan-1)?>" class="text-left font-weight-bold">Totales Curso <?=$cursoNombre?> </td>
                <td class="text-right font-weight-bold <?=$claseSubTotal?>"><?=formatNumberDec(($tEjecutadoS/$tPresupuestoS)*100)?> %</td>      
                <td class="text-right font-weight-bold"><?=formatNumberDec($tPresupuestoS)?></td>      
                <td class="text-right font-weight-bold"><?=formatNumberDec($tEjecutadoS)?></td> 
                       
            </tr>    
 <?php
    }
    if($contador!=0){
      if($tEjecutado>$tPresupuesto){
          $claseSubTotal="bg-danger";
      }else{
        if($tEjecutado<$tPresupuesto){
          $claseSubTotal="bg-warning text-dark";
        }else{
          $claseSubTotal="bg-success";
        }        
      }
     ?>
      <tr class="bg-secondary text-white">
                <td colspan="<?=($rowSpan-1)?>" class="text-center">Totales:</td>
                <td class="text-right font-weight-bold small <?=$claseSubTotal?>"><?=formatNumberDec(($tEjecutado/$tPresupuesto)*100)?> %</td>
                <td class="text-right font-weight-bold small"><?=formatNumberDec($tPresupuesto)?></td> 
                <td class="text-right font-weight-bold small"><?=formatNumberDec($tEjecutado)?></td>    
            </tr>
<?php    } ?>

 </tbody></table>

                   </div>
                </div>
              
              </div>
            </div>
          </div>  
        </div>
    </div>

