<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../assets/libraries/CifrasEnLetras.php';

session_start();
if(!isset($_GET['sol'])){
    header("location:listSolicitudRecursos.php");
}else{
    $codigo=$_GET['sol'];
    $moneda=1;
    $abrevMon=abrevMoneda($moneda);
    $nombreMonedaG=nameMoneda($moneda);
}

$dbh = new Conexion();
$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();
// Preparamos
$stmt = $dbh->prepare("SELECT p.*,e.nombre as estado_solicitud, u.abreviatura as unidad,u.nombre as nombre_unidad,a.abreviatura as area 
        from solicitud_recursos p,unidades_organizacionales u, areas a,estados_solicitudrecursos e
  where p.cod_unidadorganizacional=u.codigo and p.cod_area=a.codigo and e.codigo=p.cod_estadosolicitudrecurso and p.codigo='$codigo' order by codigo");
// Ejecutamos
$stmt->execute();
// bindColumn
            $stmt->bindColumn('codigo', $codigoX);
            $stmt->bindColumn('cod_personal', $codPersonalX);
            $stmt->bindColumn('fecha', $fechaX);
            $stmt->bindColumn('cod_unidadorganizacional', $codUnidadX);
            $stmt->bindColumn('cod_area', $codAreaX);
            $stmt->bindColumn('area', $areaX);
            $stmt->bindColumn('unidad', $unidadX);
            $stmt->bindColumn('nombre_unidad', $unidadNombreX);
            $stmt->bindColumn('estado_solicitud', $estadoX);
            $stmt->bindColumn('cod_estadosolicitudrecurso', $codEstadoX);
            $stmt->bindColumn('numero', $numeroX);
            $stmt->bindColumn('cod_simulacion', $codSimulacionX);
            $stmt->bindColumn('cod_proveedor', $codProveedorX);

while ($rowDetalle = $stmt->fetch(PDO::FETCH_BOUND)) {
    $fechaC=$fechaX;
    $unidadC=$unidadNombreX;
    $codUC=$codUnidadX;
    $monedaC="Bs";
    $codMC=$moneda;
    $numeroC=$numeroX;
    $solicitante=namePersonal($codPersonalX);
}
//INICIAR valores de las sumas
$tDebeDol=0;$tHaberDol=0;$tDebeBol=0;$tHaberBol=0;

// Llamamos a la funcion para obtener el detalle de la solicitud

$data = obtenerSolicitudRecursosDetalle($codigo);
$tc=obtenerValorTipoCambio($moneda,strftime('%Y-%m-%d',strtotime($fechaC)));
if($tc==0){$tc=1;}
$fechaActual=date("Y-m-d");
header('Content-type: text/html; charset=ISO-8859-1');
$html = '';
$html.='<html>'.
         '<head>'.
             '<!-- CSS Files -->'.
             '<link href="../assets/libraries/plantillaPDFSolicitudesRecursos.css" rel="stylesheet" />'.
           '</head>';
$html.='<body>
<img style="position:absolute;top:0.43in;left:0.43in;width:6.88in;height:0.35in" src="../assets/img/sol_recursos/vi_1.png" />
<div style="position:absolute;top:0.55in;left:2.21in;width:3.34in;line-height:0.14in;"><span style="font-style:italic;font-weight:bold;font-size:8pt;font-family:Helvetica;color:#000000">INSTITUTO BOLIVIANO DE NORMALIZACION Y CALIDAD</span><span style="font-style:italic;font-weight:bold;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:0.43in;left:7.43in;width:1.03in;height:1.03in" src="../assets/img/sol_recursos/ri_1.jpeg" />
<img style="position:absolute;top:0.43in;left:7.30in;width:1.30in;height:1.04in" src="../assets/img/sol_recursos/vi_2.png" />
<img style="position:absolute;top:0.77in;left:0.43in;width:6.88in;height:0.35in" src="../assets/img/sol_recursos/vi_3.png" />
<div style="position:absolute;top:0.89in;left:3.55in;width:0.67in;line-height:0.14in;"><span style="font-style:normal;font-weight:bold;font-size:8pt;font-family:Helvetica;color:#000000">REGISTRO</span><span style="font-style:normal;font-weight:bold;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:1.11in;left:0.43in;width:6.88in;height:0.35in" src="../assets/img/sol_recursos/vi_4.png" />
<div style="position:absolute;top:1.24in;left:3.08in;width:1.61in;line-height:0.14in;"><span style="font-style:normal;font-weight:bold;font-size:8pt;font-family:Helvetica;color:#000000">SOLICITUD DE RECURSOS</span><span style="font-style:normal;font-weight:bold;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:1.46in;left:0.43in;width:1.30in;height:0.27in" src="../assets/img/sol_recursos/vi_5.png" />
<div style="position:absolute;top:1.54in;left:0.47in;width:0.92in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">Ciudad y Fecha:</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:1.46in;left:1.71in;width:1.30in;height:0.27in" src="../assets/img/sol_recursos/vi_6.png" />
<div style="position:absolute;top:1.54in;left:1.76in;width:0.84in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">'.$unidadC.'</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:1.46in;left:3.00in;width:3.44in;height:0.27in" src="../assets/img/sol_recursos/vi_7.png" />
<div style="position:absolute;top:1.54in;left:3.05in;width:0.65in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">'.strftime('%d/%m/%Y',strtotime($fechaC)).'</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:1.46in;left:6.44in;width:0.87in;height:0.27in" src="../assets/img/sol_recursos/vi_8.png" />
<div style="position:absolute;top:1.54in;left:6.49in;width:0.50in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">Numero:</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:1.46in;left:7.30in;width:0.01in;height:0.27in" src="../assets/img/sol_recursos/vi_9.png" />
<img style="position:absolute;top:1.46in;left:7.30in;width:1.30in;height:0.01in" src="../assets/img/sol_recursos/vi_10.png" />
<img style="position:absolute;top:1.46in;left:8.59in;width:0.01in;height:0.27in" src="../assets/img/sol_recursos/vi_11.png" />
<img style="position:absolute;top:1.71in;left:7.30in;width:1.30in;height:0.01in" src="../assets/img/sol_recursos/vi_12.png" />
<div style="position:absolute;top:1.54in;left:7.78in;width:0.37in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">'.generarNumeroCeros(6,$numeroC).'</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:1.71in;left:0.43in;width:1.30in;height:0.27in" src="../assets/img/sol_recursos/vi_13.png" />
<div style="position:absolute;top:1.80in;left:0.47in;width:0.63in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">Solicitante:</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:1.71in;left:1.71in;width:0.01in;height:0.27in" src="../assets/img/sol_recursos/vi_14.png" />
<img style="position:absolute;top:1.71in;left:1.71in;width:4.73in;height:0.01in" src="../assets/img/sol_recursos/vi_15.png" />
<img style="position:absolute;top:1.71in;left:6.44in;width:0.01in;height:0.27in" src="../assets/img/sol_recursos/vi_16.png" />
<img style="position:absolute;top:1.97in;left:1.71in;width:4.73in;height:0.01in" src="../assets/img/sol_recursos/vi_17.png" />
<div style="position:absolute;top:1.80in;left:1.76in;width:1.29in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">'.$solicitante.'</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:1.71in;left:6.44in;width:0.87in;height:0.27in" src="../assets/img/sol_recursos/vi_18.png" />
<div style="position:absolute;top:1.80in;left:6.49in;width:0.23in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">T/C</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:1.71in;left:7.30in;width:1.30in;height:0.27in" src="../assets/img/sol_recursos/vi_19.png" />
<div style="position:absolute;top:1.80in;left:7.83in;width:0.27in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">6.96</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:1.97in;left:0.43in;width:1.30in;height:0.27in" src="../assets/img/sol_recursos/vi_20.png" />
<div style="position:absolute;top:2.05in;left:0.47in;width:1.08in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">Codigo de Servicio:</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:1.97in;left:1.71in;width:0.01in;height:0.27in" src="../assets/img/sol_recursos/vi_21.png" />
<img style="position:absolute;top:1.97in;left:1.71in;width:4.73in;height:0.01in" src="../assets/img/sol_recursos/vi_22.png" />
<img style="position:absolute;top:1.97in;left:6.44in;width:0.01in;height:0.27in" src="../assets/img/sol_recursos/vi_23.png" />
<img style="position:absolute;top:2.23in;left:1.71in;width:4.73in;height:0.01in" src="../assets/img/sol_recursos/vi_24.png" />
<div style="position:absolute;top:2.05in;left:1.76in;width:1.23in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">RSC-TCS-CSI-00208,</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:1.97in;left:6.44in;width:0.87in;height:0.27in" src="../assets/img/sol_recursos/vi_25.png" />
<div style="position:absolute;top:2.05in;left:6.49in;width:0.44in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">Estado:</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:1.97in;left:7.30in;width:1.30in;height:0.27in" src="../assets/img/sol_recursos/vi_26.png" />
<div style="position:absolute;top:2.05in;left:7.67in;width:0.59in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">'.$estadoX.'</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:2.31in;left:0.43in;width:0.27in;height:0.52in" src="../assets/img/sol_recursos/vi_27.png" />
<div style="position:absolute;top:2.53in;left:0.49in;width:0.17in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">N°</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:2.32in;left:0.69in;width:1.03in;height:0.17in" src="../assets/img/sol_recursos/vi_28.png" />
<img style="position:absolute;top:2.31in;left:0.68in;width:0.01in;height:0.18in" src="../assets/img/sol_recursos/vi_29.png" />
<img style="position:absolute;top:2.31in;left:0.68in;width:1.04in;height:0.01in" src="../assets/img/sol_recursos/vi_30.png" />
<img style="position:absolute;top:2.31in;left:1.71in;width:0.01in;height:0.18in" src="../assets/img/sol_recursos/vi_31.png" />
<div style="position:absolute;top:2.35in;left:0.87in;width:0.71in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">Seguimiento</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:2.49in;left:0.69in;width:1.03in;height:0.17in" src="../assets/img/sol_recursos/vi_32.png" />
<img style="position:absolute;top:2.49in;left:0.68in;width:0.01in;height:0.18in" src="../assets/img/sol_recursos/vi_33.png" />
<img style="position:absolute;top:2.49in;left:1.71in;width:0.01in;height:0.18in" src="../assets/img/sol_recursos/vi_34.png" />
<img style="position:absolute;top:2.66in;left:0.68in;width:1.04in;height:0.01in" src="../assets/img/sol_recursos/vi_35.png" />
<div style="position:absolute;top:2.53in;left:0.85in;width:0.74in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">Presupuestal</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:2.66in;left:0.68in;width:0.61in;height:0.18in" src="../assets/img/sol_recursos/vi_36.png" />
<div style="position:absolute;top:2.70in;left:0.86in;width:0.29in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">BOB</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:2.66in;left:1.28in;width:0.44in;height:0.18in" src="../assets/img/sol_recursos/vi_37.png" />
<div style="position:absolute;top:2.70in;left:1.45in;width:0.14in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">%</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:2.32in;left:1.72in;width:1.07in;height:0.26in" src="../assets/img/sol_recursos/vi_38.png" />
<img style="position:absolute;top:2.31in;left:1.71in;width:0.01in;height:0.27in" src="../assets/img/sol_recursos/vi_39.png" />
<img style="position:absolute;top:2.31in;left:1.71in;width:1.08in;height:0.01in" src="../assets/img/sol_recursos/vi_40.png" />
<img style="position:absolute;top:2.31in;left:2.79in;width:0.01in;height:0.27in" src="../assets/img/sol_recursos/vi_41.png" />
<div style="position:absolute;top:2.40in;left:1.78in;width:0.98in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">Centro de Costos</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:2.58in;left:1.72in;width:1.07in;height:0.26in" src="../assets/img/sol_recursos/vi_42.png" />
<img style="position:absolute;top:2.57in;left:1.71in;width:0.01in;height:0.27in" src="../assets/img/sol_recursos/vi_43.png" />
<img style="position:absolute;top:2.57in;left:2.79in;width:0.01in;height:0.27in" src="../assets/img/sol_recursos/vi_44.png" />
<img style="position:absolute;top:2.83in;left:1.71in;width:1.08in;height:0.01in" src="../assets/img/sol_recursos/vi_45.png" />
<div style="position:absolute;top:2.66in;left:2.09in;width:0.37in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">(Area)</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:2.31in;left:2.79in;width:1.08in;height:0.52in" src="../assets/img/sol_recursos/vi_46.png" />
<div style="position:absolute;top:2.53in;left:3.04in;width:0.61in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">Nª Factura</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:2.31in;left:3.86in;width:3.44in;height:0.52in" src="../assets/img/sol_recursos/vi_47.png" />
<div style="position:absolute;top:2.53in;left:5.27in;width:0.67in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">Descripcion</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:2.31in;left:7.30in;width:1.30in;height:0.35in" src="../assets/img/sol_recursos/vi_48.png" />
<div style="position:absolute;top:2.44in;left:7.74in;width:0.44in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">Importe</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:2.66in;left:7.30in;width:1.30in;height:0.18in" src="../assets/img/sol_recursos/vi_49.png" />
<div style="position:absolute;top:2.70in;left:7.82in;width:0.29in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">BOB</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:2.83in;left:0.43in;width:0.27in;height:0.87in" src="../assets/img/sol_recursos/vi_50.png" />
<div style="position:absolute;top:3.21in;left:0.52in;width:0.10in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">1</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:2.83in;left:0.68in;width:0.61in;height:0.87in" src="../assets/img/sol_recursos/vi_51.png" />
<img style="position:absolute;top:2.83in;left:1.28in;width:0.44in;height:0.87in" src="../assets/img/sol_recursos/vi_52.png" />
<img style="position:absolute;top:2.83in;left:1.71in;width:1.08in;height:0.87in" src="../assets/img/sol_recursos/vi_53.png" />
<div style="position:absolute;top:3.21in;left:2.13in;width:0.28in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">TCS</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<div style="position:absolute;top:3.21in;left:2.92in;width:0.85in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">N° Factura 174</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:2.83in;left:2.79in;width:1.08in;height:0.87in" src="../assets/img/sol_recursos/vi_54.png" />
<div style="position:absolute;top:2.87in;left:3.91in;width:3.17in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">Honorarios Auditores Externos - Eduardo Fabian Colombo</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<div style="position:absolute;top:3.04in;left:3.91in;width:2.40in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">Motivo: Auditoria de Renovación - ISO 9001</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<div style="position:absolute;top:3.21in;left:3.91in;width:2.02in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">            Auditoria Etapa I - ISO 45001</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<div style="position:absolute;top:3.39in;left:3.91in;width:2.40in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">Empresa: EXPRINTER LIFTVANS BOLIVIA</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<div style="position:absolute;top:3.56in;left:3.91in;width:1.34in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">Fecha: 02 al 04/12/2019</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:2.83in;left:3.86in;width:3.44in;height:0.87in" src="../assets/img/sol_recursos/vi_55.png" />
<img style="position:absolute;top:2.83in;left:7.30in;width:1.30in;height:0.87in" src="../assets/img/sol_recursos/vi_56.png" />
<div style="position:absolute;top:3.21in;left:8.11in;width:0.47in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">4802.40</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:3.69in;left:0.43in;width:1.30in;height:0.78in" src="../assets/img/sol_recursos/vi_57.png" />
<img style="position:absolute;top:3.69in;left:1.71in;width:5.59in;height:0.27in" src="../assets/img/sol_recursos/vi_58.png" />
<div style="position:absolute;top:3.77in;left:6.37in;width:0.92in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">Sub Total (BOB)</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:3.69in;left:7.30in;width:1.30in;height:0.27in" src="../assets/img/sol_recursos/vi_59.png" />
<div style="position:absolute;top:3.77in;left:8.08in;width:0.51in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">4 802,40</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:3.95in;left:1.71in;width:4.30in;height:0.27in" src="../assets/img/sol_recursos/vi_60.png" />
<div style="position:absolute;top:4.03in;left:4.66in;width:1.34in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">Retencion de Impuestos</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:3.95in;left:6.01in;width:1.30in;height:0.27in" src="../assets/img/sol_recursos/vi_61.png" />
<div style="position:absolute;top:4.03in;left:6.10in;width:1.14in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> 0% - Sin Descuento</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:3.95in;left:7.30in;width:1.30in;height:0.27in" src="../assets/img/sol_recursos/vi_62.png" />
<div style="position:absolute;top:4.03in;left:8.31in;width:0.27in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">0,00</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:4.20in;left:1.71in;width:5.59in;height:0.27in" src="../assets/img/sol_recursos/vi_63.png" />
<div style="position:absolute;top:4.29in;left:6.50in;width:0.79in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">TOTAL (BOB)</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:4.20in;left:7.30in;width:1.30in;height:0.27in" src="../assets/img/sol_recursos/vi_64.png" />
<div style="position:absolute;top:4.29in;left:8.08in;width:0.51in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">4 802,40</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:4.46in;left:0.43in;width:1.30in;height:0.27in" src="../assets/img/sol_recursos/vi_65.png" />
<div style="position:absolute;top:4.54in;left:0.54in;width:1.09in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">V°B° P-SA/P-DNAF</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:4.46in;left:1.71in;width:5.59in;height:0.27in" src="../assets/img/sol_recursos/vi_66.png" />
<div style="position:absolute;top:4.54in;left:6.50in;width:0.79in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">TOTAL (USD)</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:4.46in;left:7.30in;width:1.30in;height:0.27in" src="../assets/img/sol_recursos/vi_67.png" />
<div style="position:absolute;top:4.54in;left:8.18in;width:0.40in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">690,00</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:4.81in;left:0.43in;width:8.17in;height:0.22in" src="../assets/img/sol_recursos/vi_68.png" />
<div style="position:absolute;top:4.87in;left:4.00in;width:1.06in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">FORMA DE PAGO</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:5.02in;left:0.43in;width:2.67in;height:0.22in" src="../assets/img/sol_recursos/vi_69.png" />
<div style="position:absolute;top:5.08in;left:0.47in;width:2.18in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">(Hasta 1.000,00 BOB) Pago en Efectivo</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:5.02in;left:3.09in;width:0.78in;height:0.22in" src="../assets/img/sol_recursos/vi_70.png" />
<div style="position:absolute;top:5.08in;left:3.91in;width:2.18in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">A nombre de: Eduardo Fabian Colombo</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:5.02in;left:3.86in;width:4.73in;height:0.44in" src="../assets/img/sol_recursos/vi_71.png" />
<img style="position:absolute;top:5.24in;left:0.43in;width:2.67in;height:0.22in" src="../assets/img/sol_recursos/vi_72.png" />
<div style="position:absolute;top:5.30in;left:0.47in;width:2.26in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">(Desde 1.001,00 BOB) Pago con Cheque</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:5.24in;left:3.09in;width:0.78in;height:0.22in" src="../assets/img/sol_recursos/vi_73.png" />
<div style="position:absolute;top:5.30in;left:3.44in;width:0.11in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">X</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:5.54in;left:0.43in;width:8.17in;height:0.22in" src="../assets/img/sol_recursos/vi_74.png" />
<div style="position:absolute;top:5.60in;left:3.98in;width:1.08in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">OBSERVACIONES</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:5.75in;left:0.43in;width:0.01in;height:0.22in" src="../assets/img/sol_recursos/vi_75.png" />
<img style="position:absolute;top:5.75in;left:0.43in;width:8.17in;height:0.01in" src="../assets/img/sol_recursos/vi_76.png" />
<img style="position:absolute;top:5.75in;left:8.59in;width:0.01in;height:0.22in" src="../assets/img/sol_recursos/vi_77.png" />
<img style="position:absolute;top:5.97in;left:0.43in;width:8.17in;height:0.01in" src="../assets/img/sol_recursos/vi_78.png" />
<div style="position:absolute;top:5.81in;left:0.47in;width:4.72in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">Se debe proceder a cancelar el 70% del monto total por la entrega del primer producto.</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:6.05in;left:0.43in;width:2.05in;height:1.08in" src="../assets/img/sol_recursos/vi_79.png" />
<img style="position:absolute;top:6.05in;left:2.47in;width:2.05in;height:1.08in" src="../assets/img/sol_recursos/vi_80.png" />
<img style="position:absolute;top:6.05in;left:4.51in;width:2.05in;height:1.08in" src="../assets/img/sol_recursos/vi_81.png" />
<img style="position:absolute;top:6.05in;left:6.55in;width:2.05in;height:1.08in" src="../assets/img/sol_recursos/vi_82.png" />
<img style="position:absolute;top:7.13in;left:0.43in;width:2.05in;height:0.22in" src="../assets/img/sol_recursos/vi_83.png" />
<div style="position:absolute;top:7.19in;left:1.00in;width:0.94in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">Firma Solicitante</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:7.13in;left:2.47in;width:2.05in;height:0.22in" src="../assets/img/sol_recursos/vi_84.png" />
<div style="position:absolute;top:7.19in;left:2.76in;width:1.50in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">Autorización P-SA/P-DNAF</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:7.13in;left:4.51in;width:2.05in;height:0.22in" src="../assets/img/sol_recursos/vi_85.png" />
<div style="position:absolute;top:7.19in;left:4.63in;width:1.83in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">Autorización DNS/DR/J-GES/J-TI</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:7.13in;left:6.55in;width:2.05in;height:0.22in" src="../assets/img/sol_recursos/vi_86.png" />
<div style="position:absolute;top:7.19in;left:7.05in;width:1.06in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">Autorización DNAF</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:7.34in;left:0.43in;width:2.05in;height:0.22in" src="../assets/img/sol_recursos/vi_87.png" />
<div style="position:absolute;top:7.40in;left:0.47in;width:0.40in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">Fecha:</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:7.34in;left:2.47in;width:2.05in;height:0.22in" src="../assets/img/sol_recursos/vi_88.png" />
<div style="position:absolute;top:7.40in;left:2.51in;width:0.40in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">Fecha:</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:7.34in;left:4.51in;width:2.05in;height:0.22in" src="../assets/img/sol_recursos/vi_89.png" />
<div style="position:absolute;top:7.40in;left:4.55in;width:0.40in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">Fecha:</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:7.34in;left:6.55in;width:2.05in;height:0.22in" src="../assets/img/sol_recursos/vi_90.png" />
<div style="position:absolute;top:7.40in;left:6.59in;width:0.40in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000">Fecha:</span><span style="font-style:normal;font-weight:normal;font-size:8pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:12.32in;left:0.43in;width:2.05in;height:0.27in" src="../assets/img/sol_recursos/vi_91.png" />
<div style="position:absolute;top:12.40in;left:0.47in;width:0.66in;line-height:0.16in;"><span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Helvetica;color:#000000">IBNORCA</span><span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:12.32in;left:2.47in;width:2.05in;height:0.27in" src="../assets/img/sol_recursos/vi_92.png" />
<div style="position:absolute;top:12.40in;left:2.52in;width:1.98in;line-height:0.16in;"><span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Helvetica;color:#000000">Codigo: REG-PRE-SA-04-01.05</span><span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:12.32in;left:4.51in;width:2.05in;height:0.27in" src="../assets/img/sol_recursos/vi_93.png" />
<div style="position:absolute;top:12.40in;left:5.10in;width:0.90in;line-height:0.16in;"><span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Helvetica;color:#000000">V: 2015-09-21</span><span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
<img style="position:absolute;top:12.32in;left:6.55in;width:2.05in;height:0.27in" src="../assets/img/sol_recursos/vi_94.png" />
<div style="position:absolute;top:12.40in;left:7.54in;width:0.88in;line-height:0.16in;"><span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Helvetica;color:#000000">Pagina 1 de 1</span><span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Helvetica;color:#000000"> </span><br/></SPAN></div>
</body>'.
      '</html>';

//$html = mb_convert_encoding($html,'UTF-8', 'ISO-8859-1');

 //echo $html;           
descargarPDFSolicitudesRecursos("IBNORCA - Solicitud Recursos ".$unidadC." (".$numeroC.")",$html);
?>
