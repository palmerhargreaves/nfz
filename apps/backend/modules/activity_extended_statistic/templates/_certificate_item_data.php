<td><?php echo $ind++; ?></td>
<td><?php echo sprintf('%s (%s)', $item->getDealer()->getName(), $item->getDealer()->getNumber()); ?></td>
<td><?php echo $item->getCertificateEnd(); echo "( ".($item->getIsBlocked() ? "отключен" : "активен").")";  ?></td>
<td><?php echo $item->getPlusDays() ?></td>
<td><input type='text' data-id='<?php echo $item->getId(); ?>' class='input-days-field input-days<?php echo $item->getId(); ?>' data-regexp='/^[0-9.]+$/' placeholder='0' /> </td>
<td><input type='button' class='btn button-change-days input-button<?php echo $item->getId(); ?>' data-id="<?php echo $item->getId(); ?>" style='display: none;' value='Принять' /> </td>
<td><a href='javascript:;' class='on-delete-field' data-is-calc='<?php //echo $isCalcField ? 1 : 0; ?>' data-id='<?php ///echo $field->getId(); ?>'><img src='/images/delete-icon.png' title='Удалить' /></a></td>
