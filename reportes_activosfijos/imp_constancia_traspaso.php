<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';
require_once __DIR__.'/../functions.php';
$dbh = new Conexion();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
set_time_limit(300);
//RECIBIMOS LAS VARIABLES

$cod_unidadorganizacional=$_POST["cod_uo"];
$cod_area = $_POST['cod_area'];
$cod_responsable = $_POST["cod_responsables_responsable"];
//$cod_responsable2 = $_POST["cod_responsables_responsable2"];
$cod_responsable2 = "";
//origen
$cod_unidadorganizacionaldesde=$_POST["cod_uodesde"];
$cod_areadesde = $_POST['cod_areadesde'];
$cod_responsabledesde = $_POST["cod_responsables_responsabledesde"];
$desde=$_POST['desde'];
$hasta=$_POST['hasta'];
$sqladd="";
if($cod_responsabledesde <> -100){
    $sqladd=" having unidad_origen=$cod_unidadorganizacionaldesde and area_origen=$cod_areadesde and personal_origen=$cod_responsabledesde;";
}else{//trasnferencia nueva
    //$cod_responsabledesde=obtenerValorConfiguracion(101);//responsable de entrega activos fijos
    $cod_responsabledesde=0;
}
//$cod_autorizaactivofijo=obtenerValorConfiguracion(100);//responsable de activos fijos
$cod_autorizaactivofijo=0;
try{
    $sqlActivos="SELECT c.fechaasignacion, c.codigo,a.codigoactivo,a.activo,
(SELECT cod_unidadorganizacional from activofijos_asignaciones where codigo!=c.codigo and cod_activosfijos=c.cod_activosfijos order by fechaasignacion desc limit 1)as unidad_origen,
(SELECT cod_area from activofijos_asignaciones where codigo!=c.codigo and cod_activosfijos=c.cod_activosfijos order by fechaasignacion desc limit 1)as area_origen,
(SELECT cod_personal from activofijos_asignaciones where codigo!=c.codigo and cod_activosfijos=c.cod_activosfijos order by fechaasignacion desc limit 1)as personal_origen 

FROM `activofijos_asignaciones` c join activosfijos a on a.codigo=c.cod_activosfijos where c.fechaasignacion between '$desde 00:00:00' and '$hasta 23:59:59'
and c.cod_personal=$cod_responsable and c.cod_unidadorganizacional=$cod_unidadorganizacional and c.cod_area=$cod_area
 $sqladd";  
//echo $sqlActivos;

    $stmtActivos = $dbh->prepare($sqlActivos);
    $stmtActivos->execute();
?>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link href="../assets/libraries/plantillaPDFSolicitudesRecursos.css" rel="stylesheet" />
   </head><body>
<!-- fin formato cabeza fija para pdf--> 

<!--CONTENIDO-->
     <table class="table">
         <tr>
            <td rowspan="2" class="text-center imagen-td"><img class="imagen-logo-izq_2" src="../assets/img/logo_ibnorca_origen_2.png" width="50" ></td>
            <td class="s2 text-center" colspan="3">Instituto Boliviano de Normalización y Calidad</td>

        </tr>
        <tr>
            <td class="s2 text-center" colspan="3">ACTIVOS FIJOS</td>
        </tr>
        <tr>
            <td class="s2 text-center" colspan="4">CONSTANCIA DE TRANSFERENCIA DE ACTIVOS & FUNGIBLES</td>
        </tr>
        <tr>
            <td class="s3 text-left bg-celeste" width="18%">Fecha:</td>
            <td class="s3 text-left" width="39%"><?=strftime('%d/ %m/ %Y',strtotime($desde))?> - <?=strftime('%d/ %m/ %Y',strtotime($hasta))?></td>
            <td class="s3 text-right" colspan="2"><small><small><small></small></small></small></td>
        </tr>
        <tr>
            <td class="s3 text-left bg-celeste">ENTREGA:</td>
            <td class="s3 text-left"><?=namePersonal($cod_responsabledesde)?></td>
            <td class="s3 text-left bg-celeste">RECIBE:</td>
            <td class="s3 text-left"><?=namePersonal($cod_responsable)?></td>
        </tr>
        <tr>
            <td class="s3 text-left bg-celeste">Oficina:</td>
            <td class="s3 text-left"><?=abrevUnidad_solo($cod_unidadorganizacionaldesde)?> - <?=abrevUnidad_solo($cod_unidadorganizacional)?></td>
            <td class="s3 text-left bg-celeste">Area:</td>
            <td class="s3 text-left"><?=abrevArea_solo($cod_areadesde)?> - <?=abrevArea_solo($cod_area)?></td>
        </tr>
     </table>

      <table class="table">
        <tr class="bg-celeste">
            <td class="s3 text-center">N°</td>
            <td class="s3 text-center">CÓDIGO</td>
             <td class="s3 text-center">FECHA</td>
            <td class="s3 text-center" width="70%">DESCRIPCION</td>

        <?php
        $index=1;
        while ($row = $stmtActivos->fetch(PDO::FETCH_ASSOC)) {
          $codActivo=$row['codigoactivo'];
          $activo=$row['activo'];
          $fecha=$row['fechaasignacion'];
           ?>
        <tr>
            <td class="s3 text-center" width="4%"><?=$index?></td>
            <td class="s3 text-center"><?=$codActivo?></td>
            <td class="s3 text-center"><?=strftime('%d/ %m/ %Y',strtotime($fecha))?></td>
            <td class="s3 text-center"><?=$activo?></td>           
        </tr> 
        <?php  
        $index++; 
        }
        
        ?>

     <table class="table">
        <tr class="bg-celeste">
            <td class="s3 text-center">OBSERVACIONES</td>
        </tr>
        <tr>
            <td class="s3 text-left"><br><br><br></td>
        </tr>
     </table>
     <?php if($cod_responsable2!=""){ ?>
        <table class="table">
            <tr class="bg-celeste">
                <td class="s3 text-center" width="25%"><b>FIRMA ENTREGADO</b></td>
                <td class="s3 text-center" width="25%"><b>FIRMA RECIBIDO1</b></td>
                <td class="s3 text-center" width="25%"><b>FIRMA RECIBIDO2</b></td>
                <td class="s3 text-center" width="25%"><b>V°B° ACTIVOS FIJOS</b></td>
            </tr>
            <tr class="">
                <td class="s3 text-center"><br><br><br></td>
                <td class="s3 text-center"><br><br><br></td>
                <td class="s3 text-center"><br><br><br></td>
                <td class="s3 text-center"><br><br><br></td>
            </tr>
            <tr class="bg-celeste">
                <td class="s3 text-center"><b>Nombre:</b> <?=namePersonal($cod_responsabledesde)?></td>
                <td class="s3 text-center"><b>Nombre:</b> <?=namePersonal($cod_responsable)?></td>
                <td class="s3 text-center"><b>Nombre:</b> <?=namePersonal($cod_responsable2)?></td>
                <td class="s3 text-center"><b>Nombre:</b> Lic. <?=namePersonal($cod_autorizaactivofijo)?></td>
            </tr>
        </table>
    <?php }else{ ?>
        <table class="table">
            <tr class="bg-celeste">
                <td class="s3 text-center"><b>FIRMA ENTREGADO</b></td>
                <td class="s3 text-center"><b>FIRMA RECIBIDO</b></td>
                <td class="s3 text-center"><b>V°B° ACTIVOS FIJOS</b></td>
            </tr>
            <tr class="">
                <td class="s3 text-center"><br><br><br></td>
                <td class="s3 text-center"><br><br><br></td>
                <td class="s3 text-center"><br><br><br></td>
            </tr>
            <tr class="bg-celeste">
                <td class="s3 text-center"><b>Nombre:</b> <?=namePersonal($cod_responsabledesde)?></td>
                <td class="s3 text-center"><b>Nombre:</b> <?=namePersonal($cod_responsable)?></td>
                <td class="s3 text-left"><b>Nombre:</b> <?=namePersonal($cod_autorizaactivofijo)?></td>
            </tr>
    </table>
    <?php } ?>
    



<!-- FIN CONTENIDO-->

<!-- formato pie fijo para pdf-->  
</body></html>
<!-- fin formato pie fijo para pdf-->
<?php

    $cont=0;
    while ($rowActivos = $stmtActivos->fetch(PDO::FETCH_ASSOC)) {
     
    }

 
} catch(PDOException $ex){
    echo "Un error ocurrio".$ex->getMessage();
}
