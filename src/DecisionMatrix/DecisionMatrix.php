<?php

namespace PHPAntiSpam\DecisionMatrix;

use PHPAntiSpam\Corpus;

abstract class DecisionMatrix
{
    protected $matrix = [];
    protected $words = [];

    /** @var  \PHPAntiSpam\Corpus */
    protected $corpus;

    protected $neutral = 0.5;

    public function __construct(array $words, Corpus $corpus, $window)
    {
        $this->words = $words;
        $this->corpus = $corpus;
        $this->window = $window;
    }

    /**
     * Add word in decision matrix
     *
     * @param $usefulnessArray
     * @param $word
     * @param $probability
     */
    protected function addWord(&$usefulnessArray, $word, $probability)
    {
        $usefulness = abs($this->neutral - $probability);

        $this->matrix[$word]['probability'] = $probability;
        $this->matrix[$word]['usefulness'] = $usefulness;
        $usefulnessArray[$word] = $usefulness;
    }

    /**
     * Add double word in decision matrix
     *
     * @param array $usefulnessArray
     * @param string $word
     * @param float $probability
     */
    protected function addDoubleWord(array &$usefulnessArray, $word, $probability)
    {
        for ($i = 1; $i <= 2; $i++) {
            $word = $word . $i;
            $this->addWord($usefulnessArray, $word, $probability);
        }
    }
}