<h1>Выгрузка данных по макетам</h1>

<?php if($status) { ?>
  <div class="alert alert-success container-success">Данные по успешно выгружены</div>
<?php } ?>


<form action="<?php echo url_for('makets_info/index') ?>" method="get" class="form-inline" id="makets-form">
  <div style="display: block; width: 35%">
    <table class="table table-bordered table-striped " cellspacing="0" >
      <tr>
        <td class="span3">Дилер</td>
        <td class="span3">
          <select id='dealer_filter' name='dealer_filter'>
            <option value='-1'>Все</option>
          <?php foreach($dealers as $dealer): ?>
              <?php
                $sel = '';
                if($dealer_filter && $dealer_filter == $dealer->getId()) $sel = 'selected';
              ?>
            <option value="<?php echo $dealer->getId() ?>" <?php echo $sel; ?>><?php echo sprintf('[%s] %s', $dealer->getNumber(), $dealer->getName()) ?></option>
          <?php endforeach; ?>
          </select>
        </td>
      </tr>
      <tr>
        <td class="span3">Год</td>
        <td class="span3">
          <select name="year" id="year">
          <?php

            for($i = $startYear; $i <= $endYear; $i++) {
              $sel = '';

              if($currentYear == $i)
                $sel = 'selected';

              echo "<option value='{$i}' {$sel}>{$i}</option>";
            }
          ?>
          </select>
        </td>
      </tr>
      <tr>
        <td class="span3">Квартал</td>
        <td class="span3">
          <select name="quarter" id="quarter">
            <option value="0">Все кварталы</option>
          <?php
            for($i = 1; $i <= 4; $i++) {
              $sel = '';

              if($currentQuarter == $i)
                $sel = 'selected';

              echo "<option value='{$i}' {$sel}>{$i}</option>";
            }
          ?>
          </select>
        </td>
      </tr>

      <tr>
        <td class="span3">Активность</td>
        <td class="span3">
          <select name="activity" id="activity">
            <option value='-1'>Все</option>
          <?php

            foreach($activities as $item) {
              $sel = '';

              if($currentActivity == $item->getId())
                $sel = 'selected';

              echo "<option value='{$item->getId()}' {$sel}>".sprintf("[%s] %s", $item->getId(), $item->getName())."</option>";
            }
          ?>
          </select>
        </td>
      </tr>

      <tr>
        <td class="span3">Тип файлов</td>
        <td class="span3">
          <select name="fTypes" id="fTypes">
            <option value='-1'>Все</option>
            <?php
              foreach($filesTypes as $type):
                $sel = "";

                if($fType == '.'.$type)
                  $sel = "selected";

                echo "<option value='.{$type}' {$sel}>".strtoupper($type)."</option>";
              endforeach;
            ?>
          </select>
        </td>
      </tr>

      <tr>
        <td class="span3">Только согласованные макеты</td>
        <td class="span3">
          <input type='checkbox' id='cbOnlyAcceptModels' name='cbOnlyAcceptModels' <?php echo $onlyAccepted ? "checked" : ""; ?> />
        </td>
      </tr>

      <tr>
        <td class="span6" colspan='2'>
          <input type="submit" value="Выгрузить" class="btn">
        </td>       
      </tr>
    </table>
  </div>
</form>

<script type="text/javascript">
	$('#makets-form input.date').datepicker({ dateFormat: "dd.mm.y" });
</script>