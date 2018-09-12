<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>VW</title>
</head>

<body style="font-family:Arial, Helvetica, sans-serif; color:#2d2d2d; font-size:14px; line-height:20px">
<p>Уважаемый дилер!</p>
<p><?php echo $model->isConcept() ? 'Ваша концепция согласована.' : "Ваш макет «{$model->getName()}» согласован." ?></p>
<p>Параметры <?php echo $model->isConcept() ? 'концепции' : 'макета' ?>:</p>

<table width="564" border="1" bordercolor="#ced8d9" cellspacing="0" cellpadding="10">
<?php if(!$model->isConcept()): ?>
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>Номер</td>
        <td align="right"><?php echo $model->getId() ?></td>
      </tr>
    </table></td>
  </tr>
<?php endif; ?>
  <tr>
    <td bgcolor="#eaeeed"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>Дилер</td>
        <td align="right"><?php echo $model->getDealer()->getName() ?></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>Активность</td>
        <td align="right"><?php echo $model->getActivity()->getName() ?></td>
      </tr>
    </table></td>
  </tr>
<?php if(!$model->isConcept()): ?>
  <tr>
    <td bgcolor="#eaeeed"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>Название</td>
        <td align="right"><?php echo $model->getName() ?></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>Тип</td>
        <td align="right"><?php echo $model->getModelType()->getName() ?></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td bgcolor="#eaeeed"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>Цель</td>
        <td align="right"><?php echo $model->getTarget() ?></td>
      </tr>
    </table></td>
  </tr>
<?php endif; ?>
  
<?php $n = 0; ?>
<?php foreach($model->getModelType()->getFields() as $field): ?>
  <tr>
    <td<?php if(($n ++) % 2 != 0) echo ' bgcolor="#eaeeed"'; ?>><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td><?php echo $field->getName() ?><?php if($field->getUnits()): ?>, <?php echo $field->getUnits() ?><?php endif; ?></td>
        <td align="right"><?php echo $model->getValueByType($field->getIdentifier()) ?></td>
      </tr>
    </table></td>
  </tr>
<?php endforeach; ?>
  
<?php if(!$model->isConcept()): ?>
  <tr>
    <td<?php if(($n ++) % 2 != 0) echo ' bgcolor="#eaeeed"'; ?>><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>Сумма</td>
        <td align="right"><?php echo $model->getCost() ?></td>
      </tr>
    </table></td>
  </tr>
<?php endif; ?>
</table>
<p>----------------------------------</p>
<p>С уважением,<br>
  Ваша команда Servicepool<br>
  <a href="mailto:<?php echo sfConfig::get('app_mail_sender') ?>"><?php echo sfConfig::get('app_mail_sender') ?></a></p>
</body>
</html>
    