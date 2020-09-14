<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';

require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';
require_once  __DIR__.'/../fpdf_html.php';
require_once '../layouts/bodylogin2.php';
require_once 'configModule.php';
// fin de editar Facturas
$dbh = new Conexion();


//editar las facturas
if(isset($_POST['codigo_factura'])){
    $codigo=$_POST['codigo_factura'];
    $nit=$_POST['nit_fac'];
    $nroFac=$_POST['nro_fac'];
      
    $fechaFac=$_POST['fecha_fac'];
    $razonFac=$_POST['razon_fac'];
    $impFac=$_POST['imp_fac'];            
    $autFac=$_POST['aut_fac'];
    $conFac=$_POST['con_fac'];
            
    $exeFac=$_POST['exe_fac'];
    $tipoFac=$_POST['tipo_fac'];
    $tazaFac=$_POST['taza_fac'];
    $iceFac=$_POST['ice_fac'];
    
    $sqlDetalle="UPDATE facturas_compra SET nit='$nit', nro_factura='$nroFac', fecha='$fechaFac', 
    razon_social='$razonFac', importe='$impFac', exento='$exeFac', nro_autorizacion='$autFac', codigo_control='$conFac',
    ice='$iceFac',tasa_cero='$tazaFac',tipo_compra='$tipoFac' WHERE codigo=$codigo";
    $stmtDetalle = $dbh->prepare($sqlDetalle);
    $flagSuccessDetalle=$stmtDetalle->execute();
    //$flagSuccessDetalle=false; 
    if($flagSuccessDetalle==true){
      $tituloSuccess="Se Modificó la factura exitosamente!";
      $txtEstilo="#194519";
      $bgEstilo="#C2E7C8";
    }else{
      $tituloSuccess="Ocurrio un error al editar la factura";
      $txtEstilo="#8F1707";
      $bgEstilo="#E7C3C2";
    }
  }

