/**
 * Created by bruno.rosa on 4/2/15.
 */
$(function(){

    if($('input[data-mask="timepicker"]').length){
        setTimeout(function(){
            $('input[data-mask="timepicker"]').val('');
        },50);
    }

    $('body').on('change','select[data-sistema]', function(){
        var sistema = $(this).val();
        if(!sistema) return false;
        $.ajax({
            url:'/index/alterar-sistema/'+sistema,
            type:'GET',
            dataType:'JSON',
            success:function(res){
                if(!res.error){
                    location.reload();
                }
            }
        })
    });

    $('body').on('click','.remover-anexo',function(){
        var p = $(this).parent('p');
        var file = $(this).attr("data-file");
        var id   = $(this).attr('data-id');

        $.ajax({
            url:'/triagem/remover-anexo',
            type:'POST',
            dataType:'JSON',
            data:{'file':file,'id':id},
            success:function(res){
                p.remove();
            }
        })

        return false;
    });

    $('.form-ajax').submit(function(){
        var tipo = $(this).attr('data-acao');
        var url = $(this).attr('action');
        var data = $(this).serialize();
        var type = $(this).attr('method');

        $('#modal-status').modal('hide');
        $('#modal-progresso').modal('hide');

        loading(true);

        $.ajax({
            url:url,
            type: type,
            data: data,
            dataType:'json',
            success:function(res){
                loading(false);

                if(!res.error){
                    if(tipo == 'Fechar'){
                        $('.active .status-registro').html(res.status);
                    }
                    showMsg(res.message, false, false);
                }else{
                    showMsg(res.message, false, true);
                }

                $('tr').removeClass('active');
            }
        });

        return false;
    });

    $('body').on('click', '.cancel-progress', function(){
        $('tr').removeClass('active');
    });

    $('body').on('click', '.view-anexo', function(){
        var anexo = $(this).attr('data-anexo').split('|');
        $(".anexos-body .row").html('');
        $.each(anexo, function(i,e){
            e = e.replace('./public','');
            //e = e.replace('public','');
            var arquivo = e.split('.');
            var extencao = arquivo[arquivo.length - 1];
            var imagens = ['jpg','png','jpeg','jpg','gif'];
            var nomeArquivo = e.split('/');
            var nome = nomeArquivo[nomeArquivo.length - 1];
            if(imagens.indexOf(extencao) == -1){
                $(".anexos-body .row").append('<div class="col-md-3"><a href="'+e+'" target="_blank">Visualizar Anexo ('+nome+')</a></div>');
            }else{
                $(".anexos-body .row").append('<div class="col-md-3"><a href="'+e+'" target="_blank"><img src="'+e+'" class="img-responsive"/></a></div>');
            }
        });
        setTimeout(function () {
            $('#modal-anexo').modal('show');
        },100);
    });

    $('.dados-validos input').change(function(){
        var UF = $(this).attr('id');
        var milha = $('input[name="'+UF+'"][data-tipo="ultima_milha"]').val();
        var abstencao = $('input[name="'+UF+'"][data-tipo="abstencao"]').val();
        var data = milha+'|'+abstencao;

        $.ajax({
            url:'/configuracao/save-data',
            dataType:'json',
            type:'POST',
            data:{'dados':data,'uf':UF},
            success:function(res){
                if(res.error){
                   alert(res.message);
                }
                $.ajax({
                    url:'/configuracao/data-milha',
                    success:function(res){
                        $.each(res, function(i,e){
                            $('input[name="'+e.uf+'"][data-tipo="ultima_milha"]').val(e.ultima_milha);
                            $('input[name="'+e.uf+'"][data-tipo="abstencao"]').val(e.abstecao);
                        })
                    }
                })
            }
        })

    })
})


    if($('.reload').length){
        setTimeout(function(){
            location.reload();
        },60000);
    }

    $('body').on('click','.exibir-confirmacao', function(){
        var url = $(this).attr('data-url');
        $('.confirmacao-url').attr('href',url);
        $('#modal-confirmacao').modal("show");
    })

    $('body').on('click','.btn-confirma', function(){
        $("#modal-abstract-form").modal('hide');

        $('.hidden-print a').addClass('hide');

        if($(this).hasClass('block')){
            return false;
        }

        $('.btn-confirma').addClass('block');

        var url = $(this).attr('data-url');
        loading(true);
        ///A

        $.ajax({
            url: url,
            type:'GET',
            dataType:'json',
            success:function(json){
                $('.hidden-print a').removeClass('hide');
                $('.btn-confirma').removeClass('block');


                if(json.error){
                    loading(false);

                    alert(json.message);
                    return false;
                }

                location.reload();
            }
        })
    });



    $('.btn-editar').click(function(){

        var url = $(this).attr('data-url');
        loading(true);

        $.ajax({
            url: url,
            type:'GET',
            success:function(json){

                if(json.error){
                    loading(false);
                    alert(json.message);
                    return false;
                }else{
                    $('#modal-abstract-form .modal-body').html(json);
                    $('#modal-abstract-form').modal('show');
                    categoria();
                    checkCadastroAlerta();
                    regrasCategoria($('#Categoria').val());
                    loading(false);
                    setTimeout(function(){
                        instantiateChosen();
                    },1000);
                }

            }
        })
    });

    //$('#ocorrencia').change(function(){
    //    var valor = $(this).val();
    //    $('#titulo').val(valor);
    //});

    $('.change-status').click(function(){
        $(this).parents("tr").addClass('active');
    });

    $('.update-progress').click(function(){
        $(this).parents("tr").addClass('active');

        var code = $(this).attr('data-id');
        $('#modal-progresso form')[0].reset();
        $('#codeProgress').val(code);
        $('#modal-progresso').modal('show');
    });

    $('body').on('click',".cancel-progress",function(){
        $('.update-progress').parents('tr').removeClass('active');
    });

    $('.update-status').click(function(){
        var code = $(this).attr('data-id');
        var status = $(this).attr('data-status');

        $('#codeStatus').val(code);
        $('#status-validados').val(status);
        $('#modal-status').modal('show');
    });

    if($('#PageRelatorios').length){
        updateSubcategoria();
        $("#categoria").change(function(){
            updateSubcategoria();
        })

        function updateSubcategoria(){
            if($("#categoria").val() == 'Todas'){
                $('#uf option[value=""]').addClass('hide');
                $('#uf').val('AC');
            }else{
                $('#uf option[value=""]').removeClass('hide');
            }
        }
    }

    $('.view-progress').click(function(){
        $(this).parents("tr").addClass('active');

        var code = $(this).attr('data-id');
        var loading = '<p class="txt-carregando blink-me text-center">CARREGANDO</p>';
        //$('#view-progresso').find('.modal-title').text('Alerta '+code);
        $('#view-progresso').find('.modal-title').text('Alerta');
        $('#view-progresso').find('.alerta-dados').html('');
        $('#view-progresso').find('.alerta-atualizacoes').html('');
        $('#view-progresso').modal('show');
        $('#view-progresso').find('.txt-carregando').remove();
        $('#view-progresso').find('.modal-body').prepend(loading);

        $.ajax({
            url:'/dashboard-data/detalhes-evento/'+code,
            success:function(json){
                var atualizacoes = '';
                $.each(json.updates,function(i,v){
                    atualizacoes += '<div class="list-group" style="width: 400px; float:left;">' +
                    '<p><b>Atualização:&nbsp;</b>'+ v.atualizacao+'</p>' +

                    '<p><b>Comentário:&nbsp;</b>'+ v.valor+'</p>' +
                    '</div>';
                });

                if(json.updates.length == 0){
                    atualizacoes = '<p class="text-center">Sem registro de progresso</p>';
                }

                $('#view-progresso').find('.txt-carregando').remove();
                $('#view-progresso').find('.alerta-atualizacoes').append(atualizacoes);
            }
        });


        $('#view-progresso').modal('show');
    });


