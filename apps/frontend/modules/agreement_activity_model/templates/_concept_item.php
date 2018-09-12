<?php $discussion = $concept->getDiscussion() ?>
<?php $new_messages_count = $discussion ? $discussion->countUnreadMessages($sf_user->getAuthUser()->getRawValue()) : 0 ?>
<tr class="model-row even concept-row" id="concept-info" data-has-concept="true" data-discussion="<?php echo $concept->getDiscussionId() ?>" data-new-messages="<?php echo $new_messages_count ?>" data-model="<?php echo $concept->getId() ?>">
    <td style="padding-left: 10px;" width="70"><div class="date"><?php echo D::toLongRus($concept->created_at) ?></div></td>
    <td><div class="name">Концепция проведения акции</div></td>
    <td class="action"><div><?php echo $concept->getDealerActionText() ?></div></td>
    <td width="35">
        <div class="<?php echo $concept->getCssStatus() ?>">
          <?php if($concept->getStatus() == 'wait_specialist') echo 'x'.$concept->countWaitingSpecialists(); ?>
          <?php if($concept->getStatus() == 'declined' && $concept->countDeclines()) echo 'x'.$concept->countDeclines(); ?>
        </div>
    </td>
<?php $report = $concept->getReport(); ?>
    <td>
      <div class="<?php echo $concept->getReportCssStatus() ?>">
        <?php if($report && $report->getStatus() == 'wait_specialist') echo 'x'.$report->countWaitingSpecialists(); ?>
        <?php if($report && $report->getStatus() == 'declined' && $report->countDeclines()) echo 'x'.$report->countDeclines(); ?>
      </div>
    </td>
    <td data-sort-value="<?php echo $new_messages_count ?>">
<?php if($new_messages_count > 0): ?>
        <div class="message"><?php echo $new_messages_count ?></div>
<?php endif; ?>
    </td>
</tr>
