/**
 * Created by bruno.rosa on 04/10/15.
 */
$(function () {
    ajustarTela();

    var urlProcess = '/dashboard/load-json';

    var code = '';
    var ultimoAlertaMarquee;

      var loop = 0;
        //setInterval(function(){
        //    loop++;
        //    $.ajax({
        //        url:'/dashboard-data/ultimo-alerta',
        //        type:'get',
        //        dataType:'json',
        //        success:function(json){
        //            if(!json.error){
        //                if(json.code != code){
        //                    $('.ultimo-alerta').html('<a href="javascript:void(0)" class="nome_ultimo_alerta"><i class="fa fa-exclamation-circle fa-normal blink-me"></i>&nbsp;'+json.titulo+'</a>');
        //                    code = json.code;
        //                    ultimoAlertaMarquee = $('.ultimo-alerta').marquee({
        //                        pauseOnHover: false,
        //                        duration: 10000,
        //                        direction: 'left'
        //                    });
        //                }else{
        //                    if(loop == 10){
        //                        ultimoAlertaMarquee.marquee('destroy');
        //                        $('.ultimo-alerta').html('');
        //                        loop = 0;
        //                    }
        //                }
        //            }
        //        }
        //    })
        //},10000);

        loading(true);
        $.ajax({
            url:urlProcess,
            dataType:'json',
            async:true,
            success:function(jsonData){
                console.log(jsonData);
                atualizarTabelaEventos(jsonData);
                //atualizarUltimaMilha(jsonData);
                rodarTabela();

//                OCULTA TODAS AS PÁGINAS NÃO ATIVAS
//                ocultarPáginasAfterLoad();



                Highcharts.setOptions({
                    global: {
                        useUTC: false
                    }
                });

                /* Aplicação */
                /// QUANTIDADE DE ALERTAS ABERTO E FECHADOS - FORMATO PIZZA
                qtdAbertosFechados(jsonData);
                /// QUANTIDADE DE ALERTAS ABERTOS E FECHADOS POR TIPO
                qtdPorCategoria(jsonData);


                /* Alerta */
                qtdAbertoEtapaProcesso(jsonData);
                timeLineEtapas(jsonData);
                qtdAbertosFechadosEtapa(jsonData);

                /* Estados */
                graficoEstados(jsonData);

                /* Atualiza os Gráficos */
                setInterval(function(){
                    $.ajax({
                        url:urlProcess,
                        dataType:'json',
                        success:function(json){
                            jsonData = json;
                        }
                    })
                },20000);

                function atualizarGraficosAlertas(){
                    console.log('Alerta Atualizado');

                    qtdAbertoEtapaProcesso(jsonData);
                    timeLineEtapas(jsonData);
                    qtdAbertosFechadosEtapa(jsonData);
                }

                function atualizarGraficosAplicacao(){
                    console.log('Aplicação Atualizado');

                    atualizarTabelaEventos(jsonData);
                    qtdAbertosFechados(jsonData);
                    qtdPorCategoria(jsonData);
                    atualizarTabelaEventosEtapa(jsonData);

                    loading(false);
                }

                function getJsonData(){
                    /// Ultima Milha
                    atualizarUltimaMilha(jsonData);

                    /// Alertas
                    atualizarGraficosAlertas(jsonData);

                    /// Aplicação
                    atualizarGraficosAplicacao();

                    /// Estados
                    graficoEstados(jsonData);

                    return jsonData;
                }

                $('.menu-paginas li').click(function(){
//                    loading(true);

                    var bloco = $(this).attr('data-bloco');

                    if(bloco == 'alertas'){
                        atualizarGraficosAlertas();
                    }

                    if(bloco == 'aplicacao'){
                        atualizarGraficosAplicacao();
                    }

                    if(bloco == 'estados'){
                        graficoEstados(jsonData);
                    }

                    if(bloco == 'ultima-milha'){
                        atualizarUltimaMilha(jsonData);
                    }
                })



            }
        });
});

