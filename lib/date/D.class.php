<?php

/**
 * Utils class for date conversation
 *
 * @author Сергей
 */
class D
{
    const SECONDS_IN_YEAR = 31536000;

    static public $genetiveRusMonths = array(
        'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа',
        'сентября', 'октября', 'ноября', 'декабря'
    );

    static private $quarterStartMonths = array(1 => 1, 2 => 4, 3 => 7, 4 => 10);

    const START_YEAR = 2013;
    const MIN_DAYS = 5;

    static function toDb($date, $with_time = false)
    {
        return is_numeric($date) ? date('Y-m-d' . ($with_time ? ' H:i:s' : ''), $date) : $date;
    }

    static function toUnix($date)
    {
        return is_numeric($date) ? intval($date) : strtotime($date);
    }

    static function compare($date1, $date2)
    {
        return self::toDb($date1) == self::toDb($date2)
            ? 0
            : self::toUnix($date1) - self::toUnix($date2);
    }

    static function fromRus($date)
    {
        $exploded = explode('.', $date);
        return mktime(12, 0, 0, intval($exploded[1]), intval($exploded[0]), intval($exploded[2]));
    }

    static function toLongRus($date)
    {
        $unix_date = self::toUnix($date);
        $date = getdate($unix_date);
        $result = $date['mday'] . ' ' . self::$genetiveRusMonths[$date['mon'] - 1];

        if (abs(time() - $unix_date) > self::SECONDS_IN_YEAR)
            $result .= ' ' . $date['year'];

        return $result;
    }

    static function toShortRus($date, $return_to_day = false)
    {
        $unix_date = self::toUnix($date);
        if (self::compare($unix_date, time()) == 0)
            return $return_to_day ? 'сегодня' : '';

        return self::compare($unix_date, strtotime('-1 day')) == 0
            ? 'вчера'
            : date('d.m.y', $unix_date);
    }

    static function getQuarter($date)
    {
        if (is_numeric($date))
            $month = date('n', $date);
        else
            $month = date('n', self::toUnix($date));

        return floor(($month - 1) / 3) + 1;
    }

    static function getYear($date)
    {
        return date('Y', self::toUnix($date));
    }

    static function isPrevYear($date)
    {
        $date = self::toUnix($date);
        $year = self::getYear($date);

        $minDay = self::getQuarterStartDay($date);
        $diff_days = date('n', $date) == 1 ? 20 : $minDay;

        $nDate = strtotime('-' . $diff_days . ' days', $date);
        $nYear = self::getYear($nDate);

        if ($year != $nYear)
            return true;

        return false;
    }

    static function calcQuarterData($date, $special = false)
    {
        $date = self::toUnix($date);
        $todayDay = date('j', $date);

        $minDay = self::getQuarterStartDay($date, $special);
        $qStart = self::getFirstMonthOfQuarter(D::getQuarter($date));

        if ($qStart != date('n', $date)) {
            return $date;
        }

        $diff_days = 0;
        if ($todayDay < $minDay) {
            $diff_days = date('n', $date) == 1 ? self::getQuarterStartDay($date) : $minDay;
        }

        $nDate = strtotime('-' . $diff_days . ' days', $date);


        return $nDate;
    }

    static function getFirstMonthOfQuarter($quarter)
    {
        return (($quarter - 1) * 3) + 1;
    }

    static function getQuarterStartDay($date, $special = false)
    {
        $q = D::getQuarter($date);

        $days = BudgetCalendarTable::getDays(D::getYear($date), $special);
        if (empty($days)) {
            return self::MIN_DAYS;
        }

        return $days[$q];
    }

    static function getBudgetYears(sfWebRequest $request = null, $simple = false)
    {
        $years = array();

        if (!is_null($request)) {
            $year = D::getBudgetYear($request);
        }

        for ($i = self::START_YEAR; $i <= date('Y'); $i++) {
            $years[] = $i;
        }

        return $years;
    }

    static function getBudgetYear(sfWebRequest $request)
    {
        if ($request && $request->getParameter('year')) {
            return $request->getParameter('year');
        }

        $q = self::getQuarter(date('Y-m-d'));
        $year = date('Y');

        if ($q == 1 && date('m') == 1) {
            $day = date('d');

            $days = BudgetCalendarTable::getDays(D::getYear(date('Y-m-d')));
            if (empty($days)) {
                return $year;
            }

            return ($day >= $days[$q] ? $year : $year - 1);
        }

        return date('Y');
    }

