/**
 * Created by bruno.rosa on 18/10/15.
 */

$.material.init();

var dataTableTranslate = {
    "sEmptyTable": "Nenhum registro encontrado",
    "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
    "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
    "sInfoFiltered": "(Filtrados de _MAX_ registros)",
    "sInfoPostFix": "",
    "sInfoThousands": ".",
    "sLengthMenu": "_MENU_ resultados por página",
    "sLoadingRecords": "Carregando...",
    "sProcessing": "Processando...",
    "sZeroRecords": "Nenhum registro encontrado",
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

$(document).ready(function () {

    $('a[href="#"]').click(function(e){
        e.preventDefault();
    });

    if(localStorage.getItem('update') == 'true'){
        $('.auto-update').attr('checked',true);
    }

    $('.auto-update').change(function(){
        if($('.auto-update:checked').length){
            localStorage.setItem('update', true);
        }else{
            localStorage.setItem('update', false);
        }
    });

    setTimeout(function(){
        var countTimes = 0;
        while($('.menu-principal').height() > 60){
            countTimes++;
            var currentFontSize = $('.menu-principal .navbar-nav li a').css('font-size');
            currentFontSize = Number(currentFontSize.replace('px',''));
            $('.menu-principal .navbar-nav li a').css('font-size',currentFontSize - countTimes+'px');
        }
    },100);

    loading(true);
    selectMenu();
    removeMessage();

    $('.modal').on('show.bs.modal', centerModals);
    $(window).on('resize', centerModals);

    if($('div[data-menu="Alertas,index"]').length){
        $('.container-fluid').addClass('container').removeClass('container-fluid');
    }

    //    INITIALIZE DATATABLE
    var table=[];

    if($(".datatable").length){

        if($('table').length){
            var order = $('.datatable').attr('data-table-order');
            var orderType = $('.datatable').attr('data-table-order-type');
            var sortDate = $('.datatable').attr('data-table-date');
            if(!order) order = 1;
            if(!orderType) orderType = 'desc';
            if(!sortDate) sortDate = '';

            table = $('.datatable').DataTable({
                "lengthMenu": [ [30, 100, 1000, 10000, -1], [30, 100, 1000, 10000, "Todos"] ],
                pageLength: 30,
                "order": [ order, orderType ],
                "columnDefs": [{type: 'date-uk', targets: sortDate}],
                "language": dataTableTranslate
            });
        }

        $('.datatable thead tr').after("<tr class='filtro'></tr>");
        $('.datatable').each( function (i) {
            $(this).find('thead th').each( function (i) {
                var tableParent = $(this).parents('table');
                if($(this).hasClass('actions')){
                    $(tableParent).find('.filtro').append( '<th></th>' );
                }else{
                    $(tableParent).find('.filtro').append( '<th><div class="form-group table-search"><i class="material-icons">search</i><input type="text" placeholder="" class="form-control tabela-campo-filtro" data-coluna="'+i+'"/></div></th>' );
                }
            });
        });

        $( '.filtro input').on( 'keyup change', function () {
            var array = '';
            var i = $(this).attr('data-coluna');
            array = $(this).parents('table').attr('data-table-array');
            if(array){
                table[array]
                    .column( i )
                    .search( $(this).val() )
                    .draw();
                $('.clear-search-fields').removeClass('disabled');
            }else{
                table
                    .column( i )
                    .search( $(this).val() )
                    .draw();
                $('.clear-search-fields').removeClass('disabled');
            }
        }).on( 'focus', function () {
            $(this).parents('.table-search').find('.material-icons').addClass('off');
        }).on( 'focusout', function () {
            if(!$(this).val().length){
                $(this).parents('.table-search').find('.material-icons').removeClass('off');
            }
        });

        $('.table-search .material-icons').on( 'click', function () {
            $(this).parents('.table-search').find('input').focus();
        });

        $( '.dataTables_filter input[type="search"]').on( 'keyup change', function () {
            $('.clear-search-fields').removeClass('disabled');
        });

        $('.clear-search-fields').on('click',function(){
            $('input[type="text"].tabela-campo-filtro, input[type="search"]').val('').keyup();
            $('.dataTables_filter input[type="search"]').val('').keyup();
            $('.table-search').find('.material-icons').removeClass('off');
            $(this).addClass('disabled');
        });
    }

    $('form').on('reset',function(){
        setTimeout(function(){
            updateChosen();
        },500);
    });


    $('body').on('click','tr[data-code]',function(){
        var code = $(this).attr('data-code');
        loading(true);
        return $.ajax({
            type: "POST",
            url: '/dashboard/getEventByCode',
            dataType: "json",
            data:{code:code},
            success: function(json) {

                loading(false);
                moment.locale("pt-br");

                var panel = $('.coluna-direita .panel');
                $(panel).find('.modal-title').text(json.dados['title'].substring(0, 29)+'...');

                $(panel).find('.panel-body > *').remove();
                $.each(json.dados,function(i,v){

                    if(v==null) return true;
                    if(i=='id') return true;
                    if(i != 'description' && i != 'title') return true;

                    if((i.indexOf('Date') != -1) || (i=='created')){
                        v = epocToDate(v);
                    }
                    if(i=='description'){
                        v = nl2br(v,false);
                    }
                    if(i=='status'){
                        v = statusMap(v);
                    }

                    $(panel).find('.panel-body').append('<h6>'+translateIt(i)+'</h6><h5 class="title">'+v+'</h5>');

                });

                getProgress(json.progresso);
                openLateralDireito();
            },
            error: function(error){
                console.log(error);
            }
        });
    }).on('click','.fechar-lateral-direita,.coluna-meio',function(){
        closeLateralDireito();
    });

    //FECHAR MODAL FILTRO RELATÓRIO QUANDO USUÁRIO CLICAR EM GERAR PDF
    $('body').on('submit','#modal-report form',function(){
        $('.modal').modal('hide');
    });
    if($('.datepicker').length){
        $('.datepicker').val(moment().format('DD/MM/YYYY'));
    }

    //ULTIMO ALERTA
    var loop = 0;
    var code = '';
    setInterval(function(){
        loop++;
        $.ajax({
            url:'/dashboard-data/ultimo-alerta',
            type:'get',
            dataType:'json',
            success:function(json){
                if(!json.error){
                    if(json.code != code){
                        $('.ultimo-alerta').html('<a href="javascript:void(0)" class="nome_ultimo_alerta"><i class="fa fa-exclamation-circle fa-normal blink-me"></i>&nbsp;'+json.titulo+'</a>');
                        code = json.code;
                        ultimoAlertaMarquee = $('.ultimo-alerta').marquee({
                            pauseOnHover: false,
                            duration: 10000,
                            direction: 'left',
                            startVisible : true
                        });
                    }else{
                        if(loop == 5){
                            ultimoAlertaMarquee.marquee('destroy');
                            $('.ultimo-alerta').html('');
                            loop = 0;
                        }
                    }
                }
            }
        })
    },60000);

});


$(window).load(function() {
    // When the page has loaded
    $("#container").css('visibility','visible').fadeIn(1);
    fixDataTableCss();
    fixLaterais();
    loading(false);
});

function openLateralDireito(){
    var posicaoDireita = $('.coluna-direita').position().left;
    var larguraTela = $(window).width();
    if(posicaoDireita < larguraTela){
        return false;
    }else{
        $('.coluna-direita').animate({"right": '0'});
    }
}

function closeLateralDireito(){
    $('.coluna-direita').animate({"right": '-=350'});
}

function openCloseLateralDireito(){
    var posicaoDireita = $('.coluna-direita').position().left;
    var larguraTela = $(window).width();
    if(posicaoDireita < larguraTela){
        $('.coluna-direita').animate({"right": '-=350'});
    }else{
        $('.coluna-direita').animate({"right": '0'});
    }
}

function fixLaterais(){
    var alturaTela = $(window).height();
    var alturaPanelHeading = $('.coluna-direita .panel-heading').outerHeight();
    $('.coluna-direita .panel-body').css('height',alturaTela - alturaPanelHeading);
}

function selectMenu(){
    if($('div.menu').length){
        var pag = $('div.menu').attr('data-menu').split(",");

        $('.menu-principal li[data-menu="'+pag[0]+'"]').addClass('active');
        $('.menu-principal li[data-menu="'+pag[0]+'"] ul li[data-menu="'+pag[1]+'"]').addClass('active');
    }
}

function centerModals(){
    $('.modal').each(function(i){
        var $clone = $(this).clone().css('display', 'block').appendTo('body');
        var top = Math.round(($clone.height() - $clone.find('.modal-content').height()) / 2);
        top = top > 0 ? top : 0;
        $clone.remove();
        $(this).find('.modal-content').css("margin-top", top);
    });
}

function loading(show){
    var altura = $(window).height() + "px";
    if(show){
        $('body').append("<div class=bkg-loading style=height:"+altura+"><div class=loading-icon></div></div>");
        $('.bkg-loading').center();
        $('.loading-icon').center();
    }else{
        $('body').find('.bkg-loading').remove();
    }
}

function addMessage(type,message){
    if($('.container-alertas').length){
        $('.container-alertas').remove();
    }
    var titulo = { success : 'Yeah!', info : 'Hey!', danger : 'Ops!' };
    var msg = '<div class="container-alertas" ng-controller="MensagemCtrl"><div class="alert alert-dismissible alert-'+type+'"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><strong>'+titulo[type]+'</strong><ul><li>'+message+'</li></ul></div></div>';
    $('body').append(msg);
    removeMessage();
}

function removeMessage(){
    if($('.container-alertas').length){
        setTimeout(function(){
            $('.container-alertas').remove();
        },8000)
    }
}

function fixDataTableCss(){
    if($('.dataTables_length').length){
        $('.dataTables_length').addClass('form-group');
        $('.dataTables_length select').addClass('form-control');
        $('.dataTables_filter').addClass('form-group');
        $('.dataTables_filter input').addClass('form-control');
    }
}

jQuery.extend( jQuery.fn.dataTableExt.oSort, {
    "date-uk-pre": function ( a ) {
        if (a == null || a == "") {
            return 0;
        }
        var ukDatea = a.split('/');
        return (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
    },

    "date-uk-asc": function ( a, b ) {
        return ((a < b) ? -1 : ((a > b) ? 1 : 0));
    },

    "date-uk-desc": function ( a, b ) {
        return ((a < b) ? 1 : ((a > b) ? -1 : 0));
    }
} );

function updateChosen(){
    $('select[data-chosen="true"]').trigger("chosen:updated");
}


function ucwords(str) {
    return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
        return $1.toUpperCase();
    });
}

