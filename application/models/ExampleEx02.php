<?php

class Model_ExampleEx02 extends Sing_Model
{
    public function selectAll()
    {
        $sql = "SELECT
                    *
                FROM
                    m_account";
        $stmt = $this->getStatement($sql);
        return $stmt->fetchAll();
    }

    /**
     * メソッドチェーンの為の Creation Method
     *
     * @return Model_AdminHome
     */
    public static function create()
    {
        return new static();
    }
}