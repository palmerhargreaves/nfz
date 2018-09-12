MessagesTabs = function (config) {
    this.load_url = '';
    this.on_get_messages_list_by_type = '';
    this.limit = 0;

    $.extend(this, config);

    this.tab = '';
    this.scroll_value = 100;
    this.start_from = 0;
    this.requisted = false;
    this.send_data = {};
    this.dirty = false;
}

MessagesTabs.prototype = {
    start: function() {
        this.initEvents();

        return this;
    },

    initEvents: function() {
        var self = this;

        this.getMessageTabsContainer().on('click', '.messages-tab-header', $.proxy(this.onTabClick, this));
        this.getMessageTabsContainer().on('click', '.messages-types', $.proxy(this.onShowMessagesByType, this));

        $(window).scroll(function(e) {
            if ($(window).scrollTop() > self.scroll_value && ('start_from' in self.send_data) && $('.messages-types').parent().hasClass('active')) {
                self.scroll_value = $(window).scrollTop() + 300;
                self.start_from += self.limit;

                self.send_data.start_from = self.start_from;
                self.dirty = true;

                self._requestNewMessages();
            }
        });

    },

    resetData: function() {
        this.start_from = 0;
        this.dirty = false;
        this.scroll_value = 100;
    },

    onShowMessagesByType: function(e) {
        var $from = $(e.target);

        $('.messages-types').parent().removeClass('active');
        $from.parent().addClass('active');

        this.resetData();
        this.send_data = {
            type_parent: $(e.target).data('messages-parent'),
            type: $from.data('type'),
            start_from: this.start_from
        };

        this.getLoadingProgressContainer().fadeIn();
        this._requestNewMessages(true);
    },

    _requestNewMessages: function() {
        if (!self.requisted) {
            this.requisted = true;
            $.post(this.on_get_messages_list_by_type, this.send_data, $.proxy(this.onGetMessagesList, this));
        }
    },

    onGetMessagesList: function(result) {
        var $tab = undefined;

        $.map($('.tab-pane'), function(obj, ind) {
            if ($(obj).hasClass('active')) {
                $tab = $(obj);
            }
        });

        this.getLoadingProgressContainer().fadeOut();
        this.requisted = false;
        if ($tab != undefined && result.length > 0) {
            if ($('.messages-types').parent().hasClass('active') && this.dirty) {
                $tab.find('table > tbody > tr:last').after(result);
            } else {
                $tab.html(result);
            }
        }
    },

    onTabClick: function(e) {
        var el = $(e.target).parent();

        if (this.requisted) {
            return;
        }

        this.getLoadingProgressContainer().fadeIn();
        this.resetData();

        this.requisted = true;
        this.tab = el.data('tab');
        if (!el.hasClass()) {
            $.post(this.load_url,
                {
                    tab: this.tab
                },
                $.proxy(this.onLoadDataSuccess, this));
        }
    },

    onLoadDataSuccess: function(result) {
        $('#' + this.tab).html(result);
        $('.messages-types').parent().removeClass('active');

        this.getLoadingProgressContainer().fadeOut();
        this.requisted = false;

        var href = window.location.href.split('?');
        if (window.history) {
            window.history.replaceState(
                {
                    tab: this.tab
                },
                null,
                href[0]);
        }

        this.getMessagesTypes().data('messages-parent', this.tab);
    },

    getMessageTabsContainer: function() {
        return $('.activity-main-page');
    },

    getMessagesTypes: function() {
        return $('.messages-types');
    },

    getLoadingProgressContainer: function() {
        return $('#loading-progress-container');
    }
}

