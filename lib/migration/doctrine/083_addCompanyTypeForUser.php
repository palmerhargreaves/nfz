<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class AddCompanyTypeForUser extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('user', 'company_type', 'enum', '', array(
             'values' => 
             array(
              0 => 'dealer',
              1 => 'importer',
              2 => 'other',
             ),
             'notnull' => '1',
             ));
    }

    public function down()
    {
        $this->removeColumn('user', 'company_type');
    }
}