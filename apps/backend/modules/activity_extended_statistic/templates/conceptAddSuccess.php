<ul>
<?php
    foreach($dealerConcepts as $concept):
?>
        <li style="list-style-type: none;">
            <span class="dealer-certificate-item-<?php echo $concept->getConcept()->getId(); ?>" style="float: left;"><?php echo sprintf("Service Clinic (до): %s", date('d-m-Y', strtotime($concept->getConcept()->getAgreementModelSettings()->getCertificateDateTo()))); ?></span>
            <span class="dealer-certificate-item-<?php echo $concept->getConcept()->getId(); ?>" style="float: left;"> ( <img style="cursor: pointer;" class="on-delete-dealer-concept-certificate" data-id="<?php echo $concept->getConcept()->getId(); ?>" src="/images/delete-icon.png" title="Удалить" /> ) </span>
        </li>
<?php
    endforeach; ?>
    <img src="/images/plus-icon.png" style="cursor: pointer;" class="on-add-new-concept pull-right tip" title="Добавить новый срок выполнения"
         data-dealer-id="<?php echo $dealerId; ?>"
         data-activity-id="<?php echo $activity; ?>" />
</ul>