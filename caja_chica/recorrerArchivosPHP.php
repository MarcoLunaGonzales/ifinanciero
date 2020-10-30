<?php
session_start();
require_once '../conexion.php';
require_once '../styles.php';
require_once '../functions.php';
require_once 'configModule.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

if(isset($_GET["admin"])){
    if($_GET["admin"]=="123456"){
       $sqlInsert4="DELETE FROM archivos_adjuntos_cajachica";
       $stmtInsert4 = $dbh->prepare($sqlInsert4);
       $stmtInsert4->execute();

       // Llamamos a la función para que nos muestre el contenido de la carpeta gallery
       listarArchivos("../assets/archivos-respaldo/archivos_cajachicadetalle/");
    }else{
      echo "ERROR";
    }
}else{
    echo "ERROR";
}
  


function listarArchivos( $path ){
    $dbh = new Conexion();
    // Abrimos la carpeta que nos pasan como parámetro
    $dir = opendir($path);
    // Leo todos los ficheros de la carpeta
    while ($elemento = readdir($dir)){
        // Tratamos los elementos . y .. que tienen todas las carpetas
        if( $elemento != "." && $elemento != ".."){
            // Si es una carpeta
            if( is_dir($path.$elemento) ){
                // Muestro la carpeta
                echo "<p><strong>CARPETA: ". $elemento ."</strong></p>";
                listarArchivos($path.$elemento);
            // Si es un fichero
            } else {
                $directorioRecuperado=str_replace("../","", $path."/".$elemento);
                $tipo=0;
                $descripcion=explode(".",$elemento)[0];
                $target_path=$directorioRecuperado;
                $codigo=substr($path, 63);
                // Muestro el fichero
                 $sqlInsert="INSERT INTO archivos_adjuntos_cajachica(cod_tipoarchivo,descripcion,direccion_archivo,cod_cajachica_detalle) 
                VALUES ('$tipo','$descripcion','$target_path','$codigo')";
                 $stmtInsert = $dbh->prepare($sqlInsert);
                 $stmtInsert->execute();
                echo "<br />". $elemento." path:".$directorioRecuperado." <BR>CODIGO:".$codigo;
            }
        }
    }
}
