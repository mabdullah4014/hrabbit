<?php

namespace App\Admin\Extensions;
use Admin;

use Encore\Admin\Grid\Tools\BatchAction;

class ReleasePaid extends BatchAction
{
    protected $action;

    public function __construct($action = 1)
    {	
        $this->action = $action;
    }
	// {$this->resource}/release'            
    public function script()
    {
        return <<<EOT

$('{$this->getElementClass()}').on('click', function() {

  if(selectedRows().length > 0){
    swal({
      title: "Are you sure to mark as Received ?",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#DD6B55",
      confirmButtonText: "confirm",
      closeOnConfirm: false,
      cancelButtonText: "cancel"
    },
    function(){
        $.ajax({
         method: 'post',
         url: 'releasepaid',
         data: {
            _token:LA.token,
            ids: selectedRows(),
            action: {$this->action}
         },
         success: function (response) {
            swal.close()
            $.pjax.reload('#pjax-container');
            toastr.success('payment status updated successfully');
         }
      });

    });  
  } else {
      swal({
      title: "Please select atleast one record",
      confirmButtonColor: "#DD6B55"
    });
  } 
});

EOT;

    }
}

