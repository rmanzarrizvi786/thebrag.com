;(function($) {
    $('.ari-paging').each(function() {
        var $paging = $(this);

        $('.pagination', $paging).on('click', '.grid-page', function() {
            var pageNum = parseInt($(this).attr('data-page'), 10);

            if (pageNum >= 0) {
                $paging.trigger('paging.page_change', [pageNum]);
            }

            return false;
        });
        $('.go-to-page', $paging).on('change', function() {
            var pageNum = parseInt($(this).val(), 10);

            if (pageNum < 0)
                return ;

            $paging.trigger('paging.page_change', [pageNum]);
        });
    });
})(jQuery);