<?php
$sqlDeleteTiposPago="DELETE from solicitudes_facturacion_tipospago where cod_solicitudfacturacion=$cod_facturacion";
$stmtDelTiposPago = $dbh->prepare($sqlDeleteTiposPago);
$stmtDelTiposPago->execute();
//si existe array de objetos tipopago
if(isset($_POST['tiposPago_facturacion'])){
    $tiposPago_facturacion= json_decode($_POST['tiposPago_facturacion']);
    $nF=cantidadF($tiposPago_facturacion[0]);
    for($j=0;$j<$nF;$j++){
        $codigo_tipopago=$tiposPago_facturacion[0][$j]->codigo_tipopago;
        $monto_porcentaje=$tiposPago_facturacion[0][$j]->monto_porcentaje;
        $monto_bob=$tiposPago_facturacion[0][$j]->monto_bob;                                
        // echo "codigo_tipopago:".$codigo_tipopago."<br>";
        // echo "monto_porcentaje:".$monto_porcentaje."<br>";        
        // echo "monto_bob:".$monto_bob."<br>";          
        $sqlTiposPago="INSERT INTO solicitudes_facturacion_tipospago(cod_solicitudfacturacion, cod_tipopago, porcentaje, monto) VALUES ('$cod_facturacion','$codigo_tipopago','$monto_porcentaje','$monto_bob')";
        $stmtTiposPago = $dbh->prepare($sqlTiposPago);
        $stmtTiposPago->execute();
    }
}else{
    $codigo_tipopago=$cod_tipopago;
    $monto_porcentaje=100;
    if($tipo_solicitud==2 || $tipo_solicitud==6 || $tipo_solicitud==7){
    	$monto_bob=$_POST["modal_totalmontoserv_costo_a"];
    }else{
    	$monto_bob=$_POST["monto_total_a"];	
    }
    
    // echo "codigo_tipopago:".$codigo_tipopago."<br>";
    // echo "monto_porcentaje:".$monto_porcentaje."<br>";        
    // echo "monto_bob:".$monto_bob."<br>";    
    $sqlTiposPago="INSERT INTO solicitudes_facturacion_tipospago(cod_solicitudfacturacion, cod_tipopago, porcentaje, monto) VALUES ('$cod_facturacion','$codigo_tipopago','$monto_porcentaje','$monto_bob')";
    $stmtTiposPago = $dbh->prepare($sqlTiposPago);
    $stmtTiposPago->execute();
}
//para porcetnaje de areas
$sqlDeleteAreas="DELETE from solicitudes_facturacion_areas where cod_solicitudfacturacion=$cod_facturacion";
$stmtDelAreas = $dbh->prepare($sqlDeleteAreas);
$stmtDelAreas->execute();
$sqlDeleteAreasUO="DELETE from solicitudes_facturacion_areas_uo where cod_solicitudfacturacion=$cod_facturacion";
$stmtDelAreasUO = $dbh->prepare($sqlDeleteAreasUO);
$stmtDelAreasUO->execute();
//si existe array de objetos areas
if(isset($_POST['areas_facturacion'])){
    $areas_facturacion= json_decode($_POST['areas_facturacion']);
    $nF=cantidadF($areas_facturacion[0]);
    for($j=0;$j<$nF;$j++){
        $codigo_area=$areas_facturacion[0][$j]->codigo_areas;
        $monto_porcentaje=$areas_facturacion[0][$j]->monto_porcentaje;
        $monto_bob=$areas_facturacion[0][$j]->monto_bob;                                
        if($monto_porcentaje>0){
            // echo "codigo_area:".$codigo_area."<br>";
            // echo "monto_porcentaje:".$monto_porcentaje."<br>";        
            // echo "monto_bob:".$monto_bob."<br>";          
            $sqlTiposPago="INSERT INTO solicitudes_facturacion_areas(cod_solicitudfacturacion, cod_area, porcentaje, monto) VALUES ('$cod_facturacion','$codigo_area','$monto_porcentaje','$monto_bob')";
            $stmtTiposPago = $dbh->prepare($sqlTiposPago);
            $stmtTiposPago->execute();
            //si existe array de unidades
            if(isset($_POST['unidades_facturacion'])){
                $unidades_facturacion=json_decode($_POST['unidades_facturacion']);
                $nFU=cantidadF($unidades_facturacion[$j]);
                if($nFU>0){
                    for($u=0;$u<$nFU;$u++){                                
                        $codigo_unidad=$unidades_facturacion[$j][$u]->codigo_unidad;
                        $monto_porcentaje_uo=$unidades_facturacion[$j][$u]->monto_porcentaje;
                        $monto_bob_uo=$unidades_facturacion[$j][$u]->monto_bob;                                
                        // echo "codigo_unidad:".$codigo_unidad."<br>";
                        // echo "monto_porcentaje:".$monto_porcentaje."<br>";        
                        // echo "monto_bob:".$monto_bob."<br>";    
                        $sqlUnidades="INSERT INTO solicitudes_facturacion_areas_uo(cod_solicitudfacturacion,cod_area, cod_uo, porcentaje, monto) VALUES ('$cod_facturacion','$codigo_area','$codigo_unidad','$monto_porcentaje_uo','$monto_bob_uo')";
                        $stmtUnidades = $dbh->prepare($sqlUnidades);
                        $stmtUnidades->execute();                               
                    }
                }else{                            
                    $sqlUnidades="INSERT INTO solicitudes_facturacion_areas_uo(cod_solicitudfacturacion,cod_area, cod_uo, porcentaje, monto) VALUES ('$cod_facturacion','$codigo_area','$cod_unidadorganizacional',100,'$monto_bob')";
                    $stmtUnidades = $dbh->prepare($sqlUnidades);
                    $stmtUnidades->execute();                               
                }                        
            }else{                        
                $sqlUnidades="INSERT INTO solicitudes_facturacion_areas_uo(cod_solicitudfacturacion,cod_area, cod_uo, porcentaje, monto) VALUES ('$cod_facturacion','$codigo_area','$cod_unidadorganizacional',100,'$monto_bob')";
                $stmtUnidades = $dbh->prepare($sqlUnidades);
                $stmtUnidades->execute();
            }
        }
    }
}else{
    $codigo_area=$cod_area;
    $monto_porcentaje=100;
    if($tipo_solicitud==2 || $tipo_solicitud==6 || $tipo_solicitud==7){
    	$monto_bob=$_POST["modal_totalmontoserv_costo_a"];
    }else{
    	$monto_bob=$_POST["monto_total_a"];
    }
    
    // echo "codigo_area:".$codigo_area."<br>";
    // echo "monto_porcentaje:".$monto_porcentaje."<br>";        
    // echo "monto_bob:".$monto_bob."<br>";    
    $sqlTiposPago="INSERT INTO solicitudes_facturacion_areas(cod_solicitudfacturacion, cod_area, porcentaje, monto) VALUES ('$cod_facturacion','$codigo_area','$monto_porcentaje','$monto_bob')";
    $stmtTiposPago = $dbh->prepare($sqlTiposPago);
    $stmtTiposPago->execute();
    $sqlUnidades="INSERT INTO solicitudes_facturacion_areas_uo(cod_solicitudfacturacion,cod_area, cod_uo, porcentaje, monto) VALUES ('$cod_facturacion','$codigo_area','$cod_unidadorganizacional','$monto_porcentaje','$monto_bob')";
    $stmtUnidades = $dbh->prepare($sqlUnidades);
    $stmtUnidades->execute();
}

?>