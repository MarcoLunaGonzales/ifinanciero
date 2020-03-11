<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';
require_once 'functions.php';
require_once 'functionsGeneral.php';

$globalUserX=$_SESSION['globalUser'];
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
$nombre_personal=namePersonal($cod_personal);
//sacmos el valor de fechas hacia atrás
$dias_atras=obtenerValorConfiguracion(31);
$fecha_dias_atras=obtener_diashsbiles_atras($dias_atras,$fecha);
?>

<div class="content">
  <div class="container-fluid">
    <div class="col-md-12">
      <form id="form1" class="form-horizontal" action="<?=$urlSavereembolsoCajaChica;?>" method="post" onsubmit="return valida(this)">
        <input type="hidden" name="codigo" id="codigo" value="<?=$codigo;?>"/>
        <input type="hidden" name="cod_cc" id="cod_cc" value="<?=$cod_cc;?>"/>
        <input type="hidden" name="cod_tcc" id="cod_tcc" value="<?=$cod_tcc;?>"/>
        <div class="card">
          <div class="card-header <?=$colorCard;?> card-header-text">
          <div class="card-text">
            <h4 class="card-title"><?php if ($codigo == 0) echo "Registrar Nuevo"; else echo "Editar";?>  Reembolso</h4>
          </div>
          </div>
          <div class="card-body ">      
            <div class="row">
                <label class="col-sm-2 col-form-label">Monto</label>
                <div class="col-sm-4">
                    <div class="form-group">
                        <input class="form-control" type="text" step="any" name="monto" id="monto" value="<?=$monto;?>" required/>
                    </div>
                </div>
                <label class="col-sm-2 col-form-label">Fecha</label>
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
                <div class="col-sm-7">
                <div class="form-group">
                    <input class="form-control rounded-0" name="observaciones" id="observaciones" rows="3" required onkeyup="javascript:this.value=this.value.toUpperCase();" value="<?=$observaciones;?>" required/>                            
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


<script type="text/javascript">
  function valida(f) {
    var ok = true;
    if(f.elements["monto"].value == 0)
    {    
          var msg = "El Monto no debe ser menor a '0'...\n";
          ok = false;
    }
    if(ok == false)
      alert(msg);
    return ok;
  }
</script>