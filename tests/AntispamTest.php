<?php

namespace PHPAntiSpam;

use PHPAntiSpam\Method\GrahamMethod;

class AntiSpamTest extends \PHPUnit_Framework_TestCase
{
    private $messages = array();
    private $separators = '/[ ]/';

    private $noSpamMessage = 'This is a ham message';
    private $spamMessage = 'This is a spam message';

    public function setMessages()
    {
        $this->messages = array(
            array(
                'category' => 'nospam',
                'content' => $this->noSpamMessage,
            ),
            array(
                'category' => 'spam',
                'content' => $this->spamMessage,
            )
        );
    }
	public function setUp()
	{
		$this->setMessages();
	}
	
	public function testIsSpamGrahamMethod()
	{
        $corpus = $this->getMockBuilder('\PHPAntiSpam\Corpus')
                       ->disableOriginalConstructor()
                       ->getMock();

        /** @var GrahamMethod $method */
        $method = $this->getMockBuilder('\PHPAntiSpam\Method\GrahamMethod')
                       ->disableOriginalConstructor()
                       ->getMock();

        $method->expects($this->once())
               ->method('calculate')
               ->will($this->returnValue(0.90));

        $AntiSpam = new AntiSpam($corpus);
        $AntiSpam->setMethod($method);

        $this->assertEquals(0.90, $AntiSpam->isSpam($this->spamMessage));
	}
	
	public function testMessageIsNotSpamGrahamMethod()
	{
        $this->markTestIncomplete();
	}
}

?>