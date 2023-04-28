<?php 
class Conexion extends PDO { 

      // //CONEXION IBNORCA OFICIAL
      // private $tipo_de_base = 'mysql';
      // private $host = 'lpsit.ibnorca.org';
      // private $nombre_de_base = 'bdifinanciero';
      // private $usuario = 'ingresobd';
      // private $contrasena = 'ingresoibno';
      // private $port = '4606'; 

      // LOCAL
      // private $tipo_de_base = 'mysql';
      // private $host = 'localhost';
      // private $nombre_de_base = 'ifinanciero';
      // private $usuario = 'root';
      // private $contrasena = '';
      // private $port = '3306'; 

      // PRUEBAS EXTERNO
      // private $tipo_de_base = 'mysql';
      // private $host = 'lpsit.ibnorca.org';
      // private $nombre_de_base = 'bdifinanciero';
      // private $usuario = 'ingresobd';
      // private $contrasena = 'ingresoibno';
      // private $port = '3360';


      //CONEXION IBNORCA PRUEBAS OFICIAL INTERNO
      // private $tipo_de_base = 'mysql';
      // private $host = '192.168.30.35';
      // private $nombre_de_base = 'bdifinanciero';
      // private $usuario = 'ibnofinanciero';
      // private $contrasena = 'Financiero1bn0';
      // private $port = '3306'; 

        //CONEXION IBNORCA PRUEBAS OFICIAL INTERNO
      // private $tipo_de_base = 'mysql';
      // private $host = '192.168.20.12';
      // private $nombre_de_base = 'bdifinanciero';
      // private $usuario = 'ingresobd';
      // private $contrasena = 'ingresoibno';
      // private $port = '3306'; 

public function __construct() {
      //Sobreescribo el método constructor de la clase PDO.
      try{
         require_once 'config.php';
         
         set_time_limit(0);
         error_reporting(0);
         
         // Oficial SIN .ENV
         // parent::__construct($this->tipo_de_base.':host='.$this->host.';dbname='.$this->nombre_de_base.';port='.$this->port, $this->usuario, $this->contrasena,array(PDO::ATTR_PERSISTENT => 'TRUE',PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
         
         // Oficial
         parent::__construct(DATABASE_DRIVER.':host='.DATABASE_HOST.';dbname='.DATABASE_NAME.';port='.DATABASE_PORT, DATABASE_USER, DATABASE_PASSWORD,array(PDO::ATTR_PERSISTENT => 'TRUE',PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

      }catch(PDOException $e){
         echo 'Ha surgido un error y no se puede conectar a la base de datos. Detalle: ' . $e->getMessage();
         exit;
      }
   } 
 } 

?>