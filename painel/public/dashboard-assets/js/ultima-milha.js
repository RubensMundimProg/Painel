/**
 * Created by bruno.rosa on 15/03/2016.
 */

$(function(){
    if($('div[data-menu="ultima-milha"]').length){

        $('#form-frame.AvaliacaoPedagogica').hide();

        $(document).ready(function(){

            $('.footer').remove();

            layers = [
                new ol.layer.Tile({
                    source: new ol.source.OSM()
                }),
                new ol.layer.Group({
                    title: 'satelite',
                    visible: false,
                    layers: [
                        new ol.layer.Tile({
                            source: new ol.source.MapQuest({layer: 'sat'})
                        }),
                        new ol.layer.Tile({
                            source: new ol.source.MapQuest({layer: 'hyb'})
                        })
                    ]
                })
            ];

            $.ajax({
                url:'/dashboard/getKmlsEstados',
                dataType:'json',
                type:'GET',
                async:false,
                success:function(res){

                    feedLayers(res.dados);

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

        });


        function feedLayers(camadas){

            var url = '';
            $.each(camadas,function(index){
                var titleData = camadas[index].split('.');
                url = '/mapa/estados/'+titleData[0]+'.kml';

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

            mapUltimaMilha = new ol.Map({
                layers: layers,
                target: 'map',
                controls: ol.control.defaults({
                    attributionOptions: /** @type {olx.control.AttributionOptions} */ ({
                        collapsible: false
                    })
                }).extend([
                    new ol.control.ScaleLine()
                ]),
                renderer: 'canvas',
                view: new ol.View({
                    projection:'EPSG:3857',
                    center: ol.proj.transform([-47.9292,-15.7801], 'EPSG:4326', 'EPSG:3857'),
                    zoom: 4,
                    maxZoom: 18
                })
            });

            loadTabela();
            setInterval(function(){
                loadTabela();
                refreshKml();
            },INTERVALOR_ULTIMA_MILHA);

        }

        function refreshKml(){

            mapUltimaMilha.getLayers().getArray().forEach(function(e){
                if(e.get('tipo') == 'kml' && e.get('visible')){
                    var newUrl = '/mapa/estados/'+encodeURI(e.get('title'))+'.kml';
                    var source = new ol.source.Vector({
                        url: newUrl,
                        format: new ol.format.KML()
                    });
                    e.get('source').clear(true);
                    e.set('source',source);
                }
            });

        }


        var rodaTabelaUltimaMilha = [];
        function rodarTabelaUltimaMilha(){
            $('.rodar-tabela').each(function(i){
                if($(this).find('.js-marquee-wrapper').length){
                    rodaTabela[i].marquee('destroy');
                }
            });

            var altContainerTable = 0;
            var altTable = 0;

            $('.rodar-tabela').each(function(i){
                altContainerTable = $('.container-table-ultima-milha').height();
                altTable = $('.container-table-ultima-milha').find('.rodar-tabela').height();

                if(altTable > altContainerTable){
                    rodaTabela[i] = $(this).marquee({
                        pauseOnHover: true,
                        duration: 10000,
                        direction: 'up',
                        duplicated: true,
                        startVisible : true
                    });
                }
            });
        }

        function loadTabela(){
            $.ajax({
                url:'/dashboard/getPorcentageUltimaMilha',
                dataType:'json',
                type:'GET',
                async:true,
                success:function(res){

                    if(res.error){
                        showMsg(res,true,false);
                    }else{
                        $('tbody.tabela-ultima-milha tr').remove();
                        $.each(res.dados,function(i,v){
                            var classe = '';
                            if(v < 34) classe = 'tr-red';
                            if(v > 33 && v < 67) classe = 'tr-orange';
                            if(v > 66 && v < 100) classe = 'tr-yellow';
                            if(v > 99) classe = 'tr-green';
                            $('tbody.tabela-ultima-milha').append('<tr class='+classe+'><td>'+i+'</td><td>'+v+'%</td></tr>');
                        });

                        rodarTabelaUltimaMilha();
                    }

                },
                error: function(err){
                    //loading(false);
                }
            });
        }


    }

});