var dataTableTranslate = {
    "sEmptyTable": "Nenhum registro encontrado",
    "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
    "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
    "sInfoFiltered": "(Filtrados de _MAX_ registros)",
    "sInfoPostFix": "",
    "sInfoThousands": ".",
    "sLengthMenu": "_MENU_ resultados por página",
    "sLoadingRecords": "Carregando",
    "sProcessing": "Processando",
    "sZeroRecords": "Nenhum registro encontrado ",
    "sSearch": "Pesquisar",
    "oPaginate": {
        "sNext": "Próximo",
        "sPrevious": "Anterior",
        "sFirst": "Primeiro",
        "sLast": "Último"
    },
    "oAria": {
        "sSortAscending": ": Ordenar colunas de forma ascendente",
        "sSortDescending": ": Ordenar colunas de forma descendente"
    }
};

//CENTER MODAL
$('.modal').on('show.bs.modal', centerModals);
$(window).on('resize', centerModals);

var table = '';

$(document).ready(function(){
//    var table = '';
    if($('.datatable').length){
        table = $('.datatable').DataTable({
            "lengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos / All"] ],
            pageLength: 50,
            "order": [ 0, 'asc' ],
            "columnDefs": [ { "targets": 0, "orderable": true },{ "targets": -1, "orderable": false } ],
            "language": dataTableTranslate
        });
    }

    if($('.datatable-full').length){
        table = $('.datatable-full').DataTable({
            "lengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos / All"] ],
            pageLength: 50,
            "order": [ 0, 'asc' ],
            "language": dataTableTranslate
        });
    }

    if($('.datatable-left').length){
        table = $('.datatable-left').DataTable({
            "lengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos / All"] ],
            pageLength: 50,
            "order": [ 1, 'asc' ],
            "columnDefs": [ { "targets": 0, "orderable": false } ],
            "language": dataTableTranslate
        });
    }

