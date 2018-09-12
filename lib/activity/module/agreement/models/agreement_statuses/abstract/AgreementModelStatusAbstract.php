<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 02.04.2017
 * Time: 11:53
 */

class AgreementModelStatusAbstract {
    protected $params = null;

    public function __construct($params)
    {
        $this->params = $params;

        foreach ($params as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function getStatusCls() {
        return new $this->cls($this->params);
    }
}
