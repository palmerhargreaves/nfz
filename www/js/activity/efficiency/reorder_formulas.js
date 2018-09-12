/**
 * Created by kostet on 30.09.2016.
 */

$(function() {
    var self = this;

    self.SERVER_ACTION_ON_ORDER = '/backend.php/activity_efficiency_work_formulas/reorder';
    self.initTableDnD = function() {
        $('#effectiveness-formulas-list').tableDnD({
            hierarchyLevel: 0,
            onDragStart: function(table, row) {
                $(table).parent().find('.result').text('');
            },
            onDrop: function(table, row) {
                $.post(self.SERVER_ACTION_ON_ORDER,
                    { data : $.tableDnD.jsonize() },
                    function(result) {
                    });
            }
        });
    }

    self.initTableDnD();
});