<?php
require_once 'conexion.php';
require_once 'styles.php';
require_once 'functions.php';

// Configurar el informe y visualización de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

    $dbh = new Conexion();
    $globalAdmin=$_SESSION["globalAdmin"];
        
    /*************************
     * ? LISTA DE REPORTES
     *************************/
    $url_ecommerce = obtenerValorConfiguracion(109);
    $fecha_inicio  = !empty($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-01');
    $fecha_fin     = !empty($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-t');
    $url_ws                 = $url_ecommerce.'tienda/reporteLibelula.php';
    $sw_token               = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiIzMDAxMiIsImlkVXN1YXJpbyI6IjMwMDEyIiwiY29ycmVvIjoid2VibWFzdGVyQGNvZS1lamVyY2l0by5jb20uYm8iLCJhdWQiOiJpYm5vcmNhIiwiaWF0IjoxMzU2OTk5NTI0LCJuYmYiOjEzNTcwMDAwMDB9.9VR9tWgtfSi_s9ix8qkZSl1fPYzCExJKMOPii_quXEE";
    $parametros = array(
        "token" => $sw_token,
        "desde" => $fecha_inicio, 
        "hasta" => $fecha_fin,
        "app"   => "FRONTIBNT"
    );
    // ENVIADO - JSON LOCAL
    $json_enviado = json_encode($parametros);
    $ch           = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url_ws);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_enviado);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $remote_server_output = json_decode(curl_exec($ch));
    // var_dump($remote_server_output->cursos);
