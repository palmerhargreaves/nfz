<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class AddUnitsForModelFields extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('agreement_model_field', 'units', 'string', '255', array(
             'notnull' => '',
             ));
    }

    public function down()
    {
        $this->removeColumn('agreement_model_field', 'units');
    }
}