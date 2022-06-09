<?php
require_once 'conexion.php';
require_once 'functions.php';


$dbh = new Conexion();
$codigo=$_POST['codigo'];


$stmtANT = $dbh->prepare("SELECT * FROM activosfijosimagen where codigo =:codigo");
//Ejecutamos;
$stmtANT->bindParam(':codigo',$codigo);
$stmtANT->execute();
$resultANT = $stmtANT->fetch();
//$codigo = $result['codigo'];
$imagenANT = $resultANT['imagen'];

$flagSuccess=true;
if ($imagenANT != $_FILES['image']['name'] AND strlen($_FILES['image']['name']) > 1){//solo si es diferente actualizar
    //entra
    //echo "reemplazar";
    $results = $dbh->query("SELECT * from activosfijosimagen where codigo = ".$codigo)->fetchAll(PDO::FETCH_ASSOC);
    if(count($results))     
    {
        $stmt3 = $dbh->prepare("UPDATE activosfijosimagen set imagen = :imagen where codigo = :codigo");    
    } else {
        $stmt3 = $dbh->prepare("INSERT into activosfijosimagen (codigo, imagen) values (:codigo, :imagen)");
    }

                
    $stmt3->bindParam(':codigo', $codigo);
    $stmt3->bindParam(':imagen', $_FILES['image']['name']);//la url esta poniendo
    //if (move_uploaded_file($_FILES['image']['name'], APP_PATH . DIRECTORY_SEPARATOR ."imagenes".DIRECTORY_SEPARATOR.$_FILES['image']['name']))
    //echo $_FILES['image']['name']."...  ";
    //$archivo = imagenes".DIRECTORY_SEPARATOR.$_FILES['image']['name'];
    //$archivo = "d:\\UwAmp\\www\\ibno\\imagenes\\".$_FILES['image']['name'];//funciona
    $archivo = __DIR__.DIRECTORY_SEPARATOR."imagenes".DIRECTORY_SEPARATOR.$_FILES['image']['name'];
    //esta guardando en activosfijos\imagenes

    //echo $archivo;
    if (move_uploaded_file($_FILES['image']['tmp_name'], $archivo))
        echo "correcto";
    else
        echo "error".$_FILES["image"]["error"];//sale error 0

    $flagSuccess=$stmt3->execute();
}

       
        
    showAlertSuccessError($flagSuccess,'?opcion=activosfijosLista');
?>