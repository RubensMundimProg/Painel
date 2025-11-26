/**
 * Created by bruno.rosa on 20/10/15.
 */

var carousel =  '<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">' +
                    '<ol class="carousel-indicators slideIndicator"></ol>'+
                    '<div class="carousel-inner slides" role="listbox"></div>'+
                '</div>';

var slideIndicator = '<li data-target="#carousel-example-generic" data-slide-to="0"></li>';
var slide = '<div class="item cols"></div>';
var col = '<div class="col-md-2"><div class="dois-terco tweetBox" data-word="" data-last-word=""><div class="sobre-coluna-twitter"><h5 class="tag"></h5></div></div></div>';

var tweetBox = '' +
    '<ul class="media-list">' +
    '<li class="media">' +
    '<div class="media-left">' +
    '<a href="#" target="_blank">' +
    '<img class="media-object" src="">' +
    '</a>' +
    '</div>' +
    '<div class="media-body">' +
    '<p class="tweetUser"></p>' +
    '<p class="tweetTime"></p>' +
    '<p class="tweetLocation"></p>' +
    '<p class="tweetMessage"></p>' +
    '<p class="tweetImage"><img src="" class="img-responsive img-rounded" alt=""/></p>' +
    '</div>' +
    '</li>' +
    '</ul>';

$(document).ready(function(){

    if($('#twitter').length){

        $('#form-frame.AvaliacaoPedagogica').hide();

        //    INICIALIZA TWITTER

        var load = '';
        loadTags(load);

        ajustarTela();
        $('body').css('overflow','hidden');

        CONTINUOUS = true;
        loadTwitter(true);
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
        $( "#modal-configuracao" ).modal( "hide" );
    });

});


function loadTwitter(toggle){
    $.ajax({
        url:'/twitter/start',
        dataType:'Json',
        success:function(json){
            if(toggle){
                buildColunas(json);
            }else{
                appendBlock(json);
            }
        }
    });
}

function buildColunas(json){
    var carouselNew = $(carousel);
    var count = 0;
    var colunas = [];
    $.each(json,function(i,v){
        var coluna = $(col);
        $(coluna).find('.tag').text(i);
        $(coluna).find('.tweetBox').attr('data-word',i);

        colunas[count] = $(coluna);
        count++;
    });

    var slideContainer = $(slide);
    var slideContainers = [];
    var indexCol = 0;
    var countColuna = 0;
    var countContainers = 0;
    $.each(colunas,function(i,v){

        $(slideContainer).append($(v[0]));
        indexCol++;
        countColuna ++;
        if(indexCol == 6){
            slideContainers[countContainers] = $(slideContainer);
            slideContainer = $(slide);
            countContainers++;
            indexCol = 0;
        }else if(countColuna == colunas.length){
            slideContainers[countContainers] = $(slideContainer);
        }

    });

    var slideIndicators = '';
    var active = true;

    $.each(slideContainers,function(i,v){

        slideIndicators = $(slideIndicator);
        $(slideIndicators).attr('data-slide-to',i);

        if(active){
            $(slideIndicators).addClass('active');
            $(v[0]).addClass('active');
            active = false;
        }

        $(carouselNew).find('.slideIndicator').append($(slideIndicators));
        $(carouselNew).find('.slides').append($(v[0]));
        $('#twitter').append($(carouselNew));
    });

    $('.carousel').carousel({interval: 10000});
    appendBlock(json);
}

