<?php

namespace App\Admin\Extensions;
use Admin;

use Encore\Admin\Grid\Tools\BatchAction;

class MassPay extends BatchAction
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
        $.ajax({
         method: 'post',
         url: '/mass_payment',
         data: {
            _token:LA.token,
            ids: selectedRows(),
            action: {$this->action}
         },
         success: function (response) {
            if(response == 1){
              swal.close()
              $.pjax.reload('#pjax-container');
              toastr.success('Successfully Paid');
              //location.href='/admin/pay_to_driver';
            }else{
              swal.close()
              $.pjax.reload('#pjax-container');
              toastr.warning('Something went wrong');
              //location.href='/admin/pay_to_driver';
            }
            
         }
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

