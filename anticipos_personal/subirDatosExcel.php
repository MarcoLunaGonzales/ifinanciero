<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';
$dbh = new Conexion();

$codMes=$_POST["codMes"];
$codGestion=$_POST["codGestion"];
$opcionCargar=$_POST["opcionCargar"];
$codEstado="1";

$ruta="anticipos_personal/upload/";

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
        showAlertSuccessError($alert,$urlSubirExcel."&cod_mes=".$codMes);
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
        if((verificarBonoPersonaMes($a[$k], $codMes, $codBono)==0) and (verificarExistenciaPersona($a[$k])==true)){
        $stmt = $dbh->prepare("INSERT INTO anticipos_personal ( cod_personal,cod_gestion,cod_mes,monto, cod_estadoreferencial) 
        VALUES ($a[$k],$codGestion,$codMes,$c[$k],$codEstado)");
        $flagSuccess=$stmt->execute();    
        }
        else{
            //actualiza los existentes
            $stmt = $dbh->prepare("UPDATE anticipos_personal SET monto=$c[$k] 
            WHERE cod_gestion=$codGestion and cod_personal=$a[$k] and cod_mes=$codMes and cod_estadoreferencial=1");

        $flagSuccess=$stmt->execute();  

        }  
    }
    $k++;
}

 showAlertSuccessError($flagSuccess,$urlListMesPersona."&cod_mes=".$codMes);

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
        if((verificarBonoPersonaMes($a[$k], $codMes, $codBono)==0) and (verificarExistenciaPersona($a[$k])==true)){
            $stmt = $dbh->prepare("INSERT INTO anticipos_personal ( cod_personal,cod_gestion,cod_mes,monto, cod_estadoreferencial) 
            VALUES ($a[$k],$codGestion,$codMes,$c[$k],$codEstado)");
            $flagSuccess=$stmt->execute();    
            }
            $flagSuccess=true;
        }
        $k++;
    }
    
    showAlertSuccessError($flagSuccess,$urlListMesPersona."&cod_mes=".$codMes);
    
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
            $stmte = $dbh->prepare("UPDATE anticipos_personal SET cod_estadoreferencial=2 
            WHERE cod_gestion=$codGestion and cod_mes=$codMes and cod_personal=$a[$k] ");

            $flagSuccess=$stmte->execute();  


            $stmt = $dbh->prepare("INSERT INTO anticipos_personal ( cod_personal,cod_gestion,cod_mes,monto, cod_estadoreferencial) 
            VALUES ($a[$k],$codGestion,$codMes,$c[$k],$codEstado)");
            $flagSuccess=$stmt->execute();      
            }
        }
        $k++;
    }
    
    showAlertSuccessError($flagSuccess,$urlListMesPersona."&cod_mes=".$codMes);
    
    }
}
?>