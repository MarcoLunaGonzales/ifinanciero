<?php
session_start();
require_once '../conexion.php';
require_once '../styles.php';

require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once 'configModule.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$codigo=$_GET['cod_plantillacosto'];
$codPartida=$_GET['cod_partida'];
$tipoCalculo=$_GET['tipo_calculomonto'];
if($tipoCalculo==2){
  $tipoCal=obtenerTipodeCalculoRegistradoDetalle($codigo,$codPartida,1);
  if($tipoCal!=0){
    $tipoCalculo=$tipoCal;
  }
}
$cursos=$_GET['cursos'];
$alumnos=$_GET['alumnos'];
?>
<div class="row col-sm-12">
                    <div class="col-sm-4">
                         <div class="form-group">
                          <label class="bmd-label-static"><b id="titulo_montototalcal"></b> Calculado</label>
                          <input type="number" class="form-control" name="monto_totalplantilladetallecal" id="monto_totalplantilladetallecal" value="0" step="0.01" value="" readonly>
                         </div>
                    </div>
                    <div class="col-sm-4">
                         <div class="form-group">
                          <label class="bmd-label-static" id="titulo_montototal">Monto</label>
                          <input type="number" class="form-control" name="monto_totalplantilladetalle" id="monto_totalplantilladetalle" value="0" step="0.01" readonly>
                         </div>
                    </div>
                    <div class="col-sm-4">
                         <div class="form-group">
                          <label class="bmd-label-static" id="titulo_montototal">Filtro</label>
                             <select class="selectpicker form-control form-control-sm" onchange="mostrarInputMonto(this.value)" name="tipoMontoDetalle" id="tipoMontoDetalle" data-style="btn btn-info">
                                    <option value="monto_ibnorca1">Global</option>
                                    <option value="monto_ibnorca2">x Auditoria</option>
                                    <option value="monto_ibnorca3">x Personal</option>
                              </select>                   
                         </div>
                    </div>
  </div>
