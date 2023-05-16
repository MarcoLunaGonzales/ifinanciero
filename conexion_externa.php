<?php 
class ConexionIBNORCA extends PDO { 

      // private $tipo_de_base = 'mysql';
      // private $host = 'localhost';
      // private $nombre_de_base = 'ibnorca12092022';
      // private $usuario = 'root';
      // private $contrasena = '4868422Marco';
      // private $port = '3306'; 

      //  //CONEXION IBNORCA PRUEBAS
      // private $tipo_de_base = 'mysql';
      // private $host = 'lpsit.ibnorca.org';
      // private $nombre_de_base = 'ibnorca';
      // private $usuario = 'ingresobd';
      // private $contrasena = 'ingresoibno';
      // private $port = '3360'; 

     //CONEXION INTERNA PRUEBAS 
     // private $tipo_de_base = 'mysql';
     //  private $host = '192.168.30.35';
     //  private $nombre_de_base = 'ibnorca';
     //  private $usuario = 'ibnofinanciero';
     //  private $contrasena = 'Financiero1bn0';
     //  private $port = '3306'; 

     //CONEXION INTERNA PRUEBAS 
      // private $tipo_de_base = 'mysql';
      // private $host = 'localhost';
      // private $nombre_de_base = 'ibnorca_siat';
      // private $usuario = 'root';
      // private $contrasena = '4868422Marco';
      // private $port = '3306'; 

  private $tipo_de_base = 'mysql';
      private $host = 'localhost';
      private $nombre_de_base = 'ibnofinanciero6000';
      private $usuario = 'root';
      private $contrasena = '12345678';
      private $port = '3306'; 

    
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