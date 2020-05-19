<?php
require_once 'conexion.php';
require_once 'conexion_externa.php';
require_once 'configModule.php';
require_once 'styles.php';
$codigo_simulacion=0;//codigo de simulacion
$dbh = new Conexion();
$globalAdmin=$_SESSION["globalAdmin"];
if(isset($_GET['q'])){
  $q=$_GET['q'];
  $r=$_GET['r'];  
}
// $sql="SELECT nombre,cod_area,cod_uo from simulaciones_costos where codigo=$codigo_simulacion";
// $stmtSimu = $dbh->prepare($sql);
// $stmtSimu->execute();
// $resultSimu = $stmtSimu->fetch();
// $nombre_simulacion = $resultSimu['nombre'];
// $cod_area = $resultSimu['cod_area'];
// $cod_uo = $resultSimu['cod_uo'];
//simulamos conexion con ibnorca
$dbhIBNO = new ConexionIBNORCA();
//sacamos el nombre del curso
$stmtIBNOCurso = $dbhIBNO->prepare("SELECT pc.Nombre
FROM asignacionalumno aa, alumnos a, alumnocurso ac, clasificador c, programas_cursos pc, modulos m where aa.IdModulo=4000 and a.CiAlumno=aa.CiAlumno 
and ac.IdCurso=aa.IdCurso and ac.CiAlumno=aa.CiAlumno and ac.IdConceptoPago=c.IdClasificador and pc.IdCurso=pc.IdCurso and pc.IdCurso=aa.IdCurso and 
m.IdCurso=pc.IdCurso and m.IdModulo=aa.IdModulo limit 1");//poner el codigo de curso a buscar
  $stmtIBNOCurso->execute();
  $resultNombreCurso = $stmtIBNOCurso->fetch();
$nombre_curso = $resultNombreCurso['Nombre'];
  //datos registrado de la simulacion en curso
$stmtIBNO = $dbhIBNO->prepare("SELECT aa.IdModulo, aa.IdCurso, aa.CiAlumno, concat(a.ApPaterno,' ',a.ApMaterno,' ',a.Nombre)as nombreAlumno, c.Abrev, c.Auxiliar,
pc.Costo, pc.CantidadModulos, m.NroModulo, pc.Nombre
FROM asignacionalumno aa, alumnos a, alumnocurso ac, clasificador c, programas_cursos pc, modulos m where aa.IdModulo=4000 and a.CiAlumno=aa.CiAlumno 
and ac.IdCurso=aa.IdCurso and ac.CiAlumno=aa.CiAlumno and ac.IdConceptoPago=c.IdClasificador and pc.IdCurso=pc.IdCurso and pc.IdCurso=aa.IdCurso and 
m.IdCurso=pc.IdCurso and m.IdModulo=aa.IdModulo order by nombreAlumno");//poner el codigo de curso a buscar
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
?>
  <div class="content">
    <div class="container-fluid">
          <div class="row">
              <div class="col-md-12">
                <div class="card">
                  <div class="card-header card-header-warning card-header-icon">
                    <div class="card-icon">
                      <i class="material-icons">polymer</i>
                    </div>
                    <h4 class="card-title"><b>Solicitud de Facturación para Capacitación</b></h4>
                    <h4 class="card-title text-center"><b>Empresas</b></h4>
                  </div>
                  <div class="row">
                    <?php
                    if(isset($_GET['q'])){?>
                      <input type="hidden" name="q" id="q" value="<?=$q?>">
                      <input type="hidden" name="r" id="r" value="<?=$r?>">
                    <?php }else{?>
                      <input type="hidden" name="q" id="q" value="0">
                      <input type="hidden" name="r" id="r" value="0">
                    <?php }
                    ?>
                        <div class="col-sm-12">
                          <div class="form-group" align="right">
                            <button type="button" class="btn btn-warning btn-round btn-fab btn-sm" data-toggle="modal" data-target="#modalBuscador">
                              <i class="material-icons" title="Buscador Avanzado">search</i>
                            </button>                                 
                          </div>
                        </div>
                    </div>
                  <div class="card-body">
                    <div id="contenedor_items_empresas">
                      <table class="table d-none" id="tablePaginator" >
                        <thead>
                          <tr>
                            <th class="text-center"></th>                          
                            <th>CI Alumno</th>
                            <th>Nombre</th>
                            <th>Precio <br>curso (BOB)</th>                            
                            <th>Desc. <br>curso(%)</th>                              
                            <th>Importe <br>curso(BOB)</th>   
                            <!-- <th>Importe <br>modulo(BOB)</th>   
                            <th>Importe <br>Solicitud(BOB)</th>    -->
                            <!-- <th>Canti. Mod</th> -->
                            <!-- <th>Nro <br>Módulo</th> -->
                            <th>Nombre Mod.</th>
                            <th class="text-right">Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                        <?php
                          $index=1;
                          $descuento_por = 0;
                          $descuento_bob = 0;
                          $cont= array();
                          while ($row = $stmtIBNO->fetch(PDO::FETCH_BOUND)) {  
                            $monto_pagar=($Costo - ($Costo*$descuento/100) )/$CantidadModulos; //monto a pagar del estudiante 
                            $importe_curso=   $Costo*$descuento/100;//importe curso con desuento
                            $importe_curso= $Costo-$importe_curso;//importe curso con desuento
                            // $nombre_area=trim(abrevArea($cod_area),'-');
                            // $nombre_uo=trim(abrevUnidad($cod_uo),' - ');                      
                            //buscamos a los estudiantes que ya fueron solicitados su facturacion
                            $codigo_facturacion=0;
                            $sqlFac="SELECT sf.codigo,sf.fecha_registro,sf.fecha_solicitudfactura,sf.razon_social,sf.nit from solicitudes_facturacion sf,solicitudes_facturaciondetalle sfd where sfd.cod_solicitudfacturacion=sf.codigo and sf.cod_simulacion_servicio=$codigo_simulacion and sf.cod_cliente=$CiAlumno";
                            // echo $sqlFac;
                            $stmtSimuFact = $dbh->prepare($sqlFac);
                            $stmtSimuFact->execute();
                            $resultSimuFact = $stmtSimuFact->fetch();
                            $codigo_facturacion = $resultSimuFact['codigo'];
                            $nit = $resultSimuFact['nit'];
                            $fecha_registro = $resultSimuFact['fecha_registro'];
                            $fecha_solicitudfactura = $resultSimuFact['fecha_solicitudfactura'];
                            $razon_social = $resultSimuFact['razon_social'];
                            //verificamos si ya tiene factura generada                            
                            $stmtFact = $dbh->prepare("SELECT codigo, nro_factura from facturas_venta where cod_solicitudfacturacion=$codigo_facturacion and cod_estadofactura=1");
                            $stmtFact->execute();
                            $resultSimu = $stmtFact->fetch();
                            $codigo_fact_x = $resultSimu['codigo'];
                            $nro_fact_x = $resultSimu['nro_factura'];
                            if ($nro_fact_x==null)$nro_fact_x="-";

                            $sumaTotalMonto=0;
                            $sumaTotalDescuento_por=0;
                            $sumaTotalDescuento_bob=0;                           
                            ?>
                          <tr>
                            <td align="center"></td>
                            <td><?=$CiAlumno;?></td>
                            <td><?=$nombreAlumno;?></td>
                            <td class="text-right"><?=formatNumberDec($Costo) ;?></td>
                            <td class="text-right"><?=$descuento ;?></td>                          
                            <td class="text-right"><?=formatNumberDec($importe_curso) ;?></td>                          
                            <!-- <td class="text-right"><?=formatNumberDec($monto_pagar) ;?></td>                            
                            <td class="text-right"><?=formatNumberDec($sumaTotalImporte) ;?></td>   -->   
                            <!-- <td><?=$NroModulo;?></td> -->
                            <td><?=$nombre_mod;?></td>
                            <td class="td-actions text-right">
                              <?php
                                if($globalAdmin==1){                            
                                  if($codigo_facturacion>0){
                                    if($codigo_fact_x==0){ //no se genero factura ?>
                                      <a title="Editar Solicitud de Facturación" href='<?=$urlregistro_solicitud_facturacion?>&codigo=<?=$CiAlumno?>&cod_simulacion=<?=$codigo_simulacion;?>&cod_facturacion=<?=$codigo_facturacion?>' class="btn btn-success">
                                          <i class="material-icons"><?=$iconEdit;?></i>
                                        </a>  

                                  <?php }else{//ya se genero factura ?>
                                    <a class="btn btn-success" href='<?=$urlGenerarFacturasPrint;?>?codigo=<?=$codigo_facturacion;?>&tipo=2' target="_blank"><i class="material-icons" title="Imprimir Factura">print</i></a>
                                  <?php }?>
                                  <a href='#' rel="tooltip" class="btn btn-warning" onclick="filaTablaAGeneral($('#tablasA_registradas'),<?=$index?>,'<?=$stringCabecera?>')">
                                    <i class="material-icons" title="Ver Detalle">settings_applications</i>
                                  </a>
                                  <a class="btn btn-danger" href='<?=$urlPrintSolicitud;?>?codigo=<?=$codigo_facturacion;?>' target="_blank"><i class="material-icons" title="Imprimir Solicitud">print</i></a> 
                                  <?php }else{//no se hizo solicitud de factura ?>
                                    <a href='<?=$urlregistro_solicitud_facturacion?>&codigo=<?=$CiAlumno?>&cod_simulacion=<?=$codigo_simulacion;?>&cod_facturacion=0' rel="tooltip" class="btn" style="background-color: #0489B1;">
                                          <i class="material-icons" title="Solicitar Facturación">receipt</i>
                                        </a>                                                    
                                  <?php }                                
                                }
                              ?>                                               
                            </td>
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
                <div class="card-footer fixed-bottom">              
                 <!-- <a href='<?=$urlList;?>' class="<?=$buttonCancel;?>"><i class="material-icons" title="Volver">keyboard_return</i> Volver </a> -->
                </div>      
              </div>
          </div>  
    </div>
  </div>


<!-- Modal busqueda de items-->
<div class="modal fade" id="modalBuscador" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Buscador de Empresas</h4>
      </div>
      <div class="row">
        <div class="form-group col-sm-12">
          <h5 style="color:#FF0000;" class="text-center">* Para Filtrar toda la lista, simplemente presione "buscar"</h5>  
        </div>
      </div>
      <div class="modal-body ">
        <div class="row">
          <label class="col-sm-6 col-form-label text-center">Empresa</label>          
          <label class="col-sm-6 col-form-label text-center">Nombre Curso</label>
        </div> 
        <div class="row">
            <div class="form-group col-sm-6">            
                <select name="cod_empresa[]" id="cod_empresa" class="selectpicker form-control form-control-sm" data-style="btn btn-info select-with-transition" data-show-subtext="true" data-live-search="true" data-actions-box="true" multiple>
                <option value="0"></option>
                <?php 
                $query1 = "SELECT codigo,nombre from clientes where cod_estadoreferencial=1 order by nombre";
                $statement = $dbh->query($query1);
                while ($row = $statement->fetch()){ ?>
                    <option value="<?=$row["codigo"];?>"><?=$row["nombre"];?> </option>
                <?php } ?>
                </select>
            
            </div>
            <div class="form-group col-sm-6">
                <input class="form-control input-sm" type="text" name="glosa" id="glosa"  >
            </div>                        
        </div> 
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="botonBuscarEmpresas" name="botonBuscarEmpresas" onclick="botonBuscarEmpresasCapacitacion()">Buscar</button>
        <!-- <button type="button" class="btn btn-danger" data-dismiss="modal"> Cerrar </button> -->
      </div>
    </div>
  </div>
</div>
<!-- small modal -->
<div class="modal fade modal-primary" id="modalDetalleFac" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content card">
                <div class="card-header card-header-warning card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">settings_applications</i>
                  </div>
                  <h4 class="card-title">Detalle Solicitud</h4>
                </div>
                <div class="card-body">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                  <i class="material-icons">close</i>
                </button>
                  <div class="row" id="div_cabecera" >
                    
                  </div>
                  
                  <table class="table table-condensed">
                    <thead>
                      <tr class="text-dark bg-plomo">
                      <th>#</th>
                      <th width="20%">Item</th>
                      <th>Canti.</th>
                      <!-- <th>Precio(BOB)</th>  
                      <th>Desc(%)</th> 
                      <th>Desc(BOB)</th>  -->
                      <th width="10%">Importe(BOB)</th> 
                      <th width="45%">Glosa</th>                    
                      </tr>
                    </thead>
                    <tbody id="tablasA_registradas">
                      
                    </tbody>
                  </table>
                </div>
      </div>  
    </div>
</div>
<!--    end small modal -->

<?php 
  $lan=sizeof($cont);
  error_reporting(0);
  for ($i=0; $i < $lan; $i++) {
    ?>
    <script>var detalle_fac=[];</script>
    <?php
       for ($j=0; $j < $cont[$i]; $j++) {     
           if($cont[$i]>0){
            ?><script>detalle_fac.push({codigo:<?=$datos[$i][$j]->codigo?>,cod_facturacion:<?=$datos[$i][$j]->cod_facturacion?>,serviciox:'<?=$datos[$i][$j]->serviciox?>',cantidadX:'<?=$datos[$i][$j]->cantidadX?>',precioX:'<?=$datos[$i][$j]->precioX?>',descuento_porX:'<?=$datos[$i][$j]->descuento_porX?>',descuento_bobX:'<?=$datos[$i][$j]->descuento_bobX?>',descripcion_alternaX:'<?=$datos[$i][$j]->descripcion_alternaX?>'});</script><?php         
            }          
          }
      ?><script>detalle_tabla_general.push(detalle_fac);</script><?php                    
  }
  ?>
<script type="text/javascript">  
  $('#modalBuscador').modal('show');
</script>
  