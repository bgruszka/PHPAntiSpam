<?php

namespace PHPAntiSpam\Method;

use PHPAntiSpam\DecisionMatrix\DefaultDecisionMatrix;
use PHPAntiSpam\Math;
use PHPAntiSpam\Corpus;

/**
 * Class GrahamMethod
 * @package PHPAntiSpam\Method
 */
class GrahamMethod extends Math implements MethodInterface
{
    const WINDOW_SIZE = 15;

    protected $bias = true;

    protected $text;
    protected $decisionMatrix;

    /** @var  Corpus */
    protected $corpus;

    public function __construct(Corpus $corpus)
    {
        $this->corpus = $corpus;
    }

    public function setBias($bias)
    {
        $this->bias = $bias;
    }

    public function calculate($text)
    {
        $this->setDecisionMatrix($text);

        foreach ($this->corpus->lexemes as $word => $value) {
            $value = $this->calculateWordValue($value['spam'], $value['nospam'], $this->corpus->messagesCount['spam'],
                $this->corpus->messagesCount['nospam']);
            $this->corpus->lexemes[$word]['probability'] = $value;
        }

        $mostImportantLexemes = $this->decisionMatrix->getMostImportantLexemes();

        $result = $this->bayes($mostImportantLexemes);

        return $result;
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

        if ($this->bias) {
            $multiplier = 2;
        }

        $value = ($wordSpamCount / $spamMessagesCount) / (($wordSpamCount / $spamMessagesCount) + (($multiplier * $wordNoSpamCount) / $noSpamMessagesCount));

        return $value;
    }

    private function setDecisionMatrix($text)
    {
        $words = array_map(function ($word) {
            return strtolower($word);
        }, preg_split($this->corpus->separators, $text));

        $this->decisionMatrix = new DefaultDecisionMatrix($words, $this->corpus, self::WINDOW_SIZE);
    }
}