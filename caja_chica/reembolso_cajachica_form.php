<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';
require_once 'functions.php';
require_once 'functionsGeneral.php';

$globalUserX=$_SESSION['globalUser'];
$globalUnidad=$_SESSION['globalUnidad'];
//$dbh = new Conexion();
$dbh = new Conexion();
//por is es edit
$cod_cc=$cod_cc;
$cod_tcc=$cod_tcc;
$cod_rcc=$codigo;
$i=0;

if ($codigo > 0){
    
    $stmt = $dbh->prepare("SELECT * from caja_chicareembolsos where cod_estadoreferencial=1 and codigo =$cod_rcc");
    $stmt->execute();
    $result = $stmt->fetch();
    $monto = $result['monto'];
    $fecha = $result['fecha'];
    $cod_personal = $result['cod_personal'];    
    $observaciones = $result['observaciones'];    
    
} else {
    $fecha = date('Y-m-d');
    $monto = null;
    $cod_personal = $globalUserX;    
    $observaciones = null;    
    
}


// busquena por Oficina
$stmtUO = $dbh->prepare("SELECT (select u.abreviatura from unidades_organizacionales u where u.codigo=c.cod_unidadorganizacional)unidad,(select u.codigo from unidades_organizacionales u where u.codigo=c.cod_unidadorganizacional)as codigo_uo
from comprobantes c where c.cod_estadocomprobante!=2 GROUP BY unidad order by unidad");
$stmtUO->execute();
$stmtUO->bindColumn('unidad', $nombreUnidad_x);
$stmtUO->bindColumn('codigo_uo', $codigo_uo);

// busquena por tipo de comprobante
$stmtTipoComprobante = $dbh->prepare("SELECT (select t.nombre from tipos_comprobante t where t.codigo=c.cod_tipocomprobante)as tipo_comprobante,(select t.codigo from tipos_comprobante t where t.codigo=c.cod_tipocomprobante)as cod_tipo_comprobante
from comprobantes c where c.cod_estadocomprobante!=2 GROUP BY tipo_comprobante order by tipo_comprobante
");
$stmtTipoComprobante->execute();
$stmtTipoComprobante->bindColumn('tipo_comprobante', $nombre_tipo_comprobante);
$stmtTipoComprobante->bindColumn('cod_tipo_comprobante', $codigo_tipo_co);

$nombre_personal=namePersonal($cod_personal);
//sacmos el valor de fechas hacia atrás
$dias_atras=obtenerValorConfiguracion(31);
$fecha_dias_atras=obtener_diashsbiles_atras($dias_atras,$fecha);
?>

<div class="content">
  <div class="container-fluid">
    <div class="col-md-12">
      <form id="formReembolso" class="form-horizontal" action="<?=$urlSavereembolsoCajaChica;?>" method="post" onsubmit="return valida(this)">
        <input type="hidden" name="codigo" id="codigo" value="<?=$codigo;?>"/>
        <input type="hidden" name="cod_cc" id="cod_cc" value="<?=$cod_cc;?>"/>
        <input type="hidden" name="cod_tcc" id="cod_tcc" value="<?=$cod_tcc;?>"/>
        <div class="card">
          <div class="card-header <?=$colorCard;?> card-header-text">
            <div class="card-text">
              <h4 class="card-title"><?php if ($codigo == 0) echo "Registrar Nuevo"; else echo "Editar";?>  Reembolso</h4>              
            </div>
            <h4 align="right">              
              <button type="button" class="btn btn-warning btn-round btn-fab btn-sm" onclick="botonBuscarComprobante_caja_chica()">
                <i class="material-icons" title="Seleccionar Comprobante">search</i>
              </button>                      
            </h4>
          </div>
          <div class="card-body ">      
            <input type="hidden" name="cod_comprobante" id="cod_comprobante" value="0">
            <input type="hidden" name="cod_comprobante_detalle" id="cod_comprobante_detalle" value="0">
            <div class="row">
                <label class="col-sm-2 col-form-label">Monto</label>
                <div class="col-sm-4">
                    <div class="form-group">
                        <input class="form-control" type="text" step="any" name="monto" id="monto" value="<?=$monto;?>" required/>
                    </div>
                </div>
                <label class="col-sm-1 col-form-label">Fecha</label>
                <div class="col-sm-4">
                    <div class="form-group">
                        <input class="form-control" name="fecha" id="fecha" type="date" min="<?=$fecha_dias_atras?>" max="<?=$fecha?>" required="true" value="<?=$fecha?>" />
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <label class="col-sm-2 col-form-label">Responsable</label>
                <div class="col-sm-8">
                  <div class="form-group">
                      <input class="form-control" type="text"  value="<?=$nombre_personal?>" readonly="readonly">
                      <input class="form-control" type="text" name="cod_personal" id="cod_personal" value="<?=$globalUserX?>" hidden="hidden">                                
                  </div>
                </div>
              </div>
            <div class="row">
                <label class="col-sm-2 col-form-label">Detalle</label>
                <div class="col-sm-9">
                <div class="form-group">
                  <textarea class="form-control" name="observaciones" id="observaciones" required onkeyup="javascript:this.value=this.value.toUpperCase();" value="<?=$observaciones;?>"></textarea>
                </div>
                </div>
            </div>              
          </div>
          <div class="card-footer ml-auto mr-auto">
          <button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
          <a href="<?=$urlListDetalleCajaChica;?>&codigo=<?=$cod_cc;?>&cod_tcc=<?=$cod_tcc?>" class="<?=$buttonCancel;?>"><i class="material-icons" title="Volver">keyboard_return</i> Volver </a>
          </div>
        </div>
      </form>
    </div>
  
  </div>
</div>
<div class="cargar-ajax d-none">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold" id="texto_ajax_titulo">Procesando Datos</h4>
     <p class="text-white">Aguard&aacute; un momento por favor</p>  
  </div>
</div>
<!-- lista de comprobantes -->
<div class="modal fade" id="modal_lista_comprobantes" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
  <div class="modal-dialog modal-notice modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Lista de Comprobantes</h4>
      </div>
      <div class="modal-body ">
        <div class="row" id="contenedor_lista_comprobantes">
          
        </div>
      </div>      
    </div>
  </div>
</div>

<!-- <script type="text/javascript">  
  $('#modalBuscador').modal('show');
</script>
 -->
<script type="text/javascript">
  // $('#modalBuscador').modal('show');
  function valida(f) {
    var ok = true;
    if(f.elements["monto"].value == 0 || f.elements["monto"].value == null || f.elements["monto"].value == '' || f.elements["monto"].value == ' ')
    {    
          var msg = "Monto Incorrecto...\n";
          ok = false;
    }
    if(ok == false)
      alert(msg);
    return ok;
  }
</script>
