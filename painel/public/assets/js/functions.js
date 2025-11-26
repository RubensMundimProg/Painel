function successMessage(msg){
    alert(msg);
}

function errorMessage(msg){
    alert(msg);
}

function loading(show){
    var exibir = (show == true) ? 'show' : 'hide';
    $('#modalLoading').modal(exibir);
}

$(function(){

    $('body').on('click','.btn-confirm', function(){
        var url = $(this).attr('href');
        $('#modalConfirm .link-confirm').attr('href',url);
        $('#modalConfirm').modal('show');
        return false;
    })

    $('body').on('click','.btn-drop', function(){
        $(".dropdown-menu-sisfron").hide();
        var i = $('.btn-drop').index(this);
        var drop = $(".dropdown-menu-sisfron").eq(i);
        if(drop.hasClass('open')){
            drop.removeClass('open');
            return;
        }

        $(".dropdown-menu-sisfron").removeClass('open');
        drop.addClass('open');

        var offset = $(this).position();
        drop.css('top',offset.top+8);
        drop.css('left',offset.left+39);

        $(drop).css('display','block');
    })

    $('form').submit(function(){
        loading(true);
    })

    $('button[data-dismiss="modal"]').click(function(){
        $('.form-control').val('');
    })
})