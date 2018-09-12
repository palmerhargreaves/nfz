<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 18.03.2017
 * Time: 11:27
 */

class AgreementModelStatusRules {
    const SPECIALIST = 'specialist';
    const DEALER = 'dealer';
    const MANAGER = 'manager';

    private static $rules = array();

    /**
     * Get model rules by key
     * @param $rule_key
     */
    public static function ruleForSpecialist($rule_key) {
        return self::getRule(self::SPECIALIST, $rule_key);
    }

    private static function getRule($base_key, $rule_key) {
        if (empty(self::$rules)) {
            self::$rules = array(
                self::SPECIALIST => array(
                    'accepted' => 'accepted',
                    'declined' => 'declined',
                    'wait' => 'wait'
                ),
                self::MANAGER => array(

                ),
                self::DEALER => array(

                )
            );
        }

        if (array_key_exists($base_key, self::$rules) && array_key_exists($rule_key, self::$rules[$base_key])) {
            return self::$rules[$base_key][$rule_key];
        }

        return '';
    }

}
