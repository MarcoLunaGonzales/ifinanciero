<?php

require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'styles.php';
require_once 'functionsGeneral.php';

$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();

$stmt = $dbh->prepare("SELECT * from unidades_organizacionales where cod_estado=1 order by nombre");
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('abreviatura', $abreviatura);
$stmt->bindColumn('observaciones', $observaciones);
$stmt->bindColumn('cod_estado', $cod_estadoreferencial);
$stmt->bindColumn('created_at', $created_at);
$stmt->bindColumn('created_by', $created_by);
$stmt->bindColumn('modified_at', $modified_at);
$stmt->bindColumn('modified_by', $modified_by);

?>

<div class="content">
	<div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons"><?=$iconCard;?></i>
                  </div>
                  <h4 class="card-title"><?=$nombrePluralUO?></h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table" id="tablePaginator">

                    <thead>
                      <tr>
                          <th>#</th>
                          <th>Codigo</th>
                          <th>Nombre</th>
                          <th>Abreviatura</th>
                          <th>Observaciones</th>
                          
                          
                          <th></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $index=1;$cont= array();
                      while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { 
                        $datosX =$codigo;

                        $dbh1 = new Conexion();
                        $sqlA="SELECT codigo,cod_unidad,cod_area,cod_areapadre,
                              (select a.nombre from areas a where a.codigo=cod_area) as nombre_area,
                              (select a.nombre from areas a where a.codigo=cod_areapadre) as nombre_area_padre
                              from areas_organizacion
                              where cod_estadoreferencial=1 and cod_unidad=:codigo";
                               $stmt2 = $dbh1->prepare($sqlA);
                               $stmt2->bindParam(':codigo',$codigo);
                               $stmt2->execute(); 
                               $nc=0;
                               
                               while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                                  $dato = new stdClass();//obejto
                                  
                                  $nombre_areaX=trim($row2['nombre_area']);
                                  $nombre_area_padreX=trim($row2['nombre_area_padre']);                                  
                                  $dato->codigo=($nc+1);
                                  $dato->nombreA=$nombre_areaX;
                                  $dato->nombreAP=$nombre_area_padreX;
                                  $datos[$index-1][$nc]=$dato;                           
                                  $nc++;
                                }
                            $cont[$index-1]=$nc;  

                        ?>
                        <tr>
                            
                            <td><?=$index;?></td>
                            <td><?=$codigo;?></td>
                            <td><?=$nombre;?></td>
                            <td><?=$abreviatura;?></td>
                            <td><?=$observaciones;?></td>
                            
                            <td class="td-actions text-right">
                              <?php
                                if($globalAdmin==1){
                              ?>


                              <!--<button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modalAreas" onclick="agregaListAreas_unidad('<?=$datos;?>')">
                                <i class="material-icons" title="Listar Areas">settings_applications</i>
                              </button>-->
                              <a href='#' rel="tooltip" class="btn btn-warning" onclick="filaTablaAGeneral($('#tablasA_registradas'),<?=$index?>)">
                              <i class="material-icons" title="Ver Areas">settings_applications</i>
                            </a>

                              <!-- <a href='#' class="add_listar" product="<?=$codigo;?>">
                                  <i class="material-icons" title="Registrar Areas">playlist_add</i>
                              </a> -->

                              <a href='<?=$urlRegisterAreasU;?>&codigo=<?=$codigo;?>' rel="tooltip" class="btn btn-primary">
                                  <i class="material-icons" title="Registrar Areas">playlist_add</i>
                              </a>

                              <a href='<?=$urlFormUO;?>&codigo=<?=$codigo;?>' rel="tooltip" class="<?=$buttonEdit;?>">
                                <i class="material-icons"><?=$iconEdit;?></i>
                              </a>
                              <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDeleteUO;?>&codigo=<?=$codigo;?>')">
                                <i class="material-icons"><?=$iconDelete;?></i>
                              </button>
                              <?php
                              $nc++;}
                                $cont[$index-1]=$nc;
                              ?>
                            
                            </td>
                        </tr>
                        <?php $index++; } ?>
                    </tbody>
                    
                    </table>
                  </div>
                </div>
              </div>
              <?php
              if($globalAdmin==1){
              ?>
      				<div class="card-footer fixed-bottom">                    
                    <button class="<?=$buttonNormal;?>" onClick="location.href='<?=$urlFormUO;?>&codigo=0'">Registrar</button>
              </div>
              <?php
              }
              ?>
		  
            </div>
          </div>  
        </div>
    </div>
<!-- small modal para listar areas -->
<!-- <div class="modal fade modal-primary" id="modalAreas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content card">
      <div class="card-header card-header-warning card-header-icon">
        <div class="card-icon">
          <i class="material-icons">settings_applications</i>
        </div>
        <h4 class="card-title">Areas Registradas</h4>
      </div>
      <div class="card-body">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          <i class="material-icons">close</i>
        </button>        
        <input type="text" name="codigo_area_unidad" id="codigo_area_unidad" value="">
        <script>
              var contenedor_aux=document.getElementById("codigo_area_unidad").value;            
              //alert(contenedor_aux);
        </script>
        <table class="table table-condensed">
          <thead>
            <tr class="text-dark bg-plomo">
            <th>#</th>
            <th>Código</th>
            <th>Nombre Area</th>
            <th>Nombre Area Padre</th>
            </tr>
          </thead>
          <tbody id="tablas_registradas">

            <?php           
            

            $stmtAreas = $dbh->prepare("SELECT codigo,cod_unidad,cod_area,cod_areapadre,
            (select a.nombre from areas a where a.codigo=cod_area) as nombre_area,
            (select a.nombre from areas a where a.codigo=cod_areapadre) as nombre_area_padre
            from areas_organizacion
            where cod_estadoreferencial=1 and cod_unidad=:codigo");
            //ejecutamos
            $stmtAreas->bindParam(':codigo',$codigo_area_u);
            $stmtAreas->execute();
            //bindColumn
            $stmtAreas->bindColumn('codigo', $codigo_area_unidad);
            $stmtAreas->bindColumn('cod_unidad', $cod_unidad);
            $stmtAreas->bindColumn('cod_area', $cod_area);
            $stmtAreas->bindColumn('cod_areapadre', $cod_areapadre);
            $stmtAreas->bindColumn('nombre_area', $nombre_area);
            $stmtAreas->bindColumn('nombre_area_padre', $nombre_area_padre);

            
            while ($row = $stmtAreas->fetch(PDO::FETCH_BOUND)) { ?>
            <tr>
                <td><?=$codigo_area_unidad;?></td>
                <td><?=$nombre_area;?></td>
                <td><?=$nombre_area_padre;?></td>
            
            </tr>
            <?php } ?>
            
          </tbody>
        </table>
      </div>
    </div>  
  </div>
</div> -->


<!--<script type="text/javascript">
  $(document).ready(function(){    
    $('.add_listar').click(function(event){

      event.preventDefault();
      var area= $(this).attr('product');
      alert(area);


    }); 
    

  });
</script>-->

<?php 
$lan=sizeof($cont);
for ($i=0; $i < $lan; $i++) {
  ?>
  <script>var areas=[];</script>
  <?php
     for ($j=0; $j < $cont[$i]; $j++) { 
         if($cont[$i]>0){
          ?><script>areas.push({codigo:<?=$datos[$i][$j]->codigo?>,nombreA:'<?=$datos[$i][$j]->nombreA?>',nombreAP:'<?=$datos[$i][$j]->nombreAP?>'});</script><?php         
          }          
        }
    ?><script>areas_tabla_general.push(areas);</script><?php                    
}
require_once 'rrhh/modal.php';
?>

