$(function(){
    $('.editar-data').click(function(){
        $('.visualizar-data').addClass('hide');
        $('.editar-data').removeClass('hide');
    })
    $('.salvar-data').click(function(){
        var start = $('input[data-field="StartDate"]').val();
        var code = $('input[data-field="StartDate"]').attr('data-code');
        var end  = $('input[data-field="EndDate"]').val();
        var ExpectedStart  = $('input[data-field="ExpectedStart"]').val();
        var ExpectedEnd = $('input[data-field="ExpectedEnd"]').val();
        loading(true);
        $.ajax({
            url:'/calendario/update-data/'+code,
            type:'POST',
            dataType:'json',
            data:{'StartDate':start,'EndDate':end,'ExpectedStartDate':ExpectedStart,'ExpectedEndDate':ExpectedEnd},
            success:function(res){
                if(res.error){
                    showMsg(res.message,false,true);
                    loading(false);
                }else{
                    showMsg('Data Atualizada',false,false);
                    renderCalendar(false);

                    $('#detalhe-evento').modal('hide');
                    loading(false);
                }
            }
        })

    })
    //USES IN CALENDAR AND GANTT CHART
    $('form#form_new_progress').on('submit',function(e){
        e.preventDefault();
        var data = $(this).serialize();
        $.ajax({
            url:'/calendario/save-event-progress',
            type:'POST',
            data: data,
            dataType:'json',
            success:function(res){
                if(res.error){
                    showMsg(res.message,false,true);
                }else{
                    showMsg(res.message,false,false);
                    $('#detalhe-evento').modal('hide');
                    $('form#form_new_progress')[0].reset();
                }
            }
        });
    });

    if($('#calendar').length){
        /*generateCalendar([{
            title: 'All Day Event',
            start: '2017-03-01',
            color:'red'
        },
            {
                title: 'Long Event',
                start: '2017-03-07',
                end: '2017-03-10'
            }
        ]);*/
        renderCalendar(false);

        setTimeout(function(){
            renderCalendar('Enem');
           console.log('Render');
        },3000);
    }
});

$('body').on('click','.filtro-calendar', function(){
    renderCalendar($(this).attr('data-filtro'));
})

function renderCalendar(tipo){
    var url = '/calendario/get-calendar';
    if(tipo){
        url+='/'+tipo;
    }

    $.ajax({
        url:url,
        type:'GET',
        dataType:'json',
        success:function(res){
            generateCalendar(res.eventos);
            $("ul.legenda").html('');
            $("ul.legenda").append('<li class="filtro-calendar" data-filtro="" style="color: #000">Todos</li>');
            $.each(res.legenda, function(i,e){
                $("ul.legenda").append('<li class="'+e+' filtro-calendar" data-filtro="'+i+'">'+i+'</li>');
            });
            $("ul.legenda").append('<li class="text-black" style="border-left-width:0;padding-left: 0"><img src="/assets/img/ico/down-right-down-arrow.png" style="width: 12px;height: auto" alt="">&nbsp;Evento Filho</li>');
            $("ul.legenda").append('<li class="text-black" style="border-left-width:0;padding-left: 0"><img src="/assets/img/ico/severo.png" style="width: 12px;height: auto" alt="">&nbsp;Atrasado</li>');
            $("ul.legenda").append('<li class="text-black" style="border-left-width:0;padding-left: 0"><img src="/assets/img/ico/alto.png" style="width: 12px;height: auto" alt="">&nbsp;Próximo Término</li>');
            $("ul.legenda").append('<li class="text-black" style="border-left-width:0;padding-left: 0"><img src="/assets/img/ico/baixo.png" style="width: 12px;height: auto" alt="">&nbsp;No Prazo</li>');
            $("ul.legenda").append('<li class="text-black" style="border-left-width:0;padding-left: 0"><img src="/assets/img/ico/fechado.png" style="width: 12px;height: auto" alt="">&nbsp;Fechado</li>');
            $("ul.legenda").append('<li class="text-black" style="border-left-width:0;padding-left: 0"><img src="/assets/img/ico/fechado-severo.png" style="width: 12px;height: auto" alt="">&nbsp;Fechado c/ Atraso</li>');
            //$("ul.legenda").append('<li style="border-left-width:0;padding-left: 0"><img src="/assets/img/ico/atencao.png" style="width: 12px;height: auto" alt="">&nbsp;Atenção</li>');
            //$("ul.legenda").append('<li style="border-left-width:0;padding-left: 0"><img src="/assets/img/ico/baixo.png" style="width: 12px;height: auto" alt="">&nbsp;Baixo</li>');
        }
    });
}

