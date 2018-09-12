(function ($) {
    $.fn.popmessage = function (cmd, cls, text) {
        $(this).each(process);

        function process() {
            if (cmd == 'show') {
                if (cls == 'error') {
                    $(this).parent().addClass("error");
                } else {
                    $(this).parent().removeClass("error");
                }

                var msg = text;
                if (!msg && cls)
                    msg = getMessageEl(this).data(cls + '-text');

                if (!msg) {
                    getMessageEl(this).hide();
                }
                else {
                    getMessageEl(this).removeClass('error warning').addClass(cls).fadeIn().html(msg);
                    getMessageEl(this).css('top', $(this).position().top);
                }

            } else if (cmd == 'hide') {
                $(this).parent().removeClass("error");
                getMessageEl(this).hide();
            }

        }

        function getErrorIcon(context) {
            return $(context).siblings('.error-icon').add($(context).parent().siblings('.error-icon'));
        }

        function getMessageEl(context) {
            return $(context).data('message-selector')
                ? $($(context).data('message-selector'))
                : $(context).siblings('.message').add($(context).parent().siblings('.message'));
        }
    }
})(jQuery)