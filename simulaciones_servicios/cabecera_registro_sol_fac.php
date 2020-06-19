<div class="row">
                          <label class="col-sm-2 col-form-label">Oficina</label>
                          <div class="col-sm-4">
                            <div class="form-group">
                                <input class="form-control" type="hidden" name="cod_uo" id="cod_uo" required="true" value="<?=$cod_uo;?>" required="true" readonly/>
                                 <input class="form-control" type="text" required="true" value="<?=$name_uo;?>" required="true" readonly style="background-color:#E3CEF6;text-align: left"/>
                               
                            </div>
                          </div>
                          <label class="col-sm-2 col-form-label">Area</label>
                            <div class="col-sm-4">
                                <div class="form-group" >
                                    <div id="div_contenedor_area_tcc">
                                        <input class="form-control" type="hidden" name="cod_area" id="cod_area" required="true" value="<?=$cod_area;?>" required="true" readonly/>

                                        <input class="form-control" type="text" required="true" value="<?=$name_area;?>" required="true" readonly style="background-color:#E3CEF6;text-align: left"/>
                                       
                                    </div>                    
                                </div>
                            </div>
                        </div>
                            <!--fin ofician y area -->
                        <div class="row">
                            <label class="col-sm-2 col-form-label">F. Registro</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" type="date" name="fecha_registro" id="fecha_registro" required="true" value="<?=$fecha_registro;?>" required="true"/>
                                    <input type="hidden" name="fecha_solicitudfactura" id="fecha_solicitudfactura" value="<?=$fecha_solicitudfactura;?>"/>
                                </div>
                            </div>
                            <label class="col-sm-2 col-form-label">Tipo Objeto</label>
                            <div class="col-sm-4">
                                <div class="form-group" >

                                    <input class="form-control" type="hidden" name="cod_tipoobjeto" id="cod_tipoobjeto" required="true" value="<?=$cod_tipoobjeto;?>" required="true" readonly/>

                                    <input class="form-control" type="text" required="true" value="<?=$name_tipoPago;?>" required="true" readonly style="background-color:#E3CEF6;text-align: left"/>
                                </div>
                            </div>                            
                        </div>
                        <!-- fin fechas -->                        
                        <div class="row" >                            
                            <script>var nfac=[];itemTipoPagos_facturacion.push(nfac);var nfacAreas=[];itemAreas_facturacion.push(nfacAreas);</script>
                            <div class="">
                                <?php
                                    //====ingresamos los objetos con porcentajes
                                    if($cod_facturacion > 0)
                                    {
                                        $queryTipopagoEdit="SELECT cod_tipopago,porcentaje,monto from solicitudes_facturacion_tipospago where cod_solicitudfacturacion=$cod_facturacion";
                                        $stmtTipopagoEdit = $dbh->prepare($queryTipopagoEdit);
                                        $stmtTipopagoEdit->execute();
                                        $ncAreas=0;$contAreas= array();
                                        while ($rowAreas = $stmtTipopagoEdit->fetch(PDO::FETCH_ASSOC)) {
                                            $datoArea = new stdClass();//obejto
                                            $codFila=(int)$rowAreas["cod_tipopago"];
                                            $porcentaje_x=trim($rowAreas['porcentaje']);
                                            $monto_x=trim($rowAreas['monto']);?>
                                            <script>
                                                var tipopago={
                                                    codigo_tipopago: <?=$codFila?>,
                                                    monto_porcentaje: <?=$porcentaje_x?>,
                                                    monto_bob: <?=$monto_x?>
                                                }
                                                itemTipoPagos_facturacion[0].push(tipopago);  
                                            </script>
                                            <?php
                                        }
                                        //para objetos areas                                        
                                        $queryAreasEdit="SELECT cod_area,porcentaje,monto from solicitudes_facturacion_areas where cod_solicitudfacturacion=$cod_facturacion";
                                        $stmtAreasEdit = $dbh->prepare($queryAreasEdit);
                                        $stmtAreasEdit->execute();
                                        $ncAreas=0;$contAreas= array();
                                        while ($row = $stmtAreasEdit->fetch(PDO::FETCH_ASSOC)) {
                                            $datoArea = new stdClass();//obejto
                                            $codFila=(int)$row["cod_area"];
                                            $porcentaje_x=trim($row['porcentaje']);
                                            $monto_x=trim($row['monto']);?>
                                            <script>
                                                var area={
                                                    codigo_areas: <?=$codFila?>,
                                                    monto_porcentaje: <?=$porcentaje_x?>,
                                                    monto_bob: <?=$monto_x?>
                                                }
                                                itemAreas_facturacion[0].push(area);  
                                            </script>
                                            <?php
                                        }
                                    }
                                    //=== termina porcentaje objetos
                                    $queryAreas="SELECT codigo,nombre,abreviatura from areas where areas_ingreso=1 and cod_estado=1 order by nombre";
                                    $stmtAreas = $dbh->prepare($queryAreas);
                                    $stmtAreas->execute();
                                    $ncAreas=0;$contAreas= array();
                                    while ($rowAreas = $stmtAreas->fetch(PDO::FETCH_ASSOC)) { 
                                        //unidades de cada area?>
                                        <script>
                                            var nfacUnidades=[];itemUnidades_facturacion.push(nfacUnidades);
                                        </script>
                                        <?php
                                        //objeto dato donde se guarda las areas de servicios
                                        $datoArea = new stdClass();//obejto
                                        $codFila=(int)$rowAreas["codigo"];
                                        $nombre_x=trim($rowAreas['nombre']);
                                        $abrev_x=trim($rowAreas['abreviatura']);
                                        $datoArea->codigo=($ncAreas+1);
                                        $datoArea->cod_area=$codFila;
                                        $datoArea->nombrex=$nombre_x;
                                        $datoArea->abrevx=$abrev_x;
                                        $datosAreas[0][$ncAreas]=$datoArea;                           
                                        $ncAreas++;
                                    }
                                    $contAreas[0]=$ncAreas;
                                ?>
                                <?php //unidades
                                    $queryUnidades="SELECT codigo,nombre,abreviatura from unidades_organizacionales where cod_estado=1 order by nombre";
                                    $stmtUnidades = $dbh->prepare($queryUnidades);
                                    $stmtUnidades->execute();
                                    $ncUnidades=0;$contUnidades= array();
                                    while ($rowUnidades = $stmtUnidades->fetch(PDO::FETCH_ASSOC)) { 
                                        //objeto dato donde se guarda las areas de servicios
                                        $datoUnidades = new stdClass();//obejto
                                        $codFila=(int)$rowUnidades["codigo"];
                                        $nombre_x=trim($rowUnidades['nombre']);                                        
                                        $datoUnidades->codigo=($ncUnidades+1);
                                        $datoUnidades->cod_unidad=$codFila;
                                        $datoUnidades->nombrex=$nombre_x;                                                
                                        $datosUnidades[0][$ncUnidades]=$datoUnidades;                           
                                        $ncUnidades++;
                                    }
                                    $contUnidades[0]=$ncUnidades;
                                ?>
                            </div>
                            <label class="col-sm-2 col-form-label">Tipo Pago</label>
                            <div class="col-sm-3">
                                <div class="form-group" >
                                    <select name="cod_tipopago" id="cod_tipopago" class="selectpicker form-control form-control-sm" data-style="btn btn-info" onChange="ajaxTipoPagoContactoPersonal(this);">
                                        <?php 
                                        $queryTipoPago = "SELECT codigo,nombre FROM  tipos_pago WHERE cod_estadoreferencial=1 order by nombre";
                                        $statementPAgo = $dbh->query($queryTipoPago);
                                        $nc=0;$cont= array();
                                        while ($row = $statementPAgo->fetch()){ 
                                            //objeto dato donde guarda tipos de pago
                                            $dato = new stdClass();//obejto
                                            $codFila=(int)$row["codigo"];
                                            $nombre_x=trim($row['nombre']);
                                            $dato->codigo=($nc+1);
                                            $dato->cod_tipopago=$codFila;
                                            $dato->nombrex=$nombre_x;                                                
                                            $datos[0][$nc]=$dato;                           
                                            $nc++;
                                            ?>
                                            <option <?=($cod_tipopago==$row["codigo"])?"selected":"";?>  value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                        <?php } 
                                        $cont[0]=$nc;
                                        ?>
                                    </select>                                    
                                </div>
                            </div>
                            <div class="col-sm-1">
                                <div class="form-group" >    
                                    <button type="button" class="btn btn-danger btn-round btn-fab btn-sm" data-toggle="modal" data-target="" onclick="agregarDatosModalTipoPagoFacturacion()">
                                        <i class="material-icons" title="Tipo Pago Porcentaje">list</i>
                                        <span id="nfac" class="count bg-warning"></span>
                                     </button>
                                     <button type="button" class="btn btn-primary btn-round btn-fab btn-sm" data-toggle="modal" data-target="" onclick="agregarDatosModalAreasFacturacion()">
                                        <i class="material-icons" title="Areas Porcentaje">list</i>
                                        <span id="nfacAreas" class="count bg-warning"></span>
                                     </button>                              
                                </div>
                            </div>         
                            <label class="col-sm-2 col-form-label">Responsable</label>
                            <div class="col-sm-4">
                                <div class="form-group">            
                                    <?php  $responsable=namePersonal($cod_personal); ?>                    
                                    <input type="hidden" name="cod_personal" id="cod_personal" value="<?=$cod_personal?>" readonly="true" class="form-control">
                                    <input type="text" value="<?=$responsable?>" readonly="true" class="form-control" style="background-color:#E3CEF6;text-align: left">
                                </div>
                            </div>
                        </div>
                        <!-- fin tipos pago y objeto                 -->                        
                        <div class="row">
                            <label class="col-sm-2 col-form-label">Cliente</label>
                            <div class="col-sm-4">
                                <div class="form-group" >

                                     <input class="form-control" type="hidden" name="cod_cliente" id="cod_cliente" required="true" value="<?=$cod_cliente;?>" required="true" readonly/>

                                        <input class="form-control" type="text" required="true" value="<?=$name_cliente;?>" required="true" readonly style="background-color:#E3CEF6;text-align: left"/>
                                        
                                </div>
                            </div>
                            <label class="col-sm-2 col-form-label">Persona Contacto</label>
                            <div class="col-sm-3">
                                <div class="form-group" >
                                        <!-- <input type="text" name="persona_contacto" id="persona_contacto" value="<?=$persona_contacto?>" class="form-control" required="true"> -->
                                        <div id="div_contenedor_contactos">
                                            <select class="selectpicker form-control form-control-sm" name="persona_contacto" id="persona_contacto" data-style="btn btn-info" data-show-subtext="true" data-live-search="true" title="Seleccione Contacto">
                                              <option value=""></option>
                                              <?php 
                                              $query="SELECT * FROM clientes_contactos where cod_cliente=$cod_cliente order by nombre";
                                              $stmt = $dbh->prepare($query);
                                              $stmt->execute();
                                              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                $codigo=$row['codigo'];    
                                                $nombre_conatacto=$row['nombre']." ".$row['paterno']." ".$row['materno'];
                                                ?><option <?=($persona_contacto==$row["codigo"])?"selected":"";?> value="<?=$codigo?>" class="text-right"><?=$nombre_conatacto?></option>
                                               <?php 
                                               } ?> 
                                            </select>
                                        </div>
                                </div>

                            </div>
                            <div class="col-sm-1">
                                <div class="form-group" >                                        
                                    <a href="#" class="btn btn-warning btn-round btn-fab btn-sm" onclick="cargarDatosRegistroContacto()">
                                        <i class="material-icons" title="Add Contacto">add</i>
                                    </a>
                                    <a href="#" class="btn btn-success btn-round btn-fab btn-sm" onclick="actualizarRegistroContacto()">
                                       <i class="material-icons" title="Actualizar Clientes & Contactos">update</i>
                                    </a> 
                                </div>
                            </div>
                            
                        </div>
                        <!-- fin cliente y responsable -->

                        <div class="row">
                            <label class="col-sm-2 col-form-label">Razón Social</label>
                            <div class="col-sm-5">
                                <div class="form-group">
                                    <div id="contenedor_razonsocial">
                                        <input class="form-control" type="text" name="razon_social" id="razon_social" required="true" value="<?=$razon_social;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>    
                                    </div>
                                </div>
                            </div>
                            <label class="col-sm-1 col-form-label">Nit</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" type="number" name="nit" id="nit" required="true" value="<?=$nit;?>" onkeyup="javascript:this.value=this.value.toUpperCase();" required="true"/>
                                </div>
                            </div>
                        </div>
                        <!-- fin razon social y nit -->
                        <div class="row">
                            <label class="col-sm-2 col-form-label">Observaciones * 1</label>
                            <div class="col-sm-10">
                                <div class="form-group">
                                    <input class="form-control" type="text" name="observaciones" id="observaciones"  value="<?=$observaciones;?>" onkeyup="javascript:this.value=this.value.toUpperCase();" requerid/>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-sm-2 col-form-label">Observaciones 2</label>
                            <div class="col-sm-10">
                                <div class="form-group">
                                    <input class="form-control" type="text" name="observaciones_2" id="observaciones_2" value="<?=$observaciones_2;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
                                </div>
                            </div>
                        </div>
                        <!-- fin observaciones -->