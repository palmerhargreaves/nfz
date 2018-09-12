<?php if ($pager->haveToPaginate()): ?>
<div class="paginator">
    <?php if($pager->getPreviousPage() != $pager->getPage()): ?>
    <a href="<?php echo url_for($route, array_merge($pageLinkArray->getRawValue(), array('page' => $pager->getPreviousPage())));?>" class="left">&nbsp;</a>
    <?php endif; ?>
    <?php
        /*PAGINATOR 1 2 3...4(5)6...8 9 10 STYLE*/
        //Загрузка всех линков
        $links = $pager->getLinks($pager->getLastPage());
        //$trio - номер тройки по счёту 1,2 или 3
        //$page - текущий номер страницы, заносимой в базу
        //$i - номер ячейки внутри тройки
        //$current - выбранная страница
        //$pages - результат: массив индексов страниц, разбитый на тройки
        $pages = array(); $current = $pager->getPage();
        $page = 1; $count = count($links); 
        for ($trio = 1; ($trio <= 3) && ($page<=$count); $trio++) {
            if ($page < $count - 2) 
                if (($current>($count-2))&&($page>3))
                    $page = $count-2;  
                else if (($page>($current+1))&&($page<($count-2))) 
                    $page = $count-2;      
                else if (($page>3)&&($page<($current-1))) 
                    $page = $current-1;
            
            
            
            //Начинаем с первой ячейки в тройке
            $i = 1;
            //Массив 
            while (($page<=$count)&&($i<=3)&&((abs($page-$current)<2)||($page<=3)||($page)>=($count-2))) {
                $pages[$trio-1][$i-1] = $page;
                $page++; $i++;
            }
        }
    
    ?>
    <?php $temp = 1; foreach ($pages as $trio): ?>
        
        <?php foreach ($trio as $page): ?>
            <?php if ($page-$temp>1): ?>
                <span class="skip">...</span>
            <?php endif; ?>
            <a <?php if($pager->getPage() == $page) echo 'class="active"';?> href="<?php echo url_for($route, array_merge($pageLinkArray->getRawValue(), array('page' => $page)));?>"><?php echo $page?></a>
            <?php $temp = $page ?> 
        <?php endforeach; ?>
           
    <?php endforeach; ?>
    
    
    <?php if($pager->getNextPage() != $pager->getPage()): ?>
    <a href="<?php echo url_for($route, array_merge($pageLinkArray->getRawValue(), array('page' => $pager->getNextPage())));?>" class="right">&nbsp;</a>
    <?php endif; ?>
</div>
<?php endif; ?>  