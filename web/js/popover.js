$('.pop-toggle').click(function () {
    var $pop = $(this).children('div');
    $('[data-toggle="popover"]').not($pop).popover('hide');
    $pop.popover('toggle');
});