function qtdAbertosFechados(jsonData){

    $('#qtd-abertos-fechados').highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: 'Quantidade de alertas abertos e fechados'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>',
            textTransform: 'uppercase',
            fontSize: '20px'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black',
                        textTransform: 'uppercase',
                        fontSize: '20px'
                    }
                }
            },
            series: {
                cursor: 'pointer',
                point: {
                    events: {
                        click: function () {
                            console.log(this);
                        }
                    }
                }
            }
        },
        credits: {
            enabled: false
        },
        series: [{
            name: "Eventos",
            colorByPoint: true,
            data: [{
                name: "Abertos",
                y: jsonData.abertos
            }, {
                name: "Fechados",
                y: jsonData.fechados
            }]
        }]
    });
}

function qtdAbertosFechadosEtapa(jsonData){

    $('#qtd-abertos-etapa-processo-pizza').highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: 'Quantidade de alertas abertos e fechados'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>',
            textTransform: 'uppercase',
            fontSize: '20px'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>:<br/>{point.percentage:.1f} %',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black',
                        textTransform: 'uppercase',
                        fontSize: '20px'
                    }
                }
            }
        },
        credits: {
            enabled: false
        },
        series: [{
            name: "Eventos",
            colorByPoint: true,
            data: [{
                name: "Abertos",
                y: jsonData.porEtapa.abertos
            }, {
                name: "Fechados",
                y: jsonData.porEtapa.fechados
            }]
        }]
    });
}

function timeLineEtapas(jsonData){
    $("#qtd-abertos-etapa-pizza").highcharts({
        colors: ["#55B152", "#548DD4", "#FEF84A", "#EC632F", "#D44534", "#cccccc"],
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: 'Quantidade de alertas abertos por nível'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>',
            textTransform: 'uppercase',
            fontSize: '20px'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>:<br/>{point.percentage:.1f} %',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black',
                        textTransform: 'uppercase',
                        fontSize: '20px'
                    }
                }
            }
        },
        credits: {
            enabled: false
        },
        series: [{
            name: "Eventos",
            colorByPoint: true,
            data: jsonData.porEtapa.pizza.Aberto
        }]
    });

    $("#qtd-fechados-etapa-pizza").highcharts({
        colors: ["#55B152", "#548DD4", "#FEF84A", "#EC632F", "#D44534", "#cccccc"],
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: 'Quantidade de fechados por nível'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>',
            textTransform: 'uppercase',
            fontSize: '20px'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>:<br/>{point.percentage:.1f} %',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black',
                        textTransform: 'uppercase',
                        fontSize: '20px'
                    }
                }
            }
        },
        credits: {
            enabled: false
        },
        series: [{
            name: "Eventos",
            colorByPoint: true,
            data: jsonData.porEtapa.pizza.Fechado
        }]
    });

    /*$('#qtd-eventos-data').highcharts({
        chart: {
            type: 'line'
        },
        title: {
            text: 'Eventos Abertos / Fechados'
        },
        subtitle: {
            text: 'Mês Atual'
        },
        xAxis: {
            categories: jsonData.porEtapa.nomeDatas
        },
        yAxis: {
            title: {
                text: 'Quantidade'
            }
        },
        plotOptions: {
            line: {
                dataLabels: {
                    enabled: true
                },
                enableMouseTracking: false
            }
        },
        series: jsonData.porEtapa.porData
    });*/
}

