var site_url = window.location.protocol + '//' + window.location.host;

function deleteDataTableRecord(url, tableId){
    swal({
      title: "Are you sure want to delete this record?",
      // text: "Once deleted, you will not be able to recover this record!",
      icon: "warning",
      buttons: true,
      dangerMode: true,
      buttons: {
          cancel : 'No, cancel it!',
          confirm : 'Yes, I am sure!'
      },
  })
  .then((willDelete) => {
      if (willDelete) {
          $.ajax({
              type: "DELETE",
               data:{
               _token: $('meta[name="csrf-token"]').attr('content')
              },
              url: url,
              success: function (data) {
                  if(data.statusCode >= 200 && data.statusCode < 400){
                      toastr.success(data.message);
                      let oTable = $('#'+tableId).dataTable(); 
                      oTable.fnDraw(false);
                  }

                  if(data.statusCode >= 400 && data.statusCode < 500){
                      toastr.info(data.message);
                  }

                  if(data.statusCode >= 500){
                      toastr.error(data.message);
                  }
              },
              error: function (data) {
                  toastr.error('Error:', data);
              }
          });
      }
  });
}

function RefundTransferDataTableRecord(url, tableId,strTitle){
    swal({
      title: "Are you sure want to "+strTitle+" this user?",
      // text: "Once deleted, you will not be able to recover this record!",
      icon: "warning",
      buttons: true,
      dangerMode: true,
      buttons: {
          cancel : 'No, cancel it!',
          confirm : 'Yes, I am sure!'
      },
  })
  .then((willDelete) => {
      if (willDelete) {
          $.ajax({
              type: "POST",
               data:{
               _token: $('meta[name="csrf-token"]').attr('content')
              },
              url: url,
              success: function (data) {
                  if(data.statusCode >= 200 && data.statusCode < 400){
                      toastr.success(data.message);
                      let oTable = $('#'+tableId).dataTable(); 
                      oTable.fnDraw(false);
                  }

                  if(data.statusCode >= 400 && data.statusCode < 500){
                      toastr.info(data.message);
                  }

                  if(data.statusCode >= 500){
                      toastr.error(data.message);
                  }
              },
              error: function (data) {
                  toastr.error('Error:', data);
              }
          });
      }
  });
}