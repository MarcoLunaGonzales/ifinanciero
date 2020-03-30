<?php
session_start();
require_once 'conexion.php';
require_once 'functions.php';

$q=$_GET['q'];
$dbh = new Conexion();
    $sql="SELECT p.codigo,CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre)as nombre, p.cod_area, p.cod_unidadorganizacional, pd.perfil, pd.usuario_pon 
      from personal p, personal_datosadicionales pd 
      where p.codigo=pd.cod_personal and p.codigo='$q'";
//echo $sql;
$stmt = $dbh->prepare($sql);
$stmt->execute();
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('cod_area', $codArea);
$stmt->bindColumn('cod_unidadorganizacional', $codUnidad);
$stmt->bindColumn('perfil', $perfil);
$loginU=0;
while ($rowDetalle = $stmt->fetch(PDO::FETCH_BOUND)) {
  $nombreUnidad=abrevUnidad($codUnidad);
  $nombreArea=abrevArea($codArea);
  //SACAMOS LA GESTION ACTIVA
  $sqlGestion="SELECT cod_gestion FROM gestiones_datosadicionales where cod_estado=1";
  $stmtGestion = $dbh->prepare($sqlGestion);
  $stmtGestion->execute();
  while ($rowGestion = $stmtGestion->fetch(PDO::FETCH_ASSOC)) {
    $codGestionActiva=$rowGestion['cod_gestion'];

    $sql1="SELECT cod_mes from meses_trabajo where cod_gestion='$codGestionActiva' and cod_estadomesestrabajo=3";
        $stmt1 = $dbh->prepare($sql1);
        $stmt1->execute();
        while ($row1= $stmt1->fetch(PDO::FETCH_ASSOC)) {
          $codMesActiva=$row1['cod_mes'];
        }
  }
  $nombreGestion=nameGestion($codGestionActiva);

  $_SESSION['globalUser']=$codigo;
  $_SESSION['globalNameUser']=$nombre;
  $_SESSION['globalGestion']=$codGestionActiva;
  $_SESSION['globalMes']=$codMesActiva;
  $_SESSION['globalNombreGestion']=$nombreGestion;


  $_SESSION['globalUnidad']=$codUnidad;
  $_SESSION['globalNombreUnidad']=$nombreUnidad;

  $_SESSION['globalArea']=$codArea;
  $_SESSION['globalNombreArea']=$nombreArea;
  $_SESSION['logueado']=1;
  $_SESSION['globalPerfil']=$perfil;

  if($codigo==90 || $codigo==89 || $codigo==227 || $codigo==195){
    $_SESSION['globalAdmin']=1;     
  }else{
    $_SESSION['globalAdmin']=0; 
  }
  
  $_SESSION['globalServerArchivos']="http://ibnored.ibnorca.org/itranet/documentos/";
  $_SESSION['modulo']=4;


 $host= $_SERVER["HTTP_HOST"];
  ?>
   <!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <link rel="apple-touch-icon" sizes="76x76" href="assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="assets/img/favicon.png">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <title>
    Sistema de Monitoreo y Control - IBNORCA
  </title>
  <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
  <!--     Fonts and icons     -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css">
  <!-- CSS Files -->
  <link href="assets/css/material-dashboard.css?v=2.1.0" rel="stylesheet" />
  <!-- CSS Just for demo purpose, don't include it in your project -->
  <link href="assets/demo/demo.css" rel="stylesheet" />
</head>

<body class="off-canvas-sidebar">
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top text-white">
    <div class="container">
      <div class="navbar-wrapper">
        <a class="navbar-brand" href="#pablo">Se cambio la sesión</a>
      </div>
    </div>
  </nav>
  <!-- End Navbar -->
  <div class="wrapper wrapper-full-page">
    <div class="page-header lock-page header-filter" style="background-image: url('assets/img/lock.jpg')">
      <!--   you can change the color of the filter page using: data-color="blue | green | orange | red | purple" -->
      <div class="container">
        <div class="row">
          <div class="col-md-4 ml-auto mr-auto">
            <div class="card card-profile text-center card-hidden">
              <div class="card-header ">
                <div class="card-avatar">
                  <a href="#pablo">
                    <img class="img" src="assets/img/faces/persona1.png">
                  </a>
                </div>
              </div>
              <div class="card-body ">
                <h4 class="card-title"><?=$nombre?></h4>
                <div class="form-group">
                  <label for="exampleInput1" class="bmd-label-floating">Oficina - Area</label>
                  <input type="text" readonly class="form-control" id="exampleInput1" value="<?=$nombreUnidad?> <?=$nombreArea?>">
                </div>
              </div>
              <?php
              if(isset($_GET['url'])){
                  $url= $_GET['url'];
                  $irURL="index.php?opcion=".$url."&q=".$q;
                  //header("location:".$url."&q=".$q); 
                  ?>
                    <div class="card-footer justify-content-center">
                     <a href="<?=$irURL?>" class="btn btn-warning btn-round">Sesión Cambiada / Ir al link</a>
                   </div>
                  <?php
                }else{
                  ?>
                  <div class="card-footer justify-content-center">
                   <a href="#" class="btn btn-rose btn-round">Sesión Cambiada</a>
                 </div>
                  <?php
                }?>
              
            </div>
          </div>
        </div>
      </div>
      <footer class="footer">
        <div class="container">
        </div>
      </footer>
    </div>
  </div>
  <!--   Core JS Files   -->
  <script src="assets/js/core/jquery.min.js"></script>
  <script src="assets/js/core/popper.min.js"></script>
  <script src="assets/js/core/bootstrap-material-design.min.js"></script>
  <script src="assets/js/plugins/perfect-scrollbar.jquery.min.js"></script>
  <!--  Google Maps Plugin    -->
  <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE"></script>
  <!-- Chartist JS -->
  <script src="assets/js/plugins/chartist.min.js"></script>
  <!--  Notifications Plugin    -->
  <script src="assets/js/plugins/bootstrap-notify.js"></script>
  <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="assets/js/material-dashboard.js?v=2.1.0" type="text/javascript"></script>
  <!-- Material Dashboard DEMO methods, don't include it in your project! -->
  <script src="assets/demo/demo.js"></script>
  <script>
    $(document).ready(function() {
      $().ready(function() {
        $sidebar = $('.sidebar');

        $sidebar_img_container = $sidebar.find('.sidebar-background');

        $full_page = $('.full-page');

        $sidebar_responsive = $('body > .navbar-collapse');

        window_width = $(window).width();

        fixed_plugin_open = $('.sidebar .sidebar-wrapper .nav li.active a p').html();

        if (window_width > 767 && fixed_plugin_open == 'Dashboard') {
          if ($('.fixed-plugin .dropdown').hasClass('show-dropdown')) {
            $('.fixed-plugin .dropdown').addClass('open');
          }

        }

        $('.fixed-plugin a').click(function(event) {
          // Alex if we click on switch, stop propagation of the event, so the dropdown will not be hide, otherwise we set the  section active
          if ($(this).hasClass('switch-trigger')) {
            if (event.stopPropagation) {
              event.stopPropagation();
            } else if (window.event) {
              window.event.cancelBubble = true;
            }
          }
        });

        $('.fixed-plugin .active-color span').click(function() {
          $full_page_background = $('.full-page-background');

          $(this).siblings().removeClass('active');
          $(this).addClass('active');

          var new_color = $(this).data('color');

          if ($sidebar.length != 0) {
            $sidebar.attr('data-color', new_color);
          }

          if ($full_page.length != 0) {
            $full_page.attr('filter-color', new_color);
          }

          if ($sidebar_responsive.length != 0) {
            $sidebar_responsive.attr('data-color', new_color);
          }
        });

        $('.fixed-plugin .background-color .badge').click(function() {
          $(this).siblings().removeClass('active');
          $(this).addClass('active');

          var new_color = $(this).data('background-color');

          if ($sidebar.length != 0) {
            $sidebar.attr('data-background-color', new_color);
          }
        });

        $('.fixed-plugin .img-holder').click(function() {
          $full_page_background = $('.full-page-background');

          $(this).parent('li').siblings().removeClass('active');
          $(this).parent('li').addClass('active');


          var new_image = $(this).find("img").attr('src');

          if ($sidebar_img_container.length != 0 && $('.switch-sidebar-image input:checked').length != 0) {
            $sidebar_img_container.fadeOut('fast', function() {
              $sidebar_img_container.css('background-image', 'url("' + new_image + '")');
              $sidebar_img_container.fadeIn('fast');
            });
          }

          if ($full_page_background.length != 0 && $('.switch-sidebar-image input:checked').length != 0) {
            var new_image_full_page = $('.fixed-plugin li.active .img-holder').find('img').data('src');

            $full_page_background.fadeOut('fast', function() {
              $full_page_background.css('background-image', 'url("' + new_image_full_page + '")');
              $full_page_background.fadeIn('fast');
            });
          }

          if ($('.switch-sidebar-image input:checked').length == 0) {
            var new_image = $('.fixed-plugin li.active .img-holder').find("img").attr('src');
            var new_image_full_page = $('.fixed-plugin li.active .img-holder').find('img').data('src');

            $sidebar_img_container.css('background-image', 'url("' + new_image + '")');
            $full_page_background.css('background-image', 'url("' + new_image_full_page + '")');
          }

          if ($sidebar_responsive.length != 0) {
            $sidebar_responsive.css('background-image', 'url("' + new_image + '")');
          }
        });

        $('.switch-sidebar-image input').change(function() {
          $full_page_background = $('.full-page-background');

          $input = $(this);

          if ($input.is(':checked')) {
            if ($sidebar_img_container.length != 0) {
              $sidebar_img_container.fadeIn('fast');
              $sidebar.attr('data-image', '#');
            }

            if ($full_page_background.length != 0) {
              $full_page_background.fadeIn('fast');
              $full_page.attr('data-image', '#');
            }

            background_image = true;
          } else {
            if ($sidebar_img_container.length != 0) {
              $sidebar.removeAttr('data-image');
              $sidebar_img_container.fadeOut('fast');
            }

            if ($full_page_background.length != 0) {
              $full_page.removeAttr('data-image', '#');
              $full_page_background.fadeOut('fast');
            }

            background_image = false;
          }
        });

        $('.switch-sidebar-mini input').change(function() {
          $body = $('body');

          $input = $(this);

          if (md.misc.sidebar_mini_active == true) {
            $('body').removeClass('sidebar-mini');
            md.misc.sidebar_mini_active = false;

            $('.sidebar .sidebar-wrapper, .main-panel').perfectScrollbar();

          } else {

            $('.sidebar .sidebar-wrapper, .main-panel').perfectScrollbar('destroy');

            setTimeout(function() {
              $('body').addClass('sidebar-mini');

              md.misc.sidebar_mini_active = true;
            }, 300);
          }

          // we simulate the window Resize so the charts will get updated in realtime.
          var simulateWindowResize = setInterval(function() {
            window.dispatchEvent(new Event('resize'));
          }, 180);

          // we stop the simulation of Window Resize after the animations are completed
          setTimeout(function() {
            clearInterval(simulateWindowResize);
          }, 1000);

        });
      });
    });
  </script>
  <script>
    $(document).ready(function() {
      md.checkFullPageBackgroundImage();
      setTimeout(function() {
        // after 1000 ms we add the class animated to the login/register card
        $('.card').removeClass('card-hidden');
      }, 700);
    });
  </script>
</body>

</html> 
<?php
$loginU=1;
}
 
if($loginU==0){
  header("location:error.html");
} 