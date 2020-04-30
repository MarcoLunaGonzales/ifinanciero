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

$item_1=$_GET['item_1'];
$item_2=$_GET['item_2'];
$item_3=$_GET['item_3'];

$sql2="SELECT idestadosiguiente, ibnorca.d_clasificador(idestadosiguiente) AS descr  
        FROM ibnorca.flujoobjeto WHERE idobjeto=$item_1 AND idestado=ibnorca.id_estadoobjeto($item_1,$item_2) AND locate(ibnorca.d_abrevclasificador($item_3),rol)>0 ORDER BY 2";
$stmt2=$dbh->prepare($sql2);
$stmt2->execute(); 
  while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
    $codigoX=$row2['idestadosiguiente'];
    $descX=$row2['descr'];  
    if($codigoX!=2725){ //para quitar el estado pagado
      if($codigoX!=2729){
        ?><option value="<?=$codigoX?>"><?=$descX?></option><?php
      }else{
        
      }
      
    }else{
       $saldoSol=obtenerTodoPagoSolicitud($item_2); 
       if($saldoSol<=0){
        ?><option value="<?=$codigoX?>"><?=$descX?></option><?php
       }
    }   
  }
?> 