function nl2br(str, is_xhtml) {
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1'+ breakTag +'$2');
}

function epocToDate(date){
    moment.locale("pt-br");
    return moment(date).format('DD/MM/YYYY H:mm:ss');
}

function translateIt(word){
    var words = {id:'Id',title:'Título',description:'Descrição',progress:'Progresso',urgency:'Urgência',
        relevance:'Relevância',severity:'Severidade',latitude:'Latitude',longitude:'Longitude',
        geolocationDescription:'Descrição Geográfica',created:'Data Criação',expectedStartDate:'Previsão de Início',ExpectedStartTime:'Previsão de Início',
        expectedEndDate:'Previsão de Término',ExpectedEndTime:'Previsão de Término',startDate:'Data de Início',endDate:'Data de Término',
        deadline:'Prazo',value:'Valor',notify:'Enviar notificações por e-mail',parentEvent:'Evento Pai',
        coordinator:'Coordenador',responsible:'Responsável',involved:'Envolvidos',firstReviewer:'Primeiro Revisor',
        secondReviewer:'Segundo Revisor',thirdReviewer:'Terceiro Revisor',data:'Arquivo',fileName:'Nome do Arquivo',
        comment:'Comentário',eventType:'Tipo do Evento',code:'Código',status:'Status',Name:'Nome',
        AlertMinutesInAdvance:'Antecedência do Alerta (Minutos)',comments:'Comentário',Comments:'Comentário',FinalizedWithSuccess:'Finalizado com Sucesso',
        Justification:'Justificativa',Location:'Local',Responsible:'Responsável',ProgressStatus:'Status do Progresso',Context:'Contexto',
        Description:'Descrição',StartTime:'Data e Hora de Início',DateCreated:'Data de Criação',DateUpdated:'Data de Atualização',Place:'Local',
        Distance_Km:'Distância Km',Duration:'Duração',Number:'Número',Type:'Tipo',SubType:'Sub Tipo',NumberOfSlots:'Quantidade de Carregadores',
        UpdatedBy:'Responsável',Date:'Data',Action:'Ação',Comment:'Comentário',Property:'Campo Alterado',OldValue:'Antigo Valor',NewValue:'Novo Valor',
        AdditionalParameter:'Parâmetro Adicional',UpdateType:'Tipo de Progresso'
    };
    return (typeof words[word] == 'undefined')?word:words[word];
}

