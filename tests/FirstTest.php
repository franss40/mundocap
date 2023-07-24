<?php
/**
 * Created by PhpStorm.
 * User: Usuario
 * Date: 02/08/2018
 * Time: 9:41
 */


# mejor terminar en test
class FirstTest extends \PHPUnit_Framework_TestCase
{
    # debe de empezar por test
    public function testPushAndPop()
    {

        //$this->get('/');
        //$this->assertEquals('200', $this->response->status());
        

        $stack = array();
        $this->assertEquals(0, count($stack));

        array_push($stack, 'foo');
        $this->assertEquals('foo', $stack[count($stack)-1]);
        $this->assertEquals(1, count($stack));

        $this->assertEquals('foo', array_pop($stack));
        $this->assertEquals(0, count($stack));
        //$this->get('/test/simulacro/mercancia');
        //$this->assertEquals(200, $this->response->status());

    }
}

$a = new FirstTest();
$a->testPushAndPop();

