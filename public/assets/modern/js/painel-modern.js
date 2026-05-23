(function ($, bootstrap) {
    'use strict';

    if ($ && bootstrap && !$.fn.modal) {
        $.fn.modal = function (action) {
            return this.each(function () {
                var modal = bootstrap.Modal.getOrCreateInstance(this);

                if (action === 'hide') {
                    modal.hide();
                    return;
                }

                if (action === 'toggle') {
                    modal.toggle();
                    return;
                }

                modal.show();
            });
        };
    }

    $(document).on('click', '[data-dismiss="modal"]', function () {
        var modalElement = $(this).closest('.modal')[0];

        if (modalElement && bootstrap) {
            bootstrap.Modal.getOrCreateInstance(modalElement).hide();
        }
    });

    $(document).on('click', '[data-toggle="modal"][data-target]', function (event) {
        var target = $(this).attr('data-target');
        var modalElement = target ? document.querySelector(target) : null;

        if (modalElement && bootstrap) {
            event.preventDefault();
            bootstrap.Modal.getOrCreateInstance(modalElement).show();
        }
    });

    $(document).on('click', '[data-dismiss="alert"]', function () {
        var $container = $(this).closest('.container-alertas');
        $(this).closest('.alert').remove();

        if ($container.length && !$container.find('.alert').length) {
            $container.remove();
        }
    });

    $(document).on('click', '.modern-menu-toggle', function (event) {
        var button = this;
        if (window.matchMedia('(min-width: 1181px)').matches) {
            event.preventDefault();
            event.stopPropagation();
            $('body').toggleClass('modern-sidebar-collapsed');
            $(button).attr('aria-expanded', $('body').hasClass('modern-sidebar-collapsed') ? 'false' : 'true');

            try {
                window.localStorage.setItem('modern-sidebar-collapsed', $('body').hasClass('modern-sidebar-collapsed') ? '1' : '0');
            } catch (e) {}

            return;
        }

        var nav = document.getElementById('modernMobileNav');
        if (nav && bootstrap && bootstrap.Collapse) {
            var collapse = bootstrap.Collapse.getOrCreateInstance(nav, { toggle: false });
            collapse.toggle();
            $(button).attr('aria-expanded', $(nav).hasClass('show') ? 'false' : 'true');
        }
    });

    $(document).on('input', '[data-modern-table-search]', function () {
        var selector = $(this).attr('data-modern-table-search');
        var value = $.trim($(this).val()).toLowerCase();

        if ($.fn.DataTable && $.fn.DataTable.isDataTable(selector)) {
            $(selector).DataTable().search(value).draw();
            return;
        }

        var $rows = $(selector).find('tbody tr');

        $rows.each(function () {
            var text = $(this).text().toLowerCase();
            $(this).toggle(!value || text.indexOf(value) !== -1);
        });
    });

    $(function () {
        try {
            if (window.localStorage.getItem('modern-sidebar-collapsed') === '1') {
                $('body').addClass('modern-sidebar-collapsed');
            }
        } catch (e) {}

        if (!bootstrap || !bootstrap.Tooltip) {
            return;
        }

        $('[data-toggle="tooltip"], [data-bs-toggle="tooltip"]').each(function () {
            bootstrap.Tooltip.getOrCreateInstance(this);
        });
    });
})(window.jQuery, window.bootstrap);
