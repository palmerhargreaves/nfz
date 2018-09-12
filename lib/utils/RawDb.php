<?php

/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 29.02.2016
 * Time: 10:18
 */
class RawDb
{
    private $_db = null;

    /**
     * RawDb constructor.
     */
    public function __construct() {
        $this->_db = Doctrine_Manager::getInstance()->getCurrentConnection()->getDbh();
    }

    /**
     * @param $table
     * @param $colsValues
     * @return array
     */
    public function getRow($table, $colsValues) {
        $query = "SELECT * FROM ".$table." WHERE ";

        $queryParams = array();
        foreach($colsValues as $col => $value) {
            $queryParams[] = $col . " = :" . $col;
        }
        $query .= implode(" AND ", $queryParams);

        return $this->makeQuery($query, $colsValues);
    }

    /**
     * @param $table
     * @param $rows
     * @return array
     */
    public function insertRow($table, $rows, $returnObj = false) {
        $query = "INSERT INTO ".$table;

        $cols = array_keys($rows);
        $colsParams = array_map(function($item){
            return "`".$item."`";
        }, $cols);

        $paramCols = array_map(function($item) {
            return ":".$item;
        }, $cols);

        $query .= "(".implode(',', $colsParams).")";
        $query .= " VALUES(".implode(',', $paramCols).")";

        $result = $this->makeQuery($query, $rows, false);
        if($returnObj) {
            $id = $this->_db->lastInsertId();

            return $this->getRow($table, array('id' => $id));
        }

        return $result;
    }

    /**
     * @param $table
     * @param $rows
     * @return array
     */
    public function updateRow($table, $rows, $updateBy, $returnObj = false ) {
        $query = "UPDATE $table SET ";

        $values = array();
        foreach($rows as $col => $value) {
            $values[] = sprintf("`%s` = :%s", $col, $col);
        }
        $query .= implode(', ', $values);

        $query .= " WHERE ";
        foreach($updateBy as $key => $val) {
            $query .= $key . ' = :'.$key;
        }

        return $this->makeQuery($query, array_merge($rows, $updateBy), $returnObj);
    }

    /**
     * @param $query
     * @param $colsValues
     * @return array
     */
    private function makeQuery($query, $colsValues, $haveResult = true) {
        $queryResult = $this->_db->prepare($query);

        $params = array();
        foreach($colsValues as $col => $value) {
            $params[$col] = $value;
        }

        $result = $queryResult->execute($params);
        if($haveResult) {
            $result = $queryResult->fetch();
        }

        return $result;
    }

}