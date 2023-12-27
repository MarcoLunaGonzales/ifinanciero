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
$cod_personal = empty($_SESSION['globalUser']) ? 0 : $_SESSION['globalUser'];
$codigo       = $_GET['key'];
$fecha 		  = date('Y-m-d H:i:s');

/******************************************************/

// DETALLE
$sql="SELECT pad.codigo, CONCAT(p.primer_nombre, ' ', p.paterno) as nombre_personal,
        p.codigo as cod_personal,
        p.email,
        g.nombre as anio,
        pad.meses_trabajados,
        pad.dias_trabajados,
        DATE_FORMAT(pa.created_at,'%d-%m-%Y %H:%i:%s') as created,
        (SELECT COUNT(*) FROM planillas_aguinaldos_email WHERE cod_planilla_mes = pad.codigo) as nro_vista
        FROM planillas_aguinaldos_detalle pad
        LEFT JOIN personal p ON p.codigo = pad.cod_personal
        LEFT JOIN planillas_aguinaldos pa ON pa.codigo = pad.cod_planilla
        LEFT JOIN gestiones g ON g.codigo = pa.cod_gestion
        WHERE pad.codigo = '$codigo'";
$stmt = $dbh->prepare($sql);    
$stmt->execute();
while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $detail_mes          = 'Aguinaldo';
    $detail_gestion      = $result['anio'];
    $detail_cod_personal = $result['cod_personal'];
    $detail_personal     = $result['nombre_personal'];
    $detail_emision      = $result['created'];
    $detail_tiempo_trabajo = $result['meses_trabajados'].' meses'.($result['dias_trabajados'] > 0 ? (' y '.$result['dias_trabajados'].' días') : '');
    $detail_nro_vista    = $result['nro_vista'];
}

/*************************************************************
 * Contabiliza visualización
 */
if (empty($cod_personal) || $cod_personal == 0 || $cod_personal == $detail_cod_personal) {
    $detail_nro_vista++;
    $sql   = "INSERT INTO planillas_aguinaldos_email(cod_personal, cod_planilla_mes, fecha) 
            VALUES ('$cod_personal', '$codigo', '$fecha')";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
}
/************************************************************/

$ruta_vista = obtenerValorConfiguracion(113);

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
                                <label class="col-sm-1 col-form-label" style="color:#000000; ">Periodo :</label>
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
                                        <input type="text" class="form-control" readonly="true" value="<?=$detail_tiempo_trabajo?>" style="background-color:#E3CEF6;text-align: center" >
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
                        <iframe src="<?=$ruta_vista?>boleta.php?key=<?=$codigo?>"  id="vista_previa_frame" width="1600" class="div-center" height="600" scrolling="yes" style="border:none; border: #741899 solid 9px;border-radius:10px;">
                            No hay vista disponible
                        </iframe>
                        </div>
                    </div>

			    </div>		
            </div>
        </div>
	</div>
</div>