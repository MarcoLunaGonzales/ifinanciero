<?php 
class Conexion extends PDO {       

public function __construct() {
      //Sobreescribo el método constructor de la clase PDO.
      try{
         require_once 'config.php';
         
         // Oficial
         parent::__construct(DATABASE_DRIVER.':host='.DATABASE_HOST.';dbname='.DATABASE_NAME.';port='.DATABASE_PORT, DATABASE_USER, DATABASE_PASSWORD,array(PDO::ATTR_PERSISTENT => 'TRUE',PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

      }catch(PDOException $e){
         echo 'Ha surgido un error y no se puede conectar a la base de datos. Detalle: ' . $e->getMessage();
         exit;
      }
   } 
 } 

?>