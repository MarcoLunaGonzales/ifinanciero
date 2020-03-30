<?php

//require_once '../layouts/bodylogin.php';
require_once 'conexion.php';
require_once 'functions.php';
require_once 'configModule.php';
ini_set('display_errors',1);

$dbh = new Conexion();
try {
    $cod_entidad = $_POST["codEntidad"];    

    $cod_estadoreferencial = 1;
    //$created_at = $_POST["created_at"];
    $created_by = 1;//$_POST["created_by"];
    //$modified_at = $_POST["modified_at"];
    $modified_by = 1;//$_POST["modified_by"];
    $numeroFilas=$_POST["numero_filas"];
    
    // for ($i=0;$i<count($areas);$i++){
    //     echo $areas[$i]."<br>";
    // }
 
    $stmtDel = $dbh->prepare("DELETE FROM entidades_uo where cod_entidad='$cod_entidad'");
    $stmtDel->execute();
    $flagSuccessDetail=true;
    for ($i=0;$i<$numeroFilas;$i++){
        
        $unidadInsert="";
        $codPadreInsert="";

        if(isset($_POST["unidades".$i])){
            $unidadInsert=$_POST["unidades".$i];
            // $codPadreInsert=$_POST["cod_areaorganizacion_padre".$i];            
        }

        if($unidadInsert!=0 || $unidadInsert!=""){
            $stmt = $dbh->prepare("INSERT INTO entidades_uo(cod_entidad,cod_uo)
             values (:cod_entidad, :cod_uo)");
            $stmt->bindParam(':cod_entidad', $cod_entidad);
            $stmt->bindParam(':cod_uo', $unidadInsert);            

            $flagSuccess=$stmt->execute();
            if($flagSuccess==false){
                $flagSuccessDetail=false;
            }
        }        
    }

    if($flagSuccessDetail==true){
        showAlertSuccessError(true,$urlListEntidades);   
    }else{
        showAlertSuccessError(false,$urlListEntidades);
    }
    
    } catch(PDOException $ex){
        //manejar error
        echo "Un error ocurrio".$ex->getMessage();
    }
?>