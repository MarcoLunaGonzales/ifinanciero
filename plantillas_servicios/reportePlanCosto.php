<?php
set_time_limit(0);
session_start();
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../styles.php';
require_once '../functionsGeneral.php';
require_once '../functionsPOSIS.php';
require_once '../functions.php';
require_once 'configModule.php';
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
$anio=date("Y");
$codigo=$_GET['cod'];
if(isset($_GET['q'])){
  $q=$_GET['q'];
}
$cantidadPersonal=obtenerCantidadPersonalPlantilla($codigo);
$codArea=obtenerCodigoAreaPlantillaServicio($codigo);
$query1="select pgd.cod_plantillagruposervicio,pc.cod_unidadorganizacional,pc.cod_area,pgc.nombre,pgc.cod_tiposervicio,sum(pgd.monto_local) as local,sum(pgd.monto_externo) as externo,sum(pgd.monto_calculado) as calculado from plantillas_gruposerviciodetalle pgd join partidas_presupuestarias pp on pgd.cod_partidapresupuestaria=pp.codigo
join plantillas_gruposervicio pgc on pgd.cod_plantillagruposervicio=pgc.codigo
join plantillas_servicios pc on pgc.cod_plantillaservicio=pc.codigo 
where pc.codigo=$codigo";

 ?><div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
             <form action="saveComplemento.php" method="post"> 
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="card-icon bg-blanco">
                    <img class="" width="40" height="40" src="../assets/img/logoibnorca.png">
                  </div>
                  <div class="float-right col-sm-2"><h6 class="card-title">Exportar como:</h6></div>
                  <h4 class="card-title text-center"><strong>PLANTILLA</strong></h4>
                  <h4 class="card-title text-center"><b>CALCULO DE PRESUPUESTO PARA PLANTILLAS DE SERVICIOS</b></h4>
                </div>    
                <div class="card-body">
                  <div class="col-sm-4 float-right">
                     <table class="table table-bordered table-condensed text-small">
                           <thead>
                               <tr class="bg-info text-white">
                                 <th>Cantidad Personal</th>
                                 <th>Cantidad Auditorias Gesti&oacute;n</th>
                               </tr>
                           </thead>
                           <tbody>
                            <tr>
                              <td><?=$cantidadPersonal?></td>
                              <?php 
                              if($codArea==39){
                                 $mes=obtenerValorConfiguracion(17);
                              }else{
                                 $mes=obtenerValorConfiguracion(18);
                              }
                              
                              ?><td><?=$mes?></td>
                              </tr>     
                           </tbody>
                        </table>    
                  </div>
                  <div class="table-responsive" id="datos_pantilla_costo">
                        
                       

     <?php 
    $html='<table id="libro_mayor_rep" class="table table-bordered table-condensed" style="width:100%">'.
            '<thead class="bg-table-primary text-white">'.
            '<tr class="text-center">'.
              '<th class="text-left"><small>DETALLE</small></th>'.
              '<th><small>IMPORTE TOTAL GENERADO</small></th>'.
              '<th><small>COSTO POR AUDITORIA</small></th>'.
            '</tr>'.
           '</thead>'.
           '<tbody>'; 

         $html.='<tr class="bg-table-primary text-white">'.
               '<td>COSTO FIJO</td><td></td><td></td>'.
               '</tr>';  
  $query2=$query1." and pgc.cod_tiposervicio=1 GROUP BY pgd.cod_plantillagruposervicio order by pgd.cod_plantillagruposervicio";
  $stmt = $dbh->prepare($query2);
  $stmt->execute();
  $totalImporte=0;$totalModulo=0;$totalLocal=0;$totalExterno=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $codGrupo=$row['cod_plantillagruposervicio'];$grupoUnidad=$row['cod_unidadorganizacional'];$grupoArea=$row['cod_area'];
    if($row['calculado']==$row['local']){
      $montoCalculadoTit=$row['calculado'];
    }else{
      $montoCalculadoTit=$row['local'];
    }
    $importe_grupo=(float)$montoCalculadoTit*$mes;
    $totalImporte+=$importe_grupo;
    $totalModulo+=$montoCalculadoTit;
    $totalLocal+=$row['local'];
    $totalExterno+=$row['externo'];
    
     $html.='<tr class="bg-plomo">'.
                      '<td class="font-weight-bold text-left">'.$row['nombre'].'</td>'.
                      '<td class="text-right font-weight-bold">'.number_format($importe_grupo, 2, '.', ',').'</td>'.
                      '<td class="text-right font-weight-bold">'.number_format($montoCalculadoTit, 2, '.', ',').'</td>';
                      /*'<td class="text-right font-weight-bold">'.number_format($row['local'], 2, '.', ',').'</td>'.
                      '<td class="text-right font-weight-bold">'.number_format($row['externo'], 2, '.', ',').'</td>';*/
      $html.='</tr>';
     $query_partidas="select pgd.cod_plantillagruposervicio,pp.nombre,pgd.cod_partidapresupuestaria,pgd.tipo_calculo,pgd.monto_local,pgd.monto_externo,pgd.monto_calculado from plantillas_gruposerviciodetalle pgd join partidas_presupuestarias pp on pgd.cod_partidapresupuestaria=pp.codigo join plantillas_gruposervicio pgc on pgd.cod_plantillagruposervicio=pgc.codigo where pgd.cod_plantillagruposervicio=$codGrupo";

     $stmt_partidas = $dbh->prepare($query_partidas);
     $stmt_partidas->execute();

     while ($row_partidas = $stmt_partidas->fetch(PDO::FETCH_ASSOC)) {

         $codPartida=$row_partidas['cod_partidapresupuestaria'];
         $numeroCuentas=contarPresupuestoCuentas($codPartida);

        if($row_partidas['tipo_calculo']!=1){
          $numeroCuentas="(Manual)";
          $signoClass="add_circle";
          $estiloFila="text-muted";
          $funcionOnclick='filasPresupuesto('.$codPartida.')';
          $importe_partida=(float)$row_partidas['monto_local']*$mes;
          $montoCalculado=$row_partidas['monto_local'];
        }else{
          $numeroCuentas="(".$numeroCuentas.")";
          $signoClass="add_circle";
          $estiloFila="fila-button";
          $funcionOnclick='filasPresupuesto('.$codPartida.')';
          $importe_partida=(float)$row_partidas['monto_calculado']*$mes;
          $montoCalculado=$row_partidas['monto_calculado'];
        }
          $html.='<tr onclick="'.$funcionOnclick.'" class="'.$estiloFila.'">'.
                      '<td class="font-weight-bold text-left">&nbsp;&nbsp;<i class="material-icons icon-sm simbolo'.$codPartida.'">'.$signoClass.'</i> '.$row_partidas['nombre'].' '.$numeroCuentas.'</td>'.
                      '<td class="text-right font-weight-bold">'.number_format($importe_partida, 2, '.', ',').'</td>'.
                      '<td class="text-right font-weight-bold">'.number_format($montoCalculado, 2, '.', ',').'</td>';
      
          $html.='</tr>';
          
          if($row_partidas['tipo_calculo']==1){
            $query_cuentas="SELECT pc.*,pp.cod_partidapresupuestaria FROM plan_cuentas pc join partidaspresupuestarias_cuentas pp on pc.codigo=pp.cod_cuenta where pp.cod_partidapresupuestaria=$codPartida order by pc.codigo";
            
            $stmt_cuentas = $dbh->prepare($query_cuentas);
            $stmt_cuentas->execute();
            while ($row_cuentas = $stmt_cuentas->fetch(PDO::FETCH_ASSOC)) {
               $tipoSim=obtenerValorConfiguracion(13);
               $mesActual=date("m");
               if($tipoSim==1){
                $monto=ejecutadoEgresosMes($grupoUnidad,((int)$anio-1),12,$grupoArea,1,$row_cuentas['numero']);
                $monto=($monto/12);
               }else{
                $monto=ejecutadoEgresosMes($grupoUnidad,((int)$anio-1),$mesActual,$grupoArea,0,$row_cuentas['numero']);
               }
                
                if($monto==null){$monto=0;}
                $montoCal=costoModulo($monto,$mes);
                $html.='<tr class="cuenta'.$codPartida.'" style="display:none">'.
                      '<td class="font-weight-bold text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$row_cuentas['nombre'].'</td>'.
                      '<td class="text-right text-muted">'.number_format($monto, 2, '.', ',').'</td>'.
                      '<td class="text-right text-muted">'.number_format($montoCal, 2, '.', ',').'</td>';
                $html.='</tr>';
            }
          }else{
            // mostrar el detalle por de la plantilla cuando sea manual

            $query_cuentas=obtenerDetallePlantillaServicioPartida($codigo,$codPartida);
            while ($row_cuentas = $query_cuentas->fetch(PDO::FETCH_ASSOC)) {
              $monto=$row_cuentas['monto_total']*$mes;
              $montoCal=$row_cuentas['monto_total'];

                $html.='<tr class="cuenta'.$codPartida.'" style="display:none">'.
                      '<td class="font-weight-bold text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$row_cuentas['nombre'].' / '.$row_cuentas['glosa'].'</td>'.
                      '<td class="text-right text-muted">'.number_format($monto, 2, '.', ',').'</td>'.
                      '<td class="text-right text-muted">'.number_format($montoCal, 2, '.', ',').'</td>';
                $html.='</tr>';
            }
          }

     } 
 }


