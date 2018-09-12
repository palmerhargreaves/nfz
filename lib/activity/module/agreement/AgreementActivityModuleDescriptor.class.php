<?php

/**
 * Descriptor of agreement activity module
 *
 * @author Сергей
 */
class AgreementActivityModuleDescriptor extends ActivityModuleDescriptor
{
    function getActivityTabs()
    {
        if (!$this->user->isDealerUser())
            return array();

        return array(
            'agreement' => array(
                'name' => 'Согласование',
                'uri' => '@agreement_module_models?activity=' . $this->activity->getId()
            ),
        );
    }

    /**
     * Returns true if a module has additional configuration
     *
     * @return boolean
     */
    function hasAdditionalConfiguration()
    {
        return true;
    }

    /**
     * Returns uri to additional configuration
     *
     * @return string
     */
    function getAdditionalConfigurationUri()
    {
        return '@agreement_activity_config?activity_id=' . $this->activity->getId();
    }

    function hasActivityConcept()
    {
        return ActivityModuleTable::getInstance()
            ->createQuery('m')
            ->innerJoin('m.Activities a WITH a.id=?', $this->activity->getId())
            ->where('m.identifier=?', 'concept')
            ->count() > 0;
    }

    function getStatus()
    {
        if (!$this->user->isDealerUser())
            return parent::getStatus();

        $utils = new AgreementActivityStatusUtils($this->activity, $this->user->getDealer());
        $status = $utils->getStatus();

        return $status == self::STATUS_NONE ? parent::getStatus() : $status;
    }
}