function appendBlock(json){

    checkConsistencyTweetBox(json);

    var coluna = '';
    var colunas = [];
    var box = '';
    var item = [];
    var count = 0;
    $.each(json,function(i,v){

        if(v != null){
            $.each(json[i],function(index,value){

                if($('.tweetBox[data-word="'+i+'"]').attr('data-last-word') == value.user_id+' '+value.created_at) return true;

                box = $(tweetBox);

                //$(box).find('img.media-object').safeUrl({wanted:value.image,rm:"/dashboard-assets/img/twitter/default.png"});
                $(box).find('img.media-object').attr('src',value.image.replace('http','https')).error(function() {
                    $(this).attr('src',"/dashboard-assets/img/twitter/default.png");
                    console.log('Image does not exist !!');
                });
                $(box).find('.media a').attr('href','https://twitter.com/intent/user?user_id='+ value.user_id);
                $(box).find('.tweetUser').text(value.name+" @"+value.user);
                $(box).find('.tweetTime').html(value.created_at);
                if(value.location){
                    $(box).find('.tweetLocation').html('<i class="fa fa-map-marker"></i>&nbsp;'+value.location);
                }
                $(box).find('.tweetMessage').html(linkify(value.message));
                if(value.tweet_image){
                    $(box).find('.tweetImage img').attr('src',value.tweet_image.replace('http','https')).error(function() {
                        $(this).attr('src',"/dashboard-assets/img/twitter/default.png");
                        console.log('Image does not exist !!');
                    });
                }
                $($(box)).insertAfter('.tweetBox[data-word="'+i+'"] .sobre-coluna-twitter');

                $('.tweetBox[data-word="'+i+'"]').attr('data-last-word',value.user_id+' '+value.created_at);
            });
        }
        checkNumOfBlocks(i);
    });

    ajustarTela();
    $('body').css('overflow','hidden');

    continuousCheckTweets();

}

function checkNumOfBlocks(col){
    var qtd = $('.tweetBox[data-word="'+col+'"] .media-list').length;

    if(qtd > 30){
        var aux = -Math.abs(qtd - 30);
        $('.tweetBox[data-word="'+col+'"] .media-list').slice(aux).remove();
    }
}

function continuousCheckTweets(){
    if(CONTINUOUS){
        setInterval(function(){
            loadTwitter(false);
            ajustarTela();
        },10000);
    }
    CONTINUOUS = false;
}

function linkify(inputText) {
    var replacedText, replacePattern1, replacePattern2, replacePattern3;

    //URLs starting with http://, https://, or ftp://
    replacePattern1 = /(\b(https?|ftp):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/gim;
    replacedText = inputText.replace(replacePattern1, '<a href="$1" target="_blank">$1</a>');

    //URLs starting with "www." (without // before it, or it'd re-link the ones done above).
    replacePattern2 = /(^|[^\/])(www\.[\S]+(\b|$))/gim;
    replacedText = replacedText.replace(replacePattern2, '$1<a href="http://$2" target="_blank">$2</a>');

    //Change email addresses to mailto:: links.
    replacePattern3 = /(([a-zA-Z0-9\-\_\.])+@[a-zA-Z\_]+?(\.[a-zA-Z]{2,6})+)/gim;
    replacedText = replacedText.replace(replacePattern3, '<a href="mailto:$1">$1</a>');

    return replacedText;
}

function checkConsistencyTweetBox(json) {
    var wordsInPage = [];
    $('.tweetBox').each(function(){
        wordsInPage.push($(this).attr('data-word'));
    });

    var wordsRequest = [];
    $.each(json,function(i,v){
        wordsRequest.push(i);
    });

    var is_same = (wordsInPage.length == wordsRequest.length) && wordsInPage.every(function(element, index) {
            return element === wordsRequest[index];
        });

    if(!is_same){
        console.log('Tags foram alteradas! Refazendo os blocos do monitoramento Twitter!');
        removeAll();
        buildColunas(json);
    }

}

function removeAll(){
    $('#twitter > *').remove();
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
        $('textarea[name=words-twitter]').val(json);
    }

    ajustarTela();
    $('body').css('overflow','hidden');
}

$.fn.safeUrl=function(args){
    var that=this;
    if($(that).attr('data-safeurl') && $(that).attr('data-safeurl') === 'found'){
        return that;
    }else{
        $.ajax({
            url:args.wanted,
            type:'HEAD',
            error:
                function(){
                    $(that).attr('src',args.rm)
                },
            success:
                function(){
                    $(that).attr('src',args.wanted)
                    $(that).attr('data-safeurl','found');
                }
        });
    }


    return that;
};