$html.='<tr class="bg-table-primary text-white">'.
                      '<td class="font-weight-bold text-left">TOTAL COSTO FIJO</td>'.
                      '<td class="text-right" id="fijo_importe">'.number_format($totalImporte, 2, '.', ',').'</td>'.
                      '<td class="text-right" id="fijo_modulo">'.number_format($totalModulo, 2, '.', ',').'</td>';
                    $html.='</tr>';

  $html.='<tr class="bg-table-primary text-white">'.
               '<td>COSTO VARIABLE</td><td></td><td></td>'.
               '</tr>';  
  $query2=$query1." and pgc.cod_tiposervicio=2 GROUP BY pgd.cod_plantillagruposervicio order by pgd.cod_plantillagruposervicio";
  $stmt = $dbh->prepare($query2);
  $stmt->execute();
  $totalImporte2=0;$totalModulo2=0;$totalLocal2=0;$totalExterno2=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $codGrupo=$row['cod_plantillagruposervicio'];$grupoUnidad=$row['cod_unidadorganizacional'];$grupoArea=$row['cod_area'];
    if($row['calculado']==$row['local']){
      $montoCalculadoTit=$row['calculado'];
    }else{
      $montoCalculadoTit=$row['local'];
    }
    $importe_grupo=(float)$montoCalculadoTit*$mes;
    $totalImporte2+=$importe_grupo;
    $totalModulo2+=$montoCalculadoTit;
    $totalLocal2+=$row['local'];
    $totalExterno2+=$row['externo'];   
     $html.='<tr class="bg-plomo">'.
                      '<td class="font-weight-bold text-left">'.$row['nombre'].'</td>'.
                      '<td class="font-weight-bold text-left">-</td>'.//'<td class="text-right font-weight-bold">'.number_format($importe_grupo, 2, '.', ',').'</td>'.
                      '<td class="text-right font-weight-bold">'.number_format($montoCalculadoTit, 2, '.', ',').'</td>';
      $html.='</tr>';
     $query_partidas="select pgd.cod_plantillagruposervicio,pp.nombre,pgd.cod_partidapresupuestaria,pgd.tipo_calculo,pgd.monto_local,pgd.monto_externo,pgd.monto_calculado from plantillas_gruposerviciodetalle pgd join partidas_presupuestarias pp on pgd.cod_partidapresupuestaria=pp.codigo join plantillas_gruposervicio pgc on pgd.cod_plantillagruposervicio=pgc.codigo where pgd.cod_plantillagruposervicio=$codGrupo";

     $stmt_partidas = $dbh->prepare($query_partidas);
     $stmt_partidas->execute();

     while ($row_partidas = $stmt_partidas->fetch(PDO::FETCH_ASSOC)) {

         $codPartida=$row_partidas['cod_partidapresupuestaria'];
         $numeroCuentas=contarPresupuestoCuentas($codPartida);
        if($row_partidas['tipo_calculo']!=1){
          $numeroCuentas="(Manual)";
          $signoClass="add_circle";
          $estiloFila="text-muted";
          $funcionOnclick='filasPresupuesto('.$codPartida.')';
          $importe_partida=(float)$row_partidas['monto_local']*$mes;
          $montoCalculado=$row_partidas['monto_local'];
        }else{
          $numeroCuentas="(".$numeroCuentas.")";
          $signoClass="add_circle";
          $estiloFila="fila-button";
          $funcionOnclick='filasPresupuesto('.$codPartida.')';
          $importe_partida=(float)$row_partidas['monto_calculado']*$mes;
          $montoCalculado=$row_partidas['monto_calculado'];
        }
          $html.='<tr onclick="'.$funcionOnclick.'" class="'.$estiloFila.'">'.
                      '<td class="font-weight-bold text-left">&nbsp;&nbsp;<i class="material-icons icon-sm simbolo'.$codPartida.'">'.$signoClass.'</i> '.$row_partidas['nombre'].' '.$numeroCuentas.'</td>'.
                      '<td class="font-weight-bold text-left">-</td>'.//'<td class="text-right font-weight-bold">'.number_format($importe_partida, 2, '.', ',').'</td>'.
                      '<td class="text-right font-weight-bold">'.number_format($montoCalculado, 2, '.', ',').'</td>';
                
          $html.='</tr>';
          
          if($row_partidas['tipo_calculo']==1){
            $query_cuentas="SELECT pc.*,pp.cod_partidapresupuestaria FROM plan_cuentas pc join partidaspresupuestarias_cuentas pp on pc.codigo=pp.cod_cuenta where pp.cod_partidapresupuestaria=$codPartida order by pc.codigo";
            
            $stmt_cuentas = $dbh->prepare($query_cuentas);
            $stmt_cuentas->execute();
            while ($row_cuentas = $stmt_cuentas->fetch(PDO::FETCH_ASSOC)) {
                 $tipoSim=obtenerValorConfiguracion(13);
                 $mesActual=date("m");
               if($tipoSim==1){
                $monto=ejecutadoEgresosMes($grupoUnidad,((int)$anio-1),12,$grupoArea,1,$row_cuentas['numero']);
                $monto=($monto/12);
               }else{
                $monto=ejecutadoEgresosMes($grupoUnidad,((int)$anio-1),$mesActual,$grupoArea,0,$row_cuentas['numero']);
               }
                if($monto==null){$monto=0;}
                $montoCal=costoModulo($monto,$mes);
                $html.='<tr class="cuenta'.$codPartida.'" style="display:none">'.
                      '<td class="font-weight-bold text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$row_cuentas['nombre'].'</td>'.
                      '<td class="font-weight-bold text-left">-</td>'.//'<td class="text-right text-muted">'.number_format($monto, 2, '.', ',').'</td>'.
                      '<td class="text-right text-muted">'.number_format($montoCal, 2, '.', ',').'</td>';
                $html.='</tr>';
            }
          }else{
            // mostrar el detalle por de la plantilla cuando sea manual

            $query_cuentas=obtenerDetallePlantillaServicioPartida($codigo,$codPartida);
            $montoSimulacion=0;
            while ($row_cuentas = $query_cuentas->fetch(PDO::FETCH_ASSOC)) {
              $monto=$row_cuentas['monto_total']*$mes;
              $montoCal=$row_cuentas['monto_total'];
              $montoSimulacion+=$row_cuentas['monto_total'];
                $html.='<tr class="cuenta'.$codPartida.'" style="display:none">'.
                      '<td class="font-weight-bold text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$row_cuentas['nombre'].' / '.$row_cuentas['glosa'].'</td>'.
                      '<td class="font-weight-bold text-left">-</td>'.//'<td class="text-right text-muted">'.number_format($monto, 2, '.', ',').'</td>'.
                      '<td class="text-right text-muted">'.number_format($montoCal, 2, '.', ',').'</td>';
                $html.='</tr>';
            }
          }

     } 
 }


