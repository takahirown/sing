<?php

/**
 * DBモデル基底クラス
 *
 * @category  Sing
 * @author    t.watanabe
 * @since     2014/07/26
 */
abstract class Sing_Model
{
    /**
     * @var Zend_Db_Adapter
     */
    private $db = null;

    /**
     * コンストラクタ。
     */
    public function __construct()
    {
        $this->db = Zend_Registry::get('db');
    }

    /**
     * Zend_Db_Statement を取得します。
     *
     * @param string $sql
     * @param array $bindParams
     * @return Zenddb_Statement
     */
    public function getStatement($sql, array $bindParams = array())
    {
        return $this->db->query($sql, $bindParams);
    }

    /**
     * 更新 SQL の発行します。
     *
     * @param string  $table  対象テーブル
     * @param array   $data   更新データ
     * @param array   $where  更新条件
     * @throws Exception
     * @return 更新された行数（同じ値に更新した場合は 0 件扱い）
     */
    public function update($table, $data = array(), $where = NULL)
    {
        $this->beginTransaction();
        return $this->db->update($table, $data, $where);
    }

    /**
     * 挿入 SQL の発行します。
     *
     * @param string  $table  対象テーブル
     * @param array   $data   挿入データ
     * @throws Exception
     * @return int 最後に挿入されたシーケンス番号
     */
    public function insert($table, $data = array())
    {
        $this->beginTransaction();
        $this->db->insert($table, $data);
        return $this->db->lastInsertId();
    }

    /**
     * 削除 SQL の発行します。
     *
     * @param string $table
     * @param array $where
     * @throws Exception
     * @return
     */
    public function delete($table, $where = NULL)
    {
        $this->beginTransaction();
        return $this->db->delete($table, $where);
    }

    /**
     * DATETIME型に設定できる現在日時を取得します。
     *
     * @return string
     */
    protected function getNow()
    {
        return Sing_Date::getNow();
    }

    /**
     * トランザクションを開始します。
     */
    private function beginTransaction()
    {
        $front = Zend_Controller_Front::getInstance();
        $dbPlugin = $front->getPlugin('Sing_Controller_Plugin_DBTransaction');
        $dbPlugin->beginTransaction();
    }
}