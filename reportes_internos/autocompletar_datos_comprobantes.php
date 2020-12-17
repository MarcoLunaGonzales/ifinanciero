<?php
session_start();
require_once '../conexion.php';

$dbh = new Conexion();
$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

// Si no hay búsqueda, mostrar un arreglo vacío y salir
if (empty($_GET["busqueda"])) {
    echo "[]";
    exit;
}
$busqueda = $_GET["busqueda"];
$stmt = $dbh->prepare("SELECT p.* from comprobantes_detalle p where p.haber like '%$busqueda%' or p.debe like '%$busqueda%' or p.glosa like '%$busqueda%' limit 20");
$stmt->execute();
//$stmtProveedor->execute();
$proveedores=[];$i=0;
   while ($rowProv = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $codigoX=$rowProv['codigo'];
    $nombreX=$rowProv['glosa'];
    $labelProveedor=$nombreX;
    $imagenProveedor="../assets/img/clientes.jpg";
    /*if((int)$rowProv['Proveedor']==1){
      $imagenProveedor="../assets/img/proveedores.png"; 
      $labelProveedor.=" (Proveedor)";     
    }else{
      $labelProveedor.=" (Cliente)";       
    }*/
   $objetoLista = array('label' => trim($labelProveedor),'value' => $codigoX,'imagen' => $imagenProveedor,
    'nombre' => $rowProv['glosa']);
    $proveedores[$i]=$objetoLista;
    $i++;
  }  
//$proveedores = $stmt->fetchAll(PDO::FETCH_OBJ);
echo json_encode($proveedores);