//    DATEPICKER
//    $('.datepicker').datepicker({
//        format: "dd/mm/yyyy",
//        autoclose: true,
//        language: "pt-BR"
//    });

    selectingMenu();

    checkCadastroAlerta();

    categoria();
    $('body').on('change','[name=categoria], [name=Categoria]',function(){
        categoria();

        var categoriasSubcategorias = JSON.parse(sessionStorage.categoriaSubCategoria);
        var inputName = 'subcategoria';

        if($('[name=SubCategoria]').length){
            inputName = 'SubCategoria';
        }

        $('[name='+inputName+'] option').remove();
        if($.isArray(categoriasSubcategorias['subcategorias'][$(this).val()])){
            $('.subcategorias').show();

            if($('div.menu[data-menu=Relatórios]').length == 0){
                $('.subcategorias').find('label').text('* Subcategoria');
                $('[name=subcategoria]').attr('required',true);
            }

            $('[name='+inputName+']').append($('<option>', {
                value: '',
                text : 'Selecione'
            }));
            $.each(categoriasSubcategorias['subcategorias'][$(this).val()],function(i, item){
                $('[name='+inputName+']').append($('<option>', {
                    value: item,
                    text : item
                }));
            });
        }else{
            $('.subcategorias').hide();
            $('.subcategorias').find('label').text('Subcategoria');
            $('[name=subcategoria]').attr('required',false);
        }


    });

    statusAlerta();
    $('#status-validados').change(function() {
        statusAlerta();
    });


