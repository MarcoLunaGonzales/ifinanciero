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

$sql="SELECT tipo_solicitud,cod_simulacion_servicio,cod_cliente,ci_estudiante from solicitudes_facturacion where codigo=$codigo_facturacion";
$stmtCantidad = $dbh->prepare($sql);//and cod_estado=1
$stmtCantidad->execute();
$resutCanitdad = $stmtCantidad->fetch();
$tipo_solicitud = $resutCanitdad['tipo_solicitud'];//1 tcp_tcs, 2 capacitacion estudiantes,6 capacitacion empresas, 3 servicios,4 manual,5Normas, 7 capacitacion estudiantes grupal
$cod_simulacion_servicio = $resutCanitdad['cod_simulacion_servicio'];
$cod_cliente = $resutCanitdad['cod_cliente'];
$ci_estudiante = $resutCanitdad['ci_estudiante'];

// echo $ci_estudiante;
//para capacitacion grupal

if($tipo_solicitud==7){  
  $stmtGrupal = $dbh->prepare("SELECT sfg.cod_curso,sfg.ci_estudiante from solicitudes_facturaciondetalle sfg, solicitudes_facturacion sf where sf.codigo=sfg.cod_solicitudfacturacion and sf.codigo=$codigo_facturacion GROUP BY sfg.ci_estudiante,sfg.cod_curso");
  $stmtGrupal->execute();
  $string_ci="";
  $string_curso="";
  while ($rowPre = $stmtGrupal->fetch(PDO::FETCH_ASSOC)) {    
    $string_ci.=$rowPre['ci_estudiante'].",";
    $string_curso.=$rowPre['cod_curso'].",";
  }
  $string_ci=trim($string_ci,',');
  $string_curso=trim($string_curso,',');
}

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
    $CiAlumno=$ci_estudiante;
    ?>  
    <script type="text/javascript">
      location = "<?=$urlregistro_solicitud_facturacion_sec_estudiantes?>&codigo=<?=$CiAlumno?>&cod_simulacion=<?=$IdCurso;?>&IdCurso=<?=$IdCurso;?>&cod_facturacion=<?=$codigo_facturacion?>&cod_sw=5&q=<?=$q?>&r=<?=$v?>&s=<?=$s?>&u=<?=$u?>"
    </script><?php 
  }elseif($tipo_solicitud==6){//solicitud capacitacion empresas
    $IdCurso=$cod_simulacion_servicio;
    $idEmpresa=$cod_cliente;
    ?>  
    <script type="text/javascript">
      location = "<?=$urlregistro_solicitud_facturacion_sec_empresas?>&codigo=<?=$idEmpresa?>&cod_simulacion=<?=$IdCurso;?>&IdCurso=<?=$IdCurso;?>&cod_facturacion=<?=$codigo_facturacion?>&cod_sw=5&q=<?=$q?>&r=<?=$v?>&s=<?=$s?>&u=<?=$u?>"
    </script><?php
  }elseif($tipo_solicitud==7){//solicitud capacitacion estudiantes grupal
    // $IdCurso=$cod_simulacion_servicio;
    // $idEmpresa=$cod_cliente;
    ?>  
    <script type="text/javascript">
      location = "<?=$urlregistro_solicitud_facturacion_grupal_est?>?codigo_ci=<?=$string_ci?>&cod_simulacion=0&IdCurso=<?=$string_curso;?>&cod_facturacion=<?=$codigo_facturacion?>&cod_sw=5&q=<?=$q?>&r=<?=$v?>&s=<?=$s?>&u=<?=$u?>"
    </script>
    <?php
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
    $CiAlumno=$ci_estudiante;
    ?>  
    <script type="text/javascript">
      location = "<?=$urlregistro_solicitud_facturacion_sec_estudiantes?>&codigo=<?=$CiAlumno?>&cod_simulacion=<?=$IdCurso;?>&IdCurso=<?=$IdCurso;?>&cod_facturacion=<?=$codigo_facturacion?>&cod_sw=5"
    </script><?php 
  }elseif($tipo_solicitud==6){//solicitud capacitacion empresas
    $IdCurso=$cod_simulacion_servicio;
    $idEmpresa=$cod_cliente;
    ?>  
    <script type="text/javascript">
      location = "<?=$urlregistro_solicitud_facturacion_sec_empresas?>&codigo=<?=$idEmpresa?>&cod_simulacion=<?=$IdCurso;?>&IdCurso=<?=$IdCurso;?>&cod_facturacion=<?=$codigo_facturacion?>&cod_sw=5"
    </script><?php
  }elseif($tipo_solicitud==7){//solicitud capacitacion estudiantes grupal
    // $IdCurso=$cod_simulacion_servicio;
    // $idEmpresa=$cod_cliente;
    ?>  
    <script type="text/javascript">
      location = "<?=$urlregistro_solicitud_facturacion_grupal_est?>?codigo_ci=<?=$string_ci?>&cod_simulacion=0&IdCurso=<?=$string_curso;?>&cod_facturacion=<?=$codigo_facturacion?>&cod_sw=5"
    </script>
    <?php
  }

}

?>
