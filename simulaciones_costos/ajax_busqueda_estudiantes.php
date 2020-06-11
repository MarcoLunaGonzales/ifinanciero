<?php

require_once '../conexion.php';
require_once '../conexion_externa.php';
require_once '../styles.php';

require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once 'configModule.php';

$dbh = new Conexion();
$dbhIBNO = new ConexionIBNORCA();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

session_start();
$globalAdmin=$_SESSION["globalAdmin"];
// $globalAdmin=$_SESSION["globalAdmin"];
// $globalGestion=$_SESSION["globalGestion"];
// $globalUnidad=$_SESSION["globalUnidad"];
// $globalArea=$_SESSION["globalArea"];

$ci=$_GET['ci'];
$nombre=$_GET['nombre'];
$paterno=$_GET['paterno'];
$materno=$_GET['materno'];
$codigo_simulacion=0;//codigo de simulacion
set_time_limit(1000);
if(isset($_GET['q'])){
  $q=$_GET['q'];
  $r=$_GET['r'];  
}


// $unidadOrgString=implode(",", $cod_uo);
$sql="SELECT aa.IdModulo, aa.IdCurso, aa.CiAlumno,DATE_FORMAT(aa.FechaInscripcion,'%d/%m/%Y')as FechaInscripcion_x, concat(cpe.clPaterno,' ',cpe.clMaterno,' ',cpe.clNombreRazon)as nombreAlumno, c.Abrev, c.Auxiliar,
pc.Costo, pc.CantidadModulos, m.NroModulo, pc.Nombre, m.IdTema
FROM asignacionalumno aa, dbcliente.cliente_persona_empresa cpe, alumnocurso ac, clasificador c, programas_cursos pc, modulos m 
where cpe.clIdentificacion=aa.CiAlumno 
and ac.IdCurso=aa.IdCurso and ac.CiAlumno=aa.CiAlumno and ac.IdConceptoPago=c.IdClasificador and pc.IdCurso=aa.IdCurso and 
m.IdCurso=pc.IdCurso and m.IdModulo=aa.IdModulo ";  

if($ci!=""){
  $sql.=" and cpe.clIdentificacion=$ci";
}
if($nombre!=""){
  $sql.=" and cpe.clNombreRazon like '%$nombre%'";
}
if($paterno!=""){
  $sql.=" and cpe.clPaterno like '%$paterno%'";
}
if($materno!=""){
  $sql.=" and cpe.clMaterno like '%$materno&'";
}
$sql.=" GROUP BY IdCurso Order by aa.FechaInscripcion desc";

?>
  <table class="table table-sm" id="tablePaginator">
       <thead>
            <tr>
              <th class="text-center"></th>                          
                <th>CI Alumno</th>
                <th>Nombre</th>
                <th>Precio <br>curso (BOB)</th>                            
                <th>Desc. <br>curso(%)</th>                              
                <th>Importe <br>curso(BOB)</th>   
                <!-- <th>Importe <br>modulo(BOB)</th>   
                <th>Importe <br>Solicitud(BOB)</th>                   
                <th>Nro <br>Módulo</th>                 -->
                <th>Nombre Curso</th>   
                <th>Fecha Inscripción</th>
                <th>Estado</th>
                <th class="text-right">Actions</th>
            </tr>
        </thead>
        <tbody>                                
          <?php 
          $iii=1;
          // $queryPr="SELECT * from ibnorca.ventanormas where (idSolicitudfactura=0 or idSolicitudfactura is null) order by Fecha desc limit 20";
          // echo $queryPr;
          $stmtIBNO = $dbhIBNO->prepare($sql);
          $stmtIBNO->execute();

          $stmtIBNO->bindColumn('IdModulo', $IdModulo);
          $stmtIBNO->bindColumn('IdCurso', $IdCurso);
          $stmtIBNO->bindColumn('CiAlumno', $CiAlumno);
          $stmtIBNO->bindColumn('nombreAlumno', $nombreAlumno);
          $stmtIBNO->bindColumn('Abrev', $descuento);
          $stmtIBNO->bindColumn('Auxiliar', $Auxiliar);
          $stmtIBNO->bindColumn('Costo', $Costo);
          $stmtIBNO->bindColumn('CantidadModulos', $CantidadModulos);
          $stmtIBNO->bindColumn('NroModulo', $NroModulo);
          $stmtIBNO->bindColumn('Nombre', $nombre_mod);                                    
          $stmtIBNO->bindColumn('FechaInscripcion_x', $FechaInscripcion);
          while ($rowPre = $stmtIBNO->fetch(PDO::FETCH_ASSOC)){
            $cont_total_ws=0;
            $cont_total_pagados=0;
            $sw_aux=true;
            $verifica=verifica_pago_curso($IdCurso,$CiAlumno);
            // var_dump($verifica);
            if($verifica){
              foreach ($verifica->lstModulos as $listas) {
                $cont_total_ws++;
                $estadoPagado=$listas->EstadoPagado;              
                if($estadoPagado==1){
                  $cont_total_pagados++;
                }
              }
              // echo $cont_total_ws."-".$cont_total_pagados;              
              if($cont_total_ws==$cont_total_pagados){
                $estado="Pagado<br>total"; //pagado
                $btnEstado="btn-success";
              }else{
                $estado="Pendiente";//faltan algunos
                $btnEstado="btn-warning";
              }  
            }else{
                $estado="Sin Servicio";//faltan algunos
                $btnEstado="btn-danger";
            }
            if($cont_total_ws==0 && $cont_total_pagados==0){
              $sw_aux=false;
              $estado="No Encontrado";//faltan algunos
              $btnEstado="btn-danger"; 
            }
            

            $monto_pagar=($Costo - ($Costo*$descuento/100) )/$CantidadModulos; //monto a pagar del estudiante 
            $importe_curso=   $Costo*$descuento/100;//importe curso con desuento
            $importe_curso= $Costo-$importe_curso;//importe curso con desuento            
            //verificamos si ya tiene factura generada y esta activa                           
            $stmtFact = $dbh->prepare("SELECT codigo from solicitudes_facturacion where tipo_solicitud=2 and cod_cliente=$CiAlumno and cod_simulacion_servicio=$IdCurso");
            $stmtFact->execute();
            $resultSimu = $stmtFact->fetch();
            $codigo_facturacion = $resultSimu['codigo'];        
            if ($codigo_facturacion==null)$codigo_facturacion=0;
            $sumaTotalMonto=0;
            $sumaTotalDescuento_por=0;
            $sumaTotalDescuento_bob=0;
            $sumaTotalImporte=$sumaTotalMonto-$sumaTotalDescuento_bob;
            ?>
            <tr>
              <td align="center"></td>
              <td><?=$CiAlumno;?></td>
              <td class="text-left"><?=$nombreAlumno;?></td>              
              <td class="text-right"><?=formatNumberDec($Costo) ;?></td>
              <td class="text-right"><?=$descuento ;?></td>                          
              <td class="text-right"><?=formatNumberDec($importe_curso) ;?></td>                          
              <!-- <td class="text-right"><?=formatNumberDec($monto_pagar) ;?></td>                            
              <td class="text-right"><?=formatNumberDec($sumaTotalImporte) ;?></td>     
              <td><?=$NroModulo;?></td>                             -->
              <td class="text-left"><?=$nombre_mod;?></td>      
              <td class="text-right"><?=$FechaInscripcion;?></td>
              <td><button class="btn <?=$btnEstado?> btn-sm btn-link"><?=$estado;?></button></td> 
              <td class="td-actions text-right">
                <?php
                  if($globalAdmin==1 && $sw_aux){                            
                    if($codigo_facturacion>0){
                      if(isset($_GET['q'])){ ?>
                        <a href='<?=$urlregistro_solicitud_facturacion?>&codigo=<?=$CiAlumno?>&cod_simulacion=<?=$codigo_simulacion;?>&IdCurso=<?=$IdCurso;?>&cod_facturacion=0&q=<?=$q?>&r=<?=$r?>' rel="tooltip" class="btn" style="background-color: #0489B1;">
                          <i class="material-icons" title="Solicitar Facturación">receipt</i>
                        </a>
                        <!-- <a title="Editar Solicitud de Facturación" href='<?=$urlregistro_solicitud_facturacion?>&codigo=<?=$CiAlumno?>&cod_simulacion=<?=$IdCurso;?>&IdCurso=<?=$IdCurso;?>&cod_facturacion=<?=$codigo_facturacion?>&q=<?=$q?>&r=<?=$r?>' class="btn btn-success">
                            <i class="material-icons"><?=$iconEdit;?></i>
                        </a> -->
                        <?php 
                      }else{ ?>
                        <a href='<?=$urlregistro_solicitud_facturacion?>&codigo=<?=$CiAlumno?>&cod_simulacion=<?=$codigo_simulacion;?>&IdCurso=<?=$IdCurso;?>&cod_facturacion=0' rel="tooltip" class="btn" style="background-color: #0489B1;">
                          <i class="material-icons" title="Solicitar Facturación">receipt</i>
                        </a>
                        <!-- <a title="Editar Solicitud de Facturación" href='<?=$urlregistro_solicitud_facturacion?>&codigo=<?=$CiAlumno?>&cod_simulacion=<?=$IdCurso;?>&IdCurso=<?=$IdCurso;?>&cod_facturacion=<?=$codigo_facturacion?>' class="btn btn-success">
                            <i class="material-icons"><?=$iconEdit;?></i>
                        </a> --><?php 
                      }
                      ?>
                      <a class="btn btn-danger" href='<?=$urlPrintSolicitud;?>?codigo=<?=$codigo_facturacion;?>' target="_blank"><i class="material-icons" title="Imprimir Solicitud">print</i></a> <?php 
                    }else{//no se hizo solicitud de factura
                      if(isset($_GET['q'])){ ?>
                        <a href='<?=$urlregistro_solicitud_facturacion?>&codigo=<?=$CiAlumno?>&cod_simulacion=<?=$codigo_simulacion;?>&IdCurso=<?=$IdCurso;?>&cod_facturacion=0&q=<?=$q?>&r=<?=$r?>' rel="tooltip" class="btn" style="background-color: #0489B1;">
                          <i class="material-icons" title="Solicitar Facturación">receipt</i>
                        </a><?php 
                      }else{ ?>
                        <a href='<?=$urlregistro_solicitud_facturacion?>&codigo=<?=$CiAlumno?>&cod_simulacion=<?=$codigo_simulacion;?>&IdCurso=<?=$IdCurso;?>&cod_facturacion=0' rel="tooltip" class="btn" style="background-color: #0489B1;">
                          <i class="material-icons" title="Solicitar Facturación">receipt</i>
                        </a><?php 
                      }
                      
                    }
                  }
                ?>                                               
              </td>
            </tr>
              <?php   
                $iii++;
              } ?>  
              <input type="hidden" id="total_items" name="total_items" value="<?=$iii?>">
              <input type="hidden" id="comprobante_auxiliar" name="comprobante_auxiliar"><!-- contador de items seleccioados -->                          
        </tbody>
  </table>