//    MENU HAMBURGUER DE TABELA
    $(document).on('click',function(){
        if(!$('#menu-item').hasClass('hide')){
            $('#menu-item').addClass('hide');
        }
    }).on('click','.call-menu-item',function(e){

        e.stopPropagation();

        var top = $(this).offset().top + 8;
        var left = $(this).offset().left + 16;
        var code = $(this).attr('data-code');

        $('#menu-item ul li').each(function(i){
            var link = $(this).children('a').attr('data-url-link')+code;
            $(this).children('a').attr('href',link);
        })

        $('#menu-item').css('top',top);
        $('#menu-item').css('left',left);
        $('#menu-item').removeClass('hide');

        var width = $('#menu-item ul').width();
        var height = $('#menu-item ul').height();

        $('#menu-item').css('width',width);
        $('#menu-item').css('height',height);

    });

    //$("#autocomplete-municipio").autocomplete({
    //    source: "/triagem/filtrar-municipio",
    //    minLength: 3,
    //    search: function( event, ui ) {
    //        $(this).addClass('disabled');
    //        $(this).attr('readonly','readonly');
    //        $(this).closest('.row').isLoading(
    //            {
    //                text : "Carregando... "
    //            }
    //        );
    //    }
    //});

    $('body').on("keydown.autocomplete","#autocomplete-municipio",function(e){
        $(this).autocomplete({
            source: "/triagem/filtrar-municipio",
            minLength: 3,
            search: function( event, ui ) {
                $(this).addClass('disabled');
                $(this).attr('readonly','readonly');
                $(this).closest('.row').isLoading(
                    {
                        text : "Carregando... "
                    }
                );
            }
        });
    });

    $( document ).ajaxError(function(e) {
        if(e){
            console.log(e);
        /*    console.log('Erro na requisição AJAX! Recarregando a página...');
            setTimeout(function () {
                location.reload();
            },1000);*/
        }
    });

    $( document ).ajaxComplete(function() {
        $("#autocomplete-municipio").closest('.row').isLoading("hide");
        $("#autocomplete-municipio").removeClass('disabled');
        $("#autocomplete-municipio").removeAttr('readonly');
        $('span.isloading-show').hide();
    });

    $('body').on('focusout','input#autocomplete-municipio',function(){
        var campo = $(this).val();
        var uf;
        var municipio;
        var option;
        var check = false;
        $('ul.ui-autocomplete li').each(function(){
            option = $(this).text();

            if(campo == option){
                check = true;
                if($('input#autocomplete-uf').length){
                    uf = campo.split(' - ')[0];
                    municipio = campo.split(' - ')[1];
                    $('input#autocomplete-uf').val(uf);
                    //$('input#autocomplete-municipio').val(municipio);
                }
            }
        })

        if(!check){
            $('input#autocomplete-municipio').val('');
            if($('input#autocomplete-uf').length){
                $('input#autocomplete-uf').val('');
            }
        }
    });

    uf = '';
    municipio = '';

    $('body').on('focusout focusin','.cadastro-alerta .form-control',function(){

        defineTitle();

    }).on('focusin','#descricao, #Descricao',function(){

        defineTitle();

    }).on('focusout','.cadastro-alerta #autocomplete-municipio',function(){

        if(uf == $('#autocomplete-uf').val() && municipio == $('#autocomplete-municipio').val()) return false;

        uf = $('#autocomplete-uf').val();
        var aux = $('#autocomplete-municipio').val();
        municipio = aux.split(' - ')[1];

        if(uf && municipio){

            if($('#autocomplete-coordenacao').attr('data-chosen')){
                $('#autocomplete-coordenacao').siblings('.chosen-container').addClass('loading');
            }else{
                $('#autocomplete-coordenacao').addClass('loading');
            }

            $.ajax({
                url: '/triagem/get-coordenacao',
                type:'POST',
                data: {uf:uf,municipio:municipio},
                dataType:'json',
                success:function(json){
                    if(json.error){
                        loading(false);
                        alert(json.message);
                        return false;
                    }else{
                        $('#autocomplete-coordenacao option').remove();
                        $('#autocomplete-coordenacao').append($('<option>', {
                            value: '',
                            text : 'Selecione'
                        }));
                        $.each(json.dados, function (i, item) {
                            $('#autocomplete-coordenacao').append($('<option>', {
                                value: item,
                                text : item
                            }));
                        });

                        if($('#autocomplete-coordenacao').attr('data-chosen')){
                            $('#autocomplete-coordenacao').siblings('.chosen-container').removeClass('loading');
                        }else{
                            $('#autocomplete-coordenacao').removeClass('loading');
                        }

                        updateChosen();

                    }
                }
            });


        }

    }).on('change','#categoria, #Categoria', function(){

        var selected = $(this).val();


        regrasCategoria(selected);

    }).on('change','#subcategoria, #SubCategoria', function(){

        var selected = $(this).val();

        regrasCategoria(selected);

    }).on('submit','#editar-triagem',function(e){
        //e.preventDefault();
        //
        //var dados = $(this).serialize();
        //var action = $(this).attr('action');
        //
        //$.ajax({
        //    url: action,
        //    type:'POST',
        //    data: dados,
        //    dataType:'json',
        //    success:function(json){
        //
        //        if(json.error){
        //            loading(false);
        //            alert(json.message);
        //            return false;
        //        }else{
        //            showMsg(json,true,false);//aqui
        //            location.reload();
        //        }
        //
        //    }
        //});

    });


    if($('[data-menu="Configurações Gerais"]').length){
        loadTags('');
    }

    $('.botao-salvar-filtro-twitter').click(function(){
        var words_twitter = $('textarea[name=words-twitter]').val();

        $.ajax({
            url:'/twitter/save-tags',
            data:{'words':words_twitter},
            type:'post',
            dataType:'Json',
            success:function(json){
                loadTags(json['words']);
            }
        });
    });

    //VALIDACAO CAMPO DATA
    $('body').on('change','[data-validate]',function(){
        var parameters = $(this).attr('data-validate').split(',');
        var field = moment($(this).val(), "DD/MM/YYYY");
        var checkField = moment($('[name='+parameters[1]+']').val(), "DD/MM/YYYY");
        var operator = parameters[0];

        if($('[name='+parameters[1]+']').val() && $(this).val()){
            if(!eval(field+' '+operator+' '+checkField)){
                showMsg({'error':true,'message':'Por favor, verifique a inconsistência do campo data.'},true,true);
                $(this).val('');
            }
        }
    });

});

