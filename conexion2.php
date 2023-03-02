<?php 
class Conexion2 extends PDO { 
      
      // CONEXION IBNORCA PRUEBAS
      private $tipo_de_base = 'mysql';
      private $host = 'lpsit.ibnorca.org';
      private $nombre_de_base = 'bdifinanciero';
      private $usuario = 'ingresobd';
      private $contrasena = 'ingresoibno';
      private $port = '4606'; 

public function __construct() {
      //Sobreescribo el método constructor de la clase PDO.
      try{
         parent::__construct($this->tipo_de_base.':host='.$this->host.';dbname='.$this->nombre_de_base.';port='.$this->port, $this->usuario, $this->contrasena,array(PDO::ATTR_PERSISTENT => 'buff',PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));//
      }catch(PDOException $e){
         echo 'Ha surgido un error y no se puede conectar a la base de datos. Detalle: ' . $e->getMessage();
         exit;
      }
   } 
 } 

?>