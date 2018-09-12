
<tr class="model-dates-field">
    <td class="label" style="width: 136px;">
        Даты проведения мероприятия
    </td>
    <td class="field controls">
        <div class="modal-input-group-wrapper period-concept-group">
            <div class="modal-input-wrapper modal-short-input-wrapper">
                <input type="text" name="dates_of_service_action_start[]" class="dates-field" placeholder="от" data-format-expression="^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$" data-required="1" data-right-format="21.01.2013"/>
                <div class="modal-input-error-icon error-icon"></div>
            </div>
            <div class="modal-input-wrapper modal-short-input-wrapper">
                <input type="text" name="dates_of_service_action_end[]" class="dates-field" placeholder="до" data-format-expression="^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$" data-required="1" data-right-format="21.01.2013"/>
                <div class="modal-input-error-icon error-icon"></div>
                <div class="error message dates-error-message"></div>
            </div>
        </div>
    </td>
</tr>

<tr class="model-certificate-field">
    <td class="label" style="width: 136px;">
        Срок окончания действия сертификата<br/>
        <div class="modal-input-wrapper" style="border: none;">
            <span style='float: right; cursor: pointer; font-weight: normal; font-size: 11px; text-decoration: underline; position: relative; margin-top: 5px;' class='what-info-conception'>Что это?</span>
            <div class="modal-input-error-icon error-icon"></div>
            <div class="error message" style="display: none; z-index: 999;"></div>
        </div>
    </td>
    <td class="field controls">
        <div class="modal-input-wrapper">
            <input type="text" name="date_of_certificate_end" id="" class="dates-field" placeholder="" data-format-expression="^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$" data-required="1" data-right-format="21.01.13"/>
            <div class="modal-input-error-icon error-icon"></div>
            <div class="error message"></div>
        </div>
    </td>
</tr>
