<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 20.08.2016
 * Time: 10:22
 */

?>
<div class="content">
    <div style="float: left; width: 100%;">
        <strong>Список копируемых полей (с возможностью копирования формулы привязанной к полю)</strong>
        <hr style="margin:5px 0px; "/>
        <table class="table" style="float: left;">
            <thead style="font-weight: bold;">
            <tr>
                <td>#</td>
                <td style="width: 99%;">Поле</td>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($fields as $field): ?>
                <tr>
                    <td><input type="checkbox" class="copy-field" name="ch_copy_field[]"
                               data-field-id="<?php echo $field->getId(); ?>"/>
                    </td>
                    <td style="">
                        <?php echo $field->getName(); ?>

                        <?php if (count($formulas_list = $field->usedInFormulas()) > 0): ?>
                            <div style="margin-left: 10px; margin-top: 10px; font-size: 12px; display: none;"
                                 class="field-formulas-<?php echo $field->getId(); ?>">
                                <table class="table table-striped" style="float: left;">
                                    <thead style="font-weight: bold;">
                                    <tr>
                                        <td>#</td>
                                        <td style="width: 50%;">Формула в которой используется поле</td>
                                        <td>Параметры</td>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    <?php foreach ($formulas_list as $formula): ?>
                                        <tr>
                                            <td><input type="checkbox" class="copy-field-formula"
                                                       name="ch_copy_formula_by_field[]"
                                                       data-formula-id="<?php echo $formula->getId(); ?>"</td>
                                            <td>
                                                <strong><?php echo $formula->getName(); ?></strong>
                                                <br/>
                                                Активность: <?php echo $formula->getActivity()->getName(); ?>
                                            </td>
                                            <td>
                                                <?php
                                                $params_list = ActivityEfficiencyFormulaParamsTable::getInstance()->createQuery()->where('formula_id = ?', $formula->getId())->execute();
                                                if (count($params_list) > 0):
                                                    ?>
                                                    <ul class="sf_admin_actions">
                                                        <?php foreach ($params_list as $param_data): ?>
                                                            <li><?php echo $param_data->getParamsLabels(); ?></a></li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="alert alert-danger" style="float: left; width: 90%; font-size: 12px;">
        При копировании формулы, если в качестве параметров формулы используются (поля статистики, формулы), необходить
        создать копии (полей, формул), в противном случае копия формулы будет создана без привязки к (полям, формулам).
    </div>

    <br/>
    <hr style="margin:5px 0px; "/>
    <br/>

    <div style="float: left; width: 100%;">
        <strong>Список копируемых формул</strong>
        <hr style="margin:5px 0px; "/>
        <table class="table" style="float: left;">
            <thead style="font-weight: bold;">
            <tr>
                <td>#</td>
                <td style="width: 45%;">Формула</td>
                <td style="">Параметры формулы</td>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($formulas as $formula): ?>
            <tr>
                <td><input type="checkbox" class="copy-only-formula" name="ch_copy_only_formula[]"
                           data-only-formula-id="<?php echo $formula->getId(); ?>"/>
                </td>
                <td style=""><?php echo $formula->getName(); ?> </td>
                <td>
                    <?php
                    $params_list = ActivityEfficiencyFormulaParamsTable::getInstance()->createQuery()->where('formula_id = ?', $formula->getId())->execute();
                    if (count($params_list) > 0):
                    ?>
                    <ul class="sf_admin_actions">
                        <?php foreach ($params_list as $param_data): ?>
                            <li><?php echo $param_data->getParamsLabels(); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>


</div>
