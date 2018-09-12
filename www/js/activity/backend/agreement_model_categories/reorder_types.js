/**
 * Created by kostet on 13.01.2017.
 */

$(function() {
    var self = this;

    self.SERVER_ACTION_ON_TYPES_ORDER = '/backend.php/agreement_model_categories/reorder_types';
    self.SERVER_ACTION_ON_FIELDS_ORDER = '/backend.php/agreement_model_categories/reorder_category_fields';
    self.SERVER_ACTION_ON_MODEL_CATEGORY_ORDER = '/backend.php/agreement_model_categories/reorder_activity_model_categories';

    self.initTableDnD = function(cls, url) {
        return $(cls).tableDnD({
            hierarchyLevel: 0,
            onDragStart: function(table, row) {
                //$(table).parent().find('.result').text('');
            },
            onDrop: function(table, row) {
                $.post(url,
                    { data : $.tableDnD.jsonize() },
                    function(result) {
                    }
                );
            }
        });
    };



    // Sortable rows
    $('#activity-model-categories-list').sortable({
        containerSelector: 'table',
        itemPath: '> tbody',
        itemSelector: 'tr',
        placeholder: '<tr class="placeholder"/>',
        onDrop: function($item, container, _super) {
            var rows_ids_list = [];

            $(container.el).find('tbody > tr.model-category-item').each(function(i, tr) {
                rows_ids_list.push($(tr).attr('id'));
            });

            $.post(self.SERVER_ACTION_ON_MODEL_CATEGORY_ORDER, {
                items: rows_ids_list
            }, function(result) {

            });

            _super($item, container);
        }
    });

    self.initSortTable = function(table_cls, table_tr_cls, url) {
        $(table_cls).sortable({
            nested: true,
            containerPath: "td",
            containerSelector: '.table',
            itemPath: '> tbody',
            itemSelector: 'tr',
            placeholder: '',
            onDrop: function($item, container, _super) {
                var rows_ids_list = [];

                $(container.el).find('tbody > tr' + table_tr_cls).each(function(i, tr) {
                    rows_ids_list.push($(tr).attr('id'));
                });

                $.post(url, {
                    items: rows_ids_list
                }, function(result) {

                });

                _super($item, container);
            },
        }).disableSelection();
    }

    self.initSortTable('#activity-model-categories-list', '.model-category-item', self.SERVER_ACTION_ON_MODEL_CATEGORY_ORDER);
    //self.initSortTable('#category-fields-list', '.category-field-item', self.SERVER_ACTION_ON_FIELDS_ORDER);
    //self.initSortTable('#types-list', '.category-type-item', self.SERVER_ACTION_ON_TYPES_ORDER);

    self.initTableDnD('.types-list', self.SERVER_ACTION_ON_TYPES_ORDER);
    self.initTableDnD('.category-fields-list', self.SERVER_ACTION_ON_FIELDS_ORDER);

    //self.initTableDnD('#activity-model-categories-list', self.SERVER_ACTION_ON_FIELDS_ORDER);
});
