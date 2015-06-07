<?php

/**
 * $cd Sing/tests
 * $php phpunit.phar --bootstrap library/bootstrap.php library
 *
 * @author t.watanabe
 *
 */
class DefaultTest extends Zend_Test_PHPUnit_ControllerTestCase
{
    public function setUp()
    {
        $this->bootstrap = new Zend_Application(
            APPLICATION_ENV,
            APPLICATION_PATH . '/configs/application.ini'
        );
        parent::setUp();
    }

    /* モデル生成テスト */

    public function testAutoLoadModel()
    {
        $model = new Model_Err();
        $this->assertTrue($model instanceof Model_Err);
    }

    /* テストメソッド間の依存性 */

    public function testEmpty()
    {
        $stack = array();
        $this->assertEmpty($stack);
        return $stack;
    }

    /**
     * @depends testEmpty
     */
    public function testPush(array $stack)
    {
        $stack[] = 'foo';
        $this->assertEquals('foo', $stack[0]);
        $this->assertNotEmpty($stack);
    }

}
