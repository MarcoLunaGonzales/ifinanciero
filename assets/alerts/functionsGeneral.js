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
  var tipoComprobante=document.getElementById("tipo_comprobante").value;
  console.log("tipocomprobante: "+tipoComprobante);
  if(tipoComprobante>0){
      numFilas++;
      cantidadItems++;
      filaActiva=numFilas;
      //aumentar un itemfactura
      var nfac=[];
      itemFacturas.push(nfac);
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
      ajax.open("GET","ajaxCuentaContable.php?idFila="+numFilas,true);
      ajax.onreadystatechange=function(){
        if (ajax.readyState==4) {
          divDetalle.html(ajax.responseText);
          divDetalle.bootstrapMaterialDesign();
          $('.selectpicker').selectpicker("refresh");
          $('#myModal').modal('show');
          return false;
       }
      }   
      ajax.send(null);
  }else{
    console.log('entrando a notify!!!!');
    $('#msgError').html("<p>Debe seleccionar un tipo de comprobante</p>");
    $('#modalAlert').modal('show');
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
      console.log("debe "+formulariop.elements[i].value);    
      sumadebe += (formulariop.elements[i].value) * 1; 
    }
    if (formulariop.elements[i].id.indexOf("haber") !== -1 ){        
      console.log("haber "+formulariop.elements[i].value);    
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
      }
     } 
     itemFacturas.splice((idF-1), 1);
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
  var iva=configuraciones[0].valor;
  var importeIva=parseFloat($('#imp_fac').val())*(iva/100);
  var anterior= obtenerImportesFacturaIva(index);
  itemFacturas[index-1].push(factura);
  limpiarFormFac();
  listarFact(index);
  $("#debe"+index).val(anterior+importeIva);
  calcularTotalesComprobante();
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
      buscarComprobantes("nohay");
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
  if(nombre==""||abrev==""||!(unidad>0)||!(area>0)){
   $("#mensaje").html("<center><p class='text-danger'>Todos los campos son requeridos.</p></center>");
  }else{
     var parametros={"nombre":nombre,"abrev":abrev,"unidad":unidad,"area":area};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "ajaxRegistrarPlantilla.php",
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
var itemDetalle =[];
function listDetalle(id){
  var nombreGrupo=$("#nombre_grupo"+id).val();
  if(nombreGrupo==""){nombreGrupo="Sin Nombre";}
   $("#divTituloGrupo").html('<h4 class="card-title">'+nombreGrupo+'</h4>');
   $("#codGrupo").val(id);
   listarDet(id);
   $("#modalDet").modal("show");
 }

 function listarDet(id){
  var div=$('<div>').addClass('table-responsive');
  var table = $('<table>').addClass('table table-condensed');
  var titulos = $('<tr>').addClass('');
     titulos.append($('<th>').addClass('').text('#'));
     titulos.append($('<th>').addClass('').text('PARTIDA'));
     titulos.append($('<th>').addClass('').text('TIPO'));
     titulos.append($('<th>').addClass('').text('IBNORCA'));
     titulos.append($('<th>').addClass('').text('FUERA IBNORCA'));
     titulos.append($('<th>').addClass('').text('MONTO CALCULADO'));
     titulos.append($('<th>').addClass('').text('OPCION'));
     table.append(titulos);
   for (var i = 0; i < itemDetalle[id-1].length; i++) {
     var row = $('<tr>').addClass('');
     row.append($('<td>').addClass('').text(i+1));
     row.append($('<td>').addClass('').text(" Partida: "+itemDetalle[id-1][i].cuenta));
     row.append($('<td>').addClass('').text(itemDetalle[id-1][i].tipo));
     row.append($('<td>').addClass('').text(itemDetalle[id-1][i].monto_i));
     row.append($('<td>').addClass('').text(itemDetalle[id-1][i].monto_fi));
     row.append($('<td>').addClass('').text(itemDetalle[id-1][i].monto_cal));
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
  var detalle={
    codigo_cuenta:str_cuenta[0],
    cuenta:str_cuenta[1],
    tipo: $('#tipo_dato').val(),
    monto_i: $('#monto_ibnorca').val(),
    monto_fi: $('#monto_f_ibnorca').val(),
    monto_cal: $('#monto_calculado').val()
    }
  itemDetalle[index-1].push(detalle);
  $('#cuenta').val("");
  $('#tipo').val("");
  $('#monto_ibnorca').val("");
  $('#monto_f_ibnorca').val("");

  listarDet(index);
  mostrarDetalle(index);
  $("#ndet"+index).html(itemDetalle[index-1].length);
  /*$("#link110").addClass("active");$("#link111").removeClass("active");
  $("#nav_boton2").addClass("active");$("#nav_boton1").removeClass("active");*/
  $("#mensajeDetalle").html("<center><p class='text-success'>Registro satisfactorio</p></center>");
  $.notify({message: 'Partida '+str_cuenta[1]+' registrada' },{type: 'success'});
  
  }
  
 }

 function dividirCadena(cadenaADividir,separador) {
   var arrayDeCadenas = cadenaADividir.split(separador);
   return arrayDeCadenas;
}

function mostrarDetalle(id){
  var html="";
  for (var i = 0; i < itemDetalle[id-1].length; i++) {
    html+="<tr><td>"+itemDetalle[id-1][i].cuenta+"</td><td>"+itemDetalle[id-1][i].tipo+"</td><td class='text-right'>"+itemDetalle[id-1][i].monto_i+"</td><td class='text-right'>"+itemDetalle[id-1][i].monto_fi+"</td><td class='text-right'>"+itemDetalle[id-1][i].monto_cal+"</td></tr>";
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
         $("#monto_ibnorca").val("0");
         $("#monto_f_ibnorca").val("0");
    }else{
      calcularMontos();
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
          if($("#tipo_dato").val()==1){
            $("#monto_ibnorca").val(parseFloat(resp));
            $("#monto_f_ibnorca").val(parseFloat(resp));
          }else{
            $("#monto_ibnorca").val("0");
            $("#monto_f_ibnorca").val("0");
          }
           var momentoActual = new Date()
                var hora = momentoActual.getHours();
                var minuto = momentoActual.getMinutes();
                var segundo = momentoActual.getSeconds();
                var horaImprimible = hora + ":" + minuto + ":" + segundo;
          $("#monto_calculado").val(parseFloat(resp));
          $("#mensajeDetalle").html("<center><p class='text-info'>Cálculo realizado Hoy "+horaImprimible+"</p></center>");
        }
    });
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
  // var contenedor;
  // contenedor = document.getElementById('div_personal_UO');
  
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
  var plantilla_costo=$("#plantilla_costo").val();
  if(nombre==""||!(plantilla_costo>0)){
   $("#mensaje").html("<center><p class='text-danger'>Todos los campos son requeridos.</p></center>");
  }else{
     var parametros={"nombre":nombre,"plantilla_costo":plantilla_costo};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "ajaxRegistrarSimulacion.php",
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
function cargarPlantillaSimulacion(mes){
  var plantilla_costo=$("#plantilla_costo").val();
  if(!(plantilla_costo>0)){
   $("#mensaje").html("<center><p class='text-danger'>Seleccione una plantilla.</p></center>");
  }else{
    contenedor = document.getElementById('div_simulacion');
     var parametros={"plantilla_costo":plantilla_costo,"mes":mes};
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
function presioneBoton(){
  $("#mensaje").html("<center><p class='text-muted'><small>Presione en SIMULAR PLANTILLA</small></p></center>");
}

function guardarSimulacion(valor){
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
      if(valor=="enviar"){
       enviarSimulacionAjax();
      }else{
       guardarSimulacionAjax(0); 
      }      
    }
  }
}
function guardarSimulacionAjax(valor){
  var codigo=$("#cod_simulacion").val();
  var nombre=$("#nombre").val();
  if(valor==0){
   var plantilla_costo=$("#cod_plantilla").val();
  }else{
   var plantilla_costo=$("#plantilla_costo").val();  
  }
  var parametros={"cod_plantilla":plantilla_costo,"nombre":nombre,"codigo":codigo};
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
  var parametros={"codigo":codigo};
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
         $('#msgError4').html("<p class='text-dark font-weight-bold'>"+resp+"Se envio la Simulacion</p>");
         $('#modalSend').modal('show');
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