function generateCalendar(events){
    $(".box-calendar").html('<div id="calendar"></div>');
    $('#calendar').fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay,listMonth'
        },
        locale: 'pt-br',
        buttonIcons: false, // show the prev/next text
        navLinks: true, // can click day/week names to navigate views
        eventLimit: true, // for all non-agenda views
        views: {
            agenda: {
                eventLimit: 100 // adjust to 6 only for agendaWeek/agendaDay
            }
        },
        defaultView: 'agendaWeek',
        events: events,
        eventClick: function(calEvent, jsEvent, view) {
            //var expectedStart = new Date(calEvent.expectedStart+'T00:00:00Z').toLocaleDateString('pt-BR');
            //var expectedEnd = new Date(calEvent.expectedEnd+'T00:00:00Z').toLocaleDateString('pt-BR');;

            var expectedStart = formatDate(calEvent.expectedStart);
            var expectedEnd = formatDate(calEvent.expectedEnd);

            $('.nome-evento').html(calEvent.title);
            $('.detalhes-evento span').html(calEvent.description);
            $('.inicio-evento span').html((calEvent.start)?calEvent.start.format('DD/MM/YYYY'):'');
            $('.fim-evento span').html((calEvent.end)?calEvent.end.format('DD/MM/YYYY'):'');
            $('.inicio-esperado span').html((expectedStart)?expectedStart:'');
            $('.fim-esperado span').html((expectedEnd)?expectedEnd:'');

            $('input[data-field="StartDate"]').val((calEvent.start)?calEvent.start.format('DD/MM/YYYY'):'');
            $('input[data-field="EndDate"]').val((calEvent.end)?calEvent.end.format('DD/MM/YYYY'):'');
            $('input[data-field="ExpectedStart"]').val((expectedStart)?expectedStart:'');
            $('input[data-field="ExpectedEnd"]').val((expectedEnd)?expectedEnd:'');
            $('input[data-field="StartDate"]').attr('data-code',calEvent.code);

            $('input[name=code]').val(calEvent.code);
            var jsonProgress = JSON.parse(calEvent.progress);
            $('.progress-list li').remove();
            $.each(jsonProgress,function(i){
                var li = '<li>';
                $.each(jsonProgress[i],function(k,v){
                    li = li+'<p><b>'+k+': </b>'+v+'</p>';
                });
                li = li + '</li>';
                $('.progress-list').append($(li));
            });

            if($('ul.progress-list li').length == 0){
                $('.progress-list').append('<li>Sem progresso até o momento.</li>');
            }

            //var base = 'https://gestaoderiscos.inep.gov.br/RM/Workflow/Edit/Properties/'+calEvent.code;
            //$('.btn-evento').attr('href',base);

            $('#detalhe-evento').modal('show');
        },
        viewRender : function(callback){
            //OCULTANDO AS HORAS DO DISPLAY POR SEMANA
            setTimeout(function(){
                $('.fc-scroller.fc-time-grid-container').css('height','0');
            },1);
        }
    });


    function formatDate(date) {
        var ex = date.split('-');
        return ex[2]+'/'+ex[1]+'/'+ex[0];
    }
}