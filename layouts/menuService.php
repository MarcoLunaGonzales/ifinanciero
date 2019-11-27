<?php
  $globalUserX=$_SESSION['globalUser'];
  //echo $globalUserX;
  $globalPerfilX=$_SESSION['globalPerfil'];
  $globalNameUserX=$_SESSION['globalNameUser'];
  $globalNombreUnidadX=$_SESSION['globalNombreUnidad'];
  $globalNombreAreaX=$_SESSION['globalNombreArea'];
  $obj=$_SESSION['globalMenuJson'];
?>
<div class="sidebar" data-color="azure" data-background-color="black" data-image="assets/img/sidebar-1.jpg">
      <div class="logo">
        <a href="http://http://ibnored.ibnorca.org/imonitoreo/" class="simple-text logo-mini">
          <img src="assets/img/logoibnorca.fw.png" width="30" />
        </a>
          <span style="color:white;">iMonitoreo</span>
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
  //echo $id." ".$actividad." ".$pagina."<br>";
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
    if($padre==$id){
      //echo $idSubMenu." ".$padre." ".$actividadSubMenu." ".$paginaSubMenu."<br>";
      ?>

                <li class="nav-item ">
                  <a class="nav-link" href="<?=$paginaSubMenu;?>">
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
?>



                
                



        </ul>
      </div>
    </div>
