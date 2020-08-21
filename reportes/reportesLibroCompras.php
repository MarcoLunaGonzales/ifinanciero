<?php
require_once 'conexion.php';
require_once 'comprobantes/configModule.php';
require_once 'styles.php';
require_once 'functions.php';
require_once 'functionsGeneral.php';

$globalUser=$_SESSION["globalUser"];
$globalAdmin=$_SESSION["globalAdmin"];
$globalUnidad=$_SESSION["globalUnidad"];

$globalGestion=$_SESSION["globalGestion"];
$global_mes=$_SESSION["globalMes"];

$dbh = new Conexion();

?>

<div class="content">
	<div class="container-fluid">
		<!-- <div style="overflow-y:scroll; ">			 		 -->
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons"><?=$iconCard;?></i>
                  </div>
                  <h4 class="card-title">Reporte Libro Compras</h4>
                </div>
                <form class="" action="<?=$urlReporteCompras?>" target="_blank" method="POST">
                <div class="card-body">
                	<div class="row">
		                <label class="col-sm-2 col-form-label">Oficina</label>
		                <div class="col-sm-9">
		                	<div class="form-group">
		                		<div id="">		
		                			<?php
									$sqlUO="SELECT uo.codigo, uo.nombre,uo.abreviatura from unidades_organizacionales uo order by 2";
									$stmt = $dbh->prepare($sqlUO);
									$stmt->execute();
									?>
										<select class="selectpicker form-control form-control-sm" name="unidad[]" id="unidad" data-style="select-with-transition" multiple data-actions-box="true" required data-live-search="true">
										    <?php 
										    	while ($row = $stmt->fetch()){ 
											?>
										      	 <option value="<?=$row["codigo"];?>" data-subtext="<?=$row["nombre"];?>" <?=($row["codigo"]==$globalUnidad)?"selected":""?> ><?=$row["abreviatura"];?></option>
							    	<?php 
										 		} 
								 	?>
										</select>
		                		</div>
		                     </div>
		                </div>				             
                  	</div><!--div row-->
                	<div class="row">
		                <label class="col-sm-2 col-form-label">Gestión</label>
		                <div class="col-sm-4">
		                	<div class="form-group">
		                		<select name="gestiones" id="gestiones" onChange="ajax_mes_de_gestion(this);" class="selectpicker form-control form-control-sm" data-style="btn btn-primary"  data-show-subtext="true" data-live-search="true" required="true">
                                    <option value=""></option>
                                    <?php 
                                    $query = "SELECT codigo,nombre from gestiones where cod_estado=1 ORDER BY nombre desc";
                                    $stmt = $dbh->query($query);
                                    while ($row = $stmt->fetch()){ ?>
                                        <option value="<?=$row["codigo"];?>" <?=($row["codigo"]==$globalGestion)?"selected":""?> ><?=$row["nombre"];?></option>
                                    <?php } ?>
                                </select>
		                     </div>
		                </div>	
		                <label class="col-sm-1 col-form-label">Mes</label>
		                <div class="col-sm-4">
		                	<div class="form-group">
		                		<div id="div_contenedor_mes">		
		                			<?php $sql="SELECT c.cod_mes,(select m.nombre from meses m where m.codigo=c.cod_mes) as nombre_mes from meses_trabajo c where c.cod_gestion=$globalGestion";
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
                  	</div><!--div row-->
              	
		            <div class="row">
		            	<label class="col-sm-2 col-form-label">Razón Social</label>
		                <div class="col-sm-1">
		                	<div class="form-group">
								<div class="togglebutton">
								    <label>
										<input type="checkbox" name="check_rs_librocompras" id="check_rs_librocompras" onChange="ajax_razon_social_filtro_compras()">
										<span class="toggle"></span>
								    </label>
								</div>
							</div>
						</div>
						<div class="col-sm-8" >
		                	<div class="form-group" id="contenedor_razos_social_librocompras">
								
							</div>
						</div>
		            </div>
		   
                  	
                <div class="card-footer">
                	<button type="submit" class="<?=$buttonNormal;?>">Ver Reporte</button>
                	<a  href="#" class="btn btn-warning" onclick="descargar_txt_libro_compras()">Generar TXT</a>
				  <!-- <a href="?opcion=listComprobantes" class="<?=$buttonCancel;?>"> <-- Volver </a>-->
			  </div>
			  <hr>
			  <div class="col-sm-12">
			  	<div class="float-right">
			  		<?php 
			  		if(verificarEdicionComprobanteUsuario($globalUser)!=0){
			  			?>
			  		<a  href="index.php?opcion=reportesLibroComprasEdit" class="btn btn-danger btn-sm text-center" target="_blank"><i class="material-icons">edit</i> Editar Facturas</a>		
			  		<?php 
			  	   }
			  	   ?>
			  	  <a  href="index.php?opcion=reportesLibroComprasProy" class="btn btn-info btn-sm text-center" target="_blank"><i class="material-icons">open_in_new</i> Reporte Libro Compras - PROYECTO</a>		
			  	</div>
			  </div>
			  
               </form> 
              </div>	  
            </div>         
        <!-- </div>	 -->
	</div>
        
</div>

<div class="modal fade" id="modal_descargarTXT" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-sm" role="document">    
      <div class="modal-content">
        <div class="modal-header">          
          <h3 class="modal-title" id="myModalLabel"><b>Correcto</b></h3>
        </div>
        <div class="modal-body">                
              <center><span>El proceso se completó correctamente!</span></center>     
        </div>    
        <div id="contenedor_DescargaTxt">
          
        </div>    
      </div>
    </form>
  </div>
</div>
