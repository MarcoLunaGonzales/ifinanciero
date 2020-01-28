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
$detallePlantilla=obtenerPreciosPlantillaCosto($codigo);
$alumnosPlantilla=obtenerPlantillaCostoAlumnos($codigo);
$query1="select pgd.cod_plantillagrupocosto,pc.cod_unidadorganizacional,pc.cod_area,pgc.nombre,pgc.cod_tipocosto,sum(pgd.monto_local) as local,sum(pgd.monto_externo) as externo,sum(pgd.monto_calculado) as calculado from plantillas_grupocostodetalle pgd join partidas_presupuestarias pp on pgd.cod_partidapresupuestaria=pp.codigo
join plantillas_gruposcosto pgc on pgd.cod_plantillagrupocosto=pgc.codigo
join plantillas_costo pc on pgc.cod_plantillacosto=pc.codigo 
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
                  <h4 class="card-title text-center"><b>CALCULO DE PRESUPUESTO PARA PLANTILLAS DE COSTOS</b></h4>
                </div>    
                <div class="card-body">
                  <div class="col-sm-4 float-right">
                     <table class="table table-bordered table-condensed text-small">
                           <thead>
                               <tr class="bg-info text-white">
                                 <th>Cantidad Alumnos</th>
                                 <th>Cantidad Cursos</th>
                                 <!--<th>Semana</th>-->
                               </tr>
                           </thead>
                           <tbody>
                            <tr>
                              <td><?=$alumnosPlantilla?></td>
                              <?php 
                              $mes=obtenerValorConfiguracion(6);
                              ?><td><?=$mes?></td>
                              </tr>     
                           </tbody>
                        </table>    
                  </div>
                  <!--<div class="row">
                    <div class="col-sm-4">
                    <div class="form-group">
                        <label class="bmd-label-static">Valor Mes</label>
                        <input class="form-control" type="number" name="mes_plantilla" value="<?=$mes?>" id="mes_plantilla" required />
                        <input class="form-control" type="hidden" name="codigo_plantilla" value="<?=$codigo?>" id="codigo_plantilla" required>
                    </div>
                    <div id="mensaje_process"></div>
                  </div>
                  <div class="col-sm-3">
                    <div class="btn-group">
                        <button id="calcular" onclick="calcularDatosPlantilla()" class="btn btn-info btn-sm">
                           <span class="btn-label">
                             <i class="material-icons">cached</i>
                           </span>Calcular
                         </button> 
                         <a href="" class="btn btn-default btn-sm">Restaurar</a>
                    </div>
                  </div>
                  </div>-->
                  <!--<h5 class="card-title"><strong>COSTOS DEL EVENTO DE CAPACITACION</strong></h5>-->
                  <div class="table-responsive" id="datos_pantilla_costo">
                        
                       

     <?php 
    $html='<table id="libro_mayor_rep" class="table table-bordered table-condensed" style="width:100%">'.
            '<thead class="bg-table-primary text-white">'.
            '<tr class="text-center">'.
              '<th class="text-left"><small>DETALLE</small></th>'.
              '<th><small>IMPORTE TOTAL MENSUAL</small></th>'.
              '<th><small>COSTO POR CURSO</small></th>'.
            '</tr>'.
           '</thead>'.
           '<tbody>'; 

         $html.='<tr class="bg-table-primary text-white">'.
               '<td>COSTO FIJO</td><td></td><td></td>'.
               '</tr>';  
  $query2=$query1." and pgc.cod_tipocosto=1 GROUP BY pgd.cod_plantillagrupocosto order by pgd.cod_plantillagrupocosto";
  $stmt = $dbh->prepare($query2);
  $stmt->execute();
  $totalImporte=0;$totalModulo=0;$totalLocal=0;$totalExterno=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $codGrupo=$row['cod_plantillagrupocosto'];$grupoUnidad=$row['cod_unidadorganizacional'];$grupoArea=$row['cod_area'];
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
     $query_partidas="select pgd.cod_plantillagrupocosto,pp.nombre,pgd.cod_partidapresupuestaria,pgd.tipo_calculo,pgd.monto_local,pgd.monto_externo,pgd.monto_calculado from plantillas_grupocostodetalle pgd join partidas_presupuestarias pp on pgd.cod_partidapresupuestaria=pp.codigo join plantillas_gruposcosto pgc on pgd.cod_plantillagrupocosto=pgc.codigo where pgd.cod_plantillagrupocosto=$codGrupo";

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
                      /*'<td class="text-right font-weight-bold">'.number_format($row_partidas['monto_local'], 2, '.', ',').'</td>'.
                      '<td class="text-right font-weight-bold">'.number_format($row_partidas['monto_externo'], 2, '.', ',').'</td>';*/
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
                     /*'<td class="text-right text-muted">'.number_format($montoCal, 2, '.', ',').'</td>'.
                      '<td class="text-right text-muted">'.number_format($montoCal, 2, '.', ',').'</td>';*/
                $html.='</tr>';
            }
          }else{
            // mostrar el detalle por de la plantilla cuando sea manual

            $query_cuentas=obtenerDetallePlantillaCostosPartida($codigo,$codPartida);
            while ($row_cuentas = $query_cuentas->fetch(PDO::FETCH_ASSOC)) {
              $monto=$row_cuentas['monto_total']*$mes;
              $montoCal=$row_cuentas['monto_total'];

                $html.='<tr class="cuenta'.$codPartida.'" style="display:none">'.
                      '<td class="font-weight-bold text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$row_cuentas['nombre'].' / '.$row_cuentas['glosa'].'</td>'.
                      '<td class="text-right text-muted">'.number_format($monto, 2, '.', ',').'</td>'.
                      '<td class="text-right text-muted">'.number_format($montoCal, 2, '.', ',').'</td>';
                      /*'<td class="text-right text-muted">'.number_format($montoCal, 2, '.', ',').'</td>'.
                      '<td class="text-right text-muted">'.number_format($montoCal, 2, '.', ',').'</td>';*/
                $html.='</tr>';
            }
          }

     } 
 }


