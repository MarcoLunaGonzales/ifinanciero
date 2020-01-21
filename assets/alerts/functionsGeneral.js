function number_format(amount, decimals) {
  amount += ''; // por si pasan un numero en vez de un string
  amount = parseFloat(amount.replace(/[^0-9\.-]/g, '')); // elimino cualquier cosa que no sea numero o punto
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
  if($("#add_boton").length){
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
          if($("#add_boton").length){
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
  
  document.getElementById("totaldeb").value=sumadebe;  
  document.getElementById("totalhab").value=sumahaber;  
}

function ajaxCorrelativo(combo){
  var contenedor = document.getElementById('divnro_correlativo');
  var tipoComprobante=combo.value;
  console.log(tipoComprobante);
  ajax=nuevoAjax();
  ajax.open('GET', 'ajaxCorrelativo.php?tipo_comprobante='+tipoComprobante,true);
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
  console.log(fila);
  document.getElementById('cuenta'+fila).value=codigoCuenta;
  document.getElementById('cuenta_auxiliar'+fila).value=codigoCuentaAux;
  document.getElementById('divCuentaDetalle'+fila).innerHTML='<span class=\"text-danger font-weight-bold\">['+numeroCuenta+']-'+nombreCuenta+' </span><br><span class=\"text-primary font-weight-bold small\">'+nombreCuentaAux+'</span>';
  configuracionCentros(fila,inicio);
  configuracionEstadosCuenta(fila,codigoCuenta,codigoCuentaAux);
  $('#myModal').modal('hide');
  $(".selectpicker").selectpicker('refresh');
  $("#debe"+fila).focus();
}
function configuracionCentros(fila,inicio){
  for (var i = 0; i < configuracionCentro.length; i++) {
    if(configuracionCentro[i].cod_grupo==parseInt(inicio)){
        if(configuracionCentro[i].fijo==1){
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
        }
        
      break;  
    }
  };
}
function configuracionEstadosCuenta(fila,codigoCuenta,codigoCuentaAux){
  for (var i = 0; i < estado_cuentas.length; i++) {
    if(estado_cuentas[i].cod_cuenta==codigoCuenta){
      $("#estados_cuentas"+fila).removeClass("d-none"); 
      $("#tipo_estadocuentas"+fila).val(estado_cuentas[i].tipo);
       if(estado_cuentas[i].tipo==1){
         //$("#debe"+fila).removeAttr("readonly");
         $("#haber"+fila).val("");
         //$("#haber"+fila).attr("readonly","readonly");
       }else{
         //$("#haber"+fila).removeAttr("readonly");
         //$("#debe"+fila).attr("readonly","readonly");
         $("#debe"+fila).val("");
       }     
      break;  
    }else{
      if(estado_cuentas[i].cod_cuentaaux==codigoCuentaAux){
         $("#estados_cuentas"+fila).removeClass("d-none"); 
         $("#tipo_estadocuentas"+fila).val(estado_cuentas[i].tipo);
        if(estado_cuentas[i].tipo==1){
         //$("#debe"+fila).removeAttr("readonly");
         $("#haber"+fila).val("");
         //$("#haber"+fila).attr("readonly","readonly");
       }else{
         //$("#haber"+fila).removeAttr("readonly");
         //$("#debe"+fila).attr("readonly","readonly");
         $("#debe"+fila).val("");
       }     
      break;
      }else{
      $("#estados_cuentas"+fila).removeClass("d-none"); 
      $("#estados_cuentas"+fila).addClass("d-none");
        
      }
    }
  };
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
       $("#estados_cuentas"+nuevoId).attr("onclick","verEstadosCuentas('"+i+"',0)");
       $("#estados_cuentas"+nuevoId).attr("id","estados_cuentas"+i);
       $("#nestado"+nuevoId).attr("id","nestado"+i);     
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
     row.append($('<td>').addClass('').html('<button class="btn btn-danger btn-link" onclick="removeFac('+id+','+i+');"><i class="material-icons">remove_circle</i></button>'));
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
function saveFactura(){
  var index=$('#codCuenta').val();
  var factura={
    nit: $('#nit_fac').val(),
    nroFac: $('#nro_fac').val(),
    fechaFac: $('#fecha_fac').val(),
    razonFac: $('#razon_fac').val(),
    impFac: $('#imp_fac').val(),
    exeFac: $('#exe_fac').val(),
    autFac: $('#aut_fac').val(),
    conFac: $('#con_fac').val()
    }
    
  //cargar el credito fiscal
  //var iva=configuraciones[0].valor;
  //var importeIva=parseFloat($('#imp_fac').val())*(iva/100);
  //var anterior= obtenerImportesFacturaIva(index);
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
 function abrirFactura(index,nit,nro,fecha,razon,imp,exe,aut,con){
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
  $('#exe_fac').val('');$('#aut_fac').val('');$('#con_fac').val('');
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
  alertaModal("<h5><b>AYUDA</b></h5>Los campos en la sección 'REGISTRAR PLANTILLA DE COSTO' son modificables y cuando se presione en el botón 'GUARDAR' ubicado en la parte inferior. También se guardaran los cambios realizados en dicha sección",'bg-secondary','text-white');
}
//plantilla guardar
function guardarPlantilla(){
  var cod=$("#codigo_comprobante").val();
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
          window.location="../index.php?opcion=listComprobantes";
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

  ajax=nuevoAjax();
  ajax.open("GET","ajaxOpenPlantilla.php?codigo="+id,true);
  ajax.onreadystatechange=function(){
  if (ajax.readyState==4) {
    var fi=document.getElementById("fiel");
    fi.innerHTML=ajax.responseText;
    calcularTotalesComprobante("null");
    for (var i = 0; i < cantidadItems; i++) {
        var numeroC=$("#numero_cuenta"+(i+1)).val();
        var inicio=numeroC.substr(0,1);
         configuracionCentros((i+1),inicio);
    };
    $('.selectpicker').selectpicker(["refresh"]);
    $("#modalAbrirPlantilla").modal("hide");
    //$("#mensaje").html("<p class='text-success'>Listado de todas las plantillas</p>");
   }
  }
  ajax.send(null);  
}
function nuevaDistribucionPonerFila(fila){
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
         $("#distFila").val(fila);
      }    
    }   
   }
 }
}
function nuevaDistribucion(){
  var fila = $("#distFila").val();
  var cuenta = $("#cuenta"+fila).val();
  var debe = $("#debe"+fila).val();
  var haber = $("#haber"+fila).val();
  var glosa = $("#glosa_detalle"+fila).val();
  var area=$("#area"+fila).val();
  var cuenta_aux=$("#cuenta_auxiliar"+fila).val();
  var valor=0; var cond=9;
  if(debe==""&&haber==""){
    valor=0;
  }else{
    if(debe==0){
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

  if(valor!=0){
  minusCuentaContable(fila);
  ajax=nuevoAjax();
  ajax.open("GET","ajaxDistribucionGastos.php?area="+area+"&filas="+cantidadItems+"&cuenta_aux="+cuenta_aux+"&cuenta="+cuenta+"&cond="+cond+"&valor="+valor+"&glosa="+glosa+"&listDist="+JSON.stringify(distribucionPor),true);
  ajax.onreadystatechange=function(){
  if (ajax.readyState==4) {
    //var fi=document.getElementById("fiel");
    var fi=$("#fiel");
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
      var contenedor=$("#data_comprobantes");
      contenedor.html(ajax.responseText);
    }
  }
  ajax.send(null)
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
  var utilidadExterna=$("#utilidad_minfuera").val();
  var alumnosLocal=$("#cantidad_alumnosibnorca").val();
  var alumnosExterno=$("#cantidad_alumnosfuera").val();
  var precioLocal=$("#precio_ventaibnorca").val();
  var precioExterno=$("#precio_ventafuera").val();

  if(alumnosLocal==""||alumnosExterno==""||precioLocal==""||precioExterno==""||utilidadLocal==""||utilidadExterna==""||nombre==""||abrev==""||!(unidad>0)||!(area>0)){
    Swal.fire("Informativo!", "Todos los campos son requeridos", "warning");
  }else{
     var parametros={"nombre":nombre,"abrev":abrev,"unidad":unidad,"area":area,"utilidad_local":utilidadLocal,"utilidad_externo":utilidadExterna,"alumnos_local":alumnosLocal,"alumnos_externo":alumnosExterno,"precio_local":precioLocal,"precio_externo":precioExterno};
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
   $("#modalDet").modal("show");
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
  var titulos = $('<tr>').addClass('');
     titulos.append($('<th>').addClass('').text('#'));
     titulos.append($('<th>').addClass('').text('PARTIDA'));
     titulos.append($('<th>').addClass('').text('TIPO'));
     titulos.append($('<th>').addClass('').text('M x MES'));
     titulos.append($('<th>').addClass('').text('M x MODULO'));
     titulos.append($('<th>').addClass('').text('M x PERSONA'));
     titulos.append($('<th>').addClass('').text('OPCION'));
     table.append(titulos);
   for (var i = 0; i < itemDetalle[id-1].length; i++) {
     var row = $('<tr>').addClass('');
     row.append($('<td>').addClass('').text(i+1));
     row.append($('<td>').addClass('').text(" Partida: "+itemDetalle[id-1][i].cuenta));
     row.append($('<td>').addClass('').text(itemDetalle[id-1][i].tipo));
     row.append($('<td>').addClass('').text(itemDetalle[id-1][i].monto_i*$("#cod_mescurso").val()));
     row.append($('<td>').addClass('').text(itemDetalle[id-1][i].monto_fi));
     row.append($('<td>').addClass('').text(Math.round(itemDetalle[id-1][i].monto_cal/$("#alumnos_ibnorca").val())));
     row.append($('<td>').addClass('').html('<button class="btn btn-danger btn-link" onclick="removeDet('+id+','+i+');"><i class="material-icons">remove_circle</i></button>'));
     table.append(row);
   }
   div.append(table);
   $('#divResultadoListaDet').html(div);
 }

 function savePlantillaDetalle(){
  var index=$('#codGrupo').val();
  var str_cuenta=dividirCadena($('#cuenta_detalle').val(),"@");
  if($("#monto_ibnorca").val()==""||$("#monto_f_ibnorca").val()==""||str_cuenta.length==1){
    $("#mensajeDetalle").html("<center><p class='text-danger'>Todos los campos son requeridos</p></center>");
  }else{
    var tiDato=$('#tipo_dato').val();
    var monto_calc=$('#monto_calculado').val();
    if(tiDato==1){
      var monto_ib=$('#monto_calculado').val();
      var monto_fib=$('#monto_calculado').val();   
    }else{
      if($("#monto_ibnorca1").hasClass("d-none")){
         if($("#monto_ibnorca2").hasClass("d-none")){
          if($('#tipo_costo'+index).val()==2){
            var monto_ib=$("#monto_alumno_edit").val()*$("#alumnos_ibnorca").val();
            var monto_fib=$("#monto_alumno_edit").val()*$("#alumnos_ibnorca").val();
          }else{
            var monto_ib=$("#monto_f_ibnorca_edit").val();
            var monto_fib=$("#monto_f_ibnorca_edit").val();
          }
         }else{
           var monto_ib=$("#monto_f_ibnorca_edit").val();
           var monto_fib=$("#monto_f_ibnorca_edit").val();
         }
      }else{
        var monto_ib=$("#monto_ibnorca_edit").val()/$("#cod_mescurso").val();
        var monto_fib=$("#monto_ibnorca_edit").val()/$("#cod_mescurso").val();
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
  $("#modalDet").modal("hide");
  }
  
 }

 function dividirCadena(cadenaADividir,separador) {
   var arrayDeCadenas = cadenaADividir.split(separador);
   return arrayDeCadenas;
}

function mostrarDetalle(id){
  var html="";
  for (var i = 0; i < itemDetalle[id-1].length; i++) {
    html+="<tr><td>"+itemDetalle[id-1][i].cuenta+"</td><td>"+itemDetalle[id-1][i].tipo+"</td><td class='text-right'>"+(itemDetalle[id-1][i].monto_i*$("#cod_mescurso").val())+"</td><td class='text-right'>"+itemDetalle[id-1][i].monto_fi+"</td><td class='text-right'>"+Math.round(itemDetalle[id-1][i].monto_cal/$("#alumnos_ibnorca").val())+"</td></tr>";
  };
  $("#cuerpoDetalle").html(html);
  $("#cabezadetalle").html('<h6 class="card-title">Detale "'+$("#nombre_grupo"+id).val()+'"</h6>');
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
    }else{
        $("#montos_editables").addClass("d-none");
      //calcularMontos();
    }
}
 function calcularMontos(){
  var str_cuenta=dividirCadena($('#cuenta_detalle').val(),"@");
  var idp=str_cuenta[0];
  var unidad=$("#cod_unidad").val();
  var area=$("#cod_area").val();
  var parametros={"idp":idp,"unidad":unidad,"area":area};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "ajaxPartidaPresupuestaria.php",
        data: parametros,
        beforeSend: function () { 
         $("#mensajeDetalle").html("<center><p class='text-muted'>Cálculando espere porfavor...</p></center>"); 
        },
        success:  function (resp) {
          //if($("#tipo_dato").val()==1){
            $("#monto_ibnorca").val(parseFloat(resp)*$("#cod_mescurso").val());
            $("#monto_f_ibnorca").val(parseFloat(resp));
            $("#monto_alumno").val(Math.round(parseFloat(resp)/$("#alumnos_ibnorca").val()));

            $("#monto_calculado").val(parseFloat(resp));
            
            $("#monto_ibnorca_edit").val(parseFloat(resp)*$("#cod_mescurso").val());
            $("#monto_f_ibnorca_edit").val(parseFloat(resp));
            $("#monto_alumno_edit").val(Math.round(parseFloat(resp)/$("#alumnos_ibnorca").val()));

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
function mostrarInputMonto(id){
  if($("#"+id).hasClass("d-none")){
     $("#"+id).removeClass("d-none");
  switch (id){
    case 'monto_ibnorca1':
     if(!($("#monto_ibnorca2").hasClass("d-none"))){
       $("#monto_ibnorca2").addClass("d-none");   
     }
     if(!($("#monto_ibnorca3").hasClass("d-none"))){
       $("#monto_ibnorca3").addClass("d-none");   
     }
    break;
    case 'monto_ibnorca2':
     if(!($("#monto_ibnorca1").hasClass("d-none"))){
       $("#monto_ibnorca1").addClass("d-none");   
     }
     if(!($("#monto_ibnorca3").hasClass("d-none"))){
       $("#monto_ibnorca3").addClass("d-none");   
     }
    break;
    case 'monto_ibnorca3':
     if(!($("#monto_ibnorca2").hasClass("d-none"))){
       $("#monto_ibnorca2").addClass("d-none");   
     }
     if(!($("#monto_ibnorca1").hasClass("d-none"))){
       $("#monto_ibnorca1").addClass("d-none");   
     }
    break;
  }
  }else{
     $("#"+id).addClass("d-none");
  }

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

function ajaxAFunidadorganizacional(combo){
  var contenedor;
  var codigo_UO=combo.value;
  contenedor = document.getElementById('div_contenedor_UO');
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
}


function ajaxPersonalUbicacion(codigo_UO){
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
  var nombre=$("#nombre").val();
  var precio=$("#precio_venta").val();
  var plantilla_costo=$("#plantilla_costo").val();
  if( $("#ibnorca_check").is(':checked') ) {
      var ibnorca=1;
  }else{
      var ibnorca=2;
  }
  if(nombre==""||!(plantilla_costo>0)){
   Swal.fire('Informativo!','Debe llenar los campos!','warning'); 
  }else{
     var parametros={"nombre":nombre,"plantilla_costo":plantilla_costo,"precio":precio,"ibnorca":ibnorca};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "simulaciones_costos/ajaxRegistrarSimulacion.php",
        data: parametros,
        beforeSend: function () { 
         Swal.fire("Informativo!", "Procesando datos! espere...", "warning");
          
        },
        success:  function (resp) {
         alerts.showSwal('success-message','simulaciones_costos/registerSimulacion.php?cod='+resp);
        }
    });
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

function guardarSimulacion(valor){
  Swal.fire({
        title: '¿Esta Seguro?',
        text: "La simulación se enviará para su posterior revisión",
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
         Swal.fire("Envío Existoso!", "Se registradon los datos exitosamente!", "success")
             .then((value) => {
             location.href="../index.php?opcion=listSimulacionesCostos";
         });
         /*$('#msgError4').html("<p class='text-dark font-weight-bold'>"+resp+"Se envio la Simulacion</p>");
         $('#modalSend').modal('show');*/
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
        success:  function (resp) {
         contenedor.html(resp);
         $("#msgError").html("<p class='text-success'><small>Se eliminó el registro exitosamente!</small></p>");
         $('#modalAlert').modal('show');
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
  var precioExterno=$("#precio_venta_fuera").val();
  if(precioLocal==""||precioExterno==""){
   Swal.fire('Informativo!','Debe llenar los campos!','warning');  
  }else{
    ajax=nuevoAjax();
    ajax.open("GET","ajaxRegistrarPrecio.php?local="+precioLocal+"&externo="+precioExterno+"&codigo="+codigo,true);
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
      $("#precio_venta_fuera").val("");
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
      $("#precio_venta_fuera").val("");
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
      "unidad":null
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

 function listarTipoSolicitud(tipo){
  var url="";
  if(tipo==1){
   url="ajaxListSimulacion.php";
  }else{
   url="ajaxListProveedor.php";
  }
  ajax=nuevoAjax();
    ajax.open("GET",url,true);
    ajax.onreadystatechange=function(){
    if (ajax.readyState==4) {
      var fi=$("#lista_tipo");
      fi.html(ajax.responseText);
      fi.bootstrapMaterialDesign();
       $('.selectpicker').selectpicker("refresh");
    }
   }
    ajax.send(null);
 }
 function guardarSolicitudRecursos(){
  var numero=$("#numero").val();
  var tipo=$("#tipo_solicitud").val();
  if(tipo==1){
    var codSim=$("#simulaciones").val();
    var codProv=0;
  }else{
    var codProv=$("#proveedores").val();
    var codSim=0;
  }
  if(numero==""||tipo==""){
   $("#mensaje").html("<center><p class='text-danger'>Todos los campos son requeridos.</p></center>");
  }else{
     var parametros={"numero":numero,"cod_sim":codSim,"cod_prov":codProv};
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
   }
   $('.selectpicker').selectpicker("refresh");
}

function addSolicitudDetalle(obj,tipo) {
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
      if(tipo==1){
        var url="ajaxSolicitudRecursosDetalleSimulacion.php";
      }else{
        var url="ajaxSolicitudRecursosDetalleSimulacion.php";
       // var url="ajaxSolicitudRecursosDetalleProveedor.php";
      }
      ajax=nuevoAjax();
      ajax.open("GET",url+"?idFila="+numFilas+"&codigo="+codigoSol,true);
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
       $("#unidad"+nuevoId).attr("name","unidad"+i);
       $("#unidad"+nuevoId).attr("id","unidad"+i);
       $("#area"+nuevoId).attr("name","area"+i);
       $("#area"+nuevoId).attr("id","area"+i);
       $("#habilitar"+nuevoId).attr("name","habilitar"+i);
       $("#habilitar"+nuevoId).attr("id","habilitar"+i);
       $("#habilitar"+nuevoId).attr("onchange","habilitarFila('"+i+"')");
       if($("#simulacion").length){
        $("#partida_cuenta_id"+nuevoId).attr("name","partida_cuenta_id"+i);
        $("#partida_cuenta_id"+nuevoId).attr("id","partida_cuenta_id"+i);
        $("#partida_cuenta"+nuevoId).attr("name","partida_cuenta"+i);
        $("#partida_cuenta"+nuevoId).attr("id","partida_cuenta"+i);
        $("#detalle_detalle"+nuevoId).attr("name","detalle_detalle"+i);
        $("#detalle_detalle"+nuevoId).attr("id","detalle_detalle"+i);
        $("#importe"+nuevoId).attr("name","importe"+i);
        $("#importe"+nuevoId).attr("id","importe"+i);
        $("#proveedor"+nuevoId).attr("name","proveedor"+i);
        $("#proveedor"+nuevoId).attr("id","proveedor"+i);
       }else{

       }
       $("#boton_remove"+nuevoId).attr("onclick","minusCuentaContable('"+i+"')");
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
      }
     } 
     itemFacturas.splice((idF-1), 1);
      numFilas=numFilas-1;
      cantidadItems=cantidadItems-1;
      filaActiva=numFilas;
      document.getElementById("cantidad_filas").value=numFilas;  
}

var numArchivosDetalle=0;
function archivosPreviewDetalle(send) {
  var fila =$("#codigo_fila").val();
     var x = $("#archivosDetalle");
      var y = x.clone();
      y.attr("id", "archivos"+fila);
      y.attr("name", "archivos"+fila+"[]");
      $("#archivos_fila"+fila).html(y);
      //y.insertAfter("button");
    var inp=document.getElementById("archivosDetalle");
    var inpDetalle=document.getElementById("archivos"+fila);
    if(send!=1){
      $("#lista_archivosdetalle").html("<p class='text-success text-center'>Lista de Archivos</p>");
      for (var i = 0; i < inpDetalle.files.length; i++) {
       numArchivosDetalle++;
        var name = inpDetalle.files.item(i).name;
        $("#lista_archivosdetalle").append("<div class='text-left'><label>"+name+"</label></div>");
      };
       $("#narch"+fila).addClass("estado");
     }else{
      numArchivosDetalle=0;
        $("#lista_archivosdetalle").html("Ningun archivo seleccionado");
        $("#narch"+fila).removeClass("estado");
     }
   }

  function addArchivos(fila){
    $("#codigo_fila").val(fila);
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
     $("#importe_label"+fila).text("Importe - "+respuesta[1].substr(0,3)+"...");
  }else{
    $("#mensaje_retencion").html("<p class='text-danger'>Debe seleccionar al menos una retención</p>");
  }
  
 }

 function addSolicitudDetalleSearch() {
    var codigoSol=$("#cod_solicitud").val();
    var codCuenta=$("#cuenta_proveedor").val();
    var fechai=$("#fecha_desde").val();
    var fechaf=$("#fecha_hasta").val();
     var fi = $('#solicitud_proveedor');
      ajax=nuevoAjax();
      ajax.open("GET","ajaxSolicitudRecursosDetalleProveedor.php?codigo="+codigoSol+"&fecha_i="+fechai+"&fecha_f="+fechaf+"&cod_cuenta="+codCuenta,true);
      ajax.onreadystatechange=function(){
        if (ajax.readyState==4) {
          itemFacturas=[];
          fi.html("");
          fi.html(ajax.responseText);
          fi.bootstrapMaterialDesign();
          $('.selectpicker').selectpicker("refresh");
          return false;
       }
      }   
      ajax.send(null);
//perosnal area distribucion
}
function modificarMontos(){
  $('#modalEditPlantilla').modal('hide');
  $('#modalSimulacionCuentas').modal('show');
}
function cargarCuentasSimulacion(cods,ib){
  var fi = $('#cuentas_simulacion');
  var codp=$("#partida_presupuestaria").val();
  var codpar=$("#cod_plantilla").val();
  var al_i=$("#alumnos_plan").val();
  var al_f=$("#alumnos_plan_fuera").val();
  if(codp!=""){
  ajax=nuevoAjax();
  ajax.open("GET","ajaxCargarCuentas.php?codigo="+codp+"&codSim="+cods+"&ibnorca="+ib+"&codPar="+codpar+"&al_i="+al_i+"&al_f="+al_f,true);
  ajax.onreadystatechange=function(){
        if (ajax.readyState==4) {
          fi.html("");
          fi.html(ajax.responseText);
       }
      }   
  ajax.send(null);   
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

  document.getElementById("cod_uoE").value=d[2];
  document.getElementById("cod_areaE").value=d[3];
  document.getElementById("porcentajeE").value=d[4];
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

function filaTablaAGeneral(tabla,index){
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

function RegistrarContratoPersonal(cod_personal,cod_tipocontrato,fecha_inicio){
  $.ajax({
    type:"POST",
    data:"cod_contrato=0&cod_personal="+cod_personal+"&cod_tipocontrato="+cod_tipocontrato+"&cod_estadoreferencial=1&fecha_inicio="+fecha_inicio,
    url:"personal/savePersonalcontrato.php",
    success:function(r){
      if(r==1){
        //$('#tabla1').load('index.php');
        // alertify.success("agregado");
        alerts.showSwal('success-message','index.php?opcion=FormPersonalContratos&codigo='+cod_personal);
      }
    }
  });
}
function EditarContratoPersonal(codigo_contratoE,codigo_personalE,cod_tipocontratoE,fecha_inicioE){
  $.ajax({
    type:"POST",

    data:"cod_contrato="+codigo_contratoE+"&cod_personal=0&cod_tipocontrato="+cod_tipocontratoE+"&cod_estadoreferencial=2&fecha_inicio="+fecha_inicioE,
    url:"personal/savePersonalcontrato.php",
    success:function(r){
      if(r==1){
        //$('#tabla1').load('index.php');
        // alertify.success("agregado");
        alerts.showSwal('success-message','index.php?opcion=FormPersonalContratos&codigo='+codigo_personalE);
      }
    }
  });
}
function EditarEvaluacionPersonal(codigo_contratoEv,codigo_personalEv,fecha_EvaluacionEv){
  $.ajax({
    type:"POST",

    data:"cod_contrato="+codigo_contratoEv+"&cod_personal=0&cod_tipocontrato=0&cod_estadoreferencial=4&fecha_inicio="+fecha_EvaluacionEv,
    url:"personal/savePersonalcontrato.php",
    success:function(r){
      if(r==1){
        //$('#tabla1').load('index.php');
        // alertify.success("agregado");
        alerts.showSwal('success-message','index.php?opcion=FormPersonalContratos&codigo='+codigo_personalEv);
      }
    }
  });
}
function EliminarContratoPersonal(codigo_contratoB,codigo_personalB){
  $.ajax({
    type:"POST",
    data:"cod_contrato="+codigo_contratoB+"&cod_personal=0&cod_tipocontrato=1&cod_estadoreferencial=3&fecha_inicio=0000-00-00",
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


//=======
function calcularTotalPartida(){
  var suma=0;
  var total= $("#numero_cuentas").val();
  var monto_anterior=parseFloat($("#monto_designado").val());
  for (var i=1;i<=(total-1);i++){
    if($("#cod_ibnorca").val()==1){
    $("#monto_mod"+i).val(parseFloat($("#monto_modal"+i).val())*parseInt($("#alumnos_plan").val()));
    }else{
     $("#monto_mod"+i).val(parseFloat($("#monto_modal"+i).val())*parseInt($("#alumnos_plan_fuera").val()));
    }
    suma+=parseFloat($("#monto_mod"+i).val());
  }
  const rest=Math.abs(suma-monto_anterior);
  const porcent=(rest*100)/monto_anterior; 
  var resultPorcent= Math.round(porcent*100)/100;  
  var result=Math.round(suma*100)/100;
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
function guardarCuentasSimulacion(ib){
  var total= $("#numero_cuentas").val();
  var cosSim=$("#cod_simulacion").val();
  var conta=0;
  if((total-1)!=0){
    for (var i=1;i<=(total-1);i++){
      if($("#monto_mod"+i).val()==""){
        conta++
      }
    }
  if(conta==0){
    for (var i=1;i<=(total-1);i++){
      var codigo = $("#codigo"+i).val();
      var monto = $("#monto_mod"+i).val();
      var parametros = {"codigo":codigo,"monto":monto,"ibnorca":ib};
      $.ajax({
        type:"GET",
        data:parametros,
        url:"ajaxSaveCuentas.php",
        beforeSend: function () { 
          $("#guardar_cuenta").text("espere.."); 
          $("#guardar_cuenta").attr("disabled",true);
          $("#mensaje_cuenta").html("");
        },
        success:function(resp){
          $("#guardar_cuenta").text("Guardar");
          $("#guardar_cuenta").removeAttr("disabled");
          $("#mensaje_cuenta").html("<p class='text-success'>Se insertaron los datos correctamente! <a class='btn btn-warning btn-sm' href=''>aplicar cambios a la simulación</a></p>");
        }
      });
    }
   }else{
    $("#mensaje_cuenta").html("<p class='text-danger'>No debe haber un campo vacío!</p>");
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
function buscarCuentaNumero(numeros,val){  
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
    //var n = itemCuentas[i].numero.search(/+str+/);
    if(val==1){
       var n = itemCuentas[i].numero.indexOf(numeros);
    }else{
      var cadenaBuscar=itemCuentas[i].nombre.toLowerCase();
       var n = cadenaBuscar.indexOf(numeros.toLowerCase());
    }
    
    if(n==0){
      var textoAux="<table class='table table-condensed'>";
        for (var j = 0; j < itemCuentasAux.length; j++) {
          if(itemCuentasAux[j].codCuenta==itemCuentas[i].codigo){
            textoAux+="<tr>"+
               "<td class='text-left small'>"+itemCuentasAux[j].codigo+"</td>"+
               "<td class='text-left small'><a href=\"javascript:setBusquedaCuenta(\'"+itemCuentas[i].codigo+"\',\'"+itemCuentas[i].numero+"\',\'"+itemCuentas[i].nombre+"\',\'"+itemCuentasAux[j].codigo+"\',\'"+itemCuentasAux[j].nombre+"\');\">"+itemCuentasAux[j].nombre+"</a></td>"+
             "</tr>";
          }
        };
       textoAux+="</table>";
      html+="<tr>"+
      "<td class='text-left'>"+itemCuentas[i].numero+"</td>"+
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

// ESTADOS DE CUENTAS/////////////////////////////////////7

function verEstadosCuentas(fila,cuenta){
  if(($("#debe"+fila).val()==""&&$("#haber"+fila).val()=="")||($("#debe"+fila).val()==0&&$("#haber"+fila).val()==0)){
     $('#msgError').html("<p>El debe o el haber deben de ser llenados</p>");
     $("#modalAlert").modal("show");
  }else{
    if(cuenta==0){
      var cod_cuenta=$("#cuenta"+fila).val();
    }else{
      var cod_cuenta=cuenta;
    }
    var tipo=$("#tipo_estadocuentas"+fila).val();
    if(tipo==1){
      $("#monto_estadocuenta").val($("#debe"+fila).val());
      if(!($("#div_cuentasorigen").hasClass("d-none"))){
        $("#div_cuentasorigen").addClass("d-none");
        $("#div_cuentasorigendetalle").addClass("d-none"); 
      }      
    }else{
      $("#monto_estadocuenta").val($("#haber"+fila).val());
      if($("#div_cuentasorigen").hasClass("d-none")){
        $("#div_cuentasorigen").removeClass("d-none");
        $("#div_cuentasorigendetalle").removeClass("d-none");
      } 
      var cod_cuenta = $("#cuentas_origen").val();
    }
    //ajax estado de cuentas
    var parametros={"cod_cuenta":cod_cuenta,"tipo":tipo,"mes":12};
    $.ajax({
        type: "GET",
        dataType: 'html',
        url: "../estados_cuenta/ajaxMostrarEstadosCuenta.php",
        data: parametros,
        success:  function (resp) {
          var respuesta=resp.split('@');
          $("#div_estadocuentas").html(respuesta[0]);
          if(tipo==1){
            var rsaldo=listarEstadosCuentas(fila,respuesta[1]);
            listarEstadosCuentasDebito(fila,rsaldo);
          }else{
            var rsaldo=listarEstadosCuentasCredito(fila,respuesta[1]);
            listarEstadosCuentas(fila,rsaldo);
          }           
        }
    });
    $("#estFila").val(fila);
    /*$("#est_codcuenta").val($("#cuenta"+fila).val());
    $("#est_codcuentaaux").val($("#cuenta_auxiliar"+fila).val());*/ 
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
  if(tipo==1){
    var cuenta=0;
    var codComproDet=0;
    var nfila={
    cod_plancuenta:cuenta,
    cod_comprobantedetalle:codComproDet,
    cod_proveedor:0,//$("#proveedores").val(),
    monto:$("#monto_estadocuenta").val()
    }
    itemEstadosCuentas[fila-1]=[];
    itemEstadosCuentas[fila-1].push(nfila);
    $("#nestado"+fila).addClass("estado");
    verEstadosCuentas(fila,cuenta);
  }else{
    var cuenta=$("#cuentas_origen").val();
    var codComproDet=$('input:radio[name=cuentas_origen_detalle]:checked').val();
    if(codComproDet!=null){
    var nfila={
    cod_plancuenta:cuenta,
    cod_comprobantedetalle:codComproDet,
    cod_proveedor:0,//$("#proveedores").val(),
    monto:$("#monto_estadocuenta").val()
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
  var table = $('#tabla_estadocuenta');
   for (var i = 0; i < itemEstadosCuentas[id-1].length; i++) {
     var row = $('<tr>').addClass('bg-white');
     row.append($('<td>').addClass('text-left').text(""));
     row.append($('<td>').addClass('text-left text-danger').text("Sin Guardar"));
     var tipo=$("#tipo_estadocuentas"+id).val();
      if(tipo==1){
        row.append($('<td>').addClass('text-left').text($("#glosa_detalle"+id).val()));
        var nsaldo=parseFloat(saldo)+parseFloat(itemEstadosCuentas[id-1][i].monto);
        row.append($('<td>').addClass('text-right').text(numberFormat(itemEstadosCuentas[id-1][i].monto,2)));
        row.append($('<td>').addClass('text-right').text(""));   
      }else{
        var titulo_glosa="";
        if(itemEstadosCuentas[id-1][i].cod_comprobantedetalle!=0){
          titulo_glosa=obtieneDatosFilaEstadosCuenta(itemEstadosCuentas[id-1][i].cod_comprobantedetalle);
        }
        row.append($('<td>').addClass('text-left').html($("#glosa_detalle"+id).val()+"<small class='text-success'>"+titulo_glosa+"</small>"));
        var nsaldo=parseFloat(saldo)-parseFloat(itemEstadosCuentas[id-1][i].monto);
        row.append($('<td>').addClass('text-right').text("")); 
        row.append($('<td>').addClass('text-right').text(numberFormat(itemEstadosCuentas[id-1][i].monto,2)));  
      }
      row.append($('<td>').addClass('text-right font-weight-bold').text(numberFormat(nsaldo,2)));
     table.append(row);
     return nsaldo;
   }
 }
 function verEstadosCuentasCred(){
  var fila = $("#estFila").val();
  var cuenta = $("#cuentas_origen").val();
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
function verEstadosCuentasModal(cuenta,cod_cuenta,cod_cuentaaux,tipo){
   var parametros={"cod_cuenta":cod_cuenta,"cod_cuentaaux":cod_cuentaaux,"tipo":tipo,"mes":12};
    $.ajax({
        type: "GET",
        dataType: 'html',
        url: "estados_cuenta/ajaxListEstadoCuenta.php",
        data: parametros,
        success:  function (resp) {
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
//>>>>>>> 9665608161fbd74baa97b51d1230f7cda83c0916
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
    $("#mensaje").html("<p class='text-danger'>El monto no puede ser mayor al 50% del haber b&aacute;sico</p>");
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
         Swal.fire("Registro Existoso!", "Se registradon los datos exitosamente!", "success")
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
  $("#modal_nombresim").val(nombre_s);
  $("#modal_tiposim").val(cod_i);
  $('.selectpicker').selectpicker("refresh");

  $("#modalEditSimulacion").modal("show");
}
function guardarDatosSimulacion(btn_id){
  var codigo_s=$("#cod_simulacion").val();
   var nombre_s=$("#modal_nombresim").val();
   var cod_i=$("#modal_tiposim").val();   
   var parametros={"codigo":codigo_s,"nombre":nombre_s,"ibnorca":cod_i};

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
     $("#titulo_curso").text(nombre_s);
     $("#cod_ibnorca").val(cod_i);
     if(cod_i==1){
       $("#ibnorca").val("IBNORCA"); 
     }else{
       $("#ibnorca").val("FUERA DE IBNORCA"); 
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
  $("#modal_utibnorca").val($("#utilidad_minlocal").val());
  $("#modal_utifuera").val($("#utilidad_minext").val());
  $("#modal_alibnorca").val($("#alumnos_plan").val());
  $("#modal_alfuera").val($("#alumnos_plan_fuera").val());
  $("#modal_importeplan").val($("#cod_precioplantilla").val());

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
   var precio_p=$("#modal_importeplan").val(); 

   var parametros={"cod_sim":cod_sim,"codigo":codigo_p,"ut_i":ut_i,"ut_f":ut_f,"al_i":al_i,"al_f":al_f,"precio_p":precio_p};

  if(!(ut_i==""||ut_f==""||al_i==""||al_f=="")){
  $("#"+btn_id).attr("disabled",true); 
  $.ajax({
    url: "ajaxSaveDatosPlantilla.php",
    type: "GET",
    data: parametros,
    dataType: "html",
    success: function (resp) { 
      alerts.showSwal('success-message','registerSimulacion.php?cod='+cod_sim);
     //Swal.fire("Correcto!", "El proceso fue satisfactorio!", "success");
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

function actualizarSimulacion(){
  var codigo=$("#cod_simulacion").val();
   Swal.fire({
        title: '¿Esta Seguro?',
        text: "Los datos de la simulación se actualizarán!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonClass: 'btn btn-info',
        cancelButtonClass: 'btn btn-danger',
        confirmButtonText: 'Si',
        cancelButtonText: 'No',
        buttonsStyling: false
      }).then((result) => {           
         if (result.value) {
            location.href='registerSimulacion.php?cod='+codigo;
            $("#narch").removeClass("estado");
            return(true);
          } else if (result.dismiss === Swal.DismissReason.cancel) {
            return(false);
          }
         });
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
