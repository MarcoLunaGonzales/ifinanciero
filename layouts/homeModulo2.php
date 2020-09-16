<?php
session_start();
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../functions.php';
$codModulo=3;
if(isset($_GET["mod"])){
  $codModulo=$_GET['mod'];  
}

switch ($codModulo) {
  case 1:
   $nombreModulo="RRHH";
   $cardTema="card-themes";
   $iconoTitulo="local_atm";
   $estiloHome="#DC5143";
   $fondoModulo="fondo-dashboard-recursoshumanos";
  break;
  case 2:
   $nombreModulo="Activos Fijos";
   $cardTema="card-snippets";
   $iconoTitulo="home_work";
   $estiloHome="#DCB943";
   $fondoModulo="fondo-dashboard-activos";
  break;
  case 3:
   $nombreModulo="Contabilidad";
   $cardTema="card-templates";
   $iconoTitulo="insert_chart_outlined";
   $estiloHome="#1B82DD";
   $fondoModulo="fondo-dashboard-contabilidad";
  break;
  case 4:
   $nombreModulo="Presupuestos / Solicitudes";
   $cardTema="card-guides";
   $iconoTitulo="list_alt";
   $estiloHome="#4FA54F";
   $fondoModulo="fondo-dashboard-solicitudes";
  break;
}
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];

$dbh=new Conexion();

//datos para consultar el servicio
$oficina="0";
$area="0";
$anio=date("Y");
$mes=date("m");
if(isset($_GET["anio"])){
  $anio=$_GET["anio"];
}
if(isset($_GET["mes"])){
  $mes=$_GET["mes"];
}

$nombreMes=abrevMes($mes);
$globalGestion=$_SESSION["globalGestion"];
$global_mes=$mes;

$ingresoTotal=obtenerPresupuestoEjecucionPorAreaAcumulado($oficina,$area,$anio,$mes,0);
$ingresoTotalAcumulado=obtenerPresupuestoEjecucionPorAreaAcumulado($oficina,$area,$anio,$mes,1);
$valorIngreso=calcularValorEnPoncentaje($ingresoTotal['ejecutado'],$ingresoTotal['presupuesto']);
$valorIngresoAcumulado=calcularValorEnPoncentaje($ingresoTotalAcumulado['ejecutado'],$ingresoTotalAcumulado['presupuesto']);
$valorIngresoFormat=number_format($valorIngreso,2,'.','');
$valorIngresoFormatAcumulado=number_format($valorIngresoAcumulado,2,'.','');

$ingresoTotalMonto=number_format($ingresoTotal['ejecutado'],2,'.',',');
$presupuestoTotalMonto=number_format($ingresoTotal['presupuesto'],2,'.',',');
$ingresoTotalMontoAcumulado=number_format($ingresoTotalAcumulado['ejecutado'],2,'.',',');
$presupuestoTotalMontoAcumulado=number_format($ingresoTotalAcumulado['presupuesto'],2,'.',',');
?>
<div class="cargar-ajax d-none">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold" id="texto_ajax_titulo">Procesando Datos</h4>
     <p class="text-white">Aguard&aacute; un momento por favor</p>  
  </div>
