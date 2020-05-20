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


$cod_empresa=$_GET['cod_empresa'];
$glosa=$_GET['glosa'];
$codigo_simulacion=0;//codigo de simulacion
if(isset($_GET['q'])){
  $q=$_GET['q'];
  $r=$_GET['r'];  
}

$sql="SELECT * from programas_cursos pc where (pc.idEmpresa<>0 || pc.idEmpresa<>null)";  

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
                <!-- <th>Desc. <br>curso(%)</th>                              
                <th>Importe <br>curso(BOB)</th>    -->
                <!-- <th>Importe <br>modulo(BOB)</th>   
                <th>Importe <br>Solicitud(BOB)</th>                   
                <th>Nro <br>Módulo</th>                 -->
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
          $stmtIBNO->bindColumn('FechaRegistro', $FechaRegistro);
          while ($rowPre = $stmtIBNO->fetch(PDO::FETCH_ASSOC)){
            $monto_pagar=$Costo; //monto a pagar del estudiante                         
            $codigo_facturacion=0;
            $nombre_empresa=nameCliente($idEmpresa);
            $sumaTotalMonto=0;
            $sumaTotalDescuento_por=0;
            $sumaTotalDescuento_bob=0;
            $sumaTotalImporte=$sumaTotalMonto-$sumaTotalDescuento_bob;
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
                  if($globalAdmin==1){                            
                    if($codigo_facturacion>0){
                      if($codigo_fact_x==0){ //no se genero factura ?>
                        <!-- <a title="Editar Solicitud de Facturación" href='<?=$urlregistro_solicitud_facturacion?>&codigo=<?=$CiAlumno?>&cod_simulacion=<?=$codigo_simulacion;?>&cod_facturacion=<?=$codigo_facturacion?>' class="btn btn-success">
                            <i class="material-icons"><?=$iconEdit;?></i>
                          </a> --><?php 
                      }else{//ya se genero factura ?>
                        <!-- <a class="btn btn-success" href='<?=$urlGenerarFacturasPrint;?>?codigo=<?=$codigo_facturacion;?>&tipo=2' target="_blank"><i class="material-icons" title="Imprimir Factura">print</i></a> --><?php 
                      }?>
                      <!-- <a href='#' rel="tooltip" class="btn btn-warning" onclick="filaTablaAGeneral($('#tablasA_registradas'),<?=$index?>,'<?=$stringCabecera?>')">
                        <i class="material-icons" title="Ver Detalle">settings_applications</i>
                      </a> -->
                      <!-- <a class="btn btn-danger" href='<?=$urlPrintSolicitud;?>?codigo=<?=$codigo_facturacion;?>' target="_blank"><i class="material-icons" title="Imprimir Solicitud">print</i></a>  --><?php 
                    }else{//no se hizo solicitud de factura
                      if(isset($_GET['q'])){ ?>
                        <a href='<?=$urlregistro_solicitud_facturacion_empresas?>&codigo=<?=$idEmpresa?>&cod_simulacion=0&IdCurso=<?=$IdCurso;?>&cod_facturacion=0&q=<?=$q?>&r=<?=$r?>' rel="tooltip" class="btn" style="background-color: #0489B1;">
                          <i class="material-icons" title="Solicitar Facturación">receipt</i>
                        </a><?php 
                      }else{ ?>
                        <a href='<?=$urlregistro_solicitud_facturacion_empresas?>&codigo=<?=$idEmpresa?>&cod_simulacion=0&IdCurso=<?=$IdCurso;?>&cod_facturacion=0' rel="tooltip" class="btn" style="background-color: #0489B1;">
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
