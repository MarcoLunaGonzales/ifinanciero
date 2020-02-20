<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

// $codGestion=$_POST["gestion"];
// $codUnidad=$_POST["unidad_organizacional"];

$cantidad_filas_ccd=$_POST["cantidad_filas_ccd"];
$cod_cc=$_POST["cod_cajachica"];
$cod_tcc=$_POST["cod_tcc"];
$cod_ccd=$_POST["cod_ccd"];


// $monto_total_rendido=$_POST["monto_total"];
// $monto_faltante=$_POST["monto_faltante"];

// $cantidadFilas=$_POST["cantidad_filas"];
// $tipoComprobante=$_POST["tipo_comprobante"];
// $nroCorrelativo=$_POST["nro_correlativo"];
// $glosa=$_POST["glosa"];
$facturas= json_decode($_POST['facturas']);
// $estadosCuentas= json_decode($_POST['estados_cuentas']);
session_start();

// $globalUser=$_SESSION["globalUser"];
// $globalGestion=$_SESSION["globalGestion"];
// $globalUnidad=$_SESSION["globalUnidad"];
// $globalArea=$_SESSION["globalArea"];
// $globalAdmin=$_SESSION["globalAdmin"];

$fechaHoraActual=date("Y-m-d H:i:s");

//subir archivos al servidor
//Como el elemento es un arreglos utilizamos foreach para extraer todos los valores
    // foreach($_FILES["archivos"]['tmp_name'] as $key => $tmp_name)
    // {
    //     //Validamos que el archivos exista
    //     if($_FILES["archivos"]["name"][$key]) {
    //         $filename = $_FILES["archivos"]["name"][$key]; //Obtenemos el nombre original del archivos
    //         $source = $_FILES["archivos"]["tmp_name"][$key]; //Obtenemos un nombre temporal del archivos
            
    //         $directorio = '../assets/archivos-respaldo/COMP-'.$codComprobante.'/'; //Declaramos un  variable con la ruta donde guardaremos los archivoss
    //         //Validamos si la ruta de destino existe, en caso de no existir la creamos
    //         if(!file_exists($directorio)){
    //             mkdir($directorio, 0777,true) or die("No se puede crear el directorio de extracci&oacute;n");    
    //         }
            
            
    //         $target_path = $directorio.'/'.$filename; //Indicamos la ruta de destino, así como el nombre del archivos
            
    //         //Movemos y validamos que el archivos se haya cargado correctamente
    //         //El primer campo es el origen y el segundo el destino
    //         if(move_uploaded_file($source, $target_path)) { 
    //             echo "ok";
    //             } else {    
    //             echo "error";
    //         }
            
    //     }
    // }


      $sqlDeleteFactura="DELETE from facturas_detalle_cajachica where cod_cajachicadetalle=$cod_ccd";
      $stmtDelFactura = $dbh->prepare($sqlDeleteFactura);
      $stmtDelFactura->execute();
      $nF=cantidadF($facturas[$cantidad_filas_ccd-1]);
        //echo $nF;
        $suma_importe_fac=0;
         for($j=0;$j<$nF;$j++){
         	  $nit=$facturas[$cantidad_filas_ccd-1][$j]->nit;
         	  $nroFac=$facturas[$cantidad_filas_ccd-1][$j]->nroFac;
         	  
         	  $fecha=$facturas[$cantidad_filas_ccd-1][$j]->fechaFac;
         	  $porciones = explode("/", $fecha);
         	  $fechaFac=$porciones[2]."-".$porciones[1]."-".$porciones[0];
         	  
         	  $razonFac=$facturas[$cantidad_filas_ccd-1][$j]->razonFac;
         	  $impFac=$facturas[$cantidad_filas_ccd-1][$j]->impFac;
         	  $exeFac=0;
         	  $autFac=$facturas[$cantidad_filas_ccd-1][$j]->autFac;
         	  $conFac=$facturas[$cantidad_filas_ccd-1][$j]->conFac;

            $suma_importe_fac=$suma_importe_fac+$impFac;

            // echo "nit:".$nit."<br>";
            // echo "nroFac:".$nroFac."<br>";
            // echo "fecha:".$fecha."<br>";
            // echo "fechaFac:".$fechaFac."<br>";
            // echo "razonFac:".$razonFac."<br>";
            // echo "exeFac:".$exeFac."<br>";
            // echo "autFac:".$autFac."<br>";
            // echo "conFac:".$conFac."<br>";

		        $sqlDetalle2="INSERT INTO facturas_detalle_cajachica (cod_cajachicadetalle, nit, nro_factura, fecha, razon_social, importe, exento, nro_autorizacion, codigo_control) VALUES ('$cod_ccd', '$nit', '$nroFac', '$fechaFac', '$razonFac', '$impFac', '$exeFac', '$autFac', '$conFac')";
  		      $stmtDetalle2 = $dbh->prepare($sqlDetalle2);
  		      $flagSuccessDetalle2=$stmtDetalle2->execute();
         }




         

         $stmtCC = $dbh->prepare("SELECT cc.codigo,cc.monto_reembolso,ccd.monto
        from  caja_chicadetalle ccd,caja_chica cc
        where ccd.cod_cajachica=cc.codigo and ccd.codigo=$cod_ccd");
        $stmtCC->execute();
        $resultCC=$stmtCC->fetch();
        $cod_cajachica=$resultCC['codigo'];
        $monto_reembolso=$resultCC['monto_reembolso'];
        $monto_a_rendir=$resultCC['monto'];
        $monto_faltante=$monto_a_rendir-$suma_importe_fac;
      
      //  //------
        
        $monto_reembolso=$monto_reembolso+$monto_faltante;

        //actualizamos el monto de reeembolso de caja chica
        $stmtCCUpdate = $dbh->prepare("UPDATE caja_chica set monto_reembolso=$monto_reembolso where codigo=$cod_cc");
        $stmtCCUpdate->execute();

        //actualizamos estado en cajachjicadetalle
        $sqlCCD="UPDATE caja_chicadetalle set cod_estado=2,monto_rendicion=$suma_importe_fac where codigo=$cod_ccd";
        $stmtCCD = $dbh->prepare($sqlCCD);
        $stmtCCD->execute();
        //estado de rendicion 
        $fecha_recepcion=date("Y-m-d H:i:s");
        $sql="UPDATE rendiciones set fecha='$fecha_recepcion',cod_estado=2,monto_rendicion=$suma_importe_fac where codigo=$cod_ccd";
        $stmtUR = $dbh->prepare($sql);
        $flagSuccess=$stmtUR->execute();


if($flagSuccess==true){
	showAlertSuccessError(true,"../".$urlListDetalleCajaChica.'&codigo='.$cod_cc.'&cod_tcc='.$cod_ccd);	
}else{
	showAlertSuccessError(false,"../".$urlListDetalleCajaChica.'&codigo='.$cod_cc.'&cod_tcc='.$cod_ccd);
}


?>
