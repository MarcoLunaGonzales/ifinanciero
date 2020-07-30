  <!--   Core JS Files   -->
<link rel='stylesheet' href='https://use.fontawesome.com/releases/v5.7.0/css/all.css' integrity='sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ' crossorigin='anonymous'>

  <script src="assets/js/core/jquery.min.js"></script>
  <script src="assets/js/core/jquery-ui.min.js"></script>
  <script src="assets/js/core/popper.min.js"></script>
  <script src="assets/js/core/bootstrap-material-design.min.js"></script>
  <script src="assets/js/plugins/perfect-scrollbar.jquery.min.js"></script>
  <!-- Plugin for the momentJs  -->
  <script src="assets/js/plugins/moment.min.js"></script>
  <!--  Plugin for Sweet Alert -->
  <script src="assets/js/plugins/sweetalert2.js"></script>
  <!-- Forms Validations Plugin -->
  <script src="assets/js/plugins/jquery.validate.min.js"></script>
  <!-- Plugin for the Wizard, full documentation here: https://github.com/VinceG/twitter-bootstrap-wizard -->
  <script src="assets/js/plugins/jquery.bootstrap-wizard.js"></script>
  <!--	Plugin for Select, full documentation here: http://silviomoreto.github.io/bootstrap-select -->
  <script src="assets/js/plugins/bootstrap-selectpicker.js"></script>
  <!--  Plugin for the DateTimePicker, full documentation here: https://eonasdan.github.io/bootstrap-datetimepicker/ -->
  <script src="assets/js/plugins/bootstrap-datetimepicker.min.js"></script>
  <!--  DataTables.net Plugin, full documentation here: https://datatables.net/  -->
  <script src="assets/js/plugins/jquery.dataTables.min.js"></script>
  <script src="assets/js/plugins/dataTables.fixedHeader.min.js"></script>

  <!--	Plugin for Tags, full documentation here: https://github.com/bootstrap-tagsinput/bootstrap-tagsinputs  -->
  <script src="assets/js/plugins/bootstrap-tagsinput.js"></script>
  <!-- Plugin for Fileupload, full documentation here: http://www.jasny.net/bootstrap/javascript/#fileinput -->
  <script src="assets/js/plugins/jasny-bootstrap.min.js"></script>
  <!--  Full Calendar Plugin, full documentation here: https://github.com/fullcalendar/fullcalendar    -->
  <script src="assets/js/plugins/fullcalendar.min.js"></script>
  <!-- Vector Map plugin, full documentation here: http://jvectormap.com/documentation/ -->
  <script src="assets/js/plugins/jquery-jvectormap.js"></script>
  <!--  Plugin for the Sliders, full documentation here: http://refreshless.com/nouislider/ -->
  <script src="assets/js/plugins/nouislider.min.js"></script>
  <!-- Include a polyfill for ES6 Promises (optional) for IE11, UC Browser and Android browser support SweetAlert -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/core-js/2.4.1/core.js"></script>
  <!-- Library for adding dinamically elements -->
  <script src="assets/js/plugins/arrive.min.js"></script>
  <!--  Google Maps Plugin    -->
  <!--script src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE"></script-->
  <!-- Chartist JS -->
  <script src="assets/js/plugins/chartist.min.js"></script>
  <!--  Notifications Plugin    -->
  <script src="assets/js/plugins/bootstrap-notify.js"></script>
  <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="assets/js/material-dashboard.js?v=2.1.0"></script>
  <script src="assets/autocomplete/awesomplete.min.js"></script>
  <!-- Material Dashboard DEMO methods, don't include it in your project! -->
  <script src="assets/alerts/alerts.js"></script>

  <script src="assets/alerts/functionsGeneral.js"></script>

  <script src="assets/demo/demo.js"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.min.js"></script>



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
      // Initialise Sweet Alert library
      alerts.showSwal();
    });
	
    $(document).ready(function() {
      // Javascript method's body can be found in assets/assets-for-demo/js/demo.js
      demo.initCharts();
    });
  </script>

  <!--DEFINIMOS EL DATATABLE PARA LA ORDENACION Y PAGINACION-->
  <script type="text/javascript">
    $(document).ready(function() {

        $('#tablePaginator').DataTable( {
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
            },
            "ordering": false/*,
             'scrollY': '70vh', 
             'scrollCollapse': false,
             "scrollX": false*/
        } );
        $('#tablePaginator50NoFinder').DataTable( {
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
            },
            fixedHeader: {
              header: true
            },
            "ordering": false,
            "searching":false
        } );
        $('#tablePaginator100').DataTable( {
            "pageLength": 100,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
            },
            fixedHeader: {
              header: true,
              footer: true
            },
            "ordering": false
        } );
        
        $('#tablePaginatorHead').DataTable( {
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
            },
            fixedHeader: {
              header: true,
              footer: true
            }
        } );

        // Setup - add a text input to each footer cell
        $('#libreta_bancaria_reporte_modal tfoot th').each( function () {
            var title = $(this).text();
            $(this).html( '<input type="text" placeholder="'+title+'" />' );
        } );
     
        // DataTable
        var table = $('#libreta_bancaria_reporte_modal').DataTable({
            initComplete: function () {
                // Apply the search
                this.api().columns().every( function () {
                    var that = this;
                    $( 'input', this.footer() ).on( 'keyup change clear', function () {
                        if ( that.search() !== this.value ) {
                            that
                                .search( this.value )
                                .draw();
                        }
                    });
                });
            },
            "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
            },
            fixedHeader: {
                  header: false,
                  footer: false
            },
            "order": false,
            "paging":   false,
            "info":     false,          
            "scrollY":        "400px",
            "scrollCollapse": true
        });

        $('#modalListaLibretaBancaria').on('shown.bs.modal', function(e){
           $($.fn.dataTable.tables(true)).DataTable()
              .columns.adjust();
        });

        $('#libreta_bancaria_reporte tfoot th').each( function () {
            var title = $(this).text();
            $(this).html( '<input type="text" placeholder="'+title+'" />' );
        } );
     
        // DataTable
        var table = $('#libreta_bancaria_reporte').DataTable({
            initComplete: function () {
                // Apply the search
                this.api().columns().every( function () {
                    var that = this;
                    $( 'input', this.footer() ).on( 'keyup change clear', function () {
                        if ( that.search() !== this.value ) {
                            that
                                .search( this.value )
                                .draw();
                        }
                    });
                });
            },
            "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
            },
            fixedHeader: {
                  header: true,
                  footer: true
            },
            "order": false,
            "paging":   false,
            "info":     false
            //"searching": false
        });

        if ($('#cuenta_auto').length) {
          autocompletar("cuenta_auto","cuenta_auto_id",array_cuenta);
        }
    } );
  </script>

