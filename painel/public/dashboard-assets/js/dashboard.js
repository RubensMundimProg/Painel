/**
 * Created by bruno.rosa on 15/03/2016.
 */

$(function(){

    $('body').on('change','select[data-sistema]', function(){
        var sistema = $(this).val();
        if(!sistema) return false;
        loading(true);
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

    if($('div[data-menu="dashboard"]').length){

        $(document).ready(function(){

            loading(true);

            $('li[data-menu="filtro"]').removeClass('hide');

            $('form.range-date-dashboard input').val('');

            intervalo = '';
            filtered = [];
            INTERVALOTOGGLE = true;
            datas = {};

            $('body').on('submit','form.range-date-dashboard',function(e){
                e.preventDefault();

                var startDate = $('[name=start-date]').val();
                var endDate = $('[name=end-date]').val();
                var avaliacaoPedagogica = $('[name=AvaliacaoPedagogica]').val();

                //VALIDACAO CAMPO DATA
                if(moment(startDate, "DD/MM/YYYY") > moment(endDate,"DD/MM/YYYY")){
                    addMessage('danger','Data início não poderá ser maior que a data fim.');
                    return false;
                }

                var hoje = moment().format("DD/MM/YYYY");

                if(moment(startDate, "DD/MM/YYYY") > moment(hoje, "DD/MM/YYYY")){
                    addMessage('danger','Data início não poderá ser maior que a data de hoje.');
                    return false;
                }

                datas = $(this," input").serialize();

                clearInterval(intervalo);
                loading(true);
                INTERVALOTOGGLE = false;

                $('.navbar li[data-menu="filtro"]').addClass('filtered');
                $('#filtro-date-frame').html('<h5><b>'+startDate+'</b> à <b>'+endDate+'</b></h5>');
                getDashboardData(datas);

            });

            $('body').on('reset','form.range-date-dashboard',function(e){

                datas = {};
                loading(true);
                INTERVALOTOGGLE = true;
                $('.navbar li[data-menu="filtro"]').removeClass('filtered');
                $('#filtro-date-frame > *').remove();
                getDashboardData(datas);
                $('.modal').modal('hide');
            });

            $('input[data-mask="datepicker"]').datepicker({
                format: "dd/mm/yyyy",
                autoclose: true,
                language: "pt-BR",
                orientation: "bottom auto",
                todayHighlight: true
            });


            jsonRet = [];

            getDashboardData(datas);

            $('.carousel').carousel({
                pause: "hover",
                interval: false
            });

            $('.container-fluid').css('padding-right','0');
            $('.container-fluid').css('padding-left','0');

            ajustarTela();

            //DASHBOARD
            Highcharts.setOptions({
                global: {
                    useUTC: false
                },
                lang: {
                    drillUpText: '<< Voltar {series.name}'
                }
            });

            qtdStatus = new Highcharts.Chart({
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie',
                    renderTo: 'total-registros-por-situacao',
                    animation: Highcharts.svg // don't animate in old IE
                },
                credits: {
                    enabled: false
                },
                title: {
                    text: 'Total de Registros Por Situação'
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.y}</b>'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.y}',
                            style: {
                                color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                            }
                        }
                    }
                },
                series: [{
                    name: 'Registros',
                    colorByPoint: true,
                    data: []
                }]
            });

            //qtdStatusB = new Highcharts.Chart({
            //    chart: {
            //        plotBackgroundColor: null,
            //        plotBorderWidth: null,
            //        plotShadow: false,
            //        type: 'pie',
            //        renderTo: 'total-registros-por-situacao-b',
            //        animation: Highcharts.svg // don't animate in old IE
            //    },
            //    credits: {
            //        enabled: false
            //    },
            //    title: {
            //        text: 'Total de Registros Por Situação'
            //    },
            //    tooltip: {
            //        pointFormat: '{series.name}: <b>{point.y}</b>'
            //    },
            //    plotOptions: {
            //        pie: {
            //            allowPointSelect: true,
            //            cursor: 'pointer',
            //            dataLabels: {
            //                enabled: true,
            //                format: '<b>{point.name}</b>: {point.y}',
            //                style: {
            //                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
            //                }
            //            }
            //        }
            //    },
            //    series: [{
            //        name: 'Registros',
            //        colorByPoint: true,
            //        data: []
            //    }]
            //});

            categoriaSubcategoriaUf = new Highcharts.Chart({
                chart: {
                    type: 'column',
                    renderTo: 'categoria-subcategoria-uf',
                    animation: Highcharts.svg, // don't animate in old IE
                    events: {
                        drillup: function (e) {
                            showRegistrosRelacionados(e.seriesOptions.name,'#lista-alertas','up');
                        },
                        drilldown: function (e) {
                            showRegistrosRelacionados(e.seriesOptions.id,'#lista-alertas','down');
                        }
                    }
                },
                credits: {
                    enabled: false
                },
                title: {
                    text: 'Qtd Alertas por categoria > subcategoria > uf'
                },
                subtitle: {
                    text: 'Fonte: Risk Manager'
                },
                xAxis: {
                    type: 'category',
                    labels: {
                        formatter: function() {
                            return '<span style="font-size: 11px">'+this.value+'</span>';
                        }
                    }
                },
                yAxis: {
                    title: {
                        text: 'Total de Alertas'
                    }

                },
                legend: {
                    enabled: false
                },
                plotOptions: {
                    series: {
                        borderWidth: 0,
                        dataLabels: {
                            enabled: true,
                            format: '{point.y:1f}'
                        }
                    }
                },

                tooltip: {
                    headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                    pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:1f}</b><br/>'
                },

                series: [{
                    name: 'Categorias',
                    colorByPoint: true,
                    data: []
                }],
                drilldown: {
                    drillUpButton: {
                        relativeTo: 'spacingBox',
                        position: {
                            y: 60,
                            x: -50
                        }
                    }
                }
            });

            diaAplicacao = new Highcharts.Chart({
                chart: {
                    type: 'column',
                    renderTo: 'dia-aplicacao',
                    animation: Highcharts.svg // don't animate in old IE
                },
                credits: {
                    enabled: false
                },
                title: {
                    text: 'Quantidade de Alertas por dia de Aplicação'
                },
                subtitle: {
                    text: 'Fonte: Risk Manager'
                },
                xAxis: {
                    type: 'category',
                    labels: {
                        formatter: function() {
                            return '<span style="font-size: 11px">'+this.value+'</span>';
                        }
                    }
                },
                yAxis: {
                    title: {
                        text: 'Total de Alertas'
                    }

                },
                legend: {
                    enabled: false
                },
                plotOptions: {
                    series: {
                        borderWidth: 0,
                        dataLabels: {
                            enabled: true,
                            format: '{point.y:1f}'
                        }
                    }
                },

                tooltip: {
                    headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                    pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:1f}</b><br/>'
                },

                series: [{
                    name: 'Quantidade',
                    colorByPoint: true,
                    data: []
                }],
                drilldown: {
                    drillUpButton: {
                        relativeTo: 'spacingBox',
                        position: {
                            y: 60,
                            x: -50
                        }
                    }
                }
            });



            ufCategoriaSubcategoria = new Highcharts.Chart({
                chart: {
                    type: 'column',
                    renderTo: 'uf-categoria-subcategoria',
                    animation: Highcharts.svg // don't animate in old IE
                },
                credits: {
                    enabled: false
                },
                title: {
                    text: 'Qtd Alertas por uf > categoria > subcategoria'
                },
                subtitle: {
                    text: 'Fonte: Risk Manager'
                },
                xAxis: {
                    type: 'category',
                    labels: {
                        formatter: function() {
                            return '<span style="font-size: 11px">'+this.value+'</span>';
                        }
                    }
                },
                yAxis: {
                    title: {
                        text: 'Total de Alertas'
                    }

                },
                legend: {
                    enabled: false
                },
                plotOptions: {
                    series: {
                        borderWidth: 0,
                        dataLabels: {
                            enabled: true,
                            format: '{point.y:1f}'
                        }
                    }
                },

                tooltip: {
                    headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                    pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:1f}</b><br/>'
                },

                series: [{
                    name: 'UFs',
                    colorByPoint: true,
                    data: []
                }],
                drilldown: {
                    drillUpButton: {
                        relativeTo: 'spacingBox',
                        position: {
                            y: 60,
                            x: -50
                        }
                    }
                }
            });

            avaliacaoPedagogicaUfCategoria = new Highcharts.Chart({
                chart: {
                    type: 'column',
                    renderTo: 'avaliacao-pedagogica-uf-categoria',
                    animation: Highcharts.svg, // don't animate in old IE
                    events: {
                        drillup: function (e) {
                            showRegistrosRelacionados(e.seriesOptions.name,'#lista-alertas-b','up');
                        },
                        drilldown: function (e) {
                            showRegistrosRelacionados(e.seriesOptions.id,'#lista-alertas-b','down');
                        }
                    }
                },
                credits: {
                    enabled: false
                },
                title: {
                    text: 'Qtd Alertas por avaliação pedagógica > uf > categoria'
                },
                subtitle: {
                    text: 'Fonte: Risk Manager'
                },
                xAxis: {
                    type: 'category',
                    labels: {
                        formatter: function() {
                            return '<span style="font-size: 11px">'+this.value+'</span>';
                        }
                    }
                },
                yAxis: {
                    title: {
                        text: 'Total de Alertas'
                    }

                },
                legend: {
                    enabled: false
                },
                plotOptions: {
                    series: {
                        borderWidth: 0,
                        dataLabels: {
                            enabled: true,
                            format: '{point.y:1f}'
                        }
                    }
                },

                tooltip: {
                    headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                    pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:1f}</b><br/>'
                },

                series: [{
                    name: 'UFs',
                    colorByPoint: true,
                    data: []
                }],
                drilldown: {
                    drillUpButton: {
                        relativeTo: 'spacingBox',
                        position: {
                            y: 60,
                            x: -50
                        }
                    }
                }
            });

            topDezCategoriaSubcategoria = new Highcharts.Chart({
                chart: {
                    type: 'column',
                    renderTo: 'top-dez-categoria-subcategoria',
                    animation: Highcharts.svg // don't animate in old IE
                },
                credits: {
                    enabled: false
                },
                title: {
                    text: 'Top 10 Categorias'
                },
                subtitle: {
                    text: 'Fonte: Risk Manager'
                },
                xAxis: {
                    type: 'category'
                },
                yAxis: {
                    title: {
                        text: 'Total de Alertas'
                    }

                },
                legend: {
                    enabled: false
                },
                plotOptions: {
                    series: {
                        borderWidth: 0,
                        dataLabels: {
                            enabled: true,
                            format: '{point.y:1f}'
                        }
                    }
                },

                tooltip: {
                    headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                    pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:1f}</b><br/>'
                },

                series: [{
                    name: 'Alertas',
                    colorByPoint: true,
                    data: []
                }],
                drilldown: {}
            });

            totalOcorrencias = new Highcharts.Chart({
                chart: {
                    type: 'column',
                    renderTo: 'total-ocorrencias',
                    animation: Highcharts.svg // don't animate in old IE
                },
                credits: {
                    enabled: false
                },
                title: {
                    text: 'Total de Ocorrências'
                },
                subtitle: {
                    text: 'Fonte: Risk Manager'
                },
                xAxis: {
                    type: 'category'
                },
                yAxis: {
                    title: {
                        text: 'Total de Alertas'
                    }

                },
                legend: {
                    enabled: false
                },
                plotOptions: {
                    series: {
                        borderWidth: 0,
                        dataLabels: {
                            enabled: true,
                            format: '{point.y:1f}'
                        }
                    }
                },

                tooltip: {
                    headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                    pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:1f}</b><br/>'
                },

                series: [{
                    name: 'Ocorrências',
                    colorByPoint: true,
                    data: []
                }],
                drilldown: {}
            });

            ufNivel = new Highcharts.Chart({
                chart: {
                    type: 'column',
                    renderTo: 'uf-nivel',
                    animation: Highcharts.svg // don't animate in old IE
                },
                colors: ['red', 'orange', 'gold', 'lightgreen', 'green'],
                credits: {
                    enabled: false
                },
                title: {
                    text: 'Uf x Nível'
                },
                subtitle: {
                    text: 'Fonte: Risk Manager'
                },
                xAxis: {},
                yAxis: {
                    title: {
                        text: 'Total de Alertas'
                    }
                },
                legend: {
                    enabled: false
                },
                plotOptions: {
                    series: {
                        borderWidth: 0,
                        dataLabels: {
                            enabled: true,
                            format: '{point.y:1f}'
                        }
                    }
                },

                tooltip: {
                    headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                    pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:1f}</b><br/>'
                },

                series: [],
                drilldown: {}
            });

            //topDezUsuarios = new Highcharts.Chart({
            //    chart: {
            //        type: 'column',
            //        renderTo: 'top-10-usuarios',
            //        animation: Highcharts.svg // don't animate in old IE
            //    },
            //    credits: {
            //        enabled: false
            //    },
            //    title: {
            //        text: 'Top 10 Usuários'
            //    },
            //    subtitle: {
            //        text: 'Fonte: Risk Manager'
            //    },
            //    xAxis: {
            //        type: 'category'
            //    },
            //    yAxis: {
            //        title: {
            //            text: 'Total de Alertas'
            //        }
            //
            //    },
            //    legend: {
            //        enabled: false
            //    },
            //    plotOptions: {
            //        series: {
            //            borderWidth: 0,
            //            dataLabels: {
            //                enabled: true,
            //                format: '{point.y:1f}'
            //            }
            //        }
            //    },
            //
            //    tooltip: {
            //        headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
            //        pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:1f}</b><br/>'
            //    },
            //
            //    series: [{
            //        name: 'Usuários',
            //        colorByPoint: true,
            //        data: []
            //    }],
            //    drilldown: {}
            //});

            /*abstencao = new Highcharts.Chart({
                chart: {
                    type: 'column',
                    renderTo: 'abstencao',
                    animation: Highcharts.svg // don't animate in old IE
                },
                colors: ['red', 'green'],
                credits: {
                    enabled: false
                },
                title: {
                    text: 'Abstenção x Presentes'
                },
                subtitle: {
                    text: 'Fonte: Risk Manager'
                },
                xAxis: {},
                yAxis: {
                    title: {
                        text: 'Total'
                    }
                },
                legend: {
                    enabled: false
                },
                plotOptions: {
                    series: {
                        borderWidth: 0,
                        dataLabels: {
                            enabled: true,
                            format: '{point.y:1f}'
                        }
                    }
                },

                tooltip: {
                    headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                    pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:1f}</b><br/>'
                },

                series: [],
                drilldown: {}
            });*/

            linhaTempo = new Highcharts.Chart({
                chart: {
                    type: 'spline',
                    renderTo: 'linha-tempo',
                    animation: Highcharts.svg, // don't animate in old IE
                    marginRight: 10,
                    events: {
                        load: function () {
                            // set up the updating of the chart each second
                            var series = this.series[0];
                            setInterval(function () {
                                var x = (new Date()).getTime(); // current time
                                series.addPoint([x, jsonRet.dashboard.dados.linha_tempo], true, true);
                            }, 1000*60); //1 minuto
                        }
                    }
                },
                colors: ['#df691a'],
                credits: {
                    enabled: false
                },
                title: {
                    text: 'ALERTAS EM ABERTO POR PERÍODO'
                },
                xAxis: {
                    type: 'datetime',
                    tickPixelInterval: 150
                },
                yAxis: {
                    title: {
                        text: 'ALERTAS'
                    },
                    plotLines: [{
                        value: 0,
                        width: 1,
                        color: '#808080'
                    }]
                },
                tooltip: {
                    formatter: function () {
                        return '<b>' + this.series.name + '</b><br/>' +
                            Highcharts.dateFormat('%d-%m-%Y %H:%M:%S', this.x) + '<br/>' +
                            Highcharts.numberFormat(this.y, 0);
                    }
                },
                legend: {
                    enabled: false
                },
                exporting: {
                    enabled: false
                },
                series: [{
                    name: 'QUANTIDADE DE ALERTAS',
                    marker: {
                        symbol: 'square'
                    },
                    data: (function () {
                        // generate an array of random data
                        var data = [],
                            time = (new Date()).getTime(),
                            i;

                        for (i = -19; i <= 0; i += 1) {
                            data.push({
                                x: time + i * 1000,
                                y: 0
                            });
                        }
                        return data;
                    }())
                }]
            });

            usuariosOnline = new Highcharts.Chart({
                chart: {
                    type: 'spline',
                    renderTo: 'linha-tempo-usuarios-online',
                    animation: Highcharts.svg, // don't animate in old IE
                    marginRight: 10,
                    events: {
                        load: function () {
                            // set up the updating of the chart each second
                            var series = this.series[0];
                            setInterval(function () {
                                var x = (new Date()).getTime(); // current time
                                series.addPoint([x, jsonRet.dashboard.dados.users_online_number], true, true);
                            }, 1000*60); //1 minuto
                        }
                    }
                },
                colors: ['#55bf3b'],
                credits: {
                    enabled: false
                },
                title: {
                    text: 'USUÁRIOS ONLINE POR PERÍODO'
                },
                xAxis: {
                    type: 'datetime',
                    tickPixelInterval: 150
                },
                yAxis: {
                    title: {
                        text: 'USUÁRIOS'
                    },
                    plotLines: [{
                        value: 0,
                        width: 1,
                        color: '#808080'
                    }]
                },
                tooltip: {
                    formatter: function () {
                        return '<b>' + this.series.name + '</b><br/>' +
                            Highcharts.dateFormat('%d-%m-%Y %H:%M:%S', this.x) + '<br/>' +
                            Highcharts.numberFormat(this.y, 0);
                    }
                },
                legend: {
                    enabled: false
                },
                exporting: {
                    enabled: false
                },
                series: [{
                    name: 'QUANTIDADE DE USUÁRIOS',
                    marker: {
                        symbol: 'square'
                    },
                    data: (function () {
                        // generate an array of random data
                        var data = [],
                            time = (new Date()).getTime(),
                            i;

                        for (i = -19; i <= 0; i += 1) {
                            data.push({
                                x: time + i * 1000,
                                y: 0
                            });
                        }
                        return data;
                    }())
                }]
            });

            categoriaAbertosFechados = new Highcharts.Chart({
                chart: {
                    type: 'pie',
                    renderTo: 'categoria-abertos-fechados',
                    animation: Highcharts.svg // don't animate in old IE
                },
                credits: {
                    enabled: false
                },
                title: {
                    text: 'categoria x aberto/fechados'
                },
                subtitle: {
                    text: 'Source: Risk Manager'
                },
                yAxis: {
                    title: {
                        text: 'Total percent market share'
                    }
                },
                plotOptions: {
                    pie: {
                        shadow: false,
                        center: ['50%', '50%']
                    }
                },
                tooltip: {
                    valueSuffix: ' Alertas'
                },
                series: [{
                    name: 'Categorias',
                    data: [],
                    size: '60%',
                    dataLabels: {
                        formatter: function () {
                            return this.y > 0 ? this.point.name : null;
                        },
                        color: '#ffffff',
                        distance: -30
                    }
                }, {
                    name: 'Status',
                    data: [],
                    size: '80%',
                    innerSize: '60%',
                    dataLabels: {
                        formatter: function () {
                            // display only if larger than 1
                            return this.y > 0 ? '<b>' + this.point.name + ':</b> ' + this.y : null;
                        }
                    }
                }]
            });

            if($("#pie-caminho-critico").length){
                pieCaminhoCritico = new Highcharts.Chart({
                    chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false,
                        type: 'pie',
                        renderTo: 'pie-caminho-critico',
                        animation: Highcharts.svg // don't animate in old IE
                    },
                    credits: {
                        enabled: false
                    },
                    title: {
                        text: 'Total de Registros Por Caminho Crítico'
                    },
                    tooltip: {
                        pointFormat: '{series.name}: <b>{point.y}</b>'
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.name}</b>: {point.y}',
                                style: {
                                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                                }
                            }
                        }
                    },
                    series: [{
                        name: 'Registros',
                        colorByPoint: true,
                        data: []
                    }]
                });

                pieSeveridade = new Highcharts.Chart({
                    chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false,
                        type: 'pie',
                        renderTo: 'pie-severidade',
                        animation: Highcharts.svg // don't animate in old IE
                    },
                    colors: ['red', 'orange', 'gold', 'lightgreen', 'green'],
                    credits: {
                        enabled: false
                    },
                    title: {
                        text: 'Total de Registros Por Severidade'
                    },
                    tooltip: {
                        pointFormat: '{series.name}: <b>{point.y}</b>'
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.name}</b>: {point.y}',
                                style: {
                                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                                }
                            }
                        }
                    },
                    series: [{
                        name: 'Registros',
                        colorByPoint: true,
                        data: []
                    }]
                });
            }




        });

        function getDashboardData(filter){
            $.ajax({
                url: '/dashboard/get-dashboard-json',
                type:'POST',
                data: filter,
                success: function(ret) {

                    jsonRet = JSON.parse(ret);

                    if(jsonRet.error){
                        addMessage('danger',jsonRet.message);

                    }else{

                        if(!$.isEmptyObject(filter)){
                            $('#filtro-date-frame p').remove();
                            $('#filtro-date-frame').append('<p>Arquivo gerado em: '+jsonRet.update+'</p>');
                        }

                        jsonRet = JSON.parse(jsonRet.dados);

                        if(jsonRet.dashboard.dados.eventos.length == 0){
                            addMessage('info','Não há massa de dados para as datas selecionadas.')
                        }

                        // gráfico por situação Tela A e B
                        qtdStatus.series[0].setData(jsonRet.dashboard.dados.status_tratamento, false);
                        qtdStatus.redraw();
                        //qtdStatusB.series[0].setData(jsonRet.dashboard.dados.status_tratamento, false);
                        //qtdStatusB.redraw();

                        //Gráfico Categoria > Subcategoria > UF
                        categoriaSubcategoriaUf.series[0].setData(jsonRet.dashboard.dados.categoria_subcategoria_uf.serie.data);
                        if(jsonRet.dashboard.dados.categoria_subcategoria_uf.drilldown){
                            categoriaSubcategoriaUf.options.drilldown.series = jsonRet.dashboard.dados.categoria_subcategoria_uf.drilldown.series;
                        }
                        categoriaSubcategoriaUf.redraw();

                        //Gráfico UF > Categoria > Subcategoria
                        ufCategoriaSubcategoria.series[0].setData(jsonRet.dashboard.dados.uf_categoria_subcategoria.serie.data);
                        if(jsonRet.dashboard.dados.uf_categoria_subcategoria.drilldown){
                            ufCategoriaSubcategoria.options.drilldown.series = jsonRet.dashboard.dados.uf_categoria_subcategoria.drilldown.series;
                        }
                        ufCategoriaSubcategoria.redraw();



                        diaAplicacao.redraw();

                        diaAplicacao.series[0].setData(jsonRet.dashboard.dados.dia_aplicacao);
                        diaAplicacao.redraw();

                        //Gráfico Avaliação Pedagógica > UF Categoria
                        avaliacaoPedagogicaUfCategoria.series[0].setData(jsonRet.dashboard.dados.avaliacao_pedagogica_uf_categoria.serie.data);
                        if(jsonRet.dashboard.dados.avaliacao_pedagogica_uf_categoria.drilldown){
                            avaliacaoPedagogicaUfCategoria.options.drilldown.series = jsonRet.dashboard.dados.avaliacao_pedagogica_uf_categoria.drilldown.series;
                        }
                        avaliacaoPedagogicaUfCategoria.redraw();

                        //Gráfico Top 10 Categoria
                        topDezCategoriaSubcategoria.series[0].setData(jsonRet.dashboard.dados.top_dez_categoria_subcategoria);
                        topDezCategoriaSubcategoria.redraw();


                        //Gráfico UF por Nível
                        ufNivel.xAxis[0].setCategories(jsonRet.dashboard.dados.uf_nivel_categoria);
                        while (ufNivel.series.length > 0) {
                            ufNivel.series[0].remove(true);
                        }
                        ufNivel.colorCounter = 0;
                        $.each(jsonRet.dashboard.dados.uf_nivel, function (item) {
                            ufNivel.addSeries(jsonRet.dashboard.dados.uf_nivel[item]);
                        });

                        //Gráfico Categoria > Subcategoria > UF
                        totalOcorrencias.series[0].setData(jsonRet.dashboard.dados.total_ocorrencia);
                        totalOcorrencias.redraw();


                        //Gráfico Categorias x Abertos x Fechados
                        var categories = jsonRet.dashboard.dados.categoria_abertos_fechados.category,
                            data = jsonRet.dashboard.dados.categoria_abertos_fechados.data,
                            dataA = [],
                            dataB = [],
                            i,
                            j,
                            dataLen = data.length,
                            drillDataLen,
                            brightness;

                        // Build the data arrays
                        for (i = 0; i < dataLen; i += 1) {

                            // add browser data
                            dataA.push({
                                name: categories[i],
                                y: data[i].y,
                                color: data[i].color
                            });

                            // add version data
                            drillDataLen = data[i].drilldown.data.length;
                            for (j = 0; j < drillDataLen; j += 1) {
                                brightness = 0.2 - (j / drillDataLen) / 5;
                                dataB.push({
                                    name: data[i].drilldown.categories[j],
                                    y: data[i].drilldown.data[j],
                                    color: Highcharts.Color(data[i].color).brighten(brightness).get()
                                });
                            }
                        }

                        categoriaAbertosFechados.series[0].setData(dataA);
                        categoriaAbertosFechados.series[1].setData(dataB);
                        categoriaAbertosFechados.redraw();

                        if($("#pie-caminho-critico").length){
                            pieCaminhoCritico.series[0].setData(jsonRet.dashboard.dados.pie_qtd_por_caminho_critico, false);
                            pieCaminhoCritico.redraw();

                            pieSeveridade.series[0].setData(jsonRet.dashboard.dados.pie_qtd_por_severidade, false);
                            pieSeveridade.redraw();
                        }
                                                
                        //Gráfico Top 10 Usuários
                        //topDezUsuarios.series[0].setData(jsonRet.dashboard.dados.top_dez_usuarios);
                        //topDezUsuarios.redraw();


                        //GRÁFICO DE ABSTENÇÃO X PRESENTES
                        /*abstencao.xAxis[0].setCategories(jsonRet.dashboard.dados.abstencao_categoria);
                         while(abstencao.series.length > 0){
                         abstencao.series[0].remove(true);
                         }
                         abstencao.colorCounter = 0;
                         $.each(jsonRet.dashboard.dados.abstencao,function(item){
                         abstencao.addSeries(jsonRet.dashboard.dados.abstencao[item]);
                         });*/

                        //Gráfico top usuários
                        //buildTopUsuarios();

                        //Indicador Triagem
                        //$('#qtd-triagem').text(jsonRet['triagem']);

                        loadEventsIntoTable(jsonRet.dashboard.dados.eventos, false);

                        loadUsuariosOnlineIntoTable(jsonRet.dashboard.dados.users_online_list);

                        // call it again after a period of time
                        if (INTERVALOTOGGLE) {
                            intervalo = setInterval(function () {
                                getDashboardData(datas);
                            }, DASHBOARD_INTERVAL);
                        }

                    }

                    //FIX CHART THAT PLOTS SMALLER THEN IT SHOULD BE
                    if (CAROUSEL) {
                        setTimeout(initiateCarousel, 300);
                        CAROUSEL = false;
                    }
                    ajustarTela();

                    if(filter){
                        $('.modal').modal('hide');
                    }

                    loading(false);

                },
                cache: false
            });
        }

        function buildTopUsuarios(){
            topUsuarios = new Highcharts.Chart({
                chart: {
                    type: 'column',
                    renderTo: 'top-usuarios',
                    animation: Highcharts.svg, // don't animate in old IE
                    spacingLeft: 0,
                    spacingRight: 0
                },
                credits: {
                    enabled: false
                },
                title: {
                    text: 'Top 10 Usuários'
                },
                subtitle: {
                    text: 'Origem: Risk Manager'
                },
                xAxis: {
                    categories: jsonRet['top-usuarios-categories']
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Qtd Alertas'
                    }
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.y}</b>'

                },
                plotOptions: {
                    column: {
                        pointPadding: 0.2,
                        borderWidth: 0
                    }
                },
                series: [{
                    name: 'Registros',
                    data: jsonRet['top-usuarios'],
                    dataLabels: {
                        enabled: true,
                        color: '#FFFFFF',
                        align: 'center',
                        format: '{point.y}', // one decimal
                        y: 10, // 10 pixels down from the top
                        style: {
                            fontSize: '13px',
                            fontFamily: 'Verdana, sans-serif'
                        }
                    }
                }]
            });
        }

        function initiateCarousel(){
            $('.carousel-inner > .item.active').removeClass('active');
            $('.carousel-inner > .item:eq(0)').addClass('active');
        }

    }

    if($('div[data-menu="mapa-alerta"]').length){
        ajustarTela();
        $('body').css('overflow','hidden');
    }

});



