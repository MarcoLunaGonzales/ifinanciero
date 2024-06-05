<?php
require_once 'conexion.php';
require_once 'styles.php';
require_once 'functions.php';

// Configurar el informe y visualización de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

    $dbh = new Conexion();
    $globalAdmin=$_SESSION["globalAdmin"];
    
    $estadosConfig = obtenerValorConfiguracion(114);       
    $estadosCurso  = !empty($estadosConfig) ? explode(',', $estadosConfig) : []; // Estados de Curso para FACTURAR

    $url_list_siat=obtenerValorConfiguracion(103);
    
    $sqlDatos = "SELECT codigo, sucursalId, pasarelaId, fechaFactura, nitciCliente, razonSocial, importeTotal, tipoPago, codLibretaDetalle, usuario, idCliente, idIdentificacion, complementoCiCliente, nroTarjeta, CorreoCliente, estado, created_at
    FROM ventas_no_facturadas vnf
    WHERE vnf.estado = 1
    ORDER BY vnf.codigo DESC";
    
    $stmt = $dbh->prepare($sqlDatos);
    
    $stmt->execute();
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $nombreBDsiat=obtenerValorConfiguracion(106);
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
                <div class="card">
                    <div class="card-header card-header-warning card-header-icon">
                        <div class="card-icon">
                            <i class="material-icons">polymer</i>
                        </div>
                        <h4 class="card-title"><b>Solicitudes Pendientes de Facturación</b>
                        </h4>                    
                    </div>
                    <div class="card-body" id="data_solicitudes_facturacion">
                        <table class="table" id="tablePaginator">
                            <thead>
                                <tr>  
                                    <th width="10%"><small>#</small></th>
                                    <th width="10%"><small>Fecha</small></th>
                                    <th width="10%"><small>NIT</small></th>
                                    <th width="20%"><small>Razón Social</small></th>
                                    <th width="10%"><small><small>Importe Total</small></small></th>
                                    <th width="20%"><small><small>Concepto</small></small></th>
                                    <th width="10%" class="text-center"><small><small>Estado</small></small></th>
                                    <th width="10%" class="text-right"><small>Actions</small></th>
                                </tr>
                            </thead>
                            <tbody >
                                <?php
                                    foreach ($resultados as $row) {
                                ?>
                                    <tr>
                                        <td><small> <?=$row['codigo']?> </small></td>
                                        <td><small> <?=$row['fechaFactura']?> </small></td>
                                        <?php
                                            $codigo_factura = $row['codigo']; // Codigo de Factura
                                            $sqlDocumento="SELECT (SELECT st.descripcion from ".$nombreBDsiat.".siat_sincronizarparametricatipodocumentoidentidad st where st.codigoClasificador=fvd.idIdentificacion)as tipodoc, fvd.nitciCliente, fvd.complementoCiCliente 
                                            FROM ventas_no_facturadas fvd where fvd.codigo='$codigo_factura'";
                                            $stmtDocumento = $dbh->prepare($sqlDocumento);
                                            $stmtDocumento->execute();
                                            $nitString="";
                                            while ($rowDocumento = $stmtDocumento->fetch(PDO::FETCH_ASSOC)) {
                                                $tipoDoc     = $rowDocumento['tipodoc'];
                                                $nit         = $rowDocumento['nitciCliente'];
                                                $complemento = $rowDocumento['complementoCiCliente'];
                                                list($abrev, $nombreDoc) = explode("-", $tipoDoc);
                                                $nitString="<b><small><span style='color:red'>".$abrev."</span> ".$nit." <span style='color:red'>".$complemento."</span></small></b>";
                                            }
                                        ?>
                                        <td><small> <?=$nitString?> </small></td>
                                        <td><small> <?=$row['razonSocial']?> </small></td>
                                        <td><small> <?=$row['importeTotal']?> </small></td>
                                        <td>
                                            <small>
                                                <?php
                                                    //FORMAMOS EL CONCEPTO DE LA FACTURA
                                                    $codigo_factura = $row['codigo']; // Codigo de Factura
                                                    $stmtDetalleSol = $dbh->prepare("SELECT fvd.cantidad, fvd.precioUnitario, fvd.detalle from ventas_no_facturadas_detalle fvd where cod_venta_no_facturada=$codigo_factura");
                                                    $stmtDetalleSol->execute();
                                                    $stmtDetalleSol->bindColumn('cantidad', $cantidad);  
                                                    $stmtDetalleSol->bindColumn('precioUnitario', $precio_unitario);
                                                    $stmtDetalleSol->bindColumn('detalle', $descripcion_alterna); 
                                                    $cadenaFacturas="";
                                                    $cadenaFacturasM="";
                                                    $concepto_contabilizacion="";

                                                    while ($row_det = $stmtDetalleSol->fetch()){
                                                        $precio = $precio_unitario*$cantidad;
                                                        $concepto_contabilizacion.=$descripcion_alterna." / ".trim($cadenaFacturas,',').",".trim($cadenaFacturasM,",")." / ".$row['razonSocial']."<br>\n";
                                                        $concepto_contabilizacion.="Cantidad: ".$cantidad." * ".formatNumberDec($precio_unitario)." = ".formatNumberDec($precio)."<br>\n";
                                                    }
                                                    echo $concepto_contabilizacion;
                                                ?>
                                            </small>
                                        </td>
                                        <td class="td-actions text-center">
                                            <?php
                                                if($row['estado'] == 1){
                                            ?>
                                            <button class="btn btn-warning btn-sx"><small>Pendiente</small></button>
                                            <?php
                                                }else if($row['estado'] == 2){
                                            ?>
                                            <button class="btn btn-success btn-sx"><small>Facturado</small></button>
                                            <?php
                                                }
                                            ?>
                                        </td>
                                        <td class="td-actions text-right">
                                            <?php
                                                $verf_estado_curso = false;

                                                if($row['estado'] == 1){
                                                    $sqlDetalle = "SELECT vnf.moduloId
                                                                    FROM ventas_no_facturadas_detalle vnf
                                                                    WHERE vnf.cod_venta_no_facturada = '".$row['codigo']."' 
                                                                    ORDER BY vnf.codigo DESC";
                                                    $stmtDetalle = $dbh->prepare($sqlDetalle);
                                                    $stmtDetalle->execute();
                                                    $respDetalle = $stmtDetalle->fetchAll(PDO::FETCH_ASSOC); // Cambio aquí

                                                    foreach ($respDetalle as $rowDetalle) {
                                                        $verf_moduloId = $rowDetalle['moduloId'];
                                                        $sqlBuscar = "SELECT m.IdCurso, 
                                                                            m.IdModulo, 
                                                                            ibnorca.id_estadoobjeto(597, m.IdCurso) as idEstadoCurso, 
                                                                            ibnorca.d_clasificador(ibnorca.id_estadoobjeto(597, m.IdCurso)) AS estadoCurso
                                                                    FROM ibnorca.modulos m
                                                                    WHERE m.IdModulo = '$verf_moduloId'
                                                                    LIMIT 1";
                                                        // echo $sqlBuscar;
                                                        $stmtBuscar = $dbh->prepare($sqlBuscar);
                                                        $stmtBuscar->execute();
                                                        $registroEncontrado = $stmtBuscar->fetch(PDO::FETCH_ASSOC);
                                                        if($registroEncontrado) {
                                                            $estado_curso = $registroEncontrado['idEstadoCurso'];
                                                            if(in_array($estado_curso, $estadosCurso)){ // Verifica estados de curso para Facturar, toma en cuenta "configuración"
                                                                $verf_estado_curso = true;  // True: FACTURAR
                                                            }else{
                                                                $verf_estado_curso = false; // True: NO FACTURAR
                                                                break;
                                                            }
                                                        }
                                                    }
                                                }
                                                // Verifica boton para facturar
                                                if($verf_estado_curso){
                                            ?>
                                            <button class="btn btn-sm btn-success generarFactura" 
                                                    data-codigo="<?=$row['codigo']?>"
                                                    title="Facturar">
                                                <i class="material-icons">check</i>
                                            </button>
                                            <?php
                                                }
                                            ?>
                                        </td>
                                    </tr>
                                <?php
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>  
        </div>
    </div>

<!-- Proceso de Fusión -->
<script>
   $(document).ready(function() {

        // Controla el envío del formulario
        $('.generarFactura').on('click', function(){
            let codigo = $(this).data('codigo');
            Swal.fire({
                title: '¿Estás seguro?',
                text: 'Se enviará la factura pendiente',
                type: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, enviar',
                cancelButtonText: 'No',
                allowOutsideClick: false  // Evita que se cierre al hacer clic fuera del cuadro de diálogo
            }).then((result) => {
                if (result.value) {
                    Swal.fire({
                        title: 'Procesando...',
                        onBeforeOpen: () => {
                            Swal.showLoading();
                        },
                        allowOutsideClick: () => !Swal.isLoading()
                    });
                    $.ajax({
                        type: "POST",
                        url: "solicitud_facturacion_pendiente/generaVenta.php",
                        data: {
                            codigo: codigo,
                        },
                        success: function(response) {
                            console.log(response);
                            // Cierra el Toast después de recibir la respuesta
                            Swal.close();
                            let resp = JSON.parse(response);
                            if (resp.estado === true) {
                                Swal.fire({
                                    type: 'success',
                                    title: 'Mensaje',
                                    text: resp.mensaje,
                                    confirmButtonText: 'Aceptar'
                                });
                            } else {
                                Swal.fire({
                                    type: 'error',
                                    title: 'Mensaje',
                                    text: resp.mensaje,
                                    confirmButtonText: 'Aceptar'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    });
                }
            });
        });
    });
</script>