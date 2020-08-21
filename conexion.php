<?php 
class Conexion extends PDO {    

  private $tipo_de_base = 'mysql';
  private $host = 'localhost';
<<<<<<< HEAD
  private $nombre_de_base = 'ibnfinanciero300';
=======
  private $nombre_de_base = 'ibnfinanciero100';
>>>>>>> 1d81c93f3754be0eab016c46cfffa81de4780959
  private $usuario = 'root';
  private $contrasena = '';
  private $port = '3306';

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