function reloadIframe(elem){
    $(elem).attr( 'src', function ( i, val ) { return val; });
}
function loadHeaderClock() {
    moment.locale('pt-br');
    $('.horario').empty().append(moment().tz("America/Sao_Paulo").format('H:mm:ss'));
}

function loadEventsIntoTable(events,filtro) {

    $('#lista-alertas .tbody-alertas tr').remove();

    $('#lista-alertas-b .tbody-alertas tr').remove();

    var status = 'Fechado';

    if(datas){
        status = '';
    }

    $.each(events,function(i){

        $('#lista-alertas-b .tbody-alertas').append(
            '<tr data-code="'+events[i].EventID+'" data-filter="'+events[i].sistema+' - '+events[i].unidade_federativa+' - '+events[i].categoria_evento+'">' +
                '<td>'+events[i].sistema+'</td>' +
                '<td>'+events[i].Title+'</td>' +
                '<td>'+events[i].status_tratamento+'</td>' +
                '<td>'+events[i].data_criacao+'</td>' +
                '<td class="'+nivelAlerta(events[i].nivel_do_alerta_sgir)+'">'+events[i].nivel_do_alerta_sgir+'</td>' +
            '</tr>');

        if(events[i].Status == status) return true;

        if(filtro && events[i].filtro['field'] != events[i].filtro['value']){
            return true;
        }

        $('#lista-alertas .tbody-alertas').append(
            '<tr data-code="'+events[i].EventID+'" data-filter="'+events[i].categoria_evento+' - '+events[i].unidade_federativa+'">' +
                '<td>'+events[i].sistema+'</td>' +
                '<td>'+events[i].Title+'</td>' +
                '<td>'+events[i].status_tratamento+'</td>' +
                '<td>'+events[i].data_criacao+'</td>' +
                '<td class="'+nivelAlerta(events[i].nivel_do_alerta_sgir)+'">'+events[i].nivel_do_alerta_sgir+'</td>' +
            '</tr>');
    });

    if($('#lista-alertas .tbody-alertas tr').length == 0){
        $('#lista-alertas .tbody-alertas').append(
            '<tr colspan="3">' +
                '<td>Sem registros</td>' +
            '</tr>');
    }

    if($('#lista-alertas-b .tbody-alertas tr').length == 0){
        $('#lista-alertas-b .tbody-alertas').append(
            '<tr colspan="3">' +
            '<td>Sem registros</td>' +
            '</tr>');
    }

    rodarTabela();

}

