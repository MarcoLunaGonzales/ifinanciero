<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';

require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';
require_once  __DIR__.'/../fpdf_html.php';
require_once '../layouts/bodylogin2.php';
$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$gestion = $_POST["gestiones"];
//$cod_mes_x = $_POST["cod_mes_x"];

$unidad=$_POST["unidad"];
$stringUnidadesX=implode(",", $unidad);

$nombre_gestion=nameGestion($gestion);
//$nombre_mes=nombreMes($cod_mes_x);

$desdeInicioAnio="";
if($_POST["fecha_desde"]==""){
  $y=$globalNombreGestion;
  $desde=$y."-01-01";
  $hasta=$y."-12-31";
  $desdeInicioAnio=$y."-01-01";
}else{
  $porcionesFechaDesde = explode("-", $_POST["fecha_desde"]);
  $porcionesFechaHasta = explode("-", $_POST["fecha_hasta"]);

  $desdeInicioAnio=$porcionesFechaDesde[0]."-01-01";
  $desde=$porcionesFechaDesde[0]."-".$porcionesFechaDesde[1]."-".$porcionesFechaDesde[2];
  $hasta=$porcionesFechaHasta[0]."-".$porcionesFechaHasta[1]."-".$porcionesFechaHasta[2];
}



if (isset($_POST["check_rs_librocompras"])) {
  $check_rs_librocompras=$_POST["check_rs_librocompras"]; 
  if($check_rs_librocompras){
    $razon_social=$_POST["razon_social"]; 
    $razon_social=trim($razon_social);
    $sql_rs=" and f.razon_social like '%$razon_social%'";
  }else{
    $sql_rs="";
  }
}else{
  $sql_rs="";
}

// echo $areaString;
$sql="SELECT f.fecha,DATE_FORMAT(f.fecha,'%d/%m/%Y')as fecha_x,f.nit,f.razon_social,f.nro_factura,f.nro_autorizacion,f.codigo_control,f.importe,f.ice,f.exento,f.tipo_compra,cc.codigo as cod_comprobante
  FROM facturas_compra f, comprobantes_detalle c, comprobantes cc 
  WHERE cc.codigo=c.cod_comprobante and f.cod_comprobantedetalle=c.codigo and cc.cod_estadocomprobante<>2 and cc.cod_unidadorganizacional in ($stringUnidadesX) and cc.fecha BETWEEN '$desde 00:00:00' and '$hasta 23:59:59' $sql_rs ORDER BY f.fecha asc, f.nit, f.nro_factura"; //and MONTH(cc.fecha)=$cod_mes_x and YEAR(cc.fecha)=$nombre_gestion

//echo $sql;

$stmt2 = $dbh->prepare($sql);
// echo $sql;
// Ejecutamos                        
$stmt2->execute();
//resultado
$stmt2->bindColumn('fecha_x', $fecha);
$stmt2->bindColumn('nit', $nit);
$stmt2->bindColumn('cod_comprobante', $codComprobante);
$stmt2->bindColumn('razon_social', $razon_social);
$stmt2->bindColumn('nro_factura', $nro_factura);
$stmt2->bindColumn('nro_autorizacion', $nro_autorizacion);
$stmt2->bindColumn('codigo_control', $codigo_control);
$stmt2->bindColumn('importe', $importe);
$stmt2->bindColumn('ice', $ice);
$stmt2->bindColumn('exento', $exento);          
$stmt2->bindColumn('tipo_compra', $tipo_compra);  

$cant_unidad=sizeof($unidad);

if($cant_unidad>1){
  $cod_unidad_x=5;
}else{  
  
  if($stringUnidadesX==9 || $stringUnidadesX==10 ){
    $cod_unidad_x=$stringUnidadesX;
  }else{    
    $cod_unidad_x=5;
  }
}

//datos de la factura
$stmtPersonal = $dbh->prepare("SELECT * from titulos_oficinas where cod_uo in ($cod_unidad_x)");
$stmtPersonal->execute();
$result=$stmtPersonal->fetch();
$sucursal=$result['sucursal'];
$direccion=$result['direccion'];
$nit=$result['nit'];
$razon_social=$result['razon_social'];

$periodoTitle=" Del ".strftime('%d/%m/%Y',strtotime($desde))." al ".strftime('%d/%m/%Y',strtotime($hasta));

?>
 <script> 
          periodo='<?=$periodoTitle;?>';
          
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
                  <h3 class="card-title text-center" ><b>Libro de Compras IVA</b>
                    <span><br><h6>
                    Periodo: <?=$periodoTitle;?><br>
                    Expresado En Bolivianos</h6></span></h3>                                    
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                     <table id="" class="table table-bordered table-condensed" style="width:100%"><!--libro_mayor_rep-->
                        <thead>  
                          <tr>
                            <th colspan="9" class="text-left csp"><small> Razón Social : <?=$razon_social?><br>Sucursal : <?=$sucursal?></small></th>   
                            <th colspan="8" class="text-left csp"><small> Nit : <?=$nit?><br>Dirección : <?=$direccion?></small></th>   
                          </tr>                                                        
                        </thead>
                      </table>
                        <table id="libro_compras_rep" class="table table-bordered table-condensed" style="width:100%">
                            <thead>
                              <tr >
                                  <th width="2%" style="border:2px solid;"><small><small><b>-</b></small></small></th>   
                                  <th style="border:2px solid;" width="6%"><small><small><small><b>Fecha</b></small></small></small></th>                                
                                  <th style="border:2px solid;" width="6%"><small><small><small><b>NIT</b></small></small></small></th>
                                  <th style="border:2px solid;" width="20%"><small><small><small><b>Razón Social </b></small></small></small></th>
                                  <th style="border:2px solid;" width="6%"><small><small><small><b>Nro. Factura</b></small></small></small></th>
                                  <th style="border:2px solid;" width="6%"><small><small><small><b>Nro de Autorización</b></small></small></small></th>
                                  <th style="border:2px solid;" width="6%"><small><small><small><b>Código de Control</b></small></small></small></th>                                 
                                  <th style="border:2px solid;" width="6%"><small><small><small><b>Total Factura (A)</b></small></small></small></th>
                                  <th style="border:2px solid;" width="3%"><small><small><small><b>Total I.C.E (B)</b></small></small></small></th>
                                  <th style="border:2px solid;" width="3%"><small><small><small><small><b>Importes Exentos (C)</b></small></small></small></small></th>
                                  <th style="border:2px solid;" width="6%"><small><small><small><small><b>Imp Neto Suj a IVA (A-B-C)</b></small></small></small></small></small></th>
                                  <th style="border:2px solid;" width="6%"><small><small><small><small><b>Crédito Fiscal Obtenido</b></small></small></small></small></th>
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
                                $importe_sujeto_iva=$importe-$ice-$exento;;
                                $iva_obtenido=$importe_sujeto_iva*13/100;
                                $caracter=substr($codigo_control, -1);
                                if($caracter=='-'){
                                  $codigo_control=trim($codigo_control, '-');
                                }
                                if($codigo_control==null || $codigo_control=="")
                                  $codigo_control=0;

                                $total_importe+=$importe;
                                $total_ice+=$ice;
                                $total_exento+=$exento;
                                $total_importe_sujeto_iva+=$importe_sujeto_iva;
                                $total_iva_obtenido+=$iva_obtenido;

                                // $sumadeimporte=$importe+$ice+$exento;
                                $sumadeimporte=$importe;

                                //si es mayor a 20 caracteres, se partira
                                $nro_autorizacion_1="";
                                $nro_autorizacion_2="";
                                if (strlen($nro_autorizacion)>25) {
                                  for ($i=0; $i <25 ; $i++) { 
                                        $nro_autorizacion_1.=$nro_autorizacion[$i];
                                  }
                                  for ($i=25; $i <strlen($nro_autorizacion) ; $i++) { 
                                        $nro_autorizacion_2.=$nro_autorizacion[$i];
                                  }
                                }else{
                                  $nro_autorizacion_1=$nro_autorizacion;
                                }
                                ?>
                                <tr>
                                  <td class="text-center small"><small><?=$index;?></small></td>
                                  <td class="text-center small"><small><?=$fecha;?></small></td>
                                  <td class="text-right small"><small><?=$nit;?></small></td>
                                  <td class="text-left small"><small><span style="padding-left: 15px;"><?=$razon_social;?></span></small></td>
                                  <td class="text-right small"><small><?=$nro_factura;?></small></td>
                                  <td class="text-right small"><small><?=$nro_autorizacion_1.' '.$nro_autorizacion_2;?></small></td>
                                  <td class="text-center small"><small><?=$codigo_control;?></small></td>
                                  <td class="text-right small"><small><?=formatNumberDec($sumadeimporte);?></small></td>
                                  <td class="text-right small"><small><?=formatNumberDec($ice);?></small></td>
                                  <td class="text-right small"><small><?=formatNumberDec($exento);?></small></td>
                                  <td class="text-right small"><small><?=formatNumberDec($importe_sujeto_iva);?></small></td>
                                  <td class="text-right small"><small><?=formatNumberDec($iva_obtenido);?></small></td>                                      
                                </tr>
                                <?php                                  
                              }?>
                              <tr style="border:2px solid;">                               
                                  <td class="text-left small csp" colspan="3" style="border:2px solid;"><small>CI:</small></td>
                                  <td class="text-left small csp" colspan="3" style="border:2px solid;"><small>Nombre del Responsable:</small></td>
                                  <td class="text-center small"><small><b>SubTotal:</b></small></td>                                  
                                  <td class="text-right small"><small><?=formatNumberDec($total_importe);?></small></td>
                                  <td class="text-right small"><small><?=formatNumberDec($total_ice);?></small></td>
                                  <td class="text-right small"><small><?=formatNumberDec($total_exento);?></small></td>
                                  <td class="text-right small"><small><?=formatNumberDec($total_importe_sujeto_iva);?></small></td>
                                  <td class="text-right small"><small><?=formatNumberDec($total_iva_obtenido);?></small></td>                                      
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

