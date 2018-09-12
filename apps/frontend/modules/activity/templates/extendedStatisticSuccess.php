<div class="activity">
    <?php
    include_partial('activity/activity_head', array('activity' => $activity, 'quartersModels' => $quartersModels, 'current_q' => $current_q));
    ?>
    <div class="content-wrapper">
        <?php include_partial('activity/activity_tabs', array('activity' => $activity, 'active' => 'extended_statistic')) ?>


        <div class="pane-shadow"></div>
        <div id="agreement-models" class="pane clear">
            <div id="approvement" class="active">
                <form id='frmStatistics'>

                    <?php
                    $concepts = AgreementModelTable::getInstance()
                        ->createQuery('am')
                        ->innerJoin('am.AgreementModelSettings ams')
                        ->where('activity_id = ? and model_type_id = ?', array($activity->getId(), 10))
                        //->andWhere('ams.certificate_date_to >= ?', date('Y-m-d'))
                        ->andWhere('am.dealer_id = ?', $sf_user->getAuthUser()->getDealer()->getId())
                        ->orderBy('ams.id ASC')
                        ->execute();
                    if ($concepts && $concepts->count() > 0):
                        ?>
                        <div style="float: left; margin: 10px 0px 0px 10px; ">
                            <h6 style="font-weight: bold;">Срок действия сертификата:</h6>
                            <select id="sbActivityCertificates" name="sbActivityCertificates"
                                    style="width: 168px; border: 1px solid #d3d3d3; border-radius: 3px; height: 22px; padding: 0 0 0 10px; margin-top: 15px;">
                                <option value="-1">Выберите сертификат ...</option>
                                <?php
                                foreach ($concepts as $concept):
                                    $isBinded = ActivityExtendedStatisticFieldsTable::checkUserConcept($sf_user->getRawValue(), $concept);
                                    ?>
                                    <option value="<?php echo $concept->getId(); ?>">
                                        <?php echo sprintf("%sService Clinic: %s [%d]", $isBinded ? "+ " : "", $concept->getAgreementModelSettings()->getCertificateDateTo(), $concept->getId()); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <button id="btApplyConceptToStatistic" class="button small"
                                    data-activity-id="<?php echo $activity->getId(); ?>">Принять
                            </button>
                            <img id="imgLoader" src="/images/loader.gif" style="display: none;"/>
                        </div>
                    <?php endif; ?>

                    <div class=""
                         style="color: red; margin: 20px 0px 0px 30px; float:left; width: 100%; display: block;">
                        В периоде начальная дата должна быть меньше (равна) даты окончания<br/>
                        Все поля должны быть заполнены (для числовых значений разрешено использование "." )
                    </div>

                    <div id="materials" style="float: left; width: 99%;">
                        <div id="accommodation" class="active">
                            <?php
                            $concept = null;
                            $bindedConcept = ActivityExtendedStatisticFieldsTable::getConceptInfoByUserActivity($sf_user->getRawValue());

                            if ($bindedConcept && !$concept)
                                $concept = $bindedConcept->getConceptId();

                            include_partial('extended_statistic_data', array('activity' => $activity, 'concept' => $concept));
                            ?>
                        </div>
                    </div>
            </div>

            <table class="models">
                <tbody></tbody>
            </table>

            <div class="info-save-complete"
                 style="display: none; width: 99%; margin: 10px; padding: 10px; color: red; text-align: center; font-weight: bold;">
                Параметры статистики успешно сохранены !
            </div>

            <div style="display: block; width: 99%; height: 55px;">
                <button class="button apply-stat-button"
                        style="width: 25%; margin: 10px; margin-right: -5px; float:right; display: <?php echo $bindedConcept ? "block" : "none"; ?>"
                        data-id='<?php echo $sf_user->getAuthUser()->getId(); ?>'
                        data-concept-id="<?php echo $bindedConcept ? $bindedConcept->getConceptId() : 0; ?>">Сохранить
                </button>
            </div>

            </form>
        </div>
    </div>
</div>
</div>

