<?php
session_start();
require_once '../conexion.php';
require_once '../styles.php';

require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once 'configModule.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

// $globalNombreGestion=$_SESSION["globalNombreGestion"];
// $globalUser=$_SESSION["globalUser"];
// $globalGestion=$_SESSION["globalGestion"];
// $globalUnidad=$_SESSION["globalUnidad"];
// $globalNombreUnidad=$_SESSION['globalNombreUnidad'];
// $globalArea=$_SESSION["globalArea"];
// $globalAdmin=$_SESSION["globalAdmin"];

$filas=$_POST['filas'];
$datos=json_decode($_POST['datos']);

//$cod_cuenta_configuracion_iva=obtenerValorConfiguracion(3);
for ($fila=0; $fila < count($datos); $fila++) { 
  $nombre_x=$datos[$fila][0];//nombre
  $marca_x=$datos[$fila][1];//marca
  $norma_x=$datos[$fila][2];//norma
  $sello_x=$datos[$fila][3];//sello
  $direccion_x=$datos[$fila][4];//direccion
      // if($datos[$fila][2]==""){
    //   $datos[$fila][2]="0";
    // }      
        

    
    
  $idFila=(($filas+$fila)+1); 
      ?>      
<div id="div<?=$idFila?>">
 <div class="col-md-12">  
    <input type="hidden" name="numero_cuenta<?=$idFila;?>" value="<?=$numero_cuenta?>" id="numero_cuenta<?=$idFila;?>">    
    <div class="col-sm-1">
        <div class="form-group">      
          <input class="form-control small" type="number" placeholder="0" value="<?=$debe?>" name="debe<?=$idFila;?>" id="debe<?=$idFila;?>" onChange="calcularTotalesComprobante(this.id,event);" OnKeyUp="calcularTotalesComprobante(this.id,event);" step="any"> 
        </div>
    </div>
    <div class="col-sm-1">
      <div class="form-group">     
        <input class="form-control small" type="number" placeholder="0" value="<?=$haber?>" name="haber<?=$idFila;?>" id="haber<?=$idFila;?>" onChange="calcularTotalesComprobante(this.id,event);" OnKeyUp="calcularTotalesComprobante(this.id,event);" step="any">   
      </div>
    </div>    
    <div class="col-sm-1">
        <div class="btn-group">
          <a href="#" title="Retenciones" id="boton_ret<?=$idFila;?>" onclick="listRetencion(<?=$idFila;?>);" class="btn btn-warning text-dark btn-sm btn-fab"><i class="material-icons">ballot</i></a>
          <a title="Facturas" href="#" id="boton_fac<?=$idFila;?>" onclick="listFac(<?=$idFila;?>);" class="facturas-boton btn btn-info btn-sm btn-fab <?=($cod_cuenta_configuracion_iva==$codigoCuenta)?'':'btn-default text-dark d-none';?>" ><i class="material-icons">featured_play_list</i><span id="nfac<?=$idFila;?>" class="count bg-warning">0</span></a>
          <a title="Actividad Proyecto SIS" id="boton_actividad_proyecto<?=$idFila?>" href="#" onclick="verActividadesProyectosSis(<?=$idFila;?>);" class="btn btn-sm btn-orange btn-fab d-none"><span class="material-icons">assignment</span><span id="nestadoactproy<?=$idFila?>" class="bg-warning"></span></a>
          <a title="Solicitudes de Recursos SIS" id="boton_solicitud_recurso<?=$idFila?>" href="#" onclick="verSolicitudesDeRecursosSis(<?=$idFila;?>);" class="btn btn-sm btn-default btn-fab d-none"><span class="material-icons text-dark">view_sidebar</span><span id="nestadosol<?=$idFila?>" class="bg-warning"></span></a>
          <a title="Agregar Fila" id="boton_agregar_fila<?=$idFila?>" href="#" onclick="agregarFilaComprobante(<?=$idFila;?>);return false;" class="btn btn-sm btn-primary btn-fab"><span class="material-icons">add</span></a>              
          <a title="Eliminar (alt + q)" rel="tooltip" href="#" class="btn btn-danger btn-sm btn-fab" id="boton_remove<?=$idFila;?>" onclick="quitarFilaComprobante('<?=$idFila;?>');return false;">
                     <i class="material-icons">disabled_by_default</i>
          </a>
        </div>  
    </div>
 <div class="h-divider"></div>
</div>
</div>

<script>$("#cantidad_filas").val(<?=$idFila?>);$("#div"+<?=$idFila?>).bootstrapMaterialDesign();
      numFilas++;
      cantidadItems++;
      filaActiva=numFilas;
</script>
      <?php
  }

?>