function qtdAbertoEtapaProcesso(jsonData){
    $('#qtd-abertos-etapa-processo').highcharts({
        colors: ["#55B152", "#548DD4", "#FEF84A", "#EC632F", "#D44534", "#cccccc"],
        chart: {
            type: 'column'
        },
        title: {
            text: 'Quantidade de alertas abertos por Etapa / Nível'
        },
        xAxis: {
            categories: jsonData.porEtapa.nomeCategorias,
            labels: {
                style: {
                    fontSize: '12px'
                }
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Total por situação'
            },
            stackLabels: {
                enabled: true,
                style: {
                    color: '#fbfbfb',
                    fontSize: "16px",
                    fontWeight: "bold",
                    textShadow: "0 0 6px contrast, 00 3px contrast"
                }
            }
        },
        legend: {
            align: 'right',
            x: -30,
            verticalAlign: 'top',
            y: 25,
            floating: true,
            backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
            borderColor: '#CCC',
            borderWidth: 1,
            shadow: false
        },
        plotOptions: {
            column: {
                stacking: 'normal',
                pointPadding: 0,
                groupPadding: 0,
                dataLabels: {
                    enabled: true,
                    color: '#fbfbfb',
                    align:'right',
                    style: {
                        textShadow: '0 0 0 black',
                        textShadow: "0 0 1px contrast, 00 1px contrast",
                        fontSize: '11px',
                        fontWeight: "normal",
                        color: '#fbfbfb'
                    }
                }
            },
            series: {
                cursor: 'pointer',
                point: {
                    events: {
                        click: function () {
                            var category = this.category;
                            var color = this.color;

                            $('input[name="color"]').val(color);
                            $('input[name="category"]').val(category);
                            $('input[name="get"]').val('alerta-etapa');

                            $('#formListaEventos').submit();
/*
                            $.ajax({
                                url:'/dashboard-data/lista-eventos/alerta-etapa',
                                data:{'category':category,'color':color},
                                type:'post',
                                dataType:'json',
                                success:function(json){
                                    $('.tbody-etapa').html('');

                                    var cor = '';
                                    $.each(json, function(i,e){
                                        cor = checaCorCriticidade(e.nivel_do_alerta_sgir);
                                        var tr = '<tr data-code="'+e.EventID+'" class="ver-detalhes-evento">'
                                            +'<td class="fx-20 '+cor+'">'+e.nivel_do_alerta_sgir+'</td>'
                                            +'<td class="fx-40">'+e.Title+'</td>'
                                            +'<td class="fx-40">'+e.caminho_critico+'</td>'
                                            +'</tr>';

                                        $('.tbody-etapa').append(tr);
                                    })

                                    rodarTabela();
                                }
                            })

                            */
                        }
                    }
                }
            }
        },
        credits: {
            enabled: false
        },
        series: jsonData.porEtapa.porCategoria
    });
}

function qtdPorCategoria(jsonData){

    $('#qtd-abertos-fechados-categoria').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: 'Quantidade de alertas abertos e fechados por categoria'
        },
        xAxis: {
            categories: jsonData.nomeCategorias
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Total por situação'
            },
            stackLabels: {
                enabled: true,
                style: {
                    color: '#fbfbfb',
                    fontSize: "16px",
                    fontWeight: "bold",
                    textShadow: "0 0 6px contrast, 00 3px contrast"
                }
            }
        },
        legend: {
            align: 'right',
            x: -30,
            verticalAlign: 'top',
            y: 25,
            floating: true,
            backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
            borderColor: '#CCC',
            borderWidth: 1,
            shadow: false
        },
        plotOptions: {
            column: {
                stacking: 'normal',
                pointPadding: 0,
                groupPadding: 0,
                dataLabels: {
                    enabled: true,
                    color: '#fbfbfb',
                    align:'right',
                    style: {
                        textShadow: '0 0 0 black',
                        textShadow: "0 0 1px contrast, 00 1px contrast",
                        fontSize: '11px',
                        fontWeight: "normal",
                        color: '#fbfbfb'
                    }
                }
            },
            series: {
                cursor: 'pointer',
                point: {
                    events: {
                        click: function () {
                            var category = this.category;
                            var color = this.color;

                            $('input[name="color"]').val(color);
                            $('input[name="category"]').val(category);

                            $('#formListaEventos').submit();

                            /*$.ajax({
                                url:'/dashboard-data/lista-eventos/aplicacao-categoria',
                                data:{'category':category,'color':color},
                                type:'post',
                                dataType:'json',
                                success:function(json){
                                    $('.tbody-alertas').html('');

                                    $.each(json, function(i,e){
                                        var categoria = (e.categoria_evento == null) ? '' : e.categoria_evento;
                                        var tr = '<tr class="ver-detalhes-evento" data-code='+e.EventID+'>'
                                            +'<td>'+e.EventID+'</td>'
                                            +'<td>'+e.Title+'</td>'
                                            +'<td>'+categoria+'</td>'
                                            +'</tr>';

                                        $('.tbody-alertas').append(tr);
                                    })

                                    rodarTabela();
                                }
                            })*/
                        }
                    }
                }
            }
        },
        credits: {
            enabled: false
        },
        series: jsonData.porCategoria
    });
}

