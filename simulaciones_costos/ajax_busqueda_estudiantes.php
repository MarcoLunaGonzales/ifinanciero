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
$codigo_curso=$_GET['codigo_curso'];


$codigo_simulacion=0;//codigo de simulacion

set_time_limit(1000);
if(isset($_GET['q'])){
  $q=$_GET['q'];
  $r=$_GET['r'];
  $s=$_GET['s'];
  $u=$_GET['u'];
}
// $unidadOrgString=implode(",", $cod_uo);
$sql="SELECT aa.IdModulo, aa.IdCurso,pc.idEmpresa, aa.CiAlumno,DATE_FORMAT(aa.FechaInscripcion,'%d/%m/%Y')as FechaInscripcion_x, concat(cpe.clPaterno,' ',cpe.clMaterno,' ',cpe.clNombreRazon)as nombreAlumno, c.Abrev, c.Auxiliar,
pc.Costo, pc.CantidadModulos, m.NroModulo, pc.Nombre, m.IdTema
FROM asignacionalumno aa, dbcliente.cliente_persona_empresa cpe, alumnocurso ac, clasificador c, programas_cursos pc, modulos m 
where cpe.clIdentificacion=aa.CiAlumno 
and ac.IdCurso=aa.IdCurso and ac.CiAlumno=aa.CiAlumno and ac.IdConceptoPago=c.IdClasificador and pc.IdCurso=aa.IdCurso and 
m.IdCurso=pc.IdCurso and m.IdModulo=aa.IdModulo ";  

