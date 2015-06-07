<?php

/**
 * $cd Sing/tests
 * $php phpunit.phar --bootstrap library/bootstrap.php library
 *
 * @author t.watanabe
 *
 */
class StringTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->bootstrap = new Zend_Application(
            APPLICATION_ENV,
            APPLICATION_PATH . '/configs/application.ini'
        );
        parent::setUp();
    }

    public function testDivide()
    {
        $this->assertEquals(
                        array('aaa', 'bbb', 'ccc'),
                        Sing_String::divide('aaa|bbb|ccc', '|'));
        $this->assertEquals(
                        array('', '', ''),
                        Sing_String::divide('@@', '@'));
        $this->assertEquals(
                        array('あ', '100', 'ccc', 'ddd'),
                        Sing_String::divide('あ,100,ccc,ddd', ','));
        $this->assertEquals(
                        array('あ,100,ccc,ddd'),
                        Sing_String::divide('あ,100,ccc,ddd', '|'));
        $this->assertEquals(
                        array(''),
                        Sing_String::divide('', '|'));
        $this->assertEquals(
                        array(''),
                        Sing_String::divide(null, '|'));
        $this->assertEquals(
                        array(null),
                        Sing_String::divide(null, '|'));
    }

    public function testBlank()
    {
        $this->assertTrue(Sing_String::blank(''));
        $this->assertTrue(Sing_String::blank(null));
        $this->assertTrue(Sing_String::blank(false));
        $this->assertTrue(Sing_String::blank(0));
        $this->assertTrue(Sing_String::blank(0.0));
        $this->assertFalse(Sing_String::blank(-1));
    }
}
