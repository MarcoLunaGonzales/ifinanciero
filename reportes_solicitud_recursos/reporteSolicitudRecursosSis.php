<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';

require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';
require_once  __DIR__.'/../fpdf_html.php';
require_once '../layouts/bodylogin2.php';
require_once 'configModule.php';
// fin de editar Facturas
$dbh = new Conexion();


//RECIBIMOS LAS VARIABLES
$gestion = $_POST["gestiones"];
$cod_mes_x = $_POST["cod_mes_x"];
$stringMesX=implode(",", $cod_mes_x);

$estado=$_POST["estado"];
$stringEstadoX=implode(",", $estado);

$nombre_gestion=nameGestion($gestion);
$nombre_mes=nombreMes($cod_mes_x[0]);
if(count($cod_mes_x)>1){
  $nombre_mes=nombreMes($cod_mes_x[0])."-".nombreMes($cod_mes_x[count($cod_mes_x)-1]);
}

//datos Reporte
$gestionPost=$gestion;
$cod_mes_xPost=$stringMesX;
$estadoPost=$stringEstadoX;


// echo $areaString;
$sql="SELECT l.* FROM (SELECT sr.*,es.nombre as estado,u.abreviatura as unidad,a.abreviatura as area,(select count(*) from solicitud_recursosdetalle where cod_solicitudrecurso=sr.codigo and (cod_unidadorganizacional=3000 or cod_area=1235)) as sis_detalle 
  from solicitud_recursos sr join estados_solicitudrecursos es on sr.cod_estadosolicitudrecurso=es.codigo join unidades_organizacionales u on sr.cod_unidadorganizacional=u.codigo join areas a on sr.cod_area=a.codigo 
  where sr.cod_estadoreferencial=1 and sr.cod_estadosolicitudrecurso in ($stringEstadoX) order by sr.numero desc) l  
where (l.cod_unidadorganizacional=3000 or l.cod_area=1235 or l.sis_detalle>0) and MONTH(l.fecha) in ($stringMesX) and YEAR(l.fecha)=$nombre_gestion ORDER BY l.fecha,l.numero,l.cod_personal asc";
//echo $sql;
$stmt2 = $dbh->prepare($sql);
// echo $sql;
// Ejecutamos                        
$stmt2->execute();
//resultado
$stmt2->bindColumn('codigo', $codigo);
$stmt2->bindColumn('unidad', $unidad);
$stmt2->bindColumn('area', $area);
$stmt2->bindColumn('fecha', $fecha);
$stmt2->bindColumn('cod_personal', $codPersonal);
$stmt2->bindColumn('cod_simulacion', $codSimulacion);
$stmt2->bindColumn('cod_proveedor', $codProveedor);
$stmt2->bindColumn('cod_estadosolicitudrecurso', $codEstado);
$stmt2->bindColumn('estado', $estado_sol);
$stmt2->bindColumn('cod_comprobante', $codComprobante);
$stmt2->bindColumn('cod_simulacionservicio', $codSimulacionServicio);
$stmt2->bindColumn('numero', $numeroSol);
$stmt2->bindColumn('idServicio', $idServicioX);
$stmt2->bindColumn('glosa_estado', $glosa_estadoX);
//datos de la factura
$stmtPersonal = $dbh->prepare("SELECT * from titulos_oficinas where cod_uo in (5)");
$stmtPersonal->execute();
$result=$stmtPersonal->fetch();
$sucursal=$result['sucursal'];
$direccion=$result['direccion'];
$nit=$result['nit'];
$razon_social=$result['razon_social'];

?>
 <script> 
          gestion_reporte='<?=$nombre_gestion;?>';
          mes_reporte='<?=$nombre_mes;?>';
 </script>