function checkCadastroAlerta()
{
    if(!sessionStorage.categoriaSubCategoria && $('.cadastro-alerta').length){
        $.ajax({
            url: '/triagem/getCategoriaSubCategoriaRelacionada',
            type:'GET',
            dataType:'json',
            success:function(json){

                if(json.error){
                    loading(false);
                    alert(json.message);
                    return false;
                }else{
                    sessionStorage.categoriaSubCategoria = JSON.stringify(json.dados);
                }

            }
        });
    }

}

function selectingMenu(){
    $('body').find('li.submenu').removeClass('active');
    var menu = $('div.menu').attr('data-menu');
    if(menu == 'home'){
        $('li.home').addClass('active');
    }else {
        $('li.submenu[data-menu=\''+menu+'\']').addClass('active');
    }
}

function regrasCategoria(selected)
{
    if(!selected) return false;

    if(selected == 'Demanda Judicial'){
        $('#impacto_aplicacao').attr('disabled',true).attr('required',false);
        $('#ImpactoAplicacao').attr('disabled',true).attr('required',false);
        $('#nro_processo').attr('disabled',false).attr('required',true);
        $('#NroProcesso').attr('disabled',false).attr('required',true);

        $('.impacto-aplicacao').css('display','none');
        $('.nro-processo').css('display','block');
        return false
    }else{
        $('#nro_processo').attr('disabled',true).attr('required',false);
        $('#NroProcesso').attr('disabled',true).attr('required',false);

        $('.impacto-aplicacao').css('display','block');
        $('.nro-processo').css('display','none');
    }

    if(selected.search('Emergências Médicas') < 0 && selected.search('Eliminação de Participantes') < 0){
        $('#impacto_aplicacao').attr('disabled',false).attr('required',true);
        $('#ImpactoAplicacao').attr('disabled',false).attr('required',true);

        $('.impacto-aplicacao').css('display','block');

        return false;
    }

    $('#impacto_aplicacao').attr('disabled',true).attr('required',false);
    $('#ImpactoAplicacao').attr('disabled',true).attr('required',false);
    $('#nro_processo').attr('disabled',true).attr('required',false);
    $('#NroProcesso').attr('disabled',true).attr('required',false);

    $('.impacto-aplicacao').css('display','none');
    $('.nro-processo').css('display','none');
}

