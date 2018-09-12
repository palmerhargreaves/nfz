<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 12.07.2016
 * Time: 11:59
 */
    include_partial('messages_list',
        array
        (
            'messages' => $messages,
            'start_from' => $start_from,
            'message_type' => $message_type,
            'pager' => $pager,
            'paginatorData' => $paginatorData,
            'page_parent' => $page_parent
        )
    );
?>
