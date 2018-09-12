<?php echo $material_category->getName(); ?>
<input type='hidden' value='<?php echo $material_category->getId(); ?>' data-sortable-url='<?php echo url_for('material_category_reorder'); ?>' />