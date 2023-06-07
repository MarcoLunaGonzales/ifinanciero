<?php 
class ConexionIBNORCA extends PDO { 
    
  public function __construct() {
    //Sobreescribo el método constructor de la clase PDO.
    try{
      
      require_once 'config.php';
         
      // Oficial
      parent::__construct(DATABASE_DRIVER.':host='.DATABASE_HOST_EXT.';dbname='.DATABASE_NAME_EXT.';port='.DATABASE_PORT_EXT, DATABASE_USER_EXT, DATABASE_PASSWORD_EXT,array(PDO::ATTR_PERSISTENT => 'TRUE',PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

   }catch(PDOException $e){
       echo 'Ha surgido un error y no se puede conectar a la base de datos. Detalle: ' . $e->getMessage();
       exit;
    }
  } 
} 

?>