<script type="text/javascript">
    $(document).ready(function() {
      data_cuentas= $('#data_cuentas').DataTable({
        columnDefs: [{
        orderable: false,
        targets: [0]
        }],
          "pageLength": 50
         }
      );
      data_cuentas_ver= $('#data_cuentas_2').DataTable({
        "paging": false ,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
        }
        }
      );
      $("#form-pagos").submit(function(e) {
        var mensaje="";
        if($("#cantidad_filas").val()==0){
          Swal.fire("Informativo!", "Debe registrar al menos un pago", "warning");
          return false;
        }else{
          var cont=0;
          for (var i = 0; i < $("#cantidad_filas").val(); i++) {
             if(parseFloat($('#monto_pago'+(i+1)).val())>parseFloat($('#saldo_pago'+(i+1)).text())){
               cont++;
               break;   
             }                  
          }
          if(cont!=0){
             Swal.fire("Informativo!", "Uno de los montos ingresados es mayor al saldo", "warning"); 
             return false;
          }else{
            var conta=0;
            for (var i = 0; i < $("#cantidad_filas").val(); i++) {
              if($("#monto_pago"+(i+1)).val()>0){
               if(($('#tipo_pago'+(i+1)).val()>0)){
                if(($('#tipo_pago'+(i+1)).val()==1)){
                  if($("#banco_pago"+(i+1)).val()>0){
                   if(($('#emitidos_pago'+(i+1)).val()=="####")){
                     conta++;
                     break;
                   }
                  }else{
                    conta++;
                    break;
                  }
                }       
               }else{
                conta++;
                break;
               }
              } 
           }
           if(conta!=0){
             Swal.fire("Informativo!", "Faltan datos!", "warning"); 
             return false;
           }else{

           }          
          }
        }     
      });

      $("#form_partidaspresupuestarias").submit(function(e) {
          var datos=alertDatosTabla();
          $('<input />').attr('type', 'hidden')
            .attr('name', 'cuentas2')
            .attr('value', JSON.stringify(datos))
            .appendTo('#form_partidaspresupuestarias');     
      });
      $("#form_partidaspresupuestariasCC").submit(function(e) {
          var datos=cuentas_tabla;
          $('<input />').attr('type', 'hidden')
            .attr('name', 'cuentas2')
            .attr('value', JSON.stringify(datos))
            .appendTo('#form_partidaspresupuestariasCC');     
      });

      $("#form_partidaspresupuestariasSR").submit(function(e) {
          var datos=cuentas_tabla;
          $('<input />').attr('type', 'hidden')
            .attr('name', 'cuentas2')
            .attr('value', JSON.stringify(datos))
            .appendTo('#form_partidaspresupuestariasSR');     
      });
     $("#form_bonosgrupos").submit(function(e) {
          $('<input />').attr('type', 'hidden')
            .attr('name', 'montos')
            .attr('value', JSON.stringify(montos_personal))
            .appendTo('#form_bonosgrupos');   
      });
     $("#form_anticipospersonal").submit(function(e) {
         if($("#monto").val()>$("#haber_basico2").val()){
          $("#mensaje").html("<p class='text-danger'>El monto no puede ser mayor al 50% del haber b&aacute;sico</p>");
          return false;
         }
      });
      $("#formSoliFactTcp").submit(function(e) {        
        if($("#total_monto_bob_a_tipopago").val()){//existe array de objetos tipopago          
          var tipo_solicitud=$("#tipo_solicitud").val();          
          if(tipo_solicitud==2){            
            var montoTotalItems=$("#modal_totalmontoserv_costo_a").val();
          }else{
            var montoTotalItems=$("#monto_total_a").val();
          }
          var monto_modal_por_tipopago=$("#total_monto_bob_a_tipopago").val();
          //si existe array de objetos transformarlo a json
          $('<input />').attr('type', 'hidden')
            .attr('name', 'tiposPago_facturacion')
            .attr('value', JSON.stringify(itemTipoPagos_facturacion))
            .appendTo('#formSoliFactTcp');
          // validamos que obligue insertar archivos en caso de forma de pago deposito
          var cod_defecto_deposito_cuenta=$("#cod_defecto_deposito_cuenta").val();
          for(var j = 0; j < itemTipoPagos_facturacion[0].length; j++){
            var dato = Object.values(itemTipoPagos_facturacion[0][j]);
            var cod_tipopago_x=dato[0];
            if(cod_tipopago_x==cod_defecto_deposito_cuenta){
              if($("#cantidad_archivosadjuntos").val()==0){
                var msg = "Por favor agregue Archivo Adjunto.";
                $('#msgError').html(msg);
                $('#modalAlert').modal('show'); 
                return false;  
              }
            }            
          }
          if(monto_modal_por_tipopago!=0){
            if(montoTotalItems!=monto_modal_por_tipopago){
              var mensaje="<p>Por favor verifique los montos de la distribución de porcentajes en Formas de Pago...</p>";
              $('#msgError').html(mensaje);
              $('#modalAlert').modal('show'); 
              return false;  
            }else{
              if($("#total_monto_bob_a_areas").val()){
                var tipo_solicitud=$("#tipo_solicitud").val();          
                if(tipo_solicitud==2){            
                  var montoTotalItems=$("#modal_totalmontoserv_costo_a").val();
                }else{
                  var montoTotalItems=$("#monto_total_a").val();
                }
                var monto_modal_por_area=$("#total_monto_bob_a_areas").val();
                var sw_x=true;//para ver la cantidad de las unidades
                var mensaje='<p>Por favor verifique los montos de la distribución de porcentajes en Unidades...<p>';
                //si existe array de objetos areas
                $('<input />').attr('type', 'hidden')
                .attr('name', 'areas_facturacion')
                .attr('value', JSON.stringify(itemAreas_facturacion))
                .appendTo('#formSoliFactTcp');
                 //si existe array de objetos unidades//falta hacer sus alertas
                $('<input />').attr('type', 'hidden')
                .attr('name', 'unidades_facturacion')
                .attr('value', JSON.stringify(itemUnidades_facturacion))
                .appendTo('#formSoliFactTcp');
                for (var i =0;i < itemUnidades_facturacion.length; i++) {              
                  var dato = Object.values(itemUnidades_facturacion[i]);
                  if(dato!=''){                
                    var monto_total_unidades=0;              
                    var datoArea = Object.values(itemAreas_facturacion[0][i]);                
                    var monto_area=datoArea[2];              
                    for(var j = 0; j < itemUnidades_facturacion[i].length; j++){
                      var dato2 = Object.values(itemUnidades_facturacion[i][j]);
                      monto_total_unidades=monto_total_unidades+parseFloat(dato2[2]);
                    }
                    if(monto_area!=monto_total_unidades){
                      // alert(monto_area+"-"+monto_total_unidades);
                      sw_x=false;
                    }

                  }      
                }
                if(!sw_x){ 
                  $('#msgError').html(mensaje);
                  $('#modalAlert').modal('show');               
                  return false;    
                }          
                if(monto_modal_por_tipopago!=0){
                  if(montoTotalItems!=monto_modal_por_area){
                    var mensaje="<p>Por favor verifique los montos de la distribución de porcentajes en Areas...</p>";
                    $('#msgError').html(mensaje);
                    $('#modalAlert').modal('show'); 
                    return false;
                  }
                }
              }
            }
          }          
        }else{         
          if($("#total_monto_bob_a_areas").val()){            
            var tipo_solicitud=$("#tipo_solicitud").val();          
            if(tipo_solicitud==2){            
              var montoTotalItems=$("#modal_totalmontoserv_costo_a").val();
            }else{
              var montoTotalItems=$("#monto_total_a").val();
            }
            var monto_modal_por_area=$("#total_monto_bob_a_areas").val();
            var sw_x=true;//para ver la cantidad de las unidades
            var mensaje='<p>Por favor verifique los montos de la distribución de porcentajes en Unidades...<p>';
            //si existe array de objetos areas
            $('<input />').attr('type', 'hidden')
            .attr('name', 'areas_facturacion')
            .attr('value', JSON.stringify(itemAreas_facturacion))
            .appendTo('#formSoliFactTcp');
            //si existe array de objetos unidades
            $('<input />').attr('type', 'hidden')
            .attr('name', 'unidades_facturacion')
            .attr('value', JSON.stringify(itemUnidades_facturacion))
            .appendTo('#formSoliFactTcp');
            
            for (var i =0;i < itemUnidades_facturacion.length; i++) {              
              var dato = Object.values(itemUnidades_facturacion[i]);
              if(dato!=''){                
                var monto_total_unidades=0;              
                var datoArea = Object.values(itemAreas_facturacion[0][i]);                
                var monto_area=datoArea[2];              
                for(var j = 0; j < itemUnidades_facturacion[i].length; j++){
                  var dato2 = Object.values(itemUnidades_facturacion[i][j]);
                  monto_total_unidades=monto_total_unidades+parseFloat(dato2[2]);
                }
                if(monto_area!=monto_total_unidades){
                  // alert(monto_area+"-"+monto_total_unidades);
                  sw_x=false;
                }

              }      
            }
            if(!sw_x){ 
              $('#msgError').html(mensaje);
              $('#modalAlert').modal('show');               
              return false;    
            }
            
            if(monto_modal_por_tipopago!=0){
              if(montoTotalItems!=monto_modal_por_area){
                var mensaje="<p>Por favor verifique los montos de la distribución de porcentajes en Areas...</p>";
                $('#msgError').html(mensaje);
                $('#modalAlert').modal('show'); 
                return false;
              }

            }

          }
        }
      });      
      $("#formDetalleCajaChica").submit(function(e) {      
        $('<input />').attr('type', 'hidden')
          .attr('name', 'd_oficinas')
          .attr('value', JSON.stringify(itemDistOficina))
          .appendTo('#formDetalleCajaChica');
        $('<input />').attr('type', 'hidden')
          .attr('name', 'd_areas')
          .attr('value', JSON.stringify(itemDistArea))
          .appendTo('#formDetalleCajaChica');        
      });
    } );
  </script>

  
  <script type="text/javascript">



    $(document).ready(function() {
      $("#con_fac").mask("AA-AA-AA-AA-AA-AA-AA");
      $("#formRegFactCajaChica").submit(function(e) {
      $('<input />').attr('type', 'hidden')
            .attr('name', 'facturas')
            .attr('value', JSON.stringify(itemFacturasDCC))
            .appendTo('#formRegFactCajaChica');
      
      });
      // document.getElementById('qrquincho').addEventListener('change', readSingleFileDCC, false);

       $("#formRegFactRendiciones").submit(function(e) {
      $('<input />').attr('type', 'hidden')
            .attr('name', 'facturas')
            .attr('value', JSON.stringify(itemFacturasDRC))
            .appendTo('#formRegFactRendiciones');
      
      });
      // document.getElementById('qrquincho').addEventListener('change', readSingleFileDRC, false);    

       $("#formChequePago").submit(function(e) {
          var inicioo =parseFloat($("#inicio").val());
          var finall =parseFloat($("#final").val());
          if(inicioo>finall){
            return false;
            Swal.fire("Informativo!", "El NRO Inicio no debe ser mayor al NRO Final", "warning");      
          }
       
      });

 
      
        $('#tablePaginator50').DataTable( {
            "pageLength": 50,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
            }
        } );
    } );
  </script>
  <script type="text/javascript">
    $(document).ready(function() {
        $('#tablePaginator50_2').DataTable( {
            "pageLength": 50,

            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
            },
            fixedHeader: {
              header: true,
              footer: true
            }
        } );
    } );
    
  </script>

  <script type="text/javascript">
    $(document).ready(function() {
        $('#tablePaginatorFixed').DataTable({
            "paging":   false,
            "ordering": false,
            "info":     false,
            "searching": false,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
            },
            fixedHeader: {
              header: true,
              footer: true
            }
        } );
    } );
  </script>

  <script type="text/javascript">
    $(document).ready(function() {
        $('#tablePaginatorReport').DataTable({
            "paging":   false,
            "info":     false,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
            },
            fixedHeader: {
              header: true,
              footer: true
            },
            dom: 'Bfrtip',
            buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        } );
    } );
  </script>


  <!--script type="text/javascript">
    $(document).ready(function() {
        $('#tablePaginatorForm').DataTable(); 
            $('button').click( function() {
                var data = table.$('input, select').serialize();
                alert(
                    "The following data would have been submitted to the server: \n\n"+
                    data.substr( 0, 120 )+'...'
                );
                return false;
            });
    });
  </script-->

<script type="application/javascript">
    $('input[type="file"]').change(function(e){
        var fileName = e.target.files[0].name;
        $('.custom-file-label').html(fileName);
    });
</script>

<!--<script type="text/javascript">
$.notify({
  // options
  message: 'Hello World' 
},{
  // settings
  type: 'danger'
});  
</script>-->

</body>
</html>