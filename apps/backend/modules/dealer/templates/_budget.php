<?php if($dealer->getBudget()->count() > 0): ?>
<table class="budget">
  <tr>
    <thead>
      <th>год</th>
      <th>квартал</th>
      <th>сумма</th>
    </thead>
  </tr>
<?php foreach($dealer->getBudget() as $budget): ?>
  <tr>
    <td class="year"><?php echo $budget->getYear() ?></td>
    <td class="quarter"><?php echo $budget->getQuarter() ?></td>
    <td class="sum">
      <ul class="sf_admin_actions">
        <li class="sf_admin_action_edit">
          <a href="<?php echo url_for('budget/edit/?id='.$budget->getId()) ?>"><?php echo $budget->getPlan() ?></a>
        </li>
      </ul>
    </td>
  </tr>
<?php endforeach; ?>
</table>
<?php endif; ?>
<ul class="sf_admin_actions">
  <li class="sf_admin_action_new"><a href="<?php echo url_for('budget/new?dealer_id='.$dealer->getId()) ?>">Добавить</a></li>
</ul>
