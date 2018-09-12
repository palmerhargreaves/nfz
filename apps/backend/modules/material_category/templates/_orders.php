<ul style='list-style-type: none; text-align: center; width: 70px;'>
	<li style='display: inline;'>
		<div style='display: block; border:1px solid #ccc; border-radius: 4px; float: left; padding: 3px; margin-right: 5px;'>
			<a href='<?php echo url_for('@material_category_order_down?material_id='.$material_category->getId()); ?>'><img src='../images/down_grey.png' title='Переместить вниз' style='width: 16px;' /></a>
		</div>
	</li>
	<li style='display: inline;'>
		<div style='display: block; border:1px solid #ccc; border-radius: 4px; float: left; padding: 3px;'>
			<a href='<?php echo url_for('@material_category_order_up?material_id='.$material_category->getId()); ?>'><img src='../images/go_up.png' title='Переместить вверх' style='width: 16px;' /></a>
		</div>
	</li>
</ul>