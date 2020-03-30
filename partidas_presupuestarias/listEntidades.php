<?php

require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'styles.php';
require_once 'functionsGeneral.php';

$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();

$stmt = $dbh->prepare("SELECT * from entidades where cod_estadoreferencial=1 order by nombre");
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('abreviatura', $abreviatura);
// $stmt->bindColumn('observaciones', $observaciones);
$stmt->bindColumn('cod_estadoreferencial', $cod_estadoreferencial);
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
                  <h4 class="card-title">Entidades</h4>
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
                          <!-- <th>Observaciones</th> -->
                          
                          
                          <th></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $index=1;$cont= array();$contC= array();
                      while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { 
                        $datosX =$codigo;

                        $dbh1 = new Conexion();
                        $sqlA="SELECT codigo,cod_uo,
                              (select a.nombre from unidades_organizacionales a where a.codigo=cod_uo) as nombre_unidad                              
                              from entidades_uo
                              where cod_entidad=:codigo";
                               $stmt2 = $dbh1->prepare($sqlA);
                               $stmt2->bindParam(':codigo',$codigo);
                               $stmt2->execute(); 
                               $nc=0;
                               while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                                  $dato = new stdClass();//obejto
                                  $codFila=(int)$row2['codigo'];
                                  $nombre_unidadX=trim($row2['nombre_unidad']);                                  
                                  $dato->codigo=($nc+1);
                                  $dato->cod_entidadunidad=$codFila;
                                  $dato->nombreU=$nombre_unidadX;
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
                            <!-- <td><?=$observaciones;?></td> -->
                            
                            <td class="td-actions text-right">
                              <?php
                                if($globalAdmin==1){
                              ?>
                                  <a href='#' rel="tooltip" class="btn btn-warning" onclick="filaTablaAGeneralEntidadOrganizacional($('#tablasU_registradas'),<?=$index?>)">
                                    <i class="material-icons" title="Ver Oficinas">settings_applications</i>
                                  </a>
                                  <a href='<?=$urlRegisterUnidadesEntidad;?>&codigo=<?=$codigo;?>' rel="tooltip" class="btn btn-primary">
                                      <i class="material-icons" title="Registrar Oficinas">playlist_add</i>
                                  </a>

                                  <a href='<?=$urlRegisterEntidades;?>&codigo=<?=$codigo;?>' rel="tooltip" class="<?=$buttonEdit;?>">
                                    <i class="material-icons"><?=$iconEdit;?></i>
                                  </a>
                                  <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDeleteEntidad;?>&codigo=<?=$codigo;?>')">
                                    <i class="material-icons"><?=$iconDelete;?></i>
                                  </button>
                                <?php
                                  // $nc++;
                                }

                                // $cont[$index-1]=$nc;
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
                    <button class="<?=$buttonNormal;?>" onClick="location.href='<?=$urlRegisterEntidades;?>&codigo=0'">Registrar</button>
              </div>
              <?php
              }
              ?>
		  
            </div>
          </div>  
        </div>
    </div>


<?php 
$lan=sizeof($cont);
for ($i=0; $i < $lan; $i++) {
  ?><script>var unidades=[];</script><?php
     for ($j=0; $j < $cont[$i]; $j++) { 
         if($cont[$i]>0){
          ?><script>unidades.push({codigo:<?=$datos[$i][$j]->codigo?>,cod_entidadunidad:<?=$datos[$i][$j]->cod_entidadunidad?>,nombreU:'<?=$datos[$i][$j]->nombreU?>'});</script><?php         
          }          
        }
    ?><script>unidades_tabla_general.push(unidades);</script><?php                    
}
?>
<?php 
require_once 'partidas_presupuestarias/modal.php';
?>