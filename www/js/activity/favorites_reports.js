/**
 * Created by kostet on 02.10.2015.
 */
FavoritesReports = function(config) {
    // configurable {
    // }
    this.export_url = '';

    $.extend(this, config);

}

FavoritesReports.prototype = {
    start: function() {
        this.initEvents();

        return this;
    },

    initEvents: function() {
        var self = this;

        $(document).on('click', '.ch-check-uncheck-fav-report-item', $.proxy(this.onCheckItem, this));
        $(document).on('click', '.ch-favorite-report-items-check-uncheck', $.proxy(this.onCheckUncheckItems, this));

        this.getFavoritesToPdfBt().click($.proxy(this.onExportFavoritesReportsToPdf, this));
    },

    onCheckItem: function() {
        this.showHidePdfButton();
    },

    onCheckUncheckItems: function() {
        $('.ch-check-uncheck-fav-report-item').each(function(ind, el) {
            if($(el).is(':checked')) {
                $(el).prop('checked', false);
            } else {
                $(el).prop('checked', true);
            }
        });

        this.showHidePdfButton();
    },

    onExportFavoritesReportsToPdf: function(e) {
        this.getFavoritesToPdfBt().hide();
        this.getFavoritesReportsItemsLoader().show();

        $.post(this.export_url,
            {
                items : this.getCheckedItems().join(':')
            },
            $.proxy(this.onExportSuccess, this));
    },

    onExportSuccess: function(data) {
        this.getFavoritesToPdfBt().show();
        this.getFavoritesReportsItemsLoader().hide();

        if(data.success) {
            window.open(data.fileUrl, '_blank');
        }
    },

    getFavoritesBt: function () {
        return $('.favorites-to-archive');
    },

    getFavoritesToPdfBt: function() {
        return $('.favorites-to-pdf');
    },

    getFavoritesReportsItemsLoader: function() {
        return $('.favorites-reports-items-loader');
    },

    getCheckedItems: function() {
        var items = [];

        $('.ch-check-uncheck-fav-report-item').each(function(ind, el) {
            if($(el).is(':checked')) {
                items.push($(el).data('id'));
            }
        });

        return items;
    },

    showHidePdfButton: function() {
        var items = this.getCheckedItems();
        if(items.length > 0) {
            this.getFavoritesBt().hide();
            this.getFavoritesToPdfBt().show();
        } else {
            this.getFavoritesBt().show();
            this.getFavoritesToPdfBt().hide();
        }
    }
}