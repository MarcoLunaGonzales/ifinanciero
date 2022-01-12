<?php

require_once '../conexion.php';
require_once 'configModule.php';
require_once '../functions.php';

$dbh = new Conexion();
$codigo_cliente=$_GET['codigo_cliente'];
$lista=obtenerListaClientesWS_contactos($codigo_cliente,2);
?>
<select name="select_contactos" id="select_contactos" class="selectpicker form-control form-control-sm" data-style="btn btn-primary" onChange="modalActualizarDatosCliente_Contactos(this)">
    <option value="">SELECCIONAR</option>
<?php
foreach ($lista->lstContactos as $listaContactos) {
    $IdContactoX=$listaContactos->IdContacto;
    $NombreCompletoX=$listaContactos->NombreCompleto;
    $CargoContactoX=$listaContactos->CargoContacto;
    $FonoContactoX=$listaContactos->FonoContacto;
    $CorreoContactoX=$listaContactos->CorreoContacto;    
    $VigenciaX=$listaContactos->Vigencia;//0 inactivo 1 activo
    if($VigenciaX==1){?>
    <option value="<?=$IdContactoX;?>"><?=$NombreCompletoX;?> (<?=$CargoContactoX?>)</option><?php
    }
}
?>
</select>
