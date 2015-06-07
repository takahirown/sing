<?php

class Model_Err
{
    public function registerAccount(array $params)
    {
        $now = $this->getNow();
        $user = Sing_Session::getAuthUser();

        return $this->insert('m_account', array(
                'name'          => $params['email'],
                'password'      => Sing_Security::securePassword($params['password']),
                'birthday_date' => '2015-05-15 10:00:00',
                'gender'        => $params['gender'],
        ));
    }

    public function findByEmail($email)
    {
        $sql = "SELECT
                    *
                FROM
                    m_account
                WHERE
                    name = ?";
        $stmt = $this->getStatement($sql, array($email));
        return $stmt->fetch();
    }
}