<table class="table table-condensed table-bordered">
                       <tr class="bg-danger text-white">
                         <td>Nº</td>
                         <td>Detalle</td>
                         <td width="12%">Monto Bol</td>
                         <td width="12%">Monto Ext</td>
                         <?php 
                         // if($tipoCalculo==3){
                           ?><!--<td>Personal x D&iacute;as Aud.</td>--><?php
                          //}
                         ?>                         
                         <td>Cuentas de la Partida</td>
                         <td>Actions</td>
                       </tr>
                     <tbody>

                       <tr class="bg-plomo">
                            <td>Nuevo</td>
                            <td>
                              <div class="form-group">
                                <input class="form-control text-left" type="text" name="glosa_plantilladetalle" value="" placeholder="Detalle"  id="glosa_plantilladetalle"/>
                                <input type="hidden" id="tipo_calculomonto" value="<?=$tipoCalculo?>">
                              </div>
                            </td>
                            <td width="12%">
                               <div class="form-group">                    
                                 <input class="form-control text-right" type="number" name="monto_plantilladetalle" value="" placeholder="Monto Bol" id="monto_plantilladetalle" step="0.01"/>
                               </div>
                             </td>
                             <td width="12%">
                               <div class="form-group">                    
                                 <input class="form-control text-right" type="number" name="monto_plantilladetalleext" value="" placeholder="Monto Ext" id="monto_plantilladetalleext" step="0.01"/>
                               </div>
                             </td>
                             <?php 
                          //if($tipoCalculo==3){
                           ?><!--<td id="cantidad_personaltabla">-->
                             <?php //$alumnos ?>
                             <!--</td>--><?php
                            //}
                           ?>                       
                             <td>
                              <?php 
                                $cuentasPartida=obtenerCuentaPlantillaCostos($codPartida);
                                ?>
                                  <select class="selectpicker form-control form-control-sm" name="cuenta_plantilladetalle" id="cuenta_plantilladetalle" data-style="<?=$comboColor;?>">
                                    <option disabled selected="selected" value="">Cuenta</option>
                                    <?php 
                                     while ($rowCuenta = $cuentasPartida->fetch(PDO::FETCH_ASSOC)) {
                                      $codigoCuentaX=$rowCuenta['cod_cuenta'];
                                      $nombreCuentaX=trim($rowCuenta['nombre']);
                                      ?><option value="<?=$codigoCuentaX?>"><?=$nombreCuentaX?></option><?php
                                     }
                                    ?>
                                  </select>
                                <?php
                              ?></td>
                             <td>
                              <div class="btn-group">
                              <a href="#" class="btn btn-primary btn-sm" id="boton_plantilladetalle" onclick="agregarPlantillaDetalle(<?=$codPartida?>); return false;">
                              Agregar
                              </a>
                              <?php
                              if($tipoCalculo==3){?>
                              <!--<a href="#" title="Asignar Personal" class="btn btn-warning text-dark btn-sm btn-fab" id="boton_plantilladetallepersonal" onclick="agregarPersonalPlantillaDetalle(<?=$codPartida?>); return false;">
                              <i class="material-icons">edit</i>
                              </a>-->
                              <?php } ?> 
                              </div>
                             </td>
                          </tr>
                           <?php
                           $cantidad=obtenerCantidadPlantillaDetallesPartidaServicio($codigo,$codPartida);
                           $stmt = $dbh->prepare("SELECT c.*,p.nombre,p.numero FROM plantillas_servicios_detalle c,plan_cuentas p where p.codigo=c.cod_cuenta and c.cod_plantillatcp=$codigo and c.cod_partidapresupuestaria=$codPartida order by c.codigo");
                           $stmt->execute();
                           $indexDetalle=1;
                           $totalMontoPlantilla=0;
                           switch ($tipoCalculo) {
                            case '1':
                               $nombreInput="monto_ibnorca_edit";
                               $nombreInputCal="monto_ibnorca";
                               $nombreSelect="monto_ibnorca1";
                               $tituloMonto="Monto Global";
                            break;
                            case '2':
                               $nombreInput="monto_f_ibnorca_edit";
                               $nombreInputCal="monto_f_ibnorca";
                               $nombreSelect="monto_ibnorca2";
                               $tituloMonto="Monto x Auditoria";
                            break;
                            case '3':
                               $nombreInput="monto_alumno_edit";
                               $nombreInputCal="monto_alumno";
                               $nombreSelect="monto_ibnorca3";
                               $tituloMonto="Monto x Personal";
                            break;
                          }
                         while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                          $codigoDetalle=$row['codigo'];
                          $nombreCuenta=trim($row['nombre']);
                          $numeroCuenta=trim($row['numero']);
                          switch ($tipoCalculo) {
                            case '1':
                               $montoFila=$row['monto_total']*$cursos;
                               $montoFilaExt=$row['monto_totalext']*$cursos;
                            break;
                            case '2':
                               $montoFila=$row['monto_total'];
                               $montoFilaExt=$row['monto_totalext'];
                            break;
                            case '3':
                               $montoFila=$row['monto_total']/$alumnos;
                               $montoFilaExt=$row['monto_totalext']/$alumnos;
                            break;
                          }
                          $montoTotal=number_format($montoFila, 2, '.', ',');
                          $montoTotalExt=number_format($montoFilaExt, 2, '.', ',');
                          $glosaD=$row['glosa'];
                          $totalMontoPlantilla+=$montoFila;
                          ?>
                          <tr class="bg-white">
                             <td><?=$indexDetalle?></td>
                             <td class="text-left"><?=$row['glosa']?></td>
                             <td class="text-right"><?=$montoTotal?></td>
                             <td class="text-right"><?=$montoTotalExt?></td>
                             <?php 
                            //if($tipoCalculo==3){
                           ?><!--<td>C: --><?php //$alumnos ?><!--</td>--><?php
                            //}
                            ?> 
                             <td class="text-left font-weight-bold small">[<?=$numeroCuenta?>] <?=$nombreCuenta?></td>
                             <td>
                              <a href="#" class="btn btn-success btn-sm btn-fab" onclick="mostrarEditPlantillaDetalle(<?=$codigoDetalle?>,'<?=$montoFila?>','<?=$montoFilaExt?>','<?=$glosaD?>'); return false;">
                                <i class="material-icons"><?=$iconEdit;?></i>
                              </a>
                              <?php 
                              if($cantidad>1){
                              ?>
                              <a href="#" class="<?=$buttonDelete;?> btn-link btn-sm" onclick="removePlantillaDetalle(<?=$codigoDetalle?>); return false;">
                                <i class="material-icons"><?=$iconDelete;?></i>
                              </a>
                              <?php
                              }?>
                             </td>
                          </tr><?php
                          $indexDetalle++;
                           }
                  $totalMontoPlantilla=number_format($totalMontoPlantilla, 2, '.', '');
                           ?>

                     </tbody>
                   </table>  
                <div class="row">
                   <div class="form-group col-sm-12">
                        <a href="#" class="btn btn-info btn-round float-right" onclick="savePlantillaDetalle('<?=$nombreInput?>')">Guardar</a>
                    </div>   
                </div>
  <script>
  $("#monto_totalplantilladetallecal").val($("#<?=$nombreInputCal?>").val());
  $("#tipoMontoDetalle").val("<?=$nombreSelect?>");
  $("#titulo_montototalcal").text("<?=$tituloMonto?>");
  $("#titulo_montototal").text("<?=$tituloMonto?>");
  $("#monto_totalplantilladetalle").val(<?=$totalMontoPlantilla?>);
  $("#<?=$nombreInput?>").val(<?=$totalMontoPlantilla?>);
</script>                        