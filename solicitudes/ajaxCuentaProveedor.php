<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();
$usuario=$_SESSION['globalUser'];
$codigo=$_GET['codigo'];
 $query="SELECT p.*,pc.nombre as cuenta FROM af_proveedores p,plan_cuentas pc where p.cod_plancuenta=pc.codigo and p.codigo=$codigo"; 
  $stmt = $dbh->prepare($query);
  $stmt->execute();
  $nombrePlan="no tiene relacion con cuentas";
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nombrePlan=$row['cuenta'];     
    }
  echo $nombrePlan;  
            