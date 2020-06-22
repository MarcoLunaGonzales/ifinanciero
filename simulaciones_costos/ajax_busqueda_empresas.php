<?php

require_once '../conexion.php';
require_once '../conexion_externa.php';
require_once '../styles.php';

require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once 'configModule.php';
require_once '../layouts/bodylogin2.php';

$dbh = new Conexion();
$dbhIBNO = new ConexionIBNORCA();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

// session_start();
// $globalAdmin=$_SESSION["globalAdmin"];
// $globalAdmin=$_SESSION["globalAdmin"];
// $globalGestion=$_SESSION["globalGestion"];
// $globalUnidad=$_SESSION["globalUnidad"];
// $globalArea=$_SESSION["globalArea"];


$cod_empresa=$_GET['cod_empresa'];
$glosa=$_GET['glosa'];
$codigo_simulacion=0;//codigo de simulacion
if(isset($_GET['q'])){
  $q=$_GET['q'];
  $r=$_GET['r'];  
}

$sql="SELECT *,DATE_FORMAT(FechaRegistro,'%d/%m/%Y')as FechaRegistro_x from programas_cursos pc where (pc.idEmpresa<>0 || pc.idEmpresa<>null)";  

if($cod_empresa!=""){
  $sql.=" and idEmpresa in ($cod_empresa)";
}
if($glosa!=""){
  $sql.=" and Nombre like '%$glosa%'";
}
$sql.=" order by pc.IdCurso desc";
// echo $sql;
?>
  <table class="table table-sm" id="tablePaginator">
       <thead>
            <tr>
              <th class="text-center"></th>                          
                <th>Id Empresa</th>
                <th>Empresa</th>
                <th>Precio <br>curso (BOB)</th>
                <th>Nombre Curso</th>
                <th>Fecha Registro</th>
                <th class="text-right">Actions</th>
            </tr>
        </thead>
        <tbody>                                
          <?php 
          $iii=1;          
          $stmtIBNO = $dbhIBNO->prepare($sql);
          $stmtIBNO->execute();

          $stmtIBNO->bindColumn('IdCurso', $IdCurso);
          $stmtIBNO->bindColumn('IdPrograma', $IdPrograma);
          $stmtIBNO->bindColumn('idEmpresa', $idEmpresa);
          // $stmtIBNO->bindColumn('nombreAlumno', $nombreAlumno);                   
          $stmtIBNO->bindColumn('Costo', $Costo);
          $stmtIBNO->bindColumn('CantidadModulos', $CantidadModulos);          
          $stmtIBNO->bindColumn('Nombre', $nombre_mod);
          $stmtIBNO->bindColumn('FechaRegistro_x', $FechaRegistro);
          while ($rowPre = $stmtIBNO->fetch(PDO::FETCH_ASSOC)){
            $monto_pagar=$Costo; //monto a pagar del estudiante                         
            $codigo_facturacion=0;
            $nombre_empresa=nameCliente($idEmpresa);
            $sumaTotalMonto=0;
            $sumaTotalDescuento_por=0;
            $sumaTotalDescuento_bob=0;
            $sumaTotalImporte=$sumaTotalMonto-$sumaTotalDescuento_bob;


            //verificamos si ya tiene factura generada y esta activa                           
            $stmtFact = $dbh->prepare("SELECT codigo from solicitudes_facturacion where tipo_solicitud=2 and cod_cliente=$idEmpresa and cod_simulacion_servicio=$IdCurso");
            $stmtFact->execute();
            $resultSimu = $stmtFact->fetch();
            $codigo_facturacion = $resultSimu['codigo'];
            

            if ($codigo_facturacion==null)$codigo_facturacion=0;    
            ?>
            <tr>
              <td align="center"></td>
              <td><?=$idEmpresa;?></td>
              <td><?=$nombre_empresa;?></td>
              <td class="text-right"><?=formatNumberDec($Costo) ;?></td>                                    
              <td class="text-left"><?=$nombre_mod;?></td>      
              <td class="text-left"><?=$FechaRegistro;?></td>   
              <td class="td-actions text-right">
                <?php
                  
                    if($codigo_facturacion>0){?>                      
                      <a class="btn btn-danger" href='<?=$urlPrintSolicitud;?>?codigo=<?=$codigo_facturacion;?>' target="_blank"><i class="material-icons" title="Imprimir Solicitud">print</i></a><?php 
                    }
                    if(isset($_GET['q'])){ ?>
                      <a href='<?=$urlregistro_solicitud_facturacion_empresas?>&codigo=<?=$idEmpresa?>&cod_simulacion=0&IdCurso=<?=$IdCurso;?>&cod_facturacion=0&q=<?=$q?>&r=<?=$r?>' rel="tooltip" class="btn" style="background-color: #0489B1;">
                        <i class="material-icons" title="Solicitar Facturación">receipt</i>
                      </a><?php 
                    }else{ ?>
                      <a href='<?=$urlregistro_solicitud_facturacion_empresas?>&codigo=<?=$idEmpresa?>&cod_simulacion=0&IdCurso=<?=$IdCurso;?>&cod_facturacion=0' rel="tooltip" class="btn" style="background-color: #0489B1;">
                        <i class="material-icons" title="Solicitar Facturación">receipt</i>
                      </a><?php 
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