function updateTypeCode(code)
{
    var codes = {
        0:'Propriedade do evento atualizado.',
        1:'Anexo Adicionado.',
        2:'Campo "Progresso" atualizado ou a situação do evento alterado.'
    };

    return codes[code];
}

function actionCode(code)
{
    var codes = {
        0:'Evento criado.',
        1:'Evento fechado.',
        2:'Evento reaberto.',
        3:'Evento cancelado.',
        4:'Evento desassociado.',
        5:'Progresso atualizado.',
        6:'Evento subordinado criado.',
        7:'Evento subordinado fechado.',
        8:'Evento subordinado reaberto.',
        9:'Evento subordinado cancelado.',
        10:'Evento subordinado excluído.',
        11:'Evento subordinado desassociado.',
        12:'Progresso de evento subordinado atualizado.',
        13:'Propriedade de evento atualizada.',
        14:'Envolvidos adicionados.',
        15:'Envolvidos removidos.',
        16:'Ativos associados.',
        17:'Ativos removidos.',
        18:'Anexo adicionado à aba Progresso do evento.',
        19:'Propriedade de um evento subordinado atualizada.',
        20:'Envolvidos adicionados a um evento subordinado.',
        21:'Envolvidos removidos de um evento subordinado.',
        22:'Ativos associados a um evento subordinado.',
        23:'Ativos removidos de um evento subordinado.',
        24:'Arquivos anexados à aba Progresso de um evento subordinado.',
        25:'Evento atual associado a um evento pai.',
        26:'Evento subordinado associado a um evento pai.',
        27:'Componentes de negócio associados.',
        28:'Componentes de negócio desassociados.',
        29:'Componentes de negócio associados a um evento subordinado.',
        30:'Componentes de negócio desassociados de um evento subordinado.',
        31:'Plano de continuidade associado a um evento.',
        32:'Plano de continuidade desassociado de um evento.',
        33:'Riscos corporativos associados a um evento. ',
        34:'Riscos corporativos desassociados de um evento.  ',
        35:'Arquivos anexados a um atributo de evento.',
        36:'Arquivos removidos de um atributo de evento.',
        37:'Eventos associados.',
        38:'Eventos desassociados.     ',
        39:'Arquivos anexados a um atributo de um evento subordinado.',
        40:'Arquivos removidos de um atributo de um evento subordinado.',
        41:'Atualização de arquivo de um atributo de um evento.',
        42:'Atualização de arquivo de um atributo de um evento subordinado.'
    };

    return codes[code];
}

