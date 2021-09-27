<?php 
class Conexion extends PDO { 
  // private $tipo_de_base = 'mysql';
  // private $host = 'localhost';
  // private $nombre_de_base = 'financiero_cobofar';
  // private $usuario = 'root';
  // private $contrasena = '';
  // private $port = '3306';   

  private $tipo_de_base = 'mysql';
  private $host = '10.10.1.19';
  private $nombre_de_base = 'financiero_cobofar_200';
  private $usuario = 'Cobofar77';
  private $contrasena = 'Cobofar1';
  private $port = '3306';
  public function __construct() {
    //Sobreescribo el método constructor de la clase PDO.
    try{
       parent::__construct($this->tipo_de_base.':host='.$this->host.';dbname='.$this->nombre_de_base.';port='.$this->port, $this->usuario, $this->contrasena,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));//      
    }catch(PDOException $e){
       echo 'Ha surgido un error y no se puede conectar a la base de datos. Detalle: ' . $e->getMessage();
       exit;
    }
  } 
} 

?>