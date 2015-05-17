<?php

namespace PHPAntiSpam\DecisionMatrix;

class DecisionMatrix
{
    private $matrix = [];
    private $words = [];
    /** @var  \PHPAntiSpam\Corpus */
    private $corpus;

    private $neutral = 0.5;

    private function addWord(&$usefulnessArray, $word, $probability)
    {
        $usefulness = abs($this->neutral - $probability);

        $this->matrix[$word]['probability'] = $probability;
        $this->matrix[$word]['usefulness'] = $usefulness;
        $usefulnessArray[$word] = $usefulness;
    }

    public function __construct(array $words, \PHPAntiSpam\Corpus $corpus, $window)
    {
        $this->words = $words;
        $this->corpus = $corpus;
        $this->window = $window;
    }

    public function getMostImportantLexemes()
    {
        $usefulnessArray = array();
        $processedWords = array();

        foreach ($this->words as $key => $word) {
            $this->words[$key] = trim($word);
        }

        foreach ($this->words as $word) {
            if (strlen($word) > 0 && !in_array($word, $processedWords)) {
                // first occurence of lexeme (unit lexeme)
                if (!isset($this->corpus->lexemes[$word])) {
                    // set default / neutral lexeme probability
                    $probability = $this->neutral;
                } else {
                    $probability = $this->corpus->lexemes[$word]['probability'];
                }

                $this->addWord($usefulnessArray, $word, $probability);

                $processedWords[] = $word;
            }
        }

        // sort by usefulness
        array_multisort($usefulnessArray, SORT_DESC, $this->matrix);
        $mostImportantLexemes = array_slice($this->matrix, 0, $this->window);

        return $mostImportantLexemes;
    }
}