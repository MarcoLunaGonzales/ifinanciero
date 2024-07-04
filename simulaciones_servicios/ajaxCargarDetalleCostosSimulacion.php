<?php
set_time_limit(0);
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();
$globalNombreGestion=$_SESSION["globalNombreGestion"];
$mesActualConsulta=date("m");
if(isset($_GET["simulacion"])){
 $codigo=$_GET["simulacion"];
 $codPlan=$_GET["plantilla"];
 $usd=(float)$_GET["usd"];
 $cod_anio=$_GET["anio"];
 $codArea=obtenerCodigoAreaPlantillaServicio($codPlan);

 if($codArea==39){
   $inicioAnio=1;
 }else{
   $inicioAnio=0;
 }

 /** RONALD MOLLERICONA MIRANDA **/
 /***************************
  * Propuesta de Presupuesto
  ***************************/
$stmtPP = $dbh->prepare("SELECT sc.propuesta_gestion, sc.propuesta_gestion2, sc.propuesta_gestion3 FROM simulaciones_servicios sc WHERE sc.codigo=:codigo");
$stmtPP->bindParam(':codigo', $codigo, PDO::PARAM_STR);
$stmtPP->execute();
$stmtPP->bindColumn('propuesta_gestion', $propuesta_gestionX);
// Obtener los resultados
$rowPP = $stmtPP->fetch(PDO::FETCH_ASSOC);
$propuesta_gestion  = $rowPP['propuesta_gestion'];
$propuesta_gestion2 = $rowPP['propuesta_gestion2'];
$propuesta_gestion3 = $rowPP['propuesta_gestion3'];
/**************************************************************
 * Permite controlar el limite de gestión para los incrementos
 * en gestiones futuras
 **************************************************************/
$control_anio = 0;

/**********************
 * PORCENTAJE DE AJUSTE
 **********************/
$sql_ajuste  = "SELECT porcentaje_ajuste, porcentaje_ajuste2, porcentaje_ajuste_ing FROM plantillas_servicios WHERE codigo = '$codPlan'";
$stmt_ajuste = $dbh->prepare($sql_ajuste);
$stmt_ajuste->execute([$codPlan]);
$datos_ajuste = $stmt_ajuste->fetch(PDO::FETCH_ASSOC);
$dato_porcentaje_ajuste1    = $datos_ajuste['porcentaje_ajuste'];     // Porcentaje ajuste 1
$dato_porcentaje_ajuste2    = $datos_ajuste['porcentaje_ajuste2'];    // Porcentaje ajuste 2
$dato_porcentaje_ajuste_ing = $datos_ajuste['porcentaje_ajuste_ing']; // Porcentaje ajuste Ingreso

 ?>
   <ul class="nav nav-pills nav-pills-warning" role="tablist">
    <?php
      for ($an=$inicioAnio; $an<=$cod_anio; $an++) { 
        $active="";
        $tituloItem="Año ".$an;
        if($codArea!=39){
          $tituloItem="Año ".$an."(SEGUIMIENTO ".($an-1).")";
           if($an==0||$an==1){
           $tituloItem="Año 1 (ETAPA ".($an+1).")"; 
          }
        }

        if($an==1){
          $active="active";
        }
            ?>
      <li class="nav-item">
        <a class="nav-link <?=$active?>" data-toggle="tab" href="#link_detalle<?=$an?>" role="tablist">
           <?=$tituloItem?>
         </a>
       </li>
    <?php
    }
    ?>
    </ul>
    <div class="tab-content tab-space">
 <?php
 
 $anio = date('Y');
 $gestion = date('Y');
 for ($yyyy=$inicioAnio; $yyyy <=$cod_anio; $yyyy++) { 
    $active="";
    $tituloItem="AÑO ".$yyyy;
    if($codArea!=39){
      $tituloItem="Año ".$yyyy."(SEGUIMIENTO ".($yyyy-1).")";
       if($yyyy==0||$yyyy==1){
       $tituloItem="Año 1 (ETAPA ".($yyyy+1).")"; 
      }
    }
    if($yyyy==1){
      $active="active";
    }

    /**
     * ? VERIFICACIÓN DE GESTIÓN PARA TOMAR EN CUENTA EL 15%
     */
    $verf_gestion = true; // Verifica 15%
    if(!empty($propuesta_gestion) || !empty($propuesta_gestion2) || !empty($propuesta_gestion3)){ // Si se tiene gestión
        if($yyyy == 0 || $yyyy == 1 && !empty($propuesta_gestion)){
            $gestion = $propuesta_gestion;
        }else if($yyyy == 2 && !empty($propuesta_gestion2)){
            $gestion = $propuesta_gestion2;
        }else if($yyyy == 3 && !empty($propuesta_gestion3)){
            $gestion = $propuesta_gestion3;
        }else{
            $gestion = $gestion + ($yyyy - 1); // Captura Gestión
        }

        if($gestion > date('Y')){
            $verf_gestion = false;
        }
    }
  ?>
   <div class="tab-pane <?=$active?>" id="link_detalle<?=$yyyy?>">
    <h4 class="font-weight-bold">
        <center>COSTOS <?=$tituloItem?>
            <?php
                if($verf_gestion && (!empty($propuesta_gestion) || !empty($propuesta_gestion2) || !empty($propuesta_gestion3))){
                // Incremento 15%
            ?>
            <button type="button" class="btn btn-success btn-round btn-fab btn-sm">
                <i class="material-icons">check</i>
            </button>
            <?php
                }else{
                // Presupuesto Real
            ?>
            <button type="button" class="btn btn-warning btn-round btn-fab btn-sm">
                <i class="material-icons">playlist_add_check</i>
            </button>
            <?php 
                } 
            ?>
        </center>
    </h4>
  <?php
 
 if($codArea==39){
   $mes=obtenerCantidadAuditoriasPlantilla($codPlan);
 }else{
   $mes=obtenerCantidadAuditoriasPlantilla($codPlan);
 }
 $tipoCosto=$_GET["tipo"];
 $alumnos=obtenerCantidadPersonalSimulacionEditado($codigo);

$query1="SELECT pgd.cod_plantillagruposervicio,pc.cod_unidadorganizacional,pc.cod_area,pgc.nombre,pgc.cod_tiposervicio,sum(pgd.monto_local) as local,sum(pgd.monto_externo) as externo,sum(pgd.monto_calculado) as calculado 
from plantillas_gruposerviciodetalle pgd 
join partidas_presupuestarias pp on pgd.cod_partidapresupuestaria=pp.codigo
join plantillas_gruposervicio pgc on pgd.cod_plantillagruposervicio=pgc.codigo
join plantillas_servicios pc on pgc.cod_plantillaservicio=pc.codigo 
where pc.codigo=$codPlan";

if($tipoCosto==1){
$query2=$query1." and pgc.cod_tiposervicio=1 GROUP BY pgd.cod_plantillagruposervicio order by pgd.cod_plantillagruposervicio";
$bgClase="bg-info";
}else{
  
  $query2=$query1." and pgc.cod_tiposervicio=2 GROUP BY pgd.cod_plantillagruposervicio order by pgd.cod_plantillagruposervicio";
  $bgClase="bg-success";
}

//echo $query2;

  $stmt = $dbh->prepare($query2);
  $stmt->execute();
  $html='';$montoTotales=0;$montoTotales2=0;$montoTotales2Alumno=0;$montoTotalesPresupuesto=0;
  $precioLocalX=obtenerPrecioServiciosSimulacionPorAnio($codigo,$yyyy);
?>
       <div class=""><center>
        <?php if($tipoCosto==1){
          $porCre=($_GET['porcentaje_fijo']/100);//*($yyyy-1);

          /* DATOS PARA PRECIO EN LUGAR DE CANTIDAD AUDITORIAS*/
          $precioLocalX=obtenerPrecioServiciosSimulacionPorAnio($codigo,$yyyy);
          
            $codAreaX=0;
            $datosPlantilla=obtenerPlantillaServicioDatos($codPlan);
            while ($rowPlantilla = $datosPlantilla->fetch(PDO::FETCH_ASSOC)) {
                $codAreaX=$rowPlantilla['cod_area'];
            }

            /**********************************************
             * Obtiene Gestión de Propuesta de Presupuesto
             **********************************************/
            // ? CAPTURA GESTIÓN
            if(($yyyy == 0 || $yyyy == 1) && !empty($propuesta_gestion)){
                $anio = $propuesta_gestion;
            }else if($yyyy == 2 && !empty($propuesta_gestion2)){
                $anio = $propuesta_gestion2;
            }else if($yyyy == 3 && !empty($propuesta_gestion3)){
                $anio = $propuesta_gestion3;
            }else{
                $anio = $anio + ($yyyy == 0 ? $yyyy : ($yyyy - 1)); // Captura Gestión
            }
            // echo "anio".$anio.'<br>';
            
            // Control de Incremento 15% si pasa la FECHA LIMITE ACTUAL
            if($anio > date('Y')){
                $anio = date('Y'); // Año limite es la "FECHA ACTUAL"
                $control_anio++;
            }
            /**********************************************/
            // Obtiene Presupuesto
            if($codAreaX == 5291){ // VERIFICA TVR
                $codAreaX = 39;      // TVR pertenece a TCP
            }

            $precioRegistrado = (!empty($propuesta_gestion)) 
                                ? obtenerPresupuestoEjecucionPorAreaAcumulado(0, $codAreaX, $anio, 12, 1)['presupuesto']
                                : obtenerPrecioRegistradoPropuestaTCPTCS($codigo);
          
            if($precioRegistrado==0){
                $precioRegistrado=1;
            }
            $sumaPrecioRegistrado=0;
            $precioRegistradoAux = $precioRegistrado;

            /**********************************************************************************************************/
            // VALOR REAL
            $real_precioRegistradoAux = $precioRegistradoAux;
            // VALOR AJUSTADO FINAL
            $precioRegistradoAux      = $real_precioRegistradoAux * $dato_porcentaje_ajuste_ing;
            /**********************************************************************************************************/
            // * Si no se tiene la PROPUESTA GESTIÓN - Mantiene incremento 15% desde el segundo AÑO
            if(empty($propuesta_gestion) && empty($propuesta_gestion2) && empty($propuesta_gestion3)){
                // Procesando desde el "Segundo Año"
                if($yyyy > 1){
                    for ($anioAumento=2; $anioAumento <= $yyyy; $anioAumento++) {
                        // VALOR REAL
                        $real_precioRegistradoAux = $real_precioRegistradoAux + ($real_precioRegistradoAux * $porCre);
                        // VALOR AJUSTADO 1
                        $precioRegistradoAux  = $precioRegistradoAux + ($precioRegistradoAux * $porCre);
                    }
                }
            }else{
            // * El incremento solo se aplica cuando la gestión es futura en comparación con la gestión actual
                // Procesando gestiones futuras
                if($control_anio > 0){
                    for ($anioAumento = 1; $anioAumento <= $control_anio; $anioAumento++) {
                        // VALOR REAL
                        $real_precioRegistradoAux = $real_precioRegistradoAux + ($real_precioRegistradoAux * $porCre);
                        // VALOR AJUSTADO
                        $precioRegistradoAux  = $precioRegistradoAux + ($precioRegistradoAux * $porCre);
                    }
                }
            }
            /**********************************************************************************************************/          
            // YA NO SE UTILIZA ESTA PRODECIMIENTO
            // // VALOR REAL
            // $real_precioRegistradoAux= $precioRegistradoAux;
            // // VALOR AJUSTADO FINAL 1
            // $precioRegistradoAux0 = $real_precioRegistradoAux * $dato_porcentaje_ajuste1;
            // // VALOR AJUSTADO FINAL 2
            // $precioRegistradoAux  = $precioRegistradoAux0 * $dato_porcentaje_ajuste2;
            // if($yyyy>1){
            //     for ($anioAumento=2; $anioAumento <= $yyyy; $anioAumento++) { 
            //         // echo 'yyyy:'.$yyyy.'<br>';
            //         // VALOR REAL
            //         $real_precioRegistradoAux = $real_precioRegistradoAux + ($real_precioRegistradoAux * $porCre);
            //         // VALOR AJUSTADO 1
            //         $precioRegistradoAux0 = $precioRegistradoAux0+($precioRegistradoAux0*$porCre);
            //         // VALOR AJUSTADO 2
            //         $sumaPrecioRegistrado = $precioRegistradoAux * $porCre;
            //         $precioRegistradoAux  = $precioRegistradoAux + $sumaPrecioRegistrado;
            //     }
            //     //$precioLocalX=($precioLocalX*$porCre)+$precioLocalX;
            //     //$sumaPrecioRegistrado=$precioRegistrado*$porCre;
            // }
            /**********************************************************************************************************/



          $nAuditorias=obtenerCantidadAuditoriasPlantilla($codPlan); 
          
          $porcentPrecios=($precioLocalX*100)/($precioRegistradoAux);  
          $porcentPrecios=(float)number_format($porcentPrecios,2,'.','');
          $codOficina=0;$codAreaX=0;
          $datosPlantilla=obtenerPlantillaServicioDatos($codPlan);
          while ($rowPlantilla = $datosPlantilla->fetch(PDO::FETCH_ASSOC)) {
            $codOficina=$rowPlantilla['cod_unidadorganizacional'];
            $codAreaX=$rowPlantilla['cod_area'];
          }

          /*$presupuestoMes=obtenerPresupuestoEjecucionPorArea($codOficina,$codAreaX,$globalNombreGestion,$mesActualConsulta)['presupuesto'];
          if($presupuestoMes>0){
            $porcentPreciosMes=($precioLocalX*100)/($presupuestoMes);
          }else{
            $porcentPreciosMes=0;
          }*/
         
         $valorConfiguracionTCPTCS=obtenerValorConfiguracion(52);
         $tituloPorpuestaTCPTCS="NACIONAL";
          if($valorConfiguracionTCPTCS==1){
            $tituloPorpuestaTCPTCS=$_GET['unidad_nombre'];
          }
          /* fin de datos */
         
         ?>
          <table class="table table-condensed table-bordered">
            <tr class="text-white <?=$bgClase?>">
              <td colspan="14">DATOS</td>
            </tr>
            <tr>
              <td class="bg-plomo">PRESUPUESTO <?=$_GET['area_nombre']?>, <?=$tituloPorpuestaTCPTCS?> GESTION</td>
              <td class="text-right"><?=number_format($real_precioRegistradoAux, 2, '.', ',')?></td>
              <!-- Nueva Sección PORCENTAJE DE AJUSTE -->
              <td class="bg-plomo">Porcentaje de Ajuste</td>
              <td class="text-right"><?=number_format($dato_porcentaje_ajuste_ing * 100, 2, '.', ',')?>%</td>
              <td class="bg-plomo">Presupuesto ajustado</td>
              <td class="text-right"><?=number_format($precioRegistradoAux, 2, '.', ',')?></td>

              <td class="bg-plomo">Monto Ingreso</td>
              <td class="text-right"><?=number_format($precioLocalX, 2, '.', ',')?></td>
              <td class="bg-plomo">Porcentaje</td>
              <td class="text-right"><?=number_format($porcentPrecios, 2, '.', ',')?> %</td>
            </tr>

          </table>
       <?php
        }?>
       </center></div>
   <table class="table table-condensed table-bordered">
         <tr class="text-white <?=$bgClase?>">
        <td>Cuenta / Detalle</td>
        <?php 
          if($tipoCosto==1){
        ?> 
          <td>Presupuesto BOB</td>
          <!-- PORCENTAJE DE AJUSTE 1 -->
          <td>Presupuesto BOB <b>(<?=number_format($dato_porcentaje_ajuste1*100, 2, '.', ',')?>%)</b></td>
          <!-- PORCENTAJE DE AJUSTE 2 -->
          <td>Presupuesto BOB <b>(<?=number_format($dato_porcentaje_ajuste2*100, 2, '.', ',')?>%)</b></td>
        <?php 
          }
        ?>
        <td>Monto x Servicio BOB</td>
        <td>Monto x Servicio USD</td>
        <?php if($tipoCosto!=1){
        ?> <td>Monto x Persona BOB</td><td>Monto x Persona USD</td><td>Cantidad</td><?php 
        }
        ?>
        </tr>
<?php
$index_titulo = 0;
$id_columna = '';
//  TOTAL FINAL
$total_final1 = 0;
$total_final2 = 0;
$total_final3 = 0;
$total_final4 = 0;
$total_final5 = 0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $codGrupo=$row['cod_plantillagruposervicio'];
  $grupoUnidad=$row['cod_unidadorganizacional'];
  $grupoArea=$row['cod_area'];
    
    if($tipoCosto==1){
      if($row['calculado']==$row['local']){
        $precioRegistradoAux1=($row['calculado']*$nAuditorias);
        if($yyyy>1){
            for ($anioAumento=2; $anioAumento <= $yyyy; $anioAumento++) { 
              $sumaPrecioRegistrado1=$precioRegistradoAux1*$porCre;
              $precioRegistradoAux1=$precioRegistradoAux1+$sumaPrecioRegistrado1;
            }
          }
      $montoPresupuestoFilaOriginal=$precioRegistradoAux1;
      $montoPresupuestoFila=$montoPresupuestoFilaOriginal * $dato_porcentaje_ajuste1;
      $montoCalculadoTit=$precioRegistradoAux1*($porcentPrecios/100);
    }else{
      $precioRegistradoAux1=($row['local']*$nAuditorias);
        if($yyyy>1){
            for ($anioAumento=2; $anioAumento <= $yyyy; $anioAumento++) { 
              $sumaPrecioRegistrado1=$precioRegistradoAux1*$porCre;
              $precioRegistradoAux1=$precioRegistradoAux1+$sumaPrecioRegistrado1;
            }
          }
      $montoPresupuestoFilaOriginal=$precioRegistradoAux1;
      $montoPresupuestoFila=$montoPresupuestoFilaOriginal * $dato_porcentaje_ajuste1;
      $montoCalculadoTit=$precioRegistradoAux1*($porcentPrecios/100);
    }
    //$montoPresupuestoFila=($montoCalculadoTit*100)/($porcentPrecios);
      $montoTotales+=$montoCalculadoTit;
      $montoTotalesPresupuesto+=$montoPresupuestoFila;
      // DETALLE TOTAL AGRUPADO
      //  $html.='<tr class="bg-plomo">'.
      //                 '<td class="font-weight-bold text-left">'.$row['nombre'].'-'.$porcentPrecios.'</td>'.
      //                 '<td class="text-right font-weight-bold">'.number_format($montoPresupuestoFilaOriginal, 2, '.', ',').'</td>'.
      //                 '<td class="text-right font-weight-bold">'.number_format($montoPresupuestoFila, 2, '.', ',').'</td>'.
      //                 '<td class="text-right font-weight-bold">'.number_format($montoCalculadoTit, 2, '.', ',').'</td>'.
      //                 '<td class="text-right font-weight-bold">'.number_format($montoCalculadoTit/$usd, 2, '.', ',').'</td>';
      // $html.='</tr>';
     }else{
      // DETALLE TOTAL AGRUPADO
      //  $html.='<tr class="bg-plomo">'.
      //                 '<td class="font-weight-bold text-left">'.$row['nombre'].'</td>'.
      //                 '<td class="text-right font-weight-bold"></td><td></td><td></td><td></td><td></td>';
      // $html.='</tr>';
    }
     

     $query_partidas="select pgd.cod_plantillagruposervicio,pp.nombre,pgd.cod_partidapresupuestaria,pgd.tipo_calculo,pgd.monto_local,pgd.monto_externo,pgd.monto_calculado from plantillas_gruposerviciodetalle pgd join partidas_presupuestarias pp on pgd.cod_partidapresupuestaria=pp.codigo join plantillas_gruposervicio pgc on pgd.cod_plantillagruposervicio=pgc.codigo where pgd.cod_plantillagruposervicio=$codGrupo";
    // echo $query_partidas."</br>";
     $stmt_partidas = $dbh->prepare($query_partidas);
     $stmt_partidas->execute();

     while ($row_partidas = $stmt_partidas->fetch(PDO::FETCH_ASSOC)) {
        // TOTALES
        $columna_1 = 0;
        $columna_2 = 0;
        $columna_3 = 0;
        $columna_4 = 0;
        $columna_5 = 0;

       $codPartida=$row_partidas['cod_partidapresupuestaria'];
         $numeroCuentas=contarPresupuestoCuentas($codPartida);

        
         if($tipoCosto==1){
        if($row_partidas['tipo_calculo']!=1){
          $numeroCuentas="(Manual)";
          $precioRegistradoAux2=($row_partidas['monto_local']*$nAuditorias);
          if($yyyy>1){
            for ($anioAumento=2; $anioAumento <= $yyyy; $anioAumento++) { 
              $sumaPrecioRegistrado2=$precioRegistradoAux2*$porCre;
              $precioRegistradoAux2=$precioRegistradoAux2+$sumaPrecioRegistrado2;
            }
          }
          $montoPresupuestoFila2Original=$precioRegistradoAux2;
          $montoPresupuestoFila2 = $montoPresupuestoFila2Original * $dato_porcentaje_ajuste1; // Con porcentaje de Ajuste
          $montoCalculado=($precioRegistradoAux2*($porcentPrecios/100)) * $dato_porcentaje_ajuste1; // Con porcentaje de Ajuste
        }else{
          $numeroCuentas="(".$numeroCuentas.")";
          $precioRegistradoAux2=($row_partidas['monto_calculado']*$nAuditorias);
          if($yyyy>1){
            for ($anioAumento=2; $anioAumento <= $yyyy; $anioAumento++) { 
              $sumaPrecioRegistrado2=$precioRegistradoAux2*$porCre;
              $precioRegistradoAux2=$precioRegistradoAux2+$sumaPrecioRegistrado2;
            }
          }
          $montoPresupuestoFila2Original=$precioRegistradoAux2;
          $montoPresupuestoFila2 = $montoPresupuestoFila2Original * $dato_porcentaje_ajuste1; // Con porcentaje de Ajuste
          $montoCalculado=($precioRegistradoAux2*($porcentPrecios/100)) * $dato_porcentaje_ajuste1; // Con porcentaje de Ajuste
        }
           $index_titulo=$index_titulo+1;
           $id_columna = $yyyy.$index_titulo;
           $html.='<tr class="bg-info text-white">'.
                      '<td class="font-weight-bold text-left">&nbsp;&nbsp; '.$row_partidas['nombre'].' '.$numeroCuentas.'</td>'.
                      '<td class="text-right font-weight-bold" id="tc1'.$id_columna.'">'.number_format($montoPresupuestoFila2Original, 2, '.', ',').'</td>'.
                      '<td class="text-right font-weight-bold" id="tc2'.$id_columna.'">'.number_format($montoPresupuestoFila2, 2, '.', ',').'</td>'.
                      '<td class="text-right font-weight-bold" id="tc3'.$id_columna.'">'.number_format($montoCalculado, 2, '.', ',').'</td>'.
                      '<td class="text-right font-weight-bold" id="tc4'.$id_columna.'">'.number_format($montoCalculado/$usd, 2, '.', ',').'</td>'.
                      '<td class="text-right font-weight-bold" id="tc5'.$id_columna.'">'.number_format($montoCalculado/$usd, 2, '.', ',').'</td>';
          $html.='</tr>';
         }else{
           $html.='<tr class="bg-success text-white">'.
                      '<td class="font-weight-bold text-left">&nbsp;&nbsp; '.$row_partidas['nombre'].' '.$numeroCuentas.'</td>'.
                      '<td class="text-right font-weight-bold"></td><td></td><td></td><td></td><td></td>';
          $html.='</tr>';
         } 
        if($row_partidas['tipo_calculo']==1){
            $query_cuentas="SELECT pc.*,pp.cod_partidapresupuestaria FROM plan_cuentas pc join partidaspresupuestarias_cuentas pp on pc.codigo=pp.cod_cuenta where pp.cod_partidapresupuestaria=$codPartida order by pc.codigo";
            // LISTA DE PLAN DE CUENTAS
            // echo $query_cuentas."</br>";
            $stmt_cuentas = $dbh->prepare($query_cuentas);
            $stmt_cuentas->execute();
            while ($row_cuentas = $stmt_cuentas->fetch(PDO::FETCH_ASSOC)) {
               $tipoSim=obtenerValorConfiguracion(13);
               $mesActual=date("m");
                $valorConfiguracionTCPTCS=obtenerValorConfiguracion(52);
                //CASO ESPECIAL: TVR 5291 obtiene area de TCP 39
                $grupoArea = $grupoArea==5291 ? 39 : $grupoArea;
               if($valorConfiguracionTCPTCS!=1){
                $monto=ejecutadoEgresosMes(0,$anio,12,$grupoArea,1,$row_cuentas['numero']);
                //$monto=($monto/12);
               }else{
                $monto=ejecutadoEgresosMes(0,$anio,12,$grupoArea,1,$row_cuentas['numero']);
                //$monto=ejecutadoEgresosMes($grupoUnidad,((int)$anio-1),$mesActual,$grupoArea,0,$row_cuentas['numero']);
               }
                $precioRegistradoAux3=$monto;
                /**********************************************************************************************************/
                // * Si no se tiene la PROPUESTA GESTIÓN - Mantiene incremento 15% desde el segundo AÑO
                if(empty($propuesta_gestion) && empty($propuesta_gestion2) && empty($propuesta_gestion3)){
                    // Procesando desde el "Segundo Año"
                    if($yyyy>1){
                        for ($anioAumento=2; $anioAumento <= $yyyy; $anioAumento++) { 
                            $sumaPrecioRegistrado3 = $precioRegistradoAux3 * $porCre;
                            $precioRegistradoAux3  = $precioRegistradoAux3 + $sumaPrecioRegistrado3;
                        }
                    }
                }else{
                // * El incremento solo se aplica cuando la gestión es futura en comparación con la gestión actual
                    // Procesando gestiones futuras
                    if($yyyy>1){
                        for ($anioAumento = 1; $anioAumento <= $control_anio; $anioAumento++) { 
                            $sumaPrecioRegistrado3 = $precioRegistradoAux3 * $porCre;
                            $precioRegistradoAux3  = $precioRegistradoAux3 + $sumaPrecioRegistrado3;
                        }
                    }
                }
                /**********************************************************************************************************/
                // // YA NO SE UTILIZA ESTE FORMATO DE PROCESAMIENTO
                // if($yyyy>1){
                //     for ($anioAumento=2; $anioAumento <= $yyyy; $anioAumento++) { 
                //         $sumaPrecioRegistrado3 = $precioRegistradoAux3*$porCre;
                //         $precioRegistradoAux3  = $precioRegistradoAux3+$sumaPrecioRegistrado3;
                //     }
                // }
                /**********************************************************************************************************/

                // MONTO INICIAL
                $montoPresupuestoFila3Original = $precioRegistradoAux3;
                // Porcentaje de Ajuste 1
                $montoPresupuestoFila2         = $precioRegistradoAux3 * $dato_porcentaje_ajuste1;
                // Porcentaje de Ajuste 2
                $montoPresupuestoFila3         = $montoPresupuestoFila2 * $dato_porcentaje_ajuste2;
                // FINAL
                $montoCal                      = $montoPresupuestoFila3*($porcentPrecios/100);

                // if($montoPresupuestoFila3==null){$montoPresupuestoFila3=0;}
                //$montoCal=costoModulo($monto,$mes);
                //$montoCal=$monto*($porcentPrecios/100);
                $html.='<tr class="">'.
                      '<td class="font-weight-bold text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$row_cuentas['nombre'].'</td>';
                    if($tipoCosto==1){
                      $html.='<td class="text-right text-muted">'.number_format($montoPresupuestoFila3Original, 2, '.', ',').'</td>';
                      $columna_1 += $montoPresupuestoFila3Original;
                    }  
                $html.='<td class="text-right text-muted">'.number_format($montoPresupuestoFila2, 2, '.', ',').'</td>'.
                        '<td class="text-right text-muted">'.number_format($montoPresupuestoFila3, 2, '.', ',').'</td>'.
                        '<td class="text-right text-muted">'.number_format($montoCal, 2, '.', ',').'</td>'.
                        '<td class="text-right text-muted">'.number_format($montoCal/$usd, 2, '.', ',').'</td>';
                $html.='</tr>';

                $columna_2 += $montoPresupuestoFila2;
                $columna_3 += $montoPresupuestoFila3;
                $columna_4 += $montoCal;
                $columna_5 += $montoCal/$usd;
            }
          }else{            
          if(!isset($_GET['anio'])){
            
            $query_cuentas=obtenerDetalleSimulacionCostosPartidaServicio($codigo,$codPartida);
          }else{
           $query_cuentas=obtenerDetalleSimulacionCostosPartidaServicioPeriodo($codigo,$codPartida,$yyyy); 
          } 
            
            $montoSimulacion=0;
            while ($row_cuentas = $query_cuentas->fetch(PDO::FETCH_ASSOC)) {
              $montoCalOriginal=$row_cuentas['monto_total'];
              $montoCal=$row_cuentas['monto_total'] * $dato_porcentaje_ajuste1; // Con Porcentaje de Ajuste
              $montoSimulacion+=$row_cuentas['monto_total'];
              
              $bandera=$row_cuentas['habilitado'];
              $cantidadDetalle=$row_cuentas['cantidad'];

              //obtener la cantidad real 
              $bgFila="";
              if($bandera==0){
                 $bgFila="text-danger";   
                
              }else{
                $montoTotales2+=$row_cuentas['monto_total'];
                if($cantidadDetalle==0){
                  $cantidadDetalle=1;
                }
                
                $montoTotales2Alumno+=$montoCal/$cantidadDetalle;
                $html.='<tr class="'.$bgFila.'">'.
                      '<td class="font-weight-bold text-left small">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$row_cuentas['nombre'].' / '.$row_cuentas['glosa'].' ('.$tituloItem.')</td>';
               if($tipoCosto==1){
                 $html.='<td class="text-right text-muted">'.number_format($montoCal, 2, '.', ',').'</td>';
                 $columna_1 += $montoCal;
                }         

               $html.='<td class="text-right text-muted">'.number_format($montoCalOriginal, 2, '.', ',').'</td>'.
                      '<td class="text-right text-muted">'.number_format($montoCal/$usd, 2, '.', ',').'</td>';
                      if($tipoCosto!=1){
                        $html.='<td class="text-right text-muted">'.number_format($montoCal/$cantidadDetalle, 2, '.', ',').'</td><td class="text-right text-muted">'.number_format(($montoCal/$cantidadDetalle)/$usd, 2, '.', ',').'</td><td class="text-right text-muted">'.$cantidadDetalle.'</td>';
                      }
                      
                $html.='</tr>';
              }
              
              $columna_2 += $montoCalOriginal/$cantidadDetalle;
              $columna_3 += $montoCal/$cantidadDetalle;
              $columna_4 += ($montoCal/$cantidadDetalle)/$usd;
            }
          }
          // TOTAL FINAL
          $total_final1 += $columna_1; 
          $total_final2 += $columna_2;
          $total_final3 += $columna_3;
          $total_final4 += $columna_4;
          $total_final5 += $columna_5;

            if($row_partidas['tipo_calculo']==1){
                echo '<script>' .
                        // Obtener el elemento por su ID y modificar su contenido
                        'document.getElementById("tc1' . $id_columna . '").innerHTML = "' . number_format($columna_1, 2, '.', ',') . '";' .
                        'document.getElementById("tc2' . $id_columna . '").innerHTML = "' . number_format($columna_2, 2, '.', ',') . '";' .
                        'document.getElementById("tc3' . $id_columna . '").innerHTML = "' . number_format($columna_3, 2, '.', ',') . '";' .
                        'document.getElementById("tc4' . $id_columna . '").innerHTML = "' . number_format($columna_4, 2, '.', ',') . '";' .
                        'document.getElementById("tc5' . $id_columna . '").innerHTML = "' . number_format($columna_5, 2, '.', ',') . '";' .
                        '</script>';
            }
     }
}
    if($tipoCosto==1){
            $montoTotalesPresupuestoOriginal = $montoTotalesPresupuesto;
            $montoTotalesPresupuesto         = $montoTotalesPresupuestoOriginal * $dato_porcentaje_ajuste1;
          //  $html.='<tr class="bg-plomo">'.
          //             '<td class="font-weight-bold text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Total Fijos</td>'.
          //             '<td class="text-right text-muted font-weight-bold">'.number_format($montoTotalesPresupuestoOriginal, 2, '.', ',').'</td>'.
          //             '<td class="text-right text-muted font-weight-bold">'.number_format($montoTotalesPresupuesto, 2, '.', ',').'</td>'.
          //             '<td class="text-right text-muted font-weight-bold">'.number_format($montoTotales, 2, '.', ',').'</td>'.
          //             '<td class="text-right text-muted font-weight-bold">'.number_format($montoTotales/$usd, 2, '.', ',').'</td>';
          //       $html.='</tr>';     
          $html.='<tr class="bg-plomo">'.
                      '<td class="font-weight-bold text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Total Fijos</td>'.
                      '<td class="text-right text-muted font-weight-bold">'.number_format($total_final1, 2, '.', ',').'</td>'.
                      '<td class="text-right text-muted font-weight-bold">'.number_format($total_final2, 2, '.', ',').'</td>'.
                      '<td class="text-right text-muted font-weight-bold">'.number_format($total_final3, 2, '.', ',').'</td>'.
                      '<td class="text-right text-muted font-weight-bold">'.number_format($total_final4, 2, '.', ',').'</td>'.
                      '<td class="text-right text-muted font-weight-bold">'.number_format($total_final5, 2, '.', ',').'</td>';
                $html.='</tr>';    
         }else{
           $html.='<tr class="bg-plomo">'.
                      '<td class="font-weight-bold text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Total Variables</td>'.
                      '<td class="text-right text-muted font-weight-bold">'.number_format($montoTotales2, 2, '.', ',').'</td>'.
                      '<td class="text-right text-muted font-weight-bold">'.number_format($montoTotales2/$usd, 2, '.', ',').'</td>'.
                      '<td class="text-right text-muted font-weight-bold">'.number_format($montoTotales2Alumno, 2, '.', ',').'</td>'.
                      '<td class="text-right text-muted font-weight-bold">'.number_format($montoTotales2Alumno/$usd, 2, '.', ',').'</td>'.
                      '<td class="text-right text-muted font-weight-bold"></td>';
                $html.='</tr>';
          /* $html.='<tr class="bg-plomo">'.
                      '<td class="font-weight-bold text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Total Honorarios</td>'.
                      '<td class="text-right text-muted font-weight-bold"></td>'.
                      '<td class="text-right text-muted font-weight-bold"></td>'.
                      '<td class="text-right text-muted font-weight-bold">'.number_format(costoVariablesHonorariosSimulacionServicio($codigo,$yyyy), 2, '.', ',').'</td>'.
                      '<td class="text-right text-muted font-weight-bold">'.number_format(costoVariablesHonorariosSimulacionServicio($codigo,$yyyy)/$usd, 2, '.', ',').'</td>'.
                      '<td class="text-right text-muted font-weight-bold"></td>';
                $html.='</tr>'; */    
         } 

echo $html;
?>      
  </table>
   
   <?php 
    //if(isset($_GET['verSim'])){
      ?>
     <h4 class="font-weight-bold"><center>HONORARIOS <?=$tituloItem?> </center></h4>  
     <table class="table table-condensed table-bordered">
         <tr class="text-white <?=$bgClase?>">
            <td width="50%">Descripción</td>
            <td>Monto x Persona BOB</td>
            <td>Monto x Persona USD</td>
         </tr>
      <?php  
    $sql="SELECT s.*,t.nombre as tipo FROM simulaciones_servicios_auditores s join tipos_auditor t on s.cod_tipoauditor=t.codigo where s.cod_simulacionservicio=$codigo and s.cod_anio=$yyyy and s.habilitado=1 order by t.nro_orden,s.descripcion";
    $stmt=$dbh->prepare($sql);
    $stmt->execute();
    $iii=1;$totalAuditor=0;$totalAuditorUSD=0;
     while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigoTipo=$row['codigo'];
      $nombreTipo=$row['descripcion']; //$row['tipo'];
      $cantidadTipo=$row['cantidad_editado'];
      $diasTipo=$row['dias'];
      $codExtLoc=$row['cod_externolocal'];
      $montoAuditorIndUSD=number_format($row['monto']/$usd,2,".","");
      $montoAuditorInd=number_format($row['monto'],2,".","");
      $montoAuditor=$row['monto']*$diasTipo;
      
      $montoAuditorUSD=number_format($montoAuditor/$usd,2,".","");
      $montoAuditor=number_format($montoAuditor,2,".","");  
      $totalAuditor+=$montoAuditor;
      $totalAuditorUSD+=($montoAuditor/$usd);
      $cantPre=obtenerCantidadSimulacionDetalleAuditorPeriodo($codigo,$codigoTipo,$anio);
      $diasPre=obtenerDiasSimulacionDetalleAuditorPeriodo($codigo,$codigoTipo,$anio);
      if($cantidadTipo<$cantPre){
        $cantPre=$cantidadTipo;
      }
      if($diasTipo<$diasPre){
        $diasPre=$diasTipo;
      }

      if($row['cod_tipoauditor']==-100){
         $nombreTipo="<b class='text-danger'>".$nombreTipo."</b>";
      }
      $estiloFilaTextoAud="";
      $existeCostoVariableSolAu=obtenerCostoVariableSolicitadoPropuestaTCPTCS($codigo,$codigoTipo,2);
       ?>
       <tr>
         <td class="text-left"><?=$nombreTipo?></td>      
         <td class="text-right small"><?=$montoAuditor?></td>
         <td class="text-right small"><?=$montoAuditorUSD?></td>
        <tr> 
      <?php }
      ?>    
      <tr>
         <td class="text-left bg-plomo font-weight-bold">Totales Honorarios</td>      
         <td class="text-right small bg-plomo font-weight-bold"><?=number_format($totalAuditor,2,".","")?></td>
         <td class="text-right small bg-plomo font-weight-bold"><?=number_format($totalAuditorUSD,2,".","")?></td>
        <tr>
     </table>    
      <?php
    //}  
   if($tipoCosto!=1){
        ?><!--<div class="row div-center"><h4 class="font-weight-bold"><small>N&uacute;mero de personal registrado:</small> <small class="text-success"><?=$alumnos?></small></h4></div>--><?php 
    }
   ?></div><?php    
 }     

}
