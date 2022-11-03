<?php 
class Conexion extends PDO { 

   // private $tipo_de_base = 'mysql';
   // private $host = 'localhost';
   // private $nombre_de_base = 'ibnofinanciero';
   // private $usuario = 'root';
   // private $contrasena = '';
   // private $port = '3306'; 

     //CONEXION INTERNA PRUEBAS 
     // private $tipo_de_base = 'mysql';
     //  private $host = '192.168.30.35';
     //  private $nombre_de_base = 'bdifinanciero';
     //  private $usuario = 'ibnofinanciero';
     //  private $contrasena = 'Financiero1bn0';
     //  private $port = '3306'; 
  
      private $tipo_de_base = 'mysql';
      private $host = 'localhost';
      private $nombre_de_base = 'ibnofinanciero4000';
      private $usuario = 'root';
      private $contrasena = '12345678';
      private $port = '3306'; 

      // private $tipo_de_base = 'mysql';
      // private $host = '192.168.30.35';
      // private $nombre_de_base = 'bdifinanciero';
      // private $usuario = 'ingresobd';
      // private $contrasena = 'ingresoibno';
      // private $port = '3306'; 

public function __construct() {
      //Sobreescribo el método constructor de la clase PDO.
      try{
         //parent::__construct($this->tipo_de_base.':host='.$this->host.';dbname='.$this->nombre_de_base.';port='.$this->port, $this->usuario, $this->contrasena,array(PDO::ATTR_PERSISTENT => 'buff',PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));//
         parent::__construct($this->tipo_de_base.':host='.$this->host.';dbname='.$this->nombre_de_base.';port='.$this->port, $this->usuario, $this->contrasena,array(PDO::ATTR_PERSISTENT => 'TRUE',PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));//
         //parent::__construct($this->tipo_de_base.':host='.$this->host.';dbname='.$this->nombre_de_base.';port='.$this->port, $this->usuario, $this->contrasena);//
      }catch(PDOException $e){
         echo 'Ha surgido un error y no se puede conectar a la base de datos. Detalle: ' . $e->getMessage();
         exit;
      }
   } 
 } 

?>