function nivelAlerta(nivel){

    var corNivel = '';

    switch(nivel) {
        case '4 - Alto':
            corNivel = 'alto';
            break;
        case '5 - Severo':
            corNivel = 'severo';
            break;
    }

    return corNivel;
}

function showRegistrosRelacionados(filtro,tabela,drill) {

    $(tabela+' .tbody-alertas tr').css("display","none");

    var found = false;
    if(drill == 'down'){
        $(tabela+" .tbody-alertas tr[data-filter*='"+filtro+"']").each(function(){
            $(this).css("display","table-row");
            found = true;
        });
    }else{
        found = false;
    }

    if(!found) $(tabela+' .tbody-alertas tr').css("display","table-row");

    rodarTabela();
}

function loadUsuariosOnlineIntoTable(users)
{

    $('#usuarios-online .tbody-usuarios tr').remove();

    $.each(users,function(i){

        $('#usuarios-online .tbody-usuarios').append(
            '<tr data-code="'+users[i].EventID+'">' +
            '<td>'+users[i].USUARIO+'</td>' +
            '<td>'+users[i].IP+'</td>' +
            '<td>'+users[i].NAVEGADOR+'</td>' +
            '</tr>');
    });

    if($('#usuarios-online .tbody-usuarios tr').length == 0){
        $('#usuarios-online .tbody-usuarios').append(
            '<tr colspan="3">' +
            '<td>Sem registros</td>' +
            '</tr>');
    }

    rodarTabela();

}

var rodaTabela = [];
function rodarTabela(){
    $('.rodar-tabela').each(function(i){
        if($(this).find('.js-marquee-wrapper').length){
            rodaTabela[i].marquee('destroy');
        }
    });

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
                duplicated: true,
                startVisible : true
            });
        }
    });
}