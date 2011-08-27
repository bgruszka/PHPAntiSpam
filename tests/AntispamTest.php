<?php

require_once realpath(dirname(__FILE__)) . '/../lib/corpus.class.php';
require_once realpath(dirname(__FILE__)) . '/../lib/antispam.class.php';

class AntispamTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$messages = array(
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
			$messages[] = $message;
		}
		
		$separators = '/[-, ]/';
		
		$this->corpus = new Corpus($messages, $separators);
		$this->antispam = new Antispam($this->corpus);
	}
	
	public function testMessageIsSpamGrahamMethod()
	{
		$message = 'Medical Assistant Technician can earn up to $40,190 / year. A degree is required.';
		
		$this->antispam->setMethod(Antispam::GRAHAM_METHOD);
		$this->assertGreaterThan(0.9, $this->antispam->isSpam($message));
	}
	
	public function testMessageIsNotSpamGrahamMethod()
	{
		$message = 'Dzień Programisty wypada 13 września.';
		
		$this->antispam->setMethod(Antispam::GRAHAM_METHOD);
		$this->assertLessThan(0.9, $this->antispam->isSpam($message));
	}
	
	public function testMessageIsSpamBurtonMethod()
	{
		$message = 'Medical Assistant Technician can earn up to $40,190 / year. A degree is required.';
		
		$this->antispam->setMethod(Antispam::BURTON_METHOD);
		$this->assertGreaterThan(0.9, $this->antispam->isSpam($message));
	}
	
	public function testMessageIsNotSpamBurtonMethod()
	{
		$message = 'Dzień Programisty wypada 13 września.';
		
		$this->antispam->setMethod(Antispam::BURTON_METHOD);
		$this->assertLessThan(0.9, $this->antispam->isSpam($message));
	}
}

?>