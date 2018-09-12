<select name="dealer_id">
  <option value="">-- выберите дилера --</option>
<?php foreach($dealers as $dealer): ?>
  <option value="<?php echo $dealer->getId() ?>"><?php echo $dealer ?></option>
<?php endforeach; ?>
</select>