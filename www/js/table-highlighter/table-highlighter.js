TableHighlighter = function(config) {
    // configurable {
    this.table_selector = ''; // required table selector
    this.rows_header_selector = '';
    this.columns_header_selector = '';
    this.highlight_class = 'highlight';
    // }
    $.extend(this, config);
  
}

TableHighlighter.prototype = {
    start: function() {
        this.initEvents();
    
        return this;
    },
  
    initEvents: function() {
        $(this.getRowsHeader()).click($.proxy(this.onClickRowHeader, this));
        $(this.getColumnsHeader()).click($.proxy(this.onClickColumnHeader, this));
    },
  
//    onClickRowHeader: function(e) {
//        var tr = $(e.target).closest('tr');
//        
//        if(tr.data("highlighted")) {
//            tr.find('td').not('.column-lock').removeClass(this.highlight_class);
//            tr.find('td').removeClass('row-lock');
//            tr.removeClass(this.highlight_class);
//            tr.data("highlighted", false);
//        }
//        else {
//            tr.find('td').addClass(this.highlight_class);
//            tr.find('td').addClass('row-lock');
//            tr.addClass(this.highlight_class);
//            tr.data("highlighted", true)
//        }
//    
//        return false;
//    },
  
    onClickColumnHeader: function(e) {
        var td = $(e.target).closest('td');
        var index = td.index();
        var highlight_class = this.highlight_class;
        
        if(td.data("highlighted")) {
            this.getTable().find('tbody tr').each(function(){
                $(this).find('td:eq(' + index + ')').not('.row-lock').removeClass(highlight_class);
                $(this).find('td:eq(' + index + ')').removeClass('column-lock');
            });
            td.data("highlighted", false);
        }
        else {
            this.getTable().find('tbody tr').each(function(){
                $(this).find('td:eq(' + index + ')').addClass(highlight_class);
                $(this).find('td:eq(' + index + ')').addClass('column-lock');
            });
            td.data("highlighted", true)
        }
        
        return false;
    },
  
    getTable: function() {
        return $(this.table_selector);
    },
  
    getRowsHeader: function() {
        return $(this.rows_header_selector, this.table_selector);
    },
  
    getColumnsHeader: function() {
        return $(this.columns_header_selector, this.table_selector);
    }

}