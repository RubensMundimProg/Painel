/**
 * Created by bruno.rosa on 02/11/2016.
 */

$(function(){
    if($('div[data-menu="mapa-alerta"]').length){

        $('#form-frame.AvaliacaoPedagogica').hide();

        $(document).ready(function(){

            $('.footer').remove();

            layers = [
                new ol.layer.Tile({
                    source: new ol.source.OSM(),
                    title: 'background',
                }),
                /*new ol.layer.Vector({
                    title: 'municipios',
                    visible: true,
                    source: new ol.source.Vector({
                        projection: 'EPSG:3857',
                        url: '/dashboard-assets/geojson/municipios.geojson',
                        format: new ol.format.GeoJSON()
                    }),
                    style: new ol.style.Style({
                        image: new ol.style.Icon(({
                             anchor: [0.5, 0.5],
                             anchorXUnits: 'fraction',
                             anchorYUnits: 'fraction',
                            opacity: 0.75,
                            src: '/dashboard-assets/img/pins/pin_10.png'
                        }))
                    })
                })*/
            ];

            mapaAlerta = new ol.Map({
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

            mapaAlerta.on('click', function(evt) {
                closeLateralDireito();

                var feature = mapaAlerta.forEachFeatureAtPixel(evt.pixel,
                    function(feature, layer) {
                        return feature;
                    });

                if(feature){
                    if(feature.B.url){
                        console.log(feature.B);
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

            loadPins();
            loadAplicadores();
            loadPre();
        });

        $('#camadas .botao').click(function(e){

            var menu = $(this).find('a').attr('data-menu');
            var status = $(this).find('a').attr('data-status');

            if($(this).hasClass('active')){
                $(this).removeClass('active');
                //Remover camada
                mapaAlerta.getLayers().getArray().forEach(function(e){
                    var title = e.get('title');

                    if(status){
                        if(title.indexOf(menu) === 0 && e.get('status') == status){
                            e.setVisible(false);
                        }
                    }else{
                        if(title.indexOf(menu) === 0){
                            e.setVisible(false);
                        }
                    }
                });
            }else{
                $(this).addClass('active');
                //Adicionar camada
                mapaAlerta.getLayers().getArray().forEach(function(e){
                    var title = e.get('title');

                    if(status){
                        if(title.indexOf(menu) === 0 && e.get('status') == status){
                            e.setVisible(true);
                        }
                    }else{
                        if(title.indexOf(menu) === 0){
                            e.setVisible(true);
                        }
                    }
                });
            }

            e.stopPropagation();
        });


        function loadAplicadoresOld(){
            //loading(true);

            $.ajax({
                url:'/dashboard/get-aplicadores',
                dataType:'json',
                type:'GET',
                async:true,
                success:function(res){

                    //REMOVE PINS PARA ADICIONAR NOVOS

                    arrayAplicadores = [];

                    for(var i=0; i < res.length; i++){
                        if(arrayAplicadores.length > 100) continue;
                        var iconFeature = new ol.Feature({
                            geometry: new ol.geom.Point(ol.proj.transform([res[i].coordinates[0],res[i].coordinates[1]], 'EPSG:4326', 'EPSG:3857')),
                            name: res[i].title,
                            code: res[i].id,
                            url: res[i].url
                        });

                        var iconStyle = [new ol.style.Style({
                            zIndex: 3,
                            image: new ol.style.Icon(/** @type {olx.style.IconOptions} */ ({
                                anchor: [0, 0],
                                anchorXUnits: 'fraction',
                                anchorYUnits: 'fraction',
                                opacity: 0.75,
                                src: '/dashboard-assets/img/pins/'+definePinColor(res[i].tipo)+''
                            }))
                        })];
                        iconFeature.setStyle(iconStyle);

                        arrayAplicadores.push(iconFeature);

                    };
                    mapaAlerta.addLayer(
                        new ol.layer.Vector({
                            title: 'aplicadores',
                            tipo: 'aplicadores',
                            visible: true,
                            source: new ol.source.Vector({
                                features: arrayAplicadores
                            })
                        })
                    );

                    //loading(false);
                },
                error: function(err){
                    //loading(false);
                }
            })
        }

        function definePinColor(tipo){
            var colors = {
                'Cesgranrio':'pin_1.png',
                'FGV':'pin_3.png',
                'CAEd':'pin_13.png',
                'Cebraspe':'pin_6.png'
            };
            return colors[tipo];
        }

        function loadAplicadoresGeoJson(){
            mapaAlerta.addLayer(
                new ol.layer.Vector({
                    title: 'aplicadores',
                    tipo: 'aplicadores',
                    visible: true,
                    source: new ol.source.Vector({
                        url: '/dashboard/get-aplicadores',
                        format: new ol.format.GeoJSON()
                    })
                })
            );

        }

        function loadAplicadores(){
            $.ajax({
                url:'/dashboard/get-aplicadores',
                dataType:'json',
                type:'GET',
                async:true,
                success:function(json){
                    var qtt = 0;
                    var qttCooError = 0;
                    var qttNoTipo = 0;
                    $.each(json, function(indice,res){
                        arrayAplicadores = [];
                        for(var i=0; i < res.length; i++){
                            qtt++;
                            if(res[i].tipo == '') qttNoTipo++;

                            if(res[i].coordinates[0] % 1 === 0){
                                qttCooError++;
                                console.log(res[i].title);
                                continue;
                            }

                            var iconFeature = new ol.Feature({
                                geometry: new ol.geom.Point(ol.proj.transform([res[i].coordinates[0],res[i].coordinates[1]], 'EPSG:4326', 'EPSG:3857')),
                                name: res[i].title,
                                url: res[i].url
                            });

                            var pin = '/dashboard-assets/img/pins/'+definePinColor(res[i].tipo);

                            var iconStyle = [new ol.style.Style({
                                zIndex: 3,
                                image: new ol.style.Icon(/** @type {olx.style.IconOptions} */ ({
                                    anchor: [0, 0],
                                    anchorXUnits: 'fraction',
                                    anchorYUnits: 'fraction',
                                    opacity: 0.75,
                                    src: pin
                                }))
                            })];
                            iconFeature.setStyle(iconStyle);
                            arrayAplicadores.push(iconFeature);
                        };

                        mapaAlerta.addLayer(
                            new ol.layer.Vector({
                                title: 'aplicadoresLayer_'+indice,
                                tipo: 'aplicadoresLayer_'+indice,
                                visible: true,
                                source: new ol.source.Vector({
                                    features: arrayAplicadores
                                })
                            })
                        );
                    })

                    console.log(['Qtd municipios => '+qtt,'Qtd erro de coordenada => '+qttCooError,'Qtd sem tipo aplicador => '+qttNoTipo]);
                }
            })
        }
        function loadPre(){
            $.ajax({
                url:'/dashboard/alertas-pre/1',
                dataType:'json',
                type:'GET',
                async:true,
                success:function(res){
                    //REMOVE PINS PARA ADICIONAR NOVOS
                    mapaAlerta.getLayers().getArray().forEach(function(e){
                        if(e.get('tipo') == 'alerta-pre'){
                            setTimeout(function () {
                                mapaAlerta.removeLayer(e);
                            },100);
                        }

                        var arrayAlertas = [];
                        for(var i=0; i < res.length; i++){
                            var iconFeature = new ol.Feature({
                                geometry: new ol.geom.Point(ol.proj.transform([res[i].long,res[i].lat], 'EPSG:4326', 'EPSG:3857')),
                                name: res[i].municipio,
                                url: '/dashboard/alerta-detalhes-pre/'+res[i].municipio
                            });

                            var pin = '/dashboard-assets/img/pins/pin_9.png';

                            var iconStyle = [new ol.style.Style({
                                zIndex: 3,
                                image: new ol.style.Icon(/** @type {olx.style.IconOptions} */ ({
                                    anchor: [0.5, 0.5],
                                    anchorXUnits: 'fraction',
                                    anchorYUnits: 'fraction',
                                    opacity: 0.75,
                                    src: pin
                                }))
                            })];
                            iconFeature.setStyle(iconStyle);

                            arrayAlertas.push(iconFeature);
                        };

                        mapaAlerta.addLayer(
                            new ol.layer.Vector({
                                title: 'alerta-pre',
                                tipo: 'alerta-pre',
                                visible: false,
                                source: new ol.source.Vector({
                                    features: arrayAlertas
                                })
                            })
                        );

                    });
                }
            })
        }
        function loadPins(){
            //loading(true);

            $.ajax({
                url:'/dashboard/alertas-mapa',
                dataType:'json',
                type:'GET',
                async:true,
                success:function(res){
                    //REMOVE PINS PARA ADICIONAR NOVOS
                    mapaAlerta.getLayers().getArray().forEach(function(e){
                        if(e.get('tipo') == 'alerta'){
                            setTimeout(function () {
                                mapaAlerta.removeLayer(e);
                            },100);
                        }
                    });

                    arrayAlertas = [];
                    arrayAlertasAbertos = [];
                    arrayAlertasFechados = [];

                    for(var i=0; i < res.length; i++){
                        var iconFeature = new ol.Feature({
                            geometry: new ol.geom.Point(ol.proj.transform([res[i].long,res[i].lat], 'EPSG:4326', 'EPSG:3857')),
                            name: res[i].municipio,
                            url: '/dashboard/alerta-detalhes/'+res[i].municipio+'@'+res[i].status
                        });

                        var pin = '/dashboard-assets/img/pins/pin_12.png';

                        if(res[i].anexo && res[i].status == 'Fechado'){
                            pin = '/dashboard-assets/img/pins/pin_12_fechado_anexo.png';
                        } else if(res[i].anexo){
                            pin = '/dashboard-assets/img/pins/pin_12_anexo.png';
                        } else if(res[i].status == 'Fechado'){
                            pin = '/dashboard-assets/img/pins/pin_12_fechado.png';
                        }

                        var iconStyle = [new ol.style.Style({
                            zIndex: 3,
                            image: new ol.style.Icon(/** @type {olx.style.IconOptions} */ ({
                                anchor: [0.5, 0.5],
                                anchorXUnits: 'fraction',
                                anchorYUnits: 'fraction',
                                opacity: 0.75,
                                src: pin
                            }))
                        })];
                        iconFeature.setStyle(iconStyle);

                        if(res[i].status == 'Fechado'){
                            arrayAlertasFechados.push(iconFeature);
                        }
                        if(res[i].status == 'Aberto'){
                            arrayAlertasAbertos.push(iconFeature);
                        }
                    };

                    mapaAlerta.addLayer(
                        new ol.layer.Vector({
                            title: 'alertas',
                            tipo: 'alerta',
                            status:'Fechado',
                            visible: false,
                            source: new ol.source.Vector({
                                features: arrayAlertasFechados
                            })
                        })
                    );
                    mapaAlerta.addLayer(
                        new ol.layer.Vector({
                            title: 'alertas',
                            tipo: 'alerta',
                            status:'Aberto',
                            visible: false,
                            source: new ol.source.Vector({
                                features: arrayAlertasAbertos
                            })
                        })
                    );
                },
                error: function(err){
                    //loading(false);
                }
            })
        }

    }

    $('#btn-center-mapa').click(function(e){
        e.preventDefault();
        var municipio = $('#busca-municipio').val();
        if(municipio){
            $.ajax({
                url:'/dashboard/center-municipio/'+municipio,
                type:'GET',
                dataType:'JSON',
                success:function(res){
                    mapaAlerta.getView().setCenter(ol.proj.transform([res.long,res.lat], 'EPSG:4326', 'EPSG:3857'));
                    mapaAlerta.getView().setZoom(12);
                }
            })
        }
    });
    $('form.busca-mapa').submit(function(e){
        e.preventDefault();
        $('#btn-center-mapa').trigger('click');
    });

    $('body').on("keydown.autocomplete","#busca-municipio",function(e){
        $(this).autocomplete({
            source: "/triagem/filtrar-municipio",
            minLength: 3,
            search: function( event, ui ) {
                $(this).closest('.row').isLoading(
                    {
                        text : "Carregando... "
                    }
                );
            }
        });
    });

});

