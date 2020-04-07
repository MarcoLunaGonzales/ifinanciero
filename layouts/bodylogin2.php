<?php //header('Content-Type: text/html; charset=iso-8859-1');?>
<!DOCTYPE html>
<html lang="en">
<!--ESTE ES EL DOCUMENTO DEL BODYLOGIN -->

<head>
  <meta charset="utf-8" />
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <title>
    Sistema de Monitoreo y Control - IBNORCA
  </title>
  <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
  <!--     Fonts and icons     -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css">
  <!-- CSS Files -->
  <link href="../assets/css/material-dashboard.css?v=2.1.0" rel="stylesheet" />
  <link href="../assets/autocomplete/awesomplete.css" rel="stylesheet" />
  <link href="../assets/autocomplete/autocomplete/autocomplete-img.css" rel="stylesheet" />
  <link href="../assets/css/style.css" rel="stylesheet" />
</head>

<body class="off-canvas-sidebar">
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top text-white">
    <div class="container">
      <button class="navbar-toggler" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
        <span class="sr-only">Toggle navigation</span>
        <span class="navbar-toggler-icon icon-bar"></span>
        <span class="navbar-toggler-icon icon-bar"></span>
        <span class="navbar-toggler-icon icon-bar"></span>
      </button>
      
    </div>
  </nav>
  <!-- End Navbar -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.min.js"></script>
  <script>
    $("#con_fac").mask("AA-AA-AA-AA-AA-AA-AA");
  </script>
  <!--   Core JS Files   -->
  <script src="../assets/js/core/jquery.min.js"></script>
  <script src="../assets/js/core/popper.min.js"></script>
  <script src="../assets/js/core/bootstrap-material-design.min.js"></script>
  <script src="../assets/js/plugins/perfect-scrollbar.jquery.min.js"></script>
  
  <!-- Forms Validations Plugin -->
  <script src="../assets/js/plugins/jquery.validate.min.js"></script>
  <!-- Plugin for the momentJs  -->
  <script src="../assets/js/plugins/moment.min.js"></script>
 <!--  Plugin for the DateTimePicker, full documentation here: https://eonasdan.github.io/bootstrap-datetimepicker/ -->
  <script src="../assets/js/plugins/bootstrap-datetimepicker.min.js"></script>
  <!-- Plugin for Fileupload, full documentation here: http://www.jasny.net/bootstrap/javascript/#fileinput -->
  <script src="../assets/js/plugins/jasny-bootstrap.min.js"></script>
  <!--  Plugin for Tags, full documentation here: https://github.com/bootstrap-tagsinput/bootstrap-tagsinputs  -->
  <script src="../assets/js/plugins/bootstrap-tagsinput.js"></script>
  <!--  Notifications Plugin    -->
  <script src="../assets/js/plugins/bootstrap-notify.js"></script>
  <!--  Plugin for Sweet Alert -->
  <script src="../assets/js/plugins/sweetalert2.js"></script>
  <script src="../assets/js/plugins/jquery.dataTables.min.js"></script>
  <script src="../assets/js/plugins/dataTables.fixedHeader.min.js"></script>

  <script src="../assets/js/plugins/bootstrap-selectpicker.js"></script>
  <script src="../assets/js/material-dashboard.js?v=2.1.0" type="text/javascript"></script>
  <script src="../assets/js/mousetrap.min.js"></script>
   
  <script src="../assets/autocomplete/awesomplete.min.js"></script>
  <script src="../assets/autocomplete/autocomplete/autocomplete-img.js"></script>
  <script src="../assets/alerts/alerts.js"></script>
  <script src="../assets/alerts/functionsGeneral.js"></script>
  <!--ESTE ES EL DOCUMENTO DEL BODYLOGIN -->
  <script>
    $(document).ready(function() {
      // Initialise Sweet Alert library
      alerts.showSwal();
    });
  </script>

  <script>
    $(document).ready(function() {
      // Initialise the wizard
      demo.initMaterialWizard();
      setTimeout(function() {
        $('.card.card-wizard').addClass('active');
      }, 600);
    });
  </script>

  <script type="text/javascript">
    $(document).ready(function() {
        $('#tablePaginatorReport ').DataTable({
            "paging":   false,
            "info":     false,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
            },
            fixedHeader: {
              header: true,
              footer: true
            }
        } );
        if($("#tableCuentasBuscar").length){
          $('#tableCuentasBuscar tfoot th').each( function () {
               var title = $(this).text();
               $(this).html( '<input type="text" class="form-control" placeholder="Buscar '+title+'" />' );
           } );
 
            // DataTable
            var table = $('#tableCuentasBuscar').DataTable({
              /*"paging":false});*/
                "processing": true,
                "serverSide": true,
          "ajax":{
            url :"../comprobantes/cuentasDatos.php", // json datasource
            type: "post",  // method  , by default get
            error: function(){  // error handling
              $(".employee-grid-error").html("");
              $("#tableCuentasBuscar").append('<tbody class="employee-grid-error"><tr><th colspan="3">No hay datos en el Servidor</th></tr></tbody>');
              $("#tableCuentasBuscar_processing").css("display","none");
              
            }
          }
          ,"language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
            }
              });
             // Apply the search
             table.columns().every( function () {
                 var that = this;
 
                 $( 'input', this.footer() ).on( 'keyup change clear', function (e) {
                  if(e.keyCode == 13) {
                   
                  }
                     if ( that.search() !== this.value ) {
                         that
                             .search( this.value )
                             .draw();
                     }
                 } );
             } );
             var r = $('#tableCuentasBuscar tfoot tr');
               r.find('th').each(function(){
                    $(this).css('padding', 8);
                });
             $('#tableCuentasBuscar thead').append(r);
             $('#search_0').css('text-align', 'center');

          /*$('#tableCuentasBuscar').DataTable({
            "paging":   false,
            "info":     false,
            "order": false,
            "searching": false,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
            },
            fixedHeader: {
              header: true,
              footer: true
            }
          } );*/
        }
        
    } );
    //<!--FUNCIONES DE VALIDACION-->
    $(document).ready(function() {
       /*setFormValidation('#form1');
        jQuery.extend(jQuery.validator.messages, {
        required: "El campo es requerido."
      });*/
    //campo input autocomplete
     if ($('#cuenta_auto').length) {
       autocompletar("cuenta_auto","cuenta_auto_id",array_cuenta);
     }
     if ($('#cuenta_auto_num').length) {
       autocomplete("cuenta_auto_num","cuenta_auto_num_id", array_cuenta, imagen_cuenta);
     }
     if ($('#nro_cuenta').length) {
       //autocomplete("nro_cuenta","nro_cuenta_id", array_cuenta_numeros, imagen_cuenta);
      // autocomplete("cuenta","cuenta_id", array_cuenta_nombres, imagen_cuenta);
     }
    // $("#formRegFactCajaChica").submit(function(e) {
    //   $('<input />').attr('type', 'hidden')
    //         .attr('name', 'facturas')
    //         .attr('value', JSON.stringify(itemFacturasDCC))
    //         .appendTo('#formRegFactCajaChica');
      
    // });
    $("#formRegComp").submit(function(e) {
      var envio=0;
      var mensaje=""; var debehaber=0;
      if(numFilas==0){
        mensaje+="<p>Debe tener registrado al menos una cuenta en detalle</p>";
      }
      if($("#nro_correlativo").val()==""){
        mensaje+="<p>Debe seleccionar un tipo de comprobante</p>";
      }     
      if (numFilas == 0 ||$("#nro_correlativo").val()==""){
         $('#msgError').html(mensaje);
         $('#modalAlert').modal('show'); 
         envio=1;
      }else{
          if($("#totaldeb").val()==""||$("#totalhab").val()==""){
               mensaje+="<p>La suma total no puede ser 0 (Debe - Haber)</p>";
               $('#msgError').html(mensaje);
               $('#modalAlert').modal('show'); 
               envio=1;
          }else{
              if($("#totaldeb").val()!=$("#totalhab").val()){
                  mensaje+="<p>El total del DEBE y EL HABER no coinciden</p>";
                  $('#msgError').html(mensaje);
                  $('#modalAlert').modal('show'); 
                  envio=1;
              }else{
                  var cont=0; var contcuenta=0;
                  for (var i = 0; i < numFilas; i++) {
                    if($('select[name=area'+(i+1)+']').val()==null||$('select[name=unidad'+(i+1)+']').val()==null){
                      cont++;
                    }                  
                  }
                  if(cont!=0){
                    mensaje+="<p>Debe seleccionar la Unidad y el Area</p>";
                    $('#msgError').html(mensaje);
                    $('#modalAlert').modal('show');
                    envio=1;
                  }else{
                    for (var i = 0; i < numFilas; i++) {
                      if($("#debe"+(i+1)).val()==""&&$("#haber"+(i+1)).val()==""){
                        mensaje+="<p>Todas las filas deben tener al menos un DEBE ó un HABER</p>";
                        $('#msgError').html(mensaje);
                        $('#modalAlert').modal('show');
                        debehaber=1;
                      }
                      if($('#cuenta'+(i+1)).val()==""||$('#cuenta'+(i+1)).val()==null||$('#cuenta'+(i+1)).val()==0){
                        contcuenta++;
                      }           
                    }
                    if(contcuenta!=0){
                      mensaje+="<p>No puede existir cuentas vacías!</p>";
                      $('#msgError').html(mensaje);
                      $('#modalAlert').modal('show');
                      envio=1; 
                    }else{
                      if(debehaber==1){
                        envio=1;
                      }else{
                        var contEstadoDebito=0;
                        for (var i = 0; i < numFilas; i++){
                          console.log("entro al detalle");
                          var debeZ=parseFloat($("#debe"+(i+1)).val());
                          var haberZ=parseFloat($("#haber"+(i+1)).val());
                          var tipoComprobante=parseFloat($("#tipo_comprobante").val());
                          var tipoEstadoCuenta=$("#tipo_estadocuentas"+(i+1)).val();
                          var cuentaAuxiliar=$("#cuenta_auxiliar"+(i+1)).val();  
                          var estadoCuentaSelect=$("#nestado"+(i+1)).hasClass("estado");

                          console.log("debe: "+debeZ+" haber: "+haberZ+" TC: "+tipoComprobante+" CA: "+cuentaAuxiliar+" TEC: "+tipoEstadoCuenta+" EEC: "+estadoCuentaSelect);

                          //VALIDAMOS CUANDO LA CUENTA TENGA EC LA CUENTA AUXILIAR SIEMPRE ESTE SELECCIONADA.
                          if(tipoComprobante==3 && tipoEstadoCuenta>0 && cuentaAuxiliar==0){  
                            $('#msgError').html("La fila "+(i+1)+" debe estar asociada a una CUENTA AUXILIAR, ya que está configurada para llevar Estados de Cuenta.");
                            $('#modalAlert').modal('show');
                            return false;
                          }else{
                            console.log("cuenta sin problemas; tipoComp:3");
                          }

                          /*
                          //Validamos TipoC: Ingreso y cuando haya EC se registre obligatoriamente en el Haber
                          if( (tipoComprobante==1 && tipoEstadoCuenta==1) ){
                            if( haberZ <= 0 || estadoCuentaSelect==false){
                              $('#msgError').html("La fila "+(i+1)+" esta configurada para cerrar Estados de Cuenta.");
                              $('#modalAlert').modal('show');
                              return false;
                            }
                          }else{
                            console.log("cuenta sin problemas; tipoComp:3");
                          }
                          */


                          //COMENTAMOS LA VALIDACION DE LOS ESTADOS DE CUENTA
                          /*if($("#tipo_estadocuentas"+(i+1)).val()=="1" && haberZ>0  && (!($("#nestado"+(i+1)).hasClass("estado")))){
                             contEstadoDebito=1;
                            mensaje+="<p>Existen cuentas que deben estar relacionadas a un Estado de Cuentas</p>";
                            $('#msgError').html(mensaje);
                            $('#modalAlert').modal('show');
                            contEstadoDebito=1;
                          }
                          if($("#tipo_estadocuentas"+(i+1)).val()=="2" && debeZ>0  && (!($("#nestado"+(i+1)).hasClass("estado")))){
                             contEstadoDebito=1;
                            mensaje+="<p>Existen cuentas que deben estar relacionadas a un Estado de Cuentas</p>";
                            $('#msgError').html(mensaje);
                            $('#modalAlert').modal('show');
                            contEstadoDebito=1;
                          }*/         
                        }
                        if(contEstadoDebito==1){
                          envio=1;
                        }else{
                          for (var i = 0; i < numFilas; i++) {
                              if($("#debe"+(i+1)).val()==""){
                                $("#debe"+(i+1)).val("0");
                              }
                              if($("#haber"+(i+1)).val()==""){
                                $("#haber"+(i+1)).val("0");
                              }
                          }     
                        }
                      }               
                    } 
                  }
              }
          }
        }
        if(envio==1){
          return false;
        }else{
          $('<input />').attr('type', 'hidden')
            .attr('name', 'facturas')
            .attr('value', JSON.stringify(itemFacturas))
            .appendTo('#formRegComp');
            $('<input />').attr('type', 'hidden')
            .attr('name', 'estados_cuentas')
            .attr('value', JSON.stringify(itemEstadosCuentas))
            .appendTo('#formRegComp');
        }
    });


    $("#formRegDet").submit(function(e) {
      var mensaje="";
      if($("#cantidad_filas").val()==0){
        mensaje+="<p></p>";
        Swal.fire("Informativo!", "Debe registrar al menos un GRUPO", "warning");
        return false;
      }else{
          $('<input />').attr('type', 'hidden')
            .attr('name', 'detalles')
            .attr('value', JSON.stringify(itemDetalle))
            .appendTo('#formRegDet');
      }     
    });
    $("#formDetTcp").submit(function(e) {
      var mensaje="";
      if($("#cantidad_filas").val()==0){
        mensaje+="<p></p>";
        Swal.fire("Informativo!", "Debe registrar al menos un detalle", "warning");
        return false;
      }else{
        var cont=0;
        for (var i = 0; i < $("#cantidad_filas").val(); i++) {
           if($('#cuenta_plantilladetalle'+(i+1)).val()==""||$('#cuenta_plantilladetalle'+(i+1)).val()==null){
             cont++; 
             break;
           }                  
        }
        if(cont!=0){
           Swal.fire("Informativo!", "No esta asignada la cuenta en uno o m&aacute; detalles <a href='#' class='btn btn-just-icon btn-primary btn-link'><i class='material-icons'>view_list</i><span class='bg-danger estado2'></span></a>", "warning"); 
           return false;
        }
      }     
    });
    $("#formSolDet").submit(function(e) {
      var mensaje="";
      /*if($("#cantidad_filas").val()==0){
        mensaje+="<p></p>";
        alertaModal('Debe registrar al menos un grupo','bg-secondary','text-white');
        return false;*/
      //}else{
    if($("#cantidad_filas").val()==0){
        mensaje+="<p></p>";
        Swal.fire("Informativo!", "Debe registrar al menos un detalle", "warning");
        return false;
      }else{
        var cont=0;
        for (var i = 0; i < $("#cantidad_filas").val(); i++) {
           if(parseInt($('#cod_retencion'+(i+1)).val())==parseInt($('#cod_configuracioniva').val())){
             if(itemFacturas[i].length==0){
              cont++; 
              break;
             }      
           }                  
        }
        if(cont!=0){
           Swal.fire("Informativo!", "La Retencion IVA debe tener al menos una factura registrada", "warning"); 
           return false;
        }else{
          //para poner la retencion iva si tiene al menos una factura..
        for (var i = 0; i < $("#cantidad_filas").val(); i++) {
             if(itemFacturas[i].length!=0){
              $('#cod_retencion'+(i+1)).val($('#cod_configuracioniva').val()); 
             }                       
        }

          $('<input />').attr('type', 'hidden')
            .attr('name', 'facturas')
            .attr('value', JSON.stringify(itemFacturas))
            .appendTo('#formSolDet');
        }
      }  

      //}    
    });
   document.getElementById('qrquincho').addEventListener('change', readSingleFile, false);
   document.getElementById('archivos').addEventListener('change', archivosPreview, false);
   document.getElementById('archivosDetalle').addEventListener('change', archivosPreviewDetalle, false);
  });
  </script>
 <script>
    $(document).ready(function() {
      // initialise Datetimepicker and Sliders 
      md.initFormExtendedDatetimepickers();
      if($("#boton_solicitudbuscar").length){
        addSolicitudDetalleSearch(); //
      }
      if($("#formRegComp")){
        Mousetrap.bind('alt+t', function(){ $("#tipo_comprobante").focus(); return false; });

        Mousetrap.bind('alt+a', function(){ addCuentaContable(); return false; });
        Mousetrap.bind('alt+q', function(){ minusCuentaContable(numFilas); return false; });
        Mousetrap.bind('shift+u', function(){ $('#modalCopySel').modal('show'); return false; });
        
        Mousetrap.bind('shift+g', function(){ $('#modalCopy').modal('show'); return false; });
        Mousetrap.bind('shift+r', function(){ $('#modalFile').modal('show'); return false; });
        Mousetrap.bind('shift+p', function(){ cargarPlantillas(); return false; });
        Mousetrap.bind('shift+s', function(){ modalPlantilla(); return false; });
        //salir de los modals con escape
        Mousetrap.bind('esc', function(){ $(".modal").modal("hide"); return false; });
        Mousetrap.bind('alt+enter', function(){ $(".modal").modal("hide"); return false; });
      }
      if($("#formSolDet")){
        if($("#simulacion").length){
          var tipo_s=1;
           Mousetrap.bind('alt+a', function(){ addSolicitudDetalle(null,tipo_s); return false; });
        }else{
          Mousetrap.bind('alt+s', function(){ addSolicitudDetalleSearch(); return false; });
        }
       
        Mousetrap.bind('alt+q', function(){ minusDetalleSolicitud(numFilas); return false; });
        //salir de los modals con escape
        Mousetrap.bind('esc', function(){ $(".modal").modal("hide"); return false; });
        Mousetrap.bind('alt+enter', function(){ $(".modal").modal("hide"); return false; });
      }
      
    });
  </script>
  <script type="text/javascript">
    $(document).ready(function() {
      var table_mayor=$('#libro_mayor_rep').DataTable(
      {
          "paging":   false,
            "info":     false,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
            },
            "order": false,
            "searching": false,
            fixedHeader: {
              header: true,
              footer: true
            },
            dom: 'Bfrtip',
            buttons:[

            {
                extend: 'copy',
                text:      '<i class="material-icons">file_copy</i>',
                titleAttr: 'Copiar',
                title:'Reporte Libro Mayor',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'csv',
                text:      '<i class="material-icons">list_alt</i>',
                titleAttr: 'CSV',
                title:'Reporte Libro Mayor',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'excel',
                text:      '<i class="material-icons">assessment</i>',
                titleAttr: 'Excel',
                title:'Reporte Libro Mayor',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'pdf',
                text:'<i class="material-icons">picture_as_pdf</i>',
                titleAttr: 'Pdf',
                title:'Reporte Libro Mayor',
                exportOptions: {
                    columns: ':visible'
                },
                customize: function ( doc) {
                   doc['footer']=(function(page, pages) { return {
                         columns: ['IBNORCA - REPORTES',{alignment: 'right',text: [{ 
                              text: page.toString(), italics: true 
                             },' de ',
                             { text: pages.toString(), italics: true }]
                          }],
                         margin: [10, 5]
                        }
                   });
                doc.content.splice( 1, 0, {
                    margin: [ 0, -100, 0, 50 ],
                    alignment: 'left',
                    image: 'data:image/jpeg;base64,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',
                    width:50,
                    height:50 
                } );
               doc.content.splice( 1, 0, {
                    margin: [ 0, 0, 0, 12 ],
                    text: [{
                      text: 'Periodo: '+periodo_mayor+' \n Cuenta: '+cuenta_mayor+' \n Unidad: '+unidad_mayor,
                      bold: true,
                      fontSize: 9,
                      alignment: 'left'
                   }]        
                } );
              }
            },
            {
                extend: 'print',
                text:      '<i class="material-icons">print</i>',
                titleAttr: 'Imprimir',
                title:'Reporte Libro Mayor',
                exportOptions: {
                    columns: ':visible'
                }
            }
          ]
        });
        var table_diario=$('#libro_diario_rep').DataTable({
          "paging":   false,
            "info":     false,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
            },
            "order": false,
            "searching": false,
            fixedHeader: {
              header: true,
              footer: true
            },
            dom: 'Bfrtip',
            buttons:[

            {
                extend: 'copy',
                text:      '<i class="material-icons">file_copy</i>',
                titleAttr: 'Copiar',
                title: 'Reporte Libro Diario',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'csv',
                text:      '<i class="material-icons">list_alt</i>',
                titleAttr: 'CSV',
                title: 'Reporte Libro Diario',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'excel',
                text:      '<i class="material-icons">assessment</i>',
                titleAttr: 'Excel',
                title: 'Reporte Libro Diario',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'pdf',
                text:      '<i class="material-icons">picture_as_pdf</i>',
                titleAttr: 'Pdf',
                title: 'Reporte Libro Diario',
                //messageTop:'Reporte Libro Diario',
                exportOptions: {
                        columns: ':visible'
                },
              customize: function ( doc) {
                   doc['footer']=(function(page, pages) { return {
                         columns: ['IBNORCA - REPORTES',{alignment: 'right',text: [{ 
                              text: page.toString(), italics: true 
                             },' de ',
                             { text: pages.toString(), italics: true }]
                          }],
                         margin: [10, 5]
                        }
                   });
                doc.content.splice( 1, 0, {
                    margin: [ 0, -80, 0, 12 ],
                    alignment: 'left',
                    image: 'data:image/jpeg;base64,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',
                    width:60,
                    height:60, 
                } );
                doc.content.splice( 1, 0, {
                    margin: [ 100, 0, 0, 12 ],
                    text: [{
                      text: 'Unidad: '+unidad_reporte_diario+' \n Fecha: '+fecha_reporte_diario+' \n Tipo: '+tipo_reporte_diario,
                      bold: true,
                      fontSize: 9,
                      alignment: 'right'
                   }]        
                } );
              }
            },
            {
                extend: 'print',
                text:      '<i class="material-icons">print</i>',
                titleAttr: 'Imprimir',
                title: 'Reporte Libro Diario',
                exportOptions: {
                    columns: ':visible'
                }
            }
          ]
        });

      var table_af=$('#tablePaginatorFixed2 ').DataTable({
            "paging":   false,
              "info":     false,
              "language": {
                  "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
              },
              "order": false,
              "searching": false,
              fixedHeader: {
                header: true,
                footer: true
              },
              dom: 'Bfrtip',
              buttons:[

              {
                  extend: 'copy',
                  text:      '<i class="material-icons">file_copy</i>',
                  titleAttr: 'Copiar',
                  title: 'Reporte De Activos Fijos',
                  exportOptions: {
                      columns: ':visible'
                  }
              },
              {
                  extend: 'csv',
                  text:      '<i class="material-icons">list_alt</i>',
                  titleAttr: 'CSV',
                  title: 'Reporte De Activos Fijos',
                  exportOptions: {
                      columns: ':visible'
                  }
              },
              {
                  extend: 'excel',
                  text:      '<i class="material-icons">assessment</i>',
                  titleAttr: 'Excel',
                  title: 'Reporte De Activos Fijos',
                  exportOptions: {
                      columns: ':visible'
                  }
              },
              {
                  extend: 'pdf',
                  text:      '<i class="material-icons">picture_as_pdf</i>',
                  titleAttr: 'Pdf',
                  title: 'Reporte De Activos Fijos',
                  //messageTop:'Reporte Libro Diario',
                  exportOptions: {
                          columns: ':visible'
                  },
                customize: function ( doc) {
                     doc['footer']=(function(page, pages) { return {
                           columns: ['IBNORCA - REPORTES',{alignment: 'right',text: [{ 
                                text: page.toString(), italics: true 
                               },' de ',
                               { text: pages.toString(), italics: true }]
                            }],
                           margin: [10, 5]
                          }
                     });
                  doc.content.splice( 1, 0, {
                      margin: [ 0, -50, 0, 12 ],
                      alignment: 'left',
                      image: 'data:image/jpeg;base64,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',
                      width:60,
                      height:60 
                  } );
                }
              },
              {
                  extend: 'print',
                  text:      '<i class="material-icons">print</i>',
                  titleAttr: 'Imprimir',
                  title: 'Reporte De Activos Fijos',
                  exportOptions: {
                      columns: ':visible'
                  }
              }
            ]
          });
    
      var table_afxU=$('#tablePaginatorFixed').DataTable({
                "paging":   false,
                  "info":     false,
                  "language": {
                      "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
                  },
                  "order": false,
                  "searching": false,
                  fixedHeader: {
                    header: true,
                    footer: true
                  },
                  dom: 'Bfrtip',
                  buttons:[

                  {
                      extend: 'copy',
                      text:      '<i class="material-icons">file_copy</i>',
                      titleAttr: 'Copiar',
                      title: 'Reporte De Activos Fijos Por Unidad',
                      exportOptions: {
                          columns: ':visible'
                      }
                  },
                  {
                      extend: 'csv',
                      text:      '<i class="material-icons">list_alt</i>',
                      titleAttr: 'CSV',
                      title: 'Reporte De Activos Fijos Por Unidad',
                      exportOptions: {
                          columns: ':visible'
                      }
                  },
                  {
                      extend: 'excel',
                      text:      '<i class="material-icons">assessment</i>',
                      titleAttr: 'Excel',
                      title: 'Reporte De Activos Fijos Por Unidad',
                      exportOptions: {
                          columns: ':visible'
                      }
                  },
                  {
                      extend: 'pdf',
                      text:      '<i class="material-icons">picture_as_pdf</i>',
                      titleAttr: 'Pdf',
                      title: 'Reporte De Activos Fijos Por Unidad',
                      //messageTop:'Reporte Libro Diario',
                      exportOptions: {
                              columns: ':visible'
                      },
                    customize: function ( doc) {
                         doc['footer']=(function(page, pages) { return {
                               columns: ['IBNORCA - REPORTES',{alignment: 'right',text: [{ 
                                    text: page.toString(), italics: true 
                                   },' de ',
                                   { text: pages.toString(), italics: true }]
                                }],
                               margin: [10, 5]
                              }
                         });
                      doc.content.splice( 1, 0, {
                          margin: [ 0, -50, 0, 12 ],
                          alignment: 'left',
                          image: 'data:image/jpeg;base64,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',
                          width:60,
                          height:60 
                      } );
                    }
                  },
                  {
                      extend: 'print',
                      text:      '<i class="material-icons">print</i>',
                      titleAttr: 'Imprimir',
                      title: 'Reporte De Activos Fijos Por Unidad',
                      exportOptions: {
                          columns: ':visible'
                      }
                  }
                ]
              });

      var table_afxU=$('#tablePaginatorFixed1').DataTable({
                "paging":   false,
                  "info":     false,
                  "language": {
                      "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
                  },
                  "order": false,
                  "searching": false,
                  fixedHeader: {
                    header: true,
                    footer: true
                  },
                  dom: 'Bfrtip',
                  buttons:[

                  {
                      extend: 'copy',
                      text:      '<i class="material-icons">file_copy</i>',
                      titleAttr: 'Copiar',
                      title: 'Depreciación De Activos Fijos Por Mes Y Gestión',
                      exportOptions: {
                          columns: ':visible'
                      }
                  },
                  {
                      extend: 'csv',
                      text:      '<i class="material-icons">list_alt</i>',
                      titleAttr: 'CSV',
                      title: 'Depreciación De Activos Fijos Por Mes Y Gestión',
                      exportOptions: {
                          columns: ':visible'
                      }
                  },
                  {
                      extend: 'excel',
                      text:      '<i class="material-icons">assessment</i>',
                      titleAttr: 'Excel',
                      title: 'Depreciación De Activos Fijos Por Mes Y Gestión',
                      exportOptions: {
                          columns: ':visible'
                      }
                  },
                  {
                      extend: 'pdf',
                      text:      '<i class="material-icons">picture_as_pdf</i>',
                      titleAttr: 'Pdf',
                      title: 'Depreciación De Activos Fijos Por Mes Y Gestión',
                      //messageTop:'Reporte Libro Diario',
                      exportOptions: {
                              columns: ':visible'
                      },
                    customize: function ( doc) {
                         doc['footer']=(function(page, pages) { return {
                               columns: ['IBNORCA - REPORTES',{alignment: 'right',text: [{ 
                                    text: page.toString(), italics: true 
                                   },' de ',
                                   { text: pages.toString(), italics: true }]
                                }],
                               margin: [10, 5]
                              }
                         });
                      doc.content.splice( 1, 0, {
                          margin: [ 0, -50, 0, 12 ],
                          alignment: 'left',
                          image: 'data:image/jpeg;base64,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',
                          width:60,
                          height:60 
                      } );
                    }
                  },
                  {
                      extend: 'print',
                      text:      '<i class="material-icons">print</i>',
                      titleAttr: 'Imprimir',
                      title: 'Depreciación De Activos Fijos Por Mes Y Gestión',
                      exportOptions: {
                          columns: ':visible'
                      }
                  }
                ]
              });
      var table_afxU=$('#tablePaginatorFixed3').DataTable({
                      "paging":   false,
                        "info":     false,
                        "language": {
                            "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
                        },
                        "order": false,
                        "searching": false,
                        fixedHeader: {
                          header: true,
                          footer: true
                        },
                        dom: 'Bfrtip',
                        buttons:[

                        {
                            extend: 'copy',
                            text:      '<i class="material-icons">file_copy</i>',
                            titleAttr: 'Copiar',
                            title: 'Depreciación De Activos Fijos Por Rubro Por Mes',
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        {
                            extend: 'csv',
                            text:      '<i class="material-icons">list_alt</i>',
                            titleAttr: 'CSV',
                            title: 'Depreciación De Activos Fijos Por Rubro Por Mes',
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        {
                            extend: 'excel',
                            text:      '<i class="material-icons">assessment</i>',
                            titleAttr: 'Excel',
                            title: 'Depreciación De Activos Fijos Por Rubro Por Mes',
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        {
                            extend: 'pdf',
                            text:      '<i class="material-icons">picture_as_pdf</i>',
                            titleAttr: 'Pdf',
                            title: 'Depreciación De Activos Fijos Por Rubro Por Mes',
                            //messageTop:'Reporte Libro Diario',
                            exportOptions: {
                                    columns: ':visible'
                            },
                          customize: function ( doc) {
                               doc['footer']=(function(page, pages) { return {
                                     columns: ['IBNORCA - REPORTES',{alignment: 'right',text: [{ 
                                          text: page.toString(), italics: true 
                                         },' de ',
                                         { text: pages.toString(), italics: true }]
                                      }],
                                     margin: [10, 5]
                                    }
                               });
                            doc.content.splice( 1, 0, {
                                margin: [ 0, -50, 0, 12 ],
                                alignment: 'left',
                                image: 'data:image/jpeg;base64,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',
                                width:60,
                                height:60 
                            } );
                          }
                        },
                        {
                            extend: 'print',
                            text:      '<i class="material-icons">print</i>',
                            titleAttr: 'Imprimir',
                            title: 'Depreciación De Activos Fijos Por Rubro Por Mes',
                            exportOptions: {
                                columns: ':visible'
                            }
                        }
                      ]
                    });
      var table_afxU=$('#tablePaginatorFixedAsignacion').DataTable({
        "paging":   false,
          "info":     false,
          "language": {
              "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
          },
          "order": false,
          "searching": false,
          fixedHeader: {
            header: true,
            footer: true
          },
          dom: 'Bfrtip',
          buttons:[

          {
              extend: 'copy',
              text:      '<i class="material-icons">file_copy</i>',
              titleAttr: 'Copiar',
              title: 'Reporte De Activos Fijos Asignados',
              exportOptions: {
                  columns: ':visible'
              }
          },
          {
              extend: 'csv',
              text:      '<i class="material-icons">list_alt</i>',
              titleAttr: 'CSV',
              title: 'Reporte De Activos Fijos Asignados',
              exportOptions: {
                  columns: ':visible'
              }
          },
          {
              extend: 'excel',
              text:      '<i class="material-icons">assessment</i>',
              titleAttr: 'Excel',
              title: 'Reporte De Activos Fijos Asignados',
              exportOptions: {
                  columns: ':visible'
              }
          },
          {
              extend: 'pdf',
              text:      '<i class="material-icons">picture_as_pdf</i>',
              titleAttr: 'Pdf',
              title: 'Reporte De Activos Fijos Asignados',
              //messageTop:'Reporte Libro Diario',
              exportOptions: {
                      columns: ':visible'
              },
            customize: function ( doc) {
                 doc['footer']=(function(page, pages) { return {
                       columns: ['IBNORCA - REPORTES',{alignment: 'right',text: [{ 
                            text: page.toString(), italics: true 
                           },' de ',
                           { text: pages.toString(), italics: true }]
                        }],
                       margin: [10, 5]
                      }
                 });
              doc.content.splice( 1, 0, {
                  margin: [ 0, -50, 0, 12 ],
                  alignment: 'left',
                  image: 'data:image/jpeg;base64,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',
                  width:60,
                  height:60 
              } );
            }
          },
          {
              extend: 'print',
              text:      '<i class="material-icons">print</i>',
              titleAttr: 'Imprimir',
              title: 'Reporte De Activos Fijos Asignados',
              exportOptions: {
                  columns: ':visible'
              }
          }
        ]
      });
      

      var table_afxU=$('#tablePaginatorFixedPlanillaSueldo').DataTable({
                "paging":   false,
                  "info":     false,
                  "language": {
                      "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
                  },
                  "order": false,
                  "searching": false,
                  fixedHeader: {
                    header: true,
                    footer: true
                  },
                  dom: 'Bfrtip',
                  buttons:[

                  {
                      extend: 'copy',
                      text:      '<i class="material-icons">file_copy</i>',
                      titleAttr: 'Copiar',
                      title: 'Planilla Sueldos Personal',
                      exportOptions: {
                          columns: ':visible'
                      }
                  },
                  {
                      extend: 'csv',
                      text:      '<i class="material-icons">list_alt</i>',
                      titleAttr: 'CSV',
                      title: 'Planilla Sueldos Personal',
                      exportOptions: {
                          columns: ':visible'
                      }
                  },
                  {
                      extend: 'excel',
                      text:      '<i class="material-icons">assessment</i>',
                      titleAttr: 'Excel',
                      title: 'Planilla Sueldos Personal',
                      exportOptions: {
                          columns: ':visible'
                      }
                  },
                  {
                      extend: 'pdf',
                      text:      '<i class="material-icons">picture_as_pdf</i>',
                      titleAttr: 'Pdf',
                      title: 'Planilla Sueldos Personal',
                      //messageTop:'Reporte Libro Diario',
                      exportOptions: {
                              columns: ':visible'
                      },
                    customize: function ( doc) {
                         doc['footer']=(function(page, pages) { return {
                               columns: ['IBNORCA - REPORTES',{alignment: 'right',text: [{ 
                                    text: page.toString(), italics: true 
                                   },' de ',
                                   { text: pages.toString(), italics: true }]
                                }],
                               margin: [10, 5]
                              }
                         });
                      doc.content.splice( 1, 0, {
                          margin: [ 0, -50, 0, 12 ],
                          alignment: 'left',
                          image: 'data:image/jpeg;base64,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',
                          width:60,
                          height:60 
                      } );
                    }
                  },
                  {
                      extend: 'print',
                      text:      '<i class="material-icons">print</i>',
                      titleAttr: 'Imprimir',
                      title: 'Planilla Sueldos Personal',
                      exportOptions: {
                          columns: ':visible'
                      }
                  }
                ]
              });

    var table_afxU=$('#tablePaginatorFixedTributaria').DataTable({
                "paging":   false,
                  "info":     false,
                  "language": {
                      "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
                  },
                  "order": false,
                  "searching": false,
                  fixedHeader: {
                    header: false,
                    footer: false
                  },
                  dom: 'Bfrtip',
                  buttons:[

                  {
                      extend: 'copy',
                      text:      '<i class="material-icons">file_copy</i>',
                      titleAttr: 'Copiar',
                      title: 'Planilla Tributaria',
                      exportOptions: {
                          columns: ':visible'
                      }
                  },
                  {
                      extend: 'csv',
                      text:      '<i class="material-icons">list_alt</i>',
                      titleAttr: 'CSV',
                      title: 'Planilla Tributaria',
                      exportOptions: {
                          columns: ':visible'
                      }
                  },
                  {
                      extend: 'excel',
                      text:      '<i class="material-icons">assessment</i>',
                      titleAttr: 'Excel',
                      title: 'Planilla Tributaria',
                      exportOptions: {
                          columns: ':visible'
                      }
                  },
                  {
                      extend: 'pdf',
                      text:      '<i class="material-icons">picture_as_pdf</i>',
                      titleAttr: 'Pdf',
                      title: 'Planilla Tributaria',
                      //messageTop:'Reporte Libro Diario',
                      exportOptions: {
                              columns: ':visible'
                      },
                    customize: function ( doc) {
                         doc['footer']=(function(page, pages) { return {
                               columns: ['IBNORCA - REPORTES',{alignment: 'right',text: [{ 
                                    text: page.toString(), italics: true 
                                   },' de ',
                                   { text: pages.toString(), italics: true }]
                                }],
                               margin: [10, 5]
                              }
                         });
                      doc.content.splice( 1, 0, {
                          margin: [ 0, -50, 0, 12 ],
                          alignment: 'left',
                          image: 'data:image/jpeg;base64,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',
                          width:60,
                          height:60 
                      } );
                    }
                  },
                  {
                      extend: 'print',
                      text:      '<i class="material-icons">print</i>',
                      titleAttr: 'Imprimir',
                      title: 'Planilla Tributaria',
                      exportOptions: {
                          columns: ':visible'
                      }
                  }
                ]
              });
    var table_afxU=$('#tablePaginatorHeaderFooter').DataTable({
                "paging":   false,
                  "info":     false,
                  "language": {
                      "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
                  },
                  "order": false,
                  "searching": false,
                  fixedHeader: {
                    header: true,
                    footer: true
                  },
                  dom: 'Bfrtip',
                  buttons:[                 
                ]
              });

    var table_afx=$('#tablePaginatorFixedEstadoCuentas').DataTable({
                "paging":   false,
                  "info":     false,
                  "language": {
                      "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
                  },
                  "order": false,
                  "searching": false,
                  fixedHeader: {
                    header: true,
                    footer: true
                  },
                  dom: 'Bfrtip',
                  buttons:[

                  {
                      extend: 'copy',
                      text:      '<i class="material-icons">file_copy</i>',
                      titleAttr: 'Copiar',
                      title: 'Reporte Estado De Cuentas',
                      exportOptions: {
                          columns: ':visible'
                      }
                  },
                  {
                      extend: 'csv',
                      text:      '<i class="material-icons">list_alt</i>',
                      titleAttr: 'CSV',
                      title: 'Reporte Estado De Cuentas',
                      exportOptions: {
                          columns: ':visible'
                      }
                  },
                  {
                      extend: 'excel',
                      text:      '<i class="material-icons">assessment</i>',
                      titleAttr: 'Excel',
                      title: 'Reporte Estado De Cuentas',
                      exportOptions: {
                          columns: ':visible'
                      }
                  },
                  {
                      extend: 'pdf',
                      text:      '<i class="material-icons">picture_as_pdf</i>',
                      titleAttr: 'Pdf',
                      title: 'Reporte Estado De Cuentas',
                      //messageTop:'Reporte Libro Diario',
                      exportOptions: {
                              columns: ':visible'
                      },
                    customize: function ( doc) {
                         doc['footer']=(function(page, pages) { return {
                               columns: ['IBNORCA - REPORTES',{alignment: 'right',text: [{ 
                                    text: page.toString(), italics: true 
                                   },' de ',
                                   { text: pages.toString(), italics: true }]
                                }],
                               margin: [10, 5]
                              }
                         });
                      doc.content.splice( 1, 0, {
                          margin: [ 0, -50, 0, 12 ],
                          alignment: 'left',
                          image: 'data:image/jpeg;base64,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',
                          width:60,
                          height:60 
                      } );
                    }
                  },
                  {
                      extend: 'print',
                      text:      '<i class="material-icons">print</i>',
                      titleAttr: 'Imprimir',
                      title: 'Reporte Estado De Cuentas',
                      exportOptions: {
                          columns: ':visible'
                      }
                  }
                ]
              });
    
      
     
      
     // table_diario.buttons().container().appendTo($('.col-sm-6:eq(0)', table_diario.table().container()));
      //$("#info_tabla").html($('.col-sm-6:eq(0)', table_diario.table().container()));
      // $( tableTools.fnContainer(oTableTools.dom.container)).insertAfter('div.info');
    });

  </script>
</body>
</html>