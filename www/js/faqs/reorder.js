$(function() {
	var self = this;

	self.SERVER_ACTION_ON_ORDER = '/backend.php/faqs/faqs_reorder';
	self.initTableDnD = function() {
		$('#faqs-list').tableDnD({
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