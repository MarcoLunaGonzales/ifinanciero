<?php

require_once 'conexion.php';
require_once 'conexion_externa.php';
require_once 'styles.php';

require_once 'functionsGeneral.php';
require_once 'functions.php';
require_once 'configModule.php';
require_once 'layouts/librerias.php';

$dbh = new Conexion();
$dbhIBNO = new ConexionIBNORCA();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

$cod_empresa=$_GET['cod_empresa'];
$glosa=$_GET['glosa'];
$codigo_curso_x=$_GET['codigo_curso'];
$codigo_simulacion=0;//codigo de simulacion
if(isset($_GET['q'])){
  $q=$_GET['q'];
  $r=$_GET['r'];  
  $s=$_GET['s'];  
  $u=$_GET['u'];  
}

$sql="SELECT pc.*,DATE_FORMAT(pc.FechaRegistro,'%d/%m/%Y')as FechaRegistro_x from programas_cursos pc where (pc.idEmpresa<>0 || pc.idEmpresa<>null)";  

if($cod_empresa!=""){
  $sql.=" and pc.idEmpresa in ($cod_empresa)";
}
if($glosa!=""){
  $sql.=" and pc.Nombre like '%$glosa%'";
}
if($codigo_curso_x!=""){
  $arrayCodigo=explode("-",$codigo_curso_x);
  $IdOficina=$arrayCodigo[0];
  $idprograma=$arrayCodigo[1];
  $idtipo=$arrayCodigo[2];
  $grupo=$arrayCodigo[3];
  $grupo_x=trim($grupo,'G');
  $IdGestion=$arrayCodigo[4];
  $sql.=" and d_abrevclasificador(pc.IdOficina) like '%$IdOficina%' and d_abrevclasificador(pc.idprograma) like '%$idprograma%' and d_abrevclasificador(pc.idtipo) like '%$idtipo%' and pc.grupo=$grupo_x and d_abrevclasificador(pc.IdGestion) like '%$IdGestion%'";
}
$sql.=" order by pc.IdCurso desc";
// echo $sql;
?>
<div class="content">
  <div class="container-fluid">
    <div style="overflow-y:scroll;">
      <div class="col-md-12">      
        <?php
            if(isset($_GET['q'])){?>
              <input type="hidden" name="q" id="q" value="<?=$q?>">
              <input type="hidden" name="r" id="r" value="<?=$r?>">
              <input type="hidden" name="s" id="s" value="<?=$s?>">
              <input type="hidden" name="u" id="u" value="<?=$u?>">
            <?php }
            ?>     
        <div class="card">
          <div class="card-header card-header-warning card-header-icon">
            <div class="card-icon">
              <i class="material-icons">polymer</i>
            </div>
            <h4 class="card-title"><b>Solicitud de Facturación</b></h4>                    
            <h4 class="card-title text-center"><b>Empresas</b></h4>            
          </div>          
          <div class="card-body">                      
            <table class="table table-sm" id="tablePaginator100">
                 <thead>
                      <tr>
                        <th class="text-center"></th>                          
                          <th>Id Empresa</th>
                          <th>Empresa</th>
                          <th>Precio <br>curso (BOB)</th>
                          <th><small>Código<br>curso</small></th>   
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
                    $stmtIBNO->bindColumn('Nombre', $nombre_curso);
                    $stmtIBNO->bindColumn('FechaRegistro_x', $FechaRegistro);
                    while ($rowPre = $stmtIBNO->fetch(PDO::FETCH_ASSOC)){
                      $codigo_curso=obtenerCodigoExternoCurso($IdCurso);
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
                        <td class="small"><?=$nombre_empresa;?></td>
                        <td class="text-right small"><?=formatNumberDec($Costo) ;?></td>  
                        <td class="text-left small" ><?=$codigo_curso;?></td>                                  
                        <td class="text-left small"><?=$nombre_curso;?> / # Modulos = <?=$CantidadModulos?></td>      
                        <td class="text-left small"><?=$FechaRegistro;?></td>   
                        <td class="td-actions text-right">
                          <?php
                            
                              if($codigo_facturacion>0){?>                      
                                <a class="btn btn-danger" href='<?=$urlPrintSolicitud;?>?codigo=<?=$codigo_facturacion;?>' target="_blank"><i class="material-icons" title="Imprimir Solicitud">print</i></a><?php 
                              }
                              if(isset($_GET['q'])){ ?>
                                <a href='<?=$urlregistro_solicitud_facturacion_empresas?>&codigo=<?=$idEmpresa?>&cod_simulacion=0&IdCurso=<?=$IdCurso;?>&cod_facturacion=0&q=<?=$q?>&r=<?=$r?>&u=<?=$u?>&s=<?=$s?>' rel="tooltip" class="btn" style="background-color: #0489B1;">
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
          </div>
          <div class="card-footer fixed-bottom">            
            <?php
            if(isset($_GET['q'])){?>
              <a href='<?=$urlSolicitudfactura_empresas?>&q=<?=$q?>&r=<?=$r?>&s=<?=$s?>&u=<?=$u?>' class="<?=$buttonCancel;?>"><i class="material-icons"  title="Volver Atrás">keyboard_return</i> Volver</a>
              <?php }else{?>
                  <a href='<?=$urlSolicitudfactura_empresas?>' class="<?=$buttonCancel;?>"><i class="material-icons"  title="Volver Atrás">keyboard_return</i> Volver</a>                    
            <?php }                     
              ?> 
          </div>
        </div>      
      </div>
    </div>  
  </div>
</div>