$html.='<tr class="bg-table-primary text-white">'.
                      '<td class="font-weight-bold text-left">TOTAL COSTO FIJO</td>'.
                      '<td class="text-right" id="fijo_importe">'.number_format($totalImporte, 2, '.', ',').'</td>'.
                      '<td class="text-right" id="fijo_modulo">'.number_format($totalModulo, 2, '.', ',').'</td>';
                      /*'<td class="text-right" id="fijo_ibnorca">'.number_format($totalLocal, 2, '.', ',').'</td>'.
                      '<td class="text-right" id="fijo_fuera">'.number_format($totalExterno, 2, '.', ',').'</td>';*/
                    $html.='</tr>';

  $html.='<tr class="bg-table-primary text-white">'.
               '<td>COSTO VARIABLE</td><td></td><td></td>'.
               '</tr>';  
  $query2=$query1." and pgc.cod_tipocosto=2 GROUP BY pgd.cod_plantillagrupocosto order by pgd.cod_plantillagrupocosto";
  $stmt = $dbh->prepare($query2);
  $stmt->execute();
  $totalImporte2=0;$totalModulo2=0;$totalLocal2=0;$totalExterno2=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $codGrupo=$row['cod_plantillagrupocosto'];$grupoUnidad=$row['cod_unidadorganizacional'];$grupoArea=$row['cod_area'];
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
                      /*'<td class="text-right font-weight-bold">'.number_format($row['local'], 2, '.', ',').'</td>'.
                      '<td class="text-right font-weight-bold">'.number_format($row['externo'], 2, '.', ',').'</td>';*/
      $html.='</tr>';
     $query_partidas="select pgd.cod_plantillagrupocosto,pp.nombre,pgd.cod_partidapresupuestaria,pgd.tipo_calculo,pgd.monto_local,pgd.monto_externo,pgd.monto_calculado from plantillas_grupocostodetalle pgd join partidas_presupuestarias pp on pgd.cod_partidapresupuestaria=pp.codigo join plantillas_gruposcosto pgc on pgd.cod_plantillagrupocosto=pgc.codigo where pgd.cod_plantillagrupocosto=$codGrupo";

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
                      /*'<td class="text-right font-weight-bold">'.number_format($row_partidas['monto_local'], 2, '.', ',').'</td>'.
                      '<td class="text-right font-weight-bold">'.number_format($row_partidas['monto_externo'], 2, '.', ',').'</td>';*/
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
                      /*'<td class="text-right text-muted">'.number_format($montoCal, 2, '.', ',').'</td>'.
                      '<td class="text-right text-muted">'.number_format($montoCal, 2, '.', ',').'</td>';*/
                $html.='</tr>';
            }
          }else{
            // mostrar el detalle por de la plantilla cuando sea manual

            $query_cuentas=obtenerDetallePlantillaCostosPartida($codigo,$codPartida);
            $montoSimulacion=0;
            while ($row_cuentas = $query_cuentas->fetch(PDO::FETCH_ASSOC)) {
              $monto=$row_cuentas['monto_total']*$mes;
              $montoCal=$row_cuentas['monto_total'];
              $montoSimulacion+=$row_cuentas['monto_total'];
                $html.='<tr class="cuenta'.$codPartida.'" style="display:none">'.
                      '<td class="font-weight-bold text-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$row_cuentas['nombre'].' / '.$row_cuentas['glosa'].'</td>'.
                      '<td class="font-weight-bold text-left">-</td>'.//'<td class="text-right text-muted">'.number_format($monto, 2, '.', ',').'</td>'.
                      '<td class="text-right text-muted">'.number_format($montoCal, 2, '.', ',').'</td>';
                      /*'<td class="text-right text-muted">'.number_format($montoCal, 2, '.', ',').'</td>'.
                      '<td class="text-right text-muted">'.number_format($montoCal, 2, '.', ',').'</td>';*/
                $html.='</tr>';
            }
          }

     } 
 }


