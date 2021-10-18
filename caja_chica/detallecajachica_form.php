<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';
require_once 'functions.php';
require_once 'functionsGeneral.php';

//$dbh = new Conexion();
$dbh = new Conexion();
$globalUnidad=$_SESSION["globalUnidad"];
//por is es edit
$cod_cc=$cod_cc;
$cod_tcc=$cod_tcc;
$cod_dcc=$codigo;
$i=0;
echo "<script>var array_cuenta=[],imagen_cuenta=[];</script>";
$stmtCuenta = $dbh->prepare("SELECT pcc.cod_cuenta,pc.numero,pc.nombre from plan_cuentas_cajachica pcc,plan_cuentas pc where pcc.cod_cuenta=pc.codigo");
$stmtCuenta->execute();
while ($rowCuenta = $stmtCuenta->fetch(PDO::FETCH_ASSOC)) {
  $codigoX=$rowCuenta['cod_cuenta'];
  $numeroX=$rowCuenta['numero'];
  $nombreX=$rowCuenta['nombre'];
  ?>
  <script>
    var obtejoLista={
      label:'<?=trim($numeroX)?> - <?=trim($nombreX)?>',
      value:'<?=$codigoX?>'};
      array_cuenta[<?=$i?>]=obtejoLista;
      imagen_cuenta[<?=$i?>]='../assets/img/calc.jpg';
    </script><?php
  $i=$i+1;  
}
//cargamos las configuraciones de estados de cuetna
echo "<script>var array_configuracion_estadocuenta=[];</script>";
$stmtConfiguracionCuenta = $dbh->prepare("SELECT cod_plancuenta from configuracion_estadocuentas where cod_estadoreferencial=1");
$stmtConfiguracionCuenta->execute();
while ($rowConfi = $stmtConfiguracionCuenta->fetch(PDO::FETCH_ASSOC)) {
  $codigo_plancuentaX=$rowConfi['cod_plancuenta'];
  ?>
  <script>
    var configuracion={
      codigo_plancuenta: <?=$codigo_plancuentaX?>,
    }
    array_configuracion_estadocuenta.push(configuracion);  
  </script>
  <?php  
}