function defineTitle()
{
    var uf,municipio,categoria,subcategoria,coordenacao;

    if($('[name=municipio]').length && $('[name=categoria]').length && $('[name=coordenacao]').length && $('[name=uf]').length){
        municipio = ($('[name=municipio]').val())?$('[name=municipio]').val():'';
        categoria = ($('[name=categoria]').val())?' - '+$('[name=categoria]').val():'';
        subcategoria = ($('[name=subcategoria]').val())?' - '+$('[name=subcategoria]').val():'';
        coordenacao = ($('[name=coordenacao] option:selected').text())?' - '+$('[name=coordenacao] option:selected').text():'';
        if(coordenacao == ' - Selecione') coordenacao = '';
        $('#titulo').val(municipio+coordenacao+categoria+subcategoria);
    }else{
        //EDITAR
        municipio = ($('[name=Municipio]').val())?$('[name=Municipio]').val():'';
        categoria = ($('[name=Categoria]').val())?' - '+$('[name=Categoria]').val():'';
        subcategoria = ($('[name=SubCategoria]').val())?' - '+$('[name=SubCategoria]').val():'';
        //coordenacao = ($('[name=Coordenacao] option:selected').text())?' - '+$('[name=Coordenacao] option:selected').text():'';
        coordenacao = ($('[name=Coordenacao]').val())?' - '+$('[name=Coordenacao]').val():'';
        if(coordenacao == ' - Selecione') coordenacao = '';
        $('#Titulo').val(municipio+coordenacao+categoria+subcategoria);
    }
}

function categoria(){

    if($('[name=categoria]').length){
        var categoria = $('[name=categoria]').val();

        if(categoria == "Segurança Pública"){
            //$('.seguranca-publica').show();
            //$('#caixa-titulo').css('display','none');
            $('#ocorrencia').attr('disabled', false);
            $('#ocorrencia').attr('required', 'required');
        } else {
            //$('.seguranca-publica').hide();
            //$('#caixa-titulo').css('display','block');
            //$('#titulo').val('');
            $('#ocorrencia').val('');
            $('#ocorrencia').attr('disabled', true);
            $('#ocorrencia').removeAttr('required');
        }
    }

    if($('[name=Categoria]').length){
        var categoria = $('[name=Categoria]').val();

        if(categoria == "Segurança Pública"){
            //$('.seguranca-publica').show();
            //$('#caixa-titulo').css('display','none');
            $('#Ocorrencia').attr('disabled', false);
            $('#Ocorrencia').attr('required', 'required');
        } else {
            //$('.seguranca-publica').hide();
            //$('#caixa-titulo').css('display','block');
            //$('#Titulo').val('');
            $('#Ocorrencia').val('');
            $('#Ocorrencia').attr('disabled', true);
            $('#Ocorrencia').removeAttr('required');
        }
    }

    getCategoriasSubcategorias();

}

function getCategoriasSubcategorias(){
    if(!sessionStorage.categoriaSubCategoria)
    $.ajax({
        url: '/triagem/getCategoriasSubCategorias',
        type:'GET',
        dataType:'json',
        success:function(json){

            if(json.error){
                loading(false);
                alert(json.message);
                return false;
            }else{
                sessionStorage.categoriaSubCategoria = JSON.stringify(json.dados);
            }

        }
    });
}

function statusAlerta(){
    var status = $('#status-validados').val();

    if(status != 1){
        $('#observacao-validados').attr('required', 'required');
    } else {
        $('#observacao-validados').removeAttr('required');
    }

}

//CENTER MODAL
function centerModals(){
    $('.modal:not(#modal-abstract-form)').each(function(i){
        var $clone = $(this).clone().css('display', 'block').appendTo('body');
        var top = Math.round(($clone.height() - $clone.find('.modal-content').height()) / 2);
        top = top > 0 ? top : 0;
        $clone.remove();
        $(this).find('.modal-content').css("margin-top", top);
    });
}

