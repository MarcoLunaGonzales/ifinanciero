<?php

//require_once '../layouts/bodylogin.php';
require_once 'conexion.php';
require_once 'functions.php';
require_once 'configModule.php';
ini_set('display_errors',1);

$dbh = new Conexion();
try {
    // $cod_areaorganizacion = $_POST["cod_areaorganizacion"]; 
    $cod_areaorganizacion = $_POST["codArea"];       

    $cod_estadoreferencial = 1;
    $created_by = 1;
    $modified_by = 1;
    $numeroFilas=$_POST["numero_filas"];
    
    $cantidad              = 1;
    $cod_estadoreferencial = 1;

    // Reestablecer
    $stmtDel = $dbh->prepare("DELETE FROM cargos_areasorganizacion where cod_areaorganizacion='$cod_areaorganizacion'");
    $stmtDel->execute();
    $flagSuccessDetail=true;
    for ($i=0;$i<$numeroFilas;$i++){
        
        $cargoInsert="";

        if(isset($_POST["cargos".$i])){
            $cargoInsert=$_POST["cargos".$i];
        }

        if($cargoInsert!=0 || $cargoInsert!=""){
            $stmt = $dbh->prepare("INSERT INTO cargos_areasorganizacion(cod_areaorganizacion,cod_cargo,cantidad,cod_estadoreferencial)
             values (:cod_areaorganizacion,:cod_cargo,:cantidad,:cod_estadoreferencial)");
            $stmt->bindParam(':cod_areaorganizacion', $cod_areaorganizacion);
            $stmt->bindParam(':cod_cargo', $cargoInsert);
            $stmt->bindParam(':cantidad', $cantidad);
            $stmt->bindParam(':cod_estadoreferencial', $cod_estadoreferencial);

            $flagSuccess=$stmt->execute();
            if($flagSuccess==false){
                $flagSuccessDetail=false;
            }
        }        
    }

    if($flagSuccessDetail==true){
        showAlertSuccessError(true,$urlListAreas);   
    }else{
        showAlertSuccessError(false,$urlListAreas);
    }
    
    } catch(PDOException $ex){
        //manejar error
        echo "Un error ocurrio".$ex->getMessage();
    }
?>