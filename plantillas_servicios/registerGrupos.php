<?php
session_start();
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../styles.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';
setlocale(LC_TIME, "Spanish");
$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

if(isset($_GET['q'])){
	$q=$_GET['q'];
	$s=$_GET['s'];
	$u=$_GET['u'];
}else{
	$q=0;
	$s=0;
	$u=0;
}
$globalNombreGestion=$_SESSION["globalNombreGestion"];
$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalNombreUnidad=$_SESSION['globalNombreUnidad'];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];
$contadorRegistros=0;
?>
<script>
	numFilas=<?=$contadorRegistros;?>;
	cantidadItems=<?=$contadorRegistros;?>;
</script>

<?php
$fechaActual=date("Y-m-d");
$dbh = new Conexion();
if(isset($_GET['cod'])){
	$codigo=$_GET['cod'];
}else{
	$codigo=0;
}
			$stmt = $dbh->prepare("SELECT p.*,e.nombre as estado_plantilla, u.abreviatura as unidad,a.abreviatura as area from plantillas_servicios p,unidades_organizacionales u, areas a,estados_plantillascosto e
  where p.cod_unidadorganizacional=u.codigo and p.cod_area=a.codigo and e.codigo=p.cod_estadoplantilla and p.codigo='$codigo' order by codigo");
			$stmt->execute();
			$stmt->bindColumn('codigo', $codigoX);
            $stmt->bindColumn('nombre', $nombreX);
            $stmt->bindColumn('abreviatura', $abreviaturaX);
            $stmt->bindColumn('cod_unidadorganizacional', $codUnidadX);
            $stmt->bindColumn('cod_area', $codAreaX);
            $stmt->bindColumn('area', $areaX);
            $stmt->bindColumn('unidad', $unidadX);
            $stmt->bindColumn('estado_plantilla', $estadoX);
            $stmt->bindColumn('dias_auditoria', $diasAuditoriaX);
            $stmt->bindColumn('fecha_registro', $fechaRegistro);
            $stmt->bindColumn('utilidad_minima', $utilidadMinima);
            $stmt->bindColumn('anios', $anios);
?>
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
<form id="formRegDet" class="form-horizontal" action="saveEdit.php" method="post">
<div class="content">
	<div id="contListaGrupos" class="container-fluid">
			<input type="hidden" name="cantidad_filas" id="cantidad_filas" value="<?=$contadorRegistros;?>">
			<input type="hidden" name="cod_plantilla" id="cod_plantilla" value="<?=$codigo?>">
			<input type="hidden" name="q" id="q" value="<?=$q?>">
			<input type="hidden" name="s" id="s" value="<?=$s?>">
			<input type="hidden" name="u" id="u" value="<?=$u?>">
			<div class="card">
				<div class="card-header <?=$colorCard;?> card-header-text">
					<div class="card-text fondo-boton">
					  <h4 class="card-title">Registrar <?=$moduleNameSingular;?></h4>
					</div>
				</div>
				<div class="card-body ">
                     <div class="row">
					<?php while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                        $alumnos_ibnorcaPlan=obtenerCantidadTotalPersonalPlantilla($codigo);
                        if($alumnos_ibnorcaPlan==0||$alumnos_ibnorcaPlan==""||$alumnos_ibnorcaPlan==NULL){
                        	$alumnos_ibnorcaPlan=1;
                        }
						?>
					<input class="form-control" type="hidden" name="cod_unidad" value="<?=$codUnidadX?>" id="cod_unidad" readonly/>
					<input class="form-control" type="hidden" name="cod_area" value="<?=$codAreaX?>" id="cod_area" readonly/>
					<input class="form-control" type="hidden" name="alumnos_ibnorca" value="<?=$alumnos_ibnorcaPlan?>" id="alumnos_ibnorca"/>
						<div class="col-sm-2">
							<div class="form-group">
						  		<label class="bmd-label-static">Nombre</label>
					  			<input class="form-control" type="text" name="nombre" value="<?=$nombreX?>" id="nombre"/>
							</div>
						</div>

						<div class="col-sm-1">
							<div class="form-group">
						  		<label class="bmd-label-static">Abreviatura</label>
						  		<input class="form-control" type="text" name="abreviatura" value="<?=$abreviaturaX?>" id="abreviatura"/>
							</div>
						</div>
						<!--<div class="col-sm-2">
							<div class="form-group has-success">
						  		<label class="bmd-label-static">D&iacute;as Auditor&iacute;a</label>-->
						  		<input class="form-control" type="hidden" min="1" name="dias_auditoria" value="<?=$diasAuditoriaX?>" id="dias_auditoria"/>
							<!--</div>
						</div>-->
						<div class="col-sm-2">
							<div class="form-group has-success">
						  		<label class="bmd-label-static">Fecha Registro</label>
						  		<input class="form-control" type="text" readonly name="fecha_registro" value="<?=$fechaRegistro?>" id="fecha_auditoria"/>
							</div>
						</div>
                        <div class="col-sm-2">
							<div class="form-group">
						  		<label class="bmd-label-static">Estado</label>
						  		<input class="form-control" type="text" readonly name="estado_plan" value="<?=$estadoX?>" id="estado_plan"/>
							</div>
						</div>
						<div class="col-sm-1">
				        	<div class="form-group">
						  		<label class="bmd-label-static">UT. M&iacute;n %</label>
						  		<input class="form-control" type="text" name="utilidad_minima" value="<?=$utilidadMinima?>" id="utilidad_minima" readonly/>
							</div>
				      	</div>
				      	<div class="col-sm-1">
				        	<div class="form-group">
						  		<label class="bmd-label-static">A&ntilde;os</label>
						  		<input class="form-control" type="text" name="anios_plan" value="<?=$anios?>" id="anios_plan" readonly/>
							</div>
				      	</div>
						<div class="col-sm-1">
							<div class="form-group">
						  		<label class="bmd-label-static">Unidad</label>
						  		<input class="form-control" type="text" name="unidad" value="<?=$unidadX?>" id="unidad" readonly/>
							</div>
						</div>

						<div class="col-sm-1">
				        	<div class="form-group">
						  		<label class="bmd-label-static">Area</label>
						  		<input class="form-control" type="text" name="area" value="<?=$areaX?>" id="area" readonly/>
							</div>
				      	</div>
				      	<?php
                          if($codAreaX==39){
                           $valConf=17;
                           $valConf2=21;
                          }else{
                            if($codAreaX==38){
                              $valConf=18;
                              $valConf2=22;
                           }else{
                           	  $valConf=17;
                           	  $valConf2=21;
                           }	
                          }
				      	 ?>
				      	<div class="col-sm-1 float-right">
							<div class="">
						  		<a href="#" title="Ayuda" class="btn btn-default btn-fab btn-round" onclick="ayudaPlantilla()"><span class="material-icons">help_outline</span></a>
							</div>
						</div>
				      </div>

				      	<?php } ?>
				</div>
			</div>
			<input type="hidden" name="cod_mescurso" id="cod_mescurso" value="<?=obtenerValorConfiguracion($valConf)?>">
           <div class="row">
             <div class="col-sm-8">
			  <div class="card">
				<div class="card-header <?=$colorCard;?> card-header-text">
					<a href="#" onclick="cambiarDivPlantilla('fiel','list_servicios','list_personal')">
					<div id="button_fiel" class="card-text fondo-boton fondo-boton-active">
					  <h4 class="font-weight-bold">Detalles</h4>
					</div>
				    </a>
					<a href="#" onclick="cambiarDivPlantilla('list_servicios','fiel','list_personal')">
					 <div id="button_list_servicios" class="card-text fondo-boton">
					    <h4 class="font-weight-bold">Servicios</h4>
					</div>
				    </a>
				    <a href="#" onclick="cambiarDivPlantilla('list_personal','list_servicios','fiel')">
					 <div id="button_list_personal" class="card-text fondo-boton">
					    <h4 class="font-weight-bold">Personal</h4>
					</div>
				    </a>
				</div>

				<div class="card-body">
					<fieldset id="fiel" class="" style="width:100%;border:0;">
							<button title="Agregar (shift+n)" type="button" name="add" class="btn btn-warning btn-round btn-fab" onClick="addGrupoPlantilla(this)">
                      		  <i class="material-icons">add</i>
		                    </button>  
						<div id="div">	
							
							<div class="h-divider">
	        				</div>
		 					
	 					</div>
	 					<?php
                       $stmt = $dbh->prepare("SELECT p.codigo, p.cod_tiposervicio, p.nombre,p.abreviatura,p.cod_plantillaservicio from plantillas_gruposervicio p where p.cod_plantillaservicio=$codigo order by p.codigo");
                         $stmt->execute();
                         $idFila=1;
                         while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                           $codigoCostoX=$row['codigo'];
                          $codTipoCostoX=$row['cod_tiposervicio'];
                          $nombreCostoX=$row['nombre'];
                          $abreviaturaCostoX=$row['abreviatura'];
                          $codPlantillaCostoX=$row['cod_plantillaservicio'];
                          ?>
                          <script>numFilas++;cantidadItems++;</script>
                          <script>var ndet=[];itemDetalle.push(ndet);</script>
						 <?php
						      $stmt2 = $dbh->prepare("SELECT * FROM plantillas_gruposerviciodetalle where cod_plantillagruposervicio=$codigoCostoX");
				              $stmt2->execute();
				              $idFilas=0;
				           
						?>
                          <div id="div<?=$idFila?>" class="col-md-12">
	<div class="row">

		<div class="col-sm-3">
        	<div class="form-group">
	        <select class="selectpicker form-control form-control-sm" name="tipo_costo<?=$idFila;?>" id="tipo_costo<?=$idFila;?>" data-style="<?=$comboColor;?>" required>
	        	                <option disabled value="">Tipo</option>
			  	              <?php        
                                if($codTipoCostoX==1){
                                ?><option value="1" selected>Fijo</option>
                                  <option value="2">Variable</option>
                                <?php
                                }else{
                                 ?><option value="1">Fijo</option>
                                  <option value="2" selected>Variable</option>
                                <?php
                                }
			  	              ?>
					
			</select>
			</div>
      	</div>
		<div class="col-sm-5">
            <div class="form-group">
            	<label for="nombre_grupo<?=$idFila;?>" class="bmd-label-floating">Nombre de grupo</label>			
          		<input class="form-control" type="text" value="<?=$nombreCostoX?>" name="nombre_grupo<?=$idFila;?>" id="nombre_grupo<?=$idFila;?>" onkeyup="mostrarDetalle(<?=$idFila;?>);" required>	
			</div>
      	</div>

		<div class="col-sm-2">
            <div class="form-group">
            	<label for="abreviatura_grupo<?=$idFila;?>" class="bmd-label-floating">Abreviatura</label>			
          		<input class="form-control" type="text" value="<?=$abreviaturaCostoX?>" name="abreviatura_grupo<?=$idFila;?>" id="abreviatura_grupo<?=$idFila;?>" required> 	
			</div>
      	</div>
		<div class="col-sm-2">
		  <div class="btn-group">
		  	<a href="#" id="boton_det<?=$idFila;?>" onclick="listDetalle(<?=$idFila;?>);" class="btn btn-just-icon btn-primary btn-link">
               <i class="material-icons">view_list</i><span id="ndet<?=$idFila;?>" class="count bg-warning">0</span>
             </a>
			<a rel="tooltip" href="#" class="btn btn-just-icon btn-danger btn-link" id="boton_remove<?=$idFila;?>" onclick="minusGrupoPlantilla('<?=$idFila;?>');">
            	<i class="material-icons">remove_circle</i>
	        </a>
	        <a rel="tooltip" id="boton_det_list<?=$idFila;?>" href="#" class="btn btn-just-icon btn-info btn-link" onclick="mostrarDetalle('<?=$idFila;?>');">
            	<i class="material-icons">remove_red_eye</i>
	        </a>
	      </div>  
		</div>

	</div>
	<div class="h-divider"></div>
