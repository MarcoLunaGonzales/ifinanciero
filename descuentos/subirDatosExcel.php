<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';
$dbh = new Conexion();

$codDescuento=$_POST["codDescuento"];
$codMes=$_POST["codMes"];
$codGestion=$_POST["codGestion"];
$opcionCargar=$_POST["opcionCargar"];
$codEstado="1";

$ruta="descuentos/upload/";

foreach ($_FILES as $key){
    $ruta_temporal=$key["tmp_name"];
    //$nombre_original = $key["name"];

    if($key["type"]=="text/csv" or $key["type"]=="application/vnd.ms-excel"){
        $nombre_nuevo= formatoNombreArchivoExcel();
        $destino=$ruta.$nombre_nuevo;
        move_uploaded_file($ruta_temporal,$destino);
        $alert=true;

    }else{
        $alert=false;
        showAlertSuccessError($alert,$urlSubirDescuentoExcel."&cod_mes=".$codMes."&cod_descuento=".$codDescuento);
    }
}


if($alert==true){
$delimitador = ";";
$longitudDeLinea = 1000;
$x=0;
$a=array();
$b=array();
$c=array();
$datos=array();
$fichero=fopen($destino,'r');
$k=0;


//sobreescribir existentes e insertar nuevos
if($opcionCargar==1){

while((($datos=fgetcsv($fichero,$longitudDeLinea,$delimitador))!=FALSE)){
    $x++;
    $a[$k]=$datos[0];
    $b[$k]=$datos[1];
    $c[$k]=formatearNumerosExcel($datos[2]);

    if($x>1){
        //inserta nuevos
        if((verificarPersonaMes($a[$k], $codMes, $codDescuento)==0) and (verificarExistenciaPersona($a[$k])==true)){
        $stmt = $dbh->prepare("INSERT INTO descuentos_personal_mes (cod_descuento, cod_personal,cod_gestion,cod_mes,monto, cod_estadoreferencial) 
        VALUES ($codDescuento,$a[$k],$codGestion,$codMes,$c[$k],$codEstado)");
        $flagSuccess=$stmt->execute();    
        }
        else{
            //actualiza los existentes
            $stmt = $dbh->prepare("UPDATE descuentos_personal_mes SET monto=$c[$k] 
            WHERE cod_descuento=$codDescuento and cod_gestion=$codGestion and cod_personal=$a[$k] and cod_mes=$codMes and cod_estadoreferencial=1");

        $flagSuccess=$stmt->execute();  

        }  
    }
    $k++;
}

showAlertSuccessError($flagSuccess,$urlListMesPersona."&cod_mes=".$codMes."&cod_descuento=".$codDescuento);

}


//mantener existentes e insertar nuevos
if($opcionCargar==2){

    while((($datos=fgetcsv($fichero,$longitudDeLinea,$delimitador))!=FALSE)){
        $x++;
        $a[$k]=$datos[0];
        $b[$k]=$datos[1];
        $c[$k]=formatearNumerosExcel($datos[2]);
    
        if($x>1){
            //inserta nuevos
        if((verificarPersonaMes($a[$k], $codMes, $codDescuento)==0) and (verificarExistenciaPersona($a[$k])==true)){
            $stmt = $dbh->prepare("INSERT INTO descuentos_personal_mes (cod_descuento, cod_personal,cod_gestion,cod_mes,monto, cod_estadoreferencial) 
            VALUES ($codDescuento,$a[$k],$codGestion,$codMes,$c[$k],$codEstado)");
            $flagSuccess=$stmt->execute();    
            }
            $flagSuccess=true;
        }
        $k++;
    }
    
    showAlertSuccessError($flagSuccess,$urlListMesPersona."&cod_mes=".$codMes."&cod_descuento=".$codDescuento);
    
    }


//borrar todo y cargar de nuevo
if($opcionCargar==3){

    while((($datos=fgetcsv($fichero,$longitudDeLinea,$delimitador))!=FALSE)){
        $x++;
        $a[$k]=$datos[0];
        $b[$k]=$datos[1];
        $c[$k]=formatearNumerosExcel($datos[2]);
    
        if($x>1){
            //eliminar lógicamente los existentes
            if(verificarExistenciaPersona($a[$k])==true){
            $stmte = $dbh->prepare("UPDATE descuentos_personal_mes SET cod_estadoreferencial=2 
            WHERE cod_descuento=$codDescuento and cod_gestion=$codGestion and cod_mes=$codMes and cod_personal=$a[$k] ");

            $flagSuccess=$stmte->execute();  


            $stmt = $dbh->prepare("INSERT INTO descuentos_personal_mes (cod_descuento, cod_personal,cod_gestion,cod_mes,monto, cod_estadoreferencial) 
            VALUES ($codDescuento,$a[$k],$codGestion,$codMes,$c[$k],$codEstado)");
            $flagSuccess=$stmt->execute();      
            }
        }
        $k++;
    }
    
    showAlertSuccessError($flagSuccess,$urlListMesPersona."&cod_mes=".$codMes."&cod_descuento=".$codDescuento);
    
    }
}
?>