alerts = {
  showSwal: function(type, url) {
    if (type == 'success-message') {
      swal("Correcto!", "El proceso se completo correctamente!", "success")
          .then((value) => {
          location.href=url;
      });

    }else if (type == 'error-message') {
      swal("Error!", "El proceso tuvo un problema!. Contacte con el administrador!", "error")
          .then((value) => {
          location.href=url;
      });
    }
    else if (type == 'warning-message-and-confirmation') {
      swal({
        title: '¿Est&aacute;s Seguro?',
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
            location.href=url; 
            return(true);
          } else if (result.dismiss === Swal.DismissReason.cancel) {
            return(false);
          }
        })
    }
    else if(type == 'error-message3'){
      swal("Informativo!", "La planilla del mes ya fue registrada. Por favor Prosesar/Reprosesar planilla.", "warning")
          .then((value) => {
          location.href=url;
      });
    }
    else if(type == 'error-message4'){
      swal("Informativo!", "La planilla del año en curso ya fue registrada. Por favor Prosesar/Reprosesar planilla.", "warning")
          .then((value) => {
          location.href=url;
      });
    }
    else if(type == 'error-message5'){
      swal("ERROR!", "No puedes registrar un nuevo contrato. Por favor cierre el contrato anterior. Gracias!.", "error")
          .then((value) => {
          location.href=url;
      });
    }
    else if(type == 'error-message6'){
      swal("ERROR!", "No puedes retirar Al personal. Por favor cierre el &uacute;ltimo contrato. Gracias!.", "error")
          .then((value) => {
          location.href=url;
      });
    }
  },

  


}
