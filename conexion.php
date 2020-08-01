<?php 
class Conexion extends PDO {    

  private $tipo_de_base = 'mysql';
  private $host = 'localhost';
  private $nombre_de_base = 'ibnfinanciero200';
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