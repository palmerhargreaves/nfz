<div class="modal hide fade concept-add-modal" id="concept-add-modal" style="width: 400px; left: 45%;">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4>Добавить новый срок выполнения</h4>
    </div>
    <div class="modal-body">
        <div class="panel-info-left fields-list" style="width: 100%; float:left;">
            Привязать дилера к мероприятию
            <select id="sbConcept" name="sbConcept">
                <?php
                $concepts = AgreementModelTable::getInstance()
                                    ->createQuery('am')
                                    ->where('activity_id = ? and model_type_id = ?', array($activity, 10))
                                        ->innerJoin('am.AgreementModelSettings ams')
                                    ->execute();

                foreach($concepts as $concept):
                    $conceptName = "Service Clinic (до): ".date('d-m-Y', strtotime($concept->getAgreementModelSettings()->getCertificateDateTo()));
                ?>
                    <option value="<?php echo $concept->getId(); ?>"><?php echo $conceptName; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

    </div>
    <div class="modal-footer">
        <a href='#' class='btn action-activity-add-concept' data-field-type='information' style="float: left;">Сохранить</a>
        <a href="#" class="btn" data-dismiss="modal" aria-hidden="true">Закрыть</a>
    </div>
</div>
<script type="text/javascript">
    $(function() {
        window.concept_add = new StatisticConceptAdd({
            modal : '#concept-add-modal',
            activity_id : '<?php echo $activity; ?>',
            show_dialog: '.on-add-new-concept',
            accept_url : '<?php echo url_for("activity_extended_statistic_concept_add"); ?>',
            delete_url : '<?php echo url_for("activity_extended_statistic_delete_concept"); ?>'

        }).start();
    });
</script>
