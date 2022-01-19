<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();

//lista de contactos



$codigo_cliente=$_GET['codigo_cliente'];
$datosCliente=obtenerListaClientesWS_X($codigo_cliente);
$detalle_cliente=$datosCliente->datos;
// foreach ($lista->datos as $listaCliente) {    
    $nombreX=$detalle_cliente->NombreRazon;
    $identificacionX=$detalle_cliente->Identificacion;
    if($identificacionX==null || $identificacionX=="" || $identificacionX==" " || $identificacionX==0){
        $sql="SELECT identificacion from clientes where codigo=$codigo_cliente";
        $stmt = $dbh->prepare($sql);
        $stmt->execute();        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            
            $identificacionX=$row['identificacion'];
        }
    }
    $paisX=$detalle_cliente->Pais;
    $deptartamentoX=$detalle_cliente->Departamento;
    $Ciudad=$detalle_cliente->Ciudad;
    $direccionX=$detalle_cliente->Direccion;
    $correoX=$detalle_cliente->Correo;
    $telefonoX=$detalle_cliente->Telefono;
    ?>
    <script>itemDatosClienteActualizar.push({razonSocial:"<?=$nombreX?>",nit:"<?=$identificacionX?>",direccion:"<?=$direccionX?>",pais:"<?=$paisX?>",ciudad:"<?=$Ciudad?>",deptartamento:"<?=$deptartamentoX?>",telefono:"<?=$telefonoX?>",fax:0,email:"<?=$correoX?>",web:""});
    </script><?php
    
    
// }

//listamos los Datos MAE
$datosMAE=obtenerListaClientesWS_contactos($codigo_cliente,1);//tipo MAE
// var_dump($datosMAE);
foreach ($datosMAE->lista as $listaContactosMae) {
    $IdContactoMae=$listaContactosMae->IdContacto;
    $NombreCompletoMae=$listaContactosMae->NombreCompleto;
    $CargoContactoMae=$listaContactosMae->CargoContacto;
    $FonoContactoMae=$listaContactosMae->FonoContacto;
    $CorreoContactoMae=$listaContactosMae->CorreoContacto;    
    $VigenciaMae=$listaContactosMae->Vigencia;//0 inactivo 1 activo
    if($VigenciaMae==1){ ?>    
    <script>itemDatosClienteActualizar_contactos_mae.push({IdContacto:"<?=$IdContactoMae?>",NombreCompleto:"<?=$NombreCompletoMae?>",CargoContacto:"<?=$CargoContactoMae?>",FonoContacto:"<?=$FonoContactoMae?>",CorreoContacto:"<?=$CorreoContactoMae?>"});
        </script><?php
    }
}

//listamos los contactos
$datosContacto=obtenerListaClientesWS_contactos($codigo_cliente,2);
foreach ($datosContacto->lstContactos as $listaContactos) {
    $IdContactoX=$listaContactos->IdContacto;
    $NombreCompletoX=$listaContactos->NombreCompleto;
    $CargoContactoX=$listaContactos->CargoContacto;
    $FonoContactoX=$listaContactos->FonoContacto;
    $CorreoContactoX=$listaContactos->CorreoContacto;    
    $VigenciaX=$listaContactos->Vigencia;//0 inactivo 1 activo
    if($VigenciaX==1){?>    
    <script>itemDatosClienteActualizar_contactos.push({IdContacto:"<?=$IdContactoX?>",NombreCompleto:"<?=$NombreCompletoX?>",CargoContacto:"<?=$CargoContactoX?>",FonoContacto:"<?=$FonoContactoX?>",CorreoContacto:"<?=$CorreoContactoX?>"});
        </script><?php
    }
}
?>
