<?php
//ES PARA IMPRIMIR LA ASIGNACION ENTRE PERSONAS
require_once __DIR__.'/../conexion.php';
//require_once 'styles.php';
//require_once 'configModule.php';
require_once '../styles.php';
require_once '../layouts/bodylogin2.php';


$dbh = new Conexion();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
//RECIBIMOS LAS VARIABLES

$codigo = $_GET["codigo"];//codigoactivofijo

try{
    $stmt = $dbh->prepare("select * from v_activosfijos_asignaciones WHERE activofijosasignaciones_codigo = :codigo");
    //Ejecutamos;
    $stmt->bindParam(':codigo',$codigo);
    $stmt->execute();
    $result = $stmt->fetch();
    $codigo = $result['codigo'];
    $codigoactivo = $result['codigoactivo'];
    $tipoalta = $result['tipoalta'];
    $fechalta = $result['fechalta'];
    $indiceufv = $result['indiceufv'];
    $tipocambio = $result['tipocambio'];
    $moneda = $result['moneda'];
    $valorinicial = $result['valorinicial'];
    $depreciacionacumulada = $result['depreciacionacumulada'];
    $valorresidual = $result['valorresidual'];
    $cod_depreciaciones = $result['cod_depreciaciones'];
    $cod_tiposbienes = $result['cod_tiposbienes'];
    $vidautilmeses = $result['vidautilmeses'];
    $estadobien = $result['estadobien'];
    $otrodato = $result['otrodato'];
    $cod_ubicaciones = $result['cod_ubicaciones'];
    $cod_empresa = $result['cod_empresa'];
    $activo = $result['activo'];
    $cod_responsables_responsable = $result['cod_responsables_responsable'];
    $cod_responsables_autorizadopor = $result['cod_responsables_autorizadopor'];
    $created_at = $result['created_at'];
    $created_by = $result['created_by'];
    $modified_at = $result['modified_at'];
    $modified_by = $result['modified_by'];
    $vidautilmeses_restante = $result['vidautilmeses_restante'];
    $cod_af_proveedores = $result['cod_af_proveedores'];
    $numerofactura = $result['numerofactura'];
    $nombre_personal = $result['nombre_personal'];
    $nombre_depreciaciones = $result['nombre_depreciaciones'];
    $tipo_bien = $result['tipo_bien'];
    $activofijosasignaciones_codigo = $result['activofijosasignaciones_codigo'];
    $fechaasignacion = $result['fechaasignacion'];
    $edificio = $result['edificio'];
    $oficina = $result['oficina'];
    $nombre_uo = $result['nombre_uo'];
    $estadobien_asig = $result['estadobien_asig'];

    //==================================================================================================================
    //imagen
    $stmtIM = $dbh->prepare("SELECT * FROM activosfijosimagen  where codigo =:codigo");
    $stmtIM->bindParam(':codigo',$codigo);
    $stmtIM->execute();
    $resultIM = $stmtIM->fetch();
    //$codigo = $result['codigo'];
    $imagen = $resultIM['imagen'];


    //==================================================================================================================
?>

<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card" style="align-items: center;" >
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <h4 class="card-title"> 
                    <img  class="card-img-top"  src="../marca.png" style="width:100%; max-width:250px;">
                      Asignación De Activo Fijo
                  </h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-condensed" id="tablePaginatorFixed">
                            <tbody>
                                <tr>
                                    <td class="text-left">
                                        <p>
                                            <b>Activo Fijo : </b><?php echo $codigoactivo; ?><br>
                                            <b>Tipo alta : </b><?php echo $tipoalta; ?><br>
                                            <b>Fecha alta : </b><?php echo $fechalta; ?> <br>
                                            <b>Rubro : </b><?php echo $nombre_depreciaciones; ?> <br>
                                            <b>Tipo Bien : </b><?php echo $tipo_bien; ?> <br>
                                            
                                            <b>Descripcion : </b><?php echo $activo; ?><br>
                                            <b>Personal Asignacion : </b><?php echo $nombre_personal; ?> <br>
                                            <b>Unidad Organizacional : </b> <?php echo  $nombre_uo; ?><br>
                                            <b>Fecha Asignacion : </b><?php echo $fechaasignacion; ?><br>
                                            <b>Estado Bien Inicial : </b><?php echo $estadobien; ?><br>
                                            <b>Estado en Asignacion : </b><?php echo  $estadobien_asig; ?>
                                        </p>
                                    </td>
                                    <td class="text-right small">
                                        <img src="imagenes/<?php echo $imagen;?>" style="width: 150px; height: 150px;"><br>
                                    </td>
                                </tr>
                                <hr>
     
                            </tbody>
                        </table>
                    </div>
                </div>
              </div>
            </div>
          </div>  
        </div>
    </div>

<?php 
} catch(PDOException $ex){
    echo "Un error ocurrio".$ex->getMessage();
}
?>