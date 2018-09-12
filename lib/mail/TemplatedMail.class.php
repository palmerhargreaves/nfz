<?php

/**
 * Base class of a templated mail
 *
 * @author Сергей
 */
class TemplatedMail extends Swift_Message
{
    protected $_can_send_mail = true;
    protected $_model_id = 0;
    protected $_must_delete = false;

    function __construct($to, $template, $data)
    {
        parent::__construct(
            $this->generateSubject($template, $data),
            $this->generateBody($template, $data),
            'text/html'
        );

        $this->setupFrom();

        if (Utils::allowedIps()) {
            $this->setTo('kostig51@gmail.com');
        } else {
            $this->setTo($to);
        }
        $this->setTo($to);

        $user = UserTable::getInstance()->findOneBy('email', $to);
        if ($user && !$user->getAllowReceiveMails()) {
            $this->_can_send_mail = false;
            $this->_must_delete = true;
        }
    }

    protected function setupFrom()
    {
        $name = sfConfig::get('app_mail_sender_name');
        $email = sfConfig::get('app_mail_sender');

        $this->setFrom($email, $name);
    }

    protected function generateBody($template, $data)
    {
        sfContext::getInstance()->getConfiguration()->loadHelpers('Partial');

        return get_partial($template, $data);
    }

    protected function generateSubject($template, $data)
    {
        sfContext::getInstance()->getConfiguration()->loadHelpers('Partial');

        return trim(get_partial($template . '_subject', $data));
    }

    public function setCanSendMail($can_send) {
        $this->_can_send_mail = $can_send;
    }

    public function getCanSendMail() {
        return $this->_can_send_mail;
    }

    public function setModelId($model_id) {
        $this->_model_id = $model_id;
    }

    public function getModelId() {
        return $this->_model_id;
    }

    public function getMustDelete() {
        return $this->_must_delete;
    }
}
