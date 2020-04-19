<?php

namespace App\Admin\Extensions;

use Encore\Admin\Admin;

class ReleaseDriver
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    protected function script()
    {
        return <<<SCRIPT

$('.grid-check-row').on('click', function () {
    
swal({
  title: 'Are you sure want to release this driver from the current trip?',
  type: 'warning',
  showCancelButton: true,
  confirmButtonColor: '#3085d6',
  cancelButtonColor: '#d33',
  confirmButtonText: 'Confirm'
}).then((result) => {
  if (result.value) {
      $.ajax({
         method: 'post',
         url: '/admin/ReleaseDriver',
         data: {
            _token:LA.token,
            id: $(this).data('id'),
            action: "POST"
         },
         success: function (response) {

          if(response==='Trip Ended! Driver is released now'){
            toastr.success('Trip Ended! Driver is released now');
          } else if(response==='Driver is not engaged to trip!'){
            toastr.error(response);
          } else {
              toastr.error(response);
          }
           $.pjax.reload('#pjax-container');
         }
      });
  }
})
        
});



SCRIPT;
    }

    protected function render()
    {
        Admin::script($this->script());

        return "<a class='fa fa-refresh grid-check-row' data-id='{$this->id}'></a>";
    }
    
    public function __toString()
    {
        return $this->render();
    }
}