?>
    <!-- EFECTO LOADING -->
    <div class="cargar-ajax d-none">
        <div class="div-loading text-center">
            <h4 class="text-warning font-weight-bold" id="texto_ajax_titulo">Procesando Datos</h4>
            <p class="text-white">Aguard&aacute; un momento por favor</p>  
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div style="overflow-y:scroll;">
                <!-- <div class="card">
                    <div class="card-header card-header-warning card-header-icon">
                        <div class="card-icon">
                            <i class="material-icons">polymer</i>
                        </div>
                        <h4 class="card-title"><b>Suscripciones</b>
                        </h4>                    
                    </div>
                    <div class="card-body" id="data_solicitudes_facturacion">
                        <table class="table" id="tablePaginator">
                            <thead>
                                <tr>  
                                    <th width="10%"><small>#</small></th>
                                    <th width="10%"><small>Factura ID</small></th>
                                    <th width="10%"><small>Fecha</small></th>
                                    <th width="20%"><small>NIT</small></th>
                                    <th width="30%"><small>Nombre</small></th>
                                    <th width="10%"><small>Estado Pago</small></th>
                                    <th width="10%"><small>Total Pagado</small></th>
                                </tr>
                            </thead>
                            <tbody >
                                <?php
                                    if (!empty($remote_server_output->suscripciones)) { 
                                        foreach ($remote_server_output->suscripciones as $suscripcion) {
                                ?>
                                    <tr>
                                        <td><small> <?= $suscripcion->suscripcionId ?> </small></td>
                                        <td><small> <?= $suscripcion->facturaId ?> </small></td>
                                        <td><small> <?= $suscripcion->creacion ?> </small></td>
                                        <td><small> <?= $suscripcion->fnit ?> </small></td>
                                        <td><small> <?= $suscripcion->fnombre ?> </small></td>
                                        <td><small> <?= $suscripcion->estadoDescripcion ?> </small></td>
                                        <td><small> <?= $suscripcion->precioTotalPagado ?> </small></td>
                                    </tr>
                                <?php
                                        }
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div> -->
                <div class="card">
                    <div class="card-header card-header-success card-header-icon">
                        <div class="card-icon">
                            <i class="material-icons">polymer</i>
                        </div>
                        <h4 class="card-title"><b>Cursos</b></h4>                    
                    </div>
                    <div class="card-header">
                        <div class="row justify-content-end">
                            <div class="col-md-6">
                                <form id="filter" action="index.php" method="GET">
                                    <div class="row">
                                        <input type="hidden" name="opcion" value="reporteLibelula">
                                        <div class="col-md-5">                                    
                                            <div class="form-group">
                                                <label for="start-date">Fecha de Inicio:</label>
                                                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="<?=$fecha_inicio?>">
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label for="end-date">Fecha de Fin:</label>
                                                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="<?=$fecha_fin?>">
                                            </div>
                                        </div>
                                        <div class="col-md-2 d-flex align-items-end">
                                            <button type="submit" class="btn btn-success btn-sm">Filtrar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" id="data_solicitudes_facturacion_activas">
                        <table class="table" id="tablePaginator2">
                            <thead>
                                <tr>  
                                    <th width="10%"><small>Fecha</small></th>
                                    <th width="10%"><small>CursoId</small></th>
                                    <th width="10%"><small>Total Pagado</small></th>
                                    <th width="10%"><small>Libélula Respuesta</small></th>
                                    
                                    <th width="10%"><small>Estudiante</small></th>
                                    <th width="10%"><small>Fecha Factura</small></th>
                                    <th width="10%"><small>Estado</small></th>
                                    <th width="10%"><small>CodFacturaVenta</small></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    if (!empty($remote_server_output->cursos)) { 
                                        foreach ($remote_server_output->cursos as $curso) {
                                            $clienteId = $curso->clienteId;
                                            $moduloId  = $curso->cursoId;
                                            $sqlDatos  = "SELECT vnf.idCliente, 
                                                                vnfd.moduloId, 
                                                                c.nombre as estudiante,
                                                                vnf.fechaFactura,
                                                                vnf.estado,
                                                                vnf.cod_facturaventa
                                                        FROM ventas_no_facturadas vnf
                                                        LEFT JOIN ventas_no_facturadas_detalle vnfd ON vnfd.cod_venta_no_facturada = vnf.codigo
                                                        LEFT JOIN clientes c ON c.codigo = vnf.idCliente
                                                        WHERE vnfd.moduloId = '$moduloId'
                                                        AND vnf.idCliente = '$clienteId'
                                                        ORDER BY vnf.codigo DESC LIMIT 1";
                                            // echo $sqlDatos;
                            
                                            $stmt = $dbh->prepare($sqlDatos);
                                            $stmt->execute();
                                            $registroPrincipal = $stmt->fetch(PDO::FETCH_ASSOC);
                                            
                                            // Verificamos si se encontró el registro
                                            $nf_estudiante       = '';
                                            $nf_fechaFactura     = '';
                                            $nf_estado           = '';
                                            $nf_cod_facturaventa = '';
                                            if ($registroPrincipal) {
                                                $nf_estudiante       = $registroPrincipal['estudiante'];
                                                $nf_fechaFactura     = date('d-m-Y', strtotime($registroPrincipal['fechaFactura']));
                                                $nf_estado           = $registroPrincipal['estado'];
                                                $nf_cod_facturaventa = $registroPrincipal['cod_facturaventa'];
                                            } 
                                ?>
                                    <tr>
                                        <td><small><?= $curso->libelulaFechaRespuesta ?></small></td>
                                        <td><small><?= $curso->cursoId ?></small></td>
                                        <td><small><?= $curso->pagoCursoId ?></small></td>
                                        <td><small><?= $curso->precioTotalPagado ?></small></td>
                                        
                                        <td><small><?= $nf_estudiante ?></small></td>
                                        <td><small><?= $nf_fechaFactura ?></small></td>
                                        <td class="td-actions text-center">
                                            <?php
                                                if($nf_estado == 1){
                                            ?>
                                            <button class="btn btn-warning btn-sx"><small>Pendiente</small></button>
                                            <?php
                                                }else if($nf_estado == 2){
                                            ?>
                                            <button class="btn btn-success btn-sx"><small>Facturado</small></button>
                                            <?php
                                                }
                                            ?>
                                        </td>
                                        <td><small><?= $nf_cod_facturaventa ?></small></td>
                                    </tr>
                                <?php 
                                        }
                                    } 
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>  
        </div>
    </div>