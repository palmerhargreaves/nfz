<p style='float: left;'><?php echo $material->getName(); ?></p>
<input type="hidden" value="<?php echo $material->getId(); ?>" data-sortable-url='<?php echo url_for('material_order'); ?>' />

<?php 
	$date = date('Y-m-d', strtotime($material->getCreatedAt()));
	$todayDay = date('Y-m-d');

	if($date == $todayDay):
?>
	<img style='float: right;' src='../images/new.png' title='Новый материал' />
<?php endif; ?>