<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="card-icon bg-blanco">
                    <img class="" width="60" height="60" src="../assets/img/logo_ibnorca_origen.png">
                  </div>                  
                  <h3 class="card-title text-center" ><b>Control Solicitudes de Recursos - Proyecto SIS</b>
                    <span><br><h6>
                    Del Período: <?=$nombre_mes;?>/<?=$nombre_gestion;?><br>
                    Expresado En Bolivianos</h6></span></h3>                  
                  <!-- <h6 class="card-title">Unidad: <?=$stringUnidades;?></h6> -->
                </div>
                <div class="card-body">
                  <?php
                  if(isset($_POST['codigo_factura'])){
                     ?><div class="" style="border-radius:7px; border:2px solid <?=$txtEstilo?>;text-align:center; background:<?=$bgEstilo?>;">
                           <label class="font-weight-bold" style="color:<?=$txtEstilo?>;"><?=$tituloSuccess?></label>
                       </div>
                       <br> 
                       <?php
                   }
                  ?>
                  <div class="">
                    <table class="table table-bordered table-condensed" style="width:100%">
                      <thead>
                        <tr style="border:1px solid;">
                                  <th colspan="8" class="text-left csp"><small> <br>Sucursal : <?=$sucursal?></small></th>   

                                  <th colspan="7" class="text-left csp"><small> Nit : <?=$nit?><br>Dirección : <?=$direccion?></small></th>   
                              </tr>
                      </thead>
                    </table>

                        <table id="tablePaginatorReport" class="table table-bordered table-condensed" style="width:100%">
                            <thead> 
                              <tr>
                                  <th width="2%" style="border:2px solid;"><small><small><b>-</b></small></small></th>   
                                  <th style="border:2px solid;" width="6%"><small><small><b>Estado</b></small></small></th>
                                  <th style="border:2px solid;" width="3%"><small><small><b>Numero</b></small></small></th>
                                  <th style="border:2px solid;" width="6%"><small><small><b>Fecha</b></small></small></th>
                                  <th style="border:2px solid;" width="6%"><small><small><b>Of. - Area</b></small></small></th>
                                  <th style="border:2px solid;" width="4%"><small><small><b>Cod. Servicio </b></small></small></th>
                                  <th style="border:2px solid;" width="10%"><small><small><b>Proveedor</b></small></small></th>
                                  <th style="border:2px solid;" width="16%"><small><small><b>Cuenta</b></small></small></th>
                                  <th style="border:2px solid;" width="16%"><small><small><b>Solicitante</b></small></small></th>                          
                                  <th style="border:2px solid;" width="16%"><small><small><b>Observaciones</b></small></small></th>
                                  <th style="border:2px solid;" width="6%"><small><small><b>Total Solicitado</b></small></small></th>
                                  <th style="border:2px solid;" width="6%"><small><small><b>Opciones</b></small></small></th>
                              </tr>                                  
                            </thead>
                            <tbody>
                              <?php
                              $index=0; 
                              $total_importe=0;
                              while ($row = $stmt2->fetch()) { 
                                $index++;
                                
                                
                                $titulo_estado="";
                                switch ($codEstado) {
                                  case 1:
                                    $titulo_estado="text-muted";
                                  break;
                                  case 2:
                                    $titulo_estado="bg-danger text-white";
                                  break;
                                  case 3:
                                    $titulo_estado="bg-success text-white";
                                  break;
                                  case 4:
                                    $titulo_estado="bg-warning text-white";
                                  break;
                                  case 5:
                                    $titulo_estado="bg-principal text-white";
                                    $estado_sol.=" / ".nombreComprobante($codComprobante);
                                  break;
                                  case 6:
                                    $titulo_estado="bg-plomo";
                                  break;
                                  case 7:
                                    $titulo_estado="bg-info text-white";
                                   break;
                                  case 8:
                                    $titulo_estado="text-success";
                                   break; 
                                }
                                
                                 
                                $solicitante=namePersonal($codPersonal);
                                $codigoServicio="-";
                                $sql="SELECT codigo FROM ibnorca.servicios where idServicio=$idServicioX";
                                $stmt1=$dbh->prepare($sql);
                                $stmt1->execute();
                                while ($row1 = $stmt1->fetch(PDO::FETCH_ASSOC)) {
                                   $codigoServicio=$row1['codigo'];
                                }
                                  
                                $nombreProveedor=obtenerNombreConcatenadoProveedorDetalleSolicitudRecurso($codigo);
                                $glosa_estadoX = preg_replace("[\n|\r|\n\r]", ", ", $glosa_estadoX);
                                $glosaArray=explode("####", $glosa_estadoX);
                                $glosa_estadoX = str_replace("####", " - ", $glosa_estadoX);

                                $montoImporte=obtenerSumaDetalleSolicitud($codigo);
                                $total_importe+=$montoImporte

                                ?>
                                <tr>
                                  <td class="text-center small"><?=$index;?></td>
                                  <td class="text-center small <?=$titulo_estado?>"><?=$estado_sol;?></td>
                                  <td class="text-center small"><?=$numeroSol;?></td>
                                  <td class="text-center small"><?=strftime('%d/%m/%Y',strtotime($fecha));?></td>
                                  <td class="text-center small"><?=$unidad;?>- <?=$area;?></td>
                                  <td class="text-left small"><?=strtoupper($codigoServicio);?></td>
                                  <td class="text-right small"><?=$nombreProveedor;?></td>
                                  <td class="text-right small"><?=obtenerNombreConcatenadoCuentaDetalleSolicitudRecurso($codigo)?></td>
                                  <td class="text-left small"><img src="../assets/img/faces/persona1.png" width="20" height="20"/><?=$solicitante;?></td>
                                  <td class="text-right small"><b>
                                       <?php if(isset($glosaArray[1])){
                                            echo "".$glosaArray[0].""."<u class='text-muted'> ".$glosaArray[1]."</u>";
                                        }else{
                                            echo $glosa_estadoX;
                                        }?></b>
                                  </td>
                                  <td class="text-right small"><?=formatNumberDec($montoImporte);?></td>
                                  <td class="text-right small">
                                    <div class="btn-group">
                                      <!--<a href="#" 
                                    onclick="editarFacturaModalReporte('<?=$cod_factura?>','<?=$nit?>','<?=$nro_factura?>',
                                    '<?=$nro_autorizacion?>','<?=$codigo_control?>','<?=$importe?>','<?=$exento?>',
                                    '<?=$ice?>','<?=$tasa_cero?>','<?=$fechaFac?>','<?=trim($razon_social)?>','<?=$tipo_compra?>')" 
                                    class="btn btn-fab btn-success btn-sm"><i class="material-icons">edit</i></a>-->
                                      <a title=" Ver Solicitud de Recursos" target="_blank" href="../<?=$urlVer;?>?cod=<?=$codigo;?>&comp=2" class="btn btn-warning btn-fab btn-sm">
                                          <i class="material-icons">preview</i>
                                    </a>
                                 
                                    </div>
                                  </td>                                      
                                </tr>
                                <?php                                  
                              }?>
                              <tr style="border:2px solid;">                               
                                  <td class="text-left small csp" colspan="5" style="border:2px solid;">CI:</td>
                                  <td class="text-left small csp" colspan="3" style="border:2px solid;">Nombre del Responsable:</td>
                                  <td class="text-center small"><b>SubTotal:</b></td> 
                                  <td></td>                                 
                                  <td class="text-right small"><?=formatNumberDec($total_importe);?></td>
                                  <td></td>                                      
                                </tr>
                            </tbody>
                        </table>

                    

                  </div>
                </div>
              </div>
            </div>
          </div>  
        </div>
    </div>
