<?php

namespace PHPAntiSpam;

use PHPAntiSpam\Method\GrahamMethod;

class AntiSpamTest extends \PHPUnit_Framework_TestCase
{
	public function testCheckIsSpamMethod()
	{
        $corpus = $this->getMockBuilder('\PHPAntiSpam\Corpus')
                       ->disableOriginalConstructor()
                       ->getMock();

        $method = $this->getMockBuilder('\PHPAntiSpam\Method\MethodInterface')
                       ->disableOriginalConstructor()
                       ->getMock();

        $method->expects($this->once())
               ->method('calculate')
               ->will($this->returnValue(0.90));

        $antiSpam = new AntiSpam($corpus);
        $antiSpam->setMethod($method);

        $this->assertEquals(0.90, $antiSpam->isSpam('short text'));
	}
}

?>