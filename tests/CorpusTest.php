<?php

use PHPAntiSpam\Corpus;

class CorpusTest extends PHPUnit_Framework_TestCase
{
    public function testCreatingCorpusWithMinWordLengthOption()
    {
        $messages = [
            [
                'category' => 'spam',
                'content' => 'simple text',
            ]
        ];

        $tokenizer = $this->getMockBuilder('\PHPAntiSpam\Tokenizer\TokenizerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $tokenizer->expects($this->once())
            ->method('tokenize')
            ->will($this->returnValue(['simple', 'text']));

        $corpus = new Corpus($messages, $tokenizer, ['min_word_length' => 10]);

        $this->assertCount(0, $corpus->lexemes);
        $this->assertEquals([], $corpus->lexemes);
        $this->assertEquals(['spam' => 1, 'nospam' => 0], $corpus->messagesCount);
    }

    public function testCreatingCorpusWithSpamMessage()
    {
        $messages = [
            [
                'category' => 'spam',
                'content' => 'simple text',
            ]
        ];

        $tokenizer = $this->getMockBuilder('\PHPAntiSpam\Tokenizer\TokenizerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $tokenizer->expects($this->once())
            ->method('tokenize')
            ->will($this->returnValue(['simple', 'text']));

        $corpus = new Corpus($messages, $tokenizer);

        $this->assertCount(2, $corpus->lexemes);
        $this->assertEquals([
            'simple' => ['spam'=> 1, 'nospam' => 0],
            'text' => ['spam'=> 1, 'nospam' => 0],
        ], $corpus->lexemes);
        $this->assertEquals(['spam' => 1, 'nospam' => 0], $corpus->messagesCount);
    }

    public function testCreatingCorpusWithNoSpamMessage()
    {
        $messages = [
            [
                'category' => 'nospam',
                'content' => 'simple text',
            ]
        ];

        $tokenizer = $this->getMockBuilder('\PHPAntiSpam\Tokenizer\TokenizerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $tokenizer->expects($this->once())
            ->method('tokenize')
            ->will($this->returnValue(['simple', 'text']));

        $corpus = new Corpus($messages, $tokenizer);

        $this->assertCount(2, $corpus->lexemes);
        $this->assertEquals([
            'simple' => ['spam'=> 0, 'nospam' => 1],
            'text' => ['spam'=> 0, 'nospam' => 1],
        ], $corpus->lexemes);
        $this->assertEquals(['spam' => 0, 'nospam' => 1], $corpus->messagesCount);
    }
}
