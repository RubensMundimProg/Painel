/**
 * Created by bruno.rosa on 28/11/2016.
 */
$(function(){
    if($('#relogios').length){

        $(document).ready(function(){
            iniciarRelogios();
        });

//    RELOGIOS
        function relogios(){
//        $('#data-hoje').empty().append(moment().tz("America/Sao_Paulo").format('dddd[,] D [de] MMMM [de] YYYY'));

            //A CONFIGURAÇÃO REAL É ASSIM, MAS DEVIDO A PAUSA PARA ELEIÇÕES ESSAS CONFIGURAÇÕES ENTRARÃO EM VIGOR 4/11 0H
            $('#rio-branco').empty().append(moment().tz("America/Rio_Branco").format('HH:mm'));
            $('#manaus').empty().append(moment().tz("America/Manaus").format('HH:mm'));
            $('#para').empty().append(moment().tz("America/Belem").format('HH:mm'));
            $('#mato-grosso').empty().append(moment().tz("America/Manaus").format('HH:mm'));
            $('#brasilia').empty().append(moment().tz("America/Fortaleza").format('HH:mm'));
            $('#nordeste').empty().append(moment().tz('America/Fortaleza').format('HH:mm'));
            $('#fernando').empty().append(moment().tz('America/Noronha').format('HH:mm'));

            //HORARIO PROVISÓRIO ATÉ DIA 03/11 23:59
            // $('#rio-branco').empty().append(moment().tz("America/Rio_Branco").format('HH:mm'));
            // $('#manaus').empty().append(moment().tz("America/Manaus").format('HH:mm'));
            // $('#mato-grosso').empty().append(moment().tz("America/Manaus").format('HH:mm'));
            // $('#brasilia').empty().append(moment().tz("America/For   taleza").format('HH:mm'));
            // $('#nordeste').empty().append(moment().tz('America/Fortaleza').format('HH:mm'));
            // $('#fernando').empty().append(moment().tz('America/Fortaleza').format('HH:mm'));

            // $('#fernando').empty().append(moment().zone('-02:00').format('HH:mm'));
            //$('#fernando-noronha').empty().append(moment().tz("America/Noronha|Brazil/DeNoronha").format('HH:mm'));
        }

        function iniciarRelogios(){
            moment.locale('pt-br');
            relogios();
            setInterval(function(){
                relogios();
            },60000)
        }

        function sformat(s) {
            var fm = [
//            Math.floor(s / 60 / 60 / 24), // DAYS
//            Math.floor(s / 60 / 60) % 24, // HOURS
                Math.floor(s / 60) % 60, // MINUTES
                s % 60 // SECONDS
            ];
            return $.map(fm, function(v, i) { return ((v < 10) ? '0' : '') + v; }).join(':');
        }
    }
});