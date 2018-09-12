<?php

/**
 * Material filter form.
 *
 * @package    Servicepool2.0
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class MaterialFormFilter extends BaseMaterialFormFilter
{
    public function configure()
    {
        $this->manageActivities();
        $this->managerCategories();
    }

    protected function manageActivities() {
        $this->widgetSchema['activities_list'] = new sfWidgetFormDoctrineChoice(array('model' => 'Activity', 'query' => Doctrine::getTable('Activity')->getActivitesList(), 'add_empty' => true, 'multiple' => false, 'method' => 'getIdName'));
        $this->validatorsSchema['activities_list'] = new sfValidatorDoctrineChoice(array('model' => 'Activity', 'multiple' => false, 'required' => false));
    }

    protected function managerCategories() {
        $this->widgetSchema['categories_list'] = new sfWidgetFormDoctrineChoice(array('model' => 'MaterialCategory', 'query' => Doctrine::getTable('MaterialCategory')->getCategoriesList(), 'add_empty' => true, 'multiple' => false, 'method' => 'getName'));
        $this->validatorsSchema['categories_list'] = new sfValidatorDoctrineChoice(array('model' => 'MaterialCategory', 'multiple' => false, 'required' => false));
    }

    public function doBuildQuery(array $values)
    {
        $q = parent::doBuildQuery($values);

        if (isset($values['activities_list']) && !empty($values['activities_list'])) {
            $materials = ActivityMaterialsTable::getInstance()->createQuery()->select('material_id')->where('activity_id = ?', $values['activities_list'])->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

            if (count($materials) > 0) {
                $materials_ids = array_map(function ($material) {
                    return $material['material_id'];
                }, $materials);

                $q->whereIn('id', $materials_ids);
            } else {
                $q->where('id = ?', -1);
            }
        }

        if (isset($values['categories_list']) && !empty($values['categories_list'])) {
            $q->andWhere('category_id = ?', $values['categories_list']);
        }

        return $q;
    }
}
