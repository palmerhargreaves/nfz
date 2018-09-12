AgreementModelReportFavoritesManagementController = function (config) {
    // configurable {
    this.selector = '';
    this.add_to_favorites_url = '';
    this.remove_to_favorites_url = '';

    this.add_to_archive = '';
    this.delete_favorite_item = '';
    // }

    AgreementModelReportFavoritesManagementController.superclass.constructor.call(this, config);
}

utils.extend(AgreementModelReportFavoritesManagementController, utils.Observable, {
    start: function () {
        this.initEvents();

        return this;
    },

    initEvents: function () {
        this.getValuesBlock().on('click', '.model-report-add-to-favorites', $.proxy(this.onAddToFavorites, this));
        this.getValuesBlock().on('click', '.model-report-remove-from-favorites', $.proxy(this.onRemoveFromFavorites, this));

        this.getFavoritesAddToAcrhive().click($.proxy(this.onFavoritesAddToArchive, this));
        this.getFavoritesDeleteItemLink().click($.proxy(this.onDeleteFavoritesItem, this));
    },

    //Add to favorites
    getAddToFavoritesLink: function (file) {
        if (file != undefined) {
            return $('.report-favorites-add-file-' + file, this.getContainer());
        }

        return $('.model-report-add-to-favorites', this.getContainer());
    },

    onAddToFavorites: function (e) {
        $.post(this.add_to_favorites_url,
            {
                typeId: $(e.target).data('type-id'),
                modelReportId: $(e.target).data('model-report-id'),
                fileName: $(e.target).data('file-name'),
                fileInd: $(e.target).data('file-ind')
            },
            $.proxy(this.onLoadAddToFavorites, this));
    },

    onLoadAddToFavorites: function (data) {
        this.getAddToFavoritesLink(data.fileInd).hide();
        this.getRemoveFromFavoritesLink(data.fileInd).show();
    },

    //Remove from favorites
    getRemoveFromFavoritesLink: function (file) {
        if (file != undefined)
            return $('.report-favorites-remove-file-' + file, this.getContainer());

        return $('.model-report-remove-from-favorites', this.getContainer());
    },

    onRemoveFromFavorites: function (e) {
        $.post(this.remove_to_favorites_url,
            {
                modelReportId: $(e.target).data('model-report-id'),
                fileName: $(e.target).data('file-name'),
                fileInd: $(e.target).data('file-ind')
            },
            $.proxy(this.onLoadRemoveFromFavorites, this));
    },

    onLoadRemoveFromFavorites: function (data) {
        this.getRemoveFromFavoritesLink(data.fileInd).hide();
        this.getAddToFavoritesLink(data.fileInd).show();
    },

    onFavoritesAddToArchive: function () {
        $.post(this.add_to_archive, {}, $.proxy(this.onLoadResultAddToArchive, this));
    },

    onLoadResultAddToArchive: function (data) {
        window.location.href = data.url;
    },

    onDeleteFavoritesItem: function (e) {
        if (confirm('Удалить ?')) {
            $.post(this.delete_favorite_item,
                {
                    id: $(e.target).data('id')
                },
                $.proxy(this.onDeleteItemResult, this));
        }
    },

    onDeleteItemResult: function (data) {
        if (data.success)
            $('.favorite-item-' + data.id).remove();
    },

    getContainer: function () {
        return $('#report-panel');
    },

    getValuesBlock: function () {
        return $('.values', this.getContainer());
    },

    getFavoritesAddToAcrhive: function () {
        return $('.favorites-to-archive')
    },

    getFavoritesDeleteItemLink: function () {
        return $('.delete-favorite-item');
    }
});