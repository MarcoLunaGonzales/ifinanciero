<?php 
class ConexionIBNORCA extends PDO { 

      // private $tipo_de_base = 'mysql';
      // private $host = 'localhost';
      // private $nombre_de_base = 'ibnorca12092022';
      // private $usuario = 'root';
      // private $contrasena = '4868422Marco';
      // private $port = '3306'; 

      //  //CONEXION IBNORCA PRUEBAS
<<<<<<< HEAD
      // private $tipo_de_base = 'mysql';
      // private $host = 'lpsit.ibnorca.org';
      // private $nombre_de_base = 'ibnorca';
      // private $usuario = 'ingresobd';
      // private $contrasena = 'ingresoibno';
      // private $port = '3360'; 

      //CONEXION IBNORCA PRUEBAS EXTERNO
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
=======
      private $tipo_de_base = 'mysql';
      private $host = 'lpsit.ibnorca.org';
      private $nombre_de_base = 'ibnorca';
      private $usuario = 'ingresobd';
      private $contrasena = 'ingresoibno';
      private $port = '3360'; 
>>>>>>> a6e6db04bd8187ba1741d0931b781a93748ac356

      //CONEXION IBNORCA PRUEBAS EXTERNO
      // private $tipo_de_base = 'mysql';
      // private $host = 'lpsit.ibnorca.org';
      // private $nombre_de_base = 'bdifinanciero';
      // private $usuario = 'ingresobd';
      // private $contrasena = 'ingresoibno';
      // private $port = '3360'; 

  //CONEXION INTERNA PRUEBAS 
      private $tipo_de_base = 'mysql';
      private $host = 'localhost';
      private $nombre_de_base = 'ibnorca_siat';
      private $usuario = 'root';
      private $contrasena = '4868422Marco';
      private $port = '3306'; 

      //CONEXION IBNORCA PRUEBAS OFICIAL EXTERNO
      // private $tipo_de_base = 'mysql';
      // private $host = 'lpsit.ibnorca.org';
      // private $nombre_de_base = 'ibnorca';
      // private $usuario = 'ingresobd';
      // private $contrasena = 'ingresoibno';
      // private $port = '3360'; 

    
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