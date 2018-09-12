<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 02.04.2017
 * Time: 11:56
 */

class AgreementModelStatusFactory {
    private static $_instance = null;

    public static function getInstance() {
        if (!self::$_instance) {
            self::$_instance = new AgreementModelStatusFactory();
        }

        return self::$_instance;
    }

    /**
     * @param $params
     * @return AgreementModelDealerStatus|AgreementModelManagerDesignerStatus
     * @throws Exception
     */
    public function getStatusClass($params) {
        if (isset($params['obj'])) {
            if ($params['obj'] instanceof AgreementModel || $params['obj'] instanceof AgreementModelReport) {
                return new AgreementModelStatusAbstract($params);
            }
        }

        throw new Exception('Class not found');
    }
}
