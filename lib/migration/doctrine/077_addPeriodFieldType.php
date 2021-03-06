<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class AddPeriodFieldType extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->changeColumn('agreement_model_field', 'type', 'enum', '', array(
             'values' => 
             array(
              0 => 'string',
              1 => 'date',
              2 => 'select',
              3 => 'period',
             ),
             'notnull' => '1',
             ));
    }

    public function down()
    {
        $this->changeColumn('agreement_model_field', 'type', 'enum', '', array(
             'values' => 
             array(
              0 => 'string',
              1 => 'date',
              2 => 'select',
             ),
             'notnull' => '1',
             ));
    }
}