    static function isSpecialFirstQuarter(sfWebRequest $request)
    {
        $q = self::getQuarter(date('Y-m-d'));

        if ($q == 1) {
            $day = date('d');

            $days = BudgetCalendarTable::getDays(D::getYear(date('Y-m-d')));
            if (empty($days)) {
                return false;
            }

            return ($day > $days[$q] ? false : true);
        }

        return false;
    }

    static function getElapsedDays($st)
    {
        return floor(($st / 3600) / 24);
    }

    static function getYearsRangeList($begin = null, $end = null, $total = 10)
    {
        $year = range(!is_null($begin) ? $begin : 2010,
            (!is_null($end) ? $end : date('Y')) + $total);

        return array_merge(array(''), array_combine($year, $year));
    }

    static function getQuarterMonths($q) {
        $q_months = array
        (
            1 => array(1, 2, 3),
            2 => array(4, 5, 6),
            3 => array(7, 8, 9),
            4 => array(10, 11, 12)
        );

        return $q_months[$q];
    }

    static function getQuarterStartMonth($date)
    {
        $quartersStart = array(
            1 => 1,
            2 => 4,
            3 => 7,
            4 => 10
        );

        $quarter = self::getQuarter($date);

        return $quartersStart[$quarter];
        /*$current_month = date('m', strtotime($date));
        $current_quarter_start = floor($current_month / 4) * 3 + 1;*/

        /*
         * $start_date = date("Y-m-d H:i:s", mktime(0, 0, 0, $current_quarter_start, 1, date('Y', strtotime($data[$field])) ));
         * $end_date = date("Y-m-d H:i:s", mktime(0, 0, 0, $current_quarter_start + 3, 1, date('Y', strtotime($data[$field])) ));
         */

        //return $current_quarter_start;
    }

    /**
     * @param $date
     * @return int
     */
    static function checkDateInCalendar($date)
    {
        $days = 0;

        $check_date = date('Y-m-d', D::toUnix($date));
        $dates = CalendarTable::getCalendarDates();
        if (array_key_exists($check_date, $dates)) {
            $date_item = $dates[$check_date];

            if($date_item && isset($date_item['end_date'])) {
                $endDate = strtotime($date_item['end_date']);

                $days = 1;
                $i = 1;
                while (1) {
                    $tempDate = strtotime(date("Y-m-d", strtotime('+' . $i . ' days', D::toUnix($date_item['start_date']))));
                    if ($tempDate <= $endDate) {
                        $days++;
                        $i++;
                    } else {
                        break;
                    }
                }
            }
        }

        return $days;
    }

    /**
     * @param $model
     * @param $date
     * @return bool|string
     */
    static function makePlusDaysForModel($model, $date) {
        $plusDays = 3; //Количество дней для выполнения заявки
        if ($model->getStatus() == "accepted") {
            $plusDays = 5; //Количество дней для выполнения отчета
        }

        for ($i = 1; $i <= $plusDays; $i++) {
            $tempDate = date("d-m-Y H:i:s", strtotime('+' . $i . ' days', D::toUnix($date)));
            $d = getdate(strtotime($tempDate));

            $dPlus = self::checkDateInCalendar($tempDate);
            if ($dPlus == 0) {
                if ($d['wday'] == 0 || $d['wday'] == 6)
                    $dPlus++;
            } else if ($dPlus > 1) {
                $i += $dPlus;            }

            $plusDays += $dPlus;
        }

        return date("H:i:s d-m-Y", strtotime('+' . $plusDays . ' days', D::toUnix($date)));
    }

    static function getNewDate($date, $plusDays = 3, $sign = '+', $only_days = false, $format = 'd-m-Y H:i:s')
    {
        $total_days = 0;
        for ($i = 1; $i <= $plusDays; $i++) {
            $tempDate = date($format, strtotime($sign . $i . ' days', D::toUnix($date)));

            $d = getdate(strtotime($tempDate));
            $dPlus = self::checkDateInCalendar($tempDate);
            if ($dPlus == 0) {
                if ($d['wday'] == 0 || $d['wday'] == 6)
                    $dPlus++;
            } else if ($dPlus > 1) {
                $i += $dPlus;            }

            $plusDays += $dPlus;
            $total_days += $dPlus;
        }

        if ($only_days) {
            return $total_days;
        }

        return date($format, strtotime($sign . $plusDays . ' days', D::toUnix($date)));
    }
}
