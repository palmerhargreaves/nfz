<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 02.04.2017
 * Time: 12:15
 */

interface AgreementModelStatusObjectInterface
{
    const DEALER = 'dealer';
    const MANAGER_DESIGNER = 'manager_designer';
    const MANAGER = 'manager';
    const SPECIALIST = 'specialist';
    const IMPORTER = 'special_importer';
    const REGIONAL_MANAGER = 'special_regional_manager';

    public function getObject();
}
