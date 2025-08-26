$(function(){
    var translateDatatable = {
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

    if($('.datatable_full').length){
        $('.datatable_full').dataTable({
            "language": translateDatatable
        });
    }

    if($('.datatable_r').length){
        $('.datatable_r').dataTable({
            "columnDefs": [ { "targets": 0, "orderable": true }, { "targets": -1, "orderable": false } ],
            "language": translateDatatable
        });
    }

    if($('.datatable_l').length){
        $('.datatable_l').dataTable({
            "aaSorting": [[1, "asc"]],
            "columnDefs": [ { "targets": 0, "orderable": false } ],
            "language": translateDatatable
        });
    }
})