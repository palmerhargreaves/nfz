$(function() {
	var self = this;

	self.onAddCheckBox = function(parent) {
		$('#' + parent).after("<div style='display: block; margin:1px; clear: both; margin-left: 235px;'><input type='checkbox' id='" + parent + "_unselect_all'><label for='" + parent + "_unselect_all' style='width: 130px !important;'>Снять выделение</label></div>");		
		$('#' + parent).after("<div style='display: block; margin:1px; clear: both; margin-left: 235px;'><input type='checkbox' id='" + parent + "_select_all'><label for='" + parent + "_select_all' style='width: 130px !important;'>Выделить все</label></div>");		

		self.onClickSelect(parent);
		self.onClickUnSelect(parent);
	}

	self.onClickSelect = function(parent) {
		$(document).on('click', '#' + parent + '_select_all', function() {
			$('#' + parent + '_unselect_all').attr('checked', false);

			$('#' + parent + ' option').each(function() {
				this.selected = true;
			});
		});
	}

	self.onClickUnSelect = function(parent) {
		$(document).on('click', '#' + parent + '_unselect_all', function() {
			$('#' + parent + '_select_all').attr('checked', false);

			$('#' + parent + ' option').each(function() {
				this.selected = false;
			});
		});
	}
	
  	self.onAddCheckBox('activity_modules_list');
  	self.onAddCheckBox('activity_dealers_list');
});