<script>
    $(function () {
        var calcValues = function (field, symbol, el) {
            var $f = $('.calc-field-' + field), fields = $f.data('calc-fields').split(":"),
                v1 = !isNaN(parseFloat($('.field-' + fields[0]).val())) ? parseFloat($('.field-' + fields[0]).val()) : 0,
                v2 = !isNaN(parseFloat($('.field-' + fields[1]).val())) ? parseFloat($('.field-' + fields[1]).val()) : 0;

            calcData($f, symbol, v1, v2);

            var parentField = $f.data('calc-parent-field') != 0 ? $f.data('calc-parent-field') : el.data('calc-parent-field');
            if (parentField != 0) {
                var $p = $('.calc-field-' + parentField),
                    symbol = $p.data('calc-type'),
                    pFields = $p.data('calc-fields').split(':');

                var v1 = getFieldVal(pFields[0]),
                    v2 = getFieldVal(pFields[1]);

                calcData($p, symbol, v1, v2);
            }
        }

        var calcData = function ($f, symbol, v1, v2) {
            if (symbol == 'plus') {
                $f.text(v1 + v2);
            }
            else if (symbol == 'minus') {
                $f.text(v1 - v2);
            }
            else if (symbol == 'divide') {
                if (v2 != 0)
                    $f.text((v1 / v2).toFixed(2));
            }
            else if (symbol == 'percent') {
                $f.text((v1 * v2 / 100).toFixed(2));
            }
        }

        var getFieldVal = function (id) {
            var $f = $('.calc-field-' + id);

            if ($f.length != 0)
                return !isNaN(parseFloat($f.text())) ? parseFloat($f.text()) : 0;

            $f = $('.field-' + id);
            if ($f.length != 0)
                return !isNaN(parseFloat($f.val())) ? parseFloat($f.val()) : 0;

            return 0;
        }

        $("input[type=text]").live("input", function () {
            var reg = new RegExp($(this).data('regexp'));

            if ($(this).data('type') != 'date') {
                if (!reg.test($(this).val()) && $(this).data('type') == 'dig')
                    $(this).val($(this).val().replace(/[^\d.]/, ''));
            }

            if ($(this).attr('data-calc-field')) {
                calcValues($(this).data('calc-parent-field'), $(this).data('calc-type'), $(this));
            }
        });

        $('#btApplyConceptToStatistic').live('click', function (e) {
            var concept = $('#sbActivityCertificates').val(),
                activity = $(this).data('activity-id'),
                $bt = $(this);

            e.preventDefault();
            if (concept == -1) {
                alert("Для продолжения выберите доступную концепцию.");
                return;
            }

            $('#imgLoader').show();
            $bt.hide();

            $('.apply-stat-button').attr('data-concept-id', concept);
            $.post('<?php echo url_for('@activity_extended_bind_to_concept'); ?>',
                {
                    concept: concept,
                    activity: activity
                },
                function (result) {
                    $("#accommodation").empty().html(result);
                    $('.group-content').show();

                    $('#imgLoader').hide();
                    $bt.show();

                    $('.apply-stat-button').show();
                });
        });

        $(".apply-stat-button").click(function (e) {
            var hasError = false, data = [];

            e.preventDefault();

            $('input[type=text]').css('border', '1px solid gray').removeClass("field-position-error");
            $('input[type=text]').popmessage2('hide');

            $.each($("#frmStatistics input[type=text]"), function (ind, el) {
                var regExp = new RegExp($(el).data('regexp'));

                $(el).parent().css('border-color', '');
                if ($(el).attr("required") && ($(el).val().length == 0 || parseInt($(el).val()) == 0)) {
                    $(el).css('border', '1px solid red').addClass("field-position-error");

                    $(el).popmessage2('show', 'error', 'В полях, обязательных для заполнения, должны быть данные, отличные от 0.');
                    hasError = true;
                }
                else if ($(el).data('type') == "date" && !regExp.test($(el).val())) {
                    $(el).parent().css('border-color', 'red');
                    hasError = true;
                }

                if ($(el).data('type') != "date")
                    data.push({
                        id: $(el).data('field-id'),
                        value: $(el).val()
                    });
            });

            var startDate = getElDate($('input[name*=Start]')),
                endDate = getElDate($('input[name*=End]'));

            if (startDate == undefined || endDate == undefined) {
                scrollTop('dates');
                return;
            }

            if (endDate < startDate) {
                $('input[name*=Start]').parent().css('border-color', 'red');
                $('input[name*=End]').parent().css('border-color', 'red');

                hasError = true;
            }

            if (hasError) {
                $("#frmStatistics .stats-description").fadeIn();
                scrollTop("field-position-error");

                return;
            }

            data.push({
                id: $('input[name*=Start]').data('field-id'),
                value: $('input[name*=Start]').val() + '-' + $('input[name*=End]').val()
            });

            var bt = $(this);
            bt.fadeOut();

            $("#frmStatistics .stats-description").fadeOut();
            $.post("<?php echo url_for('@activity_extended_change_stats'); ?>",
                {data: data, concept: $(this).data('concept-id')},
                function (result) {
                    $('.info-save-complete').fadeIn("slow");

                    setTimeout(function () {
                        $('.info-save-complete').fadeOut('fast');
                        bt.fadeIn('normal');
                    }, 3000);
                }
            );
        });

        var parseDate = function (date) {
            if (date != undefined) {
                var tmp = date.split('.').reverse();

                return new Date(tmp[0], tmp[1] - 1, tmp[2]);
            }

            return null;
        }

        var getElDate = function (el) {
            var tmp = '';

            tmp = $(el);
            if (tmp != undefined)
                return parseDate(tmp.val()).getTime();

            return null;
        }

        var scrollTop = function (ancor) {
            $("body, html").animate({
                    scrollTop: ($("." + ancor).eq(0).offset().top - 10) + "px"
                },
                {duration: 500});
        }

        $('#frmStatistics .with-date').datepicker();
    });
</script>