$(function(){
    ///-> pessoa/listar
    $('body').on('click','.criar-login',function(){
        var id = $(this).attr('data-id');
        $('.id_pessoa').val(id);

        $('#criar-login-modal').modal('show');
    })

    $('body').on('click','.enviarFormNewLogin',function(){
        loading(true);

        var data = $('#formNewLogin').serialize();
        $.ajax({
            url:'/pessoas/criar-login',
            type:'POST',
            dataType:'json',
            data: data,
            success:function(json){
                loading(false);

                if(json.error){
                    errorMessage(json.message);
                    return false;
                }else{
                    location.reload();
                }
            }
        })
    })

    ///-> aplicacoes/funcionalidades
    $('body').on('click','.editar-funcionalidade', function(){
        loading(true);

        var code = $(this).attr('data-code');
        $.ajax({
            url:'/aplicacoes/dados-funcionalidade/'+code,
            dataType:'json',
            type:'GET',
            success:function(res){
                loading(false);

                if(res.error){
                    errorMessage(res.message);
                }else{
                    $.each(res.data, function(i,e){
                        if(i != 'id_aplicacao'){
                            $('#modalFuncionalidade #'+i).val(e)
                        }

                        $('#modalFuncionalidade').modal('show');
                    })
                }
            }
        })

    })
})