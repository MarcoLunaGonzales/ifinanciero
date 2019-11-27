<?php
//ES PARA IMPRIMIR LA ASIGNACION ENTRE PERSONAS
require_once __DIR__.'/../conexion.php';
//require_once 'styles.php';
//require_once 'configModule.php';

require_once  __DIR__.'/../fpdf.php';
$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$codigo=$_GET["codigo"];

$stmt = $dbh->prepare("select * from v_activosfijos_asignaciones where activofijosasignaciones_codigo=:codigo");
// Ejecutamos
$stmt->bindParam(':codigo',$codigo);
$stmt->execute();
$result = $stmt->fetch();
$codigo = $result['codigo'];
$codigoactivo = $result['codigoactivo'];
$tipoalta = $result['tipoalta'];
$fechalta = $result['fechalta'];
$indiceufv = $result['indiceufv'];
$tipocambio = $result['tipocambio'];
$moneda = $result['moneda'];
$valorinicial = $result['valorinicial'];
$depreciacionacumulada = $result['depreciacionacumulada'];
$valorresidual = $result['valorresidual'];
$cod_depreciaciones = $result['cod_depreciaciones'];
$cod_tiposbienes = $result['cod_tiposbienes'];
$vidautilmeses = $result['vidautilmeses'];
$estadobien = $result['estadobien'];
$otrodato = $result['otrodato'];
$cod_ubicaciones = $result['cod_ubicaciones'];
$cod_empresa = $result['cod_empresa'];
$activo = $result['activo'];
$cod_responsables_responsable = $result['cod_responsables_responsable'];
$cod_responsables_autorizadopor = $result['cod_responsables_autorizadopor'];
$created_at = $result['created_at'];
$created_by = $result['created_by'];
$modified_at = $result['modified_at'];
$modified_by = $result['modified_by'];
$vidautilmeses_restante = $result['vidautilmeses_restante'];
$cod_af_proveedores = $result['cod_af_proveedores'];
$numerofactura = $result['numerofactura'];
$nombre_personal = $result['nombre_personal'];
$nombre_depreciaciones = $result['nombre_depreciaciones'];
$tipo_bien = $result['tipo_bien'];
$activofijosasignaciones_codigo = $result['activofijosasignaciones_codigo'];
$fechaasignacion = $result['fechaasignacion'];
$edificio = $result['edificio'];
$oficina = $result['oficina'];
$nombre_uo = $result['nombre_uo'];
$estadobien_asig = $result['estadobien_asig'];


//$image1 = "ibno/marca.png";//logo


$pdf = new FPDF();
$pdf->AddPage();

//$pdf->Image($image1, 5, $pdf->GetY(), 33.78);

$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,5,'ACTA DE RECEPCION Y ENTREGA DE BIENES DE USO (ACTIVO FIJO)',0,1,'C',0);
$pdf->SetFont('Arial','B',10);
//$pdf->Cell(0,5,'EMPRESA',0,1,'C',0);
$pdf->Cell(0,0.2,"",1,1,'L',0);
$pdf->SetFont('Arial','B',8);


$pdf->Cell(0,5,"CODIGO ACTIVO : ".$codigoactivo,0,1,'L',0);
$pdf->Cell(0,5,$activo,0,1,'L',0);
//$pdf->Cell(0,5,$tipoalta,0,1,'C',0);
$pdf->Cell(0,5,"UNIDAD : ".$nombre_uo,0,1,'L',0);
$pdf->Cell(0,5,"EDIFICIO : ".$edificio,0,1,'L',0);
//$pdf->Cell(0,5,"OFICINA : ".$oficina,0,1,'L',0);
//$pdf->Cell(0,5,"RUBRO/DEPRECIACION : ".$dep_nombre,0,1,'L',0);
$pdf->Cell(0,5,"TIPO DE BIEN : ".$tipo_bien,0,1,'L',0);
$pdf->Cell(0,5,"ESTADO DEL BIEN EN ASIGNACION: ".$estadobien_asig,0,1,'L',0);
$pdf->Cell(0,5,"PERSONAL ASIGNADO: ".$nombre_personal,0,1,'L',0);
$pdf->Cell(0,5,"FECHA HORA: ".$fechaasignacion,0,1,'L',0);


$pdf->Cell(0,0.2,"",1,1,'L',0);//LINEA
$pdf->Output();

?>
