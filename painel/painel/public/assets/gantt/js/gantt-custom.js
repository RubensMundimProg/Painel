// jQuery Gantt Chart
// http://taitems.github.io/jQuery.Gantt/
// ==================

// Basic usage:

if($('#gantt').length){
    console.log('#gantt');
    $(".gantt").gantt({
        months:["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"],
        dow:["D", "S", "T", "Q", "Q", "S", "S"],
        waitText:'Por favor espere...',
        source: '/gantt/data',
        navigate: "scroll",
        scale: "days",
        minScale: "days",
        maxScale: "months",
        itemsPerPage: 15,
        onItemClick: function(data) {
            console.log(data);

            $('.nome-evento').html(data.title);
            $('.detalhes-evento span').html(data.description);
            $('.inicio-evento span').html((data.start)?moment(data.start,'YYYY-MM-DD').format('DD/MM/YYYY'):'');
            $('.fim-evento span').html((data.end)?moment(data.end,'YYYY-MM-DD').format('DD/MM/YYYY'):'');
            $('input[name=code]').val(data.code);
            var jsonProgress = JSON.parse(data.progress);
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

            $('#detalhe-evento').modal('show');
        },
        onAddClick: function(dt, rowId) {
            console.log("Empty space clicked - add an item!");
        },
        onRender: function() {
            if (window.console && typeof console.log === "function") {
                console.log("chart rendered");
            }

            $.ajax({
                url:'/calendario/get-calendar',
                type:'GET',
                dataType:'json',
                success:function(res){
                    $("ul.legenda li").remove();
                    $.each(res.legenda, function(i,e){
                        $("ul.legenda").append('<li class="'+e+'">'+i+'</li>');
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
    });
}