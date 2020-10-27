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
$stmt = $dbh->prepare("SELECT p.* from v_clientepersonaempresa p where p.NombreCompleto like '%$busqueda%' or p.Identificacion like '%$busqueda%' or p.Nit like '%$busqueda%' limit 20");
$stmt->execute();
//$stmtProveedor->execute();
$proveedores=[];$i=0;
   while ($rowProv = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $codigoX=$rowProv['IdCliente'];
    $nombreX=$rowProv['NombreCompleto'];
    $labelProveedor=$nombreX;
    $imagenProveedor="../assets/img/clientes.jpg";
    if((int)$rowProv['Proveedor']==1){
      $imagenProveedor="../assets/img/proveedores.png"; 
      $labelProveedor.=" (Proveedor)";     
    }else{
      $labelProveedor.=" (Cliente)";       
    }

    if(!($rowProv['Identificacion']==""||$rowProv['Identificacion']==0)){
      if($rowProv['Tipo']=="P"){
        $labelProveedor.=" CI/DNI: ".$rowProv['Identificacion']." ";
        if(!($rowProv['Nit']==""||$rowProv['Nit']==0)){
            $labelProveedor.=" NIT: ".$rowProv['Nit']." ";
        } 
      }else{
        $labelProveedor.=" NIT: ".$rowProv['Identificacion']." "; 
      }  
    }
    $clase="N";
    if($rowProv['IdPais']!=26){
        $clase="I";
    }
   $objetoLista = array('label' => trim($labelProveedor),'value' => $codigoX,'imagen' => $imagenProveedor,'proveedor' => $rowProv['Proveedor'],'tipo' => $rowProv['Tipo'],
    'nombre' => $rowProv['Nombre'],'paterno' => $rowProv['Paterno'],'materno' => $rowProv['Materno'],
    'pais' => $rowProv['IdPais'],'departamento' => $rowProv['IdDepartamento'],'ciudad' => $rowProv['IdCiudad'],'clase' => $clase
    ,'nit' => $rowProv['Nit']
    ,'identificacion' => $rowProv['Identificacion']);
    $proveedores[$i]=$objetoLista;
    $i++;
  }  
//$proveedores = $stmt->fetchAll(PDO::FETCH_OBJ);
echo json_encode($proveedores);
