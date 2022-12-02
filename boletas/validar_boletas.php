<?php

require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();
?>
  <div class="content">
    <div class="container-fluid">
      <div class="col-md-12">     
        <div class="card">
<?php 
if (isset($_GET['ws'])) {
        
    $string_codigo=$_GET['ws'];
    //echo $string_codigo."****";
    $array_codigo=explode('.', $string_codigo);
    $cod_personal=$array_codigo[0];
    $cod_planilla=$array_codigo[1];
    $cod_mes=$array_codigo[2];
    $cod_gestion=$array_codigo[3];
    $numero_exa=hexdec($array_codigo[4]);//llegará en exadecimal
    //se convierte hexa a decimal
    //generando Clave unico 
    $nuevo_numero=$cod_personal+$cod_planilla+$cod_mes+$cod_gestion;
    $cantidad_digitos=strlen($nuevo_numero);
    $numero_adicional=$nuevo_numero+100+$cantidad_digitos;
    // $numero_exa=dechex($numero_adicional);//convertimos de decimal a hexadecimal 


    $sqlGestion = "SELECT CONCAT_WS(' ',p.primer_nombre,p.paterno,p.materno) as personal, c.nombre as cargo
from personal p join cargos c on p.cod_cargo=c.codigo
where p.codigo=$cod_personal";
    $stmtGestion = $dbh->prepare($sqlGestion);
    $stmtGestion->execute();
    $resultGestion=$stmtGestion->fetch();
    $personal = $resultGestion['personal'];
    $cargo = $resultGestion['cargo'];

    if($numero_adicional==$numero_exa){ ?>
        <div class="card-body " style="background: green;">
        <center><h2><b>BOLETA CORRECTA</b><br><i class="fa fa-check-circle"></i></center></h2>
        <h5><b>Personal:</b> <?=$personal?><br><b>Cargo:</b> <?=$cargo?><br>
        <b>Mes-Gestión:</b> <?=nombreMes($cod_mes)?>-<?=nameGestion($cod_gestion)?><br>
        </h5>
    <?php }else{ ?>
        <div class="card-body " style="background: red;">
        <center><h2><b>DATOS DE BOLETA INCORRECTO</b><br><i class="fa fa-times"></i></center></h2>
    <?php }
    ?>
  </div>
     
<?php 
}else{?>
    <div class="card-body " style="background: red;">
        <center><h2><b>ACCESO DENEGADO..!!!</b><br><i class="fa fa-times"></i></center></h2>
    </div>
<?php }


?>

   </div>
      </div>
    </div>
  </div>