if($ci!=""){
  $sql.=" and cpe.clIdentificacion like '%$ci%'";
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
if($codigo_curso!=""){
  $arrayCodigo=explode("-",$codigo_curso);
  $IdOficina=$arrayCodigo[0];
  $idprograma=$arrayCodigo[1];
  $idtipo=$arrayCodigo[2];
  $grupo=$arrayCodigo[3];
  $grupo_x=trim($grupo,'G');
  $IdGestion=$arrayCodigo[4];

  $sql.=" and d_abrevclasificador(pc.IdOficina) like '%$IdOficina%' and d_abrevclasificador(pc.idprograma) like '%$idprograma%' and d_abrevclasificador(pc.idtipo) like '%$idtipo%' and pc.grupo=$grupo_x and d_abrevclasificador(pc.IdGestion) like '%$IdGestion%'";
}


$sql.=" GROUP BY IdCurso,cpe.clIdentificacion Order by pc.Nombre desc";

//echo $sql;


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
              <input type="hidden" name="s" id="s" value="<?=$s?>">
              <input type="hidden" name="u" id="u" value="<?=$u?>">
            <?php }
            ?>     
        <div class="card">
          <div class="card-header card-header-warning card-header-icon">
            <div class="card-icon">
              <i class="material-icons">polymer</i>
            </div>
            <h4 class="card-title"><b>Solicitud de Facturación para Capacitación</b></h4>                    
            <h4 class="card-title text-center"><b>Estudiantes</b></h4>            
          </div>          
          <div class="card-body">                      
            <table class="table table-sm">
              <thead>
                    <tr>
                      <th class="text-center"></th>                          
                        <th width="5%"><small>CI Alumno</small></th>
                        <th><small>Nombre</small></th>
                        <th width="3%"><small>Precio <br>curso (BOB)</small></th>                            
                        <th width="3%"><small>Desc. <br>curso(%)</small></th>                              
                        <th width="3%"><small>Importe <br>curso(BOB)</small></th>                   
                        <th><small>Código<br>curso</small></th>                   
                        <th><small>Nombre Curso</small></th>
                        <th width="5%"><small>Fecha<br>Inscripción</small></th>
                        <th width="4%"><small>Estado</small></th>
                        <th class="text-right" width="5%"><small>Actions</small></th>
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
                  $stmtIBNO->bindColumn('idEmpresa', $idEmpresa);
                  $stmtIBNO->bindColumn('nombreAlumno', $nombreAlumno);
                  $stmtIBNO->bindColumn('Abrev', $descuento);
                  $stmtIBNO->bindColumn('Auxiliar', $Auxiliar);
                  $stmtIBNO->bindColumn('Costo', $Costo);
                  $stmtIBNO->bindColumn('CantidadModulos', $CantidadModulos);
                  $stmtIBNO->bindColumn('NroModulo', $NroModulo);
                  $stmtIBNO->bindColumn('Nombre', $nombre_mod);                                    
                  $stmtIBNO->bindColumn('FechaInscripcion_x', $FechaInscripcion);

                  while ($rowPre = $stmtIBNO->fetch(PDO::FETCH_ASSOC)){

                    $CiAlumno=preg_replace("[\n|\r|\n\r]", "", $CiAlumno);
                    $codigo_curso=obtenerCodigoExternoCurso($IdCurso);
                    $descuento=trim($descuento,'%');
                    $monto_pagar=($Costo-($Costo*$descuento/100))/$CantidadModulos; //monto a pagar del estudiante 
                    $importe_curso=$Costo*$descuento/100;//importe curso con desuento
                    $importe_curso=$Costo-$importe_curso;//importe curso con desuento       
                    //verificamos si pertence a empresas
                    if($idEmpresa==0 || $idEmpresa>0){
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
                        //echo "VERIFICACION....".$cont_total_ws."-".$cont_total_pagados;              
                        if(($cont_total_ws==$cont_total_pagados || $importe_curso==0)){
                          $estado="Pagado<br>total"; //pagado
                          $btnEstado="btn-success";
                          $style_s="style='color:  #239b56;'";
                          
                        }else{
                          $estado="Pendiente";//faltan algunos
                          // $btnEstado="btn-warning";
                          $style_s="style='color: #dc7633;'";
                        }  
                      }else{
                          $estado="Sin Servicio";//faltan algunos
                          $style_s="style='color: #ff0000;'";
                          // $btnEstado="btn-danger";
                      }
                      if($cont_total_ws==0 && $cont_total_pagados==0){
                        $sw_aux=false;
                        //$estado="No Encontrado"." ws:".$cont_total_ws." pag:".$cont_total_pagados." impcurso:".$importe_curso;//faltan algunos
                        $estado="No Encontrado";
                        $style_s="style='color: #ff0000;'";
                        // $btnEstado="btn-danger"; 
                      }
                    }else{
                      $sw_aux=false;
                      $estado="Facturación Por Empresa";//faltan algunos
                      $btnEstado="btn-danger"; 
                      $style_s="style='color: #ff0000;'";
                    }
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
                      <td class="text-left small" ><?=$codigo_curso;?></td>
                      <td class="text-left small" ><?=$nombre_mod;?> / # Modulos = <?=$CantidadModulos?></td>
                      <td class="text-right small"><?=$FechaInscripcion;?></td>
                      <td>
                        <span <?=$style_s?>><small><?=$estado;?></small></span>                                  
                      </td> 
                      <td class="td-actions text-right">
                        <?php
                          if($sw_aux && $estado!="Pagado<br>total"){
                            if(isset($_GET['q'])){ ?>
                              <a href='<?=$urlregistro_solicitud_facturacion?>&codigo=<?=$CiAlumno?>&cod_simulacion=<?=$codigo_simulacion;?>&IdCurso=<?=$IdCurso;?>&cod_facturacion=0&q=<?=$q?>&r=<?=$r?>&u=<?=$u?>&s=<?=$s?>' rel="tooltip" class="btn" style="background-color: #0489B1;padding: 3px; font-size:10px;width:23px;height:23px;">
                                <i class="material-icons" title="Solicitar Facturación">receipt</i>
                              </a><?php 
                            }else{ ?>
                              <a href='<?=$urlregistro_solicitud_facturacion?>&codigo=<?=$CiAlumno?>&cod_simulacion=<?=$codigo_simulacion;?>&IdCurso=<?=$IdCurso;?>&cod_facturacion=0' rel="tooltip" class="btn" style="background-color: #0489B1;padding: 3px; font-size:10px;width:23px;height:23px;">
                                <i class="material-icons" title="Solicitar Facturación">receipt</i>
                              </a><?php 
                            }                            
                          }else{//acticvamos los pdf de la solicitud
                            if($estado!="Facturación Por Empresa"){?>
                              <div class="btn-group dropdown">
                                <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><small>SF</small></button>
                                <div class="dropdown-menu"><?php 
                                  //verificamos si ya tiene factura generada y esta activa
                                  $sql="SELECT codigo,nro_correlativo,count(*)as contador from solicitudes_facturacion where tipo_solicitud=2 and ci_estudiante='$CiAlumno' and cod_simulacion_servicio='$IdCurso'
                                  UNION
                                  SELECT fd.cod_solicitudfacturacion as codigo,f.nro_correlativo, count(*)as contador from solicitudes_facturacion f, solicitudes_facturaciondetalle fd where f.codigo=fd.cod_solicitudfacturacion and f.tipo_solicitud=7 and fd.ci_estudiante='$CiAlumno' and fd.cod_curso='$IdCurso'";
                                  //echo $sql;
                                  $stmtFact = $dbh->prepare($sql);
                                  $stmtFact->execute();
                                  $contador_SolFact=0;
                                  $contadorSolQuery=0;
                                  while ($rowFact = $stmtFact->fetch(PDO::FETCH_ASSOC)){
                                    //echo "entro";
                                    $contador_SolFact++;
                                    $cod_factura_x=$rowFact['codigo'];
                                    $nro_correlativo_x=$rowFact['nro_correlativo'];
                                    $contadorSolQuery=$rowFact['contador'];
                                    if($cod_factura_x!=0){?>
                                      <a class="dropdown-item" type="button" href='<?=$urlPrintSolicitud;?>?codigo=<?=$cod_factura_x;?>' target="_blank"><i class="material-icons text-danger" title="Imprimir Factura">print</i> SF Nro: <?=$nro_correlativo_x?></a>
                                    <?php }
                                  }
                                  if($contador_SolFact==0 || $contadorSolQuery==0){?>
                                    <span style="color: #ff0000;"><small>Solicitudes No Encontradas</small></span>                                 
                                  <?php }
                                  ?>
                                </div>
                              </div> <?php
                            }
                            
                          }
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
            <button type="submit" class="btn btn-primary">SOLICITAR FACTURACION GRUPAL</button>
            <?php
            if(isset($_GET['q'])){?>
              <a href='<?=$urlSolicitudfactura?>&q=<?=$q?>&r=<?=$r?>&s=<?=$s?>&u=<?=$u?>' class="<?=$buttonCancel;?>"><i class="material-icons"  title="Volver Atrás">keyboard_return</i> Volver</a>
              <?php }else{?>
                  <a href='<?=$urlSolicitudfactura?>' class="<?=$buttonCancel;?>"><i class="material-icons"  title="Volver Atrás">keyboard_return</i> Volver</a>                    
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