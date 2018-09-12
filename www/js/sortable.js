$(function () {
                
    function implode( glue, pieces ) {	
        return ( ( pieces instanceof Array ) ? pieces.join ( glue ) : pieces );
    }

    function sortData() 
    {
        lastsInLists();

        var list = $(this);
        var data = [];
        var position = 1;
        list.find('> tr:not(".new")').each(function () {
            var el = {};

            //data[data.length] = 'elements['+$(this).data('id')+']='+(position++);
            el.id = $(this).find("input[type=hidden]").val();
            el.position = position++;
            
            data.push(el);
        });
        
        $.ajax({
            url: list.find('input[type=hidden]:first').data('sortable-url'),
            data: { 'elements' : data },
            type: 'POST'
        });
    }

    function sort(items) {
        $(items).sortable({  
            cancel: 'tr.new',
            items: '> tr:not(".new")',
            update : sortData
        }).disableSelection();       
    }

    function lastsInLists () {
        $('div.sf_admin_list table tbody tr').removeClass('last');
        $('div.sf_admin_list table tbody tr:last-child').addClass('last');
    }

    sort('div.sf_admin_list table tbody');
})