function statusMap(number){
    var statuses = {0:'Cancelado',1:'Aberto',2:'Fechado'};
    return (typeof statuses[number] == 'undefined')?number:statuses[number];
}

function getProgress(dados){

    loading(false);
    moment.locale("pt-br");

    var panel = $('.coluna-direita .panel');
    $(panel).find('.panel-body').append('<div class="progress-wrap"><h4>Progresso</h4></div>');

    for (index = 0; index < dados.length; index++) {
        $(panel).find('.panel-body .progress-wrap').append('<div class="progress-message"></div>');
        $.each(dados[index],function(i,v){

            if(i != 'UpdatedBy' && i != 'Comment' && i != 'UpdatedBy' && i != 'Date'){
                return true;
            }

            if(i=='NewValue') return true;

            if((i.indexOf('Date') != -1) || (i=='created')){
                v = epocToDate(v);
            }
            if(i=='description'){
                v = nl2br(v,false);
            }
            if(i=='status'){
                v = statusMap(v);
            }
            if(i=='Action'){
                v = actionCode(v);
            }

            $(panel).find('.panel-body .progress-wrap .progress-message:last-child').append('<p><b>'+translateIt(i)+': </b>'+v+'</p>');

        });
    }

}

function ajustarTela(){

    $('.footer').hide();
    var altTela = $(window).height();
    var marginBottom = 0;
    var menu = 60;
    var areaRestante = altTela - menu;

//        DEFINE A ALTURA PARA CADA PÁGINA INTERNA DA APLICAÇÃO
    $('#carousel-paginas').css('height',areaRestante + 'px');

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

    //FIX CENTRALIZAÇÃO DOS INDICADORES
    fixCenterIndicador();

}
function fixCenterIndicador(){
    if($('table.indicador').length){
        $('table.indicador').each(function(){
            var heightTable = $(this).height();
            var heightContainer = $(this).closest('div').height();
            var margin = (heightContainer - heightTable) / 2;
            $(this).css('margin-top',margin+'px');
            $('.coluna-meio').css('overflow','hidden');
        });
    }
}

jQuery.fn.center = function () {
    var thisHeight = this.height() / 2;
    var thisWidth = this.width() / 2;
    this.css("position","fixed");
    this.css("top", (($(window).height() / 2) - thisHeight));
    this.css("left", (($(window).width() / 2) - thisWidth));
    return this;
};