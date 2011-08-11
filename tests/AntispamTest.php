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
		
		$this->corpus = new Corpus($messages);
		$this->antispam = new Antispam($this->corpus);
		$this->antispam->setWindow(Antispam::GRAHAM_WINDOW);
	}
	
	public function testMessageIsSpam()
	{
		$message = 'Medical Assistant Technician can earn up to $40,190 / year. A degree is required.';
		$this->assertGreaterThan(0.9, $this->antispam->isSpam($message));
	}
	
	public function testMessageIsNotSpam()
	{
		$message = 'Dzień Programisty wypada 13 września';
		$this->assertLessThan(0.9, $this->antispam->isSpam($message));
	}
}

?>