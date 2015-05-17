<?php

namespace PHPAntiSpam\Method;
use PHPAntiSpam\Corpus;
use PHPAntiSpam\DecisionMatrix\DecisionMatrix;

/**
 * Class GrahamMethod
 * @package PHPAntiSpam\Method
 */
class GrahamMethod implements MethodInterface
{
    protected $windowSize = 15;

    protected $bias = true;

    protected $text;
    protected $decisionMatrix;

    /** @var  Corpus */
    protected $corpus;

    public function __construct(Corpus $corpus, $text)
    {
        $this->corpus = $corpus;
        $this->text = $text;

        $words = array_map(function($word) {
            return strtolower($word);
        }, preg_split($this->corpus->separators, $this->text));

        $this->decisionMatrix = new DecisionMatrix($words, $this->corpus, $this->windowSize);
    }

    public function calculate()
    {
        foreach($this->corpus->lexemes as $word => $value) {
            $value = $this->calculateWordValue($value['spam'], $value['nospam'], $this->corpus->messagesCount['spam'], $this->corpus->messagesCount['nospam']);
            $this->corpus->lexemes[$word]['probability'] = $value;
        }

        $mostImportantLexemes = $this->decisionMatrix->getMostImportantLexemes();

        return $mostImportantLexemes;
    }

    /**
     * Calculate lexeme value with Paul Graham method.
     *
     * @link http://www.paulgraham.com/spam.html
     *
     * @param $wordSpamCount
     * @param $wordNoSpamCount
     * @param $spamMessagesCount
     * @param $noSpamMessagesCount
     * @return float
     */
    private function calculateWordValue($wordSpamCount, $wordNoSpamCount, $spamMessagesCount, $noSpamMessagesCount)
    {
        $multiplier = 1;

        if($this->bias) {
            $multiplier = 2;
        }

        $value = ($wordSpamCount / $spamMessagesCount) / (($wordSpamCount/$spamMessagesCount) + (($multiplier * $wordNoSpamCount) / $noSpamMessagesCount));

        return $value;
    }
}