<?php
require_once '../conexion.php';
//echo "hola";
$dbh = new Conexion();
set_time_limit(300);
// =========actualoizar campos af
$array_vida_util=[48,48,48,96,96,96,96,48,48,48,48,48,48,24,48,72,24,48,48,48,48,72,72,72,48,48,48,96,48,48,48,48,96,48,48,48,48,48,48,72,48,48,48,48,48,48,48,48,24,72,72,72,48,48,72,48,48,48,48,48,48,48,48,48,48,48,48,108,48,48,48,48,48,48,48,48,48,96,48,48,48,72,48,48,48,112,112,112,112,112,112,112,112,112,112,112,112,112,112,112,112,112,112,112,112,112,112,112,112,112,112,112,112,112,112,48,48,48,112,112,112,112,112,112,112,112,112,112,112,112,112,112,112,112,112,112,112,112,112,112,112,112,112,112,112,112,112,48,72,72,72,72,72,72,72,72,72,72,72,72,72,72,72,72,72,72,72,72,72,72,72,72,72,72,72,72,72,72,72,72,72,72,72,72,72,72,72,72,72,72,72,72,72,72,72,72,48,48,48,48,48,48,48,48,48,48,48,48,48,48,48,48,48,48,48,48,48,48,48,48,48,48,48,48,48,48,48,48,48,48,48,48,48,48,48,48,48,72,48,72,48,48,48,48,112,120,120,120,120,120,29,38,38,43,38,38,39,43,39,38,19,10,19,38,10,19,19,29,29,38,29,10,10,39,19,29,0,0,0,19,38,29,19,38,10,0,0,0,0,0,0,39,43,0,0,29,39,29,38,19,19,38,48,48,48,48,48,48,48,48,48,48,48,48,48,48,48,48,48,48,48,38,38,38,38,38,58,58,86,38,38,38,19,86,38,38,38,58,88,88,88,88,96,96,38,58,58,38,38,38,58,19,86,19,38,38,38,38,38,38,58,38,38,38,0,396];
$array_vida_restante=[11,11,11,59,59,59,59,11,11,11,11,11,11,0,11,35,0,11,11,11,11,35,35,35,11,11,11,59,11,11,11,11,59,11,11,11,11,11,11,35,11,11,11,11,11,11,11,11,0,35,35,35,11,11,35,11,11,11,11,11,11,11,11,11,11,11,11,71,11,11,11,11,11,11,11,11,11,59,11,11,11,35,11,11,11,75,75,75,75,75,75,75,75,75,75,75,75,75,75,75,75,75,75,75,75,75,75,75,75,75,75,75,75,75,75,11,11,11,75,75,75,75,75,75,75,75,75,75,75,75,75,75,75,75,75,75,75,75,75,75,75,75,75,75,75,75,75,11,35,35,35,35,35,35,35,35,35,35,35,35,35,35,35,35,35,35,35,35,35,35,35,35,35,35,35,35,35,35,35,35,35,35,35,35,35,35,35,35,35,35,35,35,35,35,35,35,11,11,11,11,11,11,11,11,11,11,11,11,11,11,11,11,11,11,11,11,11,11,11,11,11,11,11,11,11,11,11,11,11,11,11,11,11,11,11,11,11,35,11,35,11,11,11,11,75,104,105,105,114,119,0,1,1,6,1,1,2,6,2,1,0,0,0,1,0,0,0,0,0,1,0,0,0,2,0,0,0,0,0,0,1,0,0,1,0,0,0,0,0,0,0,2,6,0,0,0,2,0,1,0,0,1,13,14,15,15,20,27,27,28,28,28,28,33,34,37,40,40,40,40,41,1,1,1,1,1,21,21,49,1,1,1,0,49,1,1,1,21,51,51,51,51,63,85,1,21,21,1,1,1,21,0,49,0,1,1,1,1,1,1,21,1,1,1,0,359];
    $indexo=0;
    
    $stmt = $dbh->prepare("SELECT codigo from activosfijos where codigo BETWEEN  1185 and 1651");
    $stmt->execute();

while ($row = $stmt->fetch()) {
	$datoDes=$array_vida_util[$indexo];
	$cod_af=$row['codigo'];
	$datoDepre=$array_vida_restante[$indexo];
	
    	$sqlU="UPDATE activosfijos set vidautilmeses=$datoDes,vidautilmeses_restante=$datoDepre where codigo=$cod_af";
    	$stmtu = $dbh->prepare($sqlU);
        $stmtu->execute();
	        
	    $indexo+=1;
    }    
    
    echo $indexo;