$cod_proveedores=36272;//otros proveedores
if ($codigo > 0){
    $stmt = $dbh->prepare("SELECT codigo,cod_cuenta,fecha,cod_tipodoccajachica,nro_documento,cod_personal,monto,observaciones,nro_recibo,cod_area,cod_uo,cod_proveedores,cod_actividad_sw,cod_tipopago,
        (select c.nombre from plan_cuentas c where c.codigo=cod_cuenta) as nombre_cuenta,
        (select c.numero from plan_cuentas c where c.codigo=cod_cuenta) as nro_cuenta
    from caja_chicadetalle
    where codigo =:codigo");
    $stmt->bindParam(':codigo',$cod_dcc);
    $stmt->execute();
    $result = $stmt->fetch();
    $cod_cuenta = $result['cod_cuenta'];
    $fecha = $result['fecha'];    
    $fecha2= date("Y-m-t", strtotime($fecha)); 

    $cod_retencion = $result['cod_tipodoccajachica'];    
    $nro_documento = $result['nro_documento'];    
    $cod_personal = $result['cod_personal'];    
    $observaciones = $result['observaciones'];    
    $monto = $result['monto'];
    $nombre_cuenta = $result['nombre_cuenta'];    
    $nro_cuenta = $result['nro_cuenta']; 
    $nro_recibo= $result['nro_recibo'];
    $cod_area= $result['cod_area'];
    $cod_uo= $result['cod_uo'];
    $cod_proveedores= $result['cod_proveedores'];
    $cod_actividad_sw= $result['cod_actividad_sw'];
    $cod_tipopago= $result['cod_tipopago'];
    $cuenta_aux=$nro_cuenta." - ".$nombre_cuenta;
    // sacamos datos del comprobante (estao de cuenta)
    $stmtComprobante = $dbh->prepare("SELECT e.cod_comprobantedetalleorigen,e.cod_plancuenta,e.cod_cuentaaux,(select c.glosa from comprobantes_detalle c where c.codigo in (select ee.cod_comprobantedetalle from estados_cuenta ee where ee.codigo=e.cod_comprobantedetalleorigen))as glosa
    FROM estados_cuenta e WHERE cod_cajachicadetalle=$codigo");
    $stmtComprobante->execute();
    $resultComprobante = $stmtComprobante->fetch();
    $cod_comprobante =  $resultComprobante['cod_comprobantedetalleorigen'];//estado de cuenta
    $cod_cuenta_compro=$resultComprobante['cod_plancuenta'];
    $cod_cuenta_aux_compro=$resultComprobante['cod_cuentaaux']; 
    $glosa_comprobante=$resultComprobante['glosa']; 
}else{
    //para el numero correlativo
    $stmtCC = $dbh->prepare("SELECT nro_documento,nro_recibo from caja_chicadetalle where cod_cajachica=$cod_cc order by codigo desc limit 1");
    $stmtCC->execute();
    $resultCC = $stmtCC->fetch();
    $numero_caja_chica_aux = $resultCC['nro_documento'];
    $numero_recibo_aux = $resultCC['nro_recibo'];
    if($numero_caja_chica_aux==null){
        $numero_caja_chica_aux=0;
        $numero_recibo_aux=0;
    }
    $codigo=0;
    // $cod_cuenta = 0;
    $cod_uo=$_SESSION["globalUnidad"];
    $cod_area=$_SESSION["globalArea"];
    
    $fecha = date('Y-m-d');
    $fecha2=$fecha;//rango maximo de fecha
    $cod_retencion = null;
    $nro_documento = $numero_caja_chica_aux+1;
    $nro_recibo=$numero_recibo_aux+1;
    
    $cod_personal=$_SESSION["globalUser"];  
    $observaciones = null;    
    $monto = 0;    
    $cod_estado = 1;    
    $cod_cuenta_compro=null;
    $cod_cuenta_aux_compro=null;
    $cod_comprobante=null;
    // $cod_contra_cuenta_aux=null;

    $cuenta_aux="";
    $cod_cuenta=0;
    $cod_actividad_sw=null;
    $glosa_comprobante="";
    $cod_tipopago="";
    $nro_recibo=obtenerNumeroReciboInstancia($cod_tcc);
}


//sacmos el valor de fechas hacia atrás
$dias_atras=obtenerValorConfiguracion(31);
$fecha_dias_atras=obtener_diashsbiles_atras($dias_atras,$fecha);

//distribucion gastosarea
$distribucionOfi=obtenerDistribucionCentroCostosUnidadActivo(); //null para todas las iniciales del numero de cuenta obtenerCuentasLista(5,[5,4]);
while ($rowOfi = $distribucionOfi->fetch(PDO::FETCH_ASSOC)) {
  $codigoD=$rowOfi['codigo'];
  $codDistD=$rowOfi['cod_distribucion_gastos'];
  $codUnidadD=$rowOfi['cod_unidadorganizacional'];    
  $nombreD=$rowOfi['nombre'];
  $porcentajeD=-1;
  if($codigo>0){
    $porcentajeD=ontener_porcentaje_distribucion_cajachica($codigo,$codUnidadD,1);      
  }
  if($porcentajeD<0){
    $porcentajeD=$rowOfi['porcentaje'];
  }
  //verificarHayAmbasDistribucionesSolicitudRecurso
  ?>
  <script>
    var distri = {
      codigo:<?=$codigoD?>,
      cod_dis:<?=$codDistD?>,
      unidad:<?=$codUnidadD?>,
      nombre:'<?=$nombreD?>',
      porcentaje:<?=$porcentajeD?>
    }
    itemDistOficina.push(distri);
  </script>  
  <?php
}
$distribucionArea=obtenerDistribucionCentroCostosAreaActivo($globalUnidad); //null para todas las iniciales del numero de cuenta obtenerCuentasLista(5,[5,4]);
while ($rowArea = $distribucionArea->fetch(PDO::FETCH_ASSOC)) {
  $codigoD=$rowArea['codigo'];
  $codDistD=$rowArea['cod_distribucionarea'];
  $codAreaD=$rowArea['cod_area'];
  $nombreD=$rowArea['nombre'];
  $porcentajeD=$rowArea['porcentaje'];
  $porcentajeD=-1;
  if($codigo>0){
    $porcentajeD=ontener_porcentaje_distribucion_cajachica($codigo,$codAreaD,2);    
  }
  if($porcentajeD<0){
    $porcentajeD=$rowArea['porcentaje'];
  }
  ?>
  <script>
    var distri = {
      codigo:<?=$codigoD?>,
      cod_dis:<?=$codDistD?>,
      area:<?=$codAreaD?>,
      nombre:'<?=$nombreD?>',
      porcentaje:<?=$porcentajeD?>
    }
    itemDistArea.push(distri);
  </script>  
  <?php
}


if($codigo>0){
  $indexArea=0;
  $stmtAreas = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM areas where cod_estado=1 and centro_costos=1 order by 2");
  $stmtAreas->execute();
  while ($row = $stmtAreas->fetch(PDO::FETCH_ASSOC)) {
   $codigoX=$row['codigo'];
   $nombreX=$row['nombre'];
   $abrevX=$row['abreviatura'];
   $porcentajeA=obtenerPorcentajeDistribucionGastoCajaChicaGeneral(0,2,$codigoX,$codigo,0);
  ?>
  <script>
    var distri = {
      fila:<?=$codigoX?>,
      codigo:1,
      cod_dis:2,
      area:<?=$codigoX?>,
      nombre:'<?=$nombreX?> - <?=$abrevX?>',
      porcentaje:<?=$porcentajeA?>
    }
      itemDistAreaGlobal.push(distri);
    
  </script> 
    <?php
    $distribucionOfi=obtenerDistribucionCentroCostosUnidadActivo(); //null para todas las iniciales del numero de cuenta obtenerCuentasLista(5,[5,4]);
    while ($rowOfi = $distribucionOfi->fetch(PDO::FETCH_ASSOC)) {
      $codigoD=$rowOfi['codigo'];
      $codDistD=$rowOfi['cod_distribucion_gastos'];
      $codUnidadD=$rowOfi['cod_unidadorganizacional'];
      $porcentajeD=$rowOfi['porcentaje'];
      $nombreD=$rowOfi['nombre'];
      $porcentajeD=obtenerPorcentajeDistribucionGastoCajaChicaGeneral(0,1,$codUnidadD,$codigo,$codigoX);
    ?>
     <script>
     var ofi = {
          cod_fila:<?=$codigoX?>,
          codigo:<?=$codigoD?>,
          cod_dis:<?=$codDistD?>,
          unidad:<?=$codUnidadD?>,
          nombre:'<?=$nombreD?>',
          porcentaje:<?=$porcentajeD?>
     }
    itemDistOficinaGeneral.push(ofi);
     </script>  
  <?php
    }

    $indexArea++;
  }
}else{
  $indexArea=0;
  $stmtAreas = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM areas where cod_estado=1 and centro_costos=1 order by 2");
  $stmtAreas->execute();
  while ($row = $stmtAreas->fetch(PDO::FETCH_ASSOC)) {
    $codigoX=$row['codigo'];
    $nombreX=$row['nombre'];
    $abrevX=$row['abreviatura'];
    ?>
    <script>
      var distri = {
        fila:<?=$codigoX?>,
        codigo:1,
        cod_dis:2,
        area:<?=$codigoX?>,
        nombre:'<?=$nombreX?> - <?=$abrevX?>',
        porcentaje:0
      }
      var porcentajeOfi=0;
      for (var i = 0; i < itemDistOficina.length; i++) {
        //if (i == 0){ porcentajeOfi=100; }else{ porcentajeOfi=0;}
        var ofi = {
        cod_fila:<?=$codigoX?>,
        codigo:itemDistOficina[i].codigo,
        cod_dis:itemDistOficina[i].cod_dis,
        unidad:itemDistOficina[i].unidad,
        nombre:itemDistOficina[i].nombre,
        porcentaje:porcentajeOfi
        }
        itemDistOficinaGeneral.push(ofi); 
      }
      itemDistAreaGlobal.push(distri);
      
    </script> 
    <?php
    $indexArea++;
  }
}
$archivos_cajachica=0;//contador de archivos de caja chica
?>

<div class="content">
	<div class="container-fluid">
		<div class="col-md-12">
		  <form id="formDetalleCajaChica" class="form-horizontal" action="<?=$urlSaveDetalleCajaChica;?>" method="post" onsubmit="return valida(this)" enctype="multipart/form-data">         

        <input type="hidden" name="codigo" id="codigo" value="<?=$codigo;?>"/>
        <input type="hidden" name="cod_cc" id="cod_cc" value="<?=$cod_cc;?>"/>
        <input type="hidden" name="cod_tcc" id="cod_tcc" value="<?=$cod_tcc;?>"/>
  			<div class="card">
  			  <div class="card-header <?=$colorCard;?> card-header-text">
    				<div class="card-text">
    				  <h4 class="card-title"><?php if ($codigo == 0) echo "Registrar Nuevo"; else echo "Editar";?>  Gasto</h4>
    				</div>
  			  </div>
  			  <div class="card-body ">		
         <?php 
         //if(!isset($_GET["sr"])){ 
         ?> 	           
            <div class="row">
              <label class="col-sm-2 col-form-label">Monto</label>
                <div class="col-sm-4">
                    <div class="form-group">
                        <input class="form-control" <?=(isset($_GET["sr"]))?"readonly":"";?> type="number" step="any" name="monto" id="monto" value="<?=$monto;?>" <?=(!isset($_GET["sr"]))?"required":"";?>/>
                    </div>
                </div>
                
                    <input class="form-control" type="hidden" name="numero" id="numero" value="<?=$nro_documento;?>" onkeyup="javascript:this.value=this.value.toUpperCase();" readonly="readonly"/>
                <!-- </div>
                </div> -->
                <label class="col-sm-2 col-form-label">Nro. Recibo</label>
                <div class="col-sm-4">
                <div class="form-group">
                    <input class="form-control" <?=(isset($_GET["sr"]))?"readonly":"";?> type="number" name="nro_recibo" id="nro_recibo" value="<?=$nro_recibo;?>" onkeyup="javascript:this.value=this.value.toUpperCase();" <?=(!isset($_GET["sr"]))?"required":"";?>/>
                </div>
                </div>
            </div> 
            <div class="row">
                <label class="col-sm-2 col-form-label">Tipo Retención</label>
                <div class="col-sm-4">
                    <div class="form-group">
                      <div id="div_contenedor_tiporetencion">  
                        <?php

                        if($codigo>0 && $cod_comprobante>0 && $cod_comprobante!=""){?>
                          <select name="tipo_retencion" id="tipo_retencion" class="selectpicker form-control form-control-sm" data-style="btn btn-info" <?=(!isset($_GET["sr"]))?"required":"";?>>
                            <option value="" disabled selected="selected">-Retenciones-</option>
                              <?php                                     
                              $stmtTipoRet = $dbh->query("SELECT * from configuracion_retenciones where cod_estadoreferencial=1 order by 2");
                              while ($row = $stmtTipoRet->fetch()){ ?>

                                  <option <?=($cod_retencion==$row["codigo"])?"selected":"disabled";?> <?=(isset($_GET["sr"]))?"disabled":"";?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                              <?php } 
                              ?>
                          </select>  
                        <?php }else{?>
                          <select name="tipo_retencion" id="tipo_retencion" class="selectpicker form-control form-control-sm" data-style="btn btn-info" <?=(!isset($_GET["sr"]))?"required":"";?>>
                            <option value="" disabled selected="selected">-Retenciones-</option>
                              <?php                                     
                              $stmtTipoRet = $dbh->query("SELECT * from configuracion_retenciones where cod_estadoreferencial=1 order by 2");
                              while ($row = $stmtTipoRet->fetch()){ ?>
                                  <option <?=($cod_retencion==$row["codigo"])?"selected":"";?> <?=(isset($_GET["sr"]))?"disabled":"";?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                              <?php } ?>
                          </select>  
                        <?php }
                        ?>
                      </div>                                  
                    </div>
                </div>

                <label class="col-sm-2 col-form-label">Fecha</label>
                <div class="col-sm-4">
                    <div class="form-group">
                        
                        <input class="form-control" <?=(isset($_GET["sr"]))?"readonly":"";?> name="fecha" id="fecha" type="date" min="<?=$fecha_dias_atras?>" max="<?=$fecha2?>" <?=(!isset($_GET["sr"]))?"required":"";?> value="<?=$fecha?>" />
                    </div>
                </div>
            </div><!-- monto y fecha -->

            <div class="row">
                <label class="col-sm-2 col-form-label">Forma Pago</label>
                <div class="col-sm-4">
                    <div class="form-group">
                      <div>  
                        <select name="tipo_pago" id="tipo_pago" class="selectpicker form-control form-control-sm" data-style="btn btn-warning" <?=(!isset($_GET["sr"]))?"required":"";?>>
                            <option value="" disabled selected="selected">-Ninguno-</option>
                              <?php                                     
                               $stmtPago = $dbh->prepare("SELECT codigo,nombre FROM tipos_pagoproveedor where codigo in (2,3)");//and cod_personal=$globalUser
                                //ejecutamos
                                $stmtPago->execute();
                                //bindColumn
                                $stmtPago->bindColumn('codigo', $codigoTipo);
                                $stmtPago->bindColumn('nombre', $nombreTipo);

                                while ($rowPago = $stmtPago->fetch(PDO::FETCH_BOUND)) {         
                                   ?><option value="<?=$codigoTipo;?>" <?=($codigoTipo==$cod_tipopago)?"selected":"";?> <?=(isset($_GET["sr"]))?"disabled":"";?> ><?=$nombreTipo;?></option><?php 
                                 }   ?>
                          </select> 
                      </div>                                  
                    </div>
                </div>

                <div class="col-sm-6">
                    
                </div>
            </div><!-- forma pago -->

            <div class="row">
              <label class="col-sm-2 col-form-label">Cuenta</label>
              <div class="col-sm-8">
                <div class="form-group">
                    <input class="form-control" <?=(isset($_GET["sr"]))?"readonly":"";?>  type="text" name="cuenta_auto" id="cuenta_auto" value="<?=$cuenta_aux?>" placeholder="[numero] y nombre de cuenta" <?=(!isset($_GET["sr"]))?"required":"";?> />
                    <input class="form-control" type="hidden" name="cuenta_auto_id" id="cuenta_auto_id" value="<?=$cod_cuenta?>" required/>
                </div>
              </div>
              <div class="col-sm-2">
                <div class="form-group">                                
                  <?php
                  if($cod_comprobante>0 && $cod_comprobante!=""){?>
                    <div class="retencion_sin_gastos" >
                      <a  href="#" onclick="verEstadosCuentas_cajachica_filtro()" class="btn btn-danger btn-fab btn-round btn-sm">
                        <i class="material-icons text-dark" title="Estado de Cuentas">ballot</i>
                        <span id="ec_icon" class="count bg-warning">*</span>
                      </a>
                    </div>
                  <?php }else{?>
                    <div class="retencion_sin_gastos" style="display: none">
                      <a  href="#" onclick="verEstadosCuentas_cajachica_filtro()" class="btn btn-danger btn-fab btn-round btn-sm">
                        <i class="material-icons text-dark" title="Estado de Cuentas">ballot</i>
                        <span id="ec_icon" class="count bg-warning"></span>
                      </a>
                    </div>
                  <?php }
                  ?>
                  
                </div>
                </div>
            </div><!-- cuenta-->

            <input type="hidden" name="cuenta1" id="cuenta1" value="<?=$cod_cuenta_compro?>">
            <input type="hidden" name="cuenta_auxiliar1" id="cuenta_auxiliar1" value="<?=$cod_cuenta_aux_compro?>">

            <input type="hidden" name="comprobante" id="comprobante" value="<?=$cod_comprobante?>">
            <input type="hidden" id="tipo_estadocuentas1" value="3">
            <div class="row">
              <label class="col-sm-2 col-form-label">Personal</label>
              <div class="col-sm-8">
                <div class="form-group">
                    <select name="cod_personal" id="cod_personal" class="selectpicker form-control form-control-sm" data-style="btn btn-info"  data-show-subtext="true" data-live-search="true" onChange="ajaxCajaCPersonalUO(this);">
                        <option value=""></option>
                        <?php 
                        $querypersonal = "SELECT codigo,CONCAT_WS(' ',paterno,materno,primer_nombre)AS nombre from personal where cod_estadoreferencial=1 order by nombre";
                        $stmtPersonal = $dbh->query($querypersonal);
                        while ($row = $stmtPersonal->fetch()){ ?>
                            <option <?=($cod_personal==$row["codigo"])?"selected":"";?> <?=(isset($_GET["sr"]))?"disabled":"";?> value="<?=$row["codigo"];?>"><?=strtoupper($row["nombre"]);?></option>
                        <?php } ?>
                    </select>
                </div>
              </div>
            </div>
            <div class="row">
              <label class="col-sm-2 col-form-label">Oficina</label>
              <div class="col-sm-8">
                <div class="form-group">
                    <div id="div_contenedor_uo">                                        
                      <?php
                          $sqlUO="SELECT codigo,nombre,abreviatura from unidades_organizacionales where cod_estado=1 and centro_costos=1";
                          $stmt = $dbh->prepare($sqlUO);
                          $stmt->execute();
                          ?>
                          <select name="cod_uo" id="cod_uo" class="selectpicker form-control form-control-sm" data-style="btn btn-primary" data-show-subtext="true" data-live-search="true"  title="Elija una opción">
                            <!-- onChange="ajaxAreaUOCAJACHICA(this);" -->
                              <?php 
                                  while ($row = $stmt->fetch()){ 
                              ?>
                                   <option <?=($cod_uo==$row["codigo"])?"selected":"";?> <?=(isset($_GET["sr"]))?"disabled":"";?> data-subtext="<?=$row["codigo"];?>" value="<?=$row["codigo"];?>"><?=$row["nombre"];?>(<?=$row["abreviatura"];?>)</option>
                               <?php 
                                  } 
                              ?>
                           </select>                                              
                    </div>                                                        
                </div>
              </div>
              <!-- para la distribucion de monto -->
              <?php
              if(!isset($_GET["sr"])){
                ?>
              <div class="col-sm-2">
                <input type="hidden" name="n_distribucion" id="n_distribucion" value="0">
                <input type="hidden" name="nueva_distribucion" id="nueva_distribucion" value="0">
                <div class="btn-group dropdown">
                        <button type="button" class="btn btn-sm btn-success dropdown-toggle material-icons text-dark" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Distribucion de Gastos">
                        <i class="material-icons">call_split</i> <span id="distrib_icon" class="bg-warning"></span> <b id="boton_titulodist">Distribución</b>
                          </button>
                          <div class="dropdown-menu">   
                          <a title="Distribucion" href="#modalDist" data-toggle="modal" data-target="#modalDist" id="distribucion" onclick="cargarDistribucionSol(1)" class="dropdown-item">
                            <i class="material-icons">bubble_chart</i> x Oficina
                          </a>
                          <a title="Distribucion" href="#modalDist" data-toggle="modal" data-target="#modalDist" id="distribucion" onclick="cargarDistribucionSol(2)" class="dropdown-item">
                            <i class="material-icons">bubble_chart</i> x Área
                          </a>
                          <a title="Distribucion" href="#modalDist" data-toggle="modal" data-target="#modalDist" id="distribucion" onclick="cargarDistribucionSol(3)" class="dropdown-item">
                            <i class="material-icons">bubble_chart</i> x Oficina y x Área
                          </a>
                          <a title="Distribucion" href="#modalDist" data-toggle="modal" data-target="#modalDist" id="distribucion" onclick="cargarDistribucionSol(4)" class="dropdown-item">
                            <i class="material-icons">bubble_chart</i> x Área y Oficina
                          </a>
                          <a title="Distribucion" href="#modalDist" data-toggle="modal" data-target="#modalDist" id="distribucion" onclick="cargarDistribucionSol(0)" class="dropdown-item">
                            <i class="material-icons">bubble_chart</i> Nínguna
                          </a>
                          </div>
                      </div>
                  <div id="array_distribucion"></div>    
              </div>
             <?php
             }
             ?>     
            </div>
            <div class="row">
              <label class="col-sm-2 col-form-label">Area</label>
              <div class="col-sm-8">
                <div class="form-group">
                    <div id="div_contenedor_area">                                        
                        <?php
                        
                            //$sqlUO="SELECT cod_area,(select a.nombre from areas a where a.codigo=cod_area )as nombre_areas,(select a.abreviatura from areas a where a.codigo=cod_area)as abrev_area from areas_organizacion where cod_estadoreferencial=1 and cod_unidad=$cod_uo order by nombre_areas";
                            $sqlUO="SELECT codigo,nombre,abreviatura from areas where cod_estado=1 and centro_costos=1 ORDER BY nombre";
                            $stmt = $dbh->prepare($sqlUO);
                            $stmt->execute();
                            ?>
                            <select name="cod_area" id="cod_area" class="selectpicker form-control form-control-sm" data-style="btn btn-primary" data-show-subtext="true" data-live-search="true" >
                                <?php 
                                    while ($row = $stmt->fetch()){ 
                                ?>
                                     <option value="<?=$row["codigo"];?>" data-subtext="<?=$row["codigo"];?>" <?=($cod_area==$row["codigo"])?"selected":"";?> <?=(isset($_GET["sr"]))?"disabled":"";?> ><?=$row["nombre"];?>(<?=$row["abreviatura"];?>)</option>
                                 <?php 
                                    } 
                                ?>
                             </select>
                       
                    </div>
                </div>
              </div>
            </div>
            <div class="row">
              <label class="col-sm-2 col-form-label">Actividad</label>
              <div class="col-sm-8">
                <div class="form-group">
                  
                    <div id="div_contenedor_actividad">
                      <?php
                      $cod_uo_proy_fin=VerificarProyFinanciacion($cod_uo);//verificamos si el codigo pertenece a algun proyecto, de ser asi obtenemos el codigo                              
                      if($cod_uo_proy_fin!=null){
                        $lista= obtenerActividadesServicioImonitoreo($cod_uo_proy_fin); ?>

                        <select name="cod_actividad" id="cod_actividad" class="selectpicker form-control form-control-sm" data-style="btn btn-primary" data-show-subtext="true" data-live-search="true">
                        <option disabled selected value="">--SELECCIONE--</option>
                         <?php
                              foreach ($lista as $listas) { ?>
                                <option <?=($cod_actividad_sw==$listas->codigo)?"selected":"";?> <?=(isset($_GET["sr"]))?"disabled":"";?> value="<?=$listas->codigo?>" class="text-right"><?=substr($listas->nombre, 0, 85)?></option>

                              <?php }?>
                        </select>        
                      <?php }else{ ?>
                          <input type="hidden" name="cod_actividad" id="cod_actividad" value="<?=$cod_actividad_sw?>">

                      <?php } ?>                                                                               
                    </div>
                </div>
              </div>
            </div>
            <!-- proveedor -->
            <div class="row">
              <label class="col-sm-2 col-form-label">Proveedor :</label>
              <div class="col-sm-8">
                <div class="form-group">                        
                  <div id="div_contenedor_proveedor">
                    <select class="selectpicker form-control form-control-sm" name="proveedores" id="proveedores" data-style="btn btn-info" data-show-subtext="true" data-live-search="true" title="Seleccione Proveedor" <?=(!isset($_GET["sr"]))?"required":"";?>>
                      <?php 
                      $query="SELECT * FROM af_proveedores order by nombre";
                      $stmt = $dbh->prepare($query);
                      $stmt->execute();
                      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $codigoProv=$row['codigo'];    
                        ?><option <?=($cod_proveedores==$codigoProv)?"selected":"";?> <?=(isset($_GET["sr"]))?"disabled":"";?> value="<?=$codigoProv?>" class="text-right"><?=$row['nombre']?></option>
                       <?php 
                       } ?> 
                    </select>
                  </div>
                </div>
              </div> 
              <?php 
              if(!isset($_GET["sr"])){
                ?><div class="col-sm-2">
                  <div class="form-group">                                
                      <a href="#" class="btn btn-warning btn-round btn-fab btn-sm" onclick="cargarDatosRegistroProveedorCajaChica(<?=$cod_tcc?>,<?=$cod_cc?>,<?=$cod_dcc?>)">
                        <i class="material-icons" title="Add Proveedor">add</i>
                      </a>
                      <a href="#" class="btn btn-success btn-round btn-fab btn-sm" onclick="actualizarRegistroProveedorCajaChica(<?=$cod_tcc?>,<?=$cod_cc?>,<?=$cod_dcc?>)">
                        <i class="material-icons" title="Actualizar Proveedor">update</i>
                      </a> 
                  </div>
              </div><?php
              }?>     
                                        
            </div>          
            <div id="div_contenedor_glosa_estadocuenta">                    
                <?php
                if($codigo>0 && ($glosa_comprobante!=null || $glosa_comprobante!="")){?>
                <div class="row">
                  <label class="col-sm-2 col-form-label">Glosa Comprobante</label>
                    <div class="col-sm-8">
                      <div class="form-group">
                        <textarea class="form-control" <?=(isset($_GET["sr"]))?"readonly":"";?> <?=(!isset($_GET["sr"]))?"required":"";?>><?=$glosa_comprobante;?></textarea>
                      </div>
                    </div>
                </div>
                <?php }
                ?>
            </div>
            <div class="row">
                <label class="col-sm-2 col-form-label">Detalle</label>
                <div class="col-sm-8">
                <div class="form-group">
                    <input class="form-control rounded-0" <?=(isset($_GET["sr"]))?"readonly":"";?> name="observaciones" id="observaciones" rows="3" onkeyup="javascript:this.value=this.value.toUpperCase();" value="<?=$observaciones;?>" <?=(!isset($_GET["sr"]))?"required":"";?>/>
                    <!-- <input class="form-control" type="text" name="observaciones" id="observaciones" required="true" value="<?=$observaciones;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/> -->
                </div>
                </div>
            </div> 
            <?php //}else{
              
            //} 
              if(isset($_GET["sr"])){
                ?><input type="hidden" name="sr" id="sr" value="1"/><?php
              }  
              ?>
            <!-- para solicitud de recursos -->             
            <div class="row">              
              <div class="col-sm-12">
                <div class="form-group" align="center">                        
                  <div id="div_contenedor_sol_recursos" >
                    <?php
                    
                    if($codigo>0 && $cod_comprobante>0){                      
                      $archivos_cajachica=verificar_archivos_cajachica($codigo);
                      //sacar codigo de estado de cuenta

                      $sqlEstadoCuenta="SELECT e.cod_comprobantedetalle From estados_cuenta e where e.codigo=$cod_comprobante limit 1"; 
                      
                      //echo $sqlEstadoCuenta;
                      
                      $stmtEstadoCuenta = $dbh->prepare($sqlEstadoCuenta);
                      $stmtEstadoCuenta->execute();                    
                      $resultado=$stmtEstadoCuenta->fetch();
                      $cod_comprobantedetalle=$resultado['cod_comprobantedetalle'];
                      // $sqlDetalleX="SELECT codigo,cod_solicitudrecurso,cod_solicitudrecursodetalle,cod_proveedor,cod_tipopagoproveedor from solicitud_recursosdetalle where cod_estadocuenta=$codigo_estadoCuenta limit 1";        

                      $sqlDetalleX="SELECT sd.codigo,sd.cod_solicitudrecurso,sd.cod_proveedor,sd.cod_tipopagoproveedor 
                      FROM solicitud_recursos s,solicitud_recursosdetalle sd
                      WHERE s.codigo=sd.cod_solicitudrecurso and s.cod_comprobante in (select cd.cod_comprobante from comprobantes_detalle cd where cd.codigo=$cod_comprobantedetalle)";
                      $stmtDetalleX = $dbh->prepare($sqlDetalleX);
                      $stmtDetalleX->execute();                    
                      $resultado=$stmtDetalleX->fetch();
                      $cod_solicitudrecursodetalle_sr=$resultado['codigo'];
                      $cod_solicitudrecurso_sr=$resultado['cod_solicitudrecurso'];  
                      // echo $cod_solicitudrecurso_sr."-";
                      if($cod_solicitudrecurso_sr!=0 && $cod_solicitudrecurso_sr!='' && $cod_solicitudrecurso_sr!=null){?>
                        <a class="btn btn-success" href='<?=$urlSolicitudRecursos;?>?cod=<?=$cod_solicitudrecurso_sr;?>&v_cajachica=10' target="_blank"><i class="material-icons" title="Imprimir Factura">bar_chart</i>Ver Solicitud
                        </a>
                      <?php }
                    }
                    ?>
                  </div>
                  <?php if($codigo>0 && $archivos_cajachica>0){?>
                    <a  title="Subir Archivos Respaldo (shift+r)" href="#modalFile" data-toggle="modal" data-target="#modalFile" class="btn btn-primary btn-sm">Archivos 
                      <i class="material-icons"><?=$iconFile?></i><span id="narch" class="bg-warning estado" ></span>
                    </a>

                  <?php }else{?>
                    <a  title="Subir Archivos Respaldo (shift+r)" href="#modalFile" data-toggle="modal" data-target="#modalFile" class="btn btn-primary btn-sm">Archivos 
                    <i class="material-icons"><?=$iconFile?></i><span id="narch" class="bg-warning" ></span>
                  </a>
                  <?php } ?>
                </div>
              </div>              

            </div>
  			  </div>
  			  <div class="card-footer ml-auto mr-auto">
  				<button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
  				<a href="<?=$urlListDetalleCajaChica;?>&codigo=<?=$cod_cc;?>&cod_tcc=<?=$cod_tcc?>" class="<?=$buttonCancel;?>"><i class="material-icons" title="Volver">keyboard_return</i> Volver </a>
  			  </div>
  			</div>
        <!-- archivos adjuntos -->
       <?php  require_once 'caja_chica/modal_subirarchivos.php';?>
		  </form>
		</div>
	
	</div>
</div>

<!-- carga de proveedores -->
<div class="cargar">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold">Procesando Datos</h4>
     <p class="text-white">Aguard&aacute; un momento por favor</p>  
  </div>
</div>
<div class="cargar-ajax d-none">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold" id="texto_ajax_titulo">Procesando Datos</h4>
     <p class="text-white">Aguard&aacute; un momento por favor</p>  
  </div>
</div>
<div class="modal fade modal-arriba modal-primary" id="modalAgregarProveedor" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content card">
            <div class="card-header card-header-warning card-header-icon">
                <div class="card-icon">
                    <i class="material-icons text-dark">ballot</i>
                 </div>
                  <h4 class="card-title">Proveedor</h4>
            </div>
            <div class="card-body">
                 <div id="datosProveedorNuevo">
                   
                 </div> 
                <div class="form-group float-right">
                        <button type="button" onclick="guardarDatosProveedorCajaChica()" class="btn btn-info btn-round">Agregar</button>
                </div>
          </div>
      </div>  
    </div>
  </div>

<?php
  if($codigo>0){
    $tipo_distribucion=verificamos_distribucion_cajachica($codigo);
    if($tipo_distribucion!='0'){?>
      <script>
        // $("#nueva_distribucion").val('<?=$tipo_distribucion?>');
        $("#boton_titulodist").html('<?=$tipo_distribucion?>');
        // $("#n_distribucion").val($("#nueva_distribucion").val());    
        $("#distrib_icon").addClass("estado");
      </script>
    <?php }
  }
?>
<script>
  //el numero de cuenta comporbamos si empieza con 2
  $( "#cuenta_auto" ).blur(function() {
      var nro_cuenta = document.getElementById("cuenta_auto").value; 
      var cod_cuenta_id = document.getElementById("cuenta_auto_id").value; 
      if(nro_cuenta.substr(0,1)==2){//comprobamos el primer digito de la cuenta 
        for(var j = 0; j < array_configuracion_estadocuenta.length; j++){
          var dato = Object.values(array_configuracion_estadocuenta[j]);
          // alert(dato+"-"+cod_cuenta_id);
          if(dato==cod_cuenta_id){
            $(".retencion_sin_gastos").show();
            break;
          }else{
            $(".retencion_sin_gastos").hide();    
          }
        }
      }else{
        $(".retencion_sin_gastos").hide();
        // $(".contenedor_contra_cuenta").hide();
      }
    });  
</script>
<script>$('.selectpicker').selectpicker("refresh");</script>
<!-- verifica que no vea campos vacios -->
<script type="text/javascript">
  function valida(f) {
    var nro_cuenta2 = document.getElementById("cuenta_auto").value; 
    var cod_cuenta_id = document.getElementById("cuenta_auto_id").value; 
    var ok = true;
    if(nro_cuenta2.substr(0,1)!=5){//comprobamos el primer digito de la cuenta 
      if(f.elements["cod_personal"].value == "" && f.elements["proveedores"].value == "")
      {
        var msg = "Rellene el campo 'personal' o 'proveedor'\n";
        ok = false;
      }else{
        if(f.elements["monto"].value == 0)
        {    
              var msg = "El Monto no debe ser menor a '0'...\n";
              ok = false;
        }
      }
    }else{
      if(f.elements["monto"].value == 0)
        {    
              var msg = "El Monto no debe ser menor a '0'...\n";
              ok = false;
        }
    }
    if(nro_cuenta2.substr(0,1)==2){//comprobamos el primer digito de la cuenta 
      for(var j = 0; j < array_configuracion_estadocuenta.length; j++){
        var dato = Object.values(array_configuracion_estadocuenta[j]);
        if(dato==cod_cuenta_id){
          if(f.elements["comprobante"].value == "" && f.elements["comprobante"].value == 0)
          {
            var msg = "Estado de Cuenta No seleccionada.'\n";
            ok = false;
          }
        }
      }
    }
    if(!(f.elements["proveedores"].value>0)){//comprobamos el primer digito de la cuenta 
      var msg = "Debe seleccionar un proveedor.'\n";
      ok = false;
    }

    var dato = document.getElementById("cuenta_auto_id").value;
    if(dato==0){
        ok=false;
        msg="Cuenta Contable Incorrecta...!";
    }     
    if(ok == false)      
      Swal.fire("Informativo!",msg, "warning");
    return ok;
  }

</script>

<?php 
  require_once 'modal.php';
  require_once 'solicitudes/modal.php';
?>
