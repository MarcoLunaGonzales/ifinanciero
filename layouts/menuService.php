<?php
//include("functionsGeneral.php");

$globalUserX=$_SESSION['globalUser'];
//echo $globalUserX;
$globalPerfilX=$_SESSION['globalPerfil'];
$globalNameUserX=$_SESSION['globalNameUser'];
$globalNombreUnidadX=$_SESSION['globalNombreUnidad'];
$globalNombreAreaX=$_SESSION['globalNombreArea'];
$obj=$_SESSION['globalMenuJson'];
$menuModulo=$_SESSION['modulo'];

$nombreModuloMenu="";

switch ($menuModulo) {
  case 1:
   $nombreModulo="RRHH";
   $estiloMenu="rojo";
   $nombreModuloMenu="Remuneracion";
  break;
  case 2:
    $nombreModulo="Activos Fijos";
    $estiloMenu="amarillo";
    $nombreModuloMenu="ActivosFijos";
  break;
  case 3:
    $nombreModulo="Contabilidad";
    $estiloMenu="celeste";
    $nombreModuloMenu="Contabilidad";
  break;
  case 4:
    $nombreModulo="Presupuestos / Solicitudes";
    $estiloMenu="verde";
    $nombreModuloMenu="Presupuesto";
  break;
}

if($menuModulo==0){
?>
 <script>window.location.href="index.php";</script>
<?php
}
?>

<div class="sidebar" data-color="purple" data-background-color="<?=$estiloMenu?>" data-image="assets/img/scz.jpg">
      <div class="logo">
        <a href="http://http://ibnored.ibnorca.org/ifinanciero/" class="simple-text logo-mini">
          <img src="assets/img/logo_ibnorca1.fw.png" width="30" />
        </a>
        <a href="index.php" class="simple-text logo-normal">
          ADM & FIN & OP
        </a>
      </div>
      <div class="sidebar-wrapper">
        <div class="user">
          <div class="photo">
            <img src="assets/img/faces/persona1.png" />
          </div>
          <div class="user-info">
            <a data-toggle="collapse" href="#collapseExample" class="username">
              <span>
                <?=$globalNameUserX;?>
                <!--b class="caret"></b-->
              </span>
            </a>
          </div>
        </div>

        <ul class="nav">

<?php
$detalle=$obj->menus->menu;
foreach ($detalle as $objDet){
  $id=$objDet->id;
  $actividad=$objDet->actividad;
  $actividad=ucwords(strtolower($actividad));
  $pagina=$objDet->pagina;
  $icono=$objDet->icono;
  $moduloWS=$objDet->modulo;
  //echo $id." ".$actividad." ".$pagina."<br>";
  if($moduloWS==$nombreModuloMenu){
?>
          <li class="nav-item ">
            <a class="nav-link" data-toggle="collapse" href="#<?=$id;?>">
              <i class="material-icons"><?=$icono;?></i>
              <p> <?=$actividad;?>
                <b class="caret"></b>
              </p>
            </a>

            <div class="collapse" id="<?=$id;?>">
              <ul class="nav"><!--hasta aqui el menu 1ra parte-->

  <?php 
  $detalleNivel2=$obj->menus->subm;
  foreach($detalleNivel2 as $objDetN2){
    $idSubMenu=$objDetN2->id;
    $padre=$objDetN2->padre;
    $actividadSubMenu=$objDetN2->actividad;
    $paginaSubMenu=$objDetN2->pagina;
    $iconoSubMenu=$objDetN2->icono;
    $txtNuevaVentana="";
    if($paginaSubMenu=="reportes_ventas/index.php"){
      $txtNuevaVentana="target='_blank'";
    }
    if($padre==$id && $moduloWS==$nombreModuloMenu){
      //echo $idSubMenu." ".$padre." ".$actividadSubMenu." ".$paginaSubMenu."<br>";
      ?>

                <li class="nav-item ">
                  <a class="nav-link" href="<?=$paginaSubMenu;?>" <?=$txtNuevaVentana;?>>
                    <span class="sidebar-mini"> <?=$iconoSubMenu;?> </span>
                    <span class="sidebar-normal"> <?=$actividadSubMenu; ?> </span>
                  </a>
                </li>

      <?php
    }
  }
?>

              <!--PARTE FINAL DE CADA MENU-->  
              </ul>
            </div>
          </li>
<?php
  }
}
?>



                
                



        </ul>
      </div>
    </div>
