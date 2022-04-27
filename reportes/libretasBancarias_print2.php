<?php
session_start();
set_time_limit(0);
ini_set('memory_limit', '1G');
require_once '../layouts/bodylogin2.php';
require_once '../conexion2.php';
require_once '../styles.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../functionsLibretaBancaria.php';
// require_once '../assets/libraries/CifrasEnLetras.php';

setlocale(LC_TIME, "Spanish");

$dbh = new Conexion();
$fechaActual=date("Y-m-d");

$fecha=$_POST['fecha_desde'];
$fechaTitulo= explode("-",$fecha);
$fechaFormateada=$fechaTitulo[2].'/'.$fechaTitulo[1].'/'.$fechaTitulo[0];

$fechaHasta=$_POST['fecha_hasta'];
$fechaTituloHasta= explode("-",$fechaHasta);
$fechaFormateadaHasta=$fechaTituloHasta[2].'/'.$fechaTituloHasta[1].'/'.$fechaTituloHasta[0];


$moneda=1; //$_POST["moneda"];
$StringEntidadCodigos=$_POST['libretas'];
$stringEntidades=nameLibretas($StringEntidadCodigos);


$periodoTitle= "Del ".$fechaFormateada.' al '.$fechaFormateadaHasta; 

$sqlFiltro="";
$sqlFiltroSaldo="";
$sqlFiltro2="";

$sqlFiltroComp="";

 $saltoAnterior=obtenerSaldoAnteriorLibreta($fecha,$StringEntidadCodigos);
//$saltoAnterior=183840.23-480.00;
?>
<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">

                  <div class="card-icon bg-blanco">
                    <img class="" width="40" height="40" src="../assets/img/logoibnorca.png">
                  </div>           
                   <h4 class="card-title text-center"><?=obtenerValorConfiguracion(57)?></h4>
                   <!--<div class="float-right col-sm-2"><h6 class="card-title">Exportar como</h6></div>-->
                </div>
                <div class="card-body">
  <h6 class="card-title">Periodo Libretas: <?=$periodoTitle?></h6>
  
  <h6 class="card-title">Libretas Bancarias: <?=$stringEntidades;?></h6>
  
  <div class="col-sm-4 float-right">
    <div class="row">
        <label class="col-sm-4 col-form-label">Saldo Anterior</label>
        <div class="col-sm-8">
          <div class="form-group">
             <input class="form-control"  readonly value="<?=number_format($saltoAnterior,2,".",",")?>" id="total_reporte">                   
          </div>  
        </div>     
      </div>  
    </div>
  <div class="table-responsive col-sm-12"> 

    <table id="libreta_bancaria_reporte" class="table table-condensed small" style="width:100% !important;">
      <thead>
        <tr style="background:#21618C; color:#fff;">
          <td>Fecha</td>
          <td>Hora</td>
          <td width="35%">Descripción</td>          
          <td>Sucursal</td>
          <td>Monto</td>
          <td>Saldo</td>
          <td width="10%">Nro Doc / Nro Ref</td>
          <td>Saldo Calculado</td>
          <td>Diferencia</td>
        </tr>
      </thead> <tbody>
      <?php
      
      $sqlDetalle="SELECT ce.*
      FROM libretas_bancariasdetalle ce join libretas_bancarias lb on lb.codigo=ce.cod_libretabancaria where lb.codigo in ($StringEntidadCodigos) and ce.fecha_hora BETWEEN '$fecha 00:00:00' and '$fechaHasta 23:59:59' and
       ce.cod_estadoreferencial=1 order by ce.fecha_hora";
      //echo $sqlDetalle;
      $stmt = $dbh->prepare($sqlDetalle);      
      // Ejecutamos
      $stmt->execute();
      // bindColumn
      $stmt->bindColumn('codigo', $codigo);
      $stmt->bindColumn('descripcion', $descripcion);
      $stmt->bindColumn('informacion_complementaria', $informacion_complementaria);
      $stmt->bindColumn('agencia', $agencia);
      $stmt->bindColumn('nro_cheque', $nro_cheque);
      $stmt->bindColumn('nro_documento', $nro_documento);
      $stmt->bindColumn('fecha_hora', $fecha);
      $stmt->bindColumn('monto', $monto);
      $stmt->bindColumn('saldo', $saldolb);
      $stmt->bindColumn('cod_comprobante', $codComprobante);
      $stmt->bindColumn('cod_comprobantedetalle', $codComprobanteDetalle);
      $index=1;$totalMonto=0;
      while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
        $saltoAnterior=$saltoAnterior+$monto;
        $diferencia=$saltoAnterior-$saldolb;
        $label="<span>";
        if(number_format($diferencia,2,".",",")<>0){
          $label="<span class='bagde badge-danger'>";
        }
        ?>
        <tr>
          <td class="text-center font-weight-bold"><?=strftime('%d/%m/%Y',strtotime($fecha))?></td>
          <td class="text-center"><?=strftime('%H:%M:%S',strtotime($fecha))?></td>
          <td class="text-left">
            <?=$descripcion?> info: <?=$informacion_complementaria?>
          </td>      
          <td class="text-left"><?=$agencia?></td>
          <td class="text-right"><b><?=number_format($monto,2,".",",")?></b></td>
          <td class="text-right"><b><?=number_format($saldolb,2,".",",")?></b></td>
          <td class="text-right"><?=$nro_documento?></td>
          <td class="text-right"><b><?=number_format($saltoAnterior,2,".",",")?></b></td>
          <td class="text-right"><?=$label?><?=number_format($diferencia,2,".",",")?></span></td>
        </tr>
        <?php
      }?>

      <tfoot>
        <tr style="background:#21618C; color:#fff;">
          <th>Fecha</th>
          <th>Hora</th>
          <th>Descripcion</th>
          <th>Sucursal</th>
          <th>Monto</th>
          <th>Saldo</th>
          <th>Nro Documento</th>
          <th>Saldo Calculado</th>
          <th>Diferencia</th>
        </tr>
      </tfoot>
    </table>

 </div>
</div>
      

                
              </div>
            </div>
          </div>  
        </div>
    </div>
