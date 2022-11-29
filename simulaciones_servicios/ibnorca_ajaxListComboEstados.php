<?php
session_start();
require_once '../conexion.php';
require_once '../conexion_externa.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();
$dbh_ibnorca = new ConexionIBNORCA();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

$item_1=$_GET['item_1'];
$item_2=$_GET['item_2'];
$item_3=$_GET['item_3'];
//cambiar por ibnorca 
/*$sql2="SELECT idestadosiguiente,d_clasificador(idestadosiguiente) AS descr  
        FROM flujoobjeto WHERE idobjeto=$item_1 AND idestado=id_estadoobjeto($item_1,$item_2) AND locate(d_abrevclasificador($item_3),rol)>0 ORDER BY 2";*/

$sql2="SELECT idestadosiguiente,d_clasificador(idestadosiguiente) AS descr  
        FROM flujoobjeto WHERE idobjeto=$item_1 AND idestado=id_estadoobjeto($item_1,$item_2) ORDER BY 2";

//echo $sql2;
$stmt2=$dbh_ibnorca->prepare($sql2);
$stmt2->execute(); 
  while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
    $codigoX=$row2['idestadosiguiente'];
    $descX=$row2['descr'];  
    if($codigoX!=2725){ //para quitar el estado pagado
      if($codigoX!=2729){
        if($codigoX!=3107){
          ?><option value="<?=$codigoX?>"><?=$descX?></option><?php  
        }
      
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