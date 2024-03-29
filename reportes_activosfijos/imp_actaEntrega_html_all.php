

<?php //ESTADO FINALIZADO


ob_start();
require_once __DIR__.'/../conexion.php';
require '../assets/phpqrcode/qrlib.php';

//require_once 'configModule.php';
require_once __DIR__.'/../functions.php';
$dbh = new Conexion();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
//RECIBIMOS LAS VARIABLES
setlocale(LC_TIME, "Spanish");
$codigo_personal = $_GET["cod_personal"];//codigoactivofijo
try{

    $stmt = $dbh->prepare("SELECT (select ar.abreviatura from areas ar where ar.codigo=p.cod_area) as area,CONCAT_WS(' ',p.primer_nombre,p.paterno,p.materno) as personal,(select c.nombre from cargos c where c.codigo=p.cod_cargo) as cargo,p.identificacion
    from personal p where codigo=:codigo");
//Ejecutamos;
$stmt->bindParam(':codigo',$codigo_personal);
$stmt->execute();

$result = $stmt->fetch();
$area = $result['area'];
$personal = $result['personal'];
$cargo = $result['cargo'];
$identificacion = $result['identificacion'];


$sqlActivos="SELECT a.codigo,a.codigoactivo,a.activo as otrodato,a.cod_tiposbienes,(select tb.abreviatura from depreciaciones tb
 where tb.codigo=a.cod_depreciaciones)as tipo_bien,
 (select f.fechaasignacion from activofijos_asignaciones f where f.cod_activosfijos=a.codigo and f.cod_personal=a.cod_responsables_responsable order by f.codigo limit 1) as fechaasignacion,(select f2.fecha_recepcion from activofijos_asignaciones f2 where f2.cod_activosfijos=a.codigo and f2.cod_personal=a.cod_responsables_responsable order by f2.codigo limit 1) as fecha_recepcion,(select f2.observaciones_recepcion from activofijos_asignaciones f2 where f2.cod_activosfijos=a.codigo and f2.cod_personal=a.cod_responsables_responsable order by f2.codigo limit 1) as observaciones_recepcion
from activosfijos a 
where a.cod_estadoactivofijo=1 and  a.cod_responsables_responsable=$codigo_personal order by tipo_bien, otrodato";  
$stmtActivos = $dbh->prepare($sqlActivos);
$stmtActivos->execute();
$stmtActivos->bindColumn('codigo', $codigoSis);
$stmtActivos->bindColumn('codigoactivo', $codigoactivo);
$stmtActivos->bindColumn('otrodato', $otrodato);
$stmtActivos->bindColumn('cod_tiposbienes', $cod_tiposbienes);
$stmtActivos->bindColumn('tipo_bien', $tipo_bien);
$stmtActivos->bindColumn('fechaasignacion', $fechaasignacion);
$stmtActivos->bindColumn('fecha_recepcion', $fecha_recepcion);
if($fecha_recepcion=="00/00/0000"){
    $fecha_recepcion="";
}
$stmtActivos->bindColumn('observaciones_recepcion', $observaciones_recepcion);


$dia=date('d');
$mes=date('m');
$nom_mes=nombreMes($mes);
$anio=date('Y');



$sql_a="SELECT a.fechaasignacion as fechaasignacion,(select CONCAT_WS(' ',p.paterno,p.primer_nombre) from personal p where p.codigo=a.cod_personal_anterior) as personal, a.cod_personal_anterior, (select af.codigoactivo from activosfijos af where af.codigo=a.cod_activosfijos)as codigoactivo from activofijos_asignaciones a where a.cod_personal='$codigo_personal' and a.fechaasignacion>'2021-11-01' order by a.codigo desc limit 0,10";  
$stmtAsignaciones = $dbh->prepare($sql_a);
$stmtAsignaciones->execute();
$stmtAsignaciones->bindColumn('fechaasignacion', $fechaasignacion_a);
$stmtAsignaciones->bindColumn('personal', $personal_a);
$stmtAsignaciones->bindColumn('cod_personal_anterior', $cod_personal_anterior);
$stmtAsignaciones->bindColumn('codigoactivo', $codActivoAsignado);


$sql_t="SELECT a.fechaasignacion as fechaasignacion,(select CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=a.cod_personal) as personal, (select af.codigoactivo from activosfijos af where af.codigo=a.cod_activosfijos)as codigoactivo from activofijos_asignaciones a where a.cod_personal_anterior=$codigo_personal order by a.codigo desc limit 0,10 ";  
$stmtTransfer = $dbh->prepare($sql_t);
$stmtTransfer->execute();
$stmtTransfer->bindColumn('fechaasignacion', $fechaasignacion_t);
$stmtTransfer->bindColumn('personal', $personal_t);
$stmtTransfer->bindColumn('codigoactivo', $codActivoTransfer);

if($cod_personal_anterior==0){
    $personal_a="Asignacion Inicial";
}

?>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <style type="text/css">

        body{font-family:arial, serif;font-size: 12px;}



@page { margin-top: 50px; margin-left: 40px; margin-right: 40px; margin-bottom: 50px;}

.table{
width: 100%;  
border-collapse: collapse;
}
.table2{
width: 100%;  
}
.table .fila-primary td{

    border-top: 0px;
    border-right: 0px;
    border-bottom: 1px solid black;
    border-left: 0px;
}
.table .fila-totales td{
   padding: 5px;
    border-bottom: 0px;
    border-right: 0px;
    border-top: 1px solid black;
    border-left: 0px;
}
.table tr td {
    border: 1px solid black;
}
.table tr th {
    border: 1px solid black;
}
.td-border-none{
  border: none !important;
}
.td-color-celeste{
  background:#BFFBF1;
}
.table .table-title{
 font-size: 8px;
}
</style>
   </head><body>
    <table width="100%" >
        <tr><td>
            <table  width="100%" >
                <tr>
                    <td width="20%">&nbsp;</td>
                    <td width="60%"><center><b><span><u>Instituto Boliviano de Normalización y Calidad</u><!--br><h3>REGISTRO</h3--><br><h3>ACTA DE ENTREGA DE ACTIVOS FIJOS</h3></span></b><center>

                    </td>
                    <td width="20%"><img style="width:70px;height:70px;" src="../assets/img/logo_ibnorca_origen_3.jpg"></td>
                <tr>
                </table>
        </td></tr>
        <tr><td>
            <table align="center" class="table">
                <tbody>
                    <tr>
                        <td class="text-left" width="18%" style="background: black;color:white; ">Ciudad Y Fecha:</td>
                        <td class="text-center" colspan="3">La Paz, <?=$dia?> de <?=$nom_mes?> de <?=$anio?></td>
                    </tr>
                    <tr>
                        <td class="text-left" style="background: black;color:white; ">Responsable:</td>
                        <td class="text-center" ><?=$personal?></td>
                        <td class="text-left" style="background: black;color:white; ">C.I.</td>
                        <td class="text-center" ><?=$identificacion?></td>
                    </tr>
                    <tr>
                        <td class="text-left" style="background: black;color:white; ">Cargo:</td>
                        <td class="text-center" ><?=$cargo?></td>
                        <td class="text-left" style="background: black;color:white; ">Área:</td>
                        <td class="text-center" ><?=$area?></td>
                    </tr>
                </tbody>
            </table>
        </td></tr>
        <tr><td><p><span >El Área Administrativa mediante el presente acta, hace entrega de los bienes de uso detallados a continuación:</span></p></td></tr>
    </table>

        
            <table class="table" >
                <tr>

                    <!-- <td><center><b>TIPO DE BIEN</b></center></td>
                    <td><center><b>CODSIS</b></center></td>
                    <td><center><b>CODIGO</b></center></td>
                    <td><center><b>DESCRIPCIÓN</b></center></td>
                    <td><center><b>F.ASIGNACIÓN</b></center></td>
                    <td><center><b>F.RECEPCIÓN</b></center></td> -->

                    <th class="text-center" width="8%"><small><small>RUBRO</small></small></th>
                    <th class="text-center" width="10%"><small><small>CODSISTEMA</small></small></th>
                    <th class="text-center" width="10%"><small><small>CODIGO</small></small></th>
                    <th class="text-center" width="30%"><small><small>DESCRIPCIÓN</small></small></th>
                    <th class="text-center" width="15%"><small><small>F.RECEPCIÓN</small></small></th>
                    <th class="text-center" width="27%"><small><small>OBSERVACIONES</small></small></th>

                </tr>
                <?php 
                while ($rowActivos = $stmtActivos->fetch(PDO::FETCH_ASSOC)){ ?>
                <tr>
                    <td align="center"><small><small><?=$tipo_bien?></small></small></td>
                    <td align="center"><small><small><?=$codigoSis?></small></small></td>
                    <td align="center"><small><small><?=$codigoactivo?></small></small></td>
                    <td class="text-left small"><small><small><small><?=$otrodato?></small></small></small></td>
                    <td align="center"><small><small><?=$fecha_recepcion?></small></small></td>
                    <td class="text-left small"><small><small><small><?=$observaciones_recepcion?></small></small></small></td>
                </tr>
            <?php } ?>
            </table>
        <p><span >El funcionario es responsable de los bienes de Uso a partir de la recepción de la presente Acta de Entrega de Activos Fijos, comprometiéndose a:
            <br>
            * Asumir la responsabilidad de los Bienes de Uso y Equipos de Oficina relacionados en el presente documento.
            <br>
            * Dar uso exclusivo para el desempeño de las funciones asignadas por la empresa.
            <br>
            * Se hace cargo por el daño o la pérdida de los mismos a causa de negligencia o incumplimiento de las normas y procedimientos relacionados con el uso y conservación.
            <br>
            * Se compromete a informar oportunamente al área administrativa sobre cualquier traslado temporal o definitivo, además de situaciones que pongan en peligro el Bien o Equipo mediante los aspectos formales.</span></p>

        
            <table width="100%">
                <tr><td colspan="2"><center><b>HISTORIAL DE CAMBIOS</b></center></td></tr>
                <tr valign="top">
                    <td>
                        <table align="center" class="table">
                                          
                                <tr><td colspan="3"><center><b>ASIGNACIONES</b></center></td></tr>
                                <tr><td><b><center><small><small>Fecha</small></small></center></b></td>
                                    <td><b><center><small><small>Personal Origen</small></small></center></b></td>
                                    <td><b><center><small><small>CodigoActivo</small></small></center></b></td>
                                </tr>
                            
                                <?php
                                while ($rowAs = $stmtAsignaciones->fetch(PDO::FETCH_ASSOC)){ ?>
                                    <tr>
                                        <td align="center"><small><small><?=$fechaasignacion_a?></small></small></td>
                                        <td class="text-left"><small><small><?=$personal_a?></small></small></td>
                                        <td align="center"><small><small><?=$codActivoAsignado;?></small></small></td>
                                    </tr>
                                <?php } ?>
                            
                        </table>
                    </td>

                    <td >
                        <table align="center" class="table">
                            <tr><td colspan="3"><center><b>TRANSFERENCIAS</b></center></td></tr>
                            <tr><td><b><center><small><small>Fecha</small></small></center></b></td>
                                <td><b><center><small><small>Personal Origen</small></small></center></b></td>
                                <td><b><center><small><small>CodigoActivo</small></small></center></b></td>
                            </tr>
                            <?php
                            while ($rowT = $stmtTransfer->fetch(PDO::FETCH_ASSOC)){ ?>
                                <tr>
                                    <td align="center"><small><small><?=$fechaasignacion_t?></small></small></td>
                                    <td class="text-left"><small><small><?=$personal_t?></small></small></td>
                                    <td align="center"><small><small><?=$codActivoTransfer;?></small></small></td>
                                </tr>
                            <?php } ?>
                            
                        </table>
                    </td>
                </tr>
            </table>
            
                <!-- <table class="table" width="70% !important" align="center" ><tr><td>&nbsp;<br>&nbsp;<br>Firma:</td><td></td><td></td></tr>
                    <tr><td></td><td class="text-center">ENTREGUE CONFORME</td><td class="text-center">RECIBI CONFORME</td></tr>
                    <tr><td>Nombre</td><td></td><td></td></tr>
                    <tr><td>Cargo:</td><td></td><td></td></tr>
                </table> -->
            
        
</body></html>


<?php 
$html = ob_get_clean();

//  echo $html;

 // echo $html;
descargarPDF("IBNORCA - ACTA DE ENTREGA ACTIVOS",$html);


?>

<?php 
} catch(PDOException $ex){
    echo "Un error ocurrio".$ex->getMessage();
}
?>
