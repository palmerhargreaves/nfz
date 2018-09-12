<div class="approvement">
    <div id="agreement-models">
        <div id="materials" class="active" style="padding-top: 7px;">
            <?foreach($stats as $activityId => $stat): ?>
            <div class="group open">
                <div class="group-header">
                    <span class="title"><?php echo sprintf('Активность - %s [%s]', $stat['activity']['name'], $stat['activity']['id']); ?></span>
                </div>
                <div class="group-content" style="display: block;">
                    <table class="models" id="models-list">
                        <thead>
                            <tr>
                                <td>Квартал</td>
                                <td>Без сертификата (записей)</td>
                                <td>Сертификат (записей)</td>
                                <td>Экспорт</td>
                            </tr>
                        </thead>

                        <tbody>
                        <?php foreach($stat['data'] as $q => $data): ?>
                            <tr>
                                <td><?php echo sprintf('Квартал [%s]', $q); ?></td>
                                <td style="text-align: center;"><?php echo $data['data']['dontHaveConcept']; ?></td>
                                <td style="text-align: center;"><?php echo $data['data']['haveConcept']; ?></td>
                                <td>
                                    <button class="bt-on-export-service-clinic-stats bt-on-export-service-clinic-stats-<?php echo $stat['activity']['id']; ?>-<?php echo $q;?> button small"
                                            data-activity-id="<?php echo $stat['activity']['id']; ?>"
                                            data-quarter="<?php echo $q; ?>"
                                            data-url="<?php echo url_for('@service_clinic_stats_export'); ?>">
                                        Экспорт
                                    </button>
                                    <img class="img-export-service-clinic-loader-<?php echo $stat['activity']['id']; ?>-<?php echo $q;?>"
                                         src="/images/loader.gif"
                                         style="display: none;" />
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>