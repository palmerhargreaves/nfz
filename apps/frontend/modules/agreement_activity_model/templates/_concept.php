<div id="concept" class="active">
    <div class="border">
        <table id="activity-concept" class="models concept">
            <tbody>
    <?php if($concept): ?>
            <?php 
                foreach($concept as $conceptItem)
                    include_partial('concept_item', array('concept' => $conceptItem)); ?>
    <?php else: ?>
            <?php include_partial('concept_empty_item'); ?>
    <?php endif; ?>
            </tbody>
        </table>

    </div>

    <?php if($activity->getManyConcepts() && !$activity->getFinished()): ?>
        <div id="model-many-concepts" style="width: 100%; display: block; float: left;">
            <div id="add-model-concept-button" class="add small button" style="float:left; margin-top: 10px; z-index: 9999;">Добавить концепцию</div>
        </div>
    <?php endif; ?>
</div>

<?php /*include_partial('concept_activity/modal_concept', array('activity' => $activity))*/ ?>
