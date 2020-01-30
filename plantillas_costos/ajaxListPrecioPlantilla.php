<?php
session_start();
require_once '../conexion.php';
require_once '../styles.php';

require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once 'configModule.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$codigo=$_GET['codigo'];
?>
<tr class="bg-plomo">
                            <td>Nuevo</td>
                            <td>
                              <div class="form-group">
                                <input class="form-control text-right" type="number" name="precio_venta_ibnorca" value="" placeholder="Precio de venta"  id="precio_venta_ibnorca" step="0.01"/>
                              </div>
                            </td>
                            <!--<td>
                               <div class="form-group">                    
                                 <input class="form-control text-right" type="number" name="precio_venta_fuera" value="" placeholder="Precio de venta" id="precio_venta_fuera" step="0.01"/>
                               </div>
                             </td>-->
                             <td><a href="#" class="btn btn-success btn-sm" onclick="agregarPrecioPlantilla(<?=$codigo?>); return false;">
                              Agregar
                            </a></td>
                          </tr>
                           <?php
                           $cantidad=obtenerCantidadPreciosPlantilla($codigo);
                           $stmt = $dbh->prepare("SELECT * FROM precios_plantillacosto where cod_plantillacosto=$codigo order by codigo");
                           $stmt->execute();
                           $indexPrecio=1;
                         while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                          $codigoPrecio=$row['codigo'];
                          $precioLocal=number_format($row['venta_local'], 2, '.', ',');
                          //$precioExterno=number_format($row['venta_externo'], 2, '.', ',');
                          ?>
                          <tr class="bg-white text-dark">
                             <td><?=$indexPrecio?></td>
                             <td class="text-right"><?=$precioLocal?></td>
                             <!--<td class="text-right"></td>-->
                             <td>
                              <?php 
                              if($cantidad>1){
                              ?>
                              <a href="#" class="<?=$buttonDelete;?> btn-link btn-sm" onclick="removePrecioPlantilla(<?=$codigoPrecio?>,<?=$codigo?>); return false;">
                                <i class="material-icons"><?=$iconDelete;?></i>
                              </a>
                              <?php
                              }?>
                             </td>
                          </tr><?php
                          $indexPrecio++;
                           }?>