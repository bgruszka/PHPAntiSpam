<?php

namespace PHPAntiSpam\Method;

use PHPAntiSpam\DecisionMatrix\DefaultDecisionMatrix;

/**
 * Class GrahamMethod
 * @package PHPAntiSpam\Method
 */
class GrahamMethod extends Method implements MethodInterface
{
    const WINDOW_SIZE = 15;

    public function calculate($text)
    {
        $this->setDecisionMatrix($text);
        $this->setLexemesProbability();

        return $this->bayes($this->decisionMatrix->getMostImportantLexemes());
    }

    private function setDecisionMatrix($text)
    {
        $this->decisionMatrix = new DefaultDecisionMatrix(
            $this->getWordsFromText($text),
            $this->corpus,
            self::WINDOW_SIZE
        );
    }
}