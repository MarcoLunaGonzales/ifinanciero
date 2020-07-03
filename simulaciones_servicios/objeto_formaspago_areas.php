<?php
    //====ingresamos los objetos con porcentajes
    if($cod_facturacion > 0)
    {
        $queryTipopagoEdit="SELECT cod_tipopago,porcentaje,monto from solicitudes_facturacion_tipospago where cod_solicitudfacturacion=$cod_facturacion";
        $stmtTipopagoEdit = $dbh->prepare($queryTipopagoEdit);
        $stmtTipopagoEdit->execute();
        $ncformasPagos=0;$contFormasPagos= array();
        while ($rowAreas = $stmtTipopagoEdit->fetch(PDO::FETCH_ASSOC)) {
            $ncformasPagos++;
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
        }?>
       <!--  <script>                                             
            $("#nfacAreas").html(itemAreas_facturacion[0].length);
        </script> -->
        <?php         
    }
?>