//=======================================================
    // insert asignaciones
    
    // $index=0;
    // for ($i=2256; $i <=2295 ; $i++) {    	
    // 	$cod_af=$i;
    // 	// echo $dato."-";
    // 	// echo $cod_af;
    // 	$hora=date("Y-m-d H:i:s");    	
    // 	$sql="insert into activofijos_asignaciones(cod_activosfijos,fechaasignacion,cod_ubicaciones,cod_unidadorganizacional,cod_area,cod_personal,estadobien_asig,cod_estadoasignacionaf) values($cod_af,'$hora',4,829,847,90,'NUEVO',1)";
    // 	//para actualizar campos
    // 	// $sql="UPDATE activofijos_asignaciones set cod_activosfijos=$cod_af where codigo=$cod_af";
    // 	$stmt = $dbh->prepare($sql);
    //     $flagSuccess=$stmt->execute();
    //     $index+=1;
    // }
    // echo $index;
///================================//insert nuevos activos

// $array=[642.93,642.93,642.93,642.93];
// 	$i=0;
//     for ($i=0; $i <count($array) ; $i++) { 
//     	$dato=$array[$i];
//     	$sql="INSERT into activosfijos(valorinicial) values($dato)";
//     	$stmt = $dbh->prepare($sql);
//         $flagSuccess=$stmt->execute();
//     }
//     echo $i;

//========================
//actualizar campos estaticos
//nuevos
// 	$sql="UPDATE activosfijos 
// set tipoalta='NUEVO',indiceufv=1,tipocambio=1,moneda=1,
// valorresidual=0,cod_depreciaciones=14,cod_tiposbienes=14,vidautilmeses=0,estadobien='NUEVO',cod_ubicaciones=4,cod_empresa=1,cod_responsables_responsable=90, cod_responsables_autorizadopor=90,vidautilmeses_restante=0,cod_af_proveedores=3,numerofactura=0,cod_unidadorganizacional=829,cod_area=847,cod_estadoactivofijo=1,reevaluo=0,cod_proy_financiacion=0
// where codigo BETWEEN  2292 and 2295";
//     $stmt = $dbh->prepare($sql);
//     	$flagSuccess=$stmt->execute();
   

   //usados
//    $sql="UPDATE activosfijos 
// set tipoalta='USADO',fechalta='2016-11-30',indiceufv=1,tipocambio=1,moneda=1,
// valorresidual=0,cod_depreciaciones=14,cod_tiposbienes=14,vidautilmeses=0,estadobien='NUEVO',cod_ubicaciones=4,cod_empresa=1,cod_responsables_responsable=90, cod_responsables_autorizadopor=90,vidautilmeses_restante=0,cod_af_proveedores=3,numerofactura=0,cod_unidadorganizacional=829,cod_area=847,cod_estadoactivofijo=1,fecha_reevaluo='2016-11-30',reevaluo=0,cod_proy_financiacion=0
// where codigo BETWEEN  2256 and 2291";

//     	$stmt = $dbh->prepare($sql);
//     	$flagSuccess=$stmt->execute();

//===================actualizar datos af nuevo
// $arrayDescripcion=['TELEFONO DIGITAL INHALAMBRICO PANASONIC TGC 362 (DNS)','TELEFONO DIGITAL INHALAMBRICO PANASONIC TGC 362 (DNS)','TELEFONO DIGITAL INHALAMBRICO PANASONIC TGC 362 (DNS)','TELEFONO DIGITAL INHALAMBRICO PANASONIC TGC 362 (DNS)'];
// $arrayDepre=[54.26,54.26,54.26,54.26];
// $arrayFechaalta=['2019-05-28','2019-05-28','2019-05-28','2019-05-28'];


//     $index=0;
// for ($i=2292; $i <=2295 ; $i++) { 
    	
// 	    	$datoDes=$arrayDescripcion[$index];
// 	    	$cod_af=$i;
// 	    	$datoDepre=$arrayDepre[$index];
// 	    	$datoFecha1=$arrayFechaalta[$index];
// 	    	$datoFecha2=$datoFecha1;
// 	    	//para actualizar campos
// 	    	$sql="UPDATE activosfijos set otrodato='$datoDes',activo='$datoDes',depreciacionacumulada=$datoDepre,fechalta='$datoFecha1',fecha_reevaluo='$datoFecha2' where codigo=$cod_af";
// 	    	//$sql="UPDATE activosfijos set fechalta='$datoDes' where codigo=$cod_af";
// 	    	$stmt = $dbh->prepare($sql);
// 	        $stmt->execute();
	        
// 	    $index+=1;
//     }    
    
//     echo $index;
                                                                                                           
















?>