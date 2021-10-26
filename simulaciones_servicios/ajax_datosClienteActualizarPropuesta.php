<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();

//lista de contactos
$lista=obtenerListaClientesWS();

$codigo_cliente=$_GET['codigo_cliente'];
foreach ($lista->lista as $listaCliente) {
    $codigoX=$listaCliente->IdCliente;
    if($codigo_cliente==$codigoX){

        $nombreX=$listaCliente->NombreRazon;
        $identificacionX=$listaCliente->Identificacion;
        $paisX=$listaCliente->Pais;
        $deptartamentoX=$listaCliente->Departamento;
        $Ciudad=$listaCliente->Ciudad;
        $direccionX=$listaCliente->Direccion;
        $correoX=$listaCliente->Correo;
        $telefonoX=$listaCliente->Telefono;

        ?>
        <script>itemDatosClienteActualizar.push({razonSocial:"<?=$nombreX?>",nit:"<?=$identificacionX?>",direccion:"<?=$direccionX?>",pais:"<?=$paisX?>",ciudad:"<?=$Ciudad?>",deptartamento:"<?=$deptartamentoX?>",telefono:"<?=$telefonoX?>",fax:0,email:"<?=$correoX?>",web:""});
        </script><?php        

        // $nombreX=$listaCliente->NombreRazon;
        // if(isset($listaCliente->Identificacion))
        //     $identificacionX=$listaCliente->Identificacion;
        // else $identificacionX=0;

        // if(isset($listaCliente->Pais))
        //     $paisX=$listaCliente->Pais;
        // else $paisX=0;
        // if(isset($listaCliente->Deptartamento))
        //     $deptartamentoX=$listaCliente->Deptartamento;
        // else $deptartamentoX=0;

        // if(isset($listaCliente->Ciudad))
        //     $Ciudad=strtoupper($listaCliente->Ciudad);
        // else $Ciudad=0;

        // if(isset($listaCliente->Direccion))
        //     $direccionX=$listaCliente->Direccion;
        // else $direccionX=0;

        // if(isset($listaCliente->Correo))
        //     $correoX=$listaCliente->Correo;
        // else $correoX=0;

        // if(isset($listaCliente->Telefono))
        //     $telefonoX=$listaCliente->Telefono;
        // else $telefonoX=0;

    }
}
?>




