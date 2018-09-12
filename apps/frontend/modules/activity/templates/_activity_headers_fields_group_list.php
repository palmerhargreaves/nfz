<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 22.06.2016
 * Time: 15:53
 */

foreach ($activity->getActiveVideoRecordStatisticHeaders() as $header): ?>
    <div class="group open">
        <div class="group-header">
            <span class="title"><?php echo $header->getHeader(); ?></span>
        </div>

        <div class="group-content">
            <table class="models">
                <tbody>
                <?php
                $groups = $header->getGroupList();
                if (count($groups) > 0) {
                    $n = 0;
                    foreach ($groups as $group):
                        $fields = $group->getFieldsList($header->getId());
                        include_partial('activity_header_fields_list',
                            array
                            (
                                'fields' => $fields,
                                'header' => $header,
                                'group' => $group,
                                'owner' => false,
                                'include_group' => true,
                                'allow_to_edit' => $allow_to_edit,
                                'current_q' => $current_q,
                            )
                        );

                        $fields = $group->getFieldsList($header->getId(), $sf_user->getAuthUser()->getRawValue()->getDealer()->getId());
                        include_partial('activity_header_fields_list',
                            array
                            (
                                'fields' => $fields,
                                'header' => $header,
                                'group' => $group,
                                'owner' => true,
                                'include_group' => true,
                                'allow_to_edit' => $allow_to_edit,
                                'current_q' => $current_q,
                            )
                        );
                    endforeach; ?>
                <?php } ?>

                <?php include_partial('activity_header_fields_list',
                    array
                    (
                        'fields' => $header->getFieldsList($groups),
                        'header' => $header,
                        'owner' => false,
                        'include_group' => false,
                        'allow_to_edit' => $allow_to_edit,
                        'current_q' => $current_q,
                    )
                ); ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endforeach; ?>