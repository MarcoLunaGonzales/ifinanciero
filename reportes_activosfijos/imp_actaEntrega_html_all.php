

<?php //ESTADO FINALIZADO


ob_start();
require_once __DIR__.'/../conexion.php';
require '../assets/phpqrcode/qrlib.php';

//require_once 'configModule.php';
require_once __DIR__.'/../functions.php';
$dbh = new Conexion();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
//RECIBIMOS LAS VARIABLES

$codigo_personal = $_GET["cod_personal"];//codigoactivofijo
try{

    $stmt = $dbh->prepare("SELECT (select ar.abreviatura from areas ar where ar.codigo=p.cod_area) as area,CONCAT_WS(' ',p.primer_nombre,p.paterno,p.materno) as personal,(select c.nombre from cargos c where c.codigo=p.cod_cargo) as cargo,p.identificacion
    from personal p where codigo=:codigo");
//Ejecutamos;
$stmt->bindParam(':codigo',$codigo_personal);
$stmt->execute();

$result = $stmt->fetch();
// $codigo = $result['codigo'];
// $codigoactivo = $result['codigoactivo'];
// $otrodato = $result['otrodato'];
// $cod_tiposbienes = $result['cod_tiposbienes'];
$area = $result['area'];
$personal = $result['personal'];
$cargo = $result['cargo'];
$identificacion = $result['identificacion'];

//*******

$sqlActivos="SELECT a.codigo,a.codigoactivo,a.otrodato,a.cod_tiposbienes,(select tb.tipo_bien from tiposbienes tb
 where tb.codigo=a.cod_tiposbienes)as tipo_bien,(select CONCAT_WS('###',f.fechaasignacion,f.fecha_recepcion)as fechas from activofijos_asignaciones f where f.cod_activosfijos=a.codigo and f.cod_personal=a.cod_responsables_responsable order by f.codigo limit 1) as fechas
from activosfijos a 
where a.cod_responsables_responsable=$codigo_personal";  
$stmtActivos = $dbh->prepare($sqlActivos);
$stmtActivos->execute();
$stmtActivos->bindColumn('codigo', $codigoSis);
$stmtActivos->bindColumn('codigoactivo', $codigoactivo);
$stmtActivos->bindColumn('otrodato', $otrodato);
$stmtActivos->bindColumn('cod_tiposbienes', $cod_tiposbienes);
$stmtActivos->bindColumn('tipo_bien', $tipo_bien);
$stmtActivos->bindColumn('fechas', $fechas);



$nro_etiqueta="";
$descripcion="";
$devolucion="";
$codigo_traspaso="";

$dia=date('d');
$mes=date('m');
$nom_mes=nombreMes($mes);
$anio=date('Y');
?>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <style type="text/css">
        body{font-family:"DejaVu Sans", "Times New Roman", Times, serif;font-size: 9px;}
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
.table tr td{
    border: 1px solid black;
}
.td-border-none{
  border: none !important;
}
.td-color-celeste{
  background:#BFFBF1;
}
.table .table-title{
 font-size: 12px;
}
</style>
   </head><body>
    <table width="100%" style="font-size: 11px;">
        <tr><td>
            <table  width="100%" >
                <tr>
                    <td width="20%">&nbsp;</td>
                    <td width="60%"><center><b><span style="font:arial;"><u>Instituto Boliviano de Normalización y calidad</u><br><h3>REGISTRO</h3><br><h3>ACTA DE ENTREGA DE BIENES DE USO</h3></span></b><center>

                    </td>
                    <td width="20%"><img style="width:70px;height:70px;" src="../assets/img/ibnorca2.jpg"></td>
                <tr>
                </table>
        </td></tr>
        <tr><td>
            <table align="center" class="table">
                <tbody>
                    <tr>
                        <td class="text-left" width="18%" style="background: black;color:white; ">Ciudad Y Fecha:</td>
                        <td class="text-center" >La Paz, <?=$dia?> de <?=$nom_mes?> de <?=$anio?></td>
                        <td class="text-left" width="10%" style="background: black;color:white; ">N°</td>
                        <td class="text-left" width="20%"></td>
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
        <tr><td>
            <table class="table" >
                <tr>
                    <td class="font-weight-bold text-center">TIPO DE BIEN</td>
                    <td class="font-weight-bold text-center">CODSIS</td>
                    <td class="font-weight-bold text-center">CODIGO</td>
                    <td class="font-weight-bold text-center">DESCRIPCIÓN</td>
                    <td class="font-weight-bold text-center">F.ASIGNACIÓN</td>
                    <td class="font-weight-bold text-center">F.RECEPCIÓN</td>
                </tr>

                <?php 
                while ($rowActivos = $stmtActivos->fetch(PDO::FETCH_ASSOC)) {

                    $array_fecha=explode('###', $fechas);
                    $fecha_asig="";
                    $fecha_rec="";

                    if(isset($array_fecha[1])){
                        $fecha_asig=$array_fecha[0];
                        $fecha_rec_ar=$array_fecha[1];
                        $fecha_rec_ar=explode(' ', $fecha_rec_ar);
                        $fecha_rec=$fecha_rec_ar[0];
                    }

                ?>
                <tr>
                    <td class="text-left small"><?=$tipo_bien?></td>
                    <td class="text-center small"><?=$codigoSis?></td>
                    <td class="text-center small"><?=$codigoactivo?></td>
                    <td class="text-left small"><?=$otrodato?></td>
                    <td class="text-left small"><?=$fecha_asig?></td>
                    <td class="text-left small"><?=$fecha_rec?></td>
                </tr>
            <?php } ?>
            </table>
        </td></tr>
        <tr><td><p><span >El funcionario es responsable de los bienes de Uso a partir de la firma de la presente acta, comprometiendose a :<br>
            * Asumir la responsabilidad de los Bienes de Uso y Equipos de Oficina relacionados en el presente documento.<br>
            * Dar uso exclusivo para el desempeño de las funciones asignadas por la empresa.<br>
            * Se hace cargo por el daño o la perdida de los mismos a causa de negligencia o incumplimiento de las normas y procedimientos relacionados con el uso y conservación.<br>
            * Se compromete a informar oportunamente al area administrativa sobre cualquier traslado temporal o definitivo, ademas de situaciones que pongan en peligro el Bien o Equipo mediante los aspectos formales.</span></p></td></tr>
        <tr>
            <td>
            
                <table class="table" width="70% !important" align="center" ><tr><td>&nbsp;<br>&nbsp;<br>Firma:</td><td></td><td></td></tr>
                    <tr><td></td><td class="text-center">ENTREGUE CONFORME</td><td class="text-center">RECIBI CONFORME</td></tr>
                    <tr><td>Nombre</td><td></td><td></td></tr>
                    <tr><td>Cargo:</td><td></td><td></td></tr>
                </table>
            
            </td>
        </tr>
    </table>
</body></html>


<?php 
$html = ob_get_clean();
 // echo $html;
descargarPDF("IBNORCA - ACTA DE ENTREGA BIENES DE USO",$html);

?>

<?php 
} catch(PDOException $ex){
    echo "Un error ocurrio".$ex->getMessage();
}
?>
