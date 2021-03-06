<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class AddReportNotificationFields extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('user', 'agreement_report_notification', 'boolean', '25', array(
             'default' => '0',
             'notnull' => '1',
             ));
        $this->addColumn('user', 'new_agreement_report_notification', 'boolean', '25', array(
             'default' => '0',
             'notnull' => '1',
             ));
        $this->addColumn('user', 'final_agreement_report_notification', 'boolean', '25', array(
             'default' => '0',
             'notnull' => '1',
             ));
    }

    public function down()
    {
        $this->removeColumn('user', 'agreement_report_notification');
        $this->removeColumn('user', 'new_agreement_report_notification');
        $this->removeColumn('user', 'final_agreement_report_notification');
    }
}