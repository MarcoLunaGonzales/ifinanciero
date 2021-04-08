<?php 
class Conexion extends PDO {    

   private $tipo_de_base = 'mysql';
   private $host = '200.105.199.166';
   private $nombre_de_base = 'bdifinanciero';
   private $usuario = 'ingresofm';
   private $contrasena = 'minka123';
   private $port = '4606';
    


public function __construct() {
      //Sobreescribo el método constructor de la clase PDO.
      try{
         parent::__construct($this->tipo_de_base.':host='.$this->host.';dbname='.$this->nombre_de_base.';port='.$this->port, $this->usuario, $this->contrasena,array(PDO::ATTR_PERSISTENT => true,PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));// 
         //                                                                                                                                                                                             $this->nombre_de_base->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES 'utf8'");
      }catch(PDOException $e){
         echo 'Ha surgido un error y no se puede conectar a la base de datos. Detalle: ' . $e->getMessage();
         exit;
      }
   } 
 } 

?>