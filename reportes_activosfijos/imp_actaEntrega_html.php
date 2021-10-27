<?php //ESTADO FINALIZADO


ob_start();
require_once __DIR__.'/../conexion.php';
require '../assets/phpqrcode/qrlib.php';

//require_once 'configModule.php';
require_once __DIR__.'/../functions.php';
$dbh = new Conexion();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
//RECIBIMOS LAS VARIABLES

$codigo_af = $_GET["codigo"];//codigoactivofijo
try{

    $stmt = $dbh->prepare("SELECT a.codigo,a.codigoactivo,a.otrodato,a.cod_tiposbienes,(select ar.abreviatura from areas ar where ar.codigo=a.cod_area) as area
,CONCAT_WS(' ',p.primer_nombre,p.paterno,p.materno) as personal,(select c.nombre from cargos c where c.codigo=p.cod_cargo) as cargo,p.identificacion
from activosfijos a join personal p on a.cod_responsables_responsable=p.codigo
where a.codigo=:codigo");
//Ejecutamos;
$stmt->bindParam(':codigo',$codigo_af);
$stmt->execute();

$result = $stmt->fetch();
$codigo = $result['codigo'];
$codigoactivo = $result['codigoactivo'];
$otrodato = $result['otrodato'];
$cod_tiposbienes = $result['cod_tiposbienes'];
$area = $result['area'];
$personal = $result['personal'];
$cargo = $result['cargo'];
$identificacion = $result['identificacion'];

//*******

$tipo_bien="";
$nro_bien="00001";
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
    <link href="../assets/libraries/plantillaPDFCajaChica.css" rel="stylesheet" />
   </head><body>
    <header class="header">
    <table width="100%" style="font-size: 12px;">
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
                    <td class="font-weight-bold text-center">CODIGO BIEN</td>
                    <td class="font-weight-bold text-center">DESCRIPCIÓN</td>
                    <td class="font-weight-bold text-center">DEVOLUCIÓN</td>
                    <td class="font-weight-bold text-center">CODIGO DE TRAPASO</td>
                </tr>

               <tr>
                    <td class="text-left small"><?=$tipo_bien?></td>
                    <td class="text-center small"><?=$codigoactivo?></td>
                    <td class="text-left small"><?=$otrodato?></td>
                    <td class="text-left small"><?=$devolucion?></td>
                    <td class="text-left small"><?=$codigo_traspaso?></td>
             </tr>
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
</center>
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
