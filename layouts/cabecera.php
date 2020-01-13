<!-- Navbar -->
      <nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top ">
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
            <h6>Gestión Trabajo: </h6>&nbsp;<h4 class="text-danger font-weight-bold">[<?=$globalNombreGestion;?>]</h4>&nbsp;&nbsp;&nbsp;
            <h6>Mes Trabajo: </h6>&nbsp;<h4 class="text-danger font-weight-bold">[<?=$globalMes;?>]</h4>&nbsp;&nbsp;&nbsp;
            <h6>Unidad: </h6>&nbsp;<h4 class="text-danger font-weight-bold">[ <?=$globalNombreUnidad;?> ]</h4> &nbsp;&nbsp; <h6>Area: </h6>&nbsp;<h4 class="text-danger font-weight-bold">[ <?=$globalNombreArea;?> ]</h4>
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
require_once 'notificaciones_sistema/PHPMailer/send.php';
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

 if($contMonedas==0){
  $html='<label class="dropdown-item">No hay Notificaciones</label>';
 $numeroNot='';  
 }else{
 $numeroNot='<span class="notification">'.$contMonedas.'</span>'; 
 }
 ?>
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
            </ul>
          </div>
        </div>
      </nav>
<!-- End Navbar -->