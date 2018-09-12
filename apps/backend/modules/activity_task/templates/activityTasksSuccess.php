<ul class="dealer-tasks" data-sortable-url='<?php echo url_for('activity_tasks_orders'); ?>'>
<?php foreach($tasks as $task): ?>
  <li class="task" data-id="<?php echo $task->getId(); ?>">
    &nbsp;
    <?php echo $task->getName() ?>
  </li>
<?php endforeach; ?>
</ul>

<script>
$(function () {
                
    function implode( glue, pieces ) {  
        return ( ( pieces instanceof Array ) ? pieces.join ( glue ) : pieces );
    }

    function sort() {
        lastsInLists();
        var list = $(this);
        var data = [];
        var position = 1;
        list.find('> li:not(".new")').each(function () {
            var el = {};

            //data[data.length] = 'elements['+$(this).data('id')+']='+(position++);
            el.id = $(this).data('id');
            el.position = position++;
            
            data.push(el);
        })
        /*var data_sting = data.join ('&');
        data_sting = data_sting.replace('&&','');*/

        $.ajax({
            url: list.data('sortable-url'),
            data: { 'elements' : data, activityId : <?php echo $activityId; ?> },
            type: 'POST'
        })
    }

    function lastsInLists () {
        $('ul.dealer-tasks > li').removeClass('last');
        $('ul.dealer-tasks > li:last-child').addClass('last');
    }

    $('ul.dealer-tasks').sortable({  
        cancel: 'li.new',
        items: '> li:not(".new")',
        update : sort
    });   
})
</script>