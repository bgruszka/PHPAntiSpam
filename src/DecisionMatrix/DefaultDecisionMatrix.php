<?php

namespace PHPAntiSpam\DecisionMatrix;

class DefaultDecisionMatrix extends DecisionMatrix implements DecisionMatrixInterface
{
    public function getMostImportantLexemes()
    {
        $usefulnessArray = array();
        $processedWords = array();

        foreach ($this->words as $key => $word) {
            $this->words[$key] = trim($word);
        }

        foreach ($this->words as $word) {
            if (strlen($word) > 0 && !in_array($word, $processedWords)) {
                // first occurrence of lexeme (unit lexeme)
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