</div>


                          <?php
                    while ($rowDet = $stmt2->fetch(PDO::FETCH_ASSOC)) {
				                    $codigo_partida=$rowDet['cod_partidapresupuestaria'];

				                    $nombrePartida=namePartidaPres($rowDet['cod_partidapresupuestaria']);         

				                    $tipo=$rowDet['tipo_calculo'];
				                    $monto_i=$rowDet['monto_local'];
				                    $monto_fi=$rowDet['monto_externo'];
				                    $monto_cal=$rowDet['monto_calculado'];
				                    $idFilas=$idFilas+1;
				                    ?><script>abrirDetalleCosto(<?=$idFila?>,'<?=$codigo_partida?>','<?=$nombrePartida?>','<?=$tipo?>',<?=$monto_i?>,<?=$monto_fi?>,<?=$monto_cal?>);</script><?php
			  	              }
			  	      ?><script>$("#cantidad_filas").val(<?=$idFila?>);</script><?php              
                          $idFila++;
                      }
	 					?>
		            </fieldset>

		            <fieldset id="list_servicios" class="d-none col-sm-12">
		            	<center>
		            	<div class="col-sm-12">	
		            		<div class="row">
                                  <label class="col-sm-3 col-form-label">Lista de Servicios</label>
                                      <div class="col-sm-6">
        	                            <div class="form-group">
	                                   <select class="selectpicker form-control" data-live-search="true" name="servicios_codigo" id="servicios_codigo" data-style="fondo-boton">
	        	                          <option disabled selected value="">--Seleccione--</option>
			  	              <?php
			  	              if($codAreaX==39){
			  	              	$codigoAreaServ=108;
			  	              }else{
			  	              	if($codAreaX==38){
			  	              		$codigoAreaServ=109;
			  	              	}else{
			  	              		$codigoAreaServ=0;
			  	              	}
			  	              }
                           $stmt3 = $dbh->prepare("SELECT idclaservicio,descripcion,codigo from cla_servicios where (codigo_n1=108 or codigo_n1=109) and vigente=1 and codigo_n1=$codigoAreaServ");
                         $stmt3->execute();
                         while ($rowServ = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                          $codigoServX=$rowServ['idclaservicio'];
                          $nombreServX=$rowServ['descripcion'];
                          $abrevServX=$rowServ['codigo'];
                          ?><option value="<?=$codigoServX;?>"><?=$abrevServX?> - <?=$nombreServX?></option><?php 
                       } 
                         ?>  
					
			                         </select>
			                      </div>
			                     </div>
			                     <div class="col-sm-3">
			                     	<div class="form-group">
			                           <a href="#" class="btn btn-danger fondo-boton fondo-boton-active" onclick="guardarServicioPlantilla()">Agregar</a>
			                           <a href="#" class="btn btn-danger fondo-boton fondo-boton-active btn-fab" onclick="actualizarTablaClaServicios()"><i class="material-icons">refresh</i></a>  
			                         </div>   
			                     </div>
      	                     </div>
      	                     <div class="row">
                                <label class="col-sm-3 col-form-label">Observacion</label>
                                <div class="col-sm-9">
                                  <div class="form-group">
                                  	<textarea type="text" id="observacion_servicio" class="form-control"></textarea>
                                  </div>
                                 </div>
                              </div>
                              <!--<div class="row">
                                <label class="col-sm-3 col-form-label">Cantidad</label>
                                <div class="col-sm-3">
                                  <div class="form-group">
                                  	<input type="number" id="cantidad_servicio" min="1" class="form-control">
                                  </div>
                                 </div>
                                 <label class="col-sm-2 col-form-label">Monto</label>
                                <div class="col-sm-4">
                                  <div class="form-group">
                                  	<input type="number" id="monto_servicio" min="0" step="0.001" class="form-control">
                                  </div>
                                 </div>
                              </div>-->
                             <br>
      	                     <div class="row">
      	                     	<table class="table table-bordered table-condensed">
      	                     		<thead>
      	                     			<tr class="fondo-boton">
      	                     				<th>#</th>
      	                     				<th>Codigo</th>
      	                     				<th>Descripci&oacute;n</th>
      	                     				<th>Observaciones</th>
      	                     				<!--<th>Cantidad</th>
      	                     				<th>Monto</th>
      	                     				<th>Total</th>-->
      	                     				<th>Action</th>
      	                     			</tr>
      	                     		</thead>
      	                     		<tbody id="tabla_servicios">
      	                     			<?php 
      	                     			$sql11="SELECT s.*,c.descripcion,c.codigo as servicio_cod from plantillas_servicios_tiposervicio s,cla_servicios c where s.cod_plantillaservicio=$codigo and s.cod_claservicio=c.idclaservicio order by c.codigo";
                                        $stmt11 = $dbh->prepare($sql11);
                                        $stmt11->execute();
                                        $index11=1;$total11=0;
 while ($rowServ = $stmt11->fetch(PDO::FETCH_ASSOC)) {
    $descripcion11=$rowServ['descripcion'];
    $servicio_cod11=$rowServ['servicio_cod'];
    $observaciones11=$rowServ['observaciones'];
    $cantidad11=$rowServ['cantidad'];
    $monto11=$rowServ['monto'];
    $codigo11=$rowServ['codigo'];
    $montoTotal11=$cantidad11*$monto11;
    $total11+=$montoTotal11;
    ?>
  <tr>
    <td><?=$index11?></td>
    <td><?=$servicio_cod11?></td>
    <td><?=$descripcion11?></td>
    <td><?=$observaciones11?></td>
    <!--<td class="text-right"><?=$cantidad11?></td>
    <td class="text-right"><?=number_format($monto11, 2, '.', ',');?></td>
    <td class="text-right"><?=number_format($montoTotal11, 2, '.', ',');?></td>-->
    <td><a href="#" class="<?=$buttonDelete;?> btn-link btn-sm" onclick="removeServicioPlantilla(<?=$codigo11?>); return false;">
                              <i class="material-icons"><?=$iconDelete;?></i>
                            </a>
    </td>
  </tr>
    <?php
    $index11++;
}?>
  <!--<tr class="font-weight-bold">
    <td colspan="6" class="text-center">TOTAL</td>
    <td class="text-right"><?=number_format($total11, 2, '.', ',');?></td>
    <td></td>
  </tr>-->

      	                     		</tbody>
      	                     	</table>
      	                     </div>
      	                  </div>   
		            	</center>
		            </fieldset>
                    <fieldset id="list_personal" class="d-none col-sm-12">
                    	<center>
		            	<div class="col-sm-12">
		            	<div class="row">
			                    <div class="form-group">
			                           <a href="#" class="btn btn-danger fondo-boton fondo-boton-active" onclick="guardarAuditoresPlantilla()">GUARDAR PERSONAL</a> 
			                       </div>   
      	                     </div>	
                             <div class="row" id="tabla_personal">
      	                     	<table class="table table-bordered table-condensed">
      	                     		<thead>
      	                     			<tr class="fondo-boton">
      	                     				<th>#</th>
      	                     				<th>Descripci&oacute;n</th>
      	                     				<th>Cantidad</th>
      	                     				<th>D&iacute;as</th>
      	                     				<th>Monto Bolivia</th>
      	                     				<th>Total Bolivia</th>
      	                     				<th>Monto Extranjero</th>
      	                     				<th>Total Extranjero</th>
      	                     				<th>Quitar</th>
      	                     			</tr>
      	                     		</thead>
      	                     		<tbody>
      	                     			<?php $index11=1;$total11=0; $total11Ext=0;
      	                     			 $stmt3 = $dbh->prepare("SELECT codigo,nombre,abreviatura from tipos_auditor where cod_estadoreferencial=1");
                                          $stmt3->execute();
                                          while ($rowServ = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                                          $codigo11=$rowServ['codigo'];		
                                          $descripcion11=$rowServ['nombre'];

                                        $sql11="SELECT s.*,c.nombre,c.codigo as auditor_cod from plantillas_servicios_auditores s,tipos_auditor c where s.cod_plantillaservicio=$codigo and s.cod_tipoauditor=c.codigo and c.codigo=$codigo11";
                                        $stmt11 = $dbh->prepare($sql11);
                                        $stmt11->execute();
                                        
                                        $cantidad11=0;$dias11=$diasAuditoriaX;$monto11=0;$monto11Ext=0;
                                        $bgFila="";$idRemove=0;
                                       while ($rowServ11 = $stmt11->fetch(PDO::FETCH_ASSOC)) {
                                       	     $idRemove=$rowServ11['codigo'];
                                             $cantidad11=$rowServ11['cantidad'];
                                             $dias11=$rowServ11['dias'];
                                             $monto11=$rowServ11['monto'];
                                             $monto11Ext=$rowServ11['monto_externo'];
                                             $bgFila="bg-warning";
                                       }
                                          $montoTotal11=$cantidad11*$monto11*$dias11;
                                          $montoTotal11Ext=$cantidad11*$monto11Ext*$dias11;
                                          $total11+=$montoTotal11;
                                          $total11Ext+=$montoTotal11Ext;
                                          ?>
                                       <tr class="<?=$bgFila?>">
                                         <td><input type="hidden" id="codigo_personal<?=$index11?>" value="<?=$codigo11?>"><?=$index11?></td>
                                         <td><?=$descripcion11?></td>                                         
                                         <td class="text-right"><input type="number" min="0" id="cantidad_personal<?=$index11?>" class="form-control text-right" value="<?=$cantidad11?>" onkeyup="calcularMontoFilaPersonalServicio(<?=$index11?>)" onkeydown="calcularMontoFilaPersonalServicio(<?=$index11?>)" onchange="calcularMontoFilaPersonalServicio(<?=$index11?>)"></td>
                                         <td class="text-right"><input type="number" min="0" max="<?=$diasAuditoriaX?>" id="dias_personal<?=$index11?>" class="form-control text-right" value="<?=$dias11?>" onkeyup="calcularMontoFilaPersonalServicio(<?=$index11?>)" onkeydown="calcularMontoFilaPersonalServicio(<?=$index11?>)" onchange="calcularMontoFilaPersonalServicio(<?=$index11?>)"></td>
                                         <td class="text-right"><input type="number" step="0.01" min="0" id="monto_personal<?=$index11?>" class="form-control text-right" value="<?=number_format($monto11, 2, '.', '');?>" onkeyup="calcularMontoFilaPersonalServicio(<?=$index11?>)" onkeydown="calcularMontoFilaPersonalServicio(<?=$index11?>)" onchange="calcularMontoFilaPersonalServicio(<?=$index11?>)"></td>
                                         <td class="text-right"><input type="number" step="0.01" readonly min="0" id="total_personal<?=$index11?>" class="form-control text-right" value="<?=number_format($montoTotal11, 2, '.', '');?>"></td>
                                         <td class="text-right"><input type="number" step="0.01" min="0" id="monto_personalext<?=$index11?>" class="form-control text-right" value="<?=number_format($monto11Ext, 2, '.', '');?>" onkeyup="calcularMontoFilaPersonalServicio(<?=$index11?>)" onkeydown="calcularMontoFilaPersonalServicio(<?=$index11?>)" onchange="calcularMontoFilaPersonalServicio(<?=$index11?>)"></td>
                                         <td class="text-right"><input type="number" step="0.01" readonly min="0" id="total_personalext<?=$index11?>" class="form-control text-right" value="<?=number_format($montoTotal11Ext, 2, '.', '');?>"></td>
                                         <td>
                                           <?php 
                                           if($idRemove!=0){
                                             ?>
                                            <a href="#" class="<?=$buttonDelete;?> btn-link btn-sm" onclick="removeAuditorPlantilla(<?=$idRemove?>); return false;">
                                                                    <i class="material-icons"><?=$iconDelete;?></i>
                                              </a>
                                             <?php
                                           }
                                           ?>                                         	
                                          </td>
                                        </tr>
                                          <?php
                                          $index11++;
                                      }?>
                                      <tr class="font-weight-bold">
                                         <td colspan="5" class="text-center">TOTAL</td>
                                         <td class="text-right" id="total_personalservicio"><?=number_format($total11, 2, '.', ',');?></td>
                                         <td></td>
                                         <td class="text-right" id="total_personalservicioext"><?=number_format($total11Ext, 2, '.', ',');?></td>
                                         <td></td>
                                       </tr>
      	                     		</tbody>
      	                     	</table>
      	                     	<input type="hidden" id="cantidad_filaspersonal" value="<?=$index11?>">
      	                     </div>
      	                     <div class="row float-right">
			                     	<div class="form-group">
			                           <a href="#" class="btn btn-danger fondo-boton fondo-boton-active" onclick="guardarAuditoresPlantilla()">GUARDAR PERSONAL</a> 
			                         </div>   
      	                     </div>
      	                  </div>   
		            	</center>
                    </fieldset>
				  	<div class="card-footer fixed-bottom">

						<button type="submit" class="<?=$buttonMorado;?> fondo-boton fondo-boton-active">Guardar</button>
						
						<?php 
                        if(isset($_GET['q'])){
                         ?><a href="../<?=$urlList;?>&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>" class="<?=$buttonCancel;?> fondo-boton">Volver</a><?php
                        }else{
                         ?><a href="../<?=$urlList;?>" class="<?=$buttonCancel;?> fondo-boton">Volver</a><?php
                        }
						?>				
				  	</div>
				 </div>
			    </div>			
               </div>
               <div class="col-sm-4">
            	<div class="card">
				<div class="card-header <?=$colorCard;?> card-header-text">
					<div class="card-text" id="cabezadetalle">
					  <h6 class="card-title">Detalle</h6>
					</div>
				</div>
				<div class="card-body">
					<table class="table table-condensed table-striped text-small">
						<thead>
							<tr>
								<th>Partida</th>
								<th>Tipo</th>
								<th class="text-right">M Global</th>
								<th class="text-right">M x Persona</th>
							</tr>
						</thead>
						<tbody id="cuerpoDetalle">
						</tbody>

					</table>
				 </div>
			    </div>	
               </div>
            </div>
	</div>
</div>
</form>

<?php
require_once 'modal.php';
?>
