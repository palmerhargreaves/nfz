<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 17.03.2017
 * Time: 14:27
 */

class AgreementAcceptDeclineAbstract {
    protected $_params = array();

    protected $_class = '';
    protected $_postfix = '';

    const MANAGER_AGREEMENT = 'manager';
    const MANAGER_DESIGNER_AGREEMENT = 'manager_designer';

    public function __construct($params)
    {
        $this->_params = $params;

        foreach ($this->_params as $key => $value) {
            $this->{$key} = $value;
        }
    }

    /**
     * Make agreement class
     * @return mixed
     */
    protected function  getClass() {
        /*If model have prolongation field filled, use Manager class */
        $external_cls = '';
        if ($this->model->getAcceptInModel() != 0 || $this->model->getModelAcceptedInOnlineRedactor()) {
            $external_cls = self::MANAGER_AGREEMENT;
        }

        $cls = $this->model->getModelCategory()->getAgreementClass($external_cls);
        if (!empty($this->_class)) {
            $cls = $this->_class;
        }

        if (!empty($this->_postfix)) {
            $cls .= ucfirst($this->_postfix);
        }

        return new $cls($this->model, $this->_params);
    }

    /**
     * Make report agreement cls
     * @return mixed
     */
    protected function getReportClass() {
        /*If model have prolongation field filled, use Manager class */
        $external_cls = '';
        if ($this->model->getAcceptInModel() != 0) {
            $external_cls = self::MANAGER_AGREEMENT;
        }

        $cls = $this->model->getModelCategory()->getAgreementClass($external_cls);
        if (!empty($this->_class)) {
            $cls = $this->_class;
        }

        if ($this->model->getStatus() == 'accepted' && !$this->model->isValidModelCategory()) {
            $cls_report = $cls . 'ManagerReport';

            $cls = !class_exists($cls_report) ? ($cls . 'Report') : $cls_report;
        } else {
            if (!empty($this->_postfix)) {
                $cls .= ucfirst($this->_postfix);
            }
        }

        return new $cls($this->model, $this->_params);
    }
}