/*if (isset($_POST["check_sin_sr"])) {
  $check_sin_sr=$_POST["check_sin_sr"]; 
  if($check_sin_sr){
    $razon_social=$_POST["unidad"]; 
    $sql_rs=" and f.unidad like '%$razon_social%'";
  }else{
    $sql_rs="";
  }
}else{
  $sql_rs="";
}*/


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
$sql="(SELECT 0 as proyecto_si,s.codigo as codigo_solicitud,s.cod_comprobante,f.codigo as cod_factura,s.cod_estadosolicitudrecurso as cod_estado_sol,s.numero as numero_sol,e.nombre as estado_sol,f.fecha,DATE_FORMAT(f.fecha,'%d/%m/%Y')as fecha_x,f.nit,f.razon_social,f.nro_factura,f.nro_autorizacion,f.codigo_control,f.importe,f.ice,f.exento,f.tasa_cero,f.tipo_compra 
from facturas_compra f 
join solicitud_recursosdetalle sd on sd.codigo=f.cod_solicitudrecursodetalle
join solicitud_recursos s on s.codigo=sd.cod_solicitudrecurso
join estados_solicitudrecursos e on e.codigo =s.cod_estadosolicitudrecurso
where s.cod_estadosolicitudrecurso in ($stringEstadoX) and s.cod_estadoreferencial<>2 and (sd.cod_area=1235 or sd.cod_unidadorganizacional=3000) and MONTH(f.fecha) in ($stringMesX) and YEAR(f.fecha)=$nombre_gestion ORDER BY f.fecha,f.nit,f.nro_factura asc
)
UNION (SELECT ll.* from (
  SELECT 1 as proyecto_si, (SELECT codigo from solicitud_recursos where cod_comprobante=cc.codigo) as codigo_solicitud,cc.codigo as cod_comprobante,f.codigo as cod_factura,-1 as cod_estado_sol, '' as numero_sol,' ' as estado_sol,f.fecha,DATE_FORMAT(f.fecha,'%d/%m/%Y')as fecha_x,f.nit,f.razon_social,f.nro_factura,f.nro_autorizacion,f.codigo_control,f.importe,f.ice,f.exento,f.tasa_cero,f.tipo_compra 
  FROM facturas_compra f, comprobantes_detalle c, comprobantes cc 
  WHERE cc.codigo=c.cod_comprobante and f.cod_comprobantedetalle=c.codigo and cc.cod_estadocomprobante<>2 and MONTH(cc.fecha) in ($stringMesX) and YEAR(cc.fecha)=$nombre_gestion 
  ORDER BY f.fecha,f.nit,f.nro_factura asc) ll
)";
//(SELECT codigo from solicitud_recursos where cod_comprobante=cc.codigo) as
//and cc.cod_unidadorganizacional in ($stringUnidadesX) 
//echo $sql;
$stmt2 = $dbh->prepare($sql);
// echo $sql;
// Ejecutamos                        
$stmt2->execute();
//resultado
$stmt2->bindColumn('fecha', $fechaFac);
$stmt2->bindColumn('cod_factura', $cod_factura);
$stmt2->bindColumn('fecha_x', $fecha);
$stmt2->bindColumn('nit', $nit);
$stmt2->bindColumn('razon_social', $razon_social);
$stmt2->bindColumn('nro_factura', $nro_factura);
$stmt2->bindColumn('nro_autorizacion', $nro_autorizacion);
$stmt2->bindColumn('codigo_control', $codigo_control);
$stmt2->bindColumn('importe', $importe);
$stmt2->bindColumn('ice', $ice);
$stmt2->bindColumn('exento', $exento);          
$stmt2->bindColumn('tasa_cero', $tasa_cero);          
$stmt2->bindColumn('tipo_compra', $tipo_compra); 
$stmt2->bindColumn('estado_sol', $estado_sol); 
$stmt2->bindColumn('cod_estado_sol', $cod_estado_sol); 
$stmt2->bindColumn('numero_sol', $numero_sol);  
$stmt2->bindColumn('cod_comprobante', $cod_comprobante);  
$stmt2->bindColumn('codigo_solicitud', $codSolicitud); 
$stmt2->bindColumn('proyecto_si', $proyecto_si); 
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
                  <h3 class="card-title text-center" ><b>Libro de Compras - Edición</b>
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
                                  <th colspan="8" class="text-left csp"><small> Razón Social : <?=$razon_social?><br>Sucursal : <?=$sucursal?></small></th>   

                                  <th colspan="7" class="text-left csp"><small> Nit : <?=$nit?><br>Dirección : <?=$direccion?></small></th>   
                              </tr>
                      </thead>
                    </table>

                        <table id="tablePaginatorReport" class="table table-bordered table-condensed" style="width:100%">
                            <thead> 
                              <tr>
                                  <th width="2%" style="border:2px solid;"><small><small><b>-</b></small></small></th>   
                                  <th style="border:2px solid;" width="6%"><small><small><b>Estado (Sol)</b></small></small></th>
                                  <th style="border:2px solid;" width="6%"><small><small><b>Numero (Sol)</b></small></small></th>
                                  <th style="border:2px solid;" width="6%"><small><small><b>Fecha</b></small></small></th>
                                  <th style="border:2px solid;" width="6%"><small><small><b>NIT</b></small></small></th>
                                  <th style="border:2px solid;"><small><small><b>Razón Social </b></small></small></th>
                                  <th style="border:2px solid;" width="6%"><small><small><b>Nro. de<br> FACTURA</b></small></small></th>
                                  <th style="border:2px solid;" width="10%"><small><small><b>Nro de Autorización</b></small></small></th>
                                  <th style="border:2px solid;" width="10%"><small><small><b>Código de Control</b></small></small></th>                          
                                  <th style="border:2px solid;" width="6%"><small><small><small><b>Total Factura (A)</b></small></small></small></th>
                                  <th style="border:2px solid;" width="6%"><small><small><small><b>Total I.C.E (B)</b></small></small></small></th>
                                  <th style="border:2px solid;" width="6%"><small><small><small><b>Importes Exentos (C)</b></small></small></small></th>
                                  <th style="border:2px solid;" width="6%"><small><small><small><b>Importe Neto Sujeto a IVA <br>(A-B-C)</b></small></small></small></th>
                                  <th style="border:2px solid;" width="6%"><small><small><small><b>Crédito Fiscal Obtenido</b></small></small></small></th>
                                  <th style="border:2px solid;" width="6%"><small><small><small><b>Editar</b></small></small></small></th>
                              </tr>                                  
                            </thead>
                            <tbody>
                              <?php
                              $index=0; 
                              $total_importe=0;
                              $total_ice=0;
                              $total_exento=0;
                              $total_importe_sujeto_iva=0;
                              $total_iva_obtenido=0;
                              while ($row = $stmt2->fetch()) { 
                                $index++;
                                // $importe_sujeto_iva=$importe-$ice-$exento;
                                $importe_sujeto_iva=$importe-$ice-$exento;
                                $iva_obtenido=$importe_sujeto_iva*13/100;
                                $caracter=substr($codigo_control, -1);
                                if($caracter=='-'){
                                  $codigo_control=trim($codigo_control, '-');
                                }
                                
                                $titulo_estado="";
                                switch ($cod_estado_sol) {
                                  
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
                                    $titulo_estado="bg-primary text-white";
                                    $estado_sol.=" / ".nombreComprobante($cod_comprobante)." ".abrevUnidad_solo(obtenerCodigoUnidadComprobante($cod_comprobante));
                                  break;
                                  case 6:
                                    $titulo_estado="bg-plomo text-white";
                                  break;
                                  case 7:
                                    $titulo_estado="bg-info text-white";
                                   break;
                                  case 8:
                                    $titulo_estado="text-success";
                                    $estado_sol.=" / ".nombreComprobante($cod_comprobante)." ".abrevUnidad_solo(obtenerCodigoUnidadComprobante($cod_comprobante));
                                   break; 
                                }
                                
                                if($proyecto_si==1){
                                    $titulo_estado="bg-danger text-white";
                                    $estado_sol.=" / ".nombreComprobante($cod_comprobante)." ".abrevUnidad_solo(obtenerCodigoUnidadComprobante($cod_comprobante));
                                } 
                                $total_importe+=$importe;
                                $total_ice+=$ice;
                                $total_exento+=$exento;
                                $total_importe_sujeto_iva+=$importe_sujeto_iva;
                                $total_iva_obtenido+=$iva_obtenido;

                                // $sumadeimporte=$importe+$ice+$exento;
                                $sumadeimporte=$importe;
                                if(trim($codigo_control)==""){
                                  $codigo_control="0";
                                }
                                ?>
                                <tr>
                                  <td class="text-center small"><?=$index;?></td>
                                  <td class="text-center small <?=$titulo_estado?>"><?=$estado_sol;?></td>
                                  <td class="text-center small"><?=$numero_sol;?></td>
                                  <td class="text-center small"><?=$fecha;?></td>
                                  <td class="text-right small"><?=$nit;?></td>
                                  <td class="text-left small"><?=strtoupper($razon_social);?></td>
                                  <td class="text-right small"><?=$nro_factura;?></td>
                                  <td class="text-right small"><?=$nro_autorizacion;?></td>
                                  <td class="text-center small"><?=$codigo_control;?></td>
                                  <td class="text-right small"><?=formatNumberDec($sumadeimporte);?></td>
                                  <td class="text-right small"><?=formatNumberDec($ice);?></td>
                                  <td class="text-right small"><?=formatNumberDec($exento);?></td>
                                  <td class="text-right small"><?=formatNumberDec($importe_sujeto_iva);?></td>
                                  <td class="text-right small"><?=formatNumberDec($iva_obtenido);?></td> 
                                  <td class="text-right small">
                                    <div class="btn-group">
                                      <a href="#" 
                                    onclick="editarFacturaModalReporte('<?=$cod_factura?>','<?=$nit?>','<?=$nro_factura?>',
                                    '<?=$nro_autorizacion?>','<?=$codigo_control?>','<?=$importe?>','<?=$exento?>',
                                    '<?=$ice?>','<?=$tasa_cero?>','<?=$fechaFac?>','<?=trim($razon_social)?>','<?=$tipo_compra?>')" 
                                    class="btn btn-fab btn-success btn-sm"><i class="material-icons">edit</i></a>
                                    <?php 
                                    if($codSolicitud!="" or $codSolicitud!=null){
                                      ?>
                                     <a title=" Ver Solicitud de Recursos" target="_blank" href="../<?=$urlVer;?>?cod=<?=$codSolicitud;?>&comp=2" class="btn btn-warning btn-fab btn-sm">
                                          <i class="material-icons">preview</i>
                                    </a>
                                      <?php
                                    }
                                    ?>
                                      
                                 
                                    </div>
                                  </td>                                      
                                </tr>
                                <?php                                  
                              }
                              //facturas sin solicitud de recursos relacionados
                               


                              ?>
                              <tr style="border:2px solid;">                               
                                  <td class="text-left small csp" colspan="5" style="border:2px solid;">CI:</td>
                                  <td class="text-left small csp" colspan="3" style="border:2px solid;">Nombre del Responsable:</td>
                                  <td class="text-center small"><b>SubTotal:</b></td>                                  
                                  <td class="text-right small"><?=formatNumberDec($total_importe);?></td>
                                  <td class="text-right small"><?=formatNumberDec($total_ice);?></td>
                                  <td class="text-right small"><?=formatNumberDec($total_exento);?></td>
                                  <td class="text-right small"><?=formatNumberDec($total_importe_sujeto_iva);?></td>
                                  <td class="text-right small"><?=formatNumberDec($total_iva_obtenido);?></td>
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
