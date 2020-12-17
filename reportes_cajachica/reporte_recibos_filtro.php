<?php
session_start();
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once 'configModule.php';
require_once '../styles.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
$dbh = new Conexion();


// $globalAdmin=$_SESSION["globalAdmin"];
$globalUnidad=$_SESSION["globalUnidad"];
// $globalArea=$_SESSION["globalArea"];
$globalUser=$_SESSION["globalUser"];

$fechaActual=date("m/d/Y");
$m=date("m");
$y=date("Y");
$d=date("d",(mktime(0,0,0,$m+1,1,$y)-1));
$fechaDesde=$y."-01-01";
$fechaHasta=$y."-".$m."-".$d;

$fechaDesde2=$y."-01-01";
$fechaHasta2=$y."-12-31";
?>

<div class="content">
	<div class="container-fluid">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons"><?=$iconCard;?></i>
                  </div>
                  <h4 class="card-title">Impresión de Recibos</h4>
                </div>
                <form class="" action="reporte_recibos_print.php" target="_blank" method="POST">
                <div class="card-body">
                  	<div class="row">
		                <label class="col-sm-2 col-form-label">Caja Chica</label>
		                <div class="col-sm-7">
		                	<div class="form-group">		                		
	                			<select class="selectpicker form-control col-sm-11" name="cod_caja_chica" id="cod_caja_chica" data-live-search="true" data-size="6" data-style="btn btn-default btn-sm text-white bg-caja-chica">
             <?php 
           $stmtCaja = $dbh->prepare("SELECT *,(select uo.abreviatura from unidades_organizacionales uo where uo.codigo=cod_uo) as nombre_uo,
             (select a.abreviatura from areas a where a.codigo=cod_area)as nombre_area
             from tipos_caja_chica where cod_estadoreferencial=1");//and cod_personal=$globalUser
           //ejecutamos
           $stmtCaja->execute();
           //bindColumn
           $stmtCaja->bindColumn('codigo', $codigoTipo);
           $stmtCaja->bindColumn('nombre', $nombreTipo);
           $stmtCaja->bindColumn('cod_personal', $cod_personal);
           $stmtCaja->bindColumn('cod_uo', $cod_uo);
           $stmtCaja->bindColumn('cod_area', $cod_area);
           $stmtCaja->bindColumn('nombre_uo', $nombre_uo);
           $stmtCaja->bindColumn('nombre_area', $nombre_area);

                  while ($rowCaja = $stmtCaja->fetch(PDO::FETCH_BOUND)) {
                         
                         $stringCaja="and cod_personal=$globalUser";
                         if(verificarEdicionComprobanteUsuario($globalUser)!=0){
                             $stringCaja="";
                         }
                         
                         $sql2="SELECT *,date_format(fecha,'%d/%m/%Y') as fecha_x,
                           (select e.nombre from estados_contrato e where e.codigo=cod_estado) as nombre_estado,
                         (select CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=cod_personal) as personal
                          from caja_chica where cod_estadoreferencial=1 and cod_estado=1 and cod_tipocajachica = $codigoTipo $stringCaja order by codigo desc";
                         //echo $sql2;
                          $stmtCajaChica = $dbh->prepare($sql2);
                         //ejecutamos
                          $stmtCajaChica->execute();
                          //bindColumn
                          $stmtCajaChica->bindColumn('codigo', $cod_cajachica);
                          $stmtCajaChica->bindColumn('cod_tipocajachica', $cod_tipocajachica);
                          $stmtCajaChica->bindColumn('fecha_x', $fecha);
                          $stmtCajaChica->bindColumn('numero', $numero);
                          $stmtCajaChica->bindColumn('monto_inicio', $monto_inicio);
                          // $stmtCajaChica->bindColumn('monto_reembolso', $monto_reembolso);
                          $stmtCajaChica->bindColumn('monto_reembolso_nuevo', $monto_reembolso_nuevo);
                          $stmtCajaChica->bindColumn('observaciones', $observaciones);
                          $stmtCajaChica->bindColumn('cod_personal', $cod_personal);
                          $stmtCajaChica->bindColumn('personal', $personal);
                          $stmtCajaChica->bindColumn('cod_estado', $cod_estado);
                          $stmtCajaChica->bindColumn('nombre_estado', $nombre_estado);
                          $stmtCajaChica->bindColumn('cod_comprobante', $cod_comprobante);

                          while ($rowCajaChica = $stmtCajaChica->fetch(PDO::FETCH_BOUND)) {
                          ?><option value="<?=$cod_cajachica;?>"><?=$nombreTipo;?>, Oficina : <?=$nombre_uo?>, Area : <?=$nombre_area?> -<?=$personal?> - <?=$observaciones?> (<?=$nombre_estado?>)</option><?php 
                          }
                       }   
                        ?>
                    </select>		                		
		                     </div>
		                </div>				             
                  	</div><!--div row-->
                  	

	                <div class="row">	                  	
						<label class="col-sm-2 col-form-label">Desde</label>
						<div class="col-sm-3">
							<div class="form-group">
								<div id="div_contenedor_fechaI">				                			
									<input type="date" class="form-control" autocomplete="off" name="fecha_desde" id="fecha_desde" min="<?=$fechaDesde2?>" max="<?=$fechaHasta2?>" value="<?=$fechaDesde?>">	
								</div>		                                
							 </div>
						</div>
						<label class="col-sm-1 col-form-label">Hasta</label>
						<div class="col-sm-3">
							<div class="form-group">
								<div id="div_contenedor_fechaH">				                			
									<input type="date" class="form-control" autocomplete="off" name="fecha_hasta" id="fecha_hasta" min="<?=$fechaDesde2?>" max="<?=$fechaHasta2?>" value="<?=$fechaHasta?>">
								</div>
							   
							</div>
						</div>
	                </div><!--div fechas row-->

	                   <div class="row">	                  	
						<label class="col-sm-2 col-form-label">Rango de Números</label>
						<div class="col-sm-2">
							<div class="form-group">
								<div id="div_contenedor_fechaI">				                			
									<input type="text" placeholder="1-10" class="form-control" name="numero_rango" id="numero_rango" min="0">	
								</div>		                                
							 </div>
						</div>
	                </div><!--div rango numero row-->
	             
	            </div><!--div fechas row-->
                <div class="card-footer fixed-bottom">
                	<button type="submit" class="<?=$buttonNormal;?>">Ver Reporte</button>
				   <a href="../reportes_ventas/" class="<?=$buttonCancel;?>"> Volver </a>
			  </div>
               </form> 
              </div>	  
            </div>                 
	</div>
        
</div>

