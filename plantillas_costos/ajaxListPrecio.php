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
                          