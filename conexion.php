<?php 
class Conexion extends PDO {    

  private $tipo_de_base = 'mysql';
  private $host = 'localhost';  
<<<<<<< HEAD
  private $nombre_de_base = 'ibnfinanciero2000';
=======
  private $nombre_de_base = 'ibnfinanciero1000';
>>>>>>> b4d6ac29e4fa945eca4a72e02fcd71d0022cc30b
  private $usuario = 'root';
  private $contrasena = '';
  private $port = '3308';

public function __construct() {
      //Sobreescribo el método constructor de la clase PDO.
      try{
         parent::__construct($this->tipo_de_base.':host='.$this->host.';dbname='.$this->nombre_de_base.';port='.$this->port, $this->usuario, $this->contrasena,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));// 
         //                                                                                                                                                                                             $this->nombre_de_base->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES 'utf8'");
      }catch(PDOException $e){
         echo 'Ha surgido un error y no se puede conectar a la base de datos. Detalle: ' . $e->getMessage();
         exit;
      }
   } 
 } 

?>