/**
 * Created by bruno.rosa on 03/10/15.
 */

$.material.init();

$(function(){

    if(location.hash == '#ultima-milha'){
        setTimeout(function(){
            $('a[href="#ultima-milha"]').click();
            loading(false);
        },1000);
    }

    //CENTER MODAL
    $('.modal').on('show.bs.modal', centerModals);
    $(window).on('resize', centerModals);

    //    INICIAR CAROUSEL
    $('.carousel').carousel({
        interval: 10000
    });

    //    INICIALIZA RELOGIO
    loadHeaderClock();
    setInterval(function(){
        loadHeaderClock();
    },1000)
//    $('.horario').clock({offset: '-3', type: 'digital'});


    $('.call-menu-paginas').click(function(){
        $('.menu-paginas').addClass('active');
    });
    $('.menu-paginas li input[name=aba-habilitada]').click(function(e){
        $('.botao-salvar-alternador-paginas').trigger( "click" );
        e.stopPropagation();
    });

    $('.menu-paginas li').click(function(){
        $('.menu-paginas li').removeClass('active');
        $(this).addClass('active');
        var aba = $(this).find('a').attr('href');
        $('.tab-content .tab-pane').removeClass('in active');
        $('.tab-content .tab-pane'+aba).addClass('in active');
        $('.menu-title li').removeClass('active');
        $('.menu-title li a[data-menu-pagina='+aba+']').parent('li').addClass('active');

        ajustarTela();
    });

    $('.menu-paginas').on('mouseleave',function(){
        $(this).removeClass('active');
    });

    $('.botao-salvar-alternador-paginas').click(function(){

        var alternar_abas = ($('input[name=habilitar-alternar-abas]').is(':checked'))?true:false;

        if(alternar_abas){
            var segundos = $('input[name=segundos-alternar-abas]').val();
            segundos = (segundos < 10)?segundos*1000:10000;
            alternarAbas(segundos);
        }else{
            clearInterval(intervaloAlternador);
        }

        $( "#modal-configuracao" ).modal( "hide" );
    });

    var load = '';
    loadTags(load);

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
        $( "#modal-configuracao" ).modal( "hide" );
    });

    $(document).on('click','.ver-detalhes-evento',function(){

        var code = $(this).attr('data-code');
        var loading = '<p class="txt-carregando blink-me text-center">CARREGANDO</p>';
        $('#feature.O').find('.modal-title').text('Alerta '+code);
        $('#modal-detalhes-evento').find('.alerta-dados').html('');
        $('#modal-detalhes-evento').find('.alerta-atualizacoes').html('');
        $('#modal-detalhes-evento').modal('show');
        $('#modal-detalhes-evento').find('.txt-carregando').remove();
        $('#modal-detalhes-evento').find('.modal-body').prepend(loading);

        $.ajax({
            url:'/dashboard-data/detalhes-evento/'+code,
            success:function(json){
                if(json.error){
                    $('#modal-detalhes-evento').modal('hide');
                    alert(json.message);
                    return false;
                }

                var dados = '';
                $.each(json.titulos,function(i,v){
                    dados += '<p><b>'+ v.nome+':&nbsp;</b>'+ v.valor+'</p>';
                });
                var atualizacoes = '';
                $.each(json.updates,function(i,v){
                    atualizacoes += '<div class="list-group">' +
                        '<p><b>Data:&nbsp;</b>'+ v.atualizacao+'</p>' +
                        '<p><b>Comentário:&nbsp;</b>'+ v.valor+'</p>' +
                        '</div>';
                });
                $('#modal-detalhes-evento').find('.txt-carregando').remove();
                $('#modal-detalhes-evento').find('.alerta-dados').append(dados);
                $('feature.O').find('.alerta-atualizacoes').append(atualizacoes);
            }
        });
    });

//    MARQUEE TOGGLE
    $('.rodar-tabela table').click(function(){
        $.each(rodaTabela,function(i){
            rodaTabela[i].marquee('toggle');
        })
    });

    $('table thead tr').click(function(){
        rodarTabela();
    });

});

