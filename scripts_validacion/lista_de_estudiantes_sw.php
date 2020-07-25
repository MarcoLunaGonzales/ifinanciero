<?php
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../styles.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';

$dbh = new Conexion();
$stmt = $dbh->prepare("SELECT f.Monto, f.CiAlumno,f.IdSolicitudFactura,f.IdCurso,f.IdModulo,f.Fecha from ibnorca.controlpagos f where f.PlataformaPago=13 order by f.Fecha"); /*and sf.cod_estadosolicitudfacturacion!=5*/
$stmt->execute();
$stmt->bindColumn('Monto', $Monto);
$stmt->bindColumn('CiAlumno', $CiAlumno);
$stmt->bindColumn('IdSolicitudFactura', $IdSolicitudFactura);
$stmt->bindColumn('IdModulo', $IdModulo);
$stmt->bindColumn('IdCurso', $IdCurso);
$stmt->bindColumn('Fecha', $Fecha);


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
                      <table class="table" id="tablePaginator">
                        <thead>
                          <tr>                            
                            <th><small>Nombre Estudiante</small></th>
                            <th><small>CI</small></th>
                            <th><small>Fecha Pago</small></th>
                            <th><small>Curso</small></th>
                            <th><small>Módulo</small></th>                            
                            <th><small>Monto</small></th>
                            <th><small>Nro Solicitud</small></th>
                          </tr>
                        </thead>
                        <tbody>
                        <?php
                        $index=1;
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {           
                          $nombreAlumno=obtnerNombreComprimidoEstudiante($CiAlumno);
                          //buscamos el modulo
                          $sql="SELECT  (select ibnorca.d_clasificador(m.IdTema))as nombre_tema
                                FROM  ibnorca.modulos m 
                                where m.IdModulo=$IdModulo";    
                            // echo $queryPr;
                          $stmtModulo = $dbh->prepare($queryPr);
                          $stmtModulo->execute();
                          $result=$stmtModulo->fetch();
                          $nombreModulo=$result['nombre_tema'];     
                          //buscamos curso
                          $sql="SELECT  pc.Nombre FROM ibnorca.programas_cursos pc  where  pc.IdCurso=$IdCurso";
                          $stmtCurso = $dbh->prepare($sql);
                          $stmtCurso->execute();
                          $resultCurso=$stmtCurso->fetch();
                          $nombreCurso=$resultCurso['Nombre'];

                          $sql="SELECT  f.nro_correlativo from solicitudes_facturacion f where f.codigo=$IdSolicitudFactura";
                          $stmtSolicitud = $dbh->prepare($sql);
                          $stmtSolicitud->execute();
                          $resultSolicitud=$stmtSolicitud->fetch();
                          $nro_solicitud=$resultSolicitud['nro_correlativo'];
                           ?>
                          <tr>
                           <td><small><?=$nombreAlumno?></small></td>
                           <td><small><?=$CiAlumno;?></small></td>
                           <td><small><?=$Fecha;?></small></td>
                           <td><small><?=$nombreCurso;?></small></td>
                           <td><small><?=$nombreModulo;?></small></td>
                           <td><small><?=$Monto;?></small></td>                           
                           <td><small><?=$nro_solicitud;?></small></td>  
                          </tr>
                          <?php
                              $index++;
                            }
                          ?>
                        </tbody>
                      </table>
                  </div>
                </div>     
              </div>
          </div>  
    </div>
  </div>