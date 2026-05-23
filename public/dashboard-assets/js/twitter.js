(function ($) {
    'use strict';

    var WEATHER_CONTAINER = '#weather-alerts';
    var WEATHER_STATUS = '#weather-status-message';
    var WEATHER_UPDATED = '#weather-updated-at';
    var WEATHER_ERROR = '#weather-error';
    var WEATHER_LOADING = '#weather-loading';
    var FEED_ENDPOINT = '/rss/inmet-avisos';
    var REFRESH_INTERVAL = 5 * 60 * 1000; // 5 minutos

    $(document).ready(function () {
        if (!$(WEATHER_CONTAINER).length) {
            return;
        }

        fetchAlerts();
        setInterval(fetchAlerts, REFRESH_INTERVAL);
    });

    function fetchAlerts() {
        toggleLoading(true);
        clearError();
        $(WEATHER_STATUS).text('Carregando avisos meteorológicos...');

        $.ajax({
            url: FEED_ENDPOINT,
            method: 'GET',
            dataType: 'json'
        }).done(function (response) {
            if (!response || response.error) {
                var message = response && response.message ? response.message : 'Não foi possível carregar os avisos meteorológicos do INMET.';
                renderError(message);
                renderEmpty();
                return;
            }

            renderStatus(response.alerts);
            renderAlerts(response.alerts);
            updateTimestamp(response.fetched_at, response.source, response.message);
        }).fail(function () {
            renderError('Falha na comunicação com o serviço do INMET. Tente novamente em instantes.');
            renderEmpty();
        }).always(function () {
            toggleLoading(false);
        });
    }

    function renderStatus(alerts) {
        var $status = $(WEATHER_STATUS);
        if (!alerts || !alerts.length) {
            $status.text('Nenhum aviso meteorológico ativo no momento.');
            return;
        }

        $status.text(alerts.length + ' aviso(s) meteorológico(s) ativo(s).');
    }

    function renderAlerts(alerts) {
        var $container = $(WEATHER_CONTAINER);
        
        // 1. Para a animação atual e limpa os cards
        $container.css('animation', 'none').empty();

        if (!alerts || !alerts.length) {
            renderEmpty();
            return;
        }

        // 2. Adiciona a 1ª CÓPIA dos cards
        $.each(alerts, function (_, alert) {
            $container.append(buildAlertCard(alert));
        });

        // 3. Adiciona a 2ª CÓPIA (para o loop infinito funcionar)
        $.each(alerts, function (_, alert) {
            $container.append(buildAlertCard(alert));
        });
        
        // 4. Pega a duração da animação do CSS (ex: "300s")
        var animationDuration = $container.css('animation-duration');
        if (!animationDuration || animationDuration === "0s") {
             // Fallback caso não consiga ler o CSS
             animationDuration = "600s"; 
        }

        // 5. Força o navegador a "ver" o container vazio (truque de reflow)
        void $container[0].offsetWidth; 

        // 6. Reinicia a animação com a duração correta
        $container.css('animation', 'scrollHorizontal ' + animationDuration + ' linear infinite');
    }

function buildAlertCard(alert) {
    // Adicionado 'data-severity' para o CSS aplicar as cores
    var $card = $('<article/>', { 
        'class': 'weather-alert',
        'data-severity': alert.severity 
    });

    var $header = $('<div/>', { 'class': 'weather-alert__header' });
    var $title = $('<h3/>', { 'class': 'weather-alert__title', text: alert.title || 'Aviso meteorológico' });

    $header.append($title);

    // Esta parte que usa severityClass NÃO é mais usada para cor de fundo,
    // mas é bom manter caso o 'alert.severity' venha nulo.
    if (alert.severity) {
        $header.append(
            $('<span/>', {
                'class': 'weather-alert__severity ' + severityClass(alert.severity),
                text: alert.severity
            })
        );
    }

    $card.append($header);

    var $body = $('<div/>', { 'class': 'weather-alert__body' });

    if (alert.description) {
        $body.append($('<p/>', { text: alert.description }));
    }

    if (alert.area) {
        $body.append($('<p/>').append($('<strong/>', { text: 'Áreas afetadas: ' })).append(document.createTextNode(alert.area)));
    }

    var $metaList = $('<ul/>', { 'class': 'weather-alert__meta' });
    if (alert.status) {
        $metaList.append(buildMetaItem('Status', alert.status));
    }
    if (alert.event) {
        $metaList.append(buildMetaItem('Evento', alert.event));
    }
    if (alert.start) {
        $metaList.append(buildMetaItem('Início', alert.start));
    }
    if (alert.end) {
        $metaList.append(buildMetaItem('Fim', alert.end));
    }
    if (alert.published_at) {
        $metaList.append(buildMetaItem('Publicação', alert.published_at));
    }

    if ($metaList.children().length) {
        $body.append($metaList);
    }

    $card.append($body);

    if (alert.graphic || alert.link) {
        var href = alert.graphic || alert.link;
        $card.append(
            $('<p/>', { 'class': 'weather-alert__link' }).append(
                $('<a/>', {
                    href: href,
                    target: '_blank',
                    rel: 'noopener noreferrer',
                    text: 'Ver detalhes do aviso'
                })
            )
        );
    }

    return $card;
}

    function buildMetaItem(label, value) {
        return $('<li/>').append(
            $('<strong/>', { text: label + ': ' }),
            document.createTextNode(value)
        );
    }

    function severityClass(severity) {
        var normalized = (severity || '').toLowerCase();
        var normalizedAscii = removeDiacritics(normalized);

        if (normalized.indexOf('grande perigo') !== -1 || normalized.indexOf('emerg') !== -1 || normalizedAscii.indexOf('grande perigo') !== -1) {
            return 'weather-alert__severity--danger';
        }

        if (normalized.indexOf('perigo') !== -1 || normalizedAscii.indexOf('perigo') !== -1) {
            return 'weather-alert__severity--danger';
        }

        if (normalized.indexOf('potencial') !== -1 || normalized.indexOf('atenção') !== -1 || normalizedAscii.indexOf('potencial') !== -1 || normalizedAscii.indexOf('atencao') !== -1) {
            return 'weather-alert__severity--warning';
        }

        return 'weather-alert__severity--info';
    }

    function removeDiacritics(text) {
        if (!text || typeof text.normalize !== 'function') {
            return text;
        }

        return text.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    }

    function updateTimestamp(isoDate, source, message) {
        if (!isoDate) {
            $(WEATHER_UPDATED).text('');
            return;
        }

        var date = new Date(isoDate);
        if (isNaN(date.getTime())) {
            var fallbackText = 'Atualização: ' + isoDate;
            var suffixFallback = buildUpdateSuffix(source, message);
            if (suffixFallback) {
                fallbackText += suffixFallback;
            }
            $(WEATHER_UPDATED).text(fallbackText);
            return;
        }

        var formatted = date.toLocaleString('pt-BR', {
            hour: '2-digit',
            minute: '2-digit',
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });

        var text = 'Atualização: ' + formatted;
        var suffix = buildUpdateSuffix(source, message);
        if (suffix) {
            text += suffix;
        }

        $(WEATHER_UPDATED).text(text);
    }

    function buildUpdateSuffix(source, message) {
        var parts = [];

        if (source === 'cache') {
            parts.push('fonte: última sincronização local');
        }

        if (message) {
            parts.push(message);
        }

        if (!parts.length) {
            return '';
        }

        return ' (' + parts.join(' • ') + ')';
    }

    function toggleLoading(isLoading) {
        if (isLoading) {
            $(WEATHER_LOADING).removeClass('hidden');
        } else {
            $(WEATHER_LOADING).addClass('hidden');
        }
    }

    function renderError(message) {
        $(WEATHER_ERROR).text(message).removeClass('hidden');
        $(WEATHER_STATUS).text('Não foi possível atualizar os avisos.');
    }

    function clearError() {
        $(WEATHER_ERROR).addClass('hidden').text('');
    }

    // Mantém compatibilidade com scripts existentes que aguardam a função
    window.loadTags = function () {
        return null;
    };

})(jQuery);