function atualizarTabelaEventos(jsonData){
    $('.tbody-alertas').html('');

    $.each(jsonData.dashboard.dados.eventos, function(i,e){
        var categoria = (e.categoria_evento == null) ? '' : e.categoria_evento;
        var tr = '<tr class="ver-detalhes-evento" data-code='+e.EventID+'>'
                    +'<td>'+e.municipio_de_aplicacao+'</td>'
                    +'<td>'+categoria+'</td>'
                    +'<td>'+e.Title+'</td>'
                  +'</tr>';

        $('.tbody-alertas').append(tr);
    })
}

function atualizarTabelaEventosEtapa(jsonData){
    $('.tbody-etapa').html('');

    //console.log(jsonData.porEtapa.data);

    var cor = '';
    $.each(jsonData.porEtapa.data, function(i,e){
        cor = checaCorCriticidade(e.nivel);
        var tr = '<tr data-code="'+e.evento+'" class="ver-detalhes-evento">'
                    +'<td class="fx-20 '+cor+'">'+e.nivel+'</td>'
                    +'<td class="fx-40">'+e.titulo+'</td>'
                    +'<td class="fx-40">'+e.etapa+'</td>'
                +'</tr>';

        $('.tbody-etapa').append(tr);
    })
}

function checaCorCriticidade(criticidade){
    var classCriticidade = '';


    switch (criticidade) {
        case '1 - Baixo':
            classCriticidade = 'txt-baixo';
            break;
        case '2 - Atenção':
            classCriticidade = 'txt-atencao';
            break;
        case '3 - Elevado':
            classCriticidade = 'txt-elevado';
            break;
        case '4 - Alto':
            classCriticidade = 'txt-alto';
            break;
        case '5 - Severo':
            classCriticidade = 'txt-severo';
            break;
        case 'Não Qualificado':
            classCriticidade = 'txt-nao-qualificado';
            break;
    }

    return classCriticidade;
}

function atualizarUltimaMilha(jsonData){
    $('.tbody-ultima').html('');

    var entregues = 0;

    $.each(jsonData.ultima_milha, function(i,e){
        var classNao = (e.nao == 100) ? 'bkg-red' : 'txt-red';
        var classSim = (e.nao == 100) ? 'bkg-green' : 'txt-green';
        
        var html = '<tr><td>'+e.uf+'</td><td class="'+classNao+'">'+e.nao+'%</td><td class="'+classSim+'">'+e.entregue+'%</td></tr>';
        $('.tbody-ultima').append(html);

        if(e.uf == 'BR'){
            entregues = e.entregue;
        }
    });

    velocimetro(entregues);
}

