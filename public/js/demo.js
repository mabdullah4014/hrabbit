/*jslint  browser: true, white: true, plusplus: true */
/*global $, countries */

$(function () {
    'use strict';

    $('#phone_number').autocomplete({
        serviceUrl: '../api/booking/getphone',
        onSelect: function(suggestion) {
            $("#phone_sug").val(suggestion.value);
            $.post("dispatch/load_customer",{ phone : suggestion.value, _token : LA.token },function(data){
              if(data!=''){
	            var obj = JSON.parse(data);
		        $("#customer_name").val(obj.name);
		        $("#customer_email").val(obj.email);
		        $("#customer_id").val(obj.id);
                
		      }
        	})
        },
    });

    $('#customer_email').autocomplete({
        serviceUrl: '../api/booking/getemail',
        onSelect: function(suggestion) {
        	$("#customer_email").val(suggestion.value);
        	$.post("dispatch/load_email",{ email : suggestion.value, _token : LA.token },function(data){
        		$("#customer_email").removeClass('my-error-class');
        		 $("label[for='customer_email']").remove();
        		if(data!='Failure'){
	          	  var obj = JSON.parse(data);
	          	  $("#phone_number").val(obj.phone_number);
	          	  $("#phone_sug").val(obj.phone);
			      $("#customer_name").val(obj.name);
			      $("#customer_email").val(obj.email);
		    	  $("#customer_id").val(obj.id);
		    	}
        	})
        },
    });    

});