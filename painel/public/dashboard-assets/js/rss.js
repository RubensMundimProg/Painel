/**
 * Created by bruno.rosa on 18/10/2016.
 */



$(document).ready(function(){
    if($('#rss').length){
        loadRss();
        ajustarTela();
        setInterval(loadRss,INTERVALO_RSS);
    }
});

function loadRss()
{
    console.log('Carregando RSS...');
    $.ajax({
        url:'/rss/get-rss',
        type:'get',
        dataType:'json',
        success:function(json){

            if(json.error){

            }else{
                feedRss(json.dados);
            }
        }
    });
}

function feedRss(data)
{
    var navegacao = '<nav class="navbar navbar-default acessibilidade">' +
        '<div class="container">' +
        '<ul class="nav navbar-nav navbar-right">' +
        '<li><a href="#" onclick="" class="jfontsize-button left" id="jfontsize-m2"><i class="fa fa-minus"></i></a></li>' +
        '<li><a href="#" onclick="" class="jfontsize-button" id="jfontsize-d2"><i class="fa">A</i></a></li>' +
        '<li><a href="#" onclick="" class="jfontsize-button" id="jfontsize-p2"><i class="fa fa-plus"></i></a></li>' +
        '<li><a href="#">Tamanho da Fonte</a></li>' +
        '</ul>' +
        '</div>' +
        '</nav>';

    var containerRss = '<div class="row news" data-channel="">' +
        '<div class="col-xs-12 col-sm-12 col-md-12">' +
        '<h5 class="gray rss-channel"></h5>' +
        '<h4 class="marquee light-gray rss-messages"></h4>' +
        '</div>' +
        '</div>';

    //$('#rss').append($(navegacao));

    $('.row.news').remove();

    var msg='',copy;
    $.each(data,function(i){
        $.each(data[i],function(i,v){
            msg += v.date+' '+v.title+'  |  ';
        });
        copy = $(containerRss);
        $(copy).find('.rss-channel').text(changeTitle(i));
        $(copy).find('.rss-messages').text(msg);
        msg = '';
        $('#rss').append($(copy));
    });

    $('h4.marquee').jfontsize({
        btnMinusClasseId: '#jfontsize-m2', // Defines the class or id of the decrease button
        btnDefaultClasseId: '#jfontsize-d2', // Defines the class or id of default size button
        btnPlusClasseId: '#jfontsize-p2', // Defines the class or id of the increase button
        btnMinusMaxHits: 1, // How many times the size can be decreased
        btnPlusMaxHits: 10, // How many times the size can be increased
        sizeChange: 5 // Defines the range of change in pixels
    });

    $('.marquee').marquee({
        pauseOnHover: false,
        duration: 10000,
        direction: 'left',
        startVisible : true
    });

    ajustarTela();
    $('body').css('overflow','hidden');
}

function changeTitle(title)
{
    var titles = {
        'inmet':'INMET',
        'globo-news':'Globo News',
        'globo-educacao':'Globo Educação',
        'google-news':'Google News',
        'cptec':'Cptec - 1',
        'cptec-2':'Cptec - 2',
        'cptec-brasil':'Cptec - Brasil',
        'climatempo-destaque':'ClimaTempo - Destaque',
        'climatempo-brasil':'ClimaTempo - Brasil',
        'climatempo-regioes':'ClimaTempo - Regiões',
        'climatempo-capitais':'ClimaTempo - Capitais'
    };
    return titles[title];
}