$html.='<tr class="bg-table-primary text-white">'.
                      '<td class="font-weight-bold text-left">TOTAL COSTO VARIABLE</td>'.
                      '<td class="font-weight-bold text-left"></td>'.//'<td class="text-right" id="fijo_importe">'.number_format($totalImporte2, 2, '.', ',').'</td>'.
                      '<td class="text-right" id="fijo_modulo">'.number_format($totalModulo2, 2, '.', ',').'</td>';
                    $html.='</tr>';
$totalIbnorca=$totalLocal+$totalLocal2;
$totalFuera=$totalExterno+$totalExterno2;
$html.='<tr class="bg-table-total">'.
                      '<td class="font-weight-bold text-left">COSTO TOTAL</td>'.
                      '<td></td>'.
                      '<td class="text-right font-weight-bold" id="ibnorca">'.number_format($totalIbnorca, 2, '.', ',').'</td>';
                    $html.='</tr>';
$html.='</tbody>';
$html.=    '</table>';
echo $html;

?>

                </div> 
                <br><br>                
                </div>
                <div class="card-footer bg-white col-sm-12">
                   <div class="row col-sm-12">
                    <div class="col-sm-3">
                       <div class="form-group">
                        <input class="form-control" type="hidden" name="grupo_area" value="<?=$grupoArea?>" id="grupo_area">
                        <input class="form-control" type="hidden" name="grupo_unidad" value="<?=$grupoUnidad?>" id="grupo_unidad"> 
                          <!-- <button type="submit" class="btn btn-info">Guardar</button>-->
                        <?php 
                        if(isset($_GET['q'])){
                         ?><a href="../<?=$urlList;?>&q=<?=$q?>" class="btn btn-danger">Volver</a><?php
                        }else{
                         ?><a href="../<?=$urlList;?>" class="btn btn-danger">Volver</a><?php
                        }
                        ?>   
                           
                       </div>
                     </div>
                </div>
                
              </div><!--fin DIV card-->

             </form>
            </div>
          </div>  
        </div>
    </div>
<?php require_once 'modal.php';?>