var intervaloAlternador;
function alternarAbas(segundos){

    clearInterval(intervaloAlternador);

    var abas = [];
    $('.menu-paginas input[name=aba-habilitada]').each(function(i,v){
        if($(this).is(':checked')){
            abas.push(i);
        }
    });

    var index = 0;
    $('.menu-paginas li').eq(abas[index]).trigger( "click" );
    intervaloAlternador = setInterval(function(){
        index++;
        $('.menu-paginas li').eq(abas[index]).trigger( "click" );
        if(index == abas.length -1){
            index = -1;
        }
    },segundos);

}

//CENTER MODAL
function centerModals(){
    $('.modal').each(function(i){
        var $clone = $(this).clone().css('display', 'block').appendTo('body');
        var top = Math.round(($clone.height() - $clone.find('.modal-content').height()) / 2);
        top = top > 0 ? top : 0;
        $clone.remove();
        $(this).find('.modal-content').css("margin-top", top);
    });
}

var rodaTabela = [];
function rodarTabela(){
    $('.rodar-tabela').each(function(i){
        if($(this).find('.js-marquee-wrapper').length){
            rodaTabela[i].marquee('destroy');
        }
    })

    var altContainerTable = 0;
    var altTable = 0;

    $('.rodar-tabela').each(function(i){
        altContainerTable = $(this).height();
        altTable = $(this).find('table').height();

        if(altTable > altContainerTable){
            rodaTabela[i] = $(this).marquee({
                pauseOnHover: true,
                duration: 10000,
                direction: 'up',
                duplicated: true
            });
        }
    });
}

function startRss(){
    $('#frame-media-rss').attr( 'src', function ( i, val ) { return val; });
}


//FUNÇÃO RESPONSÁVEL PELO O AJUSTE DE TELA AUTOMATICO
function ajustarTela(){
    setTimeout(function(){
        var altTela = $(window).height();
        var marginBottom = 15;
        var menu = 45;
        var frameRelogios = 50;
        var areaRestante = altTela - menu;

//        DEFINE A ALTURA PARA CADA PÁGINA INTERNA DA APLICAÇÃO
        $('.tab-content .tab-pane.active').css('height',areaRestante + 'px');

//        BARRA DE ÚLTIMO ALERTA NA BARRA PRINCIPAL
        var menuTitle = $('.menu-title').width();
        var barraUltimoAlerta = menuTitle - 380;
        $('.ultimo-alerta').css('width',barraUltimoAlerta + 'px');

//        DEFINE AS VARIAVEIS PARA SEREM UTILIZADAS NOS ELEMENTOS DAS PÁGINAS INTERNAS
        var inteiro = areaRestante-marginBottom;
        var uma_metade = (areaRestante/2)-marginBottom;
        var um_terco = (areaRestante/3)-marginBottom;
        var dois_terco = ((areaRestante/3)*2)-marginBottom;
        var um_quarto = (areaRestante/4)-marginBottom;
        var tres_quarto = ((areaRestante/4)*3)-marginBottom;

        $('.inteiro').css('height',inteiro+'px').css('margin-bottom',marginBottom+"px");
        $('.uma-metade').css('height',uma_metade+'px').css('margin-bottom',marginBottom+"px");
        $('.um-terco').css('height',um_terco+'px').css('margin-bottom',marginBottom+"px");
        $('.dois-terco').css('height',dois_terco+'px').css('margin-bottom',marginBottom+"px");
        $('.um-quarto').css('height',um_quarto+'px').css('margin-bottom',marginBottom+"px");
        $('.tres-quarto').css('height',tres_quarto+'px').css('margin-bottom',marginBottom+"px");

//        PÁGINA ESTADOS
        $('#frame-relogios').css('height',frameRelogios+'px');
        $('.uma-metade.menos-frame-relogio').css('height',uma_metade-frameRelogios+'px').css('margin-bottom',marginBottom+"px");

    },1);
}

function reloadIframe(elem){
    $(elem).attr( 'src', function ( i, val ) { return val; });
}
function loadHeaderClock() {
    moment.locale('pt-br');
    $('.horario').empty().append(moment().tz("America/Sao_Paulo").format('H:mm:ss'));
}

function loadTags(json){
    if(json == ''){
        $.ajax({
            url:'/twitter/load-tags',
            dataType:'Json',
            success:function(json){
                console.log(json);
                $('textarea[name=words-twitter]').val(json[0]);
            }
        });
    }else{
        $('textarea[name=words-twitter]').val(json);
    }
}