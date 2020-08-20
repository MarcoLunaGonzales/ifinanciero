<?php
require_once '../conexion.php';
require_once 'configModule.php';

$codigo_comprobante = $_GET["codigo_comprobante"];
// echo $codigo_comprobante;
$dbh = new Conexion();

//insertamos estado_de_cuentas y comprobantes
if($codigo_comprobante>0){
  $codigo_sr=0;
  //sacar codigo de estado de cuenta
  // $sqlEstadoCuenta="SELECT e.codigo From estados_cuenta e where e.cod_comprobantedetalle=$codigo_comprobante limit 1"; 
  // $stmtEstadoCuenta = $dbh->prepare($sqlEstadoCuenta);
  // $stmtEstadoCuenta->execute();                    
  // $resultado=$stmtEstadoCuenta->fetch();
  // $codigo_estadoCuenta=$resultado['codigo'];
  //$sqlDetalleX="SELECT codigo,cod_solicitudrecurso,cod_solicitudrecursodetalle,cod_proveedor,cod_tipopagoproveedor from solicitud_recursosdetalle where cod_estadocuenta=$codigo_estadoCuenta limit 1";    
  $sqlDetalleX="SELECT sd.codigo,sd.cod_solicitudrecurso,sd.cod_proveedor,sd.cod_tipopagoproveedor 
    FROM solicitud_recursos s,solicitud_recursosdetalle sd
    WHERE s.codigo=sd.cod_solicitudrecurso and s.cod_comprobante in (select cd.cod_comprobante from comprobantes_detalle cd where cd.codigo=$codigo_comprobante)";
  // echo $sqlDetalleX;
  $stmtDetalleX = $dbh->prepare($sqlDetalleX);
  $stmtDetalleX->execute();                    
  $resultado=$stmtDetalleX->fetch();
  // $cod_solicitudrecursodetalle_sr=$resultado['codigo'];
  $cod_solicitudrecurso_sr=$resultado['cod_solicitudrecurso'];  
  // echo $cod_solicitudrecurso_sr."-";
  if($cod_solicitudrecurso_sr!=0 && $cod_solicitudrecurso_sr!='' && $cod_solicitudrecurso_sr!=null){?>
    <a class="btn btn-success" href='<?=$urlSolicitudRecursos;?>?cod=<?=$cod_solicitudrecurso_sr;?>&v_cajachica=10' target="_blank"><i class="material-icons" title="Imprimir Factura">bar_chart</i>Ver Solicitud
    </a>
  <?php }

}

?>
