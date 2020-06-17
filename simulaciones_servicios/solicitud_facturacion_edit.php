<?php

require_once 'conexion.php';
require_once 'conexion_externa.php';
require_once 'styles.php';
require_once 'configModule.php';

//$dbh = new Conexion();
$dbh = new Conexion();
$codigo_facturacion=$codigo_s;
// echo $codigo_facturacion; 
if(isset($_GET['q'])){
  $q=$_GET['q'];
  $v=$_GET['v'];
  $s=$_GET['s'];
  $u=$_GET['u'];
}

$stmtCantidad = $dbh->prepare("SELECT tipo_solicitud,cod_simulacion_servicio,cod_cliente from solicitudes_facturacion where codigo=$codigo_facturacion");//and cod_estado=1
$stmtCantidad->execute();
$resutCanitdad = $stmtCantidad->fetch();
$tipo_solicitud = $resutCanitdad['tipo_solicitud'];//1 tcp_tcs, 2 capacitacion estudiantes,6 capacitacion empresas, 3 servicios,4 manual,5Normas
$cod_simulacion_servicio = $resutCanitdad['cod_simulacion_servicio'];
$cod_cliente = $resutCanitdad['cod_cliente'];
if(isset($_GET['q'])){  
  if($tipo_solicitud==1){//tcp ?>  
    <script type="text/javascript">
    location = "<?=$urlRegisterSolicitudfactura;?>&cod_s=<?=$cod_simulacion_servicio?>&cod_f=<?=$codigo_facturacion?>&cod_sw=5&q=<?=$q?>&v=<?=$v?>&s=<?=$s?>&u=<?=$u?>"
  </script><?php 
  }elseif($tipo_solicitud==4){//solicitud manual ?>  
    <script type="text/javascript">
      location = "<?=$urlRegister_solicitudfacturacion_manual;?>&cod_s=<?=$cod_simulacion_servicio?>&cod_f=<?=$codigo_facturacion?>&cod_sw=5&q=<?=$q?>&v=<?=$v?>&s=<?=$s?>&u=<?=$u?>"
    </script><?php 
  }elseif($tipo_solicitud==3){//solicitud servicios sin propuesta ?>  
    <script type="text/javascript">
      location = "<?=$urlRegisterSolicitudfactura_sp?>&cod_simulacion=0&IdServicio=<?=$cod_simulacion_servicio?>&cod_facturacion=<?=$codigo_facturacion?>&cod_sw=5&q=<?=$q?>&v=<?=$v?>&s=<?=$s?>&u=<?=$u?>"
    </script><?php 
  }elseif($tipo_solicitud==5){//solicitud normas ?>  
    <script type="text/javascript">
      location = "<?=$urlRegisterSolicitudfactura_normas;?>?cod_f=<?=$codigo_facturacion?>&cod_sw=5&q=<?=$q?>&v=<?=$v?>&s=<?=$s?>&u=<?=$u?>"      
    </script><?php 
  }elseif($tipo_solicitud==2){//solicitud capacitacion estudiantes 
    $IdCurso=$cod_simulacion_servicio;
    $CiAlumno=$cod_cliente;
    ?>  
    <script type="text/javascript">
      location = "<?=$urlregistro_solicitud_facturacion_sec_estudiantes?>&codigo=<?=$CiAlumno?>&cod_simulacion=<?=$IdCurso;?>&IdCurso=<?=$IdCurso;?>&cod_facturacion=<?=$codigo_facturacion?>&cod_sw=5&q=<?=$q?>&r=<?=$v?>"
    </script><?php 
  }elseif($tipo_solicitud==6){//solicitud capacitacion empresas
    $IdCurso=$cod_simulacion_servicio;
    $idEmpresa=$cod_cliente;
    ?>  
    <script type="text/javascript">
      location = "<?=$urlregistro_solicitud_facturacion_sec_empresas?>&codigo=<?=$idEmpresa?>&cod_simulacion=<?=$IdCurso;?>&IdCurso=<?=$IdCurso;?>&cod_facturacion=<?=$codigo_facturacion?>&cod_sw=5&q=<?=$q?>&r=<?=$v?>"
    </script><?php
  }

}else{
  if($tipo_solicitud==1){//tcp ?>  
    <script type="text/javascript">
      location = "<?=$urlRegisterSolicitudfactura;?>&cod_s=<?=$cod_simulacion_servicio?>&cod_f=<?=$codigo_facturacion?>&cod_sw=5"
    </script><?php 
  }elseif($tipo_solicitud==4){//solicitud manual ?>  
    <script type="text/javascript">
      location = "<?=$urlRegister_solicitudfacturacion_manual;?>&cod_s=<?=$cod_simulacion_servicio?>&cod_f=<?=$codigo_facturacion?>&cod_sw=5"
    </script><?php 
  }elseif($tipo_solicitud==3){//solicitud servicios sin propuesta ?>  
    <script type="text/javascript">
      location = "<?=$urlRegisterSolicitudfactura_sp?>&cod_simulacion=0&IdServicio=<?=$cod_simulacion_servicio?>&cod_facturacion=<?=$codigo_facturacion?>&cod_sw=5"
    </script><?php 
  }elseif($tipo_solicitud==5){//solicitud normas ?>  
    <script type="text/javascript">
      location = "<?=$urlRegisterSolicitudfactura_normas;?>?cod_f=<?=$codigo_facturacion?>&cod_sw=5"
    </script><?php 
  }elseif($tipo_solicitud==2){//solicitud capacitacion estudiantes 
    $IdCurso=$cod_simulacion_servicio;
    $CiAlumno=$cod_cliente;
    ?>  
    <script type="text/javascript">
      location = "<?=$urlregistro_solicitud_facturacion_sec_estudiantes?>&codigo=<?=$CiAlumno?>&cod_simulacion=<?=$IdCurso;?>&IdCurso=<?=$IdCurso;?>&cod_facturacion=<?=$codigo_facturacion?>&cod_sw=5"
    </script><?php 
  }
  elseif($tipo_solicitud==6){//solicitud capacitacion empresas
    $IdCurso=$cod_simulacion_servicio;
    $idEmpresa=$cod_cliente;
    ?>  
    <script type="text/javascript">
      location = "<?=$urlregistro_solicitud_facturacion_sec_empresas?>&codigo=<?=$idEmpresa?>&cod_simulacion=<?=$IdCurso;?>&IdCurso=<?=$IdCurso;?>&cod_facturacion=<?=$codigo_facturacion?>&cod_sw=5"
    </script><?php
  }

}

?>