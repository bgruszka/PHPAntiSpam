<?php

require_once realpath(dirname(__FILE__)) . '/../lib/corpus.class.php';

class CorpusTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$messages = array(
			array(
				'category' => 'spam', 
				'content' => 'Disclaimer: According to the BLS (2010), a Medical Assistant Technician can earn up to $40,190 / year. A degree is required.'
			),
			array(
				'category' => 'nospam',
				'content' => 'Dzień Programisty wypada 13 września, a w roku przestępnym 12 września.'
			)
		);
		
		$separators = '/[-, ]/';
		$this->corpus = new Corpus($messages, $separators);
	}
	
	public function testMessagesCount()
	{
		$this->assertEquals(count($this->corpus->messagesCount['spam']), 1);
		$this->assertEquals(count($this->corpus->messagesCount['nospam']), 1);
	}
	
	public function testLexemsCountInCategories()
	{
		$this->assertEquals($this->corpus->lexemes['according']['spam'], 1);
		$this->assertEquals($this->corpus->lexemes['according']['nospam'], 0);
	}
}

?>