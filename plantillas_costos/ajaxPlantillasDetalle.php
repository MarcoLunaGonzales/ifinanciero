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
$cursos=$_GET['cursos'];
$alumnos=$_GET['alumnos'];
?>
<div class="row col-sm-12">
                    <div class="col-sm-6">
                         <div class="form-group">
                          <label class="bmd-label-static"><b id="titulo_montototalcal"></b> Calculado</label>
                          <input type="number" class="form-control" name="monto_totalplantilladetallecal" id="monto_totalplantilladetallecal" value="0" step="0.01" value="" readonly>
                         </div>
                    </div>
                    <div class="col-sm-6">
                         <div class="form-group">
                          <label class="bmd-label-static" id="titulo_montototal">Monto</label>
                          <input type="number" class="form-control" name="monto_totalplantilladetalle" id="monto_totalplantilladetalle" value="0" step="0.01" readonly>
                         </div>
                    </div>
  </div>
<table class="table table-condensed table-bordered">
                       <tr class="bg-danger text-white">
                         <td>Nº</td>
                         <td>Detalle</td>
                         <td width="12%">Monto</td>
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
                                 <input class="form-control text-right" type="number" name="monto_plantilladetalle" value="" placeholder="Monto" id="monto_plantilladetalle" step="0.01"/>
                               </div>
                             </td>
                             <td>
                              <?php 
                                $cuentasPartida=obtenerCuentaPlantillaCostos($codPartida);
                                ?>
                                  <select class="selectpicker form-control form-control-sm" name="cuenta_plantilladetalle" id="cuenta_plantilladetalle" data-style="<?=$comboColor;?>" required>
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
                             <td><a href="#" class="btn btn-primary btn-sm" id="boton_plantilladetalle" onclick="agregarPlantillaDetalle(<?=$codPartida?>); return false;">
                              Agregar
                            </a></td>
                          </tr>
                           <?php
                           $cantidad=obtenerCantidadPlantillaDetallesPartida($codigo,$codPartida);
                           $stmt = $dbh->prepare("SELECT c.*,p.nombre,p.numero FROM plantillas_servicios_detalle c,plan_cuentas p where p.codigo=c.cod_cuenta and c.cod_plantillacosto=$codigo and c.cod_partidapresupuestaria=$codPartida order by c.codigo");
                           $stmt->execute();
                           $indexDetalle=1;
                           $totalMontoPlantilla=0;
                           switch ($tipoCalculo) {
                            case '1':
                               $nombreInput="monto_ibnorca_edit";
                               $nombreInputCal="monto_ibnorca";
                               $tituloMonto="Monto x Mes";
                            break;
                            case '2':
                               $nombreInput="monto_f_ibnorca_edit";
                               $nombreInputCal="monto_f_ibnorca";
                               $tituloMonto="Monto x Modulo";
                            break;
                            case '3':
                               $nombreInput="monto_alumno_edit";
                               $nombreInputCal="monto_alumno";
                               $tituloMonto="Monto x Persona";
                            break;
                          }
                         while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                          $codigoDetalle=$row['codigo'];
                          $nombreCuenta=trim($row['nombre']);
                          $numeroCuenta=trim($row['numero']);
                          switch ($tipoCalculo) {
                            case '1':
                               $montoFila=$row['monto_total']*$cursos;
                            break;
                            case '2':
                               $montoFila=$row['monto_total'];
                            break;
                            case '3':
                               $montoFila=$row['monto_total']/$alumnos;
                            break;
                          }
                          $montoTotal=number_format($montoFila, 2, '.', ',');
                          $totalMontoPlantilla+=$montoFila;
                          ?>
                          <tr class="bg-white">
                             <td><?=$indexDetalle?></td>
                             <td class="text-left"><?=$row['glosa']?></td>
                             <td class="text-right"><?=$montoTotal?></td>
                             <td class="text-left font-weight-bold small">[<?=$numeroCuenta?>] <?=$nombreCuenta?></td>
                             <td>
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
  $("#titulo_montototalcal").text("<?=$tituloMonto?>");
  $("#titulo_montototal").text("<?=$tituloMonto?>");
  $("#monto_totalplantilladetalle").val(<?=$totalMontoPlantilla?>);
  $("#<?=$nombreInput?>").val(<?=$totalMontoPlantilla?>);
</script>                        