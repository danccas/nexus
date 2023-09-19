$(document).ready(function () {
    $(document).on('click', 'a[data-popup]', function (e) {
        var box = $(this);
        if (typeof box.attr('data-confirm') == 'undefined') {
            e.preventDefault();
            box.data('popup', (new Popup({
                url: box.attr('href'),
            }).init(true)));
            return false;
        }
    });
});
