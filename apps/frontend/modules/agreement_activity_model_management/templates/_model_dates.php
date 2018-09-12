<tr>
    <td class="label">
        Даты проведения мероприятия
    </td>

    <td>
        <?php
        $dats = array();
        foreach ($concept->getAgreementModelDates() as $dates):
            $datsCount = explode("/", $dates->getDateOf());
            if (count($datsCount) > 1) {
                list($start, $end) = explode("/", $dates->getDateOf());
                echo sprintf("с %s до %s", $start, $end) . "<br/>";
            } else {
                $dats[] = $dates->getDateOf();
            }
        endforeach;

        if (!empty($dats))
            echo sprintf("с %s", implode(" до ", $dats));
        ?>
    </td>
</tr>

<tr>
    <td class="label">
        Срок действия сертификата клиента
    </td>

    <td>
        <?php echo $concept->getAgreementModelSettings()->getCertificateDateTo(); ?>
    </td>
</tr>