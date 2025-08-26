/**
 * Created by bruno.rosa on 02/11/2016.
 */

$(function(){
    if($('div[data-menu="mapa-atividades"]').length){

        var popup = new ol.Overlay.Popup();

        $('#form-frame.AvaliacaoPedagogica').hide();

        function loadAtividades(){
            $.ajax({
                url:'/dashboard/dados-atividades',
                dataType:'json',
                success:function(res){
                    var full = '';
                    var table =   $('<table class="table timeline"><tbody></tbody></table>');
                    $.each(res.dados, function(i,e){
                        var inicio = moment(e.inicio,'HH:mm:ss');
                        var fim = moment(e.fim,'HH:mm:ss');
                        var now = moment();

                        var classe = '';
                        if(fim < now){
                            classe = 'passado';
                        }else if(inicio < now && now < fim){
                            classe = 'acontecendo';
                        }

                        //var html = '<div class="'+classe+'">'
                        //    +'<p>'+inicio.format('HH:mm')+' - '+fim.format('HH:mm')+'</p>'
                        //    + e.descricao +'</div>';



                        var html = '<tr data-json="" class="aguarde '+classe+'">'+
                            '<td class="">'+inicio.format('HH:mm')+' - '+fim.format('HH:mm')+'</td>' +
                            '<td class="'+classe+'">' +
                                '<div class="tip"></div>' +
                                '<div class="info">' +
                                '<p>'+e.descricao+'</p>' +
                                '</div>' +
                            '</td>' +
                            '</tr>';
                        full += html;
                    });
                    $(table).find('tbody').append(full);
                    $('#timeline').html(table);

                    loadAvisos();
                }
            })
        }

        function loadAvisos(){
            $.ajax({
                url:'/dashboard/dados-avisos',
                dataType:'json',
                success:function(res){
                    var full = '';
                    $.each(res.dados, function(i,e){
                        var inicio = moment(e.DataInicio,'YYYY-MM-DD').format('DD/MM/YYYY');
                        var fim = moment(e.DataFim,'YYYY-MM-DD').format('DD/MM/YYYY');

                        var html = '<li>' +
                                    '<p>['+e.AvaliacaoPedagogica+'] '+ e.Titulo+'</p>' +
                                    //'<p style="font-size: 11px">'+inicio+' à '+fim+'</p>' +
                                    '<p style="font-size: 10px; color:#ebebeb;">'+ e.Texto+'</p>' +
                                '</li>';
                        full += html;
                    });
                    $('.eventos.bloco_avisos ul.details').html(full);

                    markScrollTimeline();

                    rodarTabelaB();

                }
            })
        }

        loadAtividades();
        setInterval(loadAtividades,INTERVALO_ATIVIDADES);

        $(document).ready(function(){

            $('.bloco_atividades').css('height',$(window).height() - 120);
            $('#timeline').css('height',$('.bloco_atividades').height() - $('.bloco_atividades h6').outerHeight());
            $('.eventos.bloco_avisos .lista').css('height',$('.bloco_atividades').height() - $('.bloco_atividades h6').outerHeight());

            $('.footer').remove();

            //layers = [
            //    new ol.layer.Tile({
            //        source: new ol.source.OSM()
            //    }),
            //    new ol.layer.Vector({
            //        source: new ol.source.Vector({
            //            url: '/dashboard-assets/timezones.kml',
            //            format: new ol.format.KML()
            //        }),
            //        opacity:0.5
            //    })
            //];

            layers = [
                new ol.layer.Tile({
                    source: new ol.source.OSM()
                })
            ];

            $.ajax({
                url:'/dashboard/getKmlsFusoHorario',
                dataType:'json',
                type:'GET',
                async:false,
                success:function(res){

                    feedLayersFusoHorario(res.dados);

                    /**
                     * KML COLORS
                     * verde -> 5a14B400
                     * amarelo -> 5a14F0FA
                     * laranja -> 5014B4F0
                     * vermelho -> 5a1400FA
                     * */

                },
                error: function(err){
                    //loading(false);
                }
            });

            var mousex = 0;
            var mousey = 0;
            $("html").mousemove(function(mouse){
                mouseX = mouse.pageX;
                mouseY = mouse.pageY;
            });

            mapaAlerta.on('pointermove', function(evt) {/*
                var feature = mapaAlerta.forEachFeatureAtPixel(evt.pixel,
                    function(feature, layer) {
                        return feature;
                    });
                if (feature) {
                    var timeZone = feature.get('name');
                    var time =  timeZone.replace('GMT ', timeZone);
                    var hora = moment().zone(time);

                    if(moment().isDST() && timeZone == 'GMT -03:00'){
                        hora.add(1,'hours');
                    }

                    $(".texto-float").css("left",mouseX);
                    $(".texto-float").css("top",mouseY);

                    $('.texto-float .gmt').html(timeZone);
                    $('.texto-float .hora').html(hora.format('HH:mm'));
                    $(".texto-float").show();
                }else{
                    $(".texto-float").addClass('hide');
                }*/
            })

            mapaAlerta.addOverlay(popup);

            var coords = [
                {
                    gmt:'-05:00',
                    id:'rio-branco',
                    cord:[-7648880.179438026,-1666294.5807983994]
                },
                {
                    gmt:'-04:00',
                    id:'manaus',
                    cord:[-7056381.0701773185,-1100925.4640611075]
                },
                {
                    gmt:'-03:00',
                    id:'para',
                    cord:[-5590690.745850078,-508887.3053523686]
                },
                {
                    gmt:'-03:00',
                    id:'mato-grosso',
                    cord:[-6036690.745850078,-2108887.3053523686]
                },
                {
                    gmt:'-03:00 <b>OFICIAL</b>',
                    id:'brasilia',
                    cord:[-5105055.87810736,-2543701.8562444302]
                },
                {
                    gmt:'-03:00',
                    id:'nordeste',
                    cord:[-4402772.82922615,-1237097.5997732712]
                },
                {
                    gmt:'-02:00',
                    id:'fernando',
                    cord:[-3102772.82922615,-907097.5997732712]
                }
            ];

            $.each(coords, function(i,e){
                var popup = new ol.Overlay.Popup({insertFirst: false});
                mapaAlerta.addOverlay(popup);
                popup.show(e.cord, '<h4 id="'+ e.id +'">'+ e.id +'</h4><h6 style="font-size: 11px">'+ e.gmt+'</h6>');
            });

            mapaAlerta.on('singleclick', function(evt) {
                console.log(JSON.stringify(evt.coordinate));
            });

            /*mapaAlerta.on('singleclick', function(evt) {
                var prettyCoord = ol.coordinate.toStringHDMS(ol.proj.transform(evt.coordinate, 'EPSG:3857', 'EPSG:4326'), 2);

            });*/

            /*mapaAlerta.on('click', function(evt) {
                closeLateralDireito();

                var feature = mapaAlerta.forEachFeatureAtPixel(evt.pixel,
                    function(feature, layer) {
                        return feature;
                    });

                if(feature){
                    if(feature.B.url){
                        loading(true);
                        $.ajax({
                            url: feature.B.url,
                            dataType:'html',
                            success:function(html){
                                $('.coluna-direita .modal-title').html(feature.B.name);
                                $('.coluna-direita').find('.panel-body').html(html);
                                openLateralDireito();
                                loading(false);
                            },
                            error:function(ret){
                                loading(false);
                            }
                        })
                    }else{
                        loading(false);
                    }
                }
            });

            loadAplicadores();
            loadPins();*/

        });




        function feedLayersFusoHorario(camadas){

            var url = '';
            $.each(camadas,function(index){
                var titleData = camadas[index].split('.');
                url = '/mapa/fuso-horario/'+titleData[0]+'.kml';

                layers.push(new ol.layer.Vector({
                    title: titleData[0],
                    tipo: 'kml',
                    visible: true,
                    source: new ol.source.Vector({
                        url: url,
                        format: new ol.format.KML()
                    })
                }));

            });

            mapaAlerta = new ol.Map({
                layers: layers,
                target: 'map',
                controls: [],
                interactions: [],
                renderer: 'canvas',
                view: new ol.View({
                    projection:'EPSG:3857',
                    center: ol.proj.transform([-47.9292,-15.7801], 'EPSG:4326', 'EPSG:3857'),
                    zoom: 4,
                    maxZoom: 18
                })
            });

        }

        function markScrollTimeline(){
            if($("table.timeline tbody td.acontecendo").length){
                var alturaAcontecendo = $("table.timeline tbody td.acontecendo").height();
                var position = $("table.timeline tbody td.acontecendo").position().top;
                var alturaContainer = $("#timeline").height() / 2;

                if($('#timeline').scrollTop() == 0){
                    var alturaScrollTimelineContainer = 0;
                }else{
                    var alturaScrollTimelineContainer = $('#timeline').scrollTop();
                }
                $('#timeline').animate({
                    scrollTop: $("#timeline").offset().top + position - alturaContainer - alturaAcontecendo + alturaScrollTimelineContainer
                }, 1000);
            }
        }

    }

    function rodarTabelaB(){
        if($('.lista.scroll-pane').find('.js-marquee-wrapper').length){
            $('.lista.scroll-pane').marquee('destroy');
        }

        if($('.lista.scroll-pane').height() < $('.lista.scroll-pane .details').height()){
            $('.lista.scroll-pane').marquee({
                pauseOnHover: true,
                duration: 10000,
                direction: 'up',
                duplicated: true,
                startVisible : true
            });
        }
    }

});

