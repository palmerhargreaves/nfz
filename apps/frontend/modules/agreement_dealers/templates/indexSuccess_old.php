<?php include_partial('agreement_activity_model_management/menu', array('active' => 'dealers', 'year' => $year, 'onlyShow' => $onlyShow, 'url' => 'agreement_module_dealers')) ?>
<div class="dealers">

    <table id="dealers">
        <thead>
            <tr>
                <th style="width: 260px;" rowspan="2" class="dealer">
                    <p>Дилеры</p>
                    <input placeholder="Фильтр по дилерам" class="filter" type="text">

                    
                </th>
                <th colspan="2">I Квартал</th>
                <th colspan="2">II Квартал</th>
                <th colspan="2">III Квартал</th>
                <th colspan="2">IV Квартал</th>
                <th colspan="2">Год</th>
                <th>&nbsp;</th>
            </tr>
            <tr class="column-header">
                <th class="budget">Бюджет<br/>Факт</th>
                <th class="percent">%<br/>бонус</th>
                <th class="budget">Бюджет<br/>Факт</th>
                <th class="percent">%<br/>бонус</th>
                <th class="budget">Бюджет<br/>Факт</th>
                <th class="percent">%<br/>бонус</th>
                <th class="budget">Бюджет<br/>Факт</th>
                <th class="percent">%<br/>бонус</th>
                <th class="budget">Бюджет<br/>Факт</th>
                <th class="percent">%</th>
                <th>&nbsp;</th>
            </tr>

        </thead>
        <tbody>
<?php 
    foreach($builder->getManagers() as $id => $manager):

 ?>
            <tr class="filter-group">
                <th colspan="12" class="dealer regional"><div><?php echo strlen(trim($manager['manager'])) ? $manager['manager'] : '&ndash;'; ?><div></th>
            </tr>
            
  <?php foreach($manager['dealers'] as $dealer): 
    $t = $dealer->regional_manager_id;
    if(empty($t))
	continue;
  ?>
            <tr class="dealer" data-filter="<?php echo $dealer ?>">
                <td class="dealer"><div><a href="<?php echo url_for('@agreement_module_dealer?id='.$dealer->getId()) ?>"><span class="num"><?php echo $dealer->getShortNumber() ?></span><?php echo $dealer->getName() ?></a></div></td>
    <?php $dealer_stat = $builder->getDealerStat($dealer->getRawValue()); ?>
    <?php 
      $q = 1;
      foreach($dealer_stat['quarters'] as $quarter_stat): ?>
                <td class="budget"><?php echo number_format($quarter_stat['plan'], 0, '.', ' ') ?><br /><?php echo number_format($quarter_stat['fact'], 0, '.', ' ') ?></td>
                <td class="percent">
                  
                  <?php echo $quarter_stat['plan'] != 0 ? round($quarter_stat['fact'] / $quarter_stat['plan'] * 100) : 0 ?>%
                  <br />
      <?php if($quarter_stat['bonus']['bonus'] === null): ?>
                  <img src="/images/ok-icon.png" alt="" />
      <?php else: ?>
                  <img src="/images/<?php echo $quarter_stat['bonus']['bonus'] ? 'ok-icon-active' : 'error-icon' ?>.png" alt="" title="<?php echo $quarter_stat['bonus']['comment'] ?>" />
      <?php endif; ?>
                </td>
    <?php endforeach; ?>
                <td class="budget"><?php echo number_format($dealer_stat['total']['plan'], 0, '.', ' ') ?><br /><?php echo number_format($dealer_stat['total']['fact'], 0, '.', ' ') ?></td>
                <td class="percent"><?php echo $dealer_stat['total']['plan'] != 0 ? round($dealer_stat['total']['fact'] / $dealer_stat['total']['plan'] * 100) : 0 ?>%</td>
                <td>
    <?php if($dealer_stat['unread'] > 0): ?>
                  <div class="message" onclick="startDiscussionWithDealer(<?php echo $dealer->getId() ?>)"><?php echo $dealer_stat['unread'] ?></div>
    <?php endif; ?>
                </td>
            </tr>
  <?php endforeach; ?>
<?php endforeach; ?>
        </tbody>
    </table>
</div>

<script type="text/javascript">
  $(function() {
    new Filter({
      field: '#dealers input.filter',
      filtering_blocks: '#dealers tbody tr.dealer'
    }).start();
  })
</script>
