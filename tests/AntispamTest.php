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

        /*$corpus = new Corpus($this->messages, $this->separators);
        $AntiSpam = new AntiSpam($corpus);
        $AntiSpam->setMethod(new GrahamMethod($corpus, $this->noSpamMessage));

		$this->assertLessThan(0.9, $AntiSpam->isSpam());*/
	}
	
	/*public function testMessageIsSpamBurtonMethod()
	{
        $message = 'Disclaimer: According to the BLS (2010), a Medical Assistant Technician can earn up to $40,190 / year. A degree is required.';

        $corpus = new Corpus($this->messages, $this->separators);
        $AntiSpam = new AntiSpam($corpus);
        $AntiSpam->setMethod(AntiSpam::BURTON_METHOD);

		$this->assertGreaterThan(0.9, $AntiSpam->isSpam($message));
	}
	
	public function testMessageIsNotSpamBurtonMethod()
	{
        $message = 'Dzień Programisty wypada 13 września.';

        $corpus = new Corpus($this->messages, $this->separators);
        $AntiSpam = new AntiSpam($corpus);
        $AntiSpam->setMethod(AntiSpam::BURTON_METHOD);

		$this->assertLessThan(0.9, $AntiSpam->isSpam($message));
	}
	
	public function testMessageIsSpamRobinsonGeometricMeanTestMethod()
	{
        $message = 'Disclaimer: According to the BLS (2010), a Medical Assistant Technician can earn up to $40,190 / year. A degree is required.';

        $corpus = new Corpus($this->messages, $this->separators);
        $AntiSpam = new AntiSpam($corpus);
        $AntiSpam->setMethod(AntiSpam::ROBINSON_GEOMETRIC_MEAN_TEST_METHOD);

		$result = $AntiSpam->isSpam($message);
		$this->assertGreaterThan(0.55, $result['combined']);
	}
	
	public function testMessageIsNotSpamRobinsonGeometricMeanTestMethod()
	{
        $message = 'Dzień Programisty wypada 13 września.';

        $corpus = new Corpus($this->messages, $this->separators);
        $AntiSpam = new AntiSpam($corpus);
        $AntiSpam->setMethod(AntiSpam::ROBINSON_GEOMETRIC_MEAN_TEST_METHOD);

		$result = $AntiSpam->isSpam($message);
		$this->assertLessThan(0.55, $result['combined']);
	}
	
	public function testMessageIsSpamFisherRobinsonsInverseChiSquareTestMethod()
	{
        $message = 'Disclaimer: According to the BLS (2010), a Medical Assistant Technician can earn up to $40,190 / year. A degree is required.';

        $corpus = new Corpus($this->messages, $this->separators);
        $AntiSpam = new AntiSpam($corpus);
        $AntiSpam->setMethod(AntiSpam::FISHER_ROBINSONS_INVERSE_CHI_SQUARE_METHOD);

		$result = $AntiSpam->isSpam($message);
		$this->assertGreaterThan(0.55, $result['combined']);
	}
	
	public function testMessageIsNotSpamFisherRobinsonsInverseChiSquareTestMethod()
	{
        $message = 'Dzień Programisty wypada 13 września.';

        $corpus = new Corpus($this->messages, $this->separators);
        $AntiSpam = new AntiSpam($corpus);
        $AntiSpam->setMethod(AntiSpam::FISHER_ROBINSONS_INVERSE_CHI_SQUARE_METHOD);

		$result = $AntiSpam->isSpam($message);
		$this->assertLessThan(0.55, $result['combined']);

        $corpus = new Corpus($this->messages, $this->separators, true);
        $AntiSpam = new AntiSpam($corpus);
        $AntiSpam->setMethod(AntiSpam::FISHER_ROBINSONS_INVERSE_CHI_SQUARE_METHOD);

        $result = $AntiSpam->isSpam($message, true);
        $this->assertLessThan(0.55, $result['combined']);
	}*/
}

?>