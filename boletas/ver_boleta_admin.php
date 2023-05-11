<?php
session_start();
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../styles.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';

setlocale(LC_TIME, "Spanish");

$dbh = new Conexion();

// VISTA ADMIN
/************************************************************/
// VERIFICACIÖN DE CANTIDAD DE VISTAS OBTENIDAS
$cod_personal       = $_SESSION['globalUser'];
$cod_planilla_mes   = $_GET['key'];
$fecha 		        = date('Y-m-d H:i:s');

// DETALLE
$sql="SELECT ppm.codigo, CONCAT(p.primer_nombre, ' ', p.paterno) as nombre_personal, p.email,
        (CASE
            WHEN pl.cod_mes = '1' THEN 'ENERO'
            WHEN pl.cod_mes = '2' THEN 'FEBRERO'
            WHEN pl.cod_mes = '3' THEN 'MARZO'
            WHEN pl.cod_mes = '4' THEN 'ABRIL'
            WHEN pl.cod_mes = '5' THEN 'MAYO'
            WHEN pl.cod_mes = '6' THEN 'JUNIO'
            WHEN pl.cod_mes = '7' THEN 'JULIO'
            WHEN pl.cod_mes = '8' THEN 'AGOSTO'
            WHEN pl.cod_mes = '9' THEN 'SEPTIEMBRE'
            WHEN pl.cod_mes = '10' THEN 'OCTUBRE'
            WHEN pl.cod_mes = '11' THEN 'NOVIEMBRE'
            WHEN pl.cod_mes = '12' THEN 'DICIEMBRE'
        END) as mes,
        g.nombre as anio,
        pl.dias_trabajo,
        DATE_FORMAT(pl.created_at,'%d-%m-%Y %H:%i:%s') as created,
        (SELECT COUNT(*) FROM planillas_email WHERE cod_planilla_mes = ppm.codigo) as nro_vista
    FROM planillas_personal_mes ppm
    LEFT JOIN personal p ON p.codigo = ppm.cod_personalcargo
    LEFT JOIN planillas pl ON pl.codigo = ppm.cod_planilla
    LEFT JOIN gestiones g ON g.codigo = pl.cod_gestion
    WHERE ppm.codigo = '$cod_planilla_mes'";
$stmt = $dbh->prepare($sql);    
$stmt->execute();
while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $detail_mes          = $result['mes'];
    $detail_gestion      = $result['anio'];
    $detail_personal     = $result['nombre_personal'];
    $detail_emision      = $result['created'];
    $detail_dias_trabajo = $result['dias_trabajo'];
    $detail_nro_vista    = $result['nro_vista'];
}

$ruta_vista = obtenerValorConfiguracion(104);

?>
<div id="logo_carga" class="logo-carga" style="display:none;"></div>
<div class="content">
	<div id="contListaGrupos" class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
			    <div class="card">
                    <div class="card-header card-header-deafult card-header-text text-center card-header-primary">
                        <div class="card-text">
                        <h4 class="card-title"><b>BOLETA DE PAGO</b></h4>
                        </div>
                    </div>
				
                    <div class="card-body">
                        <div class=""> 	
                            <div class="row" id="">
                            
                                <label class="col-sm-1 col-form-label" style="color:#000000; ">Personal :</label>
                                <div class="col-sm-2">
                                <div class="form-group">
                                    <input type="text" class="form-control" readonly="true" value="<?=$detail_personal?>" style="background-color:#E3CEF6;text-align: center" >
                                </div>
                                </div>  
                                <label class="col-sm-1 col-form-label" style="color:#000000; ">Mes :</label>
                                <div class="col-sm-1">
                                <div class="form-group">
                                    <input type="text" class="form-control" readonly="true" value="<?=$detail_mes?>" style="background-color:#E3CEF6;text-align: center">
                                </div>
                                </div>  
                                <label class="col-sm-1 col-form-label" style="color:#000000; ">Gestión :</label>
                                <div class="col-sm-2">
                                <div class="form-group">
                                    <input type="text" class="form-control" readonly="true" value="<?=$detail_gestion?>" style="background-color:#E3CEF6;text-align: center" >
                                </div>
                                </div>  
                                <label class="col-sm-1 col-form-label" style="color:#000000; ">Fecha Emisión:</label>
                                <div class="col-sm-2">
                                <div class="form-group">
                                    <input type="text" class="form-control" readonly="true" value="<?=$detail_emision?>" style="background-color:#E3CEF6;text-align: center" >
                                </div>
                                </div> 
                                </div>
                                <div class="row">
                                <label class="col-sm-1 col-form-label" style="color:#000000; ">Días Trabajados</label>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <input type="text" class="form-control" readonly="true" value="<?=$detail_dias_trabajo?>" style="background-color:#E3CEF6;text-align: center" >
                                    </div>
                                </div> 
                                <label class="col-sm-1 col-form-label" style="color:#000000; ">N&uacute;mero Visualizaciones</label>
                                <div class="col-sm-1">
                                <div class="form-group">
                                    <input type="text" class="form-control" readonly="true" value="<?=$detail_nro_vista?>" style="background-color:#E3CEF6;text-align: center" >
                                </div>
                                </div>
                            </div> 
                        </div>
            
                        <hr>
                        <div class="col-sm-12 text-info font-weight-bold"><center><label id="titulo_vista_previa"><b>BOLETA DE PAGO</b></label></center></div>
                        <div class="row col-sm-12">
                        <iframe src="<?=$ruta_vista?>verf_boletas_print.php?key=<?=$cod_planilla_mes?>"  id="vista_previa_frame" width="1600" class="div-center" height="600" scrolling="yes" style="border:none; border: #741899 solid 9px;border-radius:10px;">
                            No hay vista disponible
                        </iframe>
                        <!-- <iframe src="http://localhost/ifinanciero/boletas/verf_boletas_print.php?key=<?=$cod_planilla_mes?>"  id="vista_previa_frame" width="1600" class="div-center" height="600" scrolling="yes" style="border:none; border: #741899 solid 9px;border-radius:10px;">
                            No hay vista disponible
                        </iframe> -->
                        </div>
                    </div>

			    </div>		
            </div>
        </div>
	</div>
</div>