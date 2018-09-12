<?php

/**
 * Description of MaterialsActivityModuleDescriptor
 *
 * @author Сергей
 */
class MaterialsActivityModuleDescriptor extends ActivityModuleDescriptor
{
    function getActivityTabs()
    {
        /*if (!$this->user->isDealerUser())
            return array();*/

        if ($this->activity->getBindedMaterials()->count() == 0)
            return array();

        return array(
            'materials' => array(
                'name' => 'Материалы',
                'uri' => $this->activity->getMaterialsUrl() ? $this->activity->getMaterialsUrl() : '@activity_materials?activity=' . $this->activity->getId()
            ),
        );
    }
}