function velocimetro(entregues){
    var gaugeOptions = {

        chart: {
            type: 'solidgauge',
            backgroundColor:'#2b3e50',
            style:{
                color:'#fbfbfb'
            }
        },

        title: null,

        pane: {
            center: ['50%', '85%'],
            size: '120%',
            startAngle: -90,
            endAngle: 90,
            background: {
                backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || '#EEE',
                innerRadius: '60%',
                outerRadius: '100%',
                shape: 'arc'
            }
        },

        tooltip: {
            enabled: false
        },

        // the value axis
        yAxis: {
            stops: [
                [0.1, '#55BF3B'], // green
                [0.5, '#DDDF0D'], // yellow
                [0.9, '#DF5353'] // red
            ],
            lineWidth: 0,
            minorTickInterval: null,
            tickPixelInterval: 400,
            tickWidth: 0,
            title: {
                y: -70
            },
            labels: {
                y: 16
            }
        },

        plotOptions: {
            solidgauge: {
                dataLabels: {
                    y: 5,
                    borderWidth: 0,
                    useHTML: true
                }
            }
        }
    };

    // The speed gauge
    $('#velocimetro').highcharts(Highcharts.merge(gaugeOptions, {
        yAxis: {
            min: 0,
            max: 100,
            title: {
                text: ''
            }
        },

        credits: {
            enabled: false
        },

        series: [{
            name: 'entregue',
            data: [entregues],
            dataLabels: {
                format: '<div style="text-align:center"><span style="font-size:25px;color:#fff">{y}%</span><br/>' +
                '<span style="font-size:12px;color:silver">ENTREGUE</span></div>'
            },
            tooltip: {
                valueSuffix: ' %'
                }
            }]

        }
    ));
}

function graficoEstados(jsonData){
    $(function () {
        $('#qtd-abertos-estados').highcharts({
            chart: {
                type: 'column',
                events: {
                    click: function(e){
//                        console.log(e.point);
//                        console.log(this.name);
                    }
                }
            },
            title: {
                text: 'Quantidade de Alertas por UF'
            },
            subtitle: {
                text: 'Clique na UF para filtrar os eventos'
            },
            xAxis: {
                categories: jsonData.estados.nomeEstados,
                crosshair: true
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Quantidade'
                }
            },
            legend: {
                align: 'right',
                x: -30,
                verticalAlign: 'top',
                y: 25,
                floating: true,
                backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
                borderColor: '#CCC',
                borderWidth: 1,
                shadow: false
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0,
                    dataLabels: {
                        enabled: true,
                        color: '#fbfbfb',
                        style: {
                            textShadow: '0 0 0 black',
                            fontSize: '16px',
                            color: '#fbfbfb'
                        }
                    }
                },
                bar: {
                    dataLabels: {
                        enabled: true
                    }
                },
                series: {
                    cursor: 'pointer',
                    point: {
                        events: {
                            click: function () {
                                var uf = this.category;
                                $('.tbody-estado').html('');
                                loadingTable(true);
                                $.ajax({
                                    url:'/dashboard-data/alertas-uf/'+uf,
                                    success:function(json){
                                        $('.tbody-estado').html('');
                                        $.each(json, function(i,e){
                                            var tr = '<tr data-code="'+e.codigo+'" class="ver-detalhes-evento">'
                                                +'<td>'+e.municipio+'</td>'
                                                +'<td>'+e.titulo+'</td>'
                                                +'<td>'+e.categoria+'</td>'
                                                +'</tr>';

                                            $('.tbody-estado').append(tr);


                                        })

                                        loadingTable(false);
                                        rodarTabela();
                                    }
                                })
                            }
                        }
                    }
                }
            },
            credits: {
                enabled: false
            },
            series: jsonData.estados.valores
        });
    });
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

function loadingTable(show){
    if(show){
        var topTable = $('.tab-pane.active .rodar-tabela').offset();
        var top = topTable.top + 50;

        $('body').append('<div class="carregando-container"><p class="txt-carregando blink-me text-center">CARREGANDO</p></div>');
        $('.carregando-container').css('top',top+'px');
    }else{
        $('body').find('.carregando-container').remove();
    }
}

jQuery.fn.center = function () {
    this.css("position","absolute");
    this.css("top", Math.max(0, (($(window).height() - $(this).outerHeight()) / 2) +
        $(window).scrollTop()) + "px");
    this.css("left", Math.max(0, (($(window).width() - $(this).outerWidth()) / 2) +
        $(window).scrollLeft()) + "px");
    return this;
}

