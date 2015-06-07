(function($){

    'use strict';

    /* 入力要素にエラーメッセージがある場合、親要素からエラー状態とする */
    var setErrorColor = function(){
        $('div.error').each(function(){
            if ($(this).html() != '') {
                $(this).parent().addClass('has-error');
                $(this).addClass('has-error');
            }
        });
    }

    var domReady = function(){

        setErrorColor();

        $('[data-toggle="tooltip"]').tooltip();

    }

    $(document).on('ready', domReady);

}(jQuery));