<?php 
$fechaMinima="2020-01-01";
$fechaActual=date("Y-m-d");
?>
<!-- small modal -->
<div class="modal fade modal-primary" id="editarFactura" style="z-index: 100000 !important;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content card">
               <div class="card-header card-header-primary card-header-text">
                  <div class="card-text">
                    <h4>DATOS FACTURA <label id="titulo_modal_factura" class="text-white"></label></h4>      
                  </div>
                  <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
                    <i class="material-icons">close</i>
                  </button>
                </div>
                <div class="card-body">
                  <form action="../<?=$urlReporteComprasProy?>" method="post">
                    <input type="hidden" class="form-control" name="codigo_factura" id="codigo_factura" value="-1">
                    <div class="d-none">   
                          <?php
                  $sqlUO="SELECT uo.codigo, uo.nombre from estados_solicitudrecursos uo where uo.codigo in ($estadoPost) order by 2";
                  $stmt = $dbh->prepare($sqlUO);
                  $stmt->execute();
                  ?>
                    <select class="selectpicker form-control form-control-sm" name="estado[]" id="estado" multiple>
                        <?php 
                          while ($row = $stmt->fetch()){ 
                      ?>
                             <option value="<?=$row["codigo"];?>" selected><?=$row["nombre"];?></option>
                        <?php 
                        } 
                       ?>
                         </select>
                         <select name="gestiones" id="gestiones" class="selectpicker form-control form-control-sm">
                                    <option value=""></option>
                                    <?php 
                                    $query = "SELECT codigo,nombre from gestiones where codigo in ($gestionPost) ORDER BY nombre desc";
                                    $stmt = $dbh->query($query);
                                    while ($row = $stmt->fetch()){ ?>
                                        <option value="<?=$row["codigo"];?>" selected><?=$row["nombre"];?></option>
                                    <?php } ?>
                                </select>
                         <?php $sql="SELECT c.cod_mes,(select m.nombre from meses m where m.codigo=c.cod_mes) as nombre_mes from meses_trabajo c where c.codigo in ($cod_mes_xPost)";
                         $stmtg = $dbh->prepare($sql);
                         $stmtg->execute();
                         ?>
                         <select name="cod_mes_x[]" id="cod_mes_x" class="selectpicker form-control form-control-sm" multiple>
                         <?php
                    
                         while ($rowg = $stmtg->fetch(PDO::FETCH_ASSOC)) {    
                           $cod_mes=$rowg['cod_mes'];    
                           $nombre_mes=$rowg['nombre_mes'];    
                         ?>
                             <option value="<?=$cod_mes;?>" selected><?=$nombre_mes;?></option>
                            <?php 
                            }
                          ?>
                          </select>       
                        </div>
                    
                     <div style="padding: 20px;">
                          <div class="row">                      
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">NIT</label>
                            <div class="col-sm-3">
                              <div class="form-group">  
                                <input class="form-control" type="text" name="nit_fac" id="nit_fac" required="true">                               
                              </div>
                            </div>
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Nro. Factura</label>
                            <div class="col-sm-3">
                              <div class="form-group">
                                 <input class="form-control" type="number" name="nro_fac" id="nro_fac" required="true"/>
                              </div>
                            </div>
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Fecha</label>
                            <div class="col-sm-3">
                              <div class="form-group">
                                <input type="date" class="form-control" name="fecha_fac" id="fecha_fac" value="" min="<?=$fechaMinima?>" max="<?=$fechaActual?>" required="true">
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Importe</label>
                            <div class="col-sm-3">
                              <div class="form-group">
                                <input class="form-control" type="number" readonly step="0.01" name="imp_fac" id="imp_fac"/>
                              </div>
                            </div>
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Exento</label>
                            <div class="col-sm-3">
                              <div class="form-group">
                                <input class="form-control" type="number" readonly step="0.01" name="exe_fac" id="exe_fac"value="0" />
                              </div>
                            </div>
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">ICE</label>
                            <div class="col-sm-3">
                              <div class="form-group">
                                <input class="form-control" type="number" readonly step="0.01" name="ice_fac" id="ice_fac" value="0" />
                              </div>
                             </div>
                          </div>                                                                  
                          <!--No tiene funcion este campo-->
                          <div class="row">                                            
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Tasa Cero</label>
                            <div class="col-sm-3">
                              <div class="form-group">
                                <!-- <label for="taza_fac" class="bmd-label-floating" style="color: #4a148c;">Taza Cero</label>      -->
                                <input class="form-control" type="number" readonly step="0.01" name="taza_fac" id="taza_fac" value="0" />
                              </div>
                            </div>
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Autorizaci&oacute;n</label>
                            <div class="col-sm-3">
                              <div class="form-group">
                                <input class="form-control" type="text" name="aut_fac" id="aut_fac" required="true"/>
                              </div>
                            </div>
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Cod. Control</label>
                            <div class="col-sm-3">
                              <div class="form-group">
                                <input class="form-control" type="text" name="con_fac" id="con_fac" required="true"/>
                              </div>
                             </div>
                          </div> 
                          <div class="row">
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Tipo</label>
                            <div class="col-sm-2">
                              <div class="form-group">
                                <select class="selectpicker form-control form-control-sm" required name="tipo_fac" id="tipo_fac" data-style="btn btn-primary">                                  
                                   <?php
                                       $stmt = $dbh->prepare("SELECT codigo, nombre FROM tipos_compra_facturas where cod_estadoreferencial=1");
                                       $stmt->execute();
                                      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        $codigoX=$row['codigo'];
                                        $nombreX=$row['nombre'];
                                        ?><option value="<?=$codigoX;?>"><?=$nombreX;?></option><?php
                                         }
                                     ?>
                                </select>
                              </div>
                            </div>                        
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Razón Social</label>
                            <div class="col-sm-8">
                              <div class="form-group">                                
                                <input type="text" class="form-control" name="razon_fac" id="razon_fac" required="true">
                              </div>
                            </div>   
                        </div>
                        
                      </div>           
                       
                      <hr>
                      <div class="form-group float-right">
                        <button type="submit"  class="btn btn-default">Guardar</button>
                      </div> 
                     </form>   
                  
                </div>
      </div>  
    </div>
  </div>
<!--    end small modal -->
