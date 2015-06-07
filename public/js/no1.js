(function($){

    'use strict';

	var TIMEOUT_MSEC = 6000; //6秒

    var no1Function = function(){
        $('#no1-button').click(function(){

            $('#resultString').html('&nbsp;');
            var postalCode = $('#pcode').val();

            var requestData = {
        		postalCode : postalCode
    		}

            $.ajax({
                type     : 'POST',
				url      : 'http://local.sing/v1/address',
				data     : requestData,
				dataType : 'jsonp',
				timeout  : TIMEOUT_MSEC
			}).always(function(data){
		        if (data.result == 0) {
		            if (data.address) {
		                $('#resultString').html(data.address);
		            } else {
		                $('#resultString').html('対象がありません');
		            }
		        } else {
		            $('#resultString').html(data.message);
		        }
		    });
        });
    }

    var domReady = function(){
    	no1Function();

    }

    $(document).on('ready', domReady);

}(jQuery));