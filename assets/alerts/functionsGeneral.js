posicionarMenu();
$(window).scroll(function() {    
    posicionarMenu();
});
 
function posicionarMenu() {
    var altura_del_header = $('#cabecera_scroll').outerHeight(true);
    var altura_del_menu = $('.menu').outerHeight(true);
 
    if ($(window).scrollTop() >= altura_del_header){
        $('.menu').addClass('fixed');
        $('.wrapper_caja').css('margin-top', (altura_del_menu) + 'px');
        //poner boron copia
        if($("#segundo_copy").hasClass("d-none")){
          $("#segundo_copy").removeClass("d-none");
        }
    } else {
        $('.menu').removeClass('fixed');
        $('.wrapper_caja').css('margin-top', '0');
        //poner boron copia
        if(!($("#segundo_copy").hasClass("d-none"))){
          $("#segundo_copy").addClass("d-none");
        }
    }
}

function number_format(amount, decimals) {
  amount += ''; // por si pasan un numero en vez de un string
  amount = parseFloat(amount.replace(/[^0-9\.-]/g, '')); // elimino cualquier cosa que no sea numero o punto
  decimals = decimals || 0; // por si la variable no fue fue pasada
  // si no es un numero o es igual a cero retorno el mismo cero
  if (isNaN(amount) || amount === 0) 
      return parseFloat(0).toFixed(decimals);
  // si es mayor o menor que cero retorno el valor formateado como numero
  amount = '' + amount.toFixed(decimals);
  var amount_parts = amount.split('.');
      regexp = /(\d+)(\d{3})/;
  while (regexp.test(amount_parts[0]))
      amount_parts[0] = amount_parts[0].replace(regexp, '$1' + ',' + '$2');
  return amount_parts.join('.');
}

function nuevoAjax()
{ var xmlhttp=false;
  try {
      xmlhttp = new ActiveXObject('Msxml2.XMLHTTP');
  } catch (e) {
  try {
    xmlhttp = new ActiveXObject('Microsoft.XMLHTTP');
  } catch (E) {
    xmlhttp = false;
  }
  }
  if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
  xmlhttp = new XMLHttpRequest();
  }
  return xmlhttp;
}

function ajaxObtienePadre(cuenta){
  var contenedor;
  var cuentaContable=cuenta.value;
  console.log(cuentaContable);
  //contenedor = document.getElementById('modal-body');
  ajax=nuevoAjax();
  ajax.open('GET', 'plan_cuentas/ajaxObtienePadre.php?cuenta='+cuentaContable,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      document.getElementById('padre').value = ajax.responseText
    }
  }
  ajax.send(null)
}

var numFilas=0;
var cantidadItems=0;
var filaActiva=0;
function addCuentaContable(obj) {
  if($("#add_boton").length>0){
    $("#add_boton").attr("disabled",true);
  }
  var glosa_det=$("#glosa").val();
  var tipoComprobante=document.getElementById("tipo_comprobante").value;
  console.log("tipocomprobante: "+tipoComprobante);
  if(tipoComprobante>0){
      numFilas++;
      cantidadItems++;
      filaActiva=numFilas;
      //aumentar un itemfactura
      var nfac=[];
      itemFacturas.push(nfac);
      itemEstadosCuentas.push(nfac);
      document.getElementById("cantidad_filas").value=numFilas;
      console.log("num: "+numFilas+" cantidadItems: "+cantidadItems);
      fi = document.getElementById('fiel');
      contenedor = document.createElement('div');
      contenedor.id = 'div'+numFilas;  
      fi.type="style";
      fi.appendChild(contenedor);
      var divDetalle;
      divDetalle=$("#div"+numFilas);
      //document.getElementById('nro_cuenta').focus();
      ajax=nuevoAjax();
      ajax.open("GET","ajaxCuentaContable.php?idFila="+numFilas+"&glosa="+glosa_det,true);
      ajax.onreadystatechange=function(){
        if (ajax.readyState==4) {
          divDetalle.html(ajax.responseText);
          divDetalle.bootstrapMaterialDesign();   
          $('#nro_cuenta').val("");
          $('#cuenta').val("");//
          $('#padre').val("");
          $("#divResultadoBusqueda").html("<div class='form-group col-sm-8'>Resultados de la Búsqueda</div>");
          $('.selectpicker').selectpicker("refresh");
          $('#myModal').modal('show');
          if($("#add_boton").length>0){
            $("#add_boton").removeAttr("disabled");
          }
          return false;
       }
      }   
      ajax.send(null);
  }else{
    console.log('entrando a notify!!!!');
    $('#msgError').html("<p>Debe seleccionar un tipo de comprobante</p>");
    $('#modalAlert').modal('show');
    if($("#add_boton").length){
            $("#add_boton").removeAttr("disabled");
    }
    return false;
  }
}

//inicializar el puntero el el primer input modal Buscar Cuenta...
$(document).on("shown.bs.modal","#myModal",function(){
  document.getElementById('nro_cuenta').focus();
});

function calcularTotalesComprobante(id,e){
  var sumadebe=0;
  var sumahaber=0;
  var formulariop = document.getElementById("formRegComp");
  
  /*for (var i = 0; i < numFilas; i++) {
    
    if (e.keyCode != 9) {
     if(id=="debe"+(i+1)){
      //$("haber"+(i+1)).val()="0";
      document.getElementById('haber'+(i+1)).value="0";
     }
     if(id=="haber"+(i+1)){
      document.getElementById('debe'+(i+1)).value="0";
     }   
    }  
  }*/
  for (var i=0;i<formulariop.elements.length;i++){
    if (formulariop.elements[i].id.indexOf("debe") !== -1 ){    
      //console.log("debe "+formulariop.elements[i].value);    
      sumadebe += (formulariop.elements[i].value) * 1; 
    }
    if (formulariop.elements[i].id.indexOf("haber") !== -1 ){        
      //console.log("haber "+formulariop.elements[i].value);    
      sumahaber += (formulariop.elements[i].value) * 1;
    }
  }
  

  if($("#totalhab_restante").length){
    document.getElementById("totaldeb").value=redondeo(sumadebe+parseFloat($("#totaldeb_restante").val()),2).toFixed(2);  
    document.getElementById("totalhab").value=redondeo(sumahaber+parseFloat($("#totalhab_restante").val()),2).toFixed(2);
    document.getElementById("total_dif").value=redondeo($("#totaldeb").val()-$("#totalhab").val(),2).toFixed(2);
  }else{
    document.getElementById("totaldeb").value=redondeo(sumadebe,2).toFixed(2);  
    document.getElementById("totalhab").value=redondeo(sumahaber,2).toFixed(2); 
    document.getElementById("total_dif").value=redondeo(sumadebe-sumahaber,2).toFixed(2);  
  }
  document.getElementById("totaldeb_fijo").value=document.getElementById("totaldeb").value;
  document.getElementById("totalhab_fijo").value=document.getElementById("totalhab").value;
  document.getElementById("total_dif_fijo").value=document.getElementById("total_dif").value;
}

function llenarFacturaAutomaticamente(valor,fila,importe){
  //ajaxFacturasComprobanteNit(valor,importe);
  console.log("entro llenar llenarFacturaAutomaticamente");
  var parametros={"nit":valor,"importe":importe};
  var nro_factura=0;
  var nro_autorizacion=0;
  var razon_social="";
  $.ajax({
        type: "GET",
        dataType: 'html',
        url: "../comprobantes/ajaxAutocompletarNit.php",
        data: parametros,
        success:  function (resp) {
          var respuesta=resp.split('@');
          nro_factura = respuesta[0];
          razon_social = respuesta[1];
          nro_autorizacion = respuesta[2];
          valor=valor.replace(/(\r\n|\n|\r)/gm, "");
          $('#nit_fac').val(valor);
          $('#aut_fac').val(nro_autorizacion);
          $('#razon_fac').val(razon_social);
          $('#imp_fac').val(importe);
          console.log("nitfac: "+$('#nit_fac').val());
          console.log("datosFactura: "+valor+' '+nro_factura+' '+razon_social+' '+nro_autorizacion);     
        }
  });   
}

//este ajax remmplaza al div de nit porque no esta jalando 
function ajaxFacturasComprobanteNit(nit,importe){
  var contenedor;
  contenedor = document.getElementById('divNit2FacturaDetalle');
  ajax=nuevoAjax();
  ajax.open('GET', '../comprobantes/ajax_autocompletar_nit.php?nit='+nit,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);   
      ajaxFacturasComprobanteImporte(nit,importe);
    }
  }
  ajax.send(null)  
}
function ajaxFacturasComprobanteImporte(nit,importe){
  var contenedor;
  contenedor = document.getElementById('divImporteFacturaDetalle');
  ajax=nuevoAjax();
  ajax.open('GET', '../comprobantes/ajax_autocompletar_importe.php?importe='+importe,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);   
      ajaxFacturasComprobanteNroFact(nit);
    }
  }
  ajax.send(null)  
}
function ajaxFacturasComprobanteNroFact(nit){
  var contenedor;
  contenedor = document.getElementById('divNroFacFacturaDetalle');
  ajax=nuevoAjax();
  ajax.open('GET', '../comprobantes/ajax_autocompletar_nrofactura.php?nit='+nit,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);   
      ajaxFacturasComprobanteNroAutorizacion(nit);
    }
  }
  ajax.send(null)  
}
function ajaxFacturasComprobanteNroAutorizacion(nit){
  var contenedor;
  contenedor = document.getElementById('divNroAutoFacturaDetalle');
  ajax=nuevoAjax();
  ajax.open('GET', '../comprobantes/ajax_autocompletar_nroautorizacion.php?nit='+nit,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);   
      ajaxFacturasComprobanteRazonSocial(nit);      
    }
  }
  ajax.send(null)  
}
function ajaxFacturasComprobanteRazonSocial(nit){
  var contenedor;
  contenedor = document.getElementById('divRazonFacturaDetalle');
  ajax=nuevoAjax();
  ajax.open('GET', '../comprobantes/ajax_autocompletar_razonsocial.php?nit='+nit,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);   
    }
  }
  ajax.send(null)  
}

function ajaxCorrelativo(combo){
  var contenedor = document.getElementById('divnro_correlativo');
  var tipoComprobante=combo.value;
  // console.log(tipoComprobante);
  ajax=nuevoAjax();
  ajax.open('GET', 'ajaxCorrelativo.php?tipo_comprobante='+tipoComprobante,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      ajax_fechaComprobante(tipoComprobante);
    }
  }
  ajax.send(null)
}
function ajax_fechaComprobante(tipo){
  var contenedor = document.getElementById('div_fecha_comprobante');
  var fecha = $('#fecha').val();
  ajax=nuevoAjax();
  ajax.open('GET', 'ajax_fecha_comprobante.php?tipo_comprobante='+tipo+'&fecha='+fecha,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;      
    }
  }
  ajax.send(null)

}

function buscarCuenta(combo){
  var contenedor = document.getElementById('divResultadoBusqueda');
  var nroCuenta=document.getElementById('nro_cuenta').value;
  var nombreCuenta=document.getElementById('cuenta').value;
  var padre=$("#padre").val();
  var parametros={"nro_cuenta":nroCuenta,"cuenta":nombreCuenta,"padre":padre};
  $.ajax({
        type: "GET",
        dataType: 'html',
        url: "ajaxBusquedaCuenta.php",
        data: parametros,
        success:  function (resp) {
          var respuesta=resp.split('@');
          contenedor.innerHTML = respuesta[0];
          if(respuesta[1]==1){
            setBusquedaCuenta(respuesta[2],respuesta[3],respuesta[4],'0',''); 
          }     
        }
    });
}

function setBusquedaCuenta(codigoCuenta, numeroCuenta, nombreCuenta, codigoCuentaAux, nombreCuentaAux){
  var fila=filaActiva;
  var inicio=numeroCuenta.substr(0,1);
  //console.log(fila);
  document.getElementById('cuenta'+fila).value=codigoCuenta;

  document.getElementById('cuenta_auxiliar'+fila).value=codigoCuentaAux;
  document.getElementById('divCuentaDetalle'+fila).innerHTML='<span class=\"text-danger font-weight-bold\">['+numeroCuenta+']-'+nombreCuenta+' </span><br><span class=\"text-primary font-weight-bold small\">'+nombreCuentaAux+'</span>';
  configuracionCentros(fila,inicio);
  configuracionEstadosCuenta(fila,codigoCuenta,codigoCuentaAux);
  facturacomprobante(fila);//icono de factura
  configuracionLibretasBancarias(fila,codigoCuenta);
  $('#myModal').modal('hide');
  $(".selectpicker").selectpicker('refresh');
  $("#debe"+fila).focus();
}

function setBusquedaCuentaEdit(codigoCuenta, numeroCuenta, nombreCuenta, codigoCuentaAux, nombreCuentaAux){
  var fila=filaActiva;
  var inicio=numeroCuenta.substr(0,1);
  //console.log(fila);
  document.getElementById('cuenta'+fila).value=codigoCuenta;

  document.getElementById('cuenta_auxiliar'+fila).value=codigoCuentaAux;
  document.getElementById('divCuentaDetalle'+fila).innerHTML='<span class=\"text-danger font-weight-bold\">['+numeroCuenta+']-'+nombreCuenta+' </span><br><span class=\"text-primary font-weight-bold small\">'+nombreCuentaAux+'</span>';
  configuracionEstadosCuenta(fila,codigoCuenta,codigoCuentaAux);
  facturacomprobante(fila);//icono de factura
  configuracionLibretasBancarias(fila,codigoCuenta);
  $('#myModal').modal('hide');
  $(".selectpicker").selectpicker('refresh');
  $("#debe"+fila).focus();
}

function configuracionCentros(fila,inicio){
  for (var i = 0; i < configuracionCentro.length; i++) {    
    if(configuracionCentro[i].cod_grupo==parseInt(inicio)){
        if(configuracionCentro[i].fijo==1){   //Cuando la configuracion es fija
          $("#area"+fila).append("<option value='"+configuracionCentro[i].cod_area+"'>SA</option>");
          $("#area"+fila).val(configuracionCentro[i].cod_area);
          $("#area"+fila+" option").each(function(){
              if ($(this).val() != configuracionCentro[i].cod_area){        
               $(this).remove();
              }
           });
          $("#unidad"+fila).val(configuracionCentro[i].cod_unidad);
          $("#unidad"+fila+" option").each(function(){
              if ($(this).val() != configuracionCentro[i].cod_unidad){        
               $(this).remove();
              }
          });
        }else{//Cuando la configuracion no esta fija
          $("#area"+fila).val(configuracionCentro[i].cod_area);
          $("#unidad"+fila).val(configuracionCentro[i].cod_unidad);
        }
      break;  
    }
  };
}

function configuracionEstadosCuenta(fila,codigoCuenta,codigoCuentaAux){
  var contador=0;
  for (var i = 0; i < estado_cuentas.length; i++) {
    if(estado_cuentas[i].cod_cuenta==codigoCuenta){ //&&codigoCuentaAux==0
      $("#estados_cuentas"+fila).removeClass("d-none"); 
      $("#tipo_estadocuentas"+fila).val(estado_cuentas[i].tipo);
      $("#tipo_proveedorcliente"+fila).val(estado_cuentas[i].tipo_estado_cuenta);
      $("#tipo_estadocuentas_casoespecial"+fila).val(estado_cuentas[i].cod_cuentaaux);

      console.log("tipo: "+estado_cuentas[i].tipo);
      console.log("ProvCliente: "+estado_cuentas[i].tipo_estado_cuenta);

      contador++;   
      break;  
    }else{
      $("#estados_cuentas"+fila).removeClass("d-none"); 
      $("#estados_cuentas"+fila).addClass("d-none");  
    }
  };
}

function configuracionLibretasBancarias(fila,codigoCuenta){
  var contador=0;
  for (var i = 0; i < libretas_bancarias.length; i++) {
    if(libretas_bancarias[i].cod_cuenta==codigoCuenta){
      $("#libretas_bancarias"+fila).removeClass("d-none");  
      break;  
    }else{
      $("#libretas_bancarias"+fila).removeClass("d-none"); 
      $("#libretas_bancarias"+fila).addClass("d-none");  
    }
  };
}

function facturacomprobante(fila){
  var cod_confi_iva=document.getElementById('cod_cuenta_configuracion_iva').value;  
  //alert(cod_confi_iva);
  var codigo =$("#cuenta"+fila).val();  
  if(codigo==cod_confi_iva){
      //alert("entro aqui");
      $("#boton_fac"+fila).removeClass("d-none"); 
      // $("#boton_fac"+fila).removeClass("d-none"); 
  }
}

function copiarGlosa(){
  if(numFilas!=0){
   var gls=$('#glosa').val();
   for (var i = 0; i < numFilas; i++) {
     document.getElementById('glosa_detalle'+(i+1)).value=gls;
   }
  }
}
function updateSelect(valor,id){
  if(numFilas!=0){
    var cont=0;var idn=0;
   for (var i = 0; i < numFilas; i++) {
        cont=0;
         $("#"+id+(i+1)+" option").each(function(){
           cont++;
           });
         if(cont==1){
           idn++;
         }else{
          $('select[name='+id+(i+1)+']').val(valor);
         }   
   }
   if(idn!=0){
     if(idn>1){
      $("#copiar_sel_msg").append("<small class='text-warning'>"+idn+" filas de @"+id+" no copiadas</small><br>");
    }else{
      $("#copiar_sel_msg").append("<small class='text-warning'>"+idn+" fila de @"+id+" no copiada</small><br>");
    }
    //$("#copiar_sel_msg").append("<small class='text-success'>Explicación: Existe un valor por defecto en @"+id+"</small><br>");
   }else{
    //$("#modalCopySel").modal("hide");
   }
   var exito=numFilas-idn;
   if(exito==1){
      $("#copiar_sel_msg").append("<small class='text-success'>"+exito+" filas de @"+id+" copiada con éxito</small><br>");
    }else{
      $("#copiar_sel_msg").append("<small class='text-success'>"+exito+" filas de @"+id+" copiadas con éxito</small><br>");
    }
   
   $('.selectpicker').selectpicker('refresh');
  }
} 

function copiarSelect(){
  $("#copiar_sel_msg").html("");
  var unidad =$('select[name=unidad]').val();
  var area =$('select[name=area]').val();
  updateSelect(unidad,'unidad');
  updateSelect(area,'area');
}
function minusCuentaContable(idF){
 // alert(idF+"_"+cantidadItems);
      //$('#div'+idF).remove();
      var elem = document.getElementById('div'+idF);
      elem.parentNode.removeChild(elem);
      if(idF<numFilas){
      for (var i = parseInt(idF); i < (numFilas+1); i++) {
        var nuevoId=i+1;
       $("#div"+nuevoId).attr("id","div"+i);
       $("#unidad"+nuevoId).attr("name","unidad"+i);
       $("#unidad"+nuevoId).attr("id","unidad"+i);
       $("#area"+nuevoId).attr("name","area"+i);
       $("#area"+nuevoId).attr("id","area"+i);
       $("#cuenta"+nuevoId).attr("name","cuenta"+i);
       $("#cuenta"+nuevoId).attr("id","cuenta"+i);
       $("#cuenta_auxiliar"+nuevoId).attr("name","cuenta_auxiliar"+i);
       $("#cuenta_auxiliar"+nuevoId).attr("id","cuenta_auxiliar"+i);
       $("#divCuentaDetalle"+nuevoId).attr("id","divCuentaDetalle"+i);
       $("#debe"+nuevoId).attr("name","debe"+i);
       $("#debe"+nuevoId).attr("id","debe"+i);
       $("#haber"+nuevoId).attr("name","haber"+i);
       $("#haber"+nuevoId).attr("id","haber"+i);
       $("#glosa_detalle"+nuevoId).attr("name","glosa_detalle"+i);
       $("#glosa_detalle"+nuevoId).attr("id","glosa_detalle"+i);
       $("#boton_remove"+nuevoId).attr("onclick","minusCuentaContable('"+i+"')");
       $("#boton_remove"+nuevoId).attr("id","boton_remove"+i);
       $("#boton_fac"+nuevoId).attr("onclick","listFac('"+i+"')");
       $("#boton_fac"+nuevoId).attr("id","boton_fac"+i);
       $("#nfac"+nuevoId).attr("id","nfac"+i);

       $("#mayor"+nuevoId).attr("onclick","mayorReporteComprobante('"+i+"')");
       $("#mayor"+nuevoId).attr("id","mayor"+i);
       $("#cambiar_cuenta"+nuevoId).attr("onclick","editarCuentaComprobante('"+i+"')");
       $("#cambiar_cuenta"+nuevoId).attr("id","cambiar_cuenta"+i);
       $("#distribucion"+nuevoId).attr("onclick","nuevaDistribucionPonerFila('"+i+"')");
       $("#distribucion"+nuevoId).attr("id","distribucion"+i);
       $("#boton_ret"+nuevoId).attr("onclick","listRetencion('"+i+"')");
       $("#boton_ret"+nuevoId).attr("id","boton_ret"+i);

       $("#tipo_estadocuentas"+nuevoId).attr("id","tipo_estadocuentas"+i);
       $("#tipo_proveedorcliente"+nuevoId).attr("id","tipo_proveedorcliente"+i);
       $("#tipo_estadocuentas_casoespecial"+nuevoId).attr("id","tipo_estadocuentas_casoespecial"+i);
       $("#proveedorcliente"+nuevoId).attr("id","proveedorcliente"+i);
       if($("#codigo_detalle"+i).length){
         $("#codigo_detalle"+nuevoId).attr("id","codigo_detalle"+i);
       }
       $("#estados_cuentas"+nuevoId).attr("onclick","verEstadosCuentas('"+i+"',0)");
       $("#estados_cuentas"+nuevoId).attr("id","estados_cuentas"+i);
       $("#nestado"+nuevoId).attr("id","nestado"+i);   

       //libreta bancaria  
       $("#libretas_bancarias"+nuevoId).attr("onclick","verLibretasBancarias('"+i+"')");
       $("#libretas_bancarias"+nuevoId).attr("id","libretas_bancarias"+i);
       $("#nestadolib"+nuevoId).attr("id","nestado"+i);
       $("#cod_detallelibreta"+nuevoId).attr("name","cod_detallelibreta"+i); 
       $("#cod_detallelibreta"+nuevoId).attr("id","cod_detallelibreta"+i);
       $("#descripcion_detallelibreta"+nuevoId).attr("id","descripcion_detallelibreta"+i);

       //mayores seleccion
       $("#cerrar_detalles"+nuevoId).attr("onclick","verMayoresCierre('"+i+"')");
       $("#cerrar_detalles"+nuevoId).attr("id","cerrar_detalles"+i);
      }
     } 
      itemFacturas.splice((idF-1), 1);
      itemEstadosCuentas.splice((idF-1), 1);
      numFilas=numFilas-1;
      cantidadItems=cantidadItems-1;
      filaActiva=numFilas;
      document.getElementById("cantidad_filas").value=numFilas;
      document.getElementById("totalhab").value=numFilas;
      console.log("num: "+numFilas+" cantidadItems: "+cantidadItems); 
      calcularTotalesComprobante("null");   
}

function setFormValidation(id) {
      $(id).validate({
        highlight: function(element) {
          $(element).closest('.form-group').removeClass('has-success').addClass('has-danger');
          $(element).closest('.form-check').removeClass('has-success').addClass('has-danger');
        },
        success: function(element) {
          $(element).closest('.form-group').removeClass('has-danger').addClass('has-success');
          $(element).closest('.form-check').removeClass('has-danger').addClass('has-success');
        },
        errorPlacement: function(error, element) {
          $(element).closest('.form-group').append(error);
        }
      });   
    }

 var itemFacturas =[];

 function listFac(id){
   $("#divTituloCuentaDetalle").html($("#divCuentaDetalle"+id).html());
   $("#codCuenta").val(id);
   $("#divImporteFacturaDetalle").html($("#divImporteFactura"+id).html());
   $("#divNitFacturaDetalle").html($("#divNitFactura"+id).html());

   listarFact(id);
   abrirModal('modalFac');
 }
 function abrirModal(id){
  $('#'+id).modal('show');
 }
function listarFact(id){
  var div=$('<div>').addClass('table-responsive');
  var table = $('<table>').addClass('table table-condensed');
  var titulos = $('<tr>').addClass('');
     titulos.append($('<th>').addClass('').text('#'));
     titulos.append($('<th>').addClass('').text('NIT'));
     titulos.append($('<th>').addClass('').text('FACTURA'));
     titulos.append($('<th>').addClass('').text('FECHA'));
     titulos.append($('<th>').addClass('').text('RAZON SOCIAL'));
     titulos.append($('<th>').addClass('').text('IMPORTE'));
     //titulos.append($('<th>').addClass('').text('EXCENTOS'));
     titulos.append($('<th>').addClass('').text('AUTORIZACION'));
     titulos.append($('<th>').addClass('').text('CONTROL'));
     
     titulos.append($('<th>').addClass('').text('EXENTOS'));
     titulos.append($('<th>').addClass('').text('ICE'));
     titulos.append($('<th>').addClass('').text('TIPO'));
     titulos.append($('<th>').addClass('').text('TASA'));

     titulos.append($('<th>').addClass('').text('OPCION'));

     
     table.append(titulos);
   for (var i = 0; i < itemFacturas[id-1].length; i++) {
     var row = $('<tr>').addClass('');
     row.append($('<td>').addClass('').text(i+1));
     row.append($('<td>').addClass('').text(itemFacturas[id-1][i].nit));
     row.append($('<td>').addClass('').text(itemFacturas[id-1][i].nroFac));
     row.append($('<td>').addClass('').text(itemFacturas[id-1][i].fechaFac));
     row.append($('<td>').addClass('').text(itemFacturas[id-1][i].razonFac));
     row.append($('<td>').addClass('').text(itemFacturas[id-1][i].impFac));
     //row.append($('<td>').addClass('').text(itemFacturas[id-1][i].exeFac));
     row.append($('<td>').addClass('').text(itemFacturas[id-1][i].autFac));
     row.append($('<td>').addClass('').text(itemFacturas[id-1][i].conFac));

     row.append($('<td>').addClass('').text(itemFacturas[id-1][i].exeFac));
     row.append($('<td>').addClass('').text(itemFacturas[id-1][i].iceFac));    
     row.append($('<td>').addClass('').text(itemFacturas[id-1][i].tipoFac));
     row.append($('<td>').addClass('').text(itemFacturas[id-1][i].tazaFac));

     row.append($('<td>').addClass('').html('<div class="btn-group"><button class="btn btn-success btn-sm btn-fab" onclick="editFac('+id+','+i+');"><i class="material-icons">edit</i></button><button class="btn btn-danger btn-sm btn-fab" onclick="removeFac('+id+','+i+');"><i class="material-icons">remove_circle</i></button></div>'));
     table.append(row);
   }
   div.append(table);
   $('#divResultadoListaFac').html(div);
}
function obtenerImportesFacturaIva(index){
  var total=0;
  for (var i = 0; i < itemFacturas[index-1].length; i++) {
     total+=parseFloat(itemFacturas[index-1][i].impFac);
  };
  return total*(configuraciones[0].valor/100);
}
function saveFacturaNuevo(){
  var formAValidar = document.getElementById('form_facturas');
  if (formAValidar.checkValidity() === false) {
    event.preventDefault();
    event.stopPropagation();
    formAValidar.classList.add('was-validated');
    console.log("VALIDACION FALSE!***");
  }else{
    var index=$('#codCuenta').val();
    var factura={
      nit: $('#nit_fac').val(),
      nroFac: $('#nro_fac').val(),
      fechaFac: $('#fecha_fac').val(),
      razonFac: $('#razon_fac').val(),
      impFac: $('#imp_fac').val(),    
      autFac: $('#aut_fac').val(),
      conFac: $('#con_fac').val(),
      exeFac: $('#exe_fac').val(),
      iceFac: $('#ice_fac').val(),
      tazaFac: $('#taza_fac').val(),
      tipoFac: $('#tipo_fac').val()
    }  
    var monto_debe_total_comprobante = $("#totaldeb").val();
    var monto_suma_factura=parseInt($('#imp_fac').val())+parseInt($('#ice_fac').val())+parseInt($('#exe_fac').val());
    console.log("SUMAS FACTURAS: "+monto_suma_factura+" "+monto_debe_total_comprobante);
    itemFacturas[index-1].push(factura);
    limpiarFormFac();
    listarFact(index);
    //$("#debe"+index).val(anterior+importeIva);
    if($("#debe"+index).length){
     calcularTotalesComprobante();  
    } 
    $("#nfac"+index).html(itemFacturas[index-1].length);
    $("#link110").addClass("active");$("#link111").removeClass("active");$("#link112").removeClass("active");
    $("#nav_boton1").addClass("active");$("#nav_boton2").removeClass("active");$("#nav_boton3").removeClass("active");
  }
}
function saveFactura(){
  var index=$('#codCuenta').val();
  var factura={
    nit: $('#nit_fac').val(),
    nroFac: $('#nro_fac').val(),
    fechaFac: $('#fecha_fac').val(),
    razonFac: $('#razon_fac').val(),
    impFac: $('#imp_fac').val(),    
    autFac: $('#aut_fac').val(),
    conFac: $('#con_fac').val(),
    exeFac: $('#exe_fac').val(),
    iceFac: $('#ice_fac').val(),
    tazaFac: $('#taza_fac').val(),
    tipoFac: $('#tipo_fac').val()
    }
    
  var monto_debe_total_comprobante = $("#totaldeb").val();
  var monto_suma_factura=parseInt($('#imp_fac').val())+parseInt($('#ice_fac').val())+parseInt($('#exe_fac').val());
  console.log("SUMAS FACTURAS: "+monto_suma_factura+" "+monto_debe_total_comprobante);
  //if(monto_suma_factura != monto_debe_total_comprobante){
    //alert("El monto registrado en las facturas difiere del total!");
  //}else{
    if($('#nit_fac').val()!=''){
      if($('#nro_fac').val()!=''){
        if($('#fecha_fac').val()!=''){        
            if($('#imp_fac').val()!=''){
              if($('#aut_fac').val()!=''){              
                  if($('#razon_fac').val()!=''){
                    itemFacturas[index-1].push(factura);
                    limpiarFormFac();
                    listarFact(index);
                    //$("#debe"+index).val(anterior+importeIva);
                    if($("#debe"+index).length){
                     calcularTotalesComprobante();  
                    } 
                    $("#nfac"+index).html(itemFacturas[index-1].length);
                    $("#link110").addClass("active");$("#link111").removeClass("active");$("#link112").removeClass("active");
                    $("#nav_boton1").addClass("active");$("#nav_boton2").removeClass("active");$("#nav_boton3").removeClass("active");                
                  }else{
                    alert('Campo "Razón Social" Vacío.');
                  }
              }else{
                alert('Campo "Nro. Autorización" Vacío.');
              }
            }else{
              alert('Campo "Importe" Vacío.');
            }
        }else{
          alert('Campo "Fecha" Vacío.');
        }  
      }else{
        alert('Campo "Nro. Factura" Vacío.');
      }  
    }else{
      alert('Campo "NIT" Vacío.');
    }
 // }
}
 function abrirFactura(index,nit,nro,fecha,razon,imp,exe,aut,con,ice,tipocompra,tazacero){
   var factura={
    nit: nit,
    nroFac: nro,
    fechaFac: fecha,
    razonFac:razon,
    impFac: imp,
    autFac: aut,
    conFac: con,
    exeFac: exe,
    iceFac: ice,
    tazaFac: tazacero,
    tipoFac: tipocompra
    }
    itemFacturas[index-1]=[];
    itemFacturas[index-1].push(factura);
    //listarFact(index);
    $("#nfac"+(index)).html(itemFacturas[index-1].length);
 }
 function abrirEstado(index,cuenta,codComproDet,monto){
   var estado={
    cod_plancuenta:cuenta,
    cod_comprobantedetalle:codComproDet,
    cod_proveedor:0,
    monto:monto
    }
    itemEstadosCuentas[index-1].push(estado);
    $("#nestado"+(index)).addClass("estado");
 }
 function removeFac(item,fila){
  itemFacturas[item-1].splice(fila, 1);
  listarFact(item);
  $("#nfac"+item).html(itemFacturas[item-1].length);
 }
 function limpiarFormFac(){
    $('#nit_fac').val('');$('#nro_fac').val('');$('#fecha_fac').val('');$('#razon_fac').val('');$('#imp_fac').val('');
    $('#aut_fac').val('');$('#con_fac').val('');$('#exe_fac').val('');$('#ice_fac').val('');$('#taza_fac').val('');$('#tipo_fac').val('');
 }
 function cargarDetalles(fila,un,ar,de,ha,gl){
    var divDetalle;
      divDetalle=document.getElementById("div"+fila);
      ajax=nuevoAjax();
      ajax.open("GET","ajaxCuentaContable.php?idFila="+fila+'&un='+un+'&ar='+ar+'&de='+de+'&ha='+ha+'&gl='+gl,true);
      ajax.onreadystatechange=function(){
        if (ajax.readyState==4) {
          divDetalle.innerHTML=ajax.responseText;
          //$('.selectpicker').selectpicker(["refresh"]);
        }
      }
      ajax.send(null);
 }
//modal desde caja chica
var itemFacturasDCC =[];
function listFacDCC(id,fecha,observaciones,monto,nro_dcc,codigo){
  document.getElementById("cod_ccd").value=codigo;
  document.getElementById("cantidad_filas_ccd").value=id;

  document.getElementById("fecha_dcc").value=fecha;
  document.getElementById("observaciones_dcc").value=observaciones;
  document.getElementById("monto_dcc").value=monto;
  document.getElementById("nro_dcc").value=nro_dcc;
  // alert(id);
   $("#divTituloCuentaDetalle").html($("#divCuentaDetalle"+id).html());
   $("#codCuenta").val(id);
   listarFactDCC(id);
   abrirModalDCC('modalFac');
}
function listDistribuciones_cajachica(codigo){
  // document.getElementById("cod_ccd_d").value=codigo;
  // document.getElementById("cantidad_filas_ccd_d").value=id;
  // document.getElementById("fecha_dcc_d").value=fecha;
  // document.getElementById("observaciones_dcc_d").value=observaciones;
  // document.getElementById("monto_dcc_d").value=monto;
  // document.getElementById("nro_dcc_d").value=nro_dcc;
  abrirModalDCC('modal_distribuciones');
  ajax=nuevoAjax();
  ajax.open('GET', 'caja_chica/ajax_cajachica_distribuciones.php?codigo='+codigo,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      var contenedor=$("#contenedor_uo_distribucion");
      contenedor.html(ajax.responseText);

      ajax=nuevoAjax();
      ajax.open('GET', 'caja_chica/ajax_cajachica_distribuciones_area.php?codigo='+codigo,true);
      ajax.onreadystatechange=function() {
        if (ajax.readyState==4) {
          var contenedor=$("#contenedor_area_distribucion");
          contenedor.html(ajax.responseText);      
        }
      }
      ajax.send(null);
    }
  }
  ajax.send(null);

}

function abrirModalDCC(id){
  $('#'+id).modal('show');
}
function listarFactDCC(id){
  // alert("entre");
  var div=$('<div>').addClass('table-responsive');
  var table = $('<table>').addClass('table table-condensed');
  var titulos = $('<tr>').addClass('');
     titulos.append($('<th>').addClass('').text('#'));
     titulos.append($('<th>').addClass('').text('NIT'));
     titulos.append($('<th>').addClass('').text('FACTURA'));
     titulos.append($('<th>').addClass('').text('FECHA'));
     titulos.append($('<th>').addClass('').text('RAZON SOCIAL'));
     titulos.append($('<th>').addClass('').text('IMPORTE'));
     //titulos.append($('<th>').addClass('').text('EXCENTOS'));
     titulos.append($('<th>').addClass('').text('AUTORIZACION'));
     titulos.append($('<th>').addClass('').text('CONTROL'));
     titulos.append($('<th>').addClass('').text('EXENTOS'));
     titulos.append($('<th>').addClass('').text('ICE'));     
     titulos.append($('<th>').addClass('').text('TASA'));
     titulos.append($('<th>').addClass('').text('OPCION'));
     table.append(titulos);
   // console.log(itemFacturasDCC);
   for (var i = 0; i < itemFacturasDCC[id-1].length; i++) {
     var row = $('<tr>').addClass('');
     row.append($('<td>').addClass('').text(i+1));
     row.append($('<td>').addClass('').text(itemFacturasDCC[id-1][i].nit));
     row.append($('<td>').addClass('').text(itemFacturasDCC[id-1][i].nroFac));
     row.append($('<td>').addClass('').text(itemFacturasDCC[id-1][i].fechaFac));
     row.append($('<td>').addClass('').text(itemFacturasDCC[id-1][i].razonFac));
     row.append($('<td>').addClass('').text(itemFacturasDCC[id-1][i].impFac));
     //row.append($('<td>').addClass('').text(itemFacturasDCC[id-1][i].exeFac));
     row.append($('<td>').addClass('').text(itemFacturasDCC[id-1][i].autFac));
     row.append($('<td>').addClass('').text(itemFacturasDCC[id-1][i].conFac));
     row.append($('<td>').addClass('').text(itemFacturasDCC[id-1][i].exeFac));
     row.append($('<td>').addClass('').text(itemFacturasDCC[id-1][i].iceFac));         
     row.append($('<td>').addClass('').text(itemFacturasDCC[id-1][i].tasaFac));
     row.append($('<td>').addClass('').html('<button class="btn btn-danger btn-link" onclick="removeFacDCC('+id+','+i+');"><i class="material-icons">remove_circle</i></button>'));
     table.append(row);
   }
   div.append(table);
   $('#divResultadoListaFac').html(div);
}
function saveFacturaDCC(){
  var index=$('#codCuenta').val();
  var factura={
    nit: $('#nit_fac').val(),
    nroFac: $('#nro_fac').val(),
    fechaFac: $('#fecha_fac').val(),
    razonFac: $('#razon_fac').val(),
    impFac: $('#imp_fac').val(),    
    autFac: $('#aut_fac').val(),
    conFac: $('#con_fac').val(),
    exeFac: $('#exe_fac').val(),
    iceFac: $('#ice_fac').val(),
    tasaFac: $('#taza_fac').val()  
    }
  if($('#nit_fac').val()!=''){
    if($('#nro_fac').val()!=''){
      if($('#fecha_fac').val()!=''){        
          if($('#imp_fac').val()!=''){
            if($('#aut_fac').val()!=''){
              // if($('#con_fac').val()!=''){
                if($('#razon_fac').val()!=''){
                  itemFacturasDCC[index-1].push(factura);
                  limpiarFormFacDCC();
                  listarFactDCC(index);                
                  $("#nfac"+index).html(itemFacturasDCC[index-1].length);
                  $("#link110").addClass("active");$("#link111").removeClass("active");$("#link112").removeClass("active");
                  $("#nav_boton1").addClass("active");$("#nav_boton2").removeClass("active");$("#nav_boton3").removeClass("active");                 
                }else{
                  alert('Campo "Razón Social" Vacío.');
                }
              // }else{
              //   alert('Campo "Cod. Control" Vacío.');
              // }
            }else{
              alert('Campo "Nro. Autorización" Vacío.');
            }
          }else{
            alert('Campo "Importe" Vacío.');
          }
      }else{
        alert('Campo "Fecha" Vacío.');
      }  
    }else{
      alert('Campo "Nro. Factura" Vacío.');
    }
    
  }else{
    alert('Campo "NIT" Vacío.');
  }
}
function saveImporteDirectoDCC(){
  var index=$('#codCuenta').val();
  var factura={
    nit: 0,
    nroFac: 0,
    fechaFac: 0,
    razonFac: 'Importe Directo',
    impFac: $('#importe_gasto').val(),    
    autFac: 0,
    conFac: 0,
    exeFac: 0,
    iceFac: 0,
    tasaFac: 0  
    }    
    if($('#importe_gasto').val()!=''){
      itemFacturasDCC[index-1].push(factura);
      limpiarFormFacDCC();
      listarFactDCC(index);                
      $("#nfac"+index).html(itemFacturasDCC[index-1].length);
      $("#link110").addClass("active");$("#link111").removeClass("active");$("#link112").removeClass("active");$("#link113").removeClass("active");
      $("#nav_boton1").addClass("active");$("#nav_boton2").removeClass("active");$("#nav_boton3").removeClass("active");$("#nav_boton4").removeClass("active");                 
    }else{
      alert('El Campo "Importe del Gasto" no debe ir Vacío.');
    }
}

function limpiarFormFacDCC(){
  $('#nit_fac').val('');$('#nro_fac').val('');$('#fecha_fac').val('');$('#razon_fac').val('');$('#imp_fac').val('');
    $('#aut_fac').val('');$('#con_fac').val('');$('#exe_fac').val('');$('#ice_fac').val('');$('#taza_fac').val('');    
}
 function abrirFacturaDCC(index,nit,nro,fecha,razon,imp,exe,aut,con,ice,tasacero){
   var factura={
    nit: nit,
    nroFac: nro,
    fechaFac: fecha,
    razonFac:razon,
    impFac: imp,    
    autFac: aut,
    conFac: con,
    exeFac: exe,
    iceFac: ice,    
    tasaFac: tasacero
    }
    itemFacturasDCC[index-1].push(factura);
    listarFactDCC(index);
    $("#nfac"+(index)).html(itemFacturasDCC[index-1].length);
 }

function removeFacDCC(item,fila){
  itemFacturasDCC[item-1].splice(fila, 1);
  listarFactDCC(item);
  $("#nfac"+item).html(itemFacturasDCC[item-1].length);
}

var numArchivos=0;
function archivosPreviewDCC(send) {
    var inp=document.getElementById("archivos");
    if(send!=1){
      $("#lista_archivos").html("<p class='text-success text-center'>Lista de Archivos</p>");
      for (var i = 0; i < inp.files.length; ++i) {
        numArchivos++;
        var name = inp.files.item(i).name;
        $("#lista_archivos").append("<div class='text-left'><label>"+name+"</label></div>");
       }
       $("#narch").addClass("estado");
     }else{
      numArchivos=0;
        $("#lista_archivos").html("Ningun archivo seleccionado");
        $("#narch").removeClass("estado");
     }

    
}
function readSingleFileDCC(evt) {
    var f = evt.target.files[0];
      if (f) {
          var r = new FileReader();
          r.onload = function(e) { 
              var contents = e.target.result;
              const lines = contents.split('\n').map(function (line){
                return line.split(',')
              })
              var index=$('#codCuenta').val();
              for (var i = 0; i < lines.length; i++) {
                if(String(lines[i]).trim()!=""){
                  lines[i]=String(lines[i]).split("\t");
                console.log(lines[i])
                   $('#nit_fac').val(lines[i][0]);
                   $('#nro_fac').val(lines[i][1]);
                   $('#fecha_fac').val(lines[i][3]);
                   $('#razon_fac').val("-");
                   $('#imp_fac').val(lines[i][4]);
                   $('#exe_fac').val("-");
                   $('#aut_fac').val(lines[i][2]);
                   $('#con_fac').val(lines[i][5]);
                   saveFacturaDCC();
                 } 
                }
               $("#qrquincho").val(""); 
          }
         r.readAsText(f);
      } else {
                    alert("Failed to load file");
      }
  
}

//modal desde rebdiciones
var itemFacturasDRC=[];
function listFacDRC(id,fecha,observaciones,monto,nro_dcc,codigo){  
  document.getElementById("cod_rd").value=codigo;
  document.getElementById("cantidad_filas").value=id;
  document.getElementById("fecha_dcc").value=fecha;
  document.getElementById("observaciones_dcc").value=observaciones;
  document.getElementById("monto_dcc").value=monto;
  document.getElementById("nro_dcc").value=nro_dcc;
  // alert(id);
   $("#divTituloCuentaDetalle").html($("#divCuentaDetalle"+id).html());
   $("#codCuenta").val(id);
   listarFactDRC(id);
   abrirModalDRC('modalFac');
}
function abrirModalDRC(id){
  $('#'+id).modal('show');
}
function listarFactDRC(id){
  var div=$('<div>').addClass('table-responsive');
  var table = $('<table>').addClass('table table-condensed');
  var titulos = $('<tr>').addClass('');
     titulos.append($('<th>').addClass('').text('#'));
     titulos.append($('<th>').addClass('').text('NIT'));
     titulos.append($('<th>').addClass('').text('FACTURA'));
     titulos.append($('<th>').addClass('').text('FECHA'));
     titulos.append($('<th>').addClass('').text('RAZON SOCIAL'));
     titulos.append($('<th>').addClass('').text('IMPORTE'));
     //titulos.append($('<th>').addClass('').text('EXCENTOS'));
     titulos.append($('<th>').addClass('').text('AUTORIZACION'));
     titulos.append($('<th>').addClass('').text('CONTROL'));
     titulos.append($('<th>').addClass('').text('OPCION'));
     table.append(titulos);
   
   for (var i = 0; i < itemFacturasDRC[id-1].length; i++) {
     var row = $('<tr>').addClass('');
     row.append($('<td>').addClass('').text(i+1));
     row.append($('<td>').addClass('').text(itemFacturasDRC[id-1][i].nit));
     row.append($('<td>').addClass('').text(itemFacturasDRC[id-1][i].nroFac));
     row.append($('<td>').addClass('').text(itemFacturasDRC[id-1][i].fechaFac));
     row.append($('<td>').addClass('').text(itemFacturasDRC[id-1][i].razonFac));
     row.append($('<td>').addClass('').text(itemFacturasDRC[id-1][i].impFac));
     //row.append($('<td>').addClass('').text(itemFacturasDRC[id-1][i].exeFac));
     row.append($('<td>').addClass('').text(itemFacturasDRC[id-1][i].autFac));
     row.append($('<td>').addClass('').text(itemFacturasDRC[id-1][i].conFac));
     row.append($('<td>').addClass('').html('<button class="btn btn-danger btn-link" onclick="removeFacDRC('+id+','+i+');"><i class="material-icons">remove_circle</i></button>'));
     table.append(row);
   }
   div.append(table);
   $('#divResultadoListaFac').html(div);
}
function saveFacturaDRC(){
  var index=$('#codCuenta').val();
  var factura={
    nit: $('#nit_fac').val(),
    nroFac: $('#nro_fac').val(),
    fechaFac: $('#fecha_fac').val(),
    razonFac: $('#razon_fac').val(),
    impFac: $('#imp_fac').val(),
    exeFac: $('#exe_fac').val(),
    autFac: $('#aut_fac').val(),
    conFac: $('#con_fac').val(),
    }
    
  //cargar el credito fiscal
  //var iva=configuraciones[0].valor;
  //var importeIva=parseFloat($('#imp_fac').val())*(iva/100);
  //var anterior= obtenerImportesFacturaIva(index);
  itemFacturasDRC[index-1].push(factura);
  limpiarFormFacDRC();
  listarFactDRC(index);
  //$("#debe"+index).val(anterior+importeIva);
  // if($("#debe"+index).length){
  //  calcularTotalesComprobante();  
  // } 
  $("#nfac"+index).html(itemFacturasDRC[index-1].length);
  $("#link110").addClass("active");$("#link111").removeClass("active");$("#link112").removeClass("active");
  $("#nav_boton1").addClass("active");$("#nav_boton2").removeClass("active");$("#nav_boton3").removeClass("active");
}
function limpiarFormFacDRC(){
    $('#nit_fac').val('');$('#nro_fac').val('');$('#fecha_fac').val('');$('#razon_fac').val('');$('#imp_fac').val('');
    $('#exe_fac').val('');$('#aut_fac').val('');$('#con_fac').val('');
}
 function abrirFacturaDRC(index,nit,nro,fecha,razon,imp,exe,aut,con){
   var factura={
    nit: nit,
    nroFac: nro,
    fechaFac: fecha,
    razonFac:razon,
    impFac: imp,
    exeFac: exe,
    autFac: aut,
    conFac: con
    }
    itemFacturasDRC[index-1].push(factura);
    //listarFact(index);
    $("#nfac"+(index)).html(itemFacturasDRC[index-1].length);
 }

function removeFacDRC(item,fila){
  itemFacturasDRC[item-1].splice(fila, 1);
  listarFactDRC(item);
  $("#nfac"+item).html(itemFacturasDRC[item-1].length);
}

var numArchivos=0;
function archivosPreviewDRC(send) {
    var inp=document.getElementById("archivos");
    if(send!=1){
      $("#lista_archivos").html("<p class='text-success text-center'>Lista de Archivos</p>");
      for (var i = 0; i < inp.files.length; ++i) {
        numArchivos++;
        var name = inp.files.item(i).name;
        $("#lista_archivos").append("<div class='text-left'><label>"+name+"</label></div>");
       }
       $("#narch").addClass("estado");
     }else{
      numArchivos=0;
        $("#lista_archivos").html("Ningun archivo seleccionado");
        $("#narch").removeClass("estado");
     }

    
}
function readSingleFileDRC(evt) {
    var f = evt.target.files[0];
      if (f) {
          var r = new FileReader();
          r.onload = function(e) { 
              var contents = e.target.result;
              const lines = contents.split('\n').map(function (line){
                return line.split(',')
              })
              var index=$('#codCuenta').val();
              for (var i = 0; i < lines.length; i++) {
                if(String(lines[i]).trim()!=""){
                  lines[i]=String(lines[i]).split("\t");
                console.log(lines[i])
                   $('#nit_fac').val(lines[i][0]);
                   $('#nro_fac').val(lines[i][1]);
                   $('#fecha_fac').val(lines[i][3]);
                   $('#razon_fac').val("-");
                   $('#imp_fac').val(lines[i][4]);
                   $('#exe_fac').val("-");
                   $('#aut_fac').val(lines[i][2]);
                   $('#con_fac').val(lines[i][5]);
                   saveFacturaDRC();
                 } 
                }
               $("#qrquincho").val(""); 
          }
         r.readAsText(f);
      } else {
                    alert("Failed to load file");
      }
  
  }




function modalPlantilla(){
  if(cantidadItems==0){
    $("#msgError").html("<p>Debe tener una cuenta al menos</p>");
    $("#modalAlert").modal("show");
    $('#modalPlantilla').modal('hide');
  }else{
  $('#modalPlantilla').modal('show');
  }
}
function ayudaPlantilla(){
  alertaModal("<h5><b>AYUDA</b></h5>Los campos en la sección 'REGISTRAR PLANTILLA' son modificables y cuando se presione en el botón 'GUARDAR' ubicado en la parte inferior. También se guardaran los cambios realizados en dicha sección",'bg-secondary','text-white');
}
//plantilla guardar
function guardarPlantilla(){
  var cod="10000";
  var tipo=$("#tipo_comprobante").val();
  var glosa=$("#glosa").val();
  var titulo=$("#titulo").val();
  var descrip=$("#descrip_plan").val();
  var num=$("#cantidad_filas").val();
  var detalle=[];
  if(cod!=""&&titulo!=""){
    for (var i = 0; i < numFilas; i++) {
      detalle.push({
        cuenta:$("#cuenta"+(i+1)).val(),
        cuenta_auxiliar:$("#cuenta_auxiliar"+(i+1)).val(),
        unidad:$("#unidad"+(i+1)).val(),
        area:$("#area"+(i+1)).val(),
        debe:$("#debe"+(i+1)).val(),
        haber:$("#haber"+(i+1)).val(),
        glosa_detalle:$("#glosa_detalle"+(i+1)).val(),
        orden:(i+1)
      });
      //alert(JSON.stringify(detalle));
    }

    ajax=nuevoAjax();
      ajax.open("GET","ajaxSavePlantilla.php?codigo="+cod+"&titulo="+titulo+"&tipo="+tipo+"&glosa="+glosa+"&des="+descrip+"&cantidad_filas="+num+"&det="+JSON.stringify(detalle),true);
      ajax.onreadystatechange=function(){
        if (ajax.readyState==4) {
          $("#mensaje").html("<p class='text-success'>Registro satisfactorio</p>");
          $("#titulo").val("");
          $("#descrip_plan").val("");
          $('#modalPlantilla').modal('hide');
          Swal.fire("Correcto!", "Se Guardó la Plantilla!", "success");
          //window.location="../index.php?opcion=listComprobantes";
        }
      }
      ajax.send(null);
  }else{
    $("#mensaje").html("<p class='text-danger'>Debe ingresar un titulo a la plantilla</p>");
  }     
}

function cargarPlantillas(){
  if($("#tipo_comprobante").val()==""||$("#tipo_comprobante").val()==0||$("#tipo_comprobante").val()==null){
    $("#msgError").html("<p>Debe seleccionar un tipo de comprobante</p>");
    $("#modalAlert").modal("show");
  }else{
    $("#modalAbrirPlantilla").modal("show");
    ajax=nuevoAjax();
      ajax.open("GET","ajaxLoadPlantilla.php",true);
      ajax.onreadystatechange=function(){
        if (ajax.readyState==4) {
          var divPlantilla=document.getElementById("listaPlan");
          divPlantilla.innerHTML=ajax.responseText;
          $("#mensaje").html("<p class='text-success'>Listado de todas las plantillas</p>");
        }
      }
      ajax.send(null);
  }
}

function cargarPlantillaFacturas(n){
 numFilas=0;cantidadItems=0;filaActiva=0;
  for (var i = 0; i < n; i++) {
     numFilas++;
      cantidadItems++;
      filaActiva=numFilas;
      //aumentar un itemfactura
      var nfac=[];
      itemFacturas.push(nfac);    
   }; 
}
function abrirPlantilla(id,n,glosa,tipo){
  itemFacturas =[];
  cargarPlantillaFacturas(n);
  document.getElementById("cantidad_filas").value=n;
  $("#glosa").val(glosa);
  //$("#tipo_comprobante").val(tipo);
  var parametros={"codigo":id};
  $.ajax({
        type: "GET",
        dataType: 'html',
        url: "ajaxOpenPlantilla.php",
        data: parametros,
        success:  function (resp) {
          var fi=$("#fiel");
          fi.html(resp);
          numFilas=n;
          calcularTotalesComprobante("null");
          $('.selectpicker').selectpicker("refresh");
          $("#modalAbrirPlantilla").modal("hide");     
        }
    }); 
}
function nuevaDistribucionPonerFila(fila,tipoDistribucion){
 var glosa = $("#glosa_detalle"+fila).val();
 if(glosa==""){
   $("#mensajeDist").html("<p>La glosa se encuentra vacía!</p>");
 }else{
  var area=$("#area"+fila).val();
   if(area==""||area==null||area==0){
     $("#mensajeDist").html("<p>El area esta vacía</p>");
   }else{
    if($("#debe"+fila).val()==""&& $("#haber"+fila).val()==""){
       $("#mensajeDist").html("<p>Debe ingresar El Debe o el Haber que corresponda distribuir</p>");
    }else{
      if($("#debe"+fila).val()==0&& $("#haber"+fila).val()==0){
        $("#mensajeDist").html("<p>No puede distribuir un monto 0</p>");
      }else{
         $("#mensajeDist").html("<p>¿Esta seguro de distribuir los gastos?</p>");
         $("#tipoDistribucion").val(tipoDistribucion);         
         $("#distFila").val(fila);
      }    
    }   
   }
 }
}
function nuevaDistribucion(){
  var fila = $("#distFila").val();
  var tipoDistribucion=$("#tipoDistribucion").val();
  var cuenta = $("#cuenta"+fila).val();
  var debe = $("#debe"+fila).val();
  var haber = $("#haber"+fila).val();
  var glosa = $("#glosa_detalle"+fila).val();
  var area=$("#area"+fila).val();
  var oficina=$("#unidad"+fila).val();
  var cuenta_aux=$("#cuenta_auxiliar"+fila).val();
  var valor=0; var cond=9;
  if(debe==""&&haber==""){
    valor=0;
  }else{
    if(debe==0||debe==""){
      valor = haber;
      cond=1;
      //alert(haber);
    }else{
      valor=debe;
      cond=0;
    }
  }
  if(cuenta==""){
    valor=0;
  }
  if(fila==""){valor=0;}
 /* for (var i = 0; i < distribucionPor.length; i++) {
    
  };*/
  var urlDist="";
  if(valor!=0){
    ajax=nuevoAjax();
    if(tipoDistribucion==1){
      urlDist="ajaxDistribucionGastos.php";
      ajax.open("GET",urlDist+"?fila="+fila+"&unidad="+oficina+"&area="+area+"&filas="+cantidadItems+"&cuenta_aux="+cuenta_aux+"&cuenta="+cuenta+"&cond="+cond+"&valor="+valor+"&glosa="+glosa+"&listDist="+JSON.stringify(distribucionPor),true);
    }else{
      urlDist="ajaxDistribucionGastosArea.php";
      ajax.open("GET",urlDist+"?fila="+fila+"&unidad="+oficina+"&area="+area+"&filas="+cantidadItems+"&cuenta_aux="+cuenta_aux+"&cuenta="+cuenta+"&cond="+cond+"&valor="+valor+"&glosa="+glosa+"&listDist="+JSON.stringify(distribucionPorArea),true);
    }
    ajax.onreadystatechange=function(){
      if (ajax.readyState==4) {
        var fi=$("#fiel");
        //minusCuentaContable(fila);
        //alert(ajax.responseText);
        fi.append(ajax.responseText);
        $('.selectpicker').selectpicker(["refresh"]);
      }
    }
    ajax.send(null); 
    $("#distFila").val("");
  }  
}
function buscarComprobantes(estado){
  var valor=$("#buscar_comprobantes").val();
  ajax=nuevoAjax();
  ajax.open('GET', 'comprobantes/ajaxBusquedaComprobantes.php?valor='+valor+"&estado="+estado,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      var contenedor=$("#data_solicitudes_facturacion");
      contenedor.html(ajax.responseText);
    }
  }
  ajax.send(null)
}

// function buscarComprobantesFecha(){
//     var valor1=$("#fechaBusquedaInicio").val();
//     var valor2=$("#fechaBusquedaFin").val();
    
//     ajax=nuevoAjax();
//     ajax.open('GET', 'comprobantes/ajax_busquedaComprobanteUO.php?cod_uo=null&tipo=null&fechaI='+valor1+'&fechaF='+valor2+'&glosa=null',true);
//     ajax.onreadystatechange=function() {
//       if (ajax.readyState==4) {
//         var contenedor=$("#data_comprobantes");
//         contenedor.html(ajax.responseText);
//         $("#modalBuscador").modal("hide");
//       }
//     }
//     ajax.send(null)
// }
function botonBuscarComprobante(){
  var valor_uo=$("#OficinaBusqueda").val();
  var valor_tipo=$("#tipoBusqueda").val();
  var valor_fi=$("#fechaBusquedaInicio").val();
  var valor_ff=$("#fechaBusquedaFin").val();
  var valor_glosa=$("#glosaBusqueda").val();
  var valor_nro_compr=$("#nro_comprobante").val();
  var valor_nro_cuenta=$("#cuenta_auto_id").val();
  
  
  ajax=nuevoAjax();
  ajax.open('GET', 'comprobantes/ajax_busquedaComprobanteUO.php?cod_uo='+valor_uo+'&tipo='+valor_tipo+'&fechaI='+valor_fi+'&fechaF='+valor_ff+'&glosa='+valor_glosa+'&comprobante='+valor_nro_compr+'&cuenta='+valor_nro_cuenta,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      var contenedor=$("#data_comprobantes");
      contenedor.html(ajax.responseText);
      $("#modalBuscador").modal("hide");
    }
  }
  ajax.send(null)
}
function botonBuscarComprobante2(){
  var valor_uo=$("#OficinaBusqueda").val();
  var valor_tipo=$("#tipoBusqueda").val();
  var valor_fi=$("#fechaBusquedaInicio").val();
  var valor_ff=$("#fechaBusquedaFin").val();
  var valor_glosa=$("#glosaBusqueda").val();
  var valor_nro_compr=$("#nro_comprobante").val();
  var valor_nro_cuenta=$("#cuenta_auto_id").val();
  ajax=nuevoAjax();
  ajax.open('GET', 'comprobantes/ajax_busquedaComprobanteUO2.php?cod_uo='+valor_uo+'&tipo='+valor_tipo+'&fechaI='+valor_fi+'&fechaF='+valor_ff+'&glosa='+valor_glosa+'&comprobante='+valor_nro_compr+'&cuenta='+valor_nro_cuenta,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      var contenedor=$("#data_comprobantes");
      contenedor.html(ajax.responseText);
      $("#modalBuscador").modal("hide");
    }
  }
  ajax.send(null)
}  
function botonBuscarComprobanteIng(codigo){
  ajax=nuevoAjax();
  ajax.open('GET', 'comprobantes/ajax_filtrarComporbantes.php?codigo='+codigo,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      var contenedor=$("#data_comprobantes");
      contenedor.html(ajax.responseText);
      // $("#modalBuscador").modal("hide");
    }
  }
  ajax.send(null)
}

function botonBuscarComprobanteIng2(codigo){
  ajax=nuevoAjax();
  ajax.open('GET', 'comprobantes/ajax_filtrarComporbantes2.php?codigo='+codigo,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      var contenedor=$("#data_comprobantes");
      contenedor.html(ajax.responseText);
      // $("#modalBuscador").modal("hide");
    }
  }
  ajax.send(null)
}


// var areas_tabla=[]; 
var array_comprobantes_general=[];
var numFilasA=0;
function botonSiguienteComprobante(){  
  var cantidadTotal=array_comprobantes_general.length;
  if(numFilasA < cantidadTotal-1){
    numFilasA++;
    // botonItemsFunction(numFilasA+1);//indicamos en que posicion esta el item
    // alert(cantidadTotal);
    var dato=array_comprobantes_general[numFilasA];
    // alert(numFilasA+"-"+dato);
    ajax=nuevoAjax();
    ajax.open('GET', 'comprobantes/lista_comprobantes2_ajax.php?codigo='+dato+'&pos='+(numFilasA+1)+'&total='+cantidadTotal,true);
    ajax.onreadystatechange=function() {
      if (ajax.readyState==4) {
        var contenedor=$("#data_comprobantes");
        contenedor.html(ajax.responseText);
        // $("#modalBuscador").modal("hide");
      }
    }
    ajax.send(null)
  }  
}
function botonAnteriorComprobante(){  
  var cantidadTotal=array_comprobantes_general.length;
  if(numFilasA > 0){
    numFilasA--;
    var dato=array_comprobantes_general[numFilasA];
    // botonItemsFunction(numFilasA+1);//indicamos en que posicion esta el item
    ajax=nuevoAjax();
    ajax.open('GET', 'comprobantes/lista_comprobantes2_ajax.php?codigo='+dato+'&pos='+(numFilasA+1)+'&total='+cantidadTotal,true);
    ajax.onreadystatechange=function() {
      if (ajax.readyState==4) {
        var contenedor=$("#data_comprobantes");
        contenedor.html(ajax.responseText);
        // $("#modalBuscador").modal("hide");
      }
    }
    ajax.send(null)
  }  
}
function botonInicioComprobante(){  
  var cantidadTotal=array_comprobantes_general.length;  
    numFilasA=0;
    var dato=array_comprobantes_general[numFilasA];
    // botonItemsFunction(numFilasA+1);//indicamos en que posicion esta el item
    ajax=nuevoAjax();
    ajax.open('GET', 'comprobantes/lista_comprobantes2_ajax.php?codigo='+dato+'&pos='+(numFilasA+1)+'&total='+cantidadTotal,true);
    ajax.onreadystatechange=function() {
      if (ajax.readyState==4) {
        var contenedor=$("#data_comprobantes");
        contenedor.html(ajax.responseText);
        // $("#modalBuscador").modal("hide");
      }
    }
    ajax.send(null)
  // }  
}
function botonFinComprobante(){  
  var cantidadTotal=array_comprobantes_general.length;  
  numFilasA=cantidadTotal-1;
  // botonItemsFunction(numFilasA+1);//indicamos en que posicion esta el item
  var dato=array_comprobantes_general[numFilasA];
  // alert(numFilasA+"-"+dato);
  ajax=nuevoAjax();
  ajax.open('GET', 'comprobantes/lista_comprobantes2_ajax.php?codigo='+dato+'&pos='+(numFilasA+1)+'&total='+cantidadTotal,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      var contenedor=$("#data_comprobantes");
      contenedor.html(ajax.responseText);
      // $("#modalBuscador").modal("hide");
    }
  }
  ajax.send(null)
}
function input_buscar_comprobante(){ 
  var intro_number=$("#intro_number").val();
  var cantidadTotal=array_comprobantes_general.length;
  numFilasA=intro_number-1;
  // alert(cantidadTotal);
  if(intro_number<cantidadTotal+1 && intro_number>0){
    var dato=array_comprobantes_general[intro_number-1];
    // botonItemsFunction(numFilasA+1);//indicamos en que posicion esta el item
    ajax=nuevoAjax();
    ajax.open('GET', 'comprobantes/lista_comprobantes2_ajax.php?codigo='+dato+'&pos='+(intro_number)+'&total='+cantidadTotal,true);
    ajax.onreadystatechange=function() {
      if (ajax.readyState==4) {
        var contenedor=$("#data_comprobantes");
        contenedor.html(ajax.responseText);
        // $("#modalBuscador").modal("hide");
      }
    }
    ajax.send(null)
  }else{
    Swal.fire("ERROR!", "Comprobante no encontrado!", "warning");
  }
  
}

function sendAprobacion(cod,estado){
  if(estado==3){
    $("#modalAlertStyle").addClass("bg-info");
    $("#modalAlertStyle").removeClass("bg-danger");
    $("#preg_comprobante").html("¿Desea APROBAR el comprobante?");
  }else{
    $("#preg_comprobante").html("¿Desea ANULAR el comprobante?");
    $("#modalAlertStyle").removeClass("bg-info");
    $("#modalAlertStyle").addClass("bg-danger");
  }
  $("#cod_comprobantemodal").val(cod);
  $("#cod_estado").val(estado);
}
function cambiarEstadoCompro(){
  var codigo=$("#cod_comprobantemodal").val();
  var estado=$("#cod_estado").val();
  ajax=nuevoAjax();
  ajax.open('GET', 'comprobantes/ajaxUpdateEstado.php?codigo='+codigo+"&estado="+estado,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      //buscarComprobantes("nohay");
      window.location.href="index.php?opcion=listComprobantesRegistrados";
    }
  }
  ajax.send(null)
}

/*function cargarArchivo(url){
$("#cont_archivos").load("../archivos-respaldo/COMP-5/FORM_110_3_4868422016_05112019 - copia.txt");
}
*/





 /*function listarFac(id){
  var contenedor = document.getElementById('divResultadoListaFac');
  ajax=nuevoAjax();
  ajax.open('GET', 'ajaxListarFac.php?id_compro='+nroCuenta,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
    }
  }
  ajax.send(null)
}*/   


  
/*const input = document.querySelector('input[type="file"]')

input.addEventListener('change', function (e) {
  console.log(input.files)
}, false)*/
var numArchivos=0;
function archivosPreview(send) {
    itemDocumentos=[];
    numArchivos=0;
    var inp=document.getElementById("archivos");
    if(send!=1){
      var table = $('<table>').addClass('table');
      table.addClass('table-condensed');
      table.addClass('table-bordered');
       var titulos = $('<tr>').addClass('bg-info text-white');
       titulos.append($('<td>').addClass('text-left').text('#'));
       titulos.append($('<td>').addClass('').text('DOCUMENTO'));
       titulos.append($('<td>').addClass('').text('.*'));
       if($("#formSolDet").length>0){
         titulos.append($('<td>').addClass('').text('TIPO DOCUMENTO')); 
         titulos.append($('<td>').addClass('').text('DESCRIPCIÓN TIPO'));     
       }
       table.append(titulos);
      for (var i = 0; i < inp.files.length; ++i) {
        numArchivos++;
        var name = inp.files.item(i).name;
        var row = $('<tr>').addClass('');
       row.append($('<td>').addClass('text-left').text(i+1));
       row.append($('<td>').addClass('font-weight-bold').text(name));
       row.append($('<td>').addClass('font-weight-bold').text(/[^.]+$/.exec(name)));
       if($("#formSolDet").length>0){
        var doc = {
          id:i,
          nombre:name,
          tipo:0,
          nombre_tipo:""
        }
        itemDocumentos.push(doc);
        console.log(JSON.stringify(itemDocumentos));
        var htmlSelect = '<select onChange="asignarTipoDocumento(\''+name+'\','+(i+1)+')" class="selectpicker form-control form-control-sm" name="tipo_documento'+(i+1)+'" id="tipo_documento'+(i+1)+'" data-style="btn btn-primary">';
         htmlSelect+=$("#tipo_documento").html();
         htmlSelect+='</select>';
         row.append($('<td>').addClass('').html(htmlSelect));

        var htmlInput='<input class="form-control text-right text-muted" placeholder="Ingresar descripción" id="nombre_tipodocumento'+(i+1)+'" readonly value="SIN TIPO ARCHIVO" onkeyup="asignarTipoDocumentoText('+(i+1)+')" onkeydown="asignarTipoDocumentoText('+(i+1)+')">'       
        row.append($('<td>').addClass('').html(htmlInput));
       }        
       table.append(row);
        // $("#lista_archivos").append("<div class='text-left'><label>"+name+"</label></div>");
       }
       $("#narch").addClass("estado");
       $("#lista_archivos").html(table);
       $('.selectpicker').selectpicker("refresh");
     }else{
      numArchivos=0;
        $("#lista_archivos").html("Ningun archivo seleccionado");
        $("#narch").removeClass("estado");
     }
}
var itemDocumentos=[];
var itemDocumentosDetalle=[];
function asignarTipoDocumento(nombreArchivo,fila){
  var tipos = $("#tipo_documento"+fila).val(); 
  if(tipos!=-100){
    $("#nombre_tipodocumento"+fila).val($('#tipo_documento'+fila+' option:selected').text().toUpperCase());
    if(!($("#nombre_tipodocumento"+fila).is("[readonly]"))){
      $("#nombre_tipodocumento"+fila).attr("readonly",true);
    }
  }else{
    $("#nombre_tipodocumento"+fila).val("");
    $("#nombre_tipodocumento"+fila).focus();
    if($("#nombre_tipodocumento"+fila).is("[readonly]")){
      $("#nombre_tipodocumento"+fila).removeAttr("readonly");
    } 
  }
  var nombre_tipodocumento=$("#nombre_tipodocumento"+fila).val();
  itemDocumentos[fila-1].tipo=tipos;
  itemDocumentos[fila-1].nombre_tipo=nombre_tipodocumento;
  console.log(JSON.stringify(itemDocumentos));
}

function asignarTipoDocumentoDetalle(nombreArchivo,fila,indice){
  var tipos = $("#tipo_documentodetalle"+fila).val(); 
  if(tipos!=-100){
    $("#nombre_tipodocumentodetalle"+fila).val($('#tipo_documentodetalle'+fila+' option:selected').text().toUpperCase());
    if(!($("#nombre_tipodocumentodetalle"+fila).is("[readonly]"))){
      $("#nombre_tipodocumentodetalle"+fila).attr("readonly",true);
    }
  }else{
    $("#nombre_tipodocumentodetalle"+fila).val("");
    $("#nombre_tipodocumentodetalle"+fila).focus();
    if($("#nombre_tipodocumentodetalle"+fila).is("[readonly]")){
      $("#nombre_tipodocumentodetalle"+fila).removeAttr("readonly");
    } 
  }
  var nombre_tipodocumento=$("#nombre_tipodocumentodetalle"+fila).val();
  itemDocumentosDetalle[indice-1][fila-1].tipo=tipos;
  itemDocumentosDetalle[indice-1][fila-1].nombre_tipo=nombre_tipodocumento;
  console.log(JSON.stringify(itemDocumentosDetalle));
}


function asignarTipoDocumentoText(fila){
  var nombre_tipodocumento=$("#nombre_tipodocumento"+fila).val();
  itemDocumentos[fila-1].nombre_tipo=nombre_tipodocumento;
  //console.log(JSON.stringify(itemDocumentos));
}
function asignarTipoDocumentoTextDetalle(fila,index){
  var nombre_tipodocumento=$("#nombre_tipodocumentodetalle"+fila).val();
  itemDocumentosDetalle[index-1][fila-1].nombre_tipo=nombre_tipodocumento;
}
function readSingleFile(evt) {
    var f = evt.target.files[0];
      if (f) {
          var r = new FileReader();
          r.onload = function(e) { 
              var contents = e.target.result;
              const lines = contents.split('\n').map(function (line){
                return line.split(',')
              })
              var index=$('#codCuenta').val();
              for (var i = 0; i < lines.length; i++) {
                if(String(lines[i]).trim()!=""){
                  lines[i]=String(lines[i]).split("\t");
                console.log(lines[i])
                   $('#nit_fac').val(lines[i][0]);
                   $('#nro_fac').val(lines[i][1]);
                   $('#fecha_fac').val(lines[i][3]);
                   $('#razon_fac').val("-");
                   $('#imp_fac').val(lines[i][4]);
                   $('#exe_fac').val("-");
                   $('#aut_fac').val(lines[i][2]);
                   $('#con_fac').val(lines[i][5]);
                   saveFactura();
                 } 
                }
               $("#qrquincho").val(""); 
          }
         r.readAsText(f);
      } else {
                    alert("Failed to load file");
      }
  
  }


 function pulsar(e) {
    if (e.keyCode === 13 && !e.shiftKey) {
        buscarCuenta();
    }

}

function cargarTipoCambio(id){
  const ano=$("#sel_ano").val();
  const mes=$("#sel_mes").val();
  const fecha=ano+"-"+mes;
   ajax=nuevoAjax();
  ajax.open('GET', 'ajaxLoadTipoCambio.php?codigo='+id+'&fecha='+fecha,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      document.getElementById('fiel').innerHTML = ajax.responseText
    }
  }
  ajax.send(null)
}


//**************************************************plantilla de costos  ***********************************************************************

function addGrupoPlantilla(obj) {
  var correcto=1;
  if($("#cantidad_personal").length>0){
          if($("#cantidad_personal").text()>0){
             correcto=1;
           }else{
             correcto=0;
           }
  }

  if(correcto==1){
      numFilas++;
      cantidadItems++;
      filaActiva=numFilas;
      //aumentar un itemfactura
      var ndet=[];
      itemDetalle.push(ndet);
      document.getElementById("cantidad_filas").value=numFilas;
      console.log("num: "+numFilas+" cantidadItems: "+cantidadItems);
      fi = document.getElementById('fiel');
      contenedor = document.createElement('div');
      contenedor.id = 'div'+numFilas;  
      fi.type="style";
      fi.appendChild(contenedor);
      var divDetalle;
      divDetalle=$("#div"+numFilas);
      //document.getElementById('nro_cuenta').focus();
      ajax=nuevoAjax();
      ajax.open("GET","ajaxGrupoPlantilla.php?idFila="+numFilas,true);
      ajax.onreadystatechange=function(){
        if (ajax.readyState==4) {
          divDetalle.html(ajax.responseText);
          divDetalle.bootstrapMaterialDesign();
          $('.selectpicker').selectpicker("refresh");
          return false;
       }
      }   
      ajax.send(null);   
  }else{
    Swal.fire("Informativo!", "Debe registrar al menos un Personal", "warning");
  }
}

function alertaModal(msg,fondo,texto){
  $('#msgError').html("<p>"+msg+"</p>");
  $('#modalAlert').modal('show');
  $('#modalAlertStyle').removeAttr("class");
  $('#modalAlertStyle').attr("class","modal-content "+fondo+" "+texto);
}

function guardarPlantillaCosto(){
  var nombre=$("#nombre").val();
  var abrev=$("#abreviatura").val();
  var unidad=$("#unidad").val();
  var area=$("#area").val();
  var utilidadLocal=$("#utilidad_minibnorca").val();
  //var utilidadExterna=$("#utilidad_minfuera").val();
  var alumnosLocal=$("#cantidad_alumnosibnorca").val();
  //var alumnosExterno=$("#cantidad_alumnosfuera").val();
  var precioLocal=$("#precio_ventaibnorca").val();
  //var precioExterno=$("#precio_ventafuera").val();

  if(alumnosLocal==""||precioLocal==""||utilidadLocal==""||nombre==""||abrev==""||!(unidad>0)||!(area>0)){
    Swal.fire("Informativo!", "Todos los campos son requeridos", "warning");
  }else{
     var parametros={"nombre":nombre,"abrev":abrev,"unidad":unidad,"area":area,"utilidad_local":utilidadLocal,"alumnos_local":alumnosLocal,"precio_local":precioLocal};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "plantillas_costos/ajaxRegistrarPlantilla.php",
        data: parametros,
        beforeSend: function () { 
         Swal.fire("Informativo!", "Procesando datos! espere...", "warning");
          
        },
        success:  function (resp) {
         alerts.showSwal('success-message','plantillas_costos/registerGrupos.php?cod='+resp);
        }
    });
  }
}
var itemDetalle =[];
function listDetalle(id){
  var nombreGrupo=$("#nombre_grupo"+id).val();
  if(nombreGrupo==""){nombreGrupo="Sin Nombre";}
   $("#divTituloGrupo").html('<h4 class="card-title">'+nombreGrupo+'</h4>');
   $("#codGrupo").val(id);
   listarDet(id);
   if($("#tipo_costo"+id).val()!=""){
    if($("#tipo_costo"+id).val()==2){
       $("#columna_alumno").removeClass("d-none");
    }else{
       $("#columna_alumno").addClass("d-none");
    }  
   }
    limpiarDatosDetalleModal();  
   $("#modalDet").modal("show");
 }
function limpiarDatosDetalleModal(){
  $("#tipo_dato").val("1");
  $("#cuenta_detalle").val("");
  $('.selectpicker').selectpicker("refresh");
  $("#monto_ibnorca").val(0);
  $("#monto_f_ibnorca").val(0);
  $("#monto_alumno").val(0);
  $("#monto_calculado").val(0);
  $("#monto_ibnorca_edit").val(0);
  $("#monto_f_ibnorca_edit").val(0);
  $("#monto_alumno_edit").val(0);
  if(!($("#montos_editables").hasClass("d-none"))){
    $("#montos_editables").addClass("d-none");
  }
  if(($("#boton_guardardetalle").hasClass("d-none"))){
    $("#boton_guardardetalle").removeClass("d-none");
  }
 }
function mostrarPreciosPlantilla(){
  $("#modalPrecio").modal("show");
  var parametros={"codigo":$("#cod_plantilla").val()};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "ajaxListPrecioPlantilla.php",
        data: parametros,
        success:  function (resp) {
         $("#lista_preciosplantilla").html(resp);
        }
    });
}

 function listarDet(id){
  var div=$('<div>').addClass('table-responsive');
  var table = $('<table>').addClass('table table-condensed');
  var titulos = $('<tr>').addClass('bg-info text-white');
     titulos.append($('<td>').addClass('').text('#'));
     titulos.append($('<td>').addClass('').text('PARTIDA'));
     titulos.append($('<td>').addClass('').text('TIPO'));
     if($("#codValor").length){
       titulos.append($('<td>').addClass('').text('M GLOBAL'));
       titulos.append($('<td>').addClass('').text('M x AUDITORIA'));
     }else{
       titulos.append($('<td>').addClass('').text('M x MES'));
       titulos.append($('<td>').addClass('').text('M x MODULO'));
     }    
     titulos.append($('<td>').addClass('').text('M x PERSONA'));
     titulos.append($('<td>').addClass('').text('OPCION'));
     table.append(titulos);
   for (var i = 0; i < itemDetalle[id-1].length; i++) {
     var row = $('<tr>').addClass('');
     row.append($('<td>').addClass('text-right small').text(i+1));
     row.append($('<td>').addClass('text-right small').text(" Partida: "+itemDetalle[id-1][i].cuenta));
     row.append($('<td>').addClass('text-right small').text(itemDetalle[id-1][i].tipo));
     row.append($('<td>').addClass('text-right small').text(redondeo(itemDetalle[id-1][i].monto_i*$("#cod_mescurso").val())));
     row.append($('<td>').addClass('text-right small').text(redondeo(itemDetalle[id-1][i].monto_fi)));
     row.append($('<td>').addClass('text-right small').text(redondeo(itemDetalle[id-1][i].monto_fi/$("#alumnos_ibnorca").val())));
     var htmlBoton="";
     if(itemDetalle[id-1][i].tipo==2){
       htmlBoton='<button class="btn btn-info btn-fab btn-sm" onclick="listarDetPlantilla('+id+','+i+');"><i class="material-icons">list</i></button>';
     }
     row.append($('<td>').addClass('text-right small').html(htmlBoton+'<button class="btn btn-danger btn-link" onclick="removeDet('+id+','+i+');"><i class="material-icons">remove_circle</i></button>'));
     table.append(row);
   }
   div.append(table);
   $('#divResultadoListaDet').html(div);
 }

 function savePlantillaDetalle(nombreInput){
  var index=$('#codGrupo').val();
  var str_cuenta=dividirCadena($('#cuenta_detalle').val(),"@");
  var existeFila=0; var filae=0; var pose=0;
   for (var i = 0; i < itemDetalle[index-1].length; i++) {
     if(itemDetalle[index-1][i].codigo_cuenta==str_cuenta[0]){
       existeFila=1;
       filae=index;
       pose=i;
       break;
     }
   };

   if(existeFila==1){
    if(nombreInput=='mensual'){
     Swal.fire('Informativo!','La Partida '+str_cuenta[1]+ ' Ya esta agregada!','warning'); 
    }else{
      Swal.fire({
        title: '¿Guardar Cambios?',
        text: "Se sobreescribirá el monto registrado",
         type: 'warning',
        showCancelButton: true,
        confirmButtonClass: 'btn btn-info',
        cancelButtonClass: 'btn btn-danger',
        confirmButtonText: 'Si, Guardar',
        cancelButtonText: 'No',
        buttonsStyling: false
       }).then((result) => {
          if (result.value) {
            //aqui poner para el listado
               removeDet(filae,pose);
               if($("#monto_ibnorca").val()==""||$("#monto_f_ibnorca").val()==""||str_cuenta.length==1){
                  $("#mensajeDetalle").html("<center><p class='text-danger'>Todos los campos son requeridos</p></center>");
                }else{
                 var tiDato=$('#tipo_dato').val();
                 var monto_calc=$('#monto_calculado').val();
                 if(tiDato==1){
                   var monto_ib=$('#monto_calculado').val();
                   var monto_fib=$('#monto_calculado').val();   
                 }else{
                   switch (nombreInput){
                     case 'monto_ibnorca_edit':
                        var monto_ib=$("#monto_ibnorca_edit").val()/$("#cod_mescurso").val();
                        var monto_fib=$("#monto_ibnorca_edit").val()/$("#cod_mescurso").val();
                     break;
                     case 'monto_f_ibnorca_edit':
                        var monto_ib=$("#monto_f_ibnorca_edit").val();
                        var monto_fib=$("#monto_f_ibnorca_edit").val();
                     break;
                     case 'monto_alumno_edit':
                        var monto_ib=$("#monto_alumno_edit").val()*$("#alumnos_ibnorca").val();
                        var monto_fib=$("#monto_alumno_edit").val()*$("#alumnos_ibnorca").val();
                     break;
                   }

                 }
                var detalle={
                  codigo_cuenta:str_cuenta[0],
                  cuenta:str_cuenta[1],
                  tipo: $('#tipo_dato').val(),
                  monto_i: monto_ib,
                  monto_fi: monto_fib,
                  monto_cal: monto_calc
                  }
                  itemDetalle[index-1].push(detalle);
                  $('#cuenta').val("");
                  $('#tipo').val("");

                   listarDet(index);
                   mostrarDetalle(index);
                   $("#ndet"+index).html(itemDetalle[index-1].length);
                   /*$("#link110").addClass("active");$("#link111").removeClass("active");
                   $("#nav_boton2").addClass("active");$("#nav_boton1").removeClass("active");*/
                   $("#mensajeDetalle").html("<center><p class='text-success'>Registro satisfactorio</p></center>");
                   $.notify({message: 'Partida '+str_cuenta[1]+' registrada' },{type: 'success'});
                    if(nombreInput=="mensual"){
                      $("#modalDet").modal("hide");
                    }else{
                      $("#modalDetallesPartida").modal("hide");
                    }
                   }           
            return(true);
          } else if (result.dismiss === Swal.DismissReason.cancel) {
            return(false);
          }
        });
    }
     
   }else{
  if($("#monto_ibnorca").val()==""||$("#monto_f_ibnorca").val()==""||str_cuenta.length==1){
    $("#mensajeDetalle").html("<center><p class='text-danger'>Todos los campos son requeridos</p></center>");
  }else{
    var tiDato=$('#tipo_dato').val();
    var monto_calc=$('#monto_calculado').val();
    if(tiDato==1){
      var monto_ib=$('#monto_calculado').val();
      var monto_fib=$('#monto_calculado').val();   
    }else{
      switch (nombreInput){
        case 'monto_ibnorca_edit':
           var monto_ib=$("#monto_ibnorca_edit").val()/$("#cod_mescurso").val();
           var monto_fib=$("#monto_ibnorca_edit").val()/$("#cod_mescurso").val();
        break;
        case 'monto_f_ibnorca_edit':
           var monto_ib=$("#monto_f_ibnorca_edit").val();
           var monto_fib=$("#monto_f_ibnorca_edit").val();
        break;
        case 'monto_alumno_edit':
           var monto_ib=$("#monto_alumno_edit").val()*$("#alumnos_ibnorca").val();
           var monto_fib=$("#monto_alumno_edit").val()*$("#alumnos_ibnorca").val();
        break;
      }

    }
  var detalle={
    codigo_cuenta:str_cuenta[0],
    cuenta:str_cuenta[1],
    tipo: $('#tipo_dato').val(),
    monto_i: monto_ib,
    monto_fi: monto_fib,
    monto_cal: monto_calc
    }
  itemDetalle[index-1].push(detalle);
  $('#cuenta').val("");
  $('#tipo').val("");

  listarDet(index);
  mostrarDetalle(index);
  $("#ndet"+index).html(itemDetalle[index-1].length);
  /*$("#link110").addClass("active");$("#link111").removeClass("active");
  $("#nav_boton2").addClass("active");$("#nav_boton1").removeClass("active");*/
  $("#mensajeDetalle").html("<center><p class='text-success'>Registro satisfactorio</p></center>");
  $.notify({message: 'Partida '+str_cuenta[1]+' registrada' },{type: 'success'});
   if(nombreInput=="mensual"){
     $("#modalDet").modal("hide");
   }else{
     $("#modalDetallesPartida").modal("hide");
   }
  }
    
   }
  
 }

 function dividirCadena(cadenaADividir,separador) {
   var arrayDeCadenas = cadenaADividir.split(separador);
   return arrayDeCadenas;
}

function mostrarDetalle(id){
  var html="";
  if($("#button_list_servicios").length){
    for (var i = 0; i < itemDetalle[id-1].length; i++) {
    if(itemDetalle[id-1][i].tipo==1){
      html+="<tr class=\"small\"><td>"+itemDetalle[id-1][i].cuenta+"</td><td>"+itemDetalle[id-1][i].tipo+"</td><td class='text-right'>"+redondeo(itemDetalle[id-1][i].monto_i*$("#cod_mescurso").val())+"</td><td class='text-right'>-</td></tr>";
    }else{
      html+="<tr class=\"small\"><td><a title=\"Detalles\" href=\"#\" onclick=\"listarDetPlantilla("+id+","+i+")\" class=\"btn btn-info btn-sm\">"+itemDetalle[id-1][i].cuenta+"</a></td><td>"+itemDetalle[id-1][i].tipo+"</td><td class='text-right'>"+redondeo(itemDetalle[id-1][i].monto_i*$("#cod_mescurso").val())+"</td><td class='text-right'>"+redondeo(itemDetalle[id-1][i].monto_fi/$("#alumnos_ibnorca").val())+"</td></tr>";
    } 
   };
  }else{
   for (var i = 0; i < itemDetalle[id-1].length; i++) {
    if(itemDetalle[id-1][i].tipo==1){
      html+="<tr class=\"small\"><td>"+itemDetalle[id-1][i].cuenta+"</td><td>"+itemDetalle[id-1][i].tipo+"</td><td class='text-right'>"+redondeo(itemDetalle[id-1][i].monto_i*$("#cod_mescurso").val())+"</td><td class='text-right'>"+redondeo(itemDetalle[id-1][i].monto_fi)+"</td><td class='text-right'>"+redondeo(itemDetalle[id-1][i].monto_fi/$("#alumnos_ibnorca").val())+"</td></tr>";
    }else{
      html+="<tr class=\"small\"><td><a title=\"Detalles\" href=\"#\" onclick=\"listarDetPlantilla("+id+","+i+")\" class=\"btn btn-info btn-sm\">"+itemDetalle[id-1][i].cuenta+"</a></td><td>"+itemDetalle[id-1][i].tipo+"</td><td class='text-right'>"+redondeo(itemDetalle[id-1][i].monto_i*$("#cod_mescurso").val())+"</td><td class='text-right'>"+redondeo(itemDetalle[id-1][i].monto_fi)+"</td><td class='text-right'>"+redondeo(itemDetalle[id-1][i].monto_fi/$("#alumnos_ibnorca").val())+"</td></tr>";
    } 
   };
  }
  $("#cuerpoDetalle").html(html);
  $("#cabezadetalle").html('<h6 class="card-title">Detalle "'+$("#nombre_grupo"+id).val()+'"</h6>');
}

function abrirDetalleCosto(index,cod_pr,nombre_pr,tipo_n,monto_local,monto_externo,monto_calculado){
   var detalle={
    codigo_cuenta: cod_pr,
    cuenta: nombre_pr,
    tipo:tipo_n,
    monto_i: monto_local,
    monto_fi: monto_externo,
    monto_cal: monto_calculado
    }
    itemDetalle[index-1].push(detalle);
    $("#ndet"+(index)).html(itemDetalle[index-1].length);
 }

 function minusGrupoPlantilla(idF){
      var elem = document.getElementById('div'+idF);
      elem.parentNode.removeChild(elem);
      if(idF<numFilas){
      for (var i = parseInt(idF); i < (numFilas+1); i++) {
        var nuevoId=i+1;
       $("#div"+nuevoId).attr("id","div"+i);
       $("#tipo_costo"+nuevoId).attr("name","tipo_costo"+i);
       $("#tipo_costo"+nuevoId).attr("id","tipo_costo"+i);
       $("#nombre_grupo"+nuevoId).attr("name","nombre_grupo"+i);
       $("#nombre_grupo"+nuevoId).attr("id","nombre_grupo"+i);
       $("#abreviatura_grupo"+nuevoId).attr("name","abreviatura_grupo"+i);
       $("#abreviatura_grupo"+nuevoId).attr("id","abreviatura_grupo"+i);

       $("#boton_remove"+nuevoId).attr("onclick","minusGrupoPlantilla('"+i+"')");
       $("#boton_remove"+nuevoId).attr("id","boton_remove"+i);
       $("#boton_det"+nuevoId).attr("onclick","listDetalle('"+i+"')");
       $("#boton_det"+nuevoId).attr("id","boton_det"+i);
       $("#boton_det_list"+nuevoId).attr("onclick","mostrarDetalle('"+i+"')");
       $("#boton_det_list"+nuevoId).attr("id","boton_det_list"+i);
       $("#ndet"+nuevoId).attr("id","ndet"+i);
      }
     } 
     itemDetalle.splice((idF-1), 1);
      numFilas=numFilas-1;
      cantidadItems=cantidadItems-1;
      filaActiva=numFilas;
      document.getElementById("cantidad_filas").value=numFilas;  
}
function removeDet(item,fila){
  itemDetalle[item-1].splice(fila, 1);
  listarDet(item);
  $("#ndet"+item).html(itemDetalle[item-1].length);
 }
function listarDetPlantilla(item,fila){
  $('#codGrupo').val(item);
  $("#cuenta_detalle").val(itemDetalle[item-1][fila].codigo_cuenta+"@"+itemDetalle[item-1][fila].cuenta);
  $("#tipo_dato").val(2);
  $('.selectpicker').selectpicker("refresh");

  mostrarInputMonto('monto_ibnorca2');
}
function limpiarMontos(){
    if($("#tipo_dato").val()==2){
         //$("#monto_ibnorca").val("0");
         //$("#monto_f_ibnorca").val("0");
         var fila=$("#codGrupo").val();
         var tipoGrupo=$("#tipo_costo"+fila).val();
         $("#montos_editables").removeClass("d-none");
         if(tipoGrupo==2){
          if($("#columna_edit_alumno").hasClass("d-none")){
            $("#columna_edit_alumno").removeClass("d-none");
          }
         }else{
          if(!($("#columna_edit_alumno").hasClass("d-none"))){
            $("#columna_edit_alumno").addClass("d-none");
          }
         } 

       if(!($("#boton_guardardetalle").hasClass("d-none"))){
         $("#boton_guardardetalle").addClass("d-none");
        }    
    }else{
        $("#montos_editables").addClass("d-none");
        if(($("#boton_guardardetalle").hasClass("d-none"))){
         $("#boton_guardardetalle").removeClass("d-none");
        }
      //calcularMontos();
    }
}
function limpiarMontosServicios(){
    if($("#tipo_dato").val()==2){
         var fila=$("#codGrupo").val();
         var tipoGrupo=$("#tipo_costo"+fila).val();
         $("#montos_editables").removeClass("d-none");
         if(tipoGrupo==2){
          if($("#columna_edit_alumno").hasClass("d-none")){
            $("#columna_edit_alumno").removeClass("d-none");
          }
         }else{
          if(!($("#columna_edit_alumno").hasClass("d-none"))){
            $("#columna_edit_alumno").addClass("d-none");
          }
         } 

       if(!($("#boton_guardardetalle").hasClass("d-none"))){
         $("#boton_guardardetalle").addClass("d-none");
        }    
    }else{
        $("#montos_editables").addClass("d-none");
        if(($("#boton_guardardetalle").hasClass("d-none"))){
         $("#boton_guardardetalle").removeClass("d-none");
        }
    }
}
function iniciarCargaAjax(){
  $(".cargar-ajax").removeClass("d-none");
}
function detectarCargaAjax(){
  $(".cargar-ajax").addClass("d-none");
  $(".cargar-ajax").fadeOut("slow");
}
function calcularMontos(){
  var str_cuenta=dividirCadena($('#cuenta_detalle').val(),"@");
  var idp=str_cuenta[0];
  var unidad=$("#cod_unidad").val();
  var area=$("#cod_area").val();

  var valor2=$("#cod_mescurso").val();
  var valor=$("#codValor").val();
  var parametros={"idp":idp,"unidad":unidad,"area":area,"valor":valor,"valor2":valor2};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "ajaxPartidaPresupuestaria.php",
        data: parametros,
        beforeSend: function () { 
          iniciarCargaAjax();
         $("#mensajeDetalle").html("<center><p class='text-muted'></p></center>"); 
        },
        success:  function (resp) {
           detectarCargaAjax();
          //if($("#tipo_dato").val()==1){
            $("#monto_ibnorca").val(redondeo(parseFloat(resp)*$("#cod_mescurso").val()));
            $("#monto_f_ibnorca").val(redondeo(parseFloat(resp)));
            $("#monto_alumno").val(redondeo(parseFloat(resp)/$("#alumnos_ibnorca").val()));

            $("#monto_calculado").val(parseFloat(resp));
            
            $("#monto_ibnorca_edit").val(redondeo(parseFloat(resp)*$("#cod_mescurso").val()));
            $("#monto_f_ibnorca_edit").val(redondeo(parseFloat(resp)));
            $("#monto_alumno_edit").val(redondeo(parseFloat(resp)/$("#alumnos_ibnorca").val()));

         /* }else{
            $("#monto_ibnorca").val("0");
            $("#monto_f_ibnorca").val("0");
          }*/
           var momentoActual = new Date()
                var hora = momentoActual.getHours();
                var minuto = momentoActual.getMinutes();
                var segundo = momentoActual.getSeconds();
                var horaImprimible = hora + ":" + minuto + ":" + segundo;
          
          $("#mensajeDetalle").html("<center><p class='text-info'>Cálculo realizado Hoy "+horaImprimible+"</p></center>");
        }
    });
}
function agregarPersonalPlantillaDetalle(plantilla){
  
}
function listarDetallesPartidaCuenta(tipo){
   var codPartida=$("#cuenta_detalle").val().split('@'); 
   var partida = codPartida[0];
   var plantilla =$("#cod_plantilla").val();
   var cursos=$("#cod_mescurso").val();
   var alumnos=$("#alumnos_ibnorca").val();
   if(partida==""){
      // $("#modalDetallesPartida").modal("hide");
       Swal.fire('Informativo!','Debe seleccionar una partida','warning'); 
   }else{
       var parametros={"cod_partida":partida,"cod_plantillacosto":plantilla,"tipo_calculomonto":tipo,"cursos":cursos,"alumnos":alumnos};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "ajaxPlantillasDetalle.php",
        data: parametros,
        beforeSend: function (){
            iniciarCargaAjax();
        },
        success:  function (resp) {
           detectarCargaAjax();
           $("#lista_detallespartidacuenta").html(resp);
           $('.selectpicker').selectpicker("refresh");
           $("#modalDetallesPartida").modal("show");
           $("#modalDet").modal("hide");
        },
        error: function (xhr, ajaxOptions, thrownError) {
        Swal.fire("Error de proceso!", "Contactese con el administrador", "error");
       }
    });
   }
} 
function cambiarModalDetalle(){
$("#modalDetallesPartida").modal("hide");
$("#modalDet").modal("show");
}
function cambiarModalDetalleVariable(){
$("#modalSimulacionCuentasPersonal").modal("hide");
$("#modalSimulacionCuentas").modal("show");
}
function calcularMontoRegistrado(monto,valor){
  switch (valor){
    case "1":
    var monto_r= monto/$("#cod_mescurso").val();
    break;
    case "2":
    var monto_r= monto;
    break;
    case "3":
    var monto_r= monto*$("#alumnos_ibnorca").val();
    break;
    default:
    monto_r=0;
    break;
  }
  return monto_r;
}
function agregarPlantillaDetalle(partida){
  //datos generales
  var plantilla=$("#cod_plantilla").val();
  //datos complementarios
  var detalle=$("#glosa_plantilladetalle").val();
  var monto=$("#monto_plantilladetalle").val();
  if($("#monto_plantilladetalleext").length){
   var montoe= $("#monto_plantilladetalleext").val();
  }else{
    var montoe=9999;
  }
  var cuenta=$("#cuenta_plantilladetalle").val();
  if(detalle==""||monto==""||monto==0||!(cuenta>0)||montoe==""||montoe==0){
    Swal.fire('Informativo!','Todos los campos son requeridos!','warning'); 
  }else{
   var n_monto=calcularMontoRegistrado(parseFloat(monto),$("#tipo_calculomonto").val());
   var n_montoe=calcularMontoRegistrado(parseFloat(montoe),$("#tipo_calculomonto").val());
     var parametros={"detalle":detalle,"monto":n_monto,"montoe":n_montoe,"cuenta":cuenta,"cod_plantillacosto":plantilla,"cod_partida":partida,"tipo":$("#tipo_calculomonto").val(),"monto_al":monto,"monto_ale":montoe};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "ajaxRegistrarPlantillasDetalle.php",
        data: parametros,
        beforeSend: function (){
        $("#boton_plantilladetalle").addClass("d-none");
        },
        success:  function (resp) {
          $("#boton_plantilladetalle").removeClass("d-none");
          // Swal.fire('Correcto!','Registro exitoso!','success');
           listarDetallesPartidaCuenta(parseInt($("#tipo_calculomonto").val()));
        },
        error: function (xhr, ajaxOptions, thrownError) {
        Swal.fire("Error de proceso!", "Contactese con el administrador", "error");
       }
    });
  }
}
function mostrarInputMonto(id){
  /*if($("#"+id).hasClass("d-none")){
     $("#"+id).removeClass("d-none");*/

     $("#titulo_partidadetalle").text($('#cuenta_detalle option:selected').text());
     if(!($("#boton_guardardetalle").hasClass("d-none"))){
         $("#boton_guardardetalle").addClass("d-none");
     }
  switch (id){
    case 'monto_ibnorca1':
    listarDetallesPartidaCuenta(1);
     /*if(!($("#monto_ibnorca2").hasClass("d-none"))){
       $("#monto_ibnorca2").addClass("d-none");   
     }
     if(!($("#monto_ibnorca3").hasClass("d-none"))){
       $("#monto_ibnorca3").addClass("d-none");   
     }*/
    break;
    case 'monto_ibnorca2':
    $("#titulo_partidadetalle").text($('#cuenta_detalle option:selected').text());  
     listarDetallesPartidaCuenta(2);
     
     /*if(!($("#monto_ibnorca1").hasClass("d-none"))){
       $("#monto_ibnorca1").addClass("d-none");   
     }
     if(!($("#monto_ibnorca3").hasClass("d-none"))){
       $("#monto_ibnorca3").addClass("d-none");   
     }*/
    break;
    case 'monto_ibnorca3':
    listarDetallesPartidaCuenta(3);
     /*if(!($("#monto_ibnorca2").hasClass("d-none"))){
       $("#monto_ibnorca2").addClass("d-none");   
     }
     if(!($("#monto_ibnorca1").hasClass("d-none"))){
       $("#monto_ibnorca1").addClass("d-none");   
     }*/
    break;
  }
  /*}else{
     $("#"+id).addClass("d-none");
  }*/

}

//PARTIDAS PRESUPUESTARIAS FUNCION DE BUSCAR CUENTA//
 var cuentas_tabla=[]; 
 var cuentas_tabla_general=[]; 

function sendCheked(id,nombres,numeros){
  var check=document.getElementById("cuentas"+id);
    check.onchange = function() {
     if(this.checked) {
      cuentas_tabla.push({codigo:id,nombre:nombres,numero:numeros});
      numFilas++;
     }else{
      for (var i = 0; i < cuentas_tabla.length; i++) {
        if(cuentas_tabla[i].codigo==id){
            cuentas_tabla.splice(i, 1);
            break;
        }      
      };
      numFilas--;
     }
     $("#boton_registradas").html("Cuentas Registradas <span class='badge bg-white text-warning'>"+numFilas+"</span>");
   }
} 

function filaTabla(tabla){
  var html="";
  for (var i = 0; i < cuentas_tabla.length; i++) {
    html+="<tr><td>"+(i+1)+"</td><td>"+cuentas_tabla[i].nombre+"</td><td>"+cuentas_tabla[i].numero+"</td></tr>";
  };
  tabla.html(html);
  $("#modalCuentas").modal("show");
}

function filaTablaGeneral(tabla,index){
  var html="";
  for (var i = 0; i < cuentas_tabla_general[index-1].length; i++) {
    //alert(cuentas_tabla_general[index-1][i].nombre);
    html+="<tr><td>"+(i+1)+"</td><td>"+cuentas_tabla_general[index-1][i].nombre+"</td><td>"+cuentas_tabla_general[index-1][i].numero+"</td></tr>";
  };
  tabla.html(html);
  $("#modalCuentas").modal("show");
}  

function ajaxCodigoActivo(combo){
  var contenedor;
  var codigo=combo.value;
  contenedor = document.getElementById('divCodigoAF');
  ajax=nuevoAjax();
  ajax.open('GET', 'activosFijos/ajaxCodigoActivoFijo.php?codigo='+codigo,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);
      ajaxDepreciacion(codigo);
    }
  }
  ajax.send(null)  

}
function ajaxDepreciacion(codigo){
  var contenedor;
  contenedor = document.getElementById('div_contenedor_valorR');
  ajax=nuevoAjax();
  ajax.open('GET', 'activosFijos/AFDepreciacionVidaUtilAjax.php?codigo='+codigo,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);
      ajaxTipoBien(codigo);
    }
  }
  ajax.send(null)
}
function ajaxTipoBien(codigo){
  var contenedor;
  contenedor = document.getElementById('cod_tiposbienes_containers');
  ajax=nuevoAjax();
  ajax.open('GET', 'activosFijos/ajaxTipoBien_AF.php?codigo='+codigo,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);
    }
  }
  ajax.send(null)
}

// function ajaxAFunidadorganizacional(combo){
//   var contenedor;
//   var codigo_ub=combo.value;
//   contenedor = document.getElementById('div_contenedor_UO');
//   ajax=nuevoAjax();
//   ajax.open('GET', 'activosFijos/ubicacionesUnidadAjax.php?codigo_UO='+codigo_ub,true);
//   ajax.onreadystatechange=function() {
//     if (ajax.readyState==4) {
//       contenedor.innerHTML = ajax.responseText;
//       $('.selectpicker').selectpicker(["refresh"]);
      
//       ajaxPersonalUbicacion();
//     }
//   }
//   ajax.send(null)  
// }

function ajaxRPTAF_oficina(){
  var contenedor;
  // var codigo_UO=combo.value;
  contenedor = document.getElementById('contenedor_areas_reporte');
  // var codigo = document.getElementById('unidad_organizacional');
  var codigo = $("#unidad_organizacional").val() || [];
  // alert(codigo);
  ajax=nuevoAjax();
  ajax.open('GET', 'activosFijos/ajax_rtpAF_uo.php?codigo='+codigo,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);         
    }
  }
  ajax.send(null)  
}//unidad_area-cargo

function ajaxAFunidadorganizacionalArea(combo){
  var contenedor;
  var codigo_UO=combo.value;
  contenedor = document.getElementById('div_contenedor_area');
  ajax=nuevoAjax();
  ajax.open('GET', 'activosFijos/ubicacionesUnidadAjax.php?codigo_UO='+codigo_UO,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);   
      ajaxPersonalUbicacion(codigo_UO);       
    }
  }
  ajax.send(null)  
}//unidad_area-cargo

function ajaxAFunidadorganizacionalArea_solicitud_Fact(combo){
  var contenedor;
  var codigo_UO=combo.value;
  contenedor = document.getElementById('div_contenedor_area');
  ajax=nuevoAjax();
  ajax.open('GET', 'simulaciones_servicios/ajax_uo_areas.php?codigo_UO='+codigo_UO,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);      
    }
  }
  ajax.send(null)  
}//unidad_area-cargo
function ajaxPersonalUbicacionTrasfer(combo){
  var contenedor;
  var codigo_UO=combo.value;
  contenedor = document.getElementById('div_personal_UO');
  ajax=nuevoAjax();
  ajax.open('GET', 'activosFijos/ubicacionPersonalAjaxTransfer.php?codigo_UO='+codigo_UO,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);
      ajaxAFunidadorganizacionalArea2(codigo_UO);
    }
  }
  ajax.send(null)
}
function ajaxAFunidadorganizacionalArea2(codigo_UO){
  // var contenedor;
  // var codigo_UO=combo.value;
  contenedor = document.getElementById('div_contenedor_area');
  ajax=nuevoAjax();
  ajax.open('GET', 'activosFijos/ubicacionesUnidadAjax.php?codigo_UO='+codigo_UO,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);   
       
    }
  }
  ajax.send(null)  
}//unidad_area-cargo

function ajaxPersonalUbicacion(codigo_UO){
  // var cod_uo=$("#cod_unidadorganizacional").val();  
  // alert(cod_uo);

  var contenedor; 
  contenedor = document.getElementById('div_personal_UO');
  ajax=nuevoAjax();
  ajax.open('GET', 'activosFijos/ubicacionPersonalAjax.php?codigo_UO='+codigo_UO,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);
    }
  }
  ajax.send(null)
  
}

function ajaxCajaCPersonalUO(combo){
  var contenedor;
  var codigo_personal=combo.value;
  contenedor = document.getElementById('div_contenedor_uo');
  ajax=nuevoAjax();
  ajax.open('GET', 'caja_chica/personalUOAjax.php?codigo_personal='+codigo_personal,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);
      ajaxCajaCPersonalArea(codigo_personal);
      
      // ajaxPersonalUbicacion(codigo_personal);
    }
  }
  ajax.send(null)  
}
function ajaxCajaCPersonalArea(codigo_personal){
  var contenedor;
  // var codigo_personal=combo.value;
  contenedor = document.getElementById('div_contenedor_area');
  ajax=nuevoAjax();
  ajax.open('GET', 'caja_chica/personalAreaAjax.php?codigo_personal='+codigo_personal,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);
      
      // ajaxPersonalUbicacion(codigo_personal);
    }
  }
  ajax.send(null)  
}




function agregaform(datos){
  //console.log("datos: "+datos);
  var d=datos.split('-');
  document.getElementById("codigo_af_aceptar1").value=d[0];
  document.getElementById("codigo_af_aceptar2").value=d[1];
}

function rechazarRecepcion(cod_personal,cod_af,observacion){
  $.ajax({
    type:"POST",
    data:"cod_personal="+cod_personal+"&cod_af="+cod_af+"&cod_estadoasignacionaf=3&observacion="+observacion,
    url:"activosFijos/saveAsignacion.php",
    success:function(r){
      if(r==1){
        //$('#tabla1').load('index.php');
        // alertify.success("agregado");
        alerts.showSwal('success-message','index.php?opcion=afEnCustodia');
      }
    }
  });
}
function RecepcionarAF(cod_personal,cod_af){
  $.ajax({
    type:"POST",
    data:"cod_personal="+cod_personal+"&cod_af="+cod_af+"&cod_estadoasignacionaf=2&observacion=''",
    url:"activosFijos/saveAsignacion.php",
    success:function(r){
      if(r==1){
        //$('#tabla1').load('activosFijos/afEnCustodia.php');
        //alertify.success("agregado");
        alerts.showSwal('success-message','index.php?opcion=afEnCustodia');
      }
    }
  });
}
function DevolverAF(cod_personal,cod_af,observacionD){
  $.ajax({
    type:"POST",
    data:"cod_personal="+cod_personal+"&cod_af="+cod_af+"&cod_estadoasignacionaf=5&observacion="+observacionD,
    url:"activosFijos/saveAsignacion.php",
    success:function(r){
      if(r==1){
        alerts.showSwal('success-message','index.php?opcion=afEnCustodia');
      }
    }
  });
}
function DevolverAFAll(cod_personal){
  $.ajax({
    type:"POST",
    data:"cod_personal="+cod_personal,
    url:"activosFijos/saveAsignacionAll.php",
    success:function(r){
      
        alerts.showSwal('success-message','index.php?opcion=afEnCustodia');
    }
  });
}
function rechazarDevolucion(cod_personal,cod_af){
  $.ajax({
    type:"POST",
    data:"cod_personal="+cod_personal+"&cod_af="+cod_af+"&cod_estadoasignacionaf=7&observacion=''",
    url:"activosFijos/saveAsignacion.php",
    success:function(r){
      if(r==1){
        //$('#tabla1').load('index.php');
        // alertify.success("agregado");
        alerts.showSwal('success-message','index.php?opcion=aftransaccion');
      }
    }
  });
}
function AceptarDevolucion(cod_personal,cod_af){
  $.ajax({
    type:"POST",
    data:"cod_personal="+cod_personal+"&cod_af="+cod_af+"&cod_estadoasignacionaf=6&observacion=''",
    url:"activosFijos/saveAsignacion.php",
    success:function(r){
      if(r==1){
        //$('#tabla1').load('activosFijos/afEnCustodia.php');
        //alertify.success("agregado");
        alerts.showSwal('success-message','index.php?opcion=aftransaccion');
      }
    }
  });
}

function agregaformAcc(datos){
  //console.log("datos: "+datos);
  d=datos.split('||');
  
  //document.getElementById("codigo_af_aceptar1").value=d[0];
  $('#idAccE').val(d[0]);
  $('#nombreAccE').val(d[1]);
  $('#estadoAccE').val(d[2]);

}
function agregaformAccB(datos){
  //console.log("datos: "+datos);
  d=datos.split('||');
  document.getElementById("idAccE").value=d[0];
}
function RegistrarAccAF(codigoAF,nombreAcc,estadoAcc){
    $.ajax({
    type:"POST",
    data:"idAccE=0&codigoAF="+codigoAF+"&nombreAcc="+nombreAcc+"&estadoAcc="+estadoAcc,
    url:"activosFijos/saveAccesoriosAF.php",
    success:function(r){
      if(r==1){
        //$('#tabla1').load('index.php');
        // alertify.success("agregado");
        alerts.showSwal('success-message','index.php?opcion=activofijoAccesorios&codigo='+codigoAF);
      }
    }
  });
}
function SaveEditAccAF(idAccE,codigoAF,nombreAcc,estadoAcc){
    $.ajax({
    type:"POST",
    data:"idAccE="+idAccE+"&codigoAF="+codigoAF+"&nombreAcc="+nombreAcc+"&estadoAcc="+estadoAcc,
    url:"activosFijos/saveAccesoriosAF.php",
    success:function(r){
      if(r==1){
        //$('#tabla1').load('index.php');
        // alertify.success("agregado");
        alerts.showSwal('success-message','index.php?opcion=activofijoAccesorios&codigo='+codigoAF);
      }
    }
  });
}
function SaveDeleteAccAF(idAccE,codigoAF){
    $.ajax({
    type:"POST",
    data:"idAccE="+idAccE+"&codigoAF="+codigoAF+"&nombreAcc=''&estadoAcc=6",
    url:"activosFijos/saveAccesoriosAF.php",
    success:function(r){
      if(r==1){
        alerts.showSwal('success-message','index.php?opcion=activofijoAccesorios&codigo='+codigoAF);
      }
    }
  });
}

//funciones para modal eventos AF

function agregaformEve(datos){
  //console.log("datos: "+datos);
  d=datos.split('||');
  
  //document.getElementById("codigo_af_aceptar1").value=d[0];
  $('#idEveE').val(d[0]);
  $('#nombreEveE').val(d[1]);
  $('#personalEveE').val(d[2]);
}
function agregaformEveB(datos){
  //console.log("datos: "+datos);
  d=datos.split('||');
  document.getElementById("idEveE").value=d[0];
}
function RegistrarEveAF(codigoAF,nombreEve,personalEve){
    $.ajax({
    type:"POST",
    data:"idEveE=0&codigoAF="+codigoAF+"&nombreEve="+nombreEve+"&estadoEve=1&personalEve="+personalEve,
    url:"activosFijos/saveEventosAF.php",
    success:function(r){
      if(r==1){
        alerts.showSwal('success-message','index.php?opcion=activofijoEventos&codigo='+codigoAF);
      }
    }
  });
}
function SaveEditEveAF(idEveE,codigoAF,nombreEve,cod_personalE){
    $.ajax({
    type:"POST",
    data:"idEveE="+idEveE+"&codigoAF="+codigoAF+"&nombreEve="+nombreEve+"&estadoEve=1&personalEve="+cod_personalE,
    url:"activosFijos/saveEventosAF.php",
    success:function(r){
      if(r==1){
        //$('#tabla1').load('index.php');
        // alertify.success("agregado");
        alerts.showSwal('success-message','index.php?opcion=activofijoEventos&codigo='+codigoAF);
      }
    }
  });
}

function SaveDeleteEveAF(idEveE,codigoAF){
    $.ajax({
    type:"POST",
    data:"idEveE="+idEveE+"&codigoAF="+codigoAF+"&nombreEve=''&estadoEve=2&personalEve=''",
    url:"activosFijos/saveEventosAF.php",
    success:function(r){
      if(r==1){
        alerts.showSwal('success-message','index.php?opcion=activofijoEventos&codigo='+codigoAF);
      }
    }
  });
}




function filasPresupuesto(id){
  if($(".cuenta"+id).is(":visible")){
    $(".cuenta"+id).hide();
    $(".simbolo"+id).text("add_circle");
  }else{
    $(".cuenta"+id).show();
    $(".simbolo"+id).text("remove_circle");
  }
}
function calcularDatosPlantilla(){

  var mes= $("#mes_plantilla").val();
  var codigo= $("#codigo_plantilla").val();
  var unidad= $("#grupo_unidad").val();
  var area= $("#grupo_area").val();
  contenedor = document.getElementById('datos_pantilla_costo');
  var parametros={"codigo":codigo,"unidad":unidad,"area":area,"mes":mes};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "ajaxCalcularDatosPlantilla.php",
        data: parametros,
        beforeSend: function () { 
         $("#mensaje_process").html("<center><p class='text-muted'>Cálculando espere porfavor...</p></center>"); 
         $("#calcular").attr("disabled","disabled");
        },
        success:  function (resp) {
          contenedor.innerHTML = resp;
          var momentoActual = new Date()
          var hora = momentoActual.getHours();
          var minuto = momentoActual.getMinutes();
          var segundo = momentoActual.getSeconds();
          var horaImprimible = hora + ":" + minuto + ":" + segundo;
          $("#mensaje_process").html("<center><p class='text-info'>Cálculo realizado Hoy "+horaImprimible+"</p></center>");
          $("#calcular").removeAttr("disabled");
          contenedor.bootstrapMaterialDesign();
        }
    });
}

var data_cuentas=null;
function alertDatosTabla(){
  var data = data_cuentas.$('input:checked');
  var datos=[];
  for (var i = 0; i < data.length; i++) {
     datos[i]=data[i].value;
   }; 
  return datos;
}

//funciones simulaciones
function guardarSimulacionCosto(){
  if(!($("#codigo_servicioibnorca").length)){
    var idServicio="";
  }else{
    var idServicio=$("#codigo_servicioibnorca").val();
  }
  var nombre=$("#nombre").val();
  var precio=$("#precio_venta").val();
  var plantilla_costo=$("#plantilla_costo").val();
  var cantidad_modulos=$("#cantidad_modulos").val();
  var monto_norma=$("#monto_norma").val();
  var tipo_curso=$("#tipo_curso").val();
  var fecha_estimada=$("#fecha_estimada").val();
  var cantidad_dias=$("#cantidad_dias").val();
  var normas=$("#normas").val();
  var ibnorca = 1;
  /*if( $("#ibnorca_check").is(':checked') ) {
      var ibnorca=1;
  }else{
      var ibnorca=2;
  }*/
  if(nombre==""||!(plantilla_costo>0)||cantidad_modulos==""||monto_norma==""){
   Swal.fire('Informativo!','Debe llenar los campos!','warning'); 
  }else{
     var parametros={"normas":normas,"fecha_estimada":fecha_estimada,"cantidad_dias":cantidad_dias,"tipo_curso":tipo_curso,"monto_norma":monto_norma,"nombre":nombre,"plantilla_costo":plantilla_costo,"precio":precio,"ibnorca":ibnorca,"cantidad_modulos":cantidad_modulos};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "simulaciones_costos/ajaxRegistrarSimulacion.php",
        data: parametros,
        beforeSend: function () { 
         iniciarCargaAjax();      
        },
        success:  function (resp) {
          detectarCargaAjax();
          if(!($("#codigo_servicioibnorca").length)){
                 alerts.showSwal('success-message','simulaciones_costos/registerSimulacion.php?cod='+resp);
              }else{
                var s=$("#codigo_servicioibnorca_s").val();
                var u=$("#codigo_servicioibnorca_u").val();
                alerts.showSwal('success-message','simulaciones_costos/registerSimulacion.php?cod='+resp+'&q='+idServicio+'&s='+s+'&u='+u);
        }
       //   alerts.showSwal('success-message','simulaciones_costos/registerSimulacion.php?cod='+resp);
        }
    });
  }
}

function guardarSimulacionServicio(){
  if(!($("#codigo_servicioibnorca").length)){
    var idServicio="";
  }else{
    var idServicio=$("#codigo_servicioibnorca").val();
  }  
   
  if(!($("#idPerfil").length>0)){
    var idPerfil=0;
  }else{
    var idPerfil=$("#idPerfil").val();
  } 
  var nombre=$("#nombre").val();
  var dias=$("#dias_auditoria").val();
  var cliente=$("#cliente").val();
  var objeto=$("#objeto_servicio").val();
  //var producto=$("#productos").val();
  //var sitio=$("#sitios").val();
  var norma=$("#norma").val();
  var local_extranjero=$("#local_extranjero").val();
  var utilidad=$("#utilidad_minima").val();
  var anios=$("#anios").val();
  var plantilla_servicio=$("#plantilla_servicio").val();
  var alcance="";
  if($("#afnor").length){
    if($("#afnor").is(':checked')){
      var afnor=1;
    }else{
      var afnor=0;
    }  
  }else{
    var afnor=0;
  }
  if($("#productos_div").hasClass("d-none")){
   if(norma==""||itemAtributos.length==0||dias==""||nombre==""||!(plantilla_servicio>0)){
   Swal.fire('Informativo!','Debe llenar los campos!','warning'); 
  }else{
    var tipoServicio=$("#tipo_servicio").val();
    var normas_tiposervicio=$("#normas_tiposervicio").val();
    var normas_tiposerviciotext=$("#normas_tiposerviciotext").val();
    alcance=$("#alcance").val();
    var des_serv=$("#d_servicio").val();
     var parametros={"des_serv":des_serv,"normas_tiposerviciotext":normas_tiposerviciotext,"normas_tiposervicio":JSON.stringify(normas_tiposervicio),"alcance":alcance,"id_perfil":idPerfil,"objeto_servicio":objeto,"tipo_servicio":tipoServicio,"id_servicio":idServicio,"local_extranjero":local_extranjero,"nombre":nombre,"plantilla_servicio":plantilla_servicio,"dias":dias,"utilidad":utilidad,"cliente":cliente,"atributos":JSON.stringify(itemAtributos),"norma":norma,"anios":anios,"afnor":afnor,"tipo_atributo":2};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "simulaciones_servicios/ajaxRegistrarSimulacion.php",
        data: parametros,
        beforeSend: function () { 
          iniciarCargaAjax();
        },
        success:  function (respuesta) {
          var resp = respuesta.split("####")[1];
          var log = respuesta.split("####")[0];
          detectarCargaAjax();
          if(resp=="ERROR"){
           Swal.fire('Error!','Ocurrio un error!','warning'); 
          }else{
            if (log.trim()==""){
              var param={"codigo":resp};
              $.ajax({
                 type: "GET",
                 dataType: 'html',
                 url: "simulaciones_servicios/ajaxSimulacionExitosa.php",
                 data: param,
                 success:  function (x) {
                  $("#mensaje").html(x);
                 }
              });
              if(!($("#codigo_servicioibnorca").length)){
                 alerts.showSwal('success-message','simulaciones_servicios/registerSimulacion.php?cod='+resp);
              }else{
                var s=$("#codigo_servicioibnorca_s").val();
                var u=$("#codigo_servicioibnorca_u").val();
                alerts.showSwal('success-message','simulaciones_servicios/registerSimulacion.php?cod='+resp+'&q='+idServicio+'&s='+s+'&u='+u);
              }
            }else{
              Swal.fire('Error!','Ocurrio un error de envio, verifique su conexión a Internet!','warning'); 
            }
          }
        }//fin successs
    });
  }
  }else{
    if(norma==""||itemAtributos.length==0||dias==""||nombre==""||!(plantilla_servicio>0)){
   Swal.fire('Informativo!','Debe llenar los campos!','warning'); 
     }else{
      var regionCliente=$("#region_cliente").val();
      var tipoCliente=$("#tipo_cliente").val();

      var iaf_primario=$("#iaf_primario").val();
      var iaf_secundario=$("#iaf_secundario").val();
      objeto=0;
      var des_serv=$("#d_servicio_p").val();
     var parametros={"des_serv":des_serv,"alcance":alcance,"iaf_primario":iaf_primario,"iaf_secundario":iaf_secundario,"tipo_cliente":tipoCliente,"region_cliente":regionCliente,"id_perfil":idPerfil,"objeto_servicio":objeto,"id_servicio":idServicio,"local_extranjero":local_extranjero,"nombre":nombre,"plantilla_servicio":plantilla_servicio,"dias":dias,"utilidad":utilidad,"cliente":cliente,"atributos":JSON.stringify(itemAtributos),"norma":norma,"anios":anios,"afnor":afnor,"tipo_atributo":1};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "simulaciones_servicios/ajaxRegistrarSimulacion.php",
        data: parametros,
        beforeSend: function () { 
         //Swal.fire("Informativo!", "Procesando datos! espere...", "warning");
          iniciarCargaAjax();
        },
        success:  function (respuesta) {
          var resp = respuesta.split("####")[1];
          var log = respuesta.split("####")[0];
          detectarCargaAjax();
          if(resp=="ERROR"){
           Swal.fire('Error!','Ocurrio un error!','warning'); 
          }else{
            if (log.trim()==""){
              var param={"codigo":resp};
              $.ajax({
                 type: "GET",
                 dataType: 'html',
                 url: "simulaciones_servicios/ajaxSimulacionExitosa.php",
                 data: param,
                 success:  function (x) {
                  $("#mensaje").html(x);
                 }
              });
              if(!($("#codigo_servicioibnorca").length)){
               alerts.showSwal('success-message','simulaciones_servicios/registerSimulacion.php?cod='+resp);
              }else{
                if(!($("#codigo_servicioibnorca").length>0)){
                  alerts.showSwal('success-message','simulaciones_servicios/registerSimulacion.php?cod='+resp+'&q='+idServicio);
                }else{
                  alerts.showSwal('success-message','simulaciones_servicios/registerSimulacion.php?cod='+resp+'&q='+idServicio+'&u='+idPerfil);
                }            
              }
            }else{
              Swal.fire('Error!','Ocurrio un error de envio, verifique su conexión a Internet!','warning'); 
            }
          }
        }//fin success
    });
  }
  }
  
}


function cargarPlantillaSimulacion(mes,ibnorca){
  var plantilla_costo=$("#plantilla_costo").val();
  var precio=$("#precio_venta").val();
  if(!(plantilla_costo>0)){
   $("#mensaje").html("<center><p class='text-danger'>Seleccione una plantilla.</p></center>");
  }else{
    if(precio==null){
       $("#mensaje").html("<center><p class='text-danger'>No hay registros de precios.</p></center>");
    }else{
    var alumnos=$("#alumnos_plan").val();
    var alumnosfuera=$("#alumnos_plan_fuera").val();
    if( $("#alumnos_auto").is(':checked') ) {
        alumnos=0;
        alumnosfuera=0;
     }
    contenedor = document.getElementById('div_simulacion');
     var parametros={"plantilla_costo":plantilla_costo,"mes":mes,"precio":precio,"alumnos":alumnos,"alumnos_fuera":alumnosfuera,"ibnorca":ibnorca};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "ajaxCargarSimulacion.php",
        data: parametros,
        beforeSend: function () { 
         $("#mensaje").html("<center><p class='text-warning'>Procesando. Espere...</p></center>");
          
        },
        success:  function (resp) {
          contenedor.innerHTML = resp;
         $("#mensaje").html("<center><p class='text-success'>Proceso satisfactorio!</p></center>");
        }
    });   
    }
  }
}
function presioneBoton(){
  $("#boton_simular").removeClass("d-none");
  $("#check_simular").removeClass("d-none");
  $("#mensaje").html("<center><p class='text-muted'><small>Presione en SIMULAR PLANTILLA</small></p></center>");
}
function guardarServicioSimulacion(valor){
  Swal.fire({
        title: '¿Esta Seguro?',
        text: "La propuesta se enviará para su posterior revisión",
         type: 'warning',
        showCancelButton: true,
        confirmButtonClass: 'btn btn-info',
        cancelButtonClass: 'btn btn-danger',
        confirmButtonText: 'Si',
        cancelButtonText: 'No',
        buttonsStyling: false
       }).then((result) => {
          if (result.value) {
               enviarSimulacionAjaxServ();            
            return(true);
          } else if (result.dismiss === Swal.DismissReason.cancel) {
            return(false);
          }
        });
}

function guardarSimulacion(valor){
  Swal.fire({
        title: '¿Esta Seguro?',
        text: "La propuesta se enviará para su posterior revisión",
         type: 'warning',
        showCancelButton: true,
        confirmButtonClass: 'btn btn-info',
        cancelButtonClass: 'btn btn-danger',
        confirmButtonText: 'Si',
        cancelButtonText: 'No',
        buttonsStyling: false
       }).then((result) => {
          if (result.value) {
               enviarSimulacionAjax();            
            return(true);
          } else if (result.dismiss === Swal.DismissReason.cancel) {
            return(false);
          }
        });
/*
  var nombre=$("#nombre").val();
  var plantilla_costo=$("#plantilla_costo").val();
  var plantilla_costo_actual=$("#cod_plantilla").val();  
  if(nombre==""){
    $('#msgError').html("<p>Debe singresar un nombre de la simulacion</p>");
    $('#modalAlert').modal('show');
  }else{
    if(plantilla_costo!=plantilla_costo_actual&&(plantilla_costo>0)){
      $('#msgError2').html("<p>Nueva Plantilla detectada!</p>");
      $('#modalGuardar').modal('show');
    }else{
      if($("#cantidad_alibnorca").val()!=$("#alumnos_plan").val()||$("#cantidad_alfuera").val()!=$("#alumnos_plan_fuera").val()){
        $('#msgError2').html("<p>Nuevas cantidades de Alumnos detectados!</p>");
        $('#modalGuardar').modal('show');
      }else{
        if($("#precio_venta").length){
         if($("#cod_precioplantilla").val()!=$("#precio_venta").val()){
           $('#msgError2').html("<p>Nuevo previo de venta seleccionado!</p>");
           $('#modalGuardar').modal('show');
         }else{
            if(valor=="enviar"){
            enviarSimulacionAjax();
           }else{
            guardarSimulacionAjax(0); 
           } 
         }         
        }else{
           if(valor=="enviar"){
            enviarSimulacionAjax();
           }else{
            guardarSimulacionAjax(0); 
           } 
        }
      }           
    }
  }*/
}
function guardarSimulacionAjax(valor){
  var codigo=$("#cod_simulacion").val();
  var nombre=$("#nombre").val();
  if(valor==0){
   var plantilla_costo=$("#cod_plantilla").val();
   var cantidadIbnorca=$("#alumnos_plan").val();
   var cantidadFuera=$("#alumnos_plan_fuera").val();
   var precio=$("#cod_precioplantilla").val();
  }else{
   var plantilla_costo=$("#plantilla_costo").val(); 
   var cantidadIbnorca=$("#cantidad_alibnorca").val();
   var cantidadFuera=$("#cantidad_alfuera").val();
   var precio=$("#precio_venta").val(); 
  }

  var parametros={"cod_plantilla":plantilla_costo,"nombre":nombre,"codigo":codigo,"precio":precio,"alibnorca":cantidadIbnorca,"alfuera":cantidadFuera};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "ajaxSaveSimulacion.php",
        data: parametros,
        beforeSend: function () { 
         $("#logo_carga").show();        
        },
        success:  function (resp) {
         $("#logo_carga").hide();
         $('#msgError3').html("<p class='text-success font-weight-bold'>"+resp+"Se guardo la Simulacion</p>");
         $('#modalGuardarSend').modal('show');
        }
    });
}
function enviarSimulacionAjax(){
  var codigo=$("#cod_simulacion").val();
  var aprobado=$("#aprobado").val();
  if(!($("#idPerfil").length>0)){
      var id_perfil=0;
  }else{
    var id_perfil=$("#idPerfil").val();
  }
  var parametros={"codigo":codigo,"aprobado":aprobado};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "ajaxSendSimulacion.php",
        data: parametros,
        beforeSend: function () {
         $("#logo_carga").show();        
        },
        success:  function (resp) {
         $("#logo_carga").hide();
         Swal.fire("Envío Exitoso!", "Se registradon los datos exitosamente!", "success")
             .then((value) => {
              if(!($("#id_servicioibnored").length>0)){
               location.href="../index.php?opcion=listSimulacionesCostos";
              }else{
                var q=$("#id_servicioibnored").val();
                if(!($("#idPerfil").length>0)){
                    //location.href="../index.php?opcion=listSimulacionesServ&q="+q;
                }else{
                    //location.href="../index.php?opcion=listSimulacionesServ&q="+q+"&u="+id_perfil;
                }
                
              }
         });
         /*$('#msgError4').html("<p class='text-dark font-weight-bold'>"+resp+"Se envio la Simulacion</p>");
         $('#modalSend').modal('show');*/
        }
    });
}

function enviarSimulacionAjaxServ(){
  var codigo=$("#cod_simulacion").val();
  var aprobado=$("#aprobado").val();
  if(!($("#idPerfil").length>0)){
      var id_perfil=0;
  }else{
    var id_perfil=$("#idPerfil").val();
  }

  var parametros={"codigo":codigo,"aprobado":aprobado,"id_perfil":id_perfil};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "ajaxSendSimulacion.php",
        data: parametros,
        beforeSend: function () {
         $("#logo_carga").show();        
        },
        success:  function (resp) {
         $("#logo_carga").hide();
         Swal.fire("Envío Exitoso!", "Se registradon los datos exitosamente!", "success")
             .then((value) => {
              if(!($("#id_servicioibnored").length>0)){
               location.href="../index.php?opcion=listSimulacionesServ";
              }else{
                var q=$("#id_servicioibnored").val();
                if(!($("#idPerfil").length>0)){
                    //location.href="../index.php?opcion=listSimulacionesServ&q="+q;
                }else{
                    //location.href="../index.php?opcion=listSimulacionesServ&q="+q+"&u="+id_perfil;
                }
                
              }
             
         });
        }
    });
}

/*Ultima actualizacion 28-noviembre */
////////////////////////////////variables para titulos en reporte diario y mayor PDF //////////////////////////////////
var unidad_reporte_diario=""; var fecha_reporte_diario=""; var tipo_reporte_diario="";
var periodo_mayor=""; var cuenta_mayor=""; var unidad_mayor="";

function guardarValoresMoneda(){
 var index=$("#numeroMoneda").val();
 for (var i = 0; i < index; i++) {
  var codigo=$("#codigo"+(i+1)).val();
  var valor=$("#valor"+(i+1)).val();
   ajax=nuevoAjax();
   ajax.open('GET', 'tipos_cambios/ajaxSaveTipo.php?codigo='+codigo+'&valor='+valor,true);
   ajax.onreadystatechange=function() {
     if (ajax.readyState==4) {
      window.location.href="index.php?opcion=tipoDeCambio";
     }
   }
   ajax.send(null)
  };
}
function editarCuentaComprobante(fila){
  filaActiva=fila;
  if($("#cuenta"+fila).val()!=""){
    var codigo =$("#cuenta"+fila).val();
    for (var i = 0; i < itemCuentas.length; i++) {
      if(itemCuentas[i].codigo==codigo){
        $("#nro_cuenta").val(itemCuentas[i].numero);
        $("#cuenta").val(itemCuentas[i].nombre);
        $("#cuenta_auxiliar_modal").val("");
        buscarCuentaList('numero');
        break;
      }
    };   
  }
  $('#myModal').modal('show');
}
//retenciones funciones
function borrarRetencionDetalle(cod){
  var contenedor=$('#tabla_detalle_retencion');
  var parametros={"codigo":cod};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "ajaxDeleteDetalle.php",
        data: parametros,
        beforeSend:function(){
          iniciarCargaAjax();
        },
        success:  function (resp) {
           detectarCargaAjax();
         contenedor.html(resp);
          Swal.fire('Correcto!','La transaccion tuvo exito!','success'); 
          /*$("#msgError").html("<p class='text-success'><small>Se eliminó el registro exitosamente!</small></p>");
         $('#modalAlert').modal('show');*/
        }
    });
}
function editarRetencionNombre(){
  var nombre=$("#nombre_retencion").val();
  var porcentaje=$("#cuenta_origen").val();
  if(nombre==""){
    $("#msgError").html("<p class='text-danger'><small>No se puede poner un nombre vacío a la retención</small></p>");
    $('#modalAlert').modal('show'); 
  }else{
    var codigo=$("#codigo").val();
    ajax=nuevoAjax();
    ajax.open('GET', 'ajaxUpdateRetencion.php?codigo='+codigo+'&nombre='+nombre+'&cuenta_origen='+porcentaje,true);
    ajax.onreadystatechange=function() {
      if (ajax.readyState==4) {
        window.location.href="../index.php?opcion=configuracionDeRetenciones";
      }
    }
    ajax.send(null)
  }
}
//retenciones en el comprobante
function quitarChecked(id){
  var filaCheck=0;
  $("input[name="+id+"]").each(function (index) { 
      if(filaCheck!=0){
         if($(this).is(':checked')){
          $(this).prop('checked', false);
         }   
      }else{
        $(this).prop('checked', true);
      }     
     filaCheck++;  
    });
}
function ponerChecked(id,val){
  var filaCheck=0;
  $("input[name="+id+"]").each(function (index) { 
    var respuesta=$(this).val().split('@');
      if(respuesta[0]!=val){
         if($(this).is(':checked')){
          $(this).prop('checked', false);
         }   
      }else{
        $(this).prop('checked', true);
      }   
     filaCheck++;  
    });
}
function listRetencion(id){
  var fila=id;
  var codigo=$("#cuenta"+fila).val();
  if($("#debe"+fila).length){
   var importe=$("#debe"+fila).val(); 
 }else{
   var importe=$("#importe"+fila).val();
 }
  
   $("#retencion_cuenta").html($("#divCuentaDetalle"+fila).html());
   $("#retencion_codcuenta").val(codigo);
   $("#retFila").val(id);
   $("#retencion_montoimporte").val(importe);
   if($("#cod_retencion"+fila).length){
      if($("#cod_retencion"+fila).val()==0){
        quitarChecked("retenciones");
      }else{
        ponerChecked("retenciones",$("#cod_retencion"+fila).val());
      }
   }else{
    quitarChecked("retenciones");
   }
   
   abrirModal('modalRetencion');
   $("#mensaje_retencion").html("<p class='text-info'>Seleccione una retención</p>");
 }
//inicializar el puntero el el primer input modal Buscar Cuenta...
$(document).on("shown.bs.modal","#modalRetencion",function(){
  document.getElementById('retencion_montoimporte').focus();
});

 function agregarRetencion(){
  var listaRet=[];var i=0;
  $("input[name=retenciones]").each(function (index) {  
       if($(this).is(':checked')){
          listaRet[i]= $(this).val();
          i++;
       }
    });
  if(listaRet.length>0){
    if($("#retencion_montoimporte").val()==""){
       $("#mensaje_retencion").html("<p class='text-danger'>Debe ingresar un valor en el IMPORTE</p>");
    }else{
      agregarRetencionCuenta(listaRet); 
    }  
  }else{
    $("#mensaje_retencion").html("<p class='text-danger'>Debe seleccionar al menos una retención</p>");
  }
  
 }

 function agregarRetencionCuenta(listaRet){

  var fila = $("#retFila").val();
  var cuenta = $("#cuenta"+fila).val();
  var valor=1;
  var debe=$("#retencion_montoimporte").val();
  if(cuenta==""){
    valor=0;
  }
  if(fila==""){valor=0;}
  if(valor!=0){
  ajax=nuevoAjax();
  ajax.open("GET","ajaxRetencionCuenta.php?fila_actual="+fila+"&debe="+debe+"&filas="+cantidadItems+"&cuenta="+cuenta+"&listRet="+JSON.stringify(listaRet),true);
  ajax.onreadystatechange=function(){
  if (ajax.readyState==4) {
    var fi=$("#fiel");
    fi.append(ajax.responseText);
    $('.selectpicker').selectpicker(["refresh"]);
   }
  }
  ajax.send(null); 
  $("#retFila").val("");
  $('#modalRetencion').modal('hide');
  }  
 }
 function calcularImporteDespuesRetencion(valor,fila){
  //$("#debe"+fila).val(valor);
  $("#debe"+fila).val(valor);
  $("#haber"+fila).val("0");
  $("#debe"+fila).focus();
 }

 function autocompletar(inp,inp_val,arr){
   var input = document.getElementById(inp);
       var input_value = document.getElementById(inp_val);
         new Awesomplete(input, {
          minChars: 1,
          maxItems: 10,
          autoFirst:true,
          list: arr,
          tabSelect:true,
          replace: function(suggestion) {
              input_value.value = suggestion.value;
              this.input.value = suggestion.label;
           }
          });
 }
 //funciones plantillas_costos 

 function agregarPrecioPlantilla(codigo){
  var precioLocal=$("#precio_venta_ibnorca").val();
  //var precioExterno=$("#precio_venta_fuera").val();
  if(precioLocal==""){
   Swal.fire('Informativo!','Debe llenar los campos!','warning');  
  }else{
    ajax=nuevoAjax();
    ajax.open("GET","ajaxRegistrarPrecio.php?local="+precioLocal+"&codigo="+codigo,true);
    ajax.onreadystatechange=function(){
    if (ajax.readyState==4) {
      if($("#contenido_precio").length){
        var fi = $("#contenido_precio");
      fi.html(ajax.responseText);
      fi.bootstrapMaterialDesign();
      }else{
         mostrarPreciosPlantilla();
         Swal.fire('Correcto!','La transaccion tuvo exito!','success'); 
      }
      
      $("#precio_venta_ibnorca").val("");
     // $("#precio_venta_fuera").val("");
    }
   }
    ajax.send(null);
  }
 }
 function removePlantillaDetalle(cod){
  ajax=nuevoAjax();
    ajax.open("GET","ajaxDeletePlantillasDetalle.php?cod="+cod,true);
    ajax.onreadystatechange=function(){
    if (ajax.readyState==4) {
      listarDetallesPartidaCuenta(parseInt($("#tipo_calculomonto").val()));
         //Swal.fire('Borrado!','Se borraron los datos exitosamente!','success');
    }
   }
    ajax.send(null);
 }
 function mostrarEditPlantillaDetalle(cod,monto,montoExt,glosa){
  $("#codigo_plandet").val(cod);
  $("#monto_plandet").val(redondeo(monto));
  if(montoExt!="NONE"){
    $("#monto_plandetExt").val(redondeo(montoExt));
  }
  $("#glosa_plandet").val(glosa);
  $("#modalEditPlan").modal("show");
 }
 function editPlantillaDetalle(){
  var cod=$("#codigo_plandet").val();
  var glosa=$("#glosa_plandet").val();
  var monto=$("#monto_plandet").val();
  if($("#monto_plandetExt").length){
    var montoExt=$("#monto_plandetExt").val();
  }else{
    var montoExt=9999;
  }
  
  if(glosa==""||monto==""||montoExt==""){
    Swal.fire('Informativo!','Todos los campos son requeridos!','warning');
  }else{
   var n_monto=calcularMontoRegistrado(parseFloat(monto),$("#tipo_calculomonto").val());
   var n_montoExt=calcularMontoRegistrado(parseFloat(montoExt),$("#tipo_calculomonto").val());
  ajax=nuevoAjax();
    ajax.open("GET","ajaxEditPlantillasDetalle.php?cod="+cod+"&m="+monto+"&me="+montoExt+"&g="+glosa+"&nm="+n_monto+"&nme="+n_montoExt,true);
    ajax.onreadystatechange=function(){
    if (ajax.readyState==4) {
      $("#modalEditPlan").modal("hide");
      listarDetallesPartidaCuenta(parseInt($("#tipo_calculomonto").val()));
         //Swal.fire('Borrado!','Se borraron los datos exitosamente!','success');
    }
   }
    ajax.send(null);   
  }
 }
 function removePrecioPlantilla(cod,codigo){
   ajax=nuevoAjax();
    ajax.open("GET","ajaxDeletePrecio.php?cod="+cod+"&codigo="+codigo,true);
    ajax.onreadystatechange=function(){
    if (ajax.readyState==4) {
      if($("#contenido_precio").length){
      var fi=$("#contenido_precio");
      fi.html(ajax.responseText);
      fi.bootstrapMaterialDesign();
      }else{
        mostrarPreciosPlantilla();
         Swal.fire('Borrado!','Se borraron los datos exitosamente!','success');
      }
      
      $("#precio_venta_ibnorca").val("");
      //$("#precio_venta_fuera").val("");
    }
   }
    ajax.send(null);
 }

 function listarPreciosPlantilla(codigo,label,ibnorca){
  var url="";
  if(label=="sin"){
   url="ListComboPrecio.php";
  }else{
   url="../plantillas_costos/ajaxListaComboPrecio.php";
  }
  ajax=nuevoAjax();
    ajax.open("GET",url+"?codigo="+codigo+"&ibnorca="+ibnorca,true);
    ajax.onreadystatechange=function(){
    if (ajax.readyState==4) {
      var fi=$("#lista_precios");
      fi.html(ajax.responseText);
      fi.bootstrapMaterialDesign();
       $('.selectpicker').selectpicker("refresh");
    }
   }
    ajax.send(null);
 }
function listarPreciosPlantillaSim(codigo,label,ibnorca){
  var url="";
  if(label=="sin"){
   url="simulaciones_costos/listComboPrecio.php";
  }else{
   url="plantillas_costos/ajaxListaComboPrecio.php";
  }
  ajax=nuevoAjax();
    ajax.open("GET",url+"?codigo="+codigo+"&ibnorca="+ibnorca,true);
    ajax.onreadystatechange=function(){
    if (ajax.readyState==4) {
      var fi=$("#lista_precios");
      fi.html(ajax.responseText);
      fi.bootstrapMaterialDesign();
       $('.selectpicker').selectpicker("refresh");
    }
   }
    ajax.send(null);
 }

 function listarDatosPlantillaSim(codigo){
  var url="simulaciones_servicios/listPreciosDias.php";
  ajax=nuevoAjax();
    ajax.open("GET",url+"?codigo="+codigo,true);
    ajax.onreadystatechange=function(){
    if (ajax.readyState==4) {
      var fi=$("#lista_precios");
      fi.html(ajax.responseText);
      fi.bootstrapMaterialDesign();
      $(".tagsinput").tagsinput();
       $('.selectpicker').selectpicker("refresh");
    }
   }
    ajax.send(null);
 }
 //////////////////////reporte mayores desde comprobante////////////
function mayorReporteComprobante(fila){
 if($("#cuenta"+fila).val()==""){
   $("#msgError").html("<p>Ingrese una cuenta</p>");
   $('#modalAlert').modal('show');
 }else{
  var cuenta=$("#cuenta"+fila).val();
    var parametros={
      "moneda":1,
      "fecha_desde":null,
      "fecha_hasta":null,
      "glosa_len":1,
      "unidad_costo":null,
      "area_costo":null,
      "cuenta_especifica":cuenta,
      "cuenta":null,
      "unidad":null,
      "gestion":null,
      "entidad":null
    };
     $.ajax({
        type: "POST",
        dataType: 'html',
        url: "../reportes/reporteMayor.php",
        data: parametros,
        success:  function (resp) {
         var newWindow = window.open("");
         newWindow.document.write(resp);
        }
    });
 }
}
 ///////////////////////////////////////////////////////////////////
 /*                              Solicitud de recursos                                      */

 function listarTipoSolicitud(tipo,id){
  var url="";
  if($("#ibnorca_q").length>0){
   var q=$("#ibnorca_q").val();
   var s=$("#ibnorca_s").val();
   var u=$("#ibnorca_u").val();
   var v=$("#ibnorca_v").val(); 
    if(tipo==1||tipo==-1){
      if(tipo==1){
        //para propuestas
        url="ajaxListSimulacion.php?sim=sec&q="+q+"&s="+s+"&u="+u+"&v="+v;
      }else{
        //para propuestas
        url="ajaxListSimulacion.php?sim=tcp&q="+q+"&s="+s+"&u="+u+"&v="+v;
      } 
    }else{
      if(tipo==2){
        //para proveedor
        url="ajaxListProveedor.php?q="+q+"&s="+s+"&u="+u+"&v="+v;
      }else{
        //propuestas Manuales
        url="ajaxListManual.php?q="+q+"&s="+s+"&u="+u+"&v="+v;
      }
     
    }
  }else{
    if(tipo==1||tipo==-1){
      if(tipo==1){
        //para propuestas
        url="ajaxListSimulacion.php?sim=sec";
      }else{
        //para propuestas
        url="ajaxListSimulacion.php?sim=tcp";
      }
    }else{
      if(tipo==2){
        //para proveedor
        url="ajaxListProveedor.php";
      }else{
        //propuestas Manuales
        url="ajaxListManual.php";
      }
     
    }
  }
  
  if($("#cantidad_filas").val()>0){
     Swal.fire({
        title: '¿Cambiar el Tipo?',
        text: "Se detectaron registros en el detalle ¿desea continuar?",
         type: 'warning',
        showCancelButton: true,
        confirmButtonClass: 'btn btn-warning',
        cancelButtonClass: 'btn btn-danger',
        confirmButtonText: 'Si',
        cancelButtonText: 'No',
        buttonsStyling: false
       }).then((result) => {
          if (result.value) {
            cargarDatosSelectTipoSolicitud(url,tipo,id);            
            return(true);
          } else if (result.dismiss === Swal.DismissReason.cancel) {
            return(false);
          }
      });
  }else{
     cargarDatosSelectTipoSolicitud(url,tipo,id)   
  }
 }
function cargarDatosSelectTipoSolicitud(url,tipo,id){
  $("#fiel").html("");
  //if(tipo!=3){ 
   ajax=nuevoAjax();
    ajax.open("GET",url,true);
    ajax.onreadystatechange=function(){
    if (ajax.readyState==4) {
      var fi=$("#lista_tipo");
      fi.html(ajax.responseText);
      fi.bootstrapMaterialDesign();
      if(tipo==2||tipo==3){
        if(!($("#filtros_solicitud").hasClass("d-none"))){
          $("#filtros_solicitud").addClass("d-none")
        }
        if(!($("#buscar_solicitudesdetalle").hasClass("d-none"))){
            $("#buscar_solicitudesdetalle").addClass("d-none");
        }
        if(tipo==2){
         if($("#listaPro").hasClass("d-none")){
            $("#listaPro").removeClass("d-none");
          }
        }else{
          if(!($("#listaPro").hasClass("d-none"))){
            $("#listaPro").addClass("d-none");
          }
        }      
      }else{
        if(!($("#listaPro").hasClass("d-none"))){
            $("#listaPro").addClass("d-none");
          }
       if(($("#filtros_solicitud").hasClass("d-none"))){
         $("#filtros_solicitud").removeClass("d-none")
       } 
       if($("#buscar_solicitudesdetalle").hasClass("d-none")){
         $("#buscar_solicitudesdetalle").removeClass("d-none");
        }   
      }

      if(id!="none"){
        quitarPropuestaCombo(id);
        filtrarSolicitudRecursosDetalleDatos();
      }
       $('.selectpicker').selectpicker("refresh");
    }
   }
    ajax.send(null);
 /* }else{
    if(!($("#filtros_solicitud").hasClass("d-none"))){
       $("#filtros_solicitud").addClass("d-none")
    }
    $("#fiel").html("");
    $("#cantidad_filas").val(0);
    if(!($("#buscar_solicitudesdetalle").hasClass("d-none"))){
      $("#buscar_solicitudesdetalle").addClass("d-none");
    } 
   }*/
   $("#cantidad_filas").val(0);
   numFilas=0;
   cantidadItems=0;
}

function listarTipoSolicitudAjaxPropuesta(tipo,id){
  var url="";
  if(tipo==1){
   url="ajaxListSimulacion.php";
  }else{
   url="ajaxListProveedor.php";
  }
  if(tipo!=3){
   ajax=nuevoAjax();
    ajax.open("GET",url,true);
    ajax.onreadystatechange=function(){
    if (ajax.readyState==4) {
      var fi=$("#lista_tipo");
      fi.html(ajax.responseText);
      fi.bootstrapMaterialDesign();
      $("#simulaciones").val(id);
       $('.selectpicker').selectpicker("refresh");
    }
   }
    ajax.send(null);
  }
 }
 // function listarTipoSolicitudCajaChica(tipo){
 //  var url="";
 //  if(tipo==1){
 //   url="caja_chica/ajaxListPersonal.php";
 //  }else{
 //   url="caja_chica/ajaxListProveedor.php";
 //  }
 //  ajax=nuevoAjax();
 //    ajax.open("GET",url,true);
 //    ajax.onreadystatechange=function(){
 //    if (ajax.readyState==4) {
 //      var fi=$("#lista_tipoCC");
 //      fi.html(ajax.responseText);
 //      fi.bootstrapMaterialDesign();
 //       $('.selectpicker').selectpicker("refresh");
 //    }
 //   }
 //    ajax.send(null);
 // }

 function guardarSolicitudRecursos(){
  var numero=$("#numero").val();
  var tipo=$("#tipo_solicitud").val();
  if(tipo==1){
    var codSim=$("#simulaciones").val();
    var codProv=0;
  }else{
    if(tipo==2){
      var codProv=$("#proveedores").val();
      var codSim=0;
    }else{
      var codProv=0;
      var codSim=0;
    }   
  }
  if(numero==""||tipo==""){
   $("#mensaje").html("<center><p class='text-danger'>Todos los campos son requeridos.</p></center>");
  }else{
    if($("#id_ibnorca").length){
      var q=$("#id_ibnorca").val();
      var s=$("#ibnorca_s").val();
      var u=$("#ibnorca_u").val();
      var v=$("#ibnorca_v").val();
      var parametros={"q":q,"s":s,"u":u,"v":v,"numero":numero,"cod_sim":codSim,"cod_prov":codProv};
    }else{
      var parametros={"numero":numero,"cod_sim":codSim,"cod_prov":codProv};
    }
     
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "ajaxRegistrarSolicitud.php",
        data: parametros,
        beforeSend: function () { 
         $("#mensaje").html("<center><p class='text-warning'>Procesando. Espere...</p></center>");
          
        },
        success:  function (resp) {
         $("#mensaje").html("<center><p class='text-success'>"+resp+"</p></center>");
        }
    });
  }
}

function cargarDatosCuenta(){
  var codigoProv=$("#proveedores").val();
 for (var i = 0; i < numFilas; i++) {
   $('select[name=proveedor'+(i+1)+']').val(codigoProv);
   quitarFormaPagoProveedor(i+1);  
   }
   $('.selectpicker').selectpicker("refresh");
}

function addSolicitudDetalle(obj,tipo) {
  var tipoSolicitud=$("#tipo_solicitud").val();
  if($("#cod_solicitud").length>0){
    tipoSolicitud=1;
  }
  if(tipoSolicitud>0){
   var codigoSol=$("#cod_solicitud").val();
      numFilas++;
      cantidadItems++;
      filaActiva=numFilas;
      //aumentar un itemfactura
      var ndet=[];
      itemDetalle.push(ndet);
      var nfac=[];itemFacturas.push(nfac);
      document.getElementById("cantidad_filas").value=numFilas;
      console.log("num: "+numFilas+" cantidadItems: "+cantidadItems);
      fi = document.getElementById('fiel');
      contenedor = document.createElement('div');
      contenedor.id = 'div'+numFilas;  
      fi.type="style";
      fi.appendChild(contenedor);
      var divDetalle;
      divDetalle=$("#div"+numFilas);
      var url="ajaxSolicitudRecursosDetalleSimulacion.php";
      ajax=nuevoAjax();
      if(tipoSolicitud==3){
        var unidad = $("#unidad_solicitud").val();
        var area = $("#area_solicitud").val();
        ajax.open("GET",url+"?idFila="+numFilas+"&unidad="+unidad+"&area="+area+"&codigo="+codigoSol,true);  
      }else{
        ajax.open("GET",url+"?idFila="+numFilas+"&codigo="+codigoSol,true);
      }
      ajax.onreadystatechange=function(){
        if (ajax.readyState==4) {
          divDetalle.html(ajax.responseText);
          autocompletar("partida_cuenta"+filaActiva,"partida_cuenta_id"+filaActiva,array_cuenta);
          divDetalle.bootstrapMaterialDesign();
          $('.selectpicker').selectpicker("refresh");
          return false;
       }
      }   
      ajax.send(null);
  }else{
    Swal.fire('Informativo!','Debe seleccionar un tipo de Solicitud!','warning');
    return false;
  }
    
}
function habilitarFila(fila){
  var check=document.getElementById("habilitar"+fila);
  if(!check.checked){
    $("#unidad"+fila).attr("disabled",true);
    $("#area"+fila).attr("disabled",true);
    $("#partida_cuenta"+fila).attr("disabled",true);
    $("#detalle_detalle"+fila).attr("disabled",true);
    $("#importe"+fila).attr("disabled",true);
    $("#proveedor"+fila).attr("disabled",true); 
    
    $("#boton_archivos"+fila).addClass("d-none"); 
    $("#boton_ret"+fila).addClass("d-none"); 
    $("#boton_fac"+fila).addClass("d-none"); 
    $("#boton_remove"+fila).addClass("d-none"); 
  }else{
    $("#unidad"+fila).removeAttr("disabled");
    $("#area"+fila).removeAttr("disabled");
    $("#partida_cuenta"+fila).removeAttr("disabled");
    $("#detalle_detalle"+fila).removeAttr("disabled");
    $("#importe"+fila).removeAttr("disabled");
    $("#proveedor"+fila).removeAttr("disabled");
    
    $("#boton_archivos"+fila).removeClass("d-none");
    $("#boton_ret"+fila).removeClass("d-none");
    $("#boton_fac"+fila).removeClass("d-none");
    $("#boton_remove"+fila).removeClass("d-none");
  }
  $('.selectpicker').selectpicker("refresh");
}
function minusDetalleSolicitud(idF){
      var elem = document.getElementById('div'+idF);
      elem.parentNode.removeChild(elem);
      if(idF<numFilas){
      for (var i = parseInt(idF); i < (numFilas+1); i++) {
        var nuevoId=i+1;
       $("#div"+nuevoId).attr("id","div"+i);

       $("#unidad_fila"+nuevoId).attr("name","unidad_fila"+i);
       $("#unidad_fila"+nuevoId).attr("id","unidad_fila"+i);
       $("#area_fila"+nuevoId).attr("name","area_fila"+i);
       $("#area_fila"+nuevoId).attr("id","area_fila"+i);

       $("#unidad"+nuevoId).attr("name","unidad"+i);
       $("#unidad"+nuevoId).attr("id","unidad"+i);
       $("#area"+nuevoId).attr("name","area"+i);
       $("#area"+nuevoId).attr("id","area"+i);
       $("#cod_detalleplantilla"+nuevoId).attr("name","cod_detalleplantilla"+i);
       $("#cod_detalleplantilla"+nuevoId).attr("id","cod_detalleplantilla"+i);
       $("#cod_servicioauditor"+nuevoId).attr("name","cod_servicioauditor"+i);
       $("#cod_servicioauditor"+nuevoId).attr("id","cod_servicioauditor"+i);
       $("#habilitar"+nuevoId).attr("name","habilitar"+i);
       $("#habilitar"+nuevoId).attr("id","habilitar"+i);
       $("#habilitar"+nuevoId).attr("onchange","habilitarFila('"+i+"')");
       if($("#simulacion").length){
        
       }else{

       }
       $("#partida_cuenta_id"+nuevoId).attr("name","partida_cuenta_id"+i);
        $("#partida_cuenta_id"+nuevoId).attr("id","partida_cuenta_id"+i);
        $("#partida_cuenta"+nuevoId).attr("name","partida_cuenta"+i);
        $("#partida_cuenta"+nuevoId).attr("id","partida_cuenta"+i);
        $("#detalle_detalle"+nuevoId).attr("name","detalle_detalle"+i);
        $("#detalle_detalle"+nuevoId).attr("id","detalle_detalle"+i);
        $("#importe"+nuevoId).attr("name","importe"+i);
        $("#importe"+nuevoId).attr("id","importe"+i);
        $("#importe_presupuesto"+nuevoId).attr("name","importe_presupuesto"+i);
        $("#importe_presupuesto"+nuevoId).attr("id","importe_presupuesto"+i);
        $("#proveedor"+nuevoId).attr("name","proveedor"+i);
        $("#proveedor"+nuevoId).attr("id","proveedor"+i);
       $("#boton_remove"+nuevoId).attr("onclick","minusDetalleSolicitud('"+i+"')");
       $("#boton_remove"+nuevoId).attr("id","boton_remove"+i);
       $("#boton_fac"+nuevoId).attr("onclick","listFac('"+i+"')");
       $("#boton_fac"+nuevoId).attr("id","boton_fac"+i);
       $("#nfac"+nuevoId).attr("id","nfac"+i);
       $("#boton_ret"+nuevoId).attr("onclick","listRetencion('"+i+"')");
       $("#boton_ret"+nuevoId).attr("id","boton_ret"+i);
       $("#archivos_fila"+nuevoId).attr("id","archivos_fila"+i);
       $("#archivos"+nuevoId).attr("name","archivos"+i);
       $("#archivos"+nuevoId).attr("id","archivos"+i);
       $("#boton_archivos"+nuevoId).attr("onclick","addArchivos('"+i+"')");
       $("#boton_archivos"+nuevoId).attr("id","boton_archivos"+i);
       $("#narch"+nuevoId).attr("id","narch"+i);
       $("#importe_label"+nuevoId).attr("id","importe_label"+i); 
       $("#cod_retencion"+nuevoId).attr("name","cod_retencion"+i);
       $("#cod_retencion"+nuevoId).attr("id","cod_retencion"+i);
       
       $("#cod_tipopago"+nuevoId).attr("name","cod_tipopago"+i);
       $("#cod_tipopago"+nuevoId).attr("id","cod_tipopago"+i);
       $("#nombre_beneficiario"+nuevoId).attr("name","nombre_beneficiario"+i);
       $("#nombre_beneficiario"+nuevoId).attr("id","nombre_beneficiario"+i);
       $("#apellido_beneficiario"+nuevoId).attr("name","apellido_beneficiario"+i);
       $("#apellido_beneficiario"+nuevoId).attr("id","apellido_beneficiario"+i);
       $("#cuenta_beneficiario"+nuevoId).attr("name","cuenta_beneficiario"+i);
       $("#cuenta_beneficiario"+nuevoId).attr("id","cuenta_beneficiario"+i); 

       $("#cod_cuentaBancaria"+nuevoId).attr("name","cod_cuentaBancaria"+i);
       $("#cod_cuentaBancaria"+nuevoId).attr("id","cod_cuentaBancaria"+i);
       
       $("#boton_formapago"+nuevoId).attr("onclick","agregarTipoPagoProveedorDetalle('"+i+"')");
       $("#boton_formapago"+nuevoId).attr("id","boton_formapago"+i);
       $("#nben"+nuevoId).attr("id","nben"+i);
       $("#fila_index"+nuevoId).text(i);
       $("#fila_index"+nuevoId).attr("id","fila_index"+i);

       var cantidadArchivos=$("#cantidad_archivosadjuntosdetalle"+nuevoId).val();
       for (var l = 1; l <= parseInt(cantidadArchivos); l++) {
        if($("#codigo_archivodetalle"+l+"FFFF"+nuevoId).length>0){
           $("#codigo_archivodetalle"+l+"FFFF"+nuevoId).attr("name","codigo_archivodetalle"+l+"FFFF"+i);
           $("#codigo_archivodetalle"+l+"FFFF"+nuevoId).attr("id","codigo_archivodetalle"+l+"FFFF"+i);
           $("#nombre_archivodetalle"+l+"FFFF"+nuevoId).attr("name","nombre_archivodetalle"+l+"FFFF"+i);
           $("#nombre_archivodetalle"+l+"FFFF"+nuevoId).attr("id","nombre_archivodetalle"+l+"FFFF"+i);
           $("#documentos_detalle"+l+"FFFF"+nuevoId).attr("name","documentos_detalle"+l+"FFFF"+i);
           $("#documentos_detalle"+l+"FFFF"+nuevoId).attr("id","documentos_detalle"+l+"FFFF"+i);
        } 
       };
       $("#cantidad_archivosadjuntosdetalle"+nuevoId).attr("name","cantidad_archivosadjuntosdetalle"+i);
       $("#cantidad_archivosadjuntosdetalle"+nuevoId).attr("id","cantidad_archivosadjuntosdetalle"+i);
       $("#archivos_fila"+nuevoId).attr("id","archivos_fila"+i);

       $("#cod_actividadproyecto"+nuevoId).attr("name","cod_actividadproyecto"+i);
       $("#cod_actividadproyecto"+nuevoId).attr("id","cod_actividadproyecto"+i);
       $("#des_actividadproyecto"+nuevoId).attr("name","des_actividadproyecto"+i);
       $("#des_actividadproyecto"+nuevoId).attr("id","des_actividadproyecto"+i);
      }
     } 
     itemFacturas.splice((idF-1), 1);
     itemDocumentosDetalle.splice((idF-1), 1);
      numFilas=numFilas-1;
      cantidadItems=cantidadItems-1;
      filaActiva=numFilas;
      document.getElementById("cantidad_filas").value=numFilas;
      calcularTotalesSolicitud();  
      listarProyectosSisdeUnidades();
}

var numArchivosDetalle=0;
function archivosPreviewDetalle(send) {
  var fila =$("#codigo_fila").val();
  numArchivosDetalle=0;
  itemDocumentosDetalle[fila-1]=[];
     var x = $("#archivosDetalle");
      var y = x.clone();
      y.attr("id", "archivos"+fila);
      y.attr("name", "archivos"+fila+"[]");
      $("#archivos_fila"+fila).html(y);
      //y.insertAfter("button");
    var inp=document.getElementById("archivosDetalle");
    var inpDetalle=document.getElementById("archivos"+fila);
    if(send!=1){
    var table = $('<table>').addClass('table');
      table.addClass('table-condensed');
      table.addClass('table-bordered');
       var titulos = $('<tr>').addClass('bg-info text-white');
       titulos.append($('<td>').addClass('text-left').text('#'));
       titulos.append($('<td>').addClass('').text('DOCUMENTO'));
       titulos.append($('<td>').addClass('').text('.*'));
       if($("#formSolDet").length>0){
         titulos.append($('<td>').addClass('').text('TIPO DOCUMENTO')); 
         titulos.append($('<td>').addClass('').text('DESCRIPCIÓN TIPO'));     
       }
       table.append(titulos);
      for (var i = 0; i < inpDetalle.files.length; ++i) {
        numArchivosDetalle++;
        var name = inpDetalle.files.item(i).name;
        var row = $('<tr>').addClass('');
       row.append($('<td>').addClass('text-left').text(i+1));
       row.append($('<td>').addClass('font-weight-bold').text(name));
       row.append($('<td>').addClass('font-weight-bold').text(/[^.]+$/.exec(name)));
       if($("#formSolDet").length>0){
        var doc = {
          id:i,
          nombre:name,
          tipo:0,
          nombre_tipo:""
        }
        itemDocumentosDetalle[fila-1].push(doc);
        console.log(JSON.stringify(itemDocumentosDetalle[fila-1]));
        var htmlSelect = '<select onChange="asignarTipoDocumentoDetalle(\''+name+'\','+(i+1)+','+(fila)+')" class="selectpicker form-control form-control-sm" name="tipo_documentodetalle'+(i+1)+'" id="tipo_documentodetalle'+(i+1)+'" data-style="btn btn-warning">';
         htmlSelect+=$("#tipo_documento").html();
         htmlSelect+='</select>';
         row.append($('<td>').addClass('').html(htmlSelect));

        var htmlInput='<input class="form-control text-right text-muted" placeholder="Ingresar descripción" id="nombre_tipodocumentodetalle'+(i+1)+'" readonly value="SIN TIPO ARCHIVO" onkeyup="asignarTipoDocumentoTextDetalle('+(i+1)+','+(fila)+')" onkeydown="asignarTipoDocumentoTextDetalle('+(i+1)+','+(fila)+')">'       
        row.append($('<td>').addClass('').html(htmlInput));
       }        
       table.append(row);
       }     
      $("#lista_archivosdetalle").html(table);
       $("#narch"+fila).addClass("estado");
       $('.selectpicker').selectpicker("refresh");
     }else{
      numArchivosDetalle=0;
        $("#lista_archivosdetalle").html("Ningun archivo seleccionado");
        $("#narch"+fila).removeClass("estado");
     }
   }

  function addArchivos(fila){
    $("#codigo_fila").val(fila);
    
    if($("#archivos_fila"+fila).length>0){
      cargarArchivosAdjuntosFila(fila);
    }else{
      $("#archivosDetalle").val("");
      $("#lista_archivosdetalle").html("Ningun archivo seleccionado");
      var inpDetalle=document.getElementById("archivos"+fila);
      var contador=0;
      for (var i = 0; i < inpDetalle.files.length; i++) {
       numArchivosDetalle++;
        var name = inpDetalle.files.item(i).name;
        $("#lista_archivosdetalle").append("<div class='text-left'><label>"+name+"</label></div>");
        contador++;
     };
      if(contador==0){
      $("#boton_quitararchivos").click();
     }
    }
    $('#modalFileDet').modal('show');
  }
function agregarRetencionSolicitud(){
  var listaRet=[];var i=0;
  $("input[name=retenciones]").each(function (index) {  
       if($(this).is(':checked')){
          listaRet[i]= $(this).val();
          i++;
       }
    });
  if(listaRet.length>0){
    var fila = $("#retFila").val();
     var respuesta=listaRet[0].split('@');
     $("#cod_retencion"+fila).val(respuesta[0]);
     $("#retFila").val("");
     $('#modalRetencion').modal('hide');
     $("#importe_label"+fila).html("<small>Importe-"+respuesta[1].substr(0,3)+".</small>");
  }else{
    $("#mensaje_retencion").html("<p class='text-danger'>Debe seleccionar al menos una retención</p>");
  }
  
 }

 function addSolicitudDetalleSearch() {
    //var codigoSol=$("#cod_solicitud").val();
    var codCuenta=$("#cuenta_proveedor").val();
    var fechai=$("#fecha_desde").val();
    var fechaf=$("#fecha_hasta").val();
    var tipo=$("#tipo_solicitudproveedor").val();
     var fi = $('#fiel');
     var parametros={"fecha_i":fechai,"fecha_f":fechaf,"cod_cuenta":codCuenta,"tipo":tipo};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "ajaxSolicitudRecursosDetalleProveedor.php",
        data: parametros,
        beforeSend: function (){
            iniciarCargaAjax();
        },
        success:  function (resp) {
           detectarCargaAjax();
          itemFacturas=[];
          fi.html("");
          fi.html(resp);
          fi.bootstrapMaterialDesign();
          $('.selectpicker').selectpicker("refresh");
          return false;
        },
        error: function (xhr, ajaxOptions, thrownError) {
        Swal.fire("Error de proceso!", "Contactese con el administrador", "error");
       }
    });
//perosnal area distribucion
}
function modificarMontos(){
  $('#modalEditPlantilla').modal('hide');
  $('#modalSimulacionCuentas').modal('show');
}
function modificarMontosPeriodo(anio){
  $('#modalEditPlantilla').modal('hide');
  $('#modalSimulacionCuentas'+anio).modal('show');
}
function modificarMontosPersonal(){
  $('#modalEditPlantilla').modal('hide');
  $('#modalSimulacionCuentasPersonal').modal('show');
}
function cargarCuentasSimulacion(cods,ib){
  var fi = $('#cuentas_simulacion');
  var codp=$("#partida_presupuestaria").val();
  var codpar=$("#cod_plantilla").val();
  var al_i=$("#alumnos_plan").val();
  var al_f=$("#alumnos_plan_fuera").val();
  if(codp!=""){
     var parametros={"codigo":codp,"codSim":cods,"ibnorca":ib,"codPar":codpar,"al_i":al_i,"al_f":al_f};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "ajaxCargarDetallePlantillaPartida.php",
        data: parametros,
        beforeSend: function (){
            iniciarCargaAjax();
        },
        success:  function (resp) {
           detectarCargaAjax();
          fi.html("");
          fi.html(resp);
        },
        error: function (xhr, ajaxOptions, thrownError) {
        Swal.fire("Error de proceso!", "Contactese con el administrador", "error");
       }
    });
  //ajax.open("GET","ajaxCargarCuentas.php?codigo="+codp+"&codSim="+cods+"&ibnorca="+ib+"&codPar="+codpar+"&al_i="+al_i+"&al_f="+al_f,true);
 
  }
}
///////////////////////////////////////////////////

//personal ARea distribucion
function ajaxPersonal_area_distribucion(combo){
  var contenedor;
  var codigo_UO=combo.value;
  contenedor = document.getElementById('div_contenedor_area');
  ajax=nuevoAjax();
  ajax.open('GET', 'personal/personal_area_distribucionAjax.php?codigo_UO='+codigo_UO,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);            
    }
  }
  ajax.send(null)  
}

//personal ARea distribucion
function ajaxPersonal_area_distribucionE(combo){
  var contenedor;
  var codigo_UO=combo.value;
  contenedor = document.getElementById('div_contenedor_areaE');
  ajax=nuevoAjax();
  ajax.open('GET', 'personal/personal_area_distribucionEAjax.php?codigo_UO='+codigo_UO,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);            
    }
  }
  ajax.send(null)  
}

function agregaformPAD(datos){
  //console.log("datos: "+datos);
  var d=datos.split('-');
  document.getElementById("codigo_personal").value=d[0];
  document.getElementById("codigo_distribucion").value=d[1];
}
function agregaformPADE(datos){
  //console.log("datos: "+datos);
  var d=datos.split('-');
  document.getElementById("codigo_personalE").value=d[0];
  document.getElementById("codigo_distribucionE").value=d[1];

  // document.getElementById("cod_uoE").value=d[2];
  // document.getElementById("cod_areaE").value=d[3];
  document.getElementById("porcentajeE").value=d[4];
  ajaxUO_Edit(d[2],d[3]);


}
function ajaxUO_Edit(cod_uo,cod_area){

  contenedor = document.getElementById('div_contenedor_uo_x');
  ajax=nuevoAjax();
  ajax.open('GET', 'personal/ajax_editar_uo_distibucion.php?cod_uo='+cod_uo,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]); 
      ajaxArea_Edit(cod_uo,cod_area);           
    }
  }
  ajax.send(null)  
}
function ajaxArea_Edit(cod_uo,cod_area){
  // var contenedor;
  // var codigo_UO=combo.value;
  contenedor = document.getElementById('div_contenedor_areaE');
  ajax=nuevoAjax();
  ajax.open('GET', 'personal/ajax_editar_area_distibucion.php?cod_uo='+cod_uo+'&cod_area='+cod_area,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);            
    }
  }
  ajax.send(null)  
}

function agregaformPADB(datos){
  //console.log("datos: "+datos);
  var d=datos.split('-');
  document.getElementById("codigo_personalB").value=d[0];
  document.getElementById("codigo_distribucionB").value=d[1];
}

function RegistrarDistribucion(cod_uo,cod_personal,cod_area,porcentaje){
  $.ajax({
    type:"POST",
    data:"cod_personal="+cod_personal+"&cod_uo="+cod_uo+"&cod_area="+cod_area+"&cod_estadoreferencial=1&porcentaje="+porcentaje,
    url:"personal/savePersonalAreaDistribucion.php",
    success:function(r){
      if(r==1){
        //$('#tabla1').load('index.php');
        // alertify.success("agregado");
        alerts.showSwal('success-message','index.php?opcion=PersonalAreaDistribucionForm&codigo='+cod_personal);
      }
    }
  });
}
function EditarDistribucion(cod_personal,cod_distribucion,cod_uo,cod_area,porcentaje){
  $.ajax({
    type:"POST",
    data:"cod_personal="+cod_distribucion+"&cod_uo="+cod_uo+"&cod_area="+cod_area+"&cod_estadoreferencial=2&porcentaje="+porcentaje,
    url:"personal/savePersonalAreaDistribucion.php",
    success:function(r){
      if(r==1){
        //$('#tabla1').load('index.php');
        // alertify.success("agregado");
        alerts.showSwal('success-message','index.php?opcion=PersonalAreaDistribucionForm&codigo='+cod_personal);
      }
    }
  });
}

function EliminarDistribucion(cod_personal,cod_distribucion){
  $.ajax({
    type:"POST",
    data:"cod_personal="+cod_distribucion+"&cod_uo=0&cod_area=0&cod_estadoreferencial=3&porcentaje=0",
    url:"personal/savePersonalAreaDistribucion.php",
    success:function(r){
      if(r==1){
        //$('#tabla1').load('index.php');
        // alertify.success("agregado");
        alerts.showSwal('success-message','index.php?opcion=PersonalAreaDistribucionForm&codigo='+cod_personal);    
      }
    }
  });
}
//area_unidad organizacional
// function agregaListAreas_unidad(datos){
//   //console.log("datos: "+datos);
//   var d=datos.split('-');
//   document.getElementById("codigo_area_unidad").value=d[0];
//   // alert(d[0]);



 
var areas_tabla=[]; 
var areas_tabla_general=[];
var numFilasA=0;
function sendChekedA(id){
  var check=document.getElementById("areas"+id);
    check.onchange = function() {
     if(this.checked) {
      numFilasA++;
      
     }else{
      
      numFilasA--;
     }
     $("#boton_registradasA").html("Areas Registradas <span class='badge bg-white text-warning'>"+numFilasA+"</span>");
   }
} 

// function filaTabla(tabla){
//   var html="";
//   for (var i = 0; i < areas_tabla.length; i++) {
//     html+="<tr><td>"+(i+1)+"</td><td>"+areas_tabla[i].nombre+"</td><td>"+areas_tabla[i].numero+"</td></tr>";
//   };
//   tabla.html(html);
//   $("#modalCuentas").modal("show");
// }

function filaTablaAGeneral_areas(tabla,index){
  var html="";
  for (var i = 0; i < areas_tabla_general[index-1].length; i++) {
    //alert(areas_tabla_general[index-1][i].nombre);
    html+="<tr><td>"+(i+1)+"</td><td>"+areas_tabla_general[index-1][i].nombreA+"</td><td>"+areas_tabla_general[index-1][i].nombreAP+"</td><td><a href='#' onclick='cargarCargosAreasOrganizacion("+areas_tabla_general[index-1][i].cod_areaorganizacion+",\""+areas_tabla_general[index-1][i].nombreA+"\")' class='btn btn-fab btn-info btn-sm btn-rounded'><i class='material-icons' title='Asignar Cargos'>add</i></a></td></tr>";
  }
  tabla.html(html);
  $("#modalAreas").modal("show");  
}

function cargarCargosAreasOrganizacion(cod,nom){
  var parametros={"codigo":cod,"nombreArea":nom};
   $.ajax({
    type:"GET",
    data:parametros,
    url:"rrhh/ajaxCargosAreasOrganizacion.php",
    success:function(resp){
      $("#mensajeRealizado").html("");
      $("#tutulo_cargosarea").text(nom);
      $("#areaorganizacion_id").val(cod);
      $("#tablasCargos_registrados").html(resp);
      $("#modalCargos").modal("show");
    }
  });
}
function borrarCargoAreaOrganizacion(codigo){
  var cod=$("#areaorganizacion_id").val();
  var parametros={"codigo_fila":codigo};
   $.ajax({
    type:"GET",
    data:parametros,
    url:"rrhh/ajaxBorrarCargoAreasOrganizacion.php",
    success:function(resp){ 
      $("#mensajeRealizado").html("<p class='text-danger'>"+resp+"</p>");
      cargarCargosAreasOrganizacion(cod,$("#tutulo_cargosarea").text());
    }
  });
}
function agregarCargoAreaOrganizacion(){
  var cod=$("#areaorganizacion_id").val();
  var codCargo=$("#cargo_areaorg").val();
  if(codCargo!=""){
    var parametros={"codigo":cod,"cod_cargo":codCargo};
   $.ajax({
    type:"GET",
    data:parametros,
    url:"rrhh/ajaxNuevoCargoAreasOrganizacion.php",
    success:function(resp){ 
      $("#mensajeRealizado").html("<p class='text-success'>"+resp+"</p>");
      cargarCargosAreasOrganizacion(cod,$("#tutulo_cargosarea").text());
    }
  });  
  }  
}
// function ajaxAUOPadre(combo){
//   var contenedor;
//   var codigo_UO=combo.value;
//   contenedor = document.getElementById('cod_areaorganizacion_padre_div');
//   ajax=nuevoAjax();
//   ajax.open('GET', 'rrhh/AreaPadreAjax.php?codigo_UO='+codigo_UO,true);
//   ajax.onreadystatechange=function() {
//     if (ajax.readyState==4) {
//       contenedor.innerHTML = ajax.responseText;
//       $('.selectpicker').selectpicker(["refresh"]);
      
//     }
//   }
//   ajax.send(null)  
// }
//funciones cargos
function agregaFuncionCargo(datos){
  //console.log("datos: "+datos);
  var d=datos.split('/');
  document.getElementById("cod_cargoA").value=d[0];
}
function RegistrarCargoFuncion(cod_cargoA,nombre_funcionA,pesoA){
  $.ajax({
    type:"POST",
    data:"cod_funcion=0&cod_cargo="+cod_cargoA+"&nombre_funcion="+nombre_funcionA+"&cod_estadoreferencial=1&peso="+pesoA,
    url:"rrhh/cargoFuncionSave.php",
    success:function(r){
      if(r==1){
        alerts.showSwal('success-message','index.php?opcion=cargosFunciones&codigo='+cod_cargoA);
      }else{
        alerts.showSwal('error-message','index.php?opcion=cargosFunciones&codigo='+cod_cargoE);
      } 
    }
  });
}
function agregaFuncionCargoE(datos){
  //console.log("datos: "+datos);
  var d=datos.split('/');
  document.getElementById("cod_cargoE").value=d[0];
  document.getElementById("cod_cargo_funcionE").value=d[1];
  document.getElementById("nombre_funcionE").value=d[2];
  document.getElementById("pesoE").value=d[3];
}
function EditarCargoFuncion(cod_cargoE,cod_cargo_funcionE,nombre_funcionE,pesoE){
  $.ajax({
    type:"POST",
    data:"cod_funcion="+cod_cargo_funcionE+"&cod_cargo="+cod_cargoE+"&nombre_funcion="+nombre_funcionE+"&cod_estadoreferencial=2&peso="+pesoE,
    url:"rrhh/cargoFuncionSave.php",
    success:function(r){
      if(r==1){
        alerts.showSwal('success-message','index.php?opcion=cargosFunciones&codigo='+cod_cargoE);
      }else{
        alerts.showSwal('error-message','index.php?opcion=cargosFunciones&codigo='+cod_cargoE);
      } 
    }
  });
}
function agregaFuncionCargoB(datos){
  //console.log("datos: "+datos);
  var d=datos.split('/');
  document.getElementById("cod_cargoB").value=d[0];
  document.getElementById("cod_cargo_funcionB").value=d[1];
}
function EliminarCargoFuncion(cod_cargoB,cod_cargo_funcionB){
  $.ajax({
    type:"POST",
    data:"cod_funcion="+cod_cargo_funcionB+"&cod_cargo="+cod_cargoB+"&nombre_funcion=''&cod_estadoreferencial=3&peso=0",
    url:"rrhh/cargoFuncionSave.php",
    success:function(r){
      if(r==1){
        alerts.showSwal('success-message','index.php?opcion=cargosFunciones&codigo='+cod_cargoB);
      }else{      
          alerts.showSwal('error-message','index.php?opcion=cargosFunciones&codigo='+cod_cargoB);
      } 
    }
  });
}

//cargos escalas salarial

function agregaCargoEscalaSalarialE(datos){
  var d=datos.split('/');
  document.getElementById("cod_cargoE").value=d[0];
  document.getElementById("cod_cargo_escala_salarialE").value=d[1];
  document.getElementById("nombre_nivel_escalaE").value=d[2];
  document.getElementById("montoE").value=d[3];
}

//contratos de personal
function agregaformPC(datos){
  //console.log("datos: "+datos);
  var d=datos.split('/');
  document.getElementById("codigo_personalA").value=d[0];
}
function agregaformPCE(datos){
  //console.log("datos: "+datos);
  var d=datos.split('/');
  document.getElementById("codigo_personalE").value=d[0];
  document.getElementById("codigo_contratoE").value=d[1];
  document.getElementById("fecha_inicioE").value=d[2];

  // document.getElementById("cod_areaE").value=d[2];
  // document.getElementById("porcentajeE").value=d[3];
}
function agregaContratoFin(datos){
  //console.log("datos: "+datos);
  var d=datos.split('/');
  
  // alert("d1: "+d[0]+"-d2: "+d[1]);
  document.getElementById("cod_personalCF").value=d[0];
  document.getElementById("cod_contratoCf").value=d[1];
}
function agregaformPCB(datos){
  //console.log("datos: "+datos);
  var d=datos.split('/');
  document.getElementById("codigo_personalB").value=d[0];
  document.getElementById("codigo_contratoB").value=d[1];
}

function agregaformEditEva(datos){
  //console.log("datos: "+datos);
  var d=datos.split('/');
  document.getElementById("codigo_personalEv").value=d[0];
  document.getElementById("codigo_contratoEv").value=d[1];
  document.getElementById("fecha_EvaluacionEv").value=d[3];
}
function agregaformRetiroPersonal(datos){
  //console.log("datos: "+datos);
  var d=datos.split('/');
  document.getElementById("codigo_personalR").value=d[0];

  // document.getElementById("cod_areaE").value=d[2];
  // document.getElementById("porcentajeE").value=d[3];
}
function RegistrarContratoPersonal(cod_personal,cod_tipocontrato,fecha_inicio){
  $.ajax({
    type:"POST",
    data:"cod_contrato=0&cod_personal="+cod_personal+"&cod_tipocontrato="+cod_tipocontrato+"&cod_estadoreferencial=1&fecha_inicio="+fecha_inicio+"&observaciones=''",
    url:"personal/savePersonalcontrato.php",
    success:function(r){
      if(r==1){
        alerts.showSwal('success-message','index.php?opcion=FormPersonalContratos&codigo='+cod_personal);
      }else{
        if(r==2){
          alerts.showSwal('error-message5','index.php?opcion=FormPersonalContratos&codigo='+cod_personal);
        }else{
          alerts.showSwal('error-message','index.php?opcion=FormPersonalContratos&codigo='+cod_personal);
        }
      } 
    }
  });
}
function EditarContratoPersonal(codigo_contratoE,codigo_personalE,cod_tipocontratoE,fecha_inicioE){
  $.ajax({
    type:"POST",

    data:"cod_contrato="+codigo_contratoE+"&cod_personal=0&cod_tipocontrato="+cod_tipocontratoE+"&cod_estadoreferencial=2&fecha_inicio="+fecha_inicioE+"&observaciones=''",
    url:"personal/savePersonalcontrato.php",
    success:function(r){
      if(r==1){
        //$('#tabla1').load('index.php');
        // alertify.success("agregado");
        alerts.showSwal('success-message','index.php?opcion=FormPersonalContratos&codigo='+codigo_personalE);
      }else{
        alerts.showSwal('error-message','index.php?opcion=FormPersonalContratos&codigo='+codigo_personalE);
      }
    }
  });
}
function EditarEvaluacionPersonal(codigo_contratoEv,codigo_personalEv,fecha_EvaluacionEv){
  $.ajax({
    type:"POST",

    data:"cod_contrato="+codigo_contratoEv+"&cod_personal=0&cod_tipocontrato=0&cod_estadoreferencial=4&fecha_inicio="+fecha_EvaluacionEv+"&observaciones=''",
    url:"personal/savePersonalcontrato.php",
    success:function(r){
      if(r==1){        
        alerts.showSwal('success-message','index.php?opcion=FormPersonalContratos&codigo='+codigo_personalEv);
      }else{
        alerts.showSwal('error-message','index.php?opcion=FormPersonalContratos&codigo='+codigo_personalEv);
      }
    }
  });
}
function EliminarContratoPersonal(codigo_contratoB,codigo_personalB){
  $.ajax({
    type:"POST",
    data:"cod_contrato="+codigo_contratoB+"&cod_personal=0&cod_tipocontrato=1&cod_estadoreferencial=3&fecha_inicio=0000-00-00&observaciones=''",
    url:"personal/savePersonalcontrato.php",
    success:function(r){
      if(r==1){
        //$('#tabla1').load('index.php');
        // alertify.success("agregado");
        alerts.showSwal('success-message','index.php?opcion=FormPersonalContratos&codigo='+codigo_personalB);    
      }
    }
  });
}
function RetirarPersonal(cod_personal,cod_tiporetiro,fecha_Retiro,observaciones){
  $.ajax({
    type:"POST",
    data:"cod_contrato=0&cod_personal="+cod_personal+"&cod_tipocontrato="+cod_tiporetiro+"&cod_estadoreferencial=5&fecha_inicio="+fecha_Retiro+"&observaciones="+observaciones,
    url:"personal/savePersonalcontrato.php",
    success:function(r){
      if(r==1){
        alerts.showSwal('success-message','index.php?opcion=personalLista');
      }else{
        if(r==2){
          alerts.showSwal('error-message6','index.php?opcion=FormPersonalContratos&codigo='+cod_personal);
        }else{
          alerts.showSwal('error-message','index.php?opcion=personalLista');
        }
        
      }
    }
  });
}

function FinalizarContratoPersonal(codigo_contratoCF,codigo_personalCf){
  $.ajax({
    type:"POST",

    data:"cod_contrato="+codigo_contratoCF+"&cod_personal=0&cod_tipocontrato=0&cod_estadoreferencial=6&fecha_inicio='0000-00-00'&observaciones=''",
    url:"personal/savePersonalcontrato.php",
    success:function(r){
      if(r==1){   
        alerts.showSwal('success-message','index.php?opcion=FormPersonalContratos&codigo='+codigo_personalCf);
      }else{
        alerts.showSwal('error-message','index.php?opcion=FormPersonalContratos&codigo='+codigo_personalCf);
        //$('#tabla1').load('index.php');
        // alertify.success("agregado");
        alerts.showSwal('success-message','index.php?opcion=FormPersonalContratos&codigo='+cod_personal);
      }
    }
  });
}

///=================planillas sueldos



var planillas_tabla=[]; 
var planillas_tabla_general=[]; 
var planillas_tabla_bonos=[]; 

//PrerequisitosPlanillaSueldo
function filaTablaGeneralPlanillasSueldo(tabla,index){  

  var html="";
  for (var i = 0; i < planillas_tabla_general[index-1].length; i++) {
    //alert(planillas_tabla_general[index-1][i].nombre);

    html+="<div class='row'>"+
      "<label class='col-sm-3 col-form-label'>DESCUENTOS : </label>"+
      "<div class='col-sm-7'>"+
          "<div class='form-group'>"+
          "<input class='form-control' readonly='readonly' name='descuentos' id='descuentos' value='"+planillas_tabla_general[index-1][i].descuentos+" Registros Este Mes."+"'/>"+"<br>"+
          "</div>"+
      "</div>"+
    "</div>";


    html+="<div class='row'>"+
      "<label class='col-sm-3 col-form-label'>Bonos : </label>"+
      "<div class='col-sm-7'>"+
          "<div class='form-group'>"+
          "<input class='form-control' readonly='readonly' name='bonos' id='bonos' value='"+planillas_tabla_general[index-1][i].bonos+" Registros Este Mes."+"'/>"+"<br>"+              
          "</div>"+
      "</div>"+
    "</div>";

    html+="<div class='row'>"+
      "<label class='col-sm-3 col-form-label'>Atrasos : </label>"+
      "<div class='col-sm-7'>"+
          "<div class='form-group'>"+
          "<input class='form-control' readonly='readonly' name='atrasos' id='atrasos' value='"+planillas_tabla_general[index-1][i].atrasos+" Registros Este Mes."+"'/>"+"<br>"+              
          "</div>"+
      "</div>"+
    "</div>";
    html+="<div class='row'>"+
      "<label class='col-sm-3 col-form-label'>Anticipos : </label>"+
      "<div class='col-sm-7'>"+
          "<div class='form-group'>"+
          "<input class='form-control' readonly='readonly' name='anticipos' id='anticipos' value='"+planillas_tabla_general[index-1][i].anticipos+" Registros Este Mes."+"'/>"+"<br>"+              
          "</div>"+
      "</div>"+
    "</div>";  
  }
  tabla.html(html);
  $("#modalPrerequisitos").modal("show");
}


function agregaformPre(datos){
  //console.log("datos: "+datos);
  var d=datos.split('-');
  document.getElementById("codigo_planilla").value=d[0];
}
function agregaformRP(datos){
  //console.log("datos: "+datos);
  var d=datos.split('-');
  document.getElementById("codigo_planillaRP").value=d[0];
}
function agregaformCP(datos){
  //console.log("datos: "+datos);
  var d=datos.split('-');
  document.getElementById("codigo_planillaCP").value=d[0];
}
function agregaformPreTrib(datos,plan,mes,tiempo,user){
  var d=plan.split('-');
  document.getElementById("codigo_planilla2").value=d[0];

  $("#mes_cursotitulo").html(mes);
  $("#modified_at").text(tiempo);
  $("#modified_by").text(user);
  document.getElementById("codigo_planilla_trib").value=datos;
}
function ProcesarPlanilla(cod_planilla){
  $.ajax({
    type:"POST",
    data:"cod_planilla="+cod_planilla+"&sw=2",
    url:"planillas/savePlanillaMes.php",
    beforeSend:function(objeto){ 
      $('#cargaP').css({display:'block'});
      $('#AceptarProceso').css({display:'none'});
      $('#CancelarProceso').css({display:'none'});  
    },
    success:function(r){
      if(r==1){
        //$('#tabla1').load('activosFijos/afEnCustodia.php');
        $('#cargaP').css('display','none');
        alerts.showSwal('success-message','index.php?opcion=planillasSueldoPersonal');
      }else{
        $('#cargaP').css('display','none');
        alerts.showSwal('error-message','index.php?opcion=planillasSueldoPersonal');
      }
    }
  });
}
function ReprocesarPlanilla(cod_planilla){
  $.ajax({
    type:"POST",
    data:"cod_planilla="+cod_planilla+"&sw=1",
    url:"planillas/savePlanillaMes.php",
    beforeSend:function(objeto){ 
      $('#cargaR').css({display:'block'});
      $('#AceptarReProceso').css({display:'none'});
      $('#CancelarReProceso').css({display:'none'});  
    },
    success:function(r){
      if(r==1){
        $('#cargaR').css('display','none');
        alerts.showSwal('success-message','index.php?opcion=planillasSueldoPersonal');
      }else{
        $('#cargaR').css('display','none');
        alerts.showSwal('error-message','index.php?opcion=planillasSueldoPersonal');
      }
    }
  });
}
function ReprocesarPlanillaTrib(cod_planillaTrib,cod_planilla){
  //window.location.href="planillas/savePlanillaTribMes2.php?cod_planillatrib="+cod_planillaTrib+"&cod_planilla="+cod_planilla;
  $.ajax({
    type:"POST",
    data:"cod_planillatrib="+cod_planillaTrib+"&sw=1&cod_planilla="+cod_planilla,
    url:"planillas/savePlanillaTribMes.php",
    beforeSend:function(objeto){ 
      $('#cargaR2').css({display:'block'});
      $('#AceptarReProcesoTrib').css({display:'none'});
      $('#CancelarReProcesoTrib').css({display:'none'});  
    },
    success:function(r){
      if(r==1){
        $('#cargaR2').css('display','none');
        alerts.showSwal('success-message','index.php?opcion=planillasSueldoPersonal');
      }else{
        $('#cargaR2').css('display','none');
        alerts.showSwal('error-message','index.php?opcion=planillasSueldoPersonal');
      }
    }
  });
}
function CerrarPlanilla(cod_planilla){
  $.ajax({
    type:"POST",
    data:"cod_planilla="+cod_planilla+"&sw=3",
    url:"planillas/savePlanillaMes.php",
    success:function(r){
      if(r==1){
        //$('#tabla1').load('activosFijos/afEnCustodia.php');
        //alertify.success("agregado");
        alerts.showSwal('success-message','index.php?opcion=planillasSueldoPersonal');
      }
    }
  });
}
//funciones para el personal NO Admin
function agregaformPreNA(datos){
  var d=datos.split('-');
  document.getElementById("codigo_planillaNA").value=d[0];
  document.getElementById("codigo_uoNA").value=d[1];
}
function agregaformRPNA(datos){
  var d=datos.split('-');
  document.getElementById("codigo_planillaRPNA").value=d[0];
  document.getElementById("codigo_uoRPNA").value=d[1];
}
function agregaformCPNA(datos){
  var d=datos.split('-');
  document.getElementById("codigo_planillaCPNA").value=d[0];
  document.getElementById("codigo_uoCPNA").value=d[1];
}
function ProcesarPlanillaNA(cod_planilla){  
  $.ajax({
    type:"POST",
    data:"cod_planilla="+cod_planilla+"&sw=2",
    url:"planillas/savePlanillaMesNA.php",
    beforeSend:function(objeto){ 
      $('#cargaPNA').css({display:'block'});
      $('#AceptarProcesoNA').css({display:'none'});
      $('#CancelarProcesoNA').css({display:'none'});  
    },
    success:function(r){
      if(r==1){
        //$('#tabla1').load('activosFijos/afEnCustodia.php');
        $('#cargaPNA').css('display','none');
        alerts.showSwal('success-message','index.php?opcion=planillasSueldoPersonal');
      }else{
        $('#cargaPNA').css('display','none');
        alerts.showSwal('error-message','index.php?opcion=planillasSueldoPersonal');
      }
    }
  });
}
function ReprocesarPlanillaNA(cod_planilla,cod_uo){
  $.ajax({
    type:"POST",
    data:"cod_planilla="+cod_planilla+"&sw=1&cod_uo="+cod_uo,
    url:"planillas/savePlanillaMesNA.php",
    beforeSend:function(objeto){ 
      $('#cargaRNA').css({display:'block'});
      $('#AceptarReProcesoNA').css({display:'none'});
      $('#CancelarReProcesoNA').css({display:'none'});  
    },
    success:function(r){
      if(r==1){
        $('#cargaRNA').css('display','none');
        alerts.showSwal('success-message','index.php?opcion=planillasSueldoPersonal');
      }else{
        $('#cargaRNA').css('display','none');
        alerts.showSwal('error-message','index.php?opcion=planillasSueldoPersonal');
      }
    }
  });
}
function CerrarPlanillaNA(cod_planilla){
  $.ajax({
    type:"POST",
    data:"cod_planilla="+cod_planilla+"&sw=3&cod_uo="+cod_uo,
    url:"planillas/savePlanillaMesNA.php",
    success:function(r){
      if(r==1){
        //$('#tabla1').load('activosFijos/afEnCustodia.php');
        //alertify.success("agregado");
        alerts.showSwal('success-message','index.php?opcion=planillasSueldoPersonal');
      }
    }
  });
}
//funciones par planilla aguinaldos
function ProcesarPlanillaAguinaldos(cod_planilla){
  $.ajax({
    type:"POST",
    data:"cod_planilla="+cod_planilla+"&sw=2",
    url:"planillas/savePlanillaAguinaldos.php",
    beforeSend:function(objeto){ 
      $('#cargaP').css({display:'block'});
      $('#AceptarProceso').css({display:'none'});
      $('#CancelarProceso').css({display:'none'});  
    },
    success:function(r){
      if(r==1){
        //$('#tabla1').load('activosFijos/afEnCustodia.php');
        $('#cargaP').css('display','none');
        alerts.showSwal('success-message','index.php?opcion=planillasAguinaldosPersonal');
      }else{
        $('#cargaP').css('display','none');
        alerts.showSwal('error-message','index.php?opcion=planillasAguinaldosPersonal');
      }
    }
  });
}
function ReprocesarPlanillaAguinaldos(cod_planilla){
  $.ajax({
    type:"POST",
    data:"cod_planilla="+cod_planilla+"&sw=1",
    url:"planillas/savePlanillaAguinaldos.php",
    beforeSend:function(objeto){ 
      $('#cargaR').css({display:'block'});
      $('#AceptarReProceso').css({display:'none'});
      $('#CancelarReProceso').css({display:'none'});  
    },
    success:function(r){
      if(r==1){
        $('#cargaR').css('display','none');
        alerts.showSwal('success-message','index.php?opcion=planillasAguinaldosPersonal');
      }else{
        $('#cargaR').css('display','none');
        alerts.showSwal('error-message','index.php?opcion=planillasAguinaldosPersonal');
      }
    }
  });
}
function CerrarPlanillaAguinaldos(cod_planilla){
  $.ajax({
    type:"POST",
    data:"cod_planilla="+cod_planilla+"&sw=3",
    url:"planillas/savePlanillaAguinaldos.php",
    success:function(r){
      if(r==1){
        //$('#tabla1').load('activosFijos/afEnCustodia.php');
        //alertify.success("agregado");
        alerts.showSwal('success-message','index.php?opcion=planillasAguinaldosPersonal');
      }
    }
  });
}
function ReprocesarPlanillaAguialdosNA(cod_planilla,cod_uo){
  $.ajax({
    type:"POST",
    data:"cod_planilla="+cod_planilla+"&sw=1&cod_uo="+cod_uo,
    url:"planillas/savePlanillaAguialdosNA.php",
    beforeSend:function(objeto){ 
      $('#cargaRNA').css({display:'block'});
      $('#AceptarReProcesoNA').css({display:'none'});
      $('#CancelarReProcesoNA').css({display:'none'});  
    },
    success:function(r){
      if(r==1){
        $('#cargaRNA').css('display','none');
        alerts.showSwal('success-message','index.php?opcion=planillasAguinaldosPersonal');
      }else{
        $('#cargaRNA').css('display','none');
        alerts.showSwal('error-message','index.php?opcion=planillasAguinaldosPersonal');
      }
    }
  });
}
function CerrarPlanillaAguinaldosNA(cod_planilla){
  $.ajax({
    type:"POST",
    data:"cod_planilla="+cod_planilla+"&sw=3&cod_uo="+cod_uo,
    url:"planillas/savePlanillaAguialdosNA.php",
    success:function(r){
      if(r==1){
        //$('#tabla1').load('activosFijos/afEnCustodia.php');
        //alertify.success("agregado");
        alerts.showSwal('success-message','index.php?opcion=planillasAguinaldosPersonal');
      }
    }
  });
}

function activarInputMonto(fila){
  if(!($("#monto_mod"+fila).is("[readonly]"))){
    $("#monto_mod"+fila).attr("readonly",true);
    $("#monto_modal"+fila).attr("readonly",true);
  }else{
    $("#monto_mod"+fila).removeAttr("readonly");
    $("#monto_modal"+fila).removeAttr("readonly");
  }
  calcularTotalPartida(1);
}
//=======
function calcularTotalPartida(valor){
  var suma=0;
  var total= $("#numero_cuentas").val();
  var monto_anterior=parseFloat($("#monto_designado").val());
  for (var i=1;i<=(total-1);i++){
    if(!($("#monto_mod"+i).is("[readonly]"))){
    if(valor==1){
      suma+=parseFloat($("#monto_mod"+i).val());
    }else{
     if($("#cod_ibnorca").val()==1){
         $("#monto_mod"+i).val(parseFloat($("#monto_modal"+i).val())*parseInt($("#alumnos_plan").val()));
       }else{
          $("#monto_mod"+i).val(parseFloat($("#monto_modal"+i).val())*parseInt($("#alumnos_plan_fuera").val()));
       }
     suma+=parseFloat($("#monto_mod"+i).val());  
    }
    }
   
  }
  const rest=Math.abs(suma-monto_anterior);
  const porcent=(rest*100)/monto_anterior; 
  var resultPorcent= Math.round(porcent*100)/100;  
  var result=redondeo((suma*100)/100);
  document.getElementById("monto_editable").value=result;


  if(result<monto_anterior){
    $("#monto_editable").addClass("text-danger");
    $("#monto_editable").removeClass("text-success");
    $("#monto_editable_text").addClass("text-danger");
    $("#monto_editable_text").removeClass("text-success");
    $("#monto_editable_text").text("- "+resultPorcent+" %");
  }else{
    if(result>monto_anterior){
       $("#monto_editable").addClass("text-success");
       $("#monto_editable").removeClass("text-danger");
       $("#monto_editable_text").addClass("text-success");
       $("#monto_editable_text").removeClass("text-danger");
       $("#monto_editable_text").text("+ "+resultPorcent+" %");
    }else{
       $("#monto_editable").removeClass("text-success");
       $("#monto_editable").removeClass("text-danger");
       $("#monto_editable_text").removeClass("text-success");
       $("#monto_editable_text").removeClass("text-danger");
       $("#monto_editable_text").text("");
    }
  }
}

function activarInputMontoGenericoPartida(j){
  if($("#habilitar"+j).is("[checked]")){
    $("#habilitar"+j).removeAttr("checked");
    var valor=1;
  }else{
    $("#habilitar"+j).attr("checked",true);
    var valor=0;
  }
  var ni=$("#numero_cuentas"+j).val();
  for (var i = 1; i <= ni; i++) {
    activarInputMontoGenericoPar(j+"RRR"+i,valor);
  };
}
function activarInputMontoGenericoPartidaServicio(j){
  if($("#habilitar"+j).is("[checked]")){
    $("#habilitar"+j).removeAttr("checked");
  }else{
    $("#habilitar"+j).attr("checked",true);
  }
  var ni=$("#numero_cuentas"+j).val();
  for (var i = 1; i <= ni; i++) {
    activarInputMontoGenericoServicio(j+"RRR"+i);
    if($("#habilitar"+j).is("[checked]")){
      if(!($("#habilitar"+j+"RRR"+i).is("[checked]"))){
        $("#habilitar"+j+"RRR"+i).attr("checked",true);
      }
    }else{
      if(($("#habilitar"+j+"RRR"+i).is("[checked]"))){
        $("#habilitar"+j+"RRR"+i).removeAttr("checked");
      }
    }
  };
}
//lista de partidas simulaciones
function activarInputMontoGenericoPar(matriz,valor){
  if(valor==1){
    $("#monto_mod"+matriz).attr("readonly",true);
    $("#monto_modal"+matriz).attr("readonly",true);
    if(($("#habilitar"+matriz).is("[checked]"))){
        $("#habilitar"+matriz).removeAttr("checked");
      }
  }else{
    $("#monto_mod"+matriz).removeAttr("readonly");
    $("#monto_modal"+matriz).removeAttr("readonly");
    if(!($("#habilitar"+matriz).is("[checked]"))){
      }
  }
  var respu= matriz.split('RRR');
  calcularTotalPartidaGenerico(respu[0],1);
}

function activarInputMontoGenerico(matriz){
  if(!($("#monto_mod"+matriz).is("[readonly]"))){
    $("#monto_mod"+matriz).attr("readonly",true);
    $("#monto_modal"+matriz).attr("readonly",true);
    if(($("#habilitar"+matriz).is("[checked]"))){
        $("#habilitar"+matriz).removeAttr("checked");
      
      }
  }else{
    $("#monto_mod"+matriz).removeAttr("readonly");
    $("#monto_modal"+matriz).removeAttr("readonly");
    if(!($("#habilitar"+matriz).is("[checked]"))){
        $("#habilitar"+matriz).attr("checked",true);
     
      }
  }
  var respu= matriz.split('RRR');
  calcularTotalPartidaGenerico(respu[0],2);
}
function activarInputMontoGenericoNorma(matriz){
  if(!($("#monto_norma"+matriz).is("[readonly]"))){
    $("#monto_norma"+matriz).attr("readonly",true);
    if(($("#habilitar_norma"+matriz).is("[checked]"))){
        $("#habilitar_norma"+matriz).removeAttr("checked");
      
      }
  }else{
    $("#monto_norma"+matriz).removeAttr("readonly");
    if(!($("#habilitar_norma"+matriz).is("[checked]"))){
        $("#habilitar_norma"+matriz).attr("checked",true);
     
      }
  }
}
/*function activarInputMontoGenericoServicio(matriz){
  if(!($("#monto_mod"+matriz).is("[readonly]"))){
    $("#monto_mod"+matriz).attr("readonly",true);
    $("#monto_modal"+matriz).attr("readonly",true);
  }else{
    $("#monto_mod"+matriz).removeAttr("readonly");
    $("#monto_modal"+matriz).removeAttr("readonly");
  }
  var respu= matriz.split('RRR');
  calcularTotalPartidaGenericoServicio(respu[0],1);
}*/
function activarInputMontoGenericoServicio(anio,matriz){
  if(!($("#monto_mod"+anio+"QQQ"+matriz).is("[readonly]"))){
    $("#monto_mod"+anio+"QQQ"+matriz).attr("readonly",true);
    $("#monto_modal"+anio+"QQQ"+matriz).attr("readonly",true);
    $("#monto_modUSD"+anio+"QQQ"+matriz).attr("readonly",true);
    $("#monto_modalUSD"+anio+"QQQ"+matriz).attr("readonly",true);
  }else{
    $("#monto_mod"+anio+"QQQ"+matriz).removeAttr("readonly");
    $("#monto_modal"+anio+"QQQ"+matriz).removeAttr("readonly");
    $("#monto_modUSD"+anio+"QQQ"+matriz).removeAttr("readonly");
    $("#monto_modalUSD"+anio+"QQQ"+matriz).removeAttr("readonly");
  }
  var respu= matriz.split('RRR');
  calcularTotalPartidaGenericoServicio(anio,respu[0],1);
}
function activarInputMontoPersonalServicio(anio,fila){
  if(!($("#modal_montopretotal"+anio+"FFF"+fila).is("[readonly]"))){
    $("#modal_montopretotal"+anio+"FFF"+fila).attr("readonly",true);
    $("#modal_montopre"+anio+"FFF"+fila).attr("readonly",true);
    $("#modal_montopretotalUSD"+anio+"FFF"+fila).attr("readonly",true);
    $("#modal_montopreUSD"+anio+"FFF"+fila).attr("readonly",true);

    $("#dias_personal"+anio+"FFF"+fila).attr("readonly",true);

    /*$("#modal_montopretotal"+anio+"FFF"+fila).attr("type","hidden");
    $("#modal_montopre"+anio+"FFF"+fila).attr("type","hidden");
    $("#modal_montopretotalUSD"+anio+"FFF"+fila).attr("type","hidden");
    $("#modal_montopreUSD"+anio+"FFF"+fila).attr("type","hidden");

    $("#modal_montopretotalOFF"+anio+"FFF"+fila).attr("type","text");
    $("#modal_montopreOFF"+anio+"FFF"+fila).attr("type","text");
    $("#modal_montopretotalUSDOFF"+anio+"FFF"+fila).attr("type","text");
    $("#modal_montopreUSDOFF"+anio+"FFF"+fila).attr("type","text");*/

  }else{
    $("#modal_montopretotal"+anio+"FFF"+fila).removeAttr("readonly");
    $("#modal_montopre"+anio+"FFF"+fila).removeAttr("readonly");
    $("#modal_montopretotalUSD"+anio+"FFF"+fila).removeAttr("readonly");
    $("#modal_montopreUSD"+anio+"FFF"+fila).removeAttr("readonly");

    $("#dias_personal"+anio+"FFF"+fila).removeAttr("readonly");

   //PARA HABILITAR VALOR 0 en honorarios personal
    /*$("#modal_montopretotal"+anio+"FFF"+fila).attr("type","text");
    $("#modal_montopre"+anio+"FFF"+fila).attr("type","text");
    $("#modal_montopretotalUSD"+anio+"FFF"+fila).attr("type","text");
    $("#modal_montopreUSD"+anio+"FFF"+fila).attr("type","text");

    $("#modal_montopretotalOFF"+anio+"FFF"+fila).attr("type","hidden");
    $("#modal_montopreOFF"+anio+"FFF"+fila).attr("type","hidden");
    $("#modal_montopretotalUSDOFF"+anio+"FFF"+fila).attr("type","hidden");
    $("#modal_montopreUSDOFF"+anio+"FFF"+fila).attr("type","hidden");*/
  }
  calcularTotalPersonalServicio(anio,2);
}
function activarInputsCostosVariables(anio,fila){

  if(!($("#dias_honorario"+anio+"CCCC"+fila).is("[readonly]"))){
     $("#dias_honorario"+anio+"CCCC"+fila).attr("readonly",true);
     $("#monto_honorario"+anio+"CCCC"+fila).attr("readonly",true);
     $("#monto_honorarioUSD"+anio+"CCCC"+fila).attr("readonly",true);
     if($("#codigo_filatipoauditor"+anio+"CCCC"+fila).val()!=-100){
    //deshabilitar valor 0
     $("#dias_honorario"+anio+"CCCC"+fila).attr("type","hidden");
     $("#dias_honorarioOFF"+anio+"CCCC"+fila).attr("type","number");
     $("#monto_honorario"+anio+"CCCC"+fila).attr("type","hidden");
     $("#monto_honorarioOFF"+anio+"CCCC"+fila).attr("type","number");
     $("#monto_honorarioUSD"+anio+"CCCC"+fila).attr("type","hidden");
     $("#monto_honorarioUSDOFF"+anio+"CCCC"+fila).attr("type","number");
     $("#monto_honorarioTotal"+anio+"CCCC"+fila).attr("type","hidden");
     $("#monto_honorarioTotalOFF"+anio+"CCCC"+fila).attr("type","number");
     $("#monto_honorarioTotalUSD"+anio+"CCCC"+fila).attr("type","hidden");
     $("#monto_honorarioTotalUSDOFF"+anio+"CCCC"+fila).attr("type","number");   
     }
    var columnas =$("#cantidad_columnas"+anio+"CCCC"+fila).val();
    for (var j = 1; j <=columnas; j++) {
      $("#modal_dias_personalItem"+anio+"CCCC"+j+"RRR"+fila).attr("readonly",true);

      $("#modal_dias_personalItem"+anio+"CCCC"+j+"RRR"+fila).attr("type","hidden");
      $("#modal_dias_personalItemOFF"+anio+"CCCC"+j+"RRR"+fila).attr("type","number");

      $("#monto"+anio+"CCCC"+j+"RRR"+fila).attr("type","hidden");
      $("#montoOFF"+anio+"CCCC"+j+"RRR"+fila).attr("type","number");
      $("#montoUSD"+anio+"CCCC"+j+"RRR"+fila).attr("type","hidden");
      $("#montoUSDOFF"+anio+"CCCC"+j+"RRR"+fila).attr("type","number");
      if($("#modal_dias_personalItem"+anio+"CCCC"+j+"RRR"+fila).val()>0){
        
      }
      $("#monto_mult"+anio+"CCCC"+j+"RRR"+fila).attr("type","hidden");
      $("#monto_multOFF"+anio+"CCCC"+j+"RRR"+fila).attr("type","number");
      $("#monto_multUSD"+anio+"CCCC"+j+"RRR"+fila).attr("type","hidden");
      $("#monto_multUSDOFF"+anio+"CCCC"+j+"RRR"+fila).attr("type","number");
    }; 

  }else{
      $("#dias_honorario"+anio+"CCCC"+fila).removeAttr("readonly");
      $("#monto_honorario"+anio+"CCCC"+fila).removeAttr("readonly");
      $("#monto_honorarioUSD"+anio+"CCCC"+fila).removeAttr("readonly");
     if($("#codigo_filatipoauditor"+anio+"CCCC"+fila).val()!=-100){   
    //habilitar valor 0
      $("#dias_honorario"+anio+"CCCC"+fila).attr("type","number");
      $("#dias_honorarioOFF"+anio+"CCCC"+fila).attr("type","hidden");
      $("#monto_honorario"+anio+"CCCC"+fila).attr("type","number");
      $("#monto_honorarioOFF"+anio+"CCCC"+fila).attr("type","hidden");
      $("#monto_honorarioUSD"+anio+"CCCC"+fila).attr("type","number");
      $("#monto_honorarioUSDOFF"+anio+"CCCC"+fila).attr("type","hidden");
      $("#monto_honorarioTotal"+anio+"CCCC"+fila).attr("type","number");
      $("#monto_honorarioTotalOFF"+anio+"CCCC"+fila).attr("type","hidden");
      $("#monto_honorarioTotalUSD"+anio+"CCCC"+fila).attr("type","number");
      $("#monto_honorarioTotalUSDOFF"+anio+"CCCC"+fila).attr("type","hidden");    
     }

    var columnas =$("#cantidad_columnas"+anio+"CCCC"+fila).val();
    for (var j = 1; j <=columnas; j++) {
      $("#modal_dias_personalItem"+anio+"CCCC"+j+"RRR"+fila).removeAttr("readonly");
      $("#modal_dias_personalItem"+anio+"CCCC"+j+"RRR"+fila).attr("type","number");
      $("#modal_dias_personalItemOFF"+anio+"CCCC"+j+"RRR"+fila).attr("type","hidden");

      $("#monto"+anio+"CCCC"+j+"RRR"+fila).attr("type","number");
      $("#montoOFF"+anio+"CCCC"+j+"RRR"+fila).attr("type","hidden");
      $("#montoUSD"+anio+"CCCC"+j+"RRR"+fila).attr("type","number");
      $("#montoUSDOFF"+anio+"CCCC"+j+"RRR"+fila).attr("type","hidden");
      if($("#modal_dias_personalItem"+anio+"CCCC"+j+"RRR"+fila).val()<=0){
        
      }
      $("#monto_mult"+anio+"CCCC"+j+"RRR"+fila).attr("type","number");
      $("#monto_multOFF"+anio+"CCCC"+j+"RRR"+fila).attr("type","hidden");
      $("#monto_multUSD"+anio+"CCCC"+j+"RRR"+fila).attr("type","number");
      $("#monto_multUSDOFF"+anio+"CCCC"+j+"RRR"+fila).attr("type","hidden");
    }; 
  }
    
  calcularTotalPersonalServicioAuditor(anio);
  calcularTotalPersonalServicioAuditorHonorarios(anio)
  //calcularTotalPersonalServicio(anio,2);
}

function activarInputMontoFilaServicio(anio,fila){
  if(!($("#modal_montoservtotal"+anio+"SSS"+fila).is("[readonly]"))){
    $("#modal_montoservtotal"+anio+"SSS"+fila).attr("readonly",true);
    $("#modal_montoserv"+anio+"SSS"+fila).attr("readonly",true);
    $("#modal_montoservtotalUSD"+anio+"SSS"+fila).attr("readonly",true);
    $("#modal_montoservUSD"+anio+"SSS"+fila).attr("readonly",true);

    $("#modal_montoservtotal"+anio+"SSS"+fila).attr("type","hidden");
    $("#modal_montoserv"+anio+"SSS"+fila).attr("type","hidden");
    $("#modal_montoservtotalUSD"+anio+"SSS"+fila).attr("type","hidden");
    $("#modal_montoservUSD"+anio+"SSS"+fila).attr("type","hidden");

    $("#modal_montoservtotalOFF"+anio+"SSS"+fila).attr("type","text");
    $("#modal_montoservOFF"+anio+"SSS"+fila).attr("type","text");
    $("#modal_montoservtotalUSDOFF"+anio+"SSS"+fila).attr("type","text");
    $("#modal_montoservUSDOFF"+anio+"SSS"+fila).attr("type","text");
  }else{
    $("#modal_montoservtotal"+anio+"SSS"+fila).removeAttr("readonly");
    $("#modal_montoserv"+anio+"SSS"+fila).removeAttr("readonly");
    $("#modal_montoservtotalUSD"+anio+"SSS"+fila).removeAttr("readonly");
    $("#modal_montoservUSD"+anio+"SSS"+fila).removeAttr("readonly");

    $("#modal_montoservtotal"+anio+"SSS"+fila).attr("type","text");
    $("#modal_montoserv"+anio+"SSS"+fila).attr("type","text");
    $("#modal_montoservtotalUSD"+anio+"SSS"+fila).attr("type","text");
    $("#modal_montoservUSD"+anio+"SSS"+fila).attr("type","text");

    $("#modal_montoservtotalOFF"+anio+"SSS"+fila).attr("type","hidden");
    $("#modal_montoservOFF"+anio+"SSS"+fila).attr("type","hidden");
    $("#modal_montoservtotalUSDOFF"+anio+"SSS"+fila).attr("type","hidden");
    $("#modal_montoservUSDOFF"+anio+"SSS"+fila).attr("type","hidden");
  }
  calcularTotalFilaServicio(anio,2);
}


function cambiarCantidadMontoGenericoServicio(matriz){
  var respu= matriz.split('RRR');
  calcularTotalPartidaGenericoServicio(respu[0],2);
}

function calcularTotalPartidaGenericoServicio(anio,fila,valor){
  var suma=0; var sumal=0;
  var sumaUSD=0; var sumalUSD=0;
  var usd=$("#cambio_moneda").val();
  var total= $("#numero_cuentas"+anio+"QQQ"+fila).val();
  var monto_anterior=parseFloat($("#monto_designado"+anio+"QQQ"+fila).val());
  for (var i=1;i<=(total-1);i++){
    if(!($("#monto_mod"+anio+"QQQ"+fila+"RRR"+i).is("[readonly]"))){
    if(valor==1){
      var montoAsignado=redondeo(parseFloat($("#monto_mod"+anio+"QQQ"+fila+"RRR"+i).val())/parseInt($("#cantidad_personal"+anio+"QQQ"+fila+"RRR"+i).val()));

      $("#monto_modal"+anio+"QQQ"+fila+"RRR"+i).val(montoAsignado);
      $("#monto_modUSD"+anio+"QQQ"+fila+"RRR"+i).val(redondeo((parseFloat($("#monto_mod"+anio+"QQQ"+fila+"RRR"+i).val()))/parseFloat(usd)));
      $("#monto_modalUSD"+anio+"QQQ"+fila+"RRR"+i).val(redondeo($("#monto_modal"+anio+"QQQ"+fila+"RRR"+i).val()/parseFloat(usd)));
    }else{
      if(valor==2){
        var montoAsignado=redondeo(parseFloat($("#monto_modal"+anio+"QQQ"+fila+"RRR"+i).val())*parseInt($("#cantidad_personal"+anio+"QQQ"+fila+"RRR"+i).val()));   
       
       $("#monto_mod"+anio+"QQQ"+fila+"RRR"+i).val(montoAsignado);
       $("#monto_modUSD"+anio+"QQQ"+fila+"RRR"+i).val(redondeo($("#monto_mod"+anio+"QQQ"+fila+"RRR"+i).val()/parseFloat(usd)));
       $("#monto_modalUSD"+anio+"QQQ"+fila+"RRR"+i).val(redondeo((parseFloat($("#monto_modal"+anio+"QQQ"+fila+"RRR"+i).val()))/parseFloat(usd)));
      }else{
        if(valor==3){
         var montoAsignado=redondeo(parseFloat($("#monto_modUSD"+anio+"QQQ"+fila+"RRR"+i).val())/parseInt($("#cantidad_personal"+anio+"QQQ"+fila+"RRR"+i).val()));

         $("#monto_modalUSD"+anio+"QQQ"+fila+"RRR"+i).val(montoAsignado);
         $("#monto_mod"+anio+"QQQ"+fila+"RRR"+i).val(redondeo((parseFloat($("#monto_modUSD"+anio+"QQQ"+fila+"RRR"+i).val()))*parseFloat(usd)));
         $("#monto_modal"+anio+"QQQ"+fila+"RRR"+i).val(redondeo($("#monto_modalUSD"+anio+"QQQ"+fila+"RRR"+i).val()/parseFloat(usd)));
        }else{
          var montoAsignado=redondeo(parseFloat($("#monto_modalUSD"+anio+"QQQ"+fila+"RRR"+i).val())*parseInt($("#cantidad_personal"+anio+"QQQ"+fila+"RRR"+i).val()));   
       
          $("#monto_modUSD"+anio+"QQQ"+fila+"RRR"+i).val(montoAsignado);
          $("#monto_mod"+anio+"QQQ"+fila+"RRR"+i).val(redondeo($("#monto_modUSD"+anio+"QQQ"+fila+"RRR"+i).val()*parseFloat(usd)));
          $("#monto_modal"+anio+"QQQ"+fila+"RRR"+i).val(redondeo((parseFloat($("#monto_modalUSD"+anio+"QQQ"+fila+"RRR"+i).val()))*parseFloat(usd)));
        }
      }
        
    }
     suma+=parseFloat($("#monto_mod"+anio+"QQQ"+fila+"RRR"+i).val());
     sumal+=parseFloat($("#monto_modal"+anio+"QQQ"+fila+"RRR"+i).val());
     sumalUSD+=parseFloat($("#monto_modalUSD"+anio+"QQQ"+fila+"RRR"+i).val());
     sumaUSD+=parseFloat($("#monto_modUSD"+anio+"QQQ"+fila+"RRR"+i).val());
    }
   
  }
  const rest=Math.abs(suma-monto_anterior);
  const porcent=(rest*100)/monto_anterior; 
  var resultPorcent= Math.round(porcent*100)/100;  
  var result=redondeo((suma*100)/100);
  var resulta=redondeo((sumal*100)/100);
  document.getElementById("monto_editable"+anio+"QQQ"+fila).value=result;
  $("#total_tabladetalle"+anio+"QQQ"+fila).text(result);
  $("#total_tabladetalleAl"+anio+"QQQ"+fila).text(redondeo(resulta)); 
  if(result<monto_anterior){
    $("#monto_editable"+anio+"QQQ"+fila).addClass("text-danger");
    $("#monto_editable"+anio+"QQQ"+fila).removeClass("text-success");
  }else{
    if(result>monto_anterior){
       $("#monto_editable"+anio+"QQQ"+fila).addClass("text-success");
       $("#monto_editable"+anio+"QQQ"+fila).removeClass("text-danger");
    }else{
       $("#monto_editable"+anio+"QQQ"+fila).removeClass("text-success");
       $("#monto_editable"+anio+"QQQ"+fila).removeClass("text-danger");
    }
  }
}

function calcularTotalPersonalServicio(anio,valor){
  var suma=0; var sumal=0; var sumaC=0;
  var sumae=0; var sumale=0;
  var sumaUSD=0; var sumalUSD=0;
  /*var total= $("#modal_numeropersonal"+anio).val();
  for (var i=1;i<=(total-1);i++){
    if(!($("#modal_montopretotal"+anio+"FFF"+i).is("[readonly]"))){
    if(valor==1){
      suma+=parseFloat($("#modal_montopretotal"+anio+"FFF"+i).val());
      
      $("#modal_montopre"+anio+"FFF"+i).val(redondeo(parseFloat($("#modal_montopretotal"+anio+"FFF"+i).val())/parseInt($("#cantidad_personal"+anio+"FFF"+i).val())/parseInt($("#dias_personal"+anio+"FFF"+i).val())));
     
    }else{
      
      $("#modal_montopretotal"+anio+"FFF"+i).val(redondeo(parseFloat($("#modal_montopre"+anio+"FFF"+i).val())*parseInt($("#cantidad_personal"+anio+"FFF"+i).val())*parseInt($("#dias_personal"+anio+"FFF"+i).val())));
     suma+=parseFloat($("#modal_montopretotal"+anio+"FFF"+i).val());  
     
    }
     sumal+=parseFloat($("#modal_montopre"+anio+"FFF"+i).val());
     
     sumaC+=parseInt($("#cantidad_personal"+anio+"FFF"+i).val());
    }
   
  } 
  var result=redondeo(suma);
  var resulta=redondeo(sumal);
  
  $("#modal_totalmontopretotal"+anio).text(result);
  $("#modal_cantidadpersonal"+anio).val(sumaC);
  $("#modal_totalmontopre"+anio).text(redondeo(resulta));
  */


 var usd=$("#cambio_moneda").val();
  var total= $("#modal_numeropersonal"+anio).val();
  for (var i=1;i<=(total-1);i++){
    if(!($("#modal_montopretotal"+anio+"FFF"+i).is("[readonly]"))){
    if(valor==1){
      suma+=parseFloat($("#modal_montopretotal"+anio+"FFF"+i).val());
      sumaUSD+=parseFloat($("#modal_montopretotal"+anio+"FFF"+i).val())/parseFloat(usd);
      var montoPoner=redondeo(parseFloat($("#modal_montopretotal"+anio+"FFF"+i).val())/parseInt($("#cantidad_personal"+anio+"FFF"+i).val())/parseInt($("#dias_personal"+anio+"FFF"+i).val()));
      $("#modal_montopre"+anio+"FFF"+i).val(montoPoner);
      $("#modal_montopreUSD"+anio+"FFF"+i).val(redondeo(montoPoner/parseFloat(usd)));
      $("#modal_montopretotalUSD"+anio+"FFF"+i).val(redondeo((parseFloat($("#modal_montopre"+anio+"FFF"+i).val())/parseInt($("#cantidad_personal"+anio+"FFF"+i).val())/parseInt($("#dias_personal"+anio+"FFF"+i).val()))/parseFloat(usd)));
    }else{
      if(valor==2){
      var montoPoner=redondeo(parseFloat($("#modal_montopre"+anio+"FFF"+i).val())*parseInt($("#cantidad_personal"+anio+"FFF"+i).val())*parseInt($("#dias_personal"+anio+"FFF"+i).val()));
      $("#modal_montopretotal"+anio+"FFF"+i).val(montoPoner);
      $("#modal_montopretotalUSD"+anio+"FFF"+i).val(redondeo(montoPoner/parseFloat(usd)));
      $("#modal_montopreUSD"+anio+"FFF"+i).val(redondeo(parseFloat($("#modal_montopre"+anio+"FFF"+i).val())/parseFloat(usd)));
     suma+=parseFloat($("#modal_montopretotal"+anio+"FFF"+i).val());
     sumaUSD+=parseFloat($("#modal_montopretotal"+anio+"FFF"+i).val())/parseFloat(usd);  
      }else{
        if(valor==3){
          sumaUSD+=parseFloat($("#modal_montopretotalUSD"+anio+"FFF"+i).val());
          suma+=parseFloat($("#modal_montopretotalUSD"+anio+"FFF"+i).val())*parseFloat(usd);  
          var montoPoner=redondeo(parseFloat($("#modal_montopretotalUSD"+anio+"FFF"+i).val())/parseInt($("#cantidad_personal"+anio+"FFF"+i).val())/parseInt($("#dias_personal"+anio+"FFF"+i).val()));
          $("#modal_montopreUSD"+anio+"FFF"+i).val(montoPoner);
          $("#modal_montopre"+anio+"FFF"+i).val(redondeo(montoPoner*parseFloat(usd)));
          $("#modal_montopretotal"+anio+"FFF"+i).val(redondeo((parseFloat($("#modal_montopre"+anio+"FFF"+i).val()))/parseInt($("#cantidad_personal"+anio+"FFF"+i).val())/parseInt($("#dias_personal"+anio+"FFF"+i).val())));
        }else{
          var montoPoner=redondeo(parseFloat($("#modal_montopreUSD"+anio+"FFF"+i).val())*parseInt($("#cantidad_personal"+anio+"FFF"+i).val())*parseInt($("#dias_personal"+anio+"FFF"+i).val()));
          $("#modal_montopretotalUSD"+anio+"FFF"+i).val(montoPoner);
          $("#modal_montopretotal"+anio+"FFF"+i).val(redondeo(montoPoner*parseFloat(usd)));
          $("#modal_montopre"+anio+"FFF"+i).val(redondeo(parseFloat($("#modal_montopreUSD"+anio+"FFF"+i).val())*parseFloat(usd)));
          sumaUSD+=parseFloat($("#modal_montopretotalUSD"+anio+"FFF"+i).val());
          suma+=parseFloat($("#modal_montopretotalUSD"+anio+"FFF"+i).val())*parseFloat(usd);  
        }
      }
    }
     sumal+=parseFloat($("#modal_montopre"+anio+"FFF"+i).val());
     sumalUSD+=parseFloat($("#modal_montopreUSD"+anio+"FFF"+i).val());
      sumaC+=parseInt($("#cantidad_personal"+anio+"FFF"+i).val());

    }
   
  } 
  var result=redondeo(suma);
  var resulta=redondeo(sumal);
  var resultUSD=redondeo(sumaUSD);
  var resultaUSD=redondeo(sumalUSD);
  $("#modal_totalmontopretotal"+anio).text(result);
  $("#modal_totalmontopre"+anio).text(redondeo(resulta));
  $("#modal_totalmontopretotalUSD"+anio).text(resultUSD);
  $("#modal_totalmontopreUSD"+anio).text(redondeo(resultaUSD)); 
  $("#modal_cantidadpersonal"+anio).val(sumaC);
  calcularTotalesAuditor();
}
function calcularTotalesAuditor(){
  if($("#codigo_area").val()==39){
    var inicio=1;
  }else{
    var inicio=0;
  }
  var suma=0;var sumaUSD=0;
  for (var i = inicio; i <= parseInt($("#anio_simulacion").val()); i++) {
   suma+= parseFloat($("#modal_totalmontopretotal"+i).text());
   sumaUSD+= parseFloat($("#modal_totalmontopretotalUSD"+i).text());
  };
  $("#suma_totalpre").text(redondeo(suma)+ " Bs.");
  $("#suma_totalpreUSD").text(redondeo(sumaUSD)+ " USD.");
}

function calcularTotalPersonalServicioAuditorHonorariosSingle(anio,fila,valor){
  var usd= $("#cambio_moneda").val();
  if(valor==1){
    var monto =redondeo(parseFloat($("#monto_honorario"+anio+"CCCC"+fila).val())/parseFloat(usd));
    $("#monto_honorarioUSD"+anio+"CCCC"+fila).val(monto);
  }else{
    var monto =redondeo(parseFloat($("#monto_honorarioUSD"+anio+"CCCC"+fila).val())*parseFloat(usd));
    $("#monto_honorario"+anio+"CCCC"+fila).val(monto);
  }
  calcularTotalPersonalServicioAuditorHonorarios(anio)
}
function calcularTotalPersonalServicioAuditorHonorarios(anio){
  var suma=0; var sumal=0;
  var total= $("#modal_numeropersonalauditor"+anio).val();
  var usd= $("#cambio_moneda").val();
  for (var i=1;i<=(total-1);i++){
    if(!($("#dias_honorario"+anio+"CCCC"+i).is("[readonly]"))){
     suma=redondeo(parseFloat($("#dias_honorario"+anio+"CCCC"+i).val())*parseFloat($("#monto_honorario"+anio+"CCCC"+i).val()));
     $("#monto_honorarioTotal"+anio+"CCCC"+i).val(redondeo(suma));
     $("#monto_honorarioTotalUSD"+anio+"CCCC"+i).val(redondeo(suma/parseFloat(usd)));
     sumal+=suma;
    }
  } 

  var resulta=redondeo(sumal);
  $("#total_auditorvariable"+anio).text(resulta);
  $("#total_auditorvariableUSD"+anio).text(redondeo(resulta/parseFloat(usd)));
 
  if($("#codigo_area").val()==39){
    ponerCantidadTotalesVariablesModal(1,$("#anio_simulacion").val());
  }else{
    ponerCantidadTotalesVariablesModal(0,$("#anio_simulacion").val());
  }
  
  calcularTotalPersonalServicioAuditor(anio);
}

function calcularTotalPersonalServicioAuditorDolar(val1,val2,val3){
  var usd= $("#cambio_moneda").val();
  var monto = parseFloat($("#montoUSD"+val1+"CCCC"+val2+"RRR"+val3).val());
  $("#monto"+val1+"CCCC"+val2+"RRR"+val3).val(redondeo(monto*usd));
  calcularTotalPersonalServicioAuditor(val1);
}
function calcularTotalPersonalServicioAuditor(anio){
  var suma=0; var sumal=0; var sumaC=0;
  var sumae=0; var sumale=0;
  var total= $("#modal_numeropersonalauditor"+anio).val();
  var usd= $("#cambio_moneda").val();
  var totalesItem=[];
  var columnas =$("#cantidad_columnas"+anio+"CCCC1").val();
  for (var j = 1; j <=columnas; j++) {
     totalesItem[j-1]=0; 
  }
  for (var i=1;i<=(total-1);i++){
    var columnas =$("#cantidad_columnas"+anio+"CCCC"+i).val();
    var montos=0;var montose=0; var honorariosFila=0;
    var extlocal=$("#modal_local_extranjero"+anio+"CCCC"+i).val();
    for (var j = 1; j <=columnas; j++) {
      if(!($("#modal_dias_personalItem"+anio+"CCCC"+j+"RRR"+i).is("[readonly]"))){
       if(extlocal==1){
         $("#monto_mult"+anio+"CCCC"+j+"RRR"+i).val(redondeo(($("#modal_cantidad_personal"+anio+"CCCC"+i).val()*$("#modal_dias_personalItem"+anio+"CCCC"+j+"RRR"+i).val())*parseFloat($("#monto"+anio+"CCCC"+j+"RRR"+i).val())));
         $("#monto_multUSD"+anio+"CCCC"+j+"RRR"+i).val(redondeo($("#monto_mult"+anio+"CCCC"+j+"RRR"+i).val()/parseFloat(usd)));          
          montos+=parseFloat($("#monto_mult"+anio+"CCCC"+j+"RRR"+i).val());
          totalesItem[j-1]+=redondeo($("#monto_mult"+anio+"CCCC"+j+"RRR"+i).val());
       }else{
         $("#monto_mult"+anio+"CCCC"+j+"RRR"+i).val(redondeo(($("#modal_cantidad_personal"+anio+"CCCC"+i).val()*$("#modal_dias_personalItem"+anio+"CCCC"+j+"RRR"+i).val())*parseFloat($("#montoext"+anio+"CCCC"+j+"RRR"+i).val())));     
          montos+=parseFloat($("#monto_mult"+anio+"CCCC"+j+"RRR"+i).val());
          $("#monto_multUSD"+anio+"CCCC"+j+"RRR"+i).val(redondeo($("#monto_mult"+anio+"CCCC"+j+"RRR"+i).val()/parseFloat(usd)));
          totalesItem[j-1]+=redondeo($("#monto_mult"+anio+"CCCC"+j+"RRR"+i).val());
       } 
       $("#montoUSD"+anio+"CCCC"+j+"RRR"+i).val(redondeo(parseFloat($("#monto"+anio+"CCCC"+j+"RRR"+i).val())/usd));
       //deshabilitar o habilitar montos
       if($("#modal_dias_personalItem"+anio+"CCCC"+j+"RRR"+i).val()<=0){
        $("#monto"+anio+"CCCC"+j+"RRR"+i).attr("type","hidden");
        $("#montoOFF"+anio+"CCCC"+j+"RRR"+i).attr("type","number");
        $("#montoUSD"+anio+"CCCC"+j+"RRR"+i).attr("type","hidden");
        $("#montoUSDOFF"+anio+"CCCC"+j+"RRR"+i).attr("type","number");

        $("#monto_mult"+anio+"CCCC"+j+"RRR"+i).attr("type","hidden");
        $("#monto_multOFF"+anio+"CCCC"+j+"RRR"+i).attr("type","number");
        $("#monto_multUSD"+anio+"CCCC"+j+"RRR"+i).attr("type","hidden");
        $("#monto_multUSDOFF"+anio+"CCCC"+j+"RRR"+i).attr("type","number");
       }else{
        $("#montoOFF"+anio+"CCCC"+j+"RRR"+i).attr("type","hidden");
        $("#monto"+anio+"CCCC"+j+"RRR"+i).attr("type","number");
        $("#montoUSDOFF"+anio+"CCCC"+j+"RRR"+i).attr("type","hidden");
        $("#montoUSD"+anio+"CCCC"+j+"RRR"+i).attr("type","number");
        $("#monto_mult"+anio+"CCCC"+j+"RRR"+i).attr("type","number");
        $("#monto_multOFF"+anio+"CCCC"+j+"RRR"+i).attr("type","hidden");
        $("#monto_multUSD"+anio+"CCCC"+j+"RRR"+i).attr("type","number");
        $("#monto_multUSDOFF"+anio+"CCCC"+j+"RRR"+i).attr("type","hidden");
       } 
      }
     //montose+=parseFloat($("#monto_multext"+j+"RRR"+i).val());  

    };
    if(!($("#dias_honorario"+anio+"CCCC"+i).is("[readonly]"))){
      honorariosFila=parseFloat($("#monto_honorarioTotal"+anio+"CCCC"+i).val());  
    } 
    suma=montos + honorariosFila;
     //suma=parseFloat($("#modal_cantidad_personal"+i).val()*$("#modal_dias_personal"+i).val()*montos);
     
     //sumae=montose;
     $("#total_auditor"+anio+"CCCC"+i).text(redondeo(suma));
     $("#total_auditorUSD"+anio+"CCCC"+i).text(redondeo(suma/parseFloat(usd)));
     //$("#total_unitarioauditor"+i).text(redondeo(suma/($("#modal_cantidad_personal"+i).val()*$("#modal_dias_personal"+i).val()))); 
     sumal+=suma;
     //sumale+=sumae;
     //sumaC+=suma/(($("#modal_cantidad_personal"+i).val()*$("#modal_dias_personal"+i).val())); 
  } 
  for (var j = 1; j <=columnas; j++) {
     $("#total_item"+anio+"CCCC"+j).text(redondeo(totalesItem[j-1]));
     $("#total_itemUSD"+anio+"CCCC"+j).text(redondeo(totalesItem[j-1]/parseFloat(usd)));
  }
  var resulta=redondeo(sumal);
  $("#total_auditor"+anio).text(resulta);
  $("#total_auditorUSD"+anio).text(redondeo(resulta/parseFloat(usd)));
  //$("#total_unitarioauditor").text(redondeo(sumaC));

  if($("#codigo_area").val()==39){
    ponerCantidadTotalesVariablesModal(1,$("#anio_simulacion").val());
  }else{
    ponerCantidadTotalesVariablesModal(0,$("#anio_simulacion").val());
  }

}
function calcularTotalFilaServicio(anio,valor){
  var suma=0; var sumal=0;
  var sumaUSD=0; var sumalUSD=0;
  var usd=$("#cambio_moneda").val();
  var total= $("#modal_numeroservicio"+anio).val();
  for (var i=1;i<=(total-1);i++){
    if(!($("#modal_montoservtotal"+anio+"SSS"+i).is("[readonly]"))){
    if(valor==1){
      suma+=parseFloat($("#modal_montoservtotal"+anio+"SSS"+i).val());
      sumaUSD+=parseFloat($("#modal_montoservtotal"+anio+"SSS"+i).val())/parseFloat(usd);
      var montoPoner=redondeo(parseFloat($("#modal_montoservtotal"+anio+"SSS"+i).val())/parseFloat($("#cantidad_servicios"+anio+"SSS"+i).val()));
      $("#modal_montoserv"+anio+"SSS"+i).val(montoPoner);
      $("#modal_montoservUSD"+anio+"SSS"+i).val(redondeo(montoPoner/parseFloat(usd)));
      $("#modal_montoservtotalUSD"+anio+"SSS"+i).val(redondeo((parseFloat($("#modal_montoserv"+anio+"SSS"+i).val()))/parseFloat($("#cantidad_servicios"+anio+"SSS"+i).val())/parseFloat(usd)));
    }else{
      if(valor==2){
      var montoPoner=redondeo(parseFloat($("#modal_montoserv"+anio+"SSS"+i).val())*parseFloat($("#cantidad_servicios"+anio+"SSS"+i).val()));
      $("#modal_montoservtotal"+anio+"SSS"+i).val(montoPoner);
      $("#modal_montoservtotalUSD"+anio+"SSS"+i).val(redondeo(montoPoner/parseFloat(usd)));
      $("#modal_montoservUSD"+anio+"SSS"+i).val(redondeo(parseFloat($("#modal_montoserv"+anio+"SSS"+i).val())/parseFloat(usd)));
     suma+=parseFloat($("#modal_montoservtotal"+anio+"SSS"+i).val());
     sumaUSD+=parseFloat($("#modal_montoservtotal"+anio+"SSS"+i).val())/parseFloat(usd);  
      }else{
        if(valor==3){
          sumaUSD+=parseFloat($("#modal_montoservtotalUSD"+anio+"SSS"+i).val());
          suma+=parseFloat($("#modal_montoservtotalUSD"+anio+"SSS"+i).val())*parseFloat(usd);  
          var montoPoner=redondeo(parseFloat($("#modal_montoservtotalUSD"+anio+"SSS"+i).val())/parseFloat($("#cantidad_servicios"+anio+"SSS"+i).val()));
          $("#modal_montoservUSD"+anio+"SSS"+i).val(montoPoner);
          $("#modal_montoserv"+anio+"SSS"+i).val(redondeo(montoPoner*parseFloat(usd)));
          $("#modal_montoservtotal"+anio+"SSS"+i).val(redondeo((parseFloat($("#modal_montoserv"+anio+"SSS"+i).val()))/parseFloat($("#cantidad_servicios"+anio+"SSS"+i).val())));
        }else{
          var montoPoner=redondeo(parseFloat($("#modal_montoservUSD"+anio+"SSS"+i).val())*parseFloat($("#cantidad_servicios"+anio+"SSS"+i).val()));
          $("#modal_montoservtotalUSD"+anio+"SSS"+i).val(montoPoner);
          $("#modal_montoservtotal"+anio+"SSS"+i).val(redondeo(montoPoner*parseFloat(usd)));
          $("#modal_montoserv"+anio+"SSS"+i).val(redondeo(parseFloat($("#modal_montoservUSD"+anio+"SSS"+i).val())*parseFloat(usd)));
          sumaUSD+=parseFloat($("#modal_montoservtotalUSD"+anio+"SSS"+i).val());
          suma+=parseFloat($("#modal_montoservtotalUSD"+anio+"SSS"+i).val())*parseFloat(usd);  
        }
      }
    }
     sumal+=parseFloat($("#modal_montoserv"+anio+"SSS"+i).val());
     sumalUSD+=parseFloat($("#modal_montoservUSD"+anio+"SSS"+i).val());
    }
   
  } 
  var result=redondeo(suma);
  var resulta=redondeo(sumal);
  var resultUSD=redondeo(sumaUSD);
  var resultaUSD=redondeo(sumalUSD);
  $("#modal_totalmontoservtotal"+anio).text(result);
  $("#modal_totalmontoserv"+anio).text(redondeo(resulta));
  $("#modal_totalmontoservtotalUSD"+anio).text(resultUSD);
  $("#modal_totalmontoservUSD"+anio).text(redondeo(resultaUSD)); 
}

function calcularTotalFilaServicioNuevo(anio,valor){
  var i=0;
  var usd=$("#cambio_moneda").val();
  if(valor==1){
      var montoPoner=redondeo(parseFloat($("#modal_montoservtotal"+anio+"SSS"+i).val())/parseFloat($("#cantidad_servicios"+anio+"SSS"+i).val()));
      $("#modal_montoserv"+anio+"SSS"+i).val(montoPoner);
      $("#modal_montoservUSD"+anio+"SSS"+i).val(redondeo(montoPoner/parseFloat(usd)));
      $("#modal_montoservtotalUSD"+anio+"SSS"+i).val(redondeo((parseFloat($("#modal_montoserv"+anio+"SSS"+i).val()))/parseFloat($("#cantidad_servicios"+anio+"SSS"+i).val())/parseFloat(usd)));
    }else{
      if(valor==2){
      var montoPoner=redondeo(parseFloat($("#modal_montoserv"+anio+"SSS"+i).val())*parseFloat($("#cantidad_servicios"+anio+"SSS"+i).val()));
      $("#modal_montoservtotal"+anio+"SSS"+i).val(montoPoner);
      $("#modal_montoservtotalUSD"+anio+"SSS"+i).val(redondeo(montoPoner/parseFloat(usd)));
      $("#modal_montoservUSD"+anio+"SSS"+i).val(redondeo(parseFloat($("#modal_montoserv"+anio+"SSS"+i).val())/parseFloat(usd)));
      }else{
        if(valor==3){ 
          var montoPoner=redondeo(parseFloat($("#modal_montoservtotalUSD"+anio+"SSS"+i).val())/parseFloat($("#cantidad_servicios"+anio+"SSS"+i).val()));
          $("#modal_montoservUSD"+anio+"SSS"+i).val(montoPoner);
          $("#modal_montoserv"+anio+"SSS"+i).val(redondeo(montoPoner*parseFloat(usd)));
          $("#modal_montoservtotal"+anio+"SSS"+i).val(redondeo((parseFloat($("#modal_montoserv"+anio+"SSS"+i).val()))/parseFloat($("#cantidad_servicios"+anio+"SSS"+i).val())));
        }else{
          var montoPoner=redondeo(parseFloat($("#modal_montoservUSD"+anio+"SSS"+i).val())*parseFloat($("#cantidad_servicios"+anio+"SSS"+i).val()));
          $("#modal_montoservtotalUSD"+anio+"SSS"+i).val(montoPoner);
          $("#modal_montoservtotal"+anio+"SSS"+i).val(redondeo(montoPoner*parseFloat(usd)));
          $("#modal_montoserv"+anio+"SSS"+i).val(redondeo(parseFloat($("#modal_montoservUSD"+anio+"SSS"+i).val())*parseFloat(usd))); 
        }
      }
    } 
}

function calcularTotalPersonalServicioNuevo(anio,valor){
  var i=0;
  var usd=$("#cambio_moneda").val();
  if(valor==1){
      var montoPoner=redondeo(parseFloat($("#modal_montopretotal"+anio+"FFF"+i).val())/parseFloat($("#dias_personal"+anio+"FFF"+i).val()));
      $("#modal_montopre"+anio+"FFF"+i).val(montoPoner);
      $("#modal_montopreUSD"+anio+"FFF"+i).val(redondeo(montoPoner/parseFloat(usd)));
      $("#modal_montopretotalUSD"+anio+"FFF"+i).val(redondeo((parseFloat($("#modal_montopre"+anio+"FFF"+i).val()))/parseFloat($("#dias_personal"+anio+"FFF"+i).val())/parseFloat(usd)));
    }else{
      if(valor==2){
      var montoPoner=redondeo(parseFloat($("#modal_montopre"+anio+"FFF"+i).val())*parseFloat($("#dias_personal"+anio+"FFF"+i).val()));
      $("#modal_montopretotal"+anio+"FFF"+i).val(montoPoner);
      $("#modal_montopretotalUSD"+anio+"FFF"+i).val(redondeo(montoPoner/parseFloat(usd)));
      $("#modal_montopreUSD"+anio+"FFF"+i).val(redondeo(parseFloat($("#modal_montopre"+anio+"FFF"+i).val())/parseFloat(usd)));
      }else{
        if(valor==3){ 
          var montoPoner=redondeo(parseFloat($("#modal_montopretotalUSD"+anio+"FFF"+i).val())/parseFloat($("#dias_personal"+anio+"FFF"+i).val()));
          $("#modal_montopreUSD"+anio+"FFF"+i).val(montoPoner);
          $("#modal_montopre"+anio+"FFF"+i).val(redondeo(montoPoner*parseFloat(usd)));
          $("#modal_montopretotal"+anio+"FFF"+i).val(redondeo((parseFloat($("#modal_montopre"+anio+"FFF"+i).val()))/parseFloat($("#dias_personal"+anio+"FFF"+i).val())));
        }else{
          var montoPoner=redondeo(parseFloat($("#modal_montopreUSD"+anio+"FFF"+i).val())*parseFloat($("#dias_personal"+anio+"FFF"+i).val()));
          $("#modal_montopretotalUSD"+anio+"FFF"+i).val(montoPoner);
          $("#modal_montopretotal"+anio+"FFF"+i).val(redondeo(montoPoner*parseFloat(usd)));
          $("#modal_montopre"+anio+"FFF"+i).val(redondeo(parseFloat($("#modal_montopreUSD"+anio+"FFF"+i).val())*parseFloat(usd))); 
        }
      }
    } 
}

function calcularTotalPartidaGenerico(fila,valor){
  var suma=0; var sumal=0;
  var total= $("#numero_cuentas"+fila).val();
  var monto_anterior=parseFloat($("#monto_designado"+fila).val());
  for (var i=1;i<=(total-1);i++){
    if(!($("#monto_mod"+fila+"RRR"+i).is("[readonly]"))){
    if(valor==1){
      var dias=1;
      if($("#unidad_monto_modal"+fila+"RRR"+i).val()==4){
       var dias=$("#dias_curso").val();
      }
      suma+=parseFloat($("#monto_mod"+fila+"RRR"+i).val());
      if($("#cod_ibnorca").val()==1){
         $("#monto_modal"+fila+"RRR"+i).val(redondeo(parseFloat($("#monto_mod"+fila+"RRR"+i).val())/parseInt($("#cantidad_monto_modal"+fila+"RRR"+i).val())/dias));
       }else{
          $("#monto_modal"+fila+"RRR"+i).val(redondeo(parseFloat($("#monto_mod"+fila+"RRR"+i).val())/parseInt($("#cantidad_monto_modal"+fila+"RRR"+i).val())/dias));
       } 
      
    }else{
      var dias=1;
      if($("#unidad_monto_modal"+fila+"RRR"+i).val()==4){
       var dias=$("#dias_curso").val();
      }
     if($("#cod_ibnorca").val()==1){
         $("#monto_mod"+fila+"RRR"+i).val(redondeo(parseFloat($("#monto_modal"+fila+"RRR"+i).val())*parseInt($("#cantidad_monto_modal"+fila+"RRR"+i).val())*dias));
       }else{
          $("#monto_mod"+fila+"RRR"+i).val(redondeo(parseFloat($("#monto_modal"+fila+"RRR"+i).val())*parseInt($("#cantidad_monto_modal"+fila+"RRR"+i).val())*dias));
       }
     suma+=parseFloat($("#monto_mod"+fila+"RRR"+i).val());  
    }
    sumal+=parseFloat($("#monto_modal"+fila+"RRR"+i).val());
    }
   
  }
  const rest=Math.abs(suma-monto_anterior);
  const porcent=(rest*100)/monto_anterior; 
  var resultPorcent= Math.round(porcent*100)/100;  
  var result=redondeo((suma*100)/100);
  document.getElementById("monto_editable"+fila).value=result;
  $("#total_tabladetalle"+fila).text(result); 
  if($("#cod_ibnorca").val()==1){
       $("#total_tabladetalleAl"+fila).text(redondeo(sumal));
     
       }else{
       $("#total_tabladetalleAl"+fila).text(redondeo(sumal));
       } 
  if(result<monto_anterior){
    $("#monto_editable"+fila).addClass("text-danger");
    $("#monto_editable"+fila).removeClass("text-success");
  }else{
    if(result>monto_anterior){
       $("#monto_editable"+fila).addClass("text-success");
       $("#monto_editable"+fila).removeClass("text-danger");
    }else{
       $("#monto_editable"+fila).removeClass("text-success");
       $("#monto_editable"+fila).removeClass("text-danger");
    }
  }
}
function redondeo(num, decimales = 2) {
    var signo = (num >= 0 ? 1 : -1);
    num = num * signo;
    if (decimales === 0) //con 0 decimales
        return signo * Math.round(num);
    // round(x * 10 ^ decimales)
    num = num.toString().split('e');
    num = Math.round(+(num[0] + 'e' + (num[1] ? (+num[1] + decimales) : decimales)));
    // x * 10 ^ (-decimales)
    num = num.toString().split('e');
    return signo * (num[0] + 'e' + (num[1] ? (+num[1] - decimales) : -decimales));
}

function guardarCuentasSimulacionAjax(ib){
  var total= $("#numero_cuentas").val();
  //var simulacion=$("#cod_simulacion").val();
  var plantilla =$("#cod_plantilla").val();
  var partida =$("#partida_presupuestaria").val();
  
    for (var i=1;i<=(total-1);i++){
      var habilitado=1;
      var codigo = $("#codigo"+i).val();
      var monto = $("#monto_mod"+i).val();
      if($("#monto_mod"+i).is("[readonly]")){
        habilitado=0;
      }
      var cuenta =$("#codigo_cuenta"+i).val();
      var simulacion =$("#codigo_fila"+i).val();
      var parametros = {"codigo":codigo,"monto":monto,"ibnorca":ib,"simulacion":simulacion,"plantilla":plantilla,"partida":partida,"cuenta":cuenta,"habilitado":habilitado};
      $.ajax({
        type:"GET",
        data:parametros,
        url:"ajaxSaveCuentas.php",
        beforeSend: function () { 
          $("#guardar_cuenta").text("espere.."); 
          $("#guardar_cuenta").attr("disabled",true);
          $("#mensaje_cuenta").html("");
          iniciarCargaAjax();
        },
        success:function(resp){
          detectarCargaAjax();
          $("#guardar_cuenta").text("Guardar");
          $("#guardar_cuenta").removeAttr("disabled");
          $("#mensaje_cuenta").html("<p class='text-success'>Se insertaron los datos correctamente! </p>");//<a class='btn btn-warning btn-sm' href='#' onclick='actualizarSimulacion();'>aplicar cambios a la propuesta</a>
        }
      });
    }
    actualizarSimulacion();
}
function guardarCuentasSimulacionAjaxGenericoServicio(ib){
  var supertotal=$("#numero_cuentaspartida").val();

  for (var j = 1; j <=(supertotal-1); j++) {
  var total= $("#numero_cuentas"+j).val();
  var simulaciones=$("#cod_simulacion").val();
  var plantilla =$("#cod_plantilla").val();
  var partida =$("#codigo_partida_presupuestaria"+j).val();
  
    for (var i=1;i<=(total-1);i++){
      var habilitado=1;
      var codigo = $("#codigo"+j+"RRR"+i).val();
      var monto = $("#monto_mod"+j+"RRR"+i).val();
      var cantidad = $("#cantidad_personal"+j+"RRR"+i).val();
      if($("#monto_mod"+j+"RRR"+i).is("[readonly]")){
        habilitado=0;
      }
      var cuenta =$("#codigo_cuenta"+j+"RRR"+i).val();
      var simulacion =$("#codigo_fila"+j+"RRR"+i).val();
      var parametros = {"codigo":codigo,"monto":monto,"ibnorca":ib,"simulacion":simulacion,"simulaciones":simulaciones,"plantilla":plantilla,"partida":partida,"cuenta":cuenta,"habilitado":habilitado,"cantidad":cantidad};
      $.ajax({
        type:"GET",
        data:parametros,
        url:"ajaxSaveCuentas.php",
        beforeSend: function () { 
          $("#guardar_cuenta").text("espere.."); 
          $("#guardar_cuenta").attr("disabled",true);
          $("#mensaje_cuenta").html("");
          iniciarCargaAjax();
        },
        success:function(resp){
          $("#guardar_cuenta").text("Guardar");
          $("#guardar_cuenta").removeAttr("disabled");
          $("#mensaje_cuenta").html("<p class='text-success'>Se insertaron los datos correctamente! </p>");//<a class='btn btn-warning btn-sm' href='#' onclick='actualizarSimulacion();'>aplicar cambios a la propuesta</a>
        },complete : function(xhr, status) {
        
         }
      });
    }   
  };
    actualizarSimulacion();
}

function guardarCuentasSimulacionAjaxGenericoServicioAuditorTodos(inicio){
  var anios = $("#anio_simulacion").val();
  for (var anio = inicio; anio <= parseInt(anios); anio++) {
     var ib=1;
  var supertotal=$("#numero_cuentaspartida"+anio).val();
  for (var j = 1; j <=(supertotal-1); j++) {
  var total= $("#numero_cuentas"+anio+"QQQ"+j).val();
  var simulaciones=$("#cod_simulacion").val();
  var plantilla =$("#cod_plantilla").val();
  var partida =$("#codigo_partida_presupuestaria"+anio+"QQQ"+j).val();
      for (var i=1;i<=(total-1);i++){
      
      var personalCuenta=$("#modal_numeropersonalauditor"+anio).val();
      for (var l = 1; l <=(personalCuenta-1); l++) {
         var tipoAu=$("#codigo_filaauditor"+anio+"CCCC"+l).val();
         var columnas= $("#cantidad_columnas"+anio+"CCCC"+l).val();
         var diasn=$("#modal_dias_personal"+anio+"CCCC"+l).val();
         var extlocal=$("#modal_local_extranjero"+anio+"CCCC"+l).val();
         var cantidadn=$("#modal_cantidad_personal"+anio+"CCCC"+l).val();

          //datos para el registro de los honorarios
         var dias_honorarios=$("#dias_honorario"+anio+"CCCC"+l).val();
         var monto_honorarios=$("#monto_honorario"+anio+"CCCC"+l).val();
         //registro de los montos detalle
      if(!($("#dias_honorario"+anio+"CCCC"+l).is("[readonly]"))){
        var habilitadoPersonal=1;
      }else{
        var habilitadoPersonal=0;
      }

      if(columnas==0){
        columnas++; //para que registre el honorario aunque no este registrado el detalle
      }
         for (var k = 1; k <=columnas; k++) {
          var diasn=$("#modal_dias_personalItem"+anio+"CCCC"+k+"RRR"+l).val();
           var codigoDetalle= $("#codigo_columnas"+anio+"CCCC"+k+"RRR"+l).val();
           var montoDetalle= $("#monto"+anio+"CCCC"+k+"RRR"+l).val();
           var montoDetalleext= $("#montoext"+anio+"CCCC"+k+"RRR"+l).val();

            var parametros = {"habilitadoP":habilitadoPersonal,"dias_honorarios":dias_honorarios,"monto_honorarios":monto_honorarios,"simulaciones":simulaciones,"cod_detalle":codigoDetalle,"cod_tipoau":tipoAu,"extlocal":extlocal,"monto":montoDetalle,"montoe":montoDetalleext,"dias":diasn,"cantidad":cantidadn,"anio":anio};
            $.ajax({
            type:"GET",
            data:parametros,
            url:"ajaxSaveCuentasAuditor.php",
            beforeSend: function () { 
              iniciarCargaAjax();
            },
            success:function(resp){ 
                
            }
          });
         };
         //fin de registro de los montos detalle
      };
      var habilitado=1;
      var cantidad = $("#cantidad_personal"+anio+"QQQ"+j+"RRR"+i).val();
      if($("#monto_mod"+anio+"QQQ"+j+"RRR"+i).is("[readonly]")){
        habilitado=0;
      }
      var codigo = $("#codigo"+anio+"QQQ"+j+"RRR"+i).val();
      var monto = $("#monto_mod"+anio+"QQQ"+j+"RRR"+i).val();
      var cuenta =$("#codigo_cuenta"+anio+"QQQ"+j+"RRR"+i).val();
      var simulacion =$("#codigo_fila"+anio+"QQQ"+j+"RRR"+i).val();
       var parametros = {"codigo":codigo,"monto":monto,"ibnorca":ib,"simulacion":simulacion,"simulaciones":simulaciones,"plantilla":plantilla,"partida":partida,"cuenta":cuenta,"habilitado":habilitado,"cantidad":cantidad,"anio":anio};
      $.ajax({
        type:"GET",
        data:parametros,
        url:"ajaxSaveCuentas.php",
        beforeSend: function () { 
          $("#guardar_cuenta").text("espere.."); 
          $("#guardar_cuenta").attr("disabled",true);
          $("#mensaje_cuenta").html("");
          iniciarCargaAjax();
        },
        success:function(resp){

          $("#guardar_cuenta").text("Guardar");
          $("#guardar_cuenta").removeAttr("disabled");
          $("#mensaje_cuenta"+anio).html("<p class='text-success'>Se insertaron los datos correctamente! </p>");//<a class='btn btn-warning btn-sm' href='#' onclick='actualizarSimulacion();'>aplicar cambios a la propuesta</a>
        },complete : function(xhr, status) {
        
         }
      });
    }   
  };
  //if(copiar==0){
   actualizarSimulacion();
  //}//
  }
}


function guardarCuentasSimulacionAjaxGenericoServicioAuditor(anio,copiar,otroanio){
  var ib=1;
  var supertotal=$("#numero_cuentaspartida"+anio).val();
  for (var j = 1; j <=(supertotal-1); j++) {
  var total= $("#numero_cuentas"+anio+"QQQ"+j).val();
  var simulaciones=$("#cod_simulacion").val();
  var plantilla =$("#cod_plantilla").val();
  var partida =$("#codigo_partida_presupuestaria"+anio+"QQQ"+j).val();
  
    for (var i=1;i<=(total-1);i++){
      
      var personalCuenta=$("#modal_numeropersonalauditor").val();
      for (var l = 1; l <=(personalCuenta-1); l++) {
         var tipoAu=$("#codigo_filaauditor"+l).val();
         var columnas= $("#cantidad_columnas"+l).val();
         var diasn=$("#modal_dias_personal"+l).val();
         var extlocal=$("#modal_local_extranjero"+l).val();
         var cantidadn=$("#modal_cantidad_personal"+l).val();
         for (var k = 1; k <=columnas; k++) {
          var diasn=$("#modal_dias_personalItem"+k+"RRR"+l).val();
           var codigoDetalle= $("#codigo_columnas"+k+"RRR"+l).val();
           var montoDetalle= $("#monto"+k+"RRR"+l).val();
           var montoDetalleext= $("#montoext"+k+"RRR"+l).val();
           if(copiar!=0){
             var parametros = {"otroanio":JSON.stringify(otroanio),"simulaciones":simulaciones,"cod_detalle":codigoDetalle,"cod_tipoau":tipoAu,"extlocal":extlocal,"monto":montoDetalle,"montoe":montoDetalleext,"dias":diasn,"cantidad":cantidadn,"anio":anio};
           }else{
             var parametros = {"simulaciones":simulaciones,"cod_detalle":codigoDetalle,"cod_tipoau":tipoAu,"extlocal":extlocal,"monto":montoDetalle,"montoe":montoDetalleext,"dias":diasn,"cantidad":cantidadn,"anio":anio};
           }
            $.ajax({
            type:"GET",
            data:parametros,
            url:"ajaxSaveCuentasAuditor.php",
            beforeSend: function () { 
              iniciarCargaAjax();
            },
            success:function(resp){ 
                
            }
          });
         };
      };
      var habilitado=1;
      var cantidad = $("#cantidad_personal"+anio+"QQQ"+j+"RRR"+i).val();
      if($("#monto_mod"+anio+"QQQ"+j+"RRR"+i).is("[readonly]")){
        habilitado=0;
      }
      var codigo = $("#codigo"+anio+"QQQ"+j+"RRR"+i).val();
      var monto = $("#monto_mod"+anio+"QQQ"+j+"RRR"+i).val();
      var cuenta =$("#codigo_cuenta"+anio+"QQQ"+j+"RRR"+i).val();
      var simulacion =$("#codigo_fila"+anio+"QQQ"+j+"RRR"+i).val();
      if(copiar!=0){
          var parametros = {"otroanio":JSON.stringify(otroanio),"codigo":codigo,"monto":monto,"ibnorca":ib,"simulacion":simulacion,"simulaciones":simulaciones,"plantilla":plantilla,"partida":partida,"cuenta":cuenta,"habilitado":habilitado,"cantidad":cantidad,"anio":anio};
       }else{
           var parametros = {"codigo":codigo,"monto":monto,"ibnorca":ib,"simulacion":simulacion,"simulaciones":simulaciones,"plantilla":plantilla,"partida":partida,"cuenta":cuenta,"habilitado":habilitado,"cantidad":cantidad,"anio":anio};
      }
      
      $.ajax({
        type:"GET",
        data:parametros,
        url:"ajaxSaveCuentas.php",
        beforeSend: function () { 
          $("#guardar_cuenta").text("espere.."); 
          $("#guardar_cuenta").attr("disabled",true);
          $("#mensaje_cuenta").html("");
          iniciarCargaAjax();
        },
        success:function(resp){
          $("#guardar_cuenta").text("Guardar");
          $("#guardar_cuenta").removeAttr("disabled");
          $("#mensaje_cuenta"+anio).html("<p class='text-success'>Se insertaron los datos correctamente! </p>");//<a class='btn btn-warning btn-sm' href='#' onclick='actualizarSimulacion();'>aplicar cambios a la propuesta</a>
        },complete : function(xhr, status) {
        
         }
      });
    }   
  };
  //if(copiar==0){
    actualizarSimulacion();
  //}//
    
}
function guardarCuentasSimulacionAjaxGenerico(ib){
  var supertotal=$("#numero_cuentaspartida").val();
    var montoNorma=$("#monto_norma"+supertotal).val();
  var habilitadoNorma=1;
  if($("#monto_norma"+supertotal).is("[readonly]")){
    habilitadoNorma=0;
  }
  for (var j = 1; j <=(supertotal-1); j++) {
  var total= $("#numero_cuentas"+j).val();
  var simulaciones=$("#cod_simulacion").val();
  var plantilla =$("#cod_plantilla").val();
  var partida =$("#codigo_partida_presupuestaria"+j).val();
  
    for (var i=1;i<=(total-1);i++){
      var habilitado=1;
      var codigo = $("#codigo"+j+"RRR"+i).val();
      var monto = $("#monto_mod"+j+"RRR"+i).val();
      if($("#monto_mod"+j+"RRR"+i).is("[readonly]")){
        habilitado=0;
      }
      var cuenta =$("#codigo_cuenta"+j+"RRR"+i).val();
      var simulacion =$("#codigo_fila"+j+"RRR"+i).val();
      var cantidadFila =$("#cantidad_monto_modal"+j+"RRR"+i).val();
      var unidadFila =$("#unidad_monto_modal"+j+"RRR"+i).val();
      var parametros = {"cantidad_fila":cantidadFila,"unidad_fila":unidadFila,"habilitado_norma":habilitadoNorma,"monto_norma":montoNorma,"codigo":codigo,"monto":monto,"ibnorca":ib,"simulacion":simulacion,"simulaciones":simulaciones,"plantilla":plantilla,"partida":partida,"cuenta":cuenta,"habilitado":habilitado};
      $.ajax({
        type:"GET",
        data:parametros,
        url:"ajaxSaveCuentas.php",
        beforeSend: function () { 
          $("#guardar_cuenta").text("espere.."); 
          $("#guardar_cuenta").attr("disabled",true);
          $("#mensaje_cuenta").html("");
          iniciarCargaAjax();
        },
        success:function(resp){
          $("#guardar_cuenta").text("Guardar");
          $("#guardar_cuenta").removeAttr("disabled");
          $("#mensaje_cuenta").html("<p class='text-success'>Se insertaron los datos correctamente! </p>");//<a class='btn btn-warning btn-sm' href='#' onclick='actualizarSimulacion();'>aplicar cambios a la propuesta</a>
        },complete : function(xhr, status) {
        
         }
      });
    }   
  };
    actualizarSimulacion();
}
$(document).ajaxStop(function(){
  detectarCargaAjax();
});
function listarCostosFijos(){
  cargarListaCostosDetalle(1);
}
function listarCostosFijosPeriodo(anio){
  cargarListaCostosDetallePeriodoFijo(1,anio);
}
function listarCostosVaribles(){
  cargarListaCostosDetalle(2);
}
function listarCostosVariblesPeriodo(anio){
  cargarListaCostosDetallePeriodo(2,anio);
}
function cargarListaCostosDetalle(valor){
  var tipo = valor;
  var simulacion=$("#cod_simulacion").val();
  var plantilla =$("#cod_plantilla").val();
  var url="ajaxCargarDetalleCostosSimulacion.php";
  var ibnorca=$("#cod_ibnorca").val();
    if(ibnorca==1){
      var alumnos=$("#alumnos_plan").val();
    }else{
      var alumnos=$("#alumnos_plan_fuera").val();
    }
  if($("#cambio_moneda").length){
    var parametros = {"simulacion":simulacion,"plantilla":plantilla,"tipo":tipo,"al":alumnos,"usd":$("#cambio_moneda").val(),"unidad_nombre":$("#unidad_plan").val(),"area_nombre":$("#area_plan").val(),"porcentaje_fijo":$("#porcentaje_fijo").val()};
  }else{
    var parametros = {"simulacion":simulacion,"plantilla":plantilla,"tipo":tipo,"al":alumnos,"porcentaje_fijo":$("#porcentaje_fijo").val(),"unidad_nombre":$("#unidad_plan").val(),"area_nombre":$("#area_plan").val()};
  }  
      $.ajax({
        type:"GET",
        data:parametros,
        url:url,
        beforeSend: function () { 
          iniciarCargaAjax();
        },
        success:function(resp){
          detectarCargaAjax();
           $("#lista_detallecosto").html(resp);
          $("#modalCargarDetalleCosto").modal("show");          
        }
      });
 
}
function cargarListaCostosDetallePeriodoFijo(valor,anio){
  var tipo = valor;
  var simulacion=$("#cod_simulacion").val();
  var plantilla =$("#cod_plantilla").val();
  var url="ajaxCargarDetalleCostosSimulacion.php";
  var ibnorca=$("#cod_ibnorca").val();
    if(ibnorca==1){
      var alumnos=$("#alumnos_plan").val();
    }else{
      var alumnos=$("#alumnos_plan_fuera").val();
    }
  if($("#cambio_moneda").length){
    var parametros = {"simulacion":simulacion,"plantilla":plantilla,"tipo":tipo,"al":alumnos,"usd":$("#cambio_moneda").val(),"unidad_nombre":$("#unidad_plan").val(),"area_nombre":$("#area_plan").val(),"anio":anio,"porcentaje_fijo":$("#porcentaje_fijo").val()};
  }else{
    var parametros = {"simulacion":simulacion,"plantilla":plantilla,"tipo":tipo,"al":alumnos};
  }  

  //para que solo cargue la primera vez inicio_fijomodal=0
  var inicioModal= $("#inicio_fijomodal").val(); 
  if(inicioModal==0){
      $.ajax({
        type:"GET",
        data:parametros,
        url:url,
        beforeSend: function () { 
          iniciarCargaAjax();
          $("#texto_ajax_titulo").html("Obteniendo Costos Fijos");        
        },
        success:function(resp){
          detectarCargaAjax();
          $("#texto_ajax_titulo").html("Procesando Datos"); 
           $("#lista_detallecosto").html(resp);
          $("#modalCargarDetalleCosto").modal("show");
          $("#inicio_fijomodal").val(0);
        }
      }); 
  }else{
    $("#modalCargarDetalleCosto").modal("show");
  }
 
}
function cargarListaCostosDetallePeriodo(valor,anio){
  var tipo = valor;
  var simulacion=$("#cod_simulacion").val();
  var plantilla =$("#cod_plantilla").val();
  var url="ajaxCargarDetalleCostosSimulacion.php";
  var ibnorca=$("#cod_ibnorca").val();
    if(ibnorca==1){
      var alumnos=$("#alumnos_plan").val();
    }else{
      var alumnos=$("#alumnos_plan_fuera").val();
    }

  if($("#cambio_moneda").length){
    if($("#input_versimulacion").length>0){
     var parametros = {"verSim":1,"simulacion":simulacion,"plantilla":plantilla,"tipo":tipo,"al":alumnos,"anio":anio,"usd":$("#cambio_moneda").val()};     
    }else{
      var parametros = {"simulacion":simulacion,"plantilla":plantilla,"tipo":tipo,"al":alumnos,"anio":anio,"usd":$("#cambio_moneda").val()};
    }
  }else{
    if($("#input_versimulacion").length>0){
      var parametros = {"verSim":1,"simulacion":simulacion,"plantilla":plantilla,"tipo":tipo,"al":alumnos,"anio":anio};  
    }else{
      var parametros = {"simulacion":simulacion,"plantilla":plantilla,"tipo":tipo,"al":alumnos,"anio":anio};
    }
    
  }  
  //para que solo cargue la primera vez inicio_fijomodal=0
  var inicioModal= $("#inicio_variablemodal").val(); 
  if(inicioModal==0){
      $.ajax({
        type:"GET",
        data:parametros,
        url:url,
        beforeSend: function () { 
          iniciarCargaAjax();
          $("#texto_ajax_titulo").html("Obteniendo Costos Variables");        
        },
        success:function(resp){
          detectarCargaAjax();
           $("#lista_detallecosto").html(resp);
          $("#modalCargarDetalleCosto").modal("show");
          $("#texto_ajax_titulo").html("Procesando Datos"); 
          $("#inicio_variablemodal").val(0);          
        }
      });
    }else{
      $("#modalCargarDetalleCosto").modal("show");
    }
}
function guardarCuentasSimulacionGenerico(ib){
  var conta=0; var contaRead=0;
  var supertotal= $("#numero_cuentaspartida").val();
  var cosSim=$("#cod_simulacion").val();
  for (var j = 1; j <=(supertotal-1); j++) {
  var total= $("#numero_cuentas"+j).val();
  
  if((total-1)!=0){
    for (var i=1;i<=(total-1);i++){
      if($("#monto_mod"+j+"RRR"+i).val()==""||$("#monto_modal"+j+"RRR"+i).val()==""||$("#cantidad_monto_modal"+j+"RRR"+i).val()==""||$("#cantidad_monto_modal"+j+"RRR"+i).val()==0){
        conta++
      }
      if($("#monto_mod"+j+"RRR"+i).is("[readonly]")){
        contaRead++
      }
    }    
  }    
  };
  if(conta==0){
    if(contaRead==0){
      guardarCuentasSimulacionAjaxGenerico(ib);
    }else{
        Swal.fire({
         title: 'Advertencia!',
         text: "Hay uno o más registros deshabilitados ¿Desea Continuar?",
         type: 'warning',
         showCancelButton: true,
         confirmButtonClass: 'btn btn-info',
         cancelButtonClass: 'btn btn-danger',
         confirmButtonText: 'Si',
         cancelButtonText: 'No',
         buttonsStyling: false
       }).then((result) => {
          if (result.value) {
               guardarCuentasSimulacionAjaxGenerico(ib);            
            return(true);
          } else if (result.dismiss === Swal.DismissReason.cancel) {
            return(false);
          }
        });
    }
   }else{
    Swal.fire('Informativo!','Todos los campos son requeridos!','warning'); 
   } 
}
function guardarCuentasSimulacionGenericoServicio(ib){
  var conta=0; var contaRead=0;
  var supertotal= $("#numero_cuentaspartida").val();
  var cosSim=$("#cod_simulacion").val();
  for (var j = 1; j <=(supertotal-1); j++) {
  var total= $("#numero_cuentas"+j).val();
  
  if((total-1)!=0){
    for (var i=1;i<=(total-1);i++){
      if($("#monto_mod"+j+"RRR"+i).val()==""||$("#monto_modal"+j+"RRR"+i).val()==""){
        conta++
      }
      if($("#monto_mod"+j+"RRR"+i).is("[readonly]")){
        contaRead++
      }
    }    
  }    
  };
  if(conta==0){
    if(contaRead==0){
      guardarCuentasSimulacionAjaxGenericoServicio(ib);
    }else{
        Swal.fire({
         title: 'Advertencia!',
         text: "Hay uno o más registros deshabilitados ¿Desea Continuar?",
         type: 'warning',
         showCancelButton: true,
         confirmButtonClass: 'btn btn-info',
         cancelButtonClass: 'btn btn-danger',
         confirmButtonText: 'Si',
         cancelButtonText: 'No',
         buttonsStyling: false
       }).then((result) => {
          if (result.value) {
               guardarCuentasSimulacionAjaxGenericoServicio(ib);            
            return(true);
          } else if (result.dismiss === Swal.DismissReason.cancel) {
            return(false);
          }
        });
    }
   }else{
    Swal.fire('Informativo!','Todos los campos son requeridos!','warning'); 
   } 
}

function cargarDetallesCostosVariablesTodosLosAnios(inicio,ib){
  var results = [];
  var anios = $("#anio_simulacion").val();
  $("#cuentas_simulacionpersonal").html("");
  var index=0;

  for (var anio = inicio; anio <= parseInt(anios); anio++) {
    //if(parseInt($("#anio_for").val())==0){
     var conta=0; var contaRead=0; var existeAnio=0;
  var supertotal= $("#numero_cuentaspartida"+anio).val();
  var cosSim=$("#cod_simulacion").val();
  var usd=$("#cambio_moneda").val();
  var codigosFilas="";
  var montoFilasPersonal="";
  for (var j = 1; j <=(supertotal-1); j++) {
  var total= $("#numero_cuentas"+anio+"QQQ"+j).val();  
  if((total-1)!=0){
    for (var i=1;i<=(total-1);i++){
      var codigoF=$("#codigo"+anio+"QQQ"+j+"RRR"+i).val();
      var montoF=$("#monto_modal"+anio+"QQQ"+j+"RRR"+i).val();
      if(!($("#monto_mod"+anio+"QQQ"+j+"RRR"+i).is("[readonly]"))){
        codigosFilas+=codigoF+"###";
        montoFilasPersonal+=montoF+"###";
        existeAnio++;
      }
    }    
   }    
  }; 

   //if(existeAnio!=0){
    //ajax estado de cuentas
    var parametros={"anios":$("#anio_simulacion").val(),"cod_area":$("#codigo_area").val(),"cod_simulacion":cosSim,"codigo_filas":codigosFilas,"anio":anio,"usd":usd,"monto_filas":montoFilasPersonal};
    results[anio]=parametros;
    
  //}
   //}
   // guardarCuentasSimulacionGenericoServicioPrevio(i,ib)
   index++;
  };//fin de for
  var parametros={"data":JSON.stringify(results),"anios":$("#anio_simulacion").val(),"cod_area":$("#codigo_area").val()}
  $.ajax({
        type: "GET",
        dataType: 'html',
        url: "ajaxCargarDetalleSimulacionAuditorNuevo.php",
        data: parametros,
        beforeSend:function(){
         iniciarCargaAjax();
        },
        success:  function (resp) {
         detectarCargaAjax();
         //results[index] = resp;
          $("#cuentas_simulacionpersonal").html(resp);
          $('[data-toggle="tooltip"]').tooltip();
          $('.selectpicker').selectpicker("refresh"); 
          // ponerCantidadTotalesVariablesModal(inicio,anio);            
        }
    });

  /*$("#cuentas_simulacionpersonal").append("");
  for (var i = 0; i <= index; i++) {  
    $("#cuentas_simulacionpersonal").append(results[i]);
    $('.selectpicker').selectpicker("refresh"); 
  };*/
  $("#modalSimulacionCuentasPersonal").modal("show");
  ponerCantidadTotalesVariablesModal(inicio,anios);
  $("#modalSimulacionCuentas0").modal("hide"); 

}

function ponerCantidadTotalesVariablesModal(inicio,anios){
  var sumaHonorarios=0;var sumaVariables=0;
  var sumaHonorariosUSD=0;var sumaVariablesUSD=0;
  for (var anio = parseInt(inicio); anio <= parseInt(anios); anio++) {
    sumaHonorarios+=redondeo($("#total_auditorvariable"+anio).text());
    sumaHonorariosUSD+=redondeo($("#total_auditorvariableUSD"+anio).text());
    sumaVariables+=redondeo($("#total_auditor"+anio).text());
    sumaVariablesUSD+=redondeo($("#total_auditorUSD"+anio).text());
   // alert(sumaVariables)
  }
  $("#total_honorarios_modal").text(redondeo(sumaHonorarios));
  $("#total_honorarios_modalUSD").text(redondeo(sumaHonorariosUSD));
  $("#total_variables_modal").text(redondeo(sumaVariables-sumaHonorarios));
  $("#total_variables_modalUSD").text(redondeo(sumaVariablesUSD-sumaHonorariosUSD));

  $("#total_variables_hon_modal").text(redondeo(sumaVariables));
  $("#total_variables_hon_modalUSD").text(redondeo(sumaVariablesUSD));
}
function guardarCuentasSimulacionGenericoServicioPrevio(anio,ib){
  var conta=0; var contaRead=0;
  var supertotal= $("#numero_cuentaspartida"+anio).val();
  var cosSim=$("#cod_simulacion").val();
  var usd=$("#cambio_moneda").val();
  var codigosFilas="";
  var montoFilasPersonal="";
  for (var j = 1; j <=(supertotal-1); j++) {
  var total= $("#numero_cuentas"+anio+"QQQ"+j).val();  
  if((total-1)!=0){
    for (var i=1;i<=(total-1);i++){
      var codigoF=$("#codigo"+anio+"QQQ"+j+"RRR"+i).val();
      var montoF=$("#monto_modal"+anio+"QQQ"+j+"RRR"+i).val();
      if(!($("#monto_mod"+anio+"QQQ"+j+"RRR"+i).is("[readonly]"))){
        codigosFilas+=codigoF+"###";
        montoFilasPersonal+=montoF+"###";
      }
    }    
   }    
  };
  //ajax estado de cuentas
    var parametros={"anios":$("#anio_simulacion").val(),"cod_area":$("#codigo_area").val(),"cod_simulacion":cosSim,"codigo_filas":codigosFilas,"anio":anio,"usd":usd,"monto_filas":montoFilasPersonal};
    $.ajax({
        type: "GET",
        dataType: 'html',
        url: "ajaxCargarDetalleSimulacionAuditor.php",
        data: parametros,
        beforeSend:function(){
         iniciarCargaAjax();
        },
        success:  function (resp) {
          detectarCargaAjax();
          $("#cuentas_simulacionpersonal").append(resp);
          $('.selectpicker').selectpicker("refresh");
          /*var titulo="AÑO "+anio;
          if((anio==0||anio==1)&&($("#codigo_area").val()!=39)){
           titulo="AÑO 1 (ETAPA "+(parseInt(anio)+1)+")";
          }*/
          //$("#titulo_modaldetalleslista").html(titulo);
          //$("#modalSimulacionCuentasPersonal").modal("show");
          //$("#modalSimulacionCuentas"+anio).modal("hide");           
        }
    });
}

function guardarCuentasSimulacion(ib){
  var total= $("#numero_cuentas").val();
  var cosSim=$("#cod_simulacion").val();
  var conta=0; var contaRead=0;
  if((total-1)!=0){
    for (var i=1;i<=(total-1);i++){
      if($("#monto_mod"+i).val()==""||$("#monto_modal"+i).val()==""){
        conta++
      }
      if($("#monto_mod"+i).is("[readonly]")){
        contaRead++
      }
    }
  if(conta==0){
    if(contaRead==0){
      guardarCuentasSimulacionAjax(ib);
    }else{
        Swal.fire({
         title: 'Advertencia!',
         text: "Hay uno o más registros deshabilitados ¿Desea Continuar?",
         type: 'warning',
         showCancelButton: true,
         confirmButtonClass: 'btn btn-info',
         cancelButtonClass: 'btn btn-danger',
         confirmButtonText: 'Si',
         cancelButtonText: 'No',
         buttonsStyling: false
       }).then((result) => {
          if (result.value) {
               guardarCuentasSimulacionAjax(ib);            
            return(true);
          } else if (result.dismiss === Swal.DismissReason.cancel) {
            return(false);
          }
        });
    }
   }else{
    Swal.fire('Informativo!','Todos los campos son requeridos!','warning'); 
   }     
  } 
}
var itemCuentas=[];
var itemCuentasAux=[];

function buscarCuentaList(campo){
  var contenedor = document.getElementById('divResultadoBusqueda');
  var nroCuenta=document.getElementById('nro_cuenta').value;
  var nombreCuenta=document.getElementById('cuenta').value;
  var padre=$("#padre").val();
  switch (campo){
   case "numero":
     buscarCuentaNumero(nroCuenta,1);
   break;
   case "nombre":
     buscarCuentaNumero(nombreCuenta,2);
   break;
  }     
}

function buscarCuentaListAux(campo){
  var contenedor = document.getElementById('divResultadoBusqueda');
  var nombreCuentaAux=document.getElementById('cuenta_auxiliar_modal').value;
  var nroCuenta=document.getElementById('nro_cuenta').value;
  var nombreCuenta=document.getElementById('cuenta').value;
  var padre=$("#padre").val();
  buscarCuentaNombreAux(nombreCuentaAux,nombreCuenta,nroCuenta);    
}

function buscarCuentaNombreAux(numeros,nombreCuenta,nroCuenta){  
  var contenedor = document.getElementById('divResultadoBusqueda');
  //var str = numeros.replace(/^"(.*)"$/, '$1'); 
  var html="<div class='col-md-12'>"+
    "<div class='table-responsive'>"+
    "<table class='table table-condensed'>"+
      "<thead>"+
        "<tr>"+
          "<th>Nro. Cuenta</th>"+
              "<th>Nombre</th>"+
              "<th>Auxiliar</th>"+
          "</tr>"+
      "</thead>";
      
  for (var i = 0; i < itemCuentas.length; i++) { 
      var cadenaBuscar=itemCuentas[i].nombre.toLowerCase();
      var nn=-1;
       var nom = itemCuentas[i].nombre.toLowerCase().indexOf(nombreCuenta.toLowerCase());
       var num = itemCuentas[i].numero.toLowerCase().indexOf(nroCuenta.toLowerCase());
        var n=-1;
       for (var k = 0; k < itemCuentasAux.length; k++) {
        if(itemCuentasAux[k].codCuenta==itemCuentas[i].codigo){
          nn = itemCuentasAux[k].nombre.toLowerCase().indexOf(numeros.toLowerCase());
         if(nn>=0){
          break;
          }  
         }
        };    
       
    if(nn>=0){
      if(nom>=0||num>=0){
        n=0;
      }
    }
    
    if(n>=0){
      var textoAux="<table class='table table-condensed' style='overflow-y: scroll;display: block;height:210px;'>";
      
        for (var j = 0; j < itemCuentasAux.length; j++) {
          if(itemCuentasAux[j].codCuenta==itemCuentas[i].codigo){
            if(nn>=0){
              var nnn = itemCuentasAux[j].nombre.toLowerCase().indexOf(numeros.toLowerCase());
              if(nnn>=0){
                 textoAux+="<tr >"+
               "<td class='text-left small'>"+itemCuentasAux[j].codigo+"</td>"+
               "<td class='text-left small'><a href=\"javascript:setBusquedaCuenta(\'"+itemCuentas[i].codigo+"\',\'"+itemCuentas[i].numero+"\',\'"+itemCuentas[i].nombre+"\',\'"+itemCuentasAux[j].codigo+"\',\'"+itemCuentasAux[j].nombre+"\');\">"+itemCuentasAux[j].nombre+"</a></td>"+
             "</tr>"; 
              }
            }else{
            textoAux+="<tr >"+
               "<td class='text-left small'>"+itemCuentasAux[j].codigo+"</td>"+
               "<td class='text-left small'><a href=\"javascript:setBusquedaCuenta(\'"+itemCuentas[i].codigo+"\',\'"+itemCuentas[i].numero+"\',\'"+itemCuentas[i].nombre+"\',\'"+itemCuentasAux[j].codigo+"\',\'"+itemCuentasAux[j].nombre+"\');\">"+itemCuentasAux[j].nombre+"</a></td>"+
             "</tr>";       
            }
          }
        };
       textoAux+="</table>";
       var label="";
       if(textoAux!="<table class='table table-condensed' style='overflow-y: scroll;display: block;height:210px;'></table>"){
        label='<span style="color: #0431B4;">';        
       }else{
        textoAux="<table class='table table-condensed'></table>";
        label='<span>';
       }
      html+="<tr>"+
      "<td class='text-left'>"+label+itemCuentas[i].numero+"</span></td>"+
          "<td class='text-left'><a href=\"javascript:setBusquedaCuenta(\'"+itemCuentas[i].codigo+"\',\'"+itemCuentas[i].numero+"\',\'"+itemCuentas[i].nombre+"\',\'0\',\'\');\">"+itemCuentas[i].nombre+"</a></td>"+
          "<td class='text-left'>"+textoAux+"</td>"+
      "</tr>";
    }
  };
  html+="</table>"+
  "</div>"+
  "</div>";
   contenedor.innerHTML = html;
}

function buscarCuentaNumero(numeros,val){  
  var contenedor = document.getElementById('divResultadoBusqueda');
  //var str = numeros.replace(/^"(.*)"$/, '$1'); 
  var html="<div class='col-md-12'>"+
    "<div class='table-responsive'>"+
    "<table class='table table-condensed'>"+
      "<thead>"+
        "<tr>"+
          "<th width='15%'>Nro. Cuenta</th>"+
              "<th width='35%'>Nombre Cuenta</th>"+
              "<th width='50%'>Cuenta Auxiliar</th>"+
          "</tr>"+
      "</thead>";

      
  for (var i = 0; i < itemCuentas.length; i++) { 
    //var n = itemCuentas[i].numero.search(/+str+/);
    if(val==1){
       var n = itemCuentas[i].numero.indexOf(numeros);
       if(n!=0){
        n=-1;
       }
    }else{
      var cadenaBuscar=itemCuentas[i].nombre.toLowerCase();
      var n = cadenaBuscar.indexOf(numeros.toLowerCase());
    }
  var nren=n;  
  var nombreAuxiliar = $("#cuenta_auxiliar_modal").val();  
  if($("#nro_cuenta").val()==""&&$("#cuenta").val()==""&&$("#cuenta_auxiliar_modal").val()!=""){
    n=-1;
      for (var j = 0; j < itemCuentasAux.length; j++) {
          var nn=-1;
          var cadenaBuscarAux=itemCuentasAux[j].nombre.toLowerCase();
          nn = cadenaBuscarAux.indexOf(nombreAuxiliar.toLowerCase());
          if(itemCuentasAux[j].codCuenta==itemCuentas[i].codigo){
            if(nn>=0){
             n=1;
             break;
            }
          }    
      }
  }else{
   if(($("#nro_cuenta").val()!=""||$("#cuenta").val()!="")&&$("#cuenta_auxiliar_modal").val()!=""){
    n=-1;
      for (var j = 0; j < itemCuentasAux.length; j++) {
          var nn=-1;
          var cadenaBuscarAux=itemCuentasAux[j].nombre.toLowerCase();
          nn = cadenaBuscarAux.indexOf(nombreAuxiliar.toLowerCase());
          if(itemCuentasAux[j].codCuenta==itemCuentas[i].codigo){
            if(nn>=0&&nren>=0){
             n=1;
             break;
            }
          }    
      }
    }   
  }


    if(n>=0){
      var textoAux="<table class='table table-condensed' style='overflow-y: scroll;display: block;height:210px;'>";
      
        for (var j = 0; j < itemCuentasAux.length; j++) {
          var nn=-1;
          var cadenaBuscarAux=itemCuentasAux[j].nombre.toLowerCase();
          nn = cadenaBuscarAux.indexOf(nombreAuxiliar.toLowerCase());
          if(itemCuentasAux[j].codCuenta==itemCuentas[i].codigo){
            if(nn>=0){
             textoAux+="<tr >"+
               "<td class='text-left small'>"+itemCuentasAux[j].codigo+"</td>"+
               "<td class='text-left small'><a href=\"javascript:setBusquedaCuenta(\'"+itemCuentas[i].codigo+"\',\'"+itemCuentas[i].numero+"\',\'"+itemCuentas[i].nombre+"\',\'"+itemCuentasAux[j].codigo+"\',\'"+itemCuentasAux[j].nombre+"\');\">"+itemCuentasAux[j].nombre.toUpperCase()+"</a></td>"+
             "</tr>";  
            }      
          }
        };

       textoAux+="</table>";
       var label="";
       if(textoAux!="<table class='table table-condensed' style='overflow-y: scroll;display: block;height:210px;'></table>"){
        label='<span style="color: #0431B4;">';        
       }else{
        textoAux="<table class='table table-condensed'></table>";
        label='<span>';
       }
      html+="<tr>"+
      "<td class='text-left'>"+label+itemCuentas[i].numero+"</span></td>"+
          "<td class='text-left'><a href=\"javascript:setBusquedaCuenta(\'"+itemCuentas[i].codigo+"\',\'"+itemCuentas[i].numero+"\',\'"+itemCuentas[i].nombre+"\',\'0\',\'\');\">"+itemCuentas[i].nombre+"</a></td>"+
          "<td class='text-left'>"+textoAux+"</td>"+
      "</tr>";
    }
  };
  html+="</table>"+
  "</div>"+
  "</div>";
   contenedor.innerHTML = html;
}

// ESTADOS DE CUENTAS/////////////////////////////////////
function verEstadosCuentas(fila,cuenta){
  var tipoComprobante=parseFloat($("#tipo_comprobante").val());
  
  var debeX=0;
  var haberX=0;

  if(!isNaN(parseFloat($("#debe"+fila).val()))) { debeX=parseFloat($("#debe"+fila).val());}
  if(!isNaN(parseFloat($("#haber"+fila).val()))) { haberX=parseFloat($("#haber"+fila).val());}

  var tipo=$("#tipo_estadocuentas"+fila).val();//1 DEBE; 2 HABER
  var tipo_proveedorcliente=$("#tipo_proveedorcliente"+fila).val();//1 PROV 2 CLIENTE
  var tipo_estadocuentas_casoespecial=$("#tipo_estadocuentas_casoespecial"+fila).val();//

  var banderaContinuar=1;
  var banderaCerrarEC=0;

  console.log("debe:"+debeX+" haber:"+haberX);
  if( debeX==0 && haberX==0 ){
    $('#msgError').html("<p>Debe existir un Monto Válido ya sea en el Debe o en el Haber.</p>");
    $("#modalAlert").modal("show");
    banderaContinuar=0;
  }

  if(banderaContinuar==1){
    if( debeX>0 && tipo==2 ){
      banderaCerrarEC=1;
      $("#monto_estadocuenta").val(debeX);    
    }
    if( haberX>0 && tipo==1 ){
      banderaCerrarEC=1;
      $("#monto_estadocuenta").val(haberX);
    }
    console.log("CERRAR EC: "+banderaCerrarEC);

    var cod_cuenta=$("#cuenta"+fila).val();
    var cod_cuenta_auxiliar=$("#cuenta_auxiliar"+fila).val();
    var auxi="NO";

    if($("#edicion").length>0){
      var edicion=1;
      var codigo_comprobante=$("#codigo_comprobante").val();
    }else{
      var edicion=0;
      var codigo_comprobante=0;
    }
    if(itemEstadosCuentas[fila-1].length>0){   
      var comprobanteOrigen=itemEstadosCuentas[fila-1][0].cod_comprobantedetalle;
      var parametros={"codigo_comprobante":codigo_comprobante,"edicion":edicion,"cod_cuenta":cod_cuenta,"cod_cuenta_auxiliar":cod_cuenta_auxiliar,"tipo_comprobante":tipoComprobante,"comprobante_origen":comprobanteOrigen,"cerrar_ec":banderaCerrarEC};
    }else{
      var parametros={"codigo_comprobante":codigo_comprobante,"edicion":edicion,"cod_cuenta":cod_cuenta,"cod_cuenta_auxiliar":cod_cuenta_auxiliar,"tipo_comprobante":tipoComprobante,"cerrar_ec":banderaCerrarEC};
    }
    
    //PASA Y MOSTRAMOS LOS ESTADOS DE CUENTA    
    $.ajax({
        type: "GET",
        dataType: 'html',
        url: "../estados_cuenta/ajaxMostrarEstadosCuenta.php",
        data: parametros,
        beforeSend: function () {
        $("#texto_ajax_titulo").html("Consultando Estado de Cuentas..."); 
          iniciarCargaAjax();
        },
        success:  function (resp) {
          detectarCargaAjax();
          $("#texto_ajax_titulo").html("Procesando Datos");
          var respuesta=resp.split('@');
          $("#div_estadocuentas").html(respuesta[0]);
          //REVISAR ESTA INSTRUCCION
          if(tipo==2 && tipo_proveedorcliente==1){
            var rsaldo=listarEstadosCuentas(fila,respuesta[1]);
            console.log("listarEstadoCuentasDebito;");
            //listarEstadosCuentasDebito(fila,rsaldo);
          }else{
            var rsaldo=listarEstadosCuentasCredito(fila,respuesta[1]);
            console.log("listarEstadoCuentas;");
            //listarEstadosCuentas(fila,rsaldo);
          } 
          //mostrarSelectProveedoresClientes()          
        }
    });
    $("#estFila").val(fila);
    $("#tituloCuentaModal").html($("#divCuentaDetalle"+fila).html()); 
    $("#modalEstadosCuentas").modal("show");   
  }
}  

var itemEstadosCuentas=[];
function quitarEstadoCuenta(){
  var fila=$("#estFila").val();
  itemEstadosCuentas[fila-1]=[];
  verEstadosCuentas(fila,0);
  $("#nestado"+fila).removeClass("estado");
}

function agregarEstadoCuenta(){
  $("#mensaje_estadoscuenta").html("");
  var fila=$("#estFila").val();
  var tipo=$("#tipo_estadocuentas"+fila).val();

  var tipo_proveedorcliente=$("#tipo_proveedorcliente"+fila).val();
  
  if((tipo==2 && tipo_proveedorcliente==1)||(tipo==1 && tipo_proveedorcliente==2)){ //CUMPLE LA CONDICION DE TIPO: PROVEEDOR Y HABER 
    //if((tipo==2 && tipo_proveedorcliente==1)){
      var cuenta=0;
      var codComproDet=0;
      var nfila={
      cod_plancuenta:cuenta,
      cod_plancuentaaux:$("#cuenta_auxiliar"+fila).val(),
      cod_comprobantedetalle:codComproDet,
      cod_proveedor:0,//$("#proveedores").val(),
      monto:$("#monto_estadocuenta").val()
     }    
    //}else{ 
    //}

    itemEstadosCuentas[fila-1]=[];
    itemEstadosCuentas[fila-1].push(nfila);
    $("#nestado"+fila).addClass("estado");
    verEstadosCuentas(fila,cuenta);
  }else{
    var resp = $("#cuentas_origen").val().split('###');
    var cuenta = resp[0];
    var detalle_resp=$('input:radio[name=cuentas_origen_detalle]:checked').val();
    var codComproDet=detalle_resp[0];
    var cuenta_auxiliar=detalle_resp[1];
    if(detalle_resp[0]!=null){
      if(resp[1]=="AUX"){
        var nfila={
          cod_plancuenta:0,
          cod_plancuentaaux:cuenta,
          cod_comprobantedetalle:codComproDet,
          cod_proveedor:0,//$("#proveedores").val(),
          monto:$("#monto_estadocuenta").val()
        }
      }else{
        var nfila={
          cod_plancuenta:cuenta,
          cod_plancuentaaux:cuenta_auxiliar,
          cod_comprobantedetalle:codComproDet,
          cod_proveedor:0,//$("#proveedores").val(),
          monto:$("#monto_estadocuenta").val()
        }    
      }
      itemEstadosCuentas[fila-1]=[];
      itemEstadosCuentas[fila-1].push(nfila);
      $("#nestado"+fila).addClass("estado");
      verEstadosCuentas(fila,cuenta);
    }else{
      $("#mensaje_estadoscuenta").html("<label class='text-danger'>Debe seleccionar un registro en la tabla</label>");
    }
  }
}

function verLibretasBancarias(fila){
  $("#indice").val(fila);
  $("#modalListaLibretaBancaria").modal("show"); 
  //ocultar elementos de modal en sol de facturacion
  $("#boton_libreta_detalle_todo").addClass("d-none");
  $("#modal_descripcion_pie").addClass("d-none");
  $("#boton_libreta_detalle_facturas").addClass("d-none");
  $("#contenedor_cabecera_libreta_bancaria").html("<label class='font-weight-bold'>"+$("#descripcion_detallelibreta"+fila).val()+"</label>");
}
function agregarEstadoCuentaCerrar(filaXXX,valor){
  //console.log("entro:"+fila+" "+valor);
  $("#mensaje_estadoscuenta").html("");
  
  var fila=$("#estFila").val();
  var tipo=$("#tipo_estadocuentas"+fila).val();
  var tipo_proveedorcliente=$("#tipo_proveedorcliente"+fila).val();
  var cuenta=$("#cuenta"+fila).val();
  var montoCerrar=$("#monto_estadocuenta").val();

  console.log(tipo+" "+tipo_proveedorcliente);
  
  //var resp = $("#cuentas_origen").val().split('###');
  var detalle_resp=valor.split('####');
  console.log("antes: "+detalle_resp);
  var codComproDet=detalle_resp[0];
  var cuenta_auxiliar=detalle_resp[1];
  var saldo_estadocuenta=parseFloat(detalle_resp[3]);
  if(detalle_resp[0]!=null && montoCerrar<=saldo_estadocuenta){
    console.log("entro y DetalleResp: "+detalle_resp);
    var nfila={
      cod_plancuenta:cuenta,
      cod_plancuentaaux:cuenta_auxiliar,
      cod_comprobantedetalle:codComproDet,
      cod_proveedor:0,//$("#proveedores").val(),
      monto:$("#monto_estadocuenta").val()
    }    
    itemEstadosCuentas[fila-1]=[];
    itemEstadosCuentas[fila-1].push(nfila);
    
    console.log("EC:**"+JSON.stringify(itemEstadosCuentas[fila-1]));

    $("#nestado"+fila).addClass("estado");
    //verEstadosCuentas(fila,cuenta);
    $('#modalEstadosCuentas').modal('hide');
  }else{
    Swal.fire('Estados de Cuenta!','El monto a Cerrar no puede ser mayor al saldo!','warning'); 
   // $("#mensaje_estadoscuenta").html("<label class='text-danger'>El monto a Cerrar no puede ser mayor al saldo.</label>");
  }
}


function listarEstadosCuentasCredito(id,saldo){
   var rsaldo = parseFloat(saldo);
    for (var j = 0; j < itemEstadosCuentas[id-1].length; j++) {
      var cuentaOrigen =itemEstadosCuentas[id-1][j].cod_plancuenta;
       for (var i = 0; i < numFilas; i++) {
         if($("#cuenta"+(i+1)).val()==cuentaOrigen){
            rsaldo=rsaldo+parseFloat(itemEstadosCuentas[i][0].monto); // 0 porque utilizamos solo un item
            listarEstadosCuentas(i+1,saldo);
         }
       }
     }                  
    return rsaldo;
}
function listarEstadosCuentasDebito(id,saldo){
   var cuentaOrigen =$("#cuenta"+id).val();
   var rsaldo = parseFloat(saldo);
   for (var i = 0; i < numFilas; i++) {
    for (var j = 0; j < itemEstadosCuentas[i].length; j++) {
       var cuenta = itemEstadosCuentas[i][j].cod_plancuenta;
       if(cuentaOrigen==cuenta){
        rsaldo=rsaldo-parseFloat(itemEstadosCuentas[i][j].monto);
        listarEstadosCuentas(i+1,saldo);
       }
     }                  
  }
  return rsaldo;
}
 function listarEstadosCuentas(id,saldo){
     if($("#tipo_comprobante").val()==3){
        var estiloMonto="text-success";
      }else{
        var estiloMonto="text-danger";
      }

  var table = $('#tabla_estadocuenta');
   for (var i = 0; i < itemEstadosCuentas[id-1].length; i++) {
     var row = $('<tr>').addClass('bg-white');
     row.append($('<td>').addClass('text-left').text(""));
     row.append($('<td>').addClass('text-left').text(""));
     row.append($('<td>').addClass('text-left').text(""));
     row.append($('<td>').addClass('text-left').text(""));

     row.append($('<td>').addClass('text-left').text(""));
     row.append($('<td>').addClass('text-left').text(""));
     //row.append($('<td>').addClass('text-left text-success').text($("#glosa_detalle"+id).val()));
     
     var tipo=$("#tipo_estadocuentas"+id).val();
     var tipo_proveedorcliente=$("#tipo_proveedorcliente"+id).val();
      if(tipo==2 && tipo_proveedorcliente==1){
        row.append($('<td>').addClass('text-left '+estiloMonto+'').text($("#glosa_detalle"+id).val()));
        var nsaldo=parseFloat(saldo)+parseFloat(itemEstadosCuentas[id-1][i].monto);
        row.append($('<td>').addClass('text-right '+estiloMonto+'').text(""));   
        row.append($('<td>').addClass('text-right '+estiloMonto+'  font-weight-bold').text(""));
      }else{
        var titulo_glosa="";
        if(itemEstadosCuentas[id-1][i].cod_comprobantedetalle!=0){
          titulo_glosa=obtieneDatosFilaEstadosCuenta(itemEstadosCuentas[id-1][i].cod_comprobantedetalle);
        }
        row.append($('<td>').addClass('text-left '+estiloMonto+'').html($("#glosa_detalle"+id).val()+"<small class='text-success'>"+titulo_glosa+"</small>"));
        var nsaldo=parseFloat(saldo)-parseFloat(itemEstadosCuentas[id-1][i].monto);
        //para listar los estados de cliente nuevos en el campo de credito
        if(tipo==2 && tipo_proveedorcliente==2){
          row.append($('<td>').addClass('text-right text-success').text(""));
          row.append($('<td>').addClass('text-right text-success font-weight-bold').text(""));  
        }else{
          row.append($('<td>').addClass('text-right text-success font-weight-bold').text(""));  
          row.append($('<td>').addClass('text-right text-success').text(""));       
        }
      }
      
      row.append($('<td>').addClass('text-right '+estiloMonto+' font-weight-bold').text(numberFormat(itemEstadosCuentas[id-1][i].monto,2)));
      row.append($('<td>').addClass('text-left').text(""));
      row.append($('<td>').addClass('text-left').text(""));
     table.append(row);
     return nsaldo;
   }
 }
 function verEstadosCuentasCred(){
  var fila = $("#estFila").val();
  var resp = $("#cuentas_origen").val().split('###');
  var cuenta = resp[0];
  verEstadosCuentas(fila,cuenta);
  /*if(cuenta!=""){
    var parametros={"cod_cuenta":cuenta};
    $.ajax({
        type: "GET",
        dataType: 'html',
        url: "../estados_cuenta/ajaxCargarDetalleCuenta.php",
        data: parametros,
        success:  function (resp) {
          $("#div_cuentasorigendetalle").html(resp); 
          $('.selectpicker').selectpicker("refresh");       
        }
    });
  }*/
 }
function obtieneDatosFilaEstadosCuenta(cod){
  var dato="";
 var parametros={"cod_det":cod};
    $.ajax({
        type: "GET",
        dataType: 'html',
        url: "../estados_cuenta/ajaxCargarDetalleCuenta.php",
        data: parametros,
        async:false,
        success:  function (resp) {
          dato=resp;       
        }
    });
    return dato; 
}
 function verDetalleEstadosCuenta(){
  if($(".det-estados").is(":visible")){
    $(".det-estados").hide();
  }else{
    $(".det-estados").show();
  }
}
function verDetalleEstadosCuenta2(i){
  if($(".det-estados-"+i).is(":visible")){
    $(".det-estados-"+i).hide();
  }else{
    $(".det-estados-"+i).show();
  }
}
function verEstadosCuentasModal(cuenta,cod_cuenta,cod_cuentaaux,tipo,tipo_proveedorcliente){
   var parametros={"cod_cuenta":cod_cuenta,"cod_cuentaaux":cod_cuentaaux,"tipo":tipo,"tipo_proveedorcliente":tipo_proveedorcliente,"mes":12};
    $.ajax({
        type: "GET",
        dataType: 'html',
        url: "estados_cuenta/ajaxListEstadoCuenta.php",
        data: parametros,
        beforeSend: function () {
        $("#texto_ajax_titulo").html("Consultando los estados de cuenta..."); 
          iniciarCargaAjax();
        },
        success:  function (resp) {
          detectarCargaAjax();
          $("#texto_ajax_titulo").html("Procesando Datos");
          var respuesta=resp.split('@');
          $("#div_estadocuentas").html(respuesta[0]);
          $("#titulo_cuenta").html(cuenta);
          $("#modalCuentas").modal("show");          
        }
    });
    
}
 function numberFormat(amount, decimals) {
   var sign = (amount.toString().substring(0, 1) == "-");
    amount += ''; // por si pasan un numero en vez de un string
    amount = parseFloat(amount.replace(/[^0-9\.]/g, '')); // elimino cualquier cosa que no sea numero o punto

    decimals = decimals || 0; // por si la variable no fue fue pasada

    // si no es un numero o es igual a cero retorno el mismo cero
    if (isNaN(amount) || amount === 0) 
        return parseFloat(0).toFixed(decimals);

    // si es mayor o menor que cero retorno el valor formateado como numero
    amount = '' + amount.toFixed(decimals);

    var amount_parts = amount.split('.'),
        regexp = /(\d+)(\d{3})/;

    while (regexp.test(amount_parts[0]))
        amount_parts[0] = amount_parts[0].replace(regexp, '$1' + ',' + '$2');

    return  sign ? '-' + amount_parts.join('.') : amount_parts.join('.');  //amount_parts.join('.');
}


//distribucion gastos porcentaje
function sumarPorcentaje() {
  var sumaTotal = 0;
  var formulariop = document.getElementById("form1");
  for (var i = 0; i < formulariop.elements.length; i++) {
    if (formulariop.elements[i].id.indexOf("porcentaje") != -1) {
      //console.log("input: "+formulariop.elements[i].value);    
      sumaTotal += (formulariop.elements[i].value) * 1;
    }
  }
  //console.log("suma: "+sumaTotal); 
  document.getElementById("total").value = sumaTotal;
  var boton = document.getElementById("botonGuardar");
  if (sumaTotal == 100) {
    boton.disabled = false;
  } else {
    boton.disabled = true;
  }
}


function ajaxAreaContabilizacionDetalle(combo){
  var contenedor;
  var codigo_UO=combo.value;
  contenedor = document.getElementById('div_contenedor_area');
  ajax=nuevoAjax();
  ajax.open('GET', 'rrhh/area_contabilizacion_detalle_ajax.php?codigo_UO='+codigo_UO,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);          
    }
  }
  ajax.send(null)  
}//unidad_area-cargo

function ajaxAreaUOCAJACHICA(combo){
  var contenedor;
  var codigo_UO=combo.value;
  contenedor = document.getElementById('div_contenedor_area');
  ajax=nuevoAjax();
  ajax.open('GET', 'caja_chica/area_uo_ajax.php?codigo_UO='+codigo_UO,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);   
      ajaxUOProyFinanciacion(codigo_UO);       
    }
  }
  ajax.send(null)  
}//unidad_area-cargo

function ajaxUOProyFinanciacion(codigo_UO){
  var contenedor;
  contenedor = document.getElementById('div_contenedor_actividad');
  ajax=nuevoAjax();
  ajax.open('GET', 'caja_chica/ajax_uo_proyfinanciacion.php?codigo_UO='+codigo_UO,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);
    }
  }
  ajax.send(null)  
}
function ajaxUOArea_personal_tipocajachica(combo){
  var contenedor;
  var codigo_UO=combo.value;
  contenedor = document.getElementById('div_contenedor_area_tcc');
  ajax=nuevoAjax();
  ajax.open('GET', 'caja_chica/uo_area_personal_ajax.php?codigo_UO='+codigo_UO,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);

      ajaxPersonalUOCajaChica(codigo_UO);          
    }
  }
  ajax.send(null)  
}//personal - uo-area tipo caja chica
function ajaxPersonalUOCajaChica(codigo_UO){
  var contenedor;
  contenedor = document.getElementById('div_personal_UO_tcc');
  ajax=nuevoAjax();
  ajax.open('GET', 'caja_chica/uoPersonalAjax.php?codigo_UO='+codigo_UO,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);
    }
  }
  ajax.send(null)  
}

function ajaxAreaCargos(codigo_uo,combo){
  var contenedor;
  //var codigo_uo=document.getElementById("cod_uo").value;
  var codigo_area=combo.value;
  //alert(codigo_uo+'-'+codigo_area);

  contenedor = document.getElementById('div_contenedor_cargo');
  ajax=nuevoAjax();
  ajax.open('GET', 'personal/personal_area_cargos_ajax.php?codigo_uo='+codigo_uo+'&codigo_area='+codigo_area,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);          
    }
  }
  ajax.send(null)  
}//ajax unidad - personal
function ajaxOficinaPersonal(combo){
  var contenedor;
  var codigo_UO=combo.value;
  contenedor = document.getElementById('div_contenedor_personal');
  ajax=nuevoAjax();
  ajax.open('GET', 'personal/personal_unidad_ajax.php?codigo_UO='+codigo_UO,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);          
    }
  }
  ajax.send(null)  
}//unidad_area-cargo

var montos_personal=[]; 
function registrarMontoPersonal(index){
 var monto = $("#monto_detalle"+index).val();
 montos_personal[index-1].monto=monto;
}

function calcularHaberBasico(){
  var id = $("#personal").val();
  /*var contenedor;
  contenedor = document.getElementById('monto_max');*/
  ajax=nuevoAjax();
  ajax.open('GET', 'anticipos_personal/ajaxCalcularHaberBasico.php?codigo='+id,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      /*contenedor.innerHTML = ajax.responseText;*/
      if(ajax.responseText!="NN"){
       $("#monto").val(ajax.responseText/2);
       $("#haber_basico2").val(ajax.responseText/2);
       $("#haber_basico").val(ajax.responseText);  
      }  
      $('.selectpicker').selectpicker(["refresh"]);          
    }
  }
  ajax.send(null) 
}
function montoNoMayor(){
  if($("#monto").val()>$("#haber_basico2").val()){
    $("#mensaje").html("<p class='text-danger'>El monto no puede ser mayor al 50% del haber básico</p>");
  }else{
    $("#mensaje").html("");
  }
}

function ponerSiguienteAnio(ges){
  var mes = $("#desde").val();
  $("#hasta option").each(function(){
        if ($(this).val() != "0" ){ 
          $(this).removeAttr("disabled");
          if(parseInt($(this).val())<= parseInt(mes)){
            $(this).attr("disabled",true);
          }       
        }
  });
  $('.selectpicker').selectpicker("refresh");
}

function ponerDescripcionEvento(){
  var evento= $("#evento").val();
  if(evento!=""){
    var respuesta=evento.split('@');
     $("#descripcion").val(respuesta[1]);  
  }else{
    $("#descripcion").val(""); 
  }
}
function ponerCorreoPersona(){
  var persona= $("#personal").val();
  if(persona!=""){
    var respuesta=persona.split('$$$');
     $("#correo").val(respuesta[1]);  
  }else{
    $("#correo").val(""); 
  }
}

function enviarCorreoEvento(){
   if($("#correo").val()==""||$("#correo").val()=="NN"){
        Swal.fire('Informativo!','El correo no debe estar vacío','warning');  
   }else{
     if($("#evento").val()==""||$("#personal").val()==""){
        Swal.fire('Informativo!','Debe completar los datos','warning');  
      }else{
        //enviar correo
        Swal.fire({
         title: '¿Esta seguro de enviar el correo?',
         showCancelButton: true,
         confirmButtonText: 'Enviar',
         showLoaderOnConfirm: true,
         closeOnConfirm: false
       }).then((result) => {
          if (result.value) {
               enviarCorreoEventoAjax();            
            return(true);
          } else if (result.dismiss === Swal.DismissReason.cancel) {
            return(false);
          }
        });
      }  
    }  
}
function registrarCorreoEvento(){
   if($("#correo").val()==""||$("#correo").val()=="NN"){
        Swal.fire('Informativo!','El correo no debe estar vacío','warning');  
   }else{
     if($("#evento").val()==""||$("#personal").val()==""){
        Swal.fire('Informativo!','Debe completar los datos','warning');  
      }else{
        registrarCorreoEventoAjax();
      }  
    }  
}

function registrarCorreoEventoAjax(){
   var evento=$("#evento").val();
  var personal=$("#personal").val();
  var respuesta=evento.split('@');
  var respuesta2=personal.split('$$$');
  $("#boton_enviocorreo").attr("disabled",true); 
  var parametros={"evento":respuesta[0],"personal":respuesta2[0],"correo":respuesta2[1],"titulo":respuesta[2]};
 $.ajax({
    url: "notificaciones_sistema/save.php",
    type: "GET",
    data: parametros,
    dataType: "html",
    success: function (resp) {
      var envio=resp.split('$$$');
      if(parseInt(envio[0])==1){
         Swal.fire("Registro Exitoso!", "Se registradon los datos exitosamente!", "success")
             .then((value) => {
             location.href="index.php?opcion=listNotificacionesSistema";
         });
        //redireccionar
      }else{
        Swal.fire("Error de envio!", "Contactese con el administrador", "error");
      }
      $("#boton_enviocorreo").removeAttr("disabled");      
    },
    error: function (xhr, ajaxOptions, thrownError) {
        Swal.fire("Error de envio!", "Contactese con el administrador", "error");
        $("#boton_enviocorreo").removeAttr("disabled");
    }
  });  
}
function enviarCorreoEventoAjax(){
  var evento=$("#evento").val();
  var personal=$("#personal").val();
  var respuesta=evento.split('@');
  var respuesta2=personal.split('$$$');
  var mensaje=$("#mensaje").val();
  $("#boton_enviocorreo").attr("disabled",true); 
  var parametros={"evento":respuesta[0],"personal":respuesta2[0],"correo":respuesta2[1],"mensaje":mensaje,"titulo":respuesta[2]};
 $.ajax({
    url: "notificaciones_sistema/sendEmail.php",
    type: "GET",
    data: parametros,
    dataType: "html",
    success: function (resp) {
      var envio=resp.split('$$$');
      if(parseInt(envio[0])==1){
         Swal.fire("Mensaje enviado!", "El correo se envio a "+envio[1]+" exitosamente!", "success")
             .then((value) => {
             location.href="index.php?opcion=listNotificacionesSistema";
         });
        //redireccionar
      }else{
        Swal.fire("Error de envio!", "Verifique sus datos e intentelo de nuevo", "error");
      }
      $("#boton_enviocorreo").removeAttr("disabled");      
    },
    error: function (xhr, ajaxOptions, thrownError) {
        Swal.fire("Error de envio!", "Verifique sus datos e intentelo de nuevo", "error");
        $("#boton_enviocorreo").removeAttr("disabled");
    }
  });
}

function mandarDatosBonoIndefinido(){
  var datos=$("#personal").val();
  var respuesta=datos.split('@');
  $("#monto").val(respuesta[1]);
  $("#obs").val(respuesta[2]);
}

function editarDatosSimulacion(){
  var cod_i=$("#cod_ibnorca").val();
  var nombre_s=$("#nombre").val();
  var fecha_s=$("#fecha_curso").val();
  $("#modal_nombresim").val(nombre_s);
  $("#modal_fechacurso").val(fecha_s);
  //$("#modal_tiposim").val(cod_i);
  //$('.selectpicker').selectpicker("refresh");

  $("#modalEditSimulacion").modal("show");
}

function guardarDatosSimulacion(btn_id){
  var codigo_s=$("#cod_simulacion").val();
   var nombre_s=$("#modal_nombresim").val();
   var fecha_s=$("#modal_fechacurso").val();
   //var cod_i=$("#modal_tiposim").val();
   var cod_i=1;   
   var parametros={"codigo":codigo_s,"nombre":nombre_s,"ibnorca":cod_i,"fecha_curso":fecha_s};

  if(nombre_s!=""){
  $("#"+btn_id).attr("disabled",true); 
  $.ajax({
    url: "ajaxSaveDatosSimulacion.php",
    type: "GET",
    data: parametros,
    dataType: "html",
    success: function (resp) {   
     Swal.fire("Correcto!", "El proceso fue satisfactorio!", "success");
     $("#"+btn_id).removeAttr("disabled"); 
     $("#nombre").val(nombre_s);
     $("#fecha_curso").val(fecha_s);
     $("#titulo_curso").text(nombre_s);
     $("#cod_ibnorca").val(cod_i);
     if(cod_i==1){
       $("#ibnorca").val("IBNORCA");
       //$("#tipo_ibnorca").text("IBNORCA");  
     }else{
       $("#ibnorca").val("FUERA DE IBNORCA");
       //$("#tipo_ibnorca").text("FUERA DE IBNORCA");  
     }
     $("#modalEditSimulacion").modal("hide");        
    },
    error: function (xhr, ajaxOptions, thrownError) {
     Swal.fire("Error de envio!", "Verifique los datos e intentelo de nuevo", "error");
      $("#"+btn_id).removeAttr("disabled"); 
    }
  });    
  }else{
    Swal.fire("Informativo!", "Debe llenar todos los campos", "warning");
  }
}
function editarDatosPlantilla(){
  $("#modal_diasauditoria").val($("#dias_plan").val());
  $("#modal_utibnorca").val($("#utilidad_minlocal").val());
  $("#modal_utifuera").val($("#utilidad_minext").val());
  $("#modal_alibnorca").val($("#alumnos_plan").val());
  $("#modal_alfuera").val($("#alumnos_plan_fuera").val());
  $("#modal_importeplan").val($("#cod_precioplantilla").val());


  if($("#modal_productos").length){ 
     $("#modal_productos").val($("#productos_sim").val());
     $("#modal_productos").tagsinput('removeAll');
     $("#modal_productos").tagsinput('add', $("#productos_sim").val());
  }
  if($("#modal_sitios").length){ 
     $("#modal_sitios").val($("#sitios_sim").val());
     $("#modal_sitios").tagsinput('removeAll');
     $("#modal_sitios").tagsinput('add', $("#sitios_sim").val());
  }
   //mostrar sitios y o productos
   listarAtributo();
  if($("#num_tituloservicios1").length){
     $("#num_tituloservicios1").html("("+($("#modal_numeroservicio1").val()-1)+")");
  }
  if($("#num_titulopersonal1").length){
     $("#num_titulopersonal1").html("("+($("#modal_numeropersonal1").val()-1)+")");
  }
  $('.selectpicker').selectpicker("refresh");
 $("#modalEditPlantilla").modal("show"); 
}

function editarDatosPlantillaSec(){
  $("#modal_diasauditoria").val($("#dias_plan").val());
  $("#modal_utibnorca").val($("#utilidad_minlocal").val());
  $("#modal_utifuera").val($("#utilidad_minext").val());
  $("#modal_alibnorca").val($("#alumnos_plan").val());
  $("#modal_alfuera").val($("#alumnos_plan_fuera").val());
  $("#modal_importeplan").val($("#cod_precioplantilla").val());
  $("#modal_diascurso").val($("#dias_curso").val());
  if($("#modal_importeplanedit").length>0){
    $("#modal_importeplanedit").val(redondeo(parseFloat($('#modal_importeplan option:selected').text())));
  }
  $('.selectpicker').selectpicker("refresh");
 $("#modalEditPlantilla").modal("show"); 
}

function guardarDatosPlantilla(btn_id){
  var codigo_p=$("#cod_plantilla").val();
  var cod_sim=$("#cod_simulacion").val();
   var ut_i=$("#modal_utibnorca").val();
   var ut_f=$("#modal_utifuera").val();
   var al_i=$("#modal_alibnorca").val();
   var al_f=$("#modal_alfuera").val(); 
   var dias_curso=$("#modal_diascurso").val(); 
   var precio_p=$("#modal_importeplan").val();
   var precio_pedit=$("#modal_importeplanedit").val();  
   var precio_alternativo=$("#total_preciosimulacion").val();
   var parametros={"dias_curso":dias_curso,"cod_sim":cod_sim,"codigo":codigo_p,"ut_i":ut_i,"ut_f":ut_f,"al_i":al_i,"al_f":al_f,"precio_p":precio_p,"precio_pedit":precio_pedit,"precio_alternativo":precio_alternativo};

  if(!(ut_i==""||ut_f==""||al_i==""||al_f=="")){
    var cantidadFilas=$("#cantidad_filasprecios").val();
    var error=0;
    var mensajeError="";
    for (var i = 1; i <= parseInt(cantidadFilas); i++) {
      if($("#total_alumnosAAA"+i).length>0){
         if($("#cantidad_alumnosAAA"+i).val()==""||$("#porcentaje_alumnosAAA"+i).val()==""||$("#monto_alumnosAAA"+i).val()==""){
          mensajeError="Detalles Precio: No debe existir campos vacíos";
          error=1;
         }
      }  
    };
    for (var i = 1; i <= parseInt(cantidadFilas); i++) {
      if($("#total_alumnosAAA"+i).length>0){
         if($("#cantidad_alumnosAAA"+i).val()<=0||$("#monto_alumnosAAA"+i).val()<=0){
          mensajeError="Detalles Precio: No debe existir campos negativos o valores 0";
          error=1;
         }
      }  
    };
    
    for (var i = 1; i <= parseInt(cantidadFilas); i++) {
      if($("#total_alumnosAAA"+i).length>0){
         if($("#porcentaje_alumnosAAA"+i).val()>100){
          mensajeError="Detalles Precio: No debe existir porcentajes Mayores a 100";
          error=1;
         }
      }  
    };
   if(error==0){
     $("#"+btn_id).attr("disabled",true); 
  $.ajax({
    url: "ajaxSaveDatosPlantilla.php",
    cache: false,
    type: "GET",
    data: parametros,
    dataType: "html",
    beforeSend: function () { 
     iniciarCargaAjax();  
    },
    success: function (resp) { 
      detectarCargaAjax();

      if(!($("#id_servicioibnored").length)){
        alerts.showSwal('success-message','registerSimulacion.php?cod='+cod_sim);
      }else{
        var q=$("#id_servicioibnored").val();
        var s=$("#id_servicioibnored_s").val();
        var u=$("#id_servicioibnored_u").val();
        alerts.showSwal('success-message','registerSimulacion.php?cod='+cod_sim+'&q='+q+'&s='+s+"&u="+u);
      } 

     $("#"+btn_id).removeAttr("disabled"); 
      var precios=resp.split('$$$');
      $("#precio_local").val(precios[0].trim());
      $("#precio_externo").val(precios[1].trim());
      $("#cod_precioplantilla").val(precio_p);

      $("#utilidad_minlocal").val(ut_i);
      $("#utilidad_minext").val(ut_f);
      $("#alumnos_plan").val(al_i);
      $("#alumnos_plan_fuera").val(al_f);    

     $("#modalEditPlantilla").modal("hide");
     $("#narch").addClass("estado");
      guardarPreciosDetalle();        
    },
    error: function (xhr, ajaxOptions, thrownError) {
     Swal.fire("Error de envio!", "Verifique los datos e intentelo de nuevo", "error");
      $("#"+btn_id).removeAttr("disabled"); 
    }
  }); 
   }else{
      Swal.fire("Informativo!", mensajeError, "warning");
   }
     
  }else{
    Swal.fire("Informativo!", "Debe llenar todos los campos", "warning");
  }
}

function guardarDatosPlantillaServicio(ib){
  var conta=0; var contaRead=0;
  var cosSim=$("#cod_simulacion").val();
   if($("#divResultadoListaAtributos").length){
    var inicioAnio=0;
   }else{
    var inicioAnio=1;
   }
  /*PARA PERSONAL*/
  var anios=$("#anio_simulacion").val();
  for (var anio=inicioAnio; anio<=anios; anio++) {
  var total= $("#modal_numeropersonal"+anio).val(); 
  if((total-1)!=0){
    for (var i=1;i<=(total-1);i++){
      if($("#modal_montopretotal"+anio+"FFF"+i).val()==""||$("#modal_montopre"+anio+"FFF"+i).val()==""){
        conta++
      }
      if($("#modal_montopretotal"+anio+"FFF"+i).is("[readonly]")){
        contaRead++
      }
    }    
   }
  };
  /* FIN PARA PERSONAL*/ 


  /* PARA SERVICIOS*/
  for (var anio=inicioAnio; anio<=anios; anio++) {
   var total= $("#modal_numeroservicio"+anio).val(); 
  if((total-1)!=0){
    for (var i=1;i<=(total-1);i++){
      if($("#modal_montoservtotal"+anio+"SSS"+i).val()==""||$("#modal_montoserv"+anio+"SSS"+i).val()==""){
        conta++
      }
      if($("#modal_montoservtotal"+anio+"SSS"+i).is("[readonly]")){
        contaRead++
      }
    }    
   }
    
  }

  //PARA QUE NO PREGUNTE SI HAY CAMPOS DESHABILITADOS
  contaRead=0;


  /* FIN PARA SERVICIOS */    
  if(conta==0){
    if(contaRead==0){
      guardarDatosPlantillaServicioAjax(ib);
    }else{
        Swal.fire({
         title: 'Advertencia!',
         text: "Hay uno o más registros deshabilitados ¿Desea Continuar?",
         type: 'warning',
         showCancelButton: true,
         confirmButtonClass: 'btn btn-info',
         cancelButtonClass: 'btn btn-danger',
         confirmButtonText: 'Si',
         cancelButtonText: 'No',
         buttonsStyling: false
       }).then((result) => {
          if (result.value) {
               guardarDatosPlantillaServicioAjax(ib);            
            return(true);
          } else if (result.dismiss === Swal.DismissReason.cancel) {
            return(false);
          }
        });
    }
   }else{
    Swal.fire('Informativo!','Todos los campos son requeridos!','warning'); 
   } 
}

function guardarDatosPlantillaServicioAjax(btn_id){
  var codigo_p=$("#cod_plantilla").val();
  var cod_sim=$("#cod_simulacion").val();
  var ut_i=$("#modal_utibnorca").val();
  var dia=$("#modal_diasauditoria").val();
  if($("#modal_productos").length){
   //var productos=$("#modal_productos").val();
   var tcs=0;
  }else{
    //var productos=$("#modal_sitios").val();
    var tcs=1;
  }
  var productos=itemAtributos;

  //var respuesta=productos.split(',');
  //var num_prod=respuesta.length;
  if($("#divResultadoListaAtributos").length){
    var inicioAnio=0;
    var atributosDias=JSON.stringify(itemAtributosDias);
    var auditoresDias=[];
    for (var au = 0; au < itemAtributosDias.length; au++) {
      var mySelections = [];
        $('#auditores'+au+' option').each(function(i) {
            if (this.selected == true) {
                mySelections.push(this.value+"####SI");
            }else{
                mySelections.push(this.value+"####NO");
            }
        });
      auditoresDias[au]=mySelections;
    };
    auditoresDias=JSON.stringify(auditoresDias);
    console.log(auditoresDias);
   }else{
    var inicioAnio=1;
    var atributosDias="";
    var auditoresDias="";
   }

if(!(ut_i==""||dia==""||dia==0||productos.length==0)){ 
  /*PARA PERSONAL*/
  var anios=$("#anio_simulacion").val();
  /*for (var anio=inicioAnio;anio<=anios; anio++) {
  var total=$("#modal_numeropersonal"+anio).val();
  for (var i = 1; i <=(total-1); i++) {
     var habilitado=1;
      var codigo = $("#modal_codigopersonal"+anio+"FFF"+i).val();
      var monto = $("#modal_montopre"+anio+"FFF"+i).val();
      var montol = $("#modal_montopreloc"+anio+"FFF"+i).val();
      var montoe = $("#modal_montopreext"+anio+"FFF"+i).val();
      var extlocal = $("#local_extranjero"+anio+"FFF"+i).val();
      var cantidad = $("#cantidad_personal"+anio+"FFF"+i).val();
      var cantidadTotal = $("#modal_cantidadpersonal").val();
      var dias = $("#dias_personal"+anio+"FFF"+i).val();
      if($("#modal_montopre"+anio+"FFF"+i).is("[readonly]")){
        habilitado=0;
      }
      var parametros = {"codigo":codigo,"extlocal":extlocal,"montoe":montoe,"montol":montol,"monto":monto,"simulacion":cod_sim,"sitios_dias":atributosDias,"productos":JSON.stringify(productos),"plantilla":codigo_p,"dia":dia,"utilidad":ut_i,"habilitado":habilitado,"cantidad":cantidad,"dias":dias,"cantidadT":cantidadTotal,"tcs":tcs};
      $.ajax({
        type:"GET",
        data:parametros,
        url:"ajaxSaveDatosPlantilla.php",
        beforeSend: function () { 
          iniciarCargaAjax();
        },
        success:function(resp){

          //detectarCargaAjax();
          //alerts.showSwal('success-message','registerSimulacion.php?cod='+cod_sim);
        }
      });
   };      
  };*/
  /* FIN PARA PERSONAL*/ 
  /*PARA SERVICIOS*/ 
  var anio=0; //para los servicios en una sola tabla
  //for (var anio=inicioAnio;anio<=anios; anio++) {
  var total=$("#modal_numeroservicio"+anio).val();
  for (var i = 1; i <=(total-1); i++) {
     var habilitado=1;
      var codigo = $("#modal_codigoservicio"+anio+"SSS"+i).val();
      var monto = $("#modal_montoserv"+anio+"SSS"+i).val();
      var descripcion = $("#descripcion_servicios"+anio+"SSS"+i).val();
      var cantidad = $("#cantidad_servicios"+anio+"SSS"+i).val();
      var unidad = $("#unidad_servicios"+anio+"SSS"+i).val();
      var precio_fijo=$("#precio_fijo"+anio+"SSS"+i).val();
      var anio_fila=$("#anio"+anio+"SSS"+i).val();
      if($("#modal_montoserv"+anio+"SSS"+i).is("[readonly]")){
        habilitado=0;
      }
      var alcance="";
      if($("#modal_alcance").length>0){
        alcance=$("#modal_alcance").val();
      }
      var des_serv = $("#modal_des_serv").val();
      var parametros = {"des_serv":des_serv,"alcance":alcance,"auditoresDias":auditoresDias,"descripcion":descripcion,"codigo":codigo,"monto":monto,"simulacion":cod_sim,"sitios_dias":atributosDias,"productos":JSON.stringify(productos),"precio_fijo":precio_fijo,"unidad":unidad,"plantilla":codigo_p,"dia":dia,"utilidad":ut_i,"habilitado":habilitado,"cantidad":cantidad,"anio":anio,"iteracion":i,"tcs":tcs,"anio_fila":anio_fila};
      $.ajax({
        type:"POST",
        data:parametros,
        url:"ajaxSaveDatosPlantilla2.php",
        beforeSend: function () { 
          iniciarCargaAjax();
        },
        success:function(resp){
          var respu=resp.split('WWW');
          if(parseInt(respu[0])==anios&&parseInt(respu[1])==(total-1)){
            detectarCargaAjax();
            
          }   
         //alert(resp);
        }
      });
  }; 
    
  //}
  if(!($("#id_servicioibnored").length)){
      alerts.showSwal('success-message','registerSimulacion.php?cod='+cod_sim);
    }else{
    var q=$("#id_servicioibnored").val();
    var s=$("#id_servicioibnored_s").val();
    var u=$("#id_servicioibnored_u").val();
    alerts.showSwal('success-message','registerSimulacion.php?cod='+cod_sim+'&q='+q+'&s='+s+"&u="+u);
    } 
   
  //poner un script aqui
  /* FIN PARA PERSONAL*/
 }else{
    Swal.fire("Informativo!", "Debe llenar todos los campos", "warning");
 }
}

function actualizarSimulacion(){

  var codigo=$("#cod_simulacion").val();
   Swal.fire({
        title: '¿Esta Seguro?',
        text: "Los datos de la propuesta se actualizarán!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonClass: 'btn btn-info',
        cancelButtonClass: 'btn btn-danger',
        confirmButtonText: 'Si',
        cancelButtonText: 'No',
        buttonsStyling: false
      }).then((result) => {           
         if (result.value) {
          if(!($("#id_servicioibnored").length)){
              location.href='registerSimulacion.php?cod='+codigo;
            }else{
            var q=$("#id_servicioibnored").val();
            var s=$("#id_servicioibnored_s").val();
            var u=$("#id_servicioibnored_u").val();
            location.href='registerSimulacion.php?cod='+codigo+'&q='+q+'&s='+s+'&u='+u;
            }
            
            $("#narch").removeClass("estado");
            return(true);
          } else if (result.dismiss === Swal.DismissReason.cancel) {
            return(false);
          }
         });
}

/**********************************PLANTILLA TCP********************************/
function addDetallePlantilla(obj) {
      numFilas++;
      cantidadItems++;
      filaActiva=numFilas;
      //aumentar un itemfactura
      var ndet=[];
      itemDetalle.push(ndet);
      document.getElementById("cantidad_filas").value=numFilas;
      console.log("num: "+numFilas+" cantidadItems: "+cantidadItems);
      fi = document.getElementById('fiel');
      contenedor = document.createElement('div');
      contenedor.id = 'div'+numFilas;  
      fi.type="style";
      fi.appendChild(contenedor);
      var divDetalle;
      divDetalle=$("#div"+numFilas);
      //document.getElementById('nro_cuenta').focus();
      ajax=nuevoAjax();
      ajax.open("GET","ajaxDetallePlantilla.php?idFila="+numFilas,true);
      ajax.onreadystatechange=function(){
        if (ajax.readyState==4) {
          divDetalle.html(ajax.responseText);
          divDetalle.bootstrapMaterialDesign();
          $('.selectpicker').selectpicker("refresh");
          return false;
       }
      }   
      ajax.send(null);
}
function minusDetallePlantilla(idF){
      var elem = document.getElementById('div'+idF);
      elem.parentNode.removeChild(elem);
      if(idF<numFilas){
      for (var i = parseInt(idF); i < (numFilas+1); i++) {
        var nuevoId=i+1;
       $("#div"+nuevoId).attr("id","div"+i);
       $("#tipo_costo"+nuevoId).attr("onchange","mostrarUnidadDetalle("+i+")");
       $("#tipo_costo"+nuevoId).attr("name","tipo_costo"+i);
       $("#tipo_costo"+nuevoId).attr("id","tipo_costo"+i);
       $("#detalle_plantilla"+nuevoId).attr("name","detalle_plantilla"+i);
       $("#detalle_plantilla"+nuevoId).attr("id","detalle_plantilla"+i);
       //$("#cantidad_detalleplantilla"+nuevoId).attr("onchange","calcularTotalFilaDetalle(1,"+i+")");
       $("#cantidad_detalleplantilla"+nuevoId).attr("onkeyup","calcularTotalFilaDetalle(1,"+i+")");
       $("#cantidad_detalleplantilla"+nuevoId).attr("name","cantidad_detalleplantilla"+i);
       $("#cantidad_detalleplantilla"+nuevoId).attr("id","cantidad_detalleplantilla"+i);
       $("#unidad_detalleplantilla"+nuevoId).attr("name","unidad_detalleplantilla"+i);
       $("#unidad_detalleplantilla"+nuevoId).attr("id","unidad_detalleplantilla"+i);
       //$("#monto_detalleplantilla"+nuevoId).attr("onchange","calcularTotalFilaDetalle(1,"+i+")");
       $("#monto_detalleplantilla"+nuevoId).attr("onkeyup","calcularTotalFilaDetalle(1,"+i+")");
       $("#monto_detalleplantilla"+nuevoId).attr("name","monto_detalleplantilla"+i);
       $("#monto_detalleplantilla"+nuevoId).attr("id","monto_detalleplantilla"+i);
       //$("#monto_total_detalleplantilla"+nuevoId).attr("onchange","calcularTotalFilaDetalle(2,"+i+")");
       $("#monto_total_detalleplantilla"+nuevoId).attr("onkeyup","calcularTotalFilaDetalle(2,"+i+")");
       $("#monto_total_detalleplantilla"+nuevoId).attr("name","monto_total_detalleplantilla"+i);
       $("#monto_total_detalleplantilla"+nuevoId).attr("id","monto_total_detalleplantilla"+i);

       $("#partida_presupuestaria"+nuevoId).attr("onchange","mostrarCuentasPartida2("+i+")");
       $("#partida_presupuestaria"+nuevoId).attr("name","partida_presupuestaria"+i);
       $("#partida_presupuestaria"+nuevoId).attr("id","partida_presupuestaria"+i);
       $("#cuenta_plantilladetalle"+nuevoId).attr("name","cuenta_plantilladetalle"+i);
       $("#cuenta_plantilladetalle"+nuevoId).attr("id","cuenta_plantilladetalle"+i);
       $("#cuenta_plantilladetalle"+nuevoId).attr("id","cuenta_plantilladetalle"+i);
       $("#boton_remove"+nuevoId).attr("onclick","minusDetallePlantilla('"+i+"')");
       $("#boton_remove"+nuevoId).attr("id","boton_remove"+i);

       //$("#boton_det"+nuevoId).attr("onclick","listDetallePlantilla('"+i+"')");
       //$("#boton_det"+nuevoId).attr("id","boton_det"+i);
       //$("#ndet"+nuevoId).attr("id","ndet"+i);
       $("#codigo_cuentadetalle"+nuevoId).attr("name","codigo_cuentadetalle"+i);
       $("#codigo_cuentadetalle"+nuevoId).attr("id","codigo_cuentadetalle"+i);
       $("#codigo_partidadetalle"+nuevoId).attr("name","codigo_partidadetalle"+i);
       $("#cuentas_div"+nuevoId).attr("id","cuentas_div"+i);
      }
     } 
     itemDetalle.splice((idF-1), 1);
      numFilas=numFilas-1;
      cantidadItems=cantidadItems-1;
      filaActiva=numFilas;
      document.getElementById("cantidad_filas").value=numFilas;  
}
function mostrarUnidadDetalle(fila){
  if($("#tipo_costo"+fila).val()==2){
    if(($("#unidad_detalleplantilla"+fila).is("[readonly]"))){
      $("#unidad_detalleplantilla"+fila).removeAttr("readonly");
    }
  }else{
    if(!($("#unidad_detalleplantilla"+fila).is("[readonly]"))){
      $("#unidad_detalleplantilla"+fila).attr("readonly",true);
    }
  }
}
function calcularTotalFilaDetalle(valor,fila){
  if(valor==1){
 var total = redondeo($("#cantidad_detalleplantilla"+fila).val()*$("#monto_detalleplantilla"+fila).val());
 $("#monto_total_detalleplantilla"+fila).val(total);   
  }else{
    var unitario = redondeo($("#monto_total_detalleplantilla"+fila).val()/$("#cantidad_detalleplantilla"+fila).val());
    if($("#cantidad_detalleplantilla"+fila).val()==""||$("#cantidad_detalleplantilla"+fila).val()==0){
     //unitario=0;
    } 
   $("#monto_detalleplantilla"+fila).val(unitario); 
  }
}
function listDetallePlantilla(id){
  var nombreDetalle=$("#detalle_plantilla"+id).val();
  if(nombreDetalle==""){nombreDetalle="Sin Detalle";}
   $("#divTituloGrupo").html('<h4 class="card-title">'+nombreDetalle+'</h4>');
   $("#codGrupo").val(id);
   if($("#codigo_cuentadetalle"+id).val()!=""){
     $("#partida_detalle").val($("#codigo_partidadetalle"+id).val());
     mostrarCuentasPartida();
     $("#cuenta_plantilladetalle").val($("#codigo_cuentadetalle"+id).val());
     $('.selectpicker').selectpicker("refresh");
   }else{
   $("#partida_detalle").val("");
   $("#combo_cuentas").html("");  
   }
   $('.selectpicker').selectpicker("refresh");
    //limpiarDatosDetalleModal();  
   $("#modalDetalle").modal("show");
 }

   function mostrarCuentasPartida(){
  var partida=$("#partida_detalle").val();
  var parametros={"cod_partida":partida};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "ajaxPartidaPresupuestaria.php",
        data: parametros,
        beforeSend: function () { 
          iniciarCargaAjax();
        },
        success:  function (resp) {
           detectarCargaAjax();
           $("#combo_cuentas").html(resp);
           $('.selectpicker').selectpicker("refresh");
        }
    });
 }
 function mostrarCuentasPartida2(fila){
  var partida=$("#partida_presupuestaria"+fila).val();
  var parametros={"cod_partida":partida,"fila":fila};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "ajaxPartidaPresupuestariaCuentas.php",
        data: parametros,
        beforeSend: function () { 
          iniciarCargaAjax();
        },
        success:  function (resp) {
           detectarCargaAjax();
           $("#cuentas_div"+fila).html(resp);
           $('.selectpicker').selectpicker("refresh");
        }
    });
 }
 function savePlantillaDetalleTcp(){
  if($("#partida_detalle").val()==""||$("#cuenta_plantilladetalle").val()==""){
    Swal.fire("Informativo!", "Todos los campos son requeridos", "warning");
  }else{
    var fila=$("#codGrupo").val();
    $("#codigo_cuentadetalle"+fila).val($("#cuenta_plantilladetalle").val());
    $("#codigo_partidadetalle"+fila).val($("#partida_detalle").val());
    $("#boton_det"+fila).attr("title",$('select[name="cuenta_plantilladetalle"] option:selected').text());
    $("#ndet"+fila).removeClass("bg-danger");
    $("#ndet"+fila).addClass("bg-success");
    $("#modalDetalle").modal("hide");
  }
 }
 function guardarServicioPlantilla(){
  var plantilla=$("#cod_plantilla").val();
  var codigo=$("#servicios_codigo").val();
  var observacion=$("#observacion_servicio").val();
  var cantidad="1";   //var cantidad=$("#cantidad_servicio").val();
  var monto="0" ;//var monto=$("#monto_servicio").val();
  if(!(codigo>0)){ //||cantidad==""||monto==""
       Swal.fire("Informativo!", "Debe seleccionar un servicio", "warning");
  }else{
  var parametros={"plantilla":plantilla,"codigo":codigo,"obs":observacion,"cant":cantidad,"monto":monto};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "ajaxSaveTipoServicio.php",
        data: parametros,
        beforeSend: function () { 
          iniciarCargaAjax();
        },
        success:  function (resp) {
           detectarCargaAjax();
           if(resp.trim()=="1"){
             Swal.fire("Encontrado!", "El servicio ya se encuentra registrado.", "warning");
           }else{
            $("#servicios_codigo").val("");
            $("#observacion_servicio").val("");
            //$("#cantidad_servicio").val("");
            //$("#monto_servicio").val("");
            $('.selectpicker').selectpicker("refresh");
            listarServiciosPlantilla();
             Swal.fire("Correcto!", "Se agrego el registro exitosamente.", "success");
             
           }
        }
    });
    
  }
 }

  function guardarAuditorPlantilla(){
  var plantilla=$("#cod_plantilla").val();
  var codigo=$("#personal_codigo").val();
  var cantidad=$("#cantidad_auditor").val();
  var monto=$("#monto_auditor").val();
  if(codigo==""||cantidad==""||monto==""){
       Swal.fire("Informativo!", "Debe llenar todos los campos", "warning");
  }else{
  var parametros={"plantilla":plantilla,"codigo":codigo,"cant":cantidad,"monto":monto};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "ajaxSavePersonal.php",
        data: parametros,
        beforeSend: function () { 
          iniciarCargaAjax();
        },
        success:  function (resp) {
           detectarCargaAjax();
           if(resp.trim()=="1"){
             Swal.fire("Encontrado!", "El personal ya se encuentra registrado.", "warning");
           }else{
            $("#personal_codigo").val("");
            $("#cantidad_auditor").val("");
            $("#monto_auditor").val("");
            $('.selectpicker').selectpicker("refresh");
            listarPersonalPlantilla();
             
             
           }
        }
    });
    
  }
 }
 function guardarAuditoresPlantilla(){
  var plantilla=$("#cod_plantilla").val();

  var n=$("#cantidad_filaspersonal").val();

  for (var i = 1; i < n; i++) {
    var cantidad=$("#cantidad_personal"+i).val();
    var monto=$("#monto_personal"+i).val();
    var montoExt=$("#monto_personalext"+i).val();
    var dias=$("#dias_personal"+i).val();
    var codigo = $("#codigo_personal"+i).val();
    if(cantidad==""||monto==""||parseFloat(monto)==0||montoExt==""||parseFloat(montoExt)==0||cantidad==0){
      
    }else{
     var parametros={"plantilla":plantilla,"codigo":codigo,"cant":cantidad,"monto":monto,"montoe":montoExt,"dias":dias};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "ajaxSavePersonales.php",
        data: parametros,
        beforeSend: function () { 
          iniciarCargaAjax();
        },
        success:  function (resp) {
           detectarCargaAjax(); 
           listarPersonalesPlantillaBucle();        
        }
     });
     
    }
  };
 // listarPersonalesPlantilla();
 }
function listarPersonalPlantilla(){
   var plantilla=$("#cod_plantilla").val();
   var parametros={"plantilla":plantilla};
   $.ajax({
        type: "GET",
        dataType: 'html',
        url: "ajaxListPersonal.php",
        data: parametros,
        success:  function (resp) {
         $("#tabla_personal").html(resp);
        }
    }); 
}
function listarPersonalesPlantilla(){
   var plantilla=$("#cod_plantilla").val();
   var dias_auditoria=$("#dias_auditoria").val();
   var parametros={"plantilla":plantilla,"dias_auditoria":dias_auditoria};
   $.ajax({
        type: "GET",
        dataType: 'html',
        url: "ajaxListPersonales.php",
        data: parametros,
        beforeSend: function () { 
          iniciarCargaAjax();
        },
        success:  function (resp) {
          detectarCargaAjax();
         $("#tabla_personal").html(resp);
         Swal.fire("Correcto!", "Se agrego el registro exitosamente.", "success");
        }
    }); 
}
function listarPersonalesPlantillaBucle(){
   var plantilla=$("#cod_plantilla").val();
   var dias_auditoria=$("#dias_auditoria").val();
   var parametros={"plantilla":plantilla,"dias_auditoria":dias_auditoria};
   $.ajax({
        type: "GET",
        dataType: 'html',
        url: "ajaxListPersonales.php",
        data: parametros,
        beforeSend: function () { 
          iniciarCargaAjax();
        },
        success:  function (resp) {
          detectarCargaAjax();
         $("#tabla_personal").html(resp);
        }
    }); 
}
function listarServiciosPlantilla(){
   var plantilla=$("#cod_plantilla").val();
   var parametros={"plantilla":plantilla};
   $.ajax({
        type: "GET",
        dataType: 'html',
        url: "ajaxListTipoServicio.php",
        data: parametros,
        success:  function (resp) {
         $("#tabla_servicios").html(resp);
        }
    }); 
}
function removeServicioPlantilla(cod){
  var parametros={"cod":cod};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "ajaxDeleteServicio.php",
        data: parametros,
        beforeSend: function () { 
          iniciarCargaAjax();
        },
        success:  function (resp) {
           detectarCargaAjax();
           listarServiciosPlantilla();
        }
    });
}
function removeAuditorPlantilla(cod){
  var parametros={"cod":cod};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "ajaxDeletePersonal.php",
        data: parametros,
        beforeSend: function () { 
          iniciarCargaAjax();
        },
        success:  function (resp) {
           detectarCargaAjax();
           listarPersonalesPlantilla();
        }
    });
}
function cambiarDivPlantilla(div,div2,div3){
  if(!($("#"+div2).hasClass("d-none"))){
    $("#"+div2).addClass("d-none");  
    $("#button_"+div2).removeClass("fondo-boton-active"); 
  }
  if(!($("#"+div3).hasClass("d-none"))){
      $("#"+div3).addClass("d-none");
      $("#button_"+div3).removeClass("fondo-boton-active");  
    }
  if(($("#"+div).hasClass("d-none"))){
    $("#"+div).removeClass("d-none");
    $("#button_"+div).addClass("fondo-boton-active");
  }
}
function cambiarDosDivPantalla(div,div2){
  if(!($("#"+div2).hasClass("d-none"))){
    $("#"+div2).addClass("d-none");  
    $("#button_"+div2).removeClass("fondo-boton-active"); 
  }
  if(($("#"+div).hasClass("d-none"))){
    $("#"+div).removeClass("d-none");
    $("#button_"+div).addClass("fondo-boton-active");
  }
}
//funciones despues de cargar pantalla
window.onload = detectarCarga;
  function detectarCarga(){
    $(".cargar").fadeOut("slow");
  }

$(document).ready(function() {
  //para plantillas tributarias
  if($('#AceptarReProcesoTrib').length){
    $('#AceptarReProcesoTrib').click(function(){   
     var cod_planilla=document.getElementById("codigo_planilla2").value;    
      var cod_planillaTrib=document.getElementById("codigo_planilla_trib").value;      
      ReprocesarPlanillaTrib(cod_planillaTrib,cod_planilla);
    });    
  }  
  //datepickers
  $('.datepicker').datetimepicker({
      format: 'DD/MM/YYYY',
      icons: {
        time: "fa fa-clock-o",
        date: "fa fa-calendar",
        up: "fa fa-chevron-up",
        down: "fa fa-chevron-down",
        previous: 'fa fa-chevron-left',
        next: 'fa fa-chevron-right',
        today: 'fa fa-screenshot',
        clear: 'fa fa-trash',
        close: 'fa fa-remove'
      }
    });
});

//rendiciones
// function agregarRendicionDetalle(datos){
//   //console.log("datos: "+datos);
//   var d=datos.split('/');
  
//   // alert("d1: "+d[0]+"-d2: "+d[1]);
//   document.getElementById("codigo_rendicionA").value=d[0];
//   // document.getElementById("cod_contratoCf").value=d[1];
// }

// function RegistrarDetalleRendicion(codigo_rendicionA,cod_tipo_documentoA,numero_doc,fecha_doc,monto_A,observacionesA){
//   $.ajax({
//     type:"POST",
//     data:"codigo_detRendicionE=0&codigo_rendicionA="+codigo_rendicionA+"&cod_tipo_documentoA="+cod_tipo_documentoA+"&numero_doc="+numero_doc+"&monto_A="+monto_A+"&cod_estadoreferencial=1&fecha_doc="+fecha_doc+"&observacionesA="+observacionesA,
//     url:"caja_chica/rendicionesdetalle_save.php",
//     success:function(r){
//       if(r==1){
//         alerts.showSwal('success-message','index.php?opcion=ListaRendicionesDetalle&codigo='+codigo_rendicionA);
//       }else{
//         alerts.showSwal('error-message','index.php?opcion=ListaRendicionesDetalle&codigo='+codigo_rendicionA);
//         // if(r==2){
//         //   alerts.showSwal('error-message5','index.php?opcion=FormPersonalContratos&codigo='+codigo_rendiciondetalleA);
//         // }
//       } 
//     }
//   });
// }
// function clickEditarRendicionDetalle(datos){
//   //console.log("datos: "+datos);
//   var d=datos.split('/');
  
//   document.getElementById("codigo_rendicionE").value=d[0];
//   document.getElementById("codigo_detRendicionE").value=d[1];
//   document.getElementById("cod_tipo_documentoE").value=d[2];
//   document.getElementById("numero_docE").value=d[3];
//   document.getElementById("fecha_docE").value=d[4];
//   document.getElementById("monto_E").value=d[5];
//   document.getElementById("observacionesE").value=d[6];
  

//   // document.getElementById("cod_areaE").value=d[2];
//   // document.getElementById("porcentajeE").value=d[3];
// }
// function EditarDetalleRendicion(codigo_detRendicionE,codigo_rendicionE,cod_tipo_documentoE,numero_docE,fecha_docE,monto_E,observacionesE){
//   $.ajax({
//     type:"POST",
//     data:"codigo_detRendicionE="+codigo_detRendicionE+"&codigo_rendicionA="+codigo_rendicionE+"&cod_tipo_documentoA="+cod_tipo_documentoE+"&numero_doc="+numero_docE+"&monto_A="+monto_E+"&cod_estadoreferencial=2&fecha_doc="+fecha_docE+"&observacionesA="+observacionesE,
//     url:"caja_chica/rendicionesdetalle_save.php",
//     success:function(r){
//       if(r==1){
//         alerts.showSwal('success-message','index.php?opcion=ListaRendicionesDetalle&codigo='+codigo_rendicionE);
//       }else{
//         alerts.showSwal('error-message','index.php?opcion=ListaRendicionesDetalle&codigo='+codigo_rendicionE);
//         // if(r==2){
//         //   alerts.showSwal('error-message5','index.php?opcion=FormPersonalContratos&codigo='+codigo_rendiciondetalleA);
//         // }
//       } 
//     }
//   });
// }


// function clickBorrarRendicionDetalle(datos){
//   //console.log("datos: "+datos);
//   var d=datos.split('/');
  
//   document.getElementById("codigo_rendicionB").value=d[0];
//   document.getElementById("codigo_detRendicionB").value=d[1];
  
  

//   // document.getElementById("cod_areaE").value=d[2];
//   // document.getElementById("porcentajeE").value=d[3];
// }

// function EliminarDetalleRendicion(codigo_detRendicionB,codigo_rendicionB){
//   $.ajax({
//     type:"POST",
//     data:"codigo_detRendicionE="+codigo_detRendicionB+"&codigo_rendicionA="+codigo_rendicionB+"&cod_tipo_documentoA=0&numero_doc=0&monto_A=0&cod_estadoreferencial=3&fecha_doc=''&observacionesA=''",
//     url:"caja_chica/rendicionesdetalle_save.php",
//     success:function(r){
//       if(r==1){
//         alerts.showSwal('success-message','index.php?opcion=ListaRendicionesDetalle&codigo='+codigo_rendicionB);
//       }else{
//         alerts.showSwal('error-message','index.php?opcion=ListaRendicionesDetalle&codigo='+codigo_rendicionB);
//         // if(r==2){
//         //   alerts.showSwal('error-message5','index.php?opcion=FormPersonalContratos&codigo='+codigo_rendiciondetalleA);
//         // }
//       } 
//     }
//   });
// }


// function clickGuardarRendicion(datos){
//   //console.log("datos: "+datos);
//   var d=datos.split('/');
  
//   document.getElementById("codigo_rendicionG").value=d[0];
//   document.getElementById("monto_rendicionG").value=d[1];
//   document.getElementById("cod_cajachicaDetG").value=d[2];
// }

// function GuardarRendicion(codigo_rendicionG,monto_rendicionG,cod_cajachicaDetG){
//   $.ajax({
//     type:"POST",
//     data:"codigo_detRendicionE="+cod_cajachicaDetG+"&codigo_rendicionA="+codigo_rendicionG+"&cod_tipo_documentoA=0&numero_doc=0&monto_A="+monto_rendicionG+"&cod_estadoreferencial=4&fecha_doc=''&observacionesA=''",
//     url:"caja_chica/rendicionesdetalle_save.php",
//     success:function(r){
//       if(r==1){
//         alerts.showSwal('success-message','index.php?opcion=ListaRendiciones');
//       }else{
//         alerts.showSwal('error-message','index.php?opcion=ListaRendiciones');
//         // if(r==2){
//         //   alerts.showSwal('error-message5','index.php?opcion=FormPersonalContratos&codigo='+codigo_rendiciondetalleA);
//         // }
//       } 
//     }
//   });
// }
// function sumartotalprueba2(fila){
  
//   // var monto_A_aux=parseFloat($("#monto_A"+fila).val());
//   // var suma= $("#monto_total").val()+monto_A_aux;

//  var suma= parseFloat($("#monto_total").val())+parseFloat($("#monto_A"+fila).val());
//  $("#monto_total").val(suma);
// }

function sumartotalmontoRendicion(id){
  var sumatotal=0;
  var formulariop = document.getElementById("formRegComp");
  
  for (var i=0;i<formulariop.elements.length;i++){
    if (formulariop.elements[i].id.indexOf("monto_A") !== -1 ){    
      //console.log("debe "+formulariop.elements[i].value);    
      sumatotal += parseFloat((formulariop.elements[i].value) * 1);

      monto_a_rendir=document.getElementById("monto_a_rendir").value;
      monto_faltante= monto_a_rendir-sumatotal;
    }
  }
  
  // document.getElementById("totaldeb").value=sumatotal; 
  $("#monto_total").val(sumatotal);  
  $("#monto_faltante").val(monto_faltante);  
}

function addCajaChicaDetalleADD(obj) {
  if($("#add_boton").length){
    $("#add_boton").attr("disabled",true);
  }
      numFilas++;
      cantidadItems++;
      filaActiva=numFilas;      
      document.getElementById("cantidad_filas").value=numFilas;
      console.log("num: "+numFilas+" cantidadItems: "+cantidadItems);
      fi = document.getElementById('fiel');
      contenedor = document.createElement('div');
      contenedor.id = 'div'+numFilas;  
      fi.type="style";
      fi.appendChild(contenedor);
      var divDetalle;
      divDetalle=$("#div"+numFilas);
      //document.getElementById('nro_cuenta').focus();
      ajax=nuevoAjax();
      ajax.open("GET","caja_chica/ajax_cajachica_add_facturas.php?idFila="+numFilas,true);
      ajax.onreadystatechange=function(){
        if (ajax.readyState==4) {
          divDetalle.html(ajax.responseText);
          divDetalle.bootstrapMaterialDesign();   
          $('#codigo_rendicionA').val("");
          $('#cod_tipo_documentoA').val("");//
          $('#numero_doc').val("");
          $('#fecha_doc').val("");
          $('#monto_A').val("");
          $('#observacionesA').val("");
    
          $('.selectpicker').selectpicker("refresh");
          // $('#modalAgregarDR').modal('show');
          // if(numFilas!=1){
          //   //alert((numFilas-1)+"-"+$("#monto_A"+(numFilas-1)).val());
          //   sumartotalprueba2(numFilas-1);
          // }
          


          if($("#add_boton").length){
            $("#add_boton").removeAttr("disabled");
          }
          return false;
       }
      }   
      ajax.send(null);
  
}
function addRendicionDetalle(obj) {
  if($("#add_boton").length){
    $("#add_boton").attr("disabled",true);
  }

      numFilas++;
      cantidadItems++;
      filaActiva=numFilas;      
      document.getElementById("cantidad_filas").value=numFilas;
      console.log("num: "+numFilas+" cantidadItems: "+cantidadItems);
      fi = document.getElementById('fiel');
      contenedor = document.createElement('div');
      contenedor.id = 'div'+numFilas;  
      fi.type="style";
      fi.appendChild(contenedor);
      var divDetalle;
      divDetalle=$("#div"+numFilas);
      //document.getElementById('nro_cuenta').focus();
      ajax=nuevoAjax();
      ajax.open("GET","caja_chica/ajax_rendicionesdetalle.php?idFila="+numFilas,true);
      ajax.onreadystatechange=function(){
        if (ajax.readyState==4) {
          divDetalle.html(ajax.responseText);
          divDetalle.bootstrapMaterialDesign();   
          $('#codigo_rendicionA').val("");
          $('#cod_tipo_documentoA').val("");//
          $('#numero_doc').val("");
          $('#fecha_doc').val("");
          $('#monto_A').val("");
          $('#observacionesA').val("");
    
          $('.selectpicker').selectpicker("refresh");
          // $('#modalAgregarDR').modal('show');
          // if(numFilas!=1){
          //   //alert((numFilas-1)+"-"+$("#monto_A"+(numFilas-1)).val());
          //   sumartotalprueba2(numFilas-1);
          // }
          


          if($("#add_boton").length){
            $("#add_boton").removeAttr("disabled");
          }
          return false;
       }
      }   
      ajax.send(null);
  
}
function borrarItemRendicionDetalle(idF){
 // alert(idF+"_"+cantidadItems);
      //$('#div'+idF).remove();
      var elem = document.getElementById('div'+idF);
      elem.parentNode.removeChild(elem);
      if(idF<numFilas){
      for (var i = parseInt(idF); i < (numFilas+1); i++) {
        var nuevoId=i+1;
       $("#div"+nuevoId).attr("id","div"+i);
       $("#tipo_doc"+nuevoId).attr("name","tipo_doc"+i);
       $("#tipo_doc"+nuevoId).attr("id","tipo_doc"+i);
       $("#numero_doc"+nuevoId).attr("name","numero_doc"+i);
       $("#numero_doc"+nuevoId).attr("id","numero_doc"+i);
       $("#monto_A"+nuevoId).attr("name","monto_A"+i);
       $("#monto_A"+nuevoId).attr("id","monto_A"+i);
       $("#observacionesA"+nuevoId).attr("name","observacionesA"+i);
       $("#observacionesA"+nuevoId).attr("id","observacionesA"+i);
      }
     } 
      // itemFacturas.splice((idF-1), 1);
      // itemEstadosCuentas.splice((idF-1), 1);
      numFilas=numFilas-1;
      cantidadItems=cantidadItems-1;
      filaActiva=numFilas;
      document.getElementById("cantidad_filas").value=numFilas;
      // document.getElementById("totalhab").value=numFilas;
      $("#monto_total").val(numFilas);  
      console.log("num: "+numFilas+" cantidadItems: "+cantidadItems); 
      sumartotalmontoRendicion("null");   
}

function cargarDatosRegistroProveedor(){
   var parametros={"cod":"none"};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "ajaxListarDatosRegistroProveedor.php",
        data: parametros,
        beforeSend: function () {
        $("#texto_ajax_titulo").html("Obteniendo datos del servicio..."); 
          iniciarCargaAjax();
        },
        success:  function (resp) {
           detectarCargaAjax();
           $("#datosProveedorNuevo").html(resp);
           $("#texto_ajax_titulo").html("Procesando Datos"); 
           $("#pais_empresa").val("26"); //para el pais de BOLIVIA
           seleccionarDepartamentoServicio();
           $('.selectpicker').selectpicker("refresh");
           $("#modalAgregarProveedor").modal("show");
           
        }
    });
}
function seleccionarDepartamentoServicio(){
 var parametros={"codigo":$("#pais_empresa").val()};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "ajaxListarDepto.php",
        data: parametros,
        beforeSend: function () {
        $("#texto_ajax_titulo").html("Pais: "+$("#pais_empresa option:selected" ).text()); 
          iniciarCargaAjax();
        },
        success:  function (resp) {
           detectarCargaAjax();
           $("#texto_ajax_titulo").html("Procesando Datos"); 
           $("#departamento_empresa").html(resp);
           $("#departamento_empresa").val("480"); // departamento de LA PAZ
           seleccionarCiudadServicio();
           $("#ciudad_empresa").val("");
           $('.selectpicker').selectpicker("refresh");          
        }
    }); 
}


function seleccionarDepartamentoServicioSitio(setear,depto,ciudad){
  var pais=$("#pais_empresa").val().split("####");
 var parametros={"codigo":pais[0]};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "simulaciones_servicios/ajaxListarDepto.php",
        data: parametros,
        beforeSend: function () {
        $("#texto_ajax_titulo").html("Pais: "+$("#pais_empresa option:selected" ).text()); 
          iniciarCargaAjax();
        },
        success:  function (resp) {
           detectarCargaAjax();
           $("#texto_ajax_titulo").html("Procesando Datos"); 
           $("#departamento_empresa").html(resp);
           if(setear==1){
            $("#departamento_empresa").val("480####LA PAZ"); // departamento de LA PAZ
            seleccionarCiudadServicioSitio(1);
           }else{
             $("#departamento_empresa").val(depto); // departamento de LA PAZ
            seleccionarCiudadServicioSitio(0,ciudad);
           }  
           
           $("#ciudad_empresa").val("");
           $('.selectpicker').selectpicker("refresh");          
        }
    }); 
}

function seleccionarDepartamentoServicioSitioModal(setear,depto,ciudad){
  var pais=$("#pais_empresa").val().split("####");
 var parametros={"codigo":pais[0]};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "ajaxListarDepto.php",
        data: parametros,
        beforeSend: function () {
        $("#texto_ajax_titulo").html("Pais: "+$("#pais_empresa option:selected" ).text()); 
          iniciarCargaAjax();
        },
        success:  function (resp) {
           detectarCargaAjax();
           $("#texto_ajax_titulo").html("Procesando Datos"); 
           $("#departamento_empresa").html(resp);
           if(setear==1){
            $("#departamento_empresa").val("480####LA PAZ"); // departamento de LA PAZ
            seleccionarCiudadServicioSitioModal(1);
           }else{
             $("#departamento_empresa").val(depto); // departamento de LA PAZ
            seleccionarCiudadServicioSitioModal(0,ciudad);
           }  
           
           $("#ciudad_empresa").val("");
           $('.selectpicker').selectpicker("refresh");          
        }
    }); 
}
function seleccionarCiudadServicio(){
  var parametros={"codigo":$("#departamento_empresa").val()};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "ajaxListarCiudad.php",
        data: parametros,
        beforeSend: function () {
        $("#texto_ajax_titulo").html("Estado / Departamento: "+$("#departamento_empresa option:selected" ).text()); 
          iniciarCargaAjax();
        },
        success:  function (resp) {
           detectarCargaAjax();
           $("#texto_ajax_titulo").html("Procesando Datos"); 
           $("#ciudad_empresa").html(resp);
           $("#ciudad_empresa").val("62"); //PARA LA CIUDAD DE EL ALTO
           $('.selectpicker').selectpicker("refresh");
        }
    }); 
}

function seleccionarCiudadServicioSitio(setear,ciudad){
  var depto=$("#departamento_empresa").val().split("####");
  var parametros={"codigo":depto[0]};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "simulaciones_servicios/ajaxListarCiudad.php",
        data: parametros,
        beforeSend: function () {
        $("#texto_ajax_titulo").html("Estado / Departamento: "+$("#departamento_empresa option:selected" ).text()); 
          iniciarCargaAjax();
        },
        success:  function (resp) {
           detectarCargaAjax();
           $("#texto_ajax_titulo").html("Procesando Datos"); 
           $("#ciudad_empresa").html(resp);
           if(setear==1){
            $("#ciudad_empresa").val("62####LA PAZ"); //PARA LA CIUDAD DE la paz
           }else{
            $("#ciudad_empresa").val(ciudad); //PARA LA CIUDAD DE la paz
           }
           
           $('.selectpicker').selectpicker("refresh");
        }
    }); 
}

function seleccionarCiudadServicioSitioModal(setear,ciudad){
  var depto=$("#departamento_empresa").val().split("####");
  var parametros={"codigo":depto[0]};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "ajaxListarCiudad.php",
        data: parametros,
        beforeSend: function () {
        $("#texto_ajax_titulo").html("Estado / Departamento: "+$("#departamento_empresa option:selected" ).text()); 
          iniciarCargaAjax();
        },
        success:  function (resp) {
           detectarCargaAjax();
           $("#texto_ajax_titulo").html("Procesando Datos"); 
           $("#ciudad_empresa").html(resp);
           if(setear==1){
            $("#ciudad_empresa").val("62####LA PAZ"); //PARA LA CIUDAD DE la paz
           }else{
            $("#ciudad_empresa").val(ciudad); //PARA LA CIUDAD DE la paz
           }
           
           $('.selectpicker').selectpicker("refresh");
        }
    }); 
}
function mostrarOtraCiudadServicio(){
  var ciudad = $("#ciudad_empresa").val();
  if(ciudad=="NN"){
    if($("#otra_ciudad_div").hasClass("d-none")){
      $("#otra_ciudad_div").removeClass("d-none");
    }
  }else{
   if(!($("#otra_ciudad_div").hasClass("d-none"))){
      $("#otra_ciudad_div").addClass("d-none");
    }
  }
}
function guardarDatosProveedor(){
  var nombre =$("#nombre_empresa").val();
  var nombre_p =$("#nombre_persona").val();
  var paterno_p =$("#paterno_persona").val();
  var materno_p =$("#materno_persona").val();
  var identificacion =$("#identificacion").val();
  var pais =$("#pais_empresa").val();
  var estado =$("#departamento_empresa").val();
  var ciudad =$("#ciudad_empresa").val();
  var direccion =$("#direccion_empresa").val();
  var telefono =$("#telefono_empresa").val();
  var correo =$("#correo_empresa").val();
  var nombre_contacto =$("#nombre_contacto").val();
  var apellido_contacto =$("#apellido_contacto").val();
  var cargo_contacto =$("#cargo_contacto").val();
  var correo_contacto =$("#correo_contacto").val();
  var tipo_id = $("#tipo_id").val();
  var tipo_id_otro = $("#tipo_id_otro").val();
  var numero_celular = $("#numero_celular").val();
  var ciudad_true=0;
  // validaciones de campos
  if($("#tipo_empresa").val()=='E'){
    if(nombre!=""&&identificacion!=""&&(pais>0)&&(estado>0)&&direccion!=""&&telefono!=""&&correo!=""&&nombre_contacto!=""&&apellido_contacto!=""&&cargo_contacto!=""&&correo_contacto!="")
      var sw=true;
    else{
      var sw=false;
    }
  }else{
    if(nombre_p!=""&&paterno_p!=""&&materno_p!=""&&identificacion!=""&&(pais>0)&&(estado>0)&&direccion!=""&&telefono!=""&&correo!=""&&(tipo_id>0))
      var sw=true;
    else{
      var sw=false;
    }
  }

   if(sw){
     if(ciudad>0){
       ciudad_true=1;
     }else{
      if(ciudad=="NN"){
         ciudad_true=2;
         ciudad="";
      }
     }
     if(ciudad_true>0){
        if(ciudad_true==1){
          var otra="";
        }else{
          var otra=$("#otra_ciudad").val();
        }
        if(otra==""&&ciudad_true==2){
          Swal.fire("Informativo!", "Ingrese el nombre de la Ciudad", "warning");
        }else{
          //proceso de guardado de informacion
           var parametros={"numero_celular":numero_celular,"tipo_id_otro":tipo_id_otro,"tipo_id":tipo_id,"tipo":$("#tipo_empresa").val(),"nacional":$("#nacional_empresa").val(),"nombre":nombre,"nombre_p":nombre_p,"paterno_p":paterno_p,"materno_p":materno_p,"identificacion":identificacion,"pais":pais,"estado":estado,"ciudad":ciudad,"otra":otra,"direccion":direccion,"telefono":telefono,"correo":correo,"nombre_contacto":nombre_contacto,"apellido_contacto":apellido_contacto,"cargo_contacto":cargo_contacto,"correo_contacto":correo_contacto};
            $.ajax({
               type: "GET",
               dataType: 'html',
               url: "ajaxAgregarNuevoProveedor.php",
               data: parametros,
               beforeSend: function () {
                $("#texto_ajax_titulo").html("Enviando datos al servidor..."); 
                  iniciarCargaAjax();
                },
               success:  function (respuesta) {
                  var resp = respuesta.split("####")[0];
                  detectarCargaAjax();
                  $("#texto_ajax_titulo").html("Procesando Datos"); 
                  if(resp.trim()=="1"){
                    Swal.fire("Correcto!",respuesta.split("####")[1], "success");
                    $("#modalAgregarProveedor").modal("hide");
                    actualizarRegistroProveedor();
                  }else{
                    if(resp.trim()=="2"){
                      Swal.fire("Informativo!",respuesta.split("####")[1], "warning");
                      //actualizarRegistroProveedor();
                    }else{
                      Swal.fire("Error!",respuesta.split("####")[1], "warning");
                    }                    
                  }
               }
             });  
        }       
     }else{
        Swal.fire("Informativo!", "Todos los campos son requeridos", "warning");
     }
   }else{
     Swal.fire("Informativo!", "Todos los campos son requeridos", "warning");
   }
}
function cargarDatosRegistroProveedorCajaChica(cod_tcc,cod_cc,cod_dcc){
  var parametros={"cod":"none"};
  // $('#cod_tcc').val(cod_tcc);
  // $('#cod_cc').val(cod_cc);
  // $('#cod_dcc').val(cod_dcc);
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "caja_chica/ajaxListarDatosRegistroProveedor.php?cod_tcc="+cod_tcc+"&cod_cc="+cod_cc+"&cod_dcc="+cod_dcc,
        data: parametros,
        beforeSend: function () {
        $("#texto_ajax_titulo").html("Obteniendo datos del servicio..."); 
          iniciarCargaAjax();
        },
        success:  function (resp) {
           detectarCargaAjax();
           $("#datosProveedorNuevo").html(resp);
           $("#texto_ajax_titulo").html("Procesando Datos"); 
           $("#pais_empresa").val("26"); //para el pais de BOLIVIA
           seleccionarDepartamentoServicioCajaChica();
           $('.selectpicker').selectpicker("refresh");
           $("#modalAgregarProveedor").modal("show");
           
        }
    });
}
function ajaxTipoProveedorPersona(combo){
  var contenedor;
  var codigo=combo.value;
  contenedor = document.getElementById('div_nombre_proveedor');
  ajax=nuevoAjax();

  ajax.open('GET', 'solicitudes/ajax_nombre_proveedor.php?codigo='+codigo,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]); 
      ajaxTipoProveedor_datos_add(codigo);
    }
  }
  ajax.send(null)  
}

function ajaxTipoProveedorPersonaSol(combo){
  var contenedor;
  var codigo=combo.value;
  contenedor = document.getElementById('div_nombre_proveedor');
  ajax=nuevoAjax();
  
  ajax.open('GET', 'ajax_nombre_proveedor.php?codigo='+codigo,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]); 
      ajaxTipoProveedor_datos_addSol(codigo);
    }
  }
  ajax.send(null)  
}

function ajaxTipoProveedor_datos_add(codigo){
  var contenedor;
  contenedor = document.getElementById('div_datos_add_proveedor');
  ajax=nuevoAjax();
  ajax.open('GET', 'solicitudes/ajax_datos_add_proveedor.php?codigo='+codigo,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]); 
    }
  }
  ajax.send(null)  
}
function ajaxTipoProveedor_datos_addSol(codigo){
  var contenedor;
  contenedor = document.getElementById('div_datos_add_proveedor');
  ajax=nuevoAjax();
  ajax.open('GET', 'ajax_datos_add_proveedor.php?codigo='+codigo,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]); 
    }
  }
  ajax.send(null)  
}
function ajaxTipoProveedor_datos_add_comprobantes(codigo){
  var contenedor;
  contenedor = document.getElementById('div_datos_add_proveedor');
  ajax=nuevoAjax();
  ajax.open('GET', '../solicitudes/ajax_datos_add_proveedor.php?codigo='+codigo,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]); 
    }
  }
  ajax.send(null)  
}
function seleccionarDepartamentoServicioCajaChica(){
 var parametros={"codigo":$("#pais_empresa").val()};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "solicitudes/ajaxListarDepto.php",
        data: parametros,
        beforeSend: function () {
        $("#texto_ajax_titulo").html("Pais: "+$("#pais_empresa option:selected" ).text()); 
          iniciarCargaAjax();
        },
        success:  function (resp) {
           detectarCargaAjax();
           $("#texto_ajax_titulo").html("Procesando Datos"); 
           $("#departamento_empresa").html(resp);
           $("#departamento_empresa").val("480"); // departamento de LA PAZ
           seleccionarCiudadServicioCajaChica();
           $("#ciudad_empresa").val("");
           $('.selectpicker').selectpicker("refresh");          
        }
    }); 
}
function seleccionarCiudadServicioCajaChica(){
  var parametros={"codigo":$("#departamento_empresa").val()};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "solicitudes/ajaxListarCiudad.php",
        data: parametros,
        beforeSend: function () {
        $("#texto_ajax_titulo").html("Estado / Departamento: "+$("#departamento_empresa option:selected" ).text()); 
          iniciarCargaAjax();
        },
        success:  function (resp) {
           detectarCargaAjax();
           $("#texto_ajax_titulo").html("Procesando Datos"); 
           $("#ciudad_empresa").html(resp);
           $("#ciudad_empresa").val("62"); //PARA LA CIUDAD DE EL ALTO
           $('.selectpicker').selectpicker("refresh");
        }
    }); 
}
function guardarDatosProveedorCajaChica(){
  var nombre =$("#nombre_empresa").val();
  var nombre_p =$("#nombre_persona").val();
  var paterno_p =$("#paterno_persona").val();
  var materno_p =$("#materno_persona").val();
  var identificacion =$("#identificacion").val();
  var pais =$("#pais_empresa").val();
  var estado =$("#departamento_empresa").val();
  var ciudad =$("#ciudad_empresa").val();
  var direccion =$("#direccion_empresa").val();
  var telefono =$("#telefono_empresa").val();
  var correo =$("#correo_empresa").val();
  var nombre_contacto =$("#nombre_contacto").val();
  var apellido_contacto =$("#apellido_contacto").val();
  var cargo_contacto =$("#cargo_contacto").val();
  var correo_contacto =$("#correo_contacto").val();

  var cod_tcc =$("#cod_tcc").val();
  var cod_cc =$("#cod_cc").val();
  var cod_dcc =$("#cod_dcc").val();
  var ciudad_true=0;
  // validaciones de campos
  if($("#tipo_empresa").val()=='E'){
    if(nombre!=""&&identificacion!=""&&(pais>0)&&(estado>0)&&direccion!=""&&telefono!=""&&correo!=""&&nombre_contacto!=""&&apellido_contacto!=""&&cargo_contacto!=""&&correo_contacto!="")
      var sw=true;
    else{
      var sw=false;
    }
  }else{
    if(nombre_p!=""&&paterno_p!=""&&materno_p!=""&&identificacion!=""&&(pais>0)&&(estado>0)&&direccion!=""&&telefono!=""&&correo!="")
      var sw=true;
    else{
      var sw=false;
    }
  }

   if(sw){
     if(ciudad>0){
       ciudad_true=1;
     }else{
      if(ciudad=="NN"){
         ciudad_true=2;
         ciudad="";
      }
     }
     if(ciudad_true>0){
        if(ciudad_true==1){
          var otra="";
        }else{
          var otra=$("#otra_ciudad").val();
        }
        if(otra==""&&ciudad_true==2){
          Swal.fire("Informativo!", "Ingrese el nombre de la Ciudad", "warning");
        }else{
          //proceso de guardado de informacion
           var parametros={"tipo":$("#tipo_empresa").val(),"nacional":$("#nacional_empresa").val(),"nombre":nombre,"nombre_p":nombre_p,"paterno_p":paterno_p,"materno_p":materno_p,"identificacion":identificacion,"pais":pais,"estado":estado,"ciudad":ciudad,"otra":otra,"direccion":direccion,"telefono":telefono,"correo":correo,"nombre_contacto":nombre_contacto,"apellido_contacto":apellido_contacto,"cargo_contacto":cargo_contacto,"correo_contacto":correo_contacto};
            $.ajax({
               type: "GET",
               dataType: 'html',
               url: "solicitudes/ajaxAgregarNuevoProveedor.php",
               data: parametros,
               beforeSend: function () {
                $("#texto_ajax_titulo").html("Enviando datos al servidor..."); 
                  iniciarCargaAjax();
                },
               success:  function (respuesta) {
                  var resp = respuesta.split("####")[0];
                  // actualizarRegistroProveedor();
                  // actualizarRegistroProveedorCajaChica(cod_tcc,cod_cc,cod_dcc);
                  detectarCargaAjax();
                  $("#texto_ajax_titulo").html("Procesando Datos"); 
                  if(resp.trim()=="1"){
                    //alerts.showSwal('success-message','index.php?opcion=DetalleCajaChicaForm&codigo='+cod_dcc+'&cod_tcc='+cod_tcc+'&cod_cc='+cod_cc);
                    Swal.fire("Correcto!", "Los datos se actualizaron de forma correcta.", "success");
                    actualizarComboBoxAjax();
                  }else{
                    if(resp.trim()=="2"){
                      Swal.fire("Informativo!", "La identificación del proveedor ya se encuentra registrado.", "warning");
                      actualizarComboBoxAjax();
                    }else{
                      Swal.fire("Error!", "Ocurrio un error de envio", "warning");
                    }                    
                  }
               }
             });  
        }       
     }else{
        Swal.fire("Informativo!", "Todos los campos son requeridos", "warning");
     }
   }else{
     Swal.fire("Informativo!", "Todos los campos son requeridos", "warning");
   }
}

function actualizarRegistroProveedorCajaChica(cod_tcc,cod_cc,cod_dcc){
  var codigo = $("#cod_solicitud").val();
 var parametros={"codigo":"none"};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "solicitudes/ajaxActualizarProveedores.php",
        data: parametros,
        beforeSend: function () {
        $("#texto_ajax_titulo").html("Actualizando proveedores desde el Servicio Web..."); 
          iniciarCargaAjax();
        },
        success:  function (resp) {
           detectarCargaAjax();
           $("#texto_ajax_titulo").html("Procesando Datos");
           $('.selectpicker').selectpicker("refresh"); 
           //alerts.showSwal('success-message','index.php?opcion=DetalleCajaChicaForm&codigo='+cod_dcc+'&cod_tcc='+cod_tcc+'&cod_cc='+cod_cc);
           Swal.fire("Correcto!", "Los datos se actualizaron de forma correcta.", "success");
           actualizarComboBoxAjax();
        }
    });  
}
function ajaxTipoProveedorPersonaComprobantes(combo){
  var contenedor;
  var codigo=combo.value;
  contenedor = document.getElementById('div_nombre_proveedor');
  ajax=nuevoAjax();
  ajax.open('GET', '../solicitudes/ajax_nombre_proveedor.php?codigo='+codigo,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]); 
      ajaxTipoProveedor_datos_add_comprobantes(codigo);
    }
  }
  ajax.send(null)  
}

function actualizarTablaClaServicios(){
  var codigo = $("#cod_plantilla").val();
   var parametros={"codigo":"none"};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "ajaxActualizarTablaServicios.php",
        data: parametros,
        beforeSend: function () {
        $("#texto_ajax_titulo").html("Actualizando lista desde el Servicio Web..."); 
          iniciarCargaAjax();
        },
        success:  function (resp) {
           detectarCargaAjax();
           $("#texto_ajax_titulo").html("Procesando Datos"); 
           alerts.showSwal('success-message','registerGrupos.php?cod='+codigo);
        }
    });
}
function actualizarRegistroProveedor(){
  var codigo = $("#cod_solicitud").val();
 var parametros={"codigo":"none"};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "ajaxActualizarProveedores.php",
        data: parametros,
        beforeSend: function () {
        $("#texto_ajax_titulo").html("Actualizando proveedores desde el Servicio Web..."); 
          iniciarCargaAjax();
        },
        success:  function (resp) {
           detectarCargaAjax();
           $("#texto_ajax_titulo").html("Procesando Datos"); 
           actualizarCombosProveedores();
           //alerts.showSwal('success-message','registerSolicitud.php?cod='+codigo);
        }
    });  
}

function actualizarCombosProveedores(){
  var parametros={"codigo":"none"};
  $.ajax({
        type: "GET",
        dataType: 'html',
        url: "ajaxActualizarProveedoresCombo.php",
        data: parametros,
        beforeSend: function () {
        $("#texto_ajax_titulo").html("Cargando proveedores a la lista..."); 
          iniciarCargaAjax();
        },
        success:  function (resp) {
           detectarCargaAjax();
           $("#texto_ajax_titulo").html("Procesando Datos");
           if($("#proveedores").length>0){
             $("#proveedores").html(resp);
           }  
           if($("#cantidad_filas").length>0){
             var filas = $("#cantidad_filas").val();
             for (var i = 1; i <= filas; i++) {
               if($("#proveedor"+i).length>0){
                  $("#proveedor"+i).html(resp);
               }
             };
           }
           $('.selectpicker').selectpicker("refresh"); 
           Swal.fire("Correcto!", "Los datos se actualizaron de forma correcta.", "success");
        }
    }); 

}

function calcularMontoFilaPersonalServicio(fila){
  var cantidad =$("#cantidad_personal"+fila).val();
  var dias =$("#dias_personal"+fila).val();
  var monto =$("#monto_personal"+fila).val();
  var montoExt =$("#monto_personalext"+fila).val();
  $("#total_personal"+fila).val(redondeo(cantidad*monto*dias));
  $("#total_personalext"+fila).val(redondeo(cantidad*montoExt*dias));
  var n =$("#cantidad_filaspersonal").val();
  var suma=0; 
  var sumaExt=0;
  for (var i = 1; i < n; i++) {
    suma+=parseFloat($("#total_personal"+i).val());
    sumaExt+=parseFloat($("#total_personalext"+i).val());
  };
  $("#total_personalservicio").text(redondeo(suma));
  $("#total_personalservicioext").text(redondeo(sumaExt));
}

function cambiarCantidadProductoSimulacion(){
  alert($("#modal_productos").val());
}

function montarMontoLocalExternoTabla(fila){
  var region=$("#local_extranjero"+fila).val();
  if(region==1){
    $("#modal_montopre"+fila).val($("#modal_montopreloc"+fila).val());
  }else{
    $("#modal_montopre"+fila).val($("#modal_montopreext"+fila).val()); 
  }
  calcularTotalPersonalServicio(2);
}
function montarMontoLocalExternoTablaAuditor(fila){
  var region=$("#modal_local_extranjero"+fila).val();
  var columnas =$("#cantidad_columnas"+fila).val();
  if(region==1){ 
    for (var j = 1; j <=columnas; j++) {
     $("#monto_mult"+j+"RRR"+fila).val(redondeo(($("#modal_cantidad_personal"+fila).val()*$("#modal_dias_personal"+fila).val())*parseFloat($("#monto"+j+"RRR"+fila).val())));   
    };    
  }else{
    for (var j = 1; j <=columnas; j++) {
     $("#monto_mult"+j+"RRR"+fila).val(redondeo(($("#modal_cantidad_personal"+fila).val()*$("#modal_dias_personal"+fila).val())*parseFloat($("#montoext"+j+"RRR"+fila).val()))); 
    };   
  }
  calcularTotalPersonalServicioAuditor();
}

function agregarNuevoServicioSimulacion(anio,cod_sim,cod_area){
  var cod_cla=$("#modal_editservicio"+anio).val();
  var cantidad=$("#cantidad_servicios"+anio+"SSS0").val();
  var descripcion=$("#descripcion_servicios"+anio+"SSS0").val();
  var monto=$("#modal_montoserv"+anio+"SSS0").val();
  var unidad=$("#unidad_servicios"+anio+"SSS0").val();
  var anio_fila=$("#anio"+anio+"SSS0").val();
  if(!(cod_cla>0)||cantidad==""||cantidad==0||monto==""){
   Swal.fire("Informativo!", "Debe llenar los campos requeridos", "warning");
  }else{
  var parametros={"descripcion":descripcion,"cod_sim":cod_sim,"cod_cla":cod_cla,"cantidad":cantidad,"monto":monto,"unidad":unidad,"anio":anio,"anio_fila":anio_fila};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "ajaxSaveTipoServicio.php",
        data: parametros,
        beforeSend: function () {
        $("#texto_ajax_titulo").html("Agregando servicio..."); 
          iniciarCargaAjax();
        },
        success:  function (respu) {
           detectarCargaAjax();
           $("#texto_ajax_titulo").html("Procesando Datos");
           var respuesta=respu.split("###");
           var resp=respuesta[0];
           if(resp==0){
              listarServiciosSimulacionSoloServicio(anio,cod_area,respuesta[1],anio_fila);
            //listarServiciosSimulacion(anio,cod_area);
           }else{
            Swal.fire("Informativo!", "El servicio ya existe!", "warning");
           } 
        }
    });      
  }
}

function agregarNuevoPersonalSimulacion(anio,cod_sim,cod_area){
  var cod_cla=$("#modal_editpersonal"+anio).val();
  var cantidad=$("#cantidad_personal"+anio+"FFF0").val();
  var monto=$("#modal_montopre"+anio+"FFF0").val();
  var dias=$("#dias_personal"+anio+"FFF0").val();
  
  if(!(cod_cla>0)||cantidad==""||cantidad==0||monto==""){
   Swal.fire("Informativo!", "Debe llenar los campos requeridos", "warning");
  }else{
  var parametros={"cod_sim":cod_sim,"cod_cla":cod_cla,"cantidad":cantidad,"monto":monto,"dias":dias,"anio":anio};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "ajaxSavePersonal.php",
        data: parametros,
        beforeSend: function () {
        $("#texto_ajax_titulo").html("Agregando servicio..."); 
          iniciarCargaAjax();
        },
        success:  function (respu) {
           detectarCargaAjax();
           $("#texto_ajax_titulo").html("Procesando Datos");
           var respuesta=respu.split("###");
           var resp=respuesta[0];
           if(resp==0){
              listarServiciosSimulacionSoloAuditor(anio,cod_area,respuesta[1]);
           }else{
            Swal.fire("Informativo!", "El personal ya existe!", "warning");
           } 
        }
    });      
  }
}

function  listarServiciosSimulacionSoloAuditor(anio,cod_area,codigo){
  var cod_sim=$("#cod_simulacion").val();
  var usd=$("#cambio_moneda").val();
  var parametros={"cod_sim":cod_sim,"cod_area":cod_area,"anio":anio,"usd":usd,"codigo":codigo,"cantidad_filas":$("#modal_numeropersonal"+anio).val(),"cantidad_personal":$("#modal_cantidadpersonal"+anio).val(),"dias_simulacion":$("#dias_plan").val()};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "ajaxListAuditorSolo.php",
        data: parametros,
        beforeSend: function () {
        $("#texto_ajax_titulo").html("Listando servicios..."); 
          iniciarCargaAjax();
        },
        success:  function (resp) {
           detectarCargaAjax();
           $("#texto_ajax_titulo").html("Procesando Datos");
           $("#modal_body_tabla_personal"+anio).append(resp);
           $("#cantidad_personal"+anio+"FFF0").val("1");
           $("#dias_personal"+anio+"FFF0").val("0");
           $("#modal_montopre"+anio+"FFF0").val("0");
           $("#modal_montopreUSD"+anio+"FFF0").val("0");
           $("#modal_montopretotal"+anio+"FFF0").val("0");
           $("#modal_montopretotalUSD"+anio+"FFF0").val("0");
           calcularTotalPersonalServicio(anio,2);
           $('.selectpicker').selectpicker("refresh");
        }
    });
}

function ponerDescripcionServicio(anio){
 $("#descripcion_servicios"+anio+"SSS0").val($("#modal_editservicio"+anio+" option:selected").text());
}

function listarServiciosSimulacionSoloServicio(anio,cod_area,codigo,anio_fila){
  var cod_sim=$("#cod_simulacion").val();
  var usd=$("#cambio_moneda").val();
  var anio_general=$("#anio_simulacion").val();
  var parametros={"anio_general":anio_general,"anio_fila":anio_fila,"cod_sim":cod_sim,"cod_area":cod_area,"anio":anio,"usd":usd,"codigo":codigo,"cantidad_filas":$("#modal_numeroservicio"+anio).val()};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "ajaxListTipoServicioSolo.php",
        data: parametros,
        beforeSend: function () {
        $("#texto_ajax_titulo").html("Listando servicios..."); 
          iniciarCargaAjax();
        },
        success:  function (resp) {
           detectarCargaAjax();
           $("#texto_ajax_titulo").html("Procesando Datos");
           $("#modal_body_tabla_servicios"+anio).append(resp);
           $("#cantidad_servicios"+anio+"SSS0").val("1");
           $("#unidad_servicios"+anio+"SSS0").val("1");
           $("#modal_montoserv"+anio+"SSS0").val("0");
           $("#modal_montoservUSD"+anio+"SSS0").val("0");
           $("#modal_montoservtotal"+anio+"SSS0").val("0");
           $("#modal_montoservtotalUSD"+anio+"SSS0").val("0");
           calcularTotalFilaServicio(anio,2);
           $('.selectpicker').selectpicker("refresh");
        }
    });
}

function listarServiciosSimulacion(anio,cod_area){
  var cod_sim=$("#cod_simulacion").val();
  var usd=$("#cambio_moneda").val();
  var parametros={"cod_sim":cod_sim,"cod_area":cod_area,"anio":anio,"usd":usd};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "ajaxListTipoServicio.php",
        data: parametros,
        beforeSend: function () {
        $("#texto_ajax_titulo").html("Listando servicios..."); 
          iniciarCargaAjax();
        },
        success:  function (resp) {
           detectarCargaAjax();
           $("#texto_ajax_titulo").html("Procesando Datos");
           $("#modal_contenidoservicios"+anio).html(resp);
           $('.selectpicker').selectpicker("refresh");
        }
    });
}
function cambiarTituloPersonalModal(anio){
  /*if($("#num_titulopersonal"+anio).length){
     $("#num_titulopersonal"+anio).html("("+($("#modal_numeropersonal"+anio).val()-1)+")");
  }
  if($("#num_tituloservicios"+anio).length){
     $("#num_tituloservicios"+anio).html("("+($("#modal_numeroservicio"+anio).val()-1)+")");
  }*/
}

function descuento_convertir_a_porcentaje(id){
  var monto_precio=$("#monto_precio"+id).val();// precio de item
  var descuento_bob=$("#descuento_bob"+id).val();//monto de descuento Bob
  var cantidad_x=$("#cantidad"+id).val();
  if(monto_precio<0 || monto_precio==0 || monto_precio==null){
    Swal.fire("Informativo!", "El Precio del Item NO debe ser 0 o número negativo!", "warning");
  }else{
    if(cantidad_x<0 || cantidad_x==0 || cantidad_x==null){
      Swal.fire("Informativo!", "La cantidad NO debe ser 0 o número negativo!", "warning");
    }else{
      var numero_porcentaje=parseFloat(descuento_bob)*100/parseFloat(monto_precio)/parseFloat(cantidad_x);
      //alert(numero_porcentaje);
      $("#descuento_por"+id).val(numero_porcentaje.toFixed(2));
      calcularTotalFilaServicio2();      
    }
  }
}
function descuento_convertir_a_bolivianos(id){
  var monto_precio=$("#monto_precio"+id).val();// precio de item
  var descuento_por=$("#descuento_por"+id).val();//monto de descuento %
  var cantidad_x=$("#cantidad"+id).val();
  if(monto_precio<0 || monto_precio==0 || monto_precio==null){
    Swal.fire("Informativo!", "El Precio del Item NO debe ser 0 o número negativo!", "warning");
  }else{
    if(cantidad_x<0 || cantidad_x==0 || cantidad_x==null){
      Swal.fire("Informativo!", "La cantidad NO debe ser 0 o número negativo!", "warning");
    }else{      
    var monto_bob_porcentaje=parseFloat(descuento_por)*parseFloat(monto_precio)*parseFloat(cantidad_x)/100;
    //alert(monto_bob_porcentaje);
    $("#descuento_bob"+id).val(monto_bob_porcentaje.toFixed(2));
    calcularTotalFilaServicio2();
    }
  }
}
function descuento_convertir_a_porcentaje_manual(id){
  var monto_precio=$("#monto_precio"+id).val();// precio de item
  var descuento_bob=$("#descuento_bob"+id).val();//monto de descuento Bob
  var cantidad_x=$("#cantidad"+id).val();
  if(monto_precio<0 || monto_precio==0 || monto_precio==null){
    Swal.fire("Informativo!", "El Precio del Item NO debe ser 0 o número negativo!", "warning");
  }else{
    if(cantidad_x<0 || cantidad_x==0 || cantidad_x==null){
      Swal.fire("Informativo!", "La cantidad NO debe ser 0 o número negativo!", "warning");
    }else{
      var numero_porcentaje=parseFloat(descuento_bob)*100/parseFloat(monto_precio)/parseFloat(cantidad_x);
      //alert(numero_porcentaje);
      $("#descuento_por"+id).val(numero_porcentaje.toFixed(2));
      activarInputMontoFilaServicio_manual();    
    }
  }
}
function descuento_convertir_a_bolivianos_manual(id){
  var monto_precio=$("#monto_precio"+id).val();// precio de item
  var descuento_por=$("#descuento_por"+id).val();//monto de descuento %
  var cantidad_x=$("#cantidad"+id).val();
  if(monto_precio<0 || monto_precio==0 || monto_precio==null){
    Swal.fire("Informativo!", "El Precio del Item NO debe ser 0 o número negativo!", "warning");
  }else{
    if(cantidad_x<0 || cantidad_x==0 || cantidad_x==null){
      Swal.fire("Informativo!", "La cantidad NO debe ser 0 o número negativo!", "warning");
    }else{      
    var monto_bob_porcentaje=parseFloat(descuento_por)*parseFloat(monto_precio)*parseFloat(cantidad_x)/100;
    //alert(monto_bob_porcentaje);
    $("#descuento_bob"+id).val(monto_bob_porcentaje.toFixed(2));
    activarInputMontoFilaServicio_manual();
    }
  }
}


function activarInputMontoFilaServicio2(){

  calcularTotalFilaServicio2();
}
function calcularTotalFilaServicio2(){
  // console.log('entre');
  var sumal=0;  
  var total= $("#modal_numeroservicio").val();
  var comprobante_auxiliar=0;
  // alert("entre");
  for (var i=1;i<=(total-1);i++){          
    var monto_precio=$("#monto_precio"+i).val();    
    var descuento_bob=$("#descuento_bob"+i).val();
    var cantidad_x=document.getElementById("cantidad"+i).value;    
    if(descuento_bob==null ||descuento_bob=='NaN'||descuento_bob==' '||descuento_bob==''){descuento_bob=0;}
    var monto_importe_total=(parseFloat(monto_precio)*parseFloat(cantidad_x))-parseFloat(descuento_bob);
    //alert(monto_importe_total);
    $("#modal_importe"+i).val(monto_importe_total);//irá en hidden 
    $("#modal_importe_dos"+i).val(number_format(monto_importe_total,2));//para mostrar con formato

    var check=document.getElementById("modal_check"+i).checked;
    if(check) {//BUSACMOS LOS CHECK ACTIVOS
      comprobante_auxiliar=comprobante_auxiliar+1;        
      //sumanos los importes
      sumal+=parseFloat($("#modal_importe"+i).val());
      //sacamos los datos de los items que se activaron
      var cod_serv_tiposerv = document.getElementById("cod_serv_tiposerv"+i).value;
      var servicio = document.getElementById("servicio"+i).value;
      var nombre_servicio = document.getElementById("nombre_servicio"+i).value;
      var cantidad=document.getElementById("cantidad"+i).value;
      var importe=document.getElementById("importe"+i).value;
      // aqui se guardan los servicios activados
      document.getElementById("cod_serv_tiposerv_a"+i).value=cod_serv_tiposerv;
      
      //alert("servicio:"+servicio);
      document.getElementById("servicio_a"+i).value=servicio;

      document.getElementById("cantidad_a"+i).value=cantidad;
      document.getElementById("importe_a"+i).value=importe;        
      // alert(servicio);
    }else{ nombre_servicio="";document.getElementById("servicio_a"+i).value="";}

    //document.getElementById("descripcion_alterna"+i).value=nombre_servicio;//glosa descrip
  } 
  var resulta=sumal;
  //sumamos la parte que se adiciona


  var costos_total=document.getElementById("modal_totalmontoserv_costo_a").value;//con formato
  // alert();
  nro_items = document.getElementById("cantidad_filas").value;
  if(nro_items==0){
    $("#monto_total").val(number_format(costos_total,2));
    $("#monto_total_a").val(costos_total);//tipo hidde
  }else{
    var importe_ajax=0;
    for (var j = 1; j <=nro_items; j++) {    
      importe_ajax+=parseFloat(document.getElementById("modal_importe_add"+j).value);
    }
    var suma_servicios_add=parseFloat(costos_total)+parseFloat(importe_ajax);
    $("#monto_total").val(number_format(suma_servicios_add,2));
    $("#monto_total_a").val(suma_servicios_add);//tipo hidde
    // alert(importe_ajax);
  }
  document.getElementById("modal_totalmontoserv").value=number_format(resulta,2);
  //   $("#modal_totalmontoserv").text(resulta);
  document.getElementById("modal_totalmontos").value=resulta;//escondido
  document.getElementById("comprobante_auxiliar").value=comprobante_auxiliar;
  //sumartotalAddServiciosFacturacion(id);
}
function verificar_item_activo(index){
  var check=document.getElementById("modal_check"+index).checked;
  if(check){
    calcularTotalFilaServicio2Costos();
  }else{
    Swal.fire("Informativo!", "Por favor, active la fila.", "warning");
  }

}
function activarInputMontoFilaServicio_manual()
{ 
  
  // alert("1s");
  var sumal=0;  
  var total= $("#modal_numeroservicio").val();  
  for (var i=1;i<=(total-1);i++){              
    var importe=$("#monto_precio"+i).val();
    var cantidad_x=document.getElementById("cantidad"+i).value;
    var check=document.getElementById("modal_check"+i).checked;    
    var descuento_bob=$("#descuento_bob"+i).val();//monto de descuento Bob
    if(check) {              
      sumal+=parseFloat(importe)*parseFloat(cantidad_x)-parseFloat(descuento_bob);
    }
  } 
  var resulta=sumal;
  document.getElementById("modal_totalmontoserv_costo_a").value=resulta;//si formato
  calcularTotalFilaServicio2();
}
function calcularTotalFilaServicio2Costos(){
  var sumal=0;  
  var suma_pagado=0;  
  var total= $("#modal_numeroservicio").val();
  var comprobante_auxiliar=0;
  for (var i=1;i<=(total-1);i++){          
    var importe_a_pagar=$("#importe_a_pagar"+i).val();
    var importe=$("#modal_importe"+i).val();
    var modal_importe_pagado_dos_a=$("#modal_importe_pagado_dos_a"+i).val();
    // alert(importe+"-"+modal_importe_pagado_dos_a);
    var saldo=parseFloat(importe)-parseFloat(modal_importe_pagado_dos_a);
    var monto_importe_total=parseFloat(importe_a_pagar);
    var check=document.getElementById("modal_check"+i).checked;
    if(monto_importe_total>saldo){      
      Swal.fire("Informativo!", "El Monto en la fila "+i+" Supera al Saldo! ("+number_format(saldo,2)+").", "warning");
      $("#importe_a_pagar"+i).val(0);
    }else{
      if(check) {//BUSACMOS LOS CHECK ACTIVOS
        comprobante_auxiliar=comprobante_auxiliar+1;        
        //sumanos los importes
        sumal+=parseFloat($("#importe_a_pagar"+i).val());
        suma_pagado+=parseFloat($("#modal_importe_pagado_dos_a"+i).val());
        //sacamos los datos de los items que se activaron
        var cod_serv_tiposerv = document.getElementById("cod_serv_tiposerv"+i).value;
        var servicio = document.getElementById("servicio"+i).value;      
        var cantidad=document.getElementById("cantidad"+i).value;
        var importe=document.getElementById("importe"+i).value;
        // aqui se guardan los servicios activados
        document.getElementById("cod_serv_tiposerv_a"+i).value=cod_serv_tiposerv;      
        document.getElementById("servicio_a"+i).value=servicio;
        document.getElementById("cantidad_a"+i).value=cantidad;
        document.getElementById("importe_a"+i).value=importe;
      }else{ document.getElementById("servicio_a"+i).value="";}  
    }
    
  } 

  var resulta=sumal;  
  document.getElementById("modal_totalmontoserv_costo").value=number_format(resulta,2);//con formato
  document.getElementById("modal_totalmontoserv_costo_a").value=resulta;//si formato
  document.getElementById("modal_totalmontoserv_pagado").value=number_format(suma_pagado,2);
  // document.getElementById("modal_totalmontos__costo").value=resulta;//escondido
  // document.getElementById("comprobante_auxiliar_costo").value=comprobante_auxiliar;
  calcularTotalFilaServicio2();
}
function ajax_Cliente_razonsocial(combo){
  var contenedor;
  var codigo_cliente=combo.value;
  contenedor = document.getElementById('contenedor_razonsocial');
  ajax=nuevoAjax();
  ajax.open('GET', 'simulaciones_servicios/ajax_cliente_razonsocial.php?codigo_cliente='+codigo_cliente,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);
    }
  }
  ajax.send(null)  
}//personal - uo-area tipo caja chica

function agregarNuevoServicioSimulacion2(cod_sim,cod_area){
  // var anio=1;
  var cod_fac_x=$("#cod_facturacion").val();
  var cod_simu_x=$("#cod_simulacion").val();

  var anio=$("#anio_servicio").val();
  // alert(anio);
  var cod_cla=$("#modal_editservicio").val();
  var cantidad=$("#cantidad_servicios").val();
  var monto=$("#modal_montoserv").val();
  var unidad=$("#unidad_servicios").val();
  
  // alert(cod_fac_x+"-"+cod_simu_x);

  if(!(anio>0)||!(cod_cla>0)||cantidad==""||cantidad==0||monto==""){
   Swal.fire("Informativo!", "Debe llenar los campos requeridos", "warning");
  }else{
  var parametros={"cod_sim":cod_sim,"cod_cla":cod_cla,"cantidad":cantidad,"monto":monto,"unidad":unidad,"anio":anio};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "simulaciones_servicios/ajaxSaveTipoServicio.php",
        data: parametros,
        beforeSend: function () {
        $("#texto_ajax_titulo").html("Agregando servicio..."); 
          iniciarCargaAjax();
        },        
        success:  function (resp) {
           detectarCargaAjax();
           $("#texto_ajax_titulo").html("Procesando Datos");
           if(resp==0){
            // listarServiciosSimulacion(anio,cod_area);
            alerts.showSwal('success-message','index.php?opcion=registerSolicitud_facturacion&cod_s='+cod_simu_x+'&cod_f='+cod_fac_x);
           }else{
            Swal.fire("Informativo!", "El servicio ya existe!", "warning");
           } 
        }
    });      
  }
}
function AgregarSeviciosFacturacion2(obj) {
  if($("#add_boton").length){
    $("#add_boton").attr("disabled",true);
  }
  var cod_area=document.getElementById("cod_area").value;
    var IdTipo=document.getElementById("IdTipo").value;
  // alert(cod_area);
      numFilas++;
      cantidadItems++;
      
      filaActiva=numFilas;
      document.getElementById("cantidad_filas").value=numFilas;
      console.log("num: "+numFilas+" cantidadItems: "+cantidadItems);
      fi = document.getElementById('fiel');
      contenedor = document.createElement('div');
      contenedor.id = 'div'+numFilas;  
      fi.type="style";
      fi.appendChild(contenedor);
      var divDetalle;
      divDetalle=$("#div"+numFilas);
      //document.getElementById('nro_cuenta').focus();
      ajax=nuevoAjax();
      ajax.open("GET","simulaciones_servicios/ajax_addserviciosfacturacion.php?idFila="+numFilas+"&cod_area="+cod_area+'&IdTipo='+IdTipo,true);
      ajax.onreadystatechange=function(){
        if (ajax.readyState==4) {
          divDetalle.html(ajax.responseText);
          divDetalle.bootstrapMaterialDesign();   
          // $('#codigo_rendicionA').val("");
          // $('#cod_tipo_documentoA').val("");//
          $('#modal_editservicio').val("");
          $('#cantidad_servicios').val("");
          $('#modal_montoserv').val("");

          $('.selectpicker').selectpicker("refresh");
          // $('#modalAgregarDR').modal('show');
          // if(numFilas!=1){
          //   //alert((numFilas-1)+"-"+$("#monto_A"+(numFilas-1)).val());
          //   sumartotalprueba2(numFilas-1);
          // }        
          if($("#add_boton").length){
            $("#add_boton").removeAttr("disabled");
          }
          return false;
       }
      }   
      ajax.send(null);
}

function AgregarSeviciosFacturacion_soli_manual(obj) {
  if($("#add_boton").length){
    $("#add_boton").attr("disabled",true);
  }
      numFilas++;
      cantidadItems++;
      
      filaActiva=numFilas;
      document.getElementById("cantidad_filas").value=numFilas;
      console.log("num: "+numFilas+" cantidadItems: "+cantidadItems);
      fi = document.getElementById('fiel');
      contenedor = document.createElement('div');
      contenedor.id = 'div'+numFilas;  
      fi.type="style";
      fi.appendChild(contenedor);
      var divDetalle;
      divDetalle=$("#div"+numFilas);
      //document.getElementById('nro_cuenta').focus();
      ajax=nuevoAjax();
      ajax.open("GET","simulaciones_servicios/ajax_addserviciosfacturacion_manual.php?idFila="+numFilas,true);
      ajax.onreadystatechange=function(){
        if (ajax.readyState==4) {
          divDetalle.html(ajax.responseText);
          divDetalle.bootstrapMaterialDesign();   
          // $('#codigo_rendicionA').val("");
          // $('#cod_tipo_documentoA').val("");//
          $('#modal_editservicio').val("");
          $('#cantidad_servicios').val("");
          $('#modal_montoserv').val("");

          $('.selectpicker').selectpicker("refresh");
          // $('#modalAgregarDR').modal('show');
          // if(numFilas!=1){
          //   //alert((numFilas-1)+"-"+$("#monto_A"+(numFilas-1)).val());
          //   sumartotalprueba2(numFilas-1);
          // }        
          if($("#add_boton").length){
            $("#add_boton").removeAttr("disabled");
          }
          return false;
       }
      }   
      ajax.send(null);
}

function AgregarSeviciosFacturacion2_servicios(obj) {
  if($("#add_boton").length){
    $("#add_boton").attr("disabled",true);
  }
  var IdTipo=document.getElementById("IdTipo").value;
  // alert(IdTipo);
      numFilas++;
      cantidadItems++;
      
      filaActiva=numFilas;
      document.getElementById("cantidad_filas").value=numFilas;
      console.log("num: "+numFilas+" cantidadItems: "+cantidadItems);
      fi = document.getElementById('fiel');
      contenedor = document.createElement('div');
      contenedor.id = 'div'+numFilas;  
      fi.type="style";
      fi.appendChild(contenedor);
      var divDetalle;
      divDetalle=$("#div"+numFilas);
      //document.getElementById('nro_cuenta').focus();
      ajax=nuevoAjax();
      ajax.open("GET","servicios_presupuestos/ajax_addserviciosfacturacion.php?idFila="+numFilas+"&IdTipo="+IdTipo,true);
      ajax.onreadystatechange=function(){
        if (ajax.readyState==4) {
          divDetalle.html(ajax.responseText);
          divDetalle.bootstrapMaterialDesign();   
          // $('#codigo_rendicionA').val("");
          // $('#cod_tipo_documentoA').val("");//
          $('#modal_editservicio').val("");
          $('#cantidad_servicios').val("");
          $('#modal_montoserv').val("");

          $('.selectpicker').selectpicker("refresh");
          // $('#modalAgregarDR').modal('show');
          // if(numFilas!=1){
          //   //alert((numFilas-1)+"-"+$("#monto_A"+(numFilas-1)).val());
          //   sumartotalprueba2(numFilas-1);
          // }        
          if($("#add_boton").length){
            $("#add_boton").removeAttr("disabled");
          }
          return false;
       }
      }   
      ajax.send(null);
}
function descuento_convertir_a_porcentaje_add(id){
  var monto_precio=$("#modal_montoserv"+id).val();// precio de item
  var descuento_bob=$("#descuento_bob_add"+id).val();//monto de descuento Bob
  var cantidad=$("#cantidad_servicios"+id).val();//
  if(monto_precio<0 || monto_precio==0 || monto_precio==null){
    Swal.fire("Informativo!", "El Precio del Item NO debe ser 0 o número negativo!", "warning");
  }else{
     if(cantidad<0 || cantidad==0 || cantidad==null){
      Swal.fire("Informativo!", "La cantidad NO debe ser 0 o número negativo!", "warning");
    }else{
      var numero_porcentaje=parseFloat(descuento_bob)*100/parseFloat(monto_precio)/parseFloat(cantidad);
      //alert(numero_porcentaje);
      $("#descuento_por_add"+id).val(numero_porcentaje.toFixed(2));
      //agregamos al total
      var importe_total=parseFloat(monto_precio)*parseFloat(cantidad)-parseFloat(descuento_bob);    
      $("#modal_importe_add"+id).val(importe_total);//irá en hidden 
      $("#modal_importe_dos_add"+id).val(number_format(importe_total,2));//para mostrar con formato
      sumartotalAddServiciosFacturacion(id); 
    }
  }
}
function descuento_convertir_a_bolivianos_add(id){ 
  var monto_precio=$("#modal_montoserv"+id).val();// precio de item
  var descuento_por=$("#descuento_por_add"+id).val();//monto de descuento %
  var cantidad=$("#cantidad_servicios"+id).val();//
  if(monto_precio<0 || monto_precio==0 || monto_precio==null){
    Swal.fire("Informativo!", "El Precio del Item NO debe ser 0 o número negativo!", "warning");
  }else{
      if(cantidad<0 || cantidad==0 || cantidad==null){
        Swal.fire("Informativo!", "La cantidad NO debe ser 0 o número negativo!", "warning");
      }else{        
        var monto_bob_porcentaje=parseFloat(descuento_por)*parseFloat(monto_precio)*parseFloat(cantidad)/100;
        //alert(monto_bob_porcentaje);
        $("#descuento_bob_add"+id).val(monto_bob_porcentaje.toFixed(2));

         //agregamos al total
        var importe_total=parseFloat(monto_precio)*parseFloat(cantidad)-parseFloat(monto_bob_porcentaje);    
        $("#modal_importe_add"+id).val(importe_total);//irá en hidden 
        $("#modal_importe_dos_add"+id).val(number_format(importe_total,2));//para mostrar con formato
         sumartotalAddServiciosFacturacion(id);
      }
  }  
}


function sumartotalAddServiciosFacturacion(id){
  var sumatotal=0;
  var formulariop = document.getElementById("formSoliFactTcp");
  // alert(formulariop.elements.length);
  for (var i=0;i<formulariop.elements.length;i++){
    if (formulariop.elements[i].id.indexOf("modal_importe_add") !== -1 ){    
      var monto_precio=formulariop.elements[i].value;      
      // console.log("monto "+monto_precio);
      sumatotal += parseFloat((formulariop.elements[i].value) * 1);
    }  
  }
  var modalmonto_total=parseFloat(document.getElementById("modal_totalmontos").value);
  // alert(modalmonto_total);
  if (modalmonto_total==''){modalmonto_total=0;}

  monto_Total= parseFloat(modalmonto_total)+parseFloat(sumatotal);

  $("#monto_total").val(number_format(monto_Total,2));  
  $("#monto_total_a").val(monto_Total);  
  // $("#monto_faltante").val(monto_faltante);  
}
function cantidad_por_importe_manual_sf(id){
  var monto_precio=$("#modal_montoserv"+id).val();// precio de item
  var descuento_bob=$("#descuento_bob_add"+id).val();//monto de descuento %
  var cantidad=$("#cantidad_servicios"+id).val();//
  if(monto_precio<0 || monto_precio==0 || monto_precio==null){
    Swal.fire("Informativo!", "El Precio del Item NO debe ser 0 o número negativo!", "warning");
  }else{
    if(cantidad<0 || cantidad==0 || cantidad==null){
      Swal.fire("Informativo!", "La cantidad NO debe ser 0 o número negativo!", "warning");
    }else{
      var monto_bob_porcentaje=parseFloat(monto_precio)*parseFloat(cantidad)-parseFloat(descuento_bob);
       //agregamos al total      
      $("#modal_importe_add"+id).val(monto_bob_porcentaje);//irá en hidden 
      $("#modal_importe_dos_add"+id).val(number_format(monto_bob_porcentaje,2));//para mostrar con formato
       sumartotalAddServiciosFacturacion_manual(id);
    }
  }  
}
function cantidad_por_importe_servicio_sf(id){  
  var monto_precio=$("#modal_montoserv"+id).val();// precio de item
  var descuento_bob=$("#descuento_bob_add"+id).val();//monto de descuento %
  var cantidad=$("#cantidad_servicios"+id).val();//
  if(monto_precio<0 || monto_precio==0 || monto_precio==null){
    Swal.fire("Informativo!", "El Precio del Item NO debe ser 0 o número negativo!", "warning");
  }else{
    if(cantidad<0 || cantidad==0 || cantidad==null){
      Swal.fire("Informativo!", "La cantidad NO debe ser 0 o número negativo!", "warning");
    }else{
      var monto_bob_porcentaje=parseFloat(monto_precio)*parseFloat(cantidad)-parseFloat(descuento_bob);
       //agregamos al total      
      $("#modal_importe_add"+id).val(monto_bob_porcentaje);//irá en hidden 
      $("#modal_importe_dos_add"+id).val(number_format(monto_bob_porcentaje,2));//para mostrar con formato
       sumartotalAddServiciosFacturacion(id);
    }
  }  
}
function descuento_convertir_a_porcentaje_add_manual(id){
  var monto_precio=$("#modal_montoserv"+id).val();// precio de item
  var descuento_bob=$("#descuento_bob_add"+id).val();//monto de descuento Bob
  var cantidad=$("#cantidad_servicios"+id).val();//
  if(monto_precio<0 || monto_precio==0 || monto_precio==null){
    Swal.fire("Informativo!", "El Precio del Item NO debe ser 0 o número negativo!", "warning");
  }else{
    if(cantidad<0 || cantidad==0 || cantidad==null){
      Swal.fire("Informativo!", "La cantidad NO debe ser 0 o número negativo!", "warning");
    }else{
      var numero_porcentaje=parseFloat(descuento_bob)*100/parseFloat(monto_precio)/parseFloat(cantidad);
      //alert(numero_porcentaje);
      $("#descuento_por_add"+id).val(numero_porcentaje.toFixed(2));
      //agregamos al total
      var importe_total=parseFloat(monto_precio)*parseFloat(cantidad)-parseFloat(descuento_bob);    
      $("#modal_importe_add"+id).val(importe_total);//irá en hidden 
      $("#modal_importe_dos_add"+id).val(number_format(importe_total,2));//para mostrar con formato
      sumartotalAddServiciosFacturacion_manual(id);
    }
    
  }
}


function descuento_convertir_a_bolivianos_add_manual(id){
  var monto_precio=$("#modal_montoserv"+id).val();// precio de item
  var descuento_por=$("#descuento_por_add"+id).val();//monto de descuento %
  var cantidad=$("#cantidad_servicios"+id).val();//

  if(monto_precio<0 || monto_precio==0 || monto_precio==null){
    Swal.fire("Informativo!", "El Precio del Item NO debe ser 0 o número negativo!", "warning");
  }else{
    if(cantidad<0 || cantidad==0 || cantidad==null){
      Swal.fire("Informativo!", "La cantidad NO debe ser 0 o número negativo!", "warning");
    }else{
      var monto_bob_porcentaje=parseFloat(descuento_por)*parseFloat(monto_precio)*parseFloat(cantidad)/100;
      //alert(monto_bob_porcentaje);
      $("#descuento_bob_add"+id).val(monto_bob_porcentaje.toFixed(2));

       //agregamos al total
      var importe_total=parseFloat(monto_precio)*parseFloat(cantidad)-parseFloat(monto_bob_porcentaje);    
      $("#modal_importe_add"+id).val(importe_total);//irá en hidden 
      $("#modal_importe_dos_add"+id).val(number_format(importe_total,2));//para mostrar con formato
       sumartotalAddServiciosFacturacion_manual(id);
    }
  }  
}

function sumartotalAddServiciosFacturacion_manual(id){
  var sumatotal=0;
  var formulariop = document.getElementById("formSoliFactTcp");
  // alert(formulariop.elements.length);
  for (var i=0;i<formulariop.elements.length;i++){
    if (formulariop.elements[i].id.indexOf("modal_importe_add") !== -1 ){    
      var monto_precio=formulariop.elements[i].value;      
      console.log("monto "+monto_precio);
      sumatotal += parseFloat((formulariop.elements[i].value) * 1);
    }  
  }
  var modalmonto_total=parseFloat(0);
  // alert(modalmonto_total);
  if (modalmonto_total==''){modalmonto_total=0;}

  monto_Total= parseFloat(modalmonto_total)+parseFloat(sumatotal);

  $("#monto_total").val(number_format(monto_Total,2));  
  $("#monto_total_a").val(monto_Total);  
  // $("#monto_faltante").val(monto_faltante);  
}
function borrarItemSeriviciosFacturacion_manual(idF){ 
  var elem = document.getElementById('div'+idF);
  elem.parentNode.removeChild(elem);
  if(idF<numFilas){
    for (var i = parseInt(idF); i < (numFilas+1); i++) {
      var nuevoId=i+1;
      $("#div"+nuevoId).attr("id","div"+i);
      $("#modal_editservicio"+nuevoId).attr("name","modal_editservicio"+i);
      $("#modal_editservicio"+nuevoId).attr("id","modal_editservicio"+i);
      $("#cantidad_servicios"+nuevoId).attr("name","cantidad_servicios"+i);
      $("#cantidad_servicios"+nuevoId).attr("id","cantidad_servicios"+i);

      $("#modal_montoserv"+nuevoId).attr("name","modal_montoserv"+i);
      $("#modal_montoserv"+nuevoId).attr("id","modal_montoserv"+i);
      $("#descripcion"+nuevoId).attr("name","descripcion"+i);
      $("#descripcion"+nuevoId).attr("id","descripcion"+i);       
    }
  } 
  numFilas=numFilas-1;
  cantidadItems=cantidadItems-1;
  filaActiva=numFilas;
  document.getElementById("cantidad_filas").value=numFilas;
  // document.getElementById("totalhab").value=numFilas;
  $("#monto_total").val(numFilas);
  console.log("num: "+numFilas+" cantidadItems: "+cantidadItems); 
  sumartotalAddServiciosFacturacion_manual("null");   
}
function borrarItemSeriviciosFacturacion(idF){ 
  var elem = document.getElementById('div'+idF);
  elem.parentNode.removeChild(elem);
  if(idF<numFilas){
    for (var i = parseInt(idF); i < (numFilas+1); i++) {
      var nuevoId=i+1;
      $("#div"+nuevoId).attr("id","div"+i);
      $("#modal_editservicio"+nuevoId).attr("name","modal_editservicio"+i);
      $("#modal_editservicio"+nuevoId).attr("id","modal_editservicio"+i);
      $("#cantidad_servicios"+nuevoId).attr("name","cantidad_servicios"+i);
      $("#cantidad_servicios"+nuevoId).attr("id","cantidad_servicios"+i);

      $("#modal_montoserv"+nuevoId).attr("name","modal_montoserv"+i);
      $("#modal_montoserv"+nuevoId).attr("id","modal_montoserv"+i);
      $("#descripcion"+nuevoId).attr("name","descripcion"+i);
      $("#descripcion"+nuevoId).attr("id","descripcion"+i);       
    }
  } 
  numFilas=numFilas-1;
  cantidadItems=cantidadItems-1;
  filaActiva=numFilas;
  document.getElementById("cantidad_filas").value=numFilas;
  // document.getElementById("totalhab").value=numFilas;
  $("#monto_total").val(numFilas);
  console.log("num: "+numFilas+" cantidadItems: "+cantidadItems); 
  sumartotalAddServiciosFacturacion("null");   
}
// var areas_tabla=[]; 
function filtrarSolicitudRecursosServicios(cod_sim,cod_solicitud,unidad,area){
  var anio =$("#anio_solicitud").val();
  var itemDetalle =$('select[id="item_detalle_solicitud"] option:selected').text();
  var codigoDetalle =$("#item_detalle_solicitud").val();
   var parametros={"cod_sim":cod_sim,"cod_solicitud":cod_solicitud,"anio":anio,"item_detalle":itemDetalle,"unidad":unidad,"area":area,"codigo_detalle":codigoDetalle};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "ajaxSolicitudDetalleSimulacion3.php",
        data: parametros,
        beforeSend: function () {
        $("#texto_ajax_titulo").html("Filtrando datos..."); 
          iniciarCargaAjax();
        },        
        success:  function (resp) {
           detectarCargaAjax();
           $("#texto_ajax_titulo").html("Procesando Datos");
           $("#detalles_solicitud").html(resp);
           $('.selectpicker').selectpicker("refresh");
        }
    });      
}

function filtrarSolicitudRecursosServiciosItems(){
  var tipo = $("#tipo_solicitud").val();
  var re = $("#simulaciones").val().split("$$$");
  var cod_sim=re[0];
  if(re[1]=="TCP"){
    var url = "ajaxSolicitudDetalleSimulacionNuevoFiltro.php";
  }else{
    var url = "ajaxSolicitudDetalleSimulacionNuevoFiltroSec.php";
  }
  var anio =$("#anio_solicitud").val();
  var itemDetalle =$('select[id="item_detalle_solicitud"] option:selected').text();
  var codigoDetalle =$("#item_detalle_solicitud").val();
   var parametros={"cod_sim":cod_sim,"anio":anio,"item_detalle":itemDetalle,"codigo_detalle":codigoDetalle,"tipo":tipo};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: url,
        data: parametros,
        beforeSend: function () {
        $("#texto_ajax_titulo").html("Filtrando datos..."); 
          iniciarCargaAjax();
        },        
        success:  function (resp) {
           detectarCargaAjax();
           $("#texto_ajax_titulo").html("Procesando Datos");
           $("#fiel").html(resp);
           $('.selectpicker').selectpicker("refresh");
           calcularTotalesSolicitud();
        }
    });      
}


function filtrarSolicitudRecursosDetalleDatos(){
  var tipo = $("#tipo_solicitud").val();
  if(tipo==1||tipo==-1){
    var res=$("#simulaciones").val().split("$$$");
    var cod_sim=res[0];
    if(res[1]=="SIM"){//propuestas SEC
      var url ="ajaxSolicitudDetalleSimulacionSecNuevo.php";
    }else{ //propuestas TCP TCS
      var url ="ajaxSolicitudDetalleSimulacionNuevo.php";
    }
   var parametros={"cod_sim":cod_sim,"tipo":tipo};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: url,
        data: parametros,
        beforeSend: function () {
          $('#anio_solicitud').html('<option value="all" selected>TODOS</option>');
          $('#item_detalle_solicitud').html('<option value="all" selected>TODOS</option>');
        $("#texto_ajax_titulo").html("Buscando Costos Variables..."); 
          iniciarCargaAjax();
        },        
        success:  function (resp) {
           detectarCargaAjax();
           $("#texto_ajax_titulo").html("Procesando Datos");
           //alert(resp)
           $("#fiel").html(resp);

           $('.selectpicker').selectpicker("refresh");
           calcularTotalesSolicitud();
        }
    });      
  }else{
    Swal.fire("Sin Datos!", "No se encontraron registros", "warning");
  }
}



function nuevoPagoSolicitudRecursos(){
  $("#modalRegistrarPago").modal("show");
}
function mandarValorTitulo(){
  var monto = $("#monto_pago").val();
  if(monto==""){
    monto=0;
  }

  $("#montoTitulo").html(monto);
}
function pagarSolicitudRecursos(){
  var cod_solicitud = $("#cod_solicitud").val();
  var codigo_detalle = $("#codigo_detalle").val();
  var cod_pagoproveedor = $("#cod_pagoproveedor").val();

  var monto = $("#monto_pago").val();
  var saldo = $("#saldo_pago").val();
  var tipo_pago= $("#tipo_pago").val();
  var pagar=0;
  var observaciones_pago= $("#observaciones_pago").val();
  var proveedores_pago= $("#proveedores_pago").val();
  if(monto==""||monto==0||!(tipo_pago>0)||!(proveedores_pago>0)){
    Swal.fire("Informativo!", "Debe llenar los campos requeridos", "warning");
  }else{
    if(tipo_pago==1){
      var banco=$("#banco_pago").val();
       if(!(banco>0)){
         Swal.fire("Informativo!", "Debe llenar los campos requeridos", "warning");
       }else{
        
         if(($("#emitidos_pago").val()=="####")){
          Swal.fire("Informativo!", "Debe seleccionar un cheque", "warning");
         }else{
           if($("#numero_cheque").val()==""||$("#nombre_ben").val()==""){
             Swal.fire("Informativo!", "Debe llenar los campos numero y nombre beneficiario", "warning");
           }else{
            var numero_cheque=$("#numero_cheque").val();
            var nombre_ben=$("#nombre_ben").val();
             var cheque=$("#emitidos_pago").val().split("####");
             var parametros={"codigo_detalle":codigo_detalle,"cod_solicitud":cod_solicitud,"cod_pagoproveedor":cod_pagoproveedor,"monto":monto,"saldo":saldo,"tipo_pago":tipo_pago,"proveedores_pago":proveedores_pago,"observaciones_pago":observaciones_pago,"banco":banco,"cheque":cheque[0],"numero_cheque":numero_cheque,"nombre_ben":nombre_ben};
             pago=1;
           }
         }
       } 
    }else{
      pago=1;
         var parametros={"codigo_detalle":codigo_detalle,"cod_solicitud":cod_solicitud,"cod_pagoproveedor":cod_pagoproveedor,"monto":monto,"saldo":saldo,"tipo_pago":tipo_pago,"proveedores_pago":proveedores_pago,"observaciones_pago":observaciones_pago};
    }

    if(parseFloat(monto)>parseFloat(saldo)){
      Swal.fire("Informativo!", "El Monto debe ser Menor al Saldo", "warning");
    }else{
     if(pago==1){
       $.ajax({
        type: "GET",
        dataType: 'html',
        url: "solicitudes/ajaxSavePagoDetalleSolicitudRecurso.php",
        data: parametros,
        beforeSend: function () {
        $("#texto_ajax_titulo").html("Realizando el Pago de "+redondeo(monto)+ " Bs."); 
          iniciarCargaAjax();
        },        
        success:  function (resp) {
           detectarCargaAjax();
           $("#texto_ajax_titulo").html("Procesando Datos");
           alerts.showSwal('success-message','index.php?opcion=listSolicitudPagosProveedores&codigo='+cod_solicitud);
        }
       });             
     }
      
    }//fin else
  }
}
function historialPagoSolicitudRecursos(){
  $("#modalHistorialPago").modal("show");
}

function nuevoPagoSolicitudRecursosDetalle(codigo,nombre,codProv,saldo){
  $("#codigo_detalle").val(codigo);
  $("#nombre_proveedor").val(nombre);
  $("#proveedores_pago").val(codProv);
  $("#saldo_pago").val(redondeo(saldo));
  $("#modalRegistrarPago").modal("show");
}
function mostrarDatosCheque(){
  var tipo =$("#tipo_pago").val();
  if(tipo==1){
     if(($("#div_cheques").hasClass("d-none"))){
       $("#div_cheques").removeClass("d-none");
    } 
  }else{
    if(!($("#div_cheques").hasClass("d-none"))){
      $("#div_cheques").addClass("d-none");
    }
  }
}
function cargarChequesPago(){
  var banco=$("#banco_pago").val();
   var parametros={"banco":banco};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "solicitudes/ajaxListChequesBanco.php",
        data: parametros,
        beforeSend: function () {
        $("#texto_ajax_titulo").html("Listando Cheques..."); 
          iniciarCargaAjax();
        },        
        success:  function (resp) {
           detectarCargaAjax();
           $("#texto_ajax_titulo").html("Procesando Datos");
           $("#div_chequesemitidos").html(resp);
           $('.selectpicker').selectpicker("refresh");
           $("#nombre_ben").val($("#nombre_proveedor").val());
        }
      }); 
}

function ponerNumeroChequePago(){
  var valor= $("#emitidos_pago").val().split("####");
  $("#numero_cheque").val(valor[1]);
}


function cargarDatosProveedorPagos(){
  var prov = $("#proveedor").val().split("####");
  var proveedor = prov[0];
  var parametros={"proveedor":proveedor};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "obligaciones_pago/ajaxListPagos.php",
        data: parametros,
        beforeSend: function () {
        $("#texto_ajax_titulo").html("Listando Pagos  de "+prov[1]); 
          iniciarCargaAjax();
        },        
        success:  function (resp) {
           detectarCargaAjax();
           $("#texto_ajax_titulo").html("Procesando Datos");
           $("#data_pagosproveedores").html(resp);
           $('.selectpicker').selectpicker("refresh");
           //$("#nombre_ben").val($("#nombre_proveedor").val());
        }
      });
}

function mostrarDatosChequeDetalle(fila){
  var tipo =$("#tipo_pago"+fila).val();
  if(tipo==1){
     if(($("#div_cheques"+fila).hasClass("d-none"))){
       $("#div_cheques"+fila).removeClass("d-none");
    } 
  }else{
    if(!($("#div_cheques"+fila).hasClass("d-none"))){
      $("#div_cheques"+fila).addClass("d-none"); 
    }
    if(!($("#div_chequesemitidos"+fila).hasClass("d-none"))){
      $("#div_chequesemitidos"+fila).addClass("d-none");
    }
    if(!($("#numero_cheque"+fila).is("[readonly]"))){
      $("#numero_cheque"+fila).attr("readonly",true);
      $("#numero_cheque"+fila).val("0");
      $("#beneficiario"+fila).attr("readonly",true);
    }
  }
}

function cargarChequesPagoDetalle(fila){
  var banco=$("#banco_pago"+fila).val();
   var parametros={"banco":banco,"fila":fila};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "obligaciones_pago/ajaxListChequesBanco.php",
        data: parametros,
        beforeSend: function () {
        $("#texto_ajax_titulo").html("Listando Cheques..."); 
          iniciarCargaAjax();
        },        
        success:  function (resp) {
           detectarCargaAjax();
           if(($("#div_chequesemitidos"+fila).hasClass("d-none"))){
             $("#div_chequesemitidos"+fila).removeClass("d-none");
           }
           $("#texto_ajax_titulo").html("Procesando Datos");
           $("#div_chequesemitidos"+fila).html(resp);
           $('.selectpicker').selectpicker("refresh");
        }
      }); 
}

function ponerNumeroChequePagoDetalle(fila){
  var valor= $("#emitidos_pago"+fila).val().split("####");
  $("#numero_cheque"+fila).val(valor[1]);
  $("#numero_cheque"+fila).attr("min",valor[1]);
  if(valor==""||valor==null){
    if(!($("#numero_cheque"+fila).is("[readonly]"))){
      $("#numero_cheque"+fila).attr("readonly",true);
      $("#beneficiario"+fila).attr("readonly",true);
      $("#numero_cheque"+fila).val("0");
    }
  }else{
    if(($("#numero_cheque"+fila).is("[readonly]"))){
      $("#numero_cheque"+fila).removeAttr("readonly");
      $("#beneficiario"+fila).removeAttr("readonly");  
    }
  }
}


var itemEstadosCuentas_cc=[];
function quitarEstadoCuenta_cajachica(){
  var fila=$("#estFila").val();
  itemEstadosCuentas_cc[fila-1]=[];
  verEstadosCuentas_cajachica(fila,0,0);
  $("#nestado"+fila).removeClass("estado");
}
function agregarEstadoCuenta_cajachica(){
  $("#mensaje_estadoscuenta").html("");
  var fila=$("#estFila").val();
  // var tipo=$("#tipo_estadocuentas"+fila).val();
  var tipo=2;
  if(tipo==1){
    // var cuenta=0;
    // var codComproDet=0;
    // var nfila={
    // cod_plancuenta:cuenta,
    // cod_plancuentaaux:$("#cuenta_auxiliar"+fila).val(),
    // cod_comprobantedetalle:codComproDet,
    // cod_proveedor:0,//$("#proveedores").val(),
    // monto:$("#monto_estadocuenta").val()
    // }
    // itemEstadosCuentas_cc[fila-1]=[];
    // itemEstadosCuentas_cc[fila-1].push(nfila);
    // $("#nestado"+fila).addClass("estado");
    // verEstadosCuentas_cajachica(fila,cuenta);
  }else{
    var resp = $("#cuentas_origen").val().split('####');
    var cuenta = resp[0];
    var detalle_resp=$('input:radio[name=cuentas_origen_detalle]:checked').val().split('####');
    //obtener dados del check
    var codComproDet=detalle_resp[0].trim();
    var cuenta_auxiliar=detalle_resp[1].trim();
    var cod_proveedorCompr=detalle_resp[2].trim();
    var saldo_comprob=detalle_resp[3].trim();
    var nombre_proveedor_com=detalle_resp[4].trim();
    var cod_estado_cuenta=detalle_resp[5].trim();
    // alert("saldo:"+saldo_comprob);

    if(codComproDet!=null){
      if(resp[1]=="AUX"){
        var nfila={
        cod_plancuenta:0,
        cod_plancuentaaux:cuenta,
        cod_comprobantedetalle:codComproDet,
        cod_proveedor:0,//$("#proveedores").val(),
        monto:$("#monto_estadocuenta").val(),
        nombre_proveedor:nombre_proveedor_com,
        cod_estado_cuenta:cod_estado_cuenta
        }
      }else{
        var nfila={
        cod_plancuenta:cuenta,
        cod_plancuentaaux:cuenta_auxiliar,
        cod_comprobantedetalle:codComproDet,
        cod_proveedor:0,//$("#proveedores").val(),
        monto:$("#monto_estadocuenta").val(),//monto de caja chica a descontar
        nombre_proveedor:nombre_proveedor_com,
        cod_estado_cuenta:cod_estado_cuenta
        }        
      }
    itemEstadosCuentas_cc[fila-1]=[];
    itemEstadosCuentas_cc[fila-1].push(nfila);
    $("#nestado"+fila).addClass("estado");
    document.getElementById('comprobante').value=cod_estado_cuenta;
    ajaxCajaCPersonalUO_cuentapasiva(codComproDet,cod_proveedorCompr);//ponemos oficina  en el formulario,luego area y provee

    verEstadosCuentas_cajachica(fila,cuenta,saldo_comprob);
    }else{
      $("#mensaje_estadoscuenta").html("<label class='text-danger'>Debe seleccionar un registro en la tabla</label>");
    }
  }
}
function verEstadosCuentas_cajachica(fila,cuenta,saldo_comprob){
  if($("#monto").val()=="" ||$("#monto").val()==0){
   $('#msgError').html("<p>El monto debe de ser llenado</p>");
   $("#modalAlert").modal("show");
  }else{
    itemEstadosCuentas_cc.push(fila);
    var cod_cuenta_form=$("#cuenta_auto_id").val();
    document.getElementById('cuenta'+fila).value=cod_cuenta_form;
    document.getElementById('cuenta_auxiliar'+fila).value=0;
    if(cuenta==0){
      if($("#cuenta_auxiliar"+fila).val()==0){
        var cod_cuenta=$("#cuenta"+fila).val();
        var auxi="NO";
      }else{
        var cod_cuenta=$("#cuenta_auxiliar"+fila).val();
        var auxi="SI";
      }      
    }else{
      if($("#cuenta_auxiliar"+fila).val()==0){
        var cod_cuenta=cuenta;
        var auxi="NO";
      }else{
        var cod_cuenta=cuenta;
        var auxi="SI";
      }
      var cod_cuenta=cuenta;
    }    
    var tipo=2;

    if(tipo==1){
      $("#monto_estadocuenta").val($("#monto").val());
      if(!($("#div_cuentasorigen").hasClass("d-none"))){
        $("#div_cuentasorigen").addClass("d-none");
        $("#div_cuentasorigendetalle").addClass("d-none"); 
      }      
    }else{
      $("#monto_estadocuenta").val($("#monto").val());
      if($("#div_cuentasorigen").hasClass("d-none")){
        $("#div_cuentasorigen").removeClass("d-none");
        $("#div_cuentasorigendetalle").removeClass("d-none");
      }

      if($("#cuentas_origen").val()==""||$("#cuentas_origen").val()==null){
        var cod_cuenta="";      
      }else{
        var resp = $("#cuentas_origen").val().split('###');
        var cod_cuenta = resp[0];
        if(resp[1]=="AUX"){
          var auxi="SI";
        }else{
          var auxi="NO";
        }
      } 
    }
    //ajax estado de cuentas
    var parametros={"cod_cuenta":cod_cuenta,"tipo":tipo,"mes":12,"auxi":auxi};
    $.ajax({
        type: "GET",
        dataType: 'html',
        url: "estados_cuenta/ajaxMostrarEstadosCuenta_cajachica.php",
        data: parametros,
        success:  function (resp) {          
          if(cod_cuenta==0 || cod_cuenta==""){
            $("#cuentas_origen").val(cod_cuenta_form+"###NNN");
            $('.selectpicker').selectpicker("refresh");
            verEstadosCuentasCred_cc();  
          }
          var respuesta=resp.split('@');          
          $("#div_estadocuentas").html(respuesta[0]);
          if(tipo==1){
            // var rsaldo=listarEstadosCuentas_cc(fila,respuesta[1]);
            var rsaldo=listarEstadosCuentas_cc(fila,saldo_comprob);
            listarEstadosCuentasDebito_cc(fila,rsaldo);
          }else{
            // var rsaldo=listarEstadosCuentasCredito_cc(fila,respuesta[1]);
            var rsaldo=listarEstadosCuentasCredito_cc(fila,saldo_comprob);
            listarEstadosCuentas_cc(fila,rsaldo);
          }           
        }
    });
    $("#estFila").val(fila);
    $("#tituloCuentaModal").html($("#cuenta_auto").val());
    $("#modalEstadosCuentas").modal("show");     
  }  
}

function verEstadosCuentasCred_cc(){
  var fila = $("#estFila").val();
  var resp = $("#cuentas_origen").val().split('###');
  var cuenta = resp[0];
  verEstadosCuentas_cajachica(fila,cuenta,0);
}
function listarEstadosCuentas_cc(id,saldo){
  var table = $('#tabla_estadocuenta');
  // alert(itemEstadosCuentas_cc[id-1].length);
   for (var i = 0; i < itemEstadosCuentas_cc[id-1].length; i++) {
     var row = $('<tr>').addClass('bg-white');
     row.append($('<td>').addClass('text-left').text(""));
     row.append($('<td>').addClass('text-left text-danger').text("Sin Guardar"));
     row.append($('<td>').addClass('text-left text-danger').text(itemEstadosCuentas_cc[id-1][i].nombre_proveedor));//nombre proveedor
     // var tipo=$("#tipo_estadocuentas"+id).val();
     var tipo=2;
      if(tipo==1){
        // row.append($('<td>').addClass('text-left').text($("#glosa_detalle"+id).val()));
        // var nsaldo=parseFloat(saldo)+parseFloat(itemEstadosCuentas_cc[id-1][i].monto);
        // row.append($('<td>').addClass('text-right').text(numberFormat(itemEstadosCuentas_cc[id-1][i].monto,2)));
        // row.append($('<td>').addClass('text-right').text(""));   
      }else{
        var titulo_glosa="";
        if(itemEstadosCuentas_cc[id-1][i].cod_comprobantedetalle!=0){
          // titulo_glosa=obtieneDatosFilaEstadosCuenta(itemEstadosCuentas_cc[id-1][i].cod_comprobantedetalle);
          titulo_glosa="CAjA CHICA";
        }

        row.append($('<td>').addClass('text-left').html("<small class='text-danger'>"+titulo_glosa+"</small>"));
        var nsaldo=parseFloat(saldo)-parseFloat(itemEstadosCuentas_cc[id-1][i].monto);
        row.append($('<td>').addClass('text-right text-danger').text(numberFormat(itemEstadosCuentas_cc[id-1][i].monto,2)));  
        row.append($('<td>').addClass('text-right').text("")); 
        
      }
      row.append($('<td>').addClass('text-right font-weight-bold text-danger').text(numberFormat(nsaldo,2)));
     table.append(row);
     return nsaldo;
   }
}
function listarEstadosCuentasDebito_cc(id,saldo){
  var cuentaOrigen =$("#cuenta"+id).val();
  var rsaldo = parseFloat(saldo);
  for (var i = 0; i < 1; i++) {
    for (var j = 0; j < itemEstadosCuentas_cc[i].length; j++) {
       var cuenta = itemEstadosCuentas_cc[i][j].cod_plancuenta;
       if(cuentaOrigen==cuenta){
        rsaldo=rsaldo-parseFloat(itemEstadosCuentas_cc[i][j].monto);
        listarEstadosCuentas_cc(i+1,saldo);
       }
     }                  
  }
  return rsaldo;
}
function listarEstadosCuentasCredito_cc(id,saldo){
   var rsaldo = parseFloat(saldo);
    for (var j = 0; j < itemEstadosCuentas_cc[id-1].length; j++) {
      var cuentaOrigen =itemEstadosCuentas_cc[id-1][j].cod_plancuenta;
       for (var i = 0; i < 1; i++) {
         if($("#cuenta"+(i+1)).val()==cuentaOrigen){
            rsaldo=rsaldo+parseFloat(itemEstadosCuentas_cc[i][0].monto); // 0 porque utilizamos solo un item
            listarEstadosCuentas_cc(i+1,saldo);
         }
       }
     }                  
    return rsaldo;
}


function ajaxCajaCPersonalUO_cuentapasiva(codigo_comprobante,cod_proveedor){
  var contenedor;
  // var codigo_personal=combo.value;
  contenedor = document.getElementById('div_contenedor_uo');
  ajax=nuevoAjax();
  ajax.open('GET', 'caja_chica/personalUOAjax_cuentaPasiva.php?codigo_comprobante='+codigo_comprobante,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);
      ajaxCajaCPersonalArea_cuentapasiva(codigo_comprobante,cod_proveedor);//ponemos area en el fromulario
    
    }
  }
  ajax.send(null)  
}
function ajaxCajaCPersonalArea_cuentapasiva(codigo_comprobante,cod_proveedor){
  var contenedor;
  // var codigo_personal=combo.value;
  contenedor = document.getElementById('div_contenedor_area');
  ajax=nuevoAjax();
  ajax.open('GET', 'caja_chica/personalAreaAjax_cuentaPasiva.php?codigo_comprobante='+codigo_comprobante,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);    
      ajaxCajaCProveedor_cuentapasiva(cod_proveedor);//ponemos proveedor en el fromulario  
    }
  }
  ajax.send(null)  
}


function ajaxCajaCProveedor_cuentapasiva(codigo_proveedor){
  var contenedor_p;
  // alert(codigo_proveedor);

  contenedor_p = document.getElementById('div_contenedor_proveedor');
  ajax=nuevoAjax();
  ajax.open('GET', 'caja_chica/proveedorAjax_cuentaPasiva.php?codigo_proveedor='+codigo_proveedor,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor_p.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);  
      $("#modalEstadosCuentas").modal("hide");//cerramos modal  
    }
  }
  ajax.send(null)  
}

function ajaxTipoProveedorCliente(tipo){
  var contenedor_p;
  var tipoProveedorCliente=tipo.value;
  contenedor_p = document.getElementById('divProveedorCliente');
  ajax=nuevoAjax();
  ajax.open('GET', 'cuentas_auxiliares/ajaxProveedorCliente.php?tipo='+tipoProveedorCliente,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor_p.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);    
    }
  }
  ajax.send(null)  
}

function agregarAtributoAjax(){
  //listarAtributo();
  $("#modal_fila").val("-1");
  $("#modal_atributo").modal("show");
  if(itemAtributos.length>0){
    for (var i = (itemAtributos.length-1); i < itemAtributos.length; i++) {
      if($("#modal_nombre").length){
         $("#modal_nombre").val(itemAtributos[i].nombre);
      }
      if($("#modal_marca").length){
         $("#modal_marca").val(itemAtributos[i].marca);
      }
     if($("#modal_norma").length){
        $("#modal_norma").val(itemAtributos[i].norma);
     }
     if($("#modal_sello").length){
        $("#modal_sello").val(itemAtributos[i].sello);
     }
     if($("#modal_direccion").length){
        $("#modal_direccion").val(itemAtributos[i].direccion);
     } 
    }
  }else{
  if($("#modal_nombre").length){
     $("#modal_nombre").val("");
  }
  if($("#modal_marca").length){
     $("#modal_marca").val("");
  }
  if($("#modal_norma").length){
     $("#modal_norma").val("");
  }
  if($("#modal_sello").length){
     $("#modal_sello").val("");
  }
  if($("#modal_direccion").length){
     $("#modal_direccion").val("");
  }
    
  }
  $("#normas").val("");
  $("#modal_norma").val("");

  if($("#modalEditPlantilla").length){
    $("#modalEditPlantilla").modal("hide");
    for (var i = 0; i <= parseInt($("#anio_servicio").val()); i++) {
     $("#modal_dias_sitio"+i).val(""); 
    };
    //agregar campos a los inputs
  }

   if(($("#productos_div").hasClass("d-none"))){
     //solo sitio
    $("#lbl_nombre_atributo").text("Nombre");
   }else{
    $("#lbl_nombre_atributo").text("Producto");
   }
   $("#pais_empresa").val("26####BOLIVIA"); //para el pais de BOLIVIA
    if($("#modalEditPlantilla").length){
      seleccionarDepartamentoServicioSitioModal(1);  
    }else{
       seleccionarDepartamentoServicioSitio(1);  
    }

}

function listarAtributo(){
  var sumaDias=[];
  var div=$('<div>').addClass('');
  var table = $('<table>').addClass('table');
  table.addClass("table-bordered");
  table.addClass("table-sm table-striped");
  table.addClass("small");
  var titulos = $('<tr>').addClass('bg-info text-white');
     titulos.append($('<th>').addClass('').text('#'));
     titulos.append($('<th>').addClass('').text('NOMBRE'));
     titulos.append('<td width="20%">DIRECCION</td>'); 
     if(!($("#productos_div").hasClass("d-none"))){
      titulos.append($('<th>').addClass('').text('MARCA'));
      titulos.append($('<th>').addClass('').text('NORMA'));
      titulos.append($('<th>').addClass('').text('SELLO'));
      titulos.append('<td width="7%">PAIS</td>');
      titulos.append('<td width="7%">DEPTO</td>');
      titulos.append('<td width="7%">CIUDAD</td>');
     }else{
      titulos.append('<td width="7%">PAIS</td>');
      titulos.append('<td width="7%">DEPTO</td>');
      titulos.append('<td width="7%">CIUDAD</td>');
      if($("#modalEditPlantilla").length>0){
        if($("#codigo_area").val()!=39){
        for (var k = 0; k <= parseInt($("#anio_simulacion").val()); k++) {
          sumaDias[k]=0;
          var tituloTD="Seg "+(k-1);
          if(k==0||k==1){
            if(k==1){
              tituloTD="Et "+(k+1)+" /R";
            }else{
              tituloTD="Et "+(k+1);
            } 
          }
          titulos.append('<td width="6%" class="bg-principal">'+tituloTD+'</td>');
          titulos.append('<td width="8%" class="bg-plomo text-dark">EA</td>');    
        };
       }
      }
     }
      
     titulos.append('<td class="text-right" width="18%">OPCION</td>');
     table.append(titulos);
   for (var i = 0; i < itemAtributos.length; i++) {
     var row = $('<tr>').addClass('');
     row.append($('<td>').addClass('').text(i+1));
     row.append($('<td>').addClass('').text(itemAtributos[i].nombre));
     row.append($('<td>').addClass('').text(itemAtributos[i].direccion)); 
     if(!($("#productos_div").hasClass("d-none"))){
      row.append($('<td>').addClass('').text(itemAtributos[i].marca));
      row.append($('<td>').addClass('').text(itemAtributos[i].norma));
      row.append($('<td>').addClass('').text(itemAtributos[i].sello));
      row.append($('<td>').addClass('').text(itemAtributos[i].nom_pais));
      row.append($('<td>').addClass('').text(itemAtributos[i].nom_estado));
      row.append($('<td>').addClass('').text(itemAtributos[i].nom_ciudad));
     }else{
      row.append($('<td>').addClass('').text(itemAtributos[i].nom_pais));
      row.append($('<td>').addClass('').text(itemAtributos[i].nom_estado));
      row.append($('<td>').addClass('').text(itemAtributos[i].nom_ciudad));
      if($("#modalEditPlantilla").length>0){
       if($("#codigo_area").val()!=39){
        for (var k = 0; k <=parseInt($("#anio_simulacion").val()); k++) {
          for (var j= 0; j< itemAtributosDias.length; j++) {
           if(itemAtributosDias[j].codigo_atributo==itemAtributos[i].codigo&&itemAtributosDias[j].anio==k){
            sumaDias[k]+=parseFloat(itemAtributosDias[j].dias);
            row.append('<td><input id="sitio_dias'+j+'" onchange="cambiarMontoDiasSitio('+j+')" onkeypress="cambiarMontoDiasSitio('+j+')" onkeyup="cambiarMontoDiasSitio('+j+')" class="form-control" type="number" value="'+itemAtributosDias[j].dias+'"></td>');  
            row.append('<td><select title="-" data-actions-box="true" class="form-control selectpicker form-control-sm" multiple data-style="fondo-boton fondo-boton-active" name="auditores'+j+'[]" id="auditores'+j+'">'+
              $("#auditores"+k+"EEEE"+itemAtributosDias[j].codigo_atributo).html()+
              '</select></td>');  
            //alerta 2 0 3
           } 
         };     
        };
       }    
      } //fin #modalEditPlantilla
     }
       $('.selectpicker').selectpicker("refresh");
       if($("#sinEdicionModal").length>0){
         row.append($('<td>').addClass('text-right small').html(''));
       }else{
         row.append($('<td>').addClass('text-right small').html('<div class="btn-group"><button title="Editar" class="btn btn-sm btn-fab btn-success" onclick="editarAtributo('+i+');"><i class="material-icons" >edit</i></button><button class="btn btn-sm btn-fab btn-danger" title="Eliminar" onclick="removeAtributo('+i+');"><i class="material-icons">delete</i></button></div>'));    
       }
     
     table.append(row);
   }
   if($("#modalEditPlantilla").length>0){
    if($("#codigo_area").val()!=39){
      var row = $('<tr>').addClass('');
      row.append($('<td>').addClass('font-weight-bold text-center').attr('colspan',6).text('TOTALES'));
      for (var k = 0; k <=parseInt($("#anio_simulacion").val()); k++) {
        row.append($('<td>').attr('id','dias_modal'+k).addClass('font-weight-bold').text(sumaDias[k]));
        row.append($('<td>').addClass('font-weight-bold').text(""));
      }  
      row.append($('<td>').addClass('font-weight-bold').text(""));
      table.append(row);
    }      
   }
   div.append(table);
     if($("#productos_div").hasClass("d-none")){
      $('#divResultadoListaAtributos').html(div);
      //$('#divResultadoListaAtributos').bootstrapMaterialDesign(); 
     }else{
      $('#divResultadoListaAtributosProd').html(div);
      //$('#divResultadoListaAtributosProd').bootstrapMaterialDesign(); 
     } 

}

function cambiarMontoDiasSitio(j){
  var valor = $("#sitio_dias"+j).val();
  itemAtributosDias[j].dias=valor;

  var sumaDias=[];
  for (var k = 0; k <=parseInt($("#anio_simulacion").val()); k++) {
    sumaDias[k]=0;
  }
 for (var i = 0; i < itemAtributos.length; i++) {
        for (var k = 0; k <=parseInt($("#anio_simulacion").val()); k++) {
          for (var j= 0; j< itemAtributosDias.length; j++) {
           if(itemAtributosDias[j].codigo_atributo==itemAtributos[i].codigo&&itemAtributosDias[j].anio==k){
            sumaDias[k]+=parseFloat(itemAtributosDias[j].dias);
           } 
         };     
      };
   }
 for (var k = 0; k <=parseInt($("#anio_simulacion").val()); k++) {
    $("#dias_modal"+k).text(sumaDias[k]);
  }    
}
function guardarAtributoItem(){
  if($('#modal_nombre').val()==""){
   Swal.fire("Informativo!", "Debe ingresar el Nombre del sitio o producto", "warning");
  }else{
  if(($("#productos_div").hasClass("d-none"))){
    var norma="";
    var sello="";
    var marca="";
      
  }else{
    var normasMultiple=$("#normas").val();
    var norma_cod=normasMultiple.join(",");
    var normasArray=[]; var i=0;
    $("#normas option:selected").each(function() {     
       normasArray[i]= $(this).text();      
       i++;
     });
    var norma=normasArray.join(",");

    if($("#modal_norma").val()!=""){
     norma=norma+","+$("#modal_norma").val();
    }

    var norma_otro=$("#modal_norma").val();
    var sello=$("#modal_sello").val();
    var marca=$("#modal_marca").val();
  }
  if($("#pais_empresa").val()!=null){
    var pais=$("#pais_empresa").val().split("####")[0];
    var nom_pais=$("#pais_empresa").val().split("####")[1];
    }else{
      var pais="";
      var nom_pais="SIN PAIS";
    }
    if(!($("#departamento_empresa").val()==null||$("#departamento_empresa").val()=="")){
    var estado=$("#departamento_empresa").val().split("####")[0];
    var nom_estado=$("#departamento_empresa").val().split("####")[1];
    }else{
      var estado="";
      var nom_estado="SIN DEPTO";
    }
    if(!($("#ciudad_empresa").val()==null||$("#ciudad_empresa").val()=="")){
    var ciudad=$("#ciudad_empresa").val().split("####")[0];
    var nom_ciudad=$("#ciudad_empresa").val().split("####")[1];
    }else{
      var ciudad="";
      var nom_ciudad="SIN CIUDAD";
    }
  var fila=$("#modal_fila").val();
  if(fila<0){
    var codigoNuevo=itemAtributos.length;
    var atributo={
    codigo:codigoNuevo,  
    nombre: $('#modal_nombre').val(),
    direccion: $('#modal_direccion').val(),
    norma:norma,
    norma_cod:norma_cod,
    norma_otro:norma_otro,
    marca:marca,
    sello:sello,
    pais:pais,
    estado:estado,
    ciudad:ciudad,
    nom_pais:nom_pais,
    nom_estado:nom_estado,
    nom_ciudad:nom_ciudad
    }
  itemAtributos.push(atributo);
   if($("#modalEditPlantilla").length){
    //agregar Nuevo dias sitios
    for (var i = 0; i <= parseInt($("#anio_servicio").val()); i++) {
         var atributoDias={
         codigo_atributo: codigoNuevo,  
         dias: $("#modal_dias_sitio"+i).val(),
         anio: i
         }
       itemAtributosDias.push(atributoDias);
     };   
   }
  }else{
    itemAtributos[fila].nombre=$('#modal_nombre').val();
    itemAtributos[fila].direccion=$('#modal_direccion').val();
    if(!($("#productos_div").hasClass("d-none"))){
       var normasMultiple=$("#normas").val();
    var norma_cod=normasMultiple.join(",");
    var normasArray=[]; var i=0;
    $("#normas option:selected").each(function() {     
       normasArray[i]= $(this).text();      
       i++;
     });
    var norma=normasArray.join(",");
    }
   
    if($("#modal_norma").val()!=""){
     itemAtributos[fila].norma=norma+","+$("#modal_norma").val();  
    }else{
      itemAtributos[fila].norma=norma;
    }    
    itemAtributos[fila].norma_otro=$("#modal_norma").val();
    itemAtributos[fila].norma_cod=norma_cod;
    itemAtributos[fila].marca=$('#modal_marca').val();
    itemAtributos[fila].sello=$('#modal_sello').val();
    itemAtributos[fila].pais=pais;
    itemAtributos[fila].estado=estado;
    itemAtributos[fila].ciudad=ciudad;
    itemAtributos[fila].nom_pais=nom_pais;
    itemAtributos[fila].nom_estado=nom_estado;
    itemAtributos[fila].nom_ciudad=nom_ciudad;
    /*if(($("#productos_div").hasClass("d-none"))){
      
    }*/   
    if($("#modalEditPlantilla").length){
    //editar dias sitios
      for (var i = 0; i <= parseInt($("#anio_servicio").val()); i++) {
        for (var j = 0; j < itemAtributosDias.length; j++) {
           if(itemAtributosDias[j].codigo_atributo==itemAtributos[fila].codigo&&itemAtributosDias[j].anio==i){
            itemAtributosDias[j].dias=$("#modal_dias_sitio"+i).val()
           } 
        };
     };
   }
  }
  $("#modal_nombre").val("");
  $("#modal_direccion").val("");
  $("#modal_norma").val("");
  $("#modal_marca").val("");
  $("#modal_sello").val("");
//
  if($("#modalEditPlantilla").length>0){
    limpiarModalCache("modal_atributo");
    editarDatosPlantilla();
   }else{
    listarAtributo();
    $("#modal_atributo").modal("hide");
   }
  }
  
}

function limpiarModalCache(id_modal){
  $("#"+id_modal).modal("hide");
  $('body').removeClass('modal-open');//eliminamos la clase del body para poder hacer scroll
  $('.modal-backdrop').remove();//eliminamos el backdrop del modal
}
function removeAtributo(fila){
  if($("#modalEditPlantilla").length){
    for (var i = 0; i < itemAtributosDias.length; i++) {
      if(itemAtributosDias[i].codigo_atributo==itemAtributos[fila].codigo){
        itemAtributosDias.splice(i, 1);
      }
    };
  }
  itemAtributos.splice(fila, 1);
  listarAtributo();
 }

function editarAtributo(fila){
  $("#modal_fila").val(fila);
  $('#modal_nombre').val(itemAtributos[fila].nombre);
  if($("#modal_marca").length){
    $('#modal_marca').val(itemAtributos[fila].marca);
    $('#modal_norma').val(itemAtributos[fila].norma_otro);
    $('#modal_sello').val(itemAtributos[fila].sello);
    $("#modal_norma").tagsinput('removeAll');
    $("#modal_norma").tagsinput('add', itemAtributos[fila].norma_otro);
   if(!($("#div_norma").hasClass("d-none"))){
    var normasMultiple=itemAtributos[fila].norma_cod.split(",");
    $("#normas").val(normasMultiple);
    var norma_cod=normasMultiple.join(",");
   }   
  }
  if(($("#div_marca").hasClass("d-none"))){
    $("#lbl_nombre_atributo").text("Nombre");
      
  }else{
    $("#lbl_nombre_atributo").text("Nombre");
  }
  $('#pais_empresa').val(itemAtributos[fila].pais+"####"+itemAtributos[fila].nom_pais);
    if($("#modalEditPlantilla").length){
      if(itemAtributos[fila].nom_pais!="SIN REGISTRO"){
        seleccionarDepartamentoServicioSitioModal(0,itemAtributos[fila].estado+"####"+itemAtributos[fila].nom_estado,itemAtributos[fila].ciudad+"####"+itemAtributos[fila].nom_ciudad);
      }
       
    }else{
      if(itemAtributos[fila].nom_pais!="SIN REGISTRO"){
        seleccionarDepartamentoServicioSitio(0,itemAtributos[fila].estado+"####"+itemAtributos[fila].nom_estado,itemAtributos[fila].ciudad+"####"+itemAtributos[fila].nom_ciudad);  
      }
       
    }   
    $('.selectpicker').selectpicker("refresh");
  $('#modal_direccion').val(itemAtributos[fila].direccion); 
  $("#modal_atributo").modal("show");
  if($("#modalEditPlantilla").length){
   
    for (var i = 0; i <= parseInt($("#anio_servicio").val()); i++) {
      for (var j= 0; j< itemAtributosDias.length; j++) {
        if(itemAtributosDias[j].codigo_atributo==itemAtributos[fila].codigo&&itemAtributosDias[j].anio==i){
         $("#modal_dias_sitio"+i).val(itemAtributosDias[j].dias);
        } 
      };  
    };
    $("#modalEditPlantilla").modal("hide");
    
  }
}

function verCuentasAuxiliaresSelect(){
  var cuenta= $("#cuentas_origen").val().split("###");
  console.log("CODIGO CUENTA ORIGEN: "+cuenta[0]);
  if(cuenta[1]=="NNN"){
    if($("#div_cuentasorigenaux").hasClass("d-none")){
      $("#div_cuentasorigenaux").removeClass("d-none");
    }
    var fila = $("#estFila").val();
    var tipo_proveedorcliente=$("#tipo_proveedorcliente"+fila).val();
    var id="";
     $("#cuentas_auxiliaresorigen option").each(function(){
            //console.log("entro select auxiliar:");
            console.log("value: "+$(this).val());
           if($(this).val()!="all"){
              var codigoSelect=$(this).val().split("###");
              console.log("vector 0: "+codigoSelect[0]);
              console.log("vector 1: "+codigoSelect[1]);
              if ((codigoSelect[1]==cuenta[0])){
               $(this).show();   
              }else{
                $(this).hide(); 
              }   
           }
        }); 
      $('.selectpicker').selectpicker("refresh");   
  } 
}
function mostrarSelectProveedoresClientes(){
  verEstadosCuentasCred();
}

function filtrarCuentaComprobanteDetalle(){
  var codigos=[];
  var indice=0;
  var items = document.getElementsByName('lista_check');
    for (var i = 0; i < items.length; i++) {
        if (items[i].type == 'checkbox')
          if(items[i].checked==true){
            codigos[indice]=items[i].value;
            indice++;
          }
    }
  //var cod_cuenta=$("#cuenta_decomprobante").val();
  var cod_comprobante=$("#codigo_comprobante").val();
  window.location.href="edit_prueba.php?codigo="+cod_comprobante+"&cuentas="+JSON.stringify(codigos);
}



function seleccionarTodosChecks(tagName) {
        var items = document.getElementsByName(tagName);
        for (var i = 0; i < items.length; i++) {
            if (items[i].type == 'checkbox')
                items[i].checked = true;
        }
    }

function noSeleccionarTodosChecks(tagName) {
        var items = document.getElementsByName(tagName);
        for (var i = 0; i < items.length; i++) {
            if (items[i].type == 'checkbox')
                items[i].checked = false;
        }
    }   



//enviar correo
//contratos de personal
function agregaformEnviarCorreo(datos){ 
  var d=datos.split('/');  
  document.getElementById("codigo_facturacion").value=d[0];
  document.getElementById("cod_solicitudfacturacion").value=d[1];
  document.getElementById("nro_factura").value=d[2];
  //document.getElementById("correo_destino").value=d[3];
  $("#correo_destino").tagsinput('removeAll');
  $("#correo_destino").tagsinput('add', d[3]);
  document.getElementById("razon_social").value=d[4];
}
// function agregaformEnviarCorreo_solfac(datos){ 

//   var d=datos.split('/');  
//   document.getElementById("codigo_facturacion_sf").value=d[0];
//   document.getElementById("cod_solicitudfacturacion_sf").value=d[1];
//   document.getElementById("nro_factura_sf").value=d[2];
//   document.getElementById("correo_destino_sf").value=d[3];
//   document.getElementById("razon_social_sf").value=d[4];
// }

function EnviarCorreoAjax(codigo_facturacion,nro_factura,cod_solicitudfacturacion,correo_destino,asunto,mensaje){
  iniciarCargaAjax();
  $.ajax({
    type:"POST",
    data:"codigo_facturacion="+codigo_facturacion+"&nro_factura="+nro_factura+"&cod_solicitudfacturacion="+cod_solicitudfacturacion+"&correo_destino="+correo_destino+"&asunto="+asunto+"&mensaje="+mensaje,
    url:"simulaciones_servicios/enviarCorreo.php",
    success:function(r){
      var resp = r.split('$$$');
      var success = resp[0];
      var correos = resp[1];
      detectarCargaAjax();
      if(success==1){
        alerts.showSwal('success-message','index.php?opcion=listFacturasGeneradas');
      }
      if(success==2){
        Swal.fire("ERROR! :(", "Ocurrio un error de envío a los correos: <br> "+correos, "warning");
        // alerts.showSwal('error-messageEnviarCorreo','index.php?opcion=listFacturasGeneradas');
      }
      if(success==3){        
        alerts.showSwal('error-messageEnviarCorreoAdjunto','index.php?opcion=listFacturasGeneradas');
      }
      if(success==0){
        alerts.showSwal('error-messageCamposVacios','index.php?opcion=listFacturasGeneradas');
      }
    }
  });
}
// function EnviarCorreoAjaxSolFac(codigo_facturacion,nro_factura,cod_solicitudfacturacion,correo_destino,asunto,mensaje){
//   iniciarCargaAjax();
//   $.ajax({
//     type:"POST",
//     data:"codigo_facturacion="+codigo_facturacion+"&nro_factura="+nro_factura+"&cod_solicitudfacturacion="+cod_solicitudfacturacion+"&correo_destino="+correo_destino+"&asunto="+asunto+"&mensaje="+mensaje,
//     url:"simulaciones_servicios/enviarCorreo.php",
//     success:function(r){
//       var resp = r.split('$$$');
//       var success = resp[0];
//       var correos = resp[1];
//       detectarCargaAjax();
//       if(success==1){
//         alerts.showSwal('success-message','index.php?opcion=listFacturasServicios');
//       }
//       if(success==2){
//         Swal.fire("ERROR! :(", "Ocurrio un error de envío a los correos: <br> "+correos, "warning");
//         // alerts.showSwal('error-messageEnviarCorreo','index.php?opcion=listFacturasServicios');
//       }
//       if(success==3){        
//         alerts.showSwal('error-messageEnviarCorreoAdjunto','index.php?opcion=listFacturasServicios');
//       }
//       if(success==0){
//         alerts.showSwal('error-messageCamposVacios','index.php?opcion=listFacturasServicios');
//       }
//     }
//   });
// }
//entidades 
var unidades_tabla=[]; 
var unidades_tabla_general=[];
var numFilasUE=0;
function sendChekedUnidadesEntidad(id,nombres){
  var check=document.getElementById("unidades"+id);
    check.onchange = function() {
     if(this.checked) {
      unidades_tabla.push({codigo:id,nombre:nombres});
      numFilasUE++;
      
     }else{
      for (var i = 0; i < unidades_tabla.length; i++) {
        if(unidades_tabla[i].codigo==id){
            unidades_tabla.splice(i, 1);
            break;
        }      
      };
      
      numFilasUE--;
     }
     $("#boton_registradasA").html("Unidades Registradas <span class='badge bg-white text-warning'>"+numFilasUE+"</span>");
   }
} 

function filaTablaAGeneralEntidadOrganizacional(tabla,index){
  var html="";
  for (var i = 0; i < unidades_tabla_general[index-1].length; i++) {
    //alert(unidades_tabla_general[index-1][i].nombre);
    html+="<tr><td>"+(i+1)+"</td><td>"+unidades_tabla_general[index-1][i].nombreU+"</td></tr>";
  }
  tabla.html(html);
  $("#modalUnidadesEntidad").modal("show");  
}
function filaTablaUnidadEntidad(tabla){
  var html="";
  for (var i = 0; i < unidades_tabla.length; i++) {
    html+="<tr><td>"+(i+1)+"</td><td>"+unidades_tabla[i].nombre+"</td></tr>";

    // alert(unidades_tabla[i].nombre);
  };
  tabla.html(html);
  $("#modalUnidadesEntidad").modal("show");
}

//reportes
function ajax_entidad_Oficina(){
  var contenedor;
  var arrayEntidad = $("#entidad").val();
  contenedor = document.getElementById('div_contenedor_oficina1');
  ajax=nuevoAjax();
  ajax.open('GET', 'reportes/entidadesOFAjax.php?codigo_entidad='+arrayEntidad,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);
      ajaxEntidadOficina2(arrayEntidad);
    }
  }
  ajax.send(null)  
}

function ajax_tipo_filtro_reporte_prove_cliente(){
  var contenedor;
  var array_tipo_cp = $("#tipo_cp").val();
  contenedor = document.getElementById('div_contenedor_cuenta');
  ajax=nuevoAjax();
  ajax.open('GET', 'reportes/ajax_tipo_cliente_provee_cuenta.php?codigo='+array_tipo_cp,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);
    }
  }
  ajax.send(null)  
}
function ajax_clientes_proveedores(){
  var contenedor;
  var array_cuentas = $("#cuenta").val();
  contenedor = document.getElementById('div_contenedorProv_cli');
  ajax=nuevoAjax();
  ajax.open('GET', 'reportes/ajax_clientes_proveedores.php?codigo='+array_cuentas,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);      
    }
  }
  ajax.send(null)  
}


function ajaxEntidadOficina2(arrayEntidad){
  var contenedor; 
  contenedor = document.getElementById('div_contenedor_oficina_costo');
  ajax=nuevoAjax();
  ajax.open('GET', 'reportes/entidadesOFAjax2.php?codigo_entidad='+arrayEntidad,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);
    }
  }
  ajax.send(null)
  
}
function AjaxGestionFechaDesde(combo){
  var contenedor;
  var cod_gestion=combo.value;
  contenedor= document.getElementById('div_contenedor_fechaI');
  ajax=nuevoAjax();
  ajax.open('GET', 'reportes/GestionDesdeAjax.php?cod_gestion='+cod_gestion,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      AjaxGestionFechaHasta(cod_gestion);
    }
  }
  ajax.send(null)  

}
function AjaxGestionFechaDesdeMes(combo){
  var contenedor;
  var cod_gestion=combo.value;
  contenedor= document.getElementById('div_contenedor_fechaI');
  ajax=nuevoAjax();
  ajax.open('GET', 'reportes/GestionDesdeAjaxMes.php?cod_gestion='+cod_gestion,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      AjaxGestionFechaHastaMes(cod_gestion);
    }
  }
  ajax.send(null)  

}
function AjaxGestionFechaHastaMes(cod_gestion){
  // var cod_uo=$("#cod_unidadorganizacional").val();  
  // alert(cod_uo);

  var contenedor; 
  contenedor = document.getElementById('div_contenedor_fechaH');
  ajax=nuevoAjax();
  ajax.open('GET', 'reportes/GestionhastaAjaxMes.php?cod_gestion='+cod_gestion,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;      
    }
  }
  ajax.send(null)
  
}
function AjaxGestionFechaHasta(cod_gestion){
  // var cod_uo=$("#cod_unidadorganizacional").val();  
  // alert(cod_uo);

  var contenedor; 
  contenedor = document.getElementById('div_contenedor_fechaH');
  ajax=nuevoAjax();
  ajax.open('GET', 'reportes/GestionhastaAjax.php?cod_gestion='+cod_gestion,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;      
    }
  }
  ajax.send(null)
  
}
function AjaxGestionFechaDesdeBG(combo){
  var contenedor;
  var cod_gestion=combo.value;
  contenedor= document.getElementById('div_contenedor_fechaH');
  ajax=nuevoAjax();
  ajax.open('GET', 'reportes/GestionHastaAjax_Balance.php?cod_gestion='+cod_gestion,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;      
    }
  }
  ajax.send(null)  
}
function ajaxGestionFechaDesdeER(combo){
  var contenedor;
  var cod_gestion=combo.value;
  contenedor= document.getElementById('div_contenedor_fechaD');
  ajax=nuevoAjax();
  ajax.open('GET', 'reportes/GestionDesdeAjax.php?cod_gestion='+cod_gestion,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;      
      ajaxGestionFechaHastaER(combo);
    }
  }
  ajax.send(null)  
}

function ajaxGestionFechaHastaER(combo){
  var contenedor;
  var cod_gestion=combo.value;
  contenedor= document.getElementById('div_contenedor_fechaH');
  ajax=nuevoAjax();
  ajax.open('GET', 'reportes/GestionHastaAjax.php?cod_gestion='+cod_gestion,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;      
    }
  }
  ajax.send(null)  
}


function cargarDatosRegistroProveedorActivoFijo(cod_activo){
  var parametros={"cod":"none"};
  // $('#cod_tcc').val(cod_tcc);
  // $('#cod_cc').val(cod_cc);
  // $('#cod_dcc').val(cod_dcc);
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "activosFijos/ajaxListarDatosRegistroProveedor.php?cod_activo="+cod_activo,
        data: parametros,
        beforeSend: function () {
        $("#texto_ajax_titulo").html("Obteniendo datos del servicio..."); 
          iniciarCargaAjax();
        },
        success:  function (resp) {
           detectarCargaAjax();
           $("#datosProveedorNuevo").html(resp);
           $("#texto_ajax_titulo").html("Procesando Datos"); 
           $("#pais_empresa").val("26"); //para el pais de BOLIVIA
           seleccionarDepartamentoServicioCajaChica();
           $('.selectpicker').selectpicker("refresh");
           $("#modalAgregarProveedor").modal("show");
           
        }
    });
}
function guardarDatosProveedorActivosFijos(){
  var nombre =$("#nombre_empresa").val();
  var nombre_p =$("#nombre_persona").val();
  var paterno_p =$("#paterno_persona").val();
  var materno_p =$("#materno_persona").val();
  var identificacion =$("#identificacion").val();
  var pais =$("#pais_empresa").val();
  var estado =$("#departamento_empresa").val();
  var ciudad =$("#ciudad_empresa").val();
  var direccion =$("#direccion_empresa").val();
  var telefono =$("#telefono_empresa").val();
  var correo =$("#correo_empresa").val();
  var nombre_contacto =$("#nombre_contacto").val();
  var apellido_contacto =$("#apellido_contacto").val();
  var cargo_contacto =$("#cargo_contacto").val();
  var correo_contacto =$("#correo_contacto").val();

  var cod_activo =$("#cod_activo").val();
  // var cod_cc =$("#cod_cc").val();
  // var cod_dcc =$("#cod_dcc").val();

  // alert("cod_tcc:"+cod_tcc+"-cod_cc:"+cod_cc+"-cod_dcc:"+cod_dcc);

   var ciudad_true=0;     
  // validaciones de campos
  if($("#tipo_empresa").val()=='E'){
    if(nombre!=""&&identificacion!=""&&(pais>0)&&(estado>0)&&direccion!=""&&telefono!=""&&correo!=""&&nombre_contacto!=""&&apellido_contacto!=""&&cargo_contacto!=""&&correo_contacto!="")
      var sw=true;
    else{
      var sw=false;
    }
  }else{
    if(nombre_p!=""&&paterno_p!=""&&materno_p!=""&&identificacion!=""&&(pais>0)&&(estado>0)&&direccion!=""&&telefono!=""&&correo!="")
      var sw=true;
    else{
      var sw=false;
    }
  }
  if(sw){
     if(ciudad>0){
       ciudad_true=1;
     }else{
      if(ciudad=="NN"){
         ciudad_true=2;
         ciudad="";
      }
     }
     if(ciudad_true>0){
        if(ciudad_true==1){
          var otra="";
        }else{
          var otra=$("#otra_ciudad").val();
        }
        if(otra==""&&ciudad_true==2){
          Swal.fire("Informativo!", "Ingrese el nombre de la Ciudad", "warning");
        }else{
          //proceso de guardado de informacion
           var parametros={"tipo":$("#tipo_empresa").val(),"nacional":$("#nacional_empresa").val(),"nombre":nombre,"nombre_p":nombre_p,"paterno_p":paterno_p,"materno_p":materno_p,"identificacion":identificacion,"pais":pais,"estado":estado,"ciudad":ciudad,"otra":otra,"direccion":direccion,"telefono":telefono,"correo":correo,"nombre_contacto":nombre_contacto,"apellido_contacto":apellido_contacto,"cargo_contacto":cargo_contacto,"correo_contacto":correo_contacto};
            $.ajax({
               type: "GET",
               dataType: 'html',
               url: "solicitudes/ajaxAgregarNuevoProveedor.php",
               data: parametros,
               beforeSend: function () {
                $("#texto_ajax_titulo").html("Enviando datos al servidor..."); 
                  iniciarCargaAjax();
                },
               success:  function (respuesta) {
                var resp = respuesta.split("####")[0];
                  // actualizarRegistroProveedor();
                  // actualizarRegistroProveedorActivoFijo(cod_activo);
                  detectarCargaAjax();
                  $("#texto_ajax_titulo").html("Procesando Datos"); 
                  if(resp.trim()=="1"){
                    //alerts.showSwal('success-message','index.php?opcion=activofijoRegister&codigo='+cod_activo);                    
                    Swal.fire("Correcto!", "Los datos se actualizaron de forma correcta.", "success");
                    actualizarComboBoxAjax();
                  }else{
                    if(resp.trim()=="2"){
                      Swal.fire("Informativo!", "La identificación del proveedor ya se encuentra registrado.", "warning");
                      actualizarComboBoxAjax();
                    }else{
                      Swal.fire("Error!", "Ocurrio un error de envio", "warning");
                    }                       
                  }
               }
             });  
        }       
     }else{
        Swal.fire("Informativo!", "Todos los campos son requeridos", "warning");
     }
   }else{
     Swal.fire("Informativo!", "Todos los campos son requeridos", "warning");
   }
}
function actualizarRegistroProveedorActivoFijo(cod_activo){
  // var codigo = $("#cod_solicitud").val();
 var parametros={"codigo":"none"};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "solicitudes/ajaxActualizarProveedores.php",
        data: parametros,
        beforeSend: function () {
        $("#texto_ajax_titulo").html("Actualizando proveedores desde el Servicio Web..."); 
          iniciarCargaAjax();
        },
        success:  function (resp) {
           detectarCargaAjax();
           $("#texto_ajax_titulo").html("Procesando Datos"); 
           // $('.selectpicker').selectpicker("refresh");
           //alerts.showSwal('success-message','index.php?opcion=activofijoRegister&codigo='+cod_activo);
           Swal.fire("Correcto!", "Los datos se actualizaron de forma correcta.", "success");
           actualizarComboBoxAjax();

        }
    });  
}

function actualizarComboBoxAjax(){
  var contenedor;  
  contenedor = document.getElementById('div_contenedor_proveedor');
  ajax=nuevoAjax();
  ajax.open('GET', 'caja_chica/ajax_actualizarComboAjax.php',true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);             
    }
  }
  ajax.send(null)  
}//unidad_area-cargo



function cargarDatosRegistroContacto(){
  var cod_cliente=$("#cod_cliente").val();
  var cod_personal=$("#cod_personal").val();  
  if(cod_cliente==null  || cod_cliente=='' ){
        Swal.fire("Informativo!", "Seleccione un cliente por favor.", "warning");
  }else{
    var parametros={"cod":"none"};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "simulaciones_servicios/ajaxListarDatosRegistroContacto.php?cod_cliente="+cod_cliente+"&cod_personal="+cod_personal,
        data: parametros,
        beforeSend: function () {
        $("#texto_ajax_titulo").html("Obteniendo datos del servicio..."); 
          iniciarCargaAjax();        
        },
        success:  function (resp) {
           detectarCargaAjax();
           $("#datosProveedorNuevo").html(resp);
           $("#texto_ajax_titulo").html("Procesando Datos"); 
           // $("#pais_contacto").val("26"); //para el pais de BOLIVIA
           $("#departamento_contacto").val("480"); // departamento de LA PAZ           
           $('.selectpicker').selectpicker("refresh");
           $("#modalAgregarProveedor").modal("show");
        }
    });
  }
}
function guardarDatoscontacto(){
  var cod_cliente=$("#cod_cliente").val();
  var cod_personal=$("#cod_personal").val(); 
  var nombre_contacto =$("#nombre_contacto").val();
  var paterno_contacto =$("#paterno_contacto").val();
  var materno_contacto =$("#materno_contacto").val();
  var identificacion_contacto =$("#identificacion_contacto").val();
  var pais_contacto =$("#pais_contacto").val();
  var departamento_contacto =$("#departamento_contacto").val();
  var cargo_contacto =$("#cargo_contacto").val();
  var telefono_contacto =$("#telefono_contacto").val();
  var correo_contacto =$("#correo_contacto").val();

  // validaciones de campos
  //console.log("departamento_contacto:"+departamento_contacto+"-telefono_contacto:"+telefono_contacto+"-correo_contacto:"+correo_contacto+"-identificacion_contacto:"+identificacion_contacto);
  if(nombre_contacto!=""&&paterno_contacto!=""&&identificacion_contacto>0&&departamento_contacto!=""&&telefono_contacto>0&&correo_contacto!="")
    var sw=true;
  else{
    var sw=false;
  }
  if(sw){
    //proceso de guardado de informacion
     var parametros={"cod_personal":cod_personal,"cod_cliente":cod_cliente,"nombre_contacto":nombre_contacto,"paterno_contacto":paterno_contacto,"materno_contacto":materno_contacto,"identificacion_contacto":identificacion_contacto,"departamento_contacto":departamento_contacto,"cargo_contacto":cargo_contacto,"correo_contacto":correo_contacto,"telefono_contacto":telefono_contacto};
      $.ajax({
         type: "GET",
         dataType: 'html',
         url: "simulaciones_servicios/ajaxAgregarNuevoContacto.php",
         data: parametros,
         beforeSend: function () {
          $("#texto_ajax_titulo").html("Enviando datos al servidor..."); 
            iniciarCargaAjax();
          },
         success:  function (resp) {
            detectarCargaAjax();
            $("#texto_ajax_titulo").html("Procesando Datos"); 
            if(resp.trim()=="1"){              
              Swal.fire("Correcto!", "Los datos se actualizaron de forma correcta.", "success");
              $("#modalAgregarProveedor").modal("hide");
              actualizar_contacto_cliente(cod_cliente);

            }else{              
                Swal.fire("Error!", "Ocurrio un error de envio. <br>"+resp.trim(), "warning");
            }

         }
       });  
  }else{
    Swal.fire("Informativo!", "Todos los campos son requeridos", "warning");
  }
}
function actualizar_contacto_cliente(cod_cliente){
  var contenedor;  
  contenedor = document.getElementById('div_contenedor_contactos');
  ajax=nuevoAjax();
  ajax.open('GET', 'simulaciones_servicios/ajax_actualizar_contacto_cliente.php?cod_cliente='+cod_cliente,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);    
      actualizarComboBoxAjax_cliente(cod_cliente);         
    }
  }
  ajax.send(null)  
}
function actualizarComboBoxAjax_cliente(cod_cliente){
  var contenedor;  
  contenedor = document.getElementById('div_contenedor_contactos');
  ajax=nuevoAjax();
  ajax.open('GET', 'simulaciones_servicios/ajax_actualizarComboAjax_cliente.php?cod_cliente='+cod_cliente,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);             
    }
  }
  ajax.send(null)  
}
function actualizarComboBoxAjax_SoloCliente(){
  var contenedor;  
  contenedor = document.getElementById('lista_clientes');
  ajax=nuevoAjax();
  ajax.open('GET', 'simulaciones_servicios/ajax_actualizarComboAjax_SoloCliente.php',true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);             
    }
  }
  ajax.send(null)  
}
function cargarDatosRegistroContactoNormas(){
  var cod_cliente=$("#cod_cliente").val();
  var cod_personal=$("#cod_personal").val();  
  if(cod_cliente==null  || cod_cliente=='' ){
        Swal.fire("Informativo!", "Seleccione un cliente por favor.", "warning");
  }else{
    var parametros={"cod":"none"};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "../simulaciones_servicios/ajaxListarDatosRegistroContacto.php?cod_cliente="+cod_cliente+"&cod_personal="+cod_personal,
        data: parametros,
        beforeSend: function () {
        $("#texto_ajax_titulo").html("Obteniendo datos del servicio..."); 
          iniciarCargaAjax();        
        },
        success:  function (resp) {
           detectarCargaAjax();
           $("#datosProveedorNuevo").html(resp);
           $("#texto_ajax_titulo").html("Procesando Datos"); 
           // $("#pais_contacto").val("26"); //para el pais de BOLIVIA
           $("#departamento_contacto").val("480"); // departamento de LA PAZ           
           $('.selectpicker').selectpicker("refresh");
           $("#modalAgregarProveedor").modal("show");
        }
    });
  }
}
function guardarDatoscontactoNormas(){
  var cod_cliente=$("#cod_cliente").val();
  var cod_personal=$("#cod_personal").val(); 
  var nombre_contacto =$("#nombre_contacto").val();
  var paterno_contacto =$("#paterno_contacto").val();
  var materno_contacto =$("#materno_contacto").val();
  var identificacion_contacto =$("#identificacion_contacto").val();
  var pais_contacto =$("#pais_contacto").val();
  var departamento_contacto =$("#departamento_contacto").val();
  var cargo_contacto =$("#cargo_contacto").val();
  var telefono_contacto =$("#telefono_contacto").val();
  var correo_contacto =$("#correo_contacto").val();

  // validaciones de campos
  //console.log("departamento_contacto:"+departamento_contacto+"-telefono_contacto:"+telefono_contacto+"-correo_contacto:"+correo_contacto+"-identificacion_contacto:"+identificacion_contacto);
  if(nombre_contacto!=""&&paterno_contacto!=""&&identificacion_contacto>0&&departamento_contacto!=""&&telefono_contacto>0&&correo_contacto!="")
    var sw=true;
  else{
    var sw=false;
  }
  if(sw){
    //proceso de guardado de informacion
     var parametros={"cod_personal":cod_personal,"cod_cliente":cod_cliente,"nombre_contacto":nombre_contacto,"paterno_contacto":paterno_contacto,"materno_contacto":materno_contacto,"identificacion_contacto":identificacion_contacto,"departamento_contacto":departamento_contacto,"cargo_contacto":cargo_contacto,"correo_contacto":correo_contacto,"telefono_contacto":telefono_contacto};
      $.ajax({
         type: "GET",
         dataType: 'html',
         url: "../simulaciones_servicios/ajaxAgregarNuevoContacto.php",
         data: parametros,
         beforeSend: function () {
          $("#texto_ajax_titulo").html("Enviando datos al servidor..."); 
            iniciarCargaAjax();
          },
         success:  function (resp) {
            detectarCargaAjax();
            $("#texto_ajax_titulo").html("Procesando Datos"); 
            if(resp.trim()=="1"){              
              Swal.fire("Correcto!", "Los datos se actualizaron de forma correcta.", "success");
              $("#modalAgregarProveedor").modal("hide");
              actualizar_contacto_clienteNormas(cod_cliente);

            }else{              
                Swal.fire("Error!", "Ocurrio un error de envio. <br>"+resp.trim(), "warning");
            }

         }
       });  
  }else{
    Swal.fire("Informativo!", "Todos los campos son requeridos", "warning");
  }
}
function actualizar_contacto_clienteNormas(cod_cliente){
  var contenedor;  
  contenedor = document.getElementById('div_contenedor_contactos');
  ajax=nuevoAjax();
  ajax.open('GET', '../simulaciones_servicios/ajax_actualizar_contacto_cliente.php?cod_cliente='+cod_cliente,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);    
      actualizarComboBoxAjax_clienteNormas(cod_cliente);         
    }
  }
  ajax.send(null)  
}//unidad_area-cargo
function actualizarComboBoxAjax_clienteNormas(cod_cliente){
  var contenedor;  
  contenedor = document.getElementById('div_contenedor_contactos');
  ajax=nuevoAjax();
  ajax.open('GET', '../simulaciones_servicios/ajax_actualizarComboAjax_cliente.php?cod_cliente='+cod_cliente,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);             
    }
  }
  ajax.send(null)  
}



function actualizarRegistroContacto(){
  var cod_cliente=$("#cod_cliente").val();
  var parametros={"codigo":"none"};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "simulaciones_servicios/ajaxActualizarContactos.php?",
        data: parametros,
        beforeSend: function () {
        $("#texto_ajax_titulo").html("Actualizando Contactos de Clientes desde el Servicio Web..."); 
          iniciarCargaAjax();
        },
        success:  function (resp) {
           detectarCargaAjax();
           $("#texto_ajax_titulo").html("Procesando Datos"); 
           // $('.selectpicker').selectpicker("refresh");           
           Swal.fire("Correcto!", "Los datos se actualizaron de forma correcta.", "success");
           actualizarComboBoxAjax_cliente(cod_cliente);
        }
    }); 
}
function actualizarRegistroSoloClientes(){
  var parametros={"codigo":"none"};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "simulaciones_servicios/ajaxActualizarClientes.php?",
        data: parametros,
        beforeSend: function () {
        $("#texto_ajax_titulo").html("Actualizando Clientes"); 
          iniciarCargaAjax();
        },
        success:  function (resp) {
           detectarCargaAjax();
           $("#texto_ajax_titulo").html("Procesando Datos");          
           Swal.fire("Correcto!", "Los datos se actualizaron de forma correcta.", "success");
           actualizarComboBoxAjax_SoloCliente();
        }
    }); 
}
function actualizarRegistroContactoNormas(){
  var cod_cliente=$("#cod_cliente").val();
  var parametros={"codigo":"none"};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "../simulaciones_servicios/ajaxActualizarContactos.php?",
        data: parametros,
        beforeSend: function () {
        $("#texto_ajax_titulo").html("Actualizando Contactos de Clientes desde el Servicio Web..."); 
          iniciarCargaAjax();
        },
        success:  function (resp) {
           detectarCargaAjax();
           $("#texto_ajax_titulo").html("Procesando Datos"); 
           // $('.selectpicker').selectpicker("refresh");           
           Swal.fire("Correcto!", "Los datos se actualizaron de forma correcta.", "success");
           actualizarComboBoxAjax_clienteNormas(cod_cliente);
        }
    }); 
}
function actualizarRegistroClientes(){
  // var codigo = $("#cod_solicitud").val();
 var parametros={"codigo":"none"};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "clientes/ajaxActualizarClientes.php?",
        data: parametros,
        beforeSend: function () {
        $("#texto_ajax_titulo").html("Actualizando Contactos de Clientes desde el Servicio Web..."); 
          iniciarCargaAjax();
        },
        success:  function (resp) {
           detectarCargaAjax();
           $("#texto_ajax_titulo").html("Procesando Datos"); 
           // $('.selectpicker').selectpicker("refresh");           
           Swal.fire("Correcto!", "Los datos se actualizaron de forma correcta.", "success");
           //sactualizarComboBoxAjax_cliente(cod_cliente);

        }
    });  
}


function botonBuscarActivoFijo(){
  var valor_uo=$("#OficinaBusqueda").val();
  var valor_rubro=$("#rubro").val();
  var valor_fi=$("#fechaBusquedaInicio").val();
  var valor_ff=$("#fechaBusquedaFin").val();
  var valor_responsable=$("#responsable").val();
  var valor_tipoAlta=$("#tipoAlta").val();
  var valor_proyecto=$("#proyecto").val();
  var valor_glosa=$("#glosaBusqueda").val();
  
  
  ajax=nuevoAjax();
  ajax.open('GET', 'activosFijos/ajax_busquedaAvanzadaAf.php?cod_uo='+valor_uo+'&rubro='+valor_rubro+'&fechaI='+valor_fi+'&fechaF='+valor_ff+
    '&responsable='+valor_responsable+'&tipoAlta='+valor_tipoAlta+'&proyecto='+valor_proyecto+'&glosa='+valor_glosa,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      var contenedor=$("#data_activosFijos");
      contenedor.html(ajax.responseText);
      $("#modalBuscador").modal("hide");
    }
  }
  ajax.send(null)
} 

// function sacandoUFVDepreAF(){
//     var gestion=document.getElementById("gestion").value; 
//     var mes=document.getElementById("mes").value; 
//     jax=nuevoAjax();
//   ajax.open('GET', 'activosFijos/ajax_depreciaocionUFV.php?gestion='+gestion+'&mes='+mes,true);
//   ajax.onreadystatechange=function() {
//     if (ajax.readyState==4) {
//       var contenedor=$("#data_activosFijos");
//       contenedor.html(ajax.responseText);
//       // $("#modalBuscador").modal("hide");
//     }
//   }
//   ajax.send(null)
    
//   }

function sacandoUFVDepreAF(){
  
  var gestion=document.getElementById("gestion").value; 
  var mes=document.getElementById("mes").value; 

  var contenedor;
  contenedor = document.getElementById('div_contenedor_ufv_inicio');
  ajax=nuevoAjax();
  ajax.open('GET', 'activosFijos/ajax_depreciaocionUFV.php?gestion='+gestion+'&mes='+mes,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      // $('.selectpicker').selectpicker(["refresh"]);   
      ajax_ufv_fin();       
    }
  }
  ajax.send(null)  
}

function ajax_ufv_fin(){  
  var gestion=document.getElementById("gestion").value; 
  var mes=document.getElementById("mes").value; 
  var contenedor;
  contenedor = document.getElementById('div_contenedor_ufv_fin');
  ajax=nuevoAjax();
  ajax.open('GET', 'activosFijos/ajax_depreciacionUFVfin.php?gestion='+gestion+'&mes='+mes,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      // $('.selectpicker').selectpicker(["refresh"]);   
      // ajaxPersonalUbicacion(codigo_UO);       
    }
  }
  ajax.send(null)  
}

function copiarDatosServicios(anio){
  var anios=$("#copiar_servicios"+anio).val();
  if(anios.length>0){
  Swal.fire({
        title: '¿Esta Seguro?',
        text: "Los datos se copiarán!",
         type: 'warning',
        showCancelButton: true,
        confirmButtonClass: 'btn btn-info',
        cancelButtonClass: 'btn btn-danger',
        confirmButtonText: 'COPIAR',
        cancelButtonText: 'CANCELAR',
        buttonsStyling: false
       }).then((result) => {
          if (result.value) {
               copiarDatosServiciosPorAnio(anio);            
            return(true);
          } else if (result.dismiss === Swal.DismissReason.cancel) {
            return(false);
          }
        });
  }else{
    Swal.fire("Informativo!", "Debe seleccionar al menos un Año", "warning");
  }
}

function copiarDatosServiciosPorAnio(anio){
 var anios=$("#copiar_servicios"+anio).val();
  for (var i = 0; i < anios.length; i++) {
    var items=$("#modal_numeroservicio"+anios[i]).val();
      for (var k = 1; k <= items; k++) {
          $("#cantidad_servicios"+anios[i]+"SSS"+k).val($("#cantidad_servicios"+anio+"SSS"+k).val());
          $("#unidad_servicios"+anios[i]+"SSS"+k).val($("#unidad_servicios"+anio+"SSS"+k).val());
          $("#modal_montoserv"+anios[i]+"SSS"+k).val($("#modal_montoserv"+anio+"SSS"+k).val()); 
          if(($("#modal_montoserv"+anios[i]+"SSS"+k).is("[readonly]"))&&($("#modal_montoserv"+anio+"SSS"+k).is("[readonly]"))){
            
          }else{
            if((!($("#modal_montoserv"+anios[i]+"SSS"+k).is("[readonly]")))&&(!($("#modal_montoserv"+anio+"SSS"+k).is("[readonly]")))){
            }else{
              activarInputMontoFilaServicio(anios[i],k); //para cambiar los readonly
              seleccionarEsteCheck('modal_checkserv'+anios[i]+"SSS"+k);
            }
          }        
          $('.selectpicker').selectpicker("refresh");
          calcularTotalFilaServicio(anios[i],2);
       }; 
  };
 $("#copiar_servicios"+anio).val(""); 
 $('.selectpicker').selectpicker("refresh");
}

function copiarDatosPersonal(anio){
  var anios=$("#copiar_personal"+anio).val();
  if(anios.length>0){
  Swal.fire({
        title: '¿Esta Seguro?',
        text: "Los datos se copiarán!",
         type: 'warning',
        showCancelButton: true,
        confirmButtonClass: 'btn btn-info',
        cancelButtonClass: 'btn btn-danger',
        confirmButtonText: 'COPIAR',
        cancelButtonText: 'CANCELAR',
        buttonsStyling: false
       }).then((result) => {
          if (result.value) {
               copiarDatosPersonalPorAnio(anio);            
            return(true);
          } else if (result.dismiss === Swal.DismissReason.cancel) {
            return(false);
          }
        });
  }else{
    Swal.fire("Informativo!", "Debe seleccionar al menos un Año", "warning");
  }
}

function copiarDatosPersonalPorAnio(anio){
 var anios=$("#copiar_personal"+anio).val();
  for (var i = 0; i < anios.length; i++) {
    var items=$("#modal_numeropersonal"+anios[i]).val();
      for (var k = 1; k <= items; k++) {
          $("#cantidad_personal"+anios[i]+"FFF"+k).val($("#cantidad_personal"+anio+"FFF"+k).val());
          $("#dias_personal"+anios[i]+"FFF"+k).val($("#dias_personal"+anio+"FFF"+k).val());
          $("#modal_montopre"+anios[i]+"FFF"+k).val($("#modal_montopre"+anio+"FFF"+k).val());
          if(($("#modal_montopre"+anios[i]+"FFF"+k).is("[readonly]"))&&($("#modal_montopre"+anio+"FFF"+k).is("[readonly]"))){
            
          }else{
            if((!($("#modal_montopre"+anios[i]+"FFF"+k).is("[readonly]")))&&(!($("#modal_montopre"+anio+"FFF"+k).is("[readonly]")))){
            }else{
              activarInputMontoPersonalServicio(anios[i],k); //para cambiar los readonly
              seleccionarEsteCheck('modal_checkpre'+anios[i]+"FFF"+k);
            }
          }     
          $('.selectpicker').selectpicker("refresh");
          calcularTotalPersonalServicio(anios[i],2);
       }; 
  };
 $("#copiar_personal"+anio).val(""); 
 $('.selectpicker').selectpicker("refresh");
}

function copiarCostosVariables(anio){
  var anios=$("#copiar_variables"+anio).val();
  if(anios.length>0){
  Swal.fire({
        title: '¿Esta Seguro?',
        text: "Los datos se copiarán!",
         type: 'warning',
        showCancelButton: true,
        confirmButtonClass: 'btn btn-info',
        cancelButtonClass: 'btn btn-danger',
        confirmButtonText: 'COPIAR',
        cancelButtonText: 'CANCELAR',
        buttonsStyling: false
       }).then((result) => {
          if (result.value) {
               copiarCostosVariablesPorAnio(anio);            
            return(true);
          } else if (result.dismiss === Swal.DismissReason.cancel) {
            return(false);
          }
        });
  }else{
    Swal.fire("Informativo!", "Debe seleccionar al menos un Año", "warning");
  }
}

function copiarCostosVariablesPorAnio(anio){
 var anios=$("#copiar_variables"+anio).val();
  guardarCuentasSimulacionAjaxGenericoServicioAuditor(anio,1,anios);
 $("#copiar_variables"+anio).val(""); 
 $('.selectpicker').selectpicker("refresh");
}


function mostrarCambioEstadoObjeto(codigo){
  $("#modal_codigopropuesta").val(codigo);  
  var item_1=$("#modal_tipoobjeto").val();
  var item_2=codigo;
  var item_3=$("#modal_rolpersona").val();

  var parametros={"item_1":item_1,"item_2":item_2,"item_3":item_3};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "simulaciones_servicios/ibnorca_ajaxListComboEstados.php",
        data: parametros,
        beforeSend: function () {
        $("#texto_ajax_titulo").html("Cargando los estados disponibles de la propuesta"); 
          iniciarCargaAjax();
        },
        success:  function (resp) {
           detectarCargaAjax();
           $("#texto_ajax_titulo").html("Procesando Datos");
           $("#modal_codigoestado").html(resp); 
           $('.selectpicker').selectpicker("refresh");
           $("#modalEstadoObjeto").modal("show");
        }
    });
}


function cambiarEstadoObjeto(){
  if($("#modal_codigoestado").val()>0){
    Swal.fire({
        title: '¿Esta Seguro?',
        text: "Se cambiará el estado!",
         type: 'warning',
        showCancelButton: true,
        confirmButtonClass: 'btn btn-warning',
        cancelButtonClass: 'btn btn-danger',
        confirmButtonText: 'SI',
        cancelButtonText: 'NO',
        buttonsStyling: false
       }).then((result) => {
          if (result.value) {
               cambiarEstadoObjetoAjax();            
            return(true);
          } else if (result.dismiss === Swal.DismissReason.cancel) {
            return(false);
          }
        });   
  }else{
    Swal.fire("Informativo!", "Debe Seleccionar Un estado", "warning");
  }
}

function cambiarEstadoObjetoAjax(){
  var codigo=$("#modal_codigopropuesta").val();
  var estado=$("#modal_codigoestado").val();
  var observaciones=$("#modal_observacionesestado").val();
  if($("#idPerfil").length>0){
     var parametros={"obs":observaciones,"estado":estado,"codigo":codigo,"id_perfil":$("#idPerfil").val()};
  }else{
    var parametros={"obs":observaciones,"estado":estado,"codigo":codigo,"id_perfil":0};
  }
  
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "simulaciones_servicios/ibnorca_ajaxCambiarEstado.php",
        data: parametros,
        beforeSend: function () {
        $("#texto_ajax_titulo").html("Actualizando estado..."); 
          iniciarCargaAjax();
        },
        success:  function (resp) {
           detectarCargaAjax();
           $("#texto_ajax_titulo").html("Procesando Datos");
           if($("#id_servicioibnored").length>0){
            var q=$("#id_servicioibnored").val();
            var r=$("#id_servicioibnored_rol").val();
            if($("#idPerfil").length>0){
             var u=$("#idPerfil").val();
             var s=$("#ss").val();
             alerts.showSwal('success-message','index.php?opcion=listSimulacionesServAdmin&q='+q+'&r='+r+'&s='+s+'&u='+u);   
            }else{
             alerts.showSwal('success-message','index.php?opcion=listSimulacionesServAdmin&q='+q+'&r='+r);   
            }
            
          }else{
             alerts.showSwal('success-message','index.php?opcion=listSimulacionesServAdmin');
          }
        }
    });
}

function cambiarEstadoObjetoSol(){
  $("#modalEstadoObjeto").modal("hide");
  if($("#modal_codigoestado").val()>0){
    Swal.fire({
        title: '¿Esta Seguro?',
        text: "Se cambiará el estado!",
         type: 'warning',
        showCancelButton: true,
        confirmButtonClass: 'btn btn-warning',
        cancelButtonClass: 'btn btn-danger',
        confirmButtonText: 'SI',
        cancelButtonText: 'NO',
        buttonsStyling: false
       }).then((result) => {
          if (result.value) {
               cambiarEstadoObjetoSolAjax();            
            return(true);
          } else if (result.dismiss === Swal.DismissReason.cancel) {
            return(false);
          }
        });   
  }else{
    Swal.fire("Informativo!", "Debe Seleccionar Un estado", "warning");
  }
}

function cambiarEstadoObjetoSolAjax(){
  var codigo=$("#modal_codigopropuesta").val();
  var estado=$("#modal_codigoestado").val();
  var observaciones=$("#modal_observacionesestado").val();
  if($("#id_servicioibnored_u").length>0){
    var parametros={"obs":observaciones,"estado":estado,"codigo":codigo,"u":$("#id_servicioibnored_u").val()};
  }else{
    var parametros={"obs":observaciones,"estado":estado,"codigo":codigo};
  }
  
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "solicitudes/ibnorca_ajaxCambiarEstado.php",
        data: parametros,
        beforeSend: function () {
        $("#texto_ajax_titulo").html("Actualizando estado..."); 
          iniciarCargaAjax();
        },
        success:  function (resp) {
           detectarCargaAjax();
           $("#texto_ajax_titulo").html("Procesando Datos");
           var respu=resp.split("####");
           var urlAprob='listSolicitudRecursosAdmin';
           if($("#modal_adminconta").length>0){
            urlAprob='listSolicitudRecursosAdminConta';
           }

           if(respu[1]=="none"){
             //no se creo el comprobante
             Swal.fire("Informativo!", "Una de las cuentas del detalle de la solicitud, no esta relacionada a su cuenta pasivo", "warning");
           }else{
            if($("#id_servicioibnored").length>0){
              var q=$("#id_servicioibnored").val();
              var r=$("#id_servicioibnored_rol").val();
              var s=$("#id_servicioibnored_s").val();
              var u=$("#id_servicioibnored_u").val();
              alerts.showSwal('success-message','index.php?opcion='+urlAprob+'&q='+q+'&r='+r+'&s='+s+'&u='+u);   
            }else{
              alerts.showSwal('success-message','index.php?opcion='+urlAprob);
            }    
           }
        }
    });
}

function cambiarEstadoObjetoSolFac(){
  $("#modalEstadoObjeto").modal("hide");
  if($("#modal_codigoestado").val()>0){
    Swal.fire({
        title: '¿Esta Seguro?',
        text: "Se cambiará el estado!",
         type: 'warning',
        showCancelButton: true,
        confirmButtonClass: 'btn btn-warning',
        cancelButtonClass: 'btn btn-danger',
        confirmButtonText: 'SI',
        cancelButtonText: 'NO',
        buttonsStyling: false
       }).then((result) => {
          if (result.value) {
               cambiarEstadoObjetoSolFacAjax();            
            return(true);
          } else if (result.dismiss === Swal.DismissReason.cancel) {
            return(false);
          }
        });   
  }else{
    Swal.fire("Informativo!", "Debe Seleccionar Un estado", "warning");
  }
}

function cambiarEstadoObjetoSolFacAjax(){
  var codigo=$("#modal_codigopropuesta").val();
  var estado=$("#modal_codigoestado").val();
  var observaciones=$("#modal_observacionesestado").val();
  if($("#id_servicioibnored_u").length>0){
    var parametros={"obs":observaciones,"estado":estado,"codigo":codigo,"u":$("#id_servicioibnored_u").val()};
  }else{
    var parametros={"obs":observaciones,"estado":estado,"codigo":codigo};
  }
  
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "simulaciones_servicios/ibnorca_ajaxCambiarEstadoFac.php",
        data: parametros,
        beforeSend: function () {
        $("#texto_ajax_titulo").html("Actualizando estado..."); 
          iniciarCargaAjax();
        },
        success:  function (resp) {
           detectarCargaAjax();
           $("#texto_ajax_titulo").html("Procesando Datos");
           if($("#id_servicioibnored").length>0){
            var q=$("#id_servicioibnored").val();
            var r=$("#id_servicioibnored_rol").val();
            var s=$("#id_servicioibnored_s").val();
            var u=$("#id_servicioibnored_u").val();
            alerts.showSwal('success-message','index.php?opcion=listFacturasServiciosAdmin&q='+q+'&r='+r+'&s='+s+'&u='+u);   
          }else{
             alerts.showSwal('success-message','index.php?opcion=listFacturasServiciosAdmin');
          }
        }
    });
}

function cambiarEstadoObjetoPlan(){
  if($("#modal_codigoestado").val()>0){
    Swal.fire({
        title: '¿Esta Seguro?',
        text: "Se cambiará el estado!",
         type: 'warning',
        showCancelButton: true,
        confirmButtonClass: 'btn btn-warning',
        cancelButtonClass: 'btn btn-danger',
        confirmButtonText: 'SI',
        cancelButtonText: 'NO',
        buttonsStyling: false
       }).then((result) => {
          if (result.value) {
               cambiarEstadoObjetoPlanAjax();            
            return(true);
          } else if (result.dismiss === Swal.DismissReason.cancel) {
            return(false);
          }
        });   
  }else{
    Swal.fire("Informativo!", "Debe Seleccionar Un estado", "warning");
  }
}

function cambiarEstadoObjetoPlanAjax(){
  var codigo=$("#modal_codigopropuesta").val();
  var estado=$("#modal_codigoestado").val();
  var observaciones=$("#modal_observacionesestado").val();
  if($("#id_servicioibnored_u").length>0){
    var idPerfil=$("#id_servicioibnored_u").val();
  }else{
    var idPerfil=0;
  }
  var parametros={"obs":observaciones,"estado":estado,"codigo":codigo,"id_perfil":idPerfil};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "plantillas_servicios/ibnorca_ajaxCambiarEstado.php",
        data: parametros,
        beforeSend: function () {
        $("#texto_ajax_titulo").html("Actualizando estado..."); 
          iniciarCargaAjax();
        },
        success:  function (resp) {
           detectarCargaAjax();
           $("#texto_ajax_titulo").html("Procesando Datos");
           if($("#id_servicioibnored").length>0){
            var q=$("#id_servicioibnored").val();
            var r=$("#id_servicioibnored_rol").val();
            var s=$("#id_servicioibnored_s").val();
            var u=$("#id_servicioibnored_u").val();
            alerts.showSwal('success-message','index.php?opcion=listPlantillasServiciosAdmin&q='+q+'&r='+r+'&s='+s+'&u='+u);   
          }else{
             alerts.showSwal('success-message','index.php?opcion=listPlantillasServiciosAdmin');
          }
        }
    });
}

function seleccionarEsteCheck(id) {
    var item = document.getElementById(id);
     if(item.checked==true){
      item.checked = false;
    }else{
      item.checked = true;
    }       
}


// var distribucion_sueldos=[];
// var numFilasA=0;
// function filaTablaAGeneral_distibucion(tabla,index){
//   var html="";
//   for (var i = 0; i < distribucion_sueldos[index-1].length; i++) {
//     //alert(distribucion_sueldos[index-1][i].nombre);
//     html+="<tr><td>"+(i+1)+"</td><td>"+distribucion_sueldos[index-1][i].serviciox+"</td><td>"+detalle_tabla_general[index-1][i].cantidadX+"</td><td>"+detalle_tabla_general[index-1][i].precioX+"</td><td>"+detalle_tabla_general[index-1][i].descripcion_alternaX+"</td></tr>";
//   }
//   tabla.html(html);
//   $("#modalDetalleFac").modal("show");  
// }

//proveedores en comprobantes
function cargarDatosRegistroComprobantes(){
  var parametros={"cod":"none"};
  // $('#cod_tcc').val(cod_tcc);
  // $('#cod_cc').val(cod_cc);
  // $('#cod_dcc').val(cod_dcc);
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "../comprobantes/ajaxListarDatosRegistroProveedor.php",
        data: parametros,
        beforeSend: function () {
        $("#texto_ajax_titulo").html("Obteniendo datos del servicio..."); 
          iniciarCargaAjax();
        },
        success:  function (resp) {
           detectarCargaAjax();
           $("#datosProveedorNuevo").html(resp);
           $("#texto_ajax_titulo").html("Procesando Datos"); 
           $("#pais_empresa").val("26"); //para el pais de BOLIVIA
           seleccionarDepartamentoServicioComprobantes();
           $('.selectpicker').selectpicker("refresh");
           $("#modalAgregarProveedor").modal("show");
           
        }
    });
}



function mostrarNuevoPersonalModal(anio,titulo,valor){
  $("#anio_personal").val(anio);
  $("#valor_personal").val(valor);
  $("#titulo_modal_honorarios").text(titulo);
  $("#modalNuevoPersonal").modal("show");
}

function agregarNuevoPersonalSimulacionModal(inicioAnio,ibnorcaC){
  var anio=$("#anio_personal").val();
  var cod_sim=$("#cod_simulacion").val();
  var cod_area=$("#codigo_area").val();
  var cod_cla=$("#modal_personalhonorario").val();
  var cantidad=1;
  var monto=1;
  var dias=$("#modal_diaspersonalhonorario").val();
  
  if(!(cod_cla>0)||dias==""||dias==0){
   Swal.fire("Informativo!", "Debe llenar los campos requeridos", "warning");
  }else{
  var parametros={"cod_sim":cod_sim,"cod_cla":cod_cla,"cantidad":cantidad,"monto":monto,"dias":dias,"anio":anio};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "ajaxSavePersonal.php",
        data: parametros,
        beforeSend: function () {
        $("#modalNuevoPersonal").modal("hide");
        $("#texto_ajax_titulo").html("Agregando servicio..."); 
          iniciarCargaAjax();
        },
        success:  function (respu) {
           detectarCargaAjax();
           $("#texto_ajax_titulo").html("Procesando Datos");
           $("#modal_diaspersonalhonorario").val("1");
           $("#modal_personalhonorario").val("");
           $('.selectpicker').selectpicker("refresh");
           var respuesta=respu.split("###");
           var resp=respuesta[0];
           if(resp==0){
            if($("#valor_personal").val()==1){
              actualizarSimulacionSitios();
            }else{
               cargarDetallesCostosVariablesTodosLosAnios(inicioAnio,ibnorcaC);
            }
           }else{
            Swal.fire("Informativo!", "El personal ya existe!", "warning");
           }
        }
    });      
  }
}

function editFac(fila,i){
  $("#fila_fac").val(fila);
  $("#indice_fac").val(i);
  $("#nit_fac_edit").val(itemFacturas[fila-1][i].nit);
  $("#nro_fac_edit").val(itemFacturas[fila-1][i].nroFac);
  $("#fecha_fac_edit").val(itemFacturas[fila-1][i].fechaFac);
  $("#imp_fac_edit").val(itemFacturas[fila-1][i].impFac);
  $("#exe_fac_edit").val(itemFacturas[fila-1][i].exeFac);
  $("#ice_fac_edit").val(itemFacturas[fila-1][i].iceFac);
  $("#taza_fac_edit").val(itemFacturas[fila-1][i].tazaFac);
  $("#aut_fac_edit").val(itemFacturas[fila-1][i].autFac);
  $("#con_fac_edit").val(itemFacturas[fila-1][i].conFac);
  $("#tipo_fac_edit").val(itemFacturas[fila-1][i].tipoFac);
  $("#razon_fac_edit").val(itemFacturas[fila-1][i].razonFac);
  $('.selectpicker').selectpicker("refresh");
  $("#modalEditFac").modal("show");
}
function saveFacturaEdit(){
  if($('#nit_fac_edit').val()!=''){
      if($('#nro_fac_edit').val()!=''){
        if($('#fecha_fac_edit').val()!=''){        
            if($('#imp_fac_edit').val()!=''){
              if($('#aut_fac_edit').val()!=''){              
                  if($('#razon_fac_edit').val()!=''){
                    var fila = $("#fila_fac").val();
                    var i = $("#indice_fac").val();
                    itemFacturas[fila-1][i].nit=$("#nit_fac_edit").val();
                    itemFacturas[fila-1][i].nroFac=$("#nro_fac_edit").val();
                    itemFacturas[fila-1][i].fechaFac=$("#fecha_fac_edit").val();
                    itemFacturas[fila-1][i].impFac=$("#imp_fac_edit").val();
                    itemFacturas[fila-1][i].exeFac=$("#exe_fac_edit").val();
                    itemFacturas[fila-1][i].iceFac=$("#ice_fac_edit").val();
                    itemFacturas[fila-1][i].tazaFac=$("#taza_fac_edit").val();
                    itemFacturas[fila-1][i].autFac=$("#aut_fac_edit").val();
                    itemFacturas[fila-1][i].conFac=$("#con_fac_edit").val();
                    itemFacturas[fila-1][i].tipoFac=$("#tipo_fac_edit").val();
                    itemFacturas[fila-1][i].razonFac=$("#razon_fac_edit").val();
                    $("#modalEditFac").modal("hide");
                    listarFact(fila);
                    $("#link110").addClass("active");$("#link111").removeClass("active");$("#link112").removeClass("active");
                    $("#nav_boton1").addClass("active");$("#nav_boton2").removeClass("active");$("#nav_boton3").removeClass("active");               
                  }else{
                    alertaModal('Campo "Razón Social" Vacío.','bg-primary','text-white');
                  }
              }else{
                alertaModal('Campo "Nro. Autorización" Vacío.','bg-primary','text-white');
              }
            }else{
              alertaModal('Campo "Importe" Vacío.','bg-primary','text-white');
            }
        }else{
          alertaModal('Campo "Fecha" Vacío.','bg-primary','text-white');
        }  
      }else{
        alertaModal('Campo "Nro. Factura" Vacío.','bg-primary','text-white');
      }  
    }else{
      alertaModal('Campo "NIT" Vacío.','bg-primary','text-white');
    }                 
}

function calcularTotalesSolicitud(){
  var sumapres=0;
  var sumasol=0;
  var cantidad = $("#cantidad_filas").val();
  for (var i = 1; i <= cantidad; i++) {
     sumapres += $("#importe_presupuesto"+i).val() * 1;
     sumasol += $("#importe"+i).val() * 1;
  };

    document.getElementById("total_presupuestado").value=redondeo(sumapres,2).toFixed(2);  
    document.getElementById("total_solicitado").value=redondeo(sumasol,2).toFixed(2);  
    if($("#total_presupuestado").val()>=$("#total_solicitado").val()){
      if(!($("#buttonSubmitFalse").hasClass("d-none"))){
        $("#buttonSubmitFalse").addClass("d-none");
        $("#buttonSubmit").removeClass("d-none");
      }
    }else{
      if($("#buttonSubmitFalse").hasClass("d-none")){
        $("#buttonSubmitFalse").removeClass("d-none");
        $("#buttonSubmit").addClass("d-none");
      }
    }
}


function ajaxClienteContacto(combo){
  var cod_tipopago=$("#cod_tipopago").val();  
  // alert(cod_tipopago);
  if(cod_tipopago!=null){
    var contenedor;
    var cod_cliente=combo.value;
    contenedor = document.getElementById('div_contenedor_contactos');
    ajax=nuevoAjax();
    ajax.open('GET', 'solicitud_facturacion_manual/ajax_contacto_cliente.php?cod_cliente='+cod_cliente+'&cod_tipopago='+cod_tipopago,true);
    ajax.onreadystatechange=function() {
      if (ajax.readyState==4) {
        contenedor.innerHTML = ajax.responseText;
        $('.selectpicker').selectpicker(["refresh"]);   
        ajaxCliente_nit_razonsocial(cod_cliente);       
      }
    }
    ajax.send(null)  

  }else{

  }
}//unidad_area-cargo
function ajaxClienteContactoNormas(combo){
  var cod_tipopago=$("#cod_tipopago").val();  
  // alert(cod_tipopago);
  if(cod_tipopago!=null){
    var contenedor;
    var cod_cliente=combo.value;
    contenedor = document.getElementById('div_contenedor_contactos');
    ajax=nuevoAjax();
    ajax.open('GET', '../solicitud_facturacion_manual/ajax_contacto_cliente.php?cod_cliente='+cod_cliente+'&cod_tipopago='+cod_tipopago,true);
    ajax.onreadystatechange=function() {
      if (ajax.readyState==4) {
        contenedor.innerHTML = ajax.responseText;
        $('.selectpicker').selectpicker(["refresh"]);   
        ajaxCliente_nit_razonsocialNormas(cod_cliente);       
      }
    }
    ajax.send(null)  

  }else{

  }
}//unidad_area-cargo

function ajaxCliente_nit_razonsocial(cod_cliente){
  var contenedor;  
  contenedor = document.getElementById('contenedor_razon_nit');
  ajax=nuevoAjax();
  ajax.open('GET', 'solicitud_facturacion_manual/ajax_nit_razon_cliente.php?cod_cliente='+cod_cliente,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);          
    }
  }
  ajax.send(null)  
}//unidad_area-cargo
function ajaxCliente_nit_razonsocialNormas(cod_cliente){
  var contenedor;  
  contenedor = document.getElementById('contenedor_razon_nit');
  ajax=nuevoAjax();
  ajax.open('GET', '../solicitud_facturacion_manual/ajax_nit_razon_cliente.php?cod_cliente='+cod_cliente,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);          
    }
  }
  ajax.send(null)  
}//unidad_area-cargo
function vistaPreviaArchivoSol(url,nombre){
  $("#vista_previa_frame").attr("src",url);
  $("#titulo_vista_previa").text('VISTA PREVIA "'+nombre+'"');
}

function moverModal(mod){
  $("#"+mod).draggable({
    handle: ".card-header"
  }); 
}

function quitarPropuestaCombo(id){
  $('#simulaciones option').each(function() {
    if ( $(this).val() != id ) {
        $(this).remove();
    }
  });
  $('.selectpicker').selectpicker("refresh");
}


function botonBuscarNormasSolfac(){
  var valor_glosa_cliente=$("#glosaCliente").val();  
  var valor_fi=$("#fechaBusquedaInicio").val();
  var valor_ff=$("#fechaBusquedaFin").val();
  var valor_normas=$("#normas").val();
  ajax=nuevoAjax();
  ajax.open('GET', 'solicitud_facturacion_manual/ajax_busqueda_normas.php?glosa_cliente='+valor_glosa_cliente+'&fechaI='+valor_fi+'&fechaF='+valor_ff+'&normas='+valor_normas,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      var contenedor=$("#contenedor_items_normas");
      contenedor.html(ajax.responseText);
      $("#modalBuscador").modal("hide");
    }
  }
  ajax.send(null)
}
function botonBuscarEstudiantesCapacitacion(){
  // iniciarCargaAjax();
  var valor_ci_cliente=$("#ci").val();    
  var valor_nombre_cliente=$("#nombreCliente").val();   
  var valor_paterno_cliente=$("#paternoCliente").val();   
  var valor_materno_cliente=$("#maternoCliente").val(); 
  var valor_nombre_curso= $("#nombre_curso").val(); 
  var valor_fecha_inscripcion=$("#fecha_inscripcion").val(); 
  var q=$("#q").val();   
  var r=$("#r").val();   
  var url='index.php?opcion=listFacturasServicios_costos_estudiantes&ci='+valor_ci_cliente+'&nombre='+valor_nombre_cliente+'&paterno='+valor_paterno_cliente+'&materno='+valor_materno_cliente+'&fecha='+valor_fecha_inscripcion+'&nombre_curso='+valor_nombre_curso;
  if(q!=0){    
    location.href=url+'&q='+q+'&r='+r;     
  }else{    
    location.href=url;     
  }
}
function itemsSeleccionados_capacitacion_estudiantes(){  
  var sumal=0;  
  var total=$("#total_items").val();
  var contador_auxiliar=0;
  // alert(total);
  for (var i=1;i<=(total-1);i++){              
    var check=document.getElementById("modal_check_g"+i).checked;

    if(check) {//BUSACMOS LOS CHECK ACTIVOS
      contador_auxiliar=contador_auxiliar+1;        
      //sacamos los datos de los items que se activaron
      var CiAlumno = document.getElementById("CiAlumno"+i).value;      
      var IdCurso = document.getElementById("IdCurso"+i).value;
      // aqui se guardan los items activados
      document.getElementById("CiAlumno_a"+i).value=CiAlumno;
      document.getElementById("IdCurso_a"+i).value=IdCurso;      
    }else{
      document.getElementById("CiAlumno_a"+i).value='';
      document.getElementById("IdCurso_a"+i).value='';      
    }    
  } 
  // $("#contador_auxiliar").val(contador_auxiliar);
  document.getElementById("contador_auxiliar").value=contador_auxiliar;//cantidad de items activados
}



function botonBuscarEmpresasCapacitacion(){
  iniciarCargaAjax();

  var valor_cod_empresa=$("#cod_empresa").val();    
  var valor_glosa=$("#glosa").val();   
  var q=$("#q").val();   
  var r=$("#r").val(); 

  var url='index.php?opcion=listFacturasServicios_costos_empresas&cod_empresa='+valor_cod_empresa+'&glosa='+valor_glosa;
   if(q!=0){    
    location.href=url+'&q='+q+'&r='+r;     
  }else{    
    location.href=url;     
  }
}

function actualizarRegistroNormas(){
    var parametros={"codigo":"none"};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "solicitud_facturacion_manual/ajax_actualizar_normas.php",
        data: parametros,
        beforeSend: function () {
        $("#texto_ajax_titulo").html("Actualizando Normas desde el Servicio Web..."); 
          iniciarCargaAjax();
        },
        success:  function (resp) {
           detectarCargaAjax();
           $("#texto_ajax_titulo").html("Procesando Datos");            
           // Swal.fire("Correcto!", "Los datos se actualizaron de forma correcta.", "success");
           alerts.showSwal('success-message','index.php?opcion=listSolicitud_facturacion_normas');
        }
    });
}

function itemsSeleccionados_ventaNormas(){  
  var sumal=0;  
  var total=$("#total_items").val();
  var comprobante_auxiliar=0;
  for (var i=1;i<=(total-1);i++){              
    var check=document.getElementById("modal_check"+i).checked;
    if(check) {//BUSACMOS LOS CHECK ACTIVOS
      comprobante_auxiliar=comprobante_auxiliar+1;        
      //sacamos los datos de los items que se activaron
      var idVentaNormas = document.getElementById("idVentaNormas"+i).value;      
      // aqui se guardan los items activados
      document.getElementById("idVentaNormas_a"+i).value=idVentaNormas;
    }else{
      document.getElementById("idVentaNormas_a"+i).value='';
    }
  } 
  document.getElementById("comprobante_auxiliar").value=comprobante_auxiliar;//cantidad de items activados
}


function ajaxUnidadorganizacionalAreaNormas(combo,aux){
  var contenedor;
  var codigo_UO=combo.value;
  contenedor = document.getElementById('div_contenedor_area');
  ajax=nuevoAjax();
  ajax.open('GET', '../solicitud_facturacion_manual/AjaxUnidad_area.php?codigo_UO='+codigo_UO+'&aux='+aux,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);        
    }
  }
  ajax.send(null)  
}//unidad_area-cargo

function ajaxTipoPagoContactoPersonal(combo){
  verificar_dias_credito();
  var cod_cliente=$("#cod_cliente").val();
  var contenedor;
  var cod_tipo=combo.value;
  contenedor = document.getElementById('div_contenedor_contactos');
  ajax=nuevoAjax();
  ajax.open('GET', 'simulaciones_servicios/ajax_tipopago_contacto.php?cod_tipo='+cod_tipo+'&cod_cliente='+cod_cliente,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);
    }
  }
  ajax.send(null)  
}
function ajaxTipoPagoContactoPersonal_normas(combo){
  verificar_dias_credito();
  var cod_cliente=$("#cod_cliente").val();
  var contenedor;
  var cod_tipo=combo.value;
  contenedor = document.getElementById('div_contenedor_contactos');
  ajax=nuevoAjax();
  ajax.open('GET', '../simulaciones_servicios/ajax_tipopago_contacto.php?cod_tipo='+cod_tipo+'&cod_cliente='+cod_cliente,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);        
    }
  }
  ajax.send(null)  
}
function verificar_dias_credito(){
  // alert("entre");
  var tipopago = document.getElementById("cod_tipopago").value; 
  var cod_defecto_cod_tipo_credito = document.getElementById("cod_defecto_cod_tipo_credito").value;   
  if(tipopago==cod_defecto_cod_tipo_credito){//comprobamos el primer digito de la cuenta     
    $(".dias_credito_x").show();    
  }else{
    $(".dias_credito_x").hide();
  }
}


function botonBuscarSolicitudes_conta(){
  var valor_uo=$("#OficinaBusqueda").val();
  var valor_cliente=$("#cliente").val();
  var valor_fi=$("#fechaBusquedaInicio").val();
  var valor_ff=$("#fechaBusquedaFin").val();
  // var valor_glosa=$("#glosaBusqueda").val();
  
  
  ajax=nuevoAjax();
  ajax.open('GET', 'simulaciones_servicios/ajax_buscardor_avanzado_conta.php?cod_uo='+valor_uo+'&cliente='+valor_cliente+'&fechaI='+valor_fi+'&fechaF='+valor_ff,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      var contenedor=$("#data_solicitudes_facturacion");
      contenedor.html(ajax.responseText);
      $("#modalBuscador").modal("hide");
    }
  }
  ajax.send(null)
}
function agregarDatosModalCuenta(datos){
  //console.log("datos: "+datos);
  var d=datos.split('/');
  document.getElementById("cod_tipopago").value=d[0];
  document.getElementById("tipo_pago").value=d[1];
  // document.getElementById("cod_cuenta").value=d[2];  
  //agregamos la cuenta si lo tuviese
  var cod_cuenta=d[2];
  var contenedor;  
  contenedor = document.getElementById('div_cuenta_contable_sol_fac');
  ajax=nuevoAjax();
  ajax.open('GET', 'simulaciones_servicios/ajax_cuenta_contable.php?cod_cuenta='+cod_cuenta,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);        
    }
  }
  ajax.send(null) 
}
function agregarDatosModalCuenta_areas(datos){
  //console.log("datos: "+datos);
  var d=datos.split('/');
  document.getElementById("cod_area").value=d[0];
  document.getElementById("tipo_pago").value=d[1];
  // document.getElementById("cod_cuenta").value=d[2];  
  //agregamos la cuenta si lo tuviese
  var cod_cuenta=d[2];
  var contenedor;  
  contenedor = document.getElementById('div_cuenta_contable_sol_fac_areas');
  ajax=nuevoAjax();
  ajax.open('GET', 'simulaciones_servicios/ajax_cuenta_contable_areas.php?cod_cuenta='+cod_cuenta,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);        
    }
  }
  ajax.send(null) 
}
function registrarCuentaAsociadaSOLFAC(cod_tipopago,cod_cuenta){
  $.ajax({
    type:"POST",
    data:"cod_tipopago="+cod_tipopago+"&cod_cuenta="+cod_cuenta,
    url:"simulaciones_servicios/plandecuentas_save.php",
    success:function(r){
      if(r==1){
        alerts.showSwal('success-message','index.php?opcion=listPlanCuentasSolicitudesFacturacion');
      }else{
        if(r==2){
          Swal.fire("Informativo!", "Seleccione un Cuenta Por favor", "warning");
        }else{
          alerts.showSwal('error-message','index.php?opcion=listPlanCuentasSolicitudesFacturacion');
        }
      } 
    }
  });
}
function registrarCuentaAsociadaSOLFAC_areas(cod_area,cod_cuenta){
  $.ajax({
    type:"POST",
    data:"cod_area="+cod_area+"&cod_cuenta="+cod_cuenta,
    url:"simulaciones_servicios/plandecuentasAreas_save.php",
    success:function(r){
      if(r==1){
        alerts.showSwal('success-message','index.php?opcion=listPlanCuentasAreas');
      }else{
        if(r==2){
          Swal.fire("Informativo!", "Seleccione un Cuenta Por favor", "warning");
        }else{
          alerts.showSwal('error-message','index.php?opcion=listPlanCuentasAreas');
        }
      } 
    }
  });
}
//tipo pago
var itemTipoPagos_facturacion=[];
var itemTipoPagos_facturacion_aux=[];
function agregarDatosModalTipoPagoFacturacion(sw_auxiliar){  
  // alert("llege");
  // var d=datos.split('/');
  var cod_tipopago=$("#cod_tipopago").val();
  if(sw_auxiliar==2){//capacitacion
    var monto_total=$("#modal_totalmontoserv_costo_a").val();  
  }else{
    var monto_total=$("#monto_total_a").val();    
  }
  if(monto_total<=0 || monto_total==null || monto_total==''){
    // $('#modalTipoPagoPorcentaje').modal('hide');
    Swal.fire("Informativo!", "El monto Total NO debe ser 0 o número negativo!", "warning");
  }else{
    $('#modalTipoPagoPorcentaje').modal('show');
    var contenedor;  
    contenedor = document.getElementById('div_cabecera_hidden_tipo_pago');
    ajax=nuevoAjax();
    ajax.open('GET', 'simulaciones_servicios/ajax_modal_tipopago_porcentaje.php?cod_tipopago='+cod_tipopago+'&monto_total='+monto_total,true);
    ajax.onreadystatechange=function() {
      if (ajax.readyState==4) {
        contenedor.innerHTML = ajax.responseText;
        $('.selectpicker').selectpicker(["refresh"]);  
        tablaGeneral_tipoPagos_solFac();     
      }
    }
    ajax.send(null)   
  } 
}
function agregarDatosModalTipoPagoFacturacionNormas(sw_auxiliar){   
  // alert("llege");
  // var d=datos.split('/');
  var cod_tipopago=$("#cod_tipopago").val();
  
  if(sw_auxiliar==2){//capacitacion
    var monto_total=$("#modal_totalmontoserv_costo_a").val();  
  }else{
    var monto_total=$("#monto_total_a").val();  
  }
  if(monto_total<=0 || monto_total==null || monto_total==''){
    // $('#modalTipoPagoPorcentaje').modal('hide');
    Swal.fire("Informativo!", "El monto Total NO debe ser 0 o número negativo!", "warning");
  }else{
    $('#modalTipoPagoPorcentaje').modal('show');
    var contenedor;  
    contenedor = document.getElementById('div_cabecera_hidden_tipo_pago');
    ajax=nuevoAjax();
    ajax.open('GET', '../simulaciones_servicios/ajax_modal_tipopago_porcentaje.php?cod_tipopago='+cod_tipopago+'&monto_total='+monto_total,true);
    ajax.onreadystatechange=function() {
      if (ajax.readyState==4) {
        contenedor.innerHTML = ajax.responseText;
        $('.selectpicker').selectpicker(["refresh"]);  
        tablaGeneral_tipoPagos_solFac();     
      }
    }
    ajax.send(null)   
  } 
}
function tablaGeneral_tipoPagos_solFac(){  
  var monto_total=$("#modal_totalmontos").val();
  var div=$('<div>').addClass('col-sm-12');
  var table = $('<table>').addClass('table table-bordered table-condensed table-sm');
  var titulos = $('<tr>').addClass('fondo-boton');
    titulos.append($('<th>').addClass('').text('#'));
    titulos.append($('<th>').addClass('').text('Tipo de Pago'));
    titulos.append($('<th>').addClass('').text('Porcentaje(%)'));    
    titulos.append($('<th>').addClass('').text('Monto(BOB)'));    
    table.append(titulos);
    for (var i = 0; i < itemTipoPagos_facturacion_aux[0].length; i++) {
      var nombre_x=itemTipoPagos_facturacion_aux[0][i].nombrex;
      var cod_tipopago_x=itemTipoPagos_facturacion_aux[0][i].cod_tipopago;
      var row = $('<tr>').addClass('');
      row.append($('<td>').addClass('').text(i+1));
      row.append($('<td>').addClass('').html("<input type='hidden' name='codigo_tipopago"+i+"' id='codigo_tipopago"+i+"' value='"+cod_tipopago_x+"'>"+nombre_x));
      var sw = verificaExistenciaCodigoTipoPago(cod_tipopago_x);
      // alert(sw);
      if(sw>=0){//verificamos si existe ese codigo en el array objetos
        //sacamos su valores        
        var objeto_tipopago = Object.values(itemTipoPagos_facturacion[0][sw]);
        //var dato = Object.values(itemAreas_facturacion[0][j]);
        var monto_por_obj=objeto_tipopago[1];
        var monto_bob_obj=objeto_tipopago[2];
        row.append($('<td>').addClass('').html("<input type='number' step='0.01' class='form-control' name='monto_porcentaje_tipopago"+i+"' id='monto_porcentaje_tipopago"+i+"' onkeyup='convertir_a_bolivianos_tipopago("+i+")' value='"+monto_por_obj+"'>"));
        row.append($('<td>').addClass('').html("<input type='number' class='form-control' name='monto_bob_tipopago"+i+"' id='monto_bob_tipopago"+i+"' onkeyup='convertir_a_porcentaje_tipopago("+i+")' value='"+monto_bob_obj+"'>"));        
      }else{        
        row.append($('<td>').addClass('').html("<input type='number' step='0.01' class='form-control' name='monto_porcentaje_tipopago"+i+"' id='monto_porcentaje_tipopago"+i+"' onkeyup='convertir_a_bolivianos_tipopago("+i+")' >"));      
        row.append($('<td>').addClass('').html("<input type='number' class='form-control' name='monto_bob_tipopago"+i+"' id='monto_bob_tipopago"+i+"' onkeyup='convertir_a_porcentaje_tipopago("+i+")'>"));
      }
      table.append(row);
    }
    var row = $('<tr>').addClass('');//parte de total
    row.append($('<td>').addClass('').text(''));
    row.append($('<td>').addClass('').text('TOTAL'));         
    row.append($('<td>').addClass('').html("<input type='hidden' class='form-control' name='total_monto_porcentaje_a_tipopago' id='total_monto_porcentaje_a_tipopago' value='0'><input type='text' step='0.01' class='form-control' name='total_monto_porcentaje_tipopago' id='total_monto_porcentaje_tipopago' value='"+number_format(0,2)+"' readonly='true'>"));
    row.append($('<td>').addClass('').html("<input type='hidden' step='0.01' class='form-control' name='total_monto_bob_a_tipopago' id='total_monto_bob_a_tipopago' value='0'><input type='text' step='0.01' class='form-control' name='total_monto_bob_tipopago' id='total_monto_bob_tipopago' value='"+number_format(0,2)+"' readonly='true'> "));    
    table.append(row);
    var row = $('<tr>').addClass('');//parte de la diferencia
    row.append($('<td>').addClass('').text(''));
    row.append($('<td>').addClass('').text('DIFERENCIA'));         
    row.append($('<td>').addClass('').html("<input style='background-color:#F5A9A9;' type='text' step='0.01' class='form-control' name='total_diferencia_porcentaje_tipopago' id='total_diferencia_porcentaje_tipopago' value='"+number_format(0,2)+"' readonly='true'>"));
    row.append($('<td>').addClass('').html("<input style='background-color:#F5A9A9;' type='text' step='0.01' class='form-control' name='total_diferencia_bob_tipopago' id='total_diferencia_bob_tipopago' value='"+number_format(0,2)+"' readonly='true'> "));    
    table.append(row);
    div.append(table);
    $("#total_items_tipopago").val(i);
    $('#divResultadoListaModalTiposPago').html(div);
    calcularTotalFilaTipoPagoModal();
}
function verificaExistenciaCodigoTipoPago(codigo){  
  var sw = -1;  
  for(var j = 0; j < itemTipoPagos_facturacion[0].length; j++){
    var dato = Object.values(itemTipoPagos_facturacion[0][j]);
    var cod_tipopago_x=dato[0];    
    if(cod_tipopago_x==codigo){
      // console.log("cod_x:"+cod_tipopago_x+" - cod:"+codigo);
      sw=j;
    }
    // console.log("sw_aux:"+sw);
    // console.log("dato: "+dato);
    // console.log("datos: "+dato[4]+" "+dato[7]+" "+dato[8]);    
  }   
  return sw;
}
function convertir_a_porcentaje_tipopago(id){ 
  // console.log("llega hasta aqui");
  var monto_precio=$("#monto_total_ingreso_tipopago").val();// precio de item

  var descuento_bob=$("#monto_bob_tipopago"+id).val();//monto de descuento Bob
  if(descuento_bob<0 || descuento_bob==0 || descuento_bob==null){
    // Swal.fire("Informativo!", "El monto del Item NO debe ser 0 o número negativo!", "warning");
    $("#monto_porcentaje_tipopago"+id).val(0);
    $("#monto_bob_tipopago"+id).val(0);
  }else{
    var numero_porcentaje=parseFloat(descuento_bob)*100/parseFloat(monto_precio);
    //alert(numero_porcentaje);
    $("#monto_porcentaje_tipopago"+id).val(numero_porcentaje.toFixed(2));
  }
  calcularTotalFilaTipoPagoModal();
}
function convertir_a_bolivianos_tipopago(id){
  var monto_precio=$("#monto_total_ingreso_tipopago").val();// precio de item
  var descuento_por=$("#monto_porcentaje_tipopago"+id).val();//monto de descuento %

  if(descuento_por<0 || descuento_por==0 || descuento_por==null){
    // Swal.fire("Informativo!", "El porcentaje del Item NO debe ser 0 o número negativo!", "warning");
    $("#monto_porcentaje_tipopago"+id).val(0);
    $("#monto_bob_tipopago"+id).val(0);
  }else{
    var monto_bob_porcentaje=parseFloat(descuento_por)*parseFloat(monto_precio)/100;
    //alert(monto_bob_porcentaje);
    $("#monto_bob_tipopago"+id).val(monto_bob_porcentaje.toFixed(2));
  }
  calcularTotalFilaTipoPagoModal();
}
function calcularTotalFilaTipoPagoModal(){  
  var suma_porcentaje=0;  
  var suma_bob=0;  
  var total_items= $("#total_items_tipopago").val();
  var comprobante_auxiliar=0;
  for (var i=0;i<=(total_items-1);i++){
    var monto_bob=$("#monto_bob_tipopago"+i).val();    
    var monto_porcentaje=$("#monto_porcentaje_tipopago"+i).val();
    // console.log(monto_porcentaje);
    if(monto_bob=='' || monto_bob==null)monto_bob=0;
    if(monto_porcentaje=='' || monto_porcentaje==null)monto_porcentaje=0;
    suma_porcentaje=suma_porcentaje+parseFloat(monto_porcentaje);
    suma_bob=suma_bob+parseFloat(monto_bob);
  }   
  $("#total_monto_porcentaje_tipopago").val(number_format(suma_porcentaje,2));//con formato
  $("#total_monto_bob_tipopago").val(number_format(suma_bob,2));//con formato
  $("#total_monto_porcentaje_a_tipopago").val(suma_porcentaje);//sin formato tipo hidden
  $("#total_monto_bob_a_tipopago").val(suma_bob);//sin formato tipo hidden
  //direncia
  var monto_precio=$("#monto_total_ingreso_tipopago").val();// precio de item unitario
  var diferencia_bob_tipopago = parseFloat(monto_precio) - suma_bob;
  var diferencia_porcentaje_tipopago = 100 - suma_porcentaje;
  $("#total_diferencia_porcentaje_tipopago").val(number_format(diferencia_porcentaje_tipopago,2));//con formato
  $("#total_diferencia_bob_tipopago").val(number_format(diferencia_bob_tipopago,2));//con formato
}
function savePorcentajeTipopago(){
  var total_porcentaje=$('#total_monto_porcentaje_a_tipopago').val();
  if(total_porcentaje==100){
    borrarItemsTipoPago(); //limpiamos el array de objeto para guardarlo nuevamente
    var total_items=$('#total_items_tipopago').val();
    for (var i=0;i<=(total_items-1);i++){
      var tipopago={
        codigo_tipopago: $('#codigo_tipopago'+i).val(),
        monto_porcentaje: $('#monto_porcentaje_tipopago'+i).val(),
        monto_bob: $('#monto_bob_tipopago'+i).val(),    
      }
      // console.log($('#monto_porcentaje_tipopago'+i).val());
      // console.log(tipopago);
      var monto_x=$('#monto_porcentaje_tipopago'+i).val();
      if(monto_x!=null && monto_x!=0 && monto_x!=''){
        itemTipoPagos_facturacion[0].push(tipopago);  
      }
    }  
    $("#nfac").html(itemTipoPagos_facturacion[0].length);
    $('#modalTipoPagoPorcentaje').modal('hide');
    // for(var j = 0; j < itemTipoPagos_facturacion[0].length; j++){
    //   var dato = Object.values(itemTipoPagos_facturacion[0][j]);
    //   console.log("dato: "+dato);
    //   // console.log("datos: "+dato[4]+" "+dato[7]+" "+dato[8]);    
    // }
  }else{
    Swal.fire("Informativo!", "EL porcentaje de los montos difiere del 100%", "warning");
  }   
}
function borrarItemsTipoPago(){
  itemTipoPagos_facturacion.splice(0, 1);
  var nfac=[];itemTipoPagos_facturacion.push(nfac);
}

var itemAreas_facturacion=[];//array que contiene objetos agregado
var itemAreas_facturacion_aux=[];//array que contiene todas las areas de servicios
var itemUnidades_facturacion=[];//array que contiene objetos agregados a un area
var itemUnidades_facturacion_aux=[];//array que contiene todas las unidades en general
function agregarDatosModalAreasFacturacion(sw_auxiliar){     
  var cod_area=$("#cod_area").val();  
  if(cod_area==null || cod_area==''){    
    Swal.fire("Informativo!", "Area no Encontrada, por favor seleccione Oficina y Area!", "warning");
  }else{
    
    if(sw_auxiliar==2){//capacitacion
      var monto_total=$("#modal_totalmontoserv_costo_a").val();  
    }else{
      var monto_total=$("#monto_total_a").val();
    }
    if(monto_total<=0){      
      Swal.fire("Informativo!", "El monto Total NO debe ser 0 o número negativo!", "warning");
    }else{
      $('#modalAreasPorcentaje').modal('show');
      //agregamos la cuenta si lo tuviese  
      var contenedor;  
      contenedor = document.getElementById('div_cabecera_hidden_areas');
      ajax=nuevoAjax();
      ajax.open('GET', 'simulaciones_servicios/ajax_modal_areas_porcentaje.php?cod_area='+cod_area+'&monto_total='+monto_total,true);
      ajax.onreadystatechange=function() {
        if (ajax.readyState==4) {
          contenedor.innerHTML = ajax.responseText;
          $('.selectpicker').selectpicker(["refresh"]);        
          tablaGeneral_areas_solFac();
        }
      }
      ajax.send(null);  
    } 
  }
}
function tablaGeneral_areas_solFac(){  
  var monto_total=$("#modal_totalmontos").val();//monto total de items
  var div=$('<div>').addClass('col-sm-12');
  var table = $('<table>').addClass('table table-bordered table-condensed table-sm');
  var titulos = $('<tr>').addClass('fondo-boton');
    titulos.append($('<th>').addClass('').text('#'));
    titulos.append($('<th width="50%">').addClass('').text('Area'));
    titulos.append($('<th>').addClass('').text('Porcentaje(%)'));    
    titulos.append($('<th>').addClass('').text('Monto(BOB)'));    
    titulos.append($('<th>').addClass('').text('OF'));    
    table.append(titulos);    
    for (var i = 0; i < itemAreas_facturacion_aux[0].length; i++) {
      var nombre_x=itemAreas_facturacion_aux[0][i].nombrex;
      var abrev_x=itemAreas_facturacion_aux[0][i].abrevx;
      var cod_area_x=itemAreas_facturacion_aux[0][i].cod_area;
      var row = $('<tr>').addClass('');
      row.append($('<td>').addClass('').text(i+1));
      row.append($('<td>').addClass('').html("<input type='hidden' name='codigo_areas"+i+"' id='codigo_areas"+i+"' value='"+cod_area_x+"'>"+nombre_x+" - "+abrev_x));
      var sw = verificaExistenciaCodigoArea(cod_area_x);
      
      if(sw>=0){//verificamos si existe ese codigo en el array objetos
        //sacamos su valores
        var objeto_area = Object.values(itemAreas_facturacion[0][sw]);
        var monto_por_obj=objeto_area[1];
        var monto_bob_obj=objeto_area[2];
        row.append($('<td>').addClass('').html("<input type='number' step='0.01' class='form-control' name='monto_porcentaje_areas"+i+"' id='monto_porcentaje_areas"+i+"' onkeyup='convertir_a_bolivianos_areas("+i+")' value='"+monto_por_obj+"'>"));
        row.append($('<td>').addClass('').html("<input type='number' class='form-control' name='monto_bob_areas"+i+"' id='monto_bob_areas"+i+"' onkeyup='convertir_a_porcentaje_areas("+i+")' value='"+monto_bob_obj+"'>"));        
      }else{              
        row.append($('<td>').addClass('').html("<input type='number' step='0.01' class='form-control' name='monto_porcentaje_areas"+i+"' id='monto_porcentaje_areas"+i+"' onkeyup='convertir_a_bolivianos_areas("+i+")'>"));
        row.append($('<td>').addClass('').html("<input type='number' class='form-control' name='monto_bob_areas"+i+"' id='monto_bob_areas"+i+"' onkeyup='convertir_a_porcentaje_areas("+i+")'>"));    

      }
      var cont_uo=verificamos_uo_areas(i);
      if(cont_uo>0){
        row.append($('<td>').addClass('').html("<button type='button' class='btn btn-primary btn-round btn-fab btn-sm' data-toggle='modal' data-target='' onclick='agregarDatosModalUnidadFacturacion("+i+")'><i class='material-icons' title='Unidades Porcentaje'>list</i><span id='nfacUnidades"+i+"' class='count bg-warning'>"+cont_uo+"</span></button>"));
        table.append(row);  
      }else{
        row.append($('<td>').addClass('').html("<button type='button' class='btn btn-primary btn-round btn-fab btn-sm' data-toggle='modal' data-target='' onclick='agregarDatosModalUnidadFacturacion("+i+")'><i class='material-icons' title='Unidades Porcentaje'>list</i><span id='nfacUnidades"+i+"' class='count bg-warning'></span></button>"));
        table.append(row);  
      }      
    }
    var row = $('<tr>').addClass('');//Para el total
    row.append($('<td>').addClass('').text(''));
    row.append($('<td>').addClass('').text('TOTAL'));         
    row.append($('<td>').addClass('').html("<input type='hidden' class='form-control' name='total_monto_porcentaje_a_areas' id='total_monto_porcentaje_a_areas' value='0'><input type='text' step='0.01' class='form-control' name='total_monto_porcentaje_areas' id='total_monto_porcentaje_areas' value='"+number_format(0,2)+"' readonly='true'>"));
    row.append($('<td>').addClass('').html("<input type='hidden' step='0.01' class='form-control' name='total_monto_bob_a_areas' id='total_monto_bob_a_areas' value='0'><input type='text' step='0.01' class='form-control' name='total_monto_bob_areas' id='total_monto_bob_areas' value='"+number_format(0,2)+"' readonly='true'> "));    
    table.append(row);
    var row = $('<tr>').addClass('');//parte de la diferencia
    row.append($('<td>').addClass('').text(''));
    row.append($('<td>').addClass('').text('DIFERENCIA'));         
    row.append($('<td>').addClass('').html("<input type='text' step='0.01' class='form-control' style='background-color:#F5A9A9;' name='total_diferencia_porcentaje_areas' id='total_diferencia_porcentaje_areas' value='"+number_format(0,2)+"' readonly='true'>"));
    row.append($('<td>').addClass('').html("<input type='text' step='0.01' class='form-control' style='background-color:#F5A9A9;' name='total_diferencia_bob_areas' id='total_diferencia_bob_areas' value='"+number_format(0,2)+"' readonly='true'> "));    
    table.append(row);
    div.append(table);
    $("#total_items_areas").val(i);
    $('#divResultadoListaModalAreas').html(div);
    calcularTotalFilaAreasModal();
}
function agregarDatosModalAreasFacturacionNormas(sw_auxiliar){     
  var cod_area=$("#cod_area").val();  
  if(cod_area==null || cod_area==''){    
    Swal.fire("Informativo!", "Area no Encontrada, por favor seleccione Oficina y Area!", "warning");
  }else{
    if(sw_auxiliar==2){//capacitacion
      var monto_total=$("#modal_totalmontoserv_costo_a").val();  
    }else{
      var monto_total=$("#monto_total_a").val();    
    }
    if(monto_total<=0){      
      Swal.fire("Informativo!", "El monto Total NO debe ser 0 o número negativo!", "warning");
    }else{
      $('#modalAreasPorcentaje').modal('show');
      //agregamos la cuenta si lo tuviese  
      var contenedor;  
      contenedor = document.getElementById('div_cabecera_hidden_areas');
      ajax=nuevoAjax();
      ajax.open('GET', '../simulaciones_servicios/ajax_modal_areas_porcentaje.php?cod_area='+cod_area+'&monto_total='+monto_total,true);
      ajax.onreadystatechange=function() {
        if (ajax.readyState==4) {
          contenedor.innerHTML = ajax.responseText;
          $('.selectpicker').selectpicker(["refresh"]);        
          tablaGeneral_areas_solFacNormas();
        }
      }
      ajax.send(null);  
    } 
  }
}
function tablaGeneral_areas_solFacNormas(){  
  var monto_total=$("#modal_totalmontos").val();//monto total de items
  var div=$('<div>').addClass('col-sm-12');
  var table = $('<table>').addClass('table table-bordered table-condensed table-sm');
  var titulos = $('<tr>').addClass('fondo-boton');
    titulos.append($('<th>').addClass('').text('#'));
    titulos.append($('<th width="50%">').addClass('').text('Area'));
    titulos.append($('<th>').addClass('').text('Porcentaje(%)'));    
    titulos.append($('<th>').addClass('').text('Monto(BOB)'));    
    titulos.append($('<th>').addClass('').text('OF'));    
    table.append(titulos);    
    for (var i = 0; i < itemAreas_facturacion_aux[0].length; i++) {
      var nombre_x=itemAreas_facturacion_aux[0][i].nombrex;
      var abrev_x=itemAreas_facturacion_aux[0][i].abrevx;
      var cod_area_x=itemAreas_facturacion_aux[0][i].cod_area;
      var row = $('<tr>').addClass('');
      row.append($('<td>').addClass('').text(i+1));
      row.append($('<td>').addClass('').html("<input type='hidden' name='codigo_areas"+i+"' id='codigo_areas"+i+"' value='"+cod_area_x+"'>"+nombre_x+" - "+abrev_x));
      var sw = verificaExistenciaCodigoArea(cod_area_x);
      
      if(sw>=0){//verificamos si existe ese codigo en el array objetos
        //sacamos su valores
        var objeto_area = Object.values(itemAreas_facturacion[0][sw]);
        var monto_por_obj=objeto_area[1];
        var monto_bob_obj=objeto_area[2];
        row.append($('<td>').addClass('').html("<input type='number' step='0.01' class='form-control' name='monto_porcentaje_areas"+i+"' id='monto_porcentaje_areas"+i+"' onkeyup='convertir_a_bolivianos_areas("+i+")' value='"+monto_por_obj+"'>"));
        row.append($('<td>').addClass('').html("<input type='number' class='form-control' name='monto_bob_areas"+i+"' id='monto_bob_areas"+i+"' onkeyup='convertir_a_porcentaje_areas("+i+")' value='"+monto_bob_obj+"'>"));        
      }else{              
        row.append($('<td>').addClass('').html("<input type='number' step='0.01' class='form-control' name='monto_porcentaje_areas"+i+"' id='monto_porcentaje_areas"+i+"' onkeyup='convertir_a_bolivianos_areas("+i+")'>"));
        row.append($('<td>').addClass('').html("<input type='number' class='form-control' name='monto_bob_areas"+i+"' id='monto_bob_areas"+i+"' onkeyup='convertir_a_porcentaje_areas("+i+")'>"));    

      }
      var cont_uo=verificamos_uo_areas(i);
      if(cont_uo>0){
        row.append($('<td>').addClass('').html("<button type='button' class='btn btn-primary btn-round btn-fab btn-sm' data-toggle='modal' data-target='' onclick='agregarDatosModalUnidadFacturacionNormas("+i+")'><i class='material-icons' title='Unidades Porcentaje'>list</i><span id='nfacUnidades"+i+"' class='count bg-warning'>"+div_contenedor_uo+"</span></button>"));
        table.append(row);  
      }else{
        row.append($('<td>').addClass('').html("<button type='button' class='btn btn-primary btn-round btn-fab btn-sm' data-toggle='modal' data-target='' onclick='agregarDatosModalUnidadFacturacionNormas("+i+")'><i class='material-icons' title='Unidades Porcentaje'>list</i><span id='nfacUnidades"+i+"' class='count bg-warning'></span></button>"));
        table.append(row);  
      }      
    }
    var row = $('<tr>').addClass('');//Para el total
    row.append($('<td>').addClass('').text(''));
    row.append($('<td>').addClass('').text('TOTAL'));         
    row.append($('<td>').addClass('').html("<input type='hidden' class='form-control' name='total_monto_porcentaje_a_areas' id='total_monto_porcentaje_a_areas' value='0'><input type='text' step='0.01' class='form-control' name='total_monto_porcentaje_areas' id='total_monto_porcentaje_areas' value='"+number_format(0,2)+"' readonly='true'>"));
    row.append($('<td>').addClass('').html("<input type='hidden' step='0.01' class='form-control' name='total_monto_bob_a_areas' id='total_monto_bob_a_areas' value='0'><input type='text' step='0.01' class='form-control' name='total_monto_bob_areas' id='total_monto_bob_areas' value='"+number_format(0,2)+"' readonly='true'> "));    
    table.append(row);
    var row = $('<tr>').addClass('');//parte de la diferencia
    row.append($('<td>').addClass('').text(''));
    row.append($('<td>').addClass('').text('DIFERENCIA'));         
    row.append($('<td>').addClass('').html("<input style='background-color:#F5A9A9;' type='text' step='0.01' class='form-control' name='total_diferencia_porcentaje_areas' id='total_diferencia_porcentaje_areas' value='"+number_format(0,2)+"' readonly='true'>"));
    row.append($('<td>').addClass('').html("<input style='background-color:#F5A9A9;' type='text' step='0.01' class='form-control' name='total_diferencia_bob_areas' id='total_diferencia_bob_areas' value='"+number_format(0,2)+"' readonly='true'> "));    
    table.append(row);
    div.append(table);
    $("#total_items_areas").val(i);
    $('#divResultadoListaModalAreas').html(div);
    calcularTotalFilaAreasModal();
}
function verificaExistenciaCodigoArea(codigo){  
  var sw = -1;  
  for(var j = 0; j < itemAreas_facturacion[0].length; j++){
    var dato = Object.values(itemAreas_facturacion[0][j]);
    var cod_area_x=dato[0];
    if(cod_area_x==codigo){
      sw=j;
    }
    // console.log("dato: "+dato);
    // console.log("datos: "+dato[4]+" "+dato[7]+" "+dato[8]);    
  }   
  return sw;
}
function verificamos_uo_areas(id){
  var sw = -1;
  var cont_item=itemUnidades_facturacion[id].length;
  if(cont_item>0){
    sw=cont_item;  
  }
  return sw;
}
function convertir_a_porcentaje_areas(id){  
  var monto_precio=$("#monto_total_ingreso_areas").val();// precio de item
  var descuento_bob=$("#monto_bob_areas"+id).val();//monto de descuento Bob
  if(descuento_bob<0 || descuento_bob==0 || descuento_bob==null){
    // Swal.fire("Informativo!", "El monto del Item NO debe ser 0 o número negativo!", "warning");
    $("#monto_porcentaje_areas"+id).val(0);
    $("#monto_bob_areas"+id).val(0);
  }else{
    var numero_porcentaje=parseFloat(descuento_bob)*100/parseFloat(monto_precio);
    //alert(numero_porcentaje);
    $("#monto_porcentaje_areas"+id).val(numero_porcentaje.toFixed(2));  
  }
  calcularTotalFilaAreasModal();
}
function convertir_a_bolivianos_areas(id){  
  var monto_precio=$("#monto_total_ingreso_areas").val();// precio de item
  var descuento_por=$("#monto_porcentaje_areas"+id).val();//monto de descuento %
  if(descuento_por<0 || descuento_por==0 || descuento_por==null){
    // Swal.fire("Informativo!", "El porcentaje del Item NO debe ser 0 o número negativo!", "warning");
    $("#monto_porcentaje_areas"+id).val(0);
    $("#monto_bob_areas"+id).val(0);
  }else{
    var monto_bob_porcentaje=parseFloat(descuento_por)*parseFloat(monto_precio)/100;
    //alert(monto_bob_porcentaje);
    $("#monto_bob_areas"+id).val(monto_bob_porcentaje.toFixed(2));    
  }
  calcularTotalFilaAreasModal();
}
function calcularTotalFilaAreasModal(){  
  var suma_porcentaje=0;  
  var suma_bob=0;  
  var total_items= $("#total_items_areas").val();
  var comprobante_auxiliar=0;
  for (var i=0;i<=(total_items-1);i++){
    var monto_bob=$("#monto_bob_areas"+i).val();    
    var monto_porcentaje=$("#monto_porcentaje_areas"+i).val();
    if(monto_bob=='' || monto_bob==null)monto_bob=0;
    if(monto_porcentaje=='' || monto_porcentaje==null)monto_porcentaje=0;
    suma_porcentaje=suma_porcentaje+parseFloat(monto_porcentaje);
    suma_bob=suma_bob+parseFloat(monto_bob);
  }   
  $("#total_monto_porcentaje_areas").val(number_format(suma_porcentaje,2));//con formato 
  $("#total_monto_bob_areas").val(number_format(suma_bob,2));//con formato 
  $("#total_monto_porcentaje_a_areas").val(suma_porcentaje);//sin formato tipo hidden
  $("#total_monto_bob_a_areas").val(suma_bob);//sin formato tipo hidden

  //direncia
  var monto_precio=$("#monto_total_ingreso_areas").val();// precio de item unitario
  var diferencia_bob_areas = parseFloat(monto_precio) - suma_bob;
  var diferencia_porcentaje_areas = 100 - suma_porcentaje;
  $("#total_diferencia_porcentaje_areas").val(number_format(diferencia_porcentaje_areas,2));//con formato
  $("#total_diferencia_bob_areas").val(number_format(diferencia_bob_areas,2));//con formato
}
function savePorcentajeAreas(){
  var porcentaje_dif=$('#total_diferencia_porcentaje_areas').val();
  var bob_dif=$('#total_diferencia_bob_areas').val();
  if(porcentaje_dif==0 && bob_dif==0){
    borrarItemsAreas(); 
    var total_items=$('#total_items_areas').val();
    for (var i=0;i<=(total_items-1);i++){
      var area={
        codigo_areas: $('#codigo_areas'+i).val(),
        monto_porcentaje: $('#monto_porcentaje_areas'+i).val(),
        monto_bob: $('#monto_bob_areas'+i).val(),    
      }
      // console.log($('#monto_porcentaje_areas'+i).val());
      var monto_x=$('#monto_porcentaje_areas'+i).val();
      // if(monto_x!=null && monto_x!=0 && monto_x!=''){
        itemAreas_facturacion[0].push(area);  
      // }
    }  
    //$("#nfacAreas").html(itemAreas_facturacion[0].length);
    $("#nfacAreas").addClass("estado");
    $('#modalAreasPorcentaje').modal('hide');

    // for(var j = 0; j < itemAreas_facturacion[0].length; j++){
    //   var dato = Object.values(itemAreas_facturacion[0][j]);
    //   // console.log("dato: "+dato);
    //   // console.log("datos: "+dato[4]+" "+dato[7]+" "+dato[8]);    
    // }
  }else{
    Swal.fire("Informativo!", "Verifique porcentajes y montos por favor", "warning");
  }      
}
function borrarItemsAreas(){
  itemAreas_facturacion.splice(0, 1);
  var nfacAreas=[];itemAreas_facturacion.push(nfacAreas);
}
function agregarDatosModalUnidadFacturacion(id){    
  var porcentaje_dif=$('#total_diferencia_porcentaje_areas').val();
  var bob_dif=$('#total_diferencia_bob_areas').val();
  // alert(porcentaje_dif+"-"+bob_dif);
  var porcentaje_area=$("#monto_porcentaje_areas"+id).val();    
  var monto_total=$("#monto_total_a").val();    
  var codigo_area=$("#codigo_areas"+id).val();
  if(porcentaje_area==null || porcentaje_area<0 || porcentaje_area==''){    
    Swal.fire("Informativo!", "Porcentaje de Area no Encontrada, por favor Introduzca Porcentajes!", "warning");
  }else{
    if(porcentaje_dif==0 && bob_dif==0){
      $('#modalUnidadesPorcentaje').modal('show');
      //agregamos la cuenta si lo tuviese  
      var contenedor;  
      contenedor = document.getElementById('div_cabecera_hidden_unidad');
      ajax=nuevoAjax();
      ajax.open('GET', 'simulaciones_servicios/ajax_modal_unidades_porcentaje.php?porcentaje_area='+porcentaje_area+'&monto_total='+monto_total+'&id='+id+'&codigo_area='+codigo_area,true);
      ajax.onreadystatechange=function() {
        if (ajax.readyState==4) {
          contenedor.innerHTML = ajax.responseText;
          // $('.selectpicker').selectpicker(["refresh"]);        
          tablaGeneral_unidad_solFac(porcentaje_area,id);
        }
      }
      ajax.send(null); 
    }else{
      Swal.fire("Informativo!", "Por favor, verifique que los porcentajes y montos estén correctamente!", "warning");     
    }
      
  }
}
function agregarDatosModalUnidadFacturacionNormas(id){ 
  var porcentaje_dif=$('#total_diferencia_porcentaje_areas').val();
  var bob_dif=$('#total_diferencia_bob_areas').val();   
  var porcentaje_area=$("#monto_porcentaje_areas"+id).val();    
  var monto_total=$("#monto_total_a").val();    
  var codigo_area=$("#codigo_areas"+id).val();
  if(porcentaje_area==null || porcentaje_area<0 || porcentaje_area==''){    
    Swal.fire("Informativo!", "Porcentaje de Area no Encontrada, por favor Introduzca Porcentajes!", "warning");
  }else{
    if(porcentaje_dif==0 && bob_dif==0){
      $('#modalUnidadesPorcentaje').modal('show');
      //agregamos la cuenta si lo tuviese  
      var contenedor;  
      contenedor = document.getElementById('div_cabecera_hidden_unidad');
      ajax=nuevoAjax();
      ajax.open('GET', '../simulaciones_servicios/ajax_modal_unidades_porcentaje.php?porcentaje_area='+porcentaje_area+'&monto_total='+monto_total+'&id='+id+'&codigo_area='+codigo_area,true);
      ajax.onreadystatechange=function() {
        if (ajax.readyState==4) {
          contenedor.innerHTML = ajax.responseText;
          $('.selectpicker').selectpicker(["refresh"]);        
          tablaGeneral_unidad_solFac(porcentaje_area,id);
        }
      }
      ajax.send(null);
    }else{
      Swal.fire("Informativo!", "Por favor, verifique que los porcentajes y montos estén correctamente!", "warning");     
    }
  }
}
function tablaGeneral_unidad_solFac(porcentaje_area,id){  
  var monto_total=$("#modal_totalmontos").val();//monto total de items
  $("#id_area").val(id);//guardamos el id del area
  monto_total=porcentaje_area*monto_total/100;//el monto total lo convertimos al monto del porcentaje  
  var div=$('<div>').addClass('col-sm-12');
  var table = $('<table>').addClass('table table-condensed table-sm');
  var titulos = $('<tr>').addClass('fondo-boton');
    titulos.append($('<th>').addClass('').text('#'));
    titulos.append($('<th>').addClass('').text('Oficina'));
    titulos.append($('<th>').addClass('').text('Porcentaje(%)'));    
    titulos.append($('<th>').addClass('').text('Monto(BOB)'));    
    table.append(titulos);    
    var monto_total_area_bob=0;
    var monto_total_area_por=0;
    for (var i = 0; i < itemUnidades_facturacion_aux[0].length; i++) {
      var nombre_x=itemUnidades_facturacion_aux[0][i].nombrex;
      var cod_unidad_x=itemUnidades_facturacion_aux[0][i].cod_unidad;
      var row = $('<tr>').addClass('');
      row.append($('<td>').addClass('').text(i+1));
      row.append($('<td>').addClass('').html("<input type='hidden' name='codigo_unidad"+i+"' id='codigo_unidad"+i+"' value='"+cod_unidad_x+"'>"+nombre_x));
      var sw = verificaExistenciaCodigoUnidad(cod_unidad_x,id);      
      if(sw>=0){//verificamos si existe ese codigo en el array objetos
        $("#nfacUnidades"+id).html(itemUnidades_facturacion[id].length);//muestar numero de itms en esa area
        //sacamos su valores
        var objeto_unidad = Object.values(itemUnidades_facturacion[id][sw]);
        var monto_por_obj=objeto_unidad[1];
        var monto_bob_obj=objeto_unidad[2];
        monto_total_area_por+=monto_por_obj;
        monto_total_area_bob+=monto_bob_obj;
        row.append($('<td>').addClass('').html("<input type='number' step='0.01' class='form-control' name='monto_porcentaje_unidades"+i+"' id='monto_porcentaje_unidades"+i+"' onkeyup='convertir_a_bolivianos_unidades("+i+")' value='"+monto_por_obj+"'>"));
        row.append($('<td>').addClass('').html("<input type='number' class='form-control' name='monto_bob_unidades"+i+"' id='monto_bob_unidades"+i+"' onkeyup='convertir_a_porcentaje_unidades("+i+")' value='"+monto_bob_obj+"'>"));        
      }else{              
        row.append($('<td>').addClass('').html("<input type='number' step='0.01' class='form-control' name='monto_porcentaje_unidades"+i+"' id='monto_porcentaje_unidades"+i+"' onkeyup='convertir_a_bolivianos_unidades("+i+")'>"));
        row.append($('<td>').addClass('').html("<input type='number' class='form-control' name='monto_bob_unidades"+i+"' id='monto_bob_unidades"+i+"' onkeyup='convertir_a_porcentaje_unidades("+i+")'>"));    
      }   
      table.append(row);
    }
    var row = $('<tr>').addClass('');//Para el total
    row.append($('<td>').addClass('').text(''));
    row.append($('<td>').addClass('').text('TOTAL'));         
    row.append($('<td>').addClass('').html("<input type='hidden' class='form-control' name='total_monto_porcentaje_a_unidades' id='total_monto_porcentaje_a_unidades' value='0'><input type='text' step='0.01' class='form-control' name='total_monto_porcentaje_unidades' id='total_monto_porcentaje_unidades' value='"+number_format(0,2)+"' readonly='true'>"));
    row.append($('<td>').addClass('').html("<input type='hidden' step='0.01' class='form-control' name='total_monto_bob_a_unidades' id='total_monto_bob_a_unidades' value='0'><input type='text' step='0.01' class='form-control' name='total_monto_bob_unidades' id='total_monto_bob_unidades' value='"+number_format(0,2)+"' readonly='true'> "));    
    table.append(row);
    var row = $('<tr>').addClass('');//parte de la diferencia
    row.append($('<td>').addClass('').text(''));
    row.append($('<td>').addClass('').text('DIFERENCIA'));         
    row.append($('<td>').addClass('').html("<input style='background-color:#F5A9A9;' type='text' step='0.01' class='form-control' name='total_diferencia_porcentaje_unidades' id='total_diferencia_porcentaje_unidades' value='"+number_format(0,2)+"' readonly='true'>"));
    row.append($('<td>').addClass('').html("<input style='background-color:#F5A9A9;' type='text' step='0.01' class='form-control' name='total_diferencia_bob_unidades' id='total_diferencia_bob_unidades' value='"+number_format(0,2)+"' readonly='true'> "));    
    table.append(row);    
    div.append(table);
    $("#total_items_unidades").val(i);
    $('#divResultadoListaModalUnidades').html(div);
    calcularTotalFilaUnidadesModal();
}
function convertir_a_porcentaje_unidades(id){  
  var monto_precio=$("#monto_total_ingreso_unidades").val();// precio de item
  var descuento_bob=$("#monto_bob_unidades"+id).val();//monto de descuento Bob
  if(descuento_bob<0 || descuento_bob==0 || descuento_bob==null){
    // Swal.fire("Informativo!", "El monto del Item NO debe ser 0 o número negativo!", "warning");
    $("#monto_porcentaje_unidades"+id).val(0);
    $("#monto_bob_unidades"+id).val(0);
  }else{
    var numero_porcentaje=parseFloat(descuento_bob)*100/parseFloat(monto_precio);
    //alert(numero_porcentaje);
    $("#monto_porcentaje_unidades"+id).val(numero_porcentaje.toFixed(2));  
  }
  calcularTotalFilaUnidadesModal();
}
function convertir_a_bolivianos_unidades(id){  
  var monto_precio=$("#monto_total_ingreso_unidades").val();// precio de item
  var descuento_por=$("#monto_porcentaje_unidades"+id).val();//monto de descuento %
  if(descuento_por<0 || descuento_por==0 || descuento_por==null){
    // Swal.fire("Informativo!", "El porcentaje del Item NO debe ser 0 o número negativo!", "warning");
    $("#monto_porcentaje_unidades"+id).val(0);
    $("#monto_bob_unidades"+id).val(0);
  }else{
    var monto_bob_porcentaje=parseFloat(descuento_por)*parseFloat(monto_precio)/100;
    //alert(monto_bob_porcentaje);
    $("#monto_bob_unidades"+id).val(monto_bob_porcentaje.toFixed(2));    
  }
  calcularTotalFilaUnidadesModal();
}
function calcularTotalFilaUnidadesModal(){  
  var suma_porcentaje=0;  
  var suma_bob=0;  
  var total_items= $("#total_items_unidades").val();
  var comprobante_auxiliar=0;
  for (var i=0;i<=(total_items-1);i++){
    var monto_bob=$("#monto_bob_unidades"+i).val();    
    var monto_porcentaje=$("#monto_porcentaje_unidades"+i).val();
    if(monto_bob=='' || monto_bob==null)monto_bob=0;
    if(monto_porcentaje=='' || monto_porcentaje==null)monto_porcentaje=0;
    suma_porcentaje=suma_porcentaje+parseFloat(monto_porcentaje);
    suma_bob=suma_bob+parseFloat(monto_bob);
  }   
  $("#total_monto_porcentaje_unidades").val(number_format(suma_porcentaje,2));//con formato 
  $("#total_monto_bob_unidades").val(number_format(suma_bob,2));//con formato 
  $("#total_monto_porcentaje_a_unidades").val(suma_porcentaje);//sin formato tipo hidden
  $("#total_monto_bob_a_unidades").val(suma_bob);//sin formato tipo hidden

  //direncia
  var monto_precio=$("#monto_total_ingreso_unidades").val();// precio de item unitario
  var diferencia_bob_areas = parseFloat(monto_precio) - suma_bob;
  var diferencia_porcentaje_areas = 100 - suma_porcentaje;
  $("#total_diferencia_porcentaje_unidades").val(number_format(diferencia_porcentaje_areas,2));//con formato
  $("#total_diferencia_bob_unidades").val(number_format(diferencia_bob_areas,2));//con formato
}
function savePorcentajeUnidades(){
  var id_area=$('#id_area').val();  
  var porcentaje_dif=$('#total_diferencia_porcentaje_unidades').val();
  var bob_dif=$('#total_diferencia_bob_unidades').val();
  if(porcentaje_dif==0 && bob_dif==0){
    // console.log("id: "+id_area);
    borrarItemsUnidades(id_area); 
    var total_items=$('#total_items_unidades').val();
    for (var i=0;i<=(total_items-1);i++){
      var unidad={
        codigo_unidad: $('#codigo_unidad'+i).val(),
        monto_porcentaje: $('#monto_porcentaje_unidades'+i).val(),
        monto_bob: $('#monto_bob_unidades'+i).val(),    
      }
      // console.log($('#monto_porcentaje_areas'+i).val());
      var monto_x=$('#monto_porcentaje_unidades'+i).val();
      if(monto_x!=null && monto_x!=0 && monto_x!=''){
        itemUnidades_facturacion[id_area].push(unidad);          
      }
    }  
    $("#nfacUnidades"+id_area).html(itemUnidades_facturacion[id_area].length);
    $('#modalUnidadesPorcentaje').modal('hide');
    // console.log("guardamos: "+itemUnidades_facturacion[0].length);
    // for(var j = 0; j < itemUnidades_facturacion[0].length; j++){
    //   var dato = Object.values(itemUnidades_facturacion[0][j]);
    //    console.log("dato: "+dato);
    //   // console.log("datos: "+dato[4]+" "+dato[7]+" "+dato[8]);    
    // }
  }else{
    Swal.fire("Informativo!", "Verifique porcentajes y montos por favor.", "warning");
  }      
}
function borrarItemsUnidades(id){
  var cont=itemUnidades_facturacion[id].length;
  if(cont>0){
    var nfacUnidades=[];
    itemUnidades_facturacion.splice(id,1,nfacUnidades);
    // var nfacUnidades=[];itemUnidades_facturacion[id].push(nfacUnidades); 
  }
}
function verificaExistenciaCodigoUnidad(codigo,id){  
  var sw = -1;  
  for(var j = 0; j < itemUnidades_facturacion[id].length; j++){
    var dato = Object.values(itemUnidades_facturacion[id][j]);
    var cod_unidades_x=dato[0];
    if(cod_unidades_x==codigo){
      sw=j;
    }
    // console.log("dato: "+dato);
    // console.log("datos: "+dato[4]+" "+dato[7]+" "+dato[8]);    
  }   
  return sw;
}




var itemDistOficina=[];
var itemDistArea=[];
function cargarDistribucionSol(valor){
  $("#nueva_distribucion").val(valor);
  switch (valor){
    case 1:
    cargarTablaDistribucion(itemDistOficina,0);
    $("#titulo_distribucion").html("x OFICINA");
    break;
    case 2:
    cargarTablaDistribucion(0,itemDistArea);
    $("#titulo_distribucion").html("x AREA");
    break;
    case 3:
    cargarTablaDistribucion(itemDistOficina,itemDistArea);
    $("#titulo_distribucion").html("x OFICINA y x Area");
    break;
    case 0:
    quitarDistribucionSolicitud();
    break;
  }
}
function cargarTablaDistribucion(array1,array2){  
  console.log(JSON.stringify(array1));
  console.log(JSON.stringify(array2));
   listarArrayTabla(array1,'cuerpo_tabladistofi');
   listarArrayTabla(array2,'cuerpo_tabladistarea');
   $("#modalDistribucionSol").modal("show");
}
function listarArrayTabla(array,cuerpo){
  $("#"+cuerpo).html('');
  var table = $("#"+cuerpo);
  for (var i = 0; i < array.length; i++) {
     var row = $('<tr>').addClass('');
     row.append($('<td>').addClass('').text(i+1));
     row.append($('<td>').addClass('font-weight-bold text-left').text(array[i].nombre));
     row.append($('<td>').addClass('').html('<input type="number" onkeyup="calcularTotalesSolicitudDistribucion()" onkeydown="calcularTotalesSolicitudDistribucion()" step="0.01" class="form-control text-right" id="'+cuerpo+'_'+(i+1)+'" value="'+redondeo(array[i].porcentaje)+'">'));    
     table.append(row);
   }   
   var row = $('<tr>').addClass('bg-plomo');
     row.append($('<td>').addClass('').text(""));
     row.append($('<td>').addClass('text-left font-weight-bold').text("Total"));
     row.append($('<td>').addClass('text-right font-weight-bold').text('0').attr("id","total_"+cuerpo));    
     table.append(row);
   calcularTotalesSolicitudDistribucion();  
}
function saveDistribucionSolicitudRecurso(){
  if(!($("#distrib_icon").hasClass("estado"))){
    $("#distrib_icon").addClass("estado");
   }

  for (var i = 0; i < itemDistOficina.length; i++) {  
   if($("#cuerpo_tabladistofi_"+(i+1)).length>0){
    if($("#cuerpo_tabladistofi_"+(i+1)).val()==""||$("#cuerpo_tabladistofi_"+(i+1)).val()<0){
      var valor = 0;
    }else{
      var valor = $("#cuerpo_tabladistofi_"+(i+1)).val();
    }
    itemDistOficina[i].porcentaje=valor;
   }
  }

  for (var i = 0; i < itemDistArea.length; i++) {  
   if($("#cuerpo_tabladistarea_"+(i+1)).length>0){
    if($("#cuerpo_tabladistarea_"+(i+1)).val()==""||$("#cuerpo_tabladistarea_"+(i+1)).val()<0){
      var valor = 0;
    }else{
      var valor = $("#cuerpo_tabladistarea_"+(i+1)).val();
    }
    itemDistArea[i].porcentaje=valor;
   }
  }
  $("#modalDistribucionSol").modal("hide");
  $("#boton_titulodist").html($("#titulo_distribucion").html());
  $("#n_distribucion").val($("#nueva_distribucion").val());
}

function guardarDistribucionSolicitudRecurso(){
  var sumaOfi=0; var sumaArea=0;
  for (var i = 0; i < itemDistOficina.length; i++) {  
   if($("#cuerpo_tabladistofi_"+(i+1)).length>0){
    if(!($("#cuerpo_tabladistofi_"+(i+1)).val()==""||$("#cuerpo_tabladistofi_"+(i+1)).val()<0)){
      sumaOfi+=parseFloat($("#cuerpo_tabladistofi_"+(i+1)).val())
    }
   }
  }
  for (var i = 0; i < itemDistArea.length; i++) {  
   if($("#cuerpo_tabladistarea_"+(i+1)).length>0){
    if(!($("#cuerpo_tabladistarea_"+(i+1)).val()==""||$("#cuerpo_tabladistarea_"+(i+1)).val()<0)){
      sumaArea+=parseFloat($("#cuerpo_tabladistarea_"+(i+1)).val())
    }
   }
  }
  if((itemDistArea.length==0||itemDistOficina.length==0)&&$("#titulo_distribucion").html()=="x OFICINA y x Area"){
    Swal.fire("Informativo!", "Debe existir distribucion para Area y Oficina", "warning");
  }else{
   if(sumaOfi!=100&&sumaOfi!=0){
    Swal.fire("Informativo!", "El porcentaje Total de Oficina debe ser 100 !", "warning");
   }else{
    if(sumaArea!=100&&sumaArea!=0){
     Swal.fire("Informativo!", "El porcentaje Total de Area debe ser 100 !", "warning");
    }else{
       saveDistribucionSolicitudRecurso();
    }
   }    
  }
}

function calcularTotalesSolicitudDistribucion(){
  var sumaOfi=0; var sumaArea=0;
  for (var i = 0; i < itemDistOficina.length; i++) {  
   if($("#cuerpo_tabladistofi_"+(i+1)).length>0){
    if(!($("#cuerpo_tabladistofi_"+(i+1)).val()==""||$("#cuerpo_tabladistofi_"+(i+1)).val()<0)){
      sumaOfi+=parseFloat($("#cuerpo_tabladistofi_"+(i+1)).val())
    }
   }
  }
  for (var i = 0; i < itemDistArea.length; i++) {  
   if($("#cuerpo_tabladistarea_"+(i+1)).length>0){
    if(!($("#cuerpo_tabladistarea_"+(i+1)).val()==""||$("#cuerpo_tabladistarea_"+(i+1)).val()<0)){
      sumaArea+=parseFloat($("#cuerpo_tabladistarea_"+(i+1)).val())
    }
   }
  }
 if($("#total_cuerpo_tabladistofi").length>0){
   $("#total_cuerpo_tabladistofi").text(redondeo(sumaOfi));
 }
 if($("#total_cuerpo_tabladistarea").length>0){
   $("#total_cuerpo_tabladistarea").text(redondeo(sumaArea));
 }
}
function quitarDistribucionSolicitud(){
  $("#nueva_distribucion").val(0);
  $("#n_distribucion").val(0);
  if($("#distrib_icon").hasClass("estado")){
    $("#distrib_icon").removeClass("estado");
   }
  $("#boton_titulodist").html("Distribución"); 
}

function agregarTipoPagoProveedorDetalle(fila){
  $("#fila_pago").val(fila);
  if($("#proveedor"+fila).val()>0){
      var proveedor = $("#proveedor"+fila).val();
      var parametros={"codigo":proveedor};
      $.ajax({
        type: "GET",
        dataType: 'html',
        url: "ajax_datos_bancarios.php",
        data: parametros,
        beforeSend: function () {
        $("#texto_ajax_titulo").html("Cargando los datos del proveedor..."); 
          iniciarCargaAjax();
        },
        success:  function (resp) {
          $("#cuenta_bancaria").html(resp);
           detectarCargaAjax();
           $("#tipo_pagoproveedor").val($("#cod_tipopago"+fila).val());
           $("#texto_ajax_titulo").html("Procesando Datos");   
           if(!($("#nben"+fila).hasClass("estado"))){
              $("#nombre_beneficiario").val('');
              $("#apellido_beneficiario").val('');
              $("#cuenta_beneficiario").val('');
              $("#tipo_pagoproveedor").val('');
           }else{
              $("#nombre_beneficiario").val($("#nombre_beneficiario"+fila).val());
              $("#apellido_beneficiario").val($("#apellido_beneficiario"+fila).val());
              $("#cuenta_beneficiario").val($("#cuenta_beneficiario"+fila).val());
              $("#cuenta_bancaria").val($("#cod_cuentaBancaria"+fila).val());
              $("#tipo_pagoproveedor").val($("#cod_tipopago"+fila).val());
           } 
           $('.selectpicker').selectpicker("refresh");       
           $("#modalTipoPagoSolicitud").modal("show");         
        }
      });   
  }else{
    Swal.fire("Informativo!", "Debe seleccionar un proveedor!", "warning");
  }
}
function guardarFormaPagoSolicitud(){
  var fila = $("#fila_pago").val();
  if($("#tipo_pagoproveedor").val()>0&&$("#nombre_beneficiario").val()!=""&&$("#apellido_beneficiario").val()!=""){
  $("#cod_cuentaBancaria"+fila).val($("#cuenta_bancaria").val());
  $("#cod_tipopago"+fila).val($("#tipo_pagoproveedor").val());
  $("#nombre_beneficiario"+fila).val($("#nombre_beneficiario").val());
  $("#apellido_beneficiario"+fila).val($("#apellido_beneficiario").val());
  $("#cuenta_beneficiario"+fila).val($("#cuenta_beneficiario").val());
   if(!($("#nben"+fila).hasClass("estado"))){
    $("#nben"+fila).addClass("estado");
   }
   $("#modalTipoPagoSolicitud").modal("hide");
  }else{
   Swal.fire("Informativo!", "Debe llenar los campos requeridos!", "warning"); 
  }
  
}

function quitarFormaPagoProveedor(fila){
  if($("#nben"+fila).hasClass("estado")){
    $("#nben"+fila).removeClass("estado");   
  }
  $("#cod_cuentaBancaria"+fila).val("");
  $("#cod_tipopago"+fila).val("");
  $("#nombre_beneficiario"+fila).val("");
  $("#apellido_beneficiario"+fila).val("");
  $("#cuenta_beneficiario"+fila).val("");
}

function agregarFilaArchivosAdjuntosCabecera(){
  var codigo = $("#tipo_documento_otro").val();
  var num = parseInt($("#cantidad_archivosadjuntos").val());
  num++;  
  var row = $('<tr>').addClass('').attr('id','fila_archivo'+num);
  row.append($('<td>').addClass('text-left').html('<input type="hidden" name="codigo_archivo'+num+'" id="codigo_archivo'+num+'" value="'+codigo+'">Otros Documentos <a href="#" title="Quitar" class="btn btn-default btn-round btn-sm btn-fab float-right" onClick="quitarElementoAdjunto('+num+')"><i class="material-icons">delete_outline</i></a>'));
  row.append($('<td>').addClass('text-center').html('<i class="material-icons text-danger">clear</i> NO'));
  row.append($('<td>').addClass('text-right').html('<small id="label_txt_documentos_cabecera'+num+'"></small>'+ 
                      '<span class="input-archivo">'+
                        '<input type="file" class="archivo" name="documentos_cabecera'+num+'" id="documentos_cabecera'+num+'"/>'+
                      '</span>'+
                      '<label title="Ningún archivo" for="documentos_cabecera'+num+'" id="label_documentos_cabecera'+num+'" class="label-archivo btn btn-warning btn-sm"><i class="material-icons">publish</i> Subir Archivo'+
                      '</label>'));
  row.append($('<td>').addClass('text-center').html('<input type="text" class="form-control" style="background-color:#E3CEF6;text-align: left" value="" placeholder="Ingresar Descripción" id="nombre_archivo'+num+'" name="nombre_archivo'+num+'">')); 
  $("#tabla_archivos").append(row);
  $("#cantidad_archivosadjuntos").val(num);
}
function quitarElementoAdjunto(fila){
  $("#fila_archivo"+fila).remove();
}

function agregarFilaArchivosAdjuntosDetalle(){
  var codigo = $("#tipo_documento_otro").val();
  var num = parseInt($("#cantidad_archivosadjuntosdetalle").val());
  num++;  
  var row = $('<tr>').addClass('').attr('id','fila_archivodetalle'+num);
  row.append($('<td>').addClass('text-left').html('<input type="hidden" name="codigo_archivodetalle'+num+'" id="codigo_archivodetalle'+num+'" value="'+codigo+'">Otros Documentos <a href="#" title="Quitar" class="btn btn-default btn-round btn-sm btn-fab float-right" onClick="quitarElementoAdjuntoDetalle('+num+')"><i class="material-icons">delete_outline</i></a>'));
  row.append($('<td>').addClass('text-center').html('<i class="material-icons text-danger">clear</i> NO'));
  row.append($('<td>').addClass('text-right').html('<small id="label_txt_documentos_detalle'+num+'"></small>'+ 
                      '<span class="input-archivo">'+
                        '<input type="file" class="archivo" name="documentos_detalle'+num+'" id="documentos_detalle'+num+'"/>'+
                      '</span>'+
                      '<label title="Ningún archivo" for="documentos_detalle'+num+'" id="label_documentos_detalle'+num+'" class="label-archivo btn btn-warning btn-sm"><i class="material-icons">publish</i> Subir Archivo'+
                      '</label>'));
  row.append($('<td>').addClass('text-center').html('<input type="text" class="form-control" style="background-color:#E3CEF6;text-align: left" value="" placeholder="Ingresar Descripción" id="nombre_archivodetalle'+num+'" name="nombre_archivodetalle'+num+'">')); 
  $("#tabla_archivosdetalle").append(row);
  $("#cantidad_archivosadjuntosdetalle").val(num);

  //agregar a la fila  
  var fila=$("#codigo_fila").val();
  var htmlFila='<input type="text" name="codigo_archivodetalle'+num+'FFFF'+fila+'" id="codigo_archivodetalle'+num+'FFFF'+fila+'" value="'+codigo+'">'+
               '<input type="text" value="" id="nombre_archivodetalle'+num+'FFFF'+fila+'" name="nombre_archivodetalle'+num+'FFFF'+fila+'">'+ 
               '<input type="file" name="documentos_detalle'+num+'FFFF'+fila+'" id="documentos_detalle'+num+'FFFF'+fila+'"/>';
  $("#archivos_fila"+fila).append(htmlFila);
  $("#cantidad_archivosadjuntosdetalle"+fila).val(num);
}
function quitarElementoAdjuntoDetalle(fila){
  $("#fila_archivodetalle"+fila).remove();
  var num=$("#codigo_fila").val();
  $("#codigo_archivodetalle"+fila+"FFFF"+num).remove();
  $("#nombre_archivodetalle"+fila+"FFFF"+num).remove();
  $("#documentos_detalle"+fila+"FFFF"+num).remove();
}

function guardarArchivosDetalleSolicitud(){
  var fila = $("#codigo_fila").val();
  var cantidad=$("#cantidad_archivosadjuntosdetalle").val(); 
  for (var i = 1; i <=parseInt(cantidad); i++) {
    if($("#codigo_archivodetalle"+i).length>0){
     $("#codigo_archivodetalle"+i+"FFFF"+fila).val($("#codigo_archivodetalle"+i).val());
     $("#nombre_archivodetalle"+i+"FFFF"+fila).val($("#nombre_archivodetalle"+i).val());

     var x = $("#documentos_detalle"+i);
     var y = x.clone();
      y.attr("id", "documentos_detalle"+i+"FFFF"+fila);
      y.attr("name", "documentos_detalle"+i+"FFFF"+fila);
      $("#documentos_detalle"+i+"FFFF"+fila).remove();
      y.insertAfter($("#nombre_archivodetalle"+i+"FFFF"+fila));
    }
  };
}
function cargarArchivosAdjuntosFila(fila){
  var inicio=$("#cantidad_archivosadjuntosdetalleFijos").val(); 
  var cantidad=$("#cantidad_archivosadjuntosdetalle").val(); 
  for (var i = parseInt(inicio+1); i <=parseInt(cantidad); i++) {
      $("#fila_archivodetalle"+i).remove();
  }
  $("#cantidad_archivosadjuntosdetalle").val(inicio);
  var cantidadFila=$("#cantidad_archivosadjuntosdetalle"+fila).val(); 
  for (var i = 1; i <=parseInt(cantidadFila); i++) {
    if($("#codigo_archivodetalle"+i+"FFFF"+fila).length>0){
      //falta codigo
      if(i<=inicio){

      }else{
        var codigo = $("#tipo_documento_otro").val();
        var num = parseInt($("#cantidad_archivosadjuntosdetalle").val());
        num++;  
        var row = $('<tr>').addClass('').attr('id','fila_archivodetalle'+num);
        row.append($('<td>').addClass('text-left').html('<input type="hidden" name="codigo_archivodetalle'+num+'" id="codigo_archivodetalle'+num+'" value="'+codigo+'">Otros Documentos <a href="#" title="Quitar" class="btn btn-default btn-round btn-sm btn-fab float-right" onClick="quitarElementoAdjuntoDetalle('+num+')"><i class="material-icons">delete_outline</i></a>'));
        row.append($('<td>').addClass('text-center').html('<i class="material-icons text-danger">clear</i> NO'));
        row.append($('<td>').addClass('text-right').html('<small id="label_txt_documentos_detalle'+num+'"></small>'+ 
                      '<span class="input-archivo">'+
                        '<input type="file" class="archivo" name="documentos_detalle'+num+'" id="documentos_detalle'+num+'"/>'+
                      '</span>'+
                      '<label title="Ningún archivo" for="documentos_detalle'+num+'" id="label_documentos_detalle'+num+'" class="label-archivo btn btn-warning btn-sm"><i class="material-icons">publish</i> Subir Archivo'+
                      '</label>'));
        row.append($('<td>').addClass('text-center').html('<input type="text" class="form-control" style="background-color:#E3CEF6;text-align: left" value="" placeholder="Ingresar Descripción" id="nombre_archivodetalle'+num+'" name="nombre_archivodetalle'+num+'">')); 
        $("#tabla_archivosdetalle").append(row);
        $("#cantidad_archivosadjuntosdetalle").val(num);    
      }
     $("#codigo_archivodetalle"+i).val($("#codigo_archivodetalle"+i+"FFFF"+fila).val());
     $("#nombre_archivodetalle"+i).val($("#nombre_archivodetalle"+i+"FFFF"+fila).val());
     var x = $("#documentos_detalle"+i+"FFFF"+fila);
     var y = x.clone();
      y.attr("id", "documentos_detalle"+i);
      y.attr("name", "documentos_detalle"+i);
      y.addClass("archivo");
     // $("#documentos_detalle"+i).replaceWith(y);
      $("#documentos_detalle"+i).attr("id","eliminado"+i);
      y.insertAfter($("#eliminado"+i));
      $("#eliminado"+i).remove();
      var filename = $("#documentos_detalle"+i).val().split('\\').pop();
      var idname = $("#documentos_detalle"+i).attr('id');
      mostrarArchivoCambios(filename,idname);
    }
  }  
}

function mostrarArchivoCambios(filename,idname){
  $("#label_txt_"+idname).html(filename);
  if(filename.length>28){
    $("#label_txt_"+idname).html(filename.substr(0,28)+"...");
  }  
   $("#label_"+idname).attr("title",filename);
   if(filename==""||filename==null){
    $("#label_"+idname).html('<i class="material-icons">publish</i> Subir Archivo');
    if($("#label_"+idname).hasClass("btn-primary")){
      $("#label_"+idname).removeClass('btn-primary');
      $("#label_"+idname).addClass('btn-warning');
    }  
   }else{
    $("#label_"+idname).html('<i class="material-icons">done</i> Correcto');
    if(!($("#label_"+idname).hasClass("btn-primary"))){
      $("#label_"+idname).addClass('btn-primary');
      $("#label_"+idname).removeClass('btn-warning');
    } 
   }
}

$(document).on('change', '.archivo', function() {
  var filename = $(this).val().split('\\').pop();
  var idname = $(this).attr('id');
  mostrarArchivoCambios(filename,idname);
});

function cambiarCuentaAuxiliarDetalle(cuentaPadre,tipo,com,cod,ant,nue){
  $("#tipo").val(tipo);
  $("#cod_comprobantedetalle").val(com);
  $("#cod_estadocuenta").val(cod);
  $("#cod_antiguo").val(ant);
  if(tipo==3||tipo==4){
    $("#cod_nuevo").val("");
    $("#cod_nuevo").removeAttr("readonly");
    if(!($("#div_codigo_nuevo").hasClass("d-none"))){
      $("#div_codigo_nuevo").addClass("d-none");
      $("#div_codigo_nuevo_sel").removeClass("d-none");
      var parametros={"cod_cuenta":cuentaPadre};
      $.ajax({
        type: "GET",
        dataType: 'html',
        url: "ajaxSaveAuxiliarNuevoList.php",
        data: parametros,
        success:  function (resp) {
          $("#div_codigo_nuevo_sel").html(resp); 
          $('.selectpicker').selectpicker("refresh");            
        }
      }); 
    }
  }else{
    if($("#div_codigo_nuevo").hasClass("d-none")){
      $("#div_codigo_nuevo").removeClass("d-none");
      $("#div_codigo_nuevo_sel").addClass("d-none");
    }
    $("#cod_nuevo").val(nue);
    $("#cod_nuevo").attr("readonly",true);
  }
  $("#cambioCodigoAuxiliar").modal("show");  
}
function cambiarCodigoAuxiliar(){
  if($("#tipo").val()>2){
    var nuevo = $("#cod_nuevo_sel").val();
  }else{
    var nuevo = $("#cod_nuevo").val();
  }
  var parametros={
    "tipo":$("#tipo").val(),"cod_comprobantedetalle":$("#cod_comprobantedetalle").val(),
    "cod_estadocuenta":$("#cod_estadocuenta").val(),
    "cod_nuevo":nuevo
   };
      $.ajax({
        type: "GET",
        dataType: 'html',
        url: "ajaxSaveAuxiliarNuevo.php",
        data: parametros,
        beforeSend: function () {
        $("#texto_ajax_titulo").html("Cambiando Datos..."); 
          iniciarCargaAjax();
        },
        success:  function (resp) {
           detectarCargaAjax();
           if(resp=="1"){
            Swal.fire('Correcto!','Registro exitoso!','success');
            if($("#auxiliares_det").length > 0){
              window.location.href="repoAux.php"; 
            }else{
              window.location.href="repo.php";
            }
            
           }else{
            Swal.fire('ERROR!','No se pudo registrar!','error');
           }
           $("#texto_ajax_titulo").html("Procesando Datos");   
           $("#cambioCodigoAuxiliar").modal("hide");         
        }
      });
}

function cargarArrayAreaDistribucion(valor){
  if(valor==-1){
   var unidad = $("#unidad_solicitud").val();  
  }else{
    var unidad = valor;
  }
  var parametros={"unidad":unidad};
  $.ajax({
    type: "GET",
    dataType: 'html',
    url: "ajaxCargarArrayDistribucionArea.php",
    data: parametros,
    success:  function (resp) {       
      $("#array_distribucion").html(resp);
    }
  }); 
}

function cargarDatosCuentaBancariaProveedor(){
  var fila =$("#fila_pago").val();
  var proveedor = $("#proveedor"+fila).val();
  var banco = $("#cuenta_bancaria").val();
  var parametros={"codigo":proveedor,"banco":banco};
  $.ajax({
    type: "GET",
    dataType: 'html',
    url: "ajax_datos_bancarios_cuenta.php",
    data: parametros,
    success:  function (resp) {       
      $(".mensaje").html(resp);
    }
  }); 
}

function registrarNuevoBeneficiario(){
  var fila =$("#fila_pago").val();
  var proveedor = $("#proveedor"+fila).val();  
  $("#modalTipoPagoSolicitud").modal("hide");
  $("#cod_proveedorbeneficiario").val(proveedor);
  $("#nuevo_cuenta_beneficiario").val("");
  $("#nuevo_nombre_beneficiario").val("");
  $("#nuevo_apellido_beneficiario").val("");

  $("#nombre_proveedorbeneficiario").val($("#proveedor"+fila+" option:selected").text().toUpperCase());
  $("#modalNuevoCuentaBeneficiario").modal("show");
}

function guardarNuevoBeneficiario(){
  var proveedor = $("#cod_proveedorbeneficiario").val();
  var banco = $("#nuevo_banco").val();
  var cuenta = $("#nuevo_cuenta_beneficiario").val();
  var nombre = $("#nuevo_nombre_beneficiario").val();
  var apellido = $("#nuevo_apellido_beneficiario").val();
  if(cuenta==""||nombre==""||apellido==""){
    Swal.fire('Informativo!','No se admiten campos vacíos!','warning');
  }else{
   var parametros={"codigo":proveedor,"banco":banco,"cuenta":cuenta,"nombre":nombre,"apellido":apellido};
   $.ajax({
    type: "GET",
    dataType: 'html',
    url: "ajax_datos_bancarios_cuenta_save.php",
    data: parametros,
    success:  function (resp) {       
      $(".mensaje").html(resp);
      $(".mensaje").html("");
      Swal.fire('Correcto!','Se actualizaron los datos del Beneficiario!','success');
      $("#modalNuevoCuentaBeneficiario").modal("hide");
    }
   });   
  }
}

function saveDatosBancarizacion(){
  var nro_contrato=$('#nro_contrato_modal').val();
  var nro_cuenta_doc=$('#nro_cuenta_doc_modal').val();
  var nit_entidad_financiera=$('#nit_entidad_financiera_modal').val();
  var nro_transaccion=$('#nro_transaccion_modal').val();
  var tipo_doc_pago=$('#tipo_doc_pago_modal').val();
  var fecha_doc_pago=$('#fecha_doc_pago_modal').val();
  if(nro_contrato==null || nro_contrato==''){
    Swal.fire("Informativo!", "Por favor introduzca El número de contrato (de no existir contrato introduzca 0)", "warning");
  }else{
    if(nro_cuenta_doc==null || nro_cuenta_doc=='' || nro_cuenta_doc<0){
      Swal.fire("Informativo!", "Por favor introduzca el Nro de cuenta del documento de pago.", "warning");
    }else{
      if(nit_entidad_financiera==null || nit_entidad_financiera=='' || nit_entidad_financiera<0){
        Swal.fire("Informativo!", "Por favor introduzca el NIT de Entidad Financiera.", "warning");
      }else{
        if(nro_transaccion==null || nro_transaccion=='' || nro_transaccion<0){
          Swal.fire("Informativo!", "Por favor introduzca Nro de Operación o Transaccion.", "warning");
        }else{
          if(tipo_doc_pago==null || tipo_doc_pago=='' || tipo_doc_pago<0){
            Swal.fire("Informativo!", "Por favor introduzca Tipo de documento de pago.", "warning");
          }else{
            if(fecha_doc_pago==null || fecha_doc_pago==''){
              Swal.fire("Informativo!", "Por favor seleccione la Fecha del doc. de Pago", "warning");
            }else{
              $('#nro_contrato').val(nro_contrato);
              $('#nro_cuenta_doc').val(nro_cuenta_doc);
              $('#nit_entidad_financiera').val(nit_entidad_financiera);
              $('#nro_transaccion').val(nro_transaccion);
              $('#tipo_doc_pago').val(tipo_doc_pago);
              $('#fecha_doc_pago').val(fecha_doc_pago);
              $('#modalBancarizacion').modal('hide');
            }
          }
        }
      }
    }
  }
  if(total_porcentaje==100){
    borrarItemsTipoPago(); //limpiamos el array de objeto para guardarlo nuevamente
    var total_items=$('#total_items_tipopago').val();
    for (var i=0;i<=(total_items-1);i++){
      var tipopago={
        codigo_tipopago: $('#codigo_tipopago'+i).val(),
        monto_porcentaje: $('#monto_porcentaje_tipopago'+i).val(),
        monto_bob: $('#monto_bob_tipopago'+i).val(),    
      }
      // console.log($('#monto_porcentaje_tipopago'+i).val());
      // console.log(tipopago);
      var monto_x=$('#monto_porcentaje_tipopago'+i).val();
      if(monto_x!=null && monto_x!=0 && monto_x!=''){
        itemTipoPagos_facturacion[0].push(tipopago);  
      }
    }  
    $("#nfac").html(itemTipoPagos_facturacion[0].length);
    $('#modalTipoPagoPorcentaje').modal('hide');
    // for(var j = 0; j < itemTipoPagos_facturacion[0].length; j++){
    //   var dato = Object.values(itemTipoPagos_facturacion[0][j]);
    //   console.log("dato: "+dato);
    //   // console.log("datos: "+dato[4]+" "+dato[7]+" "+dato[8]);    
    // }
  }else{
    Swal.fire("Informativo!", "EL porcentaje de los montos difiere del 100%", "warning");
  }   
}
function editarPrecioSimulacionCostos(){
  if($("#modal_importeplanedit").is("[readonly]")){
    $("#modal_importeplanedit").removeAttr("readonly");
  }else{
    $("#modal_importeplanedit").attr("readonly",true);
  }
}

function cambiarPrecioPlantilla(){
  if(!($("#modal_importeplanedit").is("[readonly]"))){
    $("#modal_importeplanedit").attr("readonly",true);
  }
  $("#modal_importeplanedit").val(redondeo(parseFloat($('#modal_importeplan option:selected').text())));

  cargarPreciosDetalle($('#modal_importeplan').val()); //fila afectada

}



//tipo pago
var itemGenerar_factura_parcial=[];
var itemGenerar_factura_parcial_aux=[];
function agregaDatosGenerarFactPagos(datos){  
  var d=datos.split('/');

  var cod_solicitudfacturacion=d[0];
  // var monto_solicitud=parseFloat(d[1]);
  // var saldo_anterior=parseFloat(d[2]);
  document.getElementById("cod_solicitudfacturacion_factpagos").value=cod_solicitudfacturacion;
  var index=(parseFloat(d[3])-1);
  // alert("index:"+index);
  var contenedor = document.getElementById('contenedor_GenerarFactParcial_cabecera');    
  ajax=nuevoAjax();
  ajax.open('GET', 'simulaciones_servicios/ajax_cabecera_modal_generarFactPar.php?cod_solicitud='+cod_solicitudfacturacion,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;      
      $('.selectpicker').selectpicker(["refresh"]);
      tablaGeneral_GenerarFact_parcial(index);
    }
  }
  ajax.send(null);
}
function tablaGeneral_GenerarFact_parcial(index){
  // var monto_total=$("#modal_totalmontos").val();
  var div=$('<div>').addClass('col-sm-12');
  var table = $('<table>').addClass('table table-bordered table-condensed table-sm');
  var titulos = $('<tr>').addClass('fondo-boton');
  titulos.append($('<th width="2%">').addClass('').text('#'));

  titulos.append($('<th >').addClass('').text('Item'));
  titulos.append($('<th width="8%">').addClass('').text('cantidad'));
  titulos.append($('<th width="8%">').addClass('').text('Precio'));    
  titulos.append($('<th width="8%">').addClass('').text('Desc BOB'));
  titulos.append($('<th width="3%">').addClass('').text('Importe'));
  titulos.append($('<th width="8%">').addClass('small').text('Importe Facturado'));
  titulos.append($('<th width="5%">').addClass('small').text('Saldo'));
  titulos.append($('<th width="8%">').addClass('').text('Importe a Pagar'));
  table.append(titulos);
  for (var i = 0; i < itemGenerar_factura_parcial_aux[index].length; i++) {
    var descripcionx=itemGenerar_factura_parcial_aux[index][i].descripcionx;
    var cantidadX=itemGenerar_factura_parcial_aux[index][i].cantidadxx;;
    var codigox=itemGenerar_factura_parcial_aux[index][i].codigox;
    var preciox=itemGenerar_factura_parcial_aux[index][i].preciox;
    var descuentox=itemGenerar_factura_parcial_aux[index][i].descuentox;;
    var importex=parseFloat(preciox)-parseFloat(descuentox);
    var importe_anterior_x=itemGenerar_factura_parcial_aux[index][i].importe_anterior_x;
    var saldox=parseFloat(importex)-parseFloat(importe_anterior_x);
    // var cod_solfacdet=itemGenerar_factura_parcial_aux[index][i].cod_solfacdet;

    var row = $('<tr style="#FFF000;">').addClass('');
    row.append($('<td>').addClass('').text(i+1).html("<input type='hidden' name='codigo_x"+i+"' id='codigo_x"+i+"' value='"+codigox+"'>" ));
    row.append($('<td>').addClass('').html("<input style='background-color:#FFFFFF;' type='text' class='form-control' name='descripcion"+i+"' id='descripcion"+i+"' value='"+descripcionx+"' readonly='true'>"));
    row.append($('<td>').addClass('text-right small').text(cantidadX));
    row.append($('<td>').addClass('').html("<input type='hidden' step='0.01' name='precio_unitario"+i+"' id='precio_unitario"+i+"' value='"+preciox+"'><input style='background-color:#FFFFFF;' type='text' class='form-control' name='precio_unitario_a"+i+"' id='precio_unitario_a"+i+"' value='"+number_format(parseFloat(preciox),2)+"' readonly='true'>" ));
    row.append($('<td>').addClass('text-right small').text(number_format(descuentox,2)));
    row.append($('<td>').addClass('').html("<input type='hidden' step='0.01' name='importe_x"+i+"' id='importe_x"+i+"' value='"+importex+"'><input style='background-color:#FFFFFF;' type='text' step='0.01' class='form-control' name='importe_x_a"+i+"' id='importe_x_a"+i+"' readonly='true' value='"+number_format(importex,2)+"'>"));        
    row.append($('<td>').addClass('').html("<input style='background-color:#FFFFFF;' type='hidden' step='0.01' class='form-control' name='importe_anterior"+i+"' id='importe_anterior"+i+"' value='"+importe_anterior_x+"' readonly='true'><input style='background-color:#FFFFFF;' type='text' step='0.01' class='form-control' name='importe_anterior_a"+i+"' id='importe_anterior_a"+i+"' value='"+number_format(importe_anterior_x,2)+"' readonly='true'>"));
    row.append($('<td>').addClass('').html("<input style='background-color:#FFFFFF;' type='hidden' step='0.01' class='form-control' name='saldo"+i+"' id='saldo"+i+"' value='"+saldox+"' readonly='true'><input style='background-color:#FFFFFF;' type='text' step='0.01' class='form-control' name='saldo_a"+i+"' id='saldo_a"+i+"' value='"+number_format(saldox,2)+"' readonly='true'>"));
    row.append($('<td>').addClass('').html("<input style='background-color:#FFFFFF;' type='number' step='0.01' class='form-control' name='importe_a_pagar"+i+"' id='importe_a_pagar"+i+"' value='0' onkeyup='monto_convertir_a_porcentaje_factPagos("+i+")'>"));        
    table.append(row);
  }
  var row = $('<tr style="background-color:#F6E3CE;">').addClass('');//parte de total
  row.append($('<td>').addClass('').text(''));
  row.append($('<td colspan="4" align="center">').addClass('').html('<b>MONTO TOTAL</b>'));
  row.append($('<td>').addClass('').html("<input type='hidden' step='0.01' class='form-control' name='total_importe' id='total_importe' value='0'><input style='background-color:#F6E3CE;' type='text' step='0.01' class='form-control' name='total_importe_a' id='total_importe_a' value='"+number_format(0,2)+"' readonly='true'> "));
  row.append($('<td>').addClass('').html("<input type='hidden' step='0.01' class='form-control' name='total_importe_anterior' id='total_importe_anterior' value='0'><input style='background-color:#F6E3CE;' type='text' step='0.01' class='form-control' name='total_importe_anterior_a' id='total_importe_anterior_a' value='"+number_format(0,2)+"' readonly='true'> "));
  row.append($('<td>').addClass('').html("<input style='background-color:#F6E3CE;' type='text' step='0.01' class='form-control' name='total_saldo_a' id='total_saldo_a' value='"+number_format(0,2)+"' readonly='true'> "));
  row.append($('<td>').addClass('').html("<input type='hidden' class='form-control' name='total_importe_pagar' id='total_importe_pagar' value='0'><input style='background-color:#F6E3CE;' type='text' step='0.01' class='form-control' name='total_importe_pagar_a' id='total_importe_pagar_a' value='"+number_format(0,2)+"' readonly='true'>"));  
  table.append(row);    
  div.append(table);    
  $("#cantidad_items").val(i);
  $('#contenedor_GenerarFactParcial').html(div);
  calcular_monto_total_items_factura_parcial();
}
function monto_convertir_a_porcentaje_factPagos(i){
  // alert("aqui");
  var importe_x=$("#importe_x"+i).val();  
  var importe_a_pagar=$("#importe_a_pagar"+i).val();
  var importe_anterior=$("#importe_anterior"+i).val();
  var saldo=parseFloat(importe_x)-parseFloat(importe_anterior);
  var sw_aux=true;  
  if(parseFloat(importe_anterior)!=0){
    if(parseFloat(importe_a_pagar)>parseFloat(saldo)){
      Swal.fire("Informativo!", "El Monto insertado es superior al Saldo Anterior ("+number_format(saldo,2)+")", "warning");
    }else{      
        calcular_monto_total_items_factura_parcial();      
    }
  }else{
    if(parseFloat(importe_a_pagar)>parseFloat(importe_x)){
        Swal.fire("Informativo!", "El monto insertado es superior al importe ("+number_format(importe_x,2)+")", "warning");
    }else{
        calcular_monto_total_items_factura_parcial();
    }
  }
}

function calcular_monto_total_items_factura_parcial(){
  var total_items=$("#cantidad_items").val();
  var total_importe_monto=0;
  var total_importe_pagar=0;
  var total_importe_anterior=0;
  var total_saldo=0;
  for(var j=0;j<total_items;j++){
    var importe_x=$("#importe_x"+j).val();
    var importe_anterior=$("#importe_anterior"+j).val();
    var importe_a_pagar=$("#importe_a_pagar"+j).val();
    var saldo_x=$("#saldo"+j).val();
    total_importe_monto+=parseFloat(importe_x);
    total_importe_anterior+=parseFloat(importe_anterior);
    total_importe_pagar+=parseFloat(importe_a_pagar);
    total_saldo+=parseFloat(saldo_x);
  }
  $("#total_importe").val(total_importe_monto);      
  $("#total_importe_a").val(number_format(total_importe_monto,2));  
  $("#total_importe_anterior").val(total_importe_anterior);  
  $("#total_importe_anterior_a").val(number_format(total_importe_anterior,2));
  $("#total_importe_pagar").val(total_importe_pagar);  
  $("#total_importe_pagar_a").val(number_format(total_importe_pagar,2));
  $("#total_saldo_a").val(number_format(total_saldo,2));
}
function RegistrarFacturaPagos(cod_solicitudfacturacion,porcentaje_pagar,monto_pagar){
  $.ajax({
    type:"POST",
    data:"cod_solicitudfacturacion="+cod_solicitudfacturacion+"&porcentaje_pagar="+porcentaje_pagar+"&monto_pagar="+monto_pagar,
    url:"simulaciones_servicios/generarFacturaPagos.php",
    success:function(r){
      if(r==1){
        alerts.showSwal('success-message','index.php?opcion=listFacturasServicios_conta');
      }else{
        if(r==2){
          Swal.fire("A ocurrido un error!", "Por favor verifique que los tipos de pago estén asociados a una cuenta.", "warning");
        }else{
          if(r==3){
            Swal.fire("A ocurrido un error!", "Por favor verifique que las areas de ingreso estén asociadas a una cuenta.", "warning");
          }else{
            if(r==4){
              Swal.fire("informativo!", "La factura ya fue generada.", "warning");
            }else{
              if(r==5){
                Swal.fire("A ocurrido un error!", "Sucursal no encontrada.", "warning");
              }else{
                if(r==6){
                  Swal.fire("A ocurrido un error!", "No tiene registrado La dosificación para la facturación..", "warning");  
                }else{
                  alerts.showSwal('error-message','index.php?opcion=listFacturasServicios_conta');
                }
                
              } 
            } 
          }  
        }  
      }
         
    }
  });
}
function agregaDatosFactManual(datos){  
  var d=datos.split('/');
  document.getElementById("cod_solicitudfacturacion_factmanual").value=d[0];  
  document.getElementById("importe_total").value="Saldo de Solicitud de Facturacón: "+number_format(d[2],2);
  
  document.getElementById("nit_cliente").value=d[4];
  document.getElementById("razon_social").value=d[5];
}
function RegistrarFacturaManual(cod_solicitudfacturacion,nro_factura,nro_autorizacion,fecha_factura,nit_cliente,razon_social,cod_libreta_manual){
  $.ajax({
    type:"POST",
    data:"cod_solicitudfacturacion="+cod_solicitudfacturacion+"&nro_factura="+nro_factura+"&nro_autorizacion="+nro_autorizacion+"&fecha_factura="+fecha_factura+"&nit_cliente="+nit_cliente+"&razon_social="+razon_social+"&cod_libreta="+cod_libreta_manual,
    url:"simulaciones_servicios/generarFacturaManual.php",
    success:function(r){
      if(r==1){
        alerts.showSwal('success-message','index.php?opcion=listFacturasServicios_conta');
      }else{
        if(r==2){
          Swal.fire("A ocurrido un error!", "Por favor verifique que los tipos de pago estén asociados a una cuenta.", "warning");
        }else{
          if(r==3){
            Swal.fire("A ocurrido un error!", "Por favor verifique que las areas de ingreso estén asociadas a una cuenta.", "warning");
          }else{
            if(r==4){
              Swal.fire("informativo!", "La factura ya fue generada.", "warning");
            }else{
              if(r==5){
                Swal.fire("A ocurrido un error!", "Sucursal no encontrada.", "warning");
              }else{
                if(r==-1){
                  Swal.fire("A ocurrido un error!", "No se tiene conexión al servicio de capacitación.", "warning");
                }else{
                  alerts.showSwal('error-message','index.php?opcion=listFacturasServicios_conta');  
                }
                
              } 
            } 
          }  
        }  
      }
         
    }
  });
}
function agregaDatosDetalleFactManual(datos){  
  var d=datos.split('/');  
  document.getElementById("cliente_facmanual").value=d[0];
  document.getElementById("razon_social_facmanual").value=d[1];
  document.getElementById("nit_cliente_facmanual").value=d[2];
  document.getElementById("nro_factura_facmanual").value=d[3];
  document.getElementById("nro_autorizacion_facmanual").value=d[4];
  document.getElementById("importe_facmanual").value=number_format(d[5],2);  

}
var detalle_tabla_general=[];
var numFilasA=0;
function filaTablaAGeneral(tabla,index,stringCabecera){
  var cabecera = stringCabecera.split("##");
  var uo = cabecera[0];
  var area = cabecera[1];
  var nombre_simulacion = cabecera[2];
  var area_simulacion = cabecera[3];
  var fecha_registro = cabecera[4];
  var fecha_facturar = cabecera[5];
  var nit = cabecera[6];
  var razon_social = cabecera[7];
  //ajax cabecera  
  var contenedor = document.getElementById('div_cabecera');
  ajax=nuevoAjax();
  ajax.open('GET', 'simulaciones_servicios/ajax_cabecera_modal.php?uo='+uo+'&area='+area+'&nombre_simulacion='+nombre_simulacion+'&area_simulacion='+area_simulacion+'&fecha_registro='+fecha_registro+'&fecha_facturar='+fecha_facturar+'&nit='+nit+'&razon_social='+razon_social,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;
      // $('.selectpicker').selectpicker(["refresh"]);
    }
  }
  ajax.send(null);

  var html="";
  var sumaTotalDetalle=0;
  var cantidadTotalDetalle=0;
  for (var i = 0; i < detalle_tabla_general[index-1].length; i++) {
    //alert(detalle_tabla_general[index-1][i].nombre);
    sumaTotalDetalle+=parseFloat(detalle_tabla_general[index-1][i].precioX);
    cantidadTotalDetalle+=parseInt(detalle_tabla_general[index-1][i].cantidadX);
    html+="<tr><td>"+(i+1)+"</td><td>"+detalle_tabla_general[index-1][i].serviciox+"</td><td>"+detalle_tabla_general[index-1][i].cantidadX+"</td><td>"+number_format(detalle_tabla_general[index-1][i].precioX,2)+"</td><td>"+detalle_tabla_general[index-1][i].descripcion_alternaX+"</td></tr>";
  }
  html+="<tr style='background-color:#d3dcde;'><td></td><td>TOTAL</td><td>"+cantidadTotalDetalle+"</td><td>"+number_format(sumaTotalDetalle,2)+"</td><td></td></tr>";
  tabla.html(html);
  $("#modalDetalleFac").modal("show");  
}


function agregaDatosComprCajaChica(datos){  
  var d=datos.split('/');  
  document.getElementById("cod_cajachica").value=d[0];  
  document.getElementById("detalle_cajachica").value=d[1];  
  document.getElementById("cod_tipocajachica").value=d[2];
  
}
function RegistrarComprobanteCajaChica(cod_cajachica,cod_tipocajachica,nro_comprobante,mes_comprobante,tipo_comprobante){
  $.ajax({
    type:"POST",
    data:"cod_cajachica="+cod_cajachica+"&nro_comprobante="+nro_comprobante+"&mes_comprobante="+mes_comprobante+"&tipo_comprobante="+tipo_comprobante,
    url:"caja_chica/executeComprobanteCajaChica_existente.php",
    success:function(r){
      var respu=r.split('#####');
      var estado=respu[0];
      var stringRetenciones=respu[1];      
      if(estado==1){
        alerts.showSwal('success-message','index.php?opcion=ListaCajaChica&codigo='+cod_tipocajachica);
      }else{
        if(estado==0){
          Swal.fire("ERROR!", "A ocurrido un error inesperado al generar el comprobante!", "warning");
        }else{
          if(estado==2){
            Swal.fire("Informativo!", "El COMPROBANTE ya fue generado. Actualice el Sistema Por favor!", "warning");
          }else{
            if(estado==3){
              Swal.fire("ERROR!", "No se pudo encontrar el comprobante, por favor verifique los datos introducidos!", "warning");
            }else{
              if(estado==4){
                Swal.fire("ERROR!", "No se pudo generar el comprobante.<br>\n Error en: "+stringRetenciones+"<br>\n No tiene Factura registrada.", "warning");
              }
            }
          }
        }      
      }
    }
  });
  // =======
  //   cargarPreciosDetalle($('#modal_importeplan').val());
}
function ajaxBuscarComprobanteCajaChica(){
  var mes_comprobante=document.getElementById("mes_comprobante").value;
  var tipo_comprobante=document.getElementById("tipo_comprobante").value;
  var nro_comprobante=document.getElementById("nro_comprobante").value;
  var unidad=document.getElementById("unidad").value;
  if(mes_comprobante!=null && tipo_comprobante!=null){
    var contenedor;
    // var codigo_UO=combo.value;
    contenedor = document.getElementById('contenedor_detalle_comprobante');
    ajax=nuevoAjax();
    ajax.open('GET', 'caja_chica/ajax_buscarComprobanteCajaChica.php?mes='+mes_comprobante+'&tipo='+tipo_comprobante+'&nro='+nro_comprobante+'&unidad='+unidad,true);
    ajax.onreadystatechange=function() {
      if (ajax.readyState==4) {
        contenedor.innerHTML = ajax.responseText;
        var detalle_comprobante=document.getElementById("detalle_comprobante").value;
        // alert(detalle_comprobante);
        if(detalle_comprobante!="no encontrado"){
          $("#guardarDatosModalComprobante").removeClass("d-none"); 
        }else{
            $("#guardarDatosModalComprobante").addClass("d-none"); 
        }
      }
    }
    ajax.send(null) 
  }else{

  }
}


function cargarPreciosDetalleSimulacionCosto(){
  var codigoPlan = $("#modal_importeplan"+fila).val();
  var parametros={"codigo":codigoPlan};
  $.ajax({
    type: "GET",
    dataType: 'html',
    url: "ajax_datos_bancarios_cuenta.php",
    data: parametros,
    success:  function (resp) {       
      $(".mensaje").html(resp);
    }
  });  
}


function agregarFilaPreciosSimulacionCabecera(){
  var precio = $("#modal_importeplanedit").val();
  var num = parseInt($("#cantidad_filasprecios").val());
  num++;  
  var row = $('<tr>').addClass('').attr('id','fila_precios'+num);
  row.append($('<td>').addClass('text-center').html('<input type="number" onkeyup="calcularPrecioTotal('+num+')" onkeydown="calcularPrecioTotal('+num+')" class="form-control" style="background-color:#E3CEF6;text-align: right" value="" placeholder="0" id="cantidad_alumnosAAA'+num+'" name="cantidad_alumnosAAA'+num+'">'));
  row.append($('<td>').addClass('text-center').html('<input type="number" onkeyup="calcularPrecioPorcentaje('+num+')" onkeydown="calcularPrecioPorcentaje('+num+')" class="form-control" style="background-color:#E3CEF6;text-align: right" value="0" placeholder="00000" id="porcentaje_alumnosAAA'+num+'" name="porcentaje_alumnosAAA'+num+'">'));
  row.append($('<td>').addClass('text-center').html('<input type="number" onkeyup="calcularPrecioTotal('+num+')" onkeydown="calcularPrecioTotal('+num+')" class="form-control" style="background-color:#E3CEF6;text-align: right" value="'+precio+'" placeholder="00000" id="monto_alumnosAAA'+num+'" name="monto_alumnosAAA'+num+'">'));
  row.append($('<td>').addClass('text-center').html('<input type="number" readonly class="form-control" style="background-color:#E3CEF6;text-align: right" value="" placeholder="00000" id="total_alumnosAAA'+num+'" name="total_alumnosAAA'+num+'">')); 
  row.append($('<td>').addClass('text-left').html('<a href="#" title="Quitar" class="btn btn-danger btn-round btn-sm btn-fab float-right" onClick="quitarElementoPrecios('+num+')"><i class="material-icons">delete_outline</i></a>'));
  $("#modal_body_tabla_alumnos").append(row);
  $("#cantidad_filasprecios").val(num);
  calcularTotalesPrecios();
}
function quitarElementoPrecios(fila){
  $("#fila_precios"+fila).remove();
  calcularTotalesPrecios();
}

function calcularPrecioTotal(fila){
  $("#total_alumnosAAA"+fila).val(redondeo($("#monto_alumnosAAA"+fila).val()*$("#cantidad_alumnosAAA"+fila).val()));
  calcularTotalesPrecios();
}
function calcularPrecioPorcentaje(fila){
  var precio = $("#modal_importeplanedit").val();
  $("#monto_alumnosAAA"+fila).val(redondeo(precio-(($("#porcentaje_alumnosAAA"+fila).val()/100)*precio)));
  calcularPrecioTotal(fila);
}
function calcularTotalesPrecios(){
  var cantidad=$("#cantidad_filasprecios").val();
  var suma=0;
  for (var i = 1; i <= parseInt(cantidad); i++) {
    if($("#total_alumnosAAA"+i).length>0){
      suma+=redondeo($("#total_alumnosAAA"+i).val());  
    }  
  };
  $("#total_preciosimulacion").val(redondeo(suma));
}

function guardarPreciosDetalle(){
  var codigo = $("#modal_importeplan").val();
  var datos=[];
  var cantidad=$("#cantidad_filasprecios").val();
  for (var i = 1; i <= parseInt(cantidad); i++) {
    if($("#total_alumnosAAA"+i).length>0){
      datos.push({
        "cantidad":$("#cantidad_alumnosAAA"+i).val(),
        "porcentaje":$("#porcentaje_alumnosAAA"+i).val(),
        "monto":$("#monto_alumnosAAA"+i).val(),
      }); 
    }  
  };
  var parametros={"codigo":codigo,"datos":JSON.stringify(datos)};
  $.ajax({
    type: "GET",
    dataType: 'html',
    url: "ajaxSavePreciosDetalle.php",
    data: parametros,
    success:  function (resp) {       
      $(".mensaje").html(resp);
    }
  });  
}

function cargarPreciosDetalle(codigo){
  var parametros={"codigo":codigo};
  $.ajax({
    type: "GET",
    dataType: 'html',
    url: "ajaxListPreciosDetalle.php",
    data: parametros,
    success:  function (resp) {       
      $("#modal_body_tabla_alumnos").html(resp);
    }
  });  
}

function cambiarReporteResumido(){
  var check=document.getElementById("resumido").checked;
    if(!check) {
      if($("#div_solicitados").hasClass("d-none")){
        $("#div_solicitados").removeClass("d-none");
      }
    }else{
      if(!($("#div_solicitados").hasClass("d-none"))){
        $("#div_solicitados").addClass("d-none");
      }
    }  
}
function ajax_mes_de_gestion(combo){
  var contenedor = document.getElementById('div_contenedor_mes');
  var gestion=combo.value;
  // console.log(gestion);
  ajax=nuevoAjax();
  ajax.open('GET', 'reportes/ajax_mes_gestion.php?cod_gestion='+gestion,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;      
      $('.selectpicker').selectpicker(["refresh"]);
    }
  }
  ajax.send(null);

}
function descargar_txt_libro_ventas(){
    var cod_gestion=$("#gestiones").val();
    var cod_mes=$("#cod_mes_x").val();
    if(cod_gestion==null || cod_gestion==''){
      Swal.fire("Informativo!", "Por favor Seleccione la gestión!", "warning");
    }else{
      if(cod_mes==null || cod_mes==''){
        Swal.fire("Informativo!", "Por favor Seleccione el mes!", "warning");
      }else{
        // alert("llegue");
        $.ajax({
        type:"POST",
        data:"cod_gestion="+cod_gestion+"&cod_mes="+cod_mes,
        url:"reportes/reportePrintLibroVentasTXT.php",
        success:function(r){
          var respu=r.split('#####');
          var estado=respu[1];
          var nombre_ar=respu[2];
          // console.log(r);
          if(estado==1){
            // Swal.fire("Correcto!", "El proceso se completo correctamente!", "success");
            // alerts.showSwal('success-message','reportes/'+nombre_ar);
            var direccion=nombre_ar;
            descargar_txt_libro_ventas_x(direccion);
          }else{
            
            Swal.fire("ERROR!", "Hubo un error al generar el TXT!", "warning");
            
          }
        }
        });         
      }
    }
}
function descargar_txt_libro_ventas_x(url){
  // var monto_total=$("#modal_totalmontos").val();
  $('#modal_descargarTXT').modal('show');
      //agregamos la cuenta si lo tuviese  
    var contenedor;  
    contenedor = document.getElementById('contenedor_DescargaTxt');
    ajax=nuevoAjax();
    ajax.open('GET', 'reportes/ajax_contenedor_boton_descarga_txt.php?url='+url,true);
    ajax.onreadystatechange=function() {
      if (ajax.readyState==4) {
        contenedor.innerHTML = ajax.responseText;
        $('.selectpicker').selectpicker(["refresh"]);          
      }
    }
    ajax.send(null); 
}

function ajaxTipoProveedorCliente_comprobante(tipo){
  var contenedor_p;
  var tipoProveedorCliente=tipo.value;
  contenedor_p = document.getElementById('divProveedorCliente');
  ajax=nuevoAjax();
  ajax.open('GET', '../cuentas_auxiliares/ajaxProveedorCliente.php?tipo='+tipoProveedorCliente,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor_p.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);    
    }
  }
  ajax.send(null)  
}
function guardarNuevaCuentaAuxi(){
  var cod_cuenta=document.getElementById('cod_cuenta').value;  
  var nombre=$('#nombre_x').val();
  var tipo=$('#tipo_x').val();
  var proveedor=$('#proveedor_cliente').val();
  if(nombre==null || nombre=='' || nombre==0){
    Swal.fire("Informativo!", "Por favor, Seleccione una cuenta!", "warning");
  }else{
    if(nombre==null || nombre=='' || nombre==0){
      Swal.fire("Informativo!", "Por favor, Introduzca el nombre de la cuenta Auxiliar!", "warning");
    }else{
      if(tipo==null || tipo=='' || tipo==0){
        Swal.fire("Informativo!", "Por favor, Seleccione un tipo de cuenta!", "warning");
      }else{
        if(proveedor==null || proveedor=='' || proveedor==0){
            Swal.fire("Informativo!", "Por favor, Seleccione un proveedor o Cliente!", "warning");
        }else{
          $.ajax({
          type:"POST",
          data:"cod_cuenta="+cod_cuenta+"&nombre="+nombre+"&tipo="+tipo+"&proveedor="+proveedor,
          url:"ajax_contenedor_cuentas_auxiliares_save.php",
          success:function(r){
            var respu=r.split('####');
            var estado=respu[1];
            var cod_cuenta_auxiliar=respu[2];
            if(estado==1){            
              Swal.fire("Correcto!", "Se guardó satisfactoriamente.!", "success");            
              $('#modalRegisterCuentasAux').modal('hide');            
              var objeto_cuenta_aux={codigo:cod_cuenta_auxiliar,
                nombre: nombre,
                codCuenta: cod_cuenta}
              itemCuentasAux.push(objeto_cuenta_aux);

            }else{
              Swal.fire("ERROR!", "Hubo un error al Guardar la cuenta Auxiliar", "error");
            }
          }
          });         
          
        }
      }
    }
  }
}


function botonBuscarComprobante_caja_chica(){
  var valor_uo=$("#OficinaBusqueda").val();
  var valor_tipo=$("#tipoBusqueda").val();
  var valor_fi=$("#fechaBusquedaInicio").val();
  var valor_ff=$("#fechaBusquedaFin").val();
  var valor_glosa=$("#glosaBusqueda").val();
  var valor_nro_compr=$("#nro_comprobante").val();
  var valor_nro_cuenta=$("#cuenta_auto_id").val();
  contenedor_p = document.getElementById('contenedor_lista_comprobantes');
  ajax=nuevoAjax();
  ajax.open('GET', 'caja_chica/ajaxListaComprobantesModal.php?cod_uo='+valor_uo+'&tipo='+valor_tipo+'&fechaI='+valor_fi+'&fechaF='+valor_ff+'&glosa='+valor_glosa+'&comprobante='+valor_nro_compr+'&cuenta='+valor_nro_cuenta,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor_p.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);
      // $("#modalBuscador").modal("hide");
      $("#modal_lista_comprobantes").modal("show");
      
    }
  }
  ajax.send(null);
}
function SeleccionarComprobante_cajachica_reembolso(cod_comprobante,cod_comprobantedetalle,glosa_x,monto_x,nombre_comprobante){
  // alert("ok");
  $("#monto").val(monto_x);
  $("#observaciones").val(glosa_x+", enlazado al comprobante ("+nombre_comprobante+")");
  $("#cod_comprobante").val(cod_comprobante);
  $("#cod_comprobante_detalle").val(cod_comprobantedetalle);
  $("#modalBuscador").modal("hide");
  $("#modal_lista_comprobantes").modal("hide");
}


function actualizarSimulacionSitios(){
  javascript:location.reload(true);
}

function actualizarPaginaNuevaP(){
  javascript:location.reload(true);
}

function removePlantillaComprobantes(cod_plantilla){  
  $.ajax({
  type:"POST",
  data:"cod_plantilla="+cod_plantilla,
  url:"delete_plantillas.php",
  success:function(r){    
    if(r==1){      
      Swal.fire("Correcto!", "El proceso se completo correctamente!", "success");
      $("#modalAbrirPlantilla").modal("hide");
    }else{
      Swal.fire("ERROR!", "Hubo un error en el borrado de la Plantilla!", "warning");
    }
  }
  });           
}

function modalDevolverSolicitud(datos){    
  var d=datos.split('###');  
  document.getElementById("cod_solicitudfacturacion").value=d[0];
  document.getElementById("nro_solicitud").value=d[1];
  document.getElementById("codigo_servicio").value=d[2];  
  document.getElementById("estado").value=d[3];  //estado
  document.getElementById("admin").value=d[4];  //tipo admin
  document.getElementById("direccion").value=d[5];  //link destino
  document.getElementById("observaciones").value=d[6];  //obs
}
function modal_rechazarFactura(datos){    
  var d=datos.split('###');  
  document.getElementById("cod_solicitudfacturacion").value=d[0];
  document.getElementById("nro_solicitud").value=d[1];
  document.getElementById("codigo_servicio").value=d[2];  
  document.getElementById("estado").value=1;  //estado
  document.getElementById("admin").value=30;  //tipo admin
  document.getElementById("direccion").value=d[3];  //link destino
  document.getElementById("codigo_factura").value=d[4];  //codigo factura
  document.getElementById("codigo_comprobante").value=d[5];  //codigo factura
  // $("#campo_nro_fact").html("<small>Nro.<br>Factua</small>");
  // $("#campo_rs_fact").html("<small>Razón<br>Social</small>");    
}
function modalDevolverSolicitud_regional(datos){    
  var d=datos.split('###');  
  document.getElementById("cod_solicitudfacturacion").value=d[0];
  document.getElementById("nro_solicitud").value=d[1];
  document.getElementById("codigo_servicio").value=d[2];  
  document.getElementById("estado").value=d[3];  //estado
  document.getElementById("admin").value=d[4];  //tipo admin
  document.getElementById("direccion").value=d[5];  //link destino
  document.getElementById("observaciones").value=d[6];  //obs
  //para los que tenga q
  var id_servicioibnored=document.getElementById("id_servicioibnored").value;  //obs
  var id_servicioibnored_rol=document.getElementById("id_servicioibnored_rol").value;  //obs
  var id_servicioibnored_s=document.getElementById("id_servicioibnored_s").value;  //obs
  var id_servicioibnored_u=document.getElementById("id_servicioibnored_u").value;  //obs

  document.getElementById("id_servicioibnored_modal").value=id_servicioibnored;  //obs
  document.getElementById("id_servicioibnored_rol_modal").value=id_servicioibnored_rol;  //obs
  document.getElementById("id_servicioibnored_s_modal").value=id_servicioibnored_s;  //obs
  document.getElementById("id_servicioibnored_u_modal").value=id_servicioibnored_u;  //obs
}

function modalReenviarSolicitudDevuelto(datos){  
  var d=datos.split('###');  
  document.getElementById("cod_solicitudfacturacion_r").value=d[0];
  document.getElementById("nro_solicitud_r").value=d[1];
  document.getElementById("codigo_servicio_r").value=d[2];  
  document.getElementById("estado_r").value=d[3];  //estado
  document.getElementById("admin_r").value=d[4];  //tipo admin
  document.getElementById("direccion_r").value=d[5];  //link destino
  document.getElementById("observaciones_r").value=d[6];  //obs

   //para los que tenga q
  var q=document.getElementById("q").value;  //obs
  var s=document.getElementById("s").value;  //obs
  var u=document.getElementById("u").value;  //obs
  var v=document.getElementById("v").value;  //obs

  document.getElementById("q").value=q;  //obs
  document.getElementById("s").value=s;  //obs
  document.getElementById("u").value=u;  //obs
  document.getElementById("v").value=v;  //obs


}
function registrarRechazoSolicitud(cod_solicitudfacturacion,observaciones,estado,admin,direccion,q,s,u,v){
  $.ajax({
    type:"POST",
    data:"cod_solicitudfacturacion="+cod_solicitudfacturacion+"&observaciones="+observaciones,
    url:"simulaciones_servicios/save_solicitud_rechazada.php",
    success:function(r){
      if(r==1){
        if(q==0){
          alerts.showSwal('success-message',direccion+'?cod='+cod_solicitudfacturacion+'&estado='+estado+'&admin='+admin);          
        }else{
          alerts.showSwal('success-message',direccion+'?cod='+cod_solicitudfacturacion+'&estado='+estado+'&admin='+admin+'&q='+q+'&s='+s+'&u='+u+'&v='+v);
        }
      }else{
        Swal.fire("A ocurrido un error!", "No se pudo devolver la solicitud.", "warning");        
      }
    }
  });
}

function registrarRechazoSolicitud_intranet(cod_solicitudfacturacion,observaciones,estado,admin,direccion,q,r,s,u){
  $.ajax({
    type:"POST",
    data:"cod_solicitudfacturacion="+cod_solicitudfacturacion+"&observaciones="+observaciones,
    url:"simulaciones_servicios/save_solicitud_rechazada.php",
    success:function(r){
      if(r==1){
        if(q==0){
          alerts.showSwal('success-message',direccion+'?cod='+cod_solicitudfacturacion+'&estado='+estado+'&admin='+admin);          
        }else{
          alerts.showSwal('success-message',direccion+'?cod='+cod_solicitudfacturacion+'&estado='+estado+'&admin='+admin+'&q='+q+'&v='+r+'&s='+s+'&u='+u);
        }
        
      }else{
        Swal.fire("A ocurrido un error!", "No se pudo devolver la solicitud.", "warning");        
      }
    }
  });
}
function registrarRechazoFactura(cod_solicitudfacturacion,observaciones,estado,admin,direccion,codigo_factura,codigo_comprobante,estado_factura){
  $.ajax({
    type:"POST",
    data:"codigo_factura="+codigo_factura+"&cod_solicitudfacturacion="+cod_solicitudfacturacion+"&observaciones="+observaciones+"&codigo_comprobante="+codigo_comprobante+"&estado_factura="+estado_factura,
    url:"simulaciones_servicios/anular_facturaGenerada.php",
    success:function(r){
      if(r==1){        
          alerts.showSwal('success-message',direccion);        
      }else{
        Swal.fire("A ocurrido un error!", "No se pudo Anular la Factura.", "warning");        
      }
    }
  });
}

function cargarLotesPago(){
  $("#modalLotesPago").modal("show");
}

function cargarDatosProveedorPagosLote(fila){
  var prov = $("#proveedor").val().split("####");
  var proveedor = prov[0];
  if($("#cod_pagoloteedit").length>0){
      var url ="ajaxListPagosLote.php";
    }else{
      var url ="obligaciones_pago/ajaxListPagosLote.php";
    } 
  var parametros={"proveedor":proveedor,"proveedor_nombre":prov[1],"fila":fila};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: url,
        data: parametros,
        beforeSend: function () {
        $("#texto_ajax_titulo").html("Listando Pagos  de "+prov[1]); 
          iniciarCargaAjax();
        },        
        success:  function (resp) {
          detectarCargaAjax();
           $("#texto_ajax_titulo").html("Procesando Datos");
           $("#data_pagosproveedores").append(resp);
           $('.selectpicker').selectpicker("refresh");
        }
      });
}
function agregarLotePago(){

 if($("#proveedor").val()!="####"){
  var codigo= $("#proveedor").val().split("####");
  var cant= parseInt($("#cantidad_proveedores").val());
  var existe=0;
  for (var i = 1; i <= cant; i++) {
    var codFila=$("#codigo_proveedor_modal"+i).val();
    if(codFila==codigo[0]){
      existe++;
    }
  };
cant++;
  if(existe==0){
    cargarDatosProveedorPagosLote(cant);   
  }else{
   Swal.fire("Informativo!", "Ya existe el proveedor en la lista.", "warning");         
  }
 }else{
   Swal.fire("Informativo!", "Debe seleccionar un proveedor.", "warning");        
 }
}

function removeListaPago(codigo){
  $('.fila_proveedor'+codigo).remove();
  $('#f_proveedor'+codigo).remove();
}



function nuevoArchivoTxtPagoLote(){
  $("#modal_txtarchivo").modal("show");
}
function activarInputFilaPago(fila){
  if(!($("#codigo_pagofila"+fila).is("[readonly]"))){
    $("#codigo_pagofila"+fila).attr("readonly",true);
  }else{
    $("#codigo_pagofila"+fila).removeAttr("readonly");
  }
}

function generarArchivosTXTVarios(){
  swal({
        title: '¿Estás Seguro?',
        text: "Se creara el archivo txt de los pagos seleccionados",
        type: 'warning',
        showCancelButton: true,
        confirmButtonClass: 'btn btn-warning',
        cancelButtonClass: 'btn btn-danger',
        confirmButtonText: 'Si, Crear!',
        cancelButtonText: 'No, Cancelar!',
        buttonsStyling: false
      }).then((result) => {
          if (result.value) {
            generarArchivoTxtVariosAjax(); 
            return(true);
          } else if (result.dismiss === Swal.DismissReason.cancel) {
            return(false);
          }
        });
  
}

function generarArchivoTxtVariosAjax(){
  var cantidad=parseInt($("#cantidad_filaspago").val());
  var codigos=[];var habilitados=[];
  for (var i = 0; i < cantidad; i++) {
    var habilitado=1;
    if($("#codigo_pagofila"+i).is("[readonly]")){
        habilitado=0;
     codigos[i] = 0;     
    }else{
     codigos[i] = $("#codigo_pagofila"+i).val();  
    } 
  };
  
  //var parametros={"codigos":JSON.stringify(codigos)};
  var xhr = new XMLHttpRequest();
    xhr.open('GET', "obligaciones_pago/generarTXTLotes.php?codigos="+JSON.stringify(codigos), true);
    xhr.responseType = 'blob';
    xhr.onload = function(e) {
      if (this.status == 200) {
         var blob = new Blob([this.response], {type: 'text/plain'});
         // This fires after the blob has been read/loaded.
       const reader = new FileReader();  
       reader.addEventListener('loadend', (bb) => {
        const text = bb.srcElement.result;
        //alert(text)
        if(text=="0000"){
           swal("Error!", "Ocurrio un Error al generar el txt.", "danger");
        }else{
          if(text=="1111"){
           swal("Informativo!", "No hay registros.", "warning");
          }else{
            var link = document.createElement('a');
               link.href = window.URL.createObjectURL(blob);
               link.download = "data_pago.txt";
               //link.click();
             swal({
               title: 'Correcto!',
               text: "El Archivo se generó exitosamente",
               type: 'success',
               confirmButtonClass: 'btn btn-danger',
               confirmButtonText: '<i class="material-icons">get_app</i> DESCARGAR EL ARCHIVO',
               buttonsStyling: false
              }).then((value) => {
                link.click();
               javascript:location.reload(true);
           });  
          }
        }
      });
      // Start reading the blob as text.
       reader.readAsText(blob);
      }
    };
    xhr.send();
}

function subirArchivoExcelLibretaBancaria(tipo,nombre_tipo){
  if(tipo==1){
    if($("#tabla_muestra_formato_a").hasClass("d-none")){
      $("#tabla_muestra_formato_a").removeClass("d-none");
      $("#tabla_muestra_formato_b").addClass("d-none");
    }
  }else{
    if($("#tabla_muestra_formato_b").hasClass("d-none")){
      $("#tabla_muestra_formato_b").removeClass("d-none");
      $("#tabla_muestra_formato_a").addClass("d-none");
    }
  }
  $("#tipo_formato").val(tipo);
  $("#formato_texto").html(nombre_tipo);
  $("#tipo_formato_titulo2").html(nombre_tipo);
  $("#modalSubirArchivoExcel").modal("show");
}


function abrirLibretaBancaria(datos,direccion,indice){
  // iniciarCargaAjax();
  var d=datos.split('/');
  var cod_solicitudfacturacion=d[0];
  var saldo=d[2];
  var razon_social=d[5];
  document.getElementById("cod_solicitudfacturacion").value=cod_solicitudfacturacion;
  document.getElementById("direccion").value=direccion;
  document.getElementById("indice").value=indice;
  document.getElementById("datos").value=datos;
  $("#modalListaLibretaBancaria").modal("show");          
  // var table =$("#libreta_bancaria_reporte_modal");
  // table.fixedHeader.enable();

  var contenedor = document.getElementById('contenedor_cabecera_libreta_bancaria');    
  ajax=nuevoAjax();
  ajax.open('GET', 'simulaciones_servicios/ajax_cabecera_modal_libretaBancaria.php?saldo='+saldo+'&razon_social='+razon_social,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;      
      $('.selectpicker').selectpicker(["refresh"]);
      //ajax_contenedor_tabla_libretaBancaria(saldo);
      // detectarCargaAjax();      
    }
  }
  ajax.send(null);
}

function ajax_contenedor_tabla_libretaBancaria(saldo){
  document.getElementById("saldo_x").value=saldo;
  var contenedor = document.getElementById('contenedor_tabla_libreta_bancaria');
  var parametros={"saldo":saldo,"tipo_listado":0};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "simulaciones_servicios/ajax_listado_libreta_bancaria.php",
        data: parametros,
        beforeSend: function () {
        $("#texto_ajax_titulo").html("Listando las libretas bancarias..."); 
          iniciarCargaAjax();
        },        
        success:  function (resp) {
          detectarCargaAjax();
           $("#texto_ajax_titulo").html("Procesando Datos");
          contenedor.innerHTML = resp;      
          $('.selectpicker').selectpicker(["refresh"]);
          cargar_dataTable_ajax('libreta_bancaria_reporte_modal');
          cargar_filtro_datatable_ajax('modalListaLibretaBancaria');
        }
      });
}

function ajax_contenedor_tabla_libretaBancariaIndividual(idLib){
  
  if($("#tipo_comprobante").length>0){
    var saldo="";  
    var url = "../simulaciones_servicios/ajax_listado_libreta_bancaria.php";
  }else{
   var saldo=$("#saldo_x").val();
   var url = "simulaciones_servicios/ajax_listado_libreta_bancaria.php";
   if($("#cantidad_filas_libretas").length>0){
     $("#cantidad_filas_libretas").val(0);
     $("#datos_libreta_bancaria_detalle").html("");
   }
  }  
  var contenedor = document.getElementById('contenedor_tabla_libreta_bancaria');
  var parametros={"saldo":saldo,"tipo_listado":0,"codigo_lib":idLib};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: url,
        data: parametros,
        beforeSend: function () {
        $("#texto_ajax_titulo").html("Listando las libretas bancarias..."); 
          iniciarCargaAjax();
        },        
        success:  function (resp) {
          detectarCargaAjax();
           $("#texto_ajax_titulo").html("Procesando Datos");
          contenedor.innerHTML = resp;   
          if($("#tipo_comprobante").length>0){
            $(".list-de-fac").addClass("d-none");
          }else{
            $(".list-de-com").addClass("d-none");
          }   
          $('.selectpicker').selectpicker(["refresh"]);
          cargar_dataTable_ajax('libreta_bancaria_reporte_modal');
          cargar_filtro_datatable_ajax('modalListaLibretaBancaria');
        }
      });
}

function ajax_contenedor_tabla_libretaBancariaCompro(){
  var contenedor = document.getElementById('contenedor_tabla_libreta_bancaria');
  var parametros={"tipo_listado":0};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "../simulaciones_servicios/ajax_listado_libreta_bancaria_comprobantes.php",
        data: parametros,
        beforeSend: function () {
        $("#texto_ajax_titulo").html("Listando las libretas bancarias..."); 
          iniciarCargaAjax();
        },        
        success:  function (resp) {
          detectarCargaAjax();
           $("#texto_ajax_titulo").html("Procesando Datos");
          contenedor.innerHTML = "";      
          $('.selectpicker').selectpicker(["refresh"]);
          cargar_dataTable_ajax('libreta_bancaria_reporte_modal');
          cargar_filtro_datatable_ajax('modalListaLibretaBancaria');
        }
      });
}

function cargar_dataTable_ajax(tabla){
  // Setup - add a text input to each footer cell
  $('#'+tabla+' tfoot th').each( function () {
      var title = $(this).text();
      $(this).html( '<input type="text" placeholder="'+title+'" />' );
  } );

  // DataTable
  var table = $('#'+tabla+'').DataTable({
      initComplete: function () {
          // Apply the search
          this.api().columns().every( function () {
              var that = this;
              $( 'input', this.footer() ).on( 'keyup change clear', function () {
                  if ( that.search() !== this.value ) {
                      that
                          .search( this.value )
                          .draw(); 
                   ponerSumatoriaDeMayorCuenta();       
                      
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
}
function cargar_filtro_datatable_ajax(modal){
  $('#'+modal).on('shown.bs.modal', function(e){
     $($.fn.dataTable.tables(true)).DataTable()
        .columns.adjust();
  });
}

function ajax_listado_libreta_bancaria_filtrar(){  
  var saldo=document.getElementById("saldo_x").value;
  var contenedor = document.getElementById('contenedor_tabla_libreta_bancaria');    
  // alert(saldo);
  ajax=nuevoAjax();
  ajax.open('GET', 'simulaciones_servicios/ajax_listado_libreta_bancaria.php?saldo='+saldo+'&tipo_listado=1',true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;      
      $('.selectpicker').selectpicker(["refresh"]);
      cargar_dataTable_ajax('libreta_bancaria_reporte_modal');
      cargar_filtro_datatable_ajax('modalListaLibretaBancaria');
    }
  }
  ajax.send(null);
}
function seleccionar_libretaBancaria(cod_libreta){
  var indice=document.getElementById("indice").value;
  var datos=document.getElementById("datos").value;
  var cod_solicitudfacturacion=document.getElementById("cod_solicitudfacturacion").value;
  var direccion=document.getElementById("direccion").value;
  if(indice==1){//generar factura normal
    $("#modalListaLibretaBancaria").modal("hide");
    $("#modalListaLibretasBancariasDetalle").modal("hide");
    
    alerts.showSwal('warning-message-and-confirmation-generar-factura',direccion+'?codigo='+cod_solicitudfacturacion+'&cod_libreta='+cod_libreta);
  }else{
    if(indice==2){
      // $("#modalListaLibretaBancaria").modal("hide");  
      // var d=datos.split('/');
      // var cod_solicitudfacturacion=d[0];
      // document.getElementById("cod_solicitudfacturacion_factpagos").value=cod_solicitudfacturacion;
      // document.getElementById("cod_libreta_pagos").value=cod_libreta;
      // var index=(parseFloat(d[3])-1);      
      // var contenedor = document.getElementById('contenedor_GenerarFactParcial_cabecera');    
      // ajax=nuevoAjax();
      // ajax.open('GET', 'simulaciones_servicios/ajax_cabecera_modal_generarFactPar.php?cod_solicitud='+cod_solicitudfacturacion,true);
      // ajax.onreadystatechange=function() {
      //   if (ajax.readyState==4) {
      //     contenedor.innerHTML = ajax.responseText;      
      //     $('.selectpicker').selectpicker(["refresh"]);
      //     $("#modalGenerarFacturapagos").modal("show");  
      //     tablaGeneral_GenerarFact_parcial(index);
      //   }
      // }
      // ajax.send(null);
    }else{
      if(indice==3){ //generar factura manual
        $("#modalListaLibretaBancaria").modal("hide");
        $("#modalListaLibretasBancariasDetalle").modal("hide");
        $("#modalFacturaManual").modal("show");  
          var d=datos.split('/');
          document.getElementById("cod_solicitudfacturacion_factmanual").value=d[0];  
          document.getElementById("cod_libreta_manual").value=cod_libreta;
          document.getElementById("importe_total").value="Saldo de Solicitud de Facturacón: "+number_format(d[2],2);
          document.getElementById("nit_cliente").value=d[4];
          document.getElementById("razon_social").value=d[5];
      }
    }
  }
  
  // alert(direccion);
  // alert("ok"+cod_libreta_Det);  
}
function activardetalleLibreta(j){  
  $(".libretaDetalles_"+j).toggle();
}

function abrirEstadoCuenta(datos,direccion,indice){
  // iniciarCargaAjax();
  var d=datos.split('/');
  var cod_solicitudfacturacion=d[0];
  var saldo=d[2];
  var razon_social=d[5];
  document.getElementById("cod_solicitudfacturacion_ec").value=cod_solicitudfacturacion;
  document.getElementById("direccion_ec").value=direccion;
  document.getElementById("indice_ec").value=indice;//comprobamos si es para factura nomra o manual
  document.getElementById("datos_ec").value=datos;
  $("#modal_estadocuenta").modal("show");          
  // var table =$("#libreta_bancaria_reporte_modal");
  // table.fixedHeader.enable();
  var contenedor = document.getElementById('contenedor_cabecera_estados_cuenta');    
  ajax=nuevoAjax();
  ajax.open('GET', 'simulaciones_servicios/ajax_cabecera_modal_estadoscuenta.php?saldo='+saldo+'&razon_social='+razon_social,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText;      
      $('.selectpicker').selectpicker(["refresh"]);
      ajax_contenedor_tabla_estados_cuenta(saldo);
      // detectarCargaAjax();      
    }
  }
  ajax.send(null);
}
function ajax_contenedor_tabla_estados_cuenta(saldo){
  document.getElementById("saldo_ec").value=saldo;
  var contenedor = document.getElementById('contenedor_tabla_estados_cuenta');
  var parametros={"saldo":saldo,"tipo_listado":0};
  $.ajax({
    type: "GET",
    dataType: 'html',
    url: "simulaciones_servicios/ajax_listado_estados_cuenta.php",
    data: parametros,
    beforeSend: function () {
    $("#texto_ajax_titulo").html("Listando los Estados de Cuenta..."); 
      iniciarCargaAjax();
    },
    success:function (resp) {
      detectarCargaAjax();
       $("#texto_ajax_titulo").html("Procesando Datos");
      contenedor.innerHTML = resp;
      $('.selectpicker').selectpicker(["refresh"]);
      cargar_dataTable_ajax('estados_cuenta_reporte_modal');
      cargar_filtro_datatable_ajax('modal_estadocuenta');
    }
  });
}
function seleccionar_estado_cuenta_sol_fac(cod_estadocuenta){
  var indice=document.getElementById("indice_ec").value;
  var datos=document.getElementById("datos_ec").value;
  var cod_solicitudfacturacion=document.getElementById("cod_solicitudfacturacion_ec").value;
  var direccion=document.getElementById("direccion_ec").value;
  if(indice==1){//factura normal
    $("#modal_estadocuenta").modal("hide");
    alerts.showSwal('warning-message-and-confirmation-generar-factura',direccion+'?codigo='+cod_solicitudfacturacion+'&cod_estadocuenta='+cod_estadocuenta);
  }else{    
      if(indice==3){
        $("#modal_estadocuenta").modal("hide");
        $("#modalFacturaManual").modal("show");  
          var d=datos.split('/');
          document.getElementById("cod_solicitudfacturacion_factmanual").value=d[0];  
          document.getElementById("cod_libreta_manual").value=cod_libreta;
          document.getElementById("importe_total").value="Saldo de Solicitud de Facturacón: "+number_format(d[2],2);
          document.getElementById("nit_cliente").value=d[4];
          document.getElementById("razon_social").value=d[5];
      }    
  }
  
  // alert(direccion);
  // alert("ok"+cod_libreta_Det);  
}
function abrirArchivosAdjuntos(datos){
  var d=datos.split('/');
  var cod_solicitudfacturacion=d[0];
  var contenedor = document.getElementById('contenedor_archivos_respaldo_sol_fac');    
  ajax=nuevoAjax();
  ajax.open('GET', 'simulaciones_servicios/ajax_modal_archivos_view.php?cod_facturacion='+cod_solicitudfacturacion,true);
  ajax.onreadystatechange=function() {
  if (ajax.readyState==4) {
    contenedor.innerHTML = ajax.responseText;      
    $('.selectpicker').selectpicker(["refresh"]);
    $("#modalFile_view").modal("show");       
  }
  }
  ajax.send(null);
}
function botonBuscarLibretaBancariaDetalle(){
  $("#modalBuscadorLibretaBancaria").modal("hide");
  // $("#modalListaLibretaBancaria").modal("hide");
  iniciarCargaAjax();
  // $("#modalListaLibretaBancaria").modal("show");
  var valor_info=$("#informacion_libreta").val();
  var valor_monto=$("#monto_libreta").val();
  var valor_rs=$("#razon_social_libreta").val();  
  var valor_datos=$("#datos").val();  
  var datos=valor_datos.split('/');
  var valor_saldo=datos[2];
  contenedor_p = document.getElementById('div_contenedor_libretaBancaria');
  ajax=nuevoAjax();
  ajax.open('GET', 'simulaciones_servicios/ajax_listado_libreta_bancaria_buscador.php?informacion='+valor_info+'&monto='+valor_monto+'&razon_social='+valor_rs+'&saldo='+valor_saldo,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor_p.innerHTML = ajax.responseText;
      $('.selectpicker').selectpicker(["refresh"]);
      detectarCargaAjax();
    }
  }
  ajax.send(null);
}

$(document).ready(function() {
  $(".csp").each(function(){
    var cantidad =  $(this).attr("colspan");
    //alert(cantidad);
    for (var i = 1; i < parseInt(cantidad); i++) {
       $(this).after("<td class='d-none'></td>");
    };
   });
});

function limpiarDetalleSolicitud(){
    Swal.fire({
        title: '¿Esta Seguro?',
        text: "Se limpiará los registros del Detalle",
         type: 'warning',
        showCancelButton: true,
        confirmButtonClass: 'btn btn-warning',
        cancelButtonClass: 'btn btn-danger',
        confirmButtonText: 'Si',
        cancelButtonText: 'No',
        buttonsStyling: false
       }).then((result) => {
          if (result.value) {
               $("#fiel").html("");            
            return(true);
          } else if (result.dismiss === Swal.DismissReason.cancel) {
            return(false);
          }
        });
}
function eliminarRelacionFactura(codigo,codigo_libreta_detalle){
  var cod_libreta=document.getElementById("codigo_libreta").value;
  swal({
        title: '¿Estás Seguro?',
        text: "¡No podra revertir el borrado!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonClass: 'btn btn-success',
        cancelButtonClass: 'btn btn-danger',
        confirmButtonText: 'Si, Borrar!',
        cancelButtonText: 'No, Cancelar!',
        buttonsStyling: false
      }).then((result) => {
          if (result.value) {
            $.ajax({
              type:"POST",
              data:"codigo="+codigo+"&codigo_libdet="+codigo_libreta_detalle,
              url:"libretas_bancarias/saveDelete_relacion_factura.php",
              success:function(r){
                if(r==1){
                    // alerts.showSwal('success-message',direccion+'?cod='+cod_solicitudfacturacion+'&estado='+estado+'&admin='+admin);
                    // Swal.fire("satisfecho!", "No se pudo devolver la solicitud.", "success");
                    alerts.showSwal('success-message','index.php?opcion=listLibretasDetalle&codigo='+cod_libreta);
                }else{
                  Swal.fire("A ocurrido un error!", "No se pudo Eliminar la Relación.", "warning");
                }
              }
            });
            return(true);
          } else if (result.dismiss === Swal.DismissReason.cancel) {
            return(false);
          }
        });
}
function relacionar_factura_libreta(codigo){  
  document.getElementById("cod_libretabancariadetalle").value=codigo;
  $('#modallista_facturas').modal('show');  
}
function seleccionar_Factura_relacion(cod_factura){
  var cod_libreta=document.getElementById("codigo_libreta").value;
  var cod_libretabancariadetalle=document.getElementById("cod_libretabancariadetalle").value;
  swal({
    title: '¿Estás Seguro?',
    text: "¡No podra revertir el Proceso!",
    type: 'warning',
    showCancelButton: true,
    confirmButtonClass: 'btn btn-success',
    cancelButtonClass: 'btn btn-danger',
    confirmButtonText: 'Si, Relacionar!',
    cancelButtonText: 'No, Cancelar!',
    buttonsStyling: false
  }).then((result) => {
    if (result.value) {
      $.ajax({
        type:"POST",
        data:"cod_factura="+cod_factura+"&cod_libretabancariadetalle="+cod_libretabancariadetalle,
        url:"libretas_bancarias/saveRelacionFactura.php",
        success:function(r){
          if(r==1){              
              alerts.showSwal('success-message','index.php?opcion=listLibretasDetalle&codigo='+cod_libreta);
          }else{
            Swal.fire("A ocurrido un error!", "No se pudo realizar la relación.", "warning");
          }
        }
      });
      return(true);
    } else if (result.dismiss === Swal.DismissReason.cancel) {
      return(false);
    }
  });
  
}

function ponerSistemasIntegrados(){
  var tipo_servicio=$("#tipo_servicio").val();
  if(tipo_servicio==2778){ //codigo Sistemas Integrados
    if($("#div_normastipo").hasClass("d-none")){
      $("#div_normastipo").removeClass("d-none");
      $("#div_normastipotexto").removeClass("d-none");
    }
  }else{
    if(!($("#div_normastipo").hasClass("d-none"))){
      $("#div_normastipo").addClass("d-none");
      $("#div_normastipotexto").addClass("d-none");
    }
  }
}

function ponerDescripcionServicio(){
 var objeto = $('#objeto_servicio option:selected').text();
 var tipoServ = $('#tipo_servicio option:selected').text();
 $("#d_servicio").val(tipoServ+" - "+objeto);
}

function mostrar_listado_facturas(){
  $("#modalListaLibretasBancariasDetalle").modal("show");
}

function listar_libretaBancaria(codLibretaDetalle,descripcion){
  if(verificarLibretaCodigo(codLibretaDetalle)==true){
  Swal.fire("A ocurrido un error!", "La libreta ya está seleccionada", "warning");
  }else{
   var n= $("#cantidad_filas_libretas").val();
   n++;
   $("#nfacturaslibretas").html(n);
   $("#cantidad_filas_libretas").val(n);
   agregarLibretaDetalleFactura(codLibretaDetalle,descripcion,n);
  }
}
function agregarLibretaDetalleFactura(codLibretaDetalle,descripcion,n){
  var descripcionList=[];
  if(descripcion!=""){
    descripcionList=descripcion.split("####");
  }else{
    descripcionList[0]="";descripcionList[1]="";descripcionList[2]="";descripcionList[3]="";
  }
   var html ='<tr id="fila_detalle_factura'+n+'">'+
    '<td>'+descripcionList[0]+'<input id="cod_detalle_libreta_bancaria'+n+'" type="hidden" value="'+codLibretaDetalle+'"></td>'+
    '<td>'+descripcionList[1]+'</td>'+
    '<td>'+descripcionList[2]+'</td>'+
    '<td>'+descripcionList[3]+'</td>'+
    '<td><button title="Eliminar de la lista" class="btn btn-sm btn-danger btn-fab" onclick="eliminarLibretaDetalleFactura('+n+')"><i class="material-icons">delete</i></td>'+   
   '</tr>';
   $("#datos_libreta_bancaria_detalle").append(html);
   $("#modalListaLibretasBancariasDetalle").modal("show");
}

function eliminarLibretaDetalleFactura(fila){
  $("#fila_detalle_factura"+fila).remove();
}

function verificarLibretaCodigo(codigo){
   var n = $("#cantidad_filas_libretas").val();
   var existe=false;
   for (var i = 1; i <= n; i++) {
      if($("#cod_detalle_libreta_bancaria"+i).length>0){
        if($("#cod_detalle_libreta_bancaria"+i).val()==codigo){
         existe=true;
        }
      }
   };
   return existe;
}

function facturarLibretaBancaria(){
  var codDetalle=[];
  var index=0;
  for (var i = 1; i <= $("#cantidad_filas_libretas").val(); i++) {
      if($("#cod_detalle_libreta_bancaria"+i).length>0){
         codDetalle[index]=$("#cod_detalle_libreta_bancaria"+i).val();
         index++;
      }
   };
  seleccionar_libretaBancaria(codDetalle.join(","));
}

function listar_comprobanteDetalle(codigo,descripcion){
  var fila=$("#indice").val();
  $("#cod_detallelibreta"+fila).val(codigo);
  $("#descripcion_detallelibreta"+fila).val(descripcion.split("####")[0]+" - "+descripcion.split("####")[1]);
  $("#nestadolib"+fila).addClass("estado");
  $("#modalListaLibretaBancaria").modal("hide");
}


function verMayoresCierre(fila){
 if($("#cuenta"+fila).val()==""){
   $("#msgError").html("<p>Ingrese una cuenta</p>");
   $('#modalAlert').modal('show');
 }else{
  $("#indice").val(fila);
  var cuenta = $("#cuenta"+fila).val();
  var contenedor = document.getElementById('contenedor_tabla_mayores_cuenta');
  $("#modalListaMayoresCuenta").modal("show"); 
    var parametros={
      "moneda":1,
      "fecha_desde":null,
      "fecha_hasta":null,
      "glosa_len":1,
      "unidad_costo":null,
      "area_costo":null,
      "cuenta_especifica":cuenta,
      "cuenta":null,
      "unidad":null,
      "gestion":null,
      "entidad":null
    };
     $.ajax({
        type: "POST",
        dataType: 'html',
        url: "ajax_listar_mayores_cuenta.php",
        data: parametros,
        beforeSend: function () {
        $("#texto_ajax_titulo").html("Obteniendo el Mayor de la cuenta..."); 
          iniciarCargaAjax();
        },        
        success:  function (resp) {
          detectarCargaAjax();
           $("#texto_ajax_titulo").html("Procesando Datos");
          contenedor.innerHTML = resp;     
          $('.selectpicker').selectpicker(["refresh"]);
          cargar_dataTable_ajax('mayores_cuenta_reporte_modal');
          cargar_filtro_datatable_ajax('modalListaMayoresCuenta');
        }
    });
 }
}


function ponerSumatoriaDeMayorCuenta(){
  var fila=$("#indice").val();
  var suma=0;
  var filas = $("#cantidad_mayor_modal").val();
  for (var i = 1; i <=filas; i++) {
    if($("#debe_mayor_ajax"+i).length>0&&!($("#fila_habilitada_mayor"+i).is("[disabled]"))){
      suma+=parseFloat($("#debe_mayor_ajax"+i).val());   
    }
  };
  $("#monto_debe_total_modal").html(redondeo(suma)+" Bs");
  $("#debe"+fila).val(redondeo(suma));
}

var array_comprobantesdetalle=[];
function listar_comprobanteDetalleMayor(codigo,index){
 if($("#fila_habilitada_mayor"+index).is("[disabled]")){
    $("#fila_habilitada_mayor"+index).removeAttr("disabled");
    if($("#boton_habilitado_mayor"+index).hasClass("btn-danger")){
      $("#boton_habilitado_mayor"+index).removeClass("btn-danger");
      $("#boton_habilitado_mayor"+index).addClass("btn-success");
    }        
 }else{
    $("#fila_habilitada_mayor"+index).attr("disabled",true); 
    if($("#boton_habilitado_mayor"+index).hasClass("btn-success")){
      $("#boton_habilitado_mayor"+index).removeClass("btn-success");
      $("#boton_habilitado_mayor"+index).addClass("btn-danger");
    }
 }
 ponerSumatoriaDeMayorCuenta(); 
}
var array_act_proy=[];
function verificarSiEsProyectosFinanciado(fila){
  var tiene = 0;

  var unidad=$("#unidad_fila"+fila).val();
   var parametros={"unidad":unidad};
     $.ajax({
        async:false,
        type: "POST",
        dataType: 'html',
        url: "ajax_verificar_proyecto_sis.php",
        data: parametros,      
        success:  function (resp) {
           if(resp.trim()=="1"){
            tiene=1;
           }else{
            tiene=0;
           }
        }
    });
  return tiene;
}
function listarProyectosSisdeUnidades(){
  array_act_proy=[];
  var n = $("#cantidad_filas").val();
  var elementos=0;
  for (var i = 1; i <=n; i++) {
    if(verificarSiEsProyectosFinanciado(i)==1){
     elementos++;
     array_act_proy.push(i);
    }else{
      $("#cod_actividadproyecto"+i).val(0);
      $("#des_actividadproyecto"+i).val("");
    }
  };
  $("#cantidad_filas_proyecto").val(elementos);
  $("#nproyectos").html(elementos)
}

function mostrarActividadesDeSolicitud(){
  cargarDatosActividadesEnTablaModal();
  $("#modalActividadesProyecto").modal("show");
}
function cargarDatosActividadesEnTablaModal(){
  $("#contenedor_actividadesmodal").html("");
  for (var i = 0; i < array_act_proy.length; i++) {
    var fila=array_act_proy[i];
    var selectHtml = '<select data-size="6" data-live-search="true" class="selectpicker form-control form-control-sm" name="actividades_detalle'+fila+'" id="actividades_detalle'+fila+'" data-style="btn btn-info">';
      selectHtml+=$("#actividades_detalle").html()+'</select>';
    if($("#cod_actividadproyecto"+fila).val()!=0){
      var rowHtml='<tr><td>'+fila+'</td><td>'+selectHtml+'</td><td><a href="#" class="btn btn-sm btn-warning"><small>ASOCIADO</small></a></td></tr>';
    }else{
      var rowHtml='<tr><td>'+fila+'</td><td>'+selectHtml+'</td><td><a href="#" class="btn btn-sm btn-default"><small>PENDIENTE</small></a></td></tr>';
    } 
   $("#contenedor_actividadesmodal").append(rowHtml);  
   $("#actividades_detalle"+fila).val($("#cod_actividadproyecto"+fila).val()); 
  };
  $('.selectpicker').selectpicker("refresh");
}

function guardarActividadFilasDetalle(){
  listarProyectosSisdeUnidades();
  for (var i = 0; i < array_act_proy.length; i++) {
    var fila=array_act_proy[i];
    if($("#actividades_detalle"+fila).val()>0){
      $("#cod_actividadproyecto"+fila).val($("#actividades_detalle"+fila).val()); 
      $("#des_actividadproyecto"+fila).val($('#actividades_detalle'+fila+' option:selected').text().toUpperCase()); 
    }else{
      $("#cod_actividadproyecto"+fila).val(0); 
      $("#des_actividadproyecto"+fila).val("");
    }  
  };
  $("#modalActividadesProyecto").modal("hide");
}