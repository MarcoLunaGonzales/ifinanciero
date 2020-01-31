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
?>
<div class="cargar">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold">Procesando Datos</h4>
     <p class="text-white">Aguard&aacute; un momento por favor</p>  
  </div>
</div>
<div class="cargar-ajax d-none">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold">Procesando Datos</h4>
     <p class="text-white">Aguard&aacute; un momento por favor</p>  
  </div>
</div>
<form id="formDetTcp" class="form-horizontal" action="saveEdit.php" method="post">
<div class="content">
	<div id="contListaGrupos" class="container-fluid">
			<input type="hidden" name="cantidad_filas" id="cantidad_filas" value="<?=$contadorRegistros;?>">
			<input type="hidden" name="cod_plantilla" id="cod_plantilla" value="<?=$codigo?>">
			<div class="card">
				<div class="card-header <?=$colorCard;?> card-header-text">
					<div class="card-text fondo-boton">
					  <h4 class="card-title">Registrar <?=$moduleNameSingular;?></h4>
					</div>
				</div>
				<div class="card-body ">
                     <div class="row">
					<?php while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {?>
					<input class="form-control" type="hidden" name="cod_unidad" value="<?=$codUnidadX?>" id="cod_unidad" readonly/>
					<input class="form-control" type="hidden" name="cod_area" value="<?=$codAreaX?>" id="cod_area" readonly/>
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
						<div class="col-sm-2">
							<div class="form-group has-success">
						  		<label class="bmd-label-static">D&iacute;as Auditor&iacute;a</label>
						  		<input class="form-control" type="number" min="1" name="dias_auditoria" value="<?=$diasAuditoriaX?>" id="dias_auditoria"/>
							</div>
						</div>
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
				      	<div class="col-sm-1 float-right">
							<div class="">
						  		<a href="#" title="Ayuda" class="btn btn-default btn-fab btn-round" onclick="ayudaPlantilla()"><span class="material-icons">help_outline</span></a>
							</div>
						</div>
				      </div>

				      	<?php } ?>
				</div>
			</div>
           <div class="row">
             <div class="col-sm-12">
			  <div class="card">
				<div class="card-header <?=$colorCard;?> card-header-text">
					<a href="#" onclick="cambiarDivPlantilla('fiel','list_servicios')">
					<div id="button_fiel" class="card-text fondo-boton fondo-boton-active">
					  <h4 class="font-weight-bold">Detalles</h4>
					</div>
				    </a>
					<a href="#" onclick="cambiarDivPlantilla('list_servicios','fiel')">
					 <div id="button_list_servicios" class="card-text fondo-boton">
					    <h4 class="font-weight-bold">Servicios</h4>
					</div>
				    </a>
				</div>

				<div class="card-body">
					<fieldset id="fiel" class="" style="width:100%;border:0;">
							<button title="Agregar (shift+n)" type="button" name="add" class="btn btn-warning btn-round btn-fab" onClick="addDetallePlantilla(this)">
                      		  <i class="material-icons">add</i>
		                    </button>  
						<div id="div">	
							
							<div class="h-divider">
	        				</div>
		 					
	 					</div>
	 					<?php
                       $stmt = $dbh->prepare("SELECT c.codigo as codCuenta,p.cod_partidapresupuestaria,p.codigo, p.cod_tipo, p.glosa,p.cantidad,p.unidad,p.monto_unitario,p.monto_total,p.cod_plantillatcp,c.nombre from plantillas_servicios_detalle p,plan_cuentas c where p.cod_plantillatcp=$codigo and p.cod_cuenta=c.codigo order by p.codigo");
                         $stmt->execute();
                         $idFila=1;
                         while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                          $codigoCostoX=$row['codigo'];
                          $codTipoCostoX=$row['cod_tipo'];
                          $nombreCostoX=$row['glosa'];
                          $cantidadCostoX=$row['cantidad'];
                          $unidadCostoX=$row['unidad'];
                          $montoTotalCostoX=$row['monto_total'];
                          $montoUnitarioCostoX=$row['monto_unitario'];
                          $codPlantillaCostoX=$row['cod_plantillatcp'];
                          $nombreCuentaX=trim($row['nombre']);
                          $codCuentaX=$row['codCuenta'];
                          $codPartidaX=$row['cod_partidapresupuestaria'];
                          ?>
                          <script>numFilas++;cantidadItems++;</script>
                          <script>var ndet=[];itemDetalle.push(ndet);</script>
						 <?php
						      $stmt2 = $dbh->prepare("SELECT * FROM plantillas_grupocostodetalle where cod_plantillagrupocosto=$codigoCostoX");
				              $stmt2->execute();
				              $idFilas=0;
				           
						?>
                          <div id="div<?=$idFila?>" class="col-md-12">
	<div class="row">

		<div class="col-sm-1">
        	<div class="form-group">
	        <select class="selectpicker form-control form-control-sm" name="tipo_costo<?=$idFila;?>" id="tipo_costo<?=$idFila;?>" data-style="fondo-boton-active" onchange="mostrarUnidadDetalle(<?=$idFila;?>)" required>
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
		<div class="col-sm-2">
            <div class="form-group">
            	<label for="detalle_plantilla<?=$idFila;?>" class="bmd-label-floating">Detalle</label>			
          		<input class="form-control" type="text" value="<?=$nombreCostoX?>" name="detalle_plantilla<?=$idFila;?>" id="detalle_plantilla<?=$idFila;?>" required>	
			</div>
      	</div>
        <div class="col-sm-2">
        	<div class="form-group">
	        <select class="selectpicker form-control form-control-sm" name="partida_presupuestaria<?=$idFila;?>" id="partida_presupuestaria<?=$idFila;?>" data-style="fondo-boton" onchange="mostrarCuentasPartida2(<?=$idFila?>);" required>
	        	     <option disabled value="">Partidas</option>
			  	              <?php
                           $stmt2 = $dbh->prepare("SELECT p.codigo, p.nombre, p.observaciones from partidas_presupuestarias p where p.cod_estadoreferencial=1 order by p.codigo");
                         $stmt2->execute();
                         while ($rowPartida = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                          $codigoParX=$rowPartida['codigo'];
                          $obsX=$rowPartida['observaciones'];
                          $nombreParX=$rowPartida['nombre'];
                          if($codPartidaX==$codigoParX){
                          ?><option selected value="<?=$codigoParX;?>"><?=$nombreParX?></option><?php 

                          }else{
                          ?><option value="<?=$codigoParX;?>"><?=$nombreParX?></option><?php
                          }
                       } 
                         ?>  
					
			</select>
			</div>
      	</div>
      	<div class="col-sm-2">
        	<div class="form-group" id="cuentas_div<?=$idFila?>">
        		<?php $cuentasPartida=obtenerCuentaPlantillaCostos($codPartidaX);
                                ?>
                                  <select class="selectpicker form-control form-control-sm" name="cuenta_plantilladetalle<?=$idFila?>" id="cuenta_plantilladetalle<?=$idFila?>" data-style="fondo-boton-active" required>
                                   <option disabled value="">Cuentas</option>
                                    <?php 
                                     while ($rowCuenta = $cuentasPartida->fetch(PDO::FETCH_ASSOC)) {
                                      $codigoCuentaX=$rowCuenta['cod_cuenta'];
                                      $nombreCuentaX=trim($rowCuenta['nombre']);
                                      if($codCuentaX==$codigoCuentaX){
                                        ?><option selected value="<?=$codigoCuentaX?>"><?=$nombreCuentaX?></option><?php
                                        }else{
                                       ?><option value="<?=$codigoCuentaX?>"><?=$nombreCuentaX?></option><?php
                                        }
                                     }
                                    ?>
                                  </select>
			</div>
      	</div>
		<div class="col-sm-1">
            <div class="form-group">
            	<label for="cantidad_detalleplantilla<?=$idFila;?>" class="bmd-label-floating">Cantidad</label>			
          		<input class="form-control" type="number" min="0" value="<?=$cantidadCostoX?>" name="cantidad_detalleplantilla<?=$idFila;?>" id="cantidad_detalleplantilla<?=$idFila;?>" onkeyup="calcularTotalFilaDetalle(1,<?=$idFila?>)" id="cantidad_detalleplantilla<?=$idFila;?>" required> 	
			</div>
      	</div>
      	<div class="col-sm-1">
            <div class="form-group">
            	<label for="unidad_detalleplantilla<?=$idFila;?>" class="bmd-label-floating">Unidad</label>			
          		<input class="form-control" type="text" value="<?=$unidadCostoX?>" <?=($codTipoCostoX==1)?"readonly":"";?> name="unidad_detalleplantilla<?=$idFila;?>" id="unidad_detalleplantilla<?=$idFila;?>" required> 	
			</div>
      	</div>
      	<div class="col-sm-1">
            <div class="form-group">
            	<label for="monto_detalleplantilla<?=$idFila;?>" class="bmd-label-floating">Precio Unit.</label>			
          		<input class="form-control" type="number" step="0.01" min="0" value="<?=$montoUnitarioCostoX?>" name="monto_detalleplantilla<?=$idFila;?>" onkeyup="calcularTotalFilaDetalle(1,<?=$idFila?>)" id="monto_detalleplantilla<?=$idFila;?>" required> 	
			</div>
      	</div>
      	<div class="col-sm-1">
            <div class="form-group">
            	<label for="monto_total_detalleplantilla<?=$idFila;?>" class="bmd-label-floating">Precio Total</label>			
          		<input class="form-control" type="number" onkeyup="calcularTotalFilaDetalle(2,<?=$idFila?>)"  step="0.01" min="0" value="<?=$montoTotalCostoX?>" name="monto_total_detalleplantilla<?=$idFila;?>" id="monto_total_detalleplantilla<?=$idFila;?>" required> 	
			</div>
      	</div>

		<div class="col-sm-1">
		   <div class="btn-group">
		  	<!--<a title="<?=$nombreCuentaX?>" href="#" id="boton_det<?=$idFila;?>" onclick="listDetallePlantilla(<?=$idFila;?>);" class="btn btn-just-icon btn-primary btn-link">
               <i class="material-icons">view_list</i><span id="ndet<?=$idFila;?>" class="bg-success estado2"></span>
             </a>-->
             <input type="hidden" value="<?=$codPartidaX?>" name="codigo_partidadetalle<?=$idFila;?>" id="codigo_partidadetalle<?=$idFila;?>">
             <input type="hidden" name="codigo_cuentadetalle<?=$idFila;?>" value="<?=$codCuentaX?>" id="codigo_cuentadetalle<?=$idFila;?>"> 	
			<a title="Eliminar Registro" href="#" class="btn btn-just-icon btn-danger btn-link" id="boton_remove<?=$idFila;?>" onclick="minusDetallePlantilla('<?=$idFila;?>');">
            	<i class="material-icons">remove_circle</i>
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
		            	<div class="col-sm-8">	
		            		<div class="row">
                                  <label class="col-sm-3 col-form-label">Lista de Servicios</label>
                                      <div class="col-sm-7">
        	                            <div class="form-group">
	                                   <select class="selectpicker form-control" name="servicios_codigo" id="servicios_codigo" data-style="fondo-boton">
	        	                          <option disabled selected value="">--Seleccione--</option>
			  	              <?php
                           $stmt3 = $dbh->prepare("SELECT idclaservicio,descripcion from claservicios where (idArea=38 or idArea=39) and vigente=1");
                         $stmt3->execute();
                         while ($rowServ = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                          $codigoServX=$rowServ['idclaservicio'];
                          $nombreServX=$rowServ['descripcion'];
                          ?><option value="<?=$codigoServX;?>"><?=$nombreServX?></option><?php 
                       } 
                         ?>  
					
			                         </select>
			                      </div>
			                     </div>
			                     <div class="col-sm-2">
			                     	<div class="form-group">
			                     <a href="#" class="btn btn-danger fondo-boton fondo-boton-active" onclick="guardarServicioPlantilla()">Agregar</a> 
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
                              <div class="row">
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
                              </div>
                             <br>
      	                     <div class="row">
      	                     	<table class="table table-bordered table-condensed">
      	                     		<thead>
      	                     			<tr class="fondo-boton">
      	                     				<th>#</th>
      	                     				<th>Codigo</th>
      	                     				<th>Descripci&oacute;n</th>
      	                     				<th>Observaciones</th>
      	                     				<th>Cantidad</th>
      	                     				<th>Monto</th>
      	                     				<th>Action</th>
      	                     			</tr>
      	                     		</thead>
      	                     		<tbody id="tabla_servicios">
      	                     			<?php 
      	                     			$sql11="SELECT s.*,c.descripcion,c.codigo as servicio_cod from plantillas_servicios_tiposervicio s,claservicios c where s.cod_plantillaservicio=$codigo and s.cod_claservicio=c.idclaservicio";
                                        $stmt11 = $dbh->prepare($sql11);
                                        $stmt11->execute();
                                        $index11=1;
 while ($rowServ = $stmt11->fetch(PDO::FETCH_ASSOC)) {
    $descripcion11=$rowServ['descripcion'];
    $servicio_cod11=$rowServ['servicio_cod'];
    $observaciones11=$rowServ['observaciones'];
    $cantidad11=$rowServ['cantidad'];
    $monto11=$rowServ['monto'];
    $codigo11=$rowServ['codigo'];
    ?>
  <tr>
    <td><?=$index11?></td>
    <td><?=$servicio_cod11?></td>
    <td><?=$descripcion11?></td>
    <td><?=$observaciones11?></td>
    <td class="text-right"><?=$cantidad11?></td>
    <td class="text-right"><?=number_format($monto11, 2, '.', ',');?></td>
    <td><a href="#" class="<?=$buttonDelete;?> btn-link btn-sm" onclick="removeServicioPlantilla(<?=$codigo11?>); return false;">
                              <i class="material-icons"><?=$iconDelete;?></i>
                            </a>
    </td>
  </tr>
    <?php
    $index11++;
}?>
      	                     		</tbody>
      	                     	</table>
      	                     </div>
      	                  </div>   
		            	</center>
		            </fieldset>

				  	<div class="card-footer fixed-bottom">
						<button type="submit" class="<?=$buttonMorado;?> fondo-boton fondo-boton-active">Guardar</button>
						<a href="../<?=$urlList;?>" class="<?=$buttonCancel;?> fondo-boton">Volver</a>

				  	</div>
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
