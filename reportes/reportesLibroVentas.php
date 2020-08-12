<?php
require_once 'conexion.php';
require_once 'comprobantes/configModule.php';
require_once 'styles.php';
require_once 'functions.php';
require_once 'functionsGeneral.php';
$globalAdmin=$_SESSION["globalAdmin"];
$globalGestion=$_SESSION["globalGestion"];
$global_mes=$_SESSION["globalMes"];

$dbh = new Conexion();
$stmt = $dbh->prepare("SELECT p.codigo, p.numero, p.nombre, p.nivel from plan_cuentas p order by p.numero");
$stmt->execute();
$i=0;
  echo "<script>var array_cuenta=[];</script>";
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	 $codigoX=$row['codigo'];
	 $numeroX=$row['numero'];
	 $nombreX=$row['nombre'];
	 $nivelX=$row['nivel'];
	 $nombreCuenta=formateaPlanCuenta($numeroX." ".$nombreX,$nivelX);
	 $arrayNuevo[$i][0]=$codigoX;
	 $arrayNuevo[$i][1]=$numeroX;
	 $arrayNuevo[$i][2]=$nombreCuenta;
	 $arrayNuevo[$i][3]=$nivelX;
		$i++;
	}
?>

<div class="content">
	<div class="container-fluid">
		<div >			 		
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons"><?=$iconCard;?></i>
                  </div>
                  <h4 class="card-title">Reporte Libro de Ventas</h4>
                </div>
                <form class="" action="<?=$urlReporteVentas?>" target="_blank" method="POST">
                <div class="card-body">
                  	<div class="row">
		                <label class="col-sm-2 col-form-label">Gestión</label>
		                <div class="col-sm-6">
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
                  	</div><!--div row-->
              		<div class="row">
		                 <label class="col-sm-2 col-form-label">Mes</label>
		                 <div class="col-sm-6">
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
		            </div>
	         	</div>
                <div class="card-footer fixed-bottom">
                	<button type="submit" class="btn btn-success">Ver Reporte</button>
                	<a  href="#" class="btn btn-warning" onclick="descargar_txt_libro_ventas()">Generar TXT</a>
				  <!-- <a href="?opcion=listComprobantes" class="<?=$buttonCancel;?>"> <-- Volver </a>-->
			  </div>
               </form> 
              </div>	  
            </div>         
        </div>	
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
