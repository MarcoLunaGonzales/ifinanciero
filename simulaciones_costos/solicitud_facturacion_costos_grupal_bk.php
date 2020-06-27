<?php

require_once 'conexion.php';
require_once 'conexion_externa.php';
require_once 'styles.php';

require_once 'functionsGeneral.php';
require_once 'functions.php';
require_once 'configModule.php';

$dbh = new Conexion();
$dbhIBNO = new ConexionIBNORCA();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

$ci=$_GET['ci'];
$nombre=$_GET['nombre'];
$paterno=$_GET['paterno'];
$materno=$_GET['materno'];
$fecha=$_GET['fecha'];
$nombre_curso=$_GET['nombre_curso'];


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
  $sql.=" and cpe.clMaterno like '%$materno%'";
}
if($fecha!=""){
  $sql.=" and aa.FechaInscripcion like '%$fecha%'";
}
if($nombre_curso!=""){
  $sql.=" and pc.Nombre like '%$nombre_curso%'";
}
$sql.=" GROUP BY IdCurso Order by aa.FechaInscripcion desc";
// echo $sql;
?>
<div class="content">
  <div class="container-fluid">
    <div style="overflow-y:scroll;">
      <div class="col-md-12">
      <form id="form111" class="form-horizontal" action="<?=$urlregistro_solicitud_facturacion_grupal_est;?>" method="post" onsubmit="return valida(this)">
        <?php
            if(isset($_GET['q'])){?>
              <input type="hidden" name="q" id="q" value="<?=$q?>">
              <input type="hidden" name="r" id="r" value="<?=$r?>">
            <?php }
            ?>     
        <div class="card">
          <div class="card-header card-header-warning card-header-icon">
            <div class="card-icon">
              <i class="material-icons">polymer</i>
            </div>
            <h4 class="card-title"><b>Solicitud de Facturación Grupal para Capacitación</b></h4>                    
            <h4 class="card-title text-center"><b>Estudiantes</b></h4>            
          </div>          
          <div class="card-body">                      
            <table class="table table-sm">
              <thead>
                    <tr>
                      <th class="text-center"></th>                          
                        <th><small>CI Alumno</small></th>
                        <th><small>Nombre</small></th>
                        <th><small>Precio <br>curso (BOB)</small></th>                            
                        <th><small>Desc. <br>curso(%)</small></th>                              
                        <th><small>Importe <br>curso(BOB)</small></th>                   
                        <th><small>Nombre Curso</small></th>   
                        <th><small>Fecha Inscripción</small></th>
                        <th><small>Estado</small></th>
                        <th class="text-right"><small>Actions</small></th>
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
                    $monto_pagar=($Costo - ($Costo*$descuento/100) )/$CantidadModulos; //monto a pagar del estudiante 
                    $importe_curso=   $Costo*$descuento/100;//importe curso con desuento
                    $importe_curso= $Costo-$importe_curso;//importe curso con desuento       

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
                      if($cont_total_ws==$cont_total_pagados || $importe_curso==0){
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
                    <!-- guardamos todos los items en inputs -->
                    <input type="hidden" id="CiAlumno<?=$iii?>" name="CiAlumno<?=$iii?>" value="<?=$CiAlumno?>">
                    <input type="hidden" id="IdCurso<?=$iii?>" name="IdCurso<?=$iii?>" value="<?=$IdCurso?>">
                    <!-- aqui se captura los items activados -->
                    <input type="hidden" id="CiAlumno_a<?=$iii?>" name="CiAlumno_a<?=$iii?>">
                    <input type="hidden" id="IdCurso_a<?=$iii?>" name="IdCurso_a<?=$iii?>">                    
                    <tr>
                      <td align="center"></td>
                      <td><?=$CiAlumno;?></td>
                      <td class="text-left small"><?=$nombreAlumno;?></td>              
                      <td class="text-right small"><?=formatNumberDec($Costo) ;?></td>
                      <td class="text-right small"><?=$descuento ;?></td>                          
                      <td class="text-right small"><?=formatNumberDec($importe_curso) ;?></td>              
                      <td class="text-left small" ><?=$nombre_mod;?></td>      
                      <td class="text-right small"><?=$FechaInscripcion;?></td>
                      <td><button class="btn <?=$btnEstado?> btn-sm btn-link"><small><?=$estado;?></small></button></td> 
                      <td class="td-actions text-right">
                        <?php
                          if($sw_aux && $estado!="Pagado<br>total"){                            
                            if(isset($_GET['q'])){ ?>
                              <a href='<?=$urlregistro_solicitud_facturacion?>&codigo=<?=$CiAlumno?>&cod_simulacion=<?=$codigo_simulacion;?>&IdCurso=<?=$IdCurso;?>&cod_facturacion=0&q=<?=$q?>&r=<?=$r?>' rel="tooltip" class="btn" style="background-color: #0489B1;">
                                <i class="material-icons" title="Solicitar Facturación">receipt</i>
                              </a><?php 
                            }else{ ?>
                              <a href='<?=$urlregistro_solicitud_facturacion?>&codigo=<?=$CiAlumno?>&cod_simulacion=<?=$codigo_simulacion;?>&IdCurso=<?=$IdCurso;?>&cod_facturacion=0' rel="tooltip" class="btn" style="background-color: #0489B1;">
                                <i class="material-icons" title="Solicitar Facturación">receipt</i>
                              </a><?php 
                            }
                            if($codigo_facturacion>0){?>
                              <a class="btn btn-danger" href='<?=$urlPrintSolicitud;?>?codigo=<?=$codigo_facturacion;?>' target="_blank"><i class="material-icons" title="Imprimir Solicitud">print</i></a> <?php 
                            }
                          }
                        ?>
                        <?php
                          if($sw_aux && $estado!="Pagado<br>total"){?>
                            <div class="togglebutton">
                              <label>
                                <input type="checkbox"  id="modal_check_g<?=$iii?>" onchange="itemsSeleccionados_capacitacion_estudiantes()">
                                <span class="toggle"></span>
                              </label>
                            </div>
                          <?php }else{ ?>
                            <div class="togglebutton d-none">
                              <label>
                                <input type="checkbox"  id="modal_check_g<?=$iii?>" onchange="itemsSeleccionados_capacitacion_estudiantes()">
                                <span class="toggle"></span>
                              </label>
                            </div>
                          <?php }
                        ?>                                               
                      </td>
                    </tr>
                    <?php   
                      $iii++;
                  } ?> 
                  <input type="hidden" id="total_items" name="total_items" value="<?=$iii?>">
                  <input type="hidden" id="contador_auxiliar" name="contador_auxiliar" >
              </tbody>              
            </table>         
          </div>
          <div class="card-footer fixed-bottom">
            
            <button type="submit" class="btn btn-primary">SOLICITAR FACTURACION</button>
            <?php
            if(isset($_GET['q'])){?>
              <a href='<?=$urlListFacturasServicios_costos_estudiantes?>&ci=<?=$ci?>&nombre=<?=$nombre?>&paterno=<?=$paterno?>&materno=<?=$materno?>&fecha=<?=$fecha?>&nombre_curso=<?=$nombre_curso?>&q=<?=$q?>&v=<?=$r?>&u=<?=$r?>&s=<?=$r?>' class="<?=$buttonCancel;?>"><i class="material-icons"  title="Ir A Solicitudes de Facturación">keyboard_return</i> Volver</a>
              <?php }else{?>
                  <a href='<?=$urlListFacturasServicios_costos_estudiantes?>&ci=<?=$ci?>&nombre=<?=$nombre?>&paterno=<?=$paterno?>&materno=<?=$materno?>&fecha=<?=$fecha?>&nombre_curso=<?=$nombre_curso?>' class="<?=$buttonCancel;?>"><i class="material-icons"  title="Ir A Solicitudes de Facturación">keyboard_return</i> Volver</a>                    
            <?php }                     
              ?> 
          </div>
        </div>
      </form>
      </div>
    </div>  
  </div>
</div>
<script type="text/javascript">
    function valida(f) {
      var ok = true;
      var msg = "Habilite los Items que desee Solicitar la factura...\n";  
      var aux=f.elements["contador_auxiliar"].value;
      // alert(aux);
      if(aux == 0 || aux < 0 || aux == '')
      {    
        ok = false;
      }
      if(ok == false)    
        Swal.fire("Informativo!",msg, "warning");
      return ok;
    }
</script>