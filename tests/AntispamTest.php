<?php

use PHPAntiSpam\AntiSpam;
use PHPAntiSpam\Corpus;

class AntiSpamTest extends PHPUnit_Framework_TestCase
{
    private $messages = array();
    private $separators = '/[-, ]/';

    public function getMessages()
    {
        $dirs = array('spam', 'nospam');

        $messages = array();

        foreach($dirs as $dir) {
            $files = glob($dir.'/*.txt');

            foreach($files as $file) {
                $message = array();

                $content = file_get_contents($file);

                $message['content'] = $content;
                $message['category'] = $dir;

                $messages[] = $message;
            }
        }

        return $messages;
    }
	public function setUp()
	{
		//$messages = $this->getMessages();

        $this->messages = array(
            array(
                'category' => 'nospam',
                'content' => 'Dzień Programisty wypada 13 września, a w roku przestępnym 12 września.'
            )
        );

        $message = array(
            'category' => 'spam',
            'content' => 'Disclaimer: According to the BLS (2010), a Medical Assistant Technician can earn up to $40,190 / year. A degree is required.'
        );

        for($i = 0; $i < 5; $i++) {
            $this->messages[] = $message;
        }
	}
	
	public function testMessageIsSpamGrahamMethod()
	{
        $message = 'Disclaimer: According to the BLS (2010), a Medical Assistant Technician can earn up to $40,190 / year. A degree is required.';

        $corpus = new Corpus($this->messages, $this->separators);
        $AntiSpam = new AntiSpam($corpus);
        $AntiSpam->setMethod(AntiSpam::GRAHAM_METHOD);

		$this->assertGreaterThan(0.9, $AntiSpam->isSpam($message));
	}
	
	public function testMessageIsNotSpamGrahamMethod()
	{
        $message = 'Dzień Programisty wypada 13 września.';

        $corpus = new Corpus($this->messages, $this->separators);
        $AntiSpam = new AntiSpam($corpus);
        $AntiSpam->setMethod(AntiSpam::GRAHAM_METHOD);

		$this->assertLessThan(0.9, $AntiSpam->isSpam($message));
	}
	
	public function testMessageIsSpamBurtonMethod()
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
	}
}

?>