//SHOW MSG ERRO
var removerMsgsAntigas = '';
function showMsg(msg,toggle,error){

    clearTimeout(removerMsgsAntigas);

    if($('body').find('.container-alertas').length){
        $('body').find('.container-alertas').remove();
    }

//    function signature
//    showMsg(1° parameter->the message itself OR return object['error':'','message':''], 2° parameter->true uses array from the first parameter AND false uses string of message, 3° parameter-> true for error type error AND false for error type success, apply only when 1°parameter is a string of message);
    if(toggle){
        var type_alert = (msg['error'])?'alert-danger':'alert-success';
        $('body').append('<div class="container-alertas"><div class="alert '+type_alert+' alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><img src="/assets/img/ico/ico_close_msg.png" alt=""/></span></button><ul><li class="error">'+msg['message']+'</li></ul></div></div>');
    }else{
        var type_alert = (error)?'alert-danger':'alert-success';
        $('body').append('<div class="container-alertas"><div class="alert '+type_alert+' alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><img src="/assets/img/ico/ico_close_msg.png" alt=""/></span></button><ul><li class="error">'+msg+'</li></ul></div></div>');
    }

    removerMsgsAntigas = setTimeout(function(){
        $('body').find('.container-alertas').remove();
    },15000)
}

//Mensagem de confirmação
function msgConfirmacao(address,registro,acao,modulo){
    var msgs = {
        exclusao:{
            registro:{titulo:'Alerta',msg:'O Registro será excluído e não será possível recuperá-lo.'},
            alerta:{titulo:'Alerta',msg:'O Alerta será excluído e não será possível recuperá-lo.'},
            evento:{titulo:'Evento',msg:'O Evento será excluído e não será possível recuperá-lo.'},
            ocorrencia:{titulo:'Ação',msg:'A ocorrência será arquivada e não será possível recuperá-la.'}
        },
        encerramento:{
            missao:{titulo:'Missão',msg:'A Missão será encerrada e não será possível reabri-la.'}
        },
        cancelamento:{
            acao:{titulo:'Ação',msg:'A Ação será cancelada e não será possível revertê-la.'}
        }
    };

    $('#msg-'+acao).find('.modal-title').text(msgs[acao][modulo]['titulo']);
    $('#msg-'+acao).find('.modal-msg').text(msgs[acao][modulo]['msg']+" Deseja continuar?");
    $('#msg-'+acao).find('.registro').val(registro);
    $('#msg-'+acao).find('form').attr('action', address);
    $('#msg-'+acao).modal('show');
}


//PRINT
//PRINT PAGE
function printPage(){

    var title = $('body').find('div.content-title h1').clone().text();
    title = title.replace('Voltar','');
    var table = $('body .print-it').clone();

    $('div.visible-print h3').html(title);
    $('div.visible-print div').html(table);

    window.print();
}


function loading(show){
    var altura = $(window).height() + "px";
    if(show){
        $('body').append("<div class=bkg-loading style=height:"+altura+"><div class=loading-icon></div><div class=loading-text><p class=blink-me>CARREGANDO</p></div></div>");
        $('.bkg-loading').center();
        $('.loading-icon').center();
        $('.loading-text').center();
    }else{
        $('body').find('.bkg-loading').remove();
    }
}

function updateChosen(){
    $('select[data-chosen="true"]').trigger("chosen:updated");
}

function instantiateChosen(){
    $('select[data-chosen="true"]').chosen({
        no_results_text: "sem resultado para",
        search_contains: true
    });
}

function loadTags(json){
    if(json == ''){
        $.ajax({
            url:'/twitter/load-tags',
            dataType:'Json',
            success:function(json){
                $('textarea[name=words-twitter]').val(json[0]);
            }
        });
    }else{
        var msg = {'error':false,'message':'Alteração salva com sucesso!'};
        showMsg(msg,true,false);
        $('textarea[name=words-twitter]').val(json);
    }
}

jQuery.fn.center = function () {
    var thisHeight = this.height() / 2;
    var thisWidth = this.width() / 2;
    this.css("position","fixed");
    this.css("top", (($(window).height() / 2) - thisHeight));
    this.css("left", (($(window).width() / 2) - thisWidth));
    return this;
}