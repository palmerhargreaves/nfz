<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <div class="well sidebar-nav">
                <ul class="nav nav-list">
                    <li class="nav-header">Загрузка бюджета дилеров за: <?php echo date('Y'); ?> год.</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span12">
            <div class="well sidebar-nav">
                <?php if ($sf_user->getFlash('success')): ?>
                    <div
                        class="alert alert-<?php echo count($stats) > 0 ? "success" : "error"; ?> container-success"
                        style="">
                        <?php echo str_replace("|", "<br/>", $sf_user->getFlash('success')); ?>
                    </div>
                <?php endif; ?>

                <form id='frmUploadData' action='<?php echo url_for('dealers_budgets_upload_file'); ?>' method='post'
                      enctype="multipart/form-data" style="padding-bottom: 10px;">
                    <ul class="nav nav-list">
                        <li class="nav-header">Загрузка файла</li>
                        <li>
                            Файл:<br/>
                            <input type="file" name="file_budget"/>
                        </li>
                        <li>
                            <input type='submit' class='btn' style='float: left; margin-right: 10px;'
                                   value='Загрузить'/>
                        </li>
                    </ul>
                </form>
            </div>
        </div>
    </div>

    <div id="dealers-budgets-uploaded-files" class="row-fluid">
        <?php if (isset($files)): ?>
            <table class="table table-bordered table-striped " cellspacing="0">
                <thead>
                <tr>
                    <th>№</th>
                    <th>Файл</th>
                    <th>Год</th>
                    <th>Дилеров</th>
                    <th>Дата загрузки</th>
                </tr>
                </thead>

                <tbody>
                <?php
                $ind = 1;
                foreach ($files as $item): ?>
                    <tr>
                        <td class="span2"><?php echo $ind++; ?> </td>
                        <td class="span3"><?php echo $item->getFileName(); ?> </td>
                        <td class="span2"><span><?php echo $item->getYear(); ?></td>
                        <td class="span2"><span><?php echo $item->getTotalDealers(); ?></td>
                        <td class="span2"><span><?php echo $item->getCreatedAt(); ?></td>
                    </tr>
                    <?php
                endforeach;
                ?>
                </tbody>
            </table>
            <?php
        endif;
        ?>
    </div>
</div>

<script>
    $(function () {
        $('input[type=submit').click(function (e) {
            e.preventDefault();

            if ($('input[name=file_budget]').val().length == 0) {
                alert('Выберите файл.');
                return;
            }

            $(this).closest('form').eq(0).submit();
        });
    });
</script>