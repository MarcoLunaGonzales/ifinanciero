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
        title: 'Esta Seguro?',
        text: "No podra revertir el borrado!",
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
    }else if(type == 'error-message3'){
      swal("Informativo!", "La planilla previa del mes ya fue generada. Se actualizará prerequisitos.", "warning")
          .then((value) => {
          location.href=url;
      });

    } 
  },

  


}
