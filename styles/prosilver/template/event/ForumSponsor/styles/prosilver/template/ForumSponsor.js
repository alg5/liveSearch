(function ($) {
    $.fn.hasAttr = function (name) {
        return this.attr(name) !== undefined;
    };

    $('.sponsor').each(function () {
        //var forumtitle = $(this).next().find('a.forumtitle');
        var forumtitle = $(this).next().find('div.list-inner');
        console.log(forumtitle);
       // var div = '<div style="float:' + S_CONTENT_FLOW_END + '; margin-' + S_CONTENT_FLOW_END + ': 5px;">' + L_FORUM_SPONSOR + ':<br />' + $(this).attr('sponsor') + '</div>';
        var div = '<div class="forumsponsor" style="float:' + S_CONTENT_FLOW_END + '; margin-' + S_CONTENT_FLOW_END + ': 5px;">' + L_FORUM_SPONSOR + ':<br><p class="sponsoright">' + $(this).attr('sponsor') + '</p></div>';
        //$(div).insertAfter(forumtitle);
        $(forumtitle).html(div + $(forumtitle).html());

        // $(forumtitle).html($(forumtitle).html() + div);
    });

})(jQuery);