$html.='<tr class="bg-table-primary text-white">'.
                      '<td class="font-weight-bold text-left">TOTAL COSTO VARIABLE</td>'.
                      '<td class="font-weight-bold text-left"></td>'.//'<td class="text-right" id="fijo_importe">'.number_format($totalImporte2, 2, '.', ',').'</td>'.
                      '<td class="text-right" id="fijo_modulo">'.number_format($totalModulo2, 2, '.', ',').'</td>';
                      /*'<td class="text-right" id="fijo_ibnorca">'.number_format($totalLocal2, 2, '.', ',').'</td>'.
                      '<td class="text-right" id="fijo_fuera">'.number_format($totalExterno2, 2, '.', ',').'</td>';*/
                    $html.='</tr>';
$totalIbnorca=$totalLocal+$totalLocal2;
$totalFuera=$totalExterno+$totalExterno2;
$html.='<tr class="bg-table-total">'.
                      '<td class="font-weight-bold text-left">COSTO TOTAL</td>'.
                      '<td></td>'.
                      '<td class="text-right font-weight-bold" id="ibnorca">'.number_format($totalIbnorca, 2, '.', ',').'</td>';
                      /*'<td class="text-right font-weight-bold" id="fuera">'.number_format($totalFuera, 2, '.', ',').'</td>'*/
                    $html.='</tr>';
$html.='</tbody>';
$html.=    '</table>';
echo $html;

?>

                </div> 
                <br><br>
                <!--<div class="row">
                  <div class="col-sm-6 div-center">
                    <table class="table table-condensed table-striped table-bordered">
                        <thead>
                          <tr>
                            <th width="10%">#</th>
                            <th width="40%">Precio Venta Ibnorca</th>
                            <th width="40%">Precio Venta Fuera Ibnorca</th>
                            <th width="10%">Actions</th>
                          </tr>
                        </thead>
                        <tbody id="contenido_precio">
                          <tr>
                            <td>Nuevo</td>
                            <td>
                              <div class="form-group">
                                <input class="form-control text-right" type="number" name="precio_venta_ibnorca" value="" placeholder="Precio de venta Ibnorca"  id="precio_venta_ibnorca" step="0.01"/>
                              </div>
                            </td>
                            <td>
                               <div class="form-group">                    
                                 <input class="form-control text-right" type="number" name="precio_venta_fuera" value="" placeholder="Precio de venta Fuera Ibnorca" id="precio_venta_fuera" step="0.01"/>
                               </div>
                             </td>
                             <td><a href="#" class="btn btn-warning btn-sm" onclick="agregarPrecioPlantilla(<?=$codigo?>); return false;">
                              Agregar
                            </a></td>
                          </tr>
                           <?php
                           $stmt = $dbh->prepare("SELECT * FROM precios_plantillacosto where cod_plantillacosto=$codigo order by codigo");
                           $stmt->execute();
                           $indexPrecio=1;
                         while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                          $codigoPrecio=$row['codigo'];
                          $precioLocal=number_format($row['venta_local'], 2, '.', ',');
                          $precioExterno=number_format($row['venta_externo'], 2, '.', ',');
                          ?><tr><td><?=$indexPrecio?></td><td class="text-right"><?=$precioLocal?></td><td class="text-right"><?=$precioExterno?></td>
                          <td><a href="#" class="<?=$buttonDelete;?> btn-link btn-sm" onclick="removePrecioPlantilla(<?=$codigoPrecio?>,<?=$codigo?>); return false;">
                              <i class="material-icons"><?=$iconDelete;?></i>
                            </a>
                          </td></tr><?php
                          $indexPrecio++;
                           }?>
                        </tbody>
                     </table>
                  </div>
                </div> -->                
                </div>
                <div class="card-footer bg-white col-sm-12">
                   <div class="row col-sm-12">
                    <div class="col-sm-3">
                       <div class="form-group">
                        <input class="form-control" type="hidden" name="grupo_area" value="<?=$grupoArea?>" id="grupo_area">
                        <input class="form-control" type="hidden" name="grupo_unidad" value="<?=$grupoUnidad?>" id="grupo_unidad"> 
                          <!-- <button type="submit" class="btn btn-info">Guardar</button>-->
                           <a href="../<?=$urlList;?>" class="btn btn-danger">Volver</a>
                       </div>
                     </div>
                    <!-- <div class="col-sm-3">
                       <div class="form-group">
                           <label class="bmd-label-static">Cantidad de Alumnos Ibnorca</label>
                           <input class="form-control" type="number" name="cantidad_alumnos" value="<?=$detallePlantilla[2]?>" id="cantidad_alumnos" required/>
                       </div>
                     </div>
                     <div class="col-sm-3">
                       <div class="form-group">
                           <label class="bmd-label-static">Cantidad de Alumnos Fuera Ibnorca</label>
                           <input class="form-control" type="number" name="cantidad_alumnos_fuera" value="<?=$detallePlantilla[3]?>" id="cantidad_alumnos_fuera" required/>
                       </div>
                     </div>
                   </div>-->
                </div>
                
              </div><!--fin DIV card-->

             </form>
            </div>
          </div>  
        </div>
    </div>
<?php require_once 'modal.php';?>