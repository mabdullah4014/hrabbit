<?php

namespace App\Admin\Extensions;

use Encore\Admin\Admin;

class RejectVehicle
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    protected function script1()
    {
        return <<<SCRIPT1

$('.grid-check-row').on('click', function () {
    
swal({
  title: 'Are you sure want to delete this item?',
  type: 'warning',
  showCancelButton: true,
  confirmButtonColor: '#CB5E4F',
  cancelButtonColor: '#969797',
  confirmButtonText: 'Confirm'
}).then((result) => {
  if (result.value) {
      $.ajax({
         method: 'post',
         url: '/admin/deletevehicle',
         data: {
            _token:LA.token,
            ids: $(this).data('id'),
            action: "POST"
         },
         success: function (response){
          if(response!='success'){
            swal(
            'Following Drivers are in trip :',
             response,
            'error'
            )
          }else {
            swal.close()
            $.pjax.reload('#pjax-container');
            toastr.success('Vehicle category deleted successfully');
          }         
            
         }
      });
  }
})
    // Your code.
        
});


SCRIPT1;
    }

    protected function render1()
    {
        Admin::script($this->script1());

        return "<a class='fa fa-trash fa fa-delete grid-check-row' data-id='{$this->id}'></a>";
    }
    
    public function __toString()
    {
        return $this->render1();
    }
}