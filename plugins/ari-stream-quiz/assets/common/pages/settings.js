;jQuery(document).on('app_ready', function(e, app) {
    var $ = jQuery,
        linkAWeber = $('#linkAWeberAuthCode');

    linkAWeber.on('click', function() {
        window.open($(this).attr('href'), '', 'width=600,height=400,top=50,left=250,resizable=yes,menubar=no,status=no,toolbar=no,scrollbars=no');

        return false;
    });

    $('.tooltipped', app.el).tooltip();
});