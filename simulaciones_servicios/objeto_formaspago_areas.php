<?php
    //====ingresamos los objetos con porcentajes
    if($cod_facturacion > 0)
    {
        $queryTipopagoEdit="SELECT cod_tipopago,porcentaje,monto from solicitudes_facturacion_tipospago where cod_solicitudfacturacion=$cod_facturacion";
        $stmtTipopagoEdit = $dbh->prepare($queryTipopagoEdit);
        $stmtTipopagoEdit->execute();
        // $ncformasPagos=0;$contFormasPagos= array();
        while ($rowAreas = $stmtTipopagoEdit->fetch(PDO::FETCH_ASSOC)) {
            // $ncformasPagos++;
            // $datoArea = new stdClass();//obejto
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
        }?>
        <!-- <script>                                             
            $("#nfac").html(itemTipoPagos_facturacion[0].length);
        </script> -->
        <?php 
        //para objetos areas                                        
        $queryAreasEdit="SELECT cod_area,porcentaje,monto from solicitudes_facturacion_areas where cod_solicitudfacturacion=$cod_facturacion";
        $stmtAreasEdit = $dbh->prepare($queryAreasEdit);
        $stmtAreasEdit->execute();
        $ncAreas=0;$contAreas= array();
        while ($row = $stmtAreasEdit->fetch(PDO::FETCH_ASSOC)) {
            $cod_area_objeto=$row["cod_area"];
           

            // $queryUnidadesEdit="SELECT codigo,porcentaje,monto from solicitudes_facturacion_areas_uo where cod_solicitudfacturacion=$cod_facturacion and cod_area=$cod_area_objeto";
            // // echo $queryUnidadesEdit;
            // $stmtUnidadesEdit = $dbh->prepare($queryUnidadesEdit);
            // $stmtUnidadesEdit->execute();
            // // $ncAreas=0;$contAreas= array();
            // while ($rowUnidades = $stmtUnidadesEdit->fetch(PDO::FETCH_ASSOC)) {              
            //     $codUnidad=(int)$rowUnidades["codigo"];
            //     $porcentaje_x=trim($rowUnidades['porcentaje']);
            //     $monto_x=trim($rowUnidades['monto']);
            //     ?>
               <script>

            //         var unidad={
            //             codigo_unidad: codUnidad,
            //             monto_porcentaje: porcentaje_x,
            //             monto_bob: $monto_x
            //         }
            //         itemUnidades_facturacion[<?=$ncAreas?>].push(unidad);
            //         console.log(itemUnidades_facturacion);
            //         $("#nfacUnidades"+<?=$ncAreas?>).html(itemUnidades_facturacion[<?=$ncAreas?>].length);//muestar numero de itms en esa area
            //     </script>
              <?php
            // }


            // $datoArea = new stdClass();//obejto
            $codArea=(int)$row["cod_area"];
            $porcentaje_x=trim($row['porcentaje']);
            $monto_x=trim($row['monto']);?>
            <script>
                var area={
                    codigo_areas: <?=$codArea?>,
                    monto_porcentaje: <?=$porcentaje_x?>,
                    monto_bob: <?=$monto_x?>
                }
                itemAreas_facturacion[0].push(area);  
            </script>
            <?php
            $ncAreas++;
        }?>
       <!--  <script>                                             
            $("#nfacAreas").html(itemAreas_facturacion[0].length);
        </script> -->

        <?php         
    }
?>