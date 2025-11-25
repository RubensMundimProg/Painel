/**
 * Created by bruno.rosa on 11/05/2016.
 */

$(document).ready(function(){
    var alturaTela = $(window).height();
    var larguraTela = $(window).width();
    if(larguraTela > 480){
        $('.block-full').css('height',alturaTela);
        //$('#fourth-block .separador').css('height',alturaTela);

        toggle = true;
        //$(window).scroll(function () {
        //    var scroll = $(window).scrollTop() + 20;
        //    var alturaTela = $(window).height();
        //    var alturaTelaToda = $(document).height();
        //
        //    if(scroll > alturaTela){
        //        if(toggle){
        //            $('.title-wrap').animate({zoom:'60%'}, 800);
        //            toggle = false;
        //        }
        //    }else{
        //        if(toggle == false){
        //            $('.title-wrap').animate({zoom:'100%'}, 800);
        //            toggle = true;
        //        }
        //    }
        //
        //    scroll = scroll - 20;
        //    if(scroll == (alturaTelaToda - alturaTela)){
        //        $('.title-wrap').hide();
        //    }else{
        //        $('.title-wrap').show();
        //    }
        //});
    }else{
        $('#first-block').css('height',larguraTela);
    }

    $('body').on('click','[data-target="#video"]',function(){
        var video = $(this).attr('data-video');
        var title = $(this).attr('data-title');
        $('#video .modal-title').text(title);
        $('#video source').attr('src','/ajuda-assets/videos/'+video);
        $("#video video")[0].load();
    });

});