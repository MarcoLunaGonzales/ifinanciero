<?php 
if(isset($_GET['q'])){
       $q=$_GET['q'];
       $url= $_GET["opcion"];
 ?><!--<a class='flotante' title="Cambiar la sesión" href='#'><img src='assets/img/nuevoUser.svg' width="35" height="35" border="0" onclick="alerts.showSwal('warning-message-change-user','change.php?q=<?=$q;?>&url=<?=$url?>')"/></a>--><?php
 $urlListGestionTrabajo="#";
$urllistUnidadOrganizacional="#";
$urmesCurso="#";
$urmesCurso2="#";
}else{
 $urlListGestionTrabajo="index.php?opcion=listGestionTrabajo";
$urllistUnidadOrganizacional="index.php?opcion=listUnidadOrganizacional";
$urmesCurso="index.php?opcion=mesCurso";
$urmesCurso2="index.php?opcion=mesCurso2"; 
}

?>
<div class="main-panel">
<!-- Navbar -->
      <nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top" style="z-index: -1;">
        <div class="container-fluid">
          <div class="navbar-wrapper">
            <div class="navbar-minimize">
              <button id="minimizeSidebar" class="btn btn-just-icon btn-white btn-fab btn-round">
                <i class="material-icons text_align-center visible-on-sidebar-regular">more_vert</i>
                <i class="material-icons design_bullet-list-67 visible-on-sidebar-mini">view_list</i>
              </button>
            </div>
            <a class="navbar-brand" href="#pablo"></a>
          </div>
          <button class="navbar-toggler" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
            <span class="sr-only">Toggle navigation</span>
            <span class="navbar-toggler-icon icon-bar"></span>
            <span class="navbar-toggler-icon icon-bar"></span>
            <span class="navbar-toggler-icon icon-bar"></span>
          </button>
            <?php 
              $globalNombreGestion=$_SESSION['globalNombreGestion'];
              $globalMes=$_SESSION['globalMes'];
              $globalNombreUnidad=$_SESSION['globalNombreUnidad'];
              $globalNombreArea=$_SESSION['globalNombreArea'];
              $fechaSistema=date("d/m/Y");
              $horaSistema=date("H:i");
            ?>
            

            <h6>Gesti&oacute;n Trabajo: </h6>&nbsp;<h4 class="text-danger font-weight-bold"><a title="Cambiar Gestión de Trabajo" style="color:#FF0000; " href='<?=$urlListGestionTrabajo?>' >[<?=$globalNombreGestion;?>]</a></h4>
            &nbsp;&nbsp;&nbsp;
            <h6>Mes Trabajo: </h6>&nbsp;<h4 class="text-danger font-weight-bold"><a title="Cambiar Mes de Trabajo" style="color:#FF0000; " href='<?=$urmesCurso2?>' >[<?=$globalMes;?>]</a></h4>&nbsp;&nbsp;&nbsp;
            <h6>Unidad: </h6>&nbsp;<h4 class="text-danger font-weight-bold"><a title="Cambiar Oficina de Trabajo" style="color:#FF0000; " href='<?=$urllistUnidadOrganizacional?>' >[ <?=$globalNombreUnidad;?> ]</a></h4> &nbsp;&nbsp; <h6>Area: </h6>&nbsp;<h4 class="text-danger font-weight-bold"><a style="color:#FF0000; " href='#' >[ <?=$globalNombreArea;?> ]</a></h4>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          
          <div class="collapse navbar-collapse justify-content-end">
            <ul class="navbar-nav">
              <!--li class="nav-item">
                <a class="nav-link" href="#pablo">
                  <i class="material-icons">dashboard</i>
                  <p class="d-lg-none d-md-block">
                    Stats
                  </p>
                </a>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link" href="http://example.com" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="material-icons">notifications</i>
                  <span class="notification">1</span>
                  <p class="d-lg-none d-md-block">
                    Some Actions
                  </p>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                  <a class="dropdown-item" href="#">RCB no registro la ejecucion del mes en curso</a>
                </div>
              </li-->
<?php
require_once 'conexion.php';
require_once 'functions.php';
require_once 'functionsGeneral.php';

//verificar si hay bonos indefinidos
bonosIndefinidos();

//enviar alertas a correos
//enviarNotificacionesSistema(1);

$fechaActual=date("Y-m-d");
$dbh = new Conexion();

// Preparamos
$stmt = $dbh->prepare("SELECT codigo,nombre,abreviatura,cod_estadoreferencial from monedas");
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('cod_estadoreferencial', $codEstadoRef);
$stmt->bindColumn('codigo', $codigoMon);
$stmt->bindColumn('abreviatura', $abreviaturaMon);
$stmt->bindColumn('nombre', $nombreMon);
$html="";
$contMonedas=0;
while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
    if($codigoMon!=1){
      $valorTipo=obtenerValorTipoCambio($codigoMon,$fechaActual);
      if($valorTipo==0){
        $html.='<a class="dropdown-item" href="?opcion=tipoDeCambio">No hay valores en '.$nombreMon.'</a>';
        $contMonedas++;
       }
     }
 }
//***clientes MORA
/*
$stmtCMora = $dbh->prepare("SELECT codigo from clientes_mora where cod_estado=1 limit 1");
$stmtCMora->execute();
$stmtCMora->bindColumn('codigo', $codigoMora);
while ($rowMora = $stmtCMora->fetch(PDO::FETCH_BOUND)) {
  $html.='<a class="dropdown-item" target="_blank" href="clientes_mora/reporte_clientesMora.php">Nuevos Clientes Mora</a>';
  $contMonedas++; 
}
*/

if($contMonedas==0){
  $html='<label class="dropdown-item">No hay Notificaciones</label>';
  $numeroNot='';  
}else{
  $numeroNot='<span class="notification">'.$contMonedas.'</span>'; 
}
 
if(!isset($_GET['q'])){?>

              <li class="nav-item dropdown">
                <a class="nav-link" href="http://example.com" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="material-icons">notifications</i>
                  <?=$numeroNot?>
                  <p class="d-lg-none d-md-block">
                    Some Actions
                  </p>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                  <?=$html?>
                  <?php 
                  if(isset($_SESSION['globalUser'])&&$_SESSION['globalUser']==227){ //IDPERSONA IVONNE 227

                    echo '<div class="dropdown-divider"></div><a class="dropdown-item" href="index.php?opcion=reportesSolicitudRecursosSis"><small>REPORTE CONTROL SR - PROY SIS</small></a>';
                    echo '<a class="dropdown-item" href="index.php?opcion=listComprobantesSis"><small>COMPROBANTES - PROY SIS</small></a>';
                  }
                  ?>
                </div>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link" href="#pablo" id="navbarDropdownProfile" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="material-icons">person</i>
                  <p class="d-lg-none d-md-block">
                    Account
                  </p>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownProfile">
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="logout.php">Salir</a>
                </div>
              </li>
              <?php
               } ?>
            </ul>
          </div>
        </div>
      </nav>
<!-- End Navbar -->