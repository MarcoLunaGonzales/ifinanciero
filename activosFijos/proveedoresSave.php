<?php

//require_once '../layouts/bodylogin.php';
require_once 'conexion.php';
require_once 'functions.php';
require_once 'configModule.php';

$dbh = new Conexion();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
    
//echo "n".$_POST["codigo"]."n"; 
if ($_POST["codigo"] == 0){
    //$cod_empresa=$_POST["cod_empresa"];
    $cod_empresa=1;//$_POST["cod_empresa"];
    $nombre=$_POST["nombre"];
    $created_at=1;//$_POST["created_at"];
    $created_by=1;//$_POST["created_by"];
    $modified_at=1;//$_POST["modified_at"];
    $modified_by=1;//$_POST["modified_by"];
    
    $direccion=$_POST["direccion"];
    $telefono=$_POST["telefono"];
    $email=$_POST["email"];
    $personacontacto=$_POST["personacontacto"];
    try{
        //Prepare
        //echo "entra insert";
        $stmt = $dbh->prepare("INSERT INTO af_proveedores(cod_empresa,nombre,cod_estado,created_at,created_by,modified_at,modified_by,direccion,telefono,email,personacontacto) values 
        (:cod_empresa, :nombre,1, :created_at, :created_by, :modified_at, :modified_by, :direccion, :telefono, :email, :personacontacto)");

        //$stmt = $dbh->prepare("INSERT INTO af_proveedores (cod_empresa,nombre,created_by,modified_by) 
        //values (:cod_empresa, :nombre, :created_by, :modified_by)");
        //Bind
        $stmt->bindParam(':cod_empresa', $cod_empresa);
        $stmt->bindParam(':nombre', $nombre);
        
        $stmt->bindParam(':created_at', $created_at);
        $stmt->bindParam(':created_by', $created_by);
        $stmt->bindParam(':modified_at', $modified_at);
        $stmt->bindParam(':modified_by', $modified_by);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':personacontacto', $personacontacto);
    


        $flagSuccess=$stmt->execute();
        $tabla_id = $dbh->lastInsertId();
        showAlertSuccessError($flagSuccess,$urlListProv);
    } catch(PDOException $ex){
        echo "Un error ocurrio".$ex->getMessage();
    }

} else {
    try{
        
        $codigo=$_POST["codigo"];
        $cod_empresa=1;//$_POST["cod_empresa"];
        $nombre=$_POST["nombre"];

        $modified_by=1;//$_POST["modified_by"];
        $direccion = $_POST["direccion"];
    $telefono = $_POST["telefono"];
    $email = $_POST["email"];
    $personacontacto = $_POST["personacontacto"];
        //prepare
        $stmt = $dbh->prepare("UPDATE af_proveedores set cod_empresa=:cod_empresa,nombre=:nombre,
        modified_by=:modified_by,direccion=:direccion,telefono=:telefono,
        email=:email,personacontacto=:personacontacto where codigo = :codigo");

        //$stmt = $dbh->prepare("UPDATE af_proveedores set cod_empresa=:cod_empresa,nombre=:nombre,
        //modified_by=:modified_by where codigo = :codigo");
        //bind
        $stmt->bindParam(':codigo', $codigo);
        $stmt->bindParam(':cod_empresa', $cod_empresa);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':modified_by', $modified_by);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':personacontacto', $personacontacto);

        $flagSuccess=$stmt->execute();
         
        showAlertSuccessError($flagSuccess,$urlListProv);
    } catch(PDOException $ex){
        echo "Un error ocurrio".$ex->getMessage();
    }

}
?>