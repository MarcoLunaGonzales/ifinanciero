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

if(isset($_GET['q'])){
  $q=$_GET['q'];
  $r=$_GET['r'];  
}


// $unidadOrgString=implode(",", $cod_uo);
$sql="SELECT aa.IdModulo, aa.IdCurso, aa.CiAlumno, concat(cpe.clPaterno,' ',cpe.clMaterno,' ',cpe.clNombreRazon)as nombreAlumno, c.Abrev, c.Auxiliar,
pc.Costo, pc.CantidadModulos, m.NroModulo, pc.Nombre, m.IdTema
FROM asignacionalumno aa, dbcliente.cliente_persona_empresa cpe, alumnocurso ac, clasificador c, programas_cursos pc, modulos m 
where cpe.clIdentificacion=aa.CiAlumno 
and ac.IdCurso=aa.IdCurso and ac.CiAlumno=aa.CiAlumno and ac.IdConceptoPago=c.IdClasificador and pc.IdCurso=aa.IdCurso and 
m.IdCurso=pc.IdCurso and m.IdModulo=aa.IdModulo";  

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
$sql.=" GROUP BY IdCurso Order by nombreAlumno";

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
                <th>Nombre Mod</th>   
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
          while ($rowPre = $stmtIBNO->fetch(PDO::FETCH_ASSOC)){
            $monto_pagar=($Costo - ($Costo*$descuento/100) )/$CantidadModulos; //monto a pagar del estudiante 
            $importe_curso=   $Costo*$descuento/100;//importe curso con desuento
            $importe_curso= $Costo-$importe_curso;//importe curso con desuento
            // $nombre_area=trim(abrevArea($cod_area),'-');
            // $nombre_uo=trim(abrevUnidad($cod_uo),' - ');                  
            //buscamos a los estudiantes que ya fueron solicitados su facturacion
            $codigo_facturacion=0;

            $sumaTotalMonto=0;
            $sumaTotalDescuento_por=0;
            $sumaTotalDescuento_bob=0;
            $sumaTotalImporte=$sumaTotalMonto-$sumaTotalDescuento_bob;
            ?>
            <tr>
              <td align="center"></td>
              <td><?=$CiAlumno;?></td>
              <td><?=$nombreAlumno;?></td>
              <td class="text-right"><?=formatNumberDec($Costo) ;?></td>
              <td class="text-right"><?=$descuento ;?></td>                          
              <td class="text-right"><?=formatNumberDec($importe_curso) ;?></td>                          
              <!-- <td class="text-right"><?=formatNumberDec($monto_pagar) ;?></td>                            
              <td class="text-right"><?=formatNumberDec($sumaTotalImporte) ;?></td>     
              <td><?=$NroModulo;?></td>                             -->
              <td class="text-left"><?=$nombre_mod;?></td>      
              <td class="td-actions text-right">
                <?php
                  if($globalAdmin==1){                            
                    if($codigo_facturacion>0){
                      if($codigo_fact_x==0){ //no se genero factura ?>
                        <a title="Editar Solicitud de Facturación" href='<?=$urlregistro_solicitud_facturacion?>&codigo=<?=$CiAlumno?>&cod_simulacion=<?=$codigo_simulacion;?>&cod_facturacion=<?=$codigo_facturacion?>' class="btn btn-success">
                            <i class="material-icons"><?=$iconEdit;?></i>
                          </a><?php 
                      }else{//ya se genero factura ?>
                        <a class="btn btn-success" href='<?=$urlGenerarFacturasPrint;?>?codigo=<?=$codigo_facturacion;?>&tipo=2' target="_blank"><i class="material-icons" title="Imprimir Factura">print</i></a><?php 
                      }?>
                      <a href='#' rel="tooltip" class="btn btn-warning" onclick="filaTablaAGeneral($('#tablasA_registradas'),<?=$index?>,'<?=$stringCabecera?>')">
                        <i class="material-icons" title="Ver Detalle">settings_applications</i>
                      </a>
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
