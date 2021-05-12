;(function($) {
    $('.ari-grid').each(function() {
        var $grid = $(this);

        $grid.on('click', '.sortable', function() {
            var $el = $(this),
                sortColumn = $el.attr('data-sort-column'),
                sortDir = $el.attr('data-sort-dir');

            if (!sortDir)
                sortDir = 'ASC';
            else
                sortDir = sortDir == 'ASC' ? 'DESC' : 'ASC';

            $grid.trigger('grid.sort', [sortColumn, sortDir]);

            return false;
        });

        setTimeout(function() {
            $grid.find('TBODY').off('click');
        }, 50);
        $grid.on('click', '.toggle-row', function(e) {
            var tr = $(this).closest('TR');

            if (tr.hasClass('is-expanded')) {
                tr.removeClass('is-expanded');
            } else {
                tr.addClass('is-expanded');
            }

            e.stopImmediatePropagation();
            return false;
        });

        $('.select-all-items', $grid).on('change', function() {
            $grid.find('.select-item').prop('checked', $(this).is(':checked'));
        });
    });
})(jQuery);