</div>
<input type="hidden" id="modulo" value="<?=$codModulo?>">
  <div class="container">
    <div class="div-center">
      <!--inicio dashboard-->
      <div class="content">
            <div class="row">
              <div class="col-md-12">
                <div class="card" style="background-color: rgba(255, 0, 0, 0) !important;">                  
                  <div class="card <?=$fondoModulo?>"></div>
                  <div class="card-header card-header-text">
                    <div class="card-text" style="background:<?=$estiloHome?>;">
                      <h4 class="card-title"><b>INGRESOS <?=$nombreMes?> <?=$anio?></b></h4>
                      <!---->
                    </div>
                    <a href="../index.php?opcion=homeModulo" title="IR A LA PAGINA DE INICIO" class="btn btn-primary btn-sm btn-fab float-right">
                      <i class="material-icons">home</i>
                    </a>
                    <a href="#" title="BUSCAR INGRESOS" class="btn btn-warning btn-sm btn-fab float-right" onclick="buscarIngresosDashboard()">
                      <i class="material-icons">search</i>
                    </a>
                      <div class="form-group float-right col-sm-1">
                        <select name="gestiones" id="gestiones" onChange="ajax_mes_de_gestion_reloj(this);" class="selectpicker form-control form-control-sm" data-style="btn btn-primary"  data-show-subtext="true" data-live-search="true" required="true">
                                    <option value=""disabled>--Gestión--</option>
                                    <?php 
                                    $query = "SELECT codigo,nombre from gestiones where cod_estado=1 ORDER BY nombre desc";

                                    $stmt = $dbh->query($query);
                                    while ($row = $stmt->fetch()){ ?>
                                        <option value="<?=$row["codigo"];?>" <?=($row["nombre"]==$anio)?"selected":""?> ><?=$row["nombre"];?></option>
                                    <?php } ?>
                                </select>
                         </div>
                      <div class="form-group float-right col-sm-2">
                        <div id="div_contenedor_mes">   
                          <?php $sql="SELECT c.cod_mes,(select m.nombre from meses m where m.codigo=c.cod_mes) as nombre_mes from meses_trabajo c join gestiones g on g.codigo=c.cod_gestion where g.nombre=$anio";
                  $stmtg = $dbh->prepare($sql);
                  $stmtg->execute();
                  ?>
                  <select name="cod_mes_x" id="cod_mes_x" class="selectpicker form-control form-control-sm" data-style="btn btn-primary"  required data-live-search="true">
                  <?php
                    
                    while ($rowg = $stmtg->fetch(PDO::FETCH_ASSOC)) {    
                      $cod_mes=$rowg['cod_mes'];    
                      $nombre_mes=$rowg['nombre_mes'];    
                    ?>
                    <option value="<?=$cod_mes;?>" <?=($cod_mes==$global_mes)?"selected":""?> ><?=$nombre_mes;?></option>
                    <?php 
                    }
                  ?>
                  </select>
                          
                        </div>                                    
                         </div>
                    
                  </div>
                  <div class="card-body">

                    <div class="row" style="background-color: rgba(255, 255, 255, 0.6) !important;">
                          <div class="col-md-4 div-center">
                            <div class="card card-chart text-center">
                              <div class="card-header card-header-rose" data-header-animation="false" style="background:<?=$estiloHome?> !important;">
                                <div id="ingreso_general_chart"></div>
                              </div>
                              <div class="card-body">
                                <div class="card-actions">
                                  
                                </div>
                                <h4 class="card-title">Mes: <?=$ingresoTotalMonto?> Bs</h4>
                                <h4 class="card-title">Acumulado : <?=$ingresoTotalMontoAcumulado?> Bs</h4>
                                <p class="card-category">Presupuesto Mes: <?=$presupuestoTotalMonto?> Bs<br> Presupuesto Acumulado: <?=$presupuestoTotalMontoAcumulado?> Bs</p>
                              </div>
                              <div class="card-footer">
                                <div class="stats">
                                  <i class="material-icons">access_time</i><small id="actualizado_ingresos"></small>
                                </div>
                              </div>
                            </div>
                          </div>
                      
                    </div>
        
                    <div class="row" style="background-color: rgba(255, 255, 255, 0.6) !important;">
                    <?php 
                    $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM areas where cod_estado=1 and centro_costos=1 and codigo not in (501,502,1235) order by 2 ");
                    $stmt->execute();
                    $cont=0;

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                      $cont++;
                      $codigoX=$row['codigo'];
                      $nombreX=$row['nombre'];
                      $abrevX=$row['abreviatura'];
                      if($codigoX==$globalArea){
                        
                      }else{
                                
                      }
                      //datos para consultar el servicio
                      $ingresoTotalArea=obtenerPresupuestoEjecucionPorAreaAcumulado($oficina,$codigoX,$anio,$mes,0);
                      $valorIngresoArea=calcularValorEnPoncentaje($ingresoTotalArea['ejecutado'],$ingresoTotalArea['presupuesto']);
                      $valorIngresoAreaFormat=number_format($valorIngresoArea,2,'.','');
                      $ingresoTotalMontoArea=number_format($ingresoTotalArea['ejecutado'],2,'.',',');
                      $presupuestoTotalMontoArea=number_format($ingresoTotalArea['presupuesto'],2,'.',',');

                      //acumulado
                      $ingresoTotalAreaAcumulado=obtenerPresupuestoEjecucionPorAreaAcumulado($oficina,$codigoX,$anio,$mes,1);
                      $valorIngresoAreaAcumulado=calcularValorEnPoncentaje($ingresoTotalAreaAcumulado['ejecutado'],$ingresoTotalAreaAcumulado['presupuesto']);
                      $valorIngresoAreaFormatAcumulado=number_format($valorIngresoAreaAcumulado,2,'.','');
                      $ingresoTotalMontoAreaAcumulado=number_format($ingresoTotalAreaAcumulado['ejecutado'],2,'.',',');
                      $presupuestoTotalMontoAreaAcumulado=number_format($ingresoTotalAreaAcumulado['presupuesto'],2,'.',',');
                      ?>
                          <div class="col-md-4">
                            <div class="card card-chart text-center">
                              <div class="card-header card-header-primary div-center" data-header-animation="false">
                                <div id="ingreso_general_chart<?=$cont?>"></div>
                              </div>
                              <div class="card-body">
                                <div class="card-actions">
                                  
                                </div>
                                <h4 class="card-title"><?=$abrevX?></h4>
                                <p class="card-category text-primary">Mes : <?=$ingresoTotalMontoArea?> Bs</p>
                                <p class="card-category text-primary">Acumulado : <?=$ingresoTotalMontoAreaAcumulado?> Bs</p>
                                <p class="card-category small">Presupuesto Mes: <?=$presupuestoTotalMontoArea?> Bs</p>
                                <p class="card-category small">Presupuesto Acumulado: <?=$presupuestoTotalMontoAreaAcumulado?> Bs</p>
                              </div>
                              <div class="card-footer">
                                <div class="stats">
                                  <i class="material-icons">access_time</i><small id="actualizado_ingresos<?=$cont?>"></small>
                                </div>
                              </div>
                            </div>
                          </div>
                          <input type="hidden" value="<?=$abrevX?>" id="nombre_area_chart<?=$cont?>">
                          <input type="hidden" value="<?=$valorIngresoAreaFormat?>" id="porcentaje_area_chart<?=$cont?>">
                          <input type="hidden" value="<?=$ingresoTotalMontoArea?>" id="ingreso_area_chart<?=$cont?>">
                          <input type="hidden" value="<?=$valorIngresoAreaFormatAcumulado?>" id="porcentaje_area_chart_acumulado<?=$cont?>">
                          <input type="hidden" value="<?=$ingresoTotalMontoAreaAcumulado?>" id="ingreso_area_chart_acumulado<?=$cont?>">
                      <?php                       
                    } 
                    ?>
                    <input type="hidden" value="<?=$cont?>" id="cantidad_filas_medidor">
                    </div>

                    <br>
                     <div class="row" style="background-color: rgba(255, 255, 255, 0.6) !important;">
                          <div class="col-md-12 text-center">
                            <div class="card">
                              <div class="card-body">
                                <h4 class="card-title" style="color:<?=$estiloHome?>"><b>MODULO - <?=strtoupper($nombreModulo)?></b></h4>
                              </div>
                            </div>
                          </div>
                      
                    </div>
                    <br>
                  </div>
                </div>
              </div>
            </div>
      </div>
      <!--fin dashboard-->            
      
    </div>
 </div>
   
    
    <script type="text/javascript">
      google.charts.load('current', {'packages':['gauge']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

        var data = google.visualization.arrayToDataTable([
          ['Label', 'Value'],
          ['MES', <?=$valorIngresoFormat?>],
          ['ACUMULADO', <?=$valorIngresoFormatAcumulado?>]
         
        ]);
        var options = {
          width: 270, height: 270,
          redFrom: 0, redTo: 25,
          yellowFrom:25, yellowTo: 75,
          greenFrom:75, greenTo: 100,
          minorTicks: 5
        };

        var chart = new google.visualization.Gauge(document.getElementById('ingreso_general_chart'));
        chart.draw(data, options);
        
        var dataAreas=[];
        var dataChart=[];
        var options_minus = {
           width: 250, height: 250,
           redFrom: 0, redTo: 25,
           yellowFrom:25, yellowTo: 75,
           greenFrom:75, greenTo: 100,
           minorTicks: 5
         };
         var meses = new Array ("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
          var diasSemana = new Array("Domingo","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado");
          var f=new Date();
          var hora = f.getHours() + ':' + f.getMinutes() + ':' + f.getSeconds();
          $('#actualizado_ingresos').html(diasSemana[f.getDay()] + ", " + f.getDate() + " de " + meses[f.getMonth()] + " de " + f.getFullYear()+"  "+hora);

        var cantidad_filas_medidor=$('#cantidad_filas_medidor').val(); 
        for (var i = 0; i < cantidad_filas_medidor; i++) {
          var data_i=google.visualization.arrayToDataTable([
                ['Label', 'Value'],
                //[$('#nombre_area_chart'+(i+1)).val(), parseFloat($("#porcentaje_area_chart"+(i+1)).val())]
                ['MES', parseFloat($("#porcentaje_area_chart"+(i+1)).val())],
                ['ACUMULADO', parseFloat($("#porcentaje_area_chart_acumulado"+(i+1)).val())],
          ]);
          dataAreas.push(data_i);
          var chart_i=new google.visualization.Gauge(document.getElementById('ingreso_general_chart'+(i+1)));
          chart_i.draw(data_i, options_minus);
          dataChart.push(chart_i); 
          $('#actualizado_ingresos'+(i+1)).html(diasSemana[f.getDay()] + ", " + f.getDate() + " de " + meses[f.getMonth()] + " de " + f.getFullYear()+"  "+hora);
        };
        
        /*setInterval(function() {
            var JSON=$.ajax({
                url:"layouts/datos_medidor.php?t=1",
                dataType: 'json',
                async: false}).responseText;
            var Respuesta=jQuery.parseJSON(JSON);
            
          

          data.setValue(0, 1,Respuesta.ibnorca);
          chart.draw(data, options);
          for (var i = 0; i < dataAreas.length; i++) {
            dataAreas[i].setValue(0, 1,Respuesta.ibnorca);
            dataChart[i].draw(dataAreas[i], options_minus);
            
          };
        }, 1300);*/
        
      }
    </script>

       
   
<!--<section class="after-loop">
  <div class="container">
    <div class="div-center text-center">
      
     <img src="assets/img/logo_ibnorca.png" width="160" height="160" alt="">
      <h3>Modulo <?=$nombreModulo?></h3>
      <p>
        <a href="index.php" class="btn btn-primary">IR A LA PAGINA DE INICIO</a>
      </p>
    </div>-->


    <!--<div class="mb-5 mb-lg-0 mx-auto">
       <a href="#" class="after-loop-item card border-0 <?=$cardTema?> shadow-lg">
          <div class="card-body d-flex align-items-end flex-column text-right">
             <h4>Modulo <?=$nombreModulo?></h4>
             <p class="w-75">Descripción del Modulo!</p>
             <i class="material-icons"><?=$iconoTitulo?></i>
          </div>
       </a>
     </div>-->
 
 <!--</div>
</section>-->