<div class="content-wrapper">
    <?php include_partial('activity/activity_tabs', array('activity' => $activity, 'active' => 'info')) ?>

    <div class="pane-shadow"></div>
    <div class="pane clear">
        <div id="information" class="active">
            <div class="main-column">
                <?php
                if($activity->hasInfoFieldsData()):
                    include_partial('activity/activity_description', array('activity' => $activity));
                elseif($activity->getRawValue()->getDescription()):
                    echo $activity->getRawValue()->getDescription();
                elseif($activity->getInfo()->count() > 0): ?>
                    <table class="description">
                        <?php foreach($activity->getInfo() as $item): ?>
                            <tr>
                                <td class="left-column">
                                    <div class="relative">
                                        <img src="/images/info/<?php echo $item->getIcon() ?>" alt="">
                                        <div class="header"><?php echo $item->getRawValue()->getName() ?></div>
                                    </div>
                                </td>
                                <td class="content-column"><?php echo $item->getRawValue()->getText() ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                    <!--<div class="print button">Печать</div>-->
                <?php endif; ?>
            </div>

            <div class="files-column">
                <?php foreach($activity->getFiles() as $file): ?>
                    <?php $helper = $file->getFileNameHelper(); ?>
                    <a href="<?php echo url_for('@activity_file_download?id='.$file->getId()); ///echo '/uploads/'.ActivityFile::FILE_PATH.'/'.$file->getFile() ?>" class="file" target="_blank">
                        <div class="name"><?php echo $file->getName() ?></div>
                        <div class="size <?php echo $helper->getExtension() ?>"><?php echo strtoupper($helper->getExtension()) ?> (<?php echo $helper->getSmartSize() ?>)</div>
						<i><?php echo strtoupper($helper->getExtension()) ?></i>
                    </a>
                <?php endforeach; ?>

            </div>
            <div class="clear"></div>
        </div>


    </div>
</div>
