<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

if(isset($_GET["codigo"])){
 $codigo=$_GET["codigo"];
 $query="SELECT * FROM plantillas_servicios_tiposervicio where cod_plantillaservicio=$codigo order by codigo";
 $stmt = $dbh->prepare($query);
 $stmt->execute();
 $sum=0;
 while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
   $monto=$row['monto'];
   $cantidad=$row['cantidad'];
   $sum+=$monto*$cantidad;
 }
 $query="SELECT * FROM plantillas_servicios where codigo=$codigo";
 $stmt = $dbh->prepare($query);
 $stmt->execute();
 $dias=1;$utilidad=1;
 while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
   $dias=$row['dias_auditoria'];
   $utilidad=$row['utilidad_minima'];
   $anios=$row['anios'];
   $areaX=$row['cod_area'];
 }
 //CARGAR DIAS PORDEFECTO DE LA TABLA DE CONFIGURACION
 $dias=obtenerValorConfiguracion(40);
  ?>
  <script>$("#productos_div").addClass("d-none");</script>
  <script>$("#sitios_div").addClass("d-none");</script>
  <script>$("#div_marca").addClass("d-none");</script>
  <script>$("#div_norma").addClass("d-none");</script>
  <script>$("#div_pais").addClass("d-none");</script> 
                      <div class="row">
                          <!--<label class="col-sm-2 col-form-label">Dias Auditor:</label>
                           <div class="col-sm-1"> 
                             <div class="form-group">
                                <div class="form-check">
                                  <div class="form-group">-->
                                  <input type="hidden" min="1" class="form-control" value="<?=$dias?>" id="dias_auditoria" name="dias_auditoria">
                                   <!--</div>
                                  </div>
                               </div>
                             </div>-->
                          <label class="col-sm-2 col-form-label">Utilidad Minima:</label>
                           <div class="col-sm-3"> 
                             <div class="form-group">
                                  <input type="text" class="form-control" value="<?=$utilidad?>" id="utilidad_minima" name="utilidad_minima">
                             </div>    
                          </div>
                          <label class="col-sm-2 col-form-label">Años:</label>
                           <div class="col-sm-2"> 
                             <div class="form-group">
                                  <input type="text" class="form-control" value="<?=$anios?>" id="anios" name="anios">
                             </div>    
                          </div> 

                    </div>
            <?php 
            if($areaX==39){
               ?>
               <script>$("#productos_div").removeClass("d-none");</script>
               <script>$("#div_marca").removeClass("d-none");</script>
               <script>$("#div_norma").removeClass("d-none");</script>
               <script>$("#titulo_modal_atributo").html("AGREGAR PRODUCTO");</script>  
               <?php
            }else{
              if($areaX==38){
                ?>
                <script>$("#div_pais").removeClass("d-none");</script>
                <script>$("#sitios_div").removeClass("d-none");</script>
                <script>$("#titulo_modal_atributo").html("AGREGAR SITIO");</script> 
                <?php
              }else{

              }
            }
            ?>       
                              
<?php   
}     