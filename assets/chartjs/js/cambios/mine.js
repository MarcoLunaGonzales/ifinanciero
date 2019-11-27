jQuery(document).ready(function($) {
	

	$('.fila_tipo input').live('change',(function(){
		alert($(this).val())
	}))





	$('.name_farm').live('change', (function(event) {
		var thiss = $(this)
		var fila = $(this).parent().parent().attr('id');
		var codigo_med = $(this).val();

		$.ajax({
			type: "POST",
			url: "ajax/rutero_cvs/medicos.php",
			dataType : 'json',
			data: { 
				medicos : codigo_med
			}
		}).done(function(data) {
			thiss.parent().parent().find("#fila_nom_"+fila).html(data.direcciones)
			thiss.parent().parent().find("#fila_espe_"+fila).html(data.especialidad)
			thiss.parent().parent().find("#fila_cate_"+fila).html(data.categoria)
		});

	}));



});