<?php
/**
 * Created by PhpStorm.
 * User: averinbox
 * Date: 26.01.16
 * Time: 17:14
 */
$fieldNames = array(
    'number' => 'номер',
    'name' => 'название',
    'address' => 'адрес',
    'phone' => 'телефон',
    'site' => 'вебсайт',
    'email' => 'email',
    'longitude' => 'долгота',
    'latitude' => 'широта',
    'city_id' => 'город',
    'regional_manager_id' => 'регинальный менеджер',
    'company_id' => 'компания',
    'importer_id' => 'импортёр',
    'dealer_type' => 'тип дилера',
    'regional_manager_id' => 'менеджер',
    'nfz_regional_manager_id' => 'Nfz менеджер',
    'status' => 'статус'
);

$dealerType = array(Dealer::TYPE_PKW => 'PKW', Dealer::TYPE_NFZ => 'NFZ', Dealer::TYPE_NFZ_PKW => 'NFZ + PKW');

?>
<div class="container">
    <div class="row">
        <div class="span12">
            <ul class="breadcrumb">
                <li><a href="/backend.php/">Главная</a> <span class="divider">/</span></li>
                <li><a href="/backend.php/dealer_list">Список дилеров</a> <span
                        class="divider">/</span></li>
                <?php if ($dealer->getName()): ?>
                    <li class="active"><?= $dealer->getName(); ?></li>
                <?php else: ?>
                    <li class="active">Новая запись</li>
                <?php endif; ?>
            </ul>
        </div>
    </div> 
    <div class="row">
        <form class="form-horizontal"
              action="/backend.php/dealer_list/edit<?= $dealer_id ? '?id=' . $dealer_id : ''; ?>" method="post">
            <input type="hidden" name="id" value="<?= $dealer->getId(); ?>"/>
            <?php if ($sf_user->hasFlash('error')): ?>
                <div class="alert alert-danger span6" role="alert"><?= $sf_user->getFlash('error') ?></div>
            <?php endif ?>
            <?php if ($sf_user->hasFlash('success')): ?>
                <div class="alert alert-success span6" role="alert"><?= $sf_user->getFlash('success') ?></div>
            <?php endif ?>
            <div class="span6">
                <?php foreach ($dealer as $key => $field): ?>
                    <?php if ($key != 'id' && $key != 'company_id' && $key != 'importer_id'): ?>
                        <?php if ($key == 'city_id'): ?>
                            <div class="control-group">
                                <label class="control-label" for="input<?= $key; ?>"><?= $fieldNames[$key]; ?></label>

                                <div class="controls">
                                    <select name="<?= $key; ?>">
                                        <option value="">Выберите город</option>
                                        <?php foreach ($cities as $city): ?>
                                            <option
                                                value="<?= $city->getId(); ?>" <?= $field == $city->getId() ? ' selected' : ''; ?>><?= $city->getName(); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        <?php elseif ($key == 'status'): ?>
                            <div class="control-group">
                                <label class="control-label" for="input<?= $key; ?>"><?= $fieldNames[$key]; ?></label>

                                <div class="controls">
                                    <select name="<?= $key; ?>" value="<?= $field; ?>">
                                        <option value="0">Не опубликован</option>
                                        <option value="1" <?= ($field == 1) ? 'selected' : ''; ?>>Опубликован</option>
                                    </select>
                                </div>
                            </div>
                        <?php elseif ($key == 'dealer_type'): ?>
                            <div class="control-group">
                                <label class="control-label" for="input<?= $key; ?>"><?= $fieldNames[$key]; ?></label>

                                <div class="controls">
                                    <select name="<?= $key; ?>" value="<?= $field; ?>">
                                        <?php foreach ($dealerType as $t => $dt) : ?>
                                            <option
                                                value="<?= $t; ?>" <?= ($t == $field) ? 'selected' : ''; ?> ><?= $dt; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <?php elseif (($key == 'regional_manager_id' || $key == 'nfz_regional_manager_id') && $dealer->getDealerType() == Dealer::TYPE_NFZ_PKW): ?>
                            <div class="control-group">
                                <label class="control-label" for="input<?= $key; ?>"><?= $fieldNames[$key]; ?></label>

                                <div class="controls">
                                    <select name="<?= $key; ?>" value="<?= $field; ?>">
                                        <option value="0">Выберите менеджера...</option>
                                        <?php foreach (NaturalPersonTable::getInstance()->createQuery()->select('id, firstname, surname')->where('importer_id = ? or importer_id = ?', array(1, 2))->orderBy('surname, firstname ASC')->execute() as $person) :
                                            $make_selection = false;
                                            if ($key == 'nfz_regional_manager_id') {
                                                $make_selection = $dealer->getNfzRegionalManagerId() == $person->getId() ?: false;
                                            } else {
                                                $make_selection = $dealer->getRegionalManagerId() == $person->getId() ?: false;
                                            }
                                            ?>
                                            <option
                                                value="<?= $person->getId(); ?>" <?= $make_selection ? 'selected' : ''; ?> ><?= sprintf('%s %s', $person->getSurname(), $person->getFirstname()); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <?php elseif (($key == 'regional_manager_id' && $dealer->getDealerType() != Dealer::TYPE_NFZ) || ($key == 'nfz_regional_manager_id' && $dealer->getDealerType() == Dealer::TYPE_NFZ)): ?>
                            <div class="control-group">
                                <label class="control-label" for="input<?= $key; ?>"><?= $fieldNames[$key]; ?></label>

                                <div class="controls">
                                    <select name="<?= $key; ?>" value="<?= $field; ?>">
                                        <option value="0">Выберите менеджера...</option>
                                        <?php foreach (NaturalPersonTable::getInstance()->createQuery()->select('id, firstname, surname')->where('importer_id = ? or importer_id = ?', array(1, 2))->orderBy('surname, firstname ASC')->execute() as $person) :
                                            $make_selection = false;
                                            if ($dealer->getDealerType() == Dealer::TYPE_NFZ) {
                                                $make_selection = $dealer->getNfzRegionalManagerId() == $person->getId() ?: false;
                                            } else {
                                                $make_selection = $dealer->getRegionalManagerId() == $person->getId() ?: false;
                                            }
                                            ?>
                                            <option
                                                value="<?= $person->getId(); ?>" <?= $make_selection ? 'selected' : ''; ?> ><?= sprintf('%s %s', $person->getSurname(), $person->getFirstname()); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        <?php elseif ($key == 'number'): ?>
                            <div class="control-group">
                                <label class="control-label" for="input<?= $key; ?>"><?= $fieldNames[$key]; ?></label>

                                <div class="controls">
                                    <?php if (empty($field)): ?>
                                        <input type="text" name="<?= $key; ?>" id="input<?= $key; ?>" value="<?= $field; ?>"
                                               placeholder="Введите <?= $fieldNames[$key]; ?>">
                                    <?php else: ?>
                                        <input type="text"  id="input<?= $key; ?>" value="<?= $field; ?>"
                                               placeholder="Введите <?= $fieldNames[$key]; ?>" disabled>
                                        <input type="hidden" name="<?= $key; ?>" value="<?= $field; ?>" >
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php elseif ($key != 'regional_manager_id' && $key != 'nfz_regional_manager_id'): ?>
                            <div class="control-group">
                                <label class="control-label" for="input<?= $key; ?>"><?= $fieldNames[$key]; ?></label>
                                <div class="controls">
                                    <input type="text" name="<?= $key; ?>" id="input<?= $key; ?>" value="<?= $field; ?>"
                                           placeholder="Введите <?= $fieldNames[$key] == 'широта' ? 'широту' : ($fieldNames[$key] == 'долгота' ? 'долготу' : $fieldNames[$key]); ?>">
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <div class="span6">
                <h4>Выберите адрес дилера на карте</h4>

                <div id="map" style="min-height: 500px; min-width: 400px;"></div>
            </div>
            <div class="span12">
                <hr>
                <button type="submit" class="btn btn-primary">Сохранить</button>
                <a href="/backend.php/dealer_list" class="btn btn-default">Вернуться к списку дилеров</a>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    var myMap, myPlacemark;
    ymaps.ready(function () {
        <?php $coords = ($dealer->getLongitude() && $dealer->getLatitude()) ? '['. $dealer->getLatitude() .','.  $dealer->getLongitude().']' : '[55.76, 37.64]';  ?>
        <?php $name = ($dealer->getName()) ? $dealer->getName() : 'Москва'; ?>
        <?php $site = ($dealer->getSite()) ? $dealer->getSite() : '#'; ?>
        myMap = new ymaps.Map("map", {
            center:  <?= $coords ?>,
            zoom: 15
        });

        myPlacemark = new ymaps.Placemark(<?= $coords ?>, {
            hintContent: '<?= $name; ?>',
            balloonContent: '<a target="_blank" href="<?= $site; ?>"><?= $site; ?></a>'
        });
        myMap.geoObjects.add(myPlacemark);

        var searchControl = myMap.controls.get('searchControl');
        searchControl.events.add('resultshow', function (event) {
            var search_index = event.originalEvent.index;
            var result = searchControl.getResult(search_index);
            var coords = result._value.geometry._coordinates;
            $('#inputlongitude').val(coords[1]);
            $('#inputlatitude').val(coords[0]);
        }, this);
    });
</script>