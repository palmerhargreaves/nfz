
<div id="special-budget-modal" class="intro modal" style="width: 740px; left: 40%;">
    <div class="modal-header">Акция на зимние колеса в сборе!</div>
    <div class="modal-close"></div>
    <div class="modal-text">

        <p>С 1 февраля 2014 года начинает действовать «Акция на зимние колеса в сборе», входящая в список обязательных активностей, необходимых для получения бонуса за маркетинг сервиса в первом квартале 2014 года.
Согласно условиям акции дилерскому предприятию необходимо осуществить закупку зимних колес в сборе в период с 1 февраля по 1 апреля 2014 года с центрального склада импортера на сумму не менее чем 120% от бюджета маркетинга сервиса за первый квартал. 
При выполнении данного условия 70% от суммы маркетингового бюджета первого квартала 2014 года будут засчитаны в качестве инвестиций в маркетинг сервиса в первом квартале 2014 года. Акция не распространяется на колеса в сборе для автомобилей Volkswagen Tiguan.</p>
        <p>Для того чтобы подтвердить свое участие в акции, вам необходимо заполнить форму ниже, указав свои данные, и отправить ее нам.
Суммы должны быть предварительно согласованы с руководителем отдела сервиса дилерского предприятия.</p>
    </div>
    

    <div class='model'>
        <form id='frmSpecialBudget modal-form'>
            <table style="width: 99%; margin: auto;">
                <thead>
                    <tr>
                        <th style="font-weight: bold; padding: 5px; border-bottom: 1px solid #bbb;">Запланированный макретинговый бюджет,<br/> 1 квартал 2014, руб.</th>
                        <th style="font-weight: bold; padding: 5px; border-bottom: 1px solid #bbb;">Сумма закупленных колес в сборе, руб.</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td style="padding: 10px;"><input id="txtSpecialBudget1Q" type='text' value='0' placeholder='Запланированный макретинговый бюджет' class='special-input'></td>
                        <td style="padding: 10px;"><input id="txtSpecialBudgetSumm" type='text' value='0' placeholder='Сумма закупленных колес' class='special-input'></td>
                    </tr>
                </tbody>
            </table>

             <div class="special-budget-form-eror-msg message-error" style="color: red; margin: auto; width: 95%; display: none;">
                <p>Проверьте вводимые данные.</p>
            </div>

            <div class="special-budget-msg message-error" style="color: red; margin: auto; width: 95%; display: none;">
                <p>Указанная вами сумма ниже 120% от суммы бюджета на маркетинг сервиса в 1 квартале 2014 года.</p>
            </div>
            
            <div style="display: block; width: 75%; margin: auto; margin-top: 40px;">

                <button class="button gray accept-button" disabled="true" style="width: 45%; float: left; clear: both;" 
                                data-id='<?php echo $sf_user->getAuthUser()->getId(); ?>'>Подтвердить участие</button>
                <!--<button class="button decline-button" style="width: 45%; float: right;"
                                data-id='<?php echo $sf_user->getAuthUser()->getId(); ?>'>Отказаться от участия</button>-->
            </div>
        </form>
    </div>
</div>


