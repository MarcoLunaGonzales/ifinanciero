<?php
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../styles.php';
require_once '../functions.php';
require_once '../functionsReportes.php';
require_once '../functionsGeneral.php';

$dbh = new Conexion();
$sql="SELECT fd.codigo as cod_detale,f.codigo as cod_factura,f.nro_factura,f.cod_solicitudfacturacion,f.fecha_factura,fd.cod_claservicio,(fd.cantidad*fd.precio-fd.descuento_bob)*(da.porcentaje/100) importe,fd.descripcion_alterna,fd.ci_estudiante,
(select SUM((ffd.cantidad*ffd.precio-ffd.descuento_bob)) from facturas_venta ff, facturas_ventadetalle ffd where 
ffd.cod_claservicio=fd.cod_claservicio and ffd.ci_estudiante=fd.ci_estudiante and ff.codigo=ffd.cod_facturaventa and ff.cod_estadofactura<>2) as importe_acumulado
FROM facturas_venta f,facturas_venta_distribucion da,facturas_ventadetalle fd
WHERE f.codigo=da.cod_factura and f.codigo=fd.cod_facturaventa and f.cod_estadofactura<>2 and da.cod_area=13 and f.fecha_factura between '2020-07-01 00:00:00' and '2020-11-04 23:59:59' and f.cod_solicitudfacturacion<>-100 order by f.fecha_factura";
$stmt = $dbh->prepare($sql); /*and sf.cod_estadosolicitudfacturacion!=5*/
$stmt->execute();
$stmt->bindColumn('cod_detale', $cod_detale);
$stmt->bindColumn('cod_factura', $cod_factura);
$stmt->bindColumn('nro_factura', $nro_factura);
$stmt->bindColumn('cod_solicitudfacturacion', $cod_solicitudfacturacion);
$stmt->bindColumn('fecha_factura', $fecha_factura);
$stmt->bindColumn('cod_claservicio', $cod_claservicio);
$stmt->bindColumn('importe', $importe);
$stmt->bindColumn('importe_acumulado', $importe_acumulado);
$stmt->bindColumn('descripcion_alterna', $descripcion_alterna);
$stmt->bindColumn('ci_estudiante', $ci_estudiante);

?>
  <div class="content">
    <div class="container-fluid">
          <div class="row">
              <div class="col-md-12">
                <div class="card">
                  <div class="card-header card-header-info card-header-icon">
                    <div class="card-icon">
                      <i class="material-icons">content_paste</i>
                    </div>
                    <h4 class="card-title"><b>Gesti&oacute;n de Facturas</b></h4>
            
                  </div>
                  <div class="card-body">
                      <table class="table" id="libro_mayor_rep" >
                        <thead>
                          <tr>
                            <th><small>Codigo Det.</small></th>
                            <th><small>Nro <br>Factura</small></th>
                            <th><small>Nombre Estudiante</small></th>
                            <th><small>CI</small></th>
                            <th class="bg-danger"><small>Codigo Curso</small></th>
                            <th class="bg-danger"><small>Codigo Modulo</small></th>
                            <th class="bg-danger"><small>Codigo Sol</small></th>
                            <th><small>Fecha Pago</small></th>
                            <th><small>Curso</small></th>
                            <th><small>Módulo</small></th>                            
                            <th><small>Nro Solicitud</small></th>
                            <th><small>Monto Factura</small></th>
                            <th><small>Monto Acumulado</small></th>
                            <th><small>Monto Pago</small></th>
                            <th><small>Cantidad Pagos</small></th>
                            <th><small>Montos Cantidad Pagos</small></th>
                          </tr>
                        </thead>
                        <tbody>
                        <?php
                        $index=1;
                        $monto_totalfactura=0;
                        $monto_totalpago=0;
                        $monto_totalfactura_acumulado=0;
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                          $importe=round($importe,2);
                          $importe_acumulado=round($importe_acumulado,2);
                          $cantidad_pago=0;
                          $monto_string_pago="";
                          if($cod_solicitudfacturacion!=-100){
                            $sql="SELECT sum(f.Monto) as Monto,count(*)as cantidad,GROUP_CONCAT(Monto)as Montos_c, f.CiAlumno,f.IdSolicitudFactura,f.IdCurso,f.IdModulo,f.Fecha from ibnorca.controlpagos f where f.PlataformaPago=13 and  f.IdModulo=$cod_claservicio and  f.CiAlumno = '$ci_estudiante' order by f.Fecha desc"; //and f.IdSolicitudFactura=$cod_solicitudfacturacion
                            $stmt2 = $dbh->prepare($sql); 
                            // echo $sql;
                            $stmt2->execute();
                            $result=$stmt2->fetch();
                            $monto_pago=$result['Monto'];
                            $cantidad_pago=$result['cantidad'];
                            $monto_string_pago=$result['Montos_c'];
                            $IdSolicitudFactura=$result['IdSolicitudFactura'];
                            //buscamos el modulo                          
                            $nombreAlumno=obtnerNombreComprimidoEstudiante($ci_estudiante);
                            //buscamos el modulo
                            $sql="SELECT  (select ibnorca.d_clasificador(m.IdTema))as nombre_tema
                                  FROM  ibnorca.modulos m 
                                  where m.IdModulo=$cod_claservicio";    
                              // echo $queryPr;
                            $stmtModulo = $dbh->prepare($sql);
                            $stmtModulo->execute();
                            $result=$stmtModulo->fetch();
                            $nombreModulo=$result['nombre_tema'];     
                            //buscamos curso
                            $sql="SELECT (select pc.Nombre from ibnorca.programas_cursos pc where pc.IdCurso=a.IdCurso)as nombre,a.IdCurso From ibnorca.asignacionalumno a where a.CiAlumno = '$ci_estudiante' and a.IdModulo=$cod_claservicio;";

                            // echo $cod_solicitudfacturacion;
                            $stmtCurso = $dbh->prepare($sql);
                            $stmtCurso->execute();
                            $resultCurso=$stmtCurso->fetch();
                            $nombreCurso=$resultCurso['nombre'];
                            $modulo_id=$cod_claservicio;
                            $curso_id=$resultCurso['IdCurso'];
                            
                            $sql="SELECT  f.nro_correlativo,f.tipo_solicitud from solicitudes_facturacion f where f.codigo=$cod_solicitudfacturacion";
                            $stmtSolicitud = $dbh->prepare($sql);
                            $stmtSolicitud->execute();
                            $resultSolicitud=$stmtSolicitud->fetch();
                            $nro_solicitud=$resultSolicitud['nro_correlativo'];
                            $tipo_solicitud=$resultSolicitud['tipo_solicitud'];
                          }else{
                            $sql=" SELECT m.curso_id,(select c.CiAlumno from ibnorca.alumnos c where c.IdAlumno=m.cliente_id)ci_estudiante,m.precio_total
                            FROM ibnorcatienda.pago_curso m
                            WHERE m.pago_id=$cod_claservicio";
                            // echo $sql; 
                            $IdSolicitudFactura="-";
                            $stmtSolicitud = $dbh->prepare($sql);
                            $stmtSolicitud->execute();
                            $resultSolicitud=$stmtSolicitud->fetch();
                            $ci_estudiante=$resultSolicitud['ci_estudiante'];
                            $curso_id=$resultSolicitud['curso_id'];
                            $modulo_id=0;
                            $monto_pago=$resultSolicitud['precio_total'];

                            $nombreCurso=obtenerNombreCurso($curso_id);
                            //buscamos el modulo                          
                            $nombreAlumno=obtnerNombreComprimidoEstudiante($ci_estudiante);
                            // $nombreAlumno="";
                            //buscamos el modulo                            
                            $nombreModulo="";                                 
                            // $nombreCurso="";
                            $nro_solicitud="TIENDA";
                            $tipo_solicitud=2;
                          }
                          if($tipo_solicitud<>4 && $tipo_solicitud<>6){
                          $monto_totalpago+=$monto_pago;
                          $monto_totalfactura_acumulado+=$importe_acumulado;
                          $monto_totalfactura+=$importe;
                          if($importe_acumulado!=$monto_pago)
                          {
                            $stringstyle="color:#ff0000";
                          }else{
                            $stringstyle="";
                          }
                          
                           ?>
                            <tr>
                             <td><small><?=$cod_detale?></small></td>
                             <td><small><?=$nro_factura?></small></td> 
                             <td><small><?=$nombreAlumno?></small></td>
                             <td><small><?=$ci_estudiante;?></small></td>

                             <td><small><?=$curso_id;?></small></td>
                             <td><small><?=$modulo_id;?></small></td>
                             <td><small><?=$IdSolicitudFactura;?></small></td>

                             <td><small><?=$fecha_factura;?></small></td>
                             <td><small><?=$nombreCurso;?></small></td>
                             <td><small><?=$nombreModulo;?></small></td>
                             <td><small><?=$nro_solicitud;?></small></td>  
                             <td><small><?=$importe;?></small></td>
                             <td><small><?=$importe_acumulado;?></small></td>                           
                             <td><small><span style="<?=$stringstyle;?>" ><?=$monto_pago;?></span></small></td>
                             <td><small><span style="<?=$stringstyle;?>" ><?=$cantidad_pago;?></span></small></td>
                             <td><small><span style="<?=$stringstyle;?>" ><?=$monto_string_pago;?></span></small></td>
                            </tr>
                            <?php
                              $index++;
                          }
                        }
                          ?>
                        </tbody>
                        <tfoot>
                          <tr>
                           <td><small>-</small></td>
                           <td><small>-</small></td> 
                           <td><small>-</small></td>
                           <td><small>-</small></td>
                           <td><small>-</small></td>
                           <td><small>-</small></td>
                           <td><small>-</small></td>
                           <td><small>-</small></td>
                           <td><small>-</small></td>
                           <td><small>-</small></td>
                           <td><small>-</small></td>  
                           <td><small><?=$monto_totalfactura;?></small></td>                           
                           <td><small><?=$monto_totalfactura_acumulado;?></small></td>
                           <td><small><?=$monto_totalpago?></small></td>
                           <td><small>-</small></td>
                           <td><small>-</small></td>  
                          </tr>
                        </tfoot>
                      </table>
                  </div>
                </div>     
              </div>
          </div>  
    </div>
  </div>