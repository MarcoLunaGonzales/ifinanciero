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

      //CONEXION IBNORCA PRUEBAS EXTERNO
      // private $tipo_de_base = 'mysql';
      // private $host = 'lpsit.ibnorca.org';
      // private $nombre_de_base = 'bdifinanciero';
      // private $usuario = 'ingresobd';
      // private $contrasena = 'ingresoibno';
      // private $port = '3360'; 

  //CONEXION INTERNA PRUEBAS 
      // private $tipo_de_base = 'mysql';
      // private $host = 'localhost';
      // private $nombre_de_base = 'ibnorca_siat';
      // private $usuario = 'root';
      // private $contrasena = '4868422Marco';
      // private $port = '3306'; 

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
      // Oficial SIN.ENV
      //  parent::__construct($this->tipo_de_base.':host='.$this->host.';dbname='.$this->nombre_de_base.';port='.$this->port, $this->usuario, $this->contrasena,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));//

      // Oficial
      parent::__construct(DATABASE_DRIVER_EXT.':host='.DATABASE_HOST_EXT.';dbname='.DATABASE_NAME_EXT.';port='.DATABASE_PORT_EXT, DATABASE_USER_EXT, DATABASE_PASSWORD_EXT,array(PDO::ATTR_PERSISTENT => 'TRUE',PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

   }catch(PDOException $e){
       echo 'Ha surgido un error y no se puede conectar a la base de datos. Detalle: ' . $e->getMessage();
       exit;
    }
  } 
} 

?>