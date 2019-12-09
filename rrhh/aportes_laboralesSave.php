<?php

//require_once '../layouts/bodylogin.php';
require_once 'conexion.php';
require_once 'functions.php';
require_once 'configModule.php';
ini_set('display_errors',1);

$dbh = new Conexion();

$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//para mostrar errores en la ejecucion

try {
    $codigo=$_POST["codigo"];
    $salario_minimo_nacional=$_POST["salario_minimo_nacional"];
    $cuenta_individual_vejez=$_POST["cuenta_individual_vejez"];
    $seguro_invalidez=$_POST["seguro_invalidez"];
    $comision_afp=$_POST["comision_afp"];
    $provivienda=$_POST["provivienda"];
    $iva=$_POST["iva"];
    $asa=$_POST["asa"];
    $aporte_nac_solidario_13=$_POST["aporte_nac_solidario_13"];
    $aporte_nac_solidario_25=$_POST["aporte_nac_solidario_25"];
    $aporte_nac_solidario_35=$_POST["aporte_nac_solidario_35"];
    $estado=1;//$_POST["estado"];
    //$created_at=$_POST["created_at"];
    $created_by=1;//$_POST["created_by"];
    //$modified_at=$_POST["modified_at"];
    $modified_by=1;//$_POST["modified_by"];

    if ($codigo == 0){
        $stmt = $dbh->prepare("INSERT INTO 
            aportes_laborales(salario_minimo_nacional,cuenta_individual_vejez,seguro_invalidez,comision_afp,provivienda,iva,asa,aporte_nac_solidario_13,aporte_nac_solidario_25,aporte_nac_solidario_35,estado,created_by,modified_by)
             values (:salario_minimo_nacional,:cuenta_individual_vejez,:seguro_invalidez,:comision_afp,:provivienda,:iva,:asa,:aporte_nac_solidario_13,:aporte_nac_solidario_25,:aporte_nac_solidario_35,:estado,:created_by,:modified_by)");
        //Bind
        $stmt->bindParam(':salario_minimo_nacional', $salario_minimo_nacional);
        $stmt->bindParam(':cuenta_individual_vejez', $cuenta_individual_vejez);
        $stmt->bindParam(':seguro_invalidez', $seguro_invalidez);
        $stmt->bindParam(':comision_afp', $comision_afp);
        $stmt->bindParam(':provivienda', $provivienda);
        $stmt->bindParam(':iva', $iva);
        $stmt->bindParam(':asa', $asa);
        $stmt->bindParam(':aporte_nac_solidario_13', $aporte_nac_solidario_13);
        $stmt->bindParam(':aporte_nac_solidario_25', $aporte_nac_solidario_25);
        $stmt->bindParam(':aporte_nac_solidario_35', $aporte_nac_solidario_35);
        $stmt->bindParam(':estado', $estado);
        //$stmt->bindParam(':created_at', $created_at);
        $stmt->bindParam(':created_by', $created_by);
        //$stmt->bindParam(':modified_at', $modified_at);
        $stmt->bindParam(':modified_by', $modified_by);

        $flagSuccess=$stmt->execute();
        $tabla_id = $dbh->lastInsertId();

        showAlertSuccessError($flagSuccess,$urlListaportes_laborales);

        //$stmt->debugDumpParams();
    } else {//upd
        
            //prepare
        $stmt = $dbh->prepare("UPDATE aportes_laborales set salario_minimo_nacional=:salario_minimo_nacional,
        cuenta_individual_vejez=:cuenta_individual_vejez,seguro_invalidez=:seguro_invalidez,comision_afp=:comision_afp,
        provivienda=:provivienda,iva=:iva,asa=:asa,aporte_nac_solidario_13=:aporte_nac_solidario_13,
        aporte_nac_solidario_25=:aporte_nac_solidario_25,aporte_nac_solidario_35=:aporte_nac_solidario_35,
        estado=:estado,created_by=:created_by,modified_by=:modified_by where codigo = :codigo");
        //bind
        $stmt->bindParam(':codigo', $codigo);
        $stmt->bindParam(':salario_minimo_nacional', $salario_minimo_nacional);
        $stmt->bindParam(':cuenta_individual_vejez', $cuenta_individual_vejez);
        $stmt->bindParam(':seguro_invalidez', $seguro_invalidez);
        $stmt->bindParam(':comision_afp', $comision_afp);
        $stmt->bindParam(':provivienda', $provivienda);
        $stmt->bindParam(':iva', $iva);
        $stmt->bindParam(':asa', $asa);
        $stmt->bindParam(':aporte_nac_solidario_13', $aporte_nac_solidario_13);
        $stmt->bindParam(':aporte_nac_solidario_25', $aporte_nac_solidario_25);
        $stmt->bindParam(':aporte_nac_solidario_35', $aporte_nac_solidario_35);
        $stmt->bindParam(':estado', $estado);
        //$stmt->bindParam(':created_at', $created_at);
        $stmt->bindParam(':created_by', $created_by);
        //$stmt->bindParam(':modified_at', $modified_at);
        $stmt->bindParam(':modified_by', $modified_by);
        $flagSuccess=$stmt->execute();
        showAlertSuccessError($flagSuccess,$urlListaportes_laborales);

    }
    
    } catch(PDOException $ex){
        //manejar error
        echo "Un error ocurrio".$ex->getMessage();
    }
?>