$(document).ready(function () {
    
    $('#adjyape').click(function (e) { 
        e.preventDefault();
        $('#btnYape').css('display','block');
        $('#yapeimg').val('');
        $('.imgyapedata').remove();
        $('#messageyape').text('');
        $('#messageyape').css('background-color','transparent')


    });
    $('#btnYape').click(function (e) { 
        e.preventDefault();
        
        url = $('#yapeimg').val().trim();
        
        $.post(base_url + 'pedidos/downloadyape/', {'url' : url}, function (data) {
            imgyape =`<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 imgyapedata" style="float:left"> 
            <img src="${base_url}/public/images/yape/${data.img}"  style="width:100% !important; height:100% !important" alt="imagen transferencia" class="img-thumbnail">
            </div>
            ` ;
            
            if(data.error == ''){
                $('#cr').text('')
                $(imgyape).appendTo('#imgyapeok');
                $('#messageyape').text(data.msg);
                $('#messageyape').css('background-color','#1fad40')
                                  .css('font-weight','bold')
                                  .css('color','#FFF')
                                  .css('padding','5px')
                                  .css('padding-left','10px');
            }else{
                $('#messageyape').text(data.error);
                $('#messageyape').css('background-color','#ff1212')
                              .css('font-weight','bold')
                              .css('color','#FFF')
                              .css('padding','5px')
                              .css('padding-left','10px');
            }
           

        }, 'json')
        .fail(function() {
            $('#messageyape').text('Intentelo nuevamente');
            $('#messageyape').css('background-color','#ff1212')
                              .css('font-weight','bold')
                              .css('color','#FFF')
                              .css('padding','5px')
                              .css('padding-left','10px');
          });
    });
});