/**
 * Created by kostet on 23.06.2016.
 */

ActivityFieldsReorder = function(config) {
    // configurable {
    // }
    this.on_reorder_fields = '';
    this.on_reorder_headers = '';

    $.extend(this, config);
}

ActivityFieldsReorder.prototype = {
    start: function() {
        this.initEvents();

        return this;
    },

    initEvents: function() {
        var $self = this;

        $('table.headers-list').tableDnD({
            hierarchyLevel: 1,
            onDragStart: function(table, row) {
                console.log(row);
                $(table).parent().find('.result').text('');
            },
            onDrop: function(table, row) {
                $.post($self.on_reorder_headers,
                    { data : $.tableDnD.jsonize() },
                    function(result) {
                    });
            }
        });

        $('table.header-fields-list').tableDnD({
            hierarchyLevel: 2,
            onDragStart: function(table, row) {
                $(table).parent().find('.result').text('');
            },
            onDrop: function(table, row) {
                $.post($self.on_reorder_fields,
                    { data : $.tableDnD.jsonize() },
